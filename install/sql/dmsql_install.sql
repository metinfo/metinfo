DROP TABLE IF EXISTS met_admin_array;
CREATE TABLE "met_admin_array"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"array_name" VARCHAR(255) NOT NULL,
"admin_type" TEXT,
"admin_ok" INT DEFAULT '0' NOT NULL,
"admin_op" VARCHAR(30) DEFAULT 'metinfo',
"admin_issueok" INT DEFAULT '0',
"admin_group" INT DEFAULT '0',
"user_webpower" INT DEFAULT '0',
"array_type" INT DEFAULT '0',
"lang" VARCHAR(50),
"langok" VARCHAR(255) DEFAULT 'metinfo',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_admin_column;
CREATE TABLE "met_admin_column"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(100),
"url" VARCHAR(255),
"bigclass" INT DEFAULT '0',
"field" INT DEFAULT '0',
"type" INT DEFAULT '0',
"list_order" INT DEFAULT '0',
"icon" VARCHAR(255),
"info" TEXT,
"display" INT DEFAULT '1',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_admin_logs;
CREATE TABLE "met_admin_logs"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"username" VARCHAR(255),
"name" VARCHAR(255),
"module" VARCHAR(255),
"current_url" VARCHAR(255),
"brower" VARCHAR(255),
"result" VARCHAR(255),
"ip" VARCHAR(50),
"client" VARCHAR(50),
"time" INT DEFAULT '0',
"user_agent" VARCHAR(255),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_admin_table;
CREATE TABLE "met_admin_table"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"admin_type" TEXT,
"admin_id" VARCHAR(20) NOT NULL,
"admin_pass" VARCHAR(64) NOT NULL,
"admin_name" VARCHAR(30) NOT NULL,
"admin_sex" INT DEFAULT '1',
"admin_tel" VARCHAR(20),
"admin_mobile" VARCHAR(20),
"admin_email" VARCHAR(150),
"admin_qq" VARCHAR(12),
"admin_msn" VARCHAR(40),
"admin_taobao" VARCHAR(40),
"admin_introduction" TEXT,
"admin_login" INT DEFAULT '0',
"admin_modify_ip" VARCHAR(20),
"admin_modify_date" DATETIME(6),
"admin_register_date" DATETIME(6),
"admin_approval_date" DATETIME(6),
"admin_ok" INT DEFAULT '0',
"admin_op" VARCHAR(30) DEFAULT 'metinfo',
"admin_issueok" INT DEFAULT '0',
"admin_group" INT DEFAULT '0',
"companyname" VARCHAR(255),
"companyaddress" VARCHAR(255),
"companyfax" VARCHAR(255),
"usertype" INT DEFAULT '0',
"checkid" INT DEFAULT '0',
"companycode" VARCHAR(50),
"companywebsite" VARCHAR(50),
"cookie" TEXT,
"admin_shortcut" TEXT,
"lang" VARCHAR(50),
"content_type" INT DEFAULT '0',
"langok" VARCHAR(255) DEFAULT 'metinfo',
"admin_login_lang" VARCHAR(50),
"admin_check" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_app_config;
CREATE TABLE "met_app_config"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"appno" INT DEFAULT '0',
"name" VARCHAR(255),
"value" TEXT,
"lang" VARCHAR(50),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_app_plugin;
CREATE TABLE "met_app_plugin"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no_order" INT DEFAULT '0',
"no" INT DEFAULT '0',
"m_name" VARCHAR(255),
"m_action" VARCHAR(255),
"effect" INT DEFAULT '0',
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_applist;
CREATE TABLE "met_applist"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no" INT DEFAULT '0',
"ver" VARCHAR(50),
"m_name" VARCHAR(50),
"m_class" VARCHAR(50),
"m_action" VARCHAR(50),
"appname" VARCHAR(50),
"info" TEXT,
"addtime" INT DEFAULT '0',
"updatetime" INT DEFAULT '0',
"target" INT DEFAULT '0',
"display" INT DEFAULT '1',
"depend" VARCHAR(100),
"mlangok" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_column;
CREATE TABLE "met_column"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(100),
"foldername" VARCHAR(50),
"filename" VARCHAR(50),
"bigclass" INT DEFAULT '0',
"samefile" INT DEFAULT '0',
"module" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"wap_nav_ok" INT DEFAULT '0',
"if_in" INT DEFAULT '0',
"nav" INT DEFAULT '0',
"ctitle" VARCHAR(200),
"keywords" VARCHAR(200),
"content" TEXT,
"description" TEXT,
"other_info" TEXT,
"custom_info" TEXT,
"list_order" INT DEFAULT '0',
"new_windows" VARCHAR(50),
"classtype" INT DEFAULT '1',
"out_url" VARCHAR(200),
"index_num" INT DEFAULT '0',
"access" INT,
"indeximg" VARCHAR(255),
"columnimg" VARCHAR(255),
"isshow" INT DEFAULT '1',
"lang" VARCHAR(50),
"namemark" VARCHAR(255),
"releclass" INT DEFAULT '0',
"display" INT DEFAULT '0',
"icon" VARCHAR(100),
"nofollow" INT DEFAULT '0',
"text_size" INT DEFAULT '0',
"text_color" VARCHAR(100),
"thumb_list" VARCHAR(50),
"thumb_detail" VARCHAR(50),
"list_length" INT DEFAULT '0',
"tab_num" INT DEFAULT '0',
"tab_name" VARCHAR(255),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_config;
CREATE TABLE "met_config"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(255),
"value" TEXT,
"mobile_value" TEXT,
"columnid" INT DEFAULT '0',
"flashid" INT DEFAULT '0',
"lang" VARCHAR(50),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_cv;
CREATE TABLE "met_cv"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"addtime" DATETIME(6),
"readok" INT DEFAULT '0',
"customerid" VARCHAR(50) DEFAULT '0',
"jobid" INT DEFAULT '0' NOT NULL,
"lang" VARCHAR(50),
"ip" VARCHAR(255),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_download;
CREATE TABLE "met_download"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"title" VARCHAR(200),
"ctitle" VARCHAR(200),
"keywords" VARCHAR(200),
"description" TEXT,
"content" TEXT,
"class1" INT DEFAULT '0',
"class2" INT DEFAULT '0',
"class3" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"new_ok" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"imgurl" VARCHAR(255),
"downloadurl" VARCHAR(255),
"filesize" VARCHAR(100),
"com_ok" INT DEFAULT '0',
"hits" INT DEFAULT '0',
"updatetime" DATETIME(6),
"addtime" DATETIME(6),
"issue" VARCHAR(100),
"access" INT,
"top_ok" INT DEFAULT '0',
"downloadaccess" INT,
"filename" VARCHAR(255),
"lang" VARCHAR(50),
"recycle" INT DEFAULT '0' NOT NULL,
"displaytype" INT DEFAULT '1' NOT NULL,
"tag" TEXT,
"links" VARCHAR(200),
"text_size" INT DEFAULT '0',
"text_color" VARCHAR(100),
"other_info" TEXT,
"custom_info" TEXT,
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_feedback;
CREATE TABLE "met_feedback"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"class1" INT DEFAULT '0',
"fdtitle" VARCHAR(255),
"fromurl" VARCHAR(255),
"ip" VARCHAR(255),
"addtime" DATETIME(6),
"readok" INT DEFAULT '0',
"useinfo" TEXT,
"customerid" VARCHAR(30) DEFAULT '0',
"lang" VARCHAR(50),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_flash;
CREATE TABLE "met_flash"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"module" TEXT,
"img_title" VARCHAR(255),
"img_path" VARCHAR(255),
"img_link" VARCHAR(255),
"flash_path" VARCHAR(255),
"flash_back" VARCHAR(255),
"no_order" INT DEFAULT '0',
"width" INT DEFAULT '0',
"height" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"img_title_color" VARCHAR(100),
"img_des" VARCHAR(255),
"img_des_color" VARCHAR(100),
"img_text_position" VARCHAR(100) DEFAULT '4',
"img_title_fontsize" INT DEFAULT '0',
"img_des_fontsize" INT DEFAULT '0',
"height_m" INT DEFAULT '0',
"height_t" INT DEFAULT '0',
"mobile_img_path" VARCHAR(255),
"img_title_mobile" VARCHAR(255),
"img_title_color_mobile" VARCHAR(100),
"img_text_position_mobile" VARCHAR(100) DEFAULT '4',
"img_title_fontsize_mobile" INT DEFAULT '0',
"img_des_mobile" VARCHAR(255),
"img_des_color_mobile" VARCHAR(100),
"img_des_fontsize_mobile" INT DEFAULT '0',
"lang" VARCHAR(50),
"target" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_flash_button;
CREATE TABLE "met_flash_button"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"flash_id" INT DEFAULT '0' NOT NULL,
"but_text" VARCHAR(255),
"but_url" VARCHAR(255),
"but_text_size" INT DEFAULT '0',
"but_text_color" VARCHAR(100),
"but_text_hover_color" VARCHAR(100),
"but_color" VARCHAR(100),
"but_hover_color" VARCHAR(100),
"but_size" VARCHAR(100),
"is_mobile" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"target" INT DEFAULT '0',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_flist;
CREATE TABLE "met_flist"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"listid" INT DEFAULT '0',
"paraid" INT DEFAULT '0',
"info" TEXT,
"lang" VARCHAR(50),
"imgname" VARCHAR(255),
"module" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_ifcolumn;
CREATE TABLE "met_ifcolumn"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no" INT DEFAULT '0',
"name" VARCHAR(50),
"appname" VARCHAR(50),
"addfile" INT DEFAULT '1',
"memberleft" INT DEFAULT '0',
"uniqueness" INT DEFAULT '0',
"fixed_name" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_ifcolumn_addfile;
CREATE TABLE "met_ifcolumn_addfile"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no" INT DEFAULT '0',
"filename" VARCHAR(255),
"m_name" VARCHAR(255),
"m_module" VARCHAR(255),
"m_class" VARCHAR(255),
"m_action" VARCHAR(255),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_ifmember_left;
CREATE TABLE "met_ifmember_left"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no" INT DEFAULT '0',
"columnid" INT DEFAULT '0',
"title" VARCHAR(50),
"foldername" VARCHAR(255),
"filename" VARCHAR(255),
"target" INT DEFAULT '0',
"own_order" VARCHAR(11),
"effect" INT DEFAULT '0',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_img;
CREATE TABLE "met_img"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"title" VARCHAR(200),
"ctitle" VARCHAR(200),
"keywords" VARCHAR(200),
"description" TEXT,
"content" TEXT,
"class1" INT DEFAULT '0',
"class2" INT DEFAULT '0',
"class3" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"new_ok" INT DEFAULT '0',
"imgurl" VARCHAR(255),
"imgurls" VARCHAR(255),
"displayimg" TEXT,
"com_ok" INT DEFAULT '0',
"hits" INT DEFAULT '0',
"updatetime" DATETIME(6),
"addtime" DATETIME(6),
"issue" VARCHAR(100),
"access" INT,
"top_ok" INT DEFAULT '0',
"filename" VARCHAR(255),
"lang" VARCHAR(50),
"content1" TEXT,
"content2" TEXT,
"content3" TEXT,
"content4" TEXT,
"contentinfo" VARCHAR(255),
"contentinfo1" VARCHAR(255),
"contentinfo2" VARCHAR(255),
"contentinfo3" VARCHAR(255),
"contentinfo4" VARCHAR(255),
"recycle" INT DEFAULT '0',
"displaytype" INT DEFAULT '1',
"tag" TEXT,
"links" VARCHAR(200),
"imgsize" VARCHAR(200),
"text_size" INT DEFAULT '0',
"text_color" VARCHAR(100),
"other_info" TEXT,
"custom_info" TEXT,
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_infoprompt;
CREATE TABLE "met_infoprompt"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"news_id" INT DEFAULT '0',
"newstitle" VARCHAR(120),
"content" TEXT,
"url" VARCHAR(200),
"member" VARCHAR(50),
"type" VARCHAR(35),
"time" INT DEFAULT '0',
"see_ok" INT DEFAULT '0',
"lang" VARCHAR(10),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_job;
CREATE TABLE "met_job"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"position" VARCHAR(200),
"count" INT DEFAULT '0',
"place" VARCHAR(200),
"deal" VARCHAR(200),
"addtime" DATE,
"updatetime" DATE,
"useful_life" INT DEFAULT '0',
"content" TEXT,
"access" INT,
"class1" INT DEFAULT '0',
"class2" INT DEFAULT '0',
"class3" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"top_ok" INT DEFAULT '0',
"email" VARCHAR(255),
"filename" VARCHAR(255),
"lang" VARCHAR(50),
"displaytype" INT DEFAULT '1',
"text_size" INT DEFAULT '0',
"text_color" VARCHAR(100),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_label;
CREATE TABLE "met_label"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"oldwords" VARCHAR(255),
"newwords" VARCHAR(255),
"newtitle" VARCHAR(255),
"url" VARCHAR(255),
"num" INT DEFAULT '99',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_lang;
CREATE TABLE "met_lang"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(100),
"useok" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"mark" VARCHAR(50),
"synchronous" VARCHAR(50),
"flag" VARCHAR(100),
"link" VARCHAR(255),
"newwindows" INT DEFAULT '0',
"met_webhtm" INT DEFAULT '0',
"met_htmtype" VARCHAR(50),
"met_weburl" VARCHAR(255),
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_lang_admin;
CREATE TABLE "met_lang_admin"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(100),
"useok" INT DEFAULT '1',
"no_order" INT DEFAULT '0',
"mark" VARCHAR(50),
"synchronous" VARCHAR(50),
"link" VARCHAR(255),
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_language;
CREATE TABLE "met_language"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(255),
"value" TEXT,
"site" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"array" INT DEFAULT '0',
"app" INT DEFAULT '0',
"lang" VARCHAR(50),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_link;
CREATE TABLE "met_link"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"webname" VARCHAR(255),
"module" TEXT,
"weburl" VARCHAR(255),
"weblogo" VARCHAR(255),
"link_type" INT DEFAULT '0',
"info" VARCHAR(255),
"contact" VARCHAR(255),
"orderno" INT DEFAULT '0',
"com_ok" INT DEFAULT '0',
"show_ok" INT DEFAULT '0',
"addtime" DATETIME(6),
"lang" VARCHAR(50),
"ip" VARCHAR(255),
"nofollow" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_menu;
CREATE TABLE "met_menu"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(255),
"url" VARCHAR(255),
"icon" VARCHAR(255),
"text_color" VARCHAR(100),
"but_color" VARCHAR(100),
"target" INT DEFAULT '0',
"enabled" INT DEFAULT '1',
"no_order" INT DEFAULT '0',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_message;
CREATE TABLE "met_message"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"ip" VARCHAR(255),
"addtime" DATETIME(6),
"readok" INT DEFAULT '0',
"useinfo" TEXT,
"lang" VARCHAR(50),
"access" INT,
"customerid" VARCHAR(30) DEFAULT '0',
"checkok" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_mlist;
CREATE TABLE "met_mlist"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"listid" INT DEFAULT '0',
"paraid" INT DEFAULT '0',
"info" TEXT,
"lang" VARCHAR(50),
"imgname" VARCHAR(255),
"module" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_news;
CREATE TABLE "met_news"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"title" VARCHAR(200),
"ctitle" VARCHAR(200),
"keywords" VARCHAR(200),
"description" TEXT,
"content" TEXT,
"class1" INT DEFAULT '0',
"class2" INT DEFAULT '0',
"class3" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"img_ok" INT DEFAULT '0',
"imgurl" VARCHAR(255),
"imgurls" VARCHAR(255),
"com_ok" INT DEFAULT '0',
"issue" VARCHAR(100),
"hits" INT DEFAULT '0',
"updatetime" DATETIME(6),
"addtime" DATETIME(6),
"access" INT,
"top_ok" INT DEFAULT '0',
"filename" VARCHAR(255),
"lang" VARCHAR(50),
"recycle" INT DEFAULT '0',
"displaytype" INT DEFAULT '1',
"tag" TEXT,
"links" VARCHAR(200),
"publisher" VARCHAR(50),
"text_size" INT DEFAULT '0',
"text_color" VARCHAR(100),
"other_info" TEXT,
"custom_info" TEXT,
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_online;
CREATE TABLE "met_online"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no_order" INT DEFAULT '0',
"name" VARCHAR(255),
"value" VARCHAR(255),
"icon" VARCHAR(255),
"type" INT DEFAULT '0',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_otherinfo;
CREATE TABLE "met_otherinfo"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"info1" VARCHAR(255),
"info2" VARCHAR(255),
"info3" VARCHAR(255),
"info4" VARCHAR(255),
"info5" VARCHAR(255),
"info6" VARCHAR(255),
"info7" VARCHAR(255),
"info8" TEXT,
"info9" TEXT,
"info10" TEXT,
"imgurl1" VARCHAR(255),
"imgurl2" VARCHAR(255),
"rightmd5" VARCHAR(255),
"righttext" VARCHAR(255),
"authcode" TEXT,
"authpass" VARCHAR(255),
"authtext" VARCHAR(255),
"data" TEXT,
"lang" VARCHAR(50),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_para;
CREATE TABLE "met_para"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"pid" INT DEFAULT '0',
"value" VARCHAR(255),
"module" INT DEFAULT '0',
"order" INT DEFAULT '0',
"lang" VARCHAR(100),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_parameter;
CREATE TABLE "met_parameter"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(100),
"options" TEXT,
"description" TEXT,
"no_order" INT DEFAULT '0',
"type" INT DEFAULT '0',
"access" INT,
"wr_ok" INT DEFAULT '0',
"class1" INT DEFAULT '0',
"class2" INT DEFAULT '0',
"class3" INT DEFAULT '0',
"module" INT DEFAULT '0',
"lang" VARCHAR(50),
"wr_oks" INT DEFAULT '0',
"related" VARCHAR(50),
"edit_ok" INT DEFAULT '1',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_plist;
CREATE TABLE "met_plist"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"listid" INT DEFAULT '0',
"paraid" INT DEFAULT '0',
"info" TEXT,
"lang" VARCHAR(50),
"imgname" VARCHAR(255),
"module" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_product;
CREATE TABLE "met_product"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"title" VARCHAR(200),
"ctitle" VARCHAR(200),
"keywords" VARCHAR(200),
"description" TEXT,
"content" TEXT,
"class1" INT DEFAULT '0',
"class2" INT DEFAULT '0',
"class3" INT DEFAULT '0',
"classother" TEXT NOT NULL,
"no_order" INT DEFAULT '0',
"wap_ok" INT DEFAULT '0',
"new_ok" INT DEFAULT '0',
"imgurl" VARCHAR(255),
"imgurls" VARCHAR(255),
"displayimg" TEXT,
"com_ok" INT DEFAULT '0',
"hits" INT DEFAULT '0',
"updatetime" DATETIME(6),
"addtime" DATETIME(6),
"issue" VARCHAR(100),
"access" INT,
"top_ok" INT DEFAULT '0',
"filename" VARCHAR(255),
"lang" VARCHAR(50),
"video" TEXT,
"content1" TEXT,
"content2" TEXT,
"content3" TEXT,
"content4" TEXT,
"contentinfo" VARCHAR(255),
"contentinfo1" VARCHAR(255),
"contentinfo2" VARCHAR(255),
"contentinfo3" VARCHAR(255),
"contentinfo4" VARCHAR(255),
"recycle" INT DEFAULT '0',
"displaytype" INT DEFAULT '1',
"tag" TEXT,
"links" VARCHAR(200),
"imgsize" VARCHAR(200),
"text_size" INT DEFAULT '0',
"text_color" VARCHAR(100),
"other_info" TEXT,
"custom_info" TEXT,
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_relation;
CREATE TABLE "met_relation"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"aid" INT DEFAULT '0',
"module" INT DEFAULT '0',
"relation_id" INT DEFAULT '0',
"relation_module" INT DEFAULT '0',
"lang" VARCHAR(50) DEFAULT NULL,
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_skin_table;
CREATE TABLE "met_skin_table"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"skin_name" VARCHAR(200),
"skin_file" VARCHAR(20),
"skin_info" TEXT,
"devices" INT DEFAULT '0',
"ver" VARCHAR(10),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_tags;
CREATE TABLE "met_tags"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"tag_name" VARCHAR(255),
"tag_pinyin" VARCHAR(255),
"module" INT DEFAULT '0',
"cid" INT DEFAULT '0',
"list_id" VARCHAR(255),
"title" VARCHAR(255),
"keywords" VARCHAR(255),
"description" VARCHAR(255),
"tag_color" VARCHAR(255),
"tag_size" INT DEFAULT '0',
"sort" INT DEFAULT '0',
"lang" VARCHAR(100),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_templates;
CREATE TABLE "met_templates"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"no" VARCHAR(20) DEFAULT '0',
"pos" INT DEFAULT '0',
"no_order" INT DEFAULT '0',
"type" INT DEFAULT '0',
"style" INT DEFAULT '0',
"selectd" VARCHAR(500),
"name" VARCHAR(50),
"value" TEXT,
"defaultvalue" TEXT,
"valueinfo" VARCHAR(100),
"tips" VARCHAR(255),
"lang" VARCHAR(50),
"bigclass" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_ui_config;
CREATE TABLE "met_ui_config"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"pid" INT DEFAULT '0',
"parent_name" VARCHAR(100),
"ui_name" VARCHAR(100),
"skin_name" VARCHAR(100),
"uip_type" INT DEFAULT '0',
"uip_style" INT DEFAULT '0',
"uip_select" VARCHAR(500) DEFAULT '1',
"uip_name" VARCHAR(100),
"uip_key" VARCHAR(100),
"uip_value" TEXT,
"uip_default" VARCHAR(255),
"uip_title" VARCHAR(100),
"uip_description" VARCHAR(255),
"uip_order" INT DEFAULT '0',
"lang" VARCHAR(100),
"uip_hidden" INT DEFAULT '0',
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_ui_list;
CREATE TABLE "met_ui_list"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"installid" INT DEFAULT '0',
"parent_name" VARCHAR(100),
"ui_name" VARCHAR(100),
"skin_name" VARCHAR(100),
"ui_page" VARCHAR(200),
"ui_title" VARCHAR(100),
"ui_description" VARCHAR(500),
"ui_order" INT DEFAULT '0',
"ui_version" VARCHAR(100),
"ui_installtime" INT DEFAULT '0',
"ui_edittime" INT DEFAULT '0',
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_user;
CREATE TABLE "met_user"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"username" VARCHAR(30),
"password" VARCHAR(32),
"head" VARCHAR(100),
"email" VARCHAR(50),
"tel" VARCHAR(20),
"groupid" INT DEFAULT '0',
"register_time" INT DEFAULT '0',
"register_ip" VARCHAR(15),
"login_time" INT DEFAULT '0',
"login_count" INT DEFAULT '0',
"login_ip" VARCHAR(15),
"valid" INT DEFAULT '0',
"source" VARCHAR(20),
"lang" VARCHAR(50),
"idvalid" INT DEFAULT '0',
"reidinfo" VARCHAR(100),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;


DROP TABLE IF EXISTS met_user_group;
CREATE TABLE "met_user_group"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"name" VARCHAR(255),
"access" INT DEFAULT '0',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_user_group_pay;
CREATE TABLE "met_user_group_pay"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"groupid" INT DEFAULT '0',
"price" DOUBLE DEFAULT '0.00',
"recharge_price" DOUBLE DEFAULT '0.00',
"buyok" INT DEFAULT '0',
"rechargeok" INT DEFAULT '0',
"lang" VARCHAR(50),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_user_list;
CREATE TABLE "met_user_list"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"listid" INT DEFAULT '0',
"paraid" INT DEFAULT '0',
"info" TEXT,
"lang" VARCHAR(50),
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_user_other;
CREATE TABLE "met_user_other"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"met_uid" INT DEFAULT '0',
"openid" VARCHAR(100),
"unionid" VARCHAR(100),
"access_token" VARCHAR(255),
"expires_in" INT DEFAULT '0',
"type" VARCHAR(10),
CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

DROP TABLE IF EXISTS met_weixin_reply_log;
CREATE TABLE "met_weixin_reply_log"
(
"id" INT IDENTITY(1, 1) NOT NULL,
"FromUserName" VARCHAR(255),
"Content" TEXT,
"rid" INT,
"CreateTime" INT,
NOT CLUSTER PRIMARY KEY("id")) STORAGE(ON "MAIN", CLUSTERBTR) ;

INSERT INTO met_applist VALUES ('0', '1.0', 'ueditor', 'index', 'doindex', '百度编辑器', '编辑器', '0', '0', '0','0','','0');
INSERT INTO met_applist VALUES ('10070','1.5', 'metconfig_sms', 'index', 'doindex', '短信功能', '短信接口', '0', '0', '0','1','','0');
INSERT INTO met_applist VALUES ('50002','1.0', 'metconfig_template', 'temtool', 'dotemlist', '官方模板管理工具', '官方商业模板请在此进行管理操作', '0', '0', '0','1','','1');


#系统全局配置
INSERT INTO met_config VALUES('metcms_v','7.6','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_ch_lang','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_lang_mark','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_admin_type_ok','0','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_admin_type','cn','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_index_type','cn','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_host','api.metinfo.cn','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_host_new','app.metinfo.cn','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_api', 'https://u.mituo.cn/api/client', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_tablename','admin_array|admin_column|admin_logs|admin_table|app_config|app_plugin|applist|column|config|cv|download|feedback|flash|flash_button|flist|ifcolumn|ifcolumn_addfile|ifmember_left|img|infoprompt|job|label|lang|lang_admin|language|link|menu|message|mlist|news|online|otherinfo|para|parameter|plist|product|relation|skin_table|tags|templates|ui_config|ui_list|user|user_group|user_group_pay|user_list|user_other|weixin_reply_log', '', '0', '0', 'metinfo');

#其他配置
INSERT INTO met_config VALUES('metconfig_safe_prompt', '0', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_uiset_guide', '1', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_301jump', '', '', 0, 0, 'metinfo');
INSERT INTO met_config VALUES('metconfig_https', '', '', 0, 0, 'metinfo');
INSERT INTO met_config VALUES('disable_cssjs', 0, '', 0, 0, 'metinfo');
INSERT INTO met_config VALUES('metconfig_secret_key','','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_member_force','byuqujz','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_editor', 'ueditor', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_text_fonts', '../public/fonts/Cantarell-Regular.ttf', '','0','0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_smsprice','0.1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_sms_token', '', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_sms_url', 'https://u.mituo.cn/api/sms', '', '0', '0', 'metinfo');

#SEO-siteMap
INSERT INTO met_config VALUES('metconfig_sitemap_lang','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_sitemap_not2','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_sitemap_not1','0','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_sitemap_txt','0','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_sitemap_xml','1','','0','0','metinfo');

#版权控制配置
INSERT INTO met_config VALUES('metconfig_agents_logo_login','../public/images/login-logo.png','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_logo_index','../public/images/logo.png','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_img','../public/images/metinfo.gif','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_copyright_foot','Powered by <b><a href=https://www.metinfo.cn target=_blank title=CMS>MetInfo $metcms_v</a></b> &copy;2008-$m_now_year &nbsp;<a href=https://www.mituo.cn target=_blank title=米拓建站>mituo.cn</a>','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_copyright_foot1','本站基于 <b><a href=https://www.metinfo.cn target=_blank title=米拓建站>米拓企业建站系统 $metcms_v</a></b> 搭建','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_copyright_foot2','技术支持：<b><a href=https://www.mituo.cn target=_blank title=米拓建站>米拓建站 $metcms_v</a></b>','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_copyright_nofollow', '0', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES('metconfig_copyright_type','1','','0','0','metinfo');
#版权控制
INSERT INTO met_config VALUES('metconfig_agents_type','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_linkurl','https://www.mituo.cn','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_pageset_logo','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_update','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_code','','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_backup','metinfo','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_sms','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_app','1','','0','0','metinfo');
INSERT INTO met_config VALUES('metconfig_agents_metmsg', '1', '', '0', '0', 'metinfo');
#代理信息
INSERT INTO met_config VALUES('metconfig_agents_thanks','感谢使用 Metinfo','','0','0','cn-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_name','MetInfo|米拓企业建站系统','','0','0','cn-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_copyright','长沙米拓信息技术有限公司（MetInfo Inc.）','','0','0','cn-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_depict_login','MetInfo','','0','0','cn-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_thanks','thanks use Metinfo','','0','0','en-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_name','Metinfo CMS','','0','0','en-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_copyright','China Changsha MetInfo Information Co., Ltd.','','0','0','en-metinfo');
INSERT INTO met_config VALUES('metconfig_agents_depict_login','Metinfo Build marketing value corporate website','','0','0','en-metinfo');

#后台栏目
SET IDENTITY_INSERT met_admin_column ON;
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('1', 'lang_administration', 'manage', '0', '1301', '1', '0', 'manage', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('2', 'lang_htmColumn', 'column', '0', '1201', '1', '1', 'column', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('3', 'lang_feedback_interaction', '', '0', '1202', '1', '2', 'feedback-interaction', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('4', 'lang_seo_set_v6', 'seo', '0', '1404', '1', '3', 'seo', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('5', 'lang_appearance', 'app/metconfig_template', '0', '1405', '1', '4', 'template', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('6', 'lang_myapp', 'myapp', '0', '1505', '1', '5', 'application', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('7', 'lang_the_user', '', '0', '1506', '1', '6', 'user', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('8', 'lang_safety', '', '0', '1200', '1', '7', 'safety', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('9', 'lang_multilingual', 'language', '0', '1002', '1', '8', 'multilingualism', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('10', 'lang_unitytxt_39', '', '0', '1100', '1', '9', 'setting', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('11', 'cooperation_platform', 'partner', '0', '1508', '1', '10', 'partner', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('21', 'lang_mod8', 'feed_feedback_8', '3', '1509', '2', '0', 'feedback', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('22', 'lang_mod7', 'feed_message_7', '3', '1510', '2', '1', 'message', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('23', 'lang_mod6', 'feed_job_6', '3', '1511', '2', '2', 'recruit', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('24', 'lang_customerService', 'online', '3', '1106', '2', '3', 'online', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('25', 'lang_indexlink', 'link', '4', '1406', '2', '0', 'link', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('26', 'lang_member', 'user', '7', '1601', '2', '0', 'member', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('27', 'lang_managertyp2', 'admin/user', '7', '1603', '2', '1', 'administrator', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('28', 'lang_safety_efficiency', 'safe', '8', '1004', '2', '0', 'safe', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('29', 'lang_data_processing', 'databack', '8', '1005', '2', '1', 'databack', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('30', 'lang_upfiletips7', 'webset', '10', '1007', '2', '0', 'information', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('31', 'lang_indexpic', 'imgmanage', '10', '1003', '2', '1', 'picture', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('32', 'lang_banner_manage', 'banner', '10', '1604', '2', '2', 'banner', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('33', 'lang_the_menu', 'menu', '10', '1605', '2', '3', 'bottom-menu', '', '1');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('34', 'lang_checkupdate', 'update', '37', '1104', '2', '4', 'update', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('35', 'lang_appinstall', 'appinstall', '6', '1800', '2', '0', 'appinstall', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('36', 'lang_dlapptips6', 'appuninstall', '6', '1801', '2', '0', 'appuninstall', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('37', 'lang_top_menu', 'top_menu', '0', '1900', '1', '0', 'top_menu', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('38', 'lang_clearCache', 'clear_cache', '37', '1901', '2', '0', 'clear_cache', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('39', 'lang_funcCollection', 'function_complete', '37', '1902', '2', '0', 'function_complete', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('40', 'lang_environmental_test', 'environmental_test', '37', '1903', '2', '0', 'environmental_test', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('41', 'lang_navSetting', 'navSetting', '6', '1904', '2', '0', 'navSetting', '', '0');
INSERT INTO met_admin_column (id, name, url, bigclass, field, type, list_order, icon, info, display) VALUES ('42', 'lang_style_settings', 'style_settings', '5', '1905', '2', '0', 'style_settings', '', '0');
SET IDENTITY_INSERT met_admin_column OFF;

#后台语言
INSERT INTO met_lang_admin VALUES ( '简体中文', '1', '1', 'cn', 'cn', '', 'cn');
INSERT INTO met_lang_admin VALUES ( 'English', '1', '2', 'en', 'en', '', 'en');

#管理员
INSERT INTO met_admin_array VALUES('管理员','metinfo','1','metinfo','0','10000','256','2','metinfo','metinfo');

#模板
INSERT INTO met_skin_table VALUES('metv75','metv75','MetInfo v7.5.0正式版新推出一套全新精致免费模板！','0','');
INSERT INTO met_otherinfo VALUES('NOUSER','2147483647','','','','','','','','','','','','','','','','','metinfo');