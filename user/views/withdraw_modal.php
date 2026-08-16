<?php defined('TT_ROOT') || exit('access denied!'); ?>

<!-- 内嵌提现表单 -->
<div id="withdraw-modal" class="withdraw-modal-wrapper" style="display: none;">
    <div class="withdraw-modal-content">
        <div class="withdraw-modal-close" id="close-withdraw">
            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill="currentColor"/></svg>
        </div>
        <div class="withdraw-modal-header">
            <div class="withdraw-modal-title">余额提现申请</div>
            <div class="withdraw-modal-desc">填写您的提现信息，我们将尽快处理您的申请</div>
        </div>
        <div class="withdraw-modal-body">
            <form class="layui-form withdraw-form" id="withdraw-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">提现金额</label>
                    <div class="layui-input-block">
                        <input type="number" name="amount" placeholder="请输入提现金额" class="layui-input">
                        <div class="form-hint">可提现余额：¥ <?= $user['money'] ?></div>
                    </div>
                </div>
                
                <div class="layui-form-item">
                    <label class="layui-form-label">提现方式</label>
                    <div class="layui-input-block">
                        <select name="method">
                            <option value="">请选择提现方式</option>
                            <option value="alipay">支付宝</option>
                            <option value="wechat">微信</option>
                            <option value="bank">银行卡</option>
                        </select>
                    </div>
                </div>
                
                <div class="layui-form-item">
                    <label class="layui-form-label">账户信息</label>
                    <div class="layui-input-block">
                        <input type="text" name="account" placeholder="请输入提现账户" class="layui-input">
                        <div class="form-hint">请输入支付宝账号/微信号/银行卡号</div>
                    </div>
                </div>
                
                <div class="layui-form-item">
                    <label class="layui-form-label">真实姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="realname" placeholder="请输入真实姓名" class="layui-input">
                        <div class="form-hint">请输入与提现账户对应的真实姓名</div>
                    </div>
                </div>
                
                <div class="layui-form-item">
                    <label class="layui-form-label">备注说明</label>
                    <div class="layui-input-block">
                        <textarea name="remark" placeholder="请输入备注说明（选填）" class="layui-textarea" rows="3"></textarea>
                    </div>
                </div>
                
                <input name="token" value="<?= LoginAuth::genToken() ?>" type="hidden"/>
            </form>
        </div>
        <div class="withdraw-form-actions">
            <button type="submit" class="withdraw-btn submit-btn" lay-submit lay-filter="withdraw-submit">
                <i class="fa fa-check"></i>
                立即提交
            </button>
            <button type="button" class="withdraw-btn cancel-btn" id="cancel-withdraw">
                <i class="fa fa-times"></i>
                取消
            </button>
        </div>
    </div>
</div>

<script>
// 提现弹窗模块
(function() {
    'use strict';
    
    // 显示提现弹窗
    window.showWithdrawModal = function() {
        $('#withdraw-modal').fadeIn(300);
        $('body').css('overflow', 'hidden');
        
        // 重新渲染Layui表单
        layui.use('form', function() {
            layui.form.render();
        });
    };
    
    // 关闭提现弹窗
    window.hideWithdrawModal = function() {
        $('#withdraw-modal').fadeOut(300);
        $('body').css('overflow', 'auto');
        // 重置表单
        $('#withdraw-form')[0].reset();
    };
    
    // 初始化事件绑定
    $(document).ready(function() {
        // 绑定关闭事件
        $('#close-withdraw, #cancel-withdraw').on('click', function() {
            window.hideWithdrawModal();
        });
        
        // 点击背景关闭
        $('#withdraw-modal').on('click', function(e) {
            if (e.target === this) {
                window.hideWithdrawModal();
            }
        });
        
        // ESC键关闭
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#withdraw-modal').is(':visible')) {
                window.hideWithdrawModal();
            }
        });
        
        // 提现表单提交
        layui.use(['form'], function() {
            var form = layui.form;
            
            form.on('submit(withdraw-submit)', function(data) {
                var formData = $('#withdraw-form').serialize();
                console.log('序列化后的表单数据:', formData);
                
                // 检查必填字段
                if (!formData.includes('amount=') || formData.includes('amount=&')) {
                    layer.msg('请输入提现金额');
                    return false;
                }
                if (!formData.includes('method=alipay') && !formData.includes('method=wechat') && !formData.includes('method=bank')) {
                    layer.msg('请选择提现方式');
                    return false;
                }
                if (!formData.includes('account=') || formData.includes('account=&')) {
                    layer.msg('请输入提现账户');
                    return false;
                }
                if (!formData.includes('realname=') || formData.includes('realname=&')) {
                    layer.msg('请输入真实姓名');
                    return false;
                }
                
                // 提交数据
                $.ajax({
                    type: "POST",
                    url: "?action=withdraw_ajax",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        layer.load(2, {
                            shade: [0.3, '#000']
                        });
                    },
                    success: function (e) {
                        layer.closeAll('loading');
                        if(e.code == 400){
                            return layer.msg(e.msg);
                        }
                        window.hideWithdrawModal();
                        layer.msg('提现申请已提交', {
                            icon: 1,
                            time: 2000
                        });
                        // 刷新页面数据
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        layer.closeAll('loading');
                        try {
                            var errorData = JSON.parse(xhr.responseText);
                            layer.msg(errorData.msg || '提交失败，请重试');
                        } catch (e) {
                            layer.msg('网络错误，请重试');
                        }
                    }
                });
                return false;
            });
        });
    });
})();
</script>