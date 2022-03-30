<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin.class.php');
load::sys_class('nav.class.php');
load::sys_class('curl');
/** 伪静态设置 */
class pseudo_static extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->seo_open = load::mod_class('seo/seo_open','new');
    }

    //获取伪静态设置
    public function doGetPseudoStatic()
    {
        global $_M;
        $list = array();
        $list['met_pseudo'] = isset($_M['config']['met_pseudo']) ? $_M['config']['met_pseudo'] : '';
        $list['met_defult_lang'] = isset($_M['config']['met_defult_lang']) ? $_M['config']['met_defult_lang'] : '';
        $this->success($list);
    }

    /**
     * 保存伪静态设置
     */
    public function doSavePseudoStatic()
    {
        global $_M;
        $met_pseudo = isset($_M['form']['met_pseudo']) ? $_M['form']['met_pseudo'] : '';
        $pseudo_download = isset($_M['form']['pseudo_download']) ? $_M['form']['pseudo_download'] : '';

        //保存系统配置
        $configlist = array();
        $configlist[] = 'met_pseudo';   //伪静态开关
        //$configlist[] = 'met_defult_lang';
        configsave($configlist);

        $met_defult_lang = $_M['form']['met_defult_lang'] ? 1 : 0;  //默认语言标识
        $sql = "UPDATE {$_M['table']['config']} SET value='{$met_defult_lang}' WHERE name = 'met_defult_lang'";
        DB::query($sql);

        //生成规则文件
        if ($met_pseudo || $pseudo_download) {
            //如果开启伪静态则关闭静态化并删除首页index.html
            if ($_M['config']['met_webhtm']) {
                $_M['config']['met_webhtm'] = 0;
                $query = "UPDATE {$_M['table']['config']} SET value = 0 WHERE name='met_webhtm' ";
                DB::query($query);
                if (file_exists(PATH_WEB . 'index.html')) {
                    @unlink(PATH_WEB . 'index.html');
                }
            }

            //获取重写规则
            $rewrite = $this->seo_open->getRewrite();
            if ($pseudo_download) {
                //查看伪静态规则
                $this->success($rewrite['rule']);
            } else {
                //删除静态文件
                $this->seo_open->delStatic();
                //创建重写文件
                $this->seo_open->buildRewrite();
            }
        } else {
            $this->seo_open->delRewrite();
        }

        buffer::clearConfig();

        //写日志
        logs::addAdminLog('pseudostatic', 'submit', 'jsok', 'doSavePseudoStatic');
        $this->success('', $_M['word']['jsok']);
    }
}


# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
