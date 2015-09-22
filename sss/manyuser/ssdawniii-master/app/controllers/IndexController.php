<?php

class IndexController extends Controller {

  public function index()
  {
    $this->display('home/index');
  }

  public function validate()
  {
    $vc = new ValidateCode();  //实例化一个对象
    $vc->doimg();
    $code = $vc->getCode();
    $_SESSION['code'] = $code;
  }
}