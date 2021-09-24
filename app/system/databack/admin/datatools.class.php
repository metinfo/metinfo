<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('admin');
load::sys_func('file');

class datatools extends admin
{
    protected $ver_allow;
    protected $admin_table_path;

    public function __construct()
    {
        global $_M, $adminurl;
        parent::__construct();
        $adminfile = $_M['config']['met_adminfile'];
        define('ADMIN_FILE', $adminfile);
        die();
    }

    /*************数据打包方法***************/
    /**
     * 生成系统数据指纹
     */
    public function doGetSysDate()
    {
        global $_M;
        $action = $_M['form']['action'];
        echo "acrion : {$_M['form']['action']} <hr>";
        echo "
        <a href='{$_M['url']['site_admin']}?n=databack&c=index&a=doGetSysDate&action=sqldata'>系统数据库指纹</a><br>
        <a href='{$_M['url']['site_admin']}?n=databack&c=index&a=doGetSysDate&action=langdata'>系统语言指纹</a><br>
        <a href='{$_M['url']['site_admin']}?n=databack&c=index&a=doGetSysDate&action=configdata'>配置库指纹</a><br>
        ";

        if ($action =='sqldata') {
            $this->dogetTablesjson();
            die('Complete');
        }
        if ($action == 'langdata') {
            $this->dogetLangData();
            die('Complete');
        }
        if ($action == 'configdata') {
            $this->dogetconfigData();
            die('Complete');
        }
    }

    /** 获取系统数据表json */
    public function dogetTablesjson()
    {
        //return;
        global $_M;
        $table_list = $_M['table'];
        foreach ($table_list as $table) {
            //$query = "desc {$table}";
            $query = "SHOW FULL FIELDS FROM {$table}";
            $res = DB::get_all($query);
            $col = array();
            foreach ($res as $row) {
                $col[$row['Field']] = $row;
            }
            $tables[$table] = $col;
        }
        $tables = json_encode($tables, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $time = time();
        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}mysql.json", $tables);
        die('table_json_complete');
    }

    /**
     * 获取语言数据json.
     */
    public function dogetLangData()
    {
        //return;
        global $_M;
        $sql = "select `name`,`value`,`site`,`no_order`,`array`,`app`,`lang` FROM {$_M['table']['language']} WHERE app = '' OR app = 0 OR app = 1";
        $lang_list = DB::get_all($sql);

        $lang_cn = array();
        $lang_en = array();
        $lang_admin_cn = array();
        $lang_web_cn = array();
        $lang_admin_en = array();
        $lang_web_en = array();

        $lang_ini_cn_admin = "#\n";
        $lang_ini_cn_web = "#\n";
        $lang_ini_en_admin = "#\n";
        $lang_ini_en_web = "#\n";

        foreach ($lang_list as $lang) {
            if ($lang['lang'] == 'cn') {
                $lang_cn[$lang['name']] = $lang;

                if ($lang['site'] == 1) {
                    $lang_admin_cn[$lang['name']] = $lang;
                    $lang_ini_cn_admin .= "{$lang['name']}={$lang['value']}\n";
                } else {
                    $lang_web_cn[$lang['name']] = $lang;
                    $lang_ini_cn_web .= "{$lang['name']}={$lang['value']}\n";
                }
            } elseif ($lang['lang'] == 'en') {
                $lang_en[$lang['name']] = $lang;

                if ($lang['site'] == 1) {
                    $lang_admin_en[$lang['name']] = $lang;
                    $lang_ini_en_admin .= "{$lang['name']}={$lang['value']}\n";
                } else {
                    $lang_web_en[$lang['name']] = $lang;
                    $lang_ini_en_web .= "{$lang['name']}={$lang['value']}\n";
                }
            }
        }

        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}lang_cn.json", json_encode($lang_cn, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}lang_en.json", json_encode($lang_en, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}lang_admin_cn.json", json_encode($lang_admin_cn, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}lang_web_cn.json", json_encode($lang_web_cn, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}lang_admin_en.json", json_encode($lang_admin_en, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}lang_web_en.json", json_encode($lang_web_en, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        file_put_contents(PATH_WEB . "language_cn1.ini", $lang_ini_cn_admin);
        file_put_contents(PATH_WEB . "language_cn.ini", $lang_ini_cn_web);
        file_put_contents(PATH_WEB . "language_en1.ini", $lang_ini_en_admin);
        file_put_contents(PATH_WEB . "language_en.ini", $lang_ini_en_web);
        die('lang_json_complete');
    }

    /**
     * 系统系统标准配置
     */
    public function dogetconfigData()
    {
        global $_M;
        $sql = "select * FROM {$_M['table']['config']} WHERE (lang = 'cn' OR lang = 'metinfo') AND columnid = '0' AND flashid = 0";
        $list = DB::get_all($sql);

        $config_list = array();
        foreach ($list as $config) {
            $config_list[$config['name']] = $config;
        }

        file_put_contents(PATH_WEB . "v{$_M['config']['metcms_v']}config.json", json_encode($config_list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die('dogetconfigData');
    }

    public function doTry()
    {
        global $_M;
        $ver = $_M['form']['ver'];
        $sys_ver = $_M['config']['metcms_v'];

        $update_database = load::mod_class('update/update_database', 'new');
        $update_database->diff_fields($sys_ver);
        $update_database->alter_table($sys_ver);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.;
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
