<?php

/**
 * 「基础数据」那一份 —— 版本 / 购买下载地址 / 联系方式 / 公告 / 广告位。
 *
 * 2026-10-05 新加的。原来这些东西散在三个旧接口上
 * （`api/emshop.php?action=admin_index|get_em_buy_info`），那套服务端已经
 * 不提供了；新协议是**一条** `POST /api/open/v1/tt/base-data`。
 *
 * ── 为什么这里还要「翻译」一次，而不是让视图直接读新形状 ──────────
 * 首页那几个地方（`admin/views/templates/.../index/index.php` 的 `applyData`、
 * `admin/views/footer.php` 的购买弹窗）是按**老形状**写的，而且写得很宽容
 * （认 `ad`/`ads`/`recommend`/`service` 一串别名）。改视图风险大、收益小，
 * 所以**在控制器这一层翻成老形状**，视图一个字不动。
 *
 *   老形状的两个坑（这就是为什么要翻译，不能把新数据直接丢过去）：
 *     · 广告位：老的是 `ad` / `recommend`；新的是 `ad_slots` —— **不在它认的那串别名里**
 *     · 联系方式：老的是 `contact.qq` / `.qq_group` / `.tg` / `.tg_url`；
 *       新的是 `contact.qq_service` / `.qq_group` / `.tg_service` / `.tg_group_url`
 *
 * ⚠️ **一次请求内只取一次**：首页那三个 action 都调它，而且都在同一个请求里。
 *
 * ⚠️ **文件名必须全小写**（autoloader 会 `strtolower($class)` 再找文件）。
 */
class BaseDataService
{
    /** 一次请求里缓存的那份原始 base-data */
    private static $cache = null;

    /** 服务端要求的「代理商身份标识」——TTSHOP 这边就是内置的 SERVICE_TOKEN */
    const DEFAULT_IDENTITY = '89Z78A9S7D8F9G7H8J9K8L7M9N8B7V8C9X8Z76T54R32E1WQ';

    /**
     * 取那份基础数据（同一个请求里只发一次）。
     *
     * ⚠️ **失败要抛**（连不上 / 服务端拒绝），调用方如实报出来 ——
     * 旧代码在这条路上是 `Ret::error('网络请求超时…')`，行为一致。
     *
     * @return array
     * @throws RuntimeException
     */
    public static function fetch()
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $identity = (defined('SERVICE_TOKEN') && SERVICE_TOKEN) ? SERVICE_TOKEN : self::DEFAULT_IDENTITY;

        self::$cache = LicenseClient::baseData(
            $identity,
            LicenseService::effectiveHost(),
            (string) Option::TT_VERSION
        );

        return self::$cache;
    }

    /**
     * 首页仪表盘那份（老的 `?action=admin_index` 形状）。
     *
     * @return array{announcements: array, ad: array, contact: array}
     */
    public static function forDashboard()
    {
        $data = self::fetch();

        return [
            /* 公告：老视图认 announcements 这个名字 ✓ */
            'announcements' => isset($data['announcements']) ? $data['announcements'] : [],
            /*
             * 广告位：老视图**只认** ad / ads / recommend / service 那几个名字，
             * 没有 `ad_slots` —— 所以这里必须改名。
             * 每条里 `title` / `content` / `link_url` 那几个字段本来就一样。
             */
            'ad' => isset($data['ad_slots']) ? $data['ad_slots'] : [],
            /* 联系方式：字段名对不上，这里换成老名字（见文件头） */
            'contact' => self::legacyContact(isset($data['contact']) ? $data['contact'] : []),
        ];
    }

    /**
     * 购买信息那份（老的 `?action=get_em_buy_info` / `get_download_url` 形状）。
     *
     * 老视图要的是 `{service_qq, buy_url: [{name, url}]}` —— `buy_url` 那个形状
     * 正好和新接口的 `buy_links` 一模一样，直接给。
     *
     * @param bool $download true = 用下载地址（老代码里 `get_download_url` 走的是同一个接口，
     *                       只是视图拿去看下载链接）
     * @return array{service_qq: string, buy_url: array, download_url: array}
     */
    public static function forBuyInfo($download = false)
    {
        $data = self::fetch();
        $contact = isset($data['contact']) ? $data['contact'] : [];

        return [
            'service_qq'   => isset($contact['qq_service']) ? (string) $contact['qq_service'] : '',
            'buy_url'      => $download
                ? (isset($data['download_links']) ? $data['download_links'] : [])
                : (isset($data['buy_links']) ? $data['buy_links'] : []),
            /* 两个都给一份，视图爱用哪个用哪个（老接口两条 action 其实查的是同一份数据）*/
            'download_url' => isset($data['download_links']) ? $data['download_links'] : [],
        ];
    }

    /** 新形状的联系方式 → 老视图认的那几个键名 */
    private static function legacyContact($contact)
    {
        $contact = is_array($contact) ? $contact : [];

        return [
            'qq'       => isset($contact['qq_service']) ? (string) $contact['qq_service'] : '',
            'qq_group' => isset($contact['qq_group']) ? (string) $contact['qq_group'] : '',
            /* 老视图认 `tg`（单数）当客服号、`tg_url` 当群链接 */
            'tg'       => isset($contact['tg_service']) ? (string) $contact['tg_service'] : '',
            'tg_url'   => isset($contact['tg_group_url']) ? (string) $contact['tg_group_url'] : '',
            'wechat'   => isset($contact['wechat_service']) ? (string) $contact['wechat_service'] : '',
            'wechat_qr' => isset($contact['wechat_qr']) ? (string) $contact['wechat_qr'] : '',
        ];
    }
}
