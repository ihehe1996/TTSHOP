<?php defined('EM_ROOT') || exit('access denied!'); ?>

<style>
#accordion > .active .menu-link{
    background: #EDF2F1!important;
}
#accordion > .active .menu-link, #accordion > .active .menu-link .fa{
    color: #4C7D71!important;
}

    /* 容器调整 */
    .auth-page-wrapper {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Helvetica Neue", STHeiti, "Microsoft Yahei", Tahoma, Simsun, sans-serif;
    }

    /* 主卡片 - 单栏居中 */
    .premium-card {
        background: #fff;
        width: 100%;
        max-width: 520px; /* 调整为适合单栏的宽度 */
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        position: relative;
        padding: 50px 40px; /* 增加内边距 */
        box-sizing: border-box;
    }

    .form-header {
        margin-bottom: 35px;
        text-align: center; /* 居中对齐 */
    }
    .form-title {
        font-size: 26px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 12px;
    }
    .form-subtitle {
        color: #888;
        font-size: 15px;
        line-height: 1.5;
    }

    /* 表单控件美化 */
    .custom-field {
        margin-bottom: 25px;
    }
    .custom-label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }
    .input-wrapper {
        position: relative;
    }
    .input-wrapper input {
        width: 100%;
        height: 50px;
        padding: 0 20px 0 45px;
        border: 2px solid #f0f0f0;
        background: #f9f9f9;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
        box-sizing: border-box;
    }
    .input-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #bbb;
        font-size: 18px;
        transition: color 0.3s;
    }
    .input-wrapper input:focus {
        border-color: #165DFF;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 4px rgba(22, 93, 255, 0.1);
    }
    .input-wrapper input:focus + i {
        color: #165DFF;
    }

    .btn-action {
        width: 100%;
        height: 50px;
        background: #EDF2F1;
        color: #4C7D71;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.5s ease;
    }
    .btn-action:hover {
        text-decoration: underline;
    }

    .link-group {
        margin-top: 25px;
        text-align: center;
        font-size: 14px;
    }
    .link-btn {
        color: #165DFF;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
    }
    .link-btn:hover {
        text-decoration: underline;
    }

    /* 成功状态 */
    .success-icon-large {
        width: 80px;
        height: 80px;
        background: #e8f3ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: #165DFF;
        font-size: 40px;
    }

    .success-info {
        background: #f8faff;
        border: 1px solid #e6f0ff;
        border-radius: 12px;
        padding: 25px;
        margin-top: 25px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 14px;
        border-bottom: 1px dashed #e1e1e1;
        padding-bottom: 15px;
    }
    .info-row:last-child {
        margin-bottom: 0;
        border-bottom: none;
        padding-bottom: 0;
    }
    .info-label {
        color: #666;
    }
    .info-val {
        font-weight: 600;
        color: #333;
        font-family: monospace;
    }
</style>

<?php if(!defined('DEMO_MODE') || DEMO_MODE != true): ?>

<div class="auth-page-wrapper">
    
    <div class="premium-card">

        <?php if (isset($_GET['error_b'])): ?>
            <div class="layui-alert layui-btn-red" style="margin-bottom: 25px; border-radius: 8px; text-align: center;">
                <i class="layui-icon layui-icon-tips"></i> 授权验证失败，请检查网络或授权码状态
            </div>
        <?php endif ?>

        <?php if (!Register::isRegLocal()) : ?>
            <!-- 未授权表单 -->
            <div class="form-header">
                <div style="width: 60px; height: 60px; background: #f0f5ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #165DFF;">
                    <i class="layui-icon layui-icon-auz" style="font-size: 32px;"></i>
                </div>
                <div class="form-title">激活正版 EMSHOP</div>
                <div class="form-subtitle">请输入您的授权许可证密钥以开启完整服务</div>
            </div>

            <form class="layui-form" action="javascript:;" id="form">
                <div class="custom-field">
                    <label class="custom-label">授权许可证 (License Key)</label>
                    <div class="input-wrapper">
                        <input type="text" name="emkey" placeholder="请输入您的授权码" autocomplete="off">
                        <i class="layui-icon layui-icon-key"></i>
                    </div>
                </div>

                <input name="token" id="token" value="<?= LoginAuth::genToken() ?>" type="hidden"/>
                
                <button type="submit" class="btn-action" lay-submit lay-filter="submit">
                    立即激活 <i class="layui-icon layui-icon-next" style="font-size: 14px; margin-left: 5px;"></i>
                </button>
                
                <div class="link-group">
                    <span style="color: #999;">还没有授权码？</span> 
                    <span class="link-btn get-em-buy-info">获取正版授权 &rarr;</span>
                </div>
            </form>

        <?php else: ?>
            <!-- 已授权信息 -->
            <div class="form-header">
                <div class="form-title">EMSHOP 正版授权</div>
                <div class="form-subtitle">感谢您的支持，系统各项服务运行中</div>
            </div>

            <div class="success-info">
                <div class="info-row">
                    <span class="info-label">系统版本</span>
                    <span class="info-val">v<?= Option::EM_VERSION ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">授权类型</span>
                    <span class="info-val" style="color: #165DFF;"><?= $emkey_type ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">授权域名</span>
                    <span class="info-val"><?= getTopHost() ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">授权密钥</span>
                    <span class="info-val"><?= substr($emkey, 0, 8) . '****' . substr($emkey, -4) ?></span>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <a href="<?= EM_LINE[0]['value'] ?>" target="_blank" class="btn-action">
                    进入官方网站
                </a>
                <div style="margin-top: 15px; font-size: 13px; color: #999; text-align: center; line-height: 1.6;">
                    请妥善保管好您的授权码，如您要更换授权域名，请前往官方网站操作。
                </div>
            </div>

        <?php endif ?>
    </div>
    
</div>

<?php endif; ?>

<script>
    layui.use(['form', 'layer'], function(){
        var $ = layui.$;
        var form = layui.form;
        var layer = layui.layer;

        // 优化输入框交互
        $('.input-wrapper input').focus(function(){
            $(this).parent().addClass('focused');
        }).blur(function(){
            $(this).parent().removeClass('focused');
        });

        form.on('submit(submit)', function(data){
            var field = data.field;
            
            
            
            var loadIndex = layer.load(2, {shade: 0.1});
            
            $.ajax({
                type: "POST",
                url: "?action=auth",
                data: field,
                dataType: "json",
                success: function (e) {
                    if(e.code == 200){
                        layer.msg(e.msg, {icon: 1, time: 1000}, function(){
                            location.reload();
                        });
                    } else {
                        layer.msg(e.msg);
                    }
                },
                error: function (xhr) {
                    layer.msg('连接服务器失败，请稍后重试');
                },
                complete: function () {
                    layer.close(loadIndex);
                },
            });
            return false; 
        });
    })
</script>

<script>
    $("#menu-auth").addClass('active');
</script>