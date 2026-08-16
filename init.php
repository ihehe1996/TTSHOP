<?php

ob_start();
header('Content-Type: text/html; charset=UTF-8');

const TT_ROOT = __DIR__;
const EM_ROOT = __DIR__; // 保留旧常量，兼容其他模板/插件

require_once TT_ROOT . '/config.php';
require_once TT_ROOT . '/base.php';
require_once TT_ROOT . '/include/lib/common.php';



if (getenv('TT_ENV') === 'develop' || (defined('ENVIRONMENT') && ENVIRONMENT === 'develop')) {
    // 显示所有错误（包括警告、通知等）
    error_reporting(E_ALL);
} else {
    error_reporting(1);
}



if (extension_loaded('mbstring')) {
    mb_internal_encoding('UTF-8');
}

spl_autoload_register("ttAutoload");

$CACHE = Cache::getInstance();
$userData = [];

define('ISLOGIN', LoginAuth::isLogin());

date_default_timezone_set(Option::get('timezone'));

const ROLE_ADMIN = 'admin';
const ROLE_EDITOR = 'editor';
const ROLE_WRITER = 'writer';
const ROLE_VISITOR = 'visitor';


define('ROLE', ISLOGIN === true ? $userData['role'] : User::ROLE_VISITOR);
define('UID', ISLOGIN === true ? (int)$userData['uid'] : 0);
define('LEVEL', ISLOGIN === true ? $userData['level'] : -1); // 用户等级

define('TT_URL', realUrl()); // 当前网址
define('TT_DOMAIN', getDomain()); // 当前域名
define('TIMESTAMP', time()); // 当前时间戳





// 保存本地身份标识
if(isset($_COOKIE['TT_LOCAL'])){
    define('TT_LOCAL', strip_tags($_COOKIE['TT_LOCAL']));
}else{
    define('TT_LOCAL', generateUUIDv4());
}
// 每次访问都更新cookie过期时间
setcookie('TT_LOCAL', TT_LOCAL, time() + 3600*24*365, '/');




const TPLS_PATH = TT_ROOT . '/content/templates/';
const TPLS_STATION_PATH = TPLS_PATH;
const PLUGIN_URL = TT_URL . 'content/plugins/';
const PLUGIN_PATH = TT_ROOT . '/content/plugins/';
const LOG_PATH = TT_ROOT . '/content/log/';

$stationModel = new Station_Model();

define('STATION_DATA', $stationModel->getStationInfo());


if(STATION_DATA){
    $nonce_templet = isMobile() ? STATION_DATA['tel_tpl']: STATION_DATA['pc_tpl'];
    define("TPLS_URL", TT_URL . 'content/templates/');
    define('TEMPLATE_PATH', TPLS_PATH . $nonce_templet . '/');
}else{
    $nonce_templet = isMobile() ? Option::get('nonce_templet_tel') : Option::get('nonce_templet');
    define("TPLS_URL", TT_URL . 'content/templates/');
    define('TEMPLATE_PATH', TPLS_PATH . $nonce_templet . '/');
}



//站点URL
define('DYNAMIC_BLOGURL', Option::get('blogurl'));
//当前模板的URL
define('TEMPLATE_URL', TPLS_URL . $nonce_templet . '/');
define('BLOG_TEMPLATE_URL', TT_URL . 'content/blog/default/');
//后台模板的绝对路径
define('ADMIN_TEMPLATE_PATH', TT_ROOT . '/admin/views/');
define('USER_TEMPLATE_PATH', TT_ROOT . '/user/views/');
//前台模板的绝对路径

define('BLOG_TEMPLATE_PATH', TT_ROOT . '/content/blog/default/');
define('COMMON_TEMPLATE_PATH', TT_ROOT . '/content/common/');

const MSGCODE_TTKEY_INVALID = 1001;
const MSGCODE_NO_UPUPDATE = 1002;
const MSGCODE_SUCCESS = 200;

const TT_LINE = [
    ['name' => '官方线路', 'value' => 'https://emshop.ihehe.me/'],
    ['name' => '备用线路', 'value' => 'http://154.44.8.63:10000/'],
//    ['name' => '测试线路(无效)', 'value' => 'http://admin.em.cc/'],
];
$options_cache = $CACHE->readCache('options');
define('CURRENT_LINE', empty($options_cache['tt_line']) || empty(TT_LINE[$options_cache['tt_line']]) ? 0 : $options_cache['tt_line']);

$active_plugins = Option::get('active_plugins');
$ttHooks = [];
if ($active_plugins && is_array($active_plugins)) {
    foreach ($active_plugins as $plugin) {
        if (true === checkPlugin($plugin)) {
            include_once(TT_ROOT . '/content/plugins/' . $plugin);
        }
    }
}


// 加载模板的系统调用文件
define('TEMPLATE_HOOK_PATH', TEMPLATE_PATH . 'plugins.php');
if (file_exists(TEMPLATE_HOOK_PATH)) {
    include_once(TEMPLATE_HOOK_PATH);
}


if (defined('DEMO') && DEMO === true) {

    $method = $_SERVER['REQUEST_METHOD'];
    $action = Input::getStrVar('action');


    if($action == 'delete'){
        output::error('演示站点无法进行该操作！');
    }
    if($action == 'upload'){
        header("HTTP/1.0 400 Bad Request");
        exit('演示站点无法进行该操作！');
    }
    if($method == 'POST'){
        if($action != 'check_update'){
            output::error('演示站点无法进行该操作！');
        }

    }

}
if (!class_exists('TplOptions', false)) {
    include __DIR__ . '/include/lib/tpl_options.php';

}
TplOptions::getInstance()->init();

if(defined('DEMO_MODE') && DEMO_MODE === true && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = Input::getStrVar('action');
    $allow = [
        'dosignin', 'goods_price_stock', 'upgrade'
    ];
    if(!in_array($action, $allow)){
        if($action === 'update' && basename($_SERVER['PHP_SELF']) === 'upgrade.php'){
            // 允许 upgrade.php 的 update 操作
        } else {
            Ret::error('当前演示站禁止该操作');
        }
    }

}

if(defined('DEMO_MODE') && DEMO_MODE === true && $_SERVER['REQUEST_METHOD'] === 'GET'){
    $action = Input::getStrVar('action');
    $noAllow = [
        'update'
    ];
    if(in_array($action, $noAllow)){
        Ret::error('当前演示站禁止该操作');
    }
    $filename = Input::getStrVar('filename');
    if(!empty($filename) || $action == 'download'){
        ttMsg('当前演示站禁止该操作', 'javascript:window.close();');
    }

}

