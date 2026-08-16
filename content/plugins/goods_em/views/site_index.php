<?php
/**
 * TTSHOP 同系统对接 - 站点列表
 */

defined('TT_ROOT') || exit('access denied!');
?>
<style>
    .tt-container {
        padding: 20px;
        background: #f6f8fa;
        min-height: calc(100vh - 120px);
    }
    .tt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .tt-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tt-header h3::before {
        content: '';
        width: 4px;
        height: 20px;
        background: linear-gradient(180deg, #4C7D71, #6BA596);
        border-radius: 2px;
    }
    .tt-actions {
        display: flex;
        gap: 10px;
    }
    .tt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        line-height: 1.5;
        border: none;
        gap: 6px;
    }
    .tt-btn-primary {
        background: linear-gradient(90deg, #4C7D71 0%, #6BA596 100%);
        color: #fff;
    }
    .tt-btn-primary:hover {
        background: linear-gradient(90deg, #3D6A5F 0%, #5A9485 100%);
        box-shadow: 0 4px 12px rgba(76, 125, 113, 0.35);
        transform: translateY(-1px);
    }
    .tt-btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    .tt-btn-secondary:hover {
        background: #e5e7eb;
        border-color: #4C7D71;
        color: #4C7D71;
    }
    .tt-btn-danger {
        background: linear-gradient(90deg, #dc2626, #ef4444);
        color: #fff;
    }
    .tt-btn-danger:hover {
        background: linear-gradient(90deg, #b91c1c, #f87171);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
        transform: translateY(-1px);
    }
    .tt-btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }
    .tt-site-list {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }
    .tt-site-card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e5e8eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .tt-site-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #4C7D71, #6BA596);
    }
    .tt-site-card:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .tt-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .tt-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        word-break: break-all;
        max-width: 220px;
    }
    .tt-card-balance {
        color: #059669;
        font-size: 15px;
        font-weight: 600;
        background: #ecfdf5;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid #d1fae5;
    }
    .tt-card-info {
        color: #666;
        font-size: 13px;
        line-height: 2.0;
        margin-bottom: 16px;
    }
    .tt-card-info .label {
        color: #9ca3af;
        font-size: 12px;
        min-width: 52px;
        display: inline-block;
    }
    .tt-card-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
        margin-top: auto;
    }
    .tt-site-add {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px dashed #d1d5db;
        background: #fafbfc;
    }
    .tt-site-add::before {
        display: none;
    }
    .tt-site-add:hover {
        border-color: #4C7D71;
        background: #f0fdf4;
    }
    .tt-add-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        transition: all 0.2s;
        color: #6b7280;
    }
    .tt-site-add:hover .tt-add-icon {
        background: #4C7D71;
        color: #fff;
    }
    .tt-add-text {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }
    .tt-empty {
        text-align: center;
        padding: 80px 20px;
        color: #9ca3af;
        background: #fff;
        border-radius: 12px;
        border: 1px dashed #e5e8eb;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        grid-column: 1 / -1;
    }
    .tt-loading {
        text-align: center;
        padding: 60px 20px;
        color: #999;
        grid-column: 1 / -1;
    }
    .tt-modal-mask {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,.5);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .tt-modal-mask.active {
        display: flex;
    }
    .tt-modal {
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 620px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: ttModalIn 0.25s ease;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
    }
    .tt-modal.tt-modal-lg {
        max-width: 860px;
    }
    @keyframes ttModalIn {
        from { opacity: 0; transform: scale(0.96) translateY(-8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .tt-modal-header {
        height: 56px;
        padding: 0 20px;
        background: #fff;
        border-bottom: 1px solid #eef2f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        position: relative;
    }
    .tt-modal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #4C7D71, #6BA596);
    }
    .tt-modal-title {
        color: #1a1a1a;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tt-modal-title::before {
        content: '';
        width: 4px;
        height: 18px;
        background: linear-gradient(180deg, #4C7D71, #6BA596);
        border-radius: 2px;
    }
    .tt-modal-close {
        width: 32px;
        height: 32px;
        background: #f5f7f9;
        border: none;
        border-radius: 16px;
        color: #666;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .tt-modal-close:hover {
        background: #fee2e2;
        color: #dc2626;
        transform: rotate(90deg);
    }
    .tt-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
        background: #fff;
    }
    .tt-modal-footer {
        padding: 16px 24px;
        background: #fafbfc;
        border-top: 1px solid #eef2f5;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
    }
    .tt-modal-footer.hidden {
        display: none;
    }
    @media (max-width: 768px) {
        .tt-modal {
            width: 95%;
            max-height: 90vh;
        }
    }
</style>

<div class="tt-container">
    <div class="tt-header">
        <h3>站点管理</h3>
        <div class="tt-actions">
            <button class="tt-btn tt-btn-primary" id="addSiteBtn">
                <i class="layui-icon layui-icon-add-1"></i> 添加站点
            </button>
            <button class="tt-btn tt-btn-secondary" id="refreshBalance">
                <i class="layui-icon layui-icon-refresh"></i> 刷新余额
            </button>
        </div>
    </div>

    <div class="tt-site-list" id="siteList">
        <div class="tt-loading">加载中...</div>
    </div>
</div>

<div class="tt-modal-mask" id="ttModalMask">
    <div class="tt-modal" id="ttModalBox">
        <div class="tt-modal-header">
            <span class="tt-modal-title" id="ttModalTitle">标题</span>
            <button class="tt-modal-close" type="button" id="ttModalClose">&times;</button>
        </div>
        <div class="tt-modal-body" id="ttModalBody"></div>
        <div class="tt-modal-footer hidden" id="ttModalFooter"></div>
    </div>
</div>

<script>
layui.use(['layer', 'jquery'], function(){
    var layer = layui.layer;
    var $ = layui.$;
    var apiBase = '<?= TT_URL ?>?plugin=goods_em';
    var isLoading = false;

    function openModal(title, url, isLarge) {
        var mask = $('#ttModalMask');
        var titleEl = $('#ttModalTitle');
        var body = $('#ttModalBody');
        var footer = $('#ttModalFooter');
        var box = $('#ttModalBox');

        titleEl.text(title);
        body.html('<div style="padding: 20px; text-align: center; color: #999;">加载中...</div>');
        footer.addClass('hidden').empty();
        mask.addClass('active');
        box.toggleClass('tt-modal-lg', !!isLarge);

        $.get(url, function(html) {
            body.html(html);

            var actions = body.find('.tt-form-actions');
            if (actions.length) {
                footer.empty().append(actions.children());
                footer.removeClass('hidden');
                actions.remove();
            }

            body.find('script').each(function() {
                var newScript = document.createElement('script');
                newScript.text = this.text || this.textContent || '';
                document.body.appendChild(newScript);
                document.body.removeChild(newScript);
            });
        });
    }

    function openSiteForm(id) {
        openModal(
            id ? '编辑站点' : '添加站点',
            '<?= TT_URL ?>?plugin=goods_em&do=site_form&id=' + id
        );
    }
    function openImport(siteId) {
        openModal(
            '导入商品',
            '<?= TT_URL ?>?plugin=goods_em&do=import&site_id=' + siteId,
            true
        );
    }
    function renderSites(sites) {
        var html = '';
        if (!sites || sites.length === 0) {
            html = '<div class="tt-empty">';
            html += '<div style="font-size:18px;color:#374151;font-weight:600;">暂无站点</div>';
            html += '<div style="color:#6b7280;">请先添加对接站点</div>';
            html += '<button class="tt-btn tt-btn-primary" id="emptyAddBtn">立即添加</button>';
            html += '</div>';
            $('#siteList').html(html);
            return;
        }

        html += '<div class="tt-site-card tt-site-add" id="cardAddSite">';
        html += '<div class="tt-add-icon"><i class="layui-icon layui-icon-add-1"></i></div>';
        html += '<div class="tt-add-text">添加新站点</div>';
        html += '</div>';

        sites.forEach(function(site){
            var balance = parseFloat(site.balance || 0).toFixed(2);
            html += '<div class="tt-site-card">';
            html += '<div class="tt-card-header">';
            html += '<div class="tt-card-title" title="' + (site.sitename || '未知站点') + '">' + (site.sitename || '未知站点') + '</div>';
            html += '<div class="tt-card-balance">¥' + balance + '</div>';
            html += '</div>';
            html += '<div class="tt-card-info">';
            html += '<div><span class="label">域名</span>' + (site.domain || '-') + '</div>';
            html += '<div><span class="label">用户ID</span>' + (site.app_id || '-') + '</div>';
            html += '</div>';
            html += '<div class="tt-card-actions">';
            html += '<button class="tt-btn tt-btn-primary tt-btn-sm btn-import" data-id="' + site.id + '">导入商品</button>';
            html += '<button class="tt-btn tt-btn-secondary tt-btn-sm btn-edit" data-id="' + site.id + '">编辑</button>';
            html += '<button class="tt-btn tt-btn-danger tt-btn-sm btn-delete" data-id="' + site.id + '">删除</button>';
            html += '</div>';
            html += '</div>';
        });

        $('#siteList').html(html);
    }

    function loadSites(refresh) {
        if (isLoading) return;
        isLoading = true;
        if (!refresh) {
            $('#siteList').html('<div class="tt-loading">加载中...</div>');
        }
        $.post(apiBase, {action: 'site_list', refresh: refresh ? 1 : 0}, function(res){
            if (res.code === 0) {
                renderSites(res.data);
                if (refresh) {
                    layer.msg('刷新成功', {icon: 1});
                }
            } else {
                $('#siteList').html('<div class="tt-empty">加载失败：' + (res.msg || '未知错误') + '</div>');
            }
        }, 'json').always(function(){
            isLoading = false;
        });
    }

    window.closeTtModal = function(reload) {
        $('#ttModalMask').removeClass('active');
        $('#ttModalFooter').addClass('hidden').empty();
        $('#ttModalBody').empty();
        if (reload) {
            loadSites();
        }
    };

    $('#addSiteBtn').on('click', function(){
        openSiteForm(0);
    });

    $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        openSiteForm(id);
    });
    $(document).on('click', '.btn-import', function(){
        var id = $(this).data('id');
        openImport(id);
    });
    $(document).on('click', '#cardAddSite, #emptyAddBtn', function(){
        openSiteForm(0);
    });

    $('#ttModalClose').on('click', function(){
        window.closeTtModal(false);
    });

    // 点击遮罩关闭（已禁用 - 不允许点击遮罩层关闭窗口）
    // $('#ttModalMask').on('click', function(e){
    //     if (e.target === this) {
    //         window.closeTtModal(false);
    //     }
    // });

    $(document).on('keydown', function(e){
        if (e.key === 'Escape') {
            window.closeTtModal(false);
        }
    });

    $('#refreshBalance').on('click', function(){
        loadSites(true);
    });

    $(document).on('click', '.btn-delete', function(){
        var id = $(this).data('id');
        layer.confirm('确定要删除此站点吗？', {icon: 3, title: '提示'}, function(index){
            $.post(apiBase, {action: 'site_delete', id: id}, function(res){
                layer.close(index);
                if(res.code === 0){
                    layer.msg('删除成功', {icon: 1});
                    loadSites();
                } else {
                    layer.msg(res.msg || '删除失败', {icon: 2});
                }
            }, 'json');
        });
    });

    loadSites();
});
</script>
