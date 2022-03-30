<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/admin/base_admin');

class relation_admin extends base_admin
{
    public $database;

    /**
     * news_admin constructor.
     */
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->database = load::mod_class('relation/relation_database', 'new');
        $this->column_label = load::mod_class('column/include/column_label', 'new');
    }

    public function doGetRelations()
    {
        global $_M;
        $cid = $_M['form']['content_id'];
        $module = $_M['form']['module'];

        $datalist = $this->jsonRelationList($cid, $module);
        //$this->json_return($datalist);
        $redata = array();
        $redata['status'] = 1;
        $redata['data'] = $datalist;
        $this->ajaxReturn($redata);

    }

    /**
     * @param string $cid
     * @param string $class
     * @param string $order
     * @return array
     */
    public function jsonRelationList($aid = '',$module = '',  $order = '')
    {
        global $_M;
        $handle = load::sys_class('handle', 'new');
        $mod = $handle->file_to_mod($module);
        $where = " lang='{$_M['lang']}' AND aid = '{$aid}' AND module = '{$mod}'";
        $data = $this->database->table_json_list($where, $order);

        $datalist = array();
        foreach ($data as $key => $val) {
            $mod_name = $handle->mod_to_name($val['relation_module']);
            if (!$mod_name) {
                continue;
            }
            $mod_lable = load::mod_class("{$mod_name}/{$mod_name}_label",'new');
            if (!method_exists($mod_lable, 'get_one_content')) {
                continue;
            }
            $content = $mod_lable->get_one_content($val['relation_id']);
            if (!$content) {
                continue;
            }

            $classnow = $content['class3'] ? $content['class3'] : ($content['class2'] ? $content['class2'] : $content['class1']);
            $class = $this->column_label->get_column_id($classnow);
            $val['relation_class'] = $classnow ?: 0;
            $val['relation_class_name'] = $class['name']?:'';
            $val['content'] = $content;

            $datalist[] = $val;
        }
        return $datalist;
    }


    /**
     *
     */
    public function doGetClasslist()
    {
        global $_M;
        $data = self::getClassTree();
        echo json_encode($data);
    }

    protected function getClassTree()
    {
        global $_M;
        /*查询表*/
        $where = "lang='{$_M['lang']}' AND module IN (2,3,4,5)";
        $field = "id,name,module";
        $sql = "SELECT {$field} FROM {$_M['table']['column']} WHERE {$where} AND classtype = 1";
        $class1 = DB::get_all($sql);
        $i = 0;
        $metinfo['citylist'][$i]['p']['name'] = "{$_M['word']['columnselect1']}" ?: '请选择';
        $metinfo['citylist'][$i]['p']['value'] = '';
        foreach ($class1 as $key1 => $val) {
            $i++;
            $metinfo['citylist'][$i]['p']['name'] = $val['name'];
            $metinfo['citylist'][$i]['p']['value'] = $val['id'];
            $metinfo['citylist'][$i]['p']['module'] = $val['module'];
            $sql = "SELECT {$field} FROM {$_M['table']['column']} WHERE {$where} AND classtype = 2 AND bigclass = '{$val['id']}'";
            $class2 = DB::get_all($sql);
            if ($class2) {
                $k = 0;
                $metinfo['citylist'][$i]['c'][$k]['n']['name'] = "{$_M['word']['modClass2']}";
                $metinfo['citylist'][$i]['c'][$k]['n']['value'] = '';
            }
            foreach ($class2 as $key2 => $val2) {
                $k++;
                $metinfo['citylist'][$i]['c'][$k]['n']['name'] = $val2['name'];
                $metinfo['citylist'][$i]['c'][$k]['n']['value'] = $val2['id'];
                $metinfo['citylist'][$i]['c'][$k]['n']['module'] = $val2['module'];
                $sql = "SELECT {$field} FROM {$_M['table']['column']} WHERE {$where} AND classtype = 3 AND bigclass = '{$val2['id']}'";
                $class3 = DB::get_all($sql);
                if ($class3) {
                    $j = 0;
                    $metinfo['citylist'][$i]['c'][$k]['a'][0]['s']['name'] = "{$_M['word']['modClass3']}";
                    $metinfo['citylist'][$i]['c'][$k]['a'][0]['s']['value'] = '';
                    foreach ($class3 as $key => $val3) {
                        $j++;
                        $metinfo['citylist'][$i]['c'][$k]['a'][$j]['s']['name'] = $val3['name'];
                        $metinfo['citylist'][$i]['c'][$k]['a'][$j]['s']['value'] = $val3['id'];
                        $metinfo['citylist'][$i]['c'][$k]['a'][$j]['s']['module'] = $val3['module'];
                    }
                }
            }
        }

        return $metinfo;
    }


    /**
     * 内容列表
     */
    public function doGetDatelist()
    {
        global $_M;
        $form = $_M['form'];
        $classid = $form['classid'] ?: 0;
        $keyword = $form['keyword'];
        $start = $form['start'] ? $form['start'] : 0;
        $length = $form['length'] ? $form['length'] : 20;

        if (!$classid || !is_numeric($classid)) {
            $redata = array();
            $redata['data'] = array();
            $redata['draw'] = $_M['form']['draw'];
            $redata['recordsFiltered'] = 0;
            $redata['recordsTotal'] = 0;
            $this->ajaxReturn($redata);
        }

        $column_database = load::mod_class('column/class/column_database', 'new');
        $class = $column_database->get_list_one_by_id($classid);

        if (!$class) {
            $this->error('栏目不存在');
        }

        $module = load::sys_class('handle', 'new')->mod_to_name($class['module']);
        $mod_handle = load::mod_class("{$module}/{$module}_handle", 'new');

        $c123 = load::mod_class('column/column_label', 'new')->get_class123_no_reclass($classid);
        $class1 = $c123['class1']['id'] ? $c123['class1']['id'] : 0;
        $class2 = $c123['class2']['id'] ? $c123['class2']['id'] : 0;
        $class3 = $c123['class3']['id'] ? $c123['class3']['id'] : 0;

        //查询构造
        $_where = " lang = '{$_M['lang']}' AND (recycle = '0' or recycle = '-1') AND ";
        if ($keyword != '') {
            //$_where .= " (title LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%' OR keywords LIKE '%{$keyword}%') AND ";
            $_where .= " (title LIKE '%{$keyword}%') AND ";
        }
        //where
        switch ($class['classtype']) {
            case 1:
                //$_where .= " class1 = '{$class['id']}'";
                $_where .= " class1 = '{$class1}'";
                break;
            case 2:
                //$_where .= " class2 = '{$class['id']}'";
                $_where .= " class1 = '{$class1}' AND class2 = '{$class2}'";
                break;
            case 3:
                //$_where .= " class3 = '{$class['id']}'";
                $_where .= " class1 = '{$class1}' AND class2 = '{$class2}' AND class3 = '{$class3}'";
                break;
        }

        //order
        $_order = " updatetime DESC ";

        $table = $_M['table'][$module];

        $this->tabledata = load::sys_class('tabledata', 'new');
        //$fields = " id,title,ctitle,class1,class2,class3,no_order,filename,lang,updatetime,addtime,other_info";
        $fields = '*';
        $data = $this->tabledata->getdata($table, $fields, $_where, $_order);
        $data = $mod_handle->para_handle($data);
        foreach ($data as $key => $val) {
            $sql = "SELECT * FROM {$_M['table']['relation']} WHERE aid ='{$val['id']}' AND module = '{$class['module']}'";
            $res = db::get_one($sql);
            $checked = $res ? 1 : 0;
            $data[$key]['checked'] = $checked;
        }
        $this->tabledata->rdata($data);
    }

    public function doAddRelation()
    {
        global $_M;
        $aid = $_M['form']['aid'];
        $class1 = $_M['form']['class1'];
        $class2 = $_M['form']['class2'];
        $class3 = $_M['form']['class3'];
        $classnow = $class3 ? $class3 : ($class2 ? $class2 : $class1);

        $column_database = load::mod_class('column/class/column_database', 'new');
        $class = $column_database->get_list_one_by_id($classnow);

    }


}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
