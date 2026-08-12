<?php

require_once 'globals.php';


$withdrawModel = new Withdraw_Model();

if (empty($action)) {

    $page = Input::getIntVar('page', 1);


    $br = '<a href="./">控制台</a><a href="./order.php">订单管理</a><a><cite>提现申请</cite></a>';
    include View::getAdmView('header');
    require_once View::getAdmView('withdraw');
    include View::getAdmView('footer');
    View::output();
}

if($action == 'index'){
    $page = Input::getIntVar('page', 1);
    $limit = Input::getIntVar('limit');

    $res = $withdrawModel->getWithdrawList(null , $page, $limit);

    output::data($res['list'], $res['total']);
}

if ($action == 'detail') {
    $id = Input::getIntVar('id');
    $withdraw = $withdrawModel->getWithdrawDetail($id);

    include View::getAdmView('open_head');
    require_once View::getAdmView('withdraw_detail');
    include View::getAdmView('open_foot');
    View::output();
}

if($action == 'cmd'){
    $type = Input::getStrVar('type');
    $id = Input::postIntVar('ids');
    $withdrawModel = new Withdraw_Model();
    if($type == 'finish'){
        $withdrawModel->pass($id);
    }
    if($type == 'reject'){
        $withdrawModel->reject($id);
    }
    Ret::success('操作成功');
}

if ($action == 'del') {
    $ids = Input::postStrVar('ids');
    $ids = preg_replace('/[^0-9,]/', '', $ids);
    $ids = trim($ids, ',');
    if (empty($ids)) {
        Ret::error('请选择要删除的记录');
    }
    $deleted = $withdrawModel->deleteByIds($ids);
    if ($deleted <= 0) {
        Ret::error('没有可删除的记录');
    }
    Ret::success('删除成功');
}


