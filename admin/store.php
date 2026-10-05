<?php
/**
 * store
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

$Store_Model = new Store_Model();


/*
 * 插件分类（商店那一排筛选）。
 *
 * ⚠️ 2026-10-05 改成**以服务端为准**：原来这里硬编了一串分类
 * （支付方式 / 系统通知 / 页面美化……），那是**老服务端**的字典。
 * 新后台的分类在 `bs_tt_app_category` 里、由站长自己维护，两边的 id 和名字
 * 都对不上 —— 硬编着用的话，**点任何一个分类都筛不出东西**（后端拿这个 id
 * 去查它自己的分类表）。
 *
 * 取不到分类（连不上 / 还没配）就只剩「全部插件」那一项：筛选没了，
 * 但列表照常能用（`categories()` 吞掉异常返回空，见那个方法）。
 */
$plugin_type_arr = [['id' => 0, 'title' => '全部插件']];

foreach (Store_Model::categories() as $categoryRow) {
    $plugin_type_arr[] = ['id' => $categoryRow['id'], 'title' => $categoryRow['title']];
}



if (empty($action)) {
    Register::isRegServer();
    $ttkey = getMyTtKey();
    $br = '<a href="./">控制台</a><a href="./store.php">应用商店</a><a><cite>全部应用</cite></a>';
    include View::getAdmView('header');
    require_once(View::getAdmView('store'));
    include View::getAdmView('footer');
    View::output();
}
if ($action === 'plu') {
    Register::isRegServer();
    $ttkey = getMyTtKey();
    $br = '<a href="./">控制台</a><a href="./store.php">应用商店</a><a><cite>扩展插件</cite></a>';

    $plugin_type = Input::getStrVar('plugin_type', 0);
    $title = Input::getStrVar('title');
    include View::getAdmView('header');
    require_once(View::getAdmView('templates/default/store/store_plu'));
    include View::getAdmView('footer');
    View::output();
}
if ($action === 'tpl') {
    Register::isRegServer();
    $ttkey = getMyTtKey();
    $br = '<a href="./">控制台</a><a href="./store.php">应用商店</a><a><cite>模板主题</cite></a>';

    include View::getAdmView('header');
    require_once(View::getAdmView('store_tpl'));
    include View::getAdmView('footer');
    View::output();
}

/**
 * 获取全部应用
 */
if($action == 'index'){
    $type = 'all';
    $page = Input::getIntVar('page', 1);
    $sid = Input::getStrVar('sid');
    $keyword = Input::getStrVar('keyword');
    $pageNum = Input::getIntVar('limit');
    $store = $Store_Model->getList($type, $page, $pageNum, $keyword, $sid);
    $apps = storeHandleData($store['list']);
    $count = $store['count'];
    output::data($apps, $count);
}

if($action == 'tpl_ajax'){
    $type = 'template';
    $page = Input::getIntVar('page', 1);
    $sid = Input::getStrVar('sid');
    $keyword = Input::getStrVar('keyword');
    $pageNum = Input::getIntVar('limit');
    $store = $Store_Model->getList($type, $page, $pageNum, $keyword, $sid);
    $apps = storeHandleData($store['list']);
    $count = $store['count'];
    output::data($apps, $count);
}



if($action == 'plu_ajax'){
    $type = 'plugin';
    $page = Input::getIntVar('page', 1);
    $sid = Input::getStrVar('plugin_type');
    $keyword = Input::getStrVar('keyword');
    $pageNum = Input::getIntVar('limit');
    $store = $Store_Model->getList($type, $page, $pageNum, $keyword, $sid);
    $apps = storeHandleData($store['list']);
    $count = $store['count'];
    output::data($apps, $count);
}



if ($action === 'mine') {
    $addons = $Store_Model->getMyAddon();
    $sub_title = '我的已购';

    include View::getAdmView('header');
    require_once(View::getAdmView('store_mine'));
    include View::getAdmView('footer');
    View::output();
}

if ($action === 'svip') {
    $addons = $Store_Model->getSvipAddon();
    $sub_title = '铁杆专属';

    include View::getAdmView('header');
    require_once(View::getAdmView('store_svip'));
    include View::getAdmView('footer');
    View::output();
}

if ($action === 'top') {
    $addons = $Store_Model->getTopAddon();
    output::ok($addons);
}

if ($action === 'error') {
    $keyword = '';
    $sub_title = '';
    $sid = '';

    $br = '<ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">控制台</a></li>
        <li class="breadcrumb-item"><a href="./store.php">应用商店</a></li>
        <li class="breadcrumb-item active" aria-current="page">全部应用</li>
    </ol>';

    include View::getAdmView('header');
    require_once(View::getAdmView('store'));
    include View::getAdmView('footer');
    View::output();
}

if ($action === 'install') {
    $type = Input::postStrVar('type');
    $plugin_id = Input::postStrVar('plugin_id');
    $source_type = Input::postStrVar('type');

    /*
     * ⚠️ 2026-10-05 起这道本地判定**只问「本机激活了没有」**：
     * 「买过这一款没有」由**服务端的下载接口**判（它会 400），客户端这边判不了、
     * 也拦不住谁 —— 见 `include/lib/register.php` 里 `verifyDownload` 那段注释。
     */
    $r = Register::verifyDownload($plugin_id);

    if ($r == 2) {
        Ret::error('您当前未激活，请先到「正版授权」页激活后再安装');
    }

    /* 新的下载地址（付费应用由服务端那两道门把着：有效授权 + 买过这一款）*/
    $url = LicenseClient::downloadUrl(
        (int) $plugin_id,
        LicenseService::effectiveHost(),
        (string) getMyTtKey()
    );
// echo $url;die;
    $temp_file = ttFetchFile($url);

    
    if (!$temp_file) {
        output::error('安装失败，下载超时或没有权限');
    }

    if ($source_type == 'tpl') {
        $unzip_path = '../content/templates/';
        $suc_url = 'template.php';
    } else {
        $unzip_path = '../content/plugins/';
        $suc_url = 'plugin.php';
    }

    $ret = ttUnZip($temp_file, $unzip_path, $source_type);
    @unlink($temp_file);
    switch ($ret) {
        case 0:
            output::ok('安装成功 <a href="' . $suc_url . '">去启用</a>');
        case 1:
        case 2:
        output::error('安装失败，请检查content下目录是否可写');
        case 3:
            output::error('安装失败，请安装php的Zip扩展');
        default:
            output::error('安装失败，不是有效的安装包');
    }
}

/**
 * 下单（商店里点「立即购买」）。
 *
 * 老流程是一颗**链接**直接跳到服务端的购买页
 * （`api/emshop.php?action=buy&ttkey=…&plugin=…`）—— 那个页面已经不在了。
 * 新协议是**先开一张单**再跳收款页：`POST /api/open/v1/tt/order` 回 `pay_url`，
 * 我们把它交给前端在新标签里打开。
 *
 * ⚠️ **金额不由我们说**：服务端按这个客户端端的授权档位算（至尊 = 0，
 * 那种情况界面上根本不会出现「购买」按钮，见视图里 `my_price == 0` 那个分支）。
 */
if ($action === 'create_order') {
    LoginAuth::checkToken();

    $appId = Input::postIntVar('app_id');

    if ($appId <= 0) {
        Ret::error('请指定要购买的应用');
    }

    try {
        $order = LicenseClient::createOrder(
            (string) getMyTtKey(),
            LicenseService::effectiveHost(),
            $appId
        );
    } catch (RuntimeException $e) {
        /* 服务端那句人话直接给用户（「下单需要有效授权…」这种）*/
        Ret::error($e->getMessage());
    }

    if (empty($order['pay_url'])) {
        Ret::error('下单成功但没拿到收款地址，请联系官方');
    }

    /*
     * ⚠️ 收款页在**服务端**那个域名下，不是本机的 —— 所以用
     * `LicenseClient::absolute()` 拼服务端地址，别拼成自己站。
     */
    Ret::success('', [
        'pay_url'  => LicenseClient::absolute($order['pay_url']),
        'order_no' => isset($order['order_no']) ? (string) $order['order_no'] : '',
    ]);
}

function storeHandleData($apps){
    $Plugin_Model = new Plugin_Model();
    $p = $Plugin_Model->getPlugins();
    $install_plugin = [];
    foreach($p as $val){
        $install_plugin[] = $val['Plugin'];
    }

    $Template_Model = new Template_Model();
    $p = $Template_Model->getTemplates();
    foreach($p as $val){
        $install_plugin[] = $val['tplfile'];
    }

    $reg_type = Register::getRegType();

    foreach($apps as $key => $val){
        $apps[$key]['reg_type'] = $reg_type;
        if(in_array($val['english_name'], $install_plugin)){
            $apps[$key]['is_install'] = 'y';
        }else{
            $apps[$key]['is_install'] = 'n';
        }
    }
    return $apps;

}