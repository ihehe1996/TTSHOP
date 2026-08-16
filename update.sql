# version 1.2.72
ALTER TABLE `{PREFIX}authorization` CHANGE `emkey` `ttkey` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
UPDATE `{PREFIX}options` SET `option_name` = 'tt_line' WHERE `option_name` = 'em_line';

# version 1.2.68
ALTER TABLE `{db_prefix}order` ADD COLUMN `pwd` varchar(50) NULL COMMENT '订单密码';
ALTER TABLE `{db_prefix}order` ADD COLUMN `em_local` varchar(38) NULL COMMENT '客户端标记';

# version 1.2.57
INSERT INTO `{db_prefix}options` (`option_name`, `option_value`) VALUES ('coupon_switch', 'y');
ALTER TABLE `{db_prefix}charge` ADD COLUMN `payment` varchar(30) NULL;

# version 1.2.55
ALTER TABLE `{db_prefix}order`
  ADD COLUMN `coupon_id` int(10) NULL DEFAULT 0 COMMENT '优惠券ID',
  ADD COLUMN `coupon_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '优惠券码',
  ADD COLUMN `origin_amount` int(10) NULL DEFAULT NULL COMMENT '优惠前金额(分)',
  ADD COLUMN `coupon_amount` int(10) NULL DEFAULT 0 COMMENT '优惠金额(分)';
CREATE TABLE `{db_prefix}coupon_usage` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `coupon_id` int(10) UNSIGNED NOT NULL COMMENT '优惠券ID',
  `coupon_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '优惠券码',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID(游客=0)',
  `order_id` int(10) UNSIGNED NOT NULL COMMENT '订单ID',
  `order_list_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '子订单ID',
  `amount_before` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠前金额(分)',
  `discount_amount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠金额(分)',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0预占 1已用 2回滚',
  `create_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_coupon_order` (`coupon_id`, `order_id`),
  KEY `idx_coupon_id` (`coupon_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_code` (`coupon_code`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `{db_prefix}coupon` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券ID',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT '优惠券类型',
  `category_id` int(10) NOT NULL DEFAULT 0 COMMENT '分类ID',
  `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `threshold_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none' COMMENT '门槛类型',
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '最低消费金额',
  `discount_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amount' COMMENT '优惠方式',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '优惠数值',
  `end_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `use_limit` int(10) NOT NULL DEFAULT 1 COMMENT '可用次数(0为不限)',
  `prefix` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '券码前缀',
  `code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '券码',
  `used_times` int(10) NOT NULL DEFAULT 0 COMMENT '已使用次数',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `create_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(16) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_code` (`code`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_type` (`type`) USING BTREE,
  KEY `idx_category` (`category_id`) USING BTREE,
  KEY `idx_goods` (`goods_id`) USING BTREE,
  KEY `idx_end_time` (`end_time`) USING BTREE
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


# version 1.2.52
ALTER TABLE {db_prefix}goods ADD COLUMN min_qty INT UNSIGNED NOT NULL DEFAULT 1 COMMENT '最小购买数量', ADD COLUMN max_qty INT UNSIGNED NULL COMMENT '最大购买数量';

# version 1.2.35
ALTER TABLE `{db_prefix}station` ADD COLUMN `money` decimal(10, 2) NULL DEFAULT 0;
ALTER TABLE `{db_prefix}station` ADD COLUMN `goods_premium` decimal(10, 2) NULL DEFAULT 0.1;
CREATE TABLE `{db_prefix}station_bill`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NULL DEFAULT NULL COMMENT '用户ID',
  `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
  `money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '更新金额',
  `_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '更新前的金额',
  `money_` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '更新后的金额',
  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '账单时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

# version 1.2.31
ALTER TABLE `{db_prefix}goods` ADD COLUMN `payment` varchar(80) NULL;
ALTER TABLE {db_prefix}product_sku ADD INDEX idx_goods_price (goods_id, guest_price);
ALTER TABLE {db_prefix}product_sku ADD INDEX idx_goods_price_id (goods_id, guest_price, id);

# version 1.2.30
ALTER TABLE `{db_prefix}goods` ADD COLUMN `home` varchar(5) NOT NULL DEFAULT 'y' COMMENT '首页展示';

# version 1.1.97
ALTER TABLE `{db_prefix}stock` ADD COLUMN `weight` bigint(16) NULL DEFAULT 0 COMMENT '权重';

# version 1.1.88
ALTER TABLE `{db_prefix}stock_usage` ADD COLUMN `content` text NULL AFTER `order_list_id`;

# version 1.1.81
ALTER TABLE `{db_prefix}stock_usage` ADD COLUMN `content` text NULL AFTER `order_list_id`;

# version 1.1.80
UPDATE {db_prefix}goods SET `type` = CASE WHEN `type` = 'ycy' THEN 'ycy_auto' WHEN `type` = 'ycy_hand' THEN 'ycy_manual' END WHERE `type` IN ('ycy', 'ycy_hand');

# version 1.1.79
RENAME TABLE `{db_prefix}goods_type` TO `{db_prefix}attribute_group`;
ALTER TABLE `{db_prefix}attribute_group` CHANGE `name` `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
RENAME TABLE `{db_prefix}sku_attr` TO `{db_prefix}specification`;
ALTER TABLE `{db_prefix}specification` CHANGE `type_id` `group_id` int(10) NULL DEFAULT NULL, CHANGE `title` `spec_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
RENAME TABLE `{db_prefix}sku_value` TO `{db_prefix}spec_option`;
ALTER TABLE `{db_prefix}spec_option` CHANGE `attr_id` `spec_id` int(10) NULL DEFAULT NULL, CHANGE `name` `option_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
RENAME TABLE `{db_prefix}skus` TO `{db_prefix}product_sku`;
ALTER TABLE `{db_prefix}product_sku` CHANGE `sku` `option_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格组合，单规格为0，多规格为spec_option.id组合如1-3';
ALTER TABLE `{db_prefix}product_sku` ADD COLUMN `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
RENAME TABLE `{db_prefix}member` TO `{db_prefix}user_tier`;
ALTER TABLE `{db_prefix}user_tier` CHANGE `name` `tier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
RENAME TABLE `{db_prefix}member_price` TO `{db_prefix}tier_price`;
ALTER TABLE `{db_prefix}tier_price` CHANGE `sku` `option_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0', CHANGE `member_level` `tier_id` tinyint(3) UNSIGNED NOT NULL;
ALTER TABLE `{db_prefix}tier_price` ADD COLUMN `sku_id` int(10) UNSIGNED NULL AFTER `goods_id`;
UPDATE `{db_prefix}tier_price` tp INNER JOIN `{db_prefix}product_sku` ps ON tp.goods_id = ps.goods_id AND tp.option_ids = ps.option_ids SET tp.sku_id = ps.id;
ALTER TABLE `{db_prefix}tier_price` DROP COLUMN `goods_id`, DROP COLUMN `option_ids`;
RENAME TABLE `{db_prefix}discount` TO `{db_prefix}quantity_pricing`;
ALTER TABLE `{db_prefix}quantity_pricing` CHANGE `amount` `discount_per_item` decimal(10,2) NULL DEFAULT NULL COMMENT '每件优惠金额(元)';
ALTER TABLE `{db_prefix}goods` CHANGE `attr_id` `group_id` int(10) NULL DEFAULT NULL;
DROP TABLE IF EXISTS `{db_prefix}stock`;
CREATE TABLE `{db_prefix}stock` (
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
      INDEX `idx_goods_sku_status` (`goods_id`, `sku_id`, `status`) USING BTREE,
      INDEX `idx_batch` (`batch_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='卡密库存表';
CREATE TABLE `{db_prefix}stock_usage` (
      `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
      `stock_id` int(10) UNSIGNED NOT NULL COMMENT '库存ID（关联em_stock.id）',
      `order_id` int(10) NOT NULL COMMENT '订单ID',
      `order_list_id` int(10) NOT NULL COMMENT '子订单ID',
      `create_time` bigint(16) NOT NULL COMMENT '使用时间',
      PRIMARY KEY (`id`) USING BTREE,
      INDEX `idx_stock_id` (`stock_id`) USING BTREE,
      INDEX `idx_order` (`order_id`, `order_list_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='卡密使用记录表';

ALTER TABLE `{db_prefix}order_list` ADD COLUMN `remote_trade_no` varchar(32) NULL AFTER `order_id`;
ALTER TABLE `{db_prefix}goods` ADD COLUMN `config` text NULL AFTER `title`;
ALTER TABLE `{db_prefix}order` CHANGE COLUMN `pwd` `contact` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `status`;
INSERT INTO {db_prefix}stock (goods_id,content,batch_no,create_time,sku_id,max_uses,used_count,status) SELECT ego.goods_id,ego.content,ego.batch_no,ego.create_time,COALESCE((SELECT ps.id FROM {db_prefix}product_sku ps WHERE ps.goods_id = ego.goods_id AND TRIM(ps.option_ids) = TRIM(ego.sku) LIMIT 1),0),1,0,0 FROM {db_prefix}goods_once ego WHERE ego.order_list_id = 0 GROUP BY ego.goods_id,ego.batch_no,ego.sku;



# version 1.1.77
ALTER TABLE `{db_prefix}goods` MODIFY COLUMN `title` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章标题' AFTER `is_sku`;

# version 1.1.75
UPDATE `{db_prefix}goods` SET group_id = -1 WHERE type IN ('ycy', 'ycy_hand', 'em_auto', 'em_manual');
ALTER TABLE `{db_prefix}user_tier` ADD COLUMN `discount` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '相对于普通用户价格的折扣率' AFTER `tier_name`;

# version 1.1.70
ALTER TABLE `{db_prefix}user` ADD COLUMN `_token` varchar(32) NULL AFTER `state`;
ALTER TABLE `{db_prefix}goods` DROP COLUMN `sales`;
ALTER TABLE `{db_prefix}order` ADD COLUMN `remote_trade` varchar(30) NULL AFTER `out_trade_no`;

# version 1.1.66
CREATE TABLE `{db_prefix}charge`  (
                                      `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                      `user_id` int(10) NULL DEFAULT NULL,
                                      `out_trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                      `trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                      `amount` int(10) NULL DEFAULT NULL,
                                      `pay_plugin` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                      `create_time` bigint(16) NULL DEFAULT NULL,
                                      `expire_time` bigint(16) NULL DEFAULT NULL,
                                      `pay_time` bigint(16) NULL DEFAULT NULL,
                                      `pay_status` tinyint(1) NULL DEFAULT 0,
                                      PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

# version 1.1.30
ALTER TABLE `{db_prefix}goods` ADD COLUMN `station_id` int(10) NOT NULL DEFAULT 0 COMMENT '分站ID' AFTER `id`;
ALTER TABLE `{db_prefix}sort` ADD COLUMN `station_id` int(10) NOT NULL DEFAULT 0 COMMENT '分站ID' AFTER `page_count`;
ALTER TABLE `{db_prefix}order` ADD COLUMN `station_id` int(10) NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE `{db_prefix}user` ADD COLUMN `station_id` int(10) NOT NULL DEFAULT 0 AFTER `uid`;
CREATE TABLE `{db_prefix}station`  (
                                       `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                       `user_id` int(10) NULL DEFAULT NULL COMMENT '用户ID',
                                       `pid` int(10) NULL DEFAULT NULL COMMENT '上级站点ID',
                                       `level_id` int(10) NULL DEFAULT NULL COMMENT '分站等级ID',
                                       `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '开通价格',
                                       `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '独立域名',
                                       `domain_2` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '二级域名',
                                       `create_time` bigint(16) NULL DEFAULT NULL COMMENT '开通时间',
                                       `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                       `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                       `master_sort` tinyint(1) NULL DEFAULT NULL,
                                       `master_goods` tinyint(1) NULL DEFAULT NULL,
                                       `domain_2_prefix` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                       `domain_2_suffix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                       `roll_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
                                       `home_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
                                       `delete_time` bigint(16) NULL DEFAULT NULL,
                                       `station_unique` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                       PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分站列表' ROW_FORMAT = Dynamic;
CREATE TABLE `{db_prefix}station_goods`  (
                                             `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                             `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
                                             `goods_id` int(10) NULL DEFAULT NULL COMMENT '商品ID',
                                             `premium` decimal(10, 2) NULL DEFAULT NULL COMMENT '加价百分比',
                                             `is_show` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'y' COMMENT '是否显示',
                                             `custom_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '自定义商品名',
                                             PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分站商品控制表' ROW_FORMAT = Dynamic;
CREATE TABLE `{db_prefix}station_level`  (
                                             `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                             `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站等级名称',
                                             `price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '分站开通价格',
                                             `is_station` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站开通权限',
                                             `is_domain` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站独立域名',
                                             `is_goods` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分站供货权限',
                                             `service_change` decimal(10, 2) NULL DEFAULT NULL COMMENT '分站供货手续费',
                                             `cash_change` decimal(10, 2) NULL DEFAULT NULL COMMENT '提现手续费',
                                             `using` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '是否启用该分站等级',
                                             `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
                                             `update_time` bigint(16) NULL DEFAULT NULL COMMENT '编辑时间',
                                             PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分站等级' ROW_FORMAT = Dynamic;
CREATE TABLE `{db_prefix}station_plugin`  (
                                              `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
                                              `plugin_id` int(11) NULL DEFAULT NULL,
                                              `plugin_name_cn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                              `plugin_name_en` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                              `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                              `pc_switch` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                              `tel_switch` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
CREATE TABLE `{db_prefix}station_sort`  (
                                            `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                            `station_id` int(10) NULL DEFAULT NULL COMMENT '分站ID',
                                            `sort_id` int(10) NULL DEFAULT NULL COMMENT '分类ID',
                                            `custom_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '自定义分类名称',
                                            `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分类类型',
                                            `is_show` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '是否显示',
                                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分站分类控制表' ROW_FORMAT = Dynamic;
CREATE TABLE `{db_prefix}station_storage`  (
                                               `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                               `station_id` int(10) NOT NULL COMMENT '分站ID',
                                               `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'tpl or plugin',
                                               `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                               `option_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                               `option_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
                                               PRIMARY KEY (`id`) USING BTREE,
                                               UNIQUE INDEX `tpl`(`station_id`, `type`, `plugin_name`, `option_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分站插件模板配置表' ROW_FORMAT = Dynamic;
CREATE TABLE `{db_prefix}withdraw`  (
                                        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                        `user_id` int(10) NULL DEFAULT NULL,
                                        `amount` decimal(10, 2) NULL DEFAULT NULL,
                                        `method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                        `account` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                        `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                        `remark` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                        `status` tinyint(1) NULL DEFAULT NULL,
                                        `create_time` bigint(16) NULL DEFAULT NULL,
                                        `service_change` decimal(10, 2) NULL DEFAULT NULL,
                                        `finish_time` bigint(16) NULL DEFAULT NULL,
                                        `reject_time` bigint(16) NULL DEFAULT NULL,
                                        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '提现记录表' ROW_FORMAT = Dynamic;

# version 1.1.27
ALTER TABLE `{db_prefix}quantity_pricing` MODIFY COLUMN `discount_per_item` decimal(10, 2) NULL DEFAULT NULL AFTER `quantity`;

# version 1.1.16
CREATE TABLE `{db_prefix}balance_log`  (
                                           `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                           `user_id` int(10) NULL DEFAULT NULL,
                                           `plus` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                           `update_before` decimal(10, 2) NULL DEFAULT NULL,
                                           `money` decimal(10, 2) NULL DEFAULT 0.00,
                                           `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                           `create_time` bigint(16) NULL DEFAULT NULL,
                                           PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

# version 1.1.13
INSERT INTO `{db_prefix}options` (`option_name`, `option_value`) VALUES ('logo', '0');

# version 1.1.09
ALTER TABLE `{db_prefix}attribute_group` ADD COLUMN `hide` varchar(10) NOT NULL DEFAULT 'n' AFTER `delete_time`;

# version 1.1.07
ALTER TABLE `{db_prefix}order_list` ADD COLUMN `cost_price` int(10) NULL DEFAULT 0 AFTER `status`;

# version 1.1.01
START TRANSACTION;
DELETE FROM `{db_prefix}goods` WHERE type = 'post';
CREATE TABLE `{db_prefix}goods_general`  (
                                             `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                             `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
                                             `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格',
                                             `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '卡密',
                                             `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
                                             `update_time` bigint(16) NULL DEFAULT NULL COMMENT '修改时间',
                                             PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
CREATE TABLE `{db_prefix}goods_general_sale`  (
                                                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                                  `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
                                                  `order_list_id` int(10) NOT NULL DEFAULT 0 COMMENT '子订单ID',
                                                  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格',
                                                  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '卡密',
                                                  `num` int(10) NOT NULL DEFAULT 0 COMMENT '购买数量',
                                                  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
                                                  `update_time` bigint(16) NULL DEFAULT NULL COMMENT '编辑时间',
                                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
CREATE TABLE `{db_prefix}goods_once`  (
                                          `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                          `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
                                          `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格',
                                          `batch_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '批次号',
                                          `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '卡密',
                                          `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
                                          `update_time` bigint(16) NULL DEFAULT NULL COMMENT '修改时间',
                                          `sale_time` bigint(16) NULL DEFAULT NULL COMMENT '出售时间',
                                          `order_list_id` int(10) NOT NULL DEFAULT 0 COMMENT '子订单ID',
                                          PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
CREATE TABLE `{db_prefix}goods_service`  (
                                             `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                             `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
                                             `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                                             `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格',
                                             `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
                                             `update_time` bigint(16) NULL DEFAULT NULL COMMENT '修改时间',
                                             PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
CREATE TABLE `{db_prefix}goods_service_sale`  (
                                                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                                  `goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '商品ID',
                                                  `order_list_id` int(10) NOT NULL DEFAULT 0 COMMENT '子订单ID',
                                                  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '规格',
                                                  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '卡密',
                                                  `num` int(10) NOT NULL DEFAULT 0 COMMENT '购买数量',
                                                  `is_default` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'y',
                                                  `create_time` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
                                                  `update_time` bigint(16) NULL DEFAULT NULL COMMENT '编辑时间',
                                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
INSERT INTO {db_prefix}goods_once (goods_id, sku, content, create_time) SELECT s.goods_id, s.sku, s.content, s.create_time FROM {db_prefix}stock s INNER JOIN {db_prefix}goods g ON s.goods_id = g.id WHERE g.type = 'duli';
INSERT INTO {db_prefix}goods_once ( goods_id, sku, order_list_id, content, sale_time ) SELECT ol.goods_id, ol.sku, d.order_list_id, d.content, d.create_time FROM {db_prefix}deliver d INNER JOIN {db_prefix}order_list ol ON d.order_list_id = ol.id INNER JOIN {db_prefix}goods g ON ol.goods_id = g.id WHERE g.type = 'duli';
INSERT INTO {db_prefix}goods_general (goods_id, sku, content, create_time) SELECT s.goods_id, s.sku, s.content, s.create_time FROM {db_prefix}stock s INNER JOIN {db_prefix}goods g ON s.goods_id = g.id WHERE g.type = 'guding';
INSERT INTO {db_prefix}goods_general_sale ( goods_id, sku, order_list_id, content, create_time, num ) SELECT ol.goods_id, ol.sku, d.order_list_id, d.content, d.create_time, ol.quantity FROM {db_prefix}deliver d INNER JOIN {db_prefix}order_list ol ON d.order_list_id = ol.id INNER JOIN {db_prefix}goods g ON ol.goods_id = g.id WHERE g.type = 'guding';
INSERT INTO {db_prefix}goods_service (goods_id, sku, create_time, content) SELECT s.goods_id, s.sku, s.create_time, '已收到您的订单，客服将尽快为您处理！' AS content FROM {db_prefix}stock s INNER JOIN {db_prefix}goods g ON s.goods_id = g.id WHERE g.type = 'xuni';
INSERT INTO {db_prefix}goods_service_sale ( goods_id, order_list_id, sku, num, content, is_default ) SELECT ol.goods_id, ol.id, ol.sku, ol.quantity, o.device, 'n' AS is_default FROM {db_prefix}order o INNER JOIN {db_prefix}order_list ol ON o.id = ol.order_id INNER JOIN {db_prefix}goods g ON ol.goods_id = g.id WHERE g.type = 'xuni';
UPDATE {db_prefix}goods SET type = CASE WHEN type = 'duli' THEN 'once' WHEN type = 'guding' THEN 'general' WHEN type = 'xuni' THEN 'service' ELSE type END;
UPDATE {db_prefix}options SET option_value = CONCAT( 'a:', (SUBSTRING_INDEX(SUBSTRING_INDEX(option_value, ':', 2), ':', -1) + 3), ':{', TRIM(BOTH ';' FROM TRIM(TRAILING '}' FROM SUBSTRING(option_value, LOCATE('{', option_value) + 1))), ';i:', SUBSTRING_INDEX(SUBSTRING_INDEX(option_value, ':', 2), ':', -1), ';s:25:"goods_once/goods_once.php"', ';i:', (SUBSTRING_INDEX(SUBSTRING_INDEX(option_value, ':', 2), ':', -1) + 1), ';s:31:"goods_general/goods_general.php"', ';i:', (SUBSTRING_INDEX(SUBSTRING_INDEX(option_value, ':', 2), ':', -1) + 2), ';s:31:"goods_service/goods_service.php"', ';}' ) WHERE option_name = 'active_plugins';
COMMIT;