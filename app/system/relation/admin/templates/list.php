<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<div class="content-relation-body">
    <div class="clearfix">
        <div data-plugin='select-linkage' data-select-url='{$url.adminurl}n=relation&c=relation_admin&a=doGetClasslist' data-required="1" data-value_key="value" data-data_val_key="module" class="clearfix mr-3 float-left">
            <select name="class1" class="form-control mr-1 w-a prov float-left"></select>
            <select name="class2" class="form-control mr-1 w-a city float-left"></select>
            <select name="class3" class="form-control mr-1 w-a dist float-left"></select>
        </div>
        <!-- <div class="input-group w-a float-left">
            <input type="search" name="keyword" placeholder="搜索" class="form-control" data-table-search="#content-relation-list">
            <div class="input-group-append">
                <div class="input-group-text btn bg-none px-2"><i class="input-search-icon fa-search" aria-hidden="true"></i></div>
            </div>
        </div> -->
    </div>
    <form action="" method="post" data-content_info="{$data.module}|{$data.content_id}">
        <input type="hidden" name="classid" value="0" data-table-search="#content-relation-list">
        <table class="table table-hover dataTable w-100" id="content-relation-list" data-ajaxurl="{$url.adminurl}n=relation&c=relation_admin&a=doGetDatelist" data-plugin="checkAll" data-datatable_order="#content-relation-list">
            <thead>
                <tr>
                    <include file="pub/content_list/checkall_all"/>
                    <th>{$word.title}</th>
                    <th>{$word.state}</th>
                </tr>
            </thead>
            <tbody>
                <?php $colspan=3; ?>
                <include file="pub/content_list/table_loader"/>
            </tbody>
            <?php
            $colspan = 2;
            $submit_text = $_M['word']['relation_add'];
            ?>
            <include file="pub/content_list/tfoot_first"/>
                    <button type="button" class="btn btn-default">{$word.relation_cancel}</button>
                </th>
            </tfoot>
        </table>
    </form>
</div>