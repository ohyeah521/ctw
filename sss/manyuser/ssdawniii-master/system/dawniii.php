<?php

//载入异常类
if (DEBUG) {
  require TOOLS_PATH . DIRECTORY_SEPARATOR . 'exception' . DIRECTORY_SEPARATOR . 'Error.php';
  Error::getInstance();
} else {
  error_reporting(0);
}

//开启session
session_start();

//载入函数库
require SYSTEM_PATH . DIRECTORY_SEPARATOR . 'functions.php';

//载入pdo
require TOOLS_PATH . DIRECTORY_SEPARATOR . 'pdo' . DIRECTORY_SEPARATOR . 'Db.php';

//载入分页
require TOOLS_PATH . DIRECTORY_SEPARATOR . 'Page.php';

//载入控制器基类
require TOOLS_PATH . DIRECTORY_SEPARATOR . 'Controller.php';

//加载验证码类
require TOOLS_PATH . DIRECTORY_SEPARATOR . 'ValidateCode.php';

//加载路由
require TOOLS_PATH . DIRECTORY_SEPARATOR . 'Route.php';
$route = new Route();
$route->run();