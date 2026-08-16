<?php


class Store_Model {


    public function getList($type, $page, $pageNum, $keyword, $sid) {
        $url = TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=store';
        $data = [
            'type'      => $type,
            'keyword'   => $keyword,
            'page'      => $page,
            'pageNum'   => $pageNum,
            'sid'       => $sid,
            'host'    => getTopHost(),
            'ttkey'    => getMyTtKey(),
        ];
        
        $res = ttCurl($url, http_build_query($data), true, [], 6);
        // echo $res;die;
        $res = json_decode($res, true);

        // d($res);die;

        $list = $res['data']['list'];
        $count = $res['data']['count'];

        return [
            'list' => $list,
            'count' => $count,
        ];
    }

    public function getApps($keyword, $page, $sid) {
        return $this->reqEmStore('all', $keyword, $page, $sid);
    }

    public function getTemplates($tag, $keyword, $page, $author_id, $sid) {
        return $this->reqEmStore('tpl', $tag, $keyword, $page, $author_id, $sid);
    }

    public function getStationTemplates($tag, $keyword, $page, $author_id, $sid, $unique) {
        return $this->reqEmStoreStation('tpl', $tag, $keyword, $page, $author_id, $sid, $unique);
    }

    public function getPlugins($tag, $keyword, $page, $author_id, $sid) {

        return $this->reqEmStore('plu', $tag, $keyword, $page, $author_id, $sid);
    }

    public function getMyAddon() {
        return $this->reqEmStore('mine');
    }

    public function getSvipAddon() {
        return $this->reqEmStore('svip');
    }

    public function getTopAddon() {
        return $this->reqEmStore('top');
    }

    public function reqEmStore($type, $keyword = '', $page = 1, $author_id = 0, $sid = 0) {
        $ttcurl = new EmCurl();

        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $domain = getDomain();
        $sql = "select * from {$db_prefix}authorization where domain='{$domain}'";
        $res = $db->once_fetch_array($sql);
        $ttkey =  empty($res) ? false : $res['ttkey'];

        $post_data = [
            'ttkey'     => $ttkey,
            'ver'       => Option::TT_VERSION,
            'type'      => $type,
            'keyword'   => $keyword,
            'page'      => $page,
            'author_id' => $author_id,
            'sid'       => $sid
        ];

        $url = "TT_LINE[CURRENT_LINE]['value'] . 'api/emshop.php?action=store'";




        

        $data = [];
        switch ($type) {
            case 'all':
                $data['apps'] = isset($ret['data']['apps']) ? $ret['data']['apps'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                break;
            case 'tpl':
                $data['templates'] = isset($ret['data']['templates']) ? $ret['data']['templates'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                break;
            case 'plu':
                $data['plugins'] = isset($ret['data']['plugins']) ? $ret['data']['plugins'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                break;
            case 'svip':
            case 'mine':
            case 'top':
                $data = isset($ret['data']) ? $ret['data'] : [];
                break;
        }
//        d($data);die;
        return $data;
    }

    public function reqEmStoreStation($type, $tag = '', $keyword = '', $page = 1, $author_id = 0, $sid = 0, $unique = 0) {
        $ttcurl = new EmCurl();

        $db = Database::getInstance();
        $db_prefix = DB_PREFIX;
        $sql = "select * from {$db_prefix}authorization where type < 3 order by type desc";
        $res = $db->once_fetch_array($sql);
        $ttkey =  empty($res) ? false : $res['ttkey'];

        $post_data = [
            'ttkey'     => $ttkey,
            'ver'       => Option::TT_VERSION,
            'type'      => $type,
            'tag'       => $tag,
            'keyword'   => $keyword,
            'page'      => $page,
            'author_id' => $author_id,
            'sid'       => $sid,
            'station_unique' => $unique
        ];
        $ttcurl->setPost($post_data);
        $ttcurl->request(TT_LINE[CURRENT_LINE]['value'] . 'api/store/index');


        $retStatus = $ttcurl->getHttpStatus();

        if ($retStatus !== MSGCODE_SUCCESS) {
            ttDirect("./store.php?action=error&error=1");
        }

        $response = $ttcurl->getRespone();
        $ret = json_decode($response, 1);


        if (empty($ret)) {
            ttDirect("./store.php?action=error&error=1");
        }
        if ($ret['code'] === MSGCODE_TTKEY_INVALID) {
            Option::updateOption('ttkey', '');
            $CACHE = Cache::getInstance();
            $CACHE->updateCache('options');
            ttDirect("./auth.php?error_store=1");
        }

        $data = [];
        switch ($type) {
            case 'all':
                $data['apps'] = isset($ret['data']['apps']) ? $ret['data']['apps'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                break;
            case 'tpl':
                $data['templates'] = isset($ret['data']['templates']) ? $ret['data']['templates'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                break;
            case 'plu':
                $data['plugins'] = isset($ret['data']['plugins']) ? $ret['data']['plugins'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                break;
            case 'svip':
            case 'mine':
            case 'top':
                $data = isset($ret['data']) ? $ret['data'] : [];
                break;
        }
//        d($data);die;
        return $data;
    }

}
