<?php

require CONTROLLERS_PATH . DIRECTORY_SEPARATOR . 'AdminCommonController.php';

class AdminController extends AdminCommonController {

  /**
   * 用户列表
   */
  public function index()
  {
    //搜索条件
    $email = empty($_GET['email']) ? '' : $_GET['email'];

    //获得用户列表
    $db = db();
    //统计条数
    if (empty($email)) {
      $sql = "select count(*) as count from member where is_admin!=1";
      $res = $db->query($sql);
    } else {
      $sql = "select count(*) as count from member where is_admin!=1 and email=?";
      $res = $db->query($sql,array($email));
    }
    $count = $res->fetch();
    $count = $count['count'];

    //分页
    $page = new Page($count,10);
    $show = $page->showpage();
    $limit= $page->limit();

    //列表数据
    if (empty($email)) {
      $sql = "select member.*,count(user.email) as count from member left join user on member.email=user.email where member.is_admin!=1 group by member.email order by member.id desc {$limit}";
      $res = $db->query($sql);
    } else {
      $sql = "select member.*,count(user.email) as count from member left join user on member.email=user.email where member.is_admin!=1 and member.email=? group by member.email order by member.id desc {$limit}";
      $res = $db->query($sql,array($email));
    }
    $rows = $res->fetchAll();

    $this->assign('page',$show);
    $this->assign('list',$rows);
    $this->assign('count',$count);
    $this->display('admin/index');
  }

  /**
   * 运行列表
   */
  public function run()
  {
    //搜索条件
    $email = empty($_GET['email']) ? '' : $_GET['email'];

    //获得用户列表
    $db = db();
    //统计条数
    if (empty($email)) {
      $sql = "select count(*) as count from user";
      $res = $db->query($sql);
    } else {
      $sql = "select count(*) as count from user where email=?";
      $res = $db->query($sql,array($email));
    }
    $count = $res->fetch();
    $count = $count['count'];

    //分页
    $page = new Page($count,10);
    $show = $page->showpage();
    $limit= $page->limit();

    //列表数据
    if (empty($email)) {
      $sql = "select * from user order by id desc {$limit}";
      $res = $db->query($sql);
    } else {
      $sql = "select * from user where email=? order by id desc {$limit}";
      $res = $db->query($sql,array($email));
    }
    $rows = $res->fetchAll();
    // p($rows);die;

    $this->assign('page',$show);
    $this->assign('list',$rows);
    $this->assign('count',$count);
    $this->display('admin/run');
  }

  /**
   * 开通用户
   */
  public function open()
  {
    $this->display('admin/open');
  }

  public function open_store()
  {
    if (!IS_POST) {
      die('非法请求');
    }

    $email = $_POST['email'];
    $passwd = mt_rand(10000,19999);
    $month = (int)$_POST['month'];

    //开通时间
    $start_time = data_to_ymd_num(time());
    $end_time = $start_time + ($month * 30 * 86400);
    //端口号
    $port = $this->getPort();

    $db = db();
    $sql = "INSERT INTO `user` VALUES ('', ?, ?, '0', '0', '0', '687194767360', ?, '1','1',?, ?)";
    $res = $db->query($sql,array($email,$passwd,$port,$start_time,$end_time));
    $falg = $res->rowCount();
    if ($falg) {
      //同步服务器 TODO
      $this->error('开通成功');
    } else {
      $this->error('开通失败');
    }
  }

  /**
   * 生成不重复的端口号
   */
  private function getPort()
  {
    $db = db();
    $port = mt_rand(50000,59999);
    $sql = "select * from user where port=?";
    $res = $db->query($sql,array($port));
    $row = $res->rowCount();
    if ($row == '0') {
      return $port;
    } else {
      $this->getPort();
    }
  }

  /**
   * 冻结
   */
  public function lock()
  {
    if (!IS_POST) {
      die('非法请求');
    }

    $id = (int)$_POST['id'];
    $email = $_POST['email'];

    $db = db();
    $sql = "update user set enable='0' where id=? and email=?";
    $res = $db->query($sql,array($id,$email));
    $row = $res->rowCount();
    if ($row) {
      $this->error('锁定成功');
    } else {
      $this->error('锁定失败');
    }
  }

  /**
   * 解除冻结
   */
  public function unlock()
  {
    if (!IS_POST) {
      die('非法请求');
    }

    $id = (int)$_POST['id'];
    $email = $_POST['email'];

    $db = db();
    $sql = "update user set enable='1' where id=? and email=?";
    $res = $db->query($sql,array($id,$email));
    $row = $res->rowCount();
    if ($row) {
      $this->error('解除锁定成功');
    } else {
      $this->error('解除锁定失败');
    }
  }

  /**
   * 退胡登陆
   */
  public function logout()
  {
    session_unset();
    session_destroy();
    go(url('index','index'));
  }
}