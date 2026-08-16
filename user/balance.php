<?php
/**
 * The productf management
 *
 * @package TTSHOP
 * @link https://www.emlog.net
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';
//require_once '../init.php';

$orderModel = new Order_Model();
$Sort_Model = new Sort_Model();
$User_Model = new User_Model();
$MediaSort_Model = new MediaSort_Model();
$Template_Model = new Template_Model();

$sta_cache = $CACHE->readCache('sta');
$action = Input::getStrVar('action');


if (empty($action)) {

    $userModel = new User_Model();
    $user = $userModel->getOneUser(UID);

    include View::getUserView('_header');
    require_once View::getUserView('balance');
    include View::getUserView('_footer');
    View::output();
}

if($action == 'withdraw_ajax'){
    $db = Database::getInstance();

    // 获取用户信息
    $userModel = new User_Model();
    $user = $userModel->getOneUser(UID);

    // 获取提现参数
    $amount = Input::postStrVar('amount'); // 提现金额
    $method = Input::postStrVar('method'); // 提现方式
    $account = Input::postStrVar('account'); // 账户信息
    $realname = Input::postStrVar('realname'); // 真实姓名
    $remark = Input::postStrVar('remark'); // 备注

    // 参数验证
    if (empty($amount) || $amount <= 0) {
        Ret::error('请输入有效的提现金额');
    }
    if($amount < 10){
        Ret::error('提现金额不能小于10元');
    }

    if (empty($method)) {
        Ret::error('请选择提现方式');
    }

    if (empty($account)) {
        Ret::error('请输入提现账户');
    }

    if (empty($realname)) {
        Ret::error('请输入真实姓名');
    }

    // 检查余额是否足够
    if ($user['money'] < $amount) {
        Ret::error('余额不足，无法提现');
    }

    $balanceModel = new Balance_Model();

    // 开始事务处理
    try {
        $db->beginTransaction();

        // 扣除用户余额
        $balanceModel->dec(UID, $amount, '余额提现');

        // 添加提现申请记录
        $withdraw_data = [
            'user_id' => UID,
            'amount' => $amount,
            'method' => $method,
            'account' => $account,
            'realname' => $realname,
            'remark' => $remark,
            'status' => 0, // 0:待审核 1:已处理 -1:已拒绝
            'create_time' => time()
        ];
        $db->add('withdraw', $withdraw_data);

        $db->commit();
        Ret::success('提现申请已提交，请等待审核');
    } catch (Exception $e) {
        $db->rollback();
        Ret::error('提现申请提交失败：' . $e->getMessage());
    }
}

if ($action == 'withdraw') {
    $db = Database::getInstance();
    $db_prefix = DB_PREFIX;
    $sql = "select * from " . DB_PREFIX . "user where uid=" . UID;
    $user = $db->once_fetch_array($sql);
    $user_id = UID;
    $log = $db->fetch_all("select * from {$db_prefix}balance_log where user_id={$user_id} order by id desc limit 10");

    include View::getUserView('open_head');
    require_once View::getUserView('withdraw');
    include View::getUserView('open_foot');
    View::output();
}

if($action == 'recharge'){
    $amount = Input::postStrVar('amount');
    $method = Input::postStrVar('method');
    $payment_name = Input::postStrVar('payment_name');
    if($amount <= 0){
        Ret::error('请输入有效的充值金额');
    }
    $timestamp = time();
    $data = [
        'user_id' => UID,
        'amount' => $amount * 100,
        'pay_plugin' => $method,
        'payment' => $payment_name,
        'out_trade_no' => 'cz' . date('YmdHis') . rand(1000, 9999),
        'create_time' => $timestamp,
        'expire_time' => $timestamp + 600
    ];
    $db = Database::getInstance();
    $db->add('charge', $data);
    Ret::success('充值订单创建成功', ['out_trade_no' => $data['out_trade_no']]);
}

if ($action == 'withdraw_index') {


    include View::getUserView('_header');
    require_once View::getUserView('withdraw_index');
    include View::getUserView('_footer');
    View::output();
}

if ($action == 'withdraw_list') {
    $page = Input::getIntVar('page', 1);
    $limit = Input::getIntVar('limit', 10);
    $withdrawModel = new Withdraw_Model();

    $res = $withdrawModel->getWithdrawList(UID, $page, $limit);
    output::data($res['list'], $res['total']);
}


if($action == 'index'){
    $page = Input::getIntVar('page', 1);
    $limit = Input::getIntVar('limit', 10);
    $start = ($page - 1) * $limit;

    $user_id = UID;

    $db = Database::getInstance();
    $db_prefix = DB_PREFIX;

    $list = $db->fetch_all("select * from {$db_prefix}balance_log where user_id={$user_id} order by id desc limit $start, $limit");
    $res = $db->once_fetch_array("select count(id) total from {$db_prefix}balance_log where user_id={$user_id}");

    foreach($list as &$val){
        $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        $val['type'] = $val['plus'] == 'n' ? '减少' : '增加';
    }

    output::data($list, $res['total']);
}
