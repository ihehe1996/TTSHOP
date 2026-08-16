<?php
/**
 * TTSHOP 同系统对接插件 - 设置入口
 */

defined('TT_ROOT') || exit('access denied!');

if (!function_exists('goodsTtPlugin')) {
    ttMsg('请先启用商品对接插件');
}

function plugin_setting_view()
{
    require_once TT_ROOT . '/content/plugins/goods_em/goods_em_show.php';
}

function plugin_setting()
{
    return true;
}
