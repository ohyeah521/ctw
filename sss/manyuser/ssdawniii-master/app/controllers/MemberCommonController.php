<?php

class MemberCommonController extends Controller {

  public function __construct()
  {
    parent::__construct();

    //判断是否登陆
    $is_login = is_login();
    if (!$is_login) {
      $this->error('您还没有登陆',url('index','index'));
    }
  }
}