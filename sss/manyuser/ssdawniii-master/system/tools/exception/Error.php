<?php

/**
 * 错误异常提示
 */
class Error {

  private static $_instance;

  public $tpl;

  private function __construct()
  {
    // 获取模板文件位置
    $this->tpl = __DIR__ . DIRECTORY_SEPARATOR . 'exception.html';
    // 配置错误级别
    error_reporting(0);
    // 致命错误
    register_shutdown_function([$this,'shutdown']);
    // 错误接管
    set_error_handler([$this,'setErrorHandle']);
    // 异常接管
    set_exception_handler([$this,'setExceptionHandle']);
  }

  /**
   * 获得实例
   */
  public static function getInstance()
  {
    if (self::$_instance instanceof self) {
      return self::$_instance;
    } else {
      self::$_instance = new self();
      return self::$_instance;
    }
  }

  /**
   * 致命错误处理
   */
  public function shutdown()
  {
    if (!error_get_last()) return;
    $e = error_get_last();
    try{
      throw new ErrorException($e['message'], 0, 0, $e['file'], $e['line']);
    }catch(ErrorException $e) {
      ob_end_clean();
      require $this->tpl;
    }
  }

  /**
   * 错误处理
   */
  public function setErrorHandle($errno,$errstr,$errfile,$errline)
  {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }

  /**
   * 异常处理
   */
  public function setExceptionHandle($e)
  {
    ob_end_clean();
    require $this->tpl;
  }
}