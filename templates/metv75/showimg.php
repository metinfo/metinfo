<?php defined('IN_MET') or exit('No permission'); ?>
<include file="head.php" />
<div class="met-showimg">
    <div class="container">
        <div class="row">
            <div class="met-showimg-body col-md-9" m-id='noset'>
                <div class="row">
                    <section class="details-title border-bottom1">
                        <h1 class='m-t-10 m-b-5'>{$data.title}</h1>
                        <div class="info">
                            <span><i class="icon wb-time m-r-5" aria-hidden="true"></i>{$data.updatetime}</span>
                            <span><i class="icon wb-eye m-r-5" aria-hidden="true"></i>{$data.hits}</span>
                        </div>
                    </section>
                    <section class='met-showimg-con'>
                        <div class='met-showimg-list fngallery cover text-xs-center' id="met-imgs-slick"  m-type="displayimgs">
                            <list data="$data['displayimgs']" name="$v">
                            <div class='slick-slide'>
                                <a href='{$v.img}' data-size='{$v.x}x{$v.y}' data-med='{$v.img}' 
                                data-med-size='{$v.x}x{$v.y}' class='lg-item-box' data-src='{$v.img}' 
                                data-exthumbimage="{$v.img|thumb:60,60}" data-sub-html='{$v.title}'>
                                    <img <if value="$v['_index'] gt 0">data-lazy<else/>src</if>="{$v.img|thumb:$c['met_imgdetail_x'],$c['met_imgdetail_y']}" 
                                    class='img-fluid' alt='{$v.title}' height="200" />
                                </a>
                            </div>
                            </list>
                        </div>
                    </section>
                    <ul class="img-paralist paralist blocks-100 blocks-sm-2 blocks-md-3 blocks-xl-4">
                        <list data="$data['para']" name='$para'>
                        <if value="$para['value']">
                            <li><span>{$para.name}：</span>{$para.value}</li>
                        </if>
                        </list>
                    </ul>
                    <section class="met-editor clearfix m-t-20">{$data.content}</section>
                    <list data="$data['taglist']" name="$tag"></list>
                    <if value="$sub">
                        <div class="tags">
                                <span>{$data.tagname}</span>
                                <list data="$data['taglist']" name="$tag" num="3">
                                        <a href="{$tag.url}" title="{$tag.name}">{$tag.name}</a>
                                </list>
                        </div>
                    </if>
                    <pagination/>



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
</div>
<include file="foot.php" />