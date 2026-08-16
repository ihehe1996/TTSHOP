<?php defined('TT_ROOT') || exit('access denied!'); ?>
<?php
function em_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<style>
    :root {
        --wd-primary: #0f766e;
        --wd-primary-soft: rgba(15, 118, 110, 0.12);
        --wd-danger: #dc2626;
        --wd-warning: #f59e0b;
        --wd-success: #16a34a;
        --wd-text: #1f2937;
        --wd-muted: #6b7280;
        --wd-border: #e5e7eb;
        --wd-bg: #f5f7fb;
        --wd-card: #ffffff;
    }
    html, body {
        height: 100%;
    }
    body {
        margin: 0;
        background: var(--wd-bg);
        color: var(--wd-text);
        overflow: hidden;
    }
    .withdraw-detail {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .detail-hero {
        padding: 24px 28px;
        background: linear-gradient(135deg, #0f766e, #14b8a6);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .hero-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .hero-sub {
        font-size: 13px;
        opacity: 0.85;
    }
    .hero-amount {
        text-align: right;
    }
    .hero-amount .value {
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.18);
        margin-top: 6px;
    }
    .detail-body {
        flex: 1;
        min-height: 0;
        padding: 24px;
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        overflow-y: auto;
    }
    .detail-card {
        background: var(--wd-card);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(15, 23, 42, 0.04);
    }
    .detail-card h3 {
        margin: 0 0 14px;
        font-size: 15px;
        font-weight: 600;
        color: var(--wd-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .detail-card h3::before {
        content: '';
        width: 4px;
        height: 16px;
        border-radius: 6px;
        background: var(--wd-primary);
    }
    .kv {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px dashed var(--wd-border);
        font-size: 13px;
    }
    .kv:last-child {
        border-bottom: none;
    }
    .kv .label {
        color: var(--wd-muted);
        flex: 0 0 90px;
    }
    .kv .value {
        text-align: right;
        flex: 1;
        word-break: break-all;
    }
    .status-tag {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: var(--wd-primary-soft);
        color: var(--wd-primary);
    }
    .status-tag.success {
        background: rgba(22, 163, 74, 0.12);
        color: var(--wd-success);
    }
    .status-tag.danger {
        background: rgba(220, 38, 38, 0.12);
        color: var(--wd-danger);
    }
    .status-tag.warning {
        background: rgba(245, 158, 11, 0.16);
        color: var(--wd-warning);
    }
    .detail-actions {
        background: #fff;
        padding: 16px 24px;
        border-top: 1px solid var(--wd-border);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        align-items: center;
    }
    .detail-actions .hint {
        margin-right: auto;
        color: var(--wd-muted);
        font-size: 12px;
    }
    .empty-state {
        padding: 40px;
        text-align: center;
        color: var(--wd-muted);
        font-size: 14px;
    }
    @media (max-width: 720px) {
        .detail-hero {
            padding: 20px;
        }
        .detail-body {
            padding: 16px;
            grid-template-columns: 1fr;
        }
        .detail-actions {
            padding: 12px 16px;
            flex-direction: column;
            align-items: stretch;
        }
        .detail-actions .hint {
            margin-right: 0;
            text-align: center;
        }
    }
</style>

<?php if (empty($withdraw)): ?>
    <div class="empty-state">该提现记录不存在或已被删除。</div>
<?php else: ?>
    <div class="withdraw-detail">
        <div class="detail-hero">
            <div>
                <div class="hero-title">提现申请 #<?= em_h($withdraw['id']) ?></div>
                <div class="hero-sub">申请时间：<?= em_h($withdraw['create_time']) ?></div>
                <div class="status-pill">状态：<?= em_h($withdraw['status_label']) ?></div>
            </div>
            <div class="hero-amount">
                <div class="value">¥ <?= em_h($withdraw['amount']) ?></div>
                <div class="hero-sub">提现方式：<?= em_h($withdraw['method_text']) ?></div>
            </div>
        </div>

        <div class="detail-body">
            <div class="detail-card">
                <h3>用户信息</h3>
                <div class="kv"><span class="label">用户账号</span><span class="value"><?= em_h($withdraw['user_account']) ?></span></div>
                <div class="kv"><span class="label">用户昵称</span><span class="value"><?= em_h($withdraw['user_nickname']) ?></span></div>
                <div class="kv"><span class="label">用户ID</span><span class="value"><?= em_h($withdraw['user_id']) ?></span></div>
                <div class="kv"><span class="label">邮箱</span><span class="value"><?= em_h($withdraw['user_email']) ?></span></div>
                <div class="kv"><span class="label">手机</span><span class="value"><?= em_h($withdraw['user_tel']) ?></span></div>
            </div>

            <div class="detail-card">
                <h3>提现信息</h3>
                <div class="kv"><span class="label">提现金额</span><span class="value">¥ <?= em_h($withdraw['amount']) ?></span></div>
                <div class="kv"><span class="label">提现方式</span><span class="value"><?= em_h($withdraw['method_text']) ?></span></div>
                <div class="kv"><span class="label">账户信息</span><span class="value"><?= em_h($withdraw['account']) ?></span></div>
                <div class="kv"><span class="label">真实姓名</span><span class="value"><?= em_h($withdraw['realname']) ?></span></div>
                <div class="kv"><span class="label">手续费</span><span class="value"><?= em_h($withdraw['service_change'] ?? '-') ?></span></div>
                <div class="kv"><span class="label">备注说明</span><span class="value"><?= em_h($withdraw['remark'] ?: '无') ?></span></div>
            </div>

            <div class="detail-card">
                <h3>处理信息</h3>
                <div class="kv">
                    <span class="label">当前状态</span>
                    <span class="value">
                        <?php
                            $statusClass = 'warning';
                            if ((int)$withdraw['status'] === 1) {
                                $statusClass = 'success';
                            } elseif ((int)$withdraw['status'] === 2) {
                                $statusClass = 'danger';
                            }
                        ?>
                        <span class="status-tag <?= em_h($statusClass) ?>"><?= em_h($withdraw['status_label']) ?></span>
                    </span>
                </div>
                <div class="kv"><span class="label">完成时间</span><span class="value"><?= em_h($withdraw['finish_time'] ?: '-') ?></span></div>
                <div class="kv"><span class="label">拒绝时间</span><span class="value"><?= em_h($withdraw['reject_time'] ?: '-') ?></span></div>
                <div class="kv"><span class="label">状态说明</span><span class="value"><?= em_h($withdraw['status_text']) ?></span></div>
            </div>
        </div>

        <div class="detail-actions">
            <?php if ((int)$withdraw['status'] === 0): ?>
                <span class="hint">提示：完成或拒绝后，该记录将进入已处理状态。</span>
                <button class="layui-btn" id="btn-finish">完成</button>
                <button class="layui-btn layui-btn-blue" id="btn-reject">拒绝</button>
                <button class="layui-btn layui-btn" id="btn-close">关闭</button>
            <?php else: ?>
                <span class="hint">当前记录已处理，可返回列表执行删除操作。</span>
                <button class="layui-btn layui-btn-disabled" disabled>完成</button>
                <button class="layui-btn layui-btn-disabled" disabled>拒绝</button>
                <button class="layui-btn layui-btn" id="btn-close">关闭</button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script>
    layui.use(['layer'], function(){
        var layer = layui.layer;
        var withdrawId = <?= isset($withdraw['id']) ? (int)$withdraw['id'] : 0 ?>;

        function closeModal() {
            if (window.parent && window.parent.AdminModal) {
                window.parent.AdminModal.close();
                return;
            }
            window.close();
        }

        function reloadParent() {
            if (window.parent && window.parent.table) {
                window.parent.table.reload();
            }
        }

        $('#btn-close').on('click', function() {
            closeModal();
        });

        $('#btn-finish').on('click', function() {
            layer.confirm('确定已转账？', {
                btn: ['确认', '取消'],
                icon: 3,
                title: '温馨提示'
            }, function(index) {
                layer.close(index);
                $.ajax({
                    url: 'withdraw.php?action=cmd&type=finish',
                    type: 'POST',
                    dataType: 'json',
                    data: { ids: withdrawId, token: '<?= LoginAuth::genToken() ?>' },
                    success: function(e) {
                        if(e.code == 400){
                            return layer.msg(e.msg);
                        }
                        layer.msg(e.msg);
                        reloadParent();
                        closeModal();
                    },
                    error: function(err) {
                        layer.msg(err.responseJSON.msg);
                    }
                });
            });
        });

        $('#btn-reject').on('click', function() {
            layer.confirm('拒绝后，余额将返还至用户的余额账户！', {
                btn: ['确认', '取消'],
                icon: 3,
                title: '温馨提示'
            }, function(index) {
                layer.close(index);
                $.ajax({
                    url: 'withdraw.php?action=cmd&type=reject',
                    type: 'POST',
                    dataType: 'json',
                    data: { ids: withdrawId, token: '<?= LoginAuth::genToken() ?>' },
                    success: function(e) {
                        if(e.code == 400){
                            return layer.msg(e.msg);
                        }
                        layer.msg(e.msg);
                        reloadParent();
                        closeModal();
                    },
                    error: function(err) {
                        layer.msg(err.responseJSON.msg);
                    }
                });
            });
        });
    });
</script>
