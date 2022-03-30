<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 基础标签类
 */

class tag_label
{

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->lang = $_M['lang'];
    }

    /**
     * 栏目列表内容列表
     * 共用<tag action="list">
     * @param  string $cid 栏目id
     * @param  string $num 数量
     * @param  string $type com/news/all
     */
    public function get_list($cid, $num, $cond = null, $order = null, $para = false)
    {//新增字段调用参数
        global $_M;
        if (is_numeric($cid)) {
            $c = load::sys_class('label', 'new')->get('column')->get_column_id($cid);
            $module = load::sys_class('handle', 'new')->mod_to_file($c['module']);
        } else {
            return false;
            //$module = $cid;
        }
        if (load::sys_class('handle', 'new')->file_to_mod($module)) {
            if (in_array($module, array('feedback', 'member', 'sitemap', 'tags'))) {
                return false;
            }
            $module_label = load::sys_class('label', 'new')->get($module);
            if (method_exists($module_label, 'get_module_list')) {
                return $module_label->get_module_list($cid, $num, $cond, $order, $para);
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * 分页按钮
     * <pager>标签
     * @param string $classnow
     * @param string $pagenow
     * @param string $page_type
     * @return bool
     */
    public function get_page_html($classnow = '', $pagenow = '', $page_type = '')
    {
        global $_M;
        if (is_numeric($classnow)) {
            $c = load::sys_class('label', 'new')->get('column')->get_column_id($classnow);
            $module = load::sys_class('handle', 'new')->mod_to_file($c['module']);
        } else {
            return false;
        }

        if (load::sys_class('handle', 'new')->file_to_mod($module)) {
            if (in_array($module, array('feedback', 'member', 'sitemap', 'tags'))) {
                return false;
            }

            $module_label = load::sys_class('label', 'new')->get($module);
            if (method_exists($module_label, 'get_list_page_html')) {
                return $module_label->get_list_page_html($classnow, $pagenow, $page_type);
            }
        } else {
            return false;
        }
    }

    /**
     * 搜索功能调用
     * 搜索模块获取列表页面url
     * @param  string $mod 栏目id
     * @param  string $page 当前分页
     */
    public function get_list_page_url($classnow = '', $pagenow = '')
    {
        global $_M;
        if (is_numeric($classnow)) {
            $c = load::sys_class('label', 'new')->get('column')->get_column_id($classnow);
            $module = load::sys_class('handle', 'new')->mod_to_file($c['module']);
        } else {
            return false;
        }
        if (load::sys_class('handle', 'new')->file_to_mod($module)) {
            $module_label = load::sys_class('label', 'new')->get($module);
            if (method_exists($module_label, 'get_page_url')) {
                $url = $module_label->get_page_url($classnow, 1);
                return $module_label->handle->replace_list_page_url($url,$pagenow);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 分页列表数据
     * @param  string $cid 模块名称或id
     * @param  string $page 分页
     */
    public function get_page($cid, $page)
    {
        global $_M;
        if (is_numeric($cid)) {
            $c = load::sys_class('label', 'new')->get('column')->get_column_id($cid);
            $module = load::sys_class('handle', 'new')->mod_to_file($c['module']);
        } else {
            return false;
        }

        if (load::sys_class('handle', 'new')->file_to_mod($module)) {
            if (in_array($module, array('feedback', 'member', 'sitemap', 'tags'))) {
                return false;
            }

            $module_label = load::sys_class('label', 'new')->get($module);
            if (method_exists($module_label, 'get_list_page')) {
                return $module_label->get_list_page($cid, $page);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $cid
     * @param $pagenow
     * @return bool
     */
    public function get_page_select($cid, $pagenow)
    {
        global $_M;
        if (is_numeric($cid)) {
            $c = load::sys_class('label', 'new')->get('column')->get_column_id($cid);
            $module = load::sys_class('handle', 'new')->mod_to_file($c['module']);
        } else {
            return false;
        }
        if (load::sys_class('handle', 'new')->file_to_mod($module)) {
            $module_label = load::sys_class('label', 'new')->get($module);
            if (method_exists($module_label, 'get_list_page_select')) {
                return $module_label->get_list_page_select($cid, $pagenow);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
