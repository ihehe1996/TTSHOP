<?php defined('EM_ROOT') || exit('access denied!'); ?>

</div>
</div>
</div>
</div>
<?php doAction('adm_footer') ?>

<script>

    $('.get-em-buy-info').click(function(){
        loadIndex = layer.load(2);
        $.ajax({
            url: "./index.php?action=get_em_buy_info",
            type: "GET",
            dataType: "json",
            success: function(e){
                if(e.code == 200){
                    var d = e.data || {};
                    var qq = d.service_qq || '';
                    var list = Array.isArray(d.buy_url) ? d.buy_url : [];
                    var clean = function(s){ return String(s || '').replace(/[`'"\s]/g,'').trim(); };
                    var linksHtml = '';
                    list.forEach(function(it){
                        var name = it.name || '官方授权渠道';
                        var url = clean(it.url);
                        if(url){
                            linksHtml += `
                                <a href="${url}" target="_blank" rel="noopener" class="em-modal-item">
                                    <div class="em-item-icon"><i class="layui-icon layui-icon-cart"></i></div>
                                    <div class="em-item-content">
                                        <span class="em-item-title">${name}</span>
                                        <span class="em-item-sub">${url}</span>
                                    </div>
                                    <div class="em-item-action"><i class="layui-icon layui-icon-right"></i></div>
                                </a>
                            `;
                        }
                    });
                    
                    var isMobile = window.innerWidth < 640;
                    var area = isMobile ? ['90%', 'auto'] : ['520px', 'auto'];
                    var desc = qq ? ('官方客服QQ：' + qq) : '请通过官方认证渠道获取正版授权';
                    
                    var content = `
                        <div class="em-modal-box">
                            <div class="em-modal-close-btn" onclick="layer.close(layer.index)">
                                <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                            </div>
                            <div class="em-modal-header">
                                <div class="em-modal-icon-wrapper"><i class="layui-icon layui-icon-diamond"></i></div>
                                <div class="em-modal-title">获取正版授权码</div>
                                <div class="em-modal-desc">${desc}</div>
                            </div>
                            <div class="em-modal-body">
                                <div class="em-modal-list">
                                    ${linksHtml}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    layer.open({
                        type: 1,
                        title: false,
                        closeBtn: 0,
                        area: area,
                        shadeClose: true,
                        skin: 'em-modal-skin',
                        btn: false,
                        content: content
                    });
                }else{
                    layer.msg(e.msg, {icon: 2});
                }
            },
            error: function(xhr, textStatus, errorThrown){
                layer.msg('网络请求超时，请重试或更换其他线路~', {icon: 2});
            },
            complete: function(){
                layer.close(loadIndex);
            }
        });
    })


    $(function () {
        $(document).on('click', 'a.scroll-to-top', function (e) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: ($($anchor.attr('href')).offset().top)
            }, 1000, 'easeInOutExpo');
            e.preventDefault();
        });



        // 初始化
        const menu = $("#left-menu");
        const overlay = $(".overlay");
        const toggleBtn = $(".show-nav");
        overlay.css({ opacity: 0.06, display: "none" });

        // 切换菜单
        toggleBtn.click(function() {
            $('#content').addClass('toright')
            $('#left-menu').addClass('toright')
            overlay.show();
        });

        // 点击遮罩层关闭菜单
        overlay.click(function() {
            $('#content').removeClass('toright')
            $('#left-menu').removeClass('toright')
            overlay.hide();
        });

    });
</script>
</body>
</html>
