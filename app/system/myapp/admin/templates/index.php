<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$head_tab_active=0;
$admin = admin_information();
if (!strstr($admin['admin_type'],'s1800') && !strstr($admin['admin_type'],'metinfo')){
    $head_tab=array(
        array('title' => $word['myapps'], 'url' => '#/myapp'),
        array('title' => $word['columnmore'],'url'=>$_M['config']['app_url'],'target'=>"1")
    );
}else {
    $head_tab = array();
    $head_tab[] = array('title' => $word['myapps'], 'url' => '#/myapp');
    if ($_M['config']['met_agents_metmsg'] == 1) {
        $head_tab[] = array('title' => $word['freeapp'], 'url' => '#/myapp/free');
        $head_tab[] = array('title' => $word['businessapp'], 'url' => '#/myapp/business');
        $head_tab[] = array('title' => $word['chargeapp'], 'url' => '#/myapp/charge');
        $head_tab[] = array('title' => $word['columnmore'], 'url' => $_M['config']['app_url'], 'target' => "1");
    }
}

?>
<div class="met-myapp">
    <include file="pub/head_tab"/>
    <if value="$c['met_agents_metmsg']">
    <div class="met-myapp-right">
    	<a href="#/myapp/login" class="mr-2">
    	<button class="btn btn-primary" >
    		{$word.landing}
    	</button>
    	</a>
    	<button class="btn btn-primary">{$word.registration}</button>
    </div>
    </if>
    <div class="met-myapp-list mt-3">
  <div class="met-myapp-list-row"></div>
</div>
<div class="app-detail"></div>
</div>