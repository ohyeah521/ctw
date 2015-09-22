<?php

class Db {

  private static $_instance;

  private $pdo;

  private $stmt = null;

  /**
   * 初始化pdo连接mysql
   * @param $config 配置项
   */
  private function __construct($config)
  {
    try{
      //连接数据库
      $this->pdo = new \PDO("mysql:host={$config['host']};dbname={$config['database']};port={$config['port']}", $config['username'], $config['password']);
    } catch(\PDOException $e) {
      throw new Exception($e->getMessage());
    }
    //设置字符集
    $this->pdo->exec("SET NAMES {$config['charset']}");
  }

  /**
   * 获取实例
   * @param $config 数据库配置项
   * @return mixed
   */
  public static function getInstance($config)
  {
    if (self::$_instance instanceof self) {
      return self::$_instance;
    } else {
      self::$_instance = new self($config);
      return self::$_instance;
    }
  }

  public function query($sql,$arr = array())
  {
    if (!empty($this->stmt)) {
      $this->stmt = null;
    }
    $this->stmt = $this->pdo->prepare($sql);
    if (empty($arr)) {
      $this->stmt->execute();
    } else {
      $this->stmt->execute($arr);
    }
    return $this->stmt;
  }
}









