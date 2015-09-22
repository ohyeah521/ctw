<?php

class LoginController extends Controller {

  /**
   * 用户登陆验证
   */
  public function store()
  {
    if (!IS_POST) {
      $this->error('非法请求',url('index','index'));
    }
    $email = $_POST['email'];
    $pass  = md6($_POST['pass']);

    //判断邮箱格式
    $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$is_email) {
      $this->error('邮箱格式错误');
    }

    //判断用户名密码
    $db = db();
    $res = $db->query("select * from member where email=? and pass=?",array($email,$pass));
    $row = $res->rowCount();
    if ($row) {
      $data = $res->fetch();
      //保存session
      $_SESSION['uid'] = $data['id'];
      $_SESSION['email'] = $data['email'];
      //保存最后登陆时间
      $db->query("update member set last_login_time=? where id=?",array(time(),$data['id']));
      go(url('member','index'));
    } else {
      $this->error('用户名或密码错误');
    }
  }

  /**
   * 管理员登陆
   */
  public function admin()
  {
    if (is_admin_login()) {
      go(url('admin','index'));
    }
    $this->display('admin/login');
  }

  public function admin_store()
  {
    if (!IS_POST) {
      $this->error('非法请求',url('index','index'));
    }
    $email = $_POST['email'];
    $pass  = md6($_POST['pass']);
    $code  = $_POST['code'];

    //判断验证码是否正确
    if (!isset($_SESSION['code']) || $_SESSION['code'] != $code) {
      $this->error('验证码错误');
    }

    //判断邮箱格式
    $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$is_email) {
      $this->error('邮箱格式错误');
    }

    //判断用户名密码
    $db = db();
    $res = $db->query("select * from member where email=? and pass=? and is_admin=1",array($email,$pass));
    $row = $res->rowCount();
    if ($row) {
      $data = $res->fetch();
      //保存session
      $_SESSION['admin_uid'] = $data['id'];
      $_SESSION['admin_email'] = $data['email'];
      //保存最后登陆时间
      $db->query("update member set last_login_time=? where id=?",array(time(),$data['id']));
      go(url('admin','index'));
    } else {
      $this->error('用户名或密码错误');
    }
  }
}