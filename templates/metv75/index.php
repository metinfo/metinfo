<?php defined('IN_MET') or exit('No permission'); ?>
<include file="head.php" />
<main>

    <!-- 产品区块 -->
    <div class="met-index-product met-index-body text-xs-center" m-id="met_index_product">
        <div class="container">
            <if value="$lang['index_product_title']">
                <h2 class="m-t-0 invisible" data-plugin="appear" data-animate="slide-top" data-repeat="false">{$lang.index_product_title}</h2>
            </if>
            <if value="$lang['index_product_desc']">
                <p class="desc m-b-0 invisible" data-plugin="appear" data-animate="fade" data-repeat="false">{$lang.index_product_desc}</p>
            </if>
            <!-- 切换卡 -->
            <ul class="nav nav-pills">
                <tag action="category" type="son" cid="$lang['index_product_id']">
                    <if value="$m['_index'] lt $lang['index_product_allnum']">
                        <li class="nav-item mr-2">
                            <a class='nav-link btn rounded-pill <if value="$m['_index'] eq 0">active
                    </if>'
                    data-toggle="tab" href="#list-type-tab-pane-{$m._index}">
                    {$m.name}
                    </a>
                    </li>
                    </if>
                </tag>
            </ul>
            <!-- 切换卡 -->

            <div class="imagesize index-product-list tab-content mt-3 media-body">

                <tag action="category" type="son" cid="$lang['index_product_id']">
                    <if value="$m['_index'] lt $lang['index_product_allnum']">
                        <div class='tab-pane <if value="$m['_index'] eq 0">active
                    </if>' id="list-type-tab-pane-{$m._index}">
                    <div class="leftbox">
                        <a href="{$m.url}" title="{$m.name}" class="block" {$g.urlnew}>
                            <img class="" src="{$m.columnimg|thumb:$lang['index_product_img_w'],$lang['index_product_img_h']}" alt="{$v.title}">
                        </a>
                    </div>

                    <div class="rightbox">
                        <tag action="list" cid="$m['id']" num="4" type="$lang['index_product_type']">
                            <div class="itembox">

                                <div class="">
                                    <img class="" src="{$v.imgurl|thumb:$lang['index_product_img_w1'],$lang['index_product_img_h1']}" alt="{$v.title}">
                                </div>

                                <a met-imgmask href="{$v.url}" title="{$v.title}" class="txt-info" {$g.urlnew}>
                                    <h4 class="card-title m-0 p-x-10 text-shadow-none text-truncate">
                                        {$v.title}
                                    </h4>
                                    <if value="$lang['index_product_moretext']">
                                        <p class="m-b-0 moretext">{$lang.index_product_moretext}</p>
                                    </if>
                                </a>

                            </div>
                        </tag>
                    </div>

            </div>

            </if>
            </tag>
        </div>
        </div>
    </div>


    <!-- 简介区块 -->
    <div class="met-index-about met-index-body text-xs-center" m-id="met_index_about" m-type="nocontent">
        <div class="container">
            <if value="$lang['home_about_title']">
                <h2 class="m-t-0 invisible" data-plugin="appear" data-animate="slide-top" data-repeat="false">{$lang.home_about_title}</h2>
            </if>
            <if value="$lang['home_about_desc']">
                <p class="desc m-b-0 invisible" data-plugin="appear" data-animate="fade" data-repeat="false">{$lang.home_about_desc}</p>
            </if>
            <div class="row">
                <div class="text met-editor">
                    {$lang.home_about_content}
                </div>
            </div>
        </div>
    </div>


    <!-- 图片区块 -->
    <div class="met-index-case met-index-body text-xs-center" m-id="met_index_case">
        <div class="container">
            <if value="$lang['home_case_title']">
                <h2 class="m-t-0 invisible" data-plugin="appear" data-animate="slide-top" data-repeat="false">{$lang.home_case_title}</h2>
            </if>
            <if value="$lang['home_case_desc']">
                <p class="desc m-b-0 invisible" data-plugin="appear" data-animate="fade" data-repeat="false">{$lang.home_case_desc}</p>
            </if>
            <div class="swiper-container">
                <ul class=" swiper-wrapper">
                    <tag action="list" cid="$lang['home_case_id']" num="$lang['home_case_num']" type="$lang['home_case_type']">

                        <if value="$lang['home_case_num']">
                        <li class="swiper-slide">
                            <if value="$lang['home_case_linkok']">
                                <a href="{$v.url}" title="{$v.title}" {$g.urlnew}>
                            </if>
                            <img src="{$v.imgurl|thumb:$lang['home_case_imgw'],$lang['home_case_imgh']}" alt="{$v.title}" style="max-width: 100%;" />
                            <if value="$lang['home_case_linkok']">
                                </a>
                            </if>

                            <a <if value="$lang['home_case_linkok']">href="{$v.url}" {$g.urlnew}
                                <else />
                                href="javascript:void(0)"</if> title="{$v.title}" class="a_box">
                                <h4 class="contenttitle">{$v.title}</h4>
                            </a>

                        </li>
                        </if>
                    </tag>
                </ul>
            </div>
            <div class="swiper-button btn-active">
                <div class="swiper-button-prev1"></div>
                <div class="swiper-button-next1"></div>
            </div>

        </div>
    </div>


    <!-- 文章区块 -->
    <div class="met-index-news met-index-body text-xs-center" m-id="met_index_news">
        <div class="container">
            <if value="$lang['index_news_title']">
                <h2 class="m-t-0 invisible" data-plugin="appear" data-animate="slide-top" data-repeat="false">{$lang.index_news_title}</h2>
            </if>
            <if value="$lang['index_news_desc']">
                <p class="desc m-b-0 invisible" data-plugin="appear" data-animate="fade" data-repeat="false">{$lang.index_news_desc}</p>
            </if>

            <if value="$lang['index_news_mytype']">
                <!-- 无图风格 -->
                <div class="text-xs-left m-t-30 index-news-list index-news-list-text">
                        <ul class="list-group blocks-lg-2">
                            <tag action="list" cid="$lang['home_news1']" num="$lang['home_news_num']" type="$lang['home_news_type']">

                                <?php
                                $v['_index'] =  $v['_index'] + 1;
                                $v['_index'] = $v['_index'] <= 9 ? '0' . $v['_index'] : $v['_index'];
                                ?>


                                <li class="invisible" data-plugin="appear" data-animate="slide-bottom" data-repeat="false">
                                    <a class="media media-lg flex" href="{$v.url}" title="{$v.title}" {$g.urlnew}>

                                        <div class="media-left">
                                            {$v._index}
                                        </div>

                                        <div class="media-body">
                                            <h4 class="media-heading m-b-10">{$v.title}</h4>
                                            <p class="info m-b-8">
                                                <span>{$v.updatetime}</span>
                                            </p>
                                            <p class="des m-b-5">{$v.description|met_substr:0,$lang['home_news_img_maxnum']}...</p>
                                        </div>
                                    </a>
                                </li>
                            </tag>
                        </ul>
                </div>
            <else />
                <!-- 有图风格 -->
                <div class="text-xs-left m-t-30 index-news-list">

                    <div class="left_box">
                        <tag action="list" cid="$lang['home_news1']" num="1" type="$lang['home_news_type']">
                            <if value="$v['_index'] eq 0">
                                <a class="itembox" href="{$v.url}" title="{$v.title}" {$g.urlnew}>
                                    <div class="imgbox">
                                        <img class="" src="{$v.imgurl|thumb:$lang['home_product_img_w1'],$lang['home_product_img_h1']}" alt="{$v.title}">
                                    </div>
                                    <div class="textbox">
                                        <h4 class="">{$v.title}</h4>
                                    </div>
                                </a>

                            </if>
                        </tag>
                    </div>

                    <div class="right_box">
                        <tag action="list" cid="$lang['home_news1']" num="4" type="$lang['home_news_type']">
                            <if value="$v['_index'] neq 0">
                                <a class="media media-lg" href="{$v.url}" title="{$v.title}" {$g.urlnew}>
                                    <div class="imgbox col-md-4">
                                        <img class="" src="{$v.imgurl|thumb:$lang['home_product_img_w'],$lang['home_product_img_h']}" alt="{$v.title}">
                                    </div>

                                    <div class="col-md-8">
                                        <h4 class=" m-b-10"> {$v.title} </h4>
                                        <p class="info">
                                            <span>{$v.updatetime}</span>
                                        </p>
                                        <p class="des m-b-5">{$v.description|met_substr:0,$lang['home_news_img_maxnum']}...</p>
                                    </div>
                                </a>
                            </if>
                        </tag>
                    </div>



                </div>
            </if>


        </div>
    </div>




</main>
<include file="foot.php" />