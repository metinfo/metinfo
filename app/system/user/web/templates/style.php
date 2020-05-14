<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$loginbg = $c['met_member_bgimage']?"background:url(".$c['met_member_bgimage'].") center / cover no-repeat;":'';
?>
<style>
.met-member{background:{$c.met_member_bgcolor};{$loginbg}}
</style>