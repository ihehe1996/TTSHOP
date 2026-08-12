<?php
defined('EM_ROOT') || exit('access denied!');
?>
<style>
    .template-page {
        padding: 0;
        background: transparent;
    }
    .template-page .template-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .template-page .template-toolbar-left,
    .template-page .template-toolbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .template-page .template-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
    }
    .template-page .template-sub {
        font-size: 12px;
        color: var(--muted);
    }
    .template-page .template-count {
        color: var(--muted);
        font-size: 13px;
    }
    .template-page .template-table-wrap {
        background: #fff;
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        overflow-x: auto;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }
    .template-page .template-table-wrap .layui-table-view {
        border: 0;
        border-radius: 16px;
        box-shadow: none;
        margin: 0;
    }
    .template-page .template-table-wrap .layui-table {
        min-width: 960px;
        table-layout: fixed;
        border: 0;
    }
    .template-page .template-table-wrap .layui-table-header {
        background: linear-gradient(180deg, #f9fafb 0%, #f2f4f6 100%);
    }
    .template-page .template-table-wrap .layui-table-header table,
    .template-page .template-table-wrap .layui-table-header thead,
    .template-page .template-table-wrap .layui-table-header tr {
        background: linear-gradient(180deg, #f9fafb 0%, #f2f4f6 100%);
    }
    .template-page .template-table-wrap .layui-table-header th {
        background: transparent;
        color: #5f6b74;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.3px;
        border-bottom: 1px solid #e6e6e6;
        border-left: none;
        border-right: none;
    }
    .template-page .template-table-wrap .layui-table-header th .layui-table-cell {
        text-align: left;
    }
    .template-page .template-table-wrap .layui-table-body td {
        color: #262626;
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0;
        border-left: none;
        border-right: none;
        vertical-align: middle;
    }
    .template-page .template-table-wrap .layui-table-body tr:nth-child(even) td {
        background: #fcfcfd;
    }
    .template-page .template-table-wrap .layui-table-body tr:hover td {
        background: #f3f8f6;
    }
    .template-page .template-table-wrap .layui-table-body tr:last-child td {
        border-bottom: none;
    }
    .template-page .template-table-wrap .layui-table-view .layui-table-cell {
        padding: 14px 16px;
        height: auto;
        line-height: 1.5;
        box-sizing: border-box;
    }
    .template-page .cover {
        width: 46px;
        height: 46px;
        border-radius: 6px;
        overflow: hidden;
        background: #f5f5f5;
        border: 1px solid #eee;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .template-page .template-table-wrap .layui-table-body tr:hover .cover {
        transform: scale(1.05);
    }
</style>

<div class="template-page">
    <div class="template-toolbar">
        <div class="template-toolbar-left">
            <span class="template-title">店铺模板</span>
            <span class="template-sub">选择分站 PC/手机模板</span>
        </div>
        <div class="template-toolbar-right">
            <span class="template-count">共 <strong id="template-count">0</strong> 个模板</span>
        </div>
    </div>
    <div class="template-table-wrap">
        <table class="layui-hide" id="template-index" lay-filter="template-index"></table>
    </div>
</div>

<script type="text/html" id="cover">
    <div class="layui-clear-space">
        <a href="javascript:;" data-id="{{ d.tplfile }}" lay-event="img">
            <img onerror="this.onerror=null; this.src='<?= EM_URL ?>admin/views/images/null.png'" class="cover" data-img="{{ d.preview }}" src="{{ d.preview }}" />
        </a>
    </div>
</script>

<script type="text/html" id="title">
    <div>
        <span>{{ d.tplname }}</span>
    </div>
</script>

<script type="text/html" id="switch">
    <input type="checkbox" name="{{= d.tplfile }}" value="{{= d.tplfile }}" title=" ON |OFF " lay-skin="switch" lay-filter="switch" {{= d.switch == 'y' ? "checked" : "" }}>
</script>
<script type="text/html" id="tel_switch">
    <input type="checkbox" name="{{= d.tplfile }}" value="{{= d.tplfile }}" title=" ON |OFF " lay-skin="switch" lay-filter="tel_switch" {{= d.tel_switch == 'y' ? "checked" : "" }}>
</script>
<script type="text/html" id="operate">
    <div class="layui-clear-space">
        {{#  if(d.has_setting == 'y'){ }}
        <a class="layui-btn" lay-event="setting">配置</a>
        {{#  } else { }}
        <span class="layui-badge layui-bg-gray">无配置</span>
        {{#  } }}
    </div>
</script>

<script>
    layui.use(['table', 'form'], function(){
        var table = layui.table;
        var form = layui.form;
        var $ = layui.$;

        window.templateTable = table.render({
            elem: '#template-index',
            autoSort: false,
            url: '?action=template_index',
            toolbar: false,
            limits: [10,20,30,50,100],
            page: false,
            defaultToolbar: [],
            cols: [[
                {field:'name', title:'图片', width: 80, templet: '#cover', align: 'center'},
                {field:'title', title:'模板名称', minWidth: 170, templet: '#title'},
                {field:'version', title:'版本号', width: 130, align: 'center' },
                {field:'switch', title:'启用（电脑）', align: 'center', width: 180, templet: '#switch'},
                {field:'tel_switch', title:'启用（手机）', align: 'center', width: 180, templet: '#tel_switch'},
                {title:'操作', templet: '#operate', width: 120, align: 'left'}
            ]],
            done: function(res, curr, count){
                var total = typeof count !== 'undefined' ? count : (res.data ? res.data.length : 0);
                $('#template-count').text(total);
            },
            error: function(res, msg){
                console.log(res, msg)
            }
        });

        form.on('switch(switch)', function(obj){
            var active = obj.elem.checked == true ? 1 : 0;
            var tpl = this.name;
            var loadSwitch = layer.load(2);
            $.ajax({
                url: '?action=template_use',
                type: 'POST',
                dataType: 'json',
                data: { tpl: tpl, status: active, token: '<?= LoginAuth::genToken() ?>' },
                success: function(e) {
                    if(e.code == 400){
                        layer.msg(e.msg);
                        return table.reload('template-index');
                    }
                    layer.msg('操作成功');
                    table.reload('template-index');
                },
                error: function(err) {
                    layer.msg(err.responseJSON ? err.responseJSON.msg : '请求失败');
                },
                complete: function() {
                    layer.close(loadSwitch);
                }
            });
        });

        form.on('switch(tel_switch)', function(obj){
            var active = obj.elem.checked == true ? 1 : 0;
            var tpl = this.name;
            var loadSwitch = layer.load(2);
            $.ajax({
                url: '?action=template_use_tel',
                type: 'POST',
                dataType: 'json',
                data: { tpl: tpl, status: active, token: '<?= LoginAuth::genToken() ?>' },
                success: function(e) {
                    if(e.code == 400){
                        layer.msg(e.msg);
                        return table.reload('template-index');
                    }
                    layer.msg('操作成功');
                    table.reload('template-index');
                },
                error: function(err) {
                    layer.msg(err.responseJSON ? err.responseJSON.msg : '请求失败');
                },
                complete: function() {
                    layer.close(loadSwitch);
                }
            });
        });

        table.on('tool(template-index)', function(obj){
            var data = obj.data;
            if(obj.event === 'img'){
                layer.photos({
                    photos: {
                        "title": data.tplname,
                        "start": 0,
                        "data": [
                            {
                                "alt": data.tplname,
                                "pid": 1,
                                "src": data.preview
                            }
                        ]
                    }
                });
            }
            if(obj.event === 'setting'){
                let isMobile = window.innerWidth < 1200;
                let area = isMobile ? ['98%', '85%']  : ['1000px', '80%'];
                layer.open({
                    id: 'setting',
                    title: '配置',
                    type: 2,
                    area: area,
                    skin: 'em-modal',
                    content: '/user/template.php?action=setting_page&tpl=' + data.tplfile,
                    fixed: false,
                    scrollbar: false,
                    maxmin: true,
                    shadeClose: true
                });
            }
        });
    });
</script>

<script>
    $('#menu-station').addClass('open');
    $('#menu-station > ul').css('display', 'block');
    $('#menu-station > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-station-template').addClass('menu-current');
</script>
