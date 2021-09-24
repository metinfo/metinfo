<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * Class tables
 * 数据库对比
 */
class tables
{
    public $version;

    public function __construct()
    {
        global $_M;
        $db_type = $_M['config']['db_type'];
        $this->tables = load::mod_class("databack/{$db_type}tables",'new');
    }

    /**
     * 对比数据库结构
     * @param $version
     */
    public function diff_fields($version = '')
    {
        global $_M;
        return $this->tables->diffFields($version);
    }

    /**
     * 更新表字段默认值
     * @param $version
     */
    public function alter_table($version = '')
    {
        global $_M;
        return $this->tables->alterTable($version);
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.;
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
