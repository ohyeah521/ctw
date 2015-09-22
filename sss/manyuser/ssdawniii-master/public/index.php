<?php
/**
 * 入口文件
 * 作者：dawn
 * 网址：www.dawniii.com
 */

//设置时区
date_default_timezone_set('PRC');

// 定义常量
define('DEBUG',true);
define('PUBLIC_PATH', __DIR__);
define('ROOT_PATH', dirname(PUBLIC_PATH));
define('APP_PATH',ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('CONTROLLERS_PATH',APP_PATH . DIRECTORY_SEPARATOR . 'controllers');
define('VIEWS_PATH',APP_PATH . DIRECTORY_SEPARATOR . 'views');
define('SYSTEM_PATH',ROOT_PATH . DIRECTORY_SEPARATOR . 'system');
define('TOOLS_PATH',SYSTEM_PATH . DIRECTORY_SEPARATOR . 'tools');
define('__ROOT__', $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
  define('IS_POST',true);
} else {
  define('IS_POST',false);
}


//启动
require SYSTEM_PATH . DIRECTORY_SEPARATOR . 'dawniii.php';

