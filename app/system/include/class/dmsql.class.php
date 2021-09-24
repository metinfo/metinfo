<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 数据库操作类.
 */
class DB
{
    public static $querynum = 0;
    public static $link;

    /**
     * 数据库连接函数.
     *
     * @param string $con_db_host 主机地址
     * @param string $con_db_id   用户名
     * @param string $con_db_pass 密码
     * @param string $con_db_name 数据库名
     * @param string $pconnect    是否打开永久链接
     */
    public static function dbconn($con_db_host, $con_db_id, $con_db_pass, $con_db_name = '', $con_db_port = '5236', $pconnect = '')
    {
        $link = dm_connect("{$con_db_host}:{$con_db_port}", $con_db_id, $con_db_pass);
        if(!$link){
            self::halt(dm_error() . ':' . dm_errormsg());
        }

        dm_setoption($link, 1, 12345, 1);
        self::$link = $link;

        $sql = "SET SCHEMA {$con_db_name}";
        $res = dm_exec($link,$sql);
        if(!$res){
            echo dm_error().':'.dm_errormsg();exit();
        }

        return;
    }

    /**
     * 选择数据库
     * @param $con_db_name 选择的数据库名
     */
    public static function select_db($con_db_name = '')
    {
        $sql = "SET SCHEMA {$con_db_name}";
        $res = dm_exec(self::$link,$sql);
        if(!$res){
            echo dm_error().':'.dm_errormsg();exit();
        }
    }

    /**
     * @param $result
     * @return array 出巡结果数组
     */
    public static function fetch_array($result)
    {
        return dm_fetch_array($result, 1);
        //return dm_fetch_into($result);
    }

    /**
     * * 获取一条数据.
     * @return array 返回执行sql语句后查询到的数据
     * @param $sql
     * @return array
     */
    public static function get_one($sql)
    {
        $result = self::query($sql);
        $rs = self::fetch_array($result);
        //如果是前台可视化编辑模式
        if (!defined('IN_ADMIN')  && $_GET['pageset'] == 1) {
            $rs = load::sys_class('view/met_datatags', 'new')->replace_sql_one($sql, $rs);
        }
        self::free_result($result);

        return $rs;
    }

    /**
     * @param $sql
     * @param string $type
     * @return array
     */
    public static function get_all($sql)
    {
        $rs = array();
        $result = self::query($sql);
        while ($line = dm_fetch_array($result)){
            $rs[] = $line;

        }
        //如果是前台可视化编辑模式
        if ( !defined('IN_ADMIN') && $_GET['pageset'] == 1) {
            $rs = load::sys_class('view/met_datatags', 'new')->replace_sql_all($sql, $rs);
        }
        self::free_result($result);

        return $rs;
    }

    /**
     * @param $sql
     * @return int
     */
    public static function query($sql)
    {
        global $_M;
        $sql = self::escapeDmsql($sql);

        //INSERT
        if (strtoupper(substr($sql, 0, 6)) == 'INSERT') {
            $sql = str_replace(array("\n", "\r"), '', $sql);
            preg_match('/insert\s+into\s+([`a-z0-9A-Z_]+)\s+set\s+(.*)/i', $sql, $match);

            if (isset($match[1]) && isset($match[2]) && $match[2]) {
                $list = array();
                $table = trim($match[1], '`');
                foreach (explode("',", $match[2]) as $val) {
                    if (!trim($val)) {
                        continue;
                    }

                    $param = explode('=', $val);
                    if (trim($param[0])) {
                        $list[trim($param[0])] = str_replace("'", '', trim($param[1]));
                    }
                }
                $row = self::insert($table, $list);
                return $row;
            }

            $pattern = '/insert\s+into\s+([`a-z0-9A-Z_]+)\s+(.*)\s+values\s+(.*)/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                $fields = str_replace('`', "\"", $match['2']);
                return str_replace($match['2'], $fields, $sql);
            }, $sql);

            //VALUES (null,
            $pattern = '/insert\s+into\s+([`a-z0-9A-Z_]+)\s+values\s+(\(\s*null\s*,)/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                $table_name = str_replace('`', '', $match[1]);
                $out = str_replace($match[1], $table_name, $match[0]);
                return str_replace($match[2], '(', $out);
            }, $sql);

            dm_autocommit(self::$link);
            $result = dm_exec(self::$link, $sql);
            if (!$result) {
                dm_rollback(self::$link);
                error(self::errorlist($sql));
            }
            dm_commit(self::$link);
            if (!self::insert_id()) {
                error(self::errorlist($sql));
            }

            return self::insert_id();
        }

        //UPDATE
        if (strtoupper(substr($sql, 0, 6)) == 'UPDATE') {
            $pattern = '/`(\w+)`/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                return "\"{$match[1]}\"";
            }, $sql);

            dm_autocommit(self::$link);
            $result = dm_exec(self::$link, $sql);
            if (!$result) {
                dm_rollback(self::$link);
                error(self::errorlist($sql));
            }
            return dm_commit(self::$link);
        }

        //SELECT
        if (strtoupper(substr($sql, 0, 6)) == 'SELECT') {
            $pattern = '/`(\w+)`/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                return "\"{$match[1]}\"";
            }, $sql);

            //
            $pattern = '/\s((\w+)\s?=)/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                return " \"$match[2]\" =";
            }, $sql);

            $pattern = '/=\s*(\d+)\s?/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                return " = '$match[1]' ";
            }, $sql);

            if(!$result = dm_exec(self::$link, $sql)){
                dd($sql . self::errno());
                //self::errorlist($sql);
            }
            return $result;
        }

        //DELETE
        if (strtoupper(substr($sql, 0, 6)) == 'DELETE') {
            $pattern = '/`(\w+)`/i';
            $sql = preg_replace_callback($pattern, function ($match) use ($sql) {
                return "\"{$match[1]}\"";
            }, $sql);
            if(!$result = dm_exec(self::$link, $sql)){
                self::errno();
            }
            return $result;
        }

        //CREATE
        if (strtoupper(substr($sql, 0, 12)) == 'CREATE TABLE') {
            $sql = load::mod_class('databack/transfer', 'new')->mysqlToDmsql($sql);
        }

        //DROP TABLE
        if (strtoupper(substr($sql, 0, 10)) == 'DROP TABLE') {
            return dm_exec(self::$link, $sql);
        }

        if(!$result = dm_exec(self::$link, $sql)){
            self::errno();
            self::errorlist($sql);
        }
        return $result;
    }

    /**
     * @param string $table
     * @param array $bind
     * @return int
     */
    public static function insert($table = '', $bind = array())
    {
        $set = array();
        foreach ($bind as $col => $val) {
            $col = trim($col, '`');

            if ($col == 'id' && (!$val || $val == 'NULL')) {
                continue;
            }
            $val = stripslashes($val);
            $val = addslashes($val);
            $val = self::escapeDmsql($val);

            $set[] = "\"$col\"";
            $vals[] = "'{$val}'";
        }
        $sql = 'INSERT INTO '
            . $table
            . ' (' . implode(', ', $set) . ') '
            . 'VALUES (' . implode(', ', $vals) . ')';

        dm_autocommit(self::$link);
        $result = dm_exec(self::$link, $sql);
        if (!$result) {
            dm_rollback(self::$link);
//            self::errno();
            dumP($bind);
            dd($sql);
            self::errorlist($sql);
        }
        dm_commit(self::$link);

        return self::insert_id();
    }

    public static function update($table = '', $bind = array(), $condition = array())
    {
        die();
        $sql1 = '';
        foreach ($bind as $key => $val) {
            if ($key != 'id') {
                $sql1 .= " $key = '{$val}',";
            }
        }
        $sql1 = trim($sql1, ',');

        $sql2 = '';
        foreach ($condition as $key => $val) {
            if ($key != 'id') {
                $sql2 .= " $key = '{$val}',";
            }
        }
        $sql2 = trim($sql2, ',');

        $sql = "UPDATE {$table} SET $sql1 WHERE $sql2";

        $res = self::query($sql);

        return $res;
    }

    /**
     * 获取指定条数数据.
     *
     * @param string $table       表名称
     * @param string $where       where条件
     * @param string $order       order条件
     * @param string $limit_start 开始条数
     * @param string $limit_num   取条数数量
     * @param string $field_name  获取的字段
     *
     * @return array 查询得到的数据
     */
    public static function get_data($table, $where, $order, $limit_start = 0, $limit_num = 20, $field_name = '*')
    {
        if ($limit_start < 0) {
            return false;
        }
        $limit_start = $limit_start ? $limit_start : 0;
        $where = str_ireplace('WHERE', '', $where);
        $order = str_ireplace('ORDER BY', '', $order);
        $conds = '';
        if ($where) {
            $conds .= " WHERE {$where} ";
        }
        if ($order) {
            $conds .= " ORDER BY {$order} ";
        }

        $conds .= " LIMIT {$limit_start},{$limit_num}";
        $query = "SELECT {$field_name} FROM {$table} {$conds}";
        $data = DB::get_all($query);
        if ($data) {
            return $data;
        } else {
            if ($limit_start == 0) {
                return $data;
            } else {
                return false;
            }
        }
    }

    /**
     * 统计条数.
     *
     * @param string $table_name insert、update等 sql语句
     * @param string $where_str  where条件,建议添加上WEHER
     * @param string $field_name 统计的字段
     *
     * @return int 统计条数
     */
    public static function counter($table_name, $where_str = '', $field_name = '*')
    {
        $where_str = trim($where_str);
        if (strtolower(substr($where_str, 0, 5)) != 'where' && $where_str) {
            $where_str = 'WHERE '.$where_str;
        }
        $query = " SELECT COUNT($field_name) as total FROM $table_name $where_str ";
        $result = self::query($query);
        $res = dm_fetch_array($result);
        if (!$res) {
            self::error();
        }
        return $res['total'];
    }

    /**
     * 返回前一次 SQL 操作所影响的记录行数。
     * @param string $dbname 选择的数据库名
     * @return int 执行成功，则返回受影响的行的数目，如果最近一次查询失败的话，函数返回 -1
     */
    public static function affected_rows()
    {
        return dm_affected_rows(self::$link);
    }

    /**
     * 返回上一个 SQL 操作产生的文本错误信息.
     *
     * @return string 错误信息
     */
    public static function error()
    {
        return dm_errormsg();
    }

    /**
     * 返回上一个 SQL 操作中的错误信息的数字编码
     *
     * @return string 错误信息的数字编码
     */
    public static function errno()
    {
        return dm_error().':'.dm_errormsg();
    }

    /**
     * 返回上一个 SQL 操作中的错误信息的数字编码
     *
     * @return array 错误信息列表
     */
    public static function errorlist($sql = '')
    {
        error($sql  . self::errno());
        return  dm_error().':'.dm_errormsg();
    }

    /**
     * 返回结果集中一个字段的值
     * @param $query
     * @param $row
     */
    public static function result($query, $row)
    {
        die('method disable');
    }

    /**
     * 返回查询的结果中行的数目.
     * @return int 行数
     */
    public static function num_rows($result)
    {
        return dm_num_rows($result);
    }

    /**
     * 返回查询的结果中字段的信息.
     * @return mixed 字段数组
     */
    public static function fields($result)
    {
        return dm_list_fields($result);
    }

    /**
     * 返回查询的结果中字段的数目.
     * @return int 字段数
     */
    public static function num_fields($result)
    {
        return dm_num_fields($result);
    }

    /**
     * 释放结果内存.
     */
    public static function free_result($result)
    {
        return dm_free_result($result);
    }

    /**
     * 返回上一步 INSERT 操作产生的 ID.
     *
     * @return int id号
     */
    public static function insert_id()
    {
        return dm_insert_id(self::$link);
        //return self::$link->insert_id;
    }

    /**
     * 从结果集中取得一行作为数字数组.
     * @return array 结果集一行数组
     */
    public static function fetch_row($result)
    {
        return dm_fetch_row(self::$link);
    }

    /**
     * 转义字符串中的特殊字符
     * @param $result
     * @param $sql
     * @return string
     */
    public static function escapeString( $sql)
    {
        return dm_escape_string( $sql);
    }

    public function escapeDmsql($sql)
    {
        $sql = str_replace("\\'", "''", $sql);
        $sql = str_replace('\\', '', $sql);

        return $sql;
    }

    /**
     * 返回数据库服务器信息.
     */
    public static function version()
    {
        return 'dmsql';
    }

    /**
     * 关闭连接.
     */
    public static function close()
    {
        return @dm_close(self::$link);
    }

    /**
     * 无法连接数据库报错.
     */
    public static function halt($dbhost)
    {
        $sqlerror = dm_error();
        $sqlerrno = dm_errormsg();
        $sqlerror = str_replace($dbhost, 'dbhost', $sqlerror);

        header('HTTP/1.1 500 Internal Server Error');
        die("$sqlerror  ( $sqlerrno )");
        exit;
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
