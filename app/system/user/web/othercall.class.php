<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class othercall extends web
{
    protected $other;
    protected $allow;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->allow = array();
    }

    public function doindex()
    {
        global $_M;
        $type = $_M['form']['type'];
        $action = $_M['form']['action'];

        if (!$type || !$action || !in_array($action, $this->allow)) {
            $this->error('error:401');
        }

        $this->other =  load::mod_class("user/web/class/{$type}", 'new');
        if (!method_exists($this->other, $action)) {
            return false;
        }

        $this->other->$action();
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>