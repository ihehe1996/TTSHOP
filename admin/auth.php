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
    $emkeyInfo =  empty($res) ? false : $res;
    $emkey = $emkeyInfo ? $emkeyInfo['emkey'] : false;

    if($emkeyInfo){

        Register::isRegServer();

        $emkey_type = '未授权';

        if($emkeyInfo['type'] == 1){
            $emkey_type = 'VIP授权';
        }
        if($emkeyInfo['type'] == 2){
            $emkey_type = 'SVIP授权';
        }
        if($emkeyInfo['type'] == 3){
            $emkey_type = '至尊授权';
        }

    }
    
    

    include View::getAdmView('header');
    require_once(View::getAdmView('auth'));
    include View::getAdmView('footer');
    View::output();
}

if ($action === 'auth') {
    $emkey = Input::postStrVar('emkey');
    if (empty($emkey)) {
        Ret::error('请输入授权码');
    }
    $r = Register::doReg($emkey);



    
    if($r['code'] == 200){

        $emkey_type = $r['data'];

        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $domain = getTopHost();

        $sql = "INSERT INTO `{$db_prefix}authorization` (`emkey`, `domain`, `type`) VALUES ('{$emkey}', '{$domain}', '{$emkey_type}');";

        $db->query($sql);

        Ret::success();
    }else{
        Ret::error($r['msg']);
    }

    if ($r === false) {
        Ret::error('授权码错误或请求授权失败，请重试');
    }

    Ret::success();
}
