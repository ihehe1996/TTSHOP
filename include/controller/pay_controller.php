<?php
/**
 * @package EMSHOP
 */

class Pay_Controller {

    /**
     * 发起支付
     */
    function index() {
        $out_trade_no = Input::getStrVar('out_trade_no');
        if(empty($out_trade_no)) emMsg('非法请求');

        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $timestamp = time();

        // 判断是否是充值订单（cz开头）
        if(strpos($out_trade_no, 'cz') === 0){
            // 充值订单直接处理支付
            $sql = "select * from {$db_prefix}charge where out_trade_no = '{$out_trade_no}' limit 1";
            $order = $db->once_fetch_array($sql);
            $pay_func = "pay_{$order['pay_plugin']}";
            $pay_func($order, []);
            die('发起支付中');
        }

        $sql = "select * from {$db_prefix}order where out_trade_no = '{$out_trade_no}' limit 1";
        $order = $db->once_fetch_array($sql);
        if(empty($order)) emMsg('非法请求');

        $sql = "select * from {$db_prefix}order_list where order_id={$order['id']}";
        $child_order = $db->fetch_all($sql);

        $pay_func = "pay_{$order['pay_plugin']}";

        if($order['amount'] == 0){
            // 余额支付
            if(ISLOGIN){
                $sql = "SELECT * FROM {$db_prefix}user WHERE uid = " . UID;

                $user = $db->once_fetch_array($sql);
                $order_money = $order['amount'] / 100;
                if($user['money'] < $order_money){
                    emMsg('您的余额不足，请联系客服充值');
                }
                $sql = "update {$db_prefix}user set money = money - {$order_money} where uid=" . UID;
                $db->query($sql);

                $blance_insert = [
                    'user_id' => UID,
                    'plus' => 'n',
                    'money' => $order_money,
                    'update_before' => $user['money'],
                    'description' => '购买商品',
                    'create_time' => $timestamp
                ];
                $db->add('balance_log', $blance_insert);
                $orderModel = new Order_Model();
                // 修改订单的支付状态
                $order_info = $orderModel->getOrderInfo($out_trade_no);
                $order_update = [
                    'pay_status' => 1,
                ];
                $orderModel->updateOrderPayStatus($order_info['id'], $order_update); // 修改订单的支付状态
                // 更新订单的支付时间
                $order_info['payment'] = $order_money == 0 ? '免费商品' : $order_info['payment'];
                $orderModel->updateOrderInfo($out_trade_no, ['pay_time' => time(), 'payment' => $order_info['payment']]);
                // 去发货
                $orderModel->deliver($order_info['id']);
                // 发货完成，订单流程结束。跳转到用户订单页面
                $pay_redirect = Option::get('pay_redirect') ? Option::get('pay_redirect') : 'list';
                if($pay_redirect == 'kami'){
                    $url = EM_URL . "user/order.php?action=detail&out_trade_no={$out_trade_no}";
                }else{
                    $url = EM_URL . 'user/order.php';
                }
                header('location: ' . $url);
                die;

            }else{
                emMsg('未登录用户无法购买免费商品，请先登录~');
            }
        }else{
            $pay_func($order, $child_order);
            die('发起支付中');
        }
        die('支付发起失败，请刷新当前页面');
    }


    /**
     * 同步通知
     */
    public function _return(){
        $orderModel = new Order_Model();

        Log::info('同步回调 - 开始');

        $url = $_SERVER['REQUEST_URI'];
        // 移除查询参数，只保留路径部分
        $path = parse_url($url, PHP_URL_PATH); // 返回：/action/notify/epay_ali
        // 按斜杠分割路径
        $parts = explode('/', trim($path, '/'));
        // 获取第三个部分（索引为2）
        $plugin = $parts[2] ?? '';
        $out_trade_no = empty($parts[3]) ? '' : $parts[3];

        $checkFunc = $plugin . "CheckSign";
        $checkSign = $checkFunc('return');

        if($checkSign){ // 验签通过 - 支付成功

            Log::info('同步回调 - 验签通过');
            // 判断是否是充值订单（cz开头）
            if(strpos($out_trade_no, 'cz') === 0){
                sleep(2); // 睡眠2秒，等待异步回调
                header("location: " . EM_URL . "user/balance.php");die;
            }

            $order_update = [
                'pay_status' => 1,
            ];
            $order = $orderModel->getOrderInfo($checkSign['out_trade_no']);
            if($order['pay_status'] == 1){
                $pay_redirect = Option::get('pay_redirect') ? Option::get('pay_redirect') : 'list';
                if($pay_redirect == 'kami'){
                    $url = EM_URL . "user/order.php?action=detail&out_trade_no={$order['out_trade_no']}";
                }else{
                    if(ISLOGIN){
                        $url = EM_URL . 'user/order.php';
                    }else{
                        $url = EM_URL . 'user/visitors.php';
                    }
                }
                header("location: {$url}");
                die;
            }else{
                $res = $orderModel->updateOrderPayStatus($order['id'], $order_update); // 修改订单的支付状态
                if($res == false){
                    $pay_redirect = Option::get('pay_redirect') ? Option::get('pay_redirect') : 'list';
                    if($pay_redirect == 'kami'){
                        $url = EM_URL . "user/order.php?action=detail&out_trade_no={$order['out_trade_no']}";
                    }else{
                        if(ISLOGIN){
                            $url = EM_URL . 'user/order.php';
                        }else{
                            $url = EM_URL . 'user/visitors.php';
                        }
                    }
                    header("location: {$url}");
                    die;
                }
            }



            // 更新订单的支付时间
            $orderModel->updateOrderInfo($checkSign['out_trade_no'], [
                'pay_time' => $checkSign['timestamp'],
                'up_no' => $checkSign['up_no']
            ]);
            // 去发货
            Log::info('同步发货');
            $orderModel->deliver($order['id']);

            if(ISLOGIN){
                header("location: " . EM_URL . 'user/order.php');
            }else{
                $url = EM_URL . 'user/visitors.php';
                header("location: {$url}");
            }


            die;



        }else{ // 验签失败
            Log::info("同步回调 - 验签失败");
            echo '验签失败';
        }
    }



    /**
     * 异步通知
     */
    public function notify(){

        Log::info('异步回调 - 开始');

        $orderModel = new Order_Model();

        $url = $_SERVER['REQUEST_URI'];
        // 移除查询参数，只保留路径部分
        $path = parse_url($url, PHP_URL_PATH); // 返回：/action/notify/epay_ali
        // 按斜杠分割路径
        $parts = explode('/', trim($path, '/'));
        // 获取支付插件名称
        $plugin = $parts[2] ?? '';
        $checkFunc = $plugin . "CheckSign";
        $checkSign = $checkFunc('notify');

        if($checkSign){ // 验签通过 - 支付成功
            Log::info('异步回调 - 验签通过');
            // 判断是否是充值订单（cz开头）
            if(strpos($checkSign['out_trade_no'], 'cz') === 0){
                try {
                    $chargeModel = new Charge_Model();
                    $order = $chargeModel->getChargeInfo($checkSign['out_trade_no']);
                    if(empty($order)){
                        die('fail');
                    }

                    if($order['pay_status'] == 1){
                        echo 'success'; die; // 重复通知
                    }

                    Log::info("开始更新充值订单支付状态");
                    $res = $chargeModel->updatePayStatus($checkSign['out_trade_no']);
                    if($res == false){
                        echo 'success'; die; // 重复通知
                    }
                    $chargeModel->updateInfo($checkSign['out_trade_no'], [
                        'pay_time' => time(),
                        'trade_no' => $checkSign['trade_no'],
                    ]);

                    // 更新用户余额
                    Log::info("开始更新用户余额，用户ID：{$order['user_id']}, 金额：{$order['amount']}");
                    $balanceModel = new Balance_Model();
                    $balanceModel->inc($order['user_id'], $order['amount'] / 100, '自助充值');
                    Log::info("用户余额更新成功");
                    die('success');

                } catch (Exception $e) {
                    Log::error("充值处理异常：" . $e->getMessage());
                    Log::error("异常文件：" . $e->getFile() . "，异常行号：" . $e->getLine());
                    die('fail');
                }

            }

            $order_info = $orderModel->getOrderInfo($checkSign['out_trade_no']);

            $order_update = [
                'pay_status' => 1,
            ];

            if($order_info['pay_status'] == 1){
                echo 'success'; die; // 重复通知
            }else{
                $res = $orderModel->updateOrderPayStatus($order_info['id'], $order_update); // 修改订单的支付状态
                if($res == false){
                    echo 'success'; die; // 重复通知
                }
            }

            // 更新订单的支付时间
            $orderModel->updateOrderInfo($checkSign['out_trade_no'], [
                'pay_time' => $checkSign['timestamp'],
                'up_no' => $checkSign['up_no']
            ]);
            // 去发货
            Log::info('异步发货');
            $orderModel->deliver($order_info['id']);
            echo 'success'; die;

        }else{ // 验签失败
            Log::info("异步回调 - 验签失败");
            echo '验签失败';
        }

    }

    /**
     * 补单
     */
    public function repay($out_trade_no){
        $orderModel = new Order_Model();
        $order_info = $orderModel->getOrderInfo($out_trade_no);

        $order_update = [
            'pay_status' => 1,
        ];
        // $res = true;
        $res = $orderModel->updateOrderPayStatus($order_info['id'], $order_update); // 修改订单的支付状态
        if(!$res){ // 重复通知
            emMsg('请勿重复补单，该订单状态为已支付！');
        }

        // 去发货
        $res = $orderModel->deliver($order_info['id']);

        Ret::success($res);

    }

    /**
     * 验证订单支付状态
     */
    public function isPay(){
        $out_trade_no = Input::postStrVar('out_trade_no');

        // 判断是否是充值订单（cz开头）
        if(strpos($out_trade_no, 'cz') === 0){
            $chargeModel = new Charge_Model();
            $order = $chargeModel->getChargeInfo($out_trade_no);
            if($order['pay_status'] == 1){
                $url = EM_URL . "user/balance.php";
                die(json_encode([
                    'code' => 200, 'msg' => 'Paid', 'data' => [
                        'is_pay' => true,
                        'url' => $url
                    ]
                ]));
            }else{
                die(json_encode([
                    'code' => 200, 'msg' => 'Unpaid', 'data' => [
                        'is_pay' => false
                    ]
                ]));
            }
        }



        $orderModel = new Order_Model();
        $order_info = $orderModel->getOrderInfo($out_trade_no);
        if($order_info['pay_time']){
            $db = Database::getInstance();
            $sql = "SELECT * FROM `" . DB_PREFIX . "order_list` WHERE `order_id` = {$order_info['id']} LIMIT 1";
            $pay_redirect = Option::get('pay_redirect') ? Option::get('pay_redirect') : 'list';
            if(ISLOGIN){
                $url = EM_URL . 'user/order.php';
            }else{
                $url = EM_URL . 'user/visitors.php';
            }



            die(json_encode([
                'code' => 200, 'msg' => 'Paid', 'data' => [
                    'is_pay' => true,
                    'url' => $url
                ]
            ]));
        }else{
            die(json_encode([
                'code' => 200, 'msg' => 'Unpaid', 'data' => [
                    'is_pay' => false
                ]
            ]));
        }
    }



}
