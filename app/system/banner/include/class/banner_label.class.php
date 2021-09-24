<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * banner标签类
 */

class banner_label
{

    public $lang;
    public $handle;
    public $banner_database;
    public $banner_content_database;

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->lang = $_M['lang'];
        $this->database = load::mod_class('banner/banner_database', 'new');
        $this->handle = load::mod_class('banner/banner_handle', 'new');
    }


    /**
     * 获取所有栏目benner图片列表//兼容系统v5
     * @return array         banner图片列表
     */
    public function get_img()
    {
        $banner = $this->database->get_banner_img_by_lang($this->lang);
        return $this->handle->img_para_handle($banner);
    }

    /**
     * 获取指定栏目banner图片和配置//系统v6使用
     * @param  string $id 栏目id
     * @return array          指定栏目banner图片和配置数组
     */
    public function get_column_banner($column_id)
    {
        global $_M;
        //页面如果没有type类型，就采用默认设置，兼容v5代码
        $banner['config']['type'] = 1;
        $banner['config']['y'] = 1000;

        //Banner列表
        $banner['img'] = $this->handle->img_para_handle($this->database->get_banner_img_by_column($column_id, $this->lang));

        return $banner;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
