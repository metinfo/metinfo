<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * Class tables
 * 数据库对比
 */
class mysqltables
{
    public $version;

    /**
     * 对比数据库结构
     * @param $version
     */
    public function diffFields($version = '')
    {
        global $_M;
        if (strtolower($_M['config']['db_type']) != 'mysql') {
            return false;
        }

        $this->version = $version;
        $diffs = self::getDiffTables();
        if (isset($diffs['table'])) {
            foreach ($diffs['table'] as $table => $detail) {
                $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (";
                foreach ($detail as $k => $v) {
                    if ($k == 'id') {
                        $sql .= "`{$k}` {$v['Type']} {$v['Extra']} ,";
                    } else {
                        $sql .= "`{$k}` {$v['Type']} ";

                        if ($v['Default'] === null) {
                            $sql .= " DEFAULT NULL ";
                        } else {
                            $sql .= " DEFAULT '{$v['Default']}' ";
                        }

                        $sql .= "  {$v['Extra']} ,";
                    }
                }
                $sql .= "PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                DB::query($sql);
                add_table(str_replace($_M['config']['tablepre'], '', $table));
            }
        }

        if (isset($diffs['field'])) {
            foreach ($diffs['field'] as $table => $v) {
                foreach ($v as $field => $f) {
                    $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}`  {$f['Type']} ";

                    if ($f['Default'] === null) {
                        $sql .= " Default NULL ";
                    } else {
                        $sql .= " Default '{$f['Default']}' ";
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
    public function alterTable($version = '')
    {
        global $_M;
        if (strtolower($_M['config']['db_type']) != 'mysql') {
            return false;
        }

        $this->version = $version;
        $base = self::getBaseTable();
        if (!$base) {
            return;
        }

        foreach ($base as $table_name => $table) {
            $table_name_now = str_replace('met_', $_M['config']['tablepre'], $table_name);
            $sql = "ALTER TABLE `{$table_name_now}` ";
            foreach ($table as $key => $field) {
                if ($key == 'id') {
                    continue;
                }

                $sql .= " MODIFY COLUMN `{$field['Field']}` {$field['Type']} ";
                if ($field['Default'] === null) {
                    $sql .= " DEFAULT NULL ";
                } else {
                    $sql .= " DEFAULT '{$field['Default']}' ";
                }
                $sql .= ',';
            }
            $sql = trim($sql, ',') . ';';
            DB::query($sql);
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
        //$query = "SHOW TABLE status";
        $query = "SHOW TABLE status WHERE Name LIKE '{$_M['config']['tablepre']}%'";
        $tables = array();
        foreach (DB::get_all($query) as $key => $v) {
            $tables[] = str_replace($_M['config']['tablepre'], 'met_', $v['Name']);
        }
        return $tables;
    }

    protected function listFields($table)
    {
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
