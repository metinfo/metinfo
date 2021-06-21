<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('news/admin/news_admin');

class img_admin extends news_admin
{
    public $shop;
    public $module;

    function __construct()
    {
        global $_M;
        parent::__construct();
        $this->module = 5;
        $this->database = load::mod_class('img/img_database', 'new');

    }

    /**
     * 增加
     */
    function doadd()
    {
        global $_M;
        return parent::doadd();
    }

    function doaddsave()
    {
        global $_M;
        return parent::doaddsave();
    }

    public function insert_list($list = array())
    {
        global $_M;;
        return parent::insert_list($list);
    }

    /**
     *系统属性
     */
    public function dopara()
    {
        return parent::dopara();
    }

    /*产品编辑*/
    function doeditor()
    {
        global $_M;
        $list = $this->database->get_list_one_by_id($_M['form']['id']);
        $list = $this->listAnalysis($list);
        $list['imgurl_all'] = $list['imgurl'];
        $displayimg = explode("|", $list['displayimg']);
        foreach ($displayimg as $val) {
            $img = explode("*", $val);
            $list['imgurl_all'] .= '|' . $img[1];
        }
        $list['imgurl_all'] = trim($list['imgurl_all'], '|');
        $access_option = $this->access_option($list['access']);
        $column_list = $this->_columnjson();

        $redata['list'] = $list;
        $redata['access_option'] = $access_option;
        $redata = array_merge($redata, $column_list);
        if (is_mobile()) {
            $this->success($redata);
        } else {
            return $redata;
        }
    }

    function doeditorsave()
    {
        global $_M;
        return parent::doeditorsave();
    }

    public function update_list($list = array(), $id = '')
    {
        global $_M;
        $list['displayimg'] = $this->displayimg_check($list['displayimg']);
        return parent::update_list($list, $id);
    }

    /**
     *列表数据
     */
    function dojson_list()
    {
        global $_M;
        parent::dojson_list();
    }


    function dolistsave()
    {
        global $_M;
        return parent::dolistsave();
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
