<?php
/**
 * article and page model
 *
 * @package TTSHOP
 * @link https://www.emlog.net
 */

class Balance_Model {

    private $db;
    private $Parsedown;
    private $table;
    private $table_user;
    private $table_sort;
    private $db_prefix;
    private $timestamp;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->table = DB_PREFIX . 'order';
        $this->table_user = DB_PREFIX . 'user';
        $this->db_prefix = DB_PREFIX;
        $this->table_sort = DB_PREFIX . 'sort';
        $this->Parsedown = new Parsedown();
        $this->Parsedown->setBreaksEnabled(true); //automatic line wrapping
        $this->timestamp = time();
    }

    /**
     * 扣除用户余额
     */
    public function dec($user_id, $amount, $message = '系统扣除'){
        $user = $this->db->once_fetch_array("select * from {$this->db_prefix}user where uid={$user_id}");
        $sql = "UPDATE `{$this->db_prefix}user` SET `money` = money - {$amount} WHERE `uid` = {$user_id}";
        $this->db->query($sql);
        $log = [
            'user_id' => $user_id,
            'plus' => 'n',
            'update_before' => $user['money'],
            'money' => $amount,
            'description' => $message,
            'create_time' => $this->timestamp
        ];
        $this->db->add('balance_log', $log);
    }

    /**
     * 增加用户余额
     */
    public function inc($user_id, $amount, $message = '系统充值'){
        $user = $this->db->once_fetch_array("select * from {$this->db_prefix}user where uid={$user_id}");
        $sql = "UPDATE `{$this->db_prefix}user` SET `money` = money + {$amount} WHERE `uid` = {$user_id}";
        $this->db->query($sql);
        $log = [
            'user_id' => $user_id,
            'plus' => 'y',
            'update_before' => $user['money'],
            'money' => $amount,
            'description' => $message,
            'create_time' => $this->timestamp
        ];
        $this->db->add('balance_log', $log);
    }



}
