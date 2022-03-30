<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class access extends web
{

    public function doinfo()
    {
        global $_M;
        $str = urldecode(load::sys_class('auth', 'new')->decode($_M['form']['str']));
        $groupid = urldecode(load::sys_class('auth', 'new')->decode($_M['form']['groupid']));
        $power = load::sys_class('user', 'new')->check_power($groupid);
        $lang = $_M['form']['lang'] ? $_M['form']['lang'] : $_M['lang'];
        $str = htmlspecialchars($str);
        if ($power > 0) {
            echo 'document.write("' . $str . '")';
        } else {
            if ($power == -2) {//用户未登录
                load::mod_class('user/user_url', 'new')->insert_m();
                $url_login = $_M['url']['login'];
                $url_register = $_M['url']['register'];
                echo 'document.write("' . "【<a href='" . $url_login . "' target='_blank'>{$_M['word']['login']}</a>】【<a href='" . $url_register . "' target='_blank'>{$_M['word']['register']}</a>】" . '")';
                die();
            }
            if ($power == -1) {//用户已登录无权限
                $str = "【<span>{$_M['word']['access']}</span>】";
                echo "document.write('{$str}')";
                die();
            }
        }
    }

    public function dodown()
    {
        global $_M;
        $id = $_M['form']['id'] ?: '';
        $lang = $_M['form']['lang'] ?: $_M['lang'];

        $sql = "SELECT * FROM {$_M['table']['download']} WHERE id = '{$id}'";
        $data = DB::get_one($sql);
        if (!$data) {
            abort();
        }
        //$url = urldecode(load::sys_class('auth', 'new')->decode($_M['form']['url']));
        $downloadaccess = $data['downloadaccess'] ?: 0;
        $power = load::sys_class('user', 'new')->check_power($downloadaccess);
        if ($power > 0) {
            if ($data && $data['downloadurl'] && substr($data['downloadurl'],0,10) == '../upload/'){
                $filePath = str_replace('../', PATH_WEB, $data['downloadurl']);
                if (!file_exists($filePath)) {
                    die("404");
                }
                $basename = basename($filePath);
                $fileSize = filesize($filePath);
                ob_clean();
                header('Content-Type:application/octet-stream');
                header('Accept-Ranges:bytes');
                header("Content-length:" . $fileSize);
                header('Content-Disposition:attachment; filename="' . $basename . '"');
                readfile("{$filePath}");
                exit;
            }else{
                header("location:{$data['downloadurl']}");
            }
        } else {
            if ($power == -2) {//跳登录
                okinfo($_M['url']['web_site'] . 'member/login.php?lang=' . $lang, $_M['word']['systips1']);
            }
            if ($power == -1) {//跳首页
                $gourl = $_M['gourl'] ? base64_encode($_M['gourl']) : base64_encode($_M['url']['web_site']);
                okinfo($_M['url']['web_site'] . 'index.php?lang=' . $lang . '&gourl=' . $gourl, $_M['word']['systips2']);
            }
        }
    }

    public function dojump()
    {
        global $_M;
        $url = urldecode(load::sys_class('auth', 'new')->decode($_M['form']['url']));
        $groupid = urldecode(load::sys_class('auth', 'new')->decode($_M['form']['groupid']));
        $power = load::sys_class('user', 'new')->check_power($groupid);
        $lang = $_M['form']['lang'] ? $_M['form']['lang'] : $_M['lang'];
        $gourl = $_M['gourl'] ? base64_encode($_M['gourl']) : base64_encode($_M['url']['web_site']);
        if ($power > 0) {
            header("location:{$url}");
        } else {
            if ($power == -2) {//跳登录
                okinfo($_M['url']['web_site'] . 'member/login.php?lang=' . $lang, $_M['word']['systips1']);
            }
            if ($power == -1) {//跳首页
                okinfo($_M['url']['web_site'] . 'index.php?lang=' . $lang . '&gourl=' . $gourl, $_M['word']['systips2']);
            }
        }
    }

    public function dochekpage()
    {
        global $_M;
        $groupid = load::sys_class('auth', 'new')->decode($_M['form']['groupid']);
        $power = load::sys_class('user', 'new')->check_power($groupid);
        $lang = $_M['form']['lang'] ? $_M['form']['lang'] : $_M['lang'];
        $gourl = $_M['gourl'] ? base64_encode($_M['gourl']) : base64_encode($_M['url']['web_site']);
        if ($power > 0) {
            $this->success();
        } else {
            if ($power == -2) {//跳登录
                $this->error($_M['word']['systips1'], 0, $_M['url']['web_site'] . 'member/login.php?lang=' . $lang . '&gourl=' . $gourl, $_M['word']['systips1']);
            }
            if ($power == -1) {//跳首页
                $this->error($_M['word']['systips2'],0,$_M['url']['web_site'] . 'index.php?lang=' . $lang);
            }
        }
    }

    public function __destruct()
    {
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>