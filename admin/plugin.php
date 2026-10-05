<?php
/**
 * plugin management
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

$plugin = Input::getStrVar("plugin");
$filter = Input::getStrVar('filter'); // on or off

if (empty($action) && empty($plugin)) {

    $br = '<a href="./">控制台</a><a><cite>插件管理</cite></a>';

    include View::getAdmView('header');
    require_once(View::getAdmView('plugin'));
    include View::getAdmView('footer');
    View::output();
}

if ($action == 'index') {
    $Plugin_Model = new Plugin_Model();
    $plugins = $Plugin_Model->getPlugins($filter);

    // 只返回基本插件数据，不进行更新检测
    foreach($plugins as $key => $val){
        $plugins[$key]['update'] = 0; // 默认无更新
        $plugins[$key]['id'] = 0;     // 默认ID为0
    }
    
    output::data($plugins, count($plugins));
}

// 检测插件更新（2026-10-05 改走新协议，见 include/service/appupdateservice.php）
if ($action == 'checkUpdates') {
    $Plugin_Model = new Plugin_Model();
    $plugins = $Plugin_Model->getPlugins($filter);

    $check = [];
    foreach($plugins as $val){
        $check[] = [
            'name_en' => $val['Plugin'],   // 服务端认的是**目录名**（那是它的唯一标识）
            'version' => $val['Version'],
        ];
    }

    try {
        $updates = AppUpdateService::checkInstalled($check);
    } catch (RuntimeException $e) {
        /*
         * ⚠️ **查不上就如实说**。旧代码在这里 `output::data([], 0)` ——
         * 界面上就是「全部已是最新」，那是在骗人（实际根本没查上）。
         */
        output::error('查不到更新：' . $e->getMessage());
        return;
    }

    $result = [];
    foreach($plugins as $val){
        $row = AppUpdateService::updateOf($updates, $val['Plugin']);

        $result[] = [
            'plugin' => $val['Plugin'],
            'update' => $row === null ? 0 : 1,
            /* 这个 id 是**服务端那个应用的 id**，升级时拿它拼下载地址 */
            'id' => $row === null ? 0 : (int) $row['app_id'],
        ];
    }

    output::data($result, count($result));
}

if($action == 'switch'){
    LoginAuth::checkToken();
    $Plugin_Model = new Plugin_Model();
    $alias = Input::postStrVar('plugin');
    $status = Input::postIntVar('status');
    if($status == 1){
        $res = $Plugin_Model->activePlugin($alias);
    }else{
        if (strpos($alias, 'tpl_options') !== false) {
            output::error('禁止操作该插件');
        }
        $Plugin_Model->inactivePlugin($alias);
        $res = true;
    }
    if($res){
        $CACHE->updateCache('options');
        output::ok('操作成功');
    }else{
        output::error('操作失败');
    }
}


// Load plug-in configuration page
if (empty($action) && $plugin) {
    require_once "../content/plugins/$plugin/{$plugin}_setting.php";
    include View::getAdmView('header');
    plugin_setting_view();
    include View::getAdmView('footer');
}
if($action == 'setting_page'){
    $type = Input::getStrVar('type');
    if($type == 'admin'){
        $br = '<a href="./">控制台</a><a><cite>插件扩展功能</cite></a>';
    }
    require_once "../content/plugins/$plugin/{$plugin}_setting.php";
    include View::getAdmView($type == 'admin' ? 'header' : 'open_head');
    plugin_setting_view();
    include View::getAdmView($type == 'admin' ? 'footer' : 'open_foot');
}

// Save plug-in settings
if ($action == 'setting') {
    if (!empty($_POST)) {
        require_once "../content/plugins/$plugin/{$plugin}_setting.php";
        if (false === plugin_setting()) {
            ttDirect("./plugin.php?plugin={$plugin}&error=1");
        } else {
            ttDirect("./plugin.php?plugin={$plugin}&setting=1");
        }
    } else {
        ttDirect("./plugin.php?plugin={$plugin}&error=1");
    }
}



if ($action == 'del') {
    LoginAuth::checkToken();
    $plugin = Input::postStrVar('plugin');
    $Plugin_Model = new Plugin_Model();
    $Plugin_Model->inactivePlugin($plugin);
    $Plugin_Model->rmCallback($plugin);
    $path = preg_replace("/^([\w-]+)\/[\w-]+\.php$/i", "$1", $plugin);

    if ($path && true === ttDeleteFile('../content/plugins/' . $path)) {
        $CACHE->updateCache('options');
        output::ok('删除成功');
    } else {
        output::ok('删除成功');
    }
}


if ($action === 'upgrade') {
    $plugin_id = Input::postStrVar('plugin_id');
    $alias = Input::postStrVar('alias');
    if (!Register::isRegLocal()) {
        output::error('当前程序未授权，无法更新！');
    }

    /*
     * 新协议的下载地址（2026-10-05 换的）。`$plugin_id` 现在就是**服务端那个
     * 应用的 id**（`checkUpdates` 里从 `app_id` 来的）。
     *
     * ⚠️ 那两个查询参数是**身份**：付费应用服务端要「有效授权 + 买过这一款」
     * 两道门，下不来时它回 400（`ttFetchFile` 见非 200 就返回 false），
     * 下面那句「未购买该插件或更新失败」正是这个意思。
     */
    $url = LicenseClient::downloadUrl(
        (int) $plugin_id,
        LicenseService::effectiveHost(),
        (string) getMyTtKey()
    );

    $temp_file = ttFetchFile($url);

    if (!$temp_file) {
        output::error('未购买该插件或更新失败！');
    }
    $unzip_path = '../content/plugins/';
    $ret = ttUnZip($temp_file, $unzip_path, 'plugin');
    @unlink($temp_file);
    switch ($ret) {
        case 0:
            $Plugin_Model = new Plugin_Model();
            $Plugin_Model->upCallback($alias);
            output::ok();
            break;
        case 1:
        case 2:
            output::error('更新失败');
            break;
        case 3:
            output::error('更新失败');
            break;
        default:
            output::error('更新失败');
    }
}
