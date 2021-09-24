<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * Class tables
 * 数据库对比
 */
class dmsqltables
{
    public $version;

    /**
     * 对比数据库结构
     * @param $version
     */
    public function diffFields($version = '')
    {
        global $_M;
        if (strtolower($_M['config']['db_type']) != 'dmsql') {
            return false;
        }

        $this->version = $version;
        $diffs = self::getDiffTables();
        if (isset($diffs['table'])) {
            foreach ($diffs['table'] as $table => $fields) {
                $id_field = false;
                $add_field = '';
                foreach ($fields as $field_name => $field_info) {
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
                    $f_type = self::transferType($type);
                    //access type change
                    $table_lsit = array('download', 'img', 'news', 'product', 'job', 'message', 'parameter', 'column');
                    if (in_array($table, $table_lsit)) {
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


                    $add_field .= "\"{$f_name}\" {$f_type} {$f_notnull} {$f_default},\n";
                }

                $create_sql = "CREATE TABLE \"{$table}\" (\n";
                $create_sql .= $add_field;

                if ($id_field) {
                    $create_sql .= "CLUSTER PRIMARY KEY(\"{$pri}\")\n";
                }

                $create_sql .= ")STORAGE(ON \"MAIN\", CLUSTERBTR);";
                //file_put_contents(__DIR__ . '/sql.txt', $create_sql . "\n\n",FILE_APPEND);
                DB::query($create_sql);

                add_table(str_replace($_M['config']['tablepre'], '', $table));

            }
            $add_table[] = $table;
        }

        if (isset($diffs['field'])) {
            foreach ($diffs['field'] as $table => $fields) {
                if (in_array($table, $add_table)) {
                    continue;
                }
                foreach ($fields as $field => $info) {
                    $f_type = self::transferType($info['Type']);
                    $sql = "ALTER TABLE \"{$table}\" ADD \"{$field}\"  {$f_type} ";

                    if ($field_info['Key'] == 'PRI') {
                        $sql .= "\"{$field}\" INT IDENTITY(1, 1) NOT NULL,\n";
                    }

                    if ($info['Default'] === null) {
                        $sql .= " Default NULL ";
                    } else {
                        $sql .= " Default '{$info['Default']}' ";
                    }
                    //file_put_contents(__DIR__ . '/sql.txt', $sql . "\n\n", FILE_APPEND);
                    DB::query($sql);
                }
            }
        }
    }

    /**
     * 更新表字段默认值
     * @param $version
     */
    public function alterTable($version = '')
    {
        global $_M;
        if (strtolower($_M['config']['db_type']) != 'dmsql') {
            return false;
        }

        $this->version = $version;
        $base = self::getBaseTable();
        if (!$base) {
            return;
        }

        foreach ($base as $table_name => $table) {
            $table_name_now = str_replace('met_', $_M['config']['tablepre'], $table_name);
            foreach ($table as $key => $field) {
                $sql = "ALTER TABLE {$table_name_now} ";
                if ($key == 'id') {
                    continue;
                }

                $f_type = self::transferType($field['type']);
                $sql .= " MODIFY  \"{$field['Field']}\" {$f_type} ";
                if ($field['Default'] === null) {
                    $sql .= " DEFAULT NULL ";
                } else {
                    $sql .= " DEFAULT '{$field['Default']}' ";
                }
                $sql .= ";";
                DB::query($sql);
            }
        }
    }

    /**
     * 获取标准数据库文件
     * @return mixed
     */
    protected function getBaseTable()
    {
        $json_sql = "https://www.metinfo.cn/upload/json/v{$this->version}mysql.json";
        $table_json = file_get_contents($json_sql);
        if (!$table_json) {
            $table_json = self::app_curl($json_sql);
        }else{
            $sql_path = PATH_SYS . "update/include/class/v{$this->version}mysql.json";
            $table_json = file_get_contents($sql_path);
        }

        $base = json_decode($table_json, true);
        return $base;
    }

    protected function getDiffTables()
    {
        global $_M;
        $tables = self::listTables();
        $base = self::getBaseTable();

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
            if (!in_array($table, $noTables)) {
                $table = str_replace('met_', $_M['config']['tablepre'], $table);
                $fields = self::listFields($table);
                $diff_field = array_diff_key($val, $fields);
                if ($diff_field) {
                    $data['field'][$table] = $diff_field;
                }
            }
        }
        return $data;
    }

    protected function listTables()
    {
        global $_M;
        $query = "SELECT table_name FROM all_tables WHERE owner='{$_M['config']['con_db_name']}'";

        $tables = array();
        foreach (DB::get_all($query) as $key => $v) {
            $tables[] = str_replace($_M['config']['tablepre'], 'met_', $v['table_name']);
        }
        return $tables;
    }

    protected function listFields($table)
    {
        global $_M;
        //$query = "SHOW FULL FIELDS FROM {$table}";
        $query = "SELECT * FROM all_tab_columns WHERE owner='{$_M['config']['con_db_name']}' AND Table_Name='{$table}'";

        $fields = DB::get_all($query);
        $data = array();
        foreach ($fields as $key => $v) {
            $data[$v['COLUMN_NAME']] = $v;
        }
        return $data;
    }

    /**
     * @param string $type
     * @param string $table
     * @return string
     */
    protected function transferType($type = '',$table = '')
    {
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

        return $f_type;
    }

    /**
     * @param string $url
     * @param array $data
     * @param int $timeout
     * @return mixed
     */
    protected function app_curl($url = '', $data = array(), $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.;
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
