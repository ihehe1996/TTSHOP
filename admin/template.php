<?php

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

$Template_Model = new Template_Model();

if ($action === '') {

    $br = '<a href="./">控制台</a><a href="./template.php">外观设置</a><a><cite>模板主题</cite></a>';

    include View::getAdmView('header');
    require_once View::getAdmView('templates/default/template/index');
    include View::getAdmView('footer');
    View::output();
}

if($action == 'index'){
    $list = $Template_Model->getTemplates();
    $nonce_template = Option::get('nonce_templet');
    $nonce_template_tel = Option::get('nonce_templet_tel');
    foreach($list as $key => $val){
        if($nonce_template == $val['tplfile']){
            $list[$key]['switch'] = 'y';
        }else{
            $list[$key]['switch'] = 'n';
        }
        if($nonce_template_tel == $val['tplfile']){
            $list[$key]['tel_switch'] = 'y';
        }else{
            $list[$key]['tel_switch'] = 'n';
        }
        // Initialize update status as 'n' (no update) - will be checked asynchronously
        $list[$key]['update'] = 'n';
        $list[$key]['id'] = '';
        $list[$key]['preview'] = '';
    }
    
    output::data($list, count($list));
}

// 检测模板更新（2026-10-05 改走新协议，和插件那边共用 AppUpdateService）
if($action == 'checkUpdates'){
    $list = $Template_Model->getTemplates();

    $check = [];
    foreach($list as $key => $val){
        $check[] = [
            'name_en' => $val['tplfile'],   // 服务端认的是**目录名**（它的唯一标识）
            'version' => $val['version'],
        ];
    }

    try {
        $updates = AppUpdateService::checkInstalled($check);
    } catch (RuntimeException $e) {
        /* ⚠️ 查不上就如实说（旧的写法是当「全部已是最新」，那是在骗人）*/
        output::error('查不到更新：' . $e->getMessage());
        return;
    }

    $result = [];
    foreach($list as $key => $val){
        $row = AppUpdateService::updateOf($updates, $val['tplfile']);

        /* ⚠️ 这里的 update 是**字符串 y / n**（视图那边是按它判的），
           别顺手改成插件的 0 / 1 —— 两页的写法本来就不一样 */
        $template_info = [
            'tplfile' => $val['tplfile'],
            'update' => $row === null ? 'n' : 'y',
        ];

        if ($row !== null) {
            $template_info['id'] = (int) $row['app_id'];
        }

        $result[] = $template_info;
    }

    output::data($result, count($result));
}

if ($action === 'use') {
    LoginAuth::checkToken();
    $tplName = Input::postStrVar('tpl');
    Option::updateOption('nonce_templet', $tplName);
    $CACHE->updateCache('options');
    $Template_Model->initCallback($tplName);
    output::ok();
}

if ($action === 'use_tel') {
    LoginAuth::checkToken();
    $tplName = Input::postStrVar('tpl');
    Option::updateOption('nonce_templet_tel', $tplName);
    $CACHE->updateCache('options');
    $Template_Model->initCallback($tplName);
    output::ok();
}

if ($action === 'del') {
    LoginAuth::checkToken();
    $tpls = Input::postStrVar('ids');
    $tpls = explode(',', $tpls);
    foreach($tpls as $val){
        $Template_Model->rmCallback($val);
        $path = preg_replace("/^([\w-]+)$/i", "$1", $val);
        ttDeleteFile(TPLS_PATH . $path);
    }
    output::ok();
}

if($action == 'setting_page'){
    $tpl = Input::getStrVar('tpl');
    include View::getAdmView('open_head');
    require_once "../content/templates/$tpl/setting.php";
    plugin_setting_view();
    include View::getAdmView('open_foot');
}
if($action == 'setting_ajax'){
    $tpl = Input::getStrVar('tpl');
    require_once "../content/templates/$tpl/setting.php";
    plugin_setting($tpl);
}



if ($action === 'upgrade') {
    $plugin_id = Input::postStrVar('plugin_id');
    $alias = Input::postStrVar('alias');
    if (!Register::isRegLocal()) {
        Ret::error('未授权版本无法更新');
    }
    /* 新协议的下载地址（2026-10-05 换的）：`$plugin_id` 就是服务端那个应用的 id */
    $url = LicenseClient::downloadUrl(
        (int) $plugin_id,
        LicenseService::effectiveHost(),
        (string) getMyTtKey()
    );

    $temp_file = ttFetchFile($url);
    if (!$temp_file) {
        Ret::error('更新包下载失败');
    }
    $unzip_path = '../content/templates/';
    $ret = ttUnZip($temp_file, $unzip_path, 'tpl');
    @unlink($temp_file);
    switch ($ret) {
        case 0:
            $Template_Model->upCallback($alias);
            Ret::success();
            break;
        case 1:
        case 2:
        Ret::error('更新失败');
            break;
        case 3:
            Ret::error('更新失败');
            break;
        default:
            Ret::error('更新失败');
    }
}
