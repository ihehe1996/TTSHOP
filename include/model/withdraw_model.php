<?php


class Withdraw_Model {

    private $db;
    private $Parsedown;
    private $table;
    private $db_prefix;
    private $timestamp;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->db_prefix = DB_PREFIX;
        $this->table = DB_PREFIX . 'withdraw';
        $this->Parsedown = new Parsedown();
        $this->Parsedown->setBreaksEnabled(true); //automatic line wrapping
        $this->timestamp = time();
    }

    private function getMethodText($method) {
        $map = [
            'alipay' => '支付宝',
            'wechat' => '微信',
            'bank' => '银行卡',
        ];
        return isset($map[$method]) ? $map[$method] : ($method ?: '未知');
    }

    private function getStatusLabel($status) {
        switch ((int)$status) {
            case 0:
                return '待处理';
            case 1:
                return '已完成';
            case 2:
                return '已拒绝';
            default:
                return '未知状态';
        }
    }

    private function getStatusText($status) {
        switch ((int)$status) {
            case 0:
                return '待处理';
            case 1:
                return '提现完成';
            case 2:
                return '申请拒绝，余额已返回账户';
            default:
                return '未知状态';
        }
    }

    private function formatWithdrawRow($row) {
        if (empty($row)) {
            return $row;
        }
        $row['create_time'] = !empty($row['create_time']) ? date('Y-m-d H:i:s', $row['create_time']) : '';
        $row['finish_time'] = !empty($row['finish_time']) ? date('Y-m-d H:i:s', $row['finish_time']) : '';
        $row['reject_time'] = !empty($row['reject_time']) ? date('Y-m-d H:i:s', $row['reject_time']) : '';
        $row['amount'] = is_numeric($row['amount']) ? number_format((float)$row['amount'], 2, '.', '') : $row['amount'];
        $row['method_text'] = $this->getMethodText($row['method']);
        $row['status_label'] = $this->getStatusLabel($row['status']);
        $row['status_text'] = $this->getStatusText($row['status']);

        $row['user_username'] = isset($row['user_username']) ? $row['user_username'] : '';
        $row['user_nickname'] = isset($row['user_nickname']) && $row['user_nickname'] !== '' ? $row['user_nickname'] : $row['user_username'];
        $row['user_email'] = isset($row['user_email']) ? $row['user_email'] : '';
        $row['user_tel'] = isset($row['user_tel']) ? $row['user_tel'] : '';

        $row['user_account'] = $row['user_username'];
        if (empty($row['user_account'])) {
            $row['user_account'] = $row['user_email'] ?: $row['user_tel'];
        }

        return $row;
    }

    /**
     * 提现通过
     */
    public function pass($id) {
        $update = [
            'status' => 1,
            'finish_time' => $this->timestamp,
        ];
        $this->db->update('withdraw', $update, ['id' => $id]);
    }
    /**
     * 提现拒绝
     */
    public function reject($id) {
        $update = [
            'status' => 2,
            'reject_time' => $this->timestamp,
        ];
        $this->db->update('withdraw', $update, ['id' => $id]);

        $withdraw = $this->db->once_fetch_array("SELECT * FROM " . $this->table . " WHERE id = '" . $id . "'");

        $balanceModel = new Balance_Model();
        $balanceModel->inc($withdraw['user_id'], $withdraw['amount'], '提现拒绝，余额已返回账户');
    }

    /**
     * 获取提现记录列表
     */
    public function getWithdrawList($user_id = null, $page = 1, $pageNum = 10) {
        // 构建WHERE条件
        $where = "";
        if (!empty($user_id)) {
            $where = " WHERE w.user_id = '" . $user_id . "'";
        }
        
        // 计算偏移量
        $offset = ($page - 1) * $pageNum;
        
        // 查询数据
        $sql = "SELECT w.*, u.username as user_username, u.nickname as user_nickname, u.email as user_email, u.tel as user_tel
                FROM " . $this->table . " w
                LEFT JOIN " . $this->db_prefix . "user u ON w.user_id = u.uid
                {$where} ORDER BY w.id DESC LIMIT " . $offset . "," . $pageNum;
        $result = $this->db->fetch_all($sql);

        // 格式化数据
        foreach($result as &$val){
            $val = $this->formatWithdrawRow($val);
        }
        
        // 查询总记录数
        $countSql = "SELECT COUNT(*) as total FROM " . $this->table . " w" . $where;
        $countResult = $this->db->once_fetch_array($countSql);
        
        // 返回结果集和总记录数
        return [
            'list' => $result,
            'total' => $countResult['total']
        ];
    }

    public function getWithdrawDetail($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return [];
        }
        $sql = "SELECT w.*, u.username as user_username, u.nickname as user_nickname, u.email as user_email, u.tel as user_tel
                FROM " . $this->table . " w
                LEFT JOIN " . $this->db_prefix . "user u ON w.user_id = u.uid
                WHERE w.id = {$id} LIMIT 1";
        $row = $this->db->once_fetch_array($sql);
        return $this->formatWithdrawRow($row);
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
        $sql = "DELETE FROM " . $this->table . " WHERE id IN ({$idStr}) AND status <> 0";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }



}
