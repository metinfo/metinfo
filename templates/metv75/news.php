<?php defined('IN_MET') or exit('No permission'); ?>
<include file="head.php" />
<include file="para_search.php" />
<section class="met-news animsition">
    <div class="container">
        <div class="row">
            <!-- news_list_page met_16_1 -->
            <div class="col-md-9 met-news-body">
                <div class="row">
                    <div class="met-news-list met-news" m-id="noset">

                        <tag action='news.list' num="$c['met_news_list']" cid="$data['classnow']"></tag>
                        <if value="$sub">
                            <ul class="ulstyle met-pager-ajax imagesize" data-scale='{$c.met_newsimg_y}x{$c.met_newsimg_x}'>
                                <include file='ajax/news'/>
                            </ul>
                        <else/>
                            <div class='h-100 text-xs-center font-size-20 vertical-align'>{$c.met_data_null}</div>
                        </if>

                        <div class='m-t-20 text-xs-center hidden-sm-down' m-type="nosysdata">
                            <pager/>
                        </div>
                        <div class="met_pager met-pager-ajax-link hidden-md-up" data-plugin="appear" 
                        data-animate="slide-bottom" data-repeat="false" m-type="nosysdata">
                            <button type="button" class="btn btn-primary btn-block btn-squared ladda-button" 
                            id="met-pager-btn" data-plugin="ladda" data-style="slide-left" data-url="" data-page="1">
                                <i class="icon wb-chevron-down m-r-5" aria-hidden="true"></i>
                            </button>
                        </div>		

                    </div>
                </div>
            </div>

            <!-- sidebar met_83_1 -->
            <div class="col-md-3">
                <div class="row">

                    
                    <aside class="met-sidebar panel panel-body m-b-0" boxmh-h m-id='news_bar' m-type='nocontent'>
                        <div class="sidebar-search" data-placeholder="search">
                            <tag action="search.column"></tag>
                        </div>

                        <if value="$lang['bar_column_open']">
                            <ul class="sidebar-column list-icons">
                                <tag action='category' cid="$data['releclass1']">
                                <li>
                                    <a href="{$m.url}" title="{$m.name}" class="<if value='$data["classnow"] eq $m["id"]'>
                                            active
                                            </if>" {$m.urlnew}>{$m.name}</a>
                                </li>
                                <tag action='category' cid="$m['id']" type='son' class='active'>
                                <li>
                                    <if value="$m['sub'] && $lang['bar_column3_open']">
                                    <a href="javascript:;" title="{$m.name}" class='{$m.class}' {$m.urlnew} data-toggle="collapse" data-target=".sidebar-column3-{$m._index}">{$m.name}<i class="wb-chevron-right-mini"></i></a>
                                    <div class="sidebar-column3-{$m._index} collapse" aria-expanded="false">
                                        <ul class="m-t-5 p-l-20">
                                            <li><a href="{$m.url}" {$m.urlnew} title="{$lang.all}" class="{$m.class}">{$lang.all}</a></li>
                                            <tag action='category' cid="$m['id']" type='son' class='active'>
                                            <li><a href="{$m.url}" {$m.urlnew} title="{$m.name}" class='{$m.class}'>{$m.name}</a></li>
                                            </tag>
                                        </ul>
                                    </div>
                                    <else/>
                                    <a href="{$m.url}" title="{$m.name}" class='{$m.class}'>{$m.name}</a>
                                    </if>
                                </li>
                                </tag>
                                </tag>
                            </ul>
                        </if>
                        <if value="$lang['news_bar_list_open']">
                            <div class="sidebar-news-list recommend">
                                <h3 class='font-size-16 m-0'>{$lang.news_bar_list_title}</h3>
                                <ul class="list-group list-group-bordered m-t-10 m-b-0">
                                <?php $id=$lang['sidebar_newslist_idid']?$lang['sidebar_newslist_idid']:$data['class1']; ?>
                                    <tag action='list' type="$lang['news_bar_list_type']" cid="$id" num="$lang['sidebar_newslist_num']">
                                        <li class="list-group-item">
                                        <if value="1">
                                        <a class="imga" href="{$v.url}" title="{$v.title}" {$g.urlnew}>
                                                <img src="{$v.imgurl|thumb:800,500}" alt="{$v.title}" style="max-width:100%">
                                            </a>
                                            </if>
                                            <a href="{$v.url}" title="{$v.title}" {$g.urlnew}>{$v.title}</a>
                                        </li>
                                    </tag>
                                </ul>
                            </div>
                        </if>
                    </aside>             
                </div>
            </div>


        </div>
    </div>
</section>
<include file="foot.php" />