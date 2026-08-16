<?php defined('TT_ROOT') || exit('access denied!'); ?>

<table class="layui-hide" id="index" lay-filter="index"></table>
<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn" lay-event="refresh">
            <i class="fa fa-refresh mr-3"></i>刷新
        </button>

    </div>
</script>
<script type="text/html" id="cover">
    <div class="layui-clear-space">
        <a href="javascript:;" data-id="{{ d.id }}" lay-event="img">
            <img onerror="this.onerror=null; this.src='./views/images/null.png'" class="cover" data-img="{{ d.icon }}" src="{{ d.icon }}" style="width: 40px; border-radius: 3px;" />
        </a>
    </div>
</script>



<script type="text/html" id="is_domain">
    <input type="checkbox" name="{{= d.id }}" value="{{= d.id }}" title=" ON |OFF " lay-skin="switch" lay-filter="switch-isdomain" {{= d.is_domain == 'y' ? "checked" : "" }}>
</script>
<script type="text/html" id="is_goods">
    <input type="checkbox" name="{{= d.id }}" value="{{= d.id }}" title=" ON |OFF " lay-skin="switch" lay-filter="switch-isgoods" {{= d.is_goods == 'y' ? "checked" : "" }}>
</script>
<script type="text/html" id="is_station">
    <input type="checkbox" name="{{= d.id }}" value="{{= d.id }}" title=" ON |OFF " lay-skin="switch" lay-filter="switch-isstation" {{= d.is_station == 'y' ? "checked" : "" }}>
</script>

<script type="text/html" id="operate">
    <div class="layui-clear-space">
        <a class="layui-btn layui-btn-red" lay-event="del">删除</a>
    </div>
</script>


<script>
    layui.use(['table'], function(){
        var table = layui.table;
        var form = layui.form;
        // 创建渲染实例
        window.table = table.render({
            elem: '#index',
            autoSort: false,
            url: '?action=lists_index', // 此处为静态模拟数据，实际使用时需换成真实接口
            toolbar: '#toolbar',
            limits: [],
            page: false,
            lineStyle: 'height: 30px;',
            defaultToolbar: ['filter', 'exports'],


            cols: [[
                {type: 'checkbox'},
                {field:'level_name', title:'等级名称', minWidth: 130 },
                {field:'name', title:'分站名称', minWidth: 130 },
                {field:'tel', title:'站长手机', minWidth: 130 },
                {field:'email', title:'站长邮箱', minWidth: 150 },
                {field:'domain_2', title:'二级域名', minWidth: 150 },
                {field:'domain', title:'独立域名', minWidth: 150 },
                {field:'money', title:'店铺余额', minWidth: 100 },
                {field:'create_time', title:'开通时间', minWidth: 160 },
                {title:'操作', templet: '#operate', width: 100, align: 'center'}
            ]],

            error: function(res, msg){
                console.log(res, msg)
            }
        });





        // 工具栏事件
        table.on('toolbar(index)', function(obj){
            var id = obj.config.id;
            var checkStatus = table.checkStatus(id);
            var othis = lay(this);
            if(obj.event == 'refresh'){
                table.reload(id);
            }


        });

        // 触发单元格工具事件
        table.on('tool(index)', function(obj){ // 双击 toolDouble
            var data = obj.data; // 获得当前行数据
            var id = obj.config.id;
            if(obj.event == 'del'){
                layer.confirm('确定删除？', {
                    btn: ['确认', '取消'], // 按钮
                    icon: 3,             // 图标，3表示问号
                    title: '温馨提示'
                }, function(index) {
                    layer.close(index); // 关闭对话框
                    $.ajax({
                        url: '?action=lists_del',
                        type: 'POST',
                        dataType: 'json',
                        data: { ids: data.id, token: '<?= LoginAuth::genToken() ?>' },
                        success: function(e) {
                            if(e.code == 400){
                                return layer.msg(e.msg)
                            }
                            layer.msg('删除成功');
                            table.reload(id);
                        },
                        error: function(err) {
                            layer.msg(err.responseJSON.msg);
                        }
                    });
                });
            }

            if(obj.event === 'img'){
                layer.photos({
                    photos: {
                        "title": data.title,
                        "start": 0,
                        "data": [
                            {
                                "alt": data.sitename,
                                "pid": 1,
                                "src": data.icon,
                            }
                        ]
                    }
                });
            }
            if(obj.event === 'edit'){
                let isMobile = window.innerWidth < 768;
                let area = isMobile ? ['98%', 'auto']  : ['700px', 'auto'];
                layer.open({
                    id: 'edit',
                    title: '编辑',
                    type: 2,
                    area: area,
                    // skin: 'layui-layer-molv',
                    skin: 'layui-layer-molv',
                    content: '?action=level_edit&id=' + data.id,
                    fixed: false, // 不固定
                    maxmin: true,
                    shadeClose: true,
                    success: function(layero, index, that){
                        layer.iframeAuto(index); // 让 iframe 高度自适应
                        that.offset(); // 重新自适应弹层坐标
                    }
                });
            }
        });

// 触发表格复选框选择
        table.on('checkbox(index)', function(obj){
            var id = obj.config.id;
            var checkData = table.checkStatus(id).data;
            console.log(checkData)
            if(checkData.length == 0){
                $('#toolbar-del').addClass('layui-btn-disabled');
            }else{
                $('#toolbar-del').removeClass('layui-btn-disabled');
            }
        });



    });
</script>

<script>
    $("#menu-station").attr('class', 'admin-menu-item has-list in');
    $("#menu-station .fa-angle-right").attr('class', 'admin-arrow fa fa-angle-right active');
    $("#menu-station > .submenu").css('display', 'block');
    $('#menu-station-lists > a').attr('class', 'menu-link active')
</script>
