<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_handle');


class relation_handle extends base_handle
{

    public function __construct()
    {
        global $_M;
        $this->construct('relation');
        $this->column_label = load::mod_class('column/include/column_label', 'new');
    }

    public function para_handle($list = array())
    {
        global $_M;
        $class_list = array();
        foreach ($list as $key => $row) {
            $one = self::one_para_handle($row);
            if (!in_array($one['relation_class'], $class_list)) {
                $class_list[$one['relation_class']]['classnow'] = $one['relation_class'];
                $class_list[$one['relation_class']]['relation_module'] = $one['relation_module'];
                $class_list[$one['relation_class']]['relation_class_name'] = $one['relation_class_name'];
            }
            $class_list[$one['relation_class']]['list'][] = $one['content'];
        }
        return $class_list;

    }

    public function one_para_handle($data = array())
    {
        global $_M;
        $mod_name = $this->mod_to_name($data['relation_module']);
        if (!$mod_name) {
            return '';
        }

        $mod_lable = load::mod_class("{$mod_name}/{$mod_name}_label", 'new');
        if (!method_exists($mod_lable, 'get_one_content')) {
            return '';
        }

        $redata = array();
        $content = $mod_lable->get_one_content($data['relation_id']);
        $classnow = $content['class3'] ? $content['class3'] : ($content['class2'] ? $content['class2'] : $content['class1']);
        $class = $this->column_label->get_column_id($classnow);

        $redata['relation_module'] = $data['relation_module'];
        $redata['relation_class'] = $classnow;
        $redata['relation_class_name'] = $class['name'];

        $content['module'] = $data['relation_module'];
        $content['classnow'] = $classnow;
        $redata['content'] = $content;
        return $redata;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
