<?php
/**
 * 登录 / 找回密码 / 验证码接口 限流防爆破
 *
 * 按「IP + 账号/邮箱」双维度计数，达到阈值后锁定，并支持指数退避。
 * 计数写入 login_attempt 表（由 install.php 或 update.sql 建表）。
 *
 * 注意：$subject 由调用方传入，需保证已通过 Input 或 filter_var 处理（与全站惯例一致）。
 *
 * @package TTSHOP
 */

class LoginThrottle {

    const TYPE_LOGIN = 'login';   // 登录
    const TYPE_RESET = 'reset';   // 找回密码发送邮件
    const TYPE_EMAIL = 'email';   // 通用邮件验证码

    /**
     * 各场景的限流参数
     */
    private static function config($type) {
        $cfg = array(
            self::TYPE_LOGIN => array(
                'window'        => 300,   // 计数窗口（秒）
                'captcha_after' => 5,     // 失败达到该次数后强制图形验证码（仅登录）
                'lock_after'    => 10,    // 失败达到该次数后锁定
                'lock_time'     => 900,   // 首次锁定时长（秒）
                'lock_max'      => 86400, // 锁定时长上限（秒）
            ),
            self::TYPE_RESET => array(
                'window'        => 3600,
                'captcha_after' => 0,
                'lock_after'    => 5,
                'lock_time'     => 3600,
                'lock_max'      => 86400,
            ),
            self::TYPE_EMAIL => array(
                'window'        => 3600,
                'captcha_after' => 0,
                'lock_after'    => 5,
                'lock_time'     => 3600,
                'lock_max'      => 86400,
            ),
        );
        return isset($cfg[$type]) ? $cfg[$type] : $cfg[self::TYPE_LOGIN];
    }

    private static function db() {
        return Database::getInstance();
    }

    private static function getRow($type, $subject) {
        $sql = "SELECT * FROM " . DB_PREFIX . "login_attempt WHERE type='$type' AND subject='$subject'";
        return self::db()->once_fetch_array($sql);
    }

    /**
     * 剩余锁定时长（秒），0 表示未锁定
     */
    public static function lockedSeconds($type, $subject) {
        if (empty($subject)) {
            return 0;
        }
        $row = self::getRow($type, $subject);
        if (empty($row) || empty($row['lock_until'])) {
            return 0;
        }
        $remain = (int)$row['lock_until'] - time();
        return $remain > 0 ? $remain : 0;
    }

    /**
     * 是否需要强制图形验证码（即便全局 login_code 关闭）
     */
    public static function needCaptcha($type, $subject) {
        if (empty($subject)) {
            return false;
        }
        $cfg = self::config($type);
        if (empty($cfg['captcha_after'])) {
            return false;
        }
        $row = self::getRow($type, $subject);
        if (empty($row) || empty($row['first_time'])) {
            return false;
        }
        if (time() - (int)$row['first_time'] > $cfg['window']) {
            return false;
        }
        return (int)$row['fail_count'] >= $cfg['captcha_after'];
    }

    /**
     * 记录一次失败/尝试，返回当前计数
     */
    public static function record($type, $subject) {
        if (empty($subject)) {
            return 0;
        }
        $cfg = self::config($type);
        $now = time();
        $row = self::getRow($type, $subject);

        if (empty($row)) {
            $count = 1;
            $first = $now;
            $lockouts = 0;
            $lock = 0;
        } else {
            $count = (int)$row['fail_count'];
            $first = (int)$row['first_time'];
            $lockouts = (int)$row['lockouts'];
            $lock = (int)$row['lock_until'];

            // 窗口已过期且当前未锁定：重新计数
            if ($now - $first > $cfg['window'] && $now >= $lock) {
                $count = 0;
                $first = $now;
            }
            $count++;

            // 达到锁定阈值：指数退避
            if ($cfg['lock_after'] > 0 && $count >= $cfg['lock_after']) {
                $lockouts++;
                $shift = $lockouts - 1;
                if ($shift > 10) {
                    $shift = 10;
                }
                $duration = $cfg['lock_time'] * (1 << $shift);
                if ($duration > $cfg['lock_max']) {
                    $duration = $cfg['lock_max'];
                }
                $lock = $now + $duration;
            }
        }

        $sql = "INSERT INTO " . DB_PREFIX . "login_attempt (type, subject, fail_count, first_time, last_time, lock_until, lockouts)
                VALUES ('$type', '$subject', $count, $first, $now, $lock, $lockouts)
                ON DUPLICATE KEY UPDATE fail_count=$count, first_time=$first, last_time=$now, lock_until=$lock, lockouts=$lockouts";
        self::db()->query($sql, true);

        return $count;
    }

    /**
     * 成功后清零
     */
    public static function clear($type, $subject) {
        if (empty($subject)) {
            return;
        }
        $sql = "DELETE FROM " . DB_PREFIX . "login_attempt WHERE type='$type' AND subject='$subject'";
        self::db()->query($sql, true);
    }

    /**
     * 获取用于限流的客户端 IP（不直接信任 X-Forwarded-For）
     */
    public static function clientIp() {
        $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        // 仅当请求来自本机/内网反向代理时，才信任 X-Forwarded-For 的第一个 IP
        if (self::isPrivateIp($remote)) {
            $xff = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
            if ($xff) {
                $parts = explode(',', $xff);
                $ip = trim($parts[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $remote;
    }

    private static function isPrivateIp($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        // 私有/回环/保留地址（含 127.0.0.1、10.x、192.168.x 等）返回 true
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
