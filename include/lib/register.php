<?php
/**
 * ⚠️ **这个类现在是「兼容壳」**（2026-10-05 改的）。
 *
 * 它原来是 TTSHOP 授权的全部实现：一张 `authorization(ttkey, domain, type)`
 * 表、走旧协议 `api/emshop.php?action=*`。那套服务端**已经不提供了**，
 * 新的那一套是 `/api/open/v1/tt/...`（见 `LicenseClient` / `LicenseService`）。
 *
 * ── 为什么不把这个类删掉、改所有调用点 ────────────────────────
 * 全站有 **18 处**在调它（`admin/index.php`、`store.php`、`plugin.php`、
 * `template.php`、`upgrade.php`、`data.php`、`article.php`、各个视图……），
 * 一处一处改风险大、收益低。所以留这一层壳，内部全部转发到
 * `LicenseService`：**调用点一行不动，行为换成新的**。
 *
 * ── ⚠️ 顺手堵掉的那个洞（这是这次改动最要紧的一处）───────────
 * 旧的 `verifyTtKey()` 里有一句：
 *
 *     if (empty($res)) { return true; }     // 网络失败 → 当作已授权
 *
 * 服务器超时 / DNS 挂了 / 502 —— 全都算「你买过了」。**断网就是免授权通行证**。
 * 现在这层壳里没有这一句了：远程核对一律走 `LicenseService::revalidateCurrent()`
 * —— **只有服务端明确回 `authorized === false` 才清本地状态**，
 * 连不上时维持原状（本地状态是当初**激活成功**才写下的，不是「谁都能编一个」）。
 */
class Register
{
    /** 授权码长度（32 位十六进制）。老码 `EM-XXXX-XXXX-XXXX` 那种不认 */
    const TTKEY_LEN = 32;

    /**
     * 这台机器**激活了没有**。
     *
     * ⚠️ 判据换了（2026-10-05）：旧的实现是 `getMyTtKey()` 拿到的串长度对不对
     * —— 只要手里有个 32 位的串就算激活，编一个也算。现在是
     * **本地记着「这台机器绑在哪条授权上」**（`license_main_host` 非空）才算，
     * 而它只可能是「服务端确认过的一次激活」写下来的。
     */
    public static function isRegLocal()
    {
        return LicenseService::isActivated();
    }

    /**
     * 进某个功能之前问一次服务端。
     *
     * 语义和旧的一样（问一次、拿结果做门槛），但：
     *   · 只有服务端**明确说未授权**才会清本地（旧的是「答不上来 = 放行」）
     *   · 问完之后的判据仍然是本地状态（见 `isRegLocal`）
     */
    public static function isRegServer()
    {
        LicenseService::revalidateCurrent();

        return LicenseService::isActivated();
    }

    /**
     * 当前档位（数字 1 / 2 / 3，未授权是 0）—— 调用方拿它显示 VIP / SVIP / 至尊。
     *
     * ⚠️ 旧实现每次调用都去 curl 一次服务端；现在读本地（顺带自检一次由
     * `isRegServer()` 负责），因为它是在页面里当**数据**用的。
     */
    public static function getRegType()
    {
        return (int) LicenseService::currentLevel();
    }

    /**
     * 激活（旧的 `?action=auth` 那条路走这里）。
     *
     * 返回形状**保持旧的样子**（`['code' => 200|400, 'data'|'msg' => …]`），
     * 因为 `admin/auth.php` 那一页是按它判断的。
     */
    public static function doReg($ttkey)
    {
        if (empty($ttkey)) {
            return ['code' => 400, 'msg' => '请填写授权码'];
        }

        try {
            $result = LicenseService::activate($ttkey);
        } catch (RuntimeException $e) {
            /* 服务端那句人话直接透给用户（「这个授权码不存在，请核对后重试」这种）*/
            return ['code' => 400, 'msg' => $e->getMessage()];
        }

        return ['code' => 200, 'data' => $result['level']];
    }

    /**
     * 这个码现在还有效吗。
     *
     * ⚠️ **行为变了**（有意的）：旧的版本在网络失败时返回 `true`（fail-open）。
     * 现在**连不上就是「不知道」，返回本地状态** —— 本地没激活过就是 false。
     * 真要「以服务端为准」，用 `isRegServer()`（它会自检）。
     */
    public static function verifyTtKey($ttkey)
    {
        if (strlen((string) $ttkey) !== self::TTKEY_LEN) {
            return false;
        }

        /* 手里的码和本地记着的那条不是同一个 → 这次问的不是「我的授权」*/
        $license = LicenseService::currentLicense();

        if ($license === null) {
            return false;
        }

        if ($license['ttkey'] !== '' && $license['ttkey'] !== $ttkey) {
            return (bool) LicenseService::revalidateCurrent();
        }

        return LicenseService::isActivated();
    }

    /**
     * 这个插件现在能不能下载。
     *
     * ⚠️ 旧实现是客户端自己拿 `plugin_id` 去问服务端「准不准下」。
     * 现在**不这么做了**：付费应用的那道门在**服务端的下载接口上**
     * （`/api/open/v1/tt/app/{id}/download` 会真的 400）——
     * 客户端这一层判不判都拦不住谁，而多一次请求就多一次「到底该信谁」。
     *
     * 所以这里只回答「本机激活了没有」：激活了就让它进去点，真正的拦截
     * 在服务端那一次下载请求上。
     *
     * @return int 1 = 可以（1 是旧的约定）/ 2 = 未激活
     */
    public static function verifyDownload($plugin_id)
    {
        return LicenseService::isActivated() ? 1 : 2;
    }

    /**
     * 清掉本地授权（旧的 `clean()` 是删 `authorization` 表那一行）。
     *
     * 现在只清本地那三个键 —— 服务端那条授权**不能**在这儿删，
     * 那该由「解绑」或站长在后台做。
     */
    public static function clean($ttkey = '')
    {
        LicenseService::clearLocalAuthorization();
    }
}
