-- ================================================================
--  TTSHOP 数据库迁移脚本：emkey / em_line 字段改名同步
-- ----------------------------------------------------------------
--  适用场景：代码已从 emshop 改名为 ttshop（emkey→ttkey、
--  em_line→tt_line），已上线站点的数据库需要同步这两处改名。
--
--  使用方法：
--    1. 把下方 {PREFIX} 全部替换成你站点的真实表前缀
--       （注意保留末尾下划线，例如 em_、tt_、shop_ 等）
--    2. 执行前务必先备份数据库
--    3. 建议在站点停止写入时执行
-- ================================================================

-- ① authorization 表的授权密钥字段 emkey → ttkey
ALTER TABLE `{PREFIX}authorization`
  CHANGE `emkey` `ttkey` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

-- ② options 表的线路选项键 em_line → tt_line
UPDATE `{PREFIX}options`
  SET `option_name` = 'tt_line'
  WHERE `option_name` = 'em_line';

-- ----------------------------------------------------------------
--  说明：
--  - 表前缀（DB_PREFIX）本身如果还是 em_，属于数据层命名，
--    本脚本不自动重命名所有表；如需一并改成 tt_，请自行
--    RENAME 所有表并同步 config.php 的 DB_PREFIX 常量。
--  - 其余保留的 em 相关数据库项（order 表的 em_local 字段、
--    goods.type 的 em_auto / em_manual 枚举值、em_stock 表名等）
--    本次未改名，属有意保留，避免不必要的结构迁移。
-- ================================================================
