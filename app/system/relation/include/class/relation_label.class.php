<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_label');

/**
 * news标签类
 */

class relation_label extends base_label
{
    public $lang; //语言

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->mod = 'relation';
        $this->database = load::mod_class($this->mod . '/' . $this->mod . '_database', 'new');
        $this->handle = load::mod_class($this->mod . '/' . $this->mod . '_handle', 'new');
    }

    public function getRelations($aid = '', $module = '')
    {
        global $_M;
        $data = $this->database->getRelations($aid , $module);
        $data = $this->handle->para_handle($data);
        return $data;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
