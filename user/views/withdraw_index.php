<?php
defined('TT_ROOT') || exit('access denied!');
?>
<style>
    /* 主内容区样式 */
    .main-content {
        padding: 20px 15px;
    }


</style>

<!-- 主内容区 -->
<main class="main-content">
    <div class="layui-panel">
        <table class="layui-hide" id="index" lay-filter="index"></table>

        <script type="text/html" id="money">
            <div class="">
                {{# if(d.plus == 'y'){ }}
                <div><span class="layui-badge layui-bg-blue">+ {{ d.money }}</span></div>
                {{#  } }}
                {{# if(d.plus == 'n'){ }}
                <div><span class="layui-badge layui-bg-cyan">- {{ d.money }}</span></div>
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
        var layer = layui.layer;
// 创建渲染实例
        window.table = table.render({
            elem: '#index',
            autoSort: false,
            url: '?action=withdraw_list', // 此处为静态模拟数据，实际使用时需换成真实接口
            toolbar: '#toolbar',
            limits: [10,20,30,50,100,200,500,1000],
            page: true,
            lineStyle: 'height: 30px;',
            defaultToolbar: ['filter', 'exports'],


            cols: [[
                {field:'amount', title: '提现金额', minWidth: 180},
                {field:'method', title:'提现方式', minWidth: 130},
                {field:'account', title:'账户信息', minWidth: 130},
                {field:'realname', title:'真实姓名', minWidth: 180},
                {field:'remark', title:'备注', minWidth: 180},
                {field:'status_text', title:'提现状态', minWidth: 180},
                {field:'create_time', title:'申请时间', minWidth: 180},
            ]],

            error: function(res, msg){
                console.log(res, msg)
            }
        });



        // 触发单元格工具事件
        table.on('tool(index)', function(obj){ // 双击 toolDouble
            var data = obj.data; // 获得当前行数据
            var id = obj.config.id;



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
    $('#menu-balance').addClass('open');
    $('#menu-balance > ul').css('display', 'block');
    $('#menu-balance > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-balance-withdraw').addClass('menu-current');
</script>