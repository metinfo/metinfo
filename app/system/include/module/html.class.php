<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class html extends web
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->html_handle = load::mod_class('html/html_handle', 'new');
    }

    /**
     * 启动定时任务(定时跟新静态页)
     */
    public function doSetval()
    {
        global $_M;
        $limit = 3600;
        $url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doSetval&lang={$_M['lang']}";
        //开启静态且自动更新
        if ($_M['config']['met_html_auto'] && $_M['config']['met_webhtm']) {
            self::updatePage();
            sleep($limit);
            $stream_opts = array(
                "ssl" => array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false
                )
            );
            file_get_contents($url.'&time='.time(),false, stream_context_create($stream_opts));
        }
        return;
    }

    /**
     * 定时更新
     */
    public function updatePage()
    {
        global $_M;
        $index = $this->html_handle->homePage();    //首页静态文件
        $static_file = PATH_WEB . $index['filename'];
        if (is_file($static_file)) {
            $filemtime = filemtime($static_file);
        }else{
            $filemtime = strtotime("-1 hour");
        }

        switch ($_M['config']['met_html_auto']) {
            case 1://daily
                $offset = 3600 * 24;
                break;
            case 2://weekly
                $offset = 3600 * 24 * 7;
                break;
            case 3://monthly
                $offset = 3600 * 24 * 7 * 30;
                break;
            default:
                return;
                break;
        }
        $expires_in = $filemtime + $offset;

        if ($expires_in > time()){//有效期内
            return;
        };

        $hour = intval(date('H'));
        if (!($hour <= 4)) {//每天4点前更新静态文件
            return;
        }

        $pageinfo = array();
        $pageinfo[] = $this->html_handle->homePage();
        $pageinfo = $this->html_handle->getPageInfo($pageinfo, '', '', '', '', 1, '');
        $pages = $this->html_handle->getQueryList($pageinfo);
        foreach ($pages as $key => $rwo) {
            if (!strstr($rwo['url'], '../')) {
                continue;
            }
            $page['url'] = $_M['url']['web_site'] . str_replace(array('../',"..%2F"),'',$rwo['url']);
            $page['filename'] = str_replace('../','',$rwo['filename']);
            $pages[$key] = $page;
        }

        //静态页列表写入缓存
        Cache::del("static_list_suc_" . $_M['lang']);
        Cache::del("static_list_err_" . $_M['lang']);
        Cache::put("static_list_" . $_M['lang'], $pages);

        $loop_url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doLoop&lang={$_M['lang']}";

        self::request($loop_url);
        return;
    }

    /**
     * @param $url
     * @param array $param
     */
    protected function request($url ,$param = array())
    {
        global $_M;
        $urlinfo = parse_url($url);
        $host = $urlinfo['host'];
        $path = $urlinfo['path'];
        $query = $urlinfo['query'];
        //$query = isset($param)? http_build_query($param) : '';

        $port = $urlinfo == 'https' ? 443 : 80;
        $errno = 0;
        $errstr = '';
        $timeout = 10;

        if (!function_exists('fsockopen')) {
            return;
        }
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        $out = "POST ".$path." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n";
        $out .= "content-length:".strlen($query)."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;

        fputs($fp, $out);
        fclose($fp);
        return;
    }

    /**
     * 循环生成静态页
     */
    public function doLoop()
    {
        global $_M;
        $limit = 1;
        $loop_url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doLoop&lang={$_M['lang']}";

        //开启静态且自动更新
        if ($_M['config']['met_webhtm']) {
            if (self::createPage() === true) {
                sleep($limit);
                //loop
                $stream_opts = array(
                    "ssl" => array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false
                    )
                );
                file_get_contents($loop_url.'&time='.time(),false, stream_context_create($stream_opts));
            }
            //finished
            jsoncallback("Finished");
            return;
        }
        return;
    }

    /**
     * @return bool
     */
    public function createPage()
    {
        global $_M;
        $page_list = cache::get("static_list_{$_M['lang']}");
        $suc = Cache::get("static_list_suc_{$_M['lang']}") ?: array();
        $err = Cache::get("static_list_err_{$_M['lang']}") ?: array();

        if (!$page_list) {
            return false;
        }

        $request_list = array();
        $urls = array();
        $limit = 10;

        for ($i = 1; $i <= $limit; $i++) {
            if (!$page_list) {
                break;
            }
            $res = array_shift($page_list);
            $request_list[] = $res;
            $urls[] = $res['url'];
        }

        //页面生成请求
        $res = self::curl_request($urls);

        foreach ($res as $key => $row) {
            $result = json_decode($row, true);
            if ($result && $result['suc'] == 1) {
                $suc[] = $request_list[$key];
            } else {
                $err[] = $request_list[$key];
            }
        }

        Cache::put("static_list_suc_{$_M['lang']}", $suc);
        Cache::put("static_list_err_{$_M['lang']}", $err);
        Cache::put("static_list_{$_M['lang']}", $page_list);
        return true;
    }

    /**
     * @param array $urls
     * @return array|bool
     */
    protected function curl_request($urls = array())
    {
        if (!$urls) {
            return false;
        }

        $mh = curl_multi_init();

        foreach ($urls as $i => $url) {
            $conn[$i] = curl_init($url);
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($conn[$i], CURLOPT_HEADER, 0);
            curl_multi_add_handle($mh, $conn[$i]);
        }

        $active = null;
        do {
            $status = curl_multi_exec($mh, $active);
            //$info = curl_multi_info_read($mh);
            //if (false !== $info) {
            //    $handle = $info['handle'];
            //}
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        $res = array();
        foreach ($urls as $i => $url) {
            $result = curl_multi_getcontent($conn[$i]);
            $res[$i] = $result;
            curl_close($conn[$i]);
        }

        return $res;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
