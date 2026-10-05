<?php

/**
 * 「本机装着的这些应用，有哪些该升」—— 插件管理和模板管理**共用这一处**。
 *
 * 2026-10-05 新加的。原来 `admin/plugin.php` 和 `admin/template.php` 各写了一遍
 * 差不多的代码，都打旧的 `api/emshop.php?action=is_plugin_upgrade`
 * （那套服务端已经不提供了）。现在都走这里 —— 一次请求、一个口径，
 * 免得「插件说能升、模板说不能」这种两边不一致。
 *
 * ── 认应用靠 `name_en`（目录名那种 slug）────────────────────
 * 服务端那边 `name_en` 是**唯一标识**（后台必填 + 唯一索引），客户端报的
 * 就是插件/模板的目录名。大小写不敏感（服务端 `utf8mb4_unicode_ci`），
 * 所以这边匹配也一律 `strtolower`。
 *
 * ⚠️ **查失败要抛，不能装作「都是最新」** —— 旧代码在请求失败时返回空的
 * 更新列表，界面上就是「全部已是最新」。那是在骗人：用户会以为真的没有更新，
 * 而实际是根本没查上。
 *
 * ⚠️ **文件名必须全小写**（autoloader 会 `strtolower($class)` 再找文件）。
 */
class AppUpdateService
{
    /**
     * 查这批已装应用有哪些该升。
     *
     * @param array $installed 每项 `['name_en' => 目录名, 'version' => 本机版本]`
     * @return array 小写目录名 => 服务端回的那一行（含 `version` / `package_url` / `type` …）
     * @throws RuntimeException 连不上 / 服务端拒绝（**调用方要如实报出来**）
     */
    public static function checkInstalled($installed)
    {
        if (empty($installed)) {
            return [];
        }

        /* 服务端只回**有更新的那些**；`code` 传空串就是「未授权」那档
           （免费应用的更新照样给，见服务端那条接口的注释）*/
        $result = LicenseClient::appUpdateCheck(
            array_values($installed),
            LicenseService::effectiveHost(),
            (string) getMyTtKey()
        );

        $updates = isset($result['data']) && is_array($result['data']) ? $result['data'] : [];
        $map = [];

        foreach ($updates as $row) {
            if (empty($row['name_en'])) {
                continue;
            }

            $map[strtolower((string) $row['name_en'])] = $row;
        }

        return $map;
    }

    /**
     * 这个（目录名叫 `$name` 的）应用有没有更新。
     *
     * @param array $updates `checkInstalled()` 的返回值
     */
    public static function hasUpdate($updates, $name)
    {
        return isset($updates[strtolower((string) $name)]);
    }

    /**
     * 有更新的话，服务端给的那一行（版本号、安装包地址……）；没有就是 null。
     *
     * @param array $updates `checkInstalled()` 的返回值
     */
    public static function updateOf($updates, $name)
    {
        $key = strtolower((string) $name);

        return isset($updates[$key]) ? $updates[$key] : null;
    }
}
