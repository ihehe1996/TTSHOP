<?php
defined('TT_ROOT') || exit('access denied!');
$payment = getPayment();
?>
<link rel="stylesheet" href="<?= TT_URL ?>/user/views/test/css/recharge_styles.css">
<style>
    .balance-page {
        display: grid;
        gap: 18px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .balance-hero {
        position: relative;
        padding: 26px 28px;
        border-radius: var(--radius-lg);
        color: #ffffff;
        background: linear-gradient(135deg, #0f766e 0%, #14b8a6 45%, #f59e0b 160%);
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.15);
    }

    .balance-hero::before,
    .balance-hero::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.18);
    }

    .balance-hero::before {
        width: 280px;
        height: 280px;
        left: -120px;
        bottom: -160px;
    }

    .balance-hero::after {
        width: 220px;
        height: 220px;
        right: -90px;
        top: -120px;
    }

    .balance-hero > * {
        position: relative;
        z-index: 1;
    }

    .balance-title {
        font-size: 14px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        opacity: 0.85;
    }

    .balance-amount {
        font-size: 40px;
        font-weight: 700;
        margin: 8px 0 18px;
        font-family: "Space Grotesk", "Noto Sans SC", sans-serif;
    }

    .balance-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .balance-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .balance-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
    }

    .table-card {
        background: var(--panel);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .table-head {
        padding: 18px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--border-soft);
        background: var(--panel-soft);
    }

    .table-head h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    .table-head p {
        margin: 6px 0 0;
        font-size: 13px;
        color: var(--muted);
    }

    .table-body {
        padding: 12px 18px 18px;
    }

    .layui-table thead tr {
        background: rgba(15, 118, 110, 0.06);
    }

    .layui-table th {
        color: var(--text);
        font-weight: 600;
        border: none;
    }

    .layui-table td {
        border: none;
        color: #374151;
    }

    .layui-table tbody tr:hover td {
        background: rgba(15, 118, 110, 0.05);
    }

    .layui-badge {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 4px 10px;
    }

    .layui-bg-blue {
        background: rgba(16, 185, 129, 0.2);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .layui-bg-cyan {
        background: rgba(239, 68, 68, 0.18);
        color: #b91c1c;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .modal-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 200;
    }

    .modal-content {
        width: 100%;
        max-width: 680px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
        overflow: hidden;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(15, 118, 110, 0.08);
        color: var(--primary-strong);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .modal-close:hover {
        background: rgba(15, 118, 110, 0.16);
    }

    .modal-header {
        padding: 20px 24px 12px;
        border-bottom: 1px solid var(--border-soft);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    .modal-desc {
        margin-top: 6px;
        font-size: 13px;
        color: var(--muted);
    }

    .modal-body {
        padding: 20px 24px;
    }

    .modal-form-actions {
        padding: 16px 24px 24px;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn {
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .submit-btn {
        background: linear-gradient(135deg, var(--primary), var(--primary-strong));
        color: #ffffff;
    }

    .cancel-btn {
        background: rgba(15, 118, 110, 0.08);
        color: var(--primary-strong);
        border-color: rgba(15, 118, 110, 0.2);
    }

    .layui-form-label {
        color: var(--text);
        font-weight: 600;
    }

    .layui-input,
    .layui-textarea,
    select {
        border-radius: 10px;
        border: 1px solid rgba(15, 118, 110, 0.2);
    }

    .form-hint {
        font-size: 12px;
        color: var(--muted);
        margin-top: 6px;
    }

    .amount-item,
    .payment-method-item {
        border-color: rgba(15, 118, 110, 0.25);
    }

    .amount-item:hover,
    .payment-method-item:hover {
        border-color: var(--primary);
        box-shadow: 0 6px 16px rgba(15, 118, 110, 0.15);
    }

    .amount-item.active,
    .payment-method-item.active {
        border-color: var(--primary);
        background: linear-gradient(135deg, rgba(15, 118, 110, 0.12) 0%, rgba(245, 158, 11, 0.08) 100%);
        color: var(--text);
        box-shadow: 0 8px 18px rgba(15, 118, 110, 0.18);
    }

    .payment-method-item.active::after {
        background: linear-gradient(135deg, var(--primary), var(--primary-strong));
    }

    @media (max-width: 768px) {
        .balance-amount {
            font-size: 32px;
        }

        .balance-actions {
            flex-direction: column;
        }

        .balance-btn {
            width: 100%;
        }
    }
</style>
<!-- 主内容区 -->
<main class="main-content">
    <div class="balance-page">
        <section class="balance-hero">
            <div class="balance-title">当前可用余额</div>
            <div class="balance-amount">¥ <?= $user['money'] ?></div>
            <div class="balance-actions">
                <button class="balance-btn tt-modal" data-modal="recharge-modal">
                    充值余额
                </button>
                <span class="balance-btn tt-modal" data-modal="withdraw-modal">
                    余额提现
                </span>
            </div>
        </section>

        <section class="table-card">
            <div class="table-head">
                <div>
                    <h2>余额流水</h2>
                    <p>查看最近的账户变动记录</p>
                </div>
            </div>
            <div class="table-body">
                <table class="layui-hide" id="index" lay-filter="index"></table>
                <script type="text/html" id="money">
                    <div class="">
                        {{# if(d.plus == 'y'){ }}
                        <div><span class="layui-badge layui-bg-blue">+ {{ d.money }}</span></div>
                        {{#  } }}
                        {{# if(d.plus == 'n'){ }}
                        <div><span class="layui-badge layui-bg-cyan">- {{ d.money }}</span></div>
                        {{#  } }}
                    </div>
                </script>
            </div>
        </section>
    </div>
</main>

<!-- 充值 -->
<div id="recharge-modal" class="modal-wrapper" style="display: none;">
    <div class="modal-content">
        <div class="modal-close close-modal-btn" data-modal="recharge-modal">
            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill="currentColor"/></svg>
        </div>
        <div class="modal-header">
            <div class="modal-title">余额充值</div>
            <!-- <div class="modal-desc">付款后，余额将会自动到达您的账户。如未到账，请联系客服人员处理。</div> -->
        </div>
        <div class="modal-body">
            <form class="layui-form modal-form" id="recharge-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">充值金额</label>
                    <div class="layui-input-block">
                        <!-- 快捷金额选择 -->
                        <div class="amount-quick-select">
                            <div class="amount-item active" data-amount="100">
                                <span class="amount-symbol">¥</span>
                                <span class="amount-value">100</span>
                            </div>
                            <div class="amount-item" data-amount="200">
                                <span class="amount-symbol">¥</span>
                                <span class="amount-value">200</span>
                            </div>
                            <div class="amount-item" data-amount="500">
                                <span class="amount-symbol">¥</span>
                                <span class="amount-value">500</span>
                            </div>
                            <div class="amount-item" data-amount="1000">
                                <span class="amount-symbol">¥</span>
                                <span class="amount-value">1000</span>
                            </div>
                            <div class="amount-item custom-amount" data-amount="custom">
                                <span class="amount-symbol">¥</span>
                                <span class="amount-value">自定义</span>
                            </div>
                        </div>
                        
                        <!-- 自定义金额输入框 -->
                        <div class="custom-amount-input" style="display: none;">
                            <input type="number" value="100" name="amount" placeholder="请输入自定义金额" class="layui-input" min="1">
                        </div>
                        
                        <div class="form-hint">最低充值金额：¥ 10.00</div>
                    </div>
                </div>
                
                <div class="layui-form-item">
                    <label class="layui-form-label">充值方式</label>
                    <div class="layui-input-block">
                        <div class="payment-methods">
                            <?php foreach($payment as $key => $val): ?>
                                <?php if($val['plugin_name'] == 'balance') continue; ?>
                                <div class="payment-method-item <?= $key == 0 ? 'active' : '' ?>" data-name="<?= $val['name'] ?>" data-method="<?= $val['plugin_name'] ?>">
                                    <div class="payment-icon">
                                        <img src="<?= $val['icon'] ?>" alt="<?= $val['title'] ?>">
                                    </div>
                                    <div class="payment-info">
                                        <div class="payment-name"><?= $val['title'] ?></div>
                                        <div class="payment-desc">安全快捷支付</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="method" value="<?= $payment[0]['plugin_name'] ?>">
                        <input type="hidden" name="payment_name" value="<?= $payment[0]['name'] ?>">
                    </div>
                </div>
                <input name="token" value="<?= LoginAuth::genToken() ?>" type="hidden"/>
            </form>
        </div>
        <div class="modal-form-actions">
            <button type="submit" class="btn submit-btn" lay-submit lay-filter="recharge-submit">
                <i class="fa fa-check"></i>
                立即提交
            </button>
            <button type="button" class="btn cancel-btn close-modal-btn" data-modal="recharge-modal">
                <i class="fa fa-times"></i>
                取消
            </button>
        </div>
    </div>
</div>

<!-- 提现 -->
<div id="withdraw-modal" class="modal-wrapper" style="display: none;">
    <div class="modal-content">
        <div class="modal-close close-modal-btn" data-modal="withdraw-modal">
            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill="currentColor"/></svg>
        </div>
        <div class="modal-header">
            <div class="modal-title">余额提现申请</div>
            <div class="modal-desc">填写您的提现信息，我们将尽快处理您的申请</div>
        </div>
        <div class="modal-body">
            <form class="layui-form modal-form" id="withdraw-form">
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
        <div class="modal-form-actions">
            <button type="submit" class="btn submit-btn" lay-submit lay-filter="withdraw-submit">
                <i class="fa fa-check"></i>
                立即提交
            </button>
            <button type="button" class="btn cancel-btn close-modal-btn" data-modal="withdraw-modal">
                <i class="fa fa-times"></i>
                取消
            </button>
        </div>
    </div>
</div>

<script>
    $(function(){
        // 快捷金额选择
        $('.amount-item').on('click', function() {
            $('.amount-item').removeClass('active');
            $(this).addClass('active');
            
            var amount = $(this).data('amount');
            var $customInput = $('.custom-amount-input');
            var $amountInput = $('input[name="amount"]');
            
            if (amount === 'custom') {
                // 显示自定义金额输入框
                $customInput.slideDown(300);
                $amountInput.focus();
                $selectedAmount.val('custom');
            } else {
                // 隐藏自定义金额输入框并设置快捷金额
                $customInput.slideUp(300);
                $amountInput.val(amount);
                $selectedAmount.val(amount);
            }
        });
        

        
        // 支付方式选择
        $('.payment-method-item').on('click', function() {
            $('.payment-method-item').removeClass('active');
            $(this).addClass('active');
            
            var method = $(this).data('method');
            var payment_name = $(this).data('name');
            $('input[name="method"]').val(method);
            $('input[name="payment_name"]').val(payment_name);
        });
    })
</script>


<script>
    // 初始化Layui模块
    layui.use(['element', 'layer'], function() {
        var table = layui.table;
        var form = layui.form;
        var layer = layui.layer;
// 创建渲染实例
        window.table = table.render({
            elem: '#index',
            autoSort: false,
            url: '?action=index', // 此处为静态模拟数据，实际使用时需换成真实接口
            toolbar: '#toolbar',
            limits: [10,20,30,50,100,200,500,1000],
            page: true,
            lineStyle: 'height: 50px;',
            defaultToolbar: ['filter', 'exports'],


            cols: [[
                {field:'description', title: '说明', minWidth: 180, height: 50},
                {field:'money', title:'更新金额', minWidth: 130, templet: '#money', height: 50},
                {field:'update_before', title:'更新前的余额', minWidth: 130, height: 50},
                {field:'create_time', title:'更新时间', minWidth: 180, height: 50},
            ]],

            error: function(res, msg){
                console.log(res, msg)
            }
        });



        
        

        
        // 提现表单提交
        layui.use(['form'], function() {
            var form = layui.form;

            form.on('submit(recharge-submit)', function(data) {
                var formData = $('#recharge-form').serialize();
                // 提交数据
                $.ajax({
                    type: "POST",
                    url: "?action=recharge",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        layer.load(2);
                    },
                    success: function (e) {
                        layer.closeAll('loading');
                        if(e.code == 400){
                            return layer.msg(e.msg);
                        }
                        // 跳转支付页面
                        location.href="<?= TT_URL ?>?action=pay&out_trade_no=" + e.data.out_trade_no;
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
            
            form.on('submit(withdraw-submit)', function(data) {
                var formData = $('#withdraw-form').serialize();
                // 提交数据
                $.ajax({
                    type: "POST",
                    url: "?action=withdraw_ajax",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        layer.load(2);
                    },
                    success: function (e) {
                        layer.closeAll('loading');
                        if(e.code == 400){
                            return layer.msg(e.msg);
                        }
                        hideTtModal('withdraw-modal');
                        layer.msg('提现申请已提交');
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

        // 工具栏事件
        table.on('toolbar(index)', function(obj){
            var id = obj.config.id;
            var checkStatus = table.checkStatus(id);
            var othis = lay(this);
            if(obj.event == 'refresh'){
                table.reload(id);
            }

           



        });

        // 触发单元格工具事件
        table.on('tool(index)', function(obj){ // 双击 toolDouble
            var data = obj.data; // 获得当前行数据
            var id = obj.config.id;


            if(obj.event === 'img'){
                layer.photos({
                    photos: {
                        "title": data.title,
                        "start": 0,
                        "data": [
                            {
                                "alt": data.title,
                                "pid": 1,
                                "src": data.cover,
                            }
                        ]
                    }
                });
            }
            if(obj.event === 'edit'){
                let isMobile = window.innerWidth < 768;
                let area = isMobile ? ['98%', 'auto']  : ['700px', 'auto'];
                layer.open({
                    id: 'edit',
                    title: '编辑',
                    type: 2,
                    area: area,
                    skin: 'layui-layer-molv',
                    content: '?action=master_goods_edit&goods_id=' + data.id,
                    fixed: false, // 不固定
                    maxmin: true,
                    shadeClose: true,
                    success: function(layero, index, that){
                        layer.iframeAuto(index); // 让 iframe 高度自适应
                        that.offset(); // 重新自适应弹层坐标
                    }
                });
            }
        });

// 触发表格复选框选择
        table.on('checkbox(index)', function(obj){
            var id = obj.config.id;
            var checkData = table.checkStatus(id).data;
            console.log(checkData)
            if(checkData.length == 0){
                $('.toolbar-select').addClass('layui-btn-disabled');
            }else{
                $('.toolbar-select').removeClass('layui-btn-disabled');
            }
        });

    });
</script>

<script>
    $('#menu-balance').addClass('open');
    $('#menu-balance > ul').css('display', 'block');
    $('#menu-balance > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-balance-index').addClass('menu-current');
</script>
