/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
(function () {
	var that = $.extend(true, {}, admin_module),
		$list=that.obj.find('.met-myapp-list');
	TEMPLOADFUNS[that.hash] = function () {
		init();
		getUserInfo();
	};
	var $app_right = that.obj.find('.met-myapp-right'),
		$headtab = that.obj.parents('.tab-content').prev('.met-headtab');
	if ($app_right.length) {
		if($headtab.find('.met-myapp-right').length){
			$app_right.remove();
		}else{
			$headtab.append($app_right);
		}
	}
	function init() {
		that.obj.find(".tab-pane").removeClass('p-4');
		$list.html(M.component.loader({
			class_name: 'w-100',
			height:'300px',
			wrapper_class: 'd-flex align-items-center justify-content-center h-100'
		}));
		M.ajax({
			url: that.own_name + "&c=index&a=doAppList&type=price",
			success: function (result) {
				let data = (that.data = result.data);
				renderList(data);
			},
		});
		that.obj.find('.search [name="keyword"]').val('');
	}

	function renderList(data) {
		let html = '';
		data.length > 0 ?
		data.map(item => {
			var card = `<div class="col col-6 col-xl-4 col-xxl-3 mb-3 px-2" >
					<div class="media bg-white h-100  flex-column transition500" data-no="${item.product_code}" data-m_name="${item.m_name}">
						<div class="body media-body flex-column w-100">
							<a href="${item.show_url}" class="link w-100 d-flex"  title="${METLANG.fliptext1}" target="_blank">
								<img class="mr-3" width="70" height="70" src="${item.product_image}">
								<div class="media-body">
									<h5 class="h6 my-1">${item.product_name}</h5>
									<div class="text-danger h5 mb-0"><span class="h5">￥</span>${item.product_price}</div>
									<div class="card-text">运行环境：PHP ${item.php_version} 及以上版本</div>
								</div>
							</a>
							<div class="card-text text-truncate mt-2">${item.product_desc}</div>
						</div>
						<ul class="actions w-100 d-flex ${item.enabled?'':'bg-grey'}">
							<li class="${item.enabled?'btn-install':'text-help'}">${item.enabled?`<a href="javascript:;" class='d-block'><i class="fa fa-cloud-download"></i>`:''}<span class="${item.enabled?`ml-2`:'font-size-12'}">${item.btn_text}</span>${item.enabled?'</a>':''}</li>
						</ul>
					</div>
				</div>`;
			html = html + card;
		}) :
		(html = `<div class="text-center w-100">${METLANG.no_data}</div>`);
		$list.html(html);
	}

	function installApp() {
		$list.on('click','.btn-install',function () {
			if ($(this).attr('disabled')) return;
			var btn = $(this),
				beforeHTML = btn.html();
			btn.html(`<i class="fa fa-circle-o-notch fa-spin"></i> ${METLANG.updateinstallnow}`).attr('disabled', true);
			M.ajax({
				url: that.own_name + "&c=index&a=doAction",
				data: {
					m_name: btn.parents(".media").data("m_name"),
					no: btn.parents(".media").data("no"),
					handle: "install",
				},
				success: function (result) {
					var msg = '',
						is_mituo_login = that.obj.find(".met-myapp-right .user-name").length;
					if (!result.status && result.msg && is_mituo_login) {
						msg = result.msg;
						result.msg = '';
					}
					metAjaxFun({
						result: result,
						true_fun: function () {
							if (M.is_admin) {
								window.location.href = M.url.admin + "#/myapp/?head_tab_active=0";
								return;
							}
							$('.pageset-nav-modal .nav-modal-item[data-path="myapp"] .met-headtab a[data-url="myapp/myapp"]').click();
						},
						false_fun: function () {
							btn.html(beforeHTML).attr("disabled", false);
							if (is_mituo_login) {
								var btn_id = "dismiss-" + new Date().getTime(),
									url = btn.parents(".media").find(".link").attr("href");
								$("body").append(`
								<div class="modal fade show met-modal alert p-0" data-keyboard="false" data-backdrop="false" style="display: block;">
									<div class="modal-dialog modal-dialog-centered">
										<div class="modal-content">
											<div class="modal-body text-center h6 mb-0"><p class="text-danger">${msg}</p>是否前往购买页面？</div>
											<div class="modal-footer justify-content-center">
												<button type="button" data-dismiss="alert" id="${btn_id}" class="btn btn-default mr-5">${METLANG.cancel}</button>
												<a href="${url}" target="_blank" onClick="$('#${btn_id}').click();" class="btn btn-primary">${METLANG.sys_purchase}</a>
											</div>
										</div>
									</div>
								</div>
								`);
							}
						},
					});
				},
			});
		});
	}

	function search() {
		var btn_search = that.obj.find(".search .input-group-text"),
			input = that.obj.find('.search [name="keyword"]');
		btn_search.click(function () {
			var value = input.val(),
				newData = that.data.filter((item) => {
					return item.product_name.indexOf(value) > -1;
				});
			renderList(newData);
		});
		input.keypress(function (e) {
			let keycode = e.keyCode ? e.keyCode : e.which;
			if (keycode == "13") {
				var value = input.val(),
					newData = that.data.filter((item) => {
						return item.product_name.indexOf(value) > -1;
					});
				renderList(newData);
			}
		});
	}

	function getUserInfo() {
		M.ajax({
			url: that.own_name + "&c=index&a=doUserInfo",
			success: function (result) {
				if (result.status) {
					var user = $(".met-myapp-right"),
						userHtml = `<div class="d-flex user">
							<div class="user-name">${result.data.username}</div>
							<a href="https://u.mituo.cn/#/user/login" target="_blank">${METLANG.account_Settings}</a>
							<button type="button" class="btn btn-default btn-logout">${METLANG.indexloginout}</button>
						</div>`;
					user.html(userHtml).find(".btn-logout").click(function () {
						M.ajax({
							url: that.own_name + "&c=index&a=doLogout",
							success: function (result) {
								metAjaxFun({
									result: result,
									true_fun: function () {
										getUserInfo(that);
									},
								});
							},
						});
					});
				} else {
					var user = $(".met-myapp-right"),
						userHtml = `<a href="#/myapp/login" onClick="setCookie('app_href_source','myapp/?head_tab_active=3')" class="mr-2">
							<button type="button" class="btn btn-default">${METLANG.loginconfirm}</button>
						</a>
						<a href="https://u.mituo.cn/#/user/register" target="_blank"><button class="btn btn-primary">${METLANG.registration}</button></a>`;
					user.html(userHtml);
				}
			},
		});
	}
	installApp();
	search();
})();