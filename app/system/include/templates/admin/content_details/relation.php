<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<h3 class='example-title'>
    <span class='d-inline-block' style="width:155px;">{$word.relation_data}</span>
    <button type="button" class="btn btn-outline-primary btn-relation" data-toggle="modal" data-target=".content-relation-manage-modal" data-modal-title="选择内容" data-modal-url="relation/list/?content_id={$data.list.id}&module={$data.n}" data-modal-size="xl" data-modal-fullheight="1" data-modal-oktext="" data-modal-notext="{$word.close}">{$word.relation_data_add}</button>
    <span class="text-help font-weight-normal font-size-14">{$word.relation_tips}</span>
</h3>
<div class="content-details-relationlist met-scrollbar scrollbar-grey" data-info="{$data.n}|{$data.list.id}" data-url="{$url.adminurl}n=relation&c=relation_admin&a=doGetRelations&content_id={$data.list.id}&module={$data.n}">
    <div class="metadmin-loader py-5"><div class="text-center d-flex align-items-center"><div class="loader loader-round-circle"></div></div></div>
</div>