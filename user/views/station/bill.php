<?php
defined('EM_ROOT') || exit('access denied!');
?>
<style>
    .main-content {
        padding: 24px 18px 30px;
    }

    .bill-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
        gap: 22px;
        padding: 22px;
        border-radius: var(--radius-lg);
        background: linear-gradient(135deg, rgba(15, 118, 110, 0.12), rgba(245, 158, 11, 0.08));
        border: 1px solid var(--border-soft);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        position: relative;
        overflow: hidden;
    }

    .bill-hero::after {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        right: -100px;
        top: -120px;
        border-radius: 50%;
        background: rgba(15, 118, 110, 0.12);
    }

    .bill-hero > * {
        position: relative;
        z-index: 1;
    }

    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--primary-strong);
        background: rgba(15, 118, 110, 0.12);
    }

    .hero-title {
        margin: 12px 0 8px;
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
    }

    .hero-sub {
        margin: 0 0 16px;
        color: var(--muted);
        font-size: 14px;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .stat-card {
        padding: 14px 16px;
        border-radius: 14px;
        background: var(--panel);
        border: 1px solid var(--border-soft);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
        position: relative;
    }

    .stat-label {
        font-size: 12px;
        color: var(--muted);
    }

    .stat-value {
        margin-top: 6px;
        font-size: 20px;
        font-weight: 700;
        color: var(--primary);
        font-family: "Space Grotesk", "Noto Sans SC", sans-serif;
    }

    .stat-hint {
        margin-top: 6px;
        font-size: 12px;
        color: var(--muted);
    }

    .stat-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: var(--primary-strong);
        background: rgba(15, 118, 110, 0.12);
        border: 1px solid rgba(15, 118, 110, 0.18);
        transition: all 0.2s ease;
        position: absolute;
        top: 12px;
        right: 12px;
    }

    .stat-link:hover {
        background: rgba(15, 118, 110, 0.2);
        color: var(--primary-strong);
    }

    .transfer-card {
        background: var(--panel);
        border-radius: var(--radius-md);
        border: 1px solid var(--border-soft);
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.08);
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .transfer-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .transfer-head h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    .transfer-head p {
        margin: 6px 0 0;
        font-size: 12px;
        color: var(--muted);
    }

    .rate-chip {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(245, 158, 11, 0.16);
        color: #b45309;
    }

    .transfer-form .layui-form-label {
        color: var(--primary-strong);
        font-weight: 600;
    }

    .input-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-row .layui-input {
        flex: 1;
        border-radius: 10px;
    }

    .max-btn {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid rgba(15, 118, 110, 0.2);
        background: rgba(15, 118, 110, 0.08);
        color: var(--primary-strong);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .max-btn:hover {
        background: rgba(15, 118, 110, 0.16);
    }

    .form-hint {
        margin-top: 8px;
        font-size: 12px;
        color: var(--muted);
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .preview-item {
        padding: 10px 12px;
        border-radius: 12px;
        background: var(--panel-soft);
        border: 1px dashed rgba(15, 118, 110, 0.2);
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 12px;
        color: var(--muted);
    }

    .preview-item strong {
        font-size: 15px;
        color: var(--text);
    }

    .transfer-submit {
        border: none;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-strong) 100%);
        box-shadow: 0 10px 20px rgba(15, 118, 110, 0.26);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        width: 100%;
    }

    .transfer-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(15, 118, 110, 0.3);
    }

    .transfer-tips {
        font-size: 12px;
        color: var(--muted);
    }

    .table-card {
        margin-top: 20px;
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

    .type-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .type-plus {
        background: rgba(16, 185, 129, 0.2);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .type-minus {
        background: rgba(239, 68, 68, 0.18);
        color: #b91c1c;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .layui-badge {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 4px 10px;
    }

    .badge-plus {
        background: rgba(16, 185, 129, 0.2);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .badge-minus {
        background: rgba(239, 68, 68, 0.18);
        color: #b91c1c;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    @media (max-width: 960px) {
        .bill-hero {
            grid-template-columns: 1fr;
        }

        .preview-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .input-row {
            flex-direction: column;
            align-items: stretch;
        }

        .max-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<main class="main-content">
    <section class="bill-hero">
        <div class="hero-info">
            <span class="hero-tag">Station Billing</span>
            <div class="hero-title">店铺账单</div>
            <div class="hero-sub">查看店铺余额变动，并支持一键划转到用户余额。</div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">店铺余额</div>
                    <div class="stat-value" id="station-balance">¥ <?= number_format($station_money, 2) ?></div>
                    <div class="stat-hint">可用于划转到余额</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">用户余额</div>
                    <div class="stat-value" id="user-balance">¥ <?= number_format($user_money, 2) ?></div>
                    <div class="stat-hint">划转后自动到账</div>
                    <a class="stat-link" href="balance.php">去提现</a>
                </div>
                <div class="stat-card">
                    <div class="stat-label">划转手续费</div>
                    <div class="stat-value"><?= $cash_change_percent_text ?>%</div>
                    <div class="stat-hint">依据分站等级配置</div>
                </div>
            </div>
        </div>

        <div class="transfer-card">
            <div class="transfer-head">
                <div>
                    <h3>划转到余额</h3>
                    <p>分站余额划转至用户余额</p>
                </div>
                <div class="rate-chip">手续费率 <?= $cash_change_percent_text ?>%</div>
            </div>

            <form class="layui-form transfer-form" id="transfer-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">划转金额</label>
                    <div class="layui-input-block">
                        <div class="input-row">
                            <input type="number" min="1" step="0.01" class="layui-input" id="transfer-amount" placeholder="请输入划转金额">
                            <button type="button" class="max-btn" id="transfer-max">全部划转</button>
                        </div>
                        <div class="form-hint">最低划转金额：¥ 1.00 ｜ 可用分站余额：<span id="station-available">¥ <?= number_format($station_money, 2) ?></span></div>
                    </div>
                </div>

                <div class="preview-grid">
                    <div class="preview-item">
                        <span>手续费</span>
                        <strong id="fee-amount">¥ 0.00</strong>
                    </div>
                    <div class="preview-item">
                        <span>到账金额</span>
                        <strong id="net-amount">¥ 0.00</strong>
                    </div>
                    <div class="preview-item">
                        <span>划转后分站余额</span>
                        <strong id="after-station">¥ <?= number_format($station_money, 2) ?></strong>
                    </div>
                    <div class="preview-item">
                        <span>划转后用户余额</span>
                        <strong id="after-user">¥ <?= number_format($user_money, 2) ?></strong>
                    </div>
                </div>

                <button type="submit" class="transfer-submit" style="margin: 15px 0 10px 0;">确认划转</button>
                <div class="transfer-tips">提示：实际到账 = 划转金额 - 手续费。</div>
            </form>
        </div>
    </section>

    <section class="table-card">
        <div class="table-head">
            <div>
                <h2>账单记录</h2>
                <p>展示最近的余额变动记录（支持分页）</p>
            </div>
        </div>
        <div class="table-body">
            <table class="layui-hide" id="bill-table" lay-filter="bill-table"></table>

            <script type="text/html" id="bill-toolbar">
                <div class="layui-btn-container">
                    <button class="layui-btn layui-btn-sm" lay-event="refresh">刷新</button>
                </div>
            </script>
            <script type="text/html" id="moneyTpl">
                {{# if(d.flow == 'plus'){ }}
                <span class="layui-badge badge-plus">+ {{ d.money_abs }}</span>
                {{#  } else { }}
                <span class="layui-badge badge-minus">- {{ d.money_abs }}</span>
                {{#  } }}
            </script>
            <script type="text/html" id="typeTpl">
                {{# if(d.flow == 'plus'){ }}
                <span class="type-chip type-plus">收入</span>
                {{#  } else { }}
                <span class="type-chip type-minus">支出</span>
                {{#  } }}
            </script>
        </div>
    </section>
</main>

<script>
    layui.use(['element', 'layer', 'table'], function() {
        var table = layui.table;
        var layer = layui.layer;

        window.billTable = table.render({
            elem: '#bill-table',
            id: 'bill-table',
            autoSort: false,
            url: '?action=bill_index',
            toolbar: '#bill-toolbar',
            limits: [10, 20, 30, 50, 100, 200],
            page: true,
            lineStyle: 'height: 45px;',
            defaultToolbar: ['filter', 'exports'],
            cols: [[
                {field: 'id', title: 'ID', width: 70},
                {field: 'flow_text', title: '类型', width: 90, templet: '#typeTpl'},
                {field: 'money', title: '变动金额', minWidth: 130, templet: '#moneyTpl'},
                {field: '_money', title: '变动前', minWidth: 120},
                {field: 'money_', title: '变动后', minWidth: 120},
                {field: 'create_time', title: '时间', minWidth: 170}
            ]],
            error: function(res, msg) {
                console.log(res, msg);
            }
        });

        table.on('toolbar(bill-table)', function(obj) {
            if (obj.event === 'refresh') {
                table.reload(obj.config.id);
            }
        });

        var stationBalance = parseFloat('<?= $station_money ?>') || 0;
        var userBalance = parseFloat('<?= $user_money ?>') || 0;
        var feeRate = parseFloat('<?= $cash_change ?>') || 0;

        function formatMoney(value) {
            if (isNaN(value)) {
                return '¥ 0.00';
            }
            return '¥ ' + value.toFixed(2);
        }

        function updatePreview() {
            var amount = parseFloat($('#transfer-amount').val());
            if (isNaN(amount) || amount <= 0) {
                amount = 0;
            }
            var fee = Math.round(amount * feeRate * 100) / 100;
            var net = Math.round((amount - fee) * 100) / 100;
            var afterStation = Math.round((stationBalance - amount) * 100) / 100;
            var afterUser = Math.round((userBalance + net) * 100) / 100;

            $('#fee-amount').text(formatMoney(fee));
            $('#net-amount').text(formatMoney(net));
            $('#after-station').text(formatMoney(afterStation < 0 ? 0 : afterStation));
            $('#after-user').text(formatMoney(afterUser < 0 ? 0 : afterUser));
        }

        $('#transfer-amount').on('input', updatePreview);

        $('#transfer-max').on('click', function() {
            $('#transfer-amount').val(stationBalance.toFixed(2));
            updatePreview();
        });

        $('#transfer-form').on('submit', function(e) {
            e.preventDefault();
            var amount = parseFloat($('#transfer-amount').val());
            if (isNaN(amount) || amount <= 0) {
                return layer.msg('请输入有效的划转金额');
            }
            if (amount < 1) {
                return layer.msg('最低划转金额为1元');
            }
            if (amount > stationBalance) {
                return layer.msg('店铺余额不足');
            }

            var fee = Math.round(amount * feeRate * 100) / 100;
            var net = Math.round((amount - fee) * 100) / 100;
            var confirmHtml = '划转金额：' + formatMoney(amount) + '<br>' +
                '手续费(' + (feeRate * 100).toFixed(2) + '%)：' + formatMoney(fee) + '<br>' +
                '到账金额：' + formatMoney(net);

            layer.confirm(confirmHtml, {
                title: '确认划转',
                btn: ['确认', '取消']
            }, function(index) {
                layer.close(index);
                var loading = layer.load(2);
                $.ajax({
                    url: '?action=bill_transfer',
                    type: 'POST',
                    dataType: 'json',
                    data: { amount: amount },
                    success: function(res) {
                        if (res.code == 400) {
                            return layer.msg(res.msg || '划转失败');
                        }
                        layer.msg(res.msg || '划转成功');
                        if (res.data) {
                            stationBalance = parseFloat(res.data.station_money) || 0;
                            userBalance = parseFloat(res.data.user_money) || 0;
                            $('#station-balance').text(formatMoney(stationBalance));
                            $('#user-balance').text(formatMoney(userBalance));
                            $('#station-available').text(formatMoney(stationBalance));
                            $('#transfer-amount').val('');
                            updatePreview();
                        }
                        table.reload('bill-table');
                    },
                    error: function(xhr) {
                        var msg = '划转失败，请重试';
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data.msg) {
                                msg = data.msg;
                            }
                        } catch (err) {}
                        layer.msg(msg);
                    },
                    complete: function() {
                        layer.close(loading);
                    }
                });
            });
        });

        updatePreview();
    });
</script>

<script>
    $('#menu-station').addClass('open');
    $('#menu-station > ul').css('display', 'block');
    $('#menu-station > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-station-bill').addClass('menu-current');
</script>
