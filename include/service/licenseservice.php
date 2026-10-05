<?php

/**
 * 本机的**授权状态** —— 它存在哪、算不算「已激活」、什么时候该清掉。
 *
 * 2026-10-05 新加的，照着 EMSHOP 那边的 `include/service/LicenseService.php` 做。
 * 授权码两个项目**通用**（一个码同时管 EMSHOP 和 TTSHOP），但两边的客户端各调
 * 各的接口：这边一律走 `/api/open/v1/tt/...`（见 `LicenseClient`）。
 *
 * ── 存在哪：`options` 键值表（不新建表）──────────────────────
 *   `license_ttkey`        授权码（明文，和 EMSHOP 那边同一个键名）
 *   `license_ttkey_type`   档位数字 1 / 2 / 3
 *   `license_main_host`    **归一后的主授权域名 —— 「已激活」的判据就是它非空**
 *   `license_alias_hosts`  别名域名（JSON 数组，这台机器换过域名时用）
 *
 * ⚠️ 读的是 `Option::get()`（**走文件缓存**），所以每次写完都要
 *    `Cache::getInstance()->updateCache('options')` 把缓存刷了 —— 不刷的话
 *    同一个请求里接着读会读到旧值（「刚激活完页面还说未授权」）。
 *
 * ── 「已激活」为什么看 `license_main_host` 非空 ────────────────
 * 旧的 `Register::isRegLocal()` 是「本地 ttkey 长度对不对」，
 * `isRegServer()` 是「问服务端」—— 两个都不对：前者只要手里有个 32 位的串
 * 就算激活（假的也算），后者一到网络异常就 fail-open。
 * 现在：**本地记着「这台机器绑在哪条授权上」才算激活**，服务端只在
 * **明确回 `authorized === false`** 时才把它清掉（见 `revalidateCurrent`）。
 *
 * ⚠️ **文件名必须全小写**（autoloader 会 `strtolower($class)` 再找文件）。
 */
class LicenseService
{
    /** 档位从低到高。和价值判断有关的只有这一处（同服务端 `EmshopLicense::TYPES`）*/
    const LEVELS = ['1' => 'VIP', '2' => 'SVIP', '3' => '至尊'];

    /** 别名域名最多几个（EMSHOP 那边也是 10） */
    const MAX_ALIAS_HOSTS = 10;

    /** 我现在的授权（没激活就是 null）*/
    public static function currentLicense()
    {
        self::migrateLegacy();

        $host = (string) self::read('license_main_host', '');

        if ($host === '') {
            return null;
        }

        return [
            'ttkey'    => (string) self::read('license_ttkey', ''),
            'type'     => (string) self::read('license_ttkey_type', ''),
            'main_host' => $host,
            'aliases'  => self::aliasHosts(),
        ];
    }

    /** 这台机器**激活了没有**（判据见文件头）*/
    public static function isActivated()
    {
        return self::currentLicense() !== null;
    }

    /** 当前档位（'1' / '2' / '3'；没激活是空串）*/
    public static function currentLevel()
    {
        $license = self::currentLicense();

        return $license === null ? '' : (string) $license['type'];
    }

    /** 档位的显示名（VIP / SVIP / 至尊）*/
    public static function levelLabel($level)
    {
        return isset(self::LEVELS[$level]) ? self::LEVELS[$level] : '未授权';
    }

    /** 当前档位**够不够**某个档位（VIP < SVIP < 至尊）*/
    public static function hasLevel($required)
    {
        $rank = ['1' => 1, '2' => 2, '3' => 3];
        $mine = $rank[self::currentLevel()] ?? 0;

        return $mine >= ($rank[(string) $required] ?? 99);
    }

    /**
     * 激活：把授权码绑到本机域名上，成功之后写本地状态。
     *
     * ⚠️ **先远程、再写本地** —— 反过来的话，远程失败就会留下一个「本地说
     * 激活了、服务端根本没这条」的假状态。
     *
     * @return array{level:string, level_label:string, bound_domain:string}
     * @throws RuntimeException 码不对 / 域名被别的码占了 / 连不上（消息是给人看的）
     */
    public static function activate($ttkey)
    {
        $ttkey = trim((string) $ttkey);

        if ($ttkey === '') {
            throw new RuntimeException('请输入授权码');
        }

        $host = self::effectiveHost();
        $result = LicenseClient::bind($ttkey, $host);

        /* 服务端归一之后的域名才是权威（它会把 www. / 端口 / 路径砍掉）*/
        $mainHost = trim((string) (isset($result['domain']) ? $result['domain'] : ''));

        if ($mainHost === '') {
            $mainHost = $host;
        }

        self::save('license_ttkey', $ttkey);
        self::save('license_ttkey_type', (string) (isset($result['license_type']) ? $result['license_type'] : ''));
        self::save('license_main_host', $mainHost);

        return [
            'level'        => self::currentLevel(),
            'level_label'  => self::levelLabel(self::currentLevel()),
            'bound_domain' => $mainHost,
        ];
    }

    /**
     * 解绑：远程解绑（码 + 当前主域名）→ 清本地。
     *
     * **只清 `main_host` 和档位，码和别名留着** —— 老客户换域名时还要用那个码
     * 重新激活，把码也清掉等于让他去翻聊天记录（和 EMSHOP 那边同一个口径）。
     *
     * @throws RuntimeException 未激活 / 域名和码对不上 / 连不上
     */
    public static function unbind()
    {
        $license = self::currentLicense();

        if ($license === null) {
            throw new RuntimeException('当前未激活，无需解绑');
        }

        if ($license['ttkey'] !== '') {
            /* 远程失败就抛出去，本地不动 —— 不然本地说解绑了、服务端还绑着 */
            LicenseClient::unbind($license['ttkey'], $license['main_host']);
        }

        self::save('license_main_host', '');
        self::save('license_ttkey_type', '');
    }

    /**
     * 自检：问服务端「这台机器现在是什么状态」，据它修正本地。
     *
     * ⚠️ **只有服务端明确回 `authorized === false` 才清本地**；
     * 网络异常（`LicenseClient` 抛异常）**保守保留** —— 那说明不了任何事。
     * 这正是旧 `Register::verifyTtKey()` 的反面（它在网络失败时当作已授权，
     * 现在是网络失败时**维持原状**、只有服务端说不行才动本地）。
     *
     * @return bool 自检跑成功了吗（网络不通就是 false，本地不动）
     */
    public static function revalidateCurrent()
    {
        $license = self::currentLicense();

        if ($license === null || $license['main_host'] === '') {
            return false;
        }

        try {
            $result = LicenseClient::status($license['main_host'], $license['ttkey']);
        } catch (RuntimeException $e) {
            /* 连不上 / 响应看不懂 → 不知道，那就什么都别改（文件头那段）*/
            return false;
        }

        $authorized = !empty($result['authorized']);

        if (!$authorized) {
            /* 服务端明确说未授权（码作废了 / 域名被解绑了）→ 清本地 */
            self::clearLocalAuthorization();

            return true;
        }

        /* 以服务端为准修正档位（站长在后台改过档位的话，本地那份就旧了）*/
        if (isset($result['license_type']) && $result['license_type'] !== '') {
            self::save('license_ttkey_type', (string) $result['license_type']);
        }

        return true;
    }

    /**
     * 清掉本地授权（**只清域名和档位，码和别名留着**）。
     *
     * 两处用：`revalidateCurrent()` 里服务端明确说未授权时、以及应用市场那边
     * 也明确回 `authorized: false` 时（同 EMSHOP 那边的口径）。
     */
    public static function clearLocalAuthorization()
    {
        self::save('license_main_host', '');
        self::save('license_ttkey_type', '');
    }

    /**
     * 本机域名：**优先用主授权域名**，没有就用当前访问的 Host。
     *
     * 激活 / 解绑时报给服务端的就是它（和「授权绑的是哪个域名」必须是同一个）。
     */
    public static function effectiveHost()
    {
        $license = self::currentLicense();

        if ($license !== null && $license['main_host'] !== '') {
            return $license['main_host'];
        }

        return getTopHost();
    }

    /** 别名域名（这台机器换过域名时，旧的那些还认）*/
    public static function aliasHosts()
    {
        $raw = (string) self::read('license_alias_hosts', '');

        if ($raw === '') {
            return [];
        }

        $list = json_decode($raw, true);

        return is_array($list) ? array_values(array_filter(array_map('strval', $list))) : [];
    }

    /**
     * 存别名域名。入参是**一行一个**（或者逗号分隔）的文本，归一到顶级域名。
     *
     * 和主域名一样去掉协议头 / 端口 / 路径 / `www.`，超上限的丢掉 ——
     * 上限是硬的：这东西会跟着每次校验一起带上服务端。
     *
     * @return int 存下来几个
     */
    public static function saveAliasHosts($input)
    {
        $parts = preg_split('/[\r\n,，]+/', (string) $input) ?: [];
        $out = [];

        foreach ($parts as $part) {
            $host = self::normalizeHost($part);

            if ($host === '' || in_array($host, $out, true)) {
                continue;
            }

            $out[] = $host;

            if (count($out) >= self::MAX_ALIAS_HOSTS) {
                break;
            }
        }

        self::save('license_alias_hosts', json_encode($out, JSON_UNESCAPED_UNICODE));

        return count($out);
    }

    /* ------------------------------------------------------------ 内部 */

    /**
     * 老站一次性迁移：`options` 里还没有新那套键、但旧的 `authorization` 表里
     * 有本域名一行时，把它搬过来。
     *
     * ⚠️ **为此专门写这一段**：老客户已经激活过了（码在旧表里），升级之后
     * 不该让他们**重新激活一次** —— 那不是升级，那是故障。
     * 搬完之后照常走 `revalidateCurrent()` 向服务端核对（码可能早就作废了）。
     */
    private static function migrateLegacy()
    {
        if ((string) self::read('license_main_host', '') !== '') {
            return;
        }

        try {
            $db = Database::getInstance();
            $row = $db->once_fetch_array(
                'SELECT ttkey, type FROM ' . DB_PREFIX . 'authorization WHERE domain = \''
                . $db->escape_string(getTopHost()) . "' LIMIT 1"
            );
        } catch (Exception $e) {
            return;
        }

        if (empty($row) || empty($row['ttkey'])) {
            return;
        }

        self::save('license_ttkey', (string) $row['ttkey']);
        self::save('license_ttkey_type', (string) $row['type']);
        self::save('license_main_host', getTopHost());
    }

    /**
     * 域名归一：去掉协议头 / 端口 / 路径 / `www.`，只留顶级域名。
     *
     * 服务端那边存的就是这个形状（`EmshopLicenseController::cleanDomain`），
     * 两边不一致就对不上。**只做减法和大小写**，不做法（如 `co.uk` 那种双后缀）
     * 的那套判断 —— 服务端用 `getTopHost` 同款逻辑，这里是客户端侧的镜像。
     */
    private static function normalizeHost($raw)
    {
        $host = trim((string) $raw);

        if ($host === '') {
            return '';
        }

        $host = preg_replace('#^[a-z]+://#i', '', $host);
        $host = explode('/', $host)[0];
        $host = explode(':', $host)[0];
        $host = strtolower($host);

        if (strpos($host, 'www.') === 0) {
            $host = substr($host, 4);
        }

        return trim($host, '.');
    }

    /** 读一个键（走 Option 的文件缓存）*/
    private static function read($key, $default)
    {
        $value = Option::get($key);

        return ($value === null || $value === false || $value === '') ? $default : $value;
    }

    /**
     * 写一个键，**并把 options 缓存刷掉**。
     *
     * ⚠️ 两个都不能省：
     *   · `escape_string` —— `Option::updateOption()` 自己**不做转义**（它是拿
     *     字符串拼 SQL 的），值里带个单引号就会把语句拼坏。别名那些是从输入框
     *     来的，必须转义
     *   · `updateCache('options')` —— 缓存是**文件**（`content/cache/options.php`），
     *     不刷的话同一个请求里接着读还是旧值
     */
    private static function save($key, $value)
    {
        $db = Database::getInstance();
        Option::updateOption($key, $db->escape_string((string) $value));
        Cache::getInstance()->updateCache('options');
    }
}
