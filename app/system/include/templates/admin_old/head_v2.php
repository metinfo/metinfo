<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$_M['form']['pageset']=1;
if($_M['word']['metinfo']){
    $met_title.='-'.$_M['word']['metinfo'];
}
$basic_admin_css_filemtime = filemtime(PATH_PUBLIC_WEB.'css/basic_admin.css');
?>
<!DOCTYPE HTML>
<html class="{$_M['html_class']}">
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta name="robots" content="noindex,nofllow">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0,minimal-ui">
<meta name="format-detection" content="telephone=no">
<title>{$met_title}</title>
<meta name="generator" content="MetInfo {$c.metcms_v}" data-variable="{$url.site}|{$_M['lang']}|{$c.met_skin_user}||||">
<link href="{$url.site}favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="{$url.public_web}css/basic_admin.css?{$basic_admin_css_filemtime}" rel='stylesheet' type='text/css'>
<?php
if(file_exists(PATH_OWN_FILE.'templates/css/metinfo.css')){
    $own_metinfo_css_filemtime = filemtime(PATH_OWN_FILE.'templates/css/metinfo.css');
?>
<link href="{$url.own_tem}css/metinfo.css?{$own_metinfo_css_filemtime}" rel='stylesheet' type='text/css'>
<?php } ?>
</head>
<!--['if lte IE 9']>
<div class="text-xs-center m-b-0 bg-blue-grey-100 alert">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">×</span>
    </button>
    {$word.browserupdatetips}
</div>
<!['endif']-->
<body class="{$_M['body_class']}">
<?php
if(!$head_no && !$_M['head_no']) {
    $privilege = background_privilege();
?>
<div class="metadmin-main container-fluid m-y-10">
    <?php
    $navlist = nav::get_nav();
    if($navlist){
    ?>
    <ul class="stat-list nav nav-tabs m-b-10 border-none">
        <?php
        foreach($navlist as $key => $val){
            $val['classnow']=$val['classnow']?'active':'';
        ?>
        <li class="nav-item"><a class='nav-link {$val.classnow}' title="{$val.title}" href="{$val.url}" target="{$val.target}">{$val.title}</a></li>
        <?php } ?>
    </ul>
<?php
    }
}
?>