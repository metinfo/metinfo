<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

class html extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->html_handle = load::mod_class('html/html_handle', 'new');
    }

    //获取静态页面设置
    public function doGetSetup()
    {
        global $_M;
        $list = array();
        $list['met_webhtm'] = isset($_M['config']['met_webhtm']) ? $_M['config']['met_webhtm'] : '';
        $list['met_htmway'] = isset($_M['config']['met_htmway']) ? $_M['config']['met_htmway'] : '';
        $list['met_htmlurl'] = isset($_M['config']['met_htmlurl']) ? $_M['config']['met_htmlurl'] : '';
        $list['met_htmtype'] = isset($_M['config']['met_htmtype']) ? $_M['config']['met_htmtype'] : '';
        $list['met_htmpagename'] = isset($_M['config']['met_htmpagename']) ? $_M['config']['met_htmpagename'] : '';
        $list['met_listhtmltype'] = isset($_M['config']['met_listhtmltype']) ? $_M['config']['met_listhtmltype'] : '';
        $list['met_htmlistname'] = isset($_M['config']['met_htmlistname']) ? $_M['config']['met_htmlistname'] : '';
        $list['met_html_auto'] = isset($_M['config']['met_html_auto']) ? $_M['config']['met_html_auto'] : '';

        $this->success($list);
    }

    //保存静态页面设置
    public function doSaveSetup()
    {
        global $_M;
        $configlist = array();
        $configlist[] = 'met_webhtm';
        $configlist[] = 'met_htmway';
        $configlist[] = 'met_htmlurl';
        $configlist[] = 'met_htmtype';
        $configlist[] = 'met_htmpagename';
        $configlist[] = 'met_listhtmltype';
        $configlist[] = 'met_htmlistname';
        $configlist[] = 'met_html_auto';

        if (isset($_M['form']['met_htmtype'])) {
            $_M['form']['met_htmtype'] = $_M['form']['met_htmtype'] == 'htm' ? $_M['form']['met_htmtype'] : 'html';
        }

        //开启静态后关闭伪静态 && 删除重写文件
        if ($_M['form']['met_webhtm']) {
            $query = "UPDATE {$_M['table']['config']} SET value = 0 WHERE name='met_pseudo'";
            DB::query($query);

            $seo_open = load::mod_class('seo/seo_open','new');
            //删除重新文件
            $seo_open->delRewrite();
            if ($_M['form']['met_webhtm'] == 3) {
                //混合模式创建重写文件
                $seo_open->buildRewrite();
            }
        }

        configsave($configlist);/*保存系统配置*/
        buffer::clearConfig();

        $redata = array();
        $redata['callback_url'] = '';
        if ($_M['form']['met_html_auto'] && $_M['form']['met_webhtm']) {//html自动更新
            $redata['callback_url'] = $url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doSetval&lang={$_M['lang']}";
        }

        //写日志
        logs::addAdminLog('physicalstatic', 'submit', 'jsok', 'doSaveSetup');
        $this->success($redata, $_M['word']['jsok']);
    }

    //删除静态文件
    public function doDelHtml(){
        global $_M;
        $pageinfo = array();
        $pageinfo[] = $this->html_handle->homePage();
        $pageinfo = $this->html_handle->getPageInfo($pageinfo, '', '', '', '', 1, '');
        $pages = $this->html_handle->getQueryList($pageinfo);

        $ext = array('html', 'htm');
        foreach ($pages as $page) {
            $fpath = PATH_WEB . $page['filename'];
            $info = pathinfo($fpath);
            if (is_file($fpath) && isset($info['extension']) && in_array($info['extension'], $ext)) {
                delfile($fpath);
            }
        }

        buffer::clearConfig();
        logs::addAdminLog('physicalstatic', 'delete', 'jsok', 'doDelHtml');
        $this->success('', $_M['word']['jsok']);
        return;
    }

    //静态页面生成页面
    public function doGetHtml()
    {
        global $_M;
        buffer::clearConfig();
        $redata = array();

        $list = array();
        $list['name'] = $_M['word']['htmAll'];
        $list['content']['name'] = $_M['word']['htmCreateAll'];
        $list['content']['url'] = "{$_M['url']['own_form']}&a=doCreatePage&all=1";
        $redata[] = $list;

        $list = array();
        $list['name'] = $_M['word']['seotips6'];
        $list['content']['name'] = $_M['word']['htmTip3'];
        $list['content']['url'] = "{$_M['url']['own_form']}&a=doCreatePage&index=1";
        $redata[] = $list;

        $module = load::mod_class('column/column_op', 'new')->get_sorting_by_module(false, $_M['mark']);
        foreach ($module as $mod => $valm) {
            if (in_array($mod, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13))) {
                foreach ($valm['class1'] as $keyc1 => $valc1) {
                    $list = array();
                    $list['name'] = $valc1['name'];
                    $list['content']['name'] = $_M['word']['htmTip1'];
                    $list['content']['url'] = "{$_M['url']['own_form']}&a=doCreatePage&type=content&module={$valc1['module']}&class1={$valc1['id']}";

                    //模块内容列表页
                    if (in_array($valc1['module'], array(2, 3, 4, 5, 6, 7)) && in_array($_M['config']['met_webhtm'], array(2, 3))) {
                        $list['column']['name'] = $_M['word']['htmTip2'];
                        $list['column']['url'] = "{$_M['url']['own_form']}&a=doCreatePage&type=column&module={$valc1['module']}&class1={$valc1['id']}";
                    }
                    $redata[] = $list;
                }

                //二级栏目
                foreach ($valm['class2'] as $keyc2 => $valc2) {
                    if (!in_array($valc2['module'], array(7, 9, 11, 12, 13))) {
                        continue;
                    }

                    $list = array();
                    if ($valc2['module'] == 7) {
                        $list['name'] = $valc2['name'];
                        $list['column']['name'] = $_M['word']['htmTip2'];
                        $list['column']['url'] = "{$_M['url']['own_form']}&a=doCreatePage&type=column&module={$valc2['module']}&class1={$valc2['id']}";
                    }else{
                        $list['name'] = $valc2['name'];
                        $list['content']['name'] = $_M['word']['htmTip1'];
                        $list['content']['url'] = "{$_M['url']['own_form']}&a=doCreatePage&type=content&module={$valc2['module']}&class1={$valc2['id']}";
                    }
                    $redata[] = $list;
                }
            }
        }
        $this->success($redata);
    }

    /**
     * 获取静态页URL 生成静态页
     */
    public function _doCreatePage()
    {
        global $_M;
        $all = isset($_M['form']['all']) ? $_M['form']['all'] : '';
        $index = isset($_M['form']['index']) ? $_M['form']['index'] : '';
        $list_page = isset($_M['form']['list_page']) ? $_M['form']['list_page'] : '';
        $module = isset($_M['form']['module']) ? $_M['form']['module'] : '';
        $type = isset($_M['form']['type']) ? $_M['form']['type'] : '';
        $class1 = isset($_M['form']['class1']) ? $_M['form']['class1'] : '';
        $content = isset($_M['form']['content']) ? $_M['form']['content'] : '';

        $pageinfo = array();
        if ($all == 1 || $index == 1) {
            $pageinfo[] = $this->html_handle->homePage();
        }

        $pageinfo = $this->html_handle->getPageInfo($pageinfo, $type, $module, $list_page, $class1, $all, $content);

        $pages = $this->html_handle->getQueryList($pageinfo);
        $total = count($pages);

        Cache::put("static_list_" . $_M['lang'], $pages);

        foreach ($pages as $key => $val) {
            $f = urldecode($val['filename']);
            $pages[$key]['suc'] = "<a target=\"_blank\" href=\"{$_M['url']['web_site']}{$f}\">{$f} {$_M['word']['physicalgenok']}</a>";
            $pages[$key]['fail'] = "<a target=\"_blank\" href=\"{$_M['url']['web_site']}{$f}\" style=\"color:red\">{$f} {$_M['word']['html_createfail_v6']}</a>";
            $pages[$key]['current'] = $key + 1;
        }

        //写日志
        logs::addAdminLog('physicalstatic', 'js54', 'jsok', 'doCreatePage');
        $this->success($pages);
    }

    /**
     * 异步生成静态页
     */
    public function doCreatePage()
    {
        global $_M;
        $all = isset($_M['form']['all']) ? $_M['form']['all'] : '';
        $index = isset($_M['form']['index']) ? $_M['form']['index'] : '';
        $list_page = isset($_M['form']['list_page']) ? $_M['form']['list_page'] : '';
        $module = isset($_M['form']['module']) ? $_M['form']['module'] : '';
        $type = isset($_M['form']['type']) ? $_M['form']['type'] : '';
        $class1 = isset($_M['form']['class1']) ? $_M['form']['class1'] : '';
        $content = isset($_M['form']['content']) ? $_M['form']['content'] : '';

        $pageinfo = array();
        if ($all == 1 || $index == 1) {
            $pageinfo[] = $this->html_handle->homePage();
        }

        $pageinfo = $this->html_handle->getPageInfo($pageinfo, $type, $module, $list_page, $class1, $all, $content);

        $pages = $this->html_handle->getQueryList($pageinfo);
        $total = count($pages);

        logs::addAdminLog('physicalstatic', 'js54', 'jsok', 'doLoop');

        //静态页列表写入缓存
        Cache::del("static_list_err_" . $_M['lang']);
        Cache::del("static_list_suc_" . $_M['lang']);
        Cache::put("static_list_" . $_M['lang'], $pages);

        $redata = array();
        $redata['total'] = $total;
        $redata['callback_url'] = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doLoop&lang={$_M['lang']}";
        $redata['check_url'] = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n=html&c=html&a=doCheckPage";;
        $redata['retry_url'] = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n=html&c=html&a=doRetry";
        $this->success($redata);
    }

    /**
     * 重新生成失败页面
     */
    public function doRetry()
    {
        global $_M;
        $pages = Cache::get("static_list_err_" . $_M['lang']);
        sleep(1);
        if (!$pages) {
            $this->success('','Finished');
        }

        Cache::del("static_list_err_" . $_M['lang']);
        Cache::del("static_list_suc_" . $_M['lang']);
        Cache::put("static_list_" . $_M['lang'], $pages);
        sleep(1);

        $redata = array();
        $redata['total'] = is_array($pages) ? count($pages) : 0;
        $redata['callback_url'] = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doLoop&lang={$_M['lang']}";
        $redata['check_url'] = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n=html&c=html&a=doCheckPage";
        $redata['retry_url'] = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n=html&c=html&a=doRetry";
        $this->success($redata);
    }

    /**
     * @return mixed
     */
    public function doCheckPage()
    {
        global $_M;
        $page_list = cache::get("static_list_" . $_M['lang']);
        if (!$page_list) {
            //生成完毕
            $status = 1;
            sleep(2);
        }else{
            //循环
            $status = 2;
        }

        $suc = Cache::get("static_list_suc_" . $_M['lang']);
        if (!$suc) {
            $suc = array();
        }

        $err = Cache::get("static_list_err_" . $_M['lang']);
        if (!$err) {
            $err = array();
        }

        $redata['suc'] = $suc;
        $redata['suc_num'] = is_array($suc) ? count($suc) : 0;
        $redata['err'] = $err;
        $redata['err_num'] = is_array($err) ? count($err) : 0;
        $redata['status'] = $status;
        return jsoncallback($redata);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
