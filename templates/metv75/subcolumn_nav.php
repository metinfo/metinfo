<if value="$lang['tagshow_2']">

<tag action='category' cid="$data['releclass1']" type='son'>
<if value="$m['_first']">
<div class="met-column-nav" m-id='subcolumn_nav' m-type='nocontent'>
	<div class="container">
		<div class="row">
			<div class="clearfix">
				<div class="subcolumn-nav">
					<ul class="met-column-nav-ul m-b-0 ulstyle">
						<tag action='category' cid="$data['releclass1']" type="current">
				<if value="$m['module'] neq 1 && $lang['all']">
					<li>
						<a href="{$m.url}"  title="{$ui.all}" {$m.urlnew} {$m.nofollow}
						<if value="$data['classnow'] eq $m['id']">
						class="active link"
						<else/>
						class="link"
						</if>
						>{$lang.all}</a>
					</li>
				<else/>
					<if value="$m['isshow']">
						<li>
							<a href="{$m.url}"  title="{$m.name}" {$m.urlnew} {$m.nofollow}
							<if value="$data['classnow'] eq $m['id']">
							class="active link"
							<else/>
							class="link"
							</if>
							>{$m.name}</a>
						</li>
					</if>
				</if>
						<tag action='category' cid="$m['id']" type='son' class="active">
						<if value="$m['sub']">
						<li class="dropdown">
							<a href="{$m.url}" title="{$m.name}" class="dropdown-toggle {$m.class} link"  {$m.nofollow} {$m.urlnew} data-toggle="dropdown">{$m.name}</a>
							<div class="dropdown-menu animation-slide-bottom10">
								<if value="$m['module'] neq 1">
									<a href="{$m.url}"  title="{$ui.all}" {$m.nofollow} {$m.urlnew} class='dropdown-item {$m.class}'>{$ui.all}</a>
								</if>
								<tag action='category' cid="$m['id']" type='son' class="active">
								<a href="{$m.url}" title="{$m.name}" {$m.nofollow} {$m.urlnew} class='dropdown-item {$m.class}'>{$m.name}</a>
								</tag>
							</div>
						</li>
						<else/>
						<li>
							<a href="{$m.url}" title="{$m.name}" {$m.nofollow} {$m.urlnew} class='{$m.class} link'>{$m.name}</a>
						</li>
						</if>
						</tag>
						</tag>
					</ul>
				</div>
		</div>
		<if value="$ui['product_search'] && $data['module'] eq 3">
		<tag action='search.option' type="page" order="1"></tag>
			<div class="product-search">
			<div class="form-group" data-placeholder="{$ui.product_placeholder}">
				<tag action="search.column"></tag>
			</div>
		</div>
		</if>
	</div>
	</div>
</div>
</if>
</tag>
</if>