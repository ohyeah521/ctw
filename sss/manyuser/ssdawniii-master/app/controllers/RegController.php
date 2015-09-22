<?php

class RegController extends Controller {

  public function store()
  {
    if (!IS_POST) {
      $this->error('非法请求',url('index','index'));
    }
    $email = $_POST['email'];
    $pass = md6($_POST['pass']);
    $code = strtolower($_POST['code']);

    //判断验证码是否正确
    if (!isset($_SESSION['code']) || $_SESSION['code'] != $code) {
      $this->error('验证码错误');
    }
    //判断邮箱格式
    $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$is_email) {
      $this->error('邮箱格式错误');
    }

    //判断邮箱是否存在
    $db = db();
    $is_have_email = $db->query("select * from member where email=?",array($email));
    $is_have_email = $is_have_email->rowCount();
    if ($is_have_email) {
      $this->error('邮箱已存在');
    }

    //插入数据库
    $res = $db->query("insert into member(email,pass,reg_time) values (?,?,?)", array($email,$pass,time()));
    $res = $res->rowCount();
    if ($res) {
      $this->error('注册成功');
    }
  }
}