<?php

/**
 * 和**授权服务器**说话的那一层 —— TTSHOP 的。
 *
 * 2026-10-05 新加的。TTSHOP 原来走的是旧协议（`api/emshop.php?action=*`，
 * 见 `include/lib/register.php`），那套服务端已经不提供了。这一份照着
 * EMSHOP 那边的 `include/lib/LicenseClient.php` 做，只是地址里是
 * `/api/open/v1/tt/...`（使用者 2026-10-05 定的：同样的操作，接口也要分出来，
 * em 是 em 的、tt 是 tt 的）。
 *
 * ── ⚠️ 和旧代码最要紧的一处不同：**「没答上来」不再等于「已授权」** ──
 * 旧的 `Register::verifyTtKey()` 里有一句：
 *
 *     if (empty($res)) { return true; }      // 网络失败 → 当作已授权
 *
 * 服务器超时、DNS 挂了、证书不对、502 —— 全都算「你买了」。**断网就是免授权
 * 通行证**，这是个真洞。所以这一层把三种情况分得清清楚楚：
 *
 *   · **没答上来**（curl 失败 / 空响应 / 响应不是 JSON）→ 抛 RuntimeException。
 *     调用方按「不知道」处理：**保守保留**本地已有状态（见 LicenseService）
 *   · **答了，但服务端说不成功**（HTTP 400 + code 400）→ 抛 RuntimeException，
 *     带上服务端那句人话（「这个授权码不存在，请核对后重试」那种）
 *   · **答了，成功**（HTTP 200 + code 200）→ 返回 data
 *
 * ── 写接口不重试 ────────────────────────────────────────────
 * `bind` / `unbind` / `createOrder` 只发一次：它们是**写操作**，重试等于重复提交
 * （订单尤其 —— 发两次就是两张单）。查那些（status / base-data / app-list /
 * app-update-check）失败重试 3 次，客户端启动时网络抖一下不该就报错。
 *
 * ⚠️ **文件名必须全小写**（`licenseclient.php`）：TTSHOP 的自动加载器
 *    （`include/lib/common.php` 的 `ttAutoload`）是 `strtolower($class)` 再找文件。
 *    本机 Windows 不区分大小写，**生产 Linux 上会找不到**。
 */
class LicenseClient
{
    /** 单个请求的超时（秒）。查更新那条要传一堆插件，给它宽一点 */
    const TIMEOUT = 8;
    const TIMEOUT_LONG = 15;

    /** 只读接口失败重试几次（写接口一律 1 次） */
    const MAX_ATTEMPTS = 3;

    /**
     * 授权服务器的地址。
     *
     * ⚠️ **只认常量、不读配置**（和 emshop 那边同一个理由）：地址必须跟着
     * 程序版本走，否则用户在线更新之后，配置里残留的旧地址会继续生效。
     */
    public static function baseUrl()
    {
        if (!defined('TT_LICENSE_SERVER_URL')) {
            throw new RuntimeException('未配置授权服务器地址（init.php 的 TT_LICENSE_SERVER_URL 缺失）');
        }

        return rtrim(TT_LICENSE_SERVER_URL, '/') . '/';
    }

    /** 解除域名与授权码的绑定（= 用户主动解绑）。**写操作，不重试** */
    public static function unbind($code, $domain)
    {
        return self::post('license/unbind', ['code' => $code, 'domain' => $domain], self::TIMEOUT, 1);
    }

    /** 把授权码绑到本机域名上（= 激活）。**写操作，不重试** */
    public static function bind($code, $domain)
    {
        return self::post('license/bind', ['code' => $code, 'domain' => $domain], self::TIMEOUT, 1);
    }

    /**
     * 问服务端「这台机器现在是什么状态」。
     *
     * 返回里的 `authorized === false` 是**服务端明确判定未授权** —— 这是
     * `LicenseService::revalidateCurrent()` 唯一会清本地状态的依据。
     *
     * ⚠️ **不重试**（和别的读接口不一样）：它挂在页面渲染路径上
     * （后台商店 / 在线更新进页面前都会问一次）。超时是保守处理的
     * —— 失败**不动本地状态**，所以重试买不到任何东西，只会让页面白等
     * （3 次 × 8 秒 = 服务器挂了时每次进页都要卡二十几秒）。
     */
    public static function status($domain, $code = '')
    {
        return self::post('license/status', ['domain' => $domain, 'code' => $code], self::TIMEOUT, 1);
    }

    /** 基础数据：版本 / 购买下载地址 / 联系方式 / 公告 / 广告位 */
    public static function baseData($identity, $domain, $version)
    {
        return self::post('base-data', [
            'identity' => $identity,
            'domain'   => $domain,
            'version'  => $version,
        ], self::TIMEOUT, self::MAX_ATTEMPTS);
    }

    /** 应用市场列表 */
    public static function appList($params)
    {
        return self::post('app-list', $params, self::TIMEOUT_LONG, self::MAX_ATTEMPTS);
    }

    /** 查更新：报上本机已装应用的「英文名 + 版本」 */
    public static function appUpdateCheck($apps, $domain, $code)
    {
        return self::post('app-update-check', [
            'domain' => $domain,
            'code'   => $code,
            'apps'   => $apps,
        ], self::TIMEOUT_LONG, self::MAX_ATTEMPTS);
    }

    /** 下单（买一个应用）。**写操作，不重试** —— 发两次就是两张单 */
    public static function createOrder($code, $domain, $appId)
    {
        return self::post('order', [
            'code'    => $code,
            'domain'  => $domain,
            'app_id'  => $appId,
        ], self::TIMEOUT_LONG, 1);
    }

    /**
     * 安装包的下载地址（**只是拼地址**，不在这里下）。
     *
     * 服务端返回的 `package_url` 是**根相对路径**，前面拼上服务器地址才是完整地址。
     * 那两个查询参数是**身份**：付费应用要「有效授权 + 买过这一款」两道门，
     * 直接在浏览器里打开这个地址也能下（服务端就是这么设计的）。
     */
    public static function downloadUrl($appId, $domain, $code)
    {
        return self::baseUrl() . 'app/' . (int) $appId . '/download'
            . '?' . http_build_query(['domain' => $domain, 'code' => $code]);
    }

    /**
     * 把服务端给的**根相对路径**（`/api/versions/tt/2/download`）拼成完整地址。
     *
     * 版本包那几个地址（`install_package_url` / `patch_package_url` /
     * `sql_package_url`）都是这个形状 —— 前端拿它可以拼自己的域名，但客户端
     * **自己去下**的时候必须补上服务器地址。
     */
    public static function absolute($path)
    {
        $path = (string) $path;

        if ($path === '') {
            return '';
        }

        /* 已经是完整地址就原样给（服务端哪天改成绝对地址也不会拼成两截）*/
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return self::baseUrl() . ltrim($path, '/');
    }

    /**
     * 发一次请求，拆掉外层的 `{code, message, data}` 信封，只把 `data` 给调用方。
     *
     * @param string $path   `/api/open/v1/tt/` 之后那一段，如 `license/bind`
     * @param array  $payload
     * @param int    $timeout
     * @param int    $maxAttempts 1 = 不重试（写操作）
     * @return array
     * @throws RuntimeException 见文件头那三种情况的说明
     */
    private static function post($path, $payload, $timeout, $maxAttempts)
    {
        $url = self::baseUrl() . 'api/open/v1/tt/' . $path;
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $headers = ['Content-Type: application/json'];

        $lastError = '';

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $raw = ttCurl($url, $body, 1, $headers, $timeout);

            /*
             * ⚠️ **传输层失败**（curl 返回 false：超时、DNS、连不上）。
             * 这里**绝不能**当成「成功」—— 那正是旧代码的洞。
             */
            if ($raw === false || $raw === null || $raw === '') {
                $lastError = '连不上授权服务器';

                continue;
            }

            $result = json_decode($raw, true);

            if (!is_array($result) || !isset($result['code'])) {
                /* 答了但不是我们那套信封（网关的 502 页面、被劫持的响应……）——
                   同样不能当成成功 */
                $lastError = '授权服务器的响应看不懂';

                continue;
            }

            if ((int) $result['code'] !== 200) {
                /* 服务端**明确**说不行（码不存在 / 域名对不上 / 参数不对）。
                   这是「答上来了」，重试没意义 —— 直接把那句人话抛出去 */
                throw new RuntimeException(
                    isset($result['message']) && $result['message'] !== ''
                        ? (string) $result['message']
                        : '授权服务器拒绝了这次请求'
                );
            }

            return isset($result['data']) && is_array($result['data']) ? $result['data'] : [];
        }

        throw new RuntimeException($lastError === '' ? '授权服务器没有响应' : $lastError);
    }
}
