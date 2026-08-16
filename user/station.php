<?php


require_once 'globals.php';

$orderModel = new Order_Model();
$Sort_Model = new Sort_Model();
$User_Model = new User_Model();
$MediaSort_Model = new MediaSort_Model();

$sta_cache = $CACHE->readCache('sta');
$action = Input::getStrVar('action');

$db = Database::getInstance();
$db_prefix = DB_PREFIX;

$station_switch = Option::get('station_switch');
if ($station_switch === 'n') {
    ttMsg('分站系统已关闭');
}

$default_premium = Option::get('station_default_premium');
$default_premium = is_numeric($default_premium) ? (float)$default_premium : 0.1;
if ($default_premium < 0) {
    $default_premium = 0.1;
}

if (empty($action)) {
    $db = Database::getInstance();
    $db_prefix = DB_PREFIX;
    global $userData;

    $station_id = $userData['station']['id'];
    $level_id = $userData['station']['level_id'];

    $station = $db->once_fetch_array("select * from {$db_prefix}station_level where id = {$level_id}");

    $my_station = $db->once_fetch_array("select * from {$db_prefix}station where delete_time IS NULL AND id = {$station_id}");


    
    // 获取订单统计数据
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $month_start = date('Y-m-01');
    
    // 今日订单数和金额
    $today_sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as amount FROM {$db_prefix}order 
                  WHERE station_id = {$station_id} AND pay_time IS NOT NULL 
                  AND DATE(FROM_UNIXTIME(pay_time)) = '{$today}'";
    $today_data = $db->once_fetch_array($today_sql);
    $today_orders = $today_data['count'];
    $today_amount = $today_data['amount'] / 100; // 转换为元
    
    // 昨日订单数和金额
    $yesterday_sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as amount FROM {$db_prefix}order 
                      WHERE station_id = {$station_id} AND pay_time IS NOT NULL 
                      AND DATE(FROM_UNIXTIME(pay_time)) = '{$yesterday}'";
    $yesterday_data = $db->once_fetch_array($yesterday_sql);
    $yesterday_orders = $yesterday_data['count'];
    $yesterday_amount = $yesterday_data['amount'] / 100; // 转换为元
    
    // 本月订单数和金额
    $month_sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as amount FROM {$db_prefix}order 
                  WHERE station_id = {$station_id} AND pay_time IS NOT NULL 
                  AND DATE(FROM_UNIXTIME(pay_time)) >= '{$month_start}'";
    $month_data = $db->once_fetch_array($month_sql);
    $month_orders = $month_data['count'];
    $month_amount = $month_data['amount'] / 100; // 转换为元
    
    // 总订单数和金额
    $total_sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as amount FROM {$db_prefix}order 
                  WHERE station_id = {$station_id} AND pay_time IS NOT NULL";
    $total_data = $db->once_fetch_array($total_sql);
    $total_orders = $total_data['count'];
    $total_amount = $total_data['amount'] / 100; // 转换为元
    
   

    include View::getUserView('_header');
    require_once View::getUserView('station/index');
    include View::getUserView('_footer');
    View::output();
}

if($action == 'bill'){ // 店铺账单
    global $userData;
    if (empty($userData['station'])) {
        ttMsg('您还未开通分站，请先开通分站');
    }

    $station_id = $userData['station']['id'];
    $level_id = $userData['station']['level_id'];

    $station_level = $db->once_fetch_array("select * from {$db_prefix}station_level where id = {$level_id}");
    $my_station = $db->once_fetch_array("select * from {$db_prefix}station where delete_time is null and id = {$station_id}");
    $user = $db->once_fetch_array("select * from {$db_prefix}user where uid = " . UID);

    $cash_change = isset($station_level['cash_change']) && is_numeric($station_level['cash_change']) ? (float)$station_level['cash_change'] : 0;
    if ($cash_change < 0) {
        $cash_change = 0;
    }
    $cash_change_percent = $cash_change * 100;
    $cash_change_percent_text = rtrim(rtrim(sprintf('%.4f', $cash_change_percent), '0'), '.');

    $station_money = isset($my_station['money']) && is_numeric($my_station['money']) ? (float)$my_station['money'] : 0;
    $user_money = isset($user['money']) && is_numeric($user['money']) ? (float)$user['money'] : 0;

    include View::getUserView('_header');
    require_once View::getUserView('station/bill');
    include View::getUserView('_footer');
    View::output();
}
if($action == 'bill_index'){
    $page = Input::getIntVar('page', 1);
    $limit = Input::getIntVar('limit', 10);
    $start = ($page - 1) * $limit;

    $user_id = UID;
    $list = $db->fetch_all("select * from {$db_prefix}station_bill where user_id={$user_id} order by id desc limit $start, $limit");
    $res = $db->once_fetch_array("select count(id) total from {$db_prefix}station_bill where user_id={$user_id}");

    foreach($list as &$val){
        $money_val = (float)$val['money'];
        $val['flow'] = $money_val >= 0 ? 'plus' : 'minus';
        $val['flow_text'] = $money_val >= 0 ? '收入' : '支出';
        $val['money_abs'] = number_format(abs($money_val), 2, '.', '');
        $val['money'] = number_format($money_val, 2, '.', '');
        $val['_money'] = number_format((float)$val['_money'], 2, '.', '');
        $val['money_'] = number_format((float)$val['money_'], 2, '.', '');
        $val['create_time'] = empty($val['create_time']) ? '' : date('Y-m-d H:i:s', $val['create_time']);
    }

    output::data($list, $res['total']);
}
if($action == 'bill_transfer'){
    $amount_raw = Input::postStrVar('amount');
    $amount = round((float)$amount_raw, 2);
    if ($amount <= 0) {
        Ret::error('请输入有效的划转金额');
    }
    if ($amount < 1) {
        Ret::error('最低划转金额为1元');
    }

    $db->beginTransaction();

    $station = $db->once_fetch_array("select id, user_id, money, level_id from {$db_prefix}station where delete_time is null and user_id = " . UID . " limit 1");
    if (empty($station)) {
        $db->rollback();
        Ret::error('未找到分站信息');
    }

    $station_money = isset($station['money']) ? (float)$station['money'] : 0;
    if ($station_money < $amount) {
        $db->rollback();
        Ret::error('店铺余额不足');
    }

    $station_level = $db->once_fetch_array("select cash_change from {$db_prefix}station_level where id = {$station['level_id']}");
    $fee_rate = isset($station_level['cash_change']) && is_numeric($station_level['cash_change']) ? (float)$station_level['cash_change'] : 0;
    if ($fee_rate < 0) {
        $fee_rate = 0;
    }

    $fee = round($amount * $fee_rate, 2);
    $net = round($amount - $fee, 2);
    if ($net <= 0) {
        $db->rollback();
        Ret::error('手续费过高，到账金额不足');
    }

    $before_money = $station_money;
    $after_money = round($before_money - $amount, 2);
    $amount_sql = number_format($amount, 2, '.', '');
    $fee_sql = number_format($fee, 2, '.', '');
    $before_sql = number_format($before_money, 2, '.', '');
    $after_sql = number_format($after_money, 2, '.', '');

    $db->query("update {$db_prefix}station set money = money - {$amount_sql} where id = {$station['id']}");
    $timestamp = time();
    $db->query("insert into {$db_prefix}station_bill (user_id, station_id, money, _money, money_, create_time) values (" . UID . ", {$station['id']}, -" . $amount_sql . ", {$before_sql}, {$after_sql}, {$timestamp})");

    $balanceModel = new Balance_Model();
    $balanceModel->inc(UID, $net, '分站余额划转到账，划转金额：' . $amount_sql . '，手续费：' . $fee_sql);

    $db->commit();

    $station_after = $db->once_fetch_array("select money from {$db_prefix}station where id = {$station['id']}");
    $user_after = $db->once_fetch_array("select money from {$db_prefix}user where uid = " . UID);

    Ret::success('划转成功', [
        'station_money' => isset($station_after['money']) ? (float)$station_after['money'] : 0,
        'user_money' => isset($user_after['money']) ? (float)$user_after['money'] : 0,
        'amount' => $amount,
        'fee' => $fee,
        'net' => $net,
        'fee_rate' => $fee_rate
    ]);
}

if ($action == 'setting') {
    $sql = "select * from {$db_prefix}station_level where id = {$userData['station']['level_id']}";
    $station = $db->once_fetch_array($sql);
    $userStation = $userData['station'];

    $options_cache = $CACHE->readCache('options');
    $station_domain = isset($options_cache['station_domain']) ? $options_cache['station_domain'] : '';
    $station_domain = preg_split("/\r?\n/", $station_domain);

    include View::getUserView('_header');
    require_once View::getUserView('station/setting');
    include View::getUserView('_footer');
    View::output();
}
if ($action == 'template') {
    include View::getUserView('_header');
    require_once View::getUserView('station/template');
    include View::getUserView('_footer');
    View::output();
}
if ($action == 'template_index') {
    global $userData;
    if (empty($userData['station'])) {
        output::data([], 0);
    }
    $Template_Model = new Template_Model();
    $list = $Template_Model->getStationTemplates($userData, true);

    $station_id = (int)$userData['station']['id'];
    $pc_tpl = 'default';
    $tel_tpl = 'default';
    $res = $db->once_fetch_array("select plugin_name_en from {$db_prefix}station_plugin where station_id = {$station_id} and type = 'tpl' and pc_switch = 'y' limit 1");
    if (!empty($res['plugin_name_en'])) {
        $pc_tpl = $res['plugin_name_en'];
    }
    $res = $db->once_fetch_array("select plugin_name_en from {$db_prefix}station_plugin where station_id = {$station_id} and type = 'tpl' and tel_switch = 'y' limit 1");
    if (!empty($res['plugin_name_en'])) {
        $tel_tpl = $res['plugin_name_en'];
    }

    foreach ($list as $key => $val) {
        $list[$key]['switch'] = ($pc_tpl === $val['tplfile']) ? 'y' : 'n';
        $list[$key]['tel_switch'] = ($tel_tpl === $val['tplfile']) ? 'y' : 'n';
        $list[$key]['update'] = 'n';
        $list[$key]['id'] = '';
        $setting_path = TPLS_PATH . $val['tplfile'] . '/setting.php';
        $list[$key]['has_setting'] = file_exists($setting_path) ? 'y' : 'n';
    }
    output::data($list, count($list));
}
if ($action == 'template_use') {
    LoginAuth::checkToken();
    global $userData;
    if (empty($userData['station'])) {
        Ret::error('您还未开通分站');
    }
    $tpl = Input::postStrVar('tpl');
    $status = Input::postIntVar('status', 0);
    $tpl = preg_replace('/[^a-zA-Z0-9_-]/', '', $tpl);
    if ($status == 0) {
        $tpl = 'default';
    }
    if (empty($tpl)) {
        Ret::error('模板参数错误');
    }
    if (!is_dir(TPLS_PATH . $tpl)) {
        Ret::error('模板不存在');
    }

    $station_id = (int)$userData['station']['id'];
    $db->update('station_plugin', ['pc_switch' => 'n'], ['station_id' => $station_id, 'type' => 'tpl']);
    $row = $db->once_fetch_array("select station_id from {$db_prefix}station_plugin where station_id = {$station_id} and type = 'tpl' and plugin_name_en = '{$tpl}' limit 1");
    if (!empty($row)) {
        $db->update('station_plugin', ['pc_switch' => 'y'], ['station_id' => $station_id, 'type' => 'tpl', 'plugin_name_en' => $tpl]);
    } else {
        $db->add('station_plugin', [
            'station_id' => $station_id,
            'plugin_id' => 0,
            'plugin_name_cn' => $tpl,
            'plugin_name_en' => $tpl,
            'type' => 'tpl',
            'pc_switch' => 'y',
            'tel_switch' => 'n'
        ]);
    }
    Ret::success('操作成功');
}
if ($action == 'template_use_tel') {
    LoginAuth::checkToken();
    global $userData;
    if (empty($userData['station'])) {
        Ret::error('您还未开通分站');
    }
    $tpl = Input::postStrVar('tpl');
    $status = Input::postIntVar('status', 0);
    $tpl = preg_replace('/[^a-zA-Z0-9_-]/', '', $tpl);
    if ($status == 0) {
        $tpl = 'default';
    }
    if (empty($tpl)) {
        Ret::error('模板参数错误');
    }
    if (!is_dir(TPLS_PATH . $tpl)) {
        Ret::error('模板不存在');
    }

    $station_id = (int)$userData['station']['id'];
    $db->update('station_plugin', ['tel_switch' => 'n'], ['station_id' => $station_id, 'type' => 'tpl']);
    $row = $db->once_fetch_array("select station_id from {$db_prefix}station_plugin where station_id = {$station_id} and type = 'tpl' and plugin_name_en = '{$tpl}' limit 1");
    if (!empty($row)) {
        $db->update('station_plugin', ['tel_switch' => 'y'], ['station_id' => $station_id, 'type' => 'tpl', 'plugin_name_en' => $tpl]);
    } else {
        $db->add('station_plugin', [
            'station_id' => $station_id,
            'plugin_id' => 0,
            'plugin_name_cn' => $tpl,
            'plugin_name_en' => $tpl,
            'type' => 'tpl',
            'pc_switch' => 'n',
            'tel_switch' => 'y'
        ]);
    }
    Ret::success('操作成功');
}
if ($action == 'master_sort') {
    include View::getUserView('_header');
    require_once View::getUserView('station/master_sort');
    include View::getUserView('_footer');
    View::output();
}
if ($action == 'master_goods') {
    include View::getUserView('_header');
    require_once View::getUserView('station/master_goods');
    include View::getUserView('_footer');
    View::output();
}
if($action == 'master_goods_index'){
    $page_num = Input::getIntVar('limit');
    $page = Input::getIntVar('page');
    $start = ($page - 1) * $page_num;
    $sql = "select g.id, g.title, sg.custom_name, sg.is_show, sg.premium from {$db_prefix}goods g  
         left join {$db_prefix}station_goods sg on g.id=sg.goods_id 
         where g.is_on_shelf=1 and g.delete_time is null and g.station_id=0 order by g.id desc
         limit $start, $page_num";
    $list = $db->fetch_all($sql);
    foreach($list as &$val){
        $default_premium_percent = $default_premium * 100;
        $val['premium'] = $val['premium'] === null ? (string)$default_premium_percent : $val['premium'] * 100;
    }
    $sql = "select count(g.id) total from {$db_prefix}goods g  
         left join {$db_prefix}station_goods sg on g.id=sg.goods_id 
         where g.is_on_shelf=1 and g.delete_time is null and g.station_id=0";
    $res = $db->once_fetch_array($sql);
    output::data($list, $res['total']);
}
if ($action == 'order') {
    include View::getUserView('_header');
    require_once View::getUserView('station/order');
    include View::getUserView('_footer');
    View::output();
}
if($action == 'order_index'){
    $station_id = $userData['station']['id'];
    $page = Input::getIntVar('page', 1);
    $limit = Input::getIntVar('limit', 10);
    $start = ($page - 1) * $limit;
    $sort1 = Input::getStrVar('field', 'uid');
    $sort2 = Input::getStrVar('order', 'desc');
    $order_by = "order by {$sort1} {$sort2}";


    $where  = [];
    $where['email_username'] = Input::getStrVar('email_username');
    $where['out_trade_no'] = Input::getStrVar('out_trade_no');
    $where['goods_title'] = Input::getStrVar('goods_title');
    $where['client_ip'] = Input::getStrVar('client_ip');
    $where['order_required'] = Input::getStrVar('order_required');
    $where['station_id'] = $station_id;

    $orderNum = $orderModel->getOrderNum($where);
    $order = $orderModel->getOrderForAdmin($start, $limit, $where);
    foreach($order as $key => $val){
        $order[$key]['pay_time'] = empty($val['pay_time']) ? '' : date('Y-m-d H:i:s', $val['pay_time']);
        $order[$key]['amount'] = number_format($val['amount'], 2);
    }
    output::data($order, $orderNum);
}
if ($action == 'master_sort_edit') {
    global $userData;
    $station_id = $userData['station']['id'];
    $sort_id = Input::getIntVar('sort_id');
    $sql = "select * from {$db_prefix}station_sort where station_id=$station_id and sort_id=$sort_id";
    $station_sort = $db->once_fetch_array($sql);
    $sort = $db->once_fetch_array("select * from {$db_prefix}sort where sid = $sort_id");
    include View::getUserView('open_head');
    require_once View::getUserView('station/master_sort_edit');
    include View::getUserView('open_foot');
    View::output();
}
if ($action == 'master_goods_edit') {
    global $userData;
    $station_id = $userData['station']['id'];
    $goods_id = Input::getIntVar('goods_id');
    $sql = "select * from {$db_prefix}station_goods where station_id=$station_id and goods_id=$goods_id";
    $station_goods = $db->once_fetch_array($sql);
    $goods = $db->once_fetch_array("select * from {$db_prefix}goods where id = $goods_id");
    include View::getUserView('open_head');
    require_once View::getUserView('station/master_goods_edit');
    include View::getUserView('open_foot');
    View::output();
}
if ($action == 'master_goods_premium') {
    global $userData;
    include View::getUserView('open_head');
    require_once View::getUserView('station/master_goods_premium');
    include View::getUserView('open_foot');
    View::output();
}
if($action == 'master_sort_hide'){
    global $userData;
    $station_id = $userData['station']['id'];
    $ids = Input::postStrVar('ids');
    $sql = "select * from {$db_prefix}station_sort where station_id=$station_id and sort_id in($ids)";
    $station_sort = $db->fetch_all($sql);
    $sql = "select * from {$db_prefix}sort where sid in($ids) and station_id=0";
    $sort = $db->fetch_all($sql);
    foreach($sort as $val){
        $is_exists = false;
        foreach($station_sort as $v){
            if($val['sid'] == $v['sort_id']){
                $is_exists = true;
            }
        }
        if($is_exists){
            $db->update('station_sort', [
                'is_show' => 'n'
            ], ['station_id' => $station_id, 'sort_id' => $val['sid']]);
        }else{
            $db->add('station_sort', [
                'station_id' => $station_id,
                'sort_id' => $val['sid'],
                'type' => 'goods',
                'is_show' => 'n',
            ]);
        }
    }
    Ret::success();
}
if($action == 'master_goods_hide'){
    global $userData;
    $station_id = $userData['station']['id'];
    $ids = Input::postStrVar('ids');
    $sql = "select * from {$db_prefix}station_goods where station_id=$station_id and goods_id in($ids)";
    $station_goods = $db->fetch_all($sql);
    $sql = "select * from {$db_prefix}goods where id in($ids) and station_id=0";
    $goods = $db->fetch_all($sql);
    foreach($goods as $val){
        $is_exists = false;
        foreach($station_goods as $v){
            if($val['id'] == $v['goods_id']){
                $is_exists = true;
            }
        }
        if($is_exists){
            $db->update('station_goods', [
                'is_show' => 'n'
            ], ['station_id' => $station_id, 'goods_id' => $val['id']]);
        }else{
            $db->add('station_goods', [
                'station_id' => $station_id,
                'goods_id' => $val['id'],
                'premium' => $default_premium,
                'is_show' => 'n',
            ]);
        }
    }
    Ret::success();
}
if($action == 'master_sort_show'){
    global $userData;
    $station_id = $userData['station']['id'];
    $ids = Input::postStrVar('ids');
    $sql = "select * from {$db_prefix}station_sort where station_id=$station_id and sort_id in($ids)";
    $station_sort = $db->fetch_all($sql);
    $sql = "select * from {$db_prefix}sort where sid in($ids) and station_id=0";
    $sort = $db->fetch_all($sql);
    foreach($sort as $val){
        $is_exists = false;
        foreach($station_sort as $v){
            if($val['sid'] == $v['sort_id']){
                $is_exists = true;
            }
        }
        if($is_exists){
            $db->update('station_sort', [
                'is_show' => 'y'
            ], ['station_id' => $station_id, 'sort_id' => $val['sid']]);
        }else{
            $db->add('station_sort', [
                'station_id' => $station_id,
                'sort_id' => $val['sid'],
                'type' => 'goods',
                'is_show' => 'y',
            ]);
        }
    }
    Ret::success();
}
if($action == 'master_goods_show'){
    global $userData;
    $station_id = $userData['station']['id'];
    $ids = Input::postStrVar('ids');
    $sql = "select * from {$db_prefix}station_goods where station_id=$station_id and goods_id in($ids)";
    $station_goods = $db->fetch_all($sql);
    $sql = "select * from {$db_prefix}goods where id in($ids) and station_id=0";
    $goods = $db->fetch_all($sql);
    foreach($goods as $val){
        $is_exists = false;
        foreach($station_goods as $v){
            if($val['id'] == $v['goods_id']){
                $is_exists = true;
            }
        }
        if($is_exists){
            $db->update('station_goods', [
                'is_show' => 'y'
            ], ['station_id' => $station_id, 'goods_id' => $val['id']]);
        }else{
            $db->add('station_goods', [
                'station_id' => $station_id,
                'goods_id' => $val['id'],
                'premium' => $default_premium,
                'is_show' => 'y',
            ]);
        }
    }
    Ret::success();
}
if($action == 'master_goods_premium_ajax'){
    global $userData;
    $station_id = $userData['station']['id'];
    $premium = Input::postStrVar('premium', 0);
    $sql = "select * from {$db_prefix}station_goods where station_id=$station_id";
    $station_goods = $db->fetch_all($sql);
    $sql = "select * from {$db_prefix}goods where station_id=0 and is_on_shelf=1 and delete_time is null";
    $goods = $db->fetch_all($sql);
    foreach($goods as $val){
        $is_exists = false;
        foreach($station_goods as $v){
            if($val['id'] == $v['goods_id']){
                $is_exists = true;
            }
        }
        if($is_exists){
            $db->update('station_goods', [
                'premium' => $premium
            ], ['station_id' => $station_id, 'goods_id' => $val['id']]);
        }else{
            $db->add('station_goods', [
                'station_id' => $station_id,
                'goods_id' => $val['id'],
                'premium' => $premium,
                'is_show' => 'n',
            ]);
        }
    }
    Ret::success();
}
if($action == 'master_sort_edit_ajax'){
    $sort_id = Input::postIntVar('sort_id');
    $custom_name = Input::postStrVar('custom_name');
    global $userData;
    $station_id = $userData['station']['id'];
    $sql = "select * from {$db_prefix}station_sort where station_id=$station_id and sort_id=$sort_id";
    $station_sort = $db->once_fetch_array($sql);
    if($station_sort){
        $db->update('station_sort', [
            'custom_name' => $custom_name
        ], ['id' => $station_sort['id']]);
    }else{
        $db->add('station_sort', [
            'station_id' => $station_id,
            'sort_id' => $sort_id,
            'type' => 'goods',
            'is_show' => 'n',
            'custom_name' => $custom_name
        ]);
    }
    Ret::success();
}
if($action == 'master_goods_edit_ajax'){
    $goods_id = Input::postIntVar('goods_id');
    $custom_name = Input::postStrVar('custom_name');
    $premium = Input::postStrVar('premium', 0);
    global $userData;
    $station_id = $userData['station']['id'];
    $sql = "select * from {$db_prefix}station_goods where station_id=$station_id and goods_id=$goods_id";
    $station_goods = $db->once_fetch_array($sql);
    if($station_goods){
        $db->update('station_goods', [
            'custom_name' => $custom_name,
            'premium' => $premium
        ], ['id' => $station_goods['id']]);
    }else{
        $db->add('station_goods', [
            'station_id' => $station_id,
            'goods_id' => $goods_id,
            'is_show' => 'n',
            'custom_name' => $custom_name,
            'premium' => $premium
        ]);
    }
    Ret::success();
}

if($action == 'master_sort_index'){
    $sql = "select s.*, ss.custom_name, ss.is_show from {$db_prefix}sort s  
         left join {$db_prefix}station_sort ss on s.sid=ss.sort_id 
         where s.type='goods' and s.station_id=0 order by s.taxis desc, s.sid asc";
    $list = $db->fetch_all($sql);
    output::data($list, count($list));
}
if($action == 'master_goods_switch'){
    global $userData;
    $goods_id = Input::postIntVar('id');
    $is_show = Input::postStrVar('is_show');
    $station_id = $userData['station']['id'];
    $sql = "select id from {$db_prefix}station_goods where goods_id=$goods_id and station_id=$station_id";
    $station_sort = $db->once_fetch_array($sql);
    if($station_sort){
        $db->update('station_goods', [
            'is_show' => $is_show
        ], ['id' => $station_sort['id']]);
    }else{
        $db->add('station_goods', [
            'station_id' => $station_id,
            'goods_id' => $goods_id,
            'premium' => $default_premium,
            'is_show' => $is_show
        ]);
    }
    Ret::success();
}
if($action == 'master_sort_switch'){
    global $userData;
    $sort_id = Input::postIntVar('id');
    $is_show = Input::postStrVar('is_show');
    $station_id = $userData['station']['id'];
    $sql = "select id from {$db_prefix}station_sort where sort_id=$sort_id and station_id=$station_id";
    $station_sort = $db->once_fetch_array($sql);
    if($station_sort){
        $db->update('station_sort', [
            'is_show' => $is_show
        ], ['id' => $station_sort['id']]);
    }else{
        $db->add('station_sort', [
            'station_id' => $station_id,
            'sort_id' => $sort_id,
            'type' => 'goods',
            'is_show' => $is_show
        ]);
    }
    Ret::success();
}

if($action == 'setting_ajax'){
    $name = Input::postStrVar('name');
    $title = Input::postStrVar('title');
    $master_sort = Input::postIntVar('master_sort');
    $master_goods = Input::postIntVar('master_goods');
    $domain_2_prefix = Input::postStrVar('domain_2_prefix');
    $domain_2_suffix = Input::postStrVar('domain_2_suffix');
    $goods_premium = Input::postStrVar('goods_premium');
    if (!empty($domain_2_prefix)) {
        $reserved_raw = Option::get('station_domain_reserved');
        if (!empty($reserved_raw)) {
            $reserved_list = preg_split("/[\r\n,]+/", strtolower($reserved_raw));
            $reserved_list = array_filter(array_map('trim', $reserved_list));
            if (in_array(strtolower($domain_2_prefix), $reserved_list, true)) {
                Ret::error('该二级域名前缀禁止使用，请更换');
            }
        }
    }
    if(!empty($domain_2_prefix)){
        if(empty($domain_2_suffix)){
            Ret::error('主站未配置二级域名后缀，暂无法设置二级域名');
        }else{
            $res = $db->once_fetch_array("select * from {$db_prefix}station where delete_time is null and domain_2_prefix != '' and domain_2_prefix is not null and domain_2_prefix = '{$domain_2_prefix}' and domain_2_suffix = '{$domain_2_suffix}' and id != {$userData['station']['id']}");
            if(!empty($res)){
                Ret::error('该二级域名的前缀已被占用，请填写其他前缀或选择其他后缀');
            }
        }

    }


    $domain = Input::postStrVar('domain');
    $roll_notice = Input::postStrVar('roll_notice');
    $home_notice = Input::postStrVar('home_notice');

    $update = [
        'name' => $name,
        'title' => $title,
        'master_sort' => $master_sort,
        'master_goods' => $master_goods,
        'domain_2_prefix' => $domain_2_prefix,
        'domain_2_suffix' => $domain_2_suffix,
        'domain_2' => $domain_2_prefix . $domain_2_suffix,
        'domain' => $domain,
        'roll_notice' => $roll_notice,
        'home_notice' => $home_notice,
        'goods_premium' => $goods_premium
    ];
    $db->update('station', $update, ['id' => $userData['station']['id']]);
    if($master_sort == 1){

    }
    if($master_goods == 1){

    }

    Ret::success('已保存');
}

if ($action == 'open') {
    $sql = "select * from {$db_prefix}station_level order by id asc";
    $station = $db->fetch_all($sql);

    include View::getUserView('_header');
    require_once View::getUserView('station/open');
    include View::getUserView('_footer');
    View::output();
}
if($action == 'open_ajax'){
    $id = Input::postIntVar('id');
    if(empty($id)){
        Ret::error('参数不合法');
    }
    $station = $db->once_fetch_array("select * from {$db_prefix}station where delete_time IS NULL AND user_id = " . UID);
    if(!empty($station)){
        Ret::error('您已开通过分站，请勿重复开通');
    }
    $user = $db->once_fetch_array("select * from {$db_prefix}user where uid = " . UID);
    if(empty($user)){
        Ret::error('用户余额查询失败');
    }
    $station_level = $db->once_fetch_array("select * from {$db_prefix}station_level where id = {$id}");
    if(empty($station_level)){
        Ret::error('套餐不存在或已被删除');
    }
    if($user['money'] < $station_level['price']){
        Ret::error('您的余额不足，请充值余额后再开通');
    }
    // 扣除用户余额
    $BalanceModel = new Balance_Model();
    $BalanceModel->dec(UID, $station_level['price'], '开通分站：' . $station_level['name']);
    // 创建分站
    $StationModel = new Station_Model();
    $StationModel->create(UID, $station_level['id'], $station_level['price']);
    // 开通分站流程结束
    Ret::success('分站开通成功');
}
