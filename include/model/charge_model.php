<?php
/**
 * 充值订单模型
 */

class Charge_Model {

    private $db;
    private $table;
    private $table_user;
    private $db_prefix;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->table = DB_PREFIX . 'charge';
        $this->table_user = DB_PREFIX . 'user';
    }
    
    /**
     * 获取充值订单信息
     */
    public function getChargeInfo($out_trade_no){
        $sql = "SELECT * FROM $this->table WHERE out_trade_no = '{$out_trade_no}'";
        return $this->db->once_fetch_array($sql);
    }
    

    /**
     * 更新订单的支付状态
     */
	public function updatePayStatus($out_trade_no){
		$sql = "UPDATE $this->table SET pay_status = 1 WHERE out_trade_no = '{$out_trade_no}'";
		return $this->db->execute($sql);
	}

    public function updateInfo($out_trade_no, $data){
        $Item = [];
		foreach ($data as $key => $var) {
		    $Item[] = "$key='$var'";
		}
		$upStr = implode(',', $Item);
		$sql = "UPDATE $this->table SET $upStr WHERE out_trade_no = '{$out_trade_no}'";
		return $this->db->execute($sql);
    }


    public function getList($user_id = null, $page = 1, $pageNum = 10) {
        // 构建WHERE条件
        $where = "";
        if (!empty($user_id)) {
            $where = " WHERE user_id = '" . $user_id . "'";
        }
        
        // 计算偏移量
        $offset = ($page - 1) * $pageNum;
        
        // 查询数据
        $sql = "SELECT c.*, u.username, u.nickname, u.email, u.tel 
                FROM " . $this->table . " c 
                LEFT JOIN " . DB_PREFIX . "user u ON c.user_id = u.uid 
                " . $where . " 
                ORDER BY c.id DESC LIMIT " . $offset . "," . $pageNum;
        $result = $this->db->fetch_all($sql);

        // 格式化数据
        foreach($result as &$val){
            $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
            $val['pay_time'] = empty($val['pay_time']) ? '' : date('Y-m-d H:i:s', $val['pay_time']);
            $val['amount'] = number_format($val['amount'] / 100, 2);
            $val['status_text'] = '未知状态';
            if($val['pay_status'] == 0){
                $val['status_text'] = '未支付';
            }
            if($val['pay_status'] == 1){
                $val['status_text'] = '已支付';
            }
        }
        
        // 查询总记录数
        $countSql = "SELECT COUNT(*) as total FROM " . $this->table . $where;
        $countResult = $this->db->once_fetch_array($countSql);
        
        // 返回结果集和总记录数
        return [
            'list' => $result,
            'total' => $countResult['total']
        ];
    }

    public function deleteByIds($ids) {
        if (empty($ids)) {
            return 0;
        }
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) {
            return 0;
        }
        $idStr = implode(',', $ids);
        $sql = "DELETE FROM " . $this->table . " WHERE id IN ({$idStr})";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }
    
}
