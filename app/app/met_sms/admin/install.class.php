<?php
defined('IN_MET') or exit ('No permission');
load::sys_class('admin');
class install extends admin {
    public $appno;
    public $appname;
    public $ver;
    public $m_name;
    public $m_class;
    public $m_action;
    public $target;
    public $mlangok;
    public $info;
    public $app_path;

    public function __construct()
    {
        global $_M;
        parent::__construct();

        $this->appname = '短信功能';
        $this->appno = 10070;
        $this->ver = "1.5";
        $this->m_name = 'met_sms';
        $this->m_class = '';
        $this->m_action = '';
        $this->target = 0;
        $this->mlangok = 0;
        $this->info = "可以用于系统通知、短信注册、批量发送";
        $this->app_path = PATH_WEB . "app/app/" . $this->m_name;
    }

    /*public function __construct() {
        global $_M;
        parent::__construct();
    }*/

     /**
     * 注册应用
     * @return [type]
     */
    public function dosql() {

        global $_M;
        $app = DB::get_one("SELECT * FROM {$_M['table']['applist']} WHERE no = '{$this->appno}'");
        if($app) {
           $this->update();
        } else {
            $time = time();
            $query = "INSERT INTO `{$_M['table']['applist']}` SET
                    `id`        =  '',
                    `no`        =  '{$this->appno}',
                    `ver`       =  '{$this->ver}',
                    `m_name`    =  '{$this->m_name}',
                    `m_class`   =  '',
                    `m_action`  =  '',
                    `appname`   =  '{$this->appname}',
                    `info`      =  '{$this->info}',
                    `addtime`   =  {$time},
                    `updatetime`=  {$time}";
            DB::query($query);

        }
    }

    public function update()
    {
        $this->updateAppInfo();
        return;
    }

    /**
     * 更新应用记录
     */
    public function updateAppInfo()
    {
        global $_M;
        $query = "UPDATE {$_M['table']['applist']} SET
                    ver         = '{$this->ver}',
                    m_name      = '{$this->m_name}',
                    m_class     = '{$this->m_class}',
                    m_action    = '{$this->m_action}',
                    appname     = '{$this->appname}',
                    info        = '{$this->info}'
                   WHERE no = '{$this->appno}'";
        DB::query($query);
    }
    
}