<?php defined('TT_ROOT') || exit('access denied!'); ?>

<div class="layui-panel">
    <div style="padding: 20px;">
        <form action="?action=setting_save" method="post" name="setting_form" id="setting_form" class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">主站域名配置</label>
                <div class="layui-input-block">
                    <textarea class="layui-textarea" name="station_domain" placeholder="多个域名请使用回车换行"><?= $station_domain ?></textarea>
                    <span class="form-tips">分站配置二级域名时选择的主域名</span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">分站系统开关</label>
                <div class="layui-input-block">
                    <input type="radio" name="station_switch" value="y" title="开启" <?= $station_switch !== 'n' ? 'checked' : '' ?>>
                    <input type="radio" name="station_switch" value="n" title="关闭" <?= $station_switch === 'n' ? 'checked' : '' ?>>
                    
                </div>
                <div class="layui-form-mid layui-text-em">关闭后分站域名将不生效，用户无法开通或访问分站</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">DNS-CNAME(域名解析)</label>
                <div class="layui-input-block">
                    <input type="text" name="station_cname_domain" class="layui-input" value="<?= $station_cname_domain ?>" />
                    <span class="form-tips">分站配置独立域名时的CNAME解析域名</span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">二级域名前缀保留词</label>
                <div class="layui-input-block">
                    <textarea class="layui-textarea" name="station_domain_reserved" placeholder="多个保留词请使用回车或逗号分隔"><?= $station_domain_reserved ?></textarea>
                    <span class="form-tips">分站二级域名前缀禁止使用的词（如 admin、api、www）</span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">主站商品默认加价比例</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline" style="width: 70px;">
                        <input type="number" name="station_default_premium" class="layui-input" step="1" min="0" value="<?= $station_default_premium ?>" placeholder="例如 10">
                    </div>
                    <div class="layui-form-mid">%</div>
                </div>
            </div>


            <input name="token" id="token" value="<?= LoginAuth::genToken() ?>" type="hidden"/>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn" lay-submit lay-filter="demo1">保存设置</button>
                    <button type="reset" class="layui-btn layui-btn">重置</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    $(function () {
        $("#menu-station").attr('class', 'admin-menu-item has-list in');
        $("#menu-station .fa-angle-right").attr('class', 'admin-arrow fa fa-angle-right active');
        $("#menu-station > .submenu").css('display', 'block');
        $('#menu-station-setting > a').attr('class', 'menu-link active')

        // 提交表单
        $("#setting_form").submit(function (event) {
            event.preventDefault();
            submitForm("#setting_form");
        });
    });
</script>
