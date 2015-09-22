<?php

require CONTROLLERS_PATH . DIRECTORY_SEPARATOR . 'MemberCommonController.php';

class MemberController extends MemberCommonController {

  public function index() {
    //获得用户信息
    $db = db();
    $userData = $db->query('select * from member where id=?',array($_SESSION['uid']));
    $userData = $userData->fetch();

    //获得上网信息
    $db = db();
    $email = $userData['email'];
    //统计条数
    $sql = "select count(*) as count from user where email=?";
    $res = $db->query($sql,array($email));
    $count = $res->fetch();
    $count = $count['count'];

    //分页
    $page = new Page($count,10);
    $show = $page->showpage();
    $limit= $page->limit();

    //列表数据
    $sql = "select * from user where email=? order by id desc {$limit}";
    $res = $db->query($sql,array($email));
    $rows = $res->fetchAll();

    $this->assign('page',$show);
    $this->assign('list',$rows);
    $this->assign('userData',$userData);
    $this->display('member/index');
  }

  /**
   * 重置运行密码
   */
  public function changeRunpwd()
  {
    if (!IS_POST) {
      die('非法请求');
    }

    $email = $_POST['email'];
    $id = (int)$_POST['id'];

    //新密码
    $passwd = mt_rand(10000,19999);

    $db = db();
    $sql = "update user set passwd=? where id=? and email=?";
    $res = $db->query($sql,array($passwd,$id,$email));
    $row = $res->rowCount();
    if ($row) {
      $this->error('重置密码成功');
    } else {
      $this->error('重置密码失败');
    }
  }

  /**
   * 退出登陆
   */
  public function logout() {
    session_unset();
    session_destroy();
    go(url('index','index'));
  }
}