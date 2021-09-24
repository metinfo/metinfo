<if value="$data['index_num'] neq 163">
<div class="para_search" m-id='para_search'>
	<div class="<if value="$lang['type'] eq 1">container<else/>container-fluid</if>">
		<div class="">
			<tag action='search.option' type="page" order="1"></tag>
				<list data="$search.para" name="$type"></list>
				<if value="$sub && $lang['attr_ok'] && $data['module'] eq 3">
				    <div class="type-order">
				    	<list data="$search.para" name="$type">
				    	<div class="clearfix my_box">
				    		<div class="attr-name col-xl-1 col-lg-2 col-md-2 col-sm-3">{$type.name}：</div>
				    		<ul class="type-order-attr col-xl-11 col-lg-10 col-md-10 col-sm-9">
									
								<list data="$type.list" name="$attr" >
									<li class="inline-block attr-value {$attr.check}">
										<a href="{$attr.url}" class="p-x-10 p-y-5">{$attr.name}</a>
									</li>
								</list>

				    		</ul>
				    	</div>
				    	</list>
				    </div>
				</if>
				<if value="$lang['sort_ok']">
				<tag action='search.option' type="page" order="1"></tag>
				<div class="clearfix p-y-10">
					<ul class="order inline-block p-0 m-y-10 m-r-10">
					<list data="$search.order" name="$res">
					<li class="order-list inline-block m-r-10">
						<a href="{$res.url}" class="p-x-10 p-y-5">{$res.name}<i class="icon wb-triangle-up" aria-hidden="true"></i></a>
					</li>
					</list>
					</ul>
					<if value="$c['shopv2_open'] && $data['module'] eq 3">
					<div class="clearfix inline-block m-y-10 ">
						<form action="" method="get">
							<input type="hidden" name="class1" value="{$data.class1}">
							<input type="hidden" name="class2" value="{$data.class2}">
							<input type="hidden" name="class3" value="{$data.class3}">
							<input type="hidden" name="lang" value="{$data.lang}">
							<input type="hidden" name="search" value="search">
							<input type="hidden" name="content" value="{$_M['form']['content']}">
							<input type="hidden" name="specv" value="{$_M['form']['specv']}">
							<input type="hidden" name="order" value="{$_M['form']['order']}">
							<span class="pricetxt">{$word.app_shop_remind_row4}：</span>
							<input type="text" name="price_low" placeholder="" value="{$_M['form']['price_low']}" class="form-control inline-block w-100 price_num">
							<span class="pricetxt">-</span>
							<input type="text" name="price_top" placeholder="" value="{$_M['form']['price_top']}" class="form-control inline-block w-100 price_num">
							<button type="submit" class='btn pricesearch' style="position: relative;top: -3px;">{$word.confirm}</button>
						</form>
					</div>
					</if>
				</div>
			</if>
		</div>
	</div>
</div>
</if>