<?php

class Controller {

  protected $view;

  public function __construct()
  {
    include TOOLS_PATH . DIRECTORY_SEPARATOR . 'View.php';
    $this->view = new View();
  }

  protected function assign($key, $value)
  {
    $this->view->assign($key, $value);
  }

  protected function display($file)
  {
    $this->view->display($file);
  }

  protected function error($msg,$url = '',$time = 3)
  {
    if (empty($url)) {
      $url = history_url();
    }
    $this->assign('msg',$msg);
    $this->assign('time',$time);
    $this->display('jump/error');
    go($url,$time);
    die;
  }
}