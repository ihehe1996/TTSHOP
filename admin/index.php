<?php
/**
 * control panel
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

if (empty($action)) {
    $db = Database::getInstance();
    $db_prefix = DB_PREFIX;

    $regType = Register::getRegType();


    $br = '<a><cite>控制台</cite></a>';

    // 销售额
    $sql = "SELECT SUM(amount) AS today_sales_amount FROM `" . DB_PREFIX . "order` WHERE delete_time IS NULL AND pay_time IS NOT NULL AND create_time >= UNIX_TIMESTAMP(CURRENT_DATE) AND create_time < UNIX_TIMESTAMP(DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY));";
    $row = $db->once_fetch_array($sql);
    $today_sales_amount = $row['today_sales_amount'] / 100;
    $sql = "SELECT SUM(amount) AS yesterday_sales_amount FROM `" . DB_PREFIX . "order` WHERE delete_time IS NULL AND pay_time IS NOT NULL AND create_time >= UNIX_TIMESTAMP(DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)) AND create_time < UNIX_TIMESTAMP(CURRENT_DATE);";
    $row = $db->once_fetch_array($sql);
    $yesterday_sales_amount = $row['yesterday_sales_amount'] / 100;
    $sql = "SELECT SUM(amount) AS current_month_sales_amount FROM `" . DB_PREFIX . "order` WHERE delete_time IS NULL AND pay_time IS NOT NULL AND create_time >= UNIX_TIMESTAMP(DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')) AND create_time < UNIX_TIMESTAMP(DATE_ADD(DATE_FORMAT(CURRENT_DATE, '%Y-%m-01'), INTERVAL 1 MONTH));";
    $row = $db->once_fetch_array($sql);
    $current_month_sales_amount = number_format($row['current_month_sales_amount'] / 100, 2);
    // 用户数量
    $sql = <<<SQL
SELECT
    -- 今日注册量
    SUM(CASE WHEN FROM_UNIXTIME(create_time) >= CURDATE() 
             AND FROM_UNIXTIME(create_time) < CURDATE() + INTERVAL 1 DAY 
             THEN 1 ELSE 0 END) AS today_registrations,
             
    -- 昨日注册量
    SUM(CASE WHEN FROM_UNIXTIME(create_time) >= CURDATE() - INTERVAL 1 DAY 
             AND FROM_UNIXTIME(create_time) < CURDATE() 
             THEN 1 ELSE 0 END) AS yesterday_registrations,
             
    -- 本月注册量
    SUM(CASE WHEN FROM_UNIXTIME(create_time) >= DATE_FORMAT(CURDATE(), '%Y-%m-01') 
             AND FROM_UNIXTIME(create_time) < CURDATE() + INTERVAL 1 DAY 
             THEN 1 ELSE 0 END) AS month_registrations
             
FROM {$db_prefix}user;
SQL;
    $user_panel = $db->once_fetch_array($sql);

    // 订单数量
    $sql = <<<SQL
SELECT
    -- 今日有效订单量
    IFNULL(SUM(CASE WHEN create_time >= UNIX_TIMESTAMP(CURDATE()) 
             AND create_time < UNIX_TIMESTAMP(CURDATE() + INTERVAL 1 DAY) 
             AND pay_time IS NOT NULL AND delete_time IS NULL
             THEN 1 ELSE 0 END), 0) AS today_orders,
             
    -- 昨日有效订单量
    IFNULL(SUM(CASE WHEN create_time >= UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 DAY) 
             AND create_time < UNIX_TIMESTAMP(CURDATE()) 
             AND pay_time IS NOT NULL AND delete_time IS NULL
             THEN 1 ELSE 0 END), 0) AS yesterday_orders,
             
    -- 本月有效订单量
    IFNULL(SUM(CASE WHEN create_time >= UNIX_TIMESTAMP(DATE_FORMAT(CURDATE(), '%Y-%m-01')) 
             AND create_time < UNIX_TIMESTAMP(CURDATE() + INTERVAL 1 DAY) 
             AND pay_time IS NOT NULL AND delete_time IS NULL
             THEN 1 ELSE 0 END), 0) AS month_orders
             
FROM {$db_prefix}order;
SQL;
    $order_panel = $db->once_fetch_array($sql);
//    d($order_panel);die;

    // server info
    $server_app = $_SERVER['SERVER_SOFTWARE'];
    $DB = Database::getInstance();
    $mysql_ver = $DB->getMysqlVersion();
    $max_execution_time = ini_get('max_execution_time') ?: '';
    $max_upload_size = ini_get('upload_max_filesize') ?: '';
    $php_ver = PHP_VERSION . ', ' . $max_execution_time . 's,' . $max_upload_size;
    $os = php_uname('s') . ' ' . php_uname('m');

    if (extension_loaded('curl')) {
        $c = curl_version();
        $php_ver .= ',curl';
    }
    if (class_exists('ZipArchive', false)) {
        $php_ver .= ',zip';
    }
    if (extension_loaded('gd')) {
        $php_ver .= ',gd';
    }

    include View::getAdmView('header');
    require_once(View::getAdmView('templates/default/index/index'));
    include View::getAdmView('footer');
    View::output();

}

if($action == 'jujue_mianze_ajax'){
    if(Input::postStrVar('jujue')){
        Option::updateOption('mianze', 0);
        $CACHE->updateCache('options');
        output::ok();
    }
    output::error('非法请求！');
}

if($action == 'mianze_ajax'){
    if(Input::postStrVar('mianze')){
        Option::updateOption('mianze', time());
        $CACHE->updateCache('options');
        output::ok();
    }
    output::error('非法请求！');
}

if($action == 'mianze'){

    $chakan = Input::getIntVar('chakan');

    include View::getAdmView('open_head');
    require_once View::getAdmView('templates/default/index/mianze');
    include View::getAdmView('open_foot');
    View::output();
}

if ($action === 'add_shortcut') {
    $shortcut = Input::postStrArray('shortcut');
    $shortcutSet = [];
    foreach ($shortcut as $item) {
        $item = explode('||', $item);
        $shortcutSet[] = [
            'name' => $item[0],
            'url'  => $item[1]
        ];
    }
    Option::updateOption('shortcut', json_encode($shortcutSet, JSON_UNESCAPED_UNICODE));
    $CACHE->updateCache('options');
    ttDirect("./index.php?add_shortcut_suc=1");
}

if($action == 'delete_cache'){
    $dir = TT_ROOT . "/content/cache";
    // 检查目录是否存在
    if (!is_dir($dir)) {
        output::error('缓存目录不存在');
    }

    // 打开目录
    $handle = opendir($dir);
    if (!$handle) {
        output::error('缓存目录无权限');
    }

    // 遍历目录
    while (false !== ($file = readdir($handle))) {
        // 跳过当前目录（.）和上级目录（..）
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $file;

        // 如果是文件则删除
        if (is_file($filePath)) {
            unlink($filePath); // 删除文件
        }
        // 如果是子目录，可以根据需求决定是否递归删除（这里仅删除文件，不处理子目录）
        // else if (is_dir($filePath)) {
        //     // 递归删除子目录下的文件（可选）
        //     deleteDirFiles($filePath);
        // }
    }

    // 关闭目录句柄
    closedir($handle);
    output::ok();
}

/**
 * 更新线路
 */
if($action == 'update_line'){
    $line = Input::postIntVar('line');

    Option::updateOption('tt_line', $line);
    $CACHE->updateCache('options');
    Ret::success('操作成功');
}

/**
 * 获取广告位等信息
 */
if($action == 'admin_index'){
    $default_token = '89Z78A9S7D8F9G7H8J9K8L7M9N8B7V8C9X8Z76T54R32E1WQ';
    $service_token = (defined('SERVICE_TOKEN') && SERVICE_TOKEN) ? SERVICE_TOKEN : $default_token;
    $data = [
        'service_token' => $service_token,
    ];
    $url = TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=admin_index';

//    echo $url;die;

    $res = ttCurl($url, $data, true, [], 10);
    if(empty($res)){
        Log::warning('本次更新服务连通失败，如多次遇到此问题，建议更换其他线路重试！');
        Ret::error('网络请求超时，请重试或更换其他线路');
    }
    $res = json_decode($res, true);
    if($res['code'] == 200){
        Ret::success('', $res['data']);
    }else{
        Ret::error($res['msg']);
    }
    
}

/**
 * 获取购买授权信息
 */
if($action == 'get_em_buy_info'){
    $default_token = '89Z78A9S7D8F9G7H8J9K8L7M9N8B7V8C9X8Z76T54R32E1WQ';
    $service_token = (defined('SERVICE_TOKEN') && SERVICE_TOKEN) ? SERVICE_TOKEN : $default_token;
    $data = [
        'service_token' => $service_token
    ];
    $res = ttCurl(TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=get_em_buy_info', $data, true, [], 10);
    // var_dump($res);die;
    if(empty($res)){
        Ret::error('网络请求超时，请重试或更换其他线路');
    }
    $res = json_decode($res, true);
    
    Ret::success('success', $res['data']);
}

if($action == 'get_download_url'){
    $default_token = '89Z78A9S7D8F9G7H8J9K8L7M9N8B7V8C9X8Z76T54R32E1WQ';
    $service_token = (defined('SERVICE_TOKEN') && SERVICE_TOKEN) ? SERVICE_TOKEN : $default_token;
    $data = [
        'service_token' => $service_token
    ];
    $res = ttCurl(TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=get_em_buy_info', $data, true, [], 10);
    if(empty($res)){
        Ret::error('网络请求超时，请重试或更换其他线路');
    }
    $res = json_decode($res, true);
    Ret::success('success', $res['data']);
}

