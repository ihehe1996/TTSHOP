<?php

require_once 'globals.php';


$chargeModel = new Charge_Model();

if (empty($action)) {

    $page = Input::getIntVar('page', 1);


    $br = '<a href="./">控制台</a><a href="./order.php">订单管理</a><a><cite>充值订单</cite></a>';
    include View::getAdmView('header');
    require_once View::getAdmView('charge');
    include View::getAdmView('footer');
    View::output();
}

if($action == 'index'){
    $page = Input::getIntVar('page', 1);
    $limit = Input::getIntVar('limit');

    $res = $chargeModel->getList(null , $page, $limit);

    $list = $res['list'];
   

    output::data($list, $res['total']);
}

if ($action == 'del') {
    $ids = Input::postStrVar('ids');
    $ids = preg_replace('/[^0-9,]/', '', $ids);
    $ids = trim($ids, ',');
    if (empty($ids)) {
        Ret::error('请选择要删除的记录');
    }
    $deleted = $chargeModel->deleteByIds($ids);
    if ($deleted <= 0) {
        Ret::error('没有可删除的记录');
    }
    Ret::success('删除成功');
}



