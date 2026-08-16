<?php

const TT_ROOT = __DIR__;

require_once TT_ROOT . '/include/lib/common.php';
require_once TT_ROOT . '/base.php';
header('Content-Type: text/html; charset=UTF-8');
spl_autoload_register("ttAutoload");

if (PHP_VERSION < '7.2') {
    ttMsg('PHP版本太低，推荐使用PHP7.4及以上版本');
}

const LOG_PATH = TT_ROOT . '/content/log/';

$act = Input::getStrVar('action');

$bt_db_host = 'localhost';
$bt_db_username = 'BT_DB_USERNAME';
$bt_db_password = 'BT_DB_PASSWORD';
$bt_db_name = 'BT_DB_NAME';

$env_em_env = getenv('TT_ENV');
$env_db_host = getenv('TT_DB_HOST');
$env_db_name = getenv('TT_DB_NAME');
$env_db_user = getenv('TT_DB_USER');
$env_db_password = getenv('TT_DB_PASSWORD');

$timestamp = time();



if (!$act) {
    ?>
    <!doctype html>
    <html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="applicable-device" content="pc,mobile">
        <title>TTSHOP 安装程序</title>
        <link rel="stylesheet" href="./content/static/css/install.css">
    </head>
    <body class="install-page">
    <div class="install">
        <header class="masthead">
            <span class="masthead-brand">
                <span class="masthead-mark" aria-hidden="true"></span>
                <span class="masthead-name">TTSHOP</span>
            </span>
            <span class="masthead-meta">
                <span>v<?= Option::TT_VERSION ?></span>
                <span class="masthead-dot" aria-hidden="true">·</span>
                <span>PHP <?= PHP_VERSION ?></span>
            </span>
        </header>

        <main class="cert">
            <div class="cert-head">
                <span class="cert-kicker">安装向导 · INSTALLATION WIZARD</span>
                <span class="cert-no">安装编号 · TT-<?= date('Ymd', $timestamp) ?>-<?= strtoupper(substr(md5($timestamp), 0, 4)) ?></span>
            </div>

            <div class="cert-grid">
                <aside class="cert-aside">
                    <p class="aside-eyebrow">INSTALLATION · 安装向导</p>
                    <h1 class="aside-title">安装你的<br>TTSHOP</h1>
                    <p class="aside-desc">填入数据库与管理员信息，系统将自动建表、写入配置并生成管理员账号。</p>

                    <ol class="steps">
                        <li class="steps-item"><span class="steps-num">01</span><span class="steps-text"><b>数据库</b>连接你的 MySQL</span></li>
                        <li class="steps-item"><span class="steps-num">02</span><span class="steps-text"><b>管理员</b>设定后台账号</span></li>
                        <li class="steps-item"><span class="steps-num">03</span><span class="steps-text"><b>建站</b>自动建表并生成站点</span></li>
                    </ol>

                    <div class="seal" aria-hidden="true">
                        <span class="seal-top">TTSHOP</span>
                        <span class="seal-mid">安装</span>
                        <span class="seal-bot">SELF-HOSTED</span>
                    </div>
                </aside>

                <form class="cert-body" id="form" method="post" action="install.php?action=install">
                    <?php $show_db_card = true; ?>
                    <?php if ($env_db_user): ?>
                        <?php $show_db_card = false; ?>
                        <input name="hostname" type="hidden" value="<?= $env_db_host ?>">
                        <input name="dbuser" type="hidden" value="<?= $env_db_user ?>">
                        <input name="dbpasswd" type="hidden" value="<?= $env_db_password ?>">
                        <input name="dbname" type="hidden" value="<?= $env_db_name ?>">
                        <input name="dbprefix" type="hidden" value="tt_">
                    <?php elseif (strpos($bt_db_username, 'BT_DB_') === false): ?>
                        <?php $show_db_card = false; ?>
                        <input name="hostname" type="hidden" value="<?= $bt_db_host ?>">
                        <input name="dbuser" type="hidden" value="<?= $bt_db_username ?>">
                        <input name="dbpasswd" type="hidden" value="<?= $bt_db_password ?>">
                        <input name="dbname" type="hidden" value="<?= $bt_db_name ?>">
                        <input name="dbprefix" type="hidden" value="tt_">
                    <?php endif; ?>

                    <?php if ($show_db_card): ?>
                    <section class="fieldset">
                        <div class="fieldset-head">
                            <span class="fieldset-tag">DATABASE</span>
                            <span class="fieldset-title">数据库配置</span>
                        </div>
                        <div class="field">
                            <label class="field-label" for="hostname">数据库地址</label>
                            <input type="text" id="hostname" name="hostname" class="field-input" value="localhost" placeholder="localhost">
                            <p class="field-hint">通常为 localhost，或指定端口 localhost:3306</p>
                        </div>
                        <div class="field">
                            <label class="field-label" for="dbname">数据库名</label>
                            <input type="text" id="dbname" name="dbname" class="field-input" placeholder="请输入数据库名">
                            <p class="field-hint">请提前创建空数据库，或使用已有数据库</p>
                        </div>
                        <div class="field">
                            <label class="field-label" for="dbuser">数据库用户名</label>
                            <input type="text" id="dbuser" name="dbuser" class="field-input" placeholder="数据库用户名">
                        </div>
                        <div class="field">
                            <label class="field-label" for="dbpasswd">数据库密码</label>
                            <input type="password" id="dbpasswd" name="dbpasswd" class="field-input" placeholder="数据库密码">
                        </div>
                        <div class="field">
                            <label class="field-label" for="dbprefix">数据表前缀</label>
                            <input type="text" id="dbprefix" name="dbprefix" class="field-input is-code" value="tt_">
                            <p class="field-hint">默认即可，由字母、数字、下划线组成，以下划线结束</p>
                        </div>
                    </section>
                    <?php endif; ?>

                    <section class="fieldset">
                        <div class="fieldset-head">
                            <span class="fieldset-tag">ADMIN</span>
                            <span class="fieldset-title">管理员配置</span>
                        </div>
                        <div class="field">
                            <label class="field-label" for="username">登录账号</label>
                            <input type="text" id="username" name="username" class="field-input" value="admin" placeholder="管理员账号">
                        </div>
                        <div class="field">
                            <label class="field-label" for="password">登录密码</label>
                            <input type="password" id="password" name="password" class="field-input" placeholder="请设置登录密码">
                        </div>
                        <div class="field">
                            <label class="field-label" for="repassword">确认密码</label>
                            <input type="password" id="repassword" name="repassword" class="field-input" placeholder="请再次输入密码">
                        </div>
                        <div class="field">
                            <label class="field-label" for="email">邮箱</label>
                            <input type="email" id="email" name="email" class="field-input" placeholder="管理员邮箱">
                        </div>
                    </section>

                    <div class="submit-row">
                        <button type="submit" class="submit-btn">开始安装</button>
                        <p class="submit-note">安装完成后可立即进入后台进行站点配置</p>
                    </div>
                </form>
            </div>
        </main>

        <footer class="install-foot">
            <span>轻量 · 自托管 · 虚拟商品商城</span>
            <span class="install-foot-sep">/</span>
            <span>TTSHOP · 基于 EMLOG 深度二次开发</span>
        </footer>
    </div>
    </body>
    </html>
    <?php
}
if ($act == 'install' || $act == 'reinstall' || $act == 'reinstall_mysql' || $act == 'reinstall_php') {
    $db_host = Input::postStrVar('hostname');
    $db_user = Input::postStrVar('dbuser');
    $db_pw = Input::postStrVar('dbpasswd');
    $db_name = Input::postStrVar('dbname');
    $db_prefix = Input::postStrVar('dbprefix');
    $username = Input::postStrVar('username');
    $password = Input::postStrVar('password');
    $repassword = Input::postStrVar('repassword');
    $email = Input::postStrVar('email');


    if ($db_prefix === '') {
        ttMsg('数据库表前缀不能为空!');
    } elseif (!$db_name) {
//        sleep(2);
        ttMsg('数据库名不能为空!');
    } elseif (!preg_match("/^[\w_]+_$/", $db_prefix)) {
        ttMsg('数据库表前缀格式错误!');
    } elseif (!$username) {
        ttMsg('管理员登录账号不能为空!');
    } elseif (!$password) {
        ttMsg('管理员登录密码不能为空!');
    }  elseif (strlen($password) < 6) {
        ttMsg('登录密码不得小于6位');
    } elseif ($password != $repassword) {
        ttMsg('两次输入的密码不一致');
    }

    define('DB_HOST', $db_host);
    define('DB_USER', $db_user);
    define('DB_PASSWD', $db_pw);
    define('DB_NAME', $db_name);
    define('DB_PREFIX', $db_prefix);

    $DB = Database::getInstance();
    $CACHE = Cache::getInstance();

    $mysql_res = $DB->once_fetch_array("SELECT VERSION() AS mysql_version");
    if($act == 'install' && !empty($mysql_res) && ($mysql_res['mysql_version'] > '5.7.99' || $mysql_res['mysql_version'] < '5.6')){
        echo <<<EOT
<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>版本提示 - TTSHOP</title>
<link rel="stylesheet" href="./content/static/css/install.css">
</head>
<body class="install-page">
<div class="install install-center">
    <main class="notice">
        <div class="notice-mark" aria-hidden="true">!</div>
        <p class="notice-eyebrow">VERSION CHECK · 版本提示</p>
        <h1 class="notice-title">MySQL 版本偏旧或过新</h1>
        <p class="notice-desc">你当前的 MySQL 版本是 <span class="mono">{$mysql_res['mysql_version']}</span>，建议使用 MySQL 5.6 / 5.7 系列。确定强制安装吗？</p>
        <form name="form1" method="post" action="install.php?action=reinstall_mysql">
            <input name="hostname" type="hidden" value="$db_host">
            <input name="dbuser" type="hidden" value="$db_user">
            <input name="dbpasswd" type="hidden" value="$db_pw">
            <input name="dbname" type="hidden" value="$db_name">
            <input name="dbprefix" type="hidden" value="$db_prefix">
            <input name="username" type="hidden" value="$username">
            <input name="password" type="hidden" value="$password">
            <input name="repassword" type="hidden" value="$repassword">
            <input name="email" type="hidden" value="$email">
            <div class="notice-actions">
                <a href="javascript:history.back(-1);" class="btn-ghost">返回</a>
                <button type="submit" class="btn-primary">强制安装</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
EOT;
        exit;
    }


    $php_version = PHP_VERSION;
    if(($act == 'install' || $act == 'reinstall_mysql') && $php_version < '7.2'){
        echo <<<EOT
<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>版本提示 - TTSHOP</title>
<link rel="stylesheet" href="./content/static/css/install.css">
</head>
<body class="install-page">
<div class="install install-center">
    <main class="notice">
        <div class="notice-mark" aria-hidden="true">!</div>
        <p class="notice-eyebrow">VERSION CHECK · 版本提示</p>
        <h1 class="notice-title">PHP 版本偏低</h1>
        <p class="notice-desc">你当前的 PHP 版本是 <span class="mono">{$php_version}</span>，建议使用 PHP 7.4 及以上版本。确定强制安装吗？</p>
        <form name="form1" method="post" action="install.php?action=reinstall_php">
            <input name="hostname" type="hidden" value="$db_host">
            <input name="dbuser" type="hidden" value="$db_user">
            <input name="dbpasswd" type="hidden" value="$db_pw">
            <input name="dbname" type="hidden" value="$db_name">
            <input name="dbprefix" type="hidden" value="$db_prefix">
            <input name="username" type="hidden" value="$username">
            <input name="password" type="hidden" value="$password">
            <input name="repassword" type="hidden" value="$repassword">
            <input name="email" type="hidden" value="$email">
            <div class="notice-actions">
                <a href="javascript:history.back(-1);" class="btn-ghost">返回</a>
                <button type="submit" class="btn-primary">强制安装</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
EOT;
        exit;
    }
    if ($act != 'reinstall' && $DB->num_rows($DB->query("SHOW TABLES LIKE '{$db_prefix}blog'")) == 1) {
        echo <<<EOT
<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>已安装 - TTSHOP</title>
<link rel="stylesheet" href="./content/static/css/install.css">
</head>
<body class="install-page">
<div class="install install-center">
    <main class="notice">
        <div class="notice-mark" aria-hidden="true">!</div>
        <p class="notice-eyebrow">ALREADY INSTALLED · 重复安装</p>
        <h1 class="notice-title">你的 TTSHOP 看起来已经安装过了</h1>
        <p class="notice-desc">继续安装将会覆盖原有数据，请确认是否继续。</p>
        <form name="form1" method="post" action="install.php?action=reinstall">
            <input name="hostname" type="hidden" value="$db_host">
            <input name="dbuser" type="hidden" value="$db_user">
            <input name="dbpasswd" type="hidden" value="$db_pw">
            <input name="dbname" type="hidden" value="$db_name">
            <input name="dbprefix" type="hidden" value="$db_prefix">
            <input name="username" type="hidden" value="$username">
            <input name="password" type="hidden" value="$password">
            <input name="repassword" type="hidden" value="$repassword">
            <input name="email" type="hidden" value="$email">
            <div class="notice-actions">
                <a href="javascript:history.back(-1);" class="btn-ghost">返回</a>
                <button type="submit" class="btn-primary">继续安装</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
EOT;
        exit;
    }else{
    }

    if (!is_writable('config.php')) {
        ttMsg('配置文件(config.php)不可写，请调整文件读写权限。');
    }
    if (!is_writable(TT_ROOT . '/content/cache')) {
        ttMsg('缓存目录（content/cache）不可写。请检查目录读写权限。');
    }

    $PHPASS = new PasswordHash(8, true);

    $config = "<?php\n"
        . "//MySQL database host\n"
        . "const DB_HOST = '$db_host';"
        . "\n//Database username\n"
        . "const DB_USER = '$db_user';"
        . "\n//Database user password\n"
        . "const DB_PASSWD = '$db_pw';"
        . "\n//Database name\n"
        . "const DB_NAME = '$db_name';"
        . "\n//Database Table Prefix\n"
        . "const DB_PREFIX = '$db_prefix';"
        . "\n//Auth key\n"
        . "const AUTH_KEY = '" . $PHPASS->HashPassword(getRandStr(32) . md5(getIp()) . getUA() . microtime()) . "';"
        . "\n//Cookie name\n"
        . "const AUTH_COOKIE_NAME = 'TT_AUTHCOOKIE_" . sha1(getRandStr(32, false) . md5(getIp()) . getUA() . microtime()) . "';";

    if (!file_put_contents('config.php', $config)) {
        ttMsg('配置文件(config.php)不可写，请调整文件读写权限。');
    }

    $password = $PHPASS->HashPassword($password);

    $table_charset_sql = 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
    $DB->query("ALTER DATABASE `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;", true);

    $widget_title = serialize(Option::getWidgetTitle());
    $def_widgets = serialize(Option::getDefWidget());
    $def_plugin = serialize(Option::getDefPlugin());

    $apikey = md5(getRandStr(32));


    $tt_url = realUrl();

    $sql = "
DROP TABLE IF EXISTS `{$db_prefix}attachment`;
CREATE TABLE `{$db_prefix}attachment`  (
  `aid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '资源文件表',
  `alias` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '资源别名',
  `author` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT '作者UID',
  `sortid` int(11) NOT NULL DEFAULT 0 COMMENT '分类ID',
  `blogid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章ID（已废弃）',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `filesize` int(11) NOT NULL DEFAULT 0 COMMENT '文件大小',
  `filepath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `addtime` bigint(20) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `width` int(11) NOT NULL DEFAULT 0 COMMENT '图片宽度',
  `height` int(11) NOT NULL DEFAULT 0 COMMENT '图片高度',
  `mimetype` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `thumfor` int(11) NOT NULL DEFAULT 0 COMMENT '缩略图的原资源ID（已废弃）',
  `download_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '下载次数',
  PRIMARY KEY (`aid`) USING BTREE,
  INDEX `thum_uid`(`thumfor`, `author`) USING BTREE,
  INDEX `addtime`(`addtime`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS {$db_prefix}twitter;
CREATE TABLE {$db_prefix}twitter (
    id INT NOT NULL AUTO_INCREMENT COMMENT '微语笔记表',
    content text NOT NULL COMMENT '微语内容',
    img varchar(255) DEFAULT NULL COMMENT '图片',
    author int(11) NOT NULL default '1' COMMENT '作者UID',
    date bigint(20) NOT NULL COMMENT '创建时间',
    replynum int(11) unsigned NOT NULL default '0' COMMENT '回复数量',
    like_count int(11) unsigned NOT NULL default '0' COMMENT '点赞量',
    private enum('n','y') NOT NULL default 'n' COMMENT '是否私密',
    ip varchar(128) NOT NULL default '' COMMENT 'ip',
    PRIMARY KEY (id),
    KEY author (author)
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}authorization`;
CREATE TABLE `{$db_prefix}authorization`  (
  `ttkey` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `type` tinyint(1) NULL DEFAULT 0
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}stock_export_log`;
CREATE TABLE `{$db_prefix}stock_export_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) NULL DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `create_time` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}balance_log`;
CREATE TABLE `{$db_prefix}balance_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NULL DEFAULT NULL,
  `plus` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `update_before` decimal(10, 2) NULL DEFAULT NULL,
  `money` decimal(10, 2) NULL DEFAULT 0.00,
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `create_time` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}blog`;
CREATE TABLE `{$db_prefix}blog`  (
  `gid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章表',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章标题',
  `date` bigint(20) NOT NULL COMMENT '发布时间',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文章内容',
  `excerpt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文章摘要',
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面图',
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章别名',
  `author` int(11) NOT NULL DEFAULT 1 COMMENT '作者UID',
  `sortid` int(11) NOT NULL DEFAULT -1 COMMENT '分类ID',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blog' COMMENT '文章OR页面',
  `views` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读量',
  `comnum` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论数量',
  `attnum` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '附件数量（已废弃）',
  `top` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '置顶',
  `sortop` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '分类置顶',
  `hide` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '草稿y',
  `checked` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'y' COMMENT '文章是否审核',
  `allow_remark` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'y' COMMENT '允许评论y',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问密码',
  `template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模板',
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '标签',
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章跳转链接',
  `feedback` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'audit feedback',
  PRIMARY KEY (`gid`) USING BTREE,
  INDEX `author`(`author`) USING BTREE,
  INDEX `views`(`views`) USING BTREE,
  INDEX `comnum`(`comnum`) USING BTREE,
  INDEX `sortid`(`sortid`) USING BTREE,
  INDEX `top`(`top`, `date`) USING BTREE,
  INDEX `date`(`date`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}blog_fields`;
CREATE TABLE `{$db_prefix}blog_fields`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gid` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `field_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `field_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `gid`(`gid`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}comment`;
CREATE TABLE `{$db_prefix}comment`  (
  `cid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论表',
  `gid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章ID',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级评论ID',
  `top` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '置顶',
  `poster` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '发布人昵称',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '发布人UID',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '评论内容',
  `mail` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'email',
  `url` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'homepage',
  `ip` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ip address',
  `agent` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'user agent',
  `hide` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '是否审核',
  `date` bigint(20) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`cid`) USING BTREE,
  INDEX `gid`(`gid`) USING BTREE,
  INDEX `date`(`date`) USING BTREE,
  INDEX `hide`(`hide`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}goods`;
CREATE TABLE `{$db_prefix}goods`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `station_id` int(10) NOT NULL DEFAULT 0 COMMENT '分站ID',
  `home` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'y',
  `payment` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `des` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '商品简介',
  `sort_num` int(10) NOT NULL DEFAULT 0 COMMENT '排序',
  `min_qty` int(10) NOT NULL DEFAULT 1 COMMENT '最小购买数量',
  `max_qty` int(10) NOT NULL DEFAULT 0 COMMENT '最大购买数量(0 不限制)',
  `sort_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '分类置顶',
  `index_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '首页置顶',
  `type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `group_id` int(10) NULL DEFAULT NULL COMMENT '规格模板组ID',
  `is_sku` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '是否是多规格',
  `title` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品标题',
  `is_on_shelf` tinyint(1) NOT NULL DEFAULT 1,
  `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '商品配置信息',
  `create_time` bigint(16) NOT NULL COMMENT '创建时间',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品详情',
  `pay_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面图',
  `sort_id` int(11) NOT NULL DEFAULT -1 COMMENT '分类ID',
  `password` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问密码',
  `template` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模板',
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '标签',
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '跳转链接',
  `delete_time` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sortid`(`sort_id`) USING BTREE,
  INDEX `top`(`create_time`) USING BTREE,
  INDEX `date`(`create_time`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}coupon`;
CREATE TABLE `{$db_prefix}coupon`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券ID',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT '优惠券类型',
  `category_id` int(10) NOT NULL DEFAULT 0 COMMENT '分类ID',
  `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `threshold_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none' COMMENT '门槛类型',
  `min_amount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '最低消费金额',
  `discount_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amount' COMMENT '优惠方式',
  `discount_value` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '优惠数值',
  `end_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `use_limit` int(10) NOT NULL DEFAULT 1 COMMENT '可用次数(0为不限)',
  `prefix` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '券码前缀',
  `code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '券码',
  `used_times` int(10) NOT NULL DEFAULT 0 COMMENT '已使用次数',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `create_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_code`(`code`) USING BTREE,
  INDEX `idx_status`(`status`) USING BTREE,
  INDEX `idx_type`(`type`) USING BTREE,
  INDEX `idx_category`(`category_id`) USING BTREE,
  INDEX `idx_goods`(`goods_id`) USING BTREE,
  INDEX `idx_end_time`(`end_time`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}coupon_usage`;
CREATE TABLE `{$db_prefix}coupon_usage`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `coupon_id` int(10) UNSIGNED NOT NULL COMMENT '优惠券ID',
  `coupon_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '优惠券码',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID(游客=0)',
  `order_id` int(10) UNSIGNED NOT NULL COMMENT '订单ID',
  `order_list_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '子订单ID',
  `amount_before` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠前金额(分)',
  `discount_amount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠金额(分)',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0预占 1已用 2回滚',
  `create_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_coupon_order`(`coupon_id`, `order_id`) USING BTREE,
  INDEX `idx_coupon_id`(`coupon_id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE,
  INDEX `idx_order_id`(`order_id`) USING BTREE,
  INDEX `idx_code`(`coupon_code`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}attribute_group`;
CREATE TABLE `{$db_prefix}attribute_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `delete_time` bigint(16) NULL DEFAULT NULL,
  `hide` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO `{$db_prefix}attribute_group` VALUES (1, '多规格演示模板', NULL, 'n');

DROP TABLE IF EXISTS `{$db_prefix}link`;
CREATE TABLE `{$db_prefix}link`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '链接表',
  `sitename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `siteurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
  `icon` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标URL',
  `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注信息',
  `hide` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '是否隐藏',
  `taxis` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序序号',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO {$db_prefix}link (id, sitename, siteurl, icon, description, taxis) VALUES (1, 'TTSHOP', 'https://emshop.ihehe.me', '', 'TTSHOP官方主页', 0);

DROP TABLE IF EXISTS `{$db_prefix}media_sort`;
CREATE TABLE `{$db_prefix}media_sort`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '资源分类表',
  `sortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}user_tier`;
CREATE TABLE `{$db_prefix}user_tier`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '相对于普通用户价格的折扣率',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}navi`;
CREATE TABLE `{$db_prefix}navi`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '导航表',
  `naviname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '导航名称',
  `url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '导航地址',
  `newtab` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '在新窗口打开',
  `hide` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '是否隐藏',
  `taxis` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序序号',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
  `isdefault` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '是否系统默认导航，如首页',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '导航类型 0自定义 1首页 2微语 3后台管理 4分类 5页面',
  `type_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '导航类型对应ID',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO {$db_prefix}navi (id, naviname, url, hide, taxis, isdefault, type) VALUES (1, '首页', '', 'y', 1, 'y', 1);
INSERT INTO {$db_prefix}navi (id, naviname, url, hide, taxis, isdefault, type) VALUES (2, '博客', 'blog', 'y', 1, 'y', 7);

DROP TABLE IF EXISTS `{$db_prefix}options`;
CREATE TABLE `{$db_prefix}options`  (
  `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '站点配置信息表',
  `option_name` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置项',
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置项值',
  PRIMARY KEY (`option_id`) USING BTREE,
  UNIQUE INDEX `option_name_uindex`(`option_name`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO {$db_prefix}options (option_name, option_value) VALUES 
('blogname','TTSHOP'),
('bloginfo','使用TTSHOP搭建的站点'),
('site_title',''),
('site_description',''),
('site_key','EM'),
('log_title_style','0'),
('blogurl','{$tt_url}'),
('icp',''),
('footer_info','本站使用 TTSHOP 免费开源程序搭建'),
('rss_output_num','10'),
('rss_output_fulltext','y'),
('index_lognum','12'),
('isfullsearch','n'),
('index_comnum','10'),
('index_newlognum','5'),
('index_hotlognum','5'),
('comment_subnum','20'),
('nonce_templet','default'),
('nonce_templet_tel','default'),
('admin_style','default'),
('tpl_sidenum','1'),
('comment_code','n'),
('comment_needchinese','n'),
('comment_interval',60),
('isgravatar','y'),
('isthumbnail','n'),
('att_maxsize','2048'),
('att_type','jpg,jpeg,png,gif,zip,rar'),
('att_imgmaxw','600'),
('att_imgmaxh','370'),
('comment_paging','y'),
('comment_pnum','10'),
('comment_order','newer'),
('iscomment','y'),
('login_comment','n'),
('ischkcomment','y'),
('isurlrewrite','0'),
('isalias','n'),
('isalias_html','n'),
('timezone','Asia/Shanghai'),
('active_plugins','$def_plugin'),
('widget_title','$widget_title'),
('custom_widget','a:0:{}'),
('widgets1','$def_widgets'),
('detect_url','y'),
('login_code','n'),
('email_code','n'),
('is_signup','y'),
('ischkarticle','y'),
('article_uneditable','n'),
('forbid_user_upload','n'),
('posts_per_day',10),
('smtp_mail',''),
('smtp_pw',''),
('smtp_server',''),
('smtp_port',''),
('is_openapi','n'),
('apikey','$apikey'),
('panel_menu_title',''),
('admin_article_perpage_num','10'),
('admin_user_perpage_num','20'),
('admin_comment_perpage_num','20'),
('sales_switch','y'),
('coupon_switch','y'),
('stock_switch','y'),
('order_email_switch','n'),
('order_pwd_switch','y'),
('order_tel_switch','n'),
('pay_redirect','list'),
('login_switch','y'),
('register_switch','y'),
('balance_switch','y'),
('register_email_switch', 'y'),
('register_tel_switch', 'y'),
('login_email_switch', 'y'),
('login_tel_switch', 'y'),
('mianze', '1'),
('admin_icon', '../favicon.ico'),
('home_icon', '../favicon.ico'),
('logo', ''),
('guest_query_contact_switch', 'y'),
('guest_query_contact_type', 'any'),
('guest_query_contact_placeholder_order', '请输入您的联系方式'),
('guest_query_contact_placeholder_query', '请输入您下单时填写的联系方式'),
('guest_query_password_switch', 'n'),
('guest_query_password_placeholder_order', '请设置订单密码'),
('guest_query_password_placeholder_query', '请输入您设置的订单密码');

DROP TABLE IF EXISTS `{$db_prefix}order`;
CREATE TABLE `{$db_prefix}order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `station_id` int(10) NOT NULL DEFAULT 0,
  `client_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '客户端ip',
  `user_id` int(10) NULL DEFAULT 0,
  `out_trade_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remote_trade` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `amount` int(10) NULL DEFAULT NULL,
  `origin_amount` int(10) NULL DEFAULT NULL COMMENT '优惠前金额(分)',
  `coupon_amount` int(10) NULL DEFAULT 0 COMMENT '优惠金额(分)',
  `coupon_id` int(10) NULL DEFAULT 0 COMMENT '优惠券ID',
  `coupon_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '优惠券码',
  `create_time` bigint(16) NULL DEFAULT NULL,
  `payment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付方式',
  `pay_plugin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付插件',
  `pay_time` bigint(16) NULL DEFAULT NULL,
  `update_time` bigint(16) NULL DEFAULT NULL,
  `qr_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `expire_time` bigint(16) NULL DEFAULT NULL,
  `device` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pay_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pay_status` int(10) NULL DEFAULT 0,
  `delete_time` bigint(16) NULL DEFAULT NULL,
  `service_status` tinyint(1) NULL DEFAULT 0,
  `status` tinyint(1) NULL DEFAULT 0,
  `contact` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pwd` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `up_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `em_local` varchar(38) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}order_list`;
CREATE TABLE `{$db_prefix}order_list`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NULL DEFAULT NULL,
  `goods_id` int(10) NULL DEFAULT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `attr_spec` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `attach_user` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remote_trade_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `quantity` int(10) NULL DEFAULT NULL,
  `unit_price` int(10) NULL DEFAULT NULL,
  `price` int(10) NULL DEFAULT NULL,
  `status` int(10) NULL DEFAULT 0 COMMENT '0:未发货，1:部分发货，2:全部发货',
  `cost_price` int(10) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}specification`;
CREATE TABLE `{$db_prefix}specification`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(10) NULL DEFAULT NULL,
  `spec_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `delete_time` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO `{$db_prefix}specification` VALUES (1, 1, '品类', NULL);
INSERT INTO `{$db_prefix}specification` VALUES (2, 1, '时长', NULL);

DROP TABLE IF EXISTS `{$db_prefix}spec_option`;
CREATE TABLE `{$db_prefix}spec_option`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spec_id` int(10) NULL DEFAULT NULL,
  `option_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `delete_time` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO `{$db_prefix}spec_option` VALUES (1, 1, '爱奇艺', NULL);
INSERT INTO `{$db_prefix}spec_option` VALUES (2, 1, '优酷', NULL);
INSERT INTO `{$db_prefix}spec_option` VALUES (3, 2, '一年', NULL);
INSERT INTO `{$db_prefix}spec_option` VALUES (4, 2, '三年', NULL);

DROP TABLE IF EXISTS `{$db_prefix}product_sku`;
CREATE TABLE `{$db_prefix}product_sku`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `option_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格组合，单规格为0，多规格为spec_option.id组合如1-3',
  `market_price` bigint(18) NULL DEFAULT NULL COMMENT '市场价',
  `cost_price` bigint(18) NULL DEFAULT NULL COMMENT '成本价',
  `guest_price` bigint(18) NULL DEFAULT NULL COMMENT '游客价格',
  `user_price` bigint(18) NULL DEFAULT NULL COMMENT '普通用户价格',
  `stock` int(10) NULL DEFAULT 0 COMMENT '库存',
  `sales` int(10) NULL DEFAULT 0 COMMENT '销量',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_goods_price`(`goods_id`, `guest_price`) USING BTREE,
  INDEX `idx_goods_price_id`(`goods_id`, `guest_price`, `id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}station_bill`;
CREATE TABLE `{$db_prefix}station_bill`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NULL DEFAULT NULL COMMENT '用户ID',
  `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
  `money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '更新金额',
  `_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '更新前的金额',
  `money_` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '更新后的金额',
  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '账单时间',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}sort`;
CREATE TABLE `{$db_prefix}sort`  (
  `sid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类表',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'goods',
  `sortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名',
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `taxis` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序序号',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父分类ID',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '备注',
  `kw` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `title` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '页面标题',
  `template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类模板',
  `sortimg` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类图像',
  `page_count` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '每页文章数量',
  `station_id` int(10) NOT NULL DEFAULT 0 COMMENT '分站ID',
  PRIMARY KEY (`sid`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO `{$db_prefix}sort` VALUES (1, 'goods', '演示分类', '', 0, 0, '', '', '', '', '', 0, 0);

DROP TABLE IF EXISTS `{$db_prefix}stock`;
CREATE TABLE `{$db_prefix}stock`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `goods_id` int(10) NOT NULL COMMENT '商品ID',
  `sku_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'SKU ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '卡密内容',
  `batch_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '批次号',
  `max_uses` int(10) NOT NULL DEFAULT 1 COMMENT '最大使用次数：1=独立，>1=限次，0=无限',
  `used_count` int(10) NOT NULL DEFAULT 0 COMMENT '已使用次数',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0可用，1已用完，2禁用',
  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '创建时间',
  `use_time` bigint(16) NULL DEFAULT NULL COMMENT '最后使用时间',
  `weight` bigint(16) NULL DEFAULT 0 COMMENT '权重',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_goods_sku_status`(`goods_id`, `sku_id`, `status`) USING BTREE,
  INDEX `idx_batch`(`batch_no`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}stock_usage`;
CREATE TABLE `{$db_prefix}stock_usage`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `stock_id` int(10) UNSIGNED NOT NULL COMMENT '库存ID（关联em_stock.id）',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `order_list_id` int(10) NOT NULL COMMENT '子订单ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `create_time` bigint(16) NOT NULL COMMENT '使用时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_stock_id`(`stock_id`) USING BTREE,
  INDEX `idx_order`(`order_id`, `order_list_id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}storage`;
CREATE TABLE `{$db_prefix}storage`  (
  `sid` int(8) NOT NULL AUTO_INCREMENT COMMENT '对象存储表',
  `plugin` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '插件名',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对象名',
  `type` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对象数据类型',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对象值',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  `lastupdate` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`sid`) USING BTREE,
  UNIQUE INDEX `plugin`(`plugin`, `name`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}tag`;
CREATE TABLE `{$db_prefix}tag`  (
  `tid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '标签表',
  `tagname` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签名',
  `description` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '页面描述',
  `title` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '页面标题',
  `kw` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `gid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`tid`) USING BTREE,
  INDEX `tagname`(`tagname`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}tpl_options_data`;
CREATE TABLE `{$db_prefix}tpl_options_data`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `depend` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `template`(`template`, `name`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}station`;
CREATE TABLE `{$db_prefix}station`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NULL DEFAULT NULL COMMENT '用户ID',
  `pid` int(10) NULL DEFAULT NULL COMMENT '上级站点ID',
  `level_id` int(10) NULL DEFAULT NULL COMMENT '分站等级ID',
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '开通价格',
  `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '独立域名',
  `domain_2` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '二级域名',
  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '开通时间',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `master_sort` tinyint(1) NULL DEFAULT NULL,
  `master_goods` tinyint(1) NULL DEFAULT NULL,
  `domain_2_prefix` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `domain_2_suffix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `roll_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `home_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `delete_time` bigint(16) NULL DEFAULT NULL,
  `station_unique` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `money` decimal(10, 2) NULL DEFAULT 0.00,
  `goods_premium` decimal(10, 2) NULL DEFAULT 0.10,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}station_goods`;
CREATE TABLE `{$db_prefix}station_goods`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
  `goods_id` int(10) NULL DEFAULT NULL COMMENT '商品ID',
  `premium` decimal(10, 2) NULL DEFAULT NULL COMMENT '加价百分比',
  `is_show` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'y' COMMENT '是否显示',
  `custom_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '自定义商品名',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}station_level`;
CREATE TABLE `{$db_prefix}station_level`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站等级名称',
  `price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '分站开通价格',
  `is_station` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站开通权限',
  `is_domain` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站独立域名',
  `is_goods` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站供货权限',
  `service_change` decimal(10, 2) NULL DEFAULT NULL COMMENT '分站供货手续费',
  `cash_change` decimal(10, 2) NULL DEFAULT NULL COMMENT '提现手续费',
  `using` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '是否启用该分站等级',
  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` bigint(16) NULL DEFAULT NULL COMMENT '编辑时间',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

INSERT INTO `{$db_prefix}station_level` VALUES (1, '体验版', 99.00, 'n', 'n', 'n', NULL, 0.20, NULL, 1770902552, 1770902562);
INSERT INTO `{$db_prefix}station_level` VALUES (2, '高级版', 199.00, 'n', 'n', 'n', NULL, 0.10, NULL, 1770902588, 1772093900);
INSERT INTO `{$db_prefix}station_level` VALUES (3, '专业版', 399.00, 'n', 'y', 'n', NULL, 0.05, NULL, 1770902598, 1770902625);
INSERT INTO `{$db_prefix}station_level` VALUES (4, '至尊版', 599.00, 'n', 'y', 'n', NULL, 0.00, NULL, 1770954213, 1770954215);

DROP TABLE IF EXISTS `{$db_prefix}station_plugin`;
CREATE TABLE `{$db_prefix}station_plugin`  (
  `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
  `plugin_id` int(11) NULL DEFAULT NULL,
  `plugin_name_cn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `plugin_name_en` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pc_switch` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tel_switch` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}station_sort`;
CREATE TABLE `{$db_prefix}station_sort`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
  `sort_id` int(10) NULL DEFAULT NULL COMMENT '分类ID',
  `custom_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '自定义分类名称',
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分类类型',
  `is_show` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '是否显示',
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}station_storage`;
CREATE TABLE `{$db_prefix}station_storage`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `station_id` int(10) NOT NULL COMMENT '分站ID',
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'tpl or plugin',
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `option_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `option_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `tpl`(`station_id`, `type`, `plugin_name`, `option_name`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}withdraw`;
CREATE TABLE `{$db_prefix}withdraw`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NULL DEFAULT NULL,
  `amount` decimal(10, 2) NULL DEFAULT NULL,
  `method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `account` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remark` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` tinyint(1) NULL DEFAULT NULL,
  `create_time` bigint(16) NULL DEFAULT NULL,
  `service_change` decimal(10, 2) NULL DEFAULT NULL,
  `finish_time` bigint(16) NULL DEFAULT NULL,
  `reject_time` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}user`;
CREATE TABLE `{$db_prefix}user`  (
  `uid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户表',
  `station_id` int(10) NOT NULL DEFAULT 0,
  `expend` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '总消费',
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户密码',
  `money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '用户余额',
  `nickname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `level` tinyint(1) NULL DEFAULT 0 COMMENT '用户等级',
  `role` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户组',
  `ischeck` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT '内容是否需要管理员审核',
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `tel` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '手机号码',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `ip` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ip地址',
  `reg_ip` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '注册IP',
  `state` tinyint(4) NOT NULL DEFAULT 0 COMMENT '用户状态 0正常 1禁用',
  `credits` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户积分',
  `_token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '用户token',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`uid`) USING BTREE,
  INDEX `username`(`username`) USING BTREE,
  INDEX `email`(`email`) USING BTREE
)" . $table_charset_sql . "
INSERT INTO {$db_prefix}user (uid, username, email, password, nickname, role, create_time, update_time) VALUES (10000,'$username','$email','$password', '管理员','admin', {$timestamp}, {$timestamp});

DROP TABLE IF EXISTS `{$db_prefix}charge`;
CREATE TABLE `{$db_prefix}charge`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NULL DEFAULT NULL,
  `out_trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `amount` int(10) NULL DEFAULT NULL,
  `pay_plugin` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `payment` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `create_time` bigint(16) NULL DEFAULT NULL,
  `expire_time` bigint(16) NULL DEFAULT NULL,
  `pay_time` bigint(16) NULL DEFAULT NULL,
  `pay_status` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
)" . $table_charset_sql . "

DROP TABLE IF EXISTS `{$db_prefix}login_attempt`;
CREATE TABLE `{$db_prefix}login_attempt`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fail_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `first_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `lock_until` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `lockouts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `type_subject` (`type`,`subject`) USING BTREE
)" . $table_charset_sql;




    $array_sql = preg_split("/;[\r\n]/", $sql);
    foreach ($array_sql as $sql) {
        $sql = trim($sql);
        if ($sql) {
            $DB->query($sql);
        }
    }
    $CACHE->updateCache();

    $show_warning = $env_em_env === 'develop' || ($env_em_env !== 'develop' && !@unlink('./install.php'));

    $ttGatewayUrl = "https://emshop.ihehe.me/api/emshop.php?action=install_record";
    $reqData = [
        "ip" => getServerIp(),
        "service_token" => SERVICE_TOKEN,
        "version" => Option::TT_VERSION
    ];
    ttCurl($ttGatewayUrl, http_build_query($reqData), true, false, 5);

    Log::info('TTSHOP系统安装完成');

    ?>
    <!doctype html>
    <html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>安装成功 - TTSHOP</title>
        <link rel="stylesheet" href="./content/static/css/install.css">
    </head>
    <body class="install-page">
    <div class="install install-center">
        <main class="success">
            <div class="success-seal" aria-hidden="true">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h1 class="success-title">安装成功</h1>
            <p class="success-subtitle">TTSHOP 已成功安装，现在可以开始使用了</p>

            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">管理员账号</span>
                    <span class="info-value"><?php echo htmlspecialchars($username); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">管理员密码</span>
                    <span class="info-value">您设置的密码</span>
                </div>
                <div class="info-row">
                    <span class="info-label">管理面板</span>
                    <span class="info-value">
                        <a href="<?= realUrl() ?>admin" target="_blank"><?= realUrl() ?>admin</a>
                    </span>
                </div>
            </div>

            <?php if ($show_warning): ?>
            <div class="warning-box">
                <span class="warning-title">安全提示</span>请手动删除根目录下的安装文件 install.php
            </div>
            <?php endif; ?>

            <div class="btn-group">
                <a href="./" class="btn-ghost">访问首页</a>
                <a href="./admin/" class="btn-primary">进入后台</a>
            </div>
        </main>
    </div>
    </body>
    </html>
    <?php
}
?>
