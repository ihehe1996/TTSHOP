<?php
defined('TT_ROOT') || exit('access denied!');
?>
<style>
    /* 主内容区样式 */
    .main-content {
        padding: 28px;
    }

    .panel-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .panel-body {
        padding: 28px;
    }

    .form-title {
        margin: 0 0 22px;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    /* 表单样式优化 */
    .layui-form {
        max-width: 860px;
    }

    .layui-form-item {
        margin-bottom: 18px;
    }

    .layui-form-label {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
    }

    .layui-input {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .layui-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        background: #ffffff;
    }

    .layui-textarea {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.9);
        resize: vertical;
        min-height: 110px;
    }

    .layui-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        background: #ffffff;
    }

    /* 单选按钮样式 */
    .layui-form-radio {
        margin-right: 16px;
        margin-bottom: 8px;
    }

    .layui-form-radio i {
        font-size: 16px;
        color: var(--primary);
    }

    .layui-form-radioed i {
        color: var(--primary);
    }

    /* 下拉选择框 */
    .layui-form-select {
        border-radius: 12px;
        background: transparent;
    }

    .layui-form-select dl {
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
    }

    /* 域名组合输入 */
    .domain-fields {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }


    .domain-suffix {
        flex: 0 0 180px;
    }

    .domain-fields .layui-form-select {
        width: 100%;
    }

    /* 输入框后缀 */
    .input-suffix {
        position: relative;
        display: inline-block;
        width: 120px;
    }

    .input-suffix .layui-input {
        padding-right: 26px;
        width: 100%;
    }

    .input-suffix .suffix {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 12px;
        pointer-events: none;
    }

    /* 提交按钮 */
    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-strong) 100%);
        color: white;
        border: none;
        padding: 12px 34px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 10px 26px rgba(15, 118, 110, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 36px rgba(15, 118, 110, 0.35);
    }

    .btn-submit:active {
        transform: translateY(0);
        box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
    }

    /* TinyMCE编辑器样式 */
    .tox-tinymce {
        border-radius: 12px !important;
        border: 1px solid var(--border) !important;
        overflow: hidden;
    }

    .tox-toolbar__group {
        background: rgba(255, 255, 255, 0.9);
    }

    .tox-edit-area__iframe {
        background: white !important;
    }

    /* 响应式优化 */
    @media (max-width: 768px) {
        .main-content {
            padding: 20px 14px 28px;
        }
        
        .panel-body {
            padding: 22px 18px;
        }
        
        .layui-form {
            max-width: 100%;
        }
        
        .domain-fields {
            flex-direction: column;
            align-items: stretch;
        }

        .domain-prefix,
        .domain-suffix {
            flex: 1 1 auto;
            width: 100%;
        }

        .input-suffix {
            width: 100%;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px 20px;
        }
    }

    @media (max-width: 480px) {
        .panel-body {
            padding: 18px 14px;
        }
        
        .layui-form-label {
            float: none;
            width: auto;
            margin-bottom: 8px;
            text-align: left;
        }
        
        .layui-input-block {
            margin-left: 0;
        }
        
        .btn-submit {
            font-size: 14px;
        }
    }
</style>

<!-- 主内容区 -->
<main class="main-content">
    <div class="layui-panel panel-card">
        <div class="panel-body">
            <h3 class="form-title">基础设置</h3>

            <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">店铺名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" value="<?= $userStation['name'] ?>" placeholder="请输入店铺名称" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">网站标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" value="<?= $userStation['title'] ?>" placeholder="请输入网站标题" class="layui-input">
                </div>
                <span class="form-tips">显示在网页浏览器上的标题</span>
            </div>
            <!-- <div class="layui-form-item">
                <label class="layui-form-label">主站分类</label>
                <div class="layui-input-block">
                    <input type="radio" name="master_sort" <?= $userStation['master_sort'] == 1 ? 'checked' : '' ?> value="1" title="全部显示" >
                    <input type="radio" name="master_sort" <?= $userStation['master_sort'] == 2 ? 'checked' : '' ?> value="2" title="全部隐藏">
                    <input type="radio" name="master_sort" <?= $userStation['master_sort'] == 3 ? 'checked' : '' ?> value="3" title="自定义（在主站分类菜单中配置）">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">主站商品</label>
                <div class="layui-input-block">
                    <input type="radio" name="master_goods" <?= $userStation['master_goods'] == 1 ? 'checked' : '' ?> value="1" title="全部显示">
                    <input type="radio" name="master_goods" <?= $userStation['master_goods'] == 2 ? 'checked' : '' ?> value="2" title="全部隐藏">
                    <input type="radio" name="master_goods" <?= $userStation['master_goods'] == 3 ? 'checked' : '' ?> value="3" title="自定义（在主站商品菜单中配置）">
                </div>
            </div> -->
            <div class="layui-form-item">
                <label class="layui-form-label">二级域名</label>
                <div class="layui-input-block">
                    <div class="domain-fields">
                        <div class="domain-prefix">
                            <input style="width: 130px;" type="text" value="<?= $userStation['domain_2_prefix'] ?>" name="domain_2_prefix" placeholder="域名前缀" class="layui-input">
                        </div>
                        <div class="domain-suffix">
                            <select name="domain_2_suffix">
                                <?php foreach($station_domain as $val): ?>
                                <option value=".<?= $val; ?>" <?= $userStation['domain_2_suffix'] == '.' . $val ? 'selected' : '' ?>>.<?= $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">独立域名</label>
                <div class="layui-input-block">
                    <input type="text" name="domain" value="<?= $userStation['domain'] ?>" placeholder="请输入您的域名" class="layui-input">
                    <span class="form-tips">cname解析域名：<?= Option::get('station_cname_domain') ?></span>
                </div>
            </div>

            <?php
            $goods_premium_percent = '';
            if (isset($userStation['goods_premium']) && is_numeric($userStation['goods_premium'])) {
                $goods_premium_percent = rtrim(rtrim(sprintf('%.4f', $userStation['goods_premium'] * 100), '0'), '.');
            }
            ?>
            <div class="layui-form-item">
                <label class="layui-form-label">商品统一加价比例</label>
                <div class="layui-input-block">
                    <div class="input-suffix">
                        <input type="number" name="goods_premium" min="0.1" step="0.01" inputmode="decimal" value="<?= $goods_premium_percent ?>" placeholder="最小 0.1" class="layui-input">
                        <span class="suffix">%</span>
                    </div>
                    <span class="form-tips">佣金 = 实际成交价 - 您的拿货价</span>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">滚动公告</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" name="roll_notice" class="layui-textarea"><?= $userStation['roll_notice'] ?></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">内容公告</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" id="home_notice" name="home_notice" class="layui-textarea"><?= $userStation['home_notice'] ?></textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="btn-submit" lay-submit lay-filter="submit">保存信息</button>
                </div>
            </div>
            </form>
        </div>
    </div>


</main>

<script src="<?= TT_URL ?>admin/tinymce/tinymce.min.js?t=<?= Option::TT_VERSION_TIMESTAMP ?>"></script>
<script>
    // 初始化Layui模块
    layui.use(['layer', 'form'], function() {
        var layer = layui.layer;
        var form = layui.form;

        form.on('submit(submit)', function(data){
            var field = data.field; // 获取表单全部字段值
            if (typeof field.goods_premium !== 'undefined') {
                var premiumPercent = parseFloat(field.goods_premium);
                if (!isFinite(premiumPercent)) {
                    layer.msg('请输入有效的加价比例');
                    return false;
                }
                if (premiumPercent < 0.1) {
                    layer.msg('加价比例最低为 0.1%');
                    return false;
                }
                field.goods_premium = (premiumPercent / 100).toString();
            }
            $.ajax({
                type: "POST",
                url: "?action=setting_ajax",
                data: field,
                dataType: "json",
                success: function (e) {
                    if(e.code == 400){
                        layer.msg(e.msg)
                    }else{
                        layer.msg(e.msg)
                    }

                },
                error: function (xhr) {
                    layer.msg('网络请求发生错误，请重试')
                },
                complete: function(){

                }
            });
            return false; // 阻止默认 form 跳转
        });

        const example_image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/admin/article.php?action=upload_cover2');
            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };
            xhr.onload = () => {
                if (xhr.status === 403) {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                    return;
                }
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }
                const json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                resolve(json.location);
            };
            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };
            const formData = new FormData();
            formData.append('image', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        });
        tinymce.init({
            selector: 'textarea#home_notice',
            language: 'zh_CN',
            height: 300,
            images_upload_handler: example_image_upload_handler,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'wordcount', 'autosave'
            ],
            autosave_ask_before_unload: false,
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',



            // 添加初始化完成后的回调
            setup: function(editor) {
                editor.on('init', function() {
                    // 保存编辑器实例到全局变量
                    editorInstance = editor;
                    console.log('TinyMCE 初始化完成');
                });
                editor.on('input change undo redo cut paste', function() {
                    // 手动更新关联的文本域
                    editor.save();
                });
            }
        }).then(function(editors) {
            // 可选：Promise 方式获取编辑器实例
            if (editors && editors.length > 0) {
                editorInstance = editors[0];
            }
        }).catch(function(error) {
            console.error('TinyMCE 初始化失败:', error);
        });

    });
</script>

<script>
    $('#menu-station').addClass('open');
    $('#menu-station > ul').css('display', 'block');
    $('#menu-station > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-station-setting').addClass('menu-current');
</script>
