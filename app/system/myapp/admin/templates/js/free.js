/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */ ;
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
		that.obj.find('.tab-pane').removeClass('p-4');
		$list.html(M.component.loader({
			class_name: 'w-100',
			height:'300px',
			wrapper_class: 'd-flex align-items-center justify-content-center h-100'
		}));
		M.ajax({
			url: that.own_name + '&c=index&a=doAppList&type=free',
			success: function (result) {
				let data = (that.data = result.data);
				renderList(data);
			}
		});
		that.obj.find('.search [name="keyword"]').val('');
	}

	function renderList(data) {
		let html = '';
		data.length > 0 ?
		data.map(item => {
			var card = `<div class="col col-6 col-xl-4 col-xxl-3 mb-3 px-2" >
					<div class="media bg-white h-100  flex-column transition500" data-no="${item.product_code}" data-m_name="${item.m_name}">
						<div class="body media-body w-100">
							<a href="${item.show_url}" class="link w-100 d-flex"  title="${METLANG.fliptext1}" target="_blank">
								<img class="mr-3" width="70" height="70" src="${item.product_image}">
								<div class="media-body cover">
									<h5 class="h6 mt-1">${item.product_name}</h5>
									<div class="card-text text-truncate">${item.product_desc}</div>
									<div class="card-text">运行环境：PHP ${item.php_version} 及以上版本</div>
								</div>
							</a>
						</div>
						<ul class="actions w-100 d-flex ${item.enabled?'':'bg-grey'}">
							<li class="${item.enabled?'btn-install':'text-help'}">${item.enabled?`<a href="javascript:;" class='d-block'><i class="fa fa-cloud-download"></i>`:''}<span class="${item.enabled?`ml-2`:'font-size-12'}">${item.btn_text}</span>${item.enabled?'</a>':''}</li>
						</ul>
					</div>
				</div>`;
			html += card;
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
				url: that.own_name + '&c=index&a=doAction',
				data: {
					m_name: btn.parents('.media').data('m_name'),
					no: btn.parents('.media').data('no'),
					handle: 'install'
				},
				success: function (result) {
					metAjaxFun({
						result: result,
						true_fun: function () {
							if (M.is_admin) {
								window.location.href = M.url.admin + '#/myapp/?head_tab_active=0'
								return;
							}
							$('.pageset-nav-modal .nav-modal-item[data-path="myapp"] .met-headtab a[data-url="myapp/myapp"]').click()
						},
						false_fun: function () {
							btn.html(beforeHTML).attr('disabled', false)
						}
					})
				}
			})
		})
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
			url: that.own_name + '&c=index&a=doUserInfo',
			success: function (result) {
				if (result.status) {
					var user = $(".met-myapp-right"),
						userHtml = `<div class="d-flex user">
							<div class="user-name">${result.data.username}</div>
							<a href="https://u.mituo.cn/#/user/login" target="_blank">${METLANG.account_Settings}</a>
							<button type="button" class="btn btn-default btn-logout">${METLANG.indexloginout}</button>
						</div>`;
					user.html(userHtml).find('.btn-logout').click(function () {
						M.ajax({
							url: that.own_name + '&c=index&a=doLogout',
							success: function (result) {
								metAjaxFun({
									result: result,
									true_fun: function () {
										getUserInfo(that)
									}
								})
							}
						});
					});
				} else {
					var user = $(".met-myapp-right"),
						userHtml = `<a href="#/myapp/login" onClick="setCookie('app_href_source','myapp/?head_tab_active=1')" class="mr-2">
							<button type="button" class="btn btn-default">${METLANG.loginconfirm}</button>
						</a>
						<a href="https://u.mituo.cn/#/user/register" target="_blank"><button class="btn btn-primary">${METLANG.registration}</button></a>`;
					user.html(userHtml);
				}
			}
		})
	}
	installApp();
	search();
})();