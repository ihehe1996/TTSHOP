<?php

/**
 * 一次性自检脚本：**走一遍新的授权那套**（2026-10-05 加的），不经过后台页面。
 *
 *   php .license-check.php activate <授权码>
 *   php .license-check.php status
 *   php .license-check.php unbind
 *
 * 为什么要这么一个脚本：后台页面要登录态，本地手测不方便；而这套逻辑
 * （LicenseClient + LicenseService + 存哪）是这次改动最要紧的地方，
 * 得能单独跑一遍。**用完可以删**（它不是程序的一部分）。
 */

$_SERVER['HTTP_HOST'] = getenv('TT_FAKE_HOST') ?: 'tt-test.example.com';

/* init.php 里那几个常量这里得自己来（这个脚本不走 init.php） */
const TT_ROOT = __DIR__;
const EM_ROOT = __DIR__;
const MSGCODE_TTKEY_INVALID = 1001;
const MSGCODE_EMKEY_INVALID = 1001;
const MSGCODE_NO_UPUPDATE = 1002;
const MSGCODE_SUCCESS = 200;

/* 授权服务器地址（init.php 里也有一个一模一样的；这个脚本不走 init.php）*/
define('TT_LICENSE_SERVER_URL', getenv('TT_LICENSE_SERVER_URL') ?: 'http://127.0.0.1:3000/');

require __DIR__ . '/config.php';
require __DIR__ . '/base.php';
require __DIR__ . '/include/lib/common.php';

spl_autoload_register('ttAutoload');

$CACHE = Cache::getInstance();
date_default_timezone_set(Option::get('timezone'));

function show($label, $value)
{
    printf("  %-22s %s\n", $label, is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE));
}

function dumpState()
{
    $license = LicenseService::currentLicense();

    show('本机域名(getTopHost)', getTopHost());

    if ($license === null) {
        show('授权状态', '未激活');
        return;
    }

    show('授权状态', '已激活');
    show('ttkey', $license['ttkey']);
    show('档位', $license['type'] . '（' . LicenseService::levelLabel($license['type']) . '）');
    show('主授权域名', $license['main_host']);
    show('别名', $license['aliases']);
    show('isRegLocal()', Register::isRegLocal() ? 'true' : 'false');
    show('getRegType()', Register::getRegType());
}

$cmd = isset($argv[1]) ? $argv[1] : 'status';

echo "── 前 ──\n";
dumpState();

try {
    if ($cmd === 'activate') {
        $code = isset($argv[2]) ? $argv[2] : '';
        echo "── 激活 ──\n";
        $result = LicenseService::activate($code);
        show('绑到', $result['bound_domain']);
        show('档位', $result['level_label']);
    } elseif ($cmd === 'unbind') {
        echo "── 解绑 ──\n";
        LicenseService::unbind();
        show('结果', '已解绑');
    } elseif ($cmd === 'revalidate') {
        echo "── 自检 ──\n";
        show('服务端核对成功', LicenseService::revalidateCurrent() ? 'true' : 'false');
    }

    echo "── 后 ──\n";
    dumpState();
} catch (Throwable $e) {
    echo "\n❌ " . $e->getMessage() . "\n";
}
