<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class online_op
{
    public function __construct()
    {
        global $_M;
        #$this->database = load::mod_class('online/online_database', 'new');
    }

    public function getOnlineHtml()
    {
        global $_M;
        $list = load::mod_class('online/online_label', 'new')->getOnlineList();
        $unm = count($list);
        foreach ($list as $key => $value) {
            $list[$key]['_index'] = $key;
            if($key==0){
                $list[$key]['_first'] = 1;
            }
            if($key==$unm-1){
                $list[$key]['_last'] = 1;
            }
        }

        $online_style = $_M['config']['met_online_skin'] ? $_M['config']['met_online_skin'] : 1;
        $data['online_list'] = $list;
        $data['url'] = $_M['url'];

        if ($_M['form']['module'] == 10001) {
            $_M['config']['met_onlinetel'] = str_replace('../', '', $_M['config']['met_onlinetel']);
        }

        $file_path = PATH_WEB . "app/system/online/web/templates/online_{$online_style}.php";
        $engine = load::sys_class('engine', 'new');
        $html = $engine->dofetch($file_path, $data);

        $redata = array();
        $redata['status'] = 1;
        $redata['html'] = $html;
        $redata['t'] = $_M['config']['met_online_type'];
        $redata['x'] = $_M['config']['met_online_x'] ? $_M['config']['met_online_x'] : "10";
        $redata['y'] = $_M['config']['met_online_y'] ? $_M['config']['met_online_y'] : "100";
        return $redata;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
