<?php
/**
 * View control
 * @package TTSHOP
 */

class View {
    public static function getView($template, $ext = '.php') {
        if(STATION_DATA){
            $nonce_templet = isMobile() ? STATION_DATA['tel_tpl'] : STATION_DATA['pc_tpl'];
            if (strpos(TPLS_PATH, 'em_null_tpl') || empty($nonce_templet)) {
                ttMsg('当前未启用任何模板，请启用模板。', TT_URL . 'user/station.php?action=template');
            }
            if (!is_dir(TPLS_PATH . $nonce_templet)) {
                ttMsg('当前使用的模板已被删除或损坏，请更换其他模板。', TT_URL . 'user/station.php?action=template');
            }
            return TPLS_PATH . $nonce_templet . '/' . $template . $ext;
        }else{
            
            $nonce_templet = isMobile() ? Option::get('nonce_templet_tel') : Option::get('nonce_templet');
            if (strpos(TEMPLATE_PATH, 'em_null_tpl') || empty($nonce_templet)) {
                ttMsg('当前未启用任何模板，请登录后台启用模板。 错误码：tt_template', TT_URL . 'admin/template.php');
            }
            if (!is_dir(TEMPLATE_PATH)) {
                ttMsg('当前使用的模板已被删除或损坏，请登录后台更换其他模板。 错误码：tt_one_template', TT_URL . 'admin/template.php');
            }
            return TEMPLATE_PATH . $template . $ext;
        }

    }

    public static function getHeaderView($template, $ext = 'header.php') {
        $template_dir = $template == 'common' ? COMMON_TEMPLATE_PATH : TEMPLATE_PATH;
        // echo $template_dir;die;
        if (!is_dir($template_dir)) {
            ttMsg('当前使用的模板已被删除或损坏，请登录后台更换其他模板。 错误码：tt_one_header', TT_URL . 'admin/template.php');
        }
        return $template_dir . $ext;
    }

    public static function getFooterView($template, $ext = 'footer.php') {
        $template_dir = $template == 'common' ? COMMON_TEMPLATE_PATH : TEMPLATE_PATH;
        if (!is_dir($template_dir)) {
            ttMsg('当前使用的模板已被删除或损坏，请登录后台更换其他模板。 错误码：tt_one_footer', TT_URL . 'admin/template.php');
        }
        return $template_dir . $ext;
    }

    public static function getCommonView($template, $ext = '.php') {
        if (!is_dir(COMMON_TEMPLATE_PATH)) {
            ttMsg('当前使用的模板已被删除或损坏，请登录后台更换其他模板。 错误码：tt_common_template', TT_URL . 'admin/template.php');
        }
        return COMMON_TEMPLATE_PATH . $template . $ext;
    }

    public static function getBlogView($template, $ext = '.php') {
        if (!is_dir(BLOG_TEMPLATE_PATH)) {
            ttMsg('当前使用的博客模板已被删除或损坏，请登录后台更换其他模板。');
        }
        return BLOG_TEMPLATE_PATH . $template . $ext;
    }

    public static function getAdmView($template, $ext = '.php') {
        if (!is_dir(ADMIN_TEMPLATE_PATH)) {
            ttMsg('后台模板已损坏', TT_URL);
        }
        return ADMIN_TEMPLATE_PATH . $template . $ext;
    }

    public static function getUserView($template, $ext = '.php') {
        if (!is_dir(USER_TEMPLATE_PATH)) {
            ttMsg('后台模板已损坏', TT_URL);
        }
        return USER_TEMPLATE_PATH . $template . $ext;
    }

    public static function isTplExist($template, $ext = '.php') {
        if (file_exists(TEMPLATE_PATH . $template . $ext)) {
            return true;
        }
        return false;
    }

    public static function output() {
        $content = ob_get_clean();
        ob_start();
        echo $content;
        ob_end_flush();
        exit;
    }

}
