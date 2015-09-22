<?php
// 函数库

/**
 * 打印调试
 */
function p ($var)
{
  if (is_bool($var)) {
    var_dump($var);
  } else if (is_null($var)) {
    var_dump(NULL);
  } else {
    echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
  }
}

/**
 * 生成url
 * @param  string $str 控制器/方法
 */
function url ($controller,$action,$other = '')
{
  $res = 'http://' . __ROOT__ . '?c=' . $controller . '&a=' . $action . $other;
  return $res;
}

/**
 * 获得资源路径
 */
function asset($file)
{
  return 'http://' . dirname(__ROOT__) . DIRECTORY_SEPARATOR . $file;
}

/**
 * 跳转
 */
function go ($url,$time = 0)
{
  if ($time != 0) {
    header("refresh:{$time};url={$url}");
  } else {
    header("Location: {$url}");
  }
}

/**
 * 获得上个页面的地址
 */
function history_url ()
{
  return $_SERVER['HTTP_REFERER'];
}

/**
 * 加密
 */
function md6 ($str)
{
  $str = $str.'dawniii';
  return md5($str);
}

/**
 * 链接数据库
 */
function db ()
{
  $config = require APP_PATH . DIRECTORY_SEPARATOR .'config.php';
  return Db::getInstance($config);
}

/**
 * 判断用户是否登陆
 */
function is_login()
{
  //判断是否登陆
  if (empty($_SESSION['uid']) || empty($_SESSION['email'])) {
    return false;
  } else {
    return true;
  }
}

/**
 * 判断管理员是否登陆
 */
function is_admin_login()
{
  //判断是否登陆
  if (empty($_SESSION['admin_uid']) || empty($_SESSION['admin_email'])) {
    return false;
  } else {
    return true;
  }
}

/**
 * 时间戳转换成00:00:00
 */
function data_to_ymd_num($time)
{
  return strtotime(date('Y-m-d',$time));
}

/**
 * 比特转换G
 */
function bit_to_g($bit)
{
  $res = $bit / 1024 / 1024 / 1024 / 8;
  return $res;
}








