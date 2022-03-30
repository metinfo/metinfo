<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * Class tables
 * 数据库对比
 */
class sqlitetables
{
    public $version;

    public function __construct()
    {
        global $_M;
        if ($_M['config']['db_type'] !== "sqlite") {
            die('不是sqllite数据库');
        }

        $this->table_list = array('admin_array', 'admin_column', 'admin_logs', 'admin_table', 'app_config', 'app_plugin', 'applist', 'column', 'config', 'cv', 'download', 'feedback', 'flash', 'flash_button', 'flist', 'ifcolumn', 'ifcolumn_addfile', 'ifmember_left', 'img', 'infoprompt', 'job', 'label', 'lang', 'lang_admin', 'language', 'link', 'menu', 'message', 'mlist', 'news', 'online', 'otherinfo', 'para', 'parameter', 'plist', 'product',  'relation', 'skin_table', 'tags', 'templates', 'ui_config', 'ui_list', 'user', 'user_group', 'user_group_pay', 'user_list', 'user_other', 'weixin_reply_log');

    }

    /**
     * 对比数据库结构
     * @param $version
     */
    public function diffFields($version = '')
    {
        global $_M;
        if (strtolower($_M['config']['db_type']) != 'sqlite') {
            return false;
        }

        self::doOldTableDelAll();

        self::doChangAll();

        self::doOldTableDelAll();
    }

    /**
     * 更新表字段默认值
     * @param $version
     */
    public function alterTable($version = '')
    {
        global $_M;

        return;
    }

    /**
     * 批量操作
     */
    public function doChangAll()
    {
        global $_M;
        foreach ($this->table_list as $table) {
            $method = "change_met_{$table}";
            if (method_exists($this, $method)) {
                $this->$method();
                file_put_contents(PATH_CACHE . 'checkSQL_sqlite.log', $method . "\n", FILE_APPEND);
            };
        }
        return true;
    }

    /**
     * 批量操作
     */
    public function doOldTableDelAll()
    {
        global $_M;
        foreach ($this->table_list as $table) {
            $old_table = "_met_{$table}_old";
            $slq = "DROP TABLE {$old_table}";
            DB::query($slq);
        }
    }

    /**************/
    public function change_met_admin_array()
    {
        //met_admin_array
        global $_M;
        $sql = "ALTER TABLE {$_M['table']['admin_array']} RENAME TO _met_admin_array_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['admin_array']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          array_name text(255) DEFAULT '',
          admin_type text,
          admin_ok integer(11) DEFAULT '0',
          admin_op text(30) DEFAULT 'metinfo',
          admin_issueok integer(11) DEFAULT '0',
          admin_group integer(11) DEFAULT '0',
          user_webpower integer(11) DEFAULT '0',
          array_type integer(11) DEFAULT '0',
          lang text(50) DEFAULT '',
          langok text(255) DEFAULT 'metinfo'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_admin_array_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['admin_array']} (id, array_name, admin_type, admin_ok, admin_op, admin_issueok, admin_group, user_webpower, array_type, lang, langok) SELECT id, array_name, admin_type, admin_ok, admin_op, admin_issueok, admin_group, user_webpower, array_type, lang, langok FROM _met_admin_array_old;";
            DB::query($sql);
        }
    }

    public function change_met_admin_column()
    {
        global $_M;
        //met_admin_column
        $sql = "ALTER TABLE {$_M['table']['admin_column']} RENAME TO _met_admin_column_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['admin_column']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              name text(100) DEFAULT '',
              url text(255) DEFAULT '',
              bigclass integer(11) DEFAULT '0',
              field integer(11) DEFAULT '0',
              type integer(11) DEFAULT '0',
              list_order integer(11) DEFAULT '0',
              icon text(255) DEFAULT '',
              info text,
              display integer(11) DEFAULT '1'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_admin_column_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['admin_column']} (id, name, url, bigclass, field, type, list_order, icon, info, display) SELECT id, name, url, bigclass, field, type, list_order, icon, info, display FROM main._met_admin_column_old;";
            DB::query($sql);
        }
    }

    public function change_met_admin_logs()
    {
        global $_M;
        //met_admin_logs
        $sql = "ALTER TABLE {$_M['table']['admin_logs']} RENAME TO _met_admin_logs_old;";
        DB::query($sql);

        $sql = "CREATE TABLE  {$_M['table']['admin_logs']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              username text(255) DEFAULT '',
              name text(255) DEFAULT '',
              module text(255) DEFAULT '',
              current_url text(255) DEFAULT '',
              brower text(255) DEFAULT '',
              result text(255) DEFAULT '',
              ip text(50) DEFAULT '',
              client text(50) DEFAULT '',
              time integer(11) DEFAULT '0',
              user_agent text(255) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_admin_logs_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['admin_logs']} (id, username, name, module, current_url, brower, result, ip, client, time, user_agent) SELECT id, username, name, module, current_url, brower, result, ip, client, time, user_agent FROM _met_admin_logs_old;";
            DB::query($sql);
        }
    }

    public function change_met_admin_table()
    {
        global $_M;
        //met_admin_table
        $sql = "ALTER TABLE {$_M['table']['admin_table']} RENAME TO _met_admin_table_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['admin_table']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              admin_type text,
              admin_id char(20) NOT NULL DEFAULT '',
              admin_pass char(64) NOT NULL DEFAULT '',
              admin_name text(30) NOT NULL DEFAULT '',
              admin_sex integer(1) DEFAULT '1',
              admin_tel text(20) DEFAULT '',
              admin_mobile text(20) DEFAULT '',
              admin_email text(150) DEFAULT '',
              admin_qq text(12) DEFAULT '',
              admin_msn text(40) DEFAULT '',
              admin_taobao text(40) DEFAULT '',
              admin_introduction text,
              admin_login integer(11) DEFAULT '0',
              admin_modify_ip text(20) DEFAULT '',
              admin_modify_date datetime DEFAULT NULL,
              admin_register_date datetime DEFAULT NULL,
              admin_approval_date datetime DEFAULT NULL,
              admin_ok integer(11) DEFAULT '0',
              admin_op text(30) DEFAULT 'metinfo',
              admin_issueok integer(11) DEFAULT '0',
              admin_group integer(11) DEFAULT '0',
              companyname text(255) DEFAULT '',
              companyaddress text(255) DEFAULT '',
              companyfax text(255) DEFAULT '',
              usertype integer(11) DEFAULT '0',
              checkid integer(1) DEFAULT '0',
              companycode text(50) DEFAULT '',
              companywebsite text(50) DEFAULT '',
              cookie text,
              admin_shortcut text,
              lang text(50) DEFAULT '',
              content_type integer(11) DEFAULT '0',
              langok text(255) DEFAULT 'metinfo',
              admin_login_lang text(50) DEFAULT '',
              admin_check integer(11) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_admin_table_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['admin_table']} (id, admin_type, admin_id, admin_pass, admin_name, admin_sex, admin_tel, admin_mobile, admin_email, admin_qq, admin_msn, admin_taobao, admin_introduction, admin_login, admin_modify_ip, admin_modify_date, admin_register_date, admin_approval_date, admin_ok, admin_op, admin_issueok, admin_group, companyname, companyaddress, companyfax, usertype, checkid, companycode, companywebsite, cookie, admin_shortcut, lang, content_type, langok, admin_login_lang, admin_check) SELECT id, admin_type, admin_id, admin_pass, admin_name, admin_sex, admin_tel, admin_mobile, admin_email, admin_qq, admin_msn, admin_taobao, admin_introduction, admin_login, admin_modify_ip, admin_modify_date, admin_register_date, admin_approval_date, admin_ok, admin_op, admin_issueok, admin_group, companyname, companyaddress, companyfax, usertype, checkid, companycode, companywebsite, cookie, admin_shortcut, lang, content_type, langok, admin_login_lang, admin_check FROM _met_admin_table_old;";
            DB::query($sql);
        }
    }

    public function change_met_app_config()
    {
        global $_M;
        //met_app_config
        $sql = "ALTER TABLE {$_M['table']['app_config']} RENAME TO _met_app_config_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['app_config']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          appno integer(20) NOT NULL DEFAULT '0',
          name text(255) DEFAULT '',
          value text,
          lang text(50) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_app_config_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['app_config']} (id, appno, name, value, lang) SELECT id, appno, name, value, lang FROM _met_app_config_old;";
            DB::query($sql);
        }
    }

    public function change_met_app_plugin()
    {
        global $_M;
        //met_app_plugin
        $sql = "ALTER TABLE {$_M['table']['app_plugin']} RENAME TO _met_app_plugin_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['app_plugin']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          no_order integer(11) DEFAULT '0',
          no integer(11) DEFAULT '0',
          m_name text(255) DEFAULT '',
          m_action text(255) DEFAULT '',
          effect integer(1) DEFAULT '0'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_app_plugin_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['app_plugin']} (id, no_order, no, m_name, m_action, effect) SELECT id, no_order, no, m_name, m_action, effect FROM _met_app_plugin_old;";
            DB::query($sql);
        }
    }

    public function change_met_applist()
    {
        global $_M;
        //met_applist
        $sql = "ALTER TABLE {$_M['table']['applist']} RENAME TO _met_applist_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['applist']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              no integer(11) DEFAULT '0',
              ver text(50) DEFAULT '',
              m_name text(50) DEFAULT '',
              m_class text(50) DEFAULT '',
              m_action text(50) DEFAULT '',
              appname text(50) DEFAULT '',
              info text,
              addtime integer(11) DEFAULT '0',
              updatetime integer(11) DEFAULT '0',
              target integer(11) DEFAULT '0',
              display integer(11) DEFAULT '1',
              depend text(100),
              mlangok integer(1) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_applist_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['applist']} (id, no, ver, m_name, m_class, m_action, appname, info, addtime, updatetime, target, display, depend, mlangok) SELECT id, no, ver, m_name, m_class, m_action, appname, info, addtime, updatetime, target, display, depend, mlangok FROM _met_applist_old;";
            DB::query($sql);
        }
    }

    public function change_met_column()
    {
        global $_M;
        //met_column
        $sql = "ALTER TABLE {$_M['table']['column']} RENAME TO _met_column_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['column']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          name text(100) DEFAULT '',
          foldername text(50) DEFAULT '',
          filename text(50) DEFAULT '',
          bigclass integer(11) DEFAULT '0',
          samefile integer(11) DEFAULT '0',
          module integer(11) DEFAULT '0',
          no_order integer(11) DEFAULT '0',
          wap_ok integer(1) DEFAULT '0',
          wap_nav_ok integer(11) DEFAULT '0',
          if_in integer(1) DEFAULT '0',
          nav integer(1) DEFAULT '0',
          ctitle text(200) DEFAULT '',
          keywords text(200) DEFAULT '',
          content longtext,
          description text,
          other_info text,
          custom_info text,
          list_order integer(11) DEFAULT '0',
          new_windows text(50) DEFAULT '',
          classtype integer(11) DEFAULT '1',
          out_url text(200) DEFAULT '',
          index_num integer(11) DEFAULT '0',
          access integer(11) DEFAULT '0',
          indeximg text(255) DEFAULT '',
          columnimg text(255) DEFAULT '',
          isshow integer(11) DEFAULT '1',
          lang text(50) DEFAULT '',
          namemark text(255) DEFAULT '',
          releclass integer(11) DEFAULT '0',
          display integer(11) DEFAULT '0',
          icon text(100) DEFAULT '',
          nofollow integer(1) DEFAULT '0',
          text_size integer(11) DEFAULT '0',
          text_color text(100) DEFAULT '',
          thumb_list text(50) DEFAULT '',
          thumb_detail text(50) DEFAULT '',
          list_length integer(11) DEFAULT '0',
          tab_num integer(11) DEFAULT '0',
          tab_name text(255) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_column_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['column']} (id, name, foldername, filename, bigclass, samefile, module, no_order, wap_ok, wap_nav_ok, if_in, nav, ctitle, keywords, content, description, other_info, custom_info, list_order, new_windows, classtype, out_url, index_num, access, indeximg, columnimg, isshow, lang, namemark, releclass, display, icon, nofollow, text_size, text_color, thumb_list, thumb_detail, list_length, tab_num, tab_name) SELECT id, name, foldername, filename, bigclass, samefile, module, no_order, wap_ok, wap_nav_ok, if_in, nav, ctitle, keywords, content, description, other_info, custom_info, list_order, new_windows, classtype, out_url, index_num, access, indeximg, columnimg, isshow, lang, namemark, releclass, display, icon, nofollow, text_size, text_color, thumb_list, thumb_detail, list_length, tab_num, tab_name FROM _met_column_old;";
            DB::query($sql);
        }
    }

    public function change_met_config()
    {
        global $_M;
        //met_config
        $sql = "ALTER TABLE {$_M['table']['config']} RENAME TO _met_config_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['config']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          name text(255) DEFAULT '',
          value text,
          mobile_value text,
          columnid integer(11) DEFAULT '0',
          flashid integer(11) DEFAULT '0',
          lang text(50) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_config_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['config']} (id, name, value, mobile_value, columnid, flashid, lang) SELECT id, name, value, mobile_value, columnid, flashid, lang FROM _met_config_old;";
            DB::query($sql);
        }
    }

    public function change_met_cv()
    {
        global $_M;
        //met_cv
        $sql = "ALTER TABLE {$_M['table']['cv']} RENAME TO _met_cv_old;";
        DB::query($sql);

        $sql = "CREATE TABLE  {$_M['table']['cv']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              addtime datetime,
              readok integer(11) DEFAULT '0',
              customerid text(50) DEFAULT '0',
              jobid integer(11) DEFAULT '0',
              lang text(50) DEFAULT '',
              ip text(255) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_cv_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['cv']} (id, addtime, readok, customerid, jobid, lang, ip) SELECT id, addtime, readok, customerid, jobid, lang, ip FROM _met_cv_old;";
            DB::query($sql);
        }
    }

    public function change_met_download()
    {
        global $_M;
        //met_download
        $sql = "ALTER TABLE {$_M['table']['download']} RENAME TO _met_download_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['download']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          title text(200) DEFAULT '',
          ctitle text(200) DEFAULT '',
          keywords text(200) DEFAULT '',
          description text,
          content longtext,
          class1 integer(11) DEFAULT '0',
          class2 integer(11) DEFAULT '0',
          class3 integer(11) DEFAULT '0',
          no_order integer(11) DEFAULT '0',
          new_ok integer(1) DEFAULT '0',
          wap_ok integer(1) DEFAULT '0',
          imgurl text(255),
          downloadurl text(255) DEFAULT '',
          filesize text(100) DEFAULT '',
          com_ok integer(1) DEFAULT '0',
          hits integer(11) DEFAULT '0',
          updatetime datetime,
          addtime datetime,
          issue text(100) DEFAULT '',
          access integer(11) DEFAULT '0',
          top_ok integer(1) DEFAULT '0',
          downloadaccess integer(11) DEFAULT '0',
          filename text(255) DEFAULT '',
          lang text(50) DEFAULT '',
          recycle integer(11) DEFAULT '0',
          displaytype integer(11) DEFAULT '1',
          tag text,
          links text(200) DEFAULT '',
          text_size integer(11) DEFAULT '0',
          text_color text(100) DEFAULT '',
          other_info text,
          custom_info text
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_download_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['download']} (id, title, ctitle, keywords, description, content, class1, class2, class3, no_order, new_ok, wap_ok, downloadurl, filesize, com_ok, hits, updatetime, addtime, issue, access, top_ok, downloadaccess, filename, lang, recycle, displaytype, tag, links, text_size, text_color, other_info, custom_info) SELECT id, title, ctitle, keywords, description, content, class1, class2, class3, no_order, new_ok, wap_ok, downloadurl, filesize, com_ok, hits, updatetime, addtime, issue, access, top_ok, downloadaccess, filename, lang, recycle, displaytype, tag, links, text_size, text_color, other_info, custom_info FROM _met_download_old;";
            DB::query($sql);
        }
    }

    public function change_met_feedback()
    {
        global $_M;
        //met_feedback
        $sql = "ALTER TABLE {$_M['table']['feedback']} RENAME TO _met_feedback_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['feedback']}  (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              class1 integer(11) DEFAULT '0',
              fdtitle text(255) DEFAULT '',
              fromurl text(255) DEFAULT '',
              ip text(255) DEFAULT '',
              addtime datetime,
              readok integer(11) DEFAULT '0',
              useinfo text,
              customerid text(30) DEFAULT '0',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_feedback_old  LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['feedback']} (id, class1, fdtitle, fromurl, ip, addtime, readok, useinfo, customerid, lang) SELECT id, class1, fdtitle, fromurl, ip, addtime, readok, useinfo, customerid, lang FROM _met_feedback_old";
            DB::query($sql);
        }
    }

    public function change_met_flash()
    {
        global $_M;
        //met_flash
        $sql = "ALTER TABLE {$_M['table']['flash']} RENAME TO _met_flash_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['flash']}  (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              module text,
              img_title text(255) DEFAULT '',
              img_path text(255) DEFAULT '',
              img_link text(255) DEFAULT '',
              flash_path text(255) DEFAULT '',
              flash_back text(255) DEFAULT '',
              no_order integer(11) DEFAULT '0',
              width integer(11) DEFAULT '0',
              height integer(11) DEFAULT '0',
              wap_ok integer(11) DEFAULT '0',
              img_title_color text(100) DEFAULT '',
              img_des text(255) DEFAULT '',
              img_des_color text(100) DEFAULT '',
              img_text_position text(100) DEFAULT '4',
              img_title_fontsize integer(11) DEFAULT '0',
              img_des_fontsize integer(11) DEFAULT '0',
              height_m integer(11) DEFAULT '0',
              height_t integer(11) DEFAULT '0',
              mobile_img_path text(255) DEFAULT '',
              img_title_mobile text(255) DEFAULT '',
              img_title_color_mobile text(100) DEFAULT '',
              img_text_position_mobile text(100) DEFAULT '4',
              img_title_fontsize_mobile integer(11) DEFAULT '0',
              img_des_mobile text(255) DEFAULT '',
              img_des_color_mobile text(100) DEFAULT '',
              img_des_fontsize_mobile integer(11) DEFAULT '0',
              lang text(50) DEFAULT '',
              target integer(11) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_flash_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['flash']} (id, module, img_title, img_path, img_link, flash_path, flash_back, no_order, width, height, wap_ok, img_title_color, img_des, img_des_color, img_text_position, img_title_fontsize, img_des_fontsize, height_m, height_t, mobile_img_path, img_title_mobile, img_title_color_mobile, img_text_position_mobile, img_title_fontsize_mobile, img_des_mobile, img_des_color_mobile, img_des_fontsize_mobile, lang, target) SELECT id, module, img_title, img_path, img_link, flash_path, flash_back, no_order, width, height, wap_ok, img_title_color, img_des, img_des_color, img_text_position, img_title_fontsize, img_des_fontsize, height_m, height_t, mobile_img_path, img_title_mobile, img_title_color_mobile, img_text_position_mobile, img_title_fontsize_mobile, img_des_mobile, img_des_color_mobile, img_des_fontsize_mobile, lang, target FROM _met_flash_old;";
            DB::query($sql);
        }
    }

    public function change_met_flash_button()
    {
        global $_M;
        //met_flash_button
        $sql = "ALTER TABLE {$_M['table']['flash_button']} RENAME TO _met_flash_button_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['flash_button']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              flash_id integer(11) DEFAULT '0',
              but_text text(255) DEFAULT '',
              but_url text(255) DEFAULT '',
              but_text_size integer(11) DEFAULT '0',
              but_text_color text(100) DEFAULT '',
              but_text_hover_color text(100) DEFAULT '',
              but_color text(100) DEFAULT '',
              but_hover_color text(100) DEFAULT '',
              but_size text(100) DEFAULT '',
              is_mobile integer(11) DEFAULT '0',
              no_order integer(11) DEFAULT '0',
              target integer(11) DEFAULT '0',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_flash_button_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['flash_button']} (id, flash_id, but_text, but_url, but_text_size, but_text_color, but_text_hover_color, but_color, but_hover_color, but_size, is_mobile, no_order, target, lang) SELECT id, flash_id, but_text, but_url, but_text_size, but_text_color, but_text_hover_color, but_color, but_hover_color, but_size, is_mobile, no_order, target, lang FROM _met_flash_button_old;";
            DB::query($sql);
        }
    }

    public function change_met_flist()
    {
        global $_M;
        //met_flist
        $sql = "ALTER TABLE {$_M['table']['flist']}  RENAME TO _met_flist_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['flist']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              listid integer(11) DEFAULT '0',
              paraid integer(11) DEFAULT '0',
              info text,
              lang text(50) DEFAULT '',
              imgname text(255) DEFAULT '',
              module integer(11) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_flist_old LIMIT 1";
        $one = DB::get_one($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['flist']} (id, listid, paraid, info, lang, imgname, module) SELECT id, listid, paraid, info, lang, imgname, module FROM _met_flist_old;";
            DB::query($sql);
        }

    }

    public function change_met_ifcolumn()
    {
        global $_M;
        //met_ifcolumn
        $sql = "ALTER TABLE {$_M['table']['ifcolumn']} RENAME TO _met_ifcolumn_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['ifcolumn']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          no integer(11) DEFAULT '0',
          name text(50) DEFAULT '',
          appname text(50) DEFAULT '',
          addfile integer(1) DEFAULT '1',
          memberleft integer(1) DEFAULT '0',
          uniqueness integer(1) DEFAULT '0',
          fixed_name text(50) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_ifcolumn_old LIMIT 1";
        $one = DB::get_one($sql);
        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['ifcolumn']} (id, no, name, appname, addfile, memberleft, uniqueness, fixed_name) SELECT id, no, name, appname, addfile, memberleft, uniqueness, fixed_name FROM _met_ifcolumn_old;";
            DB::query($sql);
        }
    }

    public function change_met_ifcolumn_addfile()
    {
        global $_M;
        //met_ifcolumn_addfile
        $sql = "ALTER TABLE {$_M['table']['ifcolumn_addfile']} RENAME TO _met_ifcolumn_addfile_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['ifcolumn_addfile']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          no integer(11) DEFAULT '0',
          filename text(255) DEFAULT '',
          m_name text(255) DEFAULT '',
          m_module text(255) DEFAULT '',
          m_class text(255) DEFAULT '',
          m_action text(255) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_ifcolumn_addfile_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['ifcolumn_addfile']} (id, no, filename, m_name, m_module, m_class, m_action) SELECT id, no, filename, m_name, m_module, m_class, m_action FROM _met_ifcolumn_addfile_old;";
            DB::query($sql);
        }

    }

    public function change_met_ifmember_left()
    {
        global $_M;
        //met_ifmember_left
        $sql = "ALTER TABLE  {$_M['table']['ifmember_left']} RENAME TO _met_ifmember_left_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['ifmember_left']}  (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          no integer(11) DEFAULT '0',
          columnid integer(11) DEFAULT '0',
          title text(50) DEFAULT '',
          foldername text(255) DEFAULT '',
          filename text(255) DEFAULT '',
          target integer(11) DEFAULT '0',
          own_order text(11) DEFAULT '',
          effect integer(1) DEFAULT '0',
          lang text(50) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_ifmember_left_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['ifmember_left']} (id, no, columnid, title, foldername, filename, target, own_order, effect, lang) SELECT id, no, columnid, title, foldername, filename, target, own_order, effect, lang FROM _met_ifmember_left_old;";
            DB::query($sql);
        }

    }

    public function change_met_img()
    {
        global $_M;
        //met_img
        $sql = "ALTER TABLE {$_M['table']['img']} RENAME TO _met_img_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['img']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          title text(200) DEFAULT '',
          ctitle text(200) DEFAULT '',
          keywords text(200) DEFAULT '',
          description text,
          content longtext,
          class1 integer(11) DEFAULT '0',
          class2 integer(11) DEFAULT '0',
          class3 integer(11) DEFAULT '0',
          no_order integer(11) DEFAULT '0',
          wap_ok integer(1) DEFAULT '0',
          new_ok integer(1) DEFAULT '0',
          imgurl text(255) DEFAULT '',
          imgurls text(255) DEFAULT '',
          displayimg text,
          com_ok integer(1) DEFAULT '0',
          hits integer(11) DEFAULT '0',
          updatetime datetime,
          addtime datetime,
          issue text(100) DEFAULT '',
          access integer(11) DEFAULT '0',
          top_ok integer(1) DEFAULT '0',
          filename text(255) DEFAULT '',
          lang text(50) DEFAULT '',
          content1 text,
          content2 text,
          content3 text,
          content4 text,
          contentinfo text(255) DEFAULT '',
          contentinfo1 text(255) DEFAULT '',
          contentinfo2 text(255) DEFAULT '',
          contentinfo3 text(255) DEFAULT '',
          contentinfo4 text(255) DEFAULT '',
          recycle integer(11) DEFAULT '0',
          displaytype integer(11) DEFAULT '1',
          tag text,
          links text(200) DEFAULT '',
          imgsize text(200) DEFAULT '',
          text_size integer(11) DEFAULT '0',
          text_color text(100) DEFAULT '',
          other_info text,
          custom_info text
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_img_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['img']} (id, title, ctitle, keywords, description, content, class1, class2, class3, no_order, wap_ok, new_ok, imgurl, imgurls, displayimg, com_ok, hits, updatetime, addtime, issue, access, top_ok, filename, lang, content1, content2, content3, content4, contentinfo, contentinfo1, contentinfo2, contentinfo3, contentinfo4, recycle, displaytype, tag, links, imgsize, text_size, text_color, other_info, custom_info) SELECT id, title, ctitle, keywords, description, content, class1, class2, class3, no_order, wap_ok, new_ok, imgurl, imgurls, displayimg, com_ok, hits, updatetime, addtime, issue, access, top_ok, filename, lang, content1, content2, content3, content4, contentinfo, contentinfo1, contentinfo2, contentinfo3, contentinfo4, recycle, displaytype, tag, links, imgsize, text_size, text_color, other_info, custom_info FROM _met_img_old;";
            DB::query($sql);
        }
    }

    public function change_met_infoprompt()
    {
        global $_M;
        //met_infoprompt
        $sql = "ALTER TABLE {$_M['table']['infoprompt']} RENAME TO _met_infoprompt_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['infoprompt']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          news_id integer(11) DEFAULT '0',
          newstitle text(120) DEFAULT '',
          content text,
          url text(200) DEFAULT '',
          member text(50) DEFAULT '',
          type text(35) DEFAULT '',
          time integer(11) DEFAULT '0',
          see_ok integer(11) DEFAULT '0',
          lang text(10) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_infoprompt_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['infoprompt']}  (id, news_id, newstitle, content, url, member, type, time, see_ok, lang) SELECT id, news_id, newstitle, content, url, member, type, time, see_ok, lang FROM _met_infoprompt_old;";
            DB::query($sql);
        }
    }

    public function change_met_job()
    {
        global $_M;
        //met_job
        $sql = "ALTER TABLE  {$_M['table']['job']} RENAME TO _met_job_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['job']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          position text(200) DEFAULT '',
          count integer(11) DEFAULT '0',
          place text(200) DEFAULT '',
          deal text(200) DEFAULT '',
          addtime date,
          updatetime date,
          useful_life integer(11) DEFAULT '0',
          content longtext,
          access integer(11) DEFAULT '0',
          class1 integer(11) DEFAULT '0',
          class2 integer(11) DEFAULT '0',
          class3 integer(11) DEFAULT '0',
          no_order integer(11) DEFAULT '0',
          wap_ok integer(1) DEFAULT '0',
          top_ok integer(1) DEFAULT '0',
          email text(255) DEFAULT '',
          filename text(255) DEFAULT '',
          lang text(50) DEFAULT '',
          displaytype integer(11) DEFAULT '1',
          text_size integer(11) DEFAULT '0',
          text_color text(100) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_job_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['job']} (id, position, count, place, deal, addtime, updatetime, useful_life, content, access, class1, class2, class3, no_order, wap_ok, top_ok, email, filename, lang, displaytype, text_size, text_color) SELECT id, position, count, place, deal, addtime, updatetime, useful_life, content, access, class1, class2, class3, no_order, wap_ok, top_ok, email, filename, lang, displaytype, text_size, text_color FROM _met_job_old;";
            DB::query($sql);
        }

    }

    public function change_met_label()
    {
        global $_M;
        //met_label
        $sql = "ALTER TABLE {$_M['table']['label']}  RENAME TO _met_label_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['label']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              oldwords text(255) DEFAULT '',
              newwords text(255) DEFAULT '',
              newtitle text(255) DEFAULT '',
              url text(255) DEFAULT '',
              num integer(11) DEFAULT '99',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_label_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['label']} (id, oldwords, newwords, newtitle, url, num, lang) SELECT id, oldwords, newwords, newtitle, url, num, lang FROM _met_label_old;";
            DB::query($sql);
        }
    }

    public function change_met_lang()
    {
        global $_M;
        //met_lang
        $sql = "ALTER TABLE {$_M['table']['lang']} RENAME TO _met_lang_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['lang']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              name text(100) DEFAULT '',
              useok integer(1) DEFAULT '0',
              no_order integer(11) DEFAULT '0',
              mark text(50) DEFAULT '',
              synchronous text(50) DEFAULT '',
              flag text(100) DEFAULT '',
              link text(255) DEFAULT '',
              newwindows integer(1) DEFAULT '0',
              met_webhtm integer(1) DEFAULT '0',
              met_htmtype text(50) DEFAULT '',
              met_weburl text(255) DEFAULT '',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_lang_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['lang']} (id, name, useok, no_order, mark, synchronous, flag, link, newwindows, met_webhtm, met_htmtype, met_weburl, lang) SELECT id, name, useok, no_order, mark, synchronous, flag, link, newwindows, met_webhtm, met_htmtype, met_weburl, lang FROM _met_lang_old;";
            DB::query($sql);
        }
    }

    public function change_met_lang_admin()
    {
        global $_M;
        //met_lang_admin
        $sql = "ALTER TABLE  {$_M['table']['lang_admin']}   RENAME TO _met_lang_admin_old;";
        DB::query($sql);

        $sql = "CREATE TABLE  {$_M['table']['lang_admin']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              name text(100) DEFAULT '',
              useok integer(1) DEFAULT '1',
              no_order integer(11) DEFAULT '0',
              mark text(50) DEFAULT '',
              synchronous text(50) DEFAULT '',
              link text(255) DEFAULT '',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_lang_admin_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['lang_admin']} (id, name, useok, no_order, mark, synchronous, link, lang) SELECT id, name, useok, no_order, mark, synchronous, link, lang FROM _met_lang_admin_old;";
            DB::query($sql);
        }
    }

    public function change_met_language()
    {
        global $_M;
        //met_language
        $sql = "ALTER TABLE {$_M['table']['language']} RENAME TO _met_language_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['language']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              name text(255) DEFAULT '',
              value text,
              site integer(1) DEFAULT '0',
              no_order integer(11) DEFAULT '0',
              array integer(11) DEFAULT '0',
              app integer(11) DEFAULT '0',
              lang text(50) DEFAULT ''
            );
            ";
        DB::query($sql);

        $sql = "SELECT * FROM _met_language_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['language']} (id, name, value, site, no_order, array, app, lang) SELECT id, name, value, site, no_order, array, app, lang FROM _met_language_old;";
            DB::query($sql);
        }
    }

    public function change_met_link()
    {
        global $_M;
        //met_link
        $sql = "ALTER TABLE  {$_M['table']['link']} RENAME TO _met_link_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['link']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              webname text(255) DEFAULT '',
              module text(255) DEFAULT '',
              weburl text(255) DEFAULT '',
              weblogo text(255) DEFAULT '',
              link_type integer(11) DEFAULT '0',
              info text(255) DEFAULT '',
              contact text(255) DEFAULT '',
              orderno integer(11) DEFAULT '0',
              com_ok integer(11) DEFAULT '0',
              show_ok integer(11) DEFAULT '0',
              addtime datetime,
              lang text(50) DEFAULT '',
              ip text(255) DEFAULT '',
              nofollow integer(1) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_link_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['link']} (id, webname, module, weburl, weblogo, link_type, info, contact, orderno, com_ok, show_ok, addtime, lang, ip, nofollow) SELECT id, webname, module, weburl, weblogo, link_type, info, contact, orderno, com_ok, show_ok, addtime, lang, ip, nofollow FROM _met_link_old;";
            DB::query($sql);
        }
    }

    public function change_met_menu()
    {
        global $_M;
        //met_menu
        $sql = "ALTER TABLE {$_M['table']['menu']} RENAME TO _met_menu_old;";
        DB::query($sql);

        $sql = "CREATE TABLE  {$_M['table']['menu']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              name text(255) DEFAULT '',
              url text(255) DEFAULT '',
              icon text(255) DEFAULT '',
              text_color text(100) DEFAULT '',
              but_color text(100) DEFAULT '',
              target integer(11) DEFAULT '0',
              enabled integer(11) DEFAULT '1',
              no_order integer(11) DEFAULT '0',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_menu_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['menu']} (id, name, url, icon, text_color, but_color, target, enabled, no_order, lang) SELECT id, name, url, icon, text_color, but_color, target, enabled, no_order, lang FROM _met_menu_old;";
            DB::query($sql);
        }
    }

    public function change_met_message()
    {
        global $_M;
        //met_message
        $sql = "ALTER TABLE {$_M['table']['message']} RENAME TO _met_message_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['message']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              ip text(255) DEFAULT '',
              addtime datetime,
              readok integer(11) DEFAULT '0',
              useinfo text,
              lang text(50) DEFAULT '',
              access integer(11) DEFAULT '0',
              customerid text(30) DEFAULT '0',
              checkok integer(11) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_message_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['message']} (id, ip, addtime, readok, useinfo, lang, access, customerid, checkok) SELECT id, ip, addtime, readok, useinfo, lang, access, customerid, checkok FROM _met_message_old;";
            DB::query($sql);
        }

    }

    public function change_met_mlist()
    {
        global $_M;
        //met_mlist
        $sql = "ALTER TABLE {$_M['table']['mlist']} RENAME TO _met_mlist_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['mlist']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              listid integer(11) DEFAULT '0',
              paraid integer(11) DEFAULT '0',
              info text,
              lang text(50) DEFAULT '',
              imgname text(255) DEFAULT '',
              module integer(11) DEFAULT '0'
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_mlist_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['mlist']}  (id, listid, paraid, info, lang, imgname, module) SELECT id, listid, paraid, info, lang, imgname, module FROM _met_mlist_old;";
            DB::query($sql);
        }
    }

    public function change_met_news()
    {
        global $_M;
        //met_news
        $sql = "ALTER TABLE {$_M['table']['news']} RENAME TO _met_news_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['news']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              title text(200) DEFAULT '',
              ctitle text(200) DEFAULT '',
              keywords text(200) DEFAULT '',
              description text,
              content longtext,
              class1 integer(11) DEFAULT '0',
              class2 integer(11) DEFAULT '0',
              class3 integer(11) DEFAULT '0',
              no_order integer(11) DEFAULT '0',
              wap_ok integer(1) DEFAULT '0',
              img_ok integer(1) DEFAULT '0',
              imgurl text(255) DEFAULT '',
              imgurls text(255) DEFAULT '',
              com_ok integer(1) DEFAULT '0',
              issue text(100) DEFAULT '',
              hits integer(11) DEFAULT '0',
              updatetime datetime,
              addtime datetime,
              access integer(11) DEFAULT '0',
              top_ok integer(1) DEFAULT '0',
              filename text(255) DEFAULT '',
              lang text(50) DEFAULT '',
              recycle integer(11) DEFAULT '0',
              displaytype integer(11) DEFAULT '1',
              tag text,
              links text(200) DEFAULT '',
              publisher text(50) DEFAULT '',
              text_size integer(11) DEFAULT '0',
              text_color text(100) DEFAULT '',
              other_info text,
              custom_info text
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_news_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['news']} (id, title, ctitle, keywords, description, content, class1, class2, class3, no_order, wap_ok, img_ok, imgurl, imgurls, com_ok, issue, hits, updatetime, addtime, access, top_ok, filename, lang, recycle, displaytype, tag, links, publisher, text_size, text_color, other_info, custom_info) SELECT id, title, ctitle, keywords, description, content, class1, class2, class3, no_order, wap_ok, img_ok, imgurl, imgurls, com_ok, issue, hits, updatetime, addtime, access, top_ok, filename, lang, recycle, displaytype, tag, links, publisher, text_size, text_color, other_info, custom_info FROM _met_news_old;";
            DB::query($sql);
        }
    }

    public function change_met_online()
    {
        global $_M;
        //met_online
        $sql = "ALTER TABLE {$_M['table']['online']} RENAME TO _met_online_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['online']}  (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              no_order integer(11) DEFAULT '0',
              name text(255) DEFAULT '',
              value text(255) DEFAULT '',
              icon text(255) DEFAULT '',
              type integer(11) DEFAULT '0',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_online_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['online']}  (id, no_order, name, value, icon, type, lang) SELECT id, no_order, name, value, icon, type, lang FROM _met_online_old;";
            DB::query($sql);
        }
    }

    public function change_met_otherinfo()
    {
        global $_M;
        //met_otherinfo
        $sql = "ALTER TABLE {$_M['table']['otherinfo']} RENAME TO _met_otherinfo_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['otherinfo']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              info1 text(255) DEFAULT '',
              info2 text(255) DEFAULT '',
              info3 text(255) DEFAULT '',
              info4 text(255) DEFAULT '',
              info5 text(255) DEFAULT '',
              info6 text(255) DEFAULT '',
              info7 text(255) DEFAULT '',
              info8 text,
              info9 text,
              info10 text,
              imgurl1 text(255) DEFAULT '',
              imgurl2 text(255) DEFAULT '',
              rightmd5 text(255) DEFAULT '',
              righttext text(255) DEFAULT '',
              authcode text,
              authpass text(255) DEFAULT '',
              authtext text(255) DEFAULT '',
              data longtext,
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_otherinfo_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['otherinfo']} (id, info1, info2, info3, info4, info5, info6, info7, info8, info9, info10, imgurl1, imgurl2, rightmd5, righttext, authcode, authpass, authtext, data, lang) SELECT id, info1, info2, info3, info4, info5, info6, info7, info8, info9, info10, imgurl1, imgurl2, rightmd5, righttext, authcode, authpass, authtext, data, lang FROM _met_otherinfo_old;";
            DB::query($sql);
        }

    }

    public function change_met_para()
    {
        global $_M;
        //met_para
        $sql = "ALTER TABLE {$_M['table']['para']} RENAME TO _met_para_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['para']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              pid integer(10) DEFAULT '0',
              value text(255) DEFAULT '',
              module integer(10) DEFAULT '0',
              \"order\" integer(10) DEFAULT '0',
              lang text(100) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_para_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['para']} (id, pid, value, module, \"order\", lang) SELECT id, pid, value, module, \"order\", lang FROM _met_para_old;";
            DB::query($sql);
        }
    }

    public function change_met_parameter()
    {
        global $_M;
        //met_parameter
        $sql = "ALTER TABLE {$_M['table']['parameter']} RENAME TO _met_parameter_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['parameter']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          name text(100) DEFAULT '',
          options text,
          description text,
          no_order integer(2) DEFAULT '0',
          type integer(2) DEFAULT '0',
          access integer(11) DEFAULT '0',
          wr_ok integer(2) DEFAULT '0',
          class1 integer(11) DEFAULT '0',
          class2 integer(11) DEFAULT '0',
          class3 integer(11) DEFAULT '0',
          module integer(2) DEFAULT '0',
          lang text(50) DEFAULT '',
          wr_oks integer(2) DEFAULT '0',
          related text(50) DEFAULT '',
          edit_ok integer(2) DEFAULT '1'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_parameter_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['parameter']} (id, name, options, description, no_order, type, access, wr_ok, class1, class2, class3, module, lang, wr_oks, related, edit_ok) SELECT id, name, options, description, no_order, type, access, wr_ok, class1, class2, class3, module, lang, wr_oks, related, edit_ok FROM _met_parameter_old;";
            DB::query($sql);
        }
    }

    public function change_met_plist()
    {
        global $_M;
        //met_plist
        $sql = "ALTER TABLE {$_M['table']['plist']} RENAME TO _met_plist_old;";
        DB::query($sql);

        $sql = "CREATE TABLE  {$_M['table']['plist']}  (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          listid integer(11) DEFAULT '0',
          paraid integer(11) DEFAULT '0',
          info text,
          lang text(50) DEFAULT '',
          imgname text(255) DEFAULT '',
          module integer(11) DEFAULT '0'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_plist_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['plist']}  (id, listid, paraid, info, lang, imgname, module) SELECT id, listid, paraid, info, lang, imgname, module FROM _met_plist_old;";
            DB::query($sql);
        }
    }

    public function change_met_product()
    {
        global $_M;
        //met_product
        $sql = "ALTER TABLE {$_M['table']['product']} RENAME TO _met_product_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['product']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              title text(200) DEFAULT '',
              ctitle text(200) DEFAULT '',
              keywords text(200) DEFAULT '',
              description text,
              content longtext,
              class1 integer(11) DEFAULT '0',
              class2 integer(11) DEFAULT '0',
              class3 integer(11) DEFAULT '0',
              classother text,
              no_order integer(11) DEFAULT '0',
              wap_ok integer(1) DEFAULT '0',
              new_ok integer(1) DEFAULT '0',
              imgurl text(255) DEFAULT '',
              imgurls text(255) DEFAULT '',
              displayimg text,
              com_ok integer(1) DEFAULT '0',
              hits integer(11) DEFAULT '0',
              updatetime datetime,
              addtime datetime,
              issue text(100) DEFAULT '',
              access integer(11) DEFAULT '0',
              top_ok integer(1) DEFAULT '0',
              filename text(255) DEFAULT '',
              lang text(50) DEFAULT '',
              content1 mediumtext,
              content2 mediumtext,
              content3 mediumtext,
              content4 mediumtext,
              contentinfo text(255) DEFAULT '',
              contentinfo1 text(255) DEFAULT '',
              contentinfo2 text(255) DEFAULT '',
              contentinfo3 text(255) DEFAULT '',
              contentinfo4 text(255) DEFAULT '',
              recycle integer(11) DEFAULT '0',
              displaytype integer(11) DEFAULT '1',
              tag text,
              links text(200) DEFAULT '',
              imgsize text(200) DEFAULT '',
              text_size integer(11) DEFAULT '0',
              text_color text(100) DEFAULT '',
              other_info text,
              custom_info text,
              video text
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_product_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['product']} (id, title, ctitle, keywords, description, content, class1, class2, class3, classother, no_order, wap_ok, new_ok, imgurl, imgurls, displayimg, com_ok, hits, updatetime, addtime, issue, access, top_ok, filename, lang, content1, content2, content3, content4, contentinfo, contentinfo1, contentinfo2, contentinfo3, contentinfo4, recycle, displaytype, tag, links, imgsize, text_size, text_color, other_info, custom_info) SELECT id, title, ctitle, keywords, description, content, class1, class2, class3, classother, no_order, wap_ok, new_ok, imgurl, imgurls, displayimg, com_ok, hits, updatetime, addtime, issue, access, top_ok, filename, lang, content1, content2, content3, content4, contentinfo, contentinfo1, contentinfo2, contentinfo3, contentinfo4, recycle, displaytype, tag, links, imgsize, text_size, text_color, other_info, custom_info FROM _met_product_old;";
            DB::query($sql);
        }
    }

    public function change_met_relation()
    {
        global $_M;
        //met_relation
        $sql = "ALTER TABLE {$_M['table']['relation']} RENAME TO _met_relation_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['relation']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              aid integer(11) DEFAULT '0' ,
              module integer(11) DEFAULT '0',
              relation_id integer(11) DEFAULT '0' ,
              relation_module integer(11) DEFAULT '0',
              lang text(50) DEFAULT ''
            )";
        DB::query($sql);

        $sql = "SELECT * FROM _met_relation_old  LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['relation']}  (id, aid, module, relation_id, relation_module, lang) SELECT id, aid, module, relation_id, relation_module, lang FROM _met_relation_old;";
            DB::query($sql);
        }
    }

    public function change_met_skin_table()
    {
        global $_M;
        //met_product
        $sql = "ALTER TABLE {$_M['table']['skin_table']} RENAME TO _met_skin_table_old;";
        DB::query($sql);

        $sql = "CREATE TABLE  {$_M['table']['skin_table']}  (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              skin_name text(200) DEFAULT '',
              skin_file text(20) DEFAULT '',
              skin_info text,
              devices integer(11) DEFAULT '0',
              ver text(10) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_skin_table_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['skin_table']}  (id, skin_name, skin_file, skin_info, devices, ver) SELECT id, skin_name, skin_file, skin_info, devices, ver FROM _met_skin_table_old;";
            DB::query($sql);
        }
    }

    public function change_met_tags()
    {
        global $_M;
        //met_tags
        $sql = "ALTER TABLE  {$_M['table']['tags']}  RENAME TO _met_tags_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['tags']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              tag_name text(255) DEFAULT '',
              tag_pinyin text(255) DEFAULT '',
              module integer(11) DEFAULT '0',
              cid integer(11) DEFAULT '0',
              list_id text(255) DEFAULT '',
              title text(255) DEFAULT '',
              keywords text(255) DEFAULT '',
              description text(255) DEFAULT '',
              tag_color text(255) DEFAULT '',
              tag_size integer(10) DEFAULT '0',
              sort integer(10) DEFAULT '0',
              lang text(100) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_tags_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['tags']} (id, tag_name, tag_pinyin, module, cid, list_id, title, keywords, description, tag_color, tag_size, sort, lang) SELECT id, tag_name, tag_pinyin, module, cid, list_id, title, keywords, description, tag_color, tag_size, sort, lang FROM _met_tags_old;";
            DB::query($sql);
        }
    }

    public function change_met_templates()
    {
        global $_M;
        //met_templates
        $sql = "ALTER TABLE {$_M['table']['templates']} RENAME TO _met_templates_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['templates']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          no text(20) DEFAULT '0',
          pos integer(11) DEFAULT '0',
          no_order integer(11) DEFAULT '0',
          type integer(11) DEFAULT '0',
          style integer(11) DEFAULT '0',
          selectd text(500) DEFAULT '',
          name text(50) DEFAULT '',
          value text,
          defaultvalue text,
          valueinfo text(100) DEFAULT '',
          tips text(255) DEFAULT '',
          lang text(50) DEFAULT '',
          bigclass integer(11) DEFAULT '0'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_templates_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['templates']} (id, no, pos, no_order, type, style, selectd, name, value, defaultvalue, valueinfo, tips, lang, bigclass) SELECT id, no, pos, no_order, type, style, selectd, name, value, defaultvalue, valueinfo, tips, lang, bigclass FROM _met_templates_old;";
            DB::query($sql);
        }
    }

    public function change_met_ui_config()
    {
        global $_M;
        //met_ui_config
        $sql = "ALTER TABLE {$_M['table']['ui_config']} RENAME TO _met_ui_config_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['ui_config']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          pid integer(10) DEFAULT '0',
          parent_name text(100) DEFAULT '',
          ui_name text(100) DEFAULT '',
          skin_name text(100) DEFAULT '',
          uip_type integer(10) DEFAULT '0',
          uip_style integer(1) DEFAULT '0',
          uip_select text(500) DEFAULT '1',
          uip_name text(100) DEFAULT '',
          uip_key text(100) DEFAULT '',
          uip_value text,
          uip_default text(255) DEFAULT '',
          uip_title text(100) DEFAULT '',
          uip_description text(255) DEFAULT '',
          uip_order integer(10) DEFAULT '0',
          lang text(100) DEFAULT '',
          uip_hidden integer(1) DEFAULT '0'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_ui_config_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['ui_config']} (id, pid, parent_name, ui_name, skin_name, uip_type, uip_style, uip_select, uip_name, uip_key, uip_value, uip_default, uip_title, uip_description, uip_order, lang, uip_hidden) SELECT id, pid, parent_name, ui_name, skin_name, uip_type, uip_style, uip_select, uip_name, uip_key, uip_value, uip_default, uip_title, uip_description, uip_order, lang, uip_hidden FROM _met_ui_config_old;";
            DB::query($sql);
        }
    }

    public function change_met_ui_list()
    {
        global $_M;
        //met_ui_list
        $sql = "ALTER TABLE {$_M['table']['ui_list']} RENAME TO _met_ui_list_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['ui_list']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          installid integer(10) DEFAULT '0',
          parent_name text(100) DEFAULT '',
          ui_name text(100) DEFAULT '',
          skin_name text(100) DEFAULT '',
          ui_page text(200) DEFAULT '',
          ui_title text(100) DEFAULT '',
          ui_description text(500) DEFAULT '',
          ui_order integer(10) DEFAULT '0',
          ui_version text(100) DEFAULT '',
          ui_installtime integer(10) DEFAULT '0',
          ui_edittime integer(10) DEFAULT '0'
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_ui_list_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['ui_list']} (id, installid, parent_name, ui_name, skin_name, ui_page, ui_title, ui_description, ui_order, ui_version, ui_installtime, ui_edittime) SELECT id, installid, parent_name, ui_name, skin_name, ui_page, ui_title, ui_description, ui_order, ui_version, ui_installtime, ui_edittime FROM _met_ui_list_old;";
            DB::query($sql);
        }
    }

    public function change_met_user()
    {
        global $_M;
        //met_user
        $sql = "ALTER TABLE met_user RENAME TO _met_user_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['user']}  (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          username text(30) DEFAULT '',
          password text(32) DEFAULT '',
          head text(100) DEFAULT '',
          email text(50) DEFAULT '',
          tel text(20) DEFAULT '',
          groupid integer(11) DEFAULT '0',
          register_time integer(11) DEFAULT '0',
          register_ip text(15) DEFAULT '',
          login_time integer(11) DEFAULT '0',
          login_count integer(11) DEFAULT '0',
          login_ip text(15) DEFAULT '',
          valid integer(1) DEFAULT '0',
          source text(20) DEFAULT '',
          lang text(50) DEFAULT '',
          idvalid integer(1) DEFAULT '0',
          reidinfo text(100) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_user_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO  {$_M['table']['user']}  (id, username, password, head, email, tel, groupid, register_time, register_ip, login_time, login_count, login_ip, valid, source, lang, idvalid, reidinfo) SELECT id, username, password, head, email, tel, groupid, register_time, register_ip, login_time, login_count, login_ip, valid, source, lang, idvalid, reidinfo FROM _met_user_old;";
            DB::query($sql);
        }

    }

    public function change_met_user_group()
    {
        global $_M;
        //met_user_group
        $sql = "ALTER TABLE {$_M['table']['user_group']} RENAME TO _met_user_group_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['user_group']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              name text(255) DEFAULT '',
              access integer(11) DEFAULT '0',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_user_group_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['user_group']} (id, name, access, lang) SELECT id, name, access, lang FROM _met_user_group_old;";
            DB::query($sql);
        }
    }

    public function change_met_user_group_pay()
    {
        global $_M;
        //met_user_group_pay
        $sql = "ALTER TABLE {$_M['table']['user_group_pay']} RENAME TO _met_user_group_pay_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['user_group_pay']} (
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              groupid integer(11) DEFAULT '0',
              price REAL(10,2) DEFAULT '0.00',
              recharge_price REAL(10,2) DEFAULT '0.00',
              buyok integer(1) DEFAULT '0',
              rechargeok integer(50) DEFAULT '0',
              lang text(50) DEFAULT ''
            );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_user_group_pay_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['user_group_pay']}  (id, groupid, price, recharge_price, buyok, rechargeok, lang) SELECT id, groupid, price, recharge_price, buyok, rechargeok, lang FROM _met_user_group_pay_old;";
            DB::query($sql);
        }
    }

    public function change_met_user_list()
    {
        global $_M;
        //met_user_list
        $sql = "ALTER TABLE {$_M['table']['user_list']} RENAME TO _met_user_list_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['user_list']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          listid integer(11) DEFAULT '0',
          paraid integer(11) DEFAULT '0',
          info text,
          lang text(50) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_user_list_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['user_list']} (id, listid, paraid, info, lang) SELECT id, listid, paraid, info, lang FROM _met_user_list_old;";
            DB::query($sql);
        }
    }

    public function change_met_user_other()
    {
        global $_M;
        //met_user_other
        $sql = "ALTER TABLE {$_M['table']['user_other']} RENAME TO _met_user_other_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['user_other']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          met_uid integer(11) DEFAULT '0',
          openid text(100) DEFAULT '',
          unionid text(100) DEFAULT '',
          access_token text(255) DEFAULT '',
          expires_in integer(11) DEFAULT '0',
          type text(10) DEFAULT ''
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_user_other_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['user_other']} (id, met_uid, openid, unionid, access_token, expires_in, type) SELECT id, met_uid, openid, unionid, access_token, expires_in, type FROM _met_user_other_old;";
            DB::query($sql);
        }
    }

    public function change_met_weixin_reply_log()
    {
        global $_M;
        //met_weixin_reply_log
        $sql = "ALTER TABLE {$_M['table']['weixin_reply_log']} RENAME TO _met_weixin_reply_log_old;";
        DB::query($sql);

        $sql = "CREATE TABLE {$_M['table']['weixin_reply_log']} (
          id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
          FromUserName text(255) DEFAULT '',
          Content text,
          rid integer(11),
          CreateTime integer(10)
        );";
        DB::query($sql);

        $sql = "SELECT * FROM _met_weixin_reply_log_old LIMIT 1";
        $one = DB::query($sql);

        if ($one) {
            $sql = "INSERT INTO {$_M['table']['weixin_reply_log']} (id, FromUserName, Content, rid, CreateTime) SELECT id, FromUserName, Content, rid, CreateTime FROM _met_weixin_reply_log_old;";
            DB::query($sql);
        }
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.;
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
