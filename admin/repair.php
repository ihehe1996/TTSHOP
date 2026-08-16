<?php


require_once 'globals.php';


if(empty($action)){
    $br = '<a href="./">控制台</a><a><cite>TTSHOP 修复系统</cite></a>';
    include View::getAdmView('header');
    require_once View::getAdmView('repair');
    include View::getAdmView('footer');
    View::output();
}

if($action == 'get_repair_code'){
    $url = TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=get_repair_code';
//    echo $url;die;
    $res = ttCurl($url, false, 0, false, 10);
//    var
    if(empty($res)){
        Ret::error('请尝试切换其他官方线路');
    }
    $res = json_decode($res, true);
    $repair_em_filepath = TT_ROOT . '/admin/repair_em.php';
    if(file_exists($repair_em_filepath)){
        unlink($repair_em_filepath);
    }
    file_put_contents($repair_em_filepath, $res['data']);
    Ret::success('success');
    var_dump($res);
}