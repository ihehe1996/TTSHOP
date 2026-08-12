<?php
/**

 */

class Register {

    const EMKEY_LEN = 32;

    public static function isRegLocal() {

        $emkey = getMyEmKey();

        if(empty($emkey) || strlen($emkey) != self::EMKEY_LEN){
            return false;
        }
        return true;

    }

    public static function getRegType() {
        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $host = getTopHost();
        $sql = "select * from {$db_prefix}authorization where domain='{$host}'";
        $res = $db->once_fetch_array($sql);
        if($res){
            return self::verifyEmKey($res['emkey']) ? $res['type'] : 0;
        }else{
            return 0;
        }
    }

    public static function isRegServer() {
        $emkey = getMyEmKey();
        return self::verifyEmKey($emkey);
    }

    public static function doReg($emkey) {
        if (empty($emkey)) {
            return ['code' => 400, 'msg' => '请填写授权码'];
        }

        if (strlen($emkey) !== self::EMKEY_LEN) {
            return ['code' => 400, 'msg' => '该授权码不存在，请检查'];
        }

        $data = [
            'emkey' => $emkey,
            'host' => getTopHost(),
        ];
        $res = emCurl(EM_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=doReg', http_build_query($data), true, [], 6);

        if(empty($res)){
            return ['code' => 400, 'msg' => '官方授权接口请求失败，请更换其他线路或重试'];
        }

        $res = json_decode($res, true);

        if($res['code'] == 400){
            return ['code' => 400, 'msg' => $res['msg']];
        }else{
            return ['code' => 200, 'data' => $res['data']['type']];
        }

        

    }

    public static function verifyEmKey($emkey) {
        if (strlen($emkey) !== self::EMKEY_LEN) {
            return false;
        }

        $data = [
            'emkey' => $emkey,
            'host' => getTopHost(),
        ];
        $res = emCurl(EM_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=verify', http_build_query($data), true, [], 5);

        if(empty($res)){
            return true;
        }

        $res = json_decode($res, true);

        
        if ($res['code'] != 200) {
            self::clean($emkey);
            return false;
        }

        return true;
        
    }

    public static function verifyDownload($plugin_id) {
        $data = [
            'host' => getTopHost(),
            'plugin_id' => $plugin_id,
        ];
        $res = emCurl(EM_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=verifyDownload', http_build_query($data), true, [], 6);

        // var_dump($res);die;

        if(empty($res)){
            return -1; // 网络请求失败
        }
        $res = json_decode($res, true);


        if($res['code'] == 200){
            return 1;
        }else{
            return 2;
        }
    }

    public static function clean($emkey) {
        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $sql = "DELETE FROM `{$db_prefix}authorization` WHERE `emkey` = '{$emkey}'";
        $db->query($sql);

    }

}
