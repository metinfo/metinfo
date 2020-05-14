<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.


defined('IN_MET') or exit('No permission');

load::mod_class('base/base_database');

/**
 * 系统标签类.
 */
class product_database extends base_database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['product']);

        if (M_MODULE != 'admin' && $_M['config']['shopv2_open']) {//开启在线订购时
            $p = $_M['table']['product'];
            $s = $_M['table']['shopv2_product'];
            $table = $p . ' Left JOIN ' . $s . " ON ({$p}.id = {$s}.pid)";
            $this->construct($table);
        } else {
            $this->construct($_M['table']['product']);
        }

        $this->multi_column = 1;
    }

    public function table_para()
    {
        return 'id|title|ctitle|keywords|description|content|content1|content2|content3|content4|class1|class2|class3|no_order|wap_ok|img_ok|imgurl|imgurls|com_ok|issue|hits|updatetime|addtime|access|top_ok|filename|lang|recycle|displaytype|tag|links|displayimg|classother|imgsize|text_size|text_color|other_info|custom_info|video';
    }

    /**
     * 删除.
     * @param string $id id
     * @return bool 删除是否成功
     */
    public function del_by_id($id = '')
    {
        if (parent::del_by_id($id)) {
            load::mod_class('parameter/parameter_database', 'new')->del_list($id, $this->table_to_module($this->table));

            return true;
        } else {
            return false;
        }
    }

    public function get_multi_column_sql($class1 = '', $class2 = '', $class3 = '')
    {
        $sql = '';
        if ($class1 || $class2 || $class3) {
            $sql .= 'AND (';
            if ($class1) {
                $sql .= " class1 = '{$class1}' AND ";
            }
            if ($class2) {
                $sql .= " class2 = '{$class2}' AND ";
            }
            if ($class3) {
                $sql .= " class3 = '{$class3}' AND ";
            }
            $sql = substr($sql, 0, -4);
            $sql .= ' OR (';
            $sql .= " classother LIKE '%|-{$class1}-";
            if ($class2) {
                $sql .= "{$class2}-";
                if ($class3) {
                    $sql .= "{$class3}-|%'";
                } else {
                    $sql .= "%'";
                }
            } else {
                $sql .= "%'";
            }

            $sql .= ' )';
            $sql .= ' )';
        }

        return $sql;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.