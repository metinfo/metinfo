/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */ ;
(function () {
	var that = $.extend(true, {}, admin_module),
		$list=that.obj.find('.met-myapp-list'),
		$detail = that.obj.find('.app-detail');
	TEMPLOADFUNS[that.hash] = function () {
		renderList();
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
	function renderList() {
		that.obj.find('.tab-pane').removeClass('p-4');
		$detail.hide();
		$list.html(M.component.loader({
			class_name: 'w-100',
			height:'300px',
			wrapper_class: 'd-flex align-items-center justify-content-center h-100'
		})).show();
		M.ajax({
			url: that.own_name + '&c=index&a=doindex',
			success: function (result) {
				if (!result.data) {
					$list.html(`<div class="tips">
						<h3>${METLANG.please_login}</h3>
						<div class="mt-2"><a href="#/myapp/login" onClick="setCookie('app_href_source','myapp/?head_tab_active=0')" class="btn btn-primary">${METLANG.loginconfirm}</a></div>
					</div>`);
					return;
				}
				var data = result.data,
					html = '';
				data.map(item => {
					const card = `<div class="col col-6 col-xl-4 col-xxl-3 mb-3 px-2" >
							<div class="media bg-white h-100 flex-column transition500 ${item.url ? 'install' : ''}" data-no="${item.no}" data-m_name="${item.m_name}" data-new_ver="${item.new_ver ? item.new_ver : ''}">
								<div class="body media-body w-100">
									<a href="${item.url ? item.url : 'javascript:;'}" ${parseInt(item.target) ? 'target="_blank"' : ''} class="link w-100 d-flex"  title="${METLANG.fliptext1}" data-newapp="${item.newapp ? item.newapp : ''}">
										<img class="mr-3" width="70" height="70" src="${item.icon}">
										<div class="media-body cover">
											<h5 class="h6 mt-1">
												<span class='mr-2'>${item.appname}</span>${item.ver?`<span class='text-grey font-size-14 version'>v${item.ver}</span>`:''}
											</h5>
											<div class="card-text text-truncate">${item.info}</div>
											${item.install?'':`<div class="card-text">运行环境：PHP ${item.php_version} 及以上版本</div>`}
										</div>
									</a>
								</div>
								<ul class="actions w-100 d-flex ${!item.install&&!item.enabled?'bg-grey':''}">
								${
									!item.install
									? `<li class="${item.enabled?'btn-install':'text-help'}">${item.enabled?`<a href="javascript:;" class='d-block'><i class="fa fa-cloud-download"></i>`:''}<span class="${item.enabled?`ml-2`:'font-size-12'}">${item.enabled?METLANG.appinstall:item.btn_text}</span>${item.enabled?'</a>':''}</li>`
									: `
									${item.new_ver ? `<li><a href="javascript:;" class='d-block update'><i class="fa fa-arrow-up"></i><span class="ml-2">${METLANG.appupgrade}</span></a></li>` : ``}
									${item.system ? `<li class="text-black-50">${METLANG.system_plugin_uninstall}</li>` : `<li class="uninstall"><a href="javascript:;" class="text-black-50 d-block"><i class="fa fa-trash"></i><span class="ml-2">${METLANG.dlapptips6}</span></a></li>`}`
								}
								</ul>
							</div>
						</div>`;
					html += card;
				})
				$list.html(html).show();
			}
		})
	}

	function installApp() {
		$list.on('click','.btn-install',function () {
			if ($(this).attr('disabled')) return;
			$(this).html(`<i class="fa fa-circle-o-notch fa-spin"></i> ${METLANG.updateinstallnow}`).attr('disabled', true);
			M.ajax({
				url: that.own_name + '&c=index&a=doAction',
				data: {
					m_name: $(this).parents('.media').data('m_name'),
					no: $(this).parents('.media').data('no'),
					handle: 'install'
				},
				success: function (result) {
					metAjaxFun({
						result: result,
					});
					renderList();
				}
			})
		})
	}

	function updateApp() {
		$list.on('click','.update',function () {
			$(this).parents('.media').append(`<div class="overlay">
				<div class="text-white">${METLANG.upgrade}</div>
			</div>`);
			M.ajax({
				url: that.own_name + '&c=index&a=doAction',
				data: {
					m_name: $(this).parents('.media').data('m_name'),
					no: $(this).parents('.media').data('no'),
					handle: 'install'
				},
				success: function (result) {
					metAjaxFun({
						result: result,
					});
					renderList();
				}
			})
		})
	}

	function deleteApp() {
		$list.on('click','.uninstall',function () {
			const btn = $(this)
			M.load('alertify', function () {
				alertify.confirm(METLANG.delete_information, function (ev) {
					M.ajax({
						url: that.own_name + 'c=index&a=doAction',
						data: {
							no: btn.parents('.media').data('no'),
							handle: 'uninstall'
						},
						success: function (result) {
							metAjaxFun({
								result: result,
							});
							renderList()
						},
						error: function (result) {
							renderList()
						}
					})
				})
			})
		})
	}

	function appLink(){
		$list.on('click', '.link:not([target])', function () {
			var url = $(this).attr('href'),
				new_app = $(this).data('newapp');
			if (url && url !== 'javascript:;') {
				if (new_app) {
					M.is_admin ? (window.location.href = url) : $('.btn-pageset-common-page').attr({
						'data-url': url.replace(M.url.admin.replace(M.weburl, '../') + '#/', ''),
						title: $('h5', this).text()
					}).trigger('clicks');
				} else {
					$detail.html(`<iframe src="${url}" ></iframe>`);
				}
				$list.hide();
				$detail.show();
			} else {
				M.load('alertify', function () {
					alertify.error(METLANG.install_first)
				})
			}
			return false;
		});
	}

	function getUserInfo() {
		M.ajax({
			url: that.own_name + '&c=index&a=doUserInfo',
			success: function (result) {
				if (result.status) {
					var user = $('.met-myapp-right'),
						userHtml = `<div class="d-flex user">
							<div class="user-name">${result.data.username}</div>
							<a href="https://u.mituo.cn/#/user/login" target="_blank">${METLANG.account_Settings}</a>
							<button type="button" class="btn btn-logout btn-default">${METLANG.indexloginout}</button>
						</div>`;
					user.html(userHtml).find('.btn-logout').click(function () {
						M.ajax({
							url: that.own_name + '&c=index&a=doLogout',
							success: function (result) {
								metAjaxFun({
									result: result,
									true_fun: function () {
										window.location.reload()
									}
								})
							}
						})
					});
				} else {
					var user =  $('.met-myapp-right'),
						userHtml = `<a href="#/myapp/login" onClick="setCookie('app_href_source','myapp/?head_tab_active=0')" class="mr-2">
							<button type="button" class="btn btn-default">${METLANG.loginconfirm}</button>
						</a>
						<a href="https://u.mituo.cn/#/user/register" target="_blank"><button class="btn btn-primary">${METLANG.registration}</button></a>`;
					user.html(userHtml);
				}
			}
		})
	}
	installApp();
	deleteApp();
	updateApp();
	appLink();
})();