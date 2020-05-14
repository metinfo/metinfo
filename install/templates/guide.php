<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
echo <<<EOT
-->
<div class="container my-5 py-5 text-center">
	<h1>欢迎使用米拓企业建站系统</h1>
	<div class="row mt-5">
		<div class="col-md-6 px-5">
			<div class="card shadow">
				<div class="card-body py-5">
					<p class="text-success h6">使用SQLite数据库，无需安装，简单方便！</p>
					<div class="text-left mt-4">
						<div class="form-group row mb-0">
							<label class="col-sm-5 col-form-label text-right text-muted">网站后台地址</label>
							<div class="col-sm-7">
								<span class="ml-1 col-form-label d-block">{$siteurl}admin/</span>
							</div>
						</div>
						<div class="form-group row mb-0">
							<label class="col-sm-5 col-form-label text-right text-muted">后台登录账号</label>
							<div class="col-sm-7">
								<span class="ml-1 col-form-label d-block">admin</span>
							</div>
						</div>
						<div class="form-group row mb-0">
							<label class="col-sm-5 col-form-label text-right text-muted">后台登录密码</label>
							<div class="col-sm-7">
								<span class="ml-1 col-form-label d-block">admin</span>
							</div>
						</div>
					</div>
					<a href="index.php?action=skipInstall" class="btn btn-lg btn-success mt-4">直接使用</a>
				</div>
			</div>
		</div>
		<div class="col-md-6 px-5">
			<div class="card shadow">
				<div class="card-body py-5">
					<div class="mt-5 h6 text-primary" style="margin-bottom: 2.4rem;">
						使用MySQL数据库，高效稳定！<b class="text-danger">推荐使用！</b><br><br>
						安装过程可自行设置管理员账号密码
					</div>
					<a href="index.php?action=inspect" class="btn btn-lg btn-primary mt-4">传统安装</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!--
EOT;
?>