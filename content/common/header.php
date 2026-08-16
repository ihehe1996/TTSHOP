<?php
/*
Template Name:默认模板
Version:1.5.9
Template Url:
Description:系统默认的自适应模板
Author:驳手
Author Url:

*/

?>
<?php

defined('TT_ROOT') || exit('access denied!');
require_once View::getView('module');

$version = '1720327727';

$home_icon = Option::get('home_icon');
global $userData;

$can_login = Option::get('login_switch') == 'y';
$can_register = Option::get('register_switch') == 'y';
$is_user_login = ISLOGIN === true;
$show_user_entry = $is_user_login || $can_login || $can_register;

$user_avatar = '';
$user_name = '';
$user_balance = '0.00';
if ($is_user_login) {
    $user_avatar = User::getAvatar($userData['photo'] ?? '');
    $user_name = $userData['nickname'] ?? ($userData['username'] ?? '用户');
    $user_balance = number_format((float)($userData['money'] ?? 0), 2);
}

?>
<!doctype html>
<html lang="zh-cn" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $site_title ?></title>
    <meta name="keywords" content="<?= $site_key ?>"/>
    <meta name="description" content="<?= $site_description ?>"/>
    <link href="<?= empty($home_icon) ? (empty(_g('favicon')) ? TT_URL . 'favicon.ico' : _g('favicon')) : $home_icon; ?>" rel="icon">
    <link rel="alternate" title="RSS" href="<?= TT_URL ?>rss.php" type="application/rss+xml"/>


    <script src="../../../admin/views/js/jquery.min.3.5.1.js"></script>
    <!-- 字体 -->
    <link rel="stylesheet" type="text/css" href="../../../admin/views/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../../admin/views/layui-v2.11.6/layui/css/layui.css">
    <script src="../../../admin/views/layui-v2.11.6/layui/layui.js"></script>





    <link rel="stylesheet" href="../../content/static/css/em.css?v=<?= Option::TT_VERSION_TIMESTAMP ?>">
    <link rel="stylesheet" href="../../content/common/common.css?v=<?= Option::TT_VERSION_TIMESTAMP ?>">
    <link href="<?= TEMPLATE_URL ?>css/style.css?v=<?= Option::TT_VERSION_TIMESTAMP ?>" rel="stylesheet"/>

    <script src="../../content/static/js/em.js?v=<?= Option::TT_VERSION_TIMESTAMP ?>"></script>
    <script src="<?= TEMPLATE_URL ?>js/main.js?v=<?= Option::TT_VERSION_TIMESTAMP ?>"></script>

    <?php doAction('index_head') ?>
</head>
<body>

<div id="mask"></div>
<header class="header">
    <div class="h-fix" id="h-fix">
        <div class="container">
            <div class="header-bar">
                <?php if(empty(Option::get('logo'))): ?>
                <h1 class="logo-text">
                    <a href="<?= TT_URL ?>" title="<?= $blogname ?>">
                        <span id="light-logo"><?= $blogname ?></span>
                    </a>
                </h1>
                <?php else: ?>
                <h1 class="logo">
                    <a href="<?= TT_URL ?>" title="<?= $blogname ?>">
                        <img id="light-logo" src="<?= Option::get('logo') ?>" alt="<?= $blogname ?>" title="<?= $blogname ?>">
                    </a>
                </h1>
                <?php endif; ?>

                <?php
                ob_start();
                blog_navi();
                $nav_content = ob_get_clean();
                if (!empty(trim($nav_content))):
                ?>
                <div class="nav-container">
                    <nav class="nav-bar" id="nav-box" data-type="index" data-infoid="">
                        <ul class="nav"><?php echo $nav_content; ?></ul>
                    </nav>
                </div>
                <?php endif; ?>

                <div class="header-right">
                    <div class="header-right-btn">
                        <a href="<?= TT_URL ?>user/visitors.php" class="header-order-link">
                            <i class="fa fa-search"></i>
                            <span>查询订单</span>
                        </a>

                        <?php if ($show_user_entry): ?>
                            <?php if ($is_user_login): ?>
                                <a href="<?= TT_URL ?>user" class="header-user-card">
                                    <img class="header-user-avatar" src="<?= $user_avatar ?>" alt="<?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="header-user-meta">
                                        <span class="header-user-name"><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="header-user-balance">余额: ￥<?= $user_balance ?></span>
                                    </span>
                                </a>
                            <?php else: ?>
                                <div class="header-auth-links">
                                    <?php if ($can_login): ?>
                                        <a class="header-auth-btn is-ghost" href="<?= TT_URL ?>user/account.php?action=signin">登录</a>
                                    <?php endif; ?>
                                    <?php if ($can_register): ?>
                                        <a class="header-auth-btn is-solid" href="<?= TT_URL ?>user/account.php?action=signup">注册</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div id="m-btn" class="m-btn"><i class="fa fa-bars"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div id="zhanwei"></div>
<script>
function setPlaceholderHeight() {
    var headerHeight = $('.h-fix').outerHeight() || 0;
    $('#zhanwei').css('height', headerHeight + 'px');
}
setPlaceholderHeight();
$(window).on('resize', setPlaceholderHeight);
</script>
