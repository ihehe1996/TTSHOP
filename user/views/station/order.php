<?php
defined('EM_ROOT') || exit('access denied!');
?>
<style>
    /* 主内容区样式 */
    .main-content {
        padding: 20px 15px;
    }

</style>
<style>


    .main-content .layui-panel{
        border-radius: 10px;
    }
    .layui-form-label{
        /*font-weight: bold;*/
        color: #a855f7;
    }
</style>

<!-- 主内容区 -->
<main class="main-content">



    <div class="layui-panel">



        <table class="layui-hide" id="index" lay-filter="index"></table>
        <script type="text/html" id="toolbar">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="refresh">刷新</button>
            </div>
        </script>
        <script type="text/html" id="goods">
            <div class="goods-info">
                <div class="row-1-hidden">{{ d.list[0].title }}</div>
                <div>规格：{{ d.list[0].attr_spec }}</div>
            </div>
        </script>

        <script type="text/html" id="quantity">
            <div class="goods-info">
                <div>{{ d.list[0].quantity }}</div>
            </div>
        </script>
        <script type="text/html" id="userinfo">
            <div class="goods-info">
                <div>{{ d.user_nickname }}</div>
                {{# if(d.user_id == 0){ }}
                <div>游客身份</div>
                {{#  } }}
                {{# if(d.user_email != ''){ }}
                <div>{{ d.user_email }}</div>
                {{#  } }}
                {{# if(d.user_tel != ''){ }}
                <div>{{ d.user_tel }}</div>
                {{#  } }}
            </div>
        </script>


    </div>


</main>

<script>
    // 初始化Layui模块
    layui.use(['element', 'layer'], function() {
        var table = layui.table;
        var form = layui.form;
// 创建渲染实例
        window.table = table.render({
            elem: '#index',
            autoSort: false,
            url: '?action=order_index', // 此处为静态模拟数据，实际使用时需换成真实接口
            toolbar: '#toolbar',
            limits: [10,20,30,50,100,200,500,1000],
            page: true,
            lineStyle: 'height: 30px;',
            defaultToolbar: ['filter', 'exports'],


            cols: [[
                {field:'out_trade_no', title: '订单号', width: 180},
                {field:'goods', title:'商品信息',templet: '#goods', minWidth: 200, maxWidth: 500},
                {field:'id', title: '数量', width: 50, templet: '#quantity'},
                {field:'amount', title:'订单金额', width: 100},
                {field:'user_email', title:'用户信息', minWidth: 130, maxWidth: 180, templet: '#userinfo'},
                {field:'status_text', title:'订单状态', width: 100},
                {field:'payment', title:'支付方式', width: 110},
                {field:'pay_time', title:'支付时间', width: 170},
            ]],

            error: function(res, msg){
                console.log(res, msg)
            }
        });

        // 状态 - 开关操作
        form.on('switch(switch)', function(obj){
            var is_show = obj.elem.checked == true ? 'y' : 'n';
            var id = this.value;
            var loadSwitch = layer.load(2);
            $.ajax({
                url: '?action=master_goods_switch',
                type: 'POST',
                dataType: 'json',
                data: { id: id, is_show: is_show, token: '<?= LoginAuth::genToken() ?>' },
                success: function(e) {
                    if(e.code == 400){
                        layer.msg(e.msg)
                    }else{
                        layer.msg('操作成功');
                    }

                },
                error: function(err) {
                    layer.msg(err.responseJSON.msg);
                },
                complete: function() {
                    layer.close(loadSwitch);
                }
            });
        });


        // 工具栏事件
        table.on('toolbar(index)', function(obj){
            var id = obj.config.id;
            var checkStatus = table.checkStatus(id);
            var othis = lay(this);
            if(obj.event == 'refresh'){
                table.reload(id);
            }

            if(obj.event == 'premium'){

                let isMobile = window.innerWidth < 768;
                let area = isMobile ? ['98%', 'auto']  : ['700px', 'auto'];
                layer.open({
                    id: 'edit',
                    title: '统一加价设置',
                    type: 2,
                    area: area,
                    skin: 'layui-layer-molv',
                    content: '?action=master_goods_premium',
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

        // 触发单元格工具事件
        table.on('tool(index)', function(obj){ // 双击 toolDouble
            var data = obj.data; // 获得当前行数据
            var id = obj.config.id;


            if(obj.event === 'img'){
                layer.photos({
                    photos: {
                        "title": data.title,
                        "start": 0,
                        "data": [
                            {
                                "alt": data.title,
                                "pid": 1,
                                "src": data.cover,
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
                    skin: 'layui-layer-molv',
                    content: '?action=master_goods_edit&goods_id=' + data.id,
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
                $('.toolbar-select').addClass('layui-btn-disabled');
            }else{
                $('.toolbar-select').removeClass('layui-btn-disabled');
            }
        });

    });
</script>

<script>
    $('#menu-station').addClass('open');
    $('#menu-station > ul').css('display', 'block');
    $('#menu-station > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-station-order').addClass('menu-current');
</script>
