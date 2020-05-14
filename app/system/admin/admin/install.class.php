<?php
defined('IN_MET') or exit ('No permission');
load::sys_class('admin');
load::sys_func('file');
class install extends admin{

    public function __construct() {
        global $_M;
        parent::__construct();
        $this->version = '7.1.0';
    }

    public function dotest()
    {
        self::update_web_lang($this->version);
        die("OK");
    }

    public function dosql() {
        global $_M;
        die("AS");

        $this->update_system($this->version);

        $arr = explode('/', trim($_M['url']['site_admin'],'/'));
        $admin_dir = array_pop($arr);
        $admin_lock = PATH_WEB . "{$admin_dir}/admin.lock";
        file_put_contents($admin_lock, 'admin_dir');

        //清除缓存
        deldir('upload/thumb_src', 1);
        deldir('cache', 1);
    }

    public function update_system($version)
    {
        global $_M;
        //更新版本号
        $this->update_ver($version);
        file_put_contents(PATH_WEB . 'update_logs.txt',"update_ver\n",FILE_APPEND);

        //检测新增数据表和字段
        $this->diff_fields($version);
        file_put_contents(PATH_WEB . 'update_logs.txt',"diff_fields\n",FILE_APPEND);

        //更新表字段默认值
        $this->alter_table($version);
        file_put_contents(PATH_WEB . 'update_logs.txt',"alter_table\n",FILE_APPEND);

        //注册数据表
        $this->table_regist();
        file_put_contents(PATH_WEB . 'update_logs.txt',"table_regist\n",FILE_APPEND);

        //添加配置
        $this->add_config();
        file_put_contents(PATH_WEB . 'update_logs.txt',"add_config\n",FILE_APPEND);

        //备份用户临时数据
        ###$this->temp_data();

        //恢复用户数据
        ###$this->recovery_data();

        //更新后台栏目
        #$this->update_admin_column();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"update_admin_column\n",FILE_APPEND);

        //6.1/6.2->7.0
        //表单模块数据迁移
        #$this->recovery_form_seting();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_form_seting\n",FILE_APPEND);

        //更新online数据
        #$this->recovery_online();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_online\n",FILE_APPEND);

        //更新友情链接数据
        #$this->recovery_link();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_link\n",FILE_APPEND);

        //更新新闻发布人
        #$this->recovery_news();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_news\n",FILE_APPEND);

        //更新栏目信息
        #$this->recovery_column();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_column\n",FILE_APPEND);

        //更新applist
        #$this->update_app_list();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"update_app_list\n",FILE_APPEND);

        #$this->update_tags();
        #file_put_contents(PATH_WEB . 'update_logs.txt',"update_tags\n",FILE_APPEND);

        //更新语言
        $this->update_language($version);
        file_put_contents(PATH_WEB . 'update_logs.txt',"update_language\n",FILE_APPEND);

        return;
    }

    /**
     * 更新版本号
     * @param string $version
     */
    public function update_ver($version = '')
    {
        global $_M;
        $query = "UPDATE {$_M['table']['config']} SET value = '{$version}' WHERE name = 'metcms_v'";
        DB::query($query);
        return;
    }

    /**
     * 对比数据库机构
     * @param $version
     */
    public function diff_fields($version)
    {
        global $_M;
        $diffs = self::get_diff_tables(PATH_WEB . 'config/v' . $version . 'mysql.json');
        if(isset($diffs['table'])){
            foreach ($diffs['table'] as $table => $detail) {
                $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (";
                foreach ($detail as $k => $v) {
                    if($k == 'id'){
                        $sql.= "`{$k}` {$v['Type']} {$v['Extra']} ,";
                    }else{
                        $sql .= "`{$k}` {$v['Type']}  ";
                        if ($v['Default'] === null) {
                            $sql .= " DEFAULT NULL ";
                        }else{
                            $sql .= " DEFAULT '{$v['Default']}' ";
                        }
                        $sql .= " {$v['Extra']} ,";
                    }
                }
                $sql.="PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                DB::query($sql);
                add_table(str_replace($_M['config']['tablepre'], '', $table));
            }
        }

        if(isset($diffs['field']))
        {
            foreach ($diffs['field'] as $table => $v) {
                foreach ($v as $field => $f) {
                    $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}`  {$f['Type']} ";

                    if ($f['Default'] === null) {
                        $sql .= " DEFAULT NULL ";
                    }else{
                        $sql .= " DEFAULT '{$f['Default']}' ";
                    }

                    DB::query($sql);
                }
            }
        }
    }

    /**
     * 更新表字段默认值
     * @param $version
     */
    public function alter_table()
    {
        global $_M;
        $base = self::get_base_table();
        foreach ($base as $table_name => $table) {
            $table_name_now = str_replace('met_', $_M['config']['tablepre'], $table_name);
            $sql = "ALTER TABLE `{$table_name_now}` ";
            foreach ($table as $key => $field){
                if ($key == 'id') {
                    continue;
                }

                $sql .= " MODIFY COLUMN `{$field['Field']}` {$field['Type']} ";
                if ($field['Default'] === null) {
                    $sql .= " DEFAULT NULL ";
                }else{
                    $sql .= " DEFAULT '{$field['Default']}' ";
                }
                $sql .= ',';
            }
            $sql= trim($sql, ',') . ';';
            DB::query($sql);
        }
    }

    /**
     * 注册数据表
     */
    public function table_regist()
    {
        global $_M;
        add_table('met_weixin_reply_log');
    }

    /**
     * 更新系统配置
     */
    public function add_config()
    {
        global $_M;
        foreach (array_keys($_M['langlist']['web']) as $lang) {
            //other_user_weixin
            self::update_config('met_weixin_gz_token', '', 0, $lang);
        }

        //global
        self::update_config('met_copyright_type', '0', 0, 'metinfo');
        self::update_config('met_agents_copyright_foot1', '本站基于 <b><a href=https://www.metinfo.cn target=_blank title=CMS>米拓企业建站系统搭建 $metcms_v</a></b> &copy;2008-$m_now_year', 0, 'metinfo');
        self::update_config('met_agents_copyright_foot2', '技术支持：<b><a href=https://www.mituo.cn target=_blank title=CMS>米拓建站 $metcms_v</a></b> &copy;2008-$m_now_year', 0, 'metinfo');
    }

    /**
     * 更新语言
     */
    public function update_language($version)
    {
        self::update_admin_lang($version);
        self::update_web_lang($version);
    }

    /**
     * 更新后台语言
     * @param $version
     */
    public function update_admin_lang($version)
    {
        global $_M;
        //添加管理员语言
        $query = "SELECT * FROM {$_M['table']['lang_admin']} WHERE lang = 'cn' AND mark = 'cn'";
        $res = DB::get_one($query);
        if (!$res) {
            $query = "INSERT INTO {$_M['table']['lang_admin']} SET name = '简体中文', useok = 1, no_order = 1, mark = 'cn', synchronous = 'cn',  link = '', lang = 'cn' ";
            DB::query($query);
        }

        //本地指纹
        $path_cn = PATH_WEB . "v{$version}lang_admin_cn.json";
        $path_en = PATH_WEB . "v{$version}lang_admin_en.json";
        $lang_json_cn = "https://www.metinfo.cn/upload/json/v{$version}lang_cn.json";
        $lang_json_en = "https://www.metinfo.cn/upload/json/v{$version}lang_en.json";

        $sql = "SELECT * FROM {$_M['table']['lang_admin']} ";
        $admin_lang_list = DB::get_all($sql);
        foreach ($admin_lang_list as $row) {
            $lang = $row['lang'];
            //语言
            if ($lang != 'en') {
                $json_sql = $lang_json_cn;
                $path = $path_cn;
            }else{
                $json_sql = $lang_json_en;
                $path = $path_en;
            }

            //获取语言对照文件
//            $lang_json = file_get_contents($json_sql);
//            if (!$lang_json) {
//                $lang_json = self::curl($json_sql);
//                if (!$lang_json) {
//                    $lang_json = file_get_contents($path);
//                }
//            }
            $lang_json = file_get_contents($path);
            $lang_data = json_decode($lang_json, true);

            if (is_array($lang_data)) {
                $sql = "DELETE FROM {$_M['table']['language']} WHERE lang = '{$lang}' AND  site = 1 AND (app = 0 OR app = 1 OR app = 50002)";
                DB::query($sql);
                foreach ($lang_data as $lang_row) {
                    if ($lang_row['site'] == 1) {
                        self::add_language($lang_row);
                    }
                }
            }
        }

        unlink($path_cn);
        unlink($path_en);
    }

    /**
     * 更新前台语言
     * @param $version
     */
    public function update_web_lang($version)
    {
        global $_M;
        //本地指纹
        $path_cn = PATH_WEB . "update_7.1.0/v{$version}lang_web_cn.json";
        $path_en = PATH_WEB . "update_7.1.0/v{$version}lang_web_en.json";

        $sql = "SELECT * FROM {$_M['table']['lang']} ";
        $web_lang_list = DB::get_all($sql);
        foreach ($web_lang_list as $row) {
            $lang = $row['lang'];
            //语言
            if ($lang != 'en') {
                $path = $path_cn;
            } else {
                $path = $path_en;
            }

            //获取语言对照文件
            $lang_json = file_get_contents($path);
            $lang_data = json_decode($lang_json, true);
            dump($path);

            if (is_array($lang_data)) {
                $query = "SELECT `id`,`name` FROM {$_M['table']['language']} WHERE lang = '{$lang}' AND site = '0'";
                $web_lang = DB::get_all($query);

                $old_lang_index = array();
                foreach ($web_lang as $lang_item) {
                    $old_lang_index[] = $lang_item;
                }


                $new_lang_index = array_keys($lang_data);
                $diff_lang_idnex = array_diff($new_lang_index, $old_lang_index);
                dump($old_lang_index);
                dump($new_lang_index);
                dump($diff_lang_idnex);
                die();

                foreach ($diff_lang_idnex as $name) {
                    if ($lang_data[$name]['site'] == 0) {
                        dump($lang_data[$name]);
                        #self::add_language($lang_data[$name]);
                    }

                }
            }
        }

        #unlink($path_cn);
        #unlink($path_en);
    }

    /**
     * 备份用户临时数据
     * @return array
     */
    public function temp_data()
    {
        global $_M;

        $data = array();
        $data['met_secret_key'] = $_M['config']['met_secret_key'];
        $data['last_version']   = $_M['config']['metcms_v'];
        $data['tablename']      = $_M['config']['met_tablename'];

        $query = "SELECT * FROM {$_M['table']['applist']}";
        $data['applist'] = DB::get_all($query);

        //用户数据缓存
        Cache::put('temp_data',$data);
        return $data;
    }

    /**
     * 恢复用户数据
     */
    public function recovery_data()
    {
        global $_M;
        if(file_exists(PATH_WEB.'cache/temp_data.php')){
            $data = Cache::get('temp_data');
            add_table($data['tablename']);

            //恢复注册数据表
            $query = "SELECT value FROM {$_M['table']['config']} WHERE name = 'met_tablename'";
            $config = DB::get_one($query);
            $_Mettables = explode('|', $config['value']);
            foreach ($_Mettables as $key => $val) {
                $_M['table'][$val] = $_M['config']['tablepre'].$val;
            }

            //恢复用户TOKEN
            $query = "UPDATE {$_M['table']['config']} SET value = '{$data['met_secret_key']}' WHERE name = 'met_secret_key'";
            DB::query($query);

            //恢复系统版本呢
            $query = "UPDATE {$_M['table']['config']} SET value = '{$data['last_version']}' WHERE name = 'metcms_v'";
            DB::query($query);


            //恢复应用列表数据
            foreach ($data['applist'] as $app) {
                $query = "SELECT id FROM {$_M['table']['applist']} WHERE m_name='{$app['m_name']}'";
                if(!DB::get_one($query) && file_exists(PATH_WEB.'app/app/'.$app['m_name'])){
                    unset($app['id']);
                    $sql = self::get_sql($app);
                    $query = "INSERT INTO {$_M['table']['applist']} SET {$sql}";
                    DB::query($query);
                }
            }
        }


        //删除临时数据文件
        Cache::del('temp_data');
    }

    /**
     * 表单模块数据迁移
     */
    public function recovery_form_seting()
    {
        global $_M;
        //job
        foreach (array_keys($_M['langlist']['web']) as $lang) {
            //栏目初始化
            ###$this->colum_label ->get_column($lang);

            $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' AND module = 6";
            $jobs = DB::get_all($query);
            if ($jobs) {
                foreach ($jobs as $job) {
                    self::recovery_job($job, $lang);
                }
            }

            //message
            $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' AND module = 7";
            $message = DB::get_one($query);
            if ($message) {
                self::recovery_message($message, $lang);
            }


            //feedback
            $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' AND module = 8";
            $feedbacks = DB::get_all($query);
            if ($feedbacks) {
                foreach ($feedbacks as $fd) {
                    self::recovery_feedback($fd, $lang);
                }
            }
        }
    }

    /**
     * 更新job配置
     */
    public function recovery_job($data = array(), $lang = '')
    {
        global $_M;
        file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_job\n",FILE_APPEND);
        if($data && $lang){
            //跟新内容层级关系
            #$class123 = self::getClass123($data['id'], $lang);
            #$query = "UPDATE {$_M['table']['job']} SET class1 = '{$class123['class1']['id']}' , class2 = '{$class123['class2']['id']}', class3 = '{$class123['class3']['id']}'";
            $query = "UPDATE {$_M['table']['job']} SET class1 = '{$data['id']}' , class2 = '0', class3 = '0' WHERE lang = '{$lang}'";
            DB::query($query);

            //更新表单配置
            $array = array();
            $array[] = array('met_cv_time', '120');
            $array[] = array('met_cv_image', '');
            $array[] = array('met_cv_showcol', '');
            $array[] = array('met_cv_emtype', '1');
            $array[] = array('met_cv_type', '');
            $array[] = array('met_cv_to', '');
            $array[] = array('met_cv_job_tel', '');
            $array[] = array('met_cv_back', '');
            $array[] = array('met_cv_email', '');
            $array[] = array('met_cv_title', '');
            $array[] = array('met_cv_content', '');
            $array[] = array('met_cv_sms_back', '');
            $array[] = array('met_cv_sms_tell', '');
            $array[] = array('met_cv_sms_content', '');

            $column_config = self::getClassConfig($data['id']);

            foreach ($array as $row) {
                $name = $row[0];
                $value = $column_config[$name] ? $column_config[$name] : $row[1];
                $cid = $data['id'];
                $lang = $data['lang'];
                self::update_config($name, $value, $cid, $lang);
            }

            //删除无用配置
            $query = "DELETE FROM {$_M['table']['config']} WHERE name LIKE '%met_cv%' AND lang = '{$data['lang']}' AND columnid = '0'";
            DB::query($query);
        }
        return;
    }

    /**
     * 更新message配置
     */
    public function recovery_message($data = array(), $lang = '')
    {
        global $_M;
        file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_message\n",FILE_APPEND);
        if ($data && $lang) {
            $array = array();
            $array[] = array('met_msg_ok', '');
            $array[] = array('met_msg_time', '120');
            $array[] = array('met_msg_name_field', '');
            $array[] = array('met_msg_content_field', '');
            $array[] = array('met_msg_show_type', '1');
            $array[] = array('met_msg_type', '1');
            $array[] = array('met_msg_to', '');
            $array[] = array('met_msg_admin_tel', '');
            $array[] = array('met_msg_back', '');
            $array[] = array('met_msg_email_field', '');
            $array[] = array('met_msg_title', '');
            $array[] = array('met_msg_content', '');
            $array[] = array('met_msg_sms_back', '');
            $array[] = array('met_msg_sms_field', '');
            $array[] = array('met_msg_sms_content', '');

            $column_config = self::getClassConfig($data['id']);

            foreach ($array as $row) {
                $name = $row[0];
                $value = $column_config[$name] ? $column_config[$name] : $row[1];
                $cid = $data['id'];
                $lang = $data['lang'];
                self::update_config($name, $value, $cid, $lang);
            }
        }
    }

    /**
     * 更新feedback配置
     */
    public function recovery_feedback($data = array(), $lang = '')
    {
        global $_M;
        file_put_contents(PATH_WEB . 'update_logs.txt',"recovery_feedback\n",FILE_APPEND);
        if ($data && $lang) {
            $column = self::getClassById($data['id'], $lang);
            $met_fdtable = $column['name'];     //反馈表单名称

            $array = array();
            $array[] = array('met_fd_ok', '');
            $array[] = array('met_fdtable', $met_fdtable);
            ###$array[] = array('met_fd_class', '');
            $array[] = array('met_fd_time', '120');
            $array[] = array('met_fd_related', '');
            $array[] = array('met_fd_showcol', '');
            $array[] = array('met_fd_inquiry', '');
            $array[] = array('met_fd_type', '');
            $array[] = array('met_fd_to', '');
            $array[] = array('met_fd_admin_tel', '');
            $array[] = array('met_fd_back', '');
            $array[] = array('met_fd_email', '');
            $array[] = array('met_fd_title', '');
            $array[] = array('met_fd_content', '');
            $array[] = array('met_fd_sms_back', '');
            $array[] = array('met_fd_sms_tell', '');
            $array[] = array('met_fd_sms_content', '');

            $column_config = self::getClassConfig($data['id']);

            foreach ($array as $row) {
                $name = $row[0];
                $value = $column_config[$name] ? $column_config[$name] : $row[1];
                $cid = $data['id'];
                $lang = $data['lang'];
                self::update_config($name, $value, $cid, $lang);
            }
        }
    }

    /**
     * 更新online数据
     */
    public function recovery_online()
    {
        global $_M;
        /*if(file_exists(PATH_WEB.'cache/temp_online.php')){
            $data = Cache::get('temp_online');
        }*/

        $query = "SELECT * FROM {$_M['table']['online']}";
        $data = DB::get_all($query);

        //清空online表
        ##$query = "TRUNCATE TABLE {$_M['table']['online']} ";
        $query = "DELETE FROM {$_M['table']['online']} ";
        DB::query($query);
        $query = "ALTER TABLE `{$_M['table']['online']}` 
                    DROP COLUMN `qq`,
                    DROP COLUMN `msn`,
                    DROP COLUMN `taobao`,
                    DROP COLUMN `alibaba`,
                    DROP COLUMN `skype`;
                    ";
        DB::query($query);

        foreach (array_keys($_M['langlist']['web']) as $lang) {
            //客服默认样式
            self::update_config('met_online_skin', 3, 0, $lang);
            //插入新客服数据
            self::onlineInsert($data, $lang);
        }
        return;
    }

    /**
     * 插入新客服数据
     * @param array $online_list
     * @param string $lang
     */
    private function onlineInsert($online_list = array(),$lang = '')
    {
        global $_M;
        foreach ($online_list as $online) {
            if ($online['lang'] == $lang) {
                if ($online['qq']) {
                    $name = $online['name'];
                    $no_order = $online['no_order'];
                    $value = $online['qq'];
                    $icon = 'icon fa-qq';
                    $type = '0';
                    $query = "INSERT INTO {$_M['table']['online']} (name,no_order,lang,value,icon,type) VALUES ('{$name}','{$no_order}','{$lang}','{$value}','{$icon}','{$type}')";
                    DB::query($query);
                }
                if ($online['msn']) {
                    $name = $online['name'];
                    $no_order = $online['no_order'];
                    $value = $online['sms'];
                    $icon = 'icon fa-facebook';
                    $type = '6';
                    $query = "INSERT INTO {$_M['table']['online']} (name,no_order,lang,value,icon,type) VALUES ('{$name}','{$no_order}','{$lang}','{$value}','{$icon}','{$type}')";
                    DB::query($query);
                }
                if ($online['taobao']) {
                    $name = $online['name'];
                    $no_order = $online['no_order'];
                    $value = $online['taobao'];
                    $icon = 'icon fa-comment';
                    $type = '1';
                    $query = "INSERT INTO {$_M['table']['online']} (name,no_order,lang,value,icon,type) VALUES ('{$name}','{$no_order}','{$lang}','{$value}','{$icon}','{$type}')";
                    DB::query($query);
                }
                if ($online['alibaba']) {
                    $name = $online['name'];
                    $no_order = $online['no_order'];
                    $value = $online['alibaba'];
                    $icon = 'icon fa-comment';
                    $type = '2';
                    $query = "INSERT INTO {$_M['table']['online']} (name,no_order,lang,value,icon,type) VALUES ('{$name}','{$no_order}','{$lang}','{$value}','{$icon}','{$type}')";
                    DB::query($query);
                }
                if ($online['skype']) {
                    $name = $online['name'];
                    $no_order = $online['no_order'];
                    $value = $online['skype'];
                    $icon = 'icon fa-skype';
                    $type = '5';
                    $query = "INSERT INTO {$_M['table']['online']} (name,no_order,lang,value,icon,type) VALUES ('{$name}','{$no_order}','{$lang}','{$value}','{$icon}','{$type}')";
                    DB::query($query);
                }

            }
        }
    }

    /**
     * 更新友情链接数据
     */
    public function recovery_link()
    {
        global $_M;
        $query = "UPDATE {$_M['table']['link']} SET module = ',10001,'";
        DB::query($query);

    }

    /**
     * 更新友情链接数据
     */
    public function recovery_news()
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['news']}";
        $news_list = DB::get_all($query);
        foreach ($news_list as $news) {
            if ($news['issue']) {
                $query = "UPDATE {$_M['table']['news']} SET publisher = '{$news['issue']}' WHERE id = {$news['id']}";
                DB::query($query);
            }
        }
        return;
    }

    /**
     * 更新栏目数据
     */
    public function recovery_column()
    {
        return;
        global $_M;
        foreach (array_keys($_M['langlist']['web']) as $lang) {
            $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' ";
            $column_list = DB::get_all($query);
            foreach ($column_list as $column) {
                if ($column['module'] == 2) {
                    $list_length = $_M['config']['met_news_list'];
                }
                if ($column['module'] == 3) {
                    $list_length = $_M['config']['met_product_list'];
                }
                if ($column['module'] == 4) {
                    $list_length = $_M['config']['met_download_list'];
                }
                if ($column['module'] == 5) {
                    $list_length = $_M['config']['met_img_list'];
                }
                if ($column['module'] == 6) {
                    $list_length = $_M['config']['met_job_list'];
                }
                if ($column['module'] == 7) {
                    $list_length = $_M['config']['met_message_list'];
                }
                if ($column['module'] == 11) {
                    $list_length = $_M['config']['met_search_list'];
                }

                $list_length = $list_length ? $list_length : 0;

                $query = "UPDATE {$_M['table']['column']} SET list_length = '{$list_length}' WHERE id = {$column['id']}";
                DB::query($query);

            }
        }


    }

    /**
     * 更新后台栏目数据
     */
    public function update_admin_column()
    {
        global $_M;
        $admin_array = self::admin_array();
        if (is_array($admin_array)) {
            //清空老数据表
            #$query = "TRUNCATE TABLE {$_M['table']['admin_column']} ";
            $query = "DELETE FROM {$_M['table']['admin_column']} ";
            DB::query($query);

            foreach ($admin_array as $row) {
                $sql = get_sql($row);
                $query = "INSERT INTO {$_M['table']['admin_column']} SET {$sql}";
                DB::query($query);
            }
        }
    }

    /**
     * @return array
     */
    protected function admin_array()
    {
        global $_M;
        $admin_array = array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'lang_administration',
                    'url' => 'manage',
                    'bigclass' => '0',
                    'field' => '1301',
                    'type' => '1',
                    'list_order' => '0',
                    'icon' => 'manage',
                    'info' => '',
                    'display' => '1',
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => 'lang_htmColumn',
                    'url' => 'column',
                    'bigclass' => '0',
                    'field' => '1201',
                    'type' => '1',
                    'list_order' => '1',
                    'icon' => 'column',
                    'info' => '',
                    'display' => '1',
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => 'lang_feedback_interaction',
                    'url' => '',
                    'bigclass' => '0',
                    'field' => '1202',
                    'type' => '1',
                    'list_order' => '2',
                    'icon' => 'feedback-interaction',
                    'info' => '',
                    'display' => '1',
                ),
            3 =>
                array(
                    'id' => '4',
                    'name' => 'lang_seo_set_v6',
                    'url' => 'seo',
                    'bigclass' => '0',
                    'field' => '1404',
                    'type' => '1',
                    'list_order' => '3',
                    'icon' => 'seo',
                    'info' => '',
                    'display' => '1',
                ),
            4 =>
                array(
                    'id' => '5',
                    'name' => 'lang_appearance',
                    'url' => 'app/met_template',
                    'bigclass' => '0',
                    'field' => '1405',
                    'type' => '1',
                    'list_order' => '4',
                    'icon' => 'template',
                    'info' => '',
                    'display' => '1',
                ),
            5 =>
                array(
                    'id' => '6',
                    'name' => 'lang_myapp',
                    'url' => 'myapp',
                    'bigclass' => '0',
                    'field' => '1505',
                    'type' => '1',
                    'list_order' => '5',
                    'icon' => 'application',
                    'info' => '',
                    'display' => '1',
                ),
            6 =>
                array(
                    'id' => '7',
                    'name' => 'lang_the_user',
                    'url' => '',
                    'bigclass' => '0',
                    'field' => '1506',
                    'type' => '1',
                    'list_order' => '6',
                    'icon' => 'user',
                    'info' => '',
                    'display' => '1',
                ),
            7 =>
                array(
                    'id' => '8',
                    'name' => 'lang_safety',
                    'url' => '',
                    'bigclass' => '0',
                    'field' => '1200',
                    'type' => '1',
                    'list_order' => '7',
                    'icon' => 'safety',
                    'info' => '',
                    'display' => '1',
                ),
            8 =>
                array(
                    'id' => '9',
                    'name' => 'lang_multilingual',
                    'url' => 'language',
                    'bigclass' => '0',
                    'field' => '1002',
                    'type' => '1',
                    'list_order' => '8',
                    'icon' => 'multilingualism',
                    'info' => '',
                    'display' => '1',
                ),
            9 =>
                array(
                    'id' => '10',
                    'name' => 'lang_unitytxt_39',
                    'url' => '',
                    'bigclass' => '0',
                    'field' => '1100',
                    'type' => '1',
                    'list_order' => '9',
                    'icon' => 'setting',
                    'info' => '',
                    'display' => '1',
                ),
            10 =>
                array(
                    'id' => '11',
                    'name' => 'cooperation_platform',
                    'url' => 'partner',
                    'bigclass' => '0',
                    'field' => '1508',
                    'type' => '1',
                    'list_order' => '10',
                    'icon' => 'partner',
                    'info' => '',
                    'display' => '1',
                ),
            11 =>
                array(
                    'id' => '21',
                    'name' => 'lang_mod8',
                    'url' => 'feed_feedback_8',
                    'bigclass' => '3',
                    'field' => '1509',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'feedback',
                    'info' => '',
                    'display' => '1',
                ),
            12 =>
                array(
                    'id' => '22',
                    'name' => 'lang_mod7',
                    'url' => 'feed_message_7',
                    'bigclass' => '3',
                    'field' => '1510',
                    'type' => '2',
                    'list_order' => '1',
                    'icon' => 'message',
                    'info' => '',
                    'display' => '1',
                ),
            13 =>
                array(
                    'id' => '23',
                    'name' => 'lang_mod6',
                    'url' => 'feed_job_6',
                    'bigclass' => '3',
                    'field' => '1511',
                    'type' => '2',
                    'list_order' => '2',
                    'icon' => 'recruit',
                    'info' => '',
                    'display' => '1',
                ),
            14 =>
                array(
                    'id' => '24',
                    'name' => 'lang_customerService',
                    'url' => 'online',
                    'bigclass' => '3',
                    'field' => '1106',
                    'type' => '2',
                    'list_order' => '3',
                    'icon' => 'online',
                    'info' => '',
                    'display' => '1',
                ),
            15 =>
                array(
                    'id' => '25',
                    'name' => 'lang_indexlink',
                    'url' => 'link',
                    'bigclass' => '4',
                    'field' => '1406',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'link',
                    'info' => '',
                    'display' => '0',
                ),
            16 =>
                array(
                    'id' => '26',
                    'name' => 'lang_member',
                    'url' => 'user',
                    'bigclass' => '7',
                    'field' => '1601',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'member',
                    'info' => '',
                    'display' => '1',
                ),
            17 =>
                array(
                    'id' => '27',
                    'name' => 'lang_managertyp2',
                    'url' => 'admin/user',
                    'bigclass' => '7',
                    'field' => '1603',
                    'type' => '2',
                    'list_order' => '1',
                    'icon' => 'administrator',
                    'info' => '',
                    'display' => '1',
                ),
            18 =>
                array(
                    'id' => '28',
                    'name' => 'lang_safety_efficiency',
                    'url' => 'safe',
                    'bigclass' => '8',
                    'field' => '1004',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'safe',
                    'info' => '',
                    'display' => '1',
                ),
            19 =>
                array(
                    'id' => '29',
                    'name' => 'lang_data_processing',
                    'url' => 'databack',
                    'bigclass' => '8',
                    'field' => '1005',
                    'type' => '2',
                    'list_order' => '1',
                    'icon' => 'databack',
                    'info' => '',
                    'display' => '1',
                ),
            20 =>
                array(
                    'id' => '30',
                    'name' => 'lang_upfiletips7',
                    'url' => 'webset',
                    'bigclass' => '10',
                    'field' => '1007',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'information',
                    'info' => '',
                    'display' => '1',
                ),
            21 =>
                array(
                    'id' => '31',
                    'name' => 'lang_indexpic',
                    'url' => 'imgmanage',
                    'bigclass' => '10',
                    'field' => '1003',
                    'type' => '2',
                    'list_order' => '1',
                    'icon' => 'picture',
                    'info' => '',
                    'display' => '1',
                ),
            22 =>
                array(
                    'id' => '32',
                    'name' => 'lang_banner_manage',
                    'url' => 'banner',
                    'bigclass' => '10',
                    'field' => '1604',
                    'type' => '2',
                    'list_order' => '2',
                    'icon' => 'banner',
                    'info' => '',
                    'display' => '1',
                ),
            23 =>
                array(
                    'id' => '33',
                    'name' => 'lang_the_menu',
                    'url' => 'menu',
                    'bigclass' => '10',
                    'field' => '1605',
                    'type' => '2',
                    'list_order' => '3',
                    'icon' => 'bottom-menu',
                    'info' => '',
                    'display' => '1',
                ),
            24 =>
                array(
                    'id' => '34',
                    'name' => 'lang_checkupdate',
                    'url' => 'update',
                    'bigclass' => '37',
                    'field' => '1104',
                    'type' => '2',
                    'list_order' => '4',
                    'icon' => 'update',
                    'info' => '',
                    'display' => '0',
                ),
            25 =>
                array(
                    'id' => '35',
                    'name' => 'lang_appinstall',
                    'url' => 'appinstall',
                    'bigclass' => '6',
                    'field' => '1800',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'appinstall',
                    'info' => '',
                    'display' => '0',
                ),
            26 =>
                array(
                    'id' => '36',
                    'name' => 'lang_dlapptips6',
                    'url' => 'appuninstall',
                    'bigclass' => '6',
                    'field' => '1801',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'appuninstall',
                    'info' => '',
                    'display' => '0',
                ),
            27 =>
                array(
                    'id' => '37',
                    'name' => 'lang_top_menu',
                    'url' => 'top_menu',
                    'bigclass' => '0',
                    'field' => '1900',
                    'type' => '1',
                    'list_order' => '0',
                    'icon' => 'top_menu',
                    'info' => '',
                    'display' => '0',
                ),
            28 =>
                array(
                    'id' => '38',
                    'name' => 'lang_clearCache',
                    'url' => 'clear_cache',
                    'bigclass' => '37',
                    'field' => '1901',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'clear_cache',
                    'info' => '',
                    'display' => '0',
                ),
            29 =>
                array(
                    'id' => '39',
                    'name' => 'lang_funcCollection',
                    'url' => 'function_complete',
                    'bigclass' => '37',
                    'field' => '1902',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'function_complete',
                    'info' => '',
                    'display' => '0',
                ),
            30 =>
                array(
                    'id' => '40',
                    'name' => 'lang_environmental_test',
                    'url' => 'environmental_test',
                    'bigclass' => '37',
                    'field' => '1903',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'environmental_test',
                    'info' => '',
                    'display' => '0',
                ),
            31 =>
                array(
                    'id' => '41',
                    'name' => 'lang_navSetting',
                    'url' => 'navSetting',
                    'bigclass' => '6',
                    'field' => '1904',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'navSetting',
                    'info' => '',
                    'display' => '0',
                ),
            32 =>
                array(
                    'id' => '42',
                    'name' => 'lang_style_settings',
                    'url' => 'style_settings',
                    'bigclass' => '5',
                    'field' => '1905',
                    'type' => '2',
                    'list_order' => '0',
                    'icon' => 'style_settings',
                    'info' => '',
                    'display' => '0',
                ),
        );
        return $admin_array;
    }

    /**
     * 更新applist
     */
    public function update_app_list()
    {
        global $_M;
        $query = "UPDATE {$_M['table']['applist']} SET display = '1' WHERE no = '50002'";
        DB::query($query);
    }

    /**
     * tags数据迁移
     */
    public function update_tags()
    {
        global $_M;
        $modules = array(2 => 'news', 3 => 'product', 4 => 'download', 5 => 'img');
        foreach ($modules as $mod => $table) {
            $query = "SELECT * FROM {$_M['table'][$table]} WHERE tag != '' AND tag IS NOT NULL AND recycle = 0";
            $list = DB::get_all($query);
            foreach ($list as $v) {
                if (!trim($v['tag'])) {
                    continue;
                }
                $tagStr = $v['tag'];
                $_M['lang'] = $v['lang'];
                $this->updateTags($tagStr, $mod, $v['class1'], $v['id'], 1);
            }
        }
    }

    public function updateTags($tagStr, $module, $class1, $id, $add = 0)
    {
        global $_M;

        $pinyin = load::sys_class('pinyin', 'new');
        $table = $this->getTableName($class1);
        $query = "SELECT tag FROM {$_M['table'][$table]} WHERE id = '{$id}'";
        $content = DB::get_one($query);

        $new = explode('|', $tagStr);
        $old = explode('|', $content['tag']);

        if ($add || $tagStr == $content['tag']) {
            // 如果文章或产品内容是新增的
            $old = array();
        }

        if (trim($content['tag'])) {
            $delete = array_diff($old, $new);
            if ($delete) {
                foreach ($delete as $key => $val) {
                    $query = "SELECT * FROM {$_M['table']['tags']} WHERE module = '{$module}' AND cid = '{$class1}' AND list_id like '%|{$id}|%' AND tag_name = '{$val}' AND lang = '{$_M['lang']}'";

                    $tags = DB::get_all($query);

                    foreach ($tags as $tag) {
                        if ($tag['list_id'] == "|{$id}|") {
                            // 如果tag表只存了id，直接删除
                            $query = "DELETE FROM {$_M['table']['tags']} WHERE id = '{$tag['id']}'";

                            DB::query($query);
                        } else {
                            $newId = str_replace("|{$id}|", '|', $tag['list_id']);
                            $query = "UPDATE {$_M['table']['tags']} SET list_id = '{$newId}' WHERE id = '{$tag['id']}'";

                            DB::query($query);
                        }
                    }
                }
            }
        }

        $create = array_diff($new, $old);
        if ($create) {
            foreach ($create as $val) {
                if (!trim($val)) {
                    continue;
                }
                if ($_M['config']['tag_search_type'] == 'module') {
                    $query = "SELECT * FROM {$_M['table']['tags']} WHERE module = '{$module}' AND tag_name = '{$val}' AND lang = '{$_M['lang']}'";
                } else {
                    $query = "SELECT * FROM {$_M['table']['tags']} WHERE module = '{$module}' AND cid = '{$class1}' AND tag_name = '{$val}' AND lang = '{$_M['lang']}'";
                }

                $tags = DB::get_one($query);
                $tag_pinyin = $pinyin->getpy($val);
                if ($tags) {
                    if ($tags['list_id'] && !strstr($tags['list_id'], "|{$id}|")) {
                        $newId = $tags['list_id']."{$id}|";
                        $query = "UPDATE {$_M['table']['tags']} SET list_id = '{$newId}',tag_name='{$val}',tag_pinyin='{$tag_pinyin}' WHERE id = '{$tags['id']}'";

                        DB::query($query);
                    }
                } else {
                    $data = array(
                        'tag_name' => $val,
                        'tag_pinyin' => $tag_pinyin,
                        'module' => $module,
                        'cid' => $class1,
                        'list_id' => "|{$id}|",
                        'lang' => $_M['lang'],
                    );

                    $set = array();
                    $vals = array();
                    foreach ($data as $col => $val) {
                        $set[] = "`$col`";
                        $vals[] = "'$val'";
                    }

                    $sql = 'INSERT INTO '
                        .$_M['table']['tags']
                        .' ('.implode(', ', $set).') '
                        .'VALUES ('.implode(', ', $vals).')';

                    DB::query($sql);
                }
            }
        }
    }

    public function getTableName($cid = 0)
    {
        global $_M;
        $column_db = load::mod_class('column/column_database', 'new');
        $category = $column_db->get_column_by_id($cid); //得到当前栏目
        $modules = array(2 => 'news', 3 => 'product', 4 => 'img', 5 => 'download');
        return $modules[$category['module']]; //得到表名
    }

    /*****************************工具方法******************************/
    /**
     * 获取标准数据库文件
     * @return mixed
     */
    public function get_base_table()
    {
        global $_M;
        $json_sql = "https://www.metinfo.cn/upload/json/v{$this->version}mysql.json";
        $path = PATH_WEB . "v{$this->version}mysql.json";

        $table_json = file_get_contents($json_sql);
        if (!$table_json) {
            $table_json = self::curl($json_sql);
            if (!$table_json) {
                $table_json = file_get_contents($path);
            }
        }
        $base = json_decode($table_json,true);
        return $base;
    }

    /**
     * @param $json_sql
     * @return array
     */
    public function get_diff_tables()
    {
        global $_M;
        $tables = self::list_tables();
        $base = self::get_base_table();

        $baseTables = array_keys($base);
        $diffTables = array_diff($baseTables, $tables);

        $noTables = array();
        $data = array();
        foreach ($diffTables as $noTable) {
            $table_name = $noTable;
            $noTable = str_replace('met_', $_M['config']['tablepre'], $noTable);
            $data['table'][$noTable] = $base[$table_name];
            $noTables[] = $noTable;
        }

        foreach ($base as $table => $val) {
            if(!in_array($table, $noTables)){
                $table = str_replace('met_', $_M['config']['tablepre'], $table);
                $fields = self::list_fields($table);
                $diff_field = array_diff_key($val, $fields);
                if($diff_field){
                    $data['field'][$table] = $diff_field;
                }
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public function list_tables() {
        global $_M;
        $query = "SHOW TABLE status";
        $tables = array();
        foreach (DB::get_all($query) as $key => $v) {
            $tables[] = str_replace($_M['config']['tablepre'], 'met_', $v['Name']);
        }
        return $tables;
    }

    /**
     * @param $table
     * @return array
     */
    public function list_fields($table) {
        global $_M;
        $query = "SHOW FULL FIELDS FROM {$table}";
        $fields = DB::get_all($query);
        $data = array();
        foreach ($fields as $key => $v) {
            $data[$v['Field']] = $v;
        }
        return $data;
    }

    /**
     * 更新配置
     * @param $name
     * @param $value
     * @param $cid
     * @param $lang
     */
    public function update_config($name,$value,$cid,$lang)
    {
        global $_M;
        $query = "SELECT id FROM {$_M['table']['config']} WHERE  name='{$name}' AND lang = '{$lang}'";
        $config = DB::get_one($query);
        if(!$config){
            $query = "INSERT INTO {$_M['table']['config']} (name,value,mobile_value,columnid,flashid,lang)VALUES ('{$name}', '{$value}', '', '{$cid}', '0', '{$lang}')";
            DB::query($query);
        }else{
            $query = "UPDATE {$_M['table']['config']} SET name = '{$name}',value = '{$value}', columnid = '{$cid}' ,lang = '{$lang}' WHERE id = '{$config['id']}'";
            DB::query($query);
        }
    }

    /**
     * 更新 插入语言
     * @param array $lang_data
     */
    public function add_language($lang_data = array())
    {
        global $_M;
        $name = $lang_data['name'];
        $value = $lang_data['value'];
        $site = $lang_data['site'];
        $lang = $lang_data['lang'];
        $js = $lang_data['array'] ? 1 : 0;
        $app = $lang_data['app'];

        if ($site == 1) {
            $query = "INSERT INTO {$_M['table']['language']} SET name = '{$name}',value='{$value}',site = '{$site}',no_order = 0,array='{$js}', app = '{$app}', lang='{$lang}';";
            DB::query($query);
        }

        if ($site == 0) {
            $query = "INSERT INTO {$_M['table']['language']} SET name = '{$name}',value='{$value}',site = '{$site}',no_order = 0,array='{$js}', app = '{$app}', lang='{$lang}';";
            DB::query($query);
        }

        /*$query = "SELECT * FROM {$_M['table']['language']} WHERE name = '{$name}' AND site = '{$site}'  AND lang = '{$lang}'";
        $has = DB::get_one($query);
        #file_put_contents(PATH_WEB . 'lang_select_sql.txt', "{$query}\n", FILE_APPEND);
        if(!$has){
            $query = "INSERT INTO {$_M['table']['language']} SET name = '{$name}',value='{$value}',site = '{$site}',no_order = 0,array='{$js}', app = '{$app}', lang='{$lang}'";
            DB::query($query);
            #file_put_contents(PATH_WEB . 'lang_add_sql.txt', "{$query}\n", FILE_APPEND);
        }else{
            if ($site == 1) {
                $query = "UPDATE {$_M['table']['language']} SET value='{$value}' WHERE name = '{$has['name']}' AND site = '{$site}' AND lang='{$lang}'";
                DB::query($query);
                #file_put_contents(PATH_WEB . 'lang_update_sql.txt', "{$query}\n", FILE_APPEND);
            }
        }*/
        return;
    }

    /**
     * 获取sql
     * @param $data
     * @return string
     */
    public function get_sql($data) {
        global $_M;
        $sql = "";
        foreach ($data as $key => $value) {
            if(strstr($value, "'")){
                $value = str_replace("'", "\'", $value);
            }
            $sql .= " {$key} = '{$value}',";
        }
        return trim($sql,',');
    }

    /**
     * 获取三级栏目信息
     */
    public function getClass123($cid = '', $lang = '')
    {
        global $_M;
        $classnow = self::getClassById($cid, $lang);

        $return = array();
        if ($classnow) {
            if ($classnow['classtype'] == 1) {
                $return['class1'] = $classnow;
                $return['class2'] = array();
                $return['class3'] = array();
            }

            if ($classnow['classtype'] == 2) {
                $return['class1'] = self::getClassById($classnow['bigclass'],$lang);
                $return['class2'] = $classnow;
                $return['class3'] = array();
            }

            if ($classnow['classtype'] == 3) {
                $bigclass = self::getClassById($classnow['bigclass'],$lang);
                $return['class1'] = self::getClassById($bigclass['bigclass'],$lang);
                $return['class2'] = $bigclass;
                $return['class3'] = $classnow;
            }
            return $return;
        }
        return false;
    }

    /**
     * 获取特定栏目信息
     * @param string $cid
     * @param string $lang
     * @return array|bool
     */
    public function getClassById($cid = '', $lang = '')
    {
        global $_M;
        if ($cid && $lang) {
            $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' AND id = '{$cid}'";
            $class = DB::get_one($query);
            return $class;
        }
        return false;
    }

    /**
     * 获取栏目配置
     * @param $class_id
     * @return array
     */
    public function getClassConfig($class_id = '')
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['config']} WHERE columnid = '{$class_id}'";
        $config_list = DB::get_all($query);
        $list = array();
        foreach($config_list as $key => $val){
            $list[$val['name']] = $val['value'];
        }
        return $list;
    }

    /**
     * CURL
     */
    protected function curl($url = '',$data = array(), $timeout = 30) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        #curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //不要删除该方法
    public function check()
    {
    }
}