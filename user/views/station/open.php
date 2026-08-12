<?php
defined('EM_ROOT') || exit('access denied!');
?>
<style>
    /* 主内容区样式 */
    .main-content {
        padding: 28px;
    }

    /* 套餐卡片样式 */
    .plan-item {
        background: var(--panel);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border: 1px solid var(--border-soft);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
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

    .plan-item.active {
        border-color: var(--primary);
        background: linear-gradient(135deg, rgba(15, 118, 110, 0.06) 0%, rgba(245, 158, 11, 0.08) 100%);
        transform: translateY(-4px);
        box-shadow: 0 14px 36px rgba(15, 118, 110, 0.2);
    }

    /* 当前选择标签 */
    .plan-tag {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.3s ease;
    }

    .plan-item.active .plan-tag {
        opacity: 1;
        transform: scale(1);
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

    .icon-crown {
        width: 24px;
        height: 24px;
        color: var(--accent);
    }

    .crown-bronze {
        color: #cd7f32;
    }

    .crown-silver {
        color: #c0c0c0;
    }

    .crown-gold {
        color: #ffd700;
    }

    .crown-diamond {
        color: #b9f2ff;
    }

    /* 套餐价格 */
    .plan-price {
        font-size: 28px;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 24px;
        line-height: 1;
    }

    .plan-price small {
        font-size: 14px;
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

    .feature-check {
        color: #10b981;
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    .feature-cross {
        color: #ef4444;
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    .feature-value {
        font-weight: 600;
        color: var(--primary);
        background: rgba(15, 118, 110, 0.12);
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
    }

    /* 自定义卡片 */
    .custom-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .custom-card-body {
        padding: 32px;
    }

    /* 信息框 */
    .info-box {
        margin-top: 20px;
        background: var(--panel-soft);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
    }

    .info-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 16px;
    }

    .info-icon {
        color: var(--primary);
    }

    .info-box p {
        margin-bottom: 12px;
        line-height: 1.6;
        color: var(--muted);
        font-size: 14px;
    }

    .info-box p:last-child {
        margin-bottom: 0;
    }

    .info-highlight {
        color: var(--primary);
    }

    .section-heading {
        margin-bottom: 24px;
    }

    .section-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
    }

    .action-row {
        margin-top: 30px;
        text-align: right;
    }

    /* 提交按钮 */
    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-strong) 100%);
        color: white;
        border: none;
        padding: 16px 48px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        box-shadow: 0 10px 26px rgba(15, 118, 110, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-submit::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 38px rgba(15, 118, 110, 0.38);
    }

    .btn-submit:active {
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(15, 118, 110, 0.3);
    }

    /* 响应式优化 */
    @media (max-width: 768px) {
        .main-content {
            padding: 20px 14px 28px;
        }
        
        .custom-card-body {
            padding: 24px 20px;
        }
        
        .plan-item {
            padding: 20px 16px;
        }
        
        .plan-title {
            font-size: 16px;
        }
        
        .plan-price {
            font-size: 24px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px 24px;
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
            font-size: 20px;
            margin-bottom: 20px;
        }
        
        .feature-item {
            padding: 10px 0;
            font-size: 13px;
        }
        
        .info-box {
            padding: 20px 16px;
        }
    }

</style>


<!-- 主内容区 -->
<main class="main-content">

    <div class="custom-card">

        <div class="custom-card-body">

            <div class="section-heading">
                <h3 class="section-title">请选择您要开通的套餐</h3>
            </div>
            <div class="grid-gap-10 grid-cols-xs-2 grid-cols-sm-2 grid-cols-md-3 grid-cols-xl-4">
                <?php foreach($station as $val): ?>
                    <div class="plan-item " data-id="<?= $val['id'] ?>">
                        <div class="plan-tag">
                            当前选择
                        </div>
                        <div class="plan-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown icon-crown crown-bronze" aria-hidden="true">
                                <path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"></path>
                                <path d="M5 21h14"></path>
                            </svg>
                            <?= $val['name'] ?>
                        </div>
                        <div class="plan-price">
                            &yen;<?= $val['price'] ?>
                            <small>/ &nbsp;永久</small>
                        </div>
                        <ul class="feature-list">

                            <li class="feature-item"><span>赠送子域名</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check icon-check feature-check" aria-hidden="true">
                                    <path d="M20 6 9 17l-5-5"></path>
                                </svg>
                            </li>
                            <li class="feature-item"><span>独立域名</span>
                                <?php if($val['is_domain'] == 'y'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check icon-check feature-check" aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x icon-x feature-cross" aria-hidden="true">
                                        <path d="M18 6 6 18"></path>
                                        <path d="m6 6 12 12"></path>
                                    </svg>
                                <?php endif; ?>
                            </li>
                            <li class="feature-item"><span>24小时技术支持</span>
                                <?php if($val['is_domain'] == 'y'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check icon-check feature-check" aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x icon-x feature-cross" aria-hidden="true">
                                        <path d="M18 6 6 18"></path>
                                        <path d="m6 6 12 12"></path>
                                    </svg>
                                <?php endif; ?>
                            </li>

                            <li class="feature-item">
                                <span>提现手续费</span>
                                <span class="feature-value"><?= $val['cash_change'] * 100 ?>%</span>
                            </li>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="info-box">
                <div class="info-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check info-icon" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>套餐说明</span>
                </div>
                <p><strong class="info-highlight">• 分站返佣：</strong> 开通分站后，您在分站售出的主站商品，将按差价返还佣金（实际成交金额 - 您的拿货价 = 您的佣金）。</p>
                <p><strong class="info-highlight">• 独立域名：</strong> 开通分站后，您可绑定自己的顶级域名，而无需使用系统默认分配的子域名。</p>
<!--                <p><strong class="info-highlight">• 供货权限：</strong> 您可自建商品分类并上架商品进行销售，主站也将协助推广与售卖。</p>-->
<!--                <p><strong class="info-highlight">• 供货手续费：</strong> 针对您自主上架的商品，每笔成功交易将收取一定比例的手续费。</p>-->
            </div>
            <div class="action-row">
                <input type="hidden" id="id" value="" type="number" />
                <button class="btn-submit">立即开通</button>
            </div>
        </div>
    </div>
</main>


<script>
    // 初始化Layui模块
    layui.use(['element', 'layer'], function() {
        var element = layui.element;
        var layer = layui.layer;

        $('.plan-item').click(function(){
            $('.plan-item').removeClass('active')
            $(this).addClass('active');
            $('#id').val($(this).data('id'))
        })

        $('.btn-submit').click(function(){
            var id = $('#id').val();
            if(id == ''){
                return layer.msg('请选择您要开通的套餐')
            }
            var loadIndex = layer.load(2);
            $.ajax({
                type: "POST",
                url: "?action=open_ajax",
                data: {id: id},
                dataType: "json",
                success: function (e) {
                    if(e.code == 400){
                        return layer.msg(e.msg)
                    }
                    layer.alert(e.msg, function(){
                        location.href="station.php";
                    });
                },
                error: function (xhr) {
                    layer.msg(JSON.parse(xhr.responseText).msg);
                },
                complete: function(){
                    layer.close(loadIndex)
                }
            });
        })

    });
</script>

<script>
    $(function () {
        $('#menu-open-station').addClass('open menu-current');
    });
</script>
