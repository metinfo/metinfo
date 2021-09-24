<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_op');

/**
 * news标签类
 */

class relation_op extends base_op
{

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->relation_database = load::mod_class('relation/relation_database', 'new');
    }

    /**
     * @param string $listid
     * @param string $module
     * @param array $relations
     */
    public function setRelations($aid = '' , $module = '', $relations = array())
    {
        global $_M;
        if (is_numeric($module)) {
            $mod = $module;
        } else {
            $mod = $this->name_to_num($module);
        }

        $this->relation_database->delRelations($aid, $module);

        $relations = json_decode(stripslashes($relations), true);
        foreach ($relations as $key => $relation) {// $relation 模块|ID
            $save_data = array();
            $save_data['aid'] = $aid;
            $save_data['module'] = $mod;
            $save_data['relation_module'] = $relation['module'];
            $save_data['relation_id'] = $relation['id'];
            $save_data['lang'] = $_M['lang'];
            $res = $this->relation_database->insert($save_data);
        }
    }

    public function delRelations($aid, $module)
    {
        $this->relation_database->delRelations($aid, $module);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
