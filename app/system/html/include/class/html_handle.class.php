<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class html_handle
{
    public function __construct()
    {
        global $_M;

    }
    /**
     * @param array $pageinfo
     * @return array
     */
    public function getQueryList(array $pageinfo)
    {
        global $_M;
        $pages = array();
        foreach ($pageinfo as $key => $val) {
            $mod = load::sys_class('handle', 'new')->mod_to_file($val['module']);
            switch ($val['type']) {
                case 'column':
                    //文件目录
                    $path = pathinfo($val['filename']);
                    $html_dir = str_replace($_M['url']['web_site'], PATH_WEB, $path['dirname']);
                    if (!file_exists($html_dir)) {
                        mkdir($html_dir, 0777, true);
                    }

                    $page = 1;
                    while ($page <= $val['count']) {
                        $p = array();
                        $mod_label = load::sys_class('label', 'new')->get($mod);
                        if (!method_exists($mod_label->handle, 'replace_list_page_url')) {
                            break;
                        }
                        $static_url = $mod_label->handle->replace_list_page_url($val['filename'], $page, $val['id'], 3);
                        $filename = urlencode(str_replace($_M['url']['web_site'], '',$static_url));

                        $dynamic_url = $mod_label->handle->replace_list_page_url($val['url'], $page, $val['id'], 1);
                        $dynamic_url .= "&html_filename={$filename}&metinfonow={$_M['config']['met_member_force']}";

                        $p['url'] = str_replace('.php&', '.php?', $dynamic_url);
                        $p['filename'] = urldecode($filename);
                        $page++;
                        $pages[] = $p;

                        if ($_M['config']['met_webhtm'] == 3) {//混合模式仅生成第一页
                            break;
                        }
                    }
                    break;
                case 'content':
                    $p = array();
                    $filename = urlencode(str_replace($_M['url']['web_site'], '', $val['filename']));
                    $dynamic_url = $val['url'] . "&metinfonow={$_M['config']['met_member_force']}" . "&html_filename={$filename}";

                    $p['url'] = str_replace('.php&', '.php?', $dynamic_url);
                    $p['filename'] = urldecode($filename);
                    $pages[] = $p;
                    break;
                case 'tags':
                    //文件目录
                    $path = pathinfo($val['filename']);
                    $html_dir = str_replace($_M['url']['web_site'], PATH_WEB, $path['dirname']);
                    if (!file_exists($html_dir)) {
                        mkdir($html_dir, 0777, true);
                    }

                    $p = array();
                    $filename = urlencode(str_replace($_M['url']['web_site'], '', $val['filename']));
                    $dynamic_url = $val['url'] . "&metinfonow={$_M['config']['met_member_force']}" . "&html_filename={$filename}";

                    $p['url'] = str_replace('.php&', '.php?', $dynamic_url);
                    $p['filename'] = urldecode($filename);
                    $pages[] = $p;
                    break;
            }
        }
        return $pages;
    }

    /**
     * @param $pageinfo
     * @param string $type
     * @param string $module
     * @param string $list_page
     * @param string $class1
     * @param string $all
     * @param string $content
     * @return array
     */
    public function getPageInfo($pageinfo ,$type ,$module ,$list_page ,$class1 ,$all ,$content)
    {
        global $_M;
        //列表页链接
        $module_list = load::mod_class('column/column_op', 'new')->get_sorting_by_module(false, $_M['mark']);
        foreach ($module_list as $mod => $valm) {
            if (($all == 1 || $mod == $module) && in_array($mod, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13))) {
                //列表页
                if (
                    ($_M['config']['met_webhtm'] == 2 || $_M['config']['met_webhtm'] == 3 || $_M['config']['met_webhtm'] === '0')
                    && ($type == 'column' || $type == 'content' || $all == 1 || $list_page == 1)
                    && in_array($mod, array(2, 3, 4, 5, 6, 7))
                ) {
                    //循环栏目获取栏目分页链接
                    foreach ($valm['class1'] as $keyc1 => $valc1) {
                        if ($all == 1 || $valc1['id'] == $class1) {
                            $pageinfo[] = $this->getPage($valc1['id'], $valc1['module']);
                            foreach ($valm['class2'] as $keyc2 => $valc2) {
                                if ($valc2['bigclass'] == $valc1['id']) {
                                    $pageinfo[] = $this->getPage($valc2['id'], $valc2['module']);
                                }
                                foreach ($valm['class3'] as $keyc3 => $valc3) {
                                    if ($valc3['bigclass'] == $valc2['id']) {
                                        $pageinfo[] = $this->getPage($valc3['id'], $valc3['module']);
                                    }
                                }
                            }
                        }
                    }

                    foreach ($valm['class2'] as $keyc2 => $valc2) {
                        if ($valc2['module'] != 7) continue;
                        $pageinfo[] = $this->getPage($valc2['id'], $valc2['module']);
                    }
                }
            }

            //内容页面
            if ($type == 'content' || $all == 1) {
                //一级栏目
                foreach ($valm['class1'] as $keyc1 => $valc1) {
                    if ($class1 && $class1 != $valc1['id']) {
                        continue;
                    }
                    if (in_array($mod, array(2, 3, 4, 5, 6))) {
                        self::delClassHtml($valc1);
                        $pageinfo = array_merge((array)$pageinfo, (array)$this->getContentList($valc1['id'], $valc1['module']));
                    } else {
                        if ($class1 == $valc1['id'] || $all == 1) {
                            $pageinfo = array_merge((array)$pageinfo, (array)$this->indexPage($valc1));
                            if ($mod == 1) {
                                foreach ($valm['class2'] as $keyc2 => $valc2) {
                                    if ($valc2['bigclass'] == $valc1['id']) {
                                        $pageinfo = array_merge((array)$pageinfo, (array)$this->indexPage($valc2));
                                    }
                                    foreach ($valm['class3'] as $keyc3 => $valc3) {
                                        if ($valc3['bigclass'] == $valc2['id']) {
                                            $pageinfo = array_merge((array)$pageinfo, (array)$this->indexPage($valc3));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                //二级栏目内容页面
                foreach ($valm['class2'] as $keyc2 => $valc2) {
                    if (in_array($valc2['module'], array(9, 11, 12, 13))) {
                        $pageinfo = array_merge((array)$pageinfo, (array)$this->indexPage($valc2));
                    }
                }

                //内容管理添加或编辑内容时——重新生成列表页(自动更新)
                if ($content) {
                    if (in_array($mod, array(2, 3, 4, 5, 6, 13))) {
                        $pageinfo = array_merge((array)$pageinfo, (array)$this->getContentList($class1, $module));
                    }
                }
            }
        }
        return $pageinfo;
    }

    /**
     * 首页url
     * @return mixed
     */
    public function homePage()
    {
        global $_M;
        $page['url'] = $_M['url']['web_site'] . 'index.php?lang=' . $_M['lang'];
        $page['count'] = 0;
        $page['filename'] = 'index';
        if ($_M['config']['met_index_type'] != $_M['lang']) {
            $page['filename'] .= '_' . $_M['lang'];
        }
        $page['filename'] .= '.' . $_M['config']['met_htmtype'];
        $page['module'] = 0;
        $page['type'] = 'content';
        return $page;
    }

    /**
     * 获取列表列表页url
     * @param string $content
     * @return array|null
     */
    protected function indexPage($content = '')
    {
        if ($content['module'] == 0 || $content['isshow'] == 0) {
            return NULL;
        } else {
            $column_handle = load::mod_class('column/column_handle', 'new');
            $page['url'] = $column_handle->url_full($content, 1);
            $page['count'] = 0;
            $page['filename'] = $column_handle->url_full($content, 3);
            $page['module'] = $content['module'];
            $page['type'] = 'content';
            $re[] = $page;
            return $re;
        }
    }

    /**
     * 列表页URL
     * @param string $id
     * @param string $module
     * @return mixed
     */
    protected function getPage($id = '', $module = '')
    {
        $mod = load::sys_class('handle', 'new')->mod_to_file($module);
        $mod_label = load::sys_class('label', 'new')->get($mod);

        $list = $mod_label->get_page_info_by_class($id, 1);
        $page['id'] = $id;
        $page['url'] = $list['url'];
        $page['count'] = $list['count'];
        $h = $mod_label->get_page_info_by_class($id, 3);
        $page['filename'] = $h['url'];
        $page['module'] = $module;
        $page['type'] = 'column';
        return $page;
    }

    /**
     * 内容URL
     * @param string $id
     * @param string $module
     * @return array
     */
    protected function getContentList($id = '', $module = '')
    {
        $mod = load::sys_class('handle', 'new')->mod_to_file($module);
        $mod_label = load::sys_class('label', 'new')->get($mod);

        $list = $mod_label->get_module_list($id);
        foreach ($list as $key => $val) {
            if ($val['links']) {
                continue;
            }
            $page = array();
            $page['url'] = $mod_label->handle->get_content_url($val, 1);
            $page['filename'] = $mod_label->handle->get_content_url($val, 3);
            $page['module'] = $module;
            $page['count'] = 0;
            $page['type'] = 'content';
            $redata[] = $page;
        }
        return $redata;
    }

    /**
     * @param array $pageinfo
     * @return array
     */
    protected function getTagsList(array &$pageinfo)
    {
        $tags_label = load::sys_class('label', 'new')->get('tags');
        $tags_list = $tags_label->get_tags_list();
        if (!$tags_list) {
            return $pageinfo;
        }

        foreach ($tags_list as $row) {
            $url = $tags_label->getTagUrl($row, 1);
            $static_url = $tags_label->getTagUrl($row, 2);

            $arr = array();
            $arr['url'] = $url;
            $arr['filename'] = $static_url . '/index.html';
            $arr['module'] = '';
            $arr['count'] = 0;
            $arr['type'] = 'tags';
            $pageinfo[] = $arr;
        }
        return $pageinfo;
    }

    /**
     * 删除栏目html
     * @param array $class
     */
    protected function DelClassHtml($class = array())
    {
        $files = traversal($class['foldername'], 'html|htm');
        foreach ($files as $fkey => $fval) {
            delfile($fval);
        }
        return;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
