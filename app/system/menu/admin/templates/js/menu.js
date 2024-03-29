/**
 * 底部菜单
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
	var that=$.extend(true,{}, admin_module);
	M.component.commonList(function(thats,table_order){
		// 底部菜单列表加载
		return {
        	ajax:{
        		dataSrc:function(result){
					var data=[];
					if(result.data){
						var del_url=that.own_name+'c=menu_admin&a=doSaveMenu&submit_type=del&allid=';
						$.each(result.data, function(index, val) {
							var item=[
									M.component.checkall('item',val.id)+M.component.formWidget('no_order-'+val.id,val.no_order),
									M.component.formWidget('name-'+val.id,val.name,'text',1),
									M.component.formWidget('url-'+val.id,val.url,'text'),
									// M.component.formWidget({
									// 	type:'select',
									// 	name:'type-'+val.id,
									// 	value:val.type||'',
									// 	data:[
									// 		{name:'普通链接',value:''},
									// 		{name:METLANG.parameter8,value:'tel'},
									// 		{name:METLANG.short_message,value:'sms'},
									// 		{name:METLANG.mailbox,value:'email'},
									// 		{name:METLANG.common_qq,value:'qq'},
									// 		{name:METLANG.enterprise_qq,value:'qyqq'},
									// 		{name:'添加微信好友',value:'wechat'}
									// 	]
									// }),
									M.component.formWidget({
										name:'icon-'+val.id,
										type:'icon',
										value:val.icon
									}),
									M.component.formWidget({
										name:'target-'+val.id,
										type:'select',
										value:val.target,
										data:[
											{name:METLANG.original_window,value:0},
											{name:METLANG.new_window,value:1},
										]
									}),
									M.component.formWidget('but_color-'+val.id,val.but_color,'color'),
									M.component.formWidget('text_color-'+val.id,val.text_color,'color'),
									M.component.formWidget({
										name:'enabled-'+val.id,
										type:'select',
										value:val.enabled,
										data:[
											{name:METLANG.yes,value:1},
											{name:METLANG.no,value:0},
										]
									}),
									M.component.btn('del',{del_url:del_url+val.id})
								];
							data.push(item);
						});
					}
				    return data;
		        }
        	}
    	};
	});
	// 底部菜单列表排序
	M.load('dragsort',function(){
        setTimeout(function(){
        	dragsortFun[that.obj.find('table tbody').attr('data-dragsort_order')]=function(wrapper,item){
	        	wrapper.find('tr [name*="no_order-"]').each(function(index, el) {
	    			$(this).val($(this).parents('tr').index());
	        	});
	        };
        },0);
    });
	// 添加按钮后的回调
	that.obj.find('table [table-addlist]').click(function(event) {
		var $self=$(this);
		setTimeout(function(){
			var $new_tr=$self.parents('table').find('tbody tr:last-child');
			$new_tr.find('[name*="no_order-"]').val($new_tr.index());
		},0);
	});
})();