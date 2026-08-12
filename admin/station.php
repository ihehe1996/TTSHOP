<?php


require_once 'globals.php';

$db = Database::getInstance();
$db_prefix = DB_PREFIX;
$timestamp = time();

if ($action == 'lists') {
    $br = '<a href="./">控制台</a><a href="./station.php">分站管理</a><a><cite>分站列表</cite></a>';
    include View::getAdmView('header');
    require_once View::getAdmView('station/lists');
    include View::getAdmView('footer');
    View::output();
}

if ($action == 'lists_index') {
    $sql = "select s.id, s.money, s.name, s.title, sl.name as level_name, u.tel, u.email, s.domain, s.domain_2, s.create_time  
            from {$db_prefix}station s 
            left join {$db_prefix}station_level sl on s.level_id = sl.id 
            left join {$db_prefix}user u on s.user_id = u.uid 
            where s.delete_time is null
            order by s.id desc";
    $list = $db->fetch_all($sql);

    foreach($list as &$val){
        $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
    }

    output::data($list, count($list));
}

if($action == 'lists_del'){
    $ids = Input::postStrVar('ids');
    $ids = explode(',', $ids);
    foreach($ids as $id){
        $update = [
            'delete_time' => time()
        ];
        $db->update('station', $update, ['id' => $id]);
    }
    Ret::success('删除成功');
}

if ($action == 'level') {
    $br = '<a href="./">控制台</a><a href="./station.php">分站管理</a><a><cite>分站等级</cite></a>';
    include View::getAdmView('header');
    require_once View::getAdmView('station/level');
    include View::getAdmView('footer');
    View::output();
}

if ($action == 'level_index') {
    $sql = "select * from {$db_prefix}station_level order by id asc";
    $list = $db->fetch_all($sql);

    foreach($list as &$val){
        $val['service_change'] = ($val['service_change'] * 100) . '%';
        $val['cash_change'] = ($val['cash_change'] * 100) . '%';
    }

    output::data($list, count($list));
}
if($action == 'level_add'){
    include View::getAdmView('open_head');
    require_once(View::getAdmView('station/level_add'));
    include View::getAdmView('open_foot');
    View::output();
}
if($action == 'level_edit'){
    $id = Input::getIntVar('id');
    $sql = "select * from {$db_prefix}station_level where id={$id}";
    $info = $db->once_fetch_array($sql);

    include View::getAdmView('open_head');
    require_once(View::getAdmView('station/level_edit'));
    include View::getAdmView('open_foot');
    View::output();
}
if($action == 'level_switch_isdomain'){
    $id = Input::postIntVar('id');
    $status = Input::postStrVar('status');
    $data = [
        'is_domain' => $status,
        'update_time' => $timestamp
    ];
    $db->update('station_level', $data, ['id' => $id]);
    Ret::success('操作成功');
}
if($action == 'level_switch_isstation'){
    $id = Input::postIntVar('id');
    $status = Input::postStrVar('status');
    $data = [
        'is_station' => $status,
        'update_time' => $timestamp
    ];
    $db->update('station_level', $data, ['id' => $id]);
    Ret::success('操作成功');
}
if($action == 'level_switch_isgoods'){
    $id = Input::postIntVar('id');
    $status = Input::postStrVar('status');
    $data = [
        'is_goods' => $status,
        'update_time' => $timestamp
    ];
    $db->update('station_level', $data, ['id' => $id]);
    Ret::success('操作成功');
}
if($action == 'level_del'){
    $ids = Input::postStrVar('ids');
    $db->del('station_level', $ids);
    Ret::success('操作成功');
}

if($action == 'level_edit_ajax'){
    $id = Input::postIntVar('id');

    $name = Input::postStrVar('name');
    $price = Input::postStrVar('price');
    $cash_change = Input::postStrVar('cash_change');
    if(empty($name)){
        Ret::error('请输入分站等级名称');
    }
    $price = empty($price) || $price < 0 ? 0 : $price;
    $cash_change = empty($cash_change) || $cash_change < 0 ? 0 : $cash_change;

    $data = [
        'name' => $name,
        'price' => $price,
        'cash_change' => $cash_change
    ];
    $db->update('station_level', $data, ['id' => $id]);
    Ret::success('编辑成功');
}

if($action == 'level_add_ajax'){
    $name = Input::postStrVar('name');
    $price = Input::postStrVar('price');
    $is_station = Input::postStrVar('is_station');
    $is_domain = Input::postStrVar('is_domain');
    $is_goods = Input::postStrVar('is_goods');
    $cash_change = Input::postStrVar('cash_change');
    if(empty($name)){
        Ret::error('请输入分站等级名称');
    }
    $price = empty($price) || $price < 0 ? 0 : $price;
    $is_station = empty($is_station) ? 'n' : $is_station;
    $is_domain = empty($is_domain) ? 'n' : $is_domain;
    $is_goods = empty($is_goods) ? 'n' : $is_goods;
    $cash_change = empty($cash_change) || $cash_change < 0 ? 0 : $cash_change;

    $data = [
        'name' => $name,
        'price' => $price,
        'is_station' => $is_station,
        'is_domain' => $is_domain,
        'is_goods' => $is_goods,
        'cash_change' => $cash_change,
        'create_time' => $timestamp
    ];
    $db->add('station_level', $data);

    Ret::success('添加成功');

}


if ($action == 'setting') {
    $options_cache = $CACHE->readCache('options');
    $station_domain = isset($options_cache['station_domain']) ? $options_cache['station_domain'] : '';
    $station_cname_domain = isset($options_cache['station_cname_domain']) ? $options_cache['station_cname_domain'] : '';
    $station_switch = isset($options_cache['station_switch']) && $options_cache['station_switch'] === 'n' ? 'n' : 'y';
    $station_domain_reserved = isset($options_cache['station_domain_reserved']) ? $options_cache['station_domain_reserved'] : '';
    $station_default_premium = isset($options_cache['station_default_premium']) ? $options_cache['station_default_premium'] : '0.1';
    if (!is_numeric($station_default_premium)) {
        $station_default_premium = '0.1';
    }
    if ($station_default_premium < 0) {
        $station_default_premium = '0.1';
    }
    $station_default_premium = (float)$station_default_premium * 100;
    $station_default_premium = rtrim(rtrim(sprintf('%.4f', $station_default_premium), '0'), '.');

    $br = '<a href="./">控制台</a><a href="./setting.php">系统管理</a><a><cite>分站配置</cite></a>';

    include View::getAdmView('header');
    require_once(View::getAdmView('station/setting'));
    include View::getAdmView('footer');
    View::output();
}

if ($action == 'setting_save') {
    LoginAuth::checkToken();
    $station_switch = Input::postStrVar('station_switch');
    $station_switch = $station_switch === 'n' ? 'n' : 'y';

    $station_default_premium = Input::postStrVar('station_default_premium');
    $station_default_premium = str_replace('%', '', $station_default_premium);
    if (!is_numeric($station_default_premium)) {
        $station_default_premium = 10;
    }
    if ($station_default_premium < 0) {
        $station_default_premium = 10;
    }
    $station_default_premium = (float)$station_default_premium / 100;
    $data = [
        'station_domain'          => Input::postStrVar('station_domain'),
        'station_cname_domain'          => Input::postStrVar('station_cname_domain'),
        'station_switch'          => $station_switch,
        'station_domain_reserved' => Input::postStrVar('station_domain_reserved'),
        'station_default_premium' => (string)$station_default_premium,
    ];

    foreach ($data as $key => $val) {
        Option::updateOption($key, $val);
    }

    $CACHE->updateCache('options');
    Output::ok();
}
