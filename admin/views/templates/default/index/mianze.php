<?php defined('EM_ROOT') || exit('access denied!'); ?>
<style>
    html, body{
        height: 100%;
    }
    body{
        margin: 0;
        overflow: hidden;
        background: #f5f7fa;
    }
    #form{
        height: 100%;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    #open-box{
        height: calc(100% - 73px);
        overflow-y: auto;
        padding: 25px;
        background: #f5f7fa;
        box-sizing: border-box;
    }
    .agreement-content{
        max-width: 920px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #eef2f5;
        border-radius: 12px;
        padding: 22px 26px 10px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }
    .agreement-content h1{
        font-size: 22px;
        color: #0f172a;
        margin: 6px 0 8px;
    }
    .agreement-content .form-tips{
        color: #64748b;
        font-size: 13px;
        margin: 4px 0;
    }
    .agreement-content .layui-timeline{
        margin-top: 18px;
    }
    .agreement-content .layui-timeline-item{
        padding-bottom: 16px;
    }
    .agreement-content .layui-timeline-axis{
        color: #0f766e;
    }
    .agreement-content .layui-timeline-title{
        font-size: 15px;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .agreement-content h4{
        font-size: 13px;
        color: #334155;
        margin: 8px 0 4px;
        font-weight: 600;
    }
    .agreement-content p,
    .agreement-content li{
        font-size: 13px;
        line-height: 1.65;
        color: #475569;
    }
    #form-btn{
        flex-shrink: 0;
        padding: 10px 0 12px;
        background: #fff;
        border-top: 1px solid #eef2f5;
    }
    #form-btn .layui-input-block{
        text-align: center;
    }
    #form-btn .layui-btn{
        height: 34px;
        line-height: 34px;
        font-size: 13px;
        padding: 0 16px;
    }
    #form-btn .layui-btn + .layui-btn{
        margin-left: 10px;
    }
</style>


<form class="layui-form " action="sort.php?action=save" id="form">
    <div id="open-box">
        <div class="agreement-content">
        <h1 style="text-align: center;">EMSHOP商城用户协议</h1>
        <p style="text-align: center;" class="form-tips">欢迎使用EMSHOP开源商城系统（严禁用于违法违规行为）</p>
        <p style="text-align: center;" class="form-tips">协议更新时间：2025年10月</p>
        <div class="layui-timeline">
            <div class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis"></i>
                <div class="layui-timeline-content layui-text">
                    <h3 class="layui-timeline-title">协议概述</h3>
                    <p>EMSHOP商城是一个完全开源的数字产品销售系统，致力于为用户提供安全、便捷的在线购物体验。在使用本系统前，请仔细阅读并同意以下用户协议。</p>
                </div>
            </div>
            <div class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis"></i>
                <div class="layui-timeline-content layui-text">
                    <h3 class="layui-timeline-title">一、服务说明</h3>
                    <h4>1.1 系统介绍</h4>
                    <p>EMSHOP商城是一个基于PHP + MySQL开发的数字产品销售系统，提供完整的在线商城解决方案，包括商品管理、订单处理、用户管理、支付集成、应用市场等功能。</p>
                    <h4>1.2 开源协议</h4>
                    <p>本系统采用 GPLv3 开源协议发布，所有核心代码均为开源代码。</p>
                    <ul>
                        <li>基于本程序代码，其修改、衍生作品或与其他代码组合后的整体，必须同样采用 GPLv3 协议开源，且需公开完整源代码。</li>
                        <li>禁止代码贡献者以专利诉讼威胁其他用户，同时区分软件版权与商标权，避免商标滥用问题。</li>
                        <li>禁止通过技术手段（如数字签名、硬件限制）阻止用户修改或安装自定义版本的软件，保障用户对设备的控制权。</li>
                    </ul>
                </div>
            </div>

            <div class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis"></i>
                <div class="layui-timeline-content layui-text">
                    <h3 class="layui-timeline-title">二、用户权利与义务</h3>
                    <h4>2.1 用户权利</h4>
                    <ul>
                        <li>免费使用系统基础功能</li>
                        <li>获得技术支持和社区帮助</li>
                        <li>参与系统改进和功能建议</li>
                        <li>在遵守协议的前提下进行二次开发</li>
                    </ul>
                    <h4>2.2 用户义务</h4>
                    <ul>
                        <li>遵守相关法律法规</li>
                        <li>不得利用本程序进行违法活动</li>
                        <li>保护用户隐私和数据安全</li>
                        <li>不得恶意攻击或破坏系统</li>
                        <li>不得利用本系统销售外挂、作弊工具或其他违法违规内容</li>
                    </ul>
                </div>
            </div>
            <div class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis"></i>
                <div class="layui-timeline-content layui-text">
                    <h3 class="layui-timeline-title">三、免责声明</h3>
                    <h4>3.1 服务可用性</h4>
                    <p>我们努力确保系统的稳定性和可用性，但无法保证：</p>
                    <ul>
                        <li>系统100%无故障运行</li>
                        <li>所有功能完全符合用户需求</li>
                        <li>第三方服务的稳定性</li>
                    </ul>
                    <h4>3.2 损失赔偿</h4>
                    <p>在以下情况下，我们不承担赔偿责任：</p>
                    <ul>
                        <li>因不可抗力导致的系统故障</li>
                        <li>用户操作不当造成的数据丢失</li>
                        <li>第三方服务商的问题</li>
                        <li>用户违反协议造成的损失</li>
                        <li>因用户违法使用系统而遭受的任何处罚或索赔</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis"></i>
            <div class="layui-timeline-content layui-text">
                <h3 class="layui-timeline-title">四、禁止违法用途声明</h3>
                <h4>4.1 严禁非法用途</h4>
                <p>您承诺不使用本系统从事任何违法、违规或损害公共利益的行为，包括但不限于：</p>
                <ul>
                    <li>售卖外挂、作弊工具、破解软件等违反游戏厂商、平台服务协议或国家法规的商品与服务</li>
                    <li>销售黄赌毒类内容、非法支付通道、诈骗产品等非法商品</li>
                    <li>构建用于洗钱、非法资金转移、组织犯罪等目的的平台或服务</li>
                </ul>
                <h4>4.2 法律责任归属</h4>
                <p>用户使用本系统进行违法行为所造成的一切后果，均由用户自行承担全部法律责任，与本系统作者及贡献者无关。</p>
                <h4>4.3 技术支持拒绝权</h4>
                <p>一经发现用户将本系统用于违法活动，我们有权立即终止其插件服务、技术支持及授权，并保留依法向有关部门举报的权利。</p>
            </div>
        </div>
        <div class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis"></i>
            <div class="layui-timeline-content layui-text">
                <h3 class="layui-timeline-title">五、协议变更</h3>
                <p>我们保留随时修改本协议的权利。重大变更将通过以下方式通知用户：</p>
                <ul>
                    <li>在本程序后台重新弹出本协议</li>
                </ul>
                <p>继续使用系统即表示同意修改后的协议条款。</p>
            </div>
        </div>
        </div>
    </div>
    <div class="" id="form-btn">
        <div class="layui-input-block" style="margin: 0 auto;">
            <?php if($chakan): ?>
                <button type="button" class="layui-btn layui-btn-red" lay-submit lay-filter="jujue">不再遵守本协议</button>
                <button type="button" class="layui-btn layui-btn-blue" lay-submit lay-filter="close">关闭窗口</button>
            <?php else: ?>
                <button type="submit" class="layui-btn layui-btn-green" lay-submit lay-filter="submit">我已完全阅读并同意此协议</button>
                <button type="button" class="layui-btn layui-btn-red" id="jujue">不同意 (退出系统)</button>
            <?php endif; ?>


        </div>
    </div>
</form>



<script>

    $('#jujue').click(function(){
        parent.location.href="account.php?action=logout"
    })

    layui.use(['table'], function(){

        var $ = layui.$;
        var form = layui.form;
        var upload = layui.upload;
        var element = layui.element;
        form.on('submit(submit)', function(data){

            layer.confirm('您确定已仔细阅读并同意《EMSHOP商城用户协议》的所有条款？', {
                btn: ['我确定', '去阅读'],
                title: '温馨提示'
            }, function(){
                var field = data.field; // 获取表单全部字段值
                var url = $('#form').attr('action');
                $.ajax({
                    type: "POST",
                    url: "index.php?action=mianze_ajax",
                    data: {mianze: 1},
                    dataType: "json",
                    success: function (e) {
                        parent.location.reload();
                    },
                    error: function (xhr) {
                        layer.msg(JSON.parse(xhr.responseText).msg);
                    }
                });
            }, function(){

            });


            return false; // 阻止默认 form 跳转
        });

        form.on('submit(close)', function(data){
            parent.layer.closeAll();
            return false; // 阻止默认 form 跳转
        });

        form.on('submit(jujue)', function(data){
            var field = data.field; // 获取表单全部字段值
            var url = $('#form').attr('action');
            $.ajax({
                type: "POST",
                url: "index.php?action=jujue_mianze_ajax",
                data: {jujue: 1},
                dataType: "json",
                success: function (e) {
                    if(e.code == 400){
                        layer.msg(e.msg)
                    }else{
                        parent.location.href="account.php?action=logout"
                    }

                },
                error: function (xhr) {
                    layer.msg(JSON.parse(xhr.responseText).msg);
                }
            });
            return false; // 阻止默认 form 跳转
        });






    })


</script>
