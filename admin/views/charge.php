<?php
defined('TT_ROOT') || exit('access denied!');
?>

<table class="layui-hide" id="index" lay-filter="index"></table>
<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn" lay-event="refresh">
            <i class="fa fa-refresh mr-3"></i>刷新
        </button>
        <button id="toolbar-del" class="layui-btn layui-btn-red layui-btn-disabled" lay-event="del">
            删除选中
        </button>
    </div>
</script>
<script type="text/html" id="cover">
    <div class="layui-clear-space">
        <a href="javascript:;" data-id="{{ d.id }}" lay-event="img">
            <img onerror="this.onerror=null; this.src='./views/images/null.png'" class="cover" data-img="{{ d.cover }}" src="{{ d.cover }}" style="width: 40px; border-radius: 3px;" />
        </a>
    </div>
</script>


<script type="text/html" id="type">
    <div class="layui-clear-space">
        <span>{{ d.type_text }}</span>
    </div>
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

        // 从本地存储读取每页条数（默认10）
        var pageSize = localStorage.getItem('goods_limit') || 10;
        pageSize = parseInt(pageSize); // 确保是数字类型

        // 创建渲染实例
        window.table = table.render({
            elem: '#index',
            autoSort: false,
            url: '?action=index', // 此处为静态模拟数据，实际使用时需换成真实接口
            toolbar: '#toolbar',
            limits: [10,20,30,50,100],
            page: true,
            limit: pageSize,
            lineStyle: 'height: 30px;',
            defaultToolbar: ['filter', 'exports', 'print', { // 右上角工具图标
                title: '提示',
                layEvent: 'LAYTABLE_TIPS',
                icon: 'layui-icon-tips',
                onClick: function(obj) { // 2.9.12+
                    layer.alert('自定义工具栏图标按钮');
                }
            }],


            cols: [[
                {type: 'checkbox'},
                {field:'amount', title: '充值金额', minWidth: 100},
                {field:'pay_plugin', title:'付款方式', minWidth: 100},
                {field:'email', title:'邮箱', minWidth: 100},
                {field:'tel', title:'手机', minWidth: 100},
                // {field:'remark', title:'备注', minWidth: 180},
                // {field:'status_text', title:'提现状态', minWidth: 180},
                {field:'create_time', title:'创建时间', minWidth: 100},
                {field:'pay_time', title:'付款时间', minWidth: 100},
                {title:'操作', templet: '#operate', width: 110, align: 'center'}
            ]],

            error: function(res, msg){
                console.log(res, msg)
            },
            done: function(res, curr, count) {
                // 绑定分页下拉框的change事件（注意：需在done回调中绑定，确保元素已渲染）
                var that = this;
                setTimeout(function () {
                    // 找到分页控件中的下拉框（Layui分页的class固定为 layui-laypage-limits）
                    document.querySelector('.layui-laypage-limits select').addEventListener('change', function () {
                        var newSize = this.value; // 获取用户选择的新每页条数
                        // 存储到本地存储
                        localStorage.setItem('goods_limit', newSize);
                        // 重新渲染表格（保持当前页码，应用新的每页条数）
                        table.reload(that.config.id, {
                            page: {
                                curr: curr // 保持当前页码
                            },
                            limit: newSize // 应用新的每页条数
                        });
                    });
                }, 0);
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
            if(obj.event == 'del'){
                var data = checkStatus.data;
                if(data.length == 0){
                    return layer.msg('请选择要删除的记录');
                }
                var ids = $.map(data, function(item) {
                    return item.id;
                }).join(',');
                layer.confirm('确定要删除选中的数据吗？', {
                    btn: ['确认', '取消'],
                    icon: 3,
                    title: '温馨提示'
                }, function(index) {
                    layer.close(index);
                    $.ajax({
                        url: '?action=del',
                        type: 'POST',
                        dataType: 'json',
                        data: { ids: ids, token: '<?= LoginAuth::genToken() ?>' },
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
        });

        // 触发单元格工具事件
        table.on('tool(index)', function(obj){ // 双击 toolDouble
            var data = obj.data; // 获得当前行数据
            var id = obj.config.id;
            if(obj.event == 'finish'){
                layer.confirm('确定已转账？', {
                    btn: ['确认', '取消'], // 按钮
                    icon: 3,             // 图标，3表示问号
                    title: '温馨提示'
                }, function(index) {
                    layer.close(index); // 关闭对话框
                    $.ajax({
                        url: '?action=cmd&type=finish',
                        type: 'POST',
                        dataType: 'json',
                        data: { ids: data.id, token: '<?= LoginAuth::genToken() ?>' },
                        success: function(e) {
                            if(e.code == 400){
                                return layer.msg(e.msg)
                            }else{
                                layer.msg(e.msg);
                                table.reload(id);
                            }

                        },
                        error: function(err) {
                            layer.msg(err.responseJSON.msg);
                        }
                    });
                });
            }
            if(obj.event == 'reject'){
                layer.confirm('拒绝后，余额将返还至用户的余额账户！', {
                    btn: ['确认', '取消'], // 按钮
                    icon: 3,             // 图标，3表示问号
                    title: '温馨提示'
                }, function(index) {
                    layer.close(index); // 关闭对话框
                    $.ajax({
                        url: '?action=cmd&type=reject',
                        type: 'POST',
                        dataType: 'json',
                        data: { ids: data.id, token: '<?= LoginAuth::genToken() ?>' },
                        success: function(e) {
                            if(e.code == 400){
                                return layer.msg(e.msg)
                            }else{
                                layer.msg(e.msg);
                                table.reload(id);
                            }

                        },
                        error: function(err) {
                            layer.msg(err.responseJSON.msg);
                        }
                    });
                });
            }
            if(obj.event == 'del'){
                layer.confirm('确定要删除这条数据吗？', {
                    btn: ['确认', '取消'],
                    icon: 3,
                    title: '温馨提示'
                }, function(index) {
                    layer.close(index);
                    $.ajax({
                        url: '?action=del',
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


        });

        // 触发排序事件
        table.on('sort(index)', function(obj){
            console.log(obj.field); // 当前排序的字段名
            console.log(obj.type); // 当前排序类型：desc（降序）、asc（升序）、null（空对象，默认排序）
            console.log(this); // 当前排序的 th 对象

            // 尽管我们的 table 自带排序功能，但并没有请求服务端。
            // 有些时候，你可能需要根据当前排序的字段，重新向后端发送请求，从而实现服务端排序，如：
            table.reload('index', {
                initSort: obj, // 记录初始排序，如果不设的话，将无法标记表头的排序状态。
                where: { // 请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                    field: obj.field, // 排序字段
                    order: obj.type // 排序方式
                }
            });
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

        // 分页栏事件
        table.on('pagebar(index)', function(obj){
            alert()
            console.log(obj); // 查看对象所有成员
            console.log(obj.config); // 当前实例的配置信息
            console.log(obj.event); // 属性 lay-event 对应的值
        });


        // 表头自定义元素工具事件 --- 2.8.8+
        table.on('colTool(test)', function(obj){
            var event = obj.event;
            console.log(obj);
            if(event === 'email-tips'){
                layer.alert(layui.util.escape(JSON.stringify(obj.col)), {
                    title: '当前列属性选项'
                });
            }
        });


    });
</script>



<script>
    $("#menu-order").attr('class', 'admin-menu-item has-list in');
    $("#menu-order .fa-angle-right").attr('class', 'admin-arrow fa fa-angle-right active');
    $("#menu-order > .submenu").css('display', 'block');
    $('#menu-order-charge > a').attr('class', 'menu-link active');
</script>
