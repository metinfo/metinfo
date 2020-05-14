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

        // ���Excel�ļ�ͷ
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
        header('Cache-Control: max-age=0');

        // ��PHP�ļ������php://output ��ʾֱ������������
        $fp = fopen('php://output', 'a');

        // ���Excel������Ϣ
        foreach ($head as $i => $v) {
            // CSV��Excel֧��GBK���룬һ��Ҫת������������
            $head [$i] = iconv('utf-8', 'gbk', $v);
        }

        // ������ͨ��fputcsvд���ļ����
        fputcsv($fp, $head);

        // ������
        $cnt = 0;
        // ÿ��$limit�У�ˢ��һ�����buffer����Ҫ̫��Ҳ��Ҫ̫С
        $limit = 8000;
        foreach ($array as $row) {
            $cnt++;
            if ($limit == $cnt) { // ˢ��һ�����buffer����ֹ�������ݹ����������
                ob_flush();
                flush();
                $cnt = 0;
            }
            $content = array();
            foreach ($head as $i => $v) {
                $content [] = iconv('utf-8', 'gbk', $row [$i]);
            }
            fputcsv($fp, $content);
        }

        if ($foot) {
            foreach ($foot as $i => $v) {
                $foot[$i] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $foot);
        }

    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>