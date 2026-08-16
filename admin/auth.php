<?php


/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

if (empty($action)) {

    $br = '<a href="./">控制台</a><a><cite>正版授权</cite></a>';

    $db = Database::getInstance();
    $db_prefix = DB_PREFIX;
    $domain = getTopHost();
    $sql = "select * from {$db_prefix}authorization where domain='{$domain}'";
    $res = $db->once_fetch_array($sql);
    $ttkeyInfo =  empty($res) ? false : $res;
    $ttkey = $ttkeyInfo ? $ttkeyInfo['ttkey'] : false;

    if($ttkeyInfo){

        Register::isRegServer();

        $ttkey_type = '未授权';

        if($ttkeyInfo['type'] == 1){
            $ttkey_type = 'VIP授权';
        }
        if($ttkeyInfo['type'] == 2){
            $ttkey_type = 'SVIP授权';
        }
        if($ttkeyInfo['type'] == 3){
            $ttkey_type = '至尊授权';
        }

    }
    
    

    include View::getAdmView('header');
    require_once(View::getAdmView('auth'));
    include View::getAdmView('footer');
    View::output();
}

if ($action === 'auth') {
    $ttkey = Input::postStrVar('ttkey');
    if (empty($ttkey)) {
        Ret::error('请输入授权码');
    }
    $r = Register::doReg($ttkey);



    
    if($r['code'] == 200){

        $ttkey_type = $r['data'];

        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $domain = getTopHost();

        $sql = "INSERT INTO `{$db_prefix}authorization` (`ttkey`, `domain`, `type`) VALUES ('{$ttkey}', '{$domain}', '{$ttkey_type}');";

        $db->query($sql);

        Ret::success();
    }else{
        Ret::error($r['msg']);
    }

    if ($r === false) {
        Ret::error('授权码错误或请求授权失败，请重试');
    }

    Ret::success('授权成功');
}
