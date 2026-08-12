<?php
/**
 * article and page model
 *
 * @package EMLOG
 * @link https://www.emlog.net
 */

class Station_Model {

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
     * 创建分站
     */
    public function create($user_id, $level_id, $amount){
        $current_domain = getDomain();
        $sql = "select * from {$this->db_prefix}station where domain = '{$current_domain}' or domain_2 = '{$current_domain}'";
        $station = $this->db->once_fetch_array($sql);
        $pid = empty($station) ? 0 : $station['id'];
        $insert = [
            'user_id' => $user_id,
            'pid' => $pid,
            'level_id' => $level_id,
            'amount' => $amount,
            'create_time' => $this->timestamp,
            'station_unique' => generateUUIDv4()
        ];
        $this->db->add('station', $insert);
    }

    /**
     * 增加分站的余额
     */
    public function incMoney($stationData, $money){
        if($money <= 0) return;

        $_money = $stationData['money'];
        $money_ = $_money + $money;

        $sql = "update {$this->db_prefix}station set money = money + {$money} where id = {$stationData['id']}";
        $this->db->query($sql);

        $timestamp = TIMESTAMP;
        $sql = "insert into {$this->db_prefix}station_bill (user_id, station_id, money, _money, money_, create_time) values ({$stationData['user_id']}, {$stationData['id']}, {$money}, {$_money}, {$money_}, {$timestamp})";
        $this->db->query($sql);
    }

    /**
     * 返回当前分站的信息
     */
    public function getStationInfo(){
        $station_switch = Option::get('station_switch');
        if ($station_switch === 'n') {
            return false;
        }
        $current_domain = getDomain();
        $sql = "select * from {$this->db_prefix}station where delete_time is null and (domain = '{$current_domain}' or domain_2 = '{$current_domain}')";
        $station = $this->db->once_fetch_array($sql);

        if (empty($station)) {
            return false;
        } else {
            $station_id = (int)$station['id'];
            $sql = "select plugin_name_en from {$this->db_prefix}station_plugin where station_id = {$station_id} and type = 'tpl' and pc_switch = 'y' limit 1";
            $res = $this->db->once_fetch_array($sql);
            $pc_tpl = empty($res) ? 'default' : $res['plugin_name_en'];

            $sql = "select plugin_name_en from {$this->db_prefix}station_plugin where station_id = {$station_id} and type = 'tpl' and tel_switch = 'y' limit 1";
            $res = $this->db->once_fetch_array($sql);
            $tel_tpl = empty($res) ? 'default' : $res['plugin_name_en'];

            $station['pc_tpl'] = $pc_tpl;
            $station['tel_tpl'] = $tel_tpl;
            $station['roll_bulletin'] = $station['roll_notice'];
            $station['home_bulletin'] = $station['home_notice'];
            return $station;
        }
    }


}
