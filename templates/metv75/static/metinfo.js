/**
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 * M['weburl']      网站网址
 * M['lang']        网站语言
 * M['tem']         模板目录路径
 * M['classnow']    当前栏目ID
 * M['id']          当前页面ID
 * M['module']      当前页面所属模块
 * met_prevarrow,
   met_nextarrow    slick插件翻页按钮自定义html
 * M['device_type'] 客户端判断（d：PC端，t：平板端，m：手机端）
 */
   $(function(){

    /*导航处理*/
    //#region
   var aLink=$(".met-nav").find('.dropdown a.nav-link');
   if(M.device_type!='d') aLink.removeAttr('data-hover');
    aLink.click(function(){
        if((M.device_type=='d'||Breakpoints.is('lg')||Breakpoints.is('xlg')) && $(this).attr("data-hover")){
            if($(this).attr('target')=='_blank'){
                window.open($(this).attr('href'));
            }else{
                location=$(this).attr('href');
            }
        }
    });

    // 导航下拉菜单三级栏目展开处理
    $met_navlist=$('.met-nav .navlist');
    if(M['device_type'] =='d'){
        if($met_navlist.find('.dropdown-submenu').length){
            $met_navlist.find('.dropdown-submenu').hover(function(){
                $(this).parent('.dropdown-menu').addClass('overflow-visible');
            },function(){
                $(this).parent('.dropdown-menu').removeClass('overflow-visible');
            });
        }
    }else{
        if($met_navlist.find('.dropdown-submenu').length){
            setTimeout(function(){
                $met_navlist.find('.dropdown-submenu .dropdown-menu').addClass('block box-shadow-none').prev('.dropdown-item').addClass('dropdown-a');
            },0)
        }
    }
    //#endregion




    /*banner 自定义高度*/
    //#region
    var img = $(".met-banner").find('img').eq(0);
    function imgh(){
        img.imageloadFun(function() {
            Breakpoints.on('md lg', {
                enter: function() {
                     $(".carousel-item").each(function(){
                        var ph=$(this).find('img').attr('pch');//pc端
                        if(ph!=0){
                            $(this).find('img').height(ph);
                        }
                    });
                }
            })
            Breakpoints.on('sm', {
                enter: function() {
                     $(".carousel-item").each(function(){
                        var ah=$(this).find('img').attr('adh');//平板
                        if(ah!=0){
                            $(this).find('img').height(ah);
                        }
                    });
                }
            })
            Breakpoints.on('xs', {
                enter: function() {
                     $(".carousel-item").each(function(){
                        var ih=$(this).find('img').attr('iph');//手机端
                        if(ih!=0){
                            $(this).find('img').height(ih);
                        }
                    });
                }
            })
        })
    }
    imgh();
    $(window).resize(debounce(imgh));
    // banner新增设置
    var btns=$(".met-banner .slick-btn");
    if(btns.length){
        btns.each(function(){
            var set=$(this).attr("infoset"),
            arr=set.split("|");
            fontsize=arr[0]!=0?arr[0]:16,
            btn_txt_color=arr[1],
            hbtn_txt_color=arr[2],
            but_bg_color=arr[3],
            hbut_bg_color=arr[4],
            but_x=arr[5]!=0?arr[5]:"auto",
            but_y=arr[6]!=0?arr[6]:"auto",
            $(this).css({
                "font-size":fontsize+'px',
                "color":btn_txt_color,
                "background-color":but_bg_color,
                "width":but_x,
                "height":but_y
            });
        });
        btns.hover(function(){
            var set=$(this).attr("infoset"),
            arr=set.split("|");
            hbtn_txt_color=arr[2],
            hbut_bg_color=arr[4];
            $(this).css({
                "color":hbtn_txt_color,
                "background-color":hbut_bg_color
            });
        },function(){
            var set=$(this).attr("infoset"),
            arr=set.split("|");
            btn_txt_color=arr[1],
            but_bg_color=arr[3];
            $(this).css({
                "color":btn_txt_color,
                "background-color":but_bg_color
            });
        });
    }
    //#endregion

    // 图片列表轮播
    //#region
    var swiper = new Swiper('.met-index-case .swiper-container', {
        slidesPerView: 3,
        slidesPerGroup: 1,
        autoplay: 0,
        spaceBetween: 22,
        // observer: true,
        // observeParents: true,
        // observeSlideChildren:true,
        speed: 500,
        prevButton:'.swiper-button-prev1',
        nextButton:'.swiper-button-next1',
        breakpoints: {
          767: {
            slidesPerView: 1,
          },
          992: {
            slidesPerView: 3,
          },
          1280: {
            slidesPerView: 3,
          }
        }
      })
    //#endregion

    // 内页二级导航
    //#region
    function subclum(){
        // $('.met-column-nav').find('.form-group form input[type="text"]').attr({placeholder:METUI['.met-column-nav'].find('.form-group').data('placeholder')});
        var m = $('.met-column-nav');//此处对应的最外层的那个css类名
        // 内页子栏目导航水平滚动

        $('body').wrapInner('<div class="cover"></div>');
        var nav = m.find('.subcolumn-nav'),
            ul = m.find('ul'),
            li=ul.find('li'),
            dropdown = ul.find('.dropdown'),
            w=li.parentWidth(),
            uw=ul.width();
        if (ul.length && w>uw) {
            ul.navtabSwiper();
            if (dropdown.length) {
                nav.css('width', '100%');
                $(".swiper-navtab").addClass("overflow-visible");
            }

        }
    }
    subclum()
    //#endregion


    // 侧边栏
    //#region
    function sidebarfun() {
        var $sidebar_piclist = $('.sidebar-piclist-ul');
        if ($sidebar_piclist.find('.masonry-child').length > 1) {
            // 图片列表瀑布流
            Breakpoints.on('xs sm', {
                enter: function () {
                    setTimeout(function () {
                        $sidebar_piclist.masonry({
                            itemSelector: ".masonry-child"
                        });
                    }, 500)
                }
            });
        }
        // $('.sidebar-search form input[type="text"]').attr($('.sidebar-search').data('placeholder'));
    }
    sidebarfun()
    //#endregion

    // 图片详情
    //#region
    function img_detailfun() {
        var a=$('.col-md-3');
        var b=$('.pright');
        if(a.length&&b.length){
                //移动端侧边栏换位置处理
            if($(window).width()>=991){
                // a.insertBefore(b);    //移动节点
                a.find(".met-sidebar").css({
                    "margin-left":"0px",
                    "margin-right":"30px"
                })
            }
        }
    }
    img_detailfun()
    //#endregion


    //简体繁体互换
    var tsChangges=function(change){
            tsChangge(change,function(isSimplified){
                $('#btn-convert').text(isSimplified?'繁體':'简体');
            });
        };
    tsChangges();
    $('#btn-convert').click(function() {
        tsChangges(1);
    });
    // 底部微信
    if($('#met-weixin').length){
        var $met_weixin=$('#met-weixin');
        Breakpoints.on('xs',{
            enter:function(){
                if($met_weixin.offset().left < 80) $met_weixin.attr({'data-placement':'right'});
                if($(window).width()-$met_weixin.offset().left-$met_weixin.outerWidth() < 80) $met_weixin.attr({'data-placement':'left'});
            }
        })
        if($met_weixin.data('trigger')=='click'){
            $met_weixin.mouseup(function(){
                $(this).click();
            });
        }
    }
    // 底部导航手机端处理
    if($('.met-footnav .mob-masonry .masonry-item').length){
        Breakpoints.get('xs').on({
            enter:function(){
                $('.met-footnav .mob-masonry').masonry({itemSelector:".masonry-item"});
            }
        });
    }
    // 底部菜单处理
    var $foot_menu=$(".met-menu-list");
    if($foot_menu.length){
        var h_m=$foot_menu.height();
        function pd(){
            if($foot_menu.hasClass('iskeshi') || $(window).width()<768){
                    $(".met-foot-info").css("padding-bottom",h_m);
                    $(".shop-product-intro .cart-favorite").css("bottom",h_m);
                }
        }
        pd();
        $(window).resize(debounce(pd));
    }
    var $foot_menu_list=$foot_menu.find(".item");
    $foot_menu_list.each(function(){
            var href=$(this).attr("href");
            if(href.indexOf("http://wpa.qq.com/")>=0){
                var patt1 = /uin=\d+&/;
                var qq=href.match(patt1);
                if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent) || /(Android)/i.test(navigator.userAgent)) {
                         $(this).attr("href","mqqwpa://im/chat?chat_type=wpa&"+qq[0]+"version=1&src_type=web&web_src=oicqzone.com");
                    }
            }
        });



    // 产品列表
    var $met_indexpro=$('.met-index-product'),
        $met_indexpro_navtabs=$met_indexpro.find(".nav-tabs");
    $met_indexpro.find('.index-product-list:gt(0) li [data-src]').each(function(index, el) {
        $(this).attr({'data-original':$(this).data('src')});
    }).lazyload({event:'sporty'});;
    if($met_indexpro_navtabs.length){
        $met_indexpro_navtabs.navtabSwiper();// 选项卡水平滚动
        $met_indexpro_navtabs.find('li a').click(function() {
            $($(this).attr('href')+' li [data-original]').trigger('sporty');
        });
    }


    // 图片区块
    var $met_indexcase=$('.met-index-case').find(".met-index-list"),
        indexcase_num=$met_indexcase.data('num');
    if(indexcase_num) indexcase_num=indexcase_num.split('|');
    $met_indexcase.slick({
        slidesToShow: indexcase_num&&indexcase_num[0]?indexcase_num[0]:8,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        responsive: [
            {
              breakpoint: 1600,
              settings: {
                slidesToShow: indexcase_num&&indexcase_num[1]?indexcase_num[1]:6,
              }
            },
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: indexcase_num&&indexcase_num[2]?indexcase_num[2]:4,
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: indexcase_num&&indexcase_num[3]?indexcase_num[3]:2,
              }
            },
          ]
    });


});