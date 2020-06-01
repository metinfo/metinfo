/*
详情页展示图片（需调用slick插件）
 */
(function() {
	$(function() {
		// 产品详情页、图片模块详情页轮播图，共用插件
		var $met_img_slick = $('#met-imgs-slick'),
			$met_img_slick_slide = $met_img_slick.find('.slick-slide');
		if ($met_img_slick_slide.length > 1) {
			// 缩略图水平滑动
			$met_img_slick.on('init', function(event, slick) {
					$met_img_slick.find('ul.slick-dots').navtabSwiper();
				})
				// 开始轮播
			var slick_swipe = true,
				slick_fade = slick_arrows = false;
			if (M.device_type == 'd' && $met_img_slick.hasClass('fngallery')) {
				slick_swipe = false;
				slick_fade = true;
			}
			if (!slick_swipe) $met_img_slick.addClass('slick-fade'); // 如果切换效果为淡入淡出，则加上标记class，其slick-slide鼠标样式为缩放镜
			if (M.device_type != 'm') slick_arrows = true;
			$met_img_slick.slick({
				arrows: slick_arrows,
				dots: true,
				speed: 300,
				fade: slick_fade,
				swipe: slick_swipe,
				customPaging: function(a, b) { // 缩略图html
					var $selfimg = $met_img_slick_slide.eq(b),
						src = $selfimg.find('.lg-item-box').data('exthumbimage'),
						alt = $selfimg.find('img').attr('alt'),
						img_html = '<img src="' + src + '" alt="' + alt + '" />';
					return img_html;
				},
				prevArrow: met_prevarrow,
				nextArrow: met_nextarrow,
				adaptiveHeight: true,
				lazyloadPrevNext: 1
			});
			// 切换图片之前，判断所有图片是否被替换，如被替换，则还原
			$met_img_slick.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
				$met_img_slick_slide.each(function(index, el) {
					var thisimg = $('img', this),
						thisimg_datasrc = thisimg.attr('data-src');
					if (!thisimg.attr('data-lazy') && thisimg.attr('src') != thisimg_datasrc) thisimg.attr({
						src: thisimg_datasrc
					});
				});
			});
		}
		// 画廊加载
		var $fngallery = $('.fngallery');
		if ($fngallery.length) {
			var $fngalleryimg = $fngallery.find('.slick-slide img');
			if ($fngalleryimg.length) {
				var fngallery_open = true;
				$fngalleryimg.each(function() {
					$(this).one('click', function() {
						if (fngallery_open) {
							if (M.device_type == 'm') {
								$fngalleryimg.each(function(index, el) {
									var size='400x400';
									$(this).parents('[data-med][data-size="x"]').attr({'data-size':size,'data-med-size':size});
								});
								$.initPhotoSwipeFromDOM('.fngallery', '.slick-slide:not(.slick-cloned) [data-med]'); //（需调用PhotoSwipe插件）
							} else {
								$fngallery.galleryLoad(); //（需调用lightGallery插件）
							}
							fngallery_open = false;
						}
					});
				})
			}
		}
		$met_img_slick.metShopVideo('.slick-slide');
	});
})();
// 商品展示视频
$.fn.metShopVideo=function(item,dots,dots_events){
	var product_video=$('textarea[name="met_product_video"]').val();
	if(!($(this).length && $(item,this).length && product_video && $(product_video).find('video,iframe,embed').length)) return;
	var $self=$(this);
	if($self.css('position')=='static') $self.css({position:'relative'});
	setTimeout(function() {
		// 显示视频
		var playinfo=$('textarea[name="met_product_video"]').data('playinfo').split('|'),
			autoplay=parseInt(M.device_type=='m'?playinfo[1]:playinfo[0]);
		if(autoplay) product_video=product_video.replace('<video ','<video muted ');
		product_video=product_video.replace('<video ','<video playsinline webkit-playsinline controlsList="nodownload" ');
		$self.append('<div class="met-product-showvideo-wrapper" hidden>'
			+'<div class="met-product-showvideo-btn hide"><a href="javascript:;" class="block d-block pull-xs-left"><i class="fa-play-circle-o"></i></a></div>'
			+'<div class="met-product-showvideo w-full w-100p vertical-align text-xs-center text-center">'
				+'<div class="vertical-align-middle bg-white text-xs-center">'+product_video
			+'<a href="javascript:;" class="video-close text-xs-center text-center">×</a></div></div>'
		+'</div>');
		var $item0=$self.find((item||'img')+':eq(0)'),
			min_width=Math.min(800,$item0.width()),
			min_height=Math.min(500,$item0.height()),
			height=$item0.height()||300,
			$video_wrapper=$self.find('.met-product-showvideo-wrapper'),
			$showvideo=$video_wrapper.find('.met-product-showvideo'),
			$btn_showvideo=$video_wrapper.find('.met-product-showvideo-btn'),
			$video_close=$video_wrapper.find('.video-close'),
			$video=$showvideo.find('video')[0],
			$obj_video=$showvideo.find('video,iframe,embed'),
			scale=$obj_video.attr('height')/$obj_video.attr('width');
		$showvideo.height(height).find('video,iframe,embed').css({'max-height':min_height}).height(height).width('auto');
		$showvideo.find('iframe').width(scale?$obj_video.height()/scale:$obj_video.height());
		setTimeout(function(){
			$video_wrapper.removeAttr('hidden');
			// 播放按钮
			$btn_showvideo.css({
				top: height-(height-$showvideo.find('video,iframe,embed').height())/2-15,left:10+$showvideo.find('.vertical-align-middle').position().left
			}).click(function(event) {
				$(this).addClass('hide');
				$showvideo.show();
				if($video){
					autoplay && $video.currentTime && ($video.muted = false);
					$video.currentTime=0;
					$video.play();
				}
			});
			// 自动播放
			autoplay && $btn_showvideo.click();
			if(M.device_type=='m'){
				if($video && /iPhone/.test(M.useragent)){
					document.addEventListener('touchstart', function(){
						autoplay && !$video.currentTime && $btn_showvideo.click();
					}, false);
				}
				$self.addClass('overflow-visible');
				$video_close.css({top:-30});
			}
			$video && $video.addEventListener('play', function () {
		        $btn_showvideo.addClass('hide');
		    });
			// 关闭视频
			$video_close.click(function(event) {
				$video && $video.pause();
				$showvideo.hide();
				$btn_showvideo.removeClass('hide');
			});
			// 切换展示图片的时候，隐藏视频
			$self.on(dots_events||'click',dots||'.slick-dots li',function(event) {
				$showvideo.is(':visible') && $video_close.click();
			});
		},100)
	}, 900);
};