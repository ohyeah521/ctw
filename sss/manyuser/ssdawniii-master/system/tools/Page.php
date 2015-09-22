<?php
class Page {
  private $total;     //总记录
  private $pagesize;    //每页显示多少条
  private $limit;       //limit
  private $page;      //当前页码
  private $pagenum; //总页码
  private $url;       //地址
  private $bothnum = 2;  //两边保持数字分页的量

  public function __construct($total,$pagesize)
  {
    $this->total = $total;
    $this->pagesize = $pagesize;
    $this->page = $this->getPageNum();
    $this->pagenum = ceil($this->total / $this->pagesize);
    $this->url = $this->setUrl();
    $this->limit = "LIMIT ".($this->page-1)*$this->pagesize.",$this->pagesize";
  }

  //获得当前页码
  private function getPageNum()
  {
    $pageNum = empty($_GET['page']) ? 1 : (int)$_GET['page'];
    if ($pageNum == 0) {
      $pageNum = 1;
    }
    return $pageNum;
  }

  //获取地址
  private function setUrl() {
    $_url = $_SERVER["REQUEST_URI"];
    $_par = parse_url($_url);
    if (isset($_par['query'])) {
      parse_str($_par['query'],$_query);
      unset($_query['page']);
      $_url = $_par['path'].'?'.http_build_query($_query);
    }
    return $_url;
  }

  //首页
  private function first() {
    if ($this->page > $this->bothnum+1) {
      return '<li><a href="'.$this->url.'">1</a></li><li><a>...</a></li>';
    }
  }

  //上一页
  private function prev() {
    if ($this->page == 1) {
      return '';
    }
    return '<li><a href="'.$this->url.'&page='.($this->page-1).'"><span>&laquo;</span></a></li>';
  }

  //下一页
  private function next() {
    if ($this->page == $this->pagenum) {
      return '';
    }
    return '<li><a href="'.$this->url.'&page='.($this->page+1).'"><span>&raquo;</span></a></li>';
  }

  //尾页
  private function last() {
    if ($this->pagenum - $this->page > $this->bothnum) {
      return '<li><a>...</a></li><li><a href="'.$this->url.'&page='.$this->pagenum.'">'.$this->pagenum.'</a></li>';
    }
  }

  private function pageList() {
    $_pagelist = '';
    for ($i=$this->bothnum;$i>=1;$i--) {
      $_page = $this->page-$i;
      if ($_page < 1) continue;
      $_pagelist .= '<li><a href="'.$this->url.'&page='.$_page.'">'.$_page.'</a></li>';
    }
    $_pagelist .= '<li class="active"><a href="javascript:;">'.$this->page.'</a></li>';
    for ($i=1;$i<=$this->bothnum;$i++) {
      $_page = $this->page+$i;
      if ($_page > $this->pagenum) break;
      $_pagelist .= '<li><a href="'.$this->url.'&page='.$_page.'">'.$_page.'</a></li>';
    }
    return $_pagelist;
  }

  //分页信息
  public function showpage() {
    $_page = '<nav><ul class="pagination">';
    $_page .= $this->prev();
    $_page .= $this->first();
    $_page .= $this->pageList();
    $_page .= $this->last();
    $_page .= $this->next();
    $_page .= '</ul></nav>';
    return $_page;
  }

  //获得limit
  public function limit() {
    return $this->limit;
  }
}











