<?php


/**
 * 正版授权（后台「正版授权」那一页）。
 *
 * 2026-10-05 改过：授权那套从**旧表 + 旧协议**搬到了
 * **`options` 键值 + 新协议**（见 `include/service/licenseservice.php`）。
 * 这个文件原来是直接读写 `authorization` 那张表的（还拼裸 SQL），现在一律
 * 走 `LicenseService` / `Register` —— 那两个是唯一知道「状态存在哪」的地方。
 *
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

/** 档位 → 页面上显示的那句话。**只有这一处**，别再在视图里另判一遍 */
function authLevelLabel($level)
{
    switch ((string) $level) {
        case '1':
            return 'VIP授权';
        case '2':
            return 'SVIP授权';
        case '3':
            return '至尊授权';
        default:
            return '未授权';
    }
}

if (empty($action)) {

    $br = '<a href="./">控制台</a><a><cite>正版授权</cite></a>';

    /*
     * 进页面先向服务端核对一次（和以前一样）。
     * ⚠️ 现在它**只在服务端明确回「未授权」时**才清本地状态 ——
     * 连不上时维持原状（旧代码在这里是「连不上 = 放行」，那是个洞）。
     */
    Register::isRegServer();

    $license = LicenseService::currentLicense();

    /* 视图要的两个变量（保持旧名字，视图那一份先不动）*/
    $ttkey = $license === null ? false : $license['ttkey'];
    $ttkey_type = authLevelLabel($license === null ? '' : $license['type']);

    /* 新给的几个：绑的域名、别名、档位数字 —— 视图下一步用得上 */
    $license_host = $license === null ? '' : $license['main_host'];
    $license_aliases = $license === null ? [] : $license['aliases'];
    $license_level = $license === null ? '' : $license['type'];

    include View::getAdmView('header');
    require_once(View::getAdmView('auth'));
    include View::getAdmView('footer');
    View::output();
}

/* 激活：传授权码，绑到本机域名上 */
if ($action === 'auth') {
    /* ⚠️ CSRF 那一道（2026-10-05 补的）：这个文件里三个 action 原来**都没校验**
       token，而后台别处（article / blogger / comment / coupon……）都是 `checkToken()`
       开头。表单本来就带着 `token`，加上它不影响任何正常流程 */
    LoginAuth::checkToken();

    $ttkey = Input::postStrVar('ttkey');

    if (empty($ttkey)) {
        Ret::error('请输入授权码');
    }

    /* doReg 内部已经走新协议了（激活成功之后它自己会写本地状态）——
       这里**不再**往 authorization 表里插一行：那张表已经不读了 */
    $r = Register::doReg($ttkey);

    if ($r['code'] != 200) {
        Ret::error($r['msg']);
    }

    Ret::success('授权成功');
}

/*
 * 解绑：把这台机器从授权上摘下来（码和档位都不动）。
 *
 * ⚠️ 会**先问服务端**（`LicenseService::unbind()` 里），远程成功才清本地 ——
 * 反过来的话会出现「本地说没绑、服务端还绑着你这个域名」，
 * 于是同一个码再也绑不到别的域名上。
 */
if ($action === 'unbind') {
    LoginAuth::checkToken();

    try {
        LicenseService::unbind();
    } catch (RuntimeException $e) {
        Ret::error($e->getMessage());
    }

    Ret::success('已解绑，这个域名可以重新绑定到别的授权码上');
}

/* 重新校验：办不了别的事的时候点它一下（换过域名、怀疑码作废了）*/
if ($action === 'revalidate') {
    LoginAuth::checkToken();

    if (LicenseService::currentLicense() === null) {
        Ret::error('当前未激活，无需校验');
    }

    $ok = LicenseService::revalidateCurrent();

    if (!$ok) {
        Ret::error('连不上授权服务器，请稍后重试');
    }

    if (!LicenseService::isActivated()) {
        Ret::error('服务端判定这条授权已失效（码作废了，或者域名被解绑了）');
    }

    Ret::success('授权正常');
}
