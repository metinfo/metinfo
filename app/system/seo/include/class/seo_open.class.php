<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 基础标签类
 */

class seo_open
{
    public function __construct()
    {
    }

    public function getRewrite()
    {
        $nowpath = explode('/', $_SERVER['PHP_SELF']);
        $cunt = count($nowpath) - 2;
        $metbase = '';
        for ($i = 0; $i < $cunt; ++$i) {
            $metbase .= $nowpath[$i] . '/';
        }

        if (stristr($_SERVER['SERVER_SOFTWARE'], 'Apache')) {
            $rule = self::createApacheHttpdurl($metbase);
            $fname = '.htaccess';
        } elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'nginx')) {
            $rule = self::createNginxHttpdurl($metbase);
            $fname = '.htaccess';
        } elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'IIS')) {
            $rule = self::createIISHttpdurl();
            $fname = 'web.config';
        }

        $redata = array();
        $redata['rule'] = $rule;
        $redata['fname'] = $fname;
        return $redata;
    }

    public function buildRewrite()
    {
        $rewrite = self::getRewrite();
        $rewrite_path = PATH_WEB . $rewrite['fname'];
        file_put_contents($rewrite_path, $rewrite['rule']);
    }

    public function delRewrite()
    {
        //删除重写文件
        if (file_exists(PATH_WEB . '.htaccess')) {
            @unlink(PATH_WEB . '.htaccess');
        }
        if (file_exists(PATH_WEB . 'web.config')) {
            @unlink(PATH_WEB . 'web.config');
        }
        if (file_exists(PATH_WEB . 'httpd.ini')) {
            @unlink(PATH_WEB . 'httpd.ini');
        }
    }

    public function delStatic()
    {
        global $_M;
        if ($_M['lang'] == $_M['config']['met_index_type'] && file_exists(PATH_WEB . 'index.htm')) {
            @unlink(PATH_WEB . 'index.htm');
        }
        if ($_M['lang'] == $_M['config']['met_index_type'] && file_exists(PATH_WEB . 'index.html')) {
            @unlink(PATH_WEB . 'index.html');
        }
        if (file_exists(PATH_WEB . 'index_' . $_M['lang'] . '.htm')) {
            @unlink(PATH_WEB . 'index_' . $_M['lang'] . '.htm');
        }
        if (file_exists(PATH_WEB . 'index_' . $_M['lang'] . '.html')) {
            @unlink(PATH_WEB . 'index_' . $_M['lang'] . '.html');
        }
    }

    /**
     * Apache伪静态规则.
     * @param string $metbase
     * @return string
     */
    protected function createApacheHttpdurl($metbase = '')
    {
        global $_M;
        $htaccess = 'RewriteEngine on' . "\n";
        $htaccess .= '# ' . $_M['word']['seohtaccess1'] . "\n";
        $htaccess .= 'Options -Indexes' . "\n";
        $htaccess .= 'RewriteBase ' . $metbase . "\n";
        $htaccess .= 'RewriteRule ^(.*)\.(asp|aspx|asa|asax|dll|jsp|cgi|fcgi|pl)(.*)$ 404.html' . "\n";
        $htaccess .= 'RewriteRule index\.php/\w+ 404.html' . "\n";
        $htaccess .= '# Rewrite ' . $_M['word']['seohtaccess1'] . "\n";

        $htaccess .= 'RewriteRule ^index-([a-zA-Z0-9_^\x00-\xff]+).html$ index.php?lang=$1&pseudo_jump=1' . "\n";
        $htaccess .= 'RewriteRule ^public/plugins/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$ public/plugins/ueditor/$1.htm [L]' . "\n";
        $htaccess .= 'RewriteRule ^public/plugins/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$ public/plugins/ueditor/$1.html [L]' . "\n";
        $htaccess .= 'RewriteRule ^app/app/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$ app/app/ueditor/$1.html [L]' . "\n";
        $htaccess .= 'RewriteRule ^wap/([a-zA-Z0-9_^\x00-\xff]+).html$ wap/$1.html [L]' . "\n";

        //列表页
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([0-9_]+)-([a-zA-Z0-9_^\x00-\xff]+).html$ $1/index.php?lang=$4&metid=$2&list=1&page=$3&pseudo_jump=1' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z0-9_^\x00-\xff]+).html$ $1/index.php?lang=$3&metid=$2&list=1&pseudo_jump=1' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([0-9_]+).html$ $1/index.php?metid=$2&list=1&page=$3&pseudo_jump=1' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+).html$ $1/index.php?metid=$2&list=1&pseudo_jump=1' . "\n";

        //详情页
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9_^\x00-\xff]+)/([a-zA-Z0-9_^\x00-\xff^\x00-\xff]+)-([a-zA-Z0-9_^\x00-\xff]+).html$ $1/index.php?lang=$3&metid=$2&pseudo_jump=1' . "\n";
        $htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-f' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9_^\x00-\xff]+)/([a-zA-Z0-9_^\x00-\xff]+).html$ $1/index.php?metid=$2&pseudo_jump=1' . "\n";

        //search
        $htaccess .= 'RewriteRule ^search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$ search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&lang=$2&page=$3' . "\n";
        $htaccess .= 'RewriteRule ^search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)$ search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&lang=$2' . "\n";
        $htaccess .= 'RewriteRule ^search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+)$ search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&page=$2' . "\n";
        $htaccess .= 'RewriteRule ^search/tag/([a-zA-Z0-9_^\x00-\xff]+)$ search/search.php?class1=&class2=&class3=&search=tag&searchword=$1' . "\n";

        //tags
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$ $1/index.php?search=tag&content=$2&lang=$3&page=$4' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)$ $1/index.php?search=tag&content=$2&lang=$3' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+)$ $1/index.php?search=tag&content=$2&page=$3' . "\n";
        $htaccess .= 'RewriteRule ^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)$ $1/index.php?search=tag&content=$2' . "\n";

        $str = load::plugin('doseourl', 1, array('str' => '', 'type' => 'apache')); //加载插件
        $htaccess = $htaccess . $str;
        $httpd = $htaccess;

        return $httpd;
    }

    /**
     * Nginx伪静态规则
     * @param string $metbase
     * @return string
     */
    protected function createNginxHttpdurl($metbase = '')
    {
        $htaccess = 'if (-f $request_filename){'. "\n";
        $htaccess .= 'rewrite (.*) $1 break;'. "\n";
        $htaccess .= '}' . "\n";

        $htaccess .= 'rewrite ^'.$metbase.'index-([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'index.php?lang=$1&pseudo_jump=1;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'public/plugins/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'public/plugins/ueditor/$1.htm last;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'public/plugins/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'public/plugins/ueditor/$1.html last;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'app/app/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'app/app/ueditor/$1.html last;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'wap/([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'wap/$1.html last;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'(.*)\.(asp|aspx|asa|asax|dll|jsp|cgi|fcgi|pl)(.*)$ '.$metbase.'404.html;' . "\n";
        $htaccess .= 'rewrite index\.php/\w+ '.$metbase.'404.html;' . "\n";

        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([0-9_]+)-([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'$1/index.php?lang=$4&metid=$2&list=1&page=$3&pseudo_jump=1;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'$1/index.php?lang=$3&metid=$2&list=1&pseudo_jump=1;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([0-9_]+).html$ '.$metbase.'$1/index.php?metid=$2&list=1&page=$3&pseudo_jump=1;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'$1/index.php?metid=$2&list=1&pseudo_jump=1;' . "\n";

        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9_^\x00-\xff]+)/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'$1/index.php?lang=$3&metid=$2&pseudo_jump=1;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9_^\x00-\xff]+)/([a-zA-Z0-9_^\x00-\xff]+).html$ '.$metbase.'$1/index.php?metid=$2&pseudo_jump=1;' . "\n";

        $htaccess .= 'rewrite ^'.$metbase.'search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$ '.$metbase.'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&lang=$2&page=$3;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)$ '.$metbase.'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&lang=$2;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+)$ '.$metbase.'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&page=$2;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'search/tag/([a-zA-Z0-9_^\x00-\xff]+)$ '.$metbase.'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1;' . "\n";

        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$ '.$metbase.'$1/index.php?search=tag&content=$2&lang=$3&page=$4;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)$ '.$metbase.'$1/index.php?search=tag&content=$2&lang=$3;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+)$ '.$metbase.'$1/index.php?search=tag&content=$2&page=$3;' . "\n";
        $htaccess .= 'rewrite ^'.$metbase.'([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)$ '.$metbase.'$1/index.php?search=tag&content=$2;' . "\n";

        $str = load::plugin('doseourl', 1, array('str' => '', 'type' => 'nginx')); //加载插件
        $htaccess = $htaccess . $str;
        $httpd = $htaccess;

        return $httpd;
    }

    /**
     * IIS伪静态规则
     * @param string $metbase
     * @return string
     */
    protected function createIISHttpdurl($metbase = '')
    {
        $web = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $web .= '<configuration>' . "\n";
        $web .= '<system.webServer>' . "\n";
        $web .= '<rewrite>' . "\n";
        $web .= '<rules>' . "\n";

        $web .= '<rule name="rule1" stopProcessing="true">' . "\n";
        $web .= '<match url="^index-([a-zA-Z0-9_\u4e00-\u9fa5]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="index.php?lang={R:1}&amp;pseudo_jump=1" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule2" stopProcessing="true">' . "\n";
        $web .= '<match url="^/public/plugins/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$" />' . "\n";
        $web .= '<action type="Rewrite" url="/public/plugins/ueditor/$1.html" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule3" stopProcessing="true">' . "\n";
        $web .= '<match url="^/app/app/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html$" />' . "\n";
        $web .= '<action type="Rewrite" url="/app/app/ueditor/$1.html" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule4" stopProcessing="true">' . "\n";
        $web .= '<match url="^/wap/([a-zA-Z0-9_^\x00-\xff]+).html$" />' . "\n";
        $web .= '<action type="Rewrite" url="/wap/$1.html" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule5" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9_\u4e00-\u9fa5]+)/list-([a-zA-Z0-9_\u4e00-\u9fa5]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php?metid={R:2}&amp;list=1&amp;pseudo_jump=1" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule6" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9_\u4e00-\u9fa5]+)/list-([a-zA-Z0-9_\u4e00-\u9fa5]+)-([0-9_]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php?metid={R:2}&amp;list=1&amp;page={R:3}&amp;pseudo_jump=1" />' . "\n";

        $web .= '</rule>' . "\n";
        $web .= '<rule name="rule7" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9_\u4e00-\u9fa5]+)/list-([a-zA-Z0-9_\u4e00-\u9fa5]+)-([a-zA-Z0-9_\u4e00-\u9fa5]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php?lang={R:3}&amp;metid={R:2}&amp;list=1&amp;pseudo_jump=1" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule8" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9_\u4e00-\u9fa5]+)/list-([a-zA-Z0-9_\u4e00-\u9fa5]+)-([0-9_]+)-([a-zA-Z0-9_\u4e00-\u9fa5]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php?lang={R:4}&amp;metid={R:2}&amp;list=1&amp;page={R:3}&amp;pseudo_jump=1" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule9" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9_\u4e00-\u9fa5]+)/([a-zA-Z0-9_\u4e00-\u9fa5]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php?metid={R:2}&amp;pseudo_jump=1" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule10" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9_\u4e00-\u9fa5]+)/([a-zA-Z0-9_\u4e00-\u9fa5]+)-([a-zA-Z0-9_\u4e00-\u9fa5]+).html" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php?lang={R:3}&amp;metid={R:2}&amp;pseudo_jump=1" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule11" stopProcessing="true">' . "\n";
        $web .= '<match url="^search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="search/search.php\?class1=&amp;class2=&amp;class3=&amp;search=tag&amp;searchword={R:1}&amp;lang={R:2}&amp;page={R:3}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule12" stopProcessing="true">' . "\n";
        $web .= '<match url="^search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="search/search.php\?class1=&amp;class2=&amp;class3=&amp;search=tag&amp;searchword={R:1}&amp;lang={R:2}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule13" stopProcessing="true">' . "\n";
        $web .= '<match url="^search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="search/search.php\?class1=&amp;class2=&amp;class3=&amp;search=tag&amp;searchword={R:1}&amp;page={R:2}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule14" stopProcessing="true">' . "\n";
        $web .= '<match url="^search/tag/([a-zA-Z0-9_^\x00-\xff]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="search/search.php\?class1=&amp;class2=&amp;class3=&amp;search=tag&amp;searchword={R:1}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule15" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php\?search=tag&amp;content={R:2}&amp;lang={R:3}&amp;page={R:4}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule16" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php\?search=tag&amp;content={R:2}&amp;lang={R:3}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule17" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php\?search=tag&amp;content={R:2}&amp;page={R:3}" />' . "\n";
        $web .= '</rule>' . "\n";

        $web .= '<rule name="rule18" stopProcessing="true">' . "\n";
        $web .= '<match url="^([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)$" />' . "\n";
        $web .= '<action type="Rewrite" url="{R:1}/index.php\?search=tag&amp;content={R:2}" />' . "\n";
        $web .= '</rule>' . "\n";

        $str = load::plugin('doseourl', 1, array('str' => '', 'type' => 'iis7')); //加载插件
        $web = $web . $str;
        $web .= '</rules>' . "\n";
        $web .= '</rewrite>' . "\n";
        $web .= '</system.webServer>' . "\n";
        $web .= '</configuration>' . "\n";

        return $web;
    }

    /**
     * @param string $metbase
     * @return string
     */
    protected function createHttpdurl($metbase = '')
    {
        $httpd = '[ISAPI_Rewrite]' . "\n";
        $httpd .= '# 3600 = 1 hour' . "\n";
        $httpd .= 'CacheClockRate 3600' . "\n";
        $httpd .= 'RepeatLimit 32' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'index-([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . 'index.php\?lang=$1&pseudo_jump=1' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'public/plugins/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . 'public/plugins/ueditor/$1.html' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'app/app/ueditor/([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . 'app/app/ueditor/$1.html' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'wap/([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . 'wap/$1.html' . "\n";

        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . '$1/index.php\?metid=$2&list=1&pseudo_jump=1' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([0-9_]+).html ' . $metbase . '$1/index.php\?metid=$2&list=1&page=$3&pseudo_jump=1' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . '$1/index.php\?lang=$3&metid=$2&list=1&pseudo_jump=1' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9_^\x00-\xff]+)/list-([a-zA-Z0-9_^\x00-\xff]+)-([0-9_]+)-([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . '$1/index.php\?lang=$4&metid=$2&list=1&page=$3&pseudo_jump=1' . "\n";

        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9_^\x00-\xff]+)/([a-zA-Z0-9_^\x00-\xff^\x00-\xff]+)-([a-zA-Z0-9_^\x00-\xff]+).html ' . $metbase . '$1/index.php\?lang=$3&metid=$2&pseudo_jump=1' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9_^\x00-\xff]+)/([a-zA-Z0-9_^\x00-\xff^\x00-\xff]+).html ' . $metbase . '$1/index.php\?metid=$2&pseudo_jump=1' . "\n";

        $httpd .= 'RewriteRule ' . $metbase . 'search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+)$ ' . $metbase . 'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&lang=$2&page=$3' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+) ' . $metbase . 'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&lang=$2' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'search/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+) ' . $metbase . 'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1&page=$2' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . 'search/tag/([a-zA-Z0-9_^\x00-\xff]+) ' . $metbase . 'search/search.php?class1=&class2=&class3=&search=tag&searchword=$1' . "\n";

        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+)-([0-9]+) ' . $metbase . '$1/index.php?search=tag&content=$2&lang=$3&page=$4' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([a-zA-Z]+) ' . $metbase . '$1/index.php?search=tag&content=$2&lang=$3' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+)-([0-9]+) ' . $metbase . '$1/index.php?search=tag&content=$2&page=$3' . "\n";
        $httpd .= 'RewriteRule ' . $metbase . '([a-zA-Z0-9]+)/tag/([a-zA-Z0-9_^\x00-\xff]+) ' . $metbase . '$1/index.php?search=tag&content=$2' . "\n";

        $str = load::plugin('doseourl', 1, array('str' => '', 'type' => 'iis6')); //加载插件
        $httpd = $httpd . $str;

        return $httpd;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
