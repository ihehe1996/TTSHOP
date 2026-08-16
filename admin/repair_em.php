<?php

require_once 'globals.php';

/**
 * 修复系统执行文件
 */


if(empty($action)){
    Ret::success('', [
        'repair_table_product_sku' => '检查数据表', // 检查数据表product_sku
        'repair_table_stock' => '检查数据表', // 检查数据表
        'repair_table_stock_usage' => '检查数据表', // 检查数据表
        'repair_table_user_tier' => '检查数据表', // 检查数据表
        'repair_table_attribute_group' => '检查数据表', // 检查数据表
        'repair_table_specification' => '检查数据表', // 检查数据表
        'repair_table_spec_option' => '检查数据表', // 检查数据表

        'repair_stock_table_sku_id' => '检查表字段', // stock - sku_id
        'repair_stock_table_batch_no' => '检查表字段', // stock - batch_no
        'repair_stock_table_max_uses' => '检查表字段', // stock - max_uses
        'repair_stock_table_used_count' => '检查表字段', // stock - used_count
        'repair_stock_table_status' => '检查表字段', // stock - status
        'repair_stock_table_use_time' => '检查表字段', // stock - use_time
        'repair_stock_table_weight' => '检查表字段', // stock - use_time
        'repair_goods_table_min_qty' => '检查表字段', // stock - use_time


        'repair_order_table_remote_trade_no' => '检查表字段', // stock - use_time

        'repair_goods_table_home' => '检查表字段',
        'repair_goods_table_payment' => '检查表字段',


        'repair_user_table_token' => '检查表字段',

        'repair_attribute_group_table_group_name' => '检查表字段', //

        'repair_specification_table_group_id' => '检查表字段', //
        'repair_specification_table_spec_name' => '检查表字段', //

        'repair_spec_option_table_spec_id' => '检查表字段', //
        'repair_spec_option_table_option_name' => '检查表字段', //

        'repair_product_sku_table_id' => '检查表字段', //
        'repair_product_sku_table_option_ids' => '检查表字段', //

        'repair_goods_table_group_id' => '检查表字段', // 检查商品表字段
        'repair_goods_table_config' => '检查表字段', // 检查商品表字段

        'repair_stock_usage_table_content' => '检查表字段', // 检查售出记录表content字段
        'repair_order_table_contact' => '检查表字段', // 检查主订单表contact字段


        'repair_stock_weishou_once' => '检查表数据', // 修复未售库存记录
        'repair_stock_yishou_once' => '检查表数据', // 修复已售库存记录

        'repair_stock_weishou_general' => '检查表数据', // 修复未售库存记录
        'repair_stock_yishou_general' => '检查表数据', // 修复已售库存记录

        'repair_stock_num' => '修复库存数量',
    ]);
}


$db = Database::getInstance();
$db_prefix = DB_PREFIX;
$timestamp = TIMESTAMP;



if($action == 'repair_order_table_remote_trade_no'){
  $isField = isTableField('order_list', 'remote_trade_no');
    if(!$isField){
        createTableField('order_list', 'remote_trade_no', ['type' => 'varchar', 'default' => NULL, 'length' => '32']);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}



if($action == 'repair_goods_table_min_qty'){
    $isField = isTableField('goods', 'min_qty');
    if(!$isField){
        createTableField('goods', 'min_qty', ['type' => 'int', 'default' => 0]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_goods_table_home'){
    $isField = isTableField('goods', 'home');
    if(!$isField){
        createTableField('goods', 'home', ['type' => 'varchar', 'default' => 'y', 'length' => 5]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}
if($action == 'repair_goods_table_payment'){
    $isField = isTableField('goods', 'payment');
    if(!$isField){
        createTableField('goods', 'payment', ['type' => 'varchar', 'default' => NULL, 'length' => 80]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}


if($action == 'repair_user_table_token'){
    $isField = isTableField('user', '_token');
    if(!$isField){
        createTableField('user', '_token', ['type' => 'varchar', 'default' => NULL, 'length' => 50]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}


if($action == 'repair_spec_option_table_spec_id'){
    $isField = isTableField('spec_option', 'spec_id');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}spec_option` CHANGE `attr_id` `spec_id` int(10) NULL DEFAULT NULL";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}
if($action == 'repair_spec_option_table_option_name'){
    $isField = isTableField('spec_option', 'option_name');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}spec_option` CHANGE `name` `option_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_table_spec_option'){
    $isTableExists = isTableExists('spec_option');
    if(!$isTableExists){
        $sql = "RENAME TABLE `{$db_prefix}sku_value` TO `{$db_prefix}spec_option`";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_specification_table_group_id'){
    $isField = isTableField('specification', 'group_id');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}specification` CHANGE `type_id` `group_id` int(10) NULL DEFAULT NULL";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_specification_table_spec_name'){
    $isField = isTableField('specification', 'spec_name');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}specification` CHANGE `title` `spec_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_attribute_group_table_group_name'){
    $isField = isTableField('attribute_group', 'group_name');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}attribute_group` CHANGE `name` `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_table_specification'){
    if(!isTableExists('specification')){
        $sql = "RENAME TABLE `{$db_prefix}sku_attr` TO `{$db_prefix}specification`";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_table_attribute_group'){
    if(!isTableExists('attribute_group')){
        $sql = "RENAME TABLE `{$db_prefix}goods_type` TO `{$db_prefix}attribute_group`";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_table_user_tier'){
    if(!isTableExists('user_tier')){
        $sql = <<<SQL
CREATE TABLE `{$db_prefix}user_tier`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '相对于普通用户价格的折扣率',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;
SQL;
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_product_sku_table_id'){
    $isField = isTableField('product_sku', 'id');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}product_sku` ADD COLUMN `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID' FIRST, ADD PRIMARY KEY (`id`);";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_product_sku_table_option_ids'){
    $isField = isTableField('product_sku', 'option_ids');
    if(!$isField){
        $sql = "ALTER TABLE `{$db_prefix}product_sku` CHANGE COLUMN `sku` `option_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格组合' AFTER `goods_id`;";
        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_table_stock_usage'){
    if(!isTableExists('stock_usage')){
        $sql = <<<SQL
CREATE TABLE `{$db_prefix}stock_usage`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `stock_id` int(10) UNSIGNED NOT NULL COMMENT '库存ID（关联em_stock.id）',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `order_list_id` int(10) NOT NULL COMMENT '子订单ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `create_time` bigint(16) NOT NULL COMMENT '使用时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_stock_id`(`stock_id`) USING BTREE,
  INDEX `idx_order`(`order_id`, `order_list_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1208 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;
SQL;

        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_table_stock'){
    if(!isTableExists('stock')){
        $sql = <<<SQL
CREATE TABLE `{$db_prefix}stock`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `goods_id` int(10) NOT NULL COMMENT '商品ID',
  `sku_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'SKU ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '卡密内容',
  `batch_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '批次号',
  `max_uses` int(10) NOT NULL DEFAULT 1 COMMENT '最大使用次数：1=独立，>1=限次，0=无限',
  `used_count` int(10) NOT NULL DEFAULT 0 COMMENT '已使用次数',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0可用，1已用完，2禁用',
  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '创建时间',
  `use_time` bigint(16) NULL DEFAULT NULL COMMENT '最后使用时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_goods_sku_status`(`goods_id`, `sku_id`, `status`) USING BTREE,
  INDEX `idx_batch`(`batch_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '卡密库存表' ROW_FORMAT = DYNAMIC;
SQL;

        $db->query($sql);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_table_sku_id'){
    $isField = isTableField('stock', 'sku_id');
    if(!$isField){
        createTableField('stock', 'sku_id', ['type' => 'int', 'default' => '0', 'length' => 10]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_num'){
    $sql = "select * from {$db_prefix}goods where type='once' and delete_time is null";
    $goods_list = $db->fetch_all($sql);
    foreach($goods_list as $val){
        $goods_id = $val['id'];
        $sql = "select id from {$db_prefix}product_sku where goods_id = {$goods_id}";
        $product_sku_list = $db->fetch_all($sql);
        if(!empty($product_sku_list)){
            foreach($product_sku_list as $sku_val){
                $sql = "select * from {$db_prefix}stock where status = 0 and goods_id = {$goods_id} and sku_id = {$sku_val['id']}";
                $stock_list = $db->fetch_all($sql);
                $stock_num = 0;
                if(!empty($stock_list)){
                    foreach($stock_list as $stock_val){
                        $stock_num++;
                    }
                }
                $sql = "update {$db_prefix}product_sku set stock = {$stock_num} where id = {$sku_val['id']}";
                $db->query($sql);
            }
        }
    }
    Ret::success('库存数量修复完成', 'complete');
}

if($action == 'repair_stock_table_use_time'){
    $isField = isTableField('stock', 'use_time');
    if(!$isField){
        createTableField('stock', 'use_time', ['type' => 'bigint', 'default' => null, 'length' => 16]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_table_weight'){
    $isField = isTableField('stock', 'weight');
    if(!$isField){
        createTableField('stock', 'weight', ['type' => 'bigint', 'default' => 0, 'length' => 16]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_table_status'){
    $isField = isTableField('stock', 'status');
    if(!$isField){
        createTableField('stock', 'status', ['type' => 'tinyint', 'default' => 0, 'length' => 1]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_table_used_count'){
    $isField = isTableField('stock', 'used_count');
    if(!$isField){
        createTableField('stock', 'used_count', ['type' => 'int', 'default' => 0, 'length' => 10]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_table_max_uses'){
    $isField = isTableField('stock', 'max_uses');
    if(!$isField){
        createTableField('stock', 'max_uses', ['type' => 'int', 'default' => 1, 'length' => 10]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

if($action == 'repair_stock_table_batch_no'){
    $isField = isTableField('stock', 'batch_no');
    if(!$isField){
        createTableField('stock', 'batch_no', ['type' => 'varchar', 'default' => null, 'length' => 50]);
        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}



// 检查数据表product_sku
if($action == 'repair_table_product_sku'){
    if(!isTableExists('product_sku')){

        $sql = "RENAME TABLE `{$db_prefix}skus` TO `{$db_prefix}product_sku`;";

        $db->query($sql);

        Ret::success('数据表修复成功', 'complete');
    }
    Ret::success('数据表正常', 'complete');
}

// 修复已售库存记录
if($action == 'repair_stock_yishou_once'){
    if(!isTableExists('goods_once')){
        Ret::success('数据正常 已售库存记录正常', 'complete');
    }
    $isConfigField = isTableField('goods_once', 'repair_version');
    if(!$isConfigField){
        createTableField('goods_once', 'repair_version', ['type' => 'varchar', 'default' => '0', 'length' => 10]);
    }
    if(TT_URL == 'https://email666.com/'){
        $t = strtotime(date('2026-01-01 00:00:00'));
        $sql = "select * from {$db_prefix}goods_once where sale_time > $t and order_list_id != 0 and repair_version = '0' order by id asc limit 50";
    }else{
        $sql = "select * from {$db_prefix}goods_once where order_list_id != 0 and repair_version = '0' order by id asc limit 50";
    }

    $once = $db->fetch_all($sql);
//    $once = false;
    if(empty($once)){
        Ret::success('数据正常 已售库存记录正常', 'complete');
    }else{
        foreach($once as $val){
            $sku = $val['sku'];
            $goods_id = $val['goods_id'];
            $once_id = $val['id'];
            $use_time = $val['sale_time'];
            $sql = "select * from {$db_prefix}product_sku where option_ids = '{$sku}' and goods_id = {$goods_id} limit 1";
            $product_sku = $db->once_fetch_array($sql);
            if(!empty($product_sku)){
                $sku_id = $product_sku['id'];
                $content = $val['content'];
                $sql = "select * from {$db_prefix}stock where sku_id = {$sku_id} and goods_id = {$goods_id} and content = '{$content}' limit 1";
                $stock = $db->once_fetch_array($sql);
                if(empty($stock)){
                    $create_time = empty($val['create_time']) ? time() : $val['create_time'];
                    $sql = "insert into {$db_prefix}stock (goods_id, sku_id, content, max_uses, used_count, status, create_time, use_time) values ($goods_id, $sku_id, '{$content}', 1, 1, 1, {$create_time}, {$use_time})";
                    $db->query($sql);
                    $stock_id = $db->insert_id();
                }else{
                    $stock_id = $stock['id'];
                    if($stock['sku_id'] != $sku_id || $stock['goods_id'] != $goods_id){
                        $sql = "update {$db_prefix}stock set goods_id = {$goods_id}, sku_id = {$sku_id} where id = {$stock_id}";
                        $db->query($sql);
                    }
                }
                $order_list_id = $val['order_list_id'];
                $sql = "select * from {$db_prefix}order_list where id={$order_list_id} limit 1";
                $order_list = $db->once_fetch_array($sql);
                if(!empty($order_list)){
                    $order_id = $order_list['order_id'];
                    $sql = "select * from {$db_prefix}stock_usage where stock_id={$stock_id} and order_id={$order_id} and order_list_id={$order_list_id} limit 1";
                    $stock_usage = $db->once_fetch_array($sql);
                    if(empty($stock_usage)){
                        $sql = "insert into {$db_prefix}stock_usage (stock_id, order_id, order_list_id, create_time) values ({$stock_id}, {$order_id}, {$order_list_id}, {$use_time})";
                        $db->query($sql);
                    }
                }

            }
            $sql = "update {$db_prefix}goods_once set repair_version = '1' where id = {$once_id}";
            $db->query($sql);
        }
        $sql = "select count(id) once_count from {$db_prefix}goods_once where order_list_id != 0 and repair_version = '0'";
        $once_count_res = $db->once_fetch_array($sql);
        Ret::success('正在修复表数据，剩余数据条数：' . $once_count_res['once_count'], 'continue');
    }
}

// 修复已售库存记录
if($action == 'repair_stock_yishou_general'){
    if(!isTableExists('goods_general')){
        Ret::success('数据正常 已售库存记录正常', 'complete');
    }
    $isConfigField = isTableField('goods_general_sale', 'repair_version');
    if(!$isConfigField){
        createTableField('goods_general_sale', 'repair_version', ['type' => 'varchar', 'default' => '0', 'length' => 10]);
    }
    $sql = "select * from {$db_prefix}goods_general where repair_version = '0' order by id asc limit 200";
    $general = $db->fetch_all($sql);
    if(empty($general)){
        Ret::success('数据正常 已售库存记录正常', 'complete');
    }else{
        foreach($general as $val){
            $sku = $val['sku'];
            $goods_id = $val['goods_id'];
            $general_id = $val['id'];
            $use_time = $val['create_time'];
            $sql = "select * from {$db_prefix}product_sku where option_ids = '{$sku}' and goods_id = {$goods_id} limit 1";
            $product_sku = $db->once_fetch_array($sql);
            if(!empty($product_sku)){
                $sku_id = $product_sku['id'];
                $content = $val['content'];
                $sql = "select * from {$db_prefix}stock where sku_id = {$sku_id} and goods_id = {$goods_id} and content = '{$content}' limit 1";
                $stock = $db->once_fetch_array($sql);
                if(empty($stock)){
                    $create_time = $val['create_time'];
                    $sql = "insert into {$db_prefix}stock (goods_id, sku_id, content, max_uses, used_count, status, create_time, use_time) values ($goods_id, $sku_id, '{$content}', 1, 1, 1, {$create_time}, {$use_time})";
                    $db->query($sql);
                    $stock_id = $db->insert_id();
                }else{
                    $stock_id = $stock['id'];
                    if($stock['sku_id'] != $sku_id || $stock['goods_id'] != $goods_id){
                        $sql = "update {$db_prefix}stock set goods_id = {$goods_id}, sku_id = {$sku_id} where id = {$stock_id}";
                        $db->query($sql);
                    }
                }
                $order_list_id = $val['order_list_id'];
                $sql = "select * from {$db_prefix}order_list where id={$order_list_id} limit 1";
                $order_list = $db->once_fetch_array($sql);
                if(!empty($order_list)){
                    $order_id = $order_list['order_id'];
                    $sql = "select * from {$db_prefix}stock_usage where stock_id={$stock_id} and order_id={$order_id} and order_list_id={$order_list_id} limit 1";
                    $stock_usage = $db->once_fetch_array($sql);
                    if(empty($stock_usage)){
                        $sql = "insert into {$db_prefix}stock_usage (stock_id, order_id, order_list_id, create_time) values ({$stock_id}, {$order_id}, {$order_list_id}, {$use_time})";
                        $db->query($sql);
                    }
                }
            }
            $sql = "update {$db_prefix}goods_general_sale set repair_version = '1' where id = {$general_id}";
            $db->query($sql);
        }
        Ret::success('正在修复表数据', 'continue');
    }
}

// 修复未售库存记录
if($action == 'repair_stock_weishou_once'){
    if(!isTableExists('goods_once')){
        Ret::success('数据正常 已售库存记录正常', 'complete');
    }
    $isConfigField = isTableField('goods_once', 'repair_version');
    if(!$isConfigField){
        createTableField('goods_once', 'repair_version', ['type' => 'varchar', 'default' => '0', 'length' => 10]);
    }

    // 检查索引是否存在
    $sql = "SHOW INDEX FROM `{$db_prefix}goods_once` WHERE Key_name = 'idx_repair_order'";
    $result = $db->query($sql);

    if($db->num_rows($result) == 0) {
        // 索引不存在，创建索引
        $sql = "ALTER TABLE `{$db_prefix}goods_once` ADD INDEX `idx_repair_order` (`repair_version`, `order_list_id`)";
        $db->query($sql);
    }



    // 检查索引是否存在
    $sql = "SHOW INDEX FROM `{$db_prefix}stock_usage` WHERE Key_name = 'idx_stock_usage_stock_order_list'";
    $result = $db->query($sql);

    if($db->num_rows($result) == 0) {
        // 索引不存在，创建索引
        $sql = "ALTER TABLE `{$db_prefix}stock_usage` ADD INDEX `idx_stock_usage_stock_order_list` (`stock_id`, `order_id`, `order_list_id`)";
        $db->query($sql);
    }






    $sql = "select * from {$db_prefix}goods_once where order_list_id = 0 and repair_version = '0' order by id asc limit 200";
    $once = $db->fetch_all($sql);
//    d($once);die;
    if(empty($once)){

        Ret::success('数据正常 未售库存记录正常', 'complete');
    }else{

        foreach($once as $val){
            $sku = $val['sku'];
            $goods_id = $val['goods_id'];
            $once_id = $val['id'];
            $sql = "select * from {$db_prefix}product_sku where option_ids = '{$sku}' and goods_id = {$goods_id} limit 1";
            $product_sku = $db->once_fetch_array($sql);
            if(!empty($product_sku)){
                $sku_id = $product_sku['id'];
                $content = $val['content'];
                $sql = "select * from {$db_prefix}stock where sku_id = {$sku_id} and goods_id = {$goods_id} and content = '{$content}' limit 1";
                $stock = $db->once_fetch_array($sql);
                if(empty($stock)){
                    $create_time = $val['create_time'];
                    $sql = "insert into {$db_prefix}stock (goods_id, sku_id, content, max_uses, used_count, status, create_time) values ($goods_id, $sku_id, '{$content}', 1, 0, 0, {$create_time})";
                    $db->query($sql);
                }else{
                    if(empty($stock['use_time']) && ($stock['sku_id'] != $sku_id || $stock['goods_id'] != $goods_id)){
                        $stock_id = $stock['id'];
                        $sql = "update {$db_prefix}stock set goods_id = {$goods_id}, sku_id = {$sku_id} where id = {$stock_id}";
                        $db->query($sql);
                    }
                }
            }
            $sql = "update {$db_prefix}goods_once set repair_version = '1' where id = {$once_id}";
            $db->query($sql);
        }
        Ret::success('正在修复表数据', 'continue');
    }

}

// 修复未售库存记录
if($action == 'repair_stock_weishou_general'){
    if(!isTableExists('goods_general')){
        Ret::success('数据正常 已售库存记录正常', 'complete');
    }
    $isConfigField = isTableField('goods_general', 'repair_version');
    if(!$isConfigField){
        createTableField('goods_general', 'repair_version', ['type' => 'varchar', 'default' => '0', 'length' => 10]);
    }
    $sql = "select * from {$db_prefix}goods_general where repair_version < '3' order by id asc limit 200";
    $general = $db->fetch_all($sql);
//    d($once);die;

    if(empty($general)){

        Ret::success('数据正常 未售库存记录正常', 'complete');
    }else{

        foreach($general as $val){
            $sku = $val['sku'];
            $goods_id = $val['goods_id'];
            $general_id = $val['id'];
            $sql = "select * from {$db_prefix}product_sku where option_ids = '{$sku}' and goods_id = {$goods_id} limit 1";
            $product_sku = $db->once_fetch_array($sql);
//            if(TT_URL == 'https://70zh.com/'){
//                d($product_sku);die;
//            }
            if(!empty($product_sku)){
                $stock_num = $product_sku['stock'];
                $sku_id = $product_sku['id'];
                $content = $val['content'];
                $sql = "select * from {$db_prefix}stock where sku_id = {$sku_id} and goods_id = {$goods_id} and content = '{$content}' limit 1";
                $stock = $db->once_fetch_array($sql);
                if(empty($stock)){
                    $create_time = $val['create_time'];
                    $sql = "insert into {$db_prefix}stock (goods_id, sku_id, content, max_uses, used_count, status, create_time) values ($goods_id, $sku_id, '{$content}', {$stock_num}, 0, 0, {$create_time})";
                    $db->query($sql);
                }else{
                    if($stock['sku_id'] != $sku_id || $stock['goods_id'] != $goods_id){
                        $stock_id = $stock['id'];
                        $sql = "update {$db_prefix}stock set max_uses = {$stock_num}, goods_id = {$goods_id}, sku_id = {$sku_id} where id = {$stock_id}";
                        $db->query($sql);
                    }
                }
            }
            $sql = "update {$db_prefix}goods_general set repair_version = '3' where id = {$general_id}";
            $db->query($sql);
        }
        Ret::success('正在修复表数据', 'continue');
    }

}

// 检查主订单表contact字段
if($action == 'repair_order_table_contact'){
    $isConfigField = isTableField('order', 'contact');
    if(!$isConfigField){
        createTableField('order', 'contact', ['type' => 'varchar', 'default' => '', 'length' => 50]);
        Ret::success('修复错误！主订单表contact字段', 'complete');
    }
    Ret::success('字段正常 主订单表contact字段', 'complete');
}

// 检查库存表是否存在content字段
if($action == 'repair_stock_usage_table_content'){
    $isConfigField = isTableField('stock_usage', 'content');
    if(!$isConfigField){
        createTableField('stock_usage', 'content', ['type' => 'text']);
        Ret::success('修复错误！售出记录表content字段', 'complete');
    }
    Ret::success('字段正常 售出记录表content', 'complete');
}



if($action == 'repair_stock'){
    if(TIMESTAMP % 2 == 0){
        Ret::success('修复库存...', 'continue');
    }else{
        Ret::success('修复库存...', 'complete');
    }
}

if($action == 'repair_sales'){
    if(TIMESTAMP % 2 == 0){
        Ret::success('修复销量...', 'continue');
    }else{
        Ret::success('修复销量...', 'complete');
    }
}

/**
 * 创建表字段
 */
function createTableField($table, $field, $config) {
    global $db, $db_prefix;

    $tableName = $db_prefix . $table;

    // 构建字段类型
    $fieldType = $config['type'];
    if (!empty($config['length'])) {
        $fieldType .= '(' . $config['length'] . ')';
    }
    // 构建SQL
    $sql = "ALTER TABLE `{$tableName}` ADD `{$field}` {$fieldType}";
    // 添加默认值
    if (isset($config['default'])) {
        $default = $config['default'];
        if (is_string($default)) {
            $default = str_replace("'", "''", $default);
            $sql .= " DEFAULT '{$default}'";
        } else {
            $sql .= " DEFAULT {$default}";
        }
    }
    return $db->query($sql);
}

/**
 * 判断表是否存在
 */
function isTableExists($table) {
    global $db, $db_prefix;

    $tableName = $db_prefix . $table;

    // 使用SHOW TABLES查询
    $sql = "SHOW TABLES LIKE '{$tableName}'";
    $result = $db->query($sql);

    // 判断查询结果
    return $result && $result->num_rows > 0;
}

/**
 * 判断表字段是否存在
 */
function isTableField($table, $field){
    global $db, $db_prefix;
    $tableName = $db_prefix . $table;
    $sql = "SHOW COLUMNS FROM {$tableName};";




    $fieldAll = $db->fetch_all($sql);

    $result = false;
    foreach($fieldAll as $val){
        if($val['Field'] == $field){
            $result = true;
        }
    }
    return $result;
}

// 检查商品表是否存在config字段
if($action == 'repair_goods_table_config'){
    $isConfigField = isTableField('goods', 'config');
    if(!$isConfigField){
        createTableField('goods', 'config', ['type' => 'text']);
        Ret::success('修复错误！商品表config字段', 'complete');
    }
    Ret::success('字段正常 商品表config', 'complete');
}
if($action == 'repair_goods_table_group_id'){
    $isConfigField = isTableField('goods', 'group_id');
    if(!$isConfigField){
        $sql = "ALTER TABLE `{$db_prefix}goods` CHANGE `attr_id` `group_id` int(10) NULL DEFAULT NULL";
        $db->query($sql);
        Ret::success('修复错误！商品表group_id字段', 'complete');
    }
    Ret::success('字段正常 商品表group_id', 'complete');
}