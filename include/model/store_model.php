<?php


/**
 * 应用商店的数据层。
 *
 * 2026-10-05 改走新协议：原来打的是旧接口 `api/emshop.php?action=store`
 * （那套服务端已经不提供了），现在是 `POST /api/open/v1/tt/app-list`。
 *
 * ── ⚠️ 这里做了一次**字段改名**，别以为可以省 ──────────────────
 * 视图（`admin/views/store.php`）是按**老字段名**写的，而且判断用的是**字符串**
 * `is_buy === 'y'` / `'n'`（不是布尔）。新接口那套名字不一样：
 *
 *   老（视图认的）      新（接口给的）
 *   name              → name_cn
 *   english_name      → name_en（本机装没装是按目录名比的，见 storeHandleData）
 *   my_price          → price（**这个客户端实际要付的**，按它的授权档位算好的）
 *   vip_price/svip_price → price_vip / price_svip
 *   is_buy            → is_pay（**真花过钱**，转成 'y'/'n'）
 *   cover             → screenshots[0].url
 *   type              → type（`template` 要转成 `tpl`：`ttUnZip` 和安装分支都认 `tpl`）
 *
 * 改视图风险大、收益小，所以在这儿翻一次，视图一个字不动
 * （同 `BaseDataService` 那套取舍）。
 */
class Store_Model {

    /** 一次请求里缓存的分类字典（视图那边的筛选下拉要用）*/
    private static $categories = null;

    /**
     * 应用列表（首页那三个 tab 都走它）。
     *
     * @param string $type    `all` / `template` / `plugin`（controller 传的就是这几个）
     * @param int    $page
     * @param int    $pageNum 每页几条（接口上限 50）
     * @param string $keyword
     * @param mixed  $sid     分类 id（视图那边叫「插件类型」，就是分类）
     * @return array{list: array, count: int}
     */
    public function getList($type, $page, $pageNum, $keyword, $sid) {
        $params = [
            'domain'   => LicenseService::effectiveHost(),
            'code'     => (string) getMyTtKey(),
            'page'     => max(1, (int) $page),
            'per_page' => min(50, max(1, (int) $pageNum ?: 20)),
        ];

        if ($keyword !== '' && $keyword !== null) {
            $params['keyword'] = $keyword;
        }

        /* `all` 就是不传 type（两种都返回）*/
        if ($type === 'template' || $type === 'plugin') {
            $params['type'] = $type;
        }

        if (!empty($sid)) {
            $params['category_id'] = (int) $sid;
        }

        $result = LicenseClient::appList($params);

        $apps = isset($result['data']) && is_array($result['data']) ? $result['data'] : [];
        $meta = isset($result['meta']) ? $result['meta'] : [];
        $list = [];

        foreach ($apps as $app) {
            $shot = (isset($app['screenshots'][0]['url']) ? $app['screenshots'][0]['url'] : '');
            $price = isset($app['price']) ? $app['price'] : '0.00';
            $paid = !empty($app['is_pay']);

            $list[] = [
                'id'           => (int) $app['id'],
                'name'         => (string) $app['name_cn'],
                /* 本机装没装是按**目录名**比的（`storeHandleData`），所以这个必须是 slug */
                'english_name' => (string) $app['name_en'],
                'description'  => (string) (isset($app['description']) ? $app['description'] : ''),
                'version'      => (string) (isset($app['version']) ? $app['version'] : ''),
                'author'       => (string) (isset($app['author']) ? $app['author'] : ''),
                /*
                 * ⚠️ **原样给**（`template` / `plugin`），别在这里转成 `tpl` ——
                 * 视图里那个安装事件自己会转（`type === 'template' ? 'tpl' : 'plugin'`），
                 * 它也是按 `template` 判的。在这里先转一道会变成「tpl 又被当成 plugin」。
                 */
                'type'         => (string) (isset($app['type']) ? $app['type'] : 'plugin'),
                'vip_price'    => (string) (isset($app['price_vip']) ? $app['price_vip'] : '0.00'),
                'svip_price'   => (string) (isset($app['price_svip']) ? $app['price_svip'] : '0.00'),
                /* 这个客户端实际要付的价（至尊 = 0）—— 视图拿它当按钮上的金额 */
                'my_price'     => $price,
                /* ⚠️ **字符串** y / n，视图是按 `=== 'y'` 判的 */
                'is_buy'       => $paid ? 'y' : 'n',
                'cover'        => $shot,
                /* 新接口多给的（视图暂时不看，但留着有用）*/
                'can_buy'      => !empty($app['can_buy']),
                'purchased'    => !empty($app['purchased']),
                'package_url'  => (string) (isset($app['package_url']) ? $app['package_url'] : ''),
                'category_id'  => (int) (isset($app['category_id']) ? $app['category_id'] : 0),
                'category_name' => (string) (isset($app['category_name']) ? $app['category_name'] : ''),
            ];
        }

        return [
            'list'  => $list,
            'count' => (int) (isset($meta['total']) ? $meta['total'] : count($list)),
        ];
    }

    /*
     * ── 三个「专属货架」：新协议里没有这个概念，给空的 ──────────────
     * 老服务端有「我的已购 / 铁杆专属(SVIP) / 热门」三个货架，各是一条独立接口。
     * 新协议只有一条 `app-list`，没有这三种货架。
     *
     * ⚠️ 留这三个空壳是**故意的**：`admin/store.php` 里还有 `?action=mine|svip|top`
     * 三个分支在调它们（那几个页面本身早就坏了 —— `store_mine.php` 这些视图文件
     * **根本不存在**，从界面上也点不到）。删掉方法的话，谁手工敲一下那个地址
     * 就是「致命错误：方法不存在」；给个空数组至少是「这个货架是空的」。
     *
     * 真要把「我的已购」做回来：用 `getList()` 那套再筛一遍 `is_buy === 'y'`
     * 就行（新接口每条都带 `is_pay`，见类注释那张对照表）。
     */

    /** 我的已购（新协议没有这个货架 —— 见上面那段注释） */
    public function getMyAddon() {
        return [];
    }

    /** 铁杆专属（同上） */
    public function getSvipAddon() {
        return [];
    }

    /** 热门（同上） */
    public function getTopAddon() {
        return [];
    }

    /**
     * 分类字典（商店那一排筛选用的）。
     *
     * ⚠️ 2026-10-05 起**以服务端为准**：原来 `admin/store.php` 顶部硬编了一串
     * 「插件类型」（支付方式 / 系统通知 / 页面美化……），那是**老服务端**的分类；
     * 新后台的分类字典在 `bs_tt_app_category`（站长自己维护的），两边对不上。
     * 拿服务端的来当筛选，点下去才真能筛出东西。
     *
     * 取不到就返回空数组 —— 调用方自己决定退回什么（见 `admin/store.php`）。
     *
     * @return list<array{id: int, title: string}>
     */
    public static function categories() {
        if (self::$categories !== null) {
            return self::$categories;
        }

        try {
            $result = LicenseClient::appList([
                'domain'   => LicenseService::effectiveHost(),
                'code'     => (string) getMyTtKey(),
                'per_page' => 1,
            ]);
        } catch (RuntimeException $e) {
            self::$categories = [];

            return self::$categories;
        }

        $rows = isset($result['categories']) && is_array($result['categories']) ? $result['categories'] : [];
        $out = [];

        foreach ($rows as $row) {
            $out[] = [
                'id'    => (int) $row['id'],
                'title' => (string) $row['name'],
            ];
        }

        self::$categories = $out;

        return $out;
    }
}
