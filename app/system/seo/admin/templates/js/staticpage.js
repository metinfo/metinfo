/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
(function () {
	var that = $.extend(true, {}, admin_module);
	getStaticPageSet();
	FormSubmit();
	TEMPLOADFUNS[that.hash] = function () {
		getStaticPageSet();
	};

	function getStaticPageSet() {
		$.ajax({
			url: M.url.admin + "?n=html&c=html&a=doGetSetup",
			type: "GET",
			dataType: "json",
			success: function (result) {
				let data = (that.data = result.data);
				Object.keys(data).map(item => {
					if (item === "met_webhtm") {
						$('[name="met_webhtm"]').removeAttr("checked");
						$(`#met_webhtm-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						met_webhtm_change(data[item],1);
						return;
					}
					if (item === "met_html_auto") {
						$('[name="met_html_auto"]').removeAttr("checked");
						$(`#met_html_auto-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						return;
					}
					if (item === "met_htmlistname") {
						$('[name="met_htmlistname"]').removeAttr("checked");
						$(`#met_htmlistname-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						return;
					}

					if (item === "met_htmpagename") {
						$('[name="met_htmpagename"]').removeAttr("checked");
						$(`#met_htmpagename-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						return;
					}
					if (item === "met_htmtype") {
						$('[name="met_htmtype"]').removeAttr("checked");
						$(`#met_htmtype-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						return;
					}
					if (item === "met_htmway") {
						$('[name="met_htmway"]').removeAttr("checked");
						$(`#met_htmway-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						return;
					}
					if (item === "met_listhtmltype") {
						$('[name="met_listhtmltype"]').removeAttr("checked");
						$(`#met_listhtmltype-${data[item]}`)
							.attr("checked", true)
							.prop({
								checked: true
							});
						return;
					}
				});
			}
		});
	}

	function FormSubmit() {
		M.load(["form", "formvalidation", "alertify"], function () {
			const form = that.obj.find(".static-form");
			const order = form.attr("data-validate_order");
			formSaveCallback(order, {
				true_fun: function (result) {
                    if(result.data.callback_url) {//开启html自动更新
                        const callback_url = result.data.callback_url;
                        M.ajax({
                            url: callback_url
                        })
                    }
					const met_webhtm = form.find('[name="met_webhtm"]:checked').val();
					let value = {};
					form.serializeArray().map(item => {
						if (item.name !== "submit_type") value[item.name] = item.value;
					});
					const res = compare(value, that.data);
					const isHtmlway = res.length == 1 && res[0] === "met_htmway";
					if (met_webhtm !== "0") {
						!isHtmlway &&
							alertify
							.okBtn(METLANG.confirm)
							.cancelBtn(METLANG.cancel)
							.confirm(METLANG.seotips12, function (e) {
								that.obj.find(".met_webhtm .btn").click();
								setTimeout(() => {
									$(".html-modal .html-link:first").click();
								}, 800);
							});
						that.obj.find(".met_webhtm").removeClass("hide");
					} else {
						that.obj.find(".met_webhtm").addClass("hide");
						if (that.data.met_webhtm === "0") {
							return;
						}
						alertify
							.okBtn(METLANG.confirm)
							.cancelBtn(METLANG.cancel)
							.confirm(METLANG.seotips11, function (e) {
								$.ajax({
									url: M.url.admin + "?n=html&c=html&a=doDelHtml",
									type: "GET",
									dataType: "json"
								});
							});
					}
					getStaticPageSet();
				}
			});
		});
	}

	function getHtml(modal) {
		M.ajax({
				url: M.url.admin + "?n=html&c=html&a=doGetHtml"
			},
			function (result) {
				let data = result.data;
				let html = "";
				data.map((item, index) => {
					html +=`<dl>
						<dt>
							<label class="form-control-label">${item.name}</label>
						</dt>
						<dd>
							${
							item.content
								? `<a data-url="${item.content.url}" tabindex=${index} class="html-link" data-name="${item.name}">${item.content.name}</a>`
								: ""
							}
							${
							item.column
								? `<a data-url="${item.column.url}" tabindex=${index} class="html-link" data-name="${item.name}">${item.column.name}</a>`
								: ""
							}
						</dd>
					</dl>`;
				});
				that.modalHtml = html;
				modal.find(".met-html").append(that.modalHtml);
				createHtml(modal);
			}
		);
	}
	M.component.modal_options[".html-modal"] = {
	    modalFullheight:1,
		callback: function () {
			const modal = $(".html-modal");
			getHtml(modal);
		}
	};

	function createHtml(modal) {
		modal.find(".html-link").click(function () {
		    var name = $(this).text(),
				title = $(this).data("name"),
				$html_loading = modal.find(".html-loading"),
				html_loading_h = $html_loading.outerHeight(),
				url = $(this).data("url"),
				handle=(other_url)=>{
					$html_loading.html(
        				`<div class="html-list"></div><p style="font-size:16px;" class="createing mb-0">${name}${METLANG.ing}...</p>`
        			);
					M.ajax({
							url: other_url||url
						},
						function (result) {
							var $html_list = $html_loading.find('.html-list'),
								loop_load=()=>{
									setTimeout(()=>{
										M.ajax({
											url:result.data.check_url,
											success:(res)=>{
												if(res.status && (res.suc_num+res.err_num)>=$html_list.find('p').length){
													let html = res.suc.map((item,index)=>{
															return `<p style="color:green">(${index+1}/${result.data.total}) <a href="${M.weburl}${item.filename}" target="_blank">${item.filename}</a> ${METLANG.physicalgenok}</p>`;
														}).join('')+res.err.map((item,index)=>{
															return `<p style="color:red">(${res.suc_num+index+1}/${result.data.total}) ${item.filename} ${METLANG.html_createfail_v6}</p>`;
														}).join('');
													$html_list.html(html);
													if(res.status==1){
														$html_loading.find(".createing").html(`${title}${METLANG.static_page_success}
														<br><span class='text-success'>${METLANG.physicalgenok}${res.suc_num}${METLANG.page}</span>
														<br><span class='text-danger'>${METLANG.html_createfail_v6}${res.err_num}${METLANG.page}${res.err_num?` <button type="button" class='btn btn-primary html-link-reset-fail'>重新生成</button>`:''}</span>`);
														res.err_num && $html_loading.find('.html-link-reset-fail').click(function () {
															handle(result.data.retry_url);
														});
													}else{
														loop_load();
													}
													var scrolltop = $html_list.outerHeight() - html_loading_h+120;
													scrolltop && $html_loading.scrollTop(scrolltop);
												}
											}
										});
									},1000);
								}
							M.ajax({
								url:result.data.callback_url
							});
							loop_load();
						}
					);
				};
			handle();
		});
	}

	function compare(obj1, obj2) {
		let arr = [];
		for (let key in obj1) {
			if (obj2[key] !== obj1[key]) {
				arr.push(key);
			}
		}
		return arr;
	}
	// 混合模式查看伪静态规则
	function met_webhtm_change(value,met_webhtm){
		met_webhtm && that.obj.find(".met_webhtm")[`${value>0?'remove':'add'}Class`]('hide');
		that.obj.find('[data-toggle="modal"][data-target=".staticpage-rules-modal"]')[`${value==3?'remove':'add'}Class`]('hide');
		if(value==3){
			that.obj.find('input[type="radio"][name="met_htmpagename"][value="3"]').attr('data-old_value',that.obj.find('input[type="radio"][name="met_htmpagename"]:checked').val()).click();
		}else{
			that.obj.find(`input[type="radio"][name="met_htmpagename"][value="${that.obj.find('input[type="radio"][name="met_htmpagename"][value="3"]').attr('data-old_value')}"]`).click();
		}
	}
	that.obj.find('input[type="radio"][name="met_webhtm"]').change(function(){
		met_webhtm_change($(this).val());
	});
	M.component.modal_options['.staticpage-rules-modal']={
		modalRefresh:'one',
		modalFullheight:1,
		modalSize:'lg',
		modalTitle:METLANG.pseudo_static,
		modalFooterok:0,
		callback:(key)=>{
		  if(!$(key+' .modal-body pre').length) $.ajax({
			url: that.own_name + 'c=pseudo_static&a=doSavePseudoStatic&pseudo_download=1',
			type: 'POST',
			dataType: 'json',
			data: {
			  pseudo_download: 1
			},
			success: function(result) {
			  let data = result.data
			  $(key+' .modal-body').html(`<pre class='mb-0'>${data}</pre>`);
			}
		  });
		}
	  }
})();