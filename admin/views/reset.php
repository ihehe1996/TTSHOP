<?php defined('TT_ROOT') || exit('access denied!'); ?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        padding: 20px;
    }

    .login-wrapper {
        width: 100%;
        max-width: 400px;
    }

    .login-logo {
        text-align: center;
        margin-bottom: 28px;
    }

    .login-logo .icon-box {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #7BA89D 0%, #9DBEB5 100%);
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 8px 24px rgba(123, 168, 157, 0.25);
    }

    .login-logo .icon-box i {
        font-size: 26px;
        color: #fff;
    }

    .login-logo h1 {
        color: #3D4F4A;
        font-size: 22px;
        font-weight: 600;
        margin: 0 0 6px;
    }

    .login-logo p {
        color: #7A8B86;
        font-size: 14px;
        margin: 0;
    }

    .login-card {
        background: #fff;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 12px 40px rgba(61, 79, 74, 0.08);
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #4A5D57;
        margin-bottom: 6px;
    }

    .input-box {
        position: relative;
    }

    .input-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #A3B5B0;
        font-size: 15px;
        transition: color 0.2s;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px 12px 42px;
        background: #F7FAF9;
        border: 1px solid #DCE5E2;
        border-radius: 10px;
        font-size: 15px;
        color: #3D4F4A;
        transition: all 0.2s;
        outline: none;
    }

    .form-control:focus {
        border-color: #7BA89D;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(123, 168, 157, 0.12);
    }

    .input-box:focus-within i {
        color: #7BA89D;
    }

    .form-control::placeholder {
        color: #A3B5B0;
    }

    .captcha-row {
        display: flex;
        gap: 10px;
    }

    .captcha-row .input-box {
        flex: 1;
    }

    .captcha-img-box {
        width: 100px;
        height: 44px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid #DCE5E2;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .captcha-img-box:hover {
        border-color: #7BA89D;
    }

    .captcha-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .btn-login {
        width: 100%;
        padding: 13px 20px;
        background: linear-gradient(135deg, #7BA89D 0%, #9DBEB5 100%);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-login:hover {
        box-shadow: 0 8px 24px rgba(123, 168, 157, 0.35);
        transform: translateY(-1px);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .login-ext {
        margin-top: 16px;
        text-align: center;
    }

    .login-ext a {
        color: #7BA89D;
        text-decoration: none;
        font-size: 13px;
        transition: color 0.2s;
    }

    .login-ext a:hover {
        color: #5D8C80;
    }

    .login-footer {
        margin-top: 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .login-footer a {
        color: #7BA89D;
        text-decoration: none;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }

    .login-footer a:hover {
        color: #5D8C80;
    }

    .login-footer .copyright {
        color: #A3B5B0;
        font-size: 12px;
    }

    .alert {
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 14px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #EAF4F1;
        border-color: #C7DED6;
        color: #3D4F4A;
    }

    .alert-danger {
        background: #FDEEEE;
        border-color: #F2B8B8;
        color: #7A3B3B;
    }

    @media (max-width: 480px) {
        body {
            padding: 16px;
            align-items: flex-start;
            padding-top: 50px;
        }

        .login-logo {
            margin-bottom: 24px;
        }

        .login-logo .icon-box {
            width: 54px;
            height: 54px;
        }

        .login-logo .icon-box i {
            font-size: 22px;
        }

        .login-logo h1 {
            font-size: 20px;
        }

        .login-card {
            padding: 26px 22px;
            border-radius: 16px;
        }

        .form-control {
            font-size: 16px;
        }

        .captcha-row {
            flex-direction: column;
        }

        .captcha-img-box {
            width: 100%;
            height: 42px;
        }

        .login-footer {
            flex-direction: column;
            gap: 10px;
            margin-top: 22px;
        }
    }

    @media (max-width: 360px) {
        body {
            padding: 12px;
            padding-top: 36px;
        }

        .login-card {
            padding: 22px 18px;
        }
    }

    @media (max-height: 700px) {
        body {
            align-items: flex-start;
            padding-top: 24px;
        }
    }

    /* ===== 找回密码教程 ===== */
    .alert-info {
        background: #EFF6FB;
        border-color: #C4DCEF;
        color: #3D4F5A;
    }

    .alert i {
        margin-right: 6px;
    }

    .steps {
        list-style: none;
        padding: 0;
        margin: 0 0 16px;
        counter-reset: step;
    }

    .step {
        position: relative;
        padding: 0 0 22px 44px;
    }

    .step:last-child {
        padding-bottom: 0;
    }

    .step::before {
        counter-increment: step;
        content: counter(step);
        position: absolute;
        left: 0;
        top: 0;
        width: 28px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        background: linear-gradient(135deg, #7BA89D 0%, #9DBEB5 100%);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        border-radius: 50%;
    }

    .step::after {
        content: '';
        position: absolute;
        left: 13px;
        top: 34px;
        bottom: 6px;
        width: 2px;
        background: #E4EEEA;
    }

    .step:last-child::after {
        display: none;
    }

    .step-title {
        font-size: 14px;
        font-weight: 600;
        color: #3D4F4A;
        margin-bottom: 4px;
    }

    .step-desc {
        font-size: 13px;
        color: #7A8B86;
        line-height: 1.6;
    }

    .step-desc code {
        background: #EAF4F1;
        color: #3D4F4A;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    .cmd {
        margin: 8px 0 0;
        padding: 10px 14px;
        background: #2B3532;
        color: #D8F0E8;
        border-radius: 8px;
        font-size: 13px;
        font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
        overflow-x: auto;
        line-height: 1.6;
        white-space: pre-wrap;
        word-break: break-all;
    }
</style>

<div class="login-wrapper">
    <div class="login-logo">
        <div class="icon-box">
            <i class="fa fa-unlock-alt"></i>
        </div>
        <h1><?= Option::get('blogname') ?></h1>
        <p>找回密码 · 使用站长工具箱重置</p>
    </div>

    <div class="login-card">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            本站已改用服务器上的「站长工具箱」重置密码，操作简单、无需依赖邮件服务。
        </div>

        <ol class="steps">
            <li class="step">
                <div class="step-title">登录服务器</div>
                <div class="step-desc">通过 SSH 连接服务器，或在宝塔面板中使用「终端」功能。</div>
            </li>
            <li class="step">
                <div class="step-title">进入网站根目录</div>
                <div class="step-desc">切换到安装本系统的目录，例如：</div>
                <pre class="cmd"><code>cd /www/wwwroot/你的站点目录</code></pre>
            </li>
            <li class="step">
                <div class="step-title">启动站长工具箱</div>
                <pre class="cmd"><code>php ttshop</code></pre>
            </li>
            <li class="step">
                <div class="step-title">选择「修改管理员密码」</div>
                <div class="step-desc">在菜单中输入 <code>3</code> 并回车，进入改密流程。</div>
            </li>
            <li class="step">
                <div class="step-title">按提示完成重置</div>
                <div class="step-desc">输入管理员 UID（直接回车默认第一个），再输入并确认新密码（至少 6 位）。</div>
            </li>
        </ol>

        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i>
            重置完成后返回登录页，使用新密码登录即可。若忘记管理员账号，可在工具箱中选择「查看管理员信息」查看。
        </div>

        <div class="login-ext">
            <a href="./">返回登录</a>
        </div>
    </div>

    <div class="login-footer">
        <a href="../">
            <i class="fa fa-arrow-left"></i>
            <span>返回首页</span>
        </a>
        <span class="copyright">&copy; <?= date('Y') ?> All rights reserved</span>
    </div>
</div>
</body>
</html>
