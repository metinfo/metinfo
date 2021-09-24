<?php defined('IN_MET') or exit('No permission'); ?>
<include file="head.php" />
<include file="para_search.php" />
<div class="met-product-list animsition" m-id="met_product">
    <div class="container">

        <div class="row myrow_box">

            <tag action='product.list' num="$c['met_product_list']"></tag>
            
            <div class="product-right" data-plugin="appear" data-animate="slide-bottom" data-repeat="false">
                

                <ul class="<if value=" $lang['column_xs'] eq 1">block-xs-100
                    <else />blocks-xs-1</if>blocks-md-2 blocks-lg-3
                    blocks-xxl-3 met-pager-ajax imagesize cover " 
                    data-scale='{$c.met_productimg_y}x{$c.met_productimg_x}'>

                    <if value="$c['met_product_page'] eq 1 && $data['sub']">

                        <tag action="category" type="son" cid="$data['classnow']">
                            <li class="prd-li">
                                <div class="ih-item square effect3 bottom_to_top">
                                    <a href="{$v.url}" {$g.urlnew} title="{$v.title}">
                                        <div class="img">
                                            <img class="img-con" src="{$m.columnimg|thumb:$c['met_productimg_x'],$c['met_productimg_y']}" 
                                            style="max-width:100%;" alt="{$m.name}">
                                        </div>
                                        <div class="info">
                                            <if value="1">
                                                <h3>{$m.name}</h3>
                                            </if>
                                        </div>
                                    </a>
                                </div>
                            </li>
                        </tag>

                    <else />

                        <!-- 展示全部的时候 -->
                        <tag action="product.list" num="$c['met_product_list']">
                            <li class="prd-li">
                                <div class="ih-item square effect3 bottom_to_top">
                                    <a href="{$v.url}" {$g.urlnew} title="{$v.title}">
                                        <div class="img">
                                            <img class="img-con" data-original="{$v.imgurl|thumb:$c['met_productimg_x'],$c['met_productimg_y']}" 
                                            alt="{$v.title}" style="max-width:100%;" />
                                        </div>
                                        <div class="info">
                                            <if value="1">
                                                <h3>
                                                    <if value="$v['_title']">{$v._title}
                                                        <else />{$v.title}
                                                    </if>
                                                </h3>
                                            </if>
                                            <p class='m-b-0 m-t-5 price'>{$v.price_str}</p>
                                        </div>
                                    </a>
                                </div>
                            </li>
                        </tag>

                    </if>
                </ul>

            </div>
            
            <if value="!$c['met_product_page']">
                <div class='m-t-20 text-xs-center hidden-sm-down' m-type="nosysdata">
                    <pager type="$c['met_product_page']" />
                </div>
                <div class="met_pager met-pager-ajax-link hidden-md-up" data-plugin="appear" data-animate="slide-bottom" data-repeat="false" m-type="nosysdata">
                    <button type="button" class="btn btn-primary btn-block btn-squared ladda-button" id="met-pager-btn" data-plugin="ladda" data-style="slide-left" data-url="" data-page="1">
                        <i class="icon wb-chevron-down m-r-5" aria-hidden="true"></i>
                        {$lang.page_ajax_next}
                    </button>
                </div>
            </if>



    </div>

</div>
</div>
<include file="foot.php" />