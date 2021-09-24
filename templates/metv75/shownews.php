<?php defined('IN_MET') or exit('No permission'); ?>
<include file="head.php" />
<main class="met-shownews animsition">
    <div class="container">
        <div class="row">
        <div class="clearfix">

            <div class="col-md-9 met-shownews-body" m-id="noset">
                <div class="row">

                    <section class="details-title border-bottom1">
						<h1 class='m-0'>{$data.title}</h1>
						<div class="info font-weight-300">
							<span>{$data.updatetime}</span>
                            <if value="1">
							 <span>{$data.issue}</span>
                            </if>
                            <if value="1">
    							<span>
    								<i class="icon wb-eye m-r-5" aria-hidden="true"></i>
    								{$data.hits}
    							</span>
                            </if>
						</div>
					</section>
					<section class="met-editor clearfix">
						{$data.content}
                    </section>
                    <if value="1">
                        <list data="$data['taglist']" name="$tag" num="4"></list>
                        <if value="$sub">
                            <div class="tag">
                                <span>{$data.tagname}</span>
                                <list data="$data['taglist']" name="$tag" num="3">
                                    <a href="{$tag.url}" title="{$tag.name}">{$tag.name}</a>
                                </list>
                            </div>
                        </if>
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
</main>
<include file="foot.php" />