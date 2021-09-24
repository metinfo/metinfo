/**
 * 参数设置
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
 (function(){
    var relation_list=[],
		class_info={};
	// 管理内容列表
	M.component.commonList(function(that){
		var $body=$('.content-relation-body'),
			$form=$body.find('form');
		// 获取已关联内容列表
		relation_list=JSON.parse($(`.content-details-relationlist[data-info="${$form.attr('data-content_info')}"] textarea`).val()||'[]');
		if(relation_list.length){
			var new_list={};
			relation_list.map(item=>{
				new_list[item.id+'|'+item.module]=item;
			});
			relation_list=new_list;
		}else{
			relation_list={};
		}
		// 默认选中第一个栏目
		var $select=$body.find('[data-plugin="select-linkage"] select:eq(0)');
		interval({
			true_val:(time)=>{
				return $select.html() && $('#content-relation-list').hasClass('dtr-inline');
			},true_fun:(time)=>{
				$select.val($select.find('option:eq(1)').attr('value')).change();
			}
		});
		// 添加关联
		interval({
			true_val:(time)=>{
				return $form.attr('data-validate_order');
			},true_fun:(time)=>{
				validate[$form.attr('data-validate_order')].success(function(e,form){
					relationHandle(1);
					return false;
				},false);
			}
		});
		// 渲染内容列表
		return {
        	ajax:{
        		dataSrc:function(result){
					var data=[];
					if(result.data){
						$.each(result.data, function(index, val) {
							var item=[
                                    M.component.checkall('item',val.id),
                                    `<a href="${val.url}" target="_blank" class="media align-items-center d-inline-flex">
                                        ${class_info.module!=4?`<img src="${val.imgurl}" width="100" class="mr-2"/>`:''}
                                        <div class="media-body">${val.title}</div>
                                    </a>`,
									listStatusHtml(val.id)
                                ];
							data.push(item);
						});
					}
				    return data;
		        }
        	}
        };
	});
	// 栏目筛选
	$(document).on('change','.content-relation-body [data-plugin="select-linkage"] select', function () {
		var $parents=$(this).parents('[data-plugin=select-linkage]');
		setTimeout(()=>{
			var class1=$parents.find('[name="class1"]').val(),
				class2=$parents.find('[name="class2"]').val(),
				class3=$parents.find('[name="class3"]').val(),
				active_class=class3||class2||class1||0,
				active_class_order=$.inArray(active_class,[class1,class2,class3]),
				$active_class=$parents.find(`select:eq(${active_class_order}) option[value="${active_class}"]`);
			$('.content-relation-body [name="classid"]').val(active_class).change();
			class_info={
				relation_class:active_class,
				relation_class_name:$active_class.text(),
				module:parseInt($active_class.data('val')),
			}
		},100)
	});
	// 添加关联
	$(document).on('click','.relation-add,.relation-del','#content-relation-list tbody', function () {
		$('#content-relation-list tbody input[type="checkbox"][name="id"]:checked').prop('checked','').change();
		$(this).parents('tr').find('input[type="checkbox"][name="id"]').prop('checked',true).change();
		$(this).hasClass('relation-add') && relationHandle(1);
	});
	// 取消关联
	$(document).on('click','tfoot .btn-default,tbody .relation-del','#content-relation-list', function () {
		M.load('alertify',()=>{
			if($('#content-relation-list tbody [name="id"]:checked').length){
				alertify.confirm('确定要取消关联吗？', function (ev) {
					relationHandle();
				});
			}else{
				alertify.error(METLANG.jslang3);
			}
		});
	});
	// 关联处理
	function relationHandle(type){
		var $checked=$('#content-relation-list tbody input[type="checkbox"][name="id"]:checked');
		$checked.each(function(){
			var id=parseInt($(this).val()),
				info=id+'|'+class_info.module,
				$tr=$(this).parents('tr');
			if(type){
				if(!relation_list[info]){
					relation_list[info]={
						id:id,
						module:class_info.module,
						relation_class:class_info.relation_class,
						relation_class_name:class_info.relation_class_name,
						title:$tr.find('td:eq(1) a .media-body').text(),
						url:$tr.find('td:eq(1) a').attr('href')
					};
					$tr.find('td:eq(2)').html(listStatusHtml(id));
				}
			}else{
				if(relation_list[info]){
					delete relation_list[info];
					$tr.find('td:eq(2)').html(listStatusHtml(id));
				}
			}
		});
		$checked.prop('checked','').change();
		var new_list=[];
		$.each(relation_list, function (index, element) {
			element && new_list.push(element);
		});
		$(`.content-details-relationlist[data-info="${$('.content-relation-body form').attr('data-content_info')}"] textarea`).val(JSON.stringify(new_list)).trigger('render');
		M.load('alertify',()=>{
			alertify.success(METLANG.jsok);
		});
	}
	// 关联内容状态html
	function listStatusHtml(id){
		return relation_list[id+'|'+class_info.module]?`<div class="d-flex align-items-center">
			<span class="badge font-weight-normal badge-success">${METLANG.relation_checked}</span>
			<button type="button" class="btn btn-default btn-sm ml-2 relation-del">${METLANG.relation_cancel}</button>
		</div>`:`<button type="button" class="btn btn-primary btn-sm relation-add">${METLANG.relation_add}</button>`
	}
})();