<?php
defined('EM_ROOT') || exit('access denied!');
?>
<style>

    /* 统计卡片样式 */
    .plan-item {
        background: var(--panel);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        position: relative;
        cursor: default;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .plan-item::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(15, 118, 110, 0.12), transparent);
        transition: left 0.6s ease;
    }

    .plan-item:hover::before {
        left: 100%;
    }

    .plan-item:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 46px rgba(15, 118, 110, 0.18);
        border-color: var(--border);
    }

    /* 套餐标题 */
    .plan-title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 16px;
    }

    .plan-title svg {
        width: 24px;
        height: 24px;
        color: var(--primary);
    }

    .plan-title .icon-crown {
        color: var(--accent);
    }

    /* 套餐价格 */
    .plan-price {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 24px;
        line-height: 1;
    }

    .plan-price small {
        font-size: 12px;
        color: var(--muted);
        font-weight: 400;
    }

    /* 功能列表 */
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .feature-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-soft);
        font-size: 14px;
        color: var(--text);
    }

    .feature-item:last-child {
        border-bottom: none;
    }

    .feature-value {
        font-weight: 600;
        color: var(--primary);
        background: rgba(15, 118, 110, 0.12);
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
    }

    /* 店铺信息卡片（参考后台官方公告样式） */
    .info-box {
        background: var(--panel);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .info-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
        border-bottom: 1px solid var(--border-soft);
        height: 50px;
        line-height: 50px;
        padding: 0 20px;
        background: rgba(255, 255, 255, 0.6);
    }

    .info-title-text {
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
    }

    .info-icon {
        color: var(--primary);
    }

    .info-details {
        display: flex;
        flex-direction: column;
        padding: 10px 16px 14px;
        gap: 6px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-bottom: 1px dashed rgba(15, 118, 110, 0.14);
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item:hover {
        background: rgba(15, 118, 110, 0.08);
        transform: translateY(-1px);
    }

    .info-tag {
        flex-shrink: 0;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        background: rgba(15, 118, 110, 0.12);
        color: var(--primary-strong);
    }

    .info-content {
        flex: 1;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .domain-a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .domain-a:hover {
        color: var(--primary-strong);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .unconfigured {
        color: #b91c1c;
        font-weight: 600;
        background: rgba(239, 68, 68, 0.14);
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .config-link {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--primary);
        background: rgba(15, 118, 110, 0.12);
        transition: all 0.2s ease;
    }

    .config-link:hover {
        color: var(--primary-strong);
        background: rgba(15, 118, 110, 0.22);
        text-decoration: none;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    /* 响应式优化 */
    @media (max-width: 768px) {
       
        .plan-item {
            padding: 20px 16px;
        }
        
        .plan-title {
            font-size: 16px;
        }
        
        .plan-price {
            font-size: 20px;
        }
        
        .info-title {
            height: auto;
            line-height: 1.4;
            padding: 12px 16px;
        }

        .info-details {
            padding: 8px 12px 12px;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            padding: 10px 12px;
        }

        .info-content {
            width: 100%;
        }
    }

    @media (max-width: 480px) {

        
        .plan-item {
            padding: 16px 12px;
        }
        
        .plan-title {
            font-size: 14px;
            gap: 6px;
        }
        
        .plan-price {
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        .feature-item {
            padding: 10px 0;
            font-size: 13px;
        }
        
        .info-details {
            padding: 6px 10px 10px;
        }
        
        .info-item {
            padding: 8px 10px;
        }
    }
</style>

<!-- 主内容区 -->
<main class="main-content">

    <div class="grid-gap-10 grid-cols-xs-1 grid-cols-sm-1 grid-cols-md-2 grid-cols-xl-2">
        <div class="info-box">
            <div class="info-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check info-icon" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
                <span class="info-title-text">店铺信息</span>
            </div>
            
            <div class="info-details">
                <div class="info-item">
                    <span class="info-tag">店铺等级</span>
                    <span class="info-content"><?= htmlspecialchars($station['name']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-tag">开通价格</span>
                    <span class="info-content"><?= number_format((float)$my_station['amount'], 2) ?> 元</span>
                </div>

                <div class="info-item">
                    <span class="info-tag">店铺名称</span>
                    <span class="info-content">
                        <?php if(empty($userData['station']['name'])): ?>
                            <span class="unconfigured">未配置</span>
                            <a class="config-link" href="?action=setting">去配置</a>
                        <?php else: ?>
                            <?= htmlspecialchars($userData['station']['name']) ?>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-tag">店铺标题</span>
                    <span class="info-content">
                        <?php if(empty($userData['station']['title'])): ?>
                            <span class="unconfigured">未配置</span>
                            <a class="config-link" href="?action=setting">去配置</a>
                        <?php else: ?>
                            <?= htmlspecialchars($userData['station']['title']) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-tag">独立域名</span>
                    <span class="info-content">
                        <?php if (!empty($station['is_domain']) && $station['is_domain'] === 'y'): ?>
                            <?php if(empty($userData['station']['domain'])): ?>
                                <span class="unconfigured">未配置</span>
                                <a class="config-link" href="?action=setting">去配置</a>
                            <?php else: ?>
                                <a href="//<?= htmlspecialchars($userData['station']['domain']) ?>" class="domain-a" target="_blank">
                                    <?= htmlspecialchars($userData['station']['domain']) ?>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="unconfigured">不支持</span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-tag">二级域名</span>
                    <span class="info-content">
                        <?php if(empty($userData['station']['domain_2'])): ?>
                            <span class="unconfigured">未配置</span>
                            <a class="config-link" href="?action=setting">去配置</a>
                        <?php else: ?>
                            <a href="//<?= htmlspecialchars($userData['station']['domain_2']) ?>" class="domain-a" target="_blank">
                                <?= htmlspecialchars($userData['station']['domain_2']) ?>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-tag">提现手续费</span>
                    <span class="info-content"><?= (float)$station['cash_change'] * 100 ?>%</span>
                </div>
            </div>
        </div>
        <div class="info-box">
            <div class="info-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check info-icon" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
                <span class="info-title-text">运营信息</span>
            </div>
            
            <div class="info-details">
                <div class="info-item">
                    <span class="info-tag">今日订单</span>
                    <span class="info-content">
                        <span>订单数：<?= $today_orders ?> 单</span>
                        <span>金额：<?= number_format((float)$today_amount, 2) ?> 元</span>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-tag">昨日订单</span>
                    <span class="info-content">
                        <span>订单数：<?= $yesterday_orders ?> 单</span>
                        <span>金额：<?= number_format((float)$yesterday_amount, 2) ?> 元</span>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-tag">本月订单</span>
                    <span class="info-content">
                        <span>订单数：<?= $month_orders ?> 单</span>
                        <span>金额：<?= number_format((float)$month_amount, 2) ?> 元</span>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-tag">历史订单</span>
                    <span class="info-content">
                        <span>订单数：<?= $total_orders ?> 单</span>
                        <span>金额：<?= number_format((float)$total_amount, 2) ?> 元</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $('#menu-station').addClass('open');
    $('#menu-station > ul').css('display', 'block');
    $('#menu-station > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-station-index').addClass('menu-current');
</script>
