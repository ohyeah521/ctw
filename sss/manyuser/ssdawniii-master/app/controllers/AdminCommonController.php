<?php


class AdminCommonController extends Controller {

  public function __construct()
  {
    parent::__construct();
    $is_admin_login = is_admin_login();
    if (!$is_admin_login) {
      $url = url('index','index');
      $this->error('您还没有登陆',$url);
    }
  }
}