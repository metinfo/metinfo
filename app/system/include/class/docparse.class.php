<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * url处理类
 */
class docparse
{
    public function __construct()
    {
    }

    /**
     * 为字段赋值
     * @param  string $module模型名称
     * @param  array $input 所有输入变量
     * @return array          合法的页面变量
     */
    public function parseDoc($doc_path = '')
    {
        global $_M;
        if (!$doc_path && is_file($doc_path)) {
            return false;
        }

        $path_info = pathinfo($doc_path);
        $arr = array('docx');
        if (!in_array($path_info['extension'], $arr)) {
            return false;
        }

        require_once PATH_WEB . 'app/system/include/class/phpword/vendor/autoload.php';
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($doc_path);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, "HTML");
        $html = $xmlWriter->getContent();
        $html = self::htmlImgReplace($html);
        return $html;
    }

    /**
     * 替换doc图片
     * @param $body_html
     * @return string
     */
    protected function htmlImgReplace($html_str = '')
    {
        $Document = new DOMDocument('1.0', 'UTF-8');
        $Document->loadHTML($html_str);

        $body_dom = $Document->getElementsByTagName('body');
        if ($body_dom && $body_dom->length > 0) {
            $body = $body_dom->item(0);
            $body_html = $Document->savehtml($body);
        }else{
            return '';
        }

        //获取内容图片并替换路径
        $body_html = self::parseImgStr($Document, $body_html);
        return $body_html;
    }

    /**
     * @param $document
     * @param string $content
     * @param string $tag_name
     * @param string $attr_name
     * @return mixed|string
     */
    protected function parseImgStr($document, $content = '', $tag_name = 'img', $attr_name = 'src')
    {
        if (!$content) {
            return '';
        }

        $tag_list = $document->getElementsByTagName($tag_name);
        $i = 0;
        $flist = array();

        foreach ($tag_list as $tag) {
            $i++;
            $attrs = $tag->attributes;
            foreach ($attrs as $name => $attr) {
                if ($name == $attr_name && $attr->value != '') {
                    $flist[] = $attr->value;
                    break;
                }
            }
        }

        $flist = array_unique($flist);
        foreach ($flist as $row) {
            if (!$row) {
                continue;
            }

            $pattern = "/^data:(\w+)\/(\w+);base64,([\S\s]+)/";
            $res = preg_match($pattern, $row, $match);
            if (!$res) {
                return '';
            }
            $ext = isset($match[2]) ? $match[2] : '';
            $imgdata = isset($match[3]) ? $match[3] : '';
            $new_url = self::parseImg($imgdata, $ext);

            $row_data = str_replace("\n", "%0A", $row);
            $row_data = str_replace("\r", "%0D", $row_data);
            $content = str_replace($row_data, $new_url, $content);
        }
        return $content;
    }

    /**
     * @param string $imgdata
     * @param string $ext
     * @return mixed|string
     */
    protected function parseImg($imgdata = '', $ext = 'jpg')
    {
        global $_M;
        if (!$imgdata) {
            return '';
        }

        $imgdata = str_replace(array("%0D","%0A"), '', $imgdata);
        $img_bin = base64_decode($imgdata);
        if (!$img_bin) {
            return '';
        }

        $new_path = "../upload/" . date("Ym") . "/" . random(10, 1) . '.' . $ext;
        $path_info = pathinfo($new_path);

        if (!is_dir($path_info['dirname'])) {
            makedir($path_info['dirname']);
        }

        file_put_contents($new_path, $img_bin);
        $new_url = "../".str_replace('../', '', $new_path);

        return $new_url;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
