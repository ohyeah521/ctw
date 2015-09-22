<?php

class View{

  protected $data = array();

  public function display ($file)
  {
      extract($this->data);

      include VIEWS_PATH . DIRECTORY_SEPARATOR . $file . '.html';
  }

  public function assign ($key, $value)
  {
      $this->data[$key] = $value;
  }
}