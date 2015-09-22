<?php

class Route {

  public $controller;

  public $action;

  public function __construct()
  {
    if (empty($_GET['c'])) {
      $this->controller = 'Index';
    } else {
      $this->controller = ucfirst($_GET['c']);
    }

    if (empty($_GET['a'])) {
      $this->action = 'index';
    } else {
      $this->action = $_GET['a'];
    }
  }

  public function run()
  {
    $controllerName = $this->controller . 'Controller';
    $controllerFile = CONTROLLERS_PATH . DIRECTORY_SEPARATOR . $controllerName . '.php';
    //载入控制器
    require $controllerFile;
    //运行方法
    $action = $this->action;
    $ctlObj = new $controllerName;
    $ctlObj->$action();
  }
}