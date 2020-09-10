<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 
defined('IN_MET') or exit('No permission');
load::sys_func('file');
class uninstall extends admin{

    public $appno;
    public $appdir;

    public function __construct()
    {
        $this->appno = 10070;
        $this->appname = 'met_sms';
    }

    /**
     * 卸载应用
     */
    public function dodel(){
        global $_M;
        turnover("{$_M['url']['own_form']}a=doindex","系统应用，无法卸载");
    }
}
?>