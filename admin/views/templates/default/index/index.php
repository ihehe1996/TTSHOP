<?php defined('EM_ROOT') || exit('access denied!'); ?>
<?php $mianze_date = strtotime(date('2025-10-25 01:12:00')) ?>

<?php bu_yao_po_jie_oj8k('?') ?>

<?php doAction('adm_main_top') ?>



<div class="mt-4 shadow-lg" style="margin-bottom: 20px;">
    <?php if (!Register::isRegLocal()) : ?>
        <div style="margin-top: 25px;">
            <div class="auth-promo-card">
                <div class="promo-content">
                    <div class="promo-icon">
                        <i class="layui-icon layui-icon-notice"></i>
                    </div>
                    <div class="promo-text">
                        <h3>系统授权提示</h3>
                        <p>您安装的 EMSHOP 尚未授权，完成授权可使用全部功能，包括如下：</p>
                        <div class="promo-list">
                            <div class="list-item">
                                <i class="layui-icon layui-icon-ok-circle"></i>
                                <span>解锁在线升级功能，一键升级到最新版本，获得来自官方的安全和功能更新</span>
                            </div>
                            <div class="list-item">
                                <i class="layui-icon layui-icon-ok-circle"></i>
                                <span>解锁应用商店，获得更多模板和插件，并支持应用在线一键更新</span>
                            </div>
                            <div class="list-item">
                                <i class="layui-icon layui-icon-ok-circle"></i>
                                <span>去除所有未注册提示及功能限制，加入专属Q群，获得官方技术指导问题解答</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="promo-actions">
                    <a href="auth.php" class="promo-btn primary">去授权 <i class="layui-icon layui-icon-right" style="font-size: 12px;"></i></a>
                    <span class="promo-btn secondary get-em-buy-info">获取授权码</span>
                </div>
            </div>
        </div>

    <?php endif ?>


</div>

<div class="index-kp">
    <div class="em-kpi-grid grid-cols-xs-1 grid-cols-sm-2 grid-cols-xl-5 grid-cols-lg-3 grid-cols-md-3 mb-3 grid-gap-10" style="width: 100%;">
        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="indigo" id="system-version-card">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-component"></i></span>
                    <span>系统版本</span>
                </div>
                <div class="em-kpi-tag">
                    <?php if (!Register::isRegLocal()) : ?>
                        <a href="<?= EM_LINE[CURRENT_LINE]['value'] ?>" target="_blank" class="auth-badge unauth">未授权</a>
                    <?php else: ?>
                        <?php if ($regType == 2): ?>
                            <a href="<?= EM_LINE[CURRENT_LINE]['value'] ?>" target="_blank" class="auth-badge svip"><i class="layui-icon layui-icon-diamond"></i>SVIP</a>
                        <?php elseif ($regType == 1): ?>
                            <a href="<?= EM_LINE[CURRENT_LINE]['value'] ?>" target="_blank" class="auth-badge vip">VIP</a>
                        <?php elseif ($regType == 3): ?>
                            <a href="<?= EM_LINE[CURRENT_LINE]['value'] ?>" target="_blank" class="auth-badge supreme"><i class="layui-icon layui-icon-star-fill"></i>至尊</a>
                        <?php else: ?>
                            <a href="<?= EM_LINE[CURRENT_LINE]['value'] ?>" target="_blank" class="auth-badge error">错误</a>
                        <?php endif ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value">v<?= ucfirst(Option::EM_VERSION) ?></div>
                <div class="em-kpi-sub">EMSHOP</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <div class="em-kpi-meta">
                    <span class="em-kpi-meta-label">检查新版本</span>
                </div>
                <div class="em-kpi-action" id="check-update">
                    <button type="button" onclick="checkUpdate()" class="layui-btn layui-btn layui-btn-xs layui-border-blue">检查更新</button>
                </div>
            </div>
        </div>
        <?php if(Option::get('mianze') > $mianze_date): ?>
            <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="emerald">
                <div class="dashboard-card__header em-kpi-head">
                    <div class="em-kpi-title">
                        <span class="em-kpi-icon"><i class="layui-icon layui-icon-ok-circle"></i></span>
                        <span>用户协议</span>
                    </div>
                    <div class="em-kpi-tag">
                        <span class="em-kpi-pill em-kpi-pill--success">同意</span>
                    </div>
                </div>
                <div class="dashboard-card__body em-kpi-body">
                    <div class="em-kpi-value"><?= date('Y-m-d', Option::get('mianze')) ?></div>
                    <div class="em-kpi-sub">签署日期</div>
                </div>
                <div class="dashboard-card__footer em-kpi-foot">
                    <div class="em-kpi-meta">
                        <span class="em-kpi-meta-label">查看协议</span>
                    </div>
                    <div class="em-kpi-action">
                        <button type="button" id="chakan-mianze" class="layui-btn layui-btn layui-btn-xs layui-border-blue">点击查看</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="sky">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-form"></i></span>
                    <span>订单数量</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--info">今日</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value"><?= $order_panel['today_orders'] ?></div>
                <div class="em-kpi-sub">昨日 <?= $order_panel['yesterday_orders'] ?> 单</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">本月订单量</span>
                <span class="em-kpi-foot-value"><?= $order_panel['month_orders'] ?> 单</span>
            </div>
        </div>
        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="amber">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-rmb"></i></span>
                    <span>销售额</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--info">今日</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value"><?= number_format($today_sales_amount, 2) ?></div>
                <div class="em-kpi-sub">昨日 <?= number_format($yesterday_sales_amount, 2) ?> 元</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">本月销售额</span>
                <span class="em-kpi-foot-value"><?= $current_month_sales_amount ?> 元</span>
            </div>
        </div>


        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="violet">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-chart"></i></span>
                    <span>客单价</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--info">今日</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value">
                    <?= empty($today_sales_amount) || empty($order_panel['today_orders']) ? 0 : number_format($today_sales_amount / $order_panel['today_orders'], 2) ?>
                </div>
                <div class="em-kpi-sub">
                    昨日 <?= empty($yesterday_sales_amount) || empty($order_panel['yesterday_orders']) ? 0 : number_format($yesterday_sales_amount / $order_panel['yesterday_orders'], 2) ?> 元
                </div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">本月客单价</span>
                <span class="em-kpi-foot-value">
                    <?= empty($current_month_sales_amount) || empty($order_panel['month_orders']) ? 0 : number_format(str_replace(',', '', $current_month_sales_amount) / $order_panel['month_orders'], 2) ?> 元
                </span>
            </div>
        </div>
        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="teal">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-user"></i></span>
                    <span>新增用户</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--info">今日</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value"><?= $user_panel['today_registrations'] ?></div>
                <div class="em-kpi-sub">昨日 <?= $user_panel['yesterday_registrations'] ?> 人</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">本月新增用户</span>
                <span class="em-kpi-foot-value"><?= $user_panel['month_registrations'] ?> 人</span>
            </div>
        </div>
        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="rose">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-vercode"></i></span>
                    <span>授权码</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--warning">官方</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value em-kpi-value--text">正版授权码</div>
                <div class="em-kpi-sub">点击下方按钮获取正版授权码</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">获取授权码</span>
                <span class="em-kpi-action"><button type="button" class="layui-btn layui-btn layui-btn-xs layui-border-blue get-em-buy-info">获取</button></span>
            </div>
        </div>
        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="slate">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-download-circle"></i></span>
                    <span>下载安装包</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--warning">官方</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value em-kpi-value--text">EMSHOP</div>
                <div class="em-kpi-sub">点击下方按钮获取下载链接</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">获取下载信息</span>
                <span class="em-kpi-action"><button type="button" class="layui-btn layui-btn layui-btn-xs layui-border-blue get-download-info">获取</button></span>
            </div>
        </div>
        <div class="dashboard-card em-kpi-card" data-tone="teal" data-icon="cyan">
            <div class="dashboard-card__header em-kpi-head">
                <div class="em-kpi-title">
                    <span class="em-kpi-icon"><i class="layui-icon layui-icon-website"></i></span>
                    <span>线路状态</span>
                </div>
                <div class="em-kpi-tag">
                    <span class="em-kpi-pill em-kpi-pill--warning">官方</span>
                </div>
            </div>
            <div class="dashboard-card__body em-kpi-body">
                <div class="em-kpi-value em-kpi-value--text"><?= EM_LINE[CURRENT_LINE]['name'] ?></div>
                <div class="em-kpi-sub">线路用于程序及模板插件的更新服务</div>
            </div>
            <div class="dashboard-card__footer em-kpi-foot">
                <span class="em-kpi-meta-label">延迟</span>
                <span class="em-kpi-foot-value" id="line-latency">
                    <i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop" style="font-size: 14px;"></i>
                </span>
            </div>
        </div>
    </div>




</div>

<!-- 官方公告和广告位版块 -->
<div class="layui-row layui-col-space15" style="margin-top: 20px;">
    <div class="layui-col-md6">
        <div class="dashboard-card notice-card">
            <div class="dashboard-card__header">
                <i class="layui-icon layui-icon-notice" style="margin-right: 8px; color: #4C7D71;"></i>
                官方公告
            </div>
            <div class="dashboard-card__body">
                <div class="notice-list" id="notice-list">
                    <span href="javascript:;" class="notice-item" id="notice-contact-item">
                        <span class="notice-tag new">官方交流</span>
                        <span class="notice-title">

                            <span>客服QQ：<span>获取失败</span></span>
                            <span style="margin-left: 20px;">QQ群：<span>获取失败</span></span>
                            <span style="margin-left: 20px;">TG群：<a href="" target="_blank">获取失败</a></span>
                        </span>
                    </span>
                    <span id="notice-extra-items" style="display: contents;">
                        <a href="https://www.rainyun.com/OTQzODE0_" class="notice-item" target="_blank">
                            <span class="notice-tag">官方推荐服务器</span>
                            <span class="notice-title">五年运营经验，超过30,000个网站运行。我们一直在为客户提供稳定长久的服务</span>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-col-md6">
        <div class="dashboard-card notice-card">
            <div class="dashboard-card__header">
                <i class="layui-icon layui-icon-speaker" style="margin-right: 8px; color: #4C7D71;"></i>
                推荐服务
            </div>
            <div class="dashboard-card__body">
                <div class="notice-list" id="recommend-list">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (User::isAdmin()): ?>
    <?php doAction('adm_main_content') ?>
<?php endif; ?>

<?php include __DIR__ . '/../../../components/modal.php'; ?>

<script>
    const openMianzeLayer = function(options){
        const opts = options || {};
        const isMobile = window.innerWidth < 768;
        const area = opts.area || (isMobile ? ['98%', '90%'] : ['860px', '80%']);
        return layer.open({
            type: 2,
            title: opts.title || 'EMSHOP商城用户协议',
            area: area,
            scrollbar: false,
            shadeClose: !!opts.shadeClose,
            skin: 'em-modal',
            closeBtn: typeof opts.closeBtn === 'number' ? opts.closeBtn : 1,
            content: opts.url || 'index.php?action=mianze'
        });
    };

    $('#chakan-mianze').click(function(){
        openMianzeLayer({
            url: 'index.php?action=mianze&chakan=1',
            shadeClose: true,
            index: 'xieyi',
        });
    });
</script>

<?php if(Option::get('mianze') < $mianze_date): ?>
    <script>
        openMianzeLayer({
            url: 'index.php?action=mianze',
            shadeClose: false,
            closeBtn: 0
        });
    </script>
<?php endif; ?>

<script>
    const renderUpdateContent = function(list) {
        let chips = '';
        list.forEach(function(item){
            chips += '<span class="version-chip">' + (item.version || '') + '</span>';
        });
        let logs = '';
        list.forEach(function(item){
            logs += '<div class="changelog-item">' + (item.content || '') + '</div>';
        });
        return (
            '<div class="update-card">'
            +   '<div class="update-header">发现以下新版本</div>'
            +   '<div class="version-badges">' + chips + '</div>'
            +   '<div class="changelog">' + logs + '</div>'
            +   '<div class="update-hint">为确保数据安全，建议在更新前备份站点和数据库</div>'
            + '</div>'
        );
    }

    const openUpdateModal = function(e){
        const data = e && e.data ? e.data : e;
        const list = Array.isArray(data) ? data : (data && data.list ? data.list : []);
        const sqlFile = (data && (data.cdn_sql || data.sql)) || '';
        const fileFile = (data && (data.cdn_file || data.file)) || '';
        const isMobile = window.innerWidth < 768;
        const area = isMobile ? ['98%', '70%']  : ['560px', '75%'];
        const rep_content = renderUpdateContent(list);
        layer.open({
            type: 1,
            title: '发现新版本',
            area: area,
            scrollbar: false,
            shadeClose: true,
            skin: 'update-modal',
            btnAlign: 'c',
            btn: ['立即更新','稍后再说'],
            content: rep_content,
            yes: function(){
                loadIndex = layer.load(2);
                $.post('./upgrade.php?action=update', {  }, function (res) {
                    layer.close(loadIndex);
                    if(res.code == 400){
                        return layer.alert(res.msg);
                    }
                    if(res.code == 200){
                        layer.open({
                            type: 1,
                            title: '更新成功',
                            area: isMobile ? ['90%', 'auto'] : ['480px','auto'],
                            shadeClose: false,
                            skin: 'update-modal',
                            btnAlign: 'c',
                            btn: ['刷新页面'],
                            content: '<div class="update-card"><div class="update-header">🎉 恭喜，更新成功</div><div class="changelog">点击下方按钮或链接刷新页面，开始体验新版本</div></div>',
                            yes: function(){
                                location.reload();
                            }
                        });
                    }

                });
            }
        });
    }




    function checkUpdate() {

        loadIndex = layer.load(2);
        $.ajax({
            url: "./upgrade.php?action=check_update",
            type: "GET",
            dataType: "json",
            success: function(e){
                if(e.code == 200){
                    openUpdateModal(e);
                }else{
                    layer.msg(e.msg);
                }
            },
            error: function(xhr, textStatus, errorThrown){
                layer.msg('检查更新失败，接口返回：' + xhr.responseText)
            },
            complete: function(){
                layer.close(loadIndex);
            }
        });
    }




    $("#menu-dashboard").addClass('active');

    $.get("./upgrade.php?action=check_update", function (e) {
        if (e && e.code === 200) {
            openUpdateModal(e);
        }
    });
    $('.get-download-info').click(function(){
        loadIndex = layer.load(2);
        $.ajax({
            url: '?action=get_download_url',
            type: 'GET',
            dataType: 'json',
            success: function(e){
                if(e.code == 200){
                    var d = e.data || {};
                    var version = d.version || '';
                    var size = d.size || '';
                    var list = [];
                    if (Array.isArray(d.buy_url)) { list = d.download_url; }
                    if (Array.isArray(d.mirrors)) { list = d.mirrors; }
                    if (d.url) { list.push({ name: '官方下载', url: d.url }); }
                    var clean = function(s){ return String(s || '').replace(/[`'"\s]/g,'').trim(); };
                    var linksHtml = '';
                    list.forEach(function(it){
                        var name = it.name || '下载地址';
                        var url = clean(it.url || it.link || '');
                        if(url){
                            linksHtml += `
                                <a href="${url}" target="_blank" rel="noopener" class="em-modal-item">
                                    <div class="em-item-icon"><i class="layui-icon layui-icon-link"></i></div>
                                    <div class="em-item-content">
                                        <span class="em-item-title">${name}</span>
                                        <span class="em-item-sub">${url}</span>
                                    </div>
                                    <div class="em-item-action"><i class="layui-icon layui-icon-right"></i></div>
                                </a>`;
                        }
                    });
                    var isMobile = window.innerWidth < 640;
                    var area = isMobile ? ['90%', 'auto'] : ['520px', 'auto'];
                    var desc = (version ? ('版本：'+ version + ' ') : '') + (size ? (' · 大小：'+ size) : '');

                    var content = `
                        <div class="em-modal-box">
                            <div class="em-modal-close-btn" onclick="layer.close(layer.index)">
                                <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                            </div>
                            <div class="em-modal-header">
                                <div class="em-modal-icon-wrapper"><i class="layui-icon layui-icon-download-circle"></i></div>
                                <div class="em-modal-title">下载安装包</div>
                                <div class="em-modal-desc">${desc || '请选择下载线路'}</div>
                            </div>
                            <div class="em-modal-body">
                                <div class="em-modal-list">
                                    ${linksHtml}
                                </div>
                            </div>
                        </div>
                    `;
                    layer.open({
                        type: 1,
                        title: false,
                        closeBtn: 0,
                        area: area,
                        shadeClose: true,
                        skin: 'em-modal-skin',
                        btn: false,
                        content: content
                    });
                } else {
                    layer.alert(e.msg);
                }
            },
            error: function(xhr, textStatus, errorThrown){
                var msg = '';
                if (xhr && xhr.responseJSON && xhr.responseJSON.msg) {
                    msg = xhr.responseJSON.msg;
                } else if (xhr && xhr.responseText) {
                    msg = (xhr.status || '') + ' ' + (xhr.statusText || '') + ' ' + xhr.responseText.slice(0, 200);
                } else {
                    msg = '获取下载信息失败：' + (errorThrown || textStatus || '未知错误');
                }
                layer.alert(msg);
            },
            complete: function(){
                layer.close(loadIndex);
            }
        });
    })
</script>

<style>
.index-kp .em-kpi-grid {
    grid-gap: 14px;
}
.index-kp .em-kpi-card {
    --kpi-accent: #0f766e;
    --kpi-accent-soft: rgba(15, 118, 110, 0.16);
    --kpi-icon: var(--kpi-accent);
    --kpi-icon-soft: var(--kpi-accent-soft);
    position: relative;
    overflow: hidden;
    border-radius: 18px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    background: #ffffff;
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
    display: flex;
    flex-direction: column;
    min-height: 182px;
}
.index-kp .em-kpi-card[data-tone="indigo"] {
    --kpi-accent: #4f46e5;
    --kpi-accent-soft: rgba(79, 70, 229, 0.18);
}
.index-kp .em-kpi-card[data-tone="emerald"] {
    --kpi-accent: #10b981;
    --kpi-accent-soft: rgba(16, 185, 129, 0.18);
}
.index-kp .em-kpi-card[data-tone="sky"] {
    --kpi-accent: #0ea5e9;
    --kpi-accent-soft: rgba(14, 165, 233, 0.18);
}
.index-kp .em-kpi-card[data-tone="amber"] {
    --kpi-accent: #f59e0b;
    --kpi-accent-soft: rgba(245, 158, 11, 0.2);
}
.index-kp .em-kpi-card[data-tone="violet"] {
    --kpi-accent: #8b5cf6;
    --kpi-accent-soft: rgba(139, 92, 246, 0.18);
}
.index-kp .em-kpi-card[data-tone="teal"] {
    --kpi-accent: #0f766e;
    --kpi-accent-soft: rgba(15, 118, 110, 0.18);
}
.index-kp .em-kpi-card[data-tone="rose"] {
    --kpi-accent: #f43f5e;
    --kpi-accent-soft: rgba(244, 63, 94, 0.18);
}
.index-kp .em-kpi-card[data-tone="slate"] {
    --kpi-accent: #64748b;
    --kpi-accent-soft: rgba(100, 116, 139, 0.18);
}
.index-kp .em-kpi-card[data-tone="cyan"] {
    --kpi-accent: #06b6d4;
    --kpi-accent-soft: rgba(6, 182, 212, 0.18);
}
.index-kp .em-kpi-card[data-icon="indigo"] {
    --kpi-icon: #4f46e5;
    --kpi-icon-soft: rgba(79, 70, 229, 0.18);
}
.index-kp .em-kpi-card[data-icon="emerald"] {
    --kpi-icon: #10b981;
    --kpi-icon-soft: rgba(16, 185, 129, 0.18);
}
.index-kp .em-kpi-card[data-icon="sky"] {
    --kpi-icon: #0ea5e9;
    --kpi-icon-soft: rgba(14, 165, 233, 0.18);
}
.index-kp .em-kpi-card[data-icon="amber"] {
    --kpi-icon: #f59e0b;
    --kpi-icon-soft: rgba(245, 158, 11, 0.2);
}
.index-kp .em-kpi-card[data-icon="violet"] {
    --kpi-icon: #8b5cf6;
    --kpi-icon-soft: rgba(139, 92, 246, 0.18);
}
.index-kp .em-kpi-card[data-icon="teal"] {
    --kpi-icon: #0f766e;
    --kpi-icon-soft: rgba(15, 118, 110, 0.18);
}
.index-kp .em-kpi-card[data-icon="rose"] {
    --kpi-icon: #f43f5e;
    --kpi-icon-soft: rgba(244, 63, 94, 0.18);
}
.index-kp .em-kpi-card[data-icon="slate"] {
    --kpi-icon: #64748b;
    --kpi-icon-soft: rgba(100, 116, 139, 0.18);
}
.index-kp .em-kpi-card[data-icon="cyan"] {
    --kpi-icon: #06b6d4;
    --kpi-icon-soft: rgba(6, 182, 212, 0.18);
}
.index-kp .em-kpi-card .dashboard-card__header,
.index-kp .em-kpi-card .dashboard-card__body,
.index-kp .em-kpi-card .dashboard-card__footer {
    position: relative;
    z-index: 1;
}
.index-kp .em-kpi-card .dashboard-card__header {
    padding: 16px 18px 8px;
    border-bottom: none;
    background: transparent;
}
.index-kp .em-kpi-card .dashboard-card__body {
    padding: 4px 18px 14px;
    text-align: left;
}
.index-kp .em-kpi-card .dashboard-card__footer {
    padding: 12px 18px 16px;
    border-top: 1px dashed rgba(15, 23, 42, 0.1);
    background: rgba(249, 250, 251, 0.7);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.index-kp .em-kpi-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.index-kp .em-kpi-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #111827;
}
.index-kp .em-kpi-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--kpi-icon-soft);
    color: var(--kpi-icon);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.65);
}
.index-kp .em-kpi-icon i {
    font-size: 18px;
}
.index-kp .em-kpi-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.index-kp .em-kpi-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1.1;
    color: var(--kpi-accent);
    font-family: "Space Grotesk", "Noto Sans SC", sans-serif;
    text-align: center;
    width: 100%;
}
.index-kp .em-kpi-value--text {
    font-size: 20px;
    text-align: center;
    width: 100%;
}
.index-kp .em-kpi-sub {
    margin-top: 6px;
    color: #6b7280;
    font-size: 12px;
    text-align: center;
}
.index-kp .em-kpi-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.index-kp .em-kpi-meta-label {
    font-size: 12px;
    color: #6b7280;
}
.index-kp .em-kpi-meta-note {
    font-size: 12px;
    color: #9ca3af;
}
.index-kp .em-kpi-foot-value {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #111827;
}
.index-kp .em-kpi-action .layui-btn {
    height: 28px;
    line-height: 28px;
    padding: 0 12px;
    border-radius: 999px;
}
.index-kp .layui-btn.layui-btn.layui-btn-xs.layui-border-blue {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.18), rgba(15, 118, 110, 0.08));
    color: #0b5f59;
    border: 1px solid rgba(15, 118, 110, 0.35);
    box-shadow: 0 8px 18px rgba(15, 118, 110, 0.18);
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}
.index-kp .layui-btn.layui-btn.layui-btn-xs.layui-border-blue:hover {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.28), rgba(15, 118, 110, 0.12));
    box-shadow: 0 10px 22px rgba(15, 118, 110, 0.22);
    transform: translateY(-1px);
}
.index-kp .layui-btn.layui-btn.layui-btn-xs.layui-border-blue:active {
    transform: translateY(0);
    box-shadow: 0 6px 14px rgba(15, 118, 110, 0.18);
}
.index-kp .em-kpi-pill {
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid transparent;
    background: rgba(15, 118, 110, 0.12);
    color: #0f766e;
}
.index-kp .em-kpi-pill--success {
    background: rgba(16, 185, 129, 0.16);
    color: #059669;
    border-color: rgba(16, 185, 129, 0.32);
}
.index-kp .em-kpi-pill--info {
    background: rgba(14, 165, 233, 0.16);
    color: #0284c7;
    border-color: rgba(14, 165, 233, 0.32);
}
.index-kp .em-kpi-pill--warning {
    background: rgba(245, 158, 11, 0.18);
    color: #b45309;
    border-color: rgba(245, 158, 11, 0.32);
}
.index-kp .em-kpi-card .auth-badge {
    border: 1px solid rgba(15, 23, 42, 0.12);
    font-weight: 600;
}
@media (max-width: 768px) {
    .index-kp .em-kpi-card {
        min-height: 168px;
    }
    .index-kp .em-kpi-value {
        font-size: 22px;
    }
}

.em-skeleton {
    position: relative;
    overflow: hidden;
    background: #E5E7EB;
}
.em-skeleton::after {
    content: '';
    position: absolute;
    top: 0;
    left: -150px;
    width: 150px;
    height: 100%;
    background: linear-gradient(90deg, rgba(255,255,255,0), rgba(255,255,255,0.7), rgba(255,255,255,0));
    animation: em-skeleton-loading 1.2s infinite;
}
.em-skeleton-line {
    height: 12px;
    border-radius: 6px;
}
.em-skeleton-tag {
    width: 44px;
    height: 18px;
    border-radius: 4px;
    display: inline-block;
}
.em-skeleton-w-40 { width: 40%; }
.em-skeleton-w-60 { width: 60%; }
.em-skeleton-w-80 { width: 80%; }
.em-skeleton-row {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    width: 100%;
}
@keyframes em-skeleton-loading {
    0% { left: -150px; }
    100% { left: 100%; }
}
</style>

<script>
(function(){
    const noticeBox = document.getElementById('notice-list');
    const recommendBox = document.getElementById('recommend-list');
    const contactItemEl = document.getElementById('notice-contact-item');
    const noticeExtraBox = document.getElementById('notice-extra-items');
    const contactOriginalHtml = contactItemEl ? contactItemEl.innerHTML : '';
    const noticeExtraOriginalHtml = noticeExtraBox ? noticeExtraBox.innerHTML : '';
    if (!noticeBox && !recommendBox) {
        return;
    }

    const escapeHtml = function(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };

    const safeUrl = function(value) {
        const url = String(value || '').trim();
        if (!url) return '';
        if (/^https?:\/\//i.test(url)) return url;
        if (url.indexOf('//') === 0) return url;
        if (/^(\/|\.\.?\/|\?|#)/.test(url)) return url;
        if (/^(mailto:|tel:)/i.test(url)) return url;
        return '';
    };

    const toArray = function(value) {
        if (!value) return [];
        if (Array.isArray(value)) return value;
        if (Array.isArray(value.list)) return value.list;
        if (Array.isArray(value.items)) return value.items;
        if (Array.isArray(value.data)) return value.data;
        return [];
    };

    const pickList = function(data, keys) {
        for (var i = 0; i < keys.length; i++) {
            if (!data) break;
            var list = toArray(data[keys[i]]);
            if (list.length) return list;
        }
        return [];
    };

    const hasHtmlTag = function(value) {
        return /<\/?[a-z][\s\S]*>/i.test(String(value || '').trim());
    };

    const isTruthyHtmlFlag = function(value) {
        if (value === true || value === 1) return true;
        if (typeof value !== 'string') return false;
        var normalized = value.trim().toLowerCase();
        return normalized === '1'
            || normalized === 'true'
            || normalized === 'yes'
            || normalized === 'y'
            || normalized === 'html'
            || normalized === 'code'
            || normalized === 'rich';
    };

    const extractHtml = function(data, keys) {
        for (var i = 0; i < keys.length; i++) {
            if (!data) break;
            var key = keys[i];
            var val = data[key];
            if (typeof val === 'string' && val.trim() !== '') {
                var keyName = String(key || '').toLowerCase();
                if (keyName.indexOf('html') !== -1 || keyName.indexOf('code') !== -1 || hasHtmlTag(val)) {
                    return val;
                }
            }
            if (val && typeof val === 'object') {
                if (typeof val.html === 'string' && val.html.trim() !== '') return val.html;
                if (typeof val.content_html === 'string' && val.content_html.trim() !== '') return val.content_html;
                if (typeof val.contentHtml === 'string' && val.contentHtml.trim() !== '') return val.contentHtml;
                if (typeof val.html_code === 'string' && val.html_code.trim() !== '') return val.html_code;
                if (typeof val.htmlCode === 'string' && val.htmlCode.trim() !== '') return val.htmlCode;
                if (typeof val.ad_code === 'string' && val.ad_code.trim() !== '') return val.ad_code;
                if (typeof val.adCode === 'string' && val.adCode.trim() !== '') return val.adCode;
            }
        }
        return '';
    };

    const buildTagStyle = function(bg, color) {
        var style = '';
        if (bg) style += 'background: ' + bg + ';';
        if (color) style += 'color: ' + color + ';';
        return style ? ' style="' + style + '"' : '';
    };

    const getItemRawHtml = function(item, title, content) {
        var html = item.html
            || item.content_html
            || item.contentHtml
            || item.title_html
            || item.titleHtml
            || item.html_code
            || item.htmlCode
            || item.ad_code
            || item.adCode
            || item.code_html
            || item.codeHtml
            || item.raw_html
            || item.rawHtml
            || '';
        if (typeof html === 'string' && html.trim() !== '') {
            return String(html);
        }
        var richContent = content || title || '';
        var richType = String(item.content_type || item.contentType || item.render_type || item.renderType || item.format || '').toLowerCase();
        var shouldUseRawHtml = isTruthyHtmlFlag(item.allow_html)
            || isTruthyHtmlFlag(item.allowHtml)
            || isTruthyHtmlFlag(item.is_html)
            || isTruthyHtmlFlag(item.isHtml)
            || isTruthyHtmlFlag(item.raw_html)
            || isTruthyHtmlFlag(item.rawHtml)
            || isTruthyHtmlFlag(item.render_html)
            || isTruthyHtmlFlag(item.renderHtml)
            || richType === 'html'
            || richType === 'code'
            || hasHtmlTag(content)
            || hasHtmlTag(title);
        return shouldUseRawHtml ? String(richContent) : '';
    };

    const setElementHtml = function(container, html) {
        if (!container) return;
        container.innerHTML = String(html || '');
        var scripts = container.querySelectorAll('script');
        for (var i = 0; i < scripts.length; i++) {
            var oldScript = scripts[i];
            if (!oldScript.parentNode) continue;
            var newScript = document.createElement('script');
            for (var j = 0; j < oldScript.attributes.length; j++) {
                var attr = oldScript.attributes[j];
                newScript.setAttribute(attr.name, attr.value);
            }
            newScript.text = oldScript.text || oldScript.textContent || oldScript.innerHTML || '';
            oldScript.parentNode.replaceChild(newScript, oldScript);
        }
    };

    const buildItemHtml = function(item) {
        if (typeof item === 'string') {
            item = { title: item };
        }
        item = item || {};
        var tag = item.tag || item.badge || item.label || item.type_name || '';
        var tagClass = item.tag_class || item.tagClass || '';
        var tagBg = item.tag_bg || item.tagBg || item.badge_bg || item.badgeBg || '';
        var tagColor = item.tag_color || item.tagColor || item.badge_color || item.badgeColor || '';
        var title = item.title || item.text || item.desc || item.name || '';
        var content = item.content || item.body || item.code || '';
        var html = getItemRawHtml(item, title, content);
        var url = item.url || item.link || item.href || item.jump || item.link_url || '';
        var target = item.target || (item.new_window || item.blank ? '_blank' : '');

        var tagStyle = buildTagStyle(tagBg, tagColor);
        var tagClassAttr = tagClass ? (' ' + tagClass) : '';
        var isRichContent = html !== '';
        var itemClass = 'notice-item';
        var titleClass = 'notice-title' + (isRichContent ? ' notice-title--rich' : '');
        var titleHtml = isRichContent ? html : escapeHtml(title || content);
        var tagHtml = tag ? ('<span class="notice-tag' + tagClassAttr + '"' + tagStyle + '>' + escapeHtml(tag) + '</span>') : '';
        var urlSafe = safeUrl(url);
        var contentTag = isRichContent ? 'div' : 'span';
        var itemTag = urlSafe ? 'a' : (isRichContent ? 'div' : 'span');
        var contentHtml = '<' + contentTag + ' class="' + titleClass + '">' + (titleHtml || '') + '</' + contentTag + '>';

        if (urlSafe) {
            var openBlank = target === '_blank' || /^https?:\/\//i.test(urlSafe) || urlSafe.indexOf('//') === 0;
            var targetAttr = openBlank ? ' target="_blank" rel="noopener"' : '';
            return '<a href="' + escapeHtml(urlSafe) + '" class="' + itemClass + '"' + targetAttr + '>' + tagHtml + contentHtml + '</a>';
        }
        return '<' + itemTag + ' class="' + itemClass + '">' + tagHtml + contentHtml + '</' + itemTag + '>';
    };

    const renderList = function(container, list) {
        if (!container || !Array.isArray(list) || !list.length) return;
        var html = '';
        list.forEach(function(item){
            html += buildItemHtml(item);
        });
        if (html) {
            setElementHtml(container, html);
        }
    };

    const renderLoading = function(container) {
        if (!container) return;
        container.innerHTML =
            '<span class="notice-item">' +
                '<span class="notice-tag em-skeleton em-skeleton-tag"></span>' +
                '<span class="notice-title em-skeleton-row">' +
                    '<span class="em-skeleton em-skeleton-line em-skeleton-w-80"></span>' +
                '</span>' +
            '</span>' +
            '<span class="notice-item">' +
                '<span class="notice-tag em-skeleton em-skeleton-tag"></span>' +
                '<span class="notice-title em-skeleton-row">' +
                    '<span class="em-skeleton em-skeleton-line em-skeleton-w-80"></span>' +
                '</span>' +
            '</span>';
    };

    const renderError = function(container, msg) {
        if (!container) return;
        var text = msg ? escapeHtml(msg) : '获取推荐服务失败';
        container.innerHTML =
            '<span class="notice-item">' +
                '<span class="notice-tag" style="background:#FEE2E2;color:#B91C1C;">错误</span>' +
                '<span class="notice-title">' + text + '</span>' +
            '</span>';
    };

    const renderEmptyAds = function(container) {
        if (!container) return;
        container.innerHTML =
            '<span class="notice-item">' +
                '<span class="notice-tag" style="background:#E5E7EB;color:#6B7280;">提示</span>' +
                '<span class="notice-title">暂无推荐</span>' +
            '</span>';
    };

    const splitBySlot = function(list) {
        var notice = [];
        var recommend = [];
        list.forEach(function(item){
            if (typeof item !== 'object' || item === null) return;
            var type = String(item.type || item.position || item.slot || item.group || item.category || '').toLowerCase();
            if (type.indexOf('notice') !== -1 || type.indexOf('官方公告') !== -1) {
                notice.push(item);
            } else if (type.indexOf('ad') !== -1 || type.indexOf('广告') !== -1 || type.indexOf('recommend') !== -1 || type.indexOf('service') !== -1 || type.indexOf('服务') !== -1) {
                recommend.push(item);
            }
        });
        return { notice: notice, recommend: recommend };
    };

    const buildContactItem = function(contact) {
        if (!contact || typeof contact !== 'object') return null;
        var parts = [];
        var qq = contact.qq || contact.service_qq || contact.kefu_qq || contact.kf_qq || '';
        var qqun = contact.qun || contact.qq_group || contact.group || contact.qqqun || '';
        var tg = contact.tg || contact.telegram || contact.tg_group || '';
        var tgUrl = contact.tg_url || contact.telegram_url || contact.tg_group_url || '';
        if (!tg && tgUrl) {
            tg = tgUrl;
        }
        if (qq) {
            parts.push('<span>客服QQ：<span>' + escapeHtml(qq) + '</span></span>');
        }
        if (qqun) {
            parts.push('<span style="margin-left: 20px;">QQ群：<span>' + escapeHtml(qqun) + '</span></span>');
        }
        if (tg) {
            var tgHtml = escapeHtml(tg);
            var tgSafe = safeUrl(tgUrl);
            if (tgSafe) {
                tgHtml = '<a href="' + escapeHtml(tgSafe) + '" target="_blank" rel="noopener">' + tgHtml + '</a>';
            }
            parts.push('<span style="margin-left: 20px;">TG群：' + tgHtml + '</span>');
        }
        if (!parts.length) return null;
        return { tag: '官方交流', html: parts.join('') };
    };

    const renderContactLoading = function() {
        if (!contactItemEl) return;
        contactItemEl.innerHTML =
            '<span class="notice-tag em-skeleton em-skeleton-tag"></span>' +
            '<span class="notice-title em-skeleton-row">' +
                '<span class="em-skeleton em-skeleton-line em-skeleton-w-80"></span>' +
            '</span>';
    };

    const renderNoticeExtraLoading = function() {
        if (!noticeExtraBox) return;
        noticeExtraBox.innerHTML =
            '<span class="notice-item">' +
                '<span class="notice-tag em-skeleton em-skeleton-tag"></span>' +
                '<span class="notice-title em-skeleton-row">' +
                    '<span class="em-skeleton em-skeleton-line em-skeleton-w-80"></span>' +
                '</span>' +
            '</span>';
    };

    const restoreContact = function() {
        if (!contactItemEl) return;
        contactItemEl.innerHTML = contactOriginalHtml;
    };

    const restoreNoticeExtra = function() {
        if (!noticeExtraBox) return;
        noticeExtraBox.innerHTML = noticeExtraOriginalHtml;
    };

    const renderContactItem = function(item) {
        if (!contactItemEl || !item) return;
        var tag = item.tag || '官方交流';
        var tagClass = item.tag_class || item.tagClass || '';
        if (tag === '官方交流') {
            tagClass = tagClass ? (tagClass + ' new') : 'new';
        }
        var tagBg = item.tag_bg || item.tagBg || '';
        var tagColor = item.tag_color || item.tagColor || '';
        var tagStyle = buildTagStyle(tagBg, tagColor);
        var tagClassAttr = tagClass ? (' ' + tagClass) : '';
        var tagHtml = '<span class="notice-tag' + tagClassAttr + '"' + tagStyle + '>' + escapeHtml(tag) + '</span>';
        var contentHtml = '<span class="notice-title">' + (item.html ? String(item.html) : '') + '</span>';
        setElementHtml(contactItemEl, tagHtml + contentHtml);
    };

    const renderNoticeExtra = function(list, html) {
        if (!noticeExtraBox) return;
        if (html) {
            setElementHtml(noticeExtraBox, html);
            return;
        }
        if (!Array.isArray(list) || !list.length) {
            restoreNoticeExtra();
            return;
        }
        var htmlStr = '';
        list.forEach(function(item){
            if (typeof item !== 'object' || item === null) {
                item = { title: item };
            }
            if (item) {
                if (!item.tag) item.tag = '官方公告';
                if (!item.url && item.link_url) item.url = item.link_url;
                if (!item.title && item.content) item.title = item.content;
            }
            htmlStr += buildItemHtml(item);
        });
        setElementHtml(noticeExtraBox, htmlStr);
    };

    const applyData = function(data) {
        data = data || {};
        var contactUpdated = false;
        var noticeHtml = extractHtml(data, ['notice_html', 'noticeHtml']) || extractHtml(data.notice, ['html', 'content_html', 'contentHtml', 'html_code', 'htmlCode']);
        var recommendHtml = extractHtml(data, ['ad_html', 'adHtml', 'recommend_html', 'recommendHtml', 'service_html', 'serviceHtml', 'html_code', 'htmlCode'])
            || extractHtml(data.ad || data.recommend || data.service, ['html', 'content_html', 'contentHtml', 'html_code', 'htmlCode', 'ad_code', 'adCode', 'code_html', 'codeHtml', 'content', 'code']);

        var noticeList = pickList(data, ['notice', 'notices', 'notice_list', 'noticeList', 'notice_items', 'noticeItems', 'announcements', 'announcement', 'announcement_list']);
        var recommendList = pickList(data, ['ad', 'ads', 'ad_list', 'adList', 'recommend', 'recommends', 'recommend_list', 'recommendList', 'service', 'services', 'service_list', 'serviceList']);

        if (!noticeList.length && !recommendList.length && Array.isArray(data.list)) {
            var grouped = splitBySlot(data.list);
            noticeList = grouped.notice;
            recommendList = grouped.recommend;
        }

        var agent = data.agent && typeof data.agent === 'object' ? data.agent : {};
        var contact = data.contact || data.contacts || {};
        if (!contact || typeof contact !== 'object') contact = {};
        if (!contact.qq && data.qq) contact.qq = data.qq;
        if (!contact.qq_group && data.qq_group) contact.qq_group = data.qq_group;
        if (!contact.tg && data.tg) contact.tg = data.tg;
        if (!contact.tg_url && data.tg_url) contact.tg_url = data.tg_url;
        if (!contact.qq && agent.service_qq) contact.qq = agent.service_qq;
        if (!contact.qq_group && agent.qq_group) contact.qq_group = agent.qq_group;
        if (!contact.tg_group && agent.tg_group) contact.tg_group = agent.tg_group;
        if (!contact.tg_url && agent.tg_group_url) contact.tg_url = agent.tg_group_url;
        var contactItem = buildContactItem(contact);
        if (contactItem) {
            if (contactItemEl) {
                renderContactItem(contactItem);
                contactUpdated = true;
            } else if (!noticeList.length) {
                noticeList = [contactItem];
            }
        }

        if (Array.isArray(recommendList) && recommendList.length) {
            recommendList = recommendList.map(function(item){
                if (typeof item !== 'object' || item === null) return item;
                var isAd = typeof item.link_url === 'string' || typeof item.expire_time !== 'undefined' || typeof item.content === 'string';
                if (isAd) {
                    if (!item.tag) item.tag = '推广';
                    if (!item.url && item.link_url) item.url = item.link_url;
                    if (!item.title && item.content) item.title = item.content;
                }
                return item;
            });
        }

        if (noticeExtraBox) {
            renderNoticeExtra(noticeList, noticeHtml);
        } else if (noticeBox && !contactItemEl) {
            if (noticeHtml) {
                setElementHtml(noticeBox, noticeHtml);
            } else if (noticeList.length) {
                renderList(noticeBox, noticeList);
            }
        }
        if (recommendBox) {
            if (recommendHtml) {
                setElementHtml(recommendBox, recommendHtml);
            } else if (recommendList.length) {
                renderList(recommendBox, recommendList);
            } else {
                renderEmptyAds(recommendBox);
            }
        }

        if (contactItemEl && !contactUpdated) {
            restoreContact();
        }
    };

    renderLoading(recommendBox);
    renderContactLoading();
    renderNoticeExtraLoading();

    $.ajax({
        url: '?action=admin_index',
        type: 'GET',
        dataType: 'json',
        success: function(e){
            if (e && e.code === 200) {
                applyData(e.data || {});
            } else if (e && e.code === 400) {
                renderError(recommendBox, e.msg || '获取推荐服务失败');
                restoreContact();
                restoreNoticeExtra();
            }
        },
        error: function(xhr){
            var msg = '';
            if (xhr && xhr.responseJSON && xhr.responseJSON.msg) {
                msg = xhr.responseJSON.msg;
            } else if (xhr && xhr.responseText) {
                msg = xhr.status + ' ' + xhr.statusText;
            }
            renderError(recommendBox, msg || '获取推荐服务失败');
            restoreContact();
            restoreNoticeExtra();
        }
    });
})();
</script>

<script>
// 线路延迟检测
(function() {
    const lineUrl = '<?= EM_LINE[CURRENT_LINE]['value'] ?>';
    const latencyEl = document.getElementById('line-latency');

    function testLatency() {
        const startTime = performance.now();
        const img = new Image();
        const timeout = 10000; // 10秒超时
        let finished = false;

        const timer = setTimeout(function() {
            if (!finished) {
                finished = true;
                latencyEl.innerHTML = '<span style="color: #EF4444; font-weight: 600;">超时</span>';
            }
        }, timeout);

        img.onload = img.onerror = function() {
            if (finished) return;
            finished = true;
            clearTimeout(timer);
            const endTime = performance.now();
            const latency = Math.round(endTime - startTime);

            let color = '#10B981';
            let bgColor = '#D1FAE5';
            let status = '极佳';
            if (latency > 500) {
                color = '#DC2626';
                bgColor = '#FEE2E2';
                status = '较慢';
            } else if (latency > 200) {
                color = '#D97706';
                bgColor = '#FEF3C7';
                status = '一般';
            } else if (latency > 100) {
                color = '#2563EB';
                bgColor = '#DBEAFE';
                status = '良好';
            }

            latencyEl.innerHTML =
                '<span style="display: inline-flex; align-items: center; gap: 6px;">' +
                    '<span style="font-size: 14px; font-weight: 700; color: ' + color + ';">' + latency + 'ms</span>' +
                    '<span style="padding: 2px 6px; background: ' + bgColor + '; color: ' + color + '; border-radius: 4px; font-size: 11px; font-weight: 600;">' + status + '</span>' +
                    '<i class="layui-icon layui-icon-refresh" onclick="window.retestLatency()" style="cursor: pointer; color: #9CA3AF; font-size: 14px;" title="重新检测"></i>' +
                '</span>';
        };

        img.src = lineUrl + '/favicon.ico?t=' + Date.now();
    }

    window.retestLatency = function() {
        latencyEl.innerHTML = '<i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop" style="font-size: 14px; color: #9CA3AF;"></i>';
        setTimeout(testLatency, 100);
    };

    setTimeout(testLatency, 500);
})();
</script>

