<?php

/**
 * 在线更新（后台首页那个「发现新版本」弹窗 + 执行更新）。
 *
 * 2026-10-05 改走新协议：原来这两个 action 一共打了四个旧接口
 * （`is_new_version` / `update_sql` / `update_zip` / `app_upgrade_num_inc`），
 * 现在都从**一条** `base-data` 的版本块来（见 `include/service/basedataservice.php`）。
 *
 * ⚠️ TTSHOP 的更新是**两步**：先跑**更新 SQL** 改表结构、再覆盖程序文件。
 *    所以服务端那条 base-data 除了安装包/更新包，还得给 SQL 的地址
 *    （`sql_package_url`，只有 TTSHOP 有这一列，见服务端迁移 022）。
 *
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

// 检测更新
if ($action === 'check_update') {
    header('Content-Type: application/json; charset=UTF-8');

    try {
        $data = BaseDataService::fetch();
    } catch (RuntimeException $e) {
        die(json_encode(['code' => 400, 'msg' => '检查更新失败：' . $e->getMessage()], JSON_UNESCAPED_UNICODE));
    }

    if (empty($data['has_new_version'])) {
        /*
         * ⚠️ **没新版要回 400**：前端只在 `code == 200` 时开弹窗 ——
         * 回 200 + 空列表的话，用户会看到一个「发现新版本」但里面什么都没有的窗。
         */
        die(json_encode(['code' => 400, 'msg' => '已是最新版本'], JSON_UNESCAPED_UNICODE));
    }

    /* 整包优先用**更新包**（增量、小），没有就用安装包（全量）*/
    $zip = $data['patch_package_url'] !== '' ? $data['patch_package_url'] : $data['install_package_url'];

    /* 形状照前端 `openUpdateModal` 要的：list[].version / .content，外加两个文件地址 */
    die(json_encode([
        'code' => 200,
        'msg'  => 'ok',
        'data' => [
            'list' => [[
                'version' => (string) $data['latest_version'],
                'content' => (string) $data['latest_changelog'],
            ]],
            /* 这两个是**给人看 / 手动下**的地址，所以拼成完整的（服务端给的是根相对路径）*/
            'cdn_sql'  => LicenseClient::absolute((string) $data['sql_package_url']),
            'cdn_file' => LicenseClient::absolute((string) $zip),
        ],
    ], JSON_UNESCAPED_UNICODE));
}

// 执行更新
if ($action === 'update' && User::isAdmin()) {
    /* 进页面前问一次服务端（和商店那一组同一个口径）*/
    if (!Register::isRegServer()) {
        Ret::error('未授权域名，无法使用在线更新服务');
    }

    try {
        $data = BaseDataService::fetch();
    } catch (RuntimeException $e) {
        Ret::error('检查更新失败：' . $e->getMessage());
    }

    if (empty($data['has_new_version'])) {
        Ret::error('已是最新版本，无需更新');
    }

    $zipUrl = LicenseClient::absolute(
        $data['patch_package_url'] !== '' ? $data['patch_package_url'] : $data['install_package_url']
    );

    if ($zipUrl === '') {
        Ret::error('这一版没有上传更新包，请联系官方');
    }

    /*
     * ── 第一步：更新 SQL（**TTSHOP 独有**，没配就跳过）──────────────
     * ⚠️ **必须在覆盖程序文件之前跑**：新的程序代码可能要用新的表结构。
     */
    $sqlUrl = LicenseClient::absolute(isset($data['sql_package_url']) ? $data['sql_package_url'] : '');

    if ($sqlUrl !== '') {
        $temp_sql_file = ttFetchFile($sqlUrl);

        if (!$temp_sql_file) {
            Ret::error('数据库更新文件下载失败');
        }

        $DB = Database::getInstance();
        $setchar = 'ALTER DATABASE `' . DB_NAME . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
        $lines = file($temp_sql_file);
        $statements = [];
        $query = '';

        $lines = $lines === false ? [] : $lines;
        array_unshift($lines, $setchar);

        foreach ($lines as $line) {
            /* `# version x.y.z` 是**分段标记**：比本机还新的那些段才是要跑的 */
            if (!empty($line) && $line[0] == '#') {
                preg_match('/#\s(version\s[\.\d]+)/i', $line, $m);
                $ver = isset($m[1]) ? trim($m[1]) : '';

                if (version_compare('version ' . Option::TT_VERSION, $ver) > 0) {
                    break;
                }
            }

            if (!$line || $line[0] == '#') {
                continue;
            }

            $value = str_replace('{db_prefix}', DB_PREFIX, trim($line));
            $query .= $value;

            if (preg_match('/\;$/i', $value)) {
                $statements[] = $query;
                $query = '';
            }
        }

        /* 第一条先跑（历史遗留的顺序：当年的脚本依赖这个次序）*/
        if (!empty($statements)) {
            $first = array_shift($statements);
            $DB->query($first, 1);

            foreach (array_reverse($statements) as $statement) {
                $DB->query($statement, 1);
            }
        }

        $CACHE->updateCache();
        @unlink($temp_sql_file);
    }

    /* ── 第二步：覆盖程序文件 ───────────────────────────────── */
    $temp_zip_file = ttFetchFile($zipUrl);

    if (!$temp_zip_file) {
        Ret::error('更新包下载失败');
    }

    $ret = ttUnZip($temp_zip_file, '../', 'update');
    @unlink($temp_zip_file);

    switch ($ret) {
        case 1:
        case 2:
            Ret::error('更新失败，目录不可写，请设置您的站点目录权限');
        case 3:
            Ret::error('解压更新失败，可能是您的 PHP 未安装 zip 扩展（ZipArchive）');
    }

    /* ⚠️ 旧的 `Ret::success('', '更新成功')` 把消息和数据写反了（第一个参数才是 msg，
       前端弹的也是 msg）—— 顺手改对 */
    Ret::success('更新成功');
}
