/*!
 * fileinput组件自动化、图片库
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function() {
	// 弹框参数
    M.load('modal',function(){
		// 图片库-弹框
        M.component.modal_options['.img-library-modal'] = {
            modalTitle: `${METLANG.upload_selectimg_v6 || '选择图片'}<span class="ml-2 h6 mb-0 text-danger">${METLANG.enter_folder}</span>`,
            modalHeaderappend: `<div class="float-right mr-3">
                <button type="button" class="btn btn-warning float-left mr-3 invisible btn-img-folder-back"><i class="fa-reply mr-2"></i>${METLANG.back_folder_list}</button>
                <div class="img-library-sort clearfix float-right">
                    <span class="float-left">${METLANG.sort}：</span>
                    <a href="javascript:;" title="按更新时间倒叙" data-type="time_desc" class="sort-time text-center float-left px-2 active"><i class="fa-clock-o"></i><br><i class="fa-sort-down"></i></a>
                    <a href="javascript:;" title="按更新时间顺叙" data-type="time_asc" class="sort-time text-center text-white float-left px-2"><i class="fa-clock-o"></i><br><i class="fa-sort-up"></i></a>
                    <a href="javascript:;" title="按名称顺序" data-type="name_asc" class="text-white float-left px-2"><i class="fa-sort-alpha-asc"></i></a>
                    <a href="javascript:;" title="按名称倒序" data-type="name_desc" class="text-white float-left px-2"><i class="fa-sort-alpha-desc"></i></a>
                </div>
            </div>`,
            modalSize: 'xl',
            modalFullheight: 1,
			modalHeight100:1,
            modalBody: `<ul class="img-library-breadcrumb list-unstyled"></ul><ul class="img-library-list list-unstyled mt-2 mb-0 row mx-0"></ul><div class="d-flex align-items-center justify-content-center hide nodata" style="height:calc(100% - 38px);"><div class="h5 mb-0">${METLANG.nopicture || '暂无图片'}</div></div>${M.component.loader({
                class_name: 'img-library-loader h-100',
                wrapper_class: 'd-flex align-items-center justify-content-center h-100'
            })}`,
            modalBodyClass: 'img-library-body'
        };
		// 外部图片-弹框
        M.component.modal_options['.img-other-modal'] = {
            modalTitle: METLANG.upload_addoutimg_v6 || '添加外部图片',
            modalType: 'centered',
            modalBody: `<div class="form-group mb-0"><input type="text" name="img_url" placeholder="${METLANG.upload_extraimglink_v6 || '请输入外部图片地址'}" class="form-control"/></div>`
        };
		// 合规素材
		M.component.modal_options['.compliance-materials-modal'] = {
            modalTitle: METLANG.compliance_materials || '合规素材',
            modalBody: `<div class="text-warning py-5 h4 mb-0 text-center">功能正在开发，敬请期待...</div>`,
			modalType: 'centered',
			modalFooterok:0,
			modalStyle:'z-index:1704'
        };
    });
	M.fileinput_lang = typeof M.synchronous != 'undefined' ? (M.synchronous == 'cn' ? 'zh' : 'en') : 'zh';
	$.fn.extend({
		// 上传文件
		metFileInput: function() {
			if (!$(this).length) return;
			var messageFun = function(obj, type, msg) {
					var $form_group = obj.parents('.form-group'),
						class1 = type == 'error' ? 'success' : 'danger',
						class2 = type == 'error' ? 'danger' : 'success';
					$form_group.removeClass('has-'+class1).addClass('has-'+class2);
					if (!$form_group.find('span.form-control-label').length) $form_group.append('<span class="form-control-label font-size-14 ml-2"></span>');
					$form_group.find('span.form-control-label').html(msg);
				},
				errorFun = function(obj, data) {
					var $file_input = obj.parents('.file-input'),
						order = typeof data.response.order != 'undefined' ? data.response.order : data.index;
					$file_input.find(`.file-preview-thumbnails .file-preview-frame .file-preview-view[data-order="${order}"]:not([data-size])`).parents('.file-preview-frame').remove();
					// 显示报错文字
					messageFun($file_input, 'error', data.response.error);
				},
				successFun = function(obj, data, multiple) {
					var $file_input = obj.parents('.file-input'),
                        $text = $file_input.parents('form').find(`.fileinput-input-text[name="${obj.attr('name')}"]`),
                        delimiter = obj.data('delimiter') || ',',
						path = '';
					if (multiple) {
						fileinput_upindex++;
						fileinput_upsrc[data.response.order] = data.response.original;
						if (fileinput_upindex == fileinput_uplength) {
							fileinput_upsrc = fileinput_upsrc.join(delimiter);
							path = $text.val() ? $text.val()+delimiter+fileinput_upsrc : fileinput_upsrc;
							$text.val(path).change(); // input值更新
						}
					} else {
						path = data.response.original;
						$file_input.find('.file-preview-thumbnails .file-preview-frame:last-child').prev().remove();
						$text.val(path).change(); // input值更新
					}
					// 显示上传成功文字
					$file_input.find('.input-group .file-caption-name').html('<span class="fa-file kv-caption-icon"></span>'+path).attr({
						title: path
					});
					messageFun($file_input, 'success', METLANG.jsx17);
					// 更新上传文件预览地址
					var $preview_view = $file_input.find(`.file-preview-thumbnails .file-preview-frame .file-preview-view[data-order="${data.response.order}"]:not([data-size])`),
						$frame=$preview_view.parents('.file-preview-frame');
					$preview_view.attr({
						href: data.response.original,
						'data-size': data.response.filesize
					});
					$frame.removeClass('hide').find('>a').attr('href',data.response.original);
					$frame.find('video').after(`<a href="${data.response.original}" target="_blank" class="d-flex align-items-center justify-content-center">
						<video width="200" controls>
							<source src="${data.response.original}" type="${$frame.find('video source').attr('type')}">
						</video>
					</a>`).remove();
					setTimeout(function() {
						$frame.addClass('file-preview-initial');
					}, 100);
				},
				fileinput_uplength,fileinput_upindex,fileinput_upsrc;
			$(this).each(function(index, el) {
				// if (!(typeof MET.url.admin != 'undefined' || (typeof $(this).data('url') != 'undefined' && $(this).data('url').indexOf('&c=uploadify&m=include&a=dohead') >= 0))) return;
				var $self = $(this),
					$form_group = $(this).parents('.form-group:eq(0)'),
					name = $(this).attr('name'),
					multiple = typeof $(this).attr('multiple') != 'undefined' ? true : false,
					delimiter = $(this).data('delimiter') || ',',
					minFileCount = $(this).data('fileinput-minfilecount') || 1,
					maxFileCount = $(this).data('fileinput-maxfilecount') || 20,
					maxFileSize = $(this).data('fileinput-maxfilesize') || 0,
					accept = $.trim($(this).attr('accept') || ''),
					url = $(this).data('url') || `${M.url.system}entrance.php?lang=${M.lang}&c=uploadify&m=include&a=doup${accept == '*' ? 'file' : 'img'}&type=1`,
					format = '',
					initialPreview = [],
					dropZoneEnabled = $(this).data('drop-zone-enabled') == 'false' ? false : true,
					value = $(this).attr('value');
				if (typeof value != 'undefined' && value != '') {
					var values = value.indexOf(delimiter) >= 0 ? value.split(delimiter) : [value];
					$.each(values, function(index, val) {
						initialPreview.push(fileThumbnail(val));
					});
				}
				if (accept) {
					if (accept.indexOf(',') >= 0) {
						accept = accept.split(',');
					} else {
						accept = [accept];
					}
					$.each(accept, function(index, val) {
						val = val.indexOf('/') >= 0 ? val.split('/')[1] : '';
						if (val.indexOf('x-') >= 0) val = val.replace('x-', '');
						switch (val) {
							case 'icon':
								val = 'ico';
								break;
							case 'jpeg':
								val = 'jpg';
								break;
							case '*':
								val = '';
								break;
						}
						if (val) {
							if (format) format+='|';
							format+=val;
						}
					});
					if (accept == 'image/*') format = 'jpg|jpeg|png|bmp|gif|webp|ico|svg';
				}
				if($(this).data('format')) format=$(this).data('format');
				if (format) url+='&format='+format;
				var allowedFileExtensions = format ? (format.indexOf('|') ? format.split('|') : [format]) : '';
                // 插入name
				var required = $(this).attr('data-filerequired') ? `required data-fv-notEmpty-message="${$(this).attr('data-notEmpty-message') || METLANG.js15}"` : '';
				if (!$(this).parents('form').find(`.fileinput-input-text[name="${name}"]`).length){
                    if($(this).attr('data-prev-input')){
                        $(this).parent().before(`<input type="text" name="${name}" value="${value}" ${required} class='form-control mr-1 fileinput-input-text'>`);
                    }else{
                        setTimeout(()=>{
                            $(this).after(`<input type="text" name="${name}" value="${value}" ${required} class='fileinput-input-text'>`);
                        },0);
                    }
                }
				$(this).removeAttr('hidden').fileinput({ // fileinput插件
					uploadUrl: url, // 处理上传
					uploadAsync: multiple, // 异步批量上传
					allowedFileExtensions: allowedFileExtensions, // 接收的文件后缀,
					minFileCount: minFileCount,
					maxFileCount: maxFileCount,
					maxFileSize: maxFileSize,
					language: M.fileinput_lang, // 语言文字
					initialPreview: initialPreview,
					initialCaption: value, // 初始化输入框值
					// showCaption:false,         // 输入框
					// showRemove:false,          // 删除按钮
					// browseLabel:'',            // 按钮文字
					showUpload: false, // 上传按钮
					dropZoneEnabled: dropZoneEnabled, // 是否显示拖拽区域
					// browseClass:"btn btn-primary", //按钮样式
					nocompliance:!parseInt(M.met_agents_metmsg)
				}).on("filebatchselected", function(event, files) {
					fileinput_uplength = files.length;
					fileinput_upindex = 0;
					fileinput_upsrc = [];
					var $self = $(this),
						$is_compress = $(this).parents('.file-input').find('.file-preview-frame:not(.file-preview-initial) img').filter(function(index) {
							var result = this.naturalWidth > 2600 || this.naturalHeight > 2000;
							return result;
						}),
						compress_length = $is_compress.length;
					if (compress_length) {
						var i = 0;
						$is_compress.each(function(index, el) {
							// 缩放图片需要的canvas
							var canvas = document.createElement('canvas'),
								context = canvas.getContext('2d'),
								size = imgScale(2600, 2000, this.naturalWidth, this.naturalHeight),
								order = parseInt($(this).parents('.file-preview-frame').find('.file-preview-view').attr('data-order'));
							canvas.width = size.width;
							canvas.height = size.height;
							context.clearRect(0, 0, size.width, size.height);
							context.drawImage(this, 0, 0, size.width, size.height);
							canvas.toBlob(function(blob) {
								i++;
								files[order] = blob;
								i == compress_length && $self.fileinput('upload', files);
							}, files[order].type || 'image/jpeg');
						});
					} else {
						$(this).fileinput('upload');
					}
				}).on('filebatchuploadsuccess', function(event, data, previewId, index) { // 同步上传成功结果处理
					successFun($(this), data, multiple);
				}).on('fileuploaded', function(event, data, previewId, index) { // 异步上传成功结果处理
					successFun($(this), data, multiple);
				}).on('filebatchuploaderror', function(event, data, previewId, index) { // 同步上传错误结果处理
					errorFun($(this), data);
				}).on('fileuploaderror', function(event, data, previewId, index) { // 异步上传错误结果处理
					errorFun($(this), data);
				});
				// 验证初始化
				required && M.load('form', function() {
					setTimeout(()=>{
						$form_group.metFormAddField();
					},0);
				});
				// 多图拖曳改变位置
				$(this).attr('multiple') && M.load('dragsort', function() {
					var $thumbnails = $self.parents('.file-input').find('.file-preview-thumbnails');
					!$thumbnails.find('.file-preview-frame').length && $thumbnails.html('<div hidden></div>');
					$thumbnails.metDragsort();
					setTimeout(function() {
						dragsortFun[$thumbnails.attr('data-dragsort_order')] = function(wrapper, item) {
							var img_url = '';
							$thumbnails.find('.file-preview-frame .file-preview-view').each(function(index, el) {
								img_url+=index ? (delimiter+$(this).attr('href')) : $(this).attr('href');
							});
							$self.parents('form').find(`.fileinput-input-text[name="${name}"]`).val(img_url).change();
						}
					}, 100)
				});
				// 尺寸信息初始化
				$(this).data('size') && $(this).imgSize();
				// 上传按钮组样式优化
				setTimeout(function() {
					$self.parents('.file-input').find('.input-group .input-group-append .btn:visible').addClass('element-visible');
				}, 100);
			});
		},
		// 上传图片组件改变值
		metFileInputChange: function(img_url) {
			var $file_input = $(this).parents('.file-input'),
				delimiter = $(this).data('delimiter') || ',',
				name = $(this).attr('name'),
				html = '',
				img_urls = img_url.indexOf(delimiter) >= 0 ? img_url.split(delimiter) : [img_url],
                $text=$file_input.parents('form').find(`.fileinput-input-text[name="${name}"]`);
			if ($(this).attr('multiple')) {
				var old_val = $text.val();
				if (old_val) {
					img_url = old_val+delimiter+img_url;
					old_val = old_val.indexOf(delimiter) >= 0 ? old_val.split(delimiter) : [old_val];
				} else {
					old_val = [];
				}
				img_urls = old_val.concat(img_urls);
			}
			$.each(img_urls, function(index, val) {
				html+=`<div class="file-preview-frame file-preview-initial">${fileThumbnail(val)}</div>`;
			});
			if (html && $file_input.hasClass('file-input-new')) $file_input.removeClass('file-input-new');
			$file_input.find('.file-drop-zone .file-drop-zone-title').remove();
			$file_input.find('.file-preview-thumbnails').html(html);
			$file_input.find('.input-group .file-caption-name').html(`<span class="fa-file kv-caption-icon"></span>${img_url}`).attr({
				title: img_url
			});
			$text.val(img_url).change();
		},
		// 更新图片尺寸
		imgSize: function() {
			var $self = $(this),
				$thumbnails = $(this).parents('.file-input').find('.file-preview-thumbnails'),
				file_length = $thumbnails.find('.file-preview-frame .file-preview-view').length,
				load_i = 0,
				size = [];
			$thumbnails.find('.file-preview-frame img').each(function(index, el) {
				var sizeHandle = function(info) {
					load_i++;
					size[index] = info.naturalWidth+'x'+info.naturalHeight;
					if (load_i == file_length) {
						size = size.join('|');
						var $imgsize = $self.parents('form').find('input[type="hidden"][name="imgsizes"]');
						$imgsize.length ? $imgsize.val(size) : $self.after(`<input type="hidden" name="imgsizes" value="${size}">`);
					}
				};
				if (this.src.indexOf('blob') == 0 || !this.naturalWidth) {
					this.naturalWidth && (this.src = $(this).parents('.file-preview-frame').find('.file-preview-view').attr('href'));
					this.onload = function() {
						sizeHandle(this);
					}
				} else sizeHandle(this);
			});
		}
	});
	// 预览图
	function fileThumbnail(imgurl) {
		var imgurl_handle=imgurl.toLowerCase(),
			suffix_list={
				image:['png','jpeg','jpg','bmp','gif','ico','svg'],
				video:['mp4','webm','ogg']
			},
			file_html = (()=>{
				var html='',
					type='',
					suffix='';
				$.each(suffix_list, function(index, val) {
					$.each(val, function(index1, val1) {
						if(imgurl_handle.indexOf('.'+val1) >= 0){
							type=index;
							suffix=val1;
							return false;
						}
					});
					if(type) return false;
				});
				switch(type){
					case 'image':
						html=`<img src="${imgurl}" class="file-preview-image vertical-align-middle">`;
						break;
					case 'video':
						html=`<video width="200" controls="">
							<source src="${imgurl}" type="video/${suffix}">
						</video>`;
						break;
					default:
						html='<div class="file-preview-other text-grey" style="width:68px;"><i class="fa-file"></i></div>';
						break;
				}
				return html;
			})(),
			html = `<a href="${imgurl}" target="_blank" class="d-flex align-items-center justify-content-center">${file_html}</a>
            <div class="file-thumbnail-footer">
                <div class="file-caption-name" title="${imgurl}">${imgurl}</div>
                <div class="file-actions">
                    <a href="${imgurl}" class="btn btn-xs btn-default file-preview-view" title="${METLANG.clickview}" target="_blank"><i class="fa-eye text-grey"></i></a>
                    <div class="file-footer-buttons">
                        <button type="button" class="kv-file-remove btn btn-xs btn-default" title="${$.fn.fileinputLocales[M.fileinput_lang].removeTitle}"><i class="fa-trash-o text-grey"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>`;
		return html;
	}
	// 上传图片尺寸缩放
	function imgScale(max_width, max_height, width, height) {
		if (width > max_width || height > max_height) {
			var scale = height / width;
			if (width >= height) {
				width = max_width;
				height = Math.ceil(max_width * scale);
			} else {
				height = max_height;
				width = Math.ceil(max_height / scale);
			};
		}
		return {
			width: width,
			height: height
		};
	}
	// 删除图片后图片路径数据更新
	$(document).on('click', '.file-input .file-preview-thumbnails .file-preview-frame .kv-file-remove,.fileinput-remove', function(event) {
		event.preventDefault();
		var $file_input = $(this).parents('.file-input'),
			$file = $file_input.find('input[type="file"]'),
			delimiter = $file.data('delimiter') || ',',
			name = $file.attr('name'),
			multiple = typeof $file.attr('multiple') != 'undefined' ? true : false,
			$input_name = $file_input.parents('form').find(`.fileinput-input-text[name="${name}"]`),
			input_value = $input_name.val(),
			$caption_name = $file_input.find('.input-group .file-caption-name');
		if (input_value) {
			if ($(this).hasClass('kv-file-remove')) {
				var $parents = $(this).parents('.file-preview-frame'),
					active = $parents.index(),
					path = '';
				setTimeout(function() {
					var input_value = $input_name.val();
					if ($parents.length) $parents.remove();
					if (multiple) {
						if (input_value) {
							if (input_value.indexOf(delimiter) >= 0) {
								input_value = input_value.split(delimiter);
							} else {
								input_value = [input_value];
							}
							$.each(input_value, function(index, val) {
								if (index != active) path = path ? path+delimiter+val : val;
							});
						}
					} else {
						var $file_preview_frame = $file_input.find('.file-preview-thumbnails .file-preview-frame');
						path = $file_preview_frame.length ? $file_preview_frame.find('img').attr('src') : '';
					}
					if (path) {
						if ($input_name.val()) { // input值更新
							$input_name.val(path).change();
						}
					} else if (multiple && !$file_input.find('.file-drop-zone-title').length) {
						$file_input.find('.fileinput-remove').click();
					}
					$caption_name.html('<span class="fa-file kv-caption-icon"></span>'+path).attr({
						title: path
					});
				}, 1000)
				if (!multiple) $file_input.find('.fileinput-remove').click();
			} else {
				$input_name.val('').change(); // input值更新
				$caption_name.html('<span class="fa-file kv-caption-icon"></span>').removeAttr('title');
			}
		}
		$(this).parents('.form-group').removeClass('has-success has-danger').find('span.form-control-label').html('');
	});
	// 文件地址更新后，图片尺寸更新
	$(document).on('change', 'form .fileinput-input-text[name]', function(event) {
		var $file = $(this).parent().find('input[type="file"]');
		setTimeout(()=>{
			$file.data('size') && $file.imgSize();
			// 自定义回调
			var callback = $file.data('callback');
			callback && eval(callback+'($file,$(this).val())');
		}, 0);
	});
	var filelist_url = M.url.admin+'index.php?n=system&c=filept&a=doGetFileList';
	// 图片库-点击按钮
	$(document).on('click', '.file-input .fileinput-file-choose', function(event) {
		var id = $(this).parents('.file-input').find('input[type="file"]').attr('id'),
			$sort=$('.img-library-sort');
		if(!$sort.attr('data-init')){
			$sort.attr('data-init',1);
			var sort=getCookie('upload-onlineimages-sort');
			sort && $sort.find(`a[data-type="${sort}"]`).trigger('clicks');
		}
		setTimeout(function() {
			$('.img-library-modal button[data-ok]').attr({'data-id': id});
		}, 0)
	});
	// 渲染图片库列表
	function imgLibraryList(obj){
		var $img_library_modal = $('.img-library-modal'),
			$loader = $img_library_modal.find('.img-library-loader'),
			$img_library_list = $img_library_modal.find('.img-library-list'),
			dir=obj?obj.attr('data-path')||'':'';
		$img_library_modal.find('.modal-body>*').addClass('hide');
		$img_library_list.html('');
		$loader.removeClass('hide');
		$img_library_modal.find('.btn-img-folder-back').removeClass('invisible');
		M.ajax({
			url: filelist_url,
			data: {
				dir: dir,
				sort:$('.img-library-sort a.active').data('type')
			},
			success: function(result) {
				if (result) {
					var html = '';
					$.each(result, function(index, val) {
						if(val.type=='dir'){
							html+=`<li class="text-center mb-2 col-6 col-md-4 col-lg-3 col-xl-2">
									<a href="javascript:;" title="${val.name}" data-path="${val.path}" class="d-flex align-items-center justify-content-center text-content dir">
										<div>
											<i class="fa-folder-open-o h1"></i>
											<h4 class="h6 mb-0">${val.name}</h4>
										</div>
									</a>
								</li>`;
						}else{
							html+=`<li class="text-center mb-2 col-6 col-md-4 col-lg-3 col-xl-2 px-1">
								<div title="${val.name}" class="d-flex align-items-center justify-content-center p-1 border position-relative img">
									<img ${index > 5 ? 'data-original' : 'src'}="${val.value}" class="vertical-align-middle img-fluid"/>
									<i class="fa-check text-white position-absolute hide check"></i>
									<a href="${M.url.admin+val.value}" title="${METLANG.View+METLANG.image}" target="_blank" class="view position-absolute p-1 h5 mb-0 rounded-circle"><i class="fa-search-plus text-content"></i></a>
								</div>
							</li>`;
						}
					});
					$loader.addClass('hide');
					if (html){
						$img_library_list.html(html).removeClass('hide').find('[data-original]').metLazyLoad({
							container: '.img-library-body'
						});
					}else{
						$img_library_modal.find('.nodata').removeClass('hide');
					}
					// 图片路径面包屑
					if(dir){
						var breadcrumb=[],
						breadcrumb_path='';
						$.each(dir.split('/'), function(index, val) {
							if(val){
								breadcrumb.push(`<a href="javascript:;" ${index==1?'':`data-path="${breadcrumb_path}/${val}"`}>${val}</a>`);
								breadcrumb_path+='/'+val;
							}
						});
						breadcrumb=`/ ${breadcrumb.join(' / ')} /`;
						$('.img-library-breadcrumb').removeClass('hide').html(breadcrumb);
					}
				}
			}
		});
	}
	// 图片库-弹框
	$(document).on('show.bs.modal', '.img-library-modal', function(event) {
		setTimeout(function(){
			imgLibraryList();
		},0);
	});
	// 图片库-选择文件夹
	$(document).on('dblclick', '.img-library-list li a.dir,.img-library-modal .btn-img-folder-back,.img-library-breadcrumb a', function(event) {
		imgLibraryList($(this));
	});
	// 图片库-返回文件夹列表
	$(document).on('click', '.img-library-modal .btn-img-folder-back,.img-library-breadcrumb a', function(event) {
		var $breadcrumb_a=$('.img-library-breadcrumb a'),
			breadcrumb_a_num=$breadcrumb_a.length;
		if($(this).parents('.img-library-breadcrumb').length){
			$(this).attr('data-path')?$(this).dblclick():$('.img-library-modal').trigger('show.bs.modal');
		}else{
			breadcrumb_a_num>2?$breadcrumb_a.eq(breadcrumb_a_num-2).dblclick():$('.img-library-modal').trigger('show.bs.modal');
		}
	});
	// 图片库-选择图片
	$(document).on('click', '.img-library-list li .img', function(event) {
		var multiple = $('.file-input #'+$('.img-library-modal button[data-ok]').attr('data-id')).attr('multiple') ? true : false;
		$(this).toggleClass('active border-primary').find('.check').toggleClass('hide');
		if (!multiple) $(this).parents('li').siblings('li').find('.img').removeClass('active border-primary').find('.check').addClass('hide');
	});
	// 图片库-提交
	$(document).on('click', '.img-library-modal button[data-ok]', function(event) {
		var $self = $(this),
			$img_library_modal = $('.img-library-modal'),
			$file = $('.file-input #'+$self.attr('data-id')),
			delimiter = $file.data('delimiter') || ',',
			img_url = '';
		$img_library_modal.find('.img-library-list li .img.active img').each(function(index, el) {
			img_url+=(index ? delimiter : '')+$(this).attr('src');
		});
		M.load('alertify', function() {
			if (img_url) {
				$file.metFileInputChange(img_url);
				$img_library_modal.modal('hide');
				alertify.success(METLANG.jsok || '操作成功');
				$file.parents('.form-group').removeClass('has-success has-danger').find('span.form-control-label').html('');
				$file.parents('.file-input').find('.kv-upload-progress').addClass('hide');
			} else {
				alertify.error(METLANG.upload_pselectimg_v6 || '请选择图片');
			}
		});
	});
	// 图片库-图片列表-点击排序
	$(document).on('click clicks', '.img-library-sort a', function(event) {
		$(this).addClass('active').removeClass('text-white').siblings('a').removeClass('active').addClass('text-white');
		if(event.type=='click'){
			$('.img-library-breadcrumb').is(':visible')?$('.img-library-breadcrumb a:last-child').click():$('.img-library-modal').trigger('show.bs.modal');
			setCookie('upload-onlineimages-sort',$(this).data('type'),'','365');
		}
	});
	// 外部图片-弹框
	$(document).on('click', '.file-input .fileinput-file-other', function(event) {
		var id = $(this).parents('.file-input').find('input[type="file"]').attr('id');
		setTimeout(function() {
			$('.img-other-modal [name="img_url"]').val('');
			$('.img-other-modal button[data-ok]').attr({
				'data-id': id
			});
		});
	});
	// 外部图片-提交
	$(document).on('click', '.img-other-modal button[data-ok]', function(event) {
		var $self = $(this),
			$img_other_modal = $('.img-other-modal'),
			img_url = $img_other_modal.find('[name="img_url"]').val(),
			$file=$('.file-input #'+$self.attr('data-id'));
		M.load('alertify', function() {
			if (img_url) {
				$file.metFileInputChange(img_url);
				$img_other_modal.modal('hide');
				alertify.success(METLANG.jsok || '操作成功');
				$file.parents('.form-group').removeClass('has-success has-danger').find('span.form-control-label').html('');
				$file.parents('.file-input').find('.kv-upload-progress').addClass('hide');
			} else {
				alertify.error(METLANG.upload_extraimglink_v6 || '请输入外部图片地址');
				$img_other_modal.find('[name="img_url"]').focus();
			}
		});
	});
})();