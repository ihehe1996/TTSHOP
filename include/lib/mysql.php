<?php
/**
 * Database operation routing (only compatible with old version, not recommended)
 *
 * @package TTSHOP
 * @link https://www.emlog.net
 */

class MySql {

    public static function getInstance() {
        if (class_exists('mysqli', FALSE)) {
            return MySqlii::getInstance();
        }

        ttMsg('服务器PHP不支持MySQL数据库');
    }

}
