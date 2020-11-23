<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

class csv
{

    /**
     * @param $filename
     * @param $array
     * @param $head
     * @param array $foot
     */
    public function get_csv($filename, $array, $head, $foot = array())
    {
        // 输出Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
        header('Cache-Control: max-age=0');

        // ��PHP�ļ������php://output ��ʾֱ������������
        $fp = fopen('php://output', 'a');

        // 表头
        foreach ($head as $i => $v) {
            $head [$i] = iconv('utf-8', 'gb2312//TRANSLIT', $v);
        }
        fputcsv($fp, $head);

        $cnt = 0;
        // 内容
        $limit = 8000;
        foreach ($array as $row) {
            $cnt++;
            if ($limit == $cnt) {
                ob_flush();
                flush();
                $cnt = 0;
            }
            $content = array();
            foreach ($head as $i => $v) {
                $content [] = iconv('utf-8', 'gb2312//TRANSLIT', $row [$i]);
            }
            fputcsv($fp, $content);
        }

        if ($foot) {
            foreach ($foot as $i => $v) {
                $foot[$i] = iconv('utf-8', 'gb2312//TRANSLIT', $v);
            }
            fputcsv($fp, $foot);
        }
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>