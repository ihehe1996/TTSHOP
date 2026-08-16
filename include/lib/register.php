<?php
/**

 */

class Register {

    const TTKEY_LEN = 32;

    public static function isRegLocal() {

        $ttkey = getMyTtKey();

        if(empty($ttkey) || strlen($ttkey) != self::TTKEY_LEN){
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
            return self::verifyTtKey($res['ttkey']) ? $res['type'] : 0;
        }else{
            return 0;
        }
    }

    public static function isRegServer() {
        $ttkey = getMyTtKey();
        return self::verifyTtKey($ttkey);
    }

    public static function doReg($ttkey) {
        if (empty($ttkey)) {
            return ['code' => 400, 'msg' => '请填写授权码'];
        }

        if (strlen($ttkey) !== self::TTKEY_LEN) {
            return ['code' => 400, 'msg' => '该授权码不存在，请检查'];
        }

        $data = [
            'ttkey' => $ttkey,
            'host' => getTopHost(),
        ];
        $res = ttCurl(TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=doReg', http_build_query($data), true, [], 6);

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

    public static function verifyTtKey($ttkey) {
        if (strlen($ttkey) !== self::TTKEY_LEN) {
            return false;
        }

        $data = [
            'ttkey' => $ttkey,
            'host' => getTopHost(),
        ];
        $res = ttCurl(TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=verify', http_build_query($data), true, [], 5);

        if(empty($res)){
            return true;
        }

        $res = json_decode($res, true);

        
        if ($res['code'] != 200) {
            self::clean($ttkey);
            return false;
        }

        return true;
        
    }

    public static function verifyDownload($plugin_id) {
        $data = [
            'host' => getTopHost(),
            'plugin_id' => $plugin_id,
        ];
        $res = ttCurl(TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=verifyDownload', http_build_query($data), true, [], 6);

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

    public static function clean($ttkey) {
        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $sql = "DELETE FROM `{$db_prefix}authorization` WHERE `ttkey` = '{$ttkey}'";
        $db->query($sql);

    }

}
