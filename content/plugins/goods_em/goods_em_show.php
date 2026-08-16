<?php
/**
 * TTSHOP 同系统对接插件 - 后台路由处理
 */

defined('TT_ROOT') || exit('access denied!');

if (!function_exists('goodsTtOutputJson')) {
    function goodsTtOutputJson($code, $msg, $data = [], $count = 0)
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'count' => $count
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$action = $_REQUEST['action'] ?? '';
$ignoreActions = ['setting_page', 'setting'];

if ($action && !in_array($action, $ignoreActions, true)) {
    if (ROLE !== 'admin' && ROLE !== 'editor') {
        goodsTtOutputJson(1, '无权限访问');
    }

    switch ($action) {
        case 'site_list':
            $refresh = !empty($_POST['refresh']);
            $sites = ttGetSiteList($refresh);
            goodsTtOutputJson(0, 'success', $sites, count($sites));
            break;

        case 'site_save':
            $data = [
                'id' => Input::postIntVar('id'),
                'domain' => Input::postStrVar('domain'),
                'app_id' => Input::postStrVar('app_id'),
                'app_key' => Input::postStrVar('app_key')
            ];
            $result = ttSaveSite($data);
            if ($result['success']) {
                goodsTtOutputJson(0, $result['message'], ['id' => $result['id'] ?? 0]);
            } else {
                goodsTtOutputJson(1, $result['message']);
            }
            break;

        case 'site_delete':
            $siteId = Input::postIntVar('id');
            if (ttDeleteSite($siteId)) {
                goodsTtOutputJson(0, '删除成功');
            } else {
                goodsTtOutputJson(1, '删除失败，该站点可能有关联商品');
            }
            break;

        case 'import_goods':
            $params = [
                'site_id' => Input::postIntVar('site_id'),
                'goods_ids' => $_POST['goods_ids'] ?? [],
                'sort_id' => Input::postIntVar('sort_id'),
                'raise_type' => Input::postStrVar('raise_type', 'percent'),
                'raise_value' => floatval($_POST['raise_value'] ?? 10)
            ];
            $result = ttImportGoods($params);
            goodsTtOutputJson(0, '导入完成', $result);
            break;
        case 'sync_stock':
            $goodsId = Input::postIntVar('goods_id');
            $db = Database::getInstance();
            $goods = $db->once_fetch_array("SELECT * FROM " . DB_PREFIX . "goods WHERE id = " . (int)$goodsId);
            if (!$goods) {
                goodsTtOutputJson(1, '商品不存在');
            }
            if (ttSyncSku($goods)) {
                goodsTtOutputJson(0, '同步成功');
            } else {
                goodsTtOutputJson(1, '同步失败');
            }
            break;
        case 'get_stock':
            $goodsId = Input::postIntVar('goods_id');
            $db = Database::getInstance();
            $goods = $db->once_fetch_array("SELECT * FROM " . DB_PREFIX . "goods WHERE id = " . (int)$goodsId);
            if (!$goods) {
                goodsTtOutputJson(1, '商品不存在');
            }
            if (!in_array($goods['type'], ['em_auto', 'em_manual'])) {
                goodsTtOutputJson(1, '非对接商品');
            }
            $skus = $db->fetch_all("SELECT option_ids, stock, sales, cost_price, user_price FROM " . DB_PREFIX . "product_sku WHERE goods_id = " . (int)$goodsId);
            $data = [];
            foreach ($skus as $sku) {
                $optionIds = $sku['option_ids'];
                if ($optionIds == '0') {
                    $optionText = '默认';
                } else if (function_exists('ttFormatSkuOptionIds')) {
                    $optionText = rtrim(ttFormatSkuOptionIds($goodsId, $optionIds), '；');
                } else {
                    $optionText = $optionIds;
                }
                $data[] = [
                    'option_ids' => $optionIds,
                    'option_text' => $optionText,
                    'stock' => (int)$sku['stock'],
                    'sales' => (int)$sku['sales'],
                    'cost_price' => number_format($sku['cost_price'] / 100, 2),
                    'user_price' => number_format($sku['user_price'] / 100, 2)
                ];
            }
            goodsTtOutputJson(0, 'success', $data, count($data));
            break;

        case 'query_order':
            $orderListId = Input::postIntVar('order_list_id');
            $db = Database::getInstance();

            $childOrder = $db->once_fetch_array("SELECT * FROM " . DB_PREFIX . "order_list WHERE id = " . (int)$orderListId);
            if (!$childOrder || empty($childOrder['remote_trade_no'])) {
                goodsTtOutputJson(1, '订单不存在或无远程订单号');
            }

            $goods = $db->once_fetch_array("SELECT * FROM " . DB_PREFIX . "goods WHERE id = " . (int)$childOrder['goods_id']);
            if (!$goods) {
                goodsTtOutputJson(1, '商品不存在');
            }

            $ttGoods = $db->once_fetch_array("SELECT * FROM " . DB_PREFIX . "em_goods WHERE goods_id = " . (int)$goods['id']);
            if (!$ttGoods) {
                goodsTtOutputJson(1, '对接信息不存在');
            }

            $site = ttGetSite($ttGoods['site_id']);
            if (!$site) {
                goodsTtOutputJson(1, '对接站点不存在');
            }

            $api = TtApi::fromSite($site);
            $result = $api->query($childOrder['remote_trade_no']);

            if ($result) {
                if (isset($result['status']) && $result['status'] == 2 && !empty($result['secret'])) {
                    $secrets = TtApi::parseSecrets($result['secret']);
                    $timestamp = time();

                    foreach ($secrets as $secret) {
                        $db->query("INSERT INTO " . DB_PREFIX . "stock_usage
                            (stock_id, order_id, order_list_id, content, create_time)
                            VALUES (0, {$childOrder['order_id']}, {$childOrder['id']}, '{$db->escape_string($secret)}', {$timestamp})");
                    }

                    $db->query("UPDATE " . DB_PREFIX . "order_list SET status = 2 WHERE id = {$childOrder['id']}");
                    $db->query("UPDATE " . DB_PREFIX . "order SET status = 2 WHERE id = {$childOrder['order_id']}");
                }

                goodsTtOutputJson(0, '查询成功', $result);
            } else {
                goodsTtOutputJson(1, '查询失败：' . $api->getLastError());
            }
            break;
        default:
            goodsTtOutputJson(1, '未知操作');
    }
}

$action = Input::getStrVar('do', 'site');

switch ($action) {
    case 'site':
        // 站点管理
        include __DIR__ . '/views/site_index.php';
        break;

    case 'site_form':
        // 站点添加/编辑
        $siteId = Input::getIntVar('id');
        $site = $siteId ? ttGetSite($siteId) : null;
        include __DIR__ . '/views/site_form.php';
        break;

    case 'import':
        // 商品导入
        $siteId = Input::getIntVar('site_id');
        $site = ttGetSite($siteId);
        if (!$site) {
            echo '<script>layer.msg("站点不存在");history.back();</script>';
            exit;
        }
        include __DIR__ . '/views/import.php';
        break;
    default:
        include __DIR__ . '/views/site_index.php';
        break;
}
