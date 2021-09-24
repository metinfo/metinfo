<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('common');
/**
 * Class transfer
 */
class transfer extends common
{
    /******DB change*******/
    /**
     * mysql->sqlite
     */
    public function mysqlExportSqlite()
    {
        global $_M;
        $sqlite = new SQLite3(PATH_WEB.$_M['config']['db_name']);

        foreach ($_M['table'] as $key => $table) {
            $this->transferMysqlTableToSqllite($sqlite, $table);
        }
    }

    /**
     * sqlite->mysql
     * @param $config
     */
    public function sqliteExportMysql($config = array())
    {
        global $_M;
        @extract($config);
        $mysql = @new mysqli($con_db_host, $con_db_id, $con_db_pass, $con_db_name, $con_db_port);
        if ($mysql->connect_error) {
            halt($con_db_host);
        }

        if ($mysql->server_info > '4.1') {
            if (!$db_charset) {
                $db_charset = 'utf8';
            }
            if ($db_charset != 'latin1') {
                $mysql->query("SET character_set_connection=$db_charset, character_set_results=$db_charset, character_set_client=binary");
            }

            if ($mysql->server_info > '5.0.1') {
                $mysql->query("SET sql_mode=''");
            }
        }

        if ($con_db_name) {
            $mysql->select_db($con_db_name);
        }

        // $this->diff_fields($mysql, $_M['config']['metcms_v']);
        foreach ($_M['table'] as $key => $table) {
            $newTable = str_replace($_M['config']['tablepre'], $tablepre, $table);

            $drop = "DROP TABLE IF EXISTS $newTable;";
            $mysql->query($drop);
            $res = DB::$link->query("PRAGMA table_info(${table})");
            $tabledump = "CREATE TABLE IF NOT EXISTS `{$newTable}` (";
            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                if ($row['name'] == 'id') {
                    $tabledump .= '`id` int(11) NOT NULL AUTO_INCREMENT,';
                    continue;
                }
                $type = str_replace('text(', 'varchar(', $row['type']);
                $type = str_replace('integer(', 'int(', $type);
                $notnull = $row['notnull'] ? '' : 'NOT NULL';
                $notnull = '';
                $default = $row['dflt_value'] == 'NULL' ? '' : "DEFAULT {$row['dflt_value']}";
                if (trim($default) == 'DEFAULT') {
                    $default = '';
                }
                $tabledump .= "`{$row['name']}` {$type} {$notnull} {$default},";
            }
            if (!strstr($tabledump, 'AUTO_INCREMENT')) {
                continue;
            }
            $tabledump .= 'PRIMARY KEY (`id`)';
            $tabledump .= ') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
            $res->finalize();
            $res = $mysql->query($tabledump);

            $this->tranferSqliteRowsToMysql($mysql, $table, $newTable);
        }
        $mysql->close();
    }

    /**
     * mysql->dmsql
     * @param array $config
     */
    public function mySQLExportDMSQL($config = array())
    {
        global $_M;
        @extract($config);
        $link = dm_connect("{$con_db_host}:{$con_db_port}", $con_db_id, $con_db_pass);
        if (!$link) {
            halt(dm_error() . ':' . dm_errormsg());
        }
        $sql = "CREATE SCHEMA \"{$con_db_name}\" AUTHORIZATION \"{$con_db_id}\";";
        $result = dm_exec($link, $sql);
        if (!$result) {
            error("创建数据库失败__" . dm_error() . ':' . dm_errormsg());
        }

        dm_setoption($link, 1, 12345, 1);

        $sql = "SET SCHEMA {$con_db_name}";
        $result = dm_exec($link, $sql);
        if (!$result) {
            error(dm_error() . ':' . dm_errormsg());
        }

        foreach ($_M['table'] as $key => $table) {
            $newTable = str_replace($_M['config']['tablepre'], $tablepre, $table);

            //drop table
            $drop_sql = "DROP TABLE IF EXISTS $newTable;\n";

            //create table
            $sql = "SHOW CREATE TABLE {$table}";
            $res = DB::query($sql);
            $res = DB::fetch_row($res);
            if (!$res) {
                continue;
            }

            $sql = "SHOW COLUMNS FROM {$table}";
            $table_fields_info = DB::get_all($sql);
            $fields_num = count($table_fields_info);

            $id_field = false;
            $add_field = '';
            foreach ($table_fields_info as $field_info) {
                if ($field_info['Key'] == 'PRI') {
                    $id_field = true;
                    $pri = $field_info['Field'];
                    $add_field .= "\"{$pri}\" INT IDENTITY(1, 1) NOT NULL,\n";
                    continue;
                }

                //NAME
                $f_name = $field_info['Field'];

                //TYPE
                $type = $field_info['Type'];
                $type = strtoupper($type);
                switch ($type) {
                    case 'TEXT':
                    case 'LONGTEXT':
                    case 'MEDIUMTEXT':
                    case 'TINYTEXT':
                        $f_type = 'TEXT';
                        break;
                    case 'DATETIME':
                        $f_type = 'DATETIME';
                        break;
                    case 'DOUBLE':
                        break;
                    default:
                        $f_type = $type;
                        break;
                }
                //int
                if (strstr($type, 'INT') ) {
                    $f_type = "INT";
                }
                //var_chart
                if (strstr($type, 'CHAR') ) {
                    $resss = preg_match('/\(\d+\)/', $type, $match);
                    $leng = $match[0];
                    if ($resss) {
                        $f_type = "VARCHAR{$leng}";
                    }else{
                        $f_type = "VARCHAR(255)";
                    }
                }
                //double
                if (strstr($type, 'DOUBLE') ) {
                    $f_type = "DOUBLE";
                }
                //access type change
                $table_lsit = array('download', 'img', 'news', 'product', 'job', 'message', 'parameter', 'column');
                if (in_array($key, $table_lsit)) {
                    if ($type = 'TEXT') {
                        if (in_array($f_name, array('downloadaccess', 'access'))) {
                            $f_type = 'INT';
                        }
                    }
                }

                //NOT NULL
                $f_notnull = '';
                if (strtoupper($field_info['Null']) == 'NO') {
                    $f_notnull = "NOT NULL";
                }

                //default
                $f_default = $field_info['Default'] == '' ? '' : "DEFAULT '{$field_info['Default']}'";

                $add_field.= "\"{$f_name}\" {$f_type} {$f_notnull} {$f_default},\n";
            }

            $create_sql = "CREATE TABLE \"{$newTable}\" (\n";
            $create_sql .= $add_field;

            if ($id_field) {
                $create_sql .= "CLUSTER PRIMARY KEY(\"{$pri}\")\n";
            }

            $create_sql .= ")STORAGE(ON \"MAIN\", CLUSTERBTR);";

            //开启事务
            dm_autocommit($link);
            dm_exec($link, $drop_sql);
            $result = dm_exec($link, $create_sql);
            if (!$result) {
                error("{$create_sql}创建数据表失败__" . dm_error() . ':' . dm_errormsg());
            }

            //提交事务
            dm_commit($link);

            self::tranferMysqlRowToDmsql($link, $table, $newTable, 0);
        }

        @dm_close($link);
        return;
    }

    /**
     * dmsql->mysql
     * @param array $config
     */
    public function dmSQLExportMySQL($config = array())
    {
        global $_M;
        @extract($config);
        $mysql = @new mysqli($con_db_host, $con_db_id, $con_db_pass, $con_db_name, $con_db_port);
        if ($mysql->connect_error) {
            halt($mysql->connect_error);
        }

        if ($mysql->server_info > '4.1') {
            if (!$db_charset) {
                $db_charset = 'utf8';
            }
            if ($db_charset != 'latin1') {
                $mysql->query("SET character_set_connection=$db_charset, character_set_results=$db_charset, character_set_client=binary");
            }

            if ($mysql->server_info > '5.0.1') {
                $mysql->query("SET sql_mode=''");
            }
        }

        if ($con_db_name) {
            $mysql->select_db($con_db_name);
        }

        //循环创建数据表
        foreach ($_M['table'] as $key => $table) {
            $newTable = str_replace($_M['config']['tablepre'], $tablepre, $table);

            //删表
            $drop = "DROP TABLE IF EXISTS $newTable;";
            $mysql->query($drop);

            //创建新数据表
            $db_name = $_M['config']['con_db_name'];
            $sql = "SELECT * FROM all_tab_columns WHERE owner='{$db_name}' AND Table_Name='{$table}'";
            $table_fields_info = DB::get_all($sql); //字段信息
            if (!$table_fields_info) { //表信息不存在
                continue;
            }

            $id_field = false;
            $add_field = '';

            //PK
            $p_key_sql = "SELECT * FROM ALL_CONSTRAINTS AS t1 LEFT JOIN USER_IND_COLUMNS AS t2 ON t1.index_name = t2.index_name WHERE t1.CONSTRAINT_TYPE='P' AND t1.OWNER = '{$db_name}' AND t1.TABLE_NAME = '{$table}'";
            $p_key = db::get_one($p_key_sql);

            foreach ($table_fields_info as $field_info) {
                //if ($field_info['COLUMN_NAME'] == 'id') {
                if ($p_key && $field_info['COLUMN_NAME'] == $p_key['COLUMN_NAME']) {
                    $id_field = true;
                    $add_field .= "`{$p_key['COLUMN_NAME']}` int(11) NOT NULL AUTO_INCREMENT,\n";
                    continue;
                }

                //name
                $f_name = $field_info['COLUMN_NAME'];

                //leng
                $f_leng = $field_info['DATA_LENGTH'];

                //type
                $type = $field_info['DATA_TYPE'];

                if (strtoupper($type) == 'INT') {
                    $f_type = "INT(11)";
                }elseif (strtoupper($type) == 'VARCHAR') {
                    $f_type = "VARCHAR({$f_leng})";
                }elseif(strtoupper($type) == 'TEXT') {
                    $f_type = "TEXT";
                }else{
                    $f_type = $type;
                }

                //NOT NULL
                $f_notnull = '';
                if (strtoupper($field_info['NULLABLE']) == 'N') {
                    $f_notnull = "NOT NULL";
                }

                //default
                $f_default = $field_info['DATA_DEFAULT'] == '' ? '' : "DEFAULT {$field_info['DATA_DEFAULT']}";

                $add_field.= "`{$f_name}` {$f_type} {$f_notnull} {$f_default},\n";
            }

            $tabledump = "CREATE TABLE IF NOT EXISTS `{$newTable}` (\n";
            $tabledump .= $add_field;
            if ($id_field) {
                $tabledump .= "PRIMARY KEY (`{$p_key['COLUMN_NAME']}`)\n";
            }
            $tabledump .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 ";

            if ($id_field) {
                $tabledump .= "AUTO_INCREMENT=1";
            }
            $tabledump .= ";";
            $mysql->query($tabledump);
            if ($mysql->errno) {
                error($mysql->errno);
            }

            $this->tranferDmsqlRowToMysql($mysql, $table, $newTable);

        }
        $mysql->close();
    }


    /******TansferSql*******/
    /**
     * @param $sqlite
     * @param string $table
     */
    public function transferMysqlTableToSqllite($sqlite, $table = '')
    {
        global $_M;

        $sql = "DROP TABLE IF EXISTS $table;\n";
        $res = DB::query('SHOW CREATE TABLE '.$table);

        $create = DB::fetch_row($res);
        if (!$create) {
            return;
        }

        $sql .= str_replace(strtolower($table), $table, $create[1]).";\n\n";
        $sql = $this->mysqlToSqlite($sql);

        $sqlite->exec('begin;');
        $result = $sqlite->exec($sql);
        if (!$result) {
            $error = $sql.$sqlite->lastErrorMsg();
            $sqlite->exec('rollback;');
            error($error);
        }
        $sqlite->exec('commit;');
        $this->tranferMysqlRowsToSqlite($sqlite, $table);
    }

    /**
     * @param $sqlite
     * @param $table
     * @param int $start
     */
    public function tranferMysqlRowsToSqlite($sqlite, $table, $start = 0)
    {
        global $_M;
        $sql = '';
        $offset = 1000;
        $sqlite->exec('begin;');
        $rows = DB::query("SELECT * FROM {$table} LIMIT {$start},{$offset}");
        $numfields = DB::num_fields($rows);
        $numrows = DB::num_rows($rows);
        if ($numrows <= 0) {
            return;
        }
        while ($row = DB::fetch_row($rows)) {
            $values = '';
            $sql .= "INSERT INTO $table VALUES(";
            for ($i = 0; $i < $numfields; ++$i) {
                $sql .= $values."'".str_replace("'", "''", $row[$i])."'";
                $values = ',';
            }
            $sql .= ");\n";
        }

        $result = $sqlite->exec($sql);
        if (!$result) {
            $error = $sqlite->lastErrorMsg();
            $sqlite->exec('rollback;');
            error($error);
        }
        $sqlite->exec('commit;');
        $start += $offset;
        $this->tranferMysqlRowsToSqlite($sqlite, $table, $start);
    }

    /**
     * @param $mysql
     * @param $table
     * @param $newTable
     * @param int $start
     */
    public function tranferSqliteRowsToMysql($mysql, $table, $newTable, $start = 0)
    {
        global $_M;
        $offset = 1000;
        $rows = DB::query("SELECT * FROM {$table} LIMIT {$start},{$offset}");

        $numfields = DB::num_fields($rows);

        while ($row = DB::fetch_row($rows)) {
            $sql = '';
            $values = '';
            $sql .= "INSERT INTO $newTable VALUES(";
            for ($i = 0; $i < $numfields; ++$i) {
                // $sql .= $values."'".str_replace("'", "''", $row[$i])."'";
                $sql .= $values."'".mysqli_real_escape_string($mysql, $row[$i])."'";
                $values = ',';
            }
            $sql .= ");\n";
            $result = $mysql->query($sql);
            if ($result !== true) {
                error($mysql->error.$sql);
            }
        }

        $sql = trim($sql);
        if (!$sql) {
            return;
        }

        $start += $offset;
        $this->tranferSqliteRowsToMysql($mysql, $table, $newTable, $start);
    }

    /**
     * @param $link dm连接句柄
     * @param $table 数据表名
     * @param $newTable 新表名
     * @param $db_name  数据库名称
     * @param int $start 偏移值
     */
    public function tranferMysqlRowToDmsql($link, $table, $newTable, $start = 0)
    {
        global $_M;
        $offset = 1000;
        $rows = DB::query("SELECT * FROM {$table} LIMIT {$start},{$offset}");
        $numfields = DB::num_fields($rows);

        /****DMSQL****/
        //开启事务
        dm_autocommit($link);

        $query = "SET IDENTITY_INSERT {$newTable} ON;";
        dm_exec($link, $query);

        while ($row = DB::fetch_array($rows)) {
            $sql = '';
            $fields = array_keys($row);
            $values = array_values($row);
            $f_separate = $v_separate = '';

            $sql .= "INSERT INTO $newTable (";
            for ($i = 0; $i < $numfields; ++$i) {
                $sql .= "$f_separate \"{$fields[$i]}\"" ;
                $f_separate = ',';
            }
            $sql .= ") VALUES (";
            for ($i = 0; $i < $numfields; ++$i) {
                $v_data = dm_escape_string($values[$i]);
                $v_data = str_replace("\\'", "''", $v_data);
                $v_data = str_replace('\\', '', $v_data);
                $v_data = str_replace('0000-00-00 00:00:00', date("Y-m-d H:i:s"), $v_data);
                $sql .= "$v_separate '{$v_data}'" ;
                $v_separate = ',';
            }
            $sql .= ");\n";

            $res = dm_exec($link, $sql);
            if (!$res) {
                error("数据写入失败__" . dm_error() . ':' . dm_errormsg() . $sql);
            }
        }

        $query = "SET IDENTITY_INSERT {$newTable} OFF;";
        dm_exec($link, $query);

        //提交事务
        dm_commit($link);

        $sql = trim($sql);
        if (!$sql) {
            return;
        }

        $start += $offset;
        $this->tranferMysqlRowToDmsql($link, $table, $newTable, $start);
    }

    /**
     * @param $link 连接句柄
     * @param $table
     * @param $newTable
     * @param int $start
     */
    public function tranferDmsqlRowToMysql($link, $table, $newTable, $start = 0)
    {
        global $_M;
        $offset = 1000;
        $query = "SELECT * FROM {$table} LIMIT {$start},{$offset}";
        $data_res = DB::query($query);
        $numfields = dm_num_fields($data_res);


        while ($row = dm_fetch_array($data_res)){
            $sql = '';
            $fields = array_keys($row);
            $values = array_values($row);
            $f_separate = $v_separate = '';

            $sql .= "INSERT INTO $newTable (";
            for ($i = 0; $i < $numfields; ++$i) {
                $sql .= "$f_separate `{$fields[$i]}`" ;
                $f_separate = ',';
            }
            $sql .= ") VALUES (";
            for ($i = 0; $i < $numfields; ++$i) {
                $v_data = dm_escape_string($values[$i]);
                $v_data = str_replace("\\'", "''", $v_data);
                $v_data = str_replace('\\', '', $v_data);
                $v_data = str_replace('0000-00-00 00:00:00', date("Y-m-d H:i:s"), $v_data);
                $sql .= "$v_separate '{$v_data}'" ;
                $v_separate = ',';
            }
            $sql .= ");\n";

            //file_put_contents(__DIR__ . '/sql.log', "{$sql}\n", FILE_APPEND);
            $add_res = $link->query($sql);
            if ($link->error) {
                dd("$sql \n $link->error");
            }
        }
        DB::free_result($data_res);

        $sql = trim($sql);
        if (!$sql) {
            return;
        }

        $start += $offset;
        $this->tranferDmsqlRowToMysql($link, $table, $newTable, $start);
    }
    /******\TansferSql*******/


    public function get_all($mysql, $query)
    {
        $result = $mysql->query($query);
        $rs = array();
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $rs[] = $row;
            }
        } else {
            die($mysql->error());
        }

        return $rs;
    }

    public function mysqlToSqlite($sql)
    {
        $expr = array(
            '/`(\w+)`\s/' => '[$1] ',
            '/\s+UNSIGNED/i' => '',
            '/\s+[A-Z]*INT(\([0-9]+\))/i' => ' integer$1',
            '/\s+INTEGER\(\d+\)(.+AUTO_INCREMENT)/i' => ' integer$1',
            '/\s+AUTO_INCREMENT(?!=)/i' => ' PRIMARY KEY AUTOINCREMENT',
            '/\s+CHARACTER\s+SET\s+utf8\s+COLLATE\s+utf8_bin/i' => ' ',
            '/\s+ROW_FORMAT\s*=\s*DYNAMIC/i' => ' ',
            '/\s+ENUM\([^)]+\)/i' => ' text(255)',
            '/\s+varchar\((\d+)\)/i' => ' text($1)',
            '/\s+double/i' => ' REAL',
            '/\s+ON\s+UPDATE\s+[^,]*/i' => ' ',
            '/\s+COMMENT\s+(["\']).+\1/iU' => ' ',
            '/[\r\n]+\s+PRIMARY\s+KEY\s+[^\r\n]+/i' => '',
            '/[\r\n]+\s+UNIQUE\s+KEY\s+[^\r\n]+/i' => '',
            '/[\r\n]+\s+KEY\s+[^\r\n]+/i' => '',
            '/,([\s\r\n])+\)/i' => '$1)',
            '/\s+ENGINE\s*=\s*\w+/i' => ' ',
            '/DEFAULT\s+CHARSET\s*=\s*\w+/i' => ' ',
            '/\s+AUTO_INCREMENT\s*=\s*\d+/i' => ' ',
            '/\s+DEFAULT\s+;/i' => ';',
            '/\)([\s\r\n])+;/i' => ');',
            '/,?PRIMARY\sKEY\s\(`id`\)/i' => '',
            '/,\s+\)/i' => ')',
            '/\s+zerofill/i' => '',
        );

        foreach ($expr as $key => $value) {
            $sql = preg_replace_callback($key, function ($match) use ($value) {
                return str_replace('$1', $match[1], $value);
            }, $sql);
        }

        return $sql === null ? '' : $sql;
    }

    public function mysqlToDmsql($sql)
    {
        $expr = array(
            '/`(\w+)`\s/' => '"$1" ',
            '/`(\w+)`\(/' => '"$1" (',
            '/\s+UNSIGNED/i' => '',
            '/\s+[A-Z]*INT(\([0-9]+\))/i' => ' int',
            '/\s+INTEGER\(\d+\)(.+AUTO_INCREMENT)/i' => ' integer$1',
            '/\s+AUTO_INCREMENT(?!=)/i' => ' identity(1, 1)',
            '/\s+CHARACTER\s+SET\s+utf8\s+COLLATE\s+utf8_bin/i' => ' ',
            '/\s+ROW_FORMAT\s*=\s*DYNAMIC/i' => ' ',
            '/\s+ENUM\([^)]+\)/i' => ' text(255)',
            '/\s+tinytext\s+/i' => ' text ',
            '/\s+longtext\s*/i' => ' text ',
            '/\s+mediumtext\s*/i' => ' text ',
            '/\s+double\s*\([\w,]+\)/i' => ' double(20)',
            '/\s+ON\s+UPDATE\s+[^,]*/i' => ' ',
            '/\s+COMMENT\s+(["\']).+\1/iU' => ' ',
            //'/[\r\n]+\s+PRIMARY\s+KEY\s+[^\r\n]+/i' => '',
            '/[\r\n]+\s+UNIQUE\s+KEY\s+[^\r\n]+/i' => '',
            '/[\r\n]+\s+KEY\s+[^\r\n]+/i' => '',
            '/,([\s\r\n])+\)/i' => '$1)',
            '/\s+ENGINE\s*=\s*\w+/i' => ' ',
            '/DEFAULT\s+CHARSET\s*=\s*\w+/i' => ' ',
            '/\s+AUTO_INCREMENT\s*=\s*\d+/i' => ' ',
            '/\s+DEFAULT\s+;/i' => ';',
            '/\)([\s\r\n])+;/i' => ');',
            '/PRIMARY\sKEY\s+\(`(\w+)`\)/i' => ' PRIMARY KEY ("$1")',
            '/,\s+\)/i' => ')',
            '/\s+zerofill/i' => '',
            '/\sUSING\sBTREE/i' => '',
            '/CREATE\s+TABLE\s+IF\sNOT\s+EXISTS/i' => 'CREATE TABLE ',
        );

        foreach ($expr as $key => $value) {
            $sql = preg_replace_callback($key, function ($match) use ($value) {
                return str_replace('$1', $match[1], $value);
            }, $sql);
        }
        return $sql === null ? '' : $sql;
    }


    /******数据导入*******/
    /**
     * @param $string
     */
    public function importSql($string)
    {
        global $_M;
        preg_match("/^#[^\r\n]+\//im", $string, $match);
        $site_url = trim($match[0], '#');
        $tablepre = $_M['config']['tablepre'];
        $string = str_replace($site_url, $_M['url']['site'], $string);
        $old = array('DROP TABLE IF EXISTS met_', 'CREATE TABLE `met_', 'INSERT INTO met_');
        $new = array("DROP TABLE IF EXISTS {$tablepre}", "CREATE TABLE `{$tablepre}", "INSERT INTO {$tablepre}");
        $string = str_replace($old, $new, $string);
        switch (strtolower($_M['config']['db_type'])) {
            case 'mysql':
                $this->importMysql($string);
                break;
            case 'sqlite':
                $this->importSqlite($string);
                break;
            case 'dmsql':
                $this->importDmsql($string);
                break;
        }
    }

    public function importSqlite($string)
    {
        global $_M;
        $tablepre = $_M['config']['tablepre'];
        DB::$link->exec('begin;');
        $sqls = $this->getQuery($string);
        foreach ($sqls as $query) {
            if (trim($query)) {
                if (stristr($query, 'CREATE TABLE')) {
                    $query = $this->mysqlToSqlite($query);
                }
                if (strstr($query, $tablepre.'admin_table')) {
                    continue;
                }

                if (strstr($query, $tablepre.'templates')) {
                    continue;
                }

                if (strstr($query, $tablepre.'admin_column')) {
                    continue;
                }

                if (strstr($query, $tablepre.'language')) {
                    continue;
                }
                $query = trim($query, ';');
                if (!$query) {
                    continue;
                }
                $query .= ';';
                $query = DB::escapeSqlite($query);

                $rs = DB::$link->exec($query);
                if (!$rs) {
                    file_put_contents(PATH_WEB.getAdminDir().'/sqlite_error.txt', $query.DB::$link->lastErrorMsg()."\n", FILE_APPEND);
                }
            }
        }
        DB::$link->exec('commit;');
    }

    public function importMysql($string)
    {
        global $_M;
        $tablepre = $_M['config']['tablepre'];
        $sqls = $this->getQuery($string);
        foreach ($sqls as $query) {
            if (trim($query)) {
                if (strstr($query, $tablepre.'admin_table')) {
                    continue;
                }

                if (strstr($query, $tablepre.'templates')) {
                    continue;
                }

                if (strstr($query, $tablepre.'admin_column')) {
                    continue;
                }

                if (strstr($query, $tablepre.'language')) {
                    continue;
                }
                $query = trim($query, ';');
                if (!$query) {
                    continue;
                }
                $query .= ';';
                DB::query($query);
            }
        }
    }

    public function importDmsql($string)
    {
        global $_M;
        $tablepre = $_M['config']['tablepre'];
        $sqls = $this->getQuery($string);

        //事务
        dm_autocommit(DB::$link);

        foreach ($sqls as $query) {
            if (trim($query)) {
                //CREATE TABLE
                if (stristr($query, 'CREATE TABLE')) {
                    $query = $this->mysqlToDmsql($query);
                }

                if (strstr($query, $tablepre.'admin_table')) {
                    continue;
                }

                if (strstr($query, $tablepre.'templates')) {
                    continue;
                }

                if (strstr($query, $tablepre.'admin_column')) {
                    continue;
                }

                if (strstr($query, $tablepre.'language')) {
                    continue;
                }

                $query = trim($query, ';');
                if (!$query) {
                    continue;
                }
                $query .= ';';

                //INSERT
                if (strtoupper(substr($query, 0, 6)) == 'INSERT') {
                    preg_match('/insert\s+into\s+(([`a-z0-9A-Z_]+)\s?values)(.+)/i', $query, $match);
                    $db_name = $_M['config']['con_db_name'];
                    $table = $match[2];

                    //表字段
                    $sql = "select COLUMN_NAME,DATA_TYPE from all_tab_columns where table_name='{$table}' AND  owner='{$db_name}';";
                    $table_info = DB::get_all($sql);
                    $fields = array_column($table_info, 'COLUMN_NAME');
                    if (!is_array($fields)) {
                        continue;
                    }
                    $field_str = '(';
                    foreach ($fields as $field) {
                        $field_str .= "\"{$field}\" ,";
                    }
                    $field_str = trim($field_str, ',');
                    $field_str = trim($field_str);
                    $field_str .= ')';

                    //**
                    $sql = "SET IDENTITY_INSERT {$table} ON;";
                    dm_exec(DB::$link, $sql);

                    $query = str_replace($match[1], $match[2] . " {$field_str} VALUES ", $query);
                    $query = DB::escapeDmsql($query);
                    $rs = dm_exec(DB::$link, $query);
                    if (!$rs) {
                        file_put_contents(PATH_CACHE.'dmsql_error.log', $query.DB::errno()."\n\n", FILE_APPEND);
                    }

                    //**
                    $sql = "SET IDENTITY_INSERT {$table} OFF;";
                    dm_exec(DB::$link, $sql);
                    continue;
                }


                $query = DB::escapeDmsql($query);
                $rs = dm_exec(DB::$link, $query);
                if (!$rs) {
                    file_put_contents(PATH_CACHE.'dmsql_error.log', $query.DB::errno()."\n\n", FILE_APPEND);
                }
            }
        }
        //事务
        dm_commit(DB::$link);
    }

    public function getQuery($string)
    {
        global $_M;
        $sqls = array();
        preg_match_all('/DROP\s+TABLE\s+IF\s+EXISTS\s+\w+;/i', $string, $matchA);
        preg_match_all('/CREATE\s+TABLE[\s\S]+?;/i', $string, $matchB);
        //$sqls = array_merge($matchA[0], $matchB[0]);
        if (is_array($matchA)) {
            $sqls = array_merge($sqls, $matchA[0]);
        }
        if (is_array($matchB)) {
            $sqls = array_merge($sqls, $matchB[0]);
        }

        $sqlArray = explode("');\n", $string);

        foreach ($sqlArray as $sql) {
            //dumpfile / outfile 过滤
            $matchC_res = preg_match('/into\s+(dumpfile|outfile)\s+/i', $sql, $matchC);
            if ($matchC_res) {
                continue;
            }

            $sql = $sql."');";
            if (strstr($sql, 'CREATE') || strstr($sql, 'DROP')) {
                foreach (explode(";\n", $sql) as $query) {
                    if (trim($query)) {
                        $query = $query.';';
                        $query = str_replace(';;', ';', $query);
                        if (strstr($query, 'CREATE') || strstr($query, 'DROP')) {
                            continue;
                        }
                        $new_sql = $query;
                    }
                }
            } else {
                if (trim($sql)) {
                    $query = str_replace(';;', ';', $sql);
                    $new_sql = $query;
                }
            }

            $matched = preg_match('/\w+/', $new_sql, $match);
            if (!$matched) {
                continue;
            }

            $sqls[] = str_replace("\n", '', $new_sql);
        }

        return $sqls;
    }
    /******\数据导入*******/

}

// This program is an open source system, commercial use, please consciously to purchase commercial license.;
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
