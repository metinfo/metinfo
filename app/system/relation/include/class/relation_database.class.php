<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.


defined('IN_MET') or exit('No permission');

load::mod_class('base/base_database');

/**
 * 系统标签类.
 */
class relation_database extends base_database
{
    public $multi_column = 0; //是否支持多栏目

    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['relation']);
    }

    //字段注册
    public function table_para()
    {
        return 'id|aid|module|relation_id|relation_module|lang';
    }

    /**
     * @param string $aid
     * @param string $module
     * @return int|mixed
     */
    public function delRelations($aid = '', $module = '')
    {
        global $_M;
        $sql = "DELETE FROM {$_M['table']['relation']} WHERE lang = '{$_M['lang']}' AND aid = '{$aid}' AND module = '{$module}' ";
        return DB::query($sql);
    }

    /**
     * @param string $aid
     * @param string $module
     * @param string $num
     * @return array|void
     */
    public function getRelations($aid = '', $module = '')
    {
        global $_M;
        $sql = "SELECT * FROM {$_M['table']['relation']} WHERE aid = '{$aid}' AND module = '{$module}' AND lang = '{$_M['lang']}' ORDER BY id ";
        $list = DB::get_all($sql);
        return $list;
    }
}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.