<?php defined('TT_ROOT') || exit('access denied!'); ?>
<style>
    :root{
        --bg: #f6f7fb;
        --card: #ffffff;
        --border: #e5e7eb;
        --text: #111827;
        --muted: #6b7280;
        --primary: #1e80ff;
    }
    
    .layui-form{
        height: 100%;
        display: flex;
        flex-direction: column;
        background: var(--card);
        overflow: hidden;
    }
    #open-box{
        flex: 1 1 auto;
        overflow-y: auto;
    }
    .form-body{
        padding: 12px 24px 0px;
    }
    .layui-form-item{
        margin-bottom: 18px;
    }
    .layui-form-label{
        color: var(--muted);
    }
    .form-tips{
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: var(--muted);
    }
    .input-with-suffix{
        position: relative;
    }
    .input-with-suffix .layui-input{
        padding-right: 46px;
    }
    .input-suffix{
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #475467;
        background: #eef2f7;
        border: 1px solid #e4e7ec;
        border-radius: 6px;
        padding: 2px 6px;
        line-height: 1;
        pointer-events: none;
    }
    
    #form-btn{
        padding: 18px 16px 18px 16px;
        height: 30px;
        background: linear-gradient(to top, #f1f4f8 0%, #f5f7fb 70%, rgba(255, 255, 255, 0) 100%);
        border-top: 0;
        box-shadow: none;
    }
    #form-btn .layui-input-block{
        margin-left: 0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    #form-btn .layui-btn{
        min-width: 88px;
        border-radius: 8px;
        height: 34px;
        line-height: 34px;
        padding: 0 16px;
        font-weight: 600;
        box-shadow: 0 6px 16px rgba(17, 24, 39, 0.08);
        transition: transform .15s ease, box-shadow .15s ease, background .2s ease, border-color .2s ease;
    }
    #form-btn .layui-btn{
        border-color: #d0d5dd;
        color: #344054;
        background: linear-gradient(180deg, #ffffff 0%, #f3f5f9 100%);
    }
    #form-btn .layui-btn:hover{
        border-color: #b8c0cc;
        box-shadow: 0 8px 18px rgba(17, 24, 39, 0.12);
        transform: translateY(-1px);
    }
    #form-btn .layui-btn:hover{
        box-shadow: 0 10px 20px rgba(17, 24, 39, 0.16);
        transform: translateY(-1px);
    }
    #form-btn .layui-btn:active{
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(17, 24, 39, 0.12);
    }
    #form-btn .layui-btn:not(.layui-btn){
        background: linear-gradient(180deg, #67c1b0 0%, #3fa08f 100%);
        border-color: #3fa08f;
    }
    #form-btn .layui-btn:not(.layui-btn):hover{
        background: linear-gradient(180deg, #6fd0be 0%, #45ad9b 100%);
        border-color: #45ad9b;
    }
 
    #modal .modal-content{
        border-radius: 12px;
        overflow: hidden;
        border-color: var(--border);
    }
    #modal .modal-body{
        padding: 16px;
        background: #f8fafc;
    }
    #modal .modal-footer{
        border-top: 1px solid var(--border);
        background: #ffffff;
        padding: 1rem 1.25rem;
    }
    #modal .btn{
        border-radius: 8px;
        padding: 6px 14px;
    }
    @media (max-width: 480px){
        body{
            padding: 12px;
        }
        .layui-form-label{
            width: 100%;
            text-align: left;
            padding: 0 0 6px;
        }
        .layui-input-block{
            margin-left: 0;
        }
        #open-box{
            padding: 0;
        }
        .form-body{
            padding: 18px 16px 20px;
        }
    }
</style>


<form class="layui-form " action="?action=level_add_ajax" id="form">
    <div id="open-box">
        <div class="form-body">
            <div class="layui-form-item">
                <label class="layui-form-label">分站等级名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">分站开通价格</label>
                <div class="layui-input-block">
                    <input type="text" name="price" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">提现手续费(%)</label>
                <div class="layui-input-block">
                    <div class="input-with-suffix">
                        <input type="text" name="cash_change" class="layui-input" value="" placeholder="" inputmode="decimal">
                        <span class="input-suffix">%</span>
                    </div>
                </div>
            </div>
            <input name="token" id="token" value="<?= LoginAuth::genToken() ?>" type="hidden"/>
        </div>
    </div>
    <div id="form-btn">
        <div class="layui-input-block">
            <button type="reset" class="layui-btn layui-btn">重置</button>
            <button type="submit" class="layui-btn" lay-submit lay-filter="submit">保存</button>
        </div>
    </div>
</form>



<script>
    layui.use(['table'], function(){
        var $ = layui.$;
        var form = layui.form;
        var upload = layui.upload;
        var element = layui.element;
        form.on('submit(submit)', function(data){
            var field = data.field; // 获取表单全部字段值
            if (field.cash_change !== undefined && field.cash_change !== '') {
                var cashRaw = String(field.cash_change).replace(/[^\d.]/g, '');
                var cashNum = parseFloat(cashRaw);
                if (!isNaN(cashNum)) {
                    field.cash_change = cashNum / 100;
                }
            }
            var url = $('#form').attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: field,
                dataType: "json",
                success: function (e) {
                    if(e.code == 400){
                        return layer.msg(e.msg)
                    }
                    parent.layer.close('add')
                    parent.layer.msg('添加成功');
                    window.parent.table.reload();
                },
                error: function (xhr) {
                    layer.msg(JSON.parse(xhr.responseText).msg);
                }
            });
            return false; // 阻止默认 form 跳转
        });


        var uploadInst = upload.render({
            elem: '#ID-upload-demo-btn',
            field: 'image',
            url: './article.php?action=upload_cover', // 实际使用时改成您自己的上传接口即可。
            before: function(obj){
                // 预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#ID-upload-demo-img').attr('src', result); // 图片链接（base64）
                });

                element.progress('filter-demo', '0%'); // 进度条复位
                loadIndex = layer.load(2);
            },
            done: function(res){
                // 若上传失败
                if(res.code > 0){
                    return layer.msg('上传失败');
                }
                // 上传成功的一些操作
                if(res.code == 0){
                    $('#sortimg').val(res.data)
                }
                $('#ID-upload-demo-text').html(''); // 置空上传失败的状态
            },
            error: function(){
                // 演示失败状态，并实现重传
                var demoText = $('#ID-upload-demo-text');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
            },
            // 进度条
            progress: function(n, elem, e){
                element.progress('filter-demo', n + '%'); // 可配合 layui 进度条元素使用
                if(n == 100){
                    layer.close(loadIndex)
                }
            }
        });

        var $win = $(window);
        function updateOpenBoxHeight(){
            var bodyPadding = parseInt($('body').css('padding-top'), 10) + parseInt($('body').css('padding-bottom'), 10);
            if (isNaN(bodyPadding)) {
                bodyPadding = 0;
            }
            var btnHeight = $('#form-btn').outerHeight(true) || 0;
            var maxHeight = $win.height() - bodyPadding - btnHeight - 0;
            if (maxHeight < 140) {
                maxHeight = 140;
            }
            $("#open-box").css({
                "max-height": maxHeight + "px"
            });
        }

        updateOpenBoxHeight();
        $win.on('resize', updateOpenBoxHeight);
    })
</script>
