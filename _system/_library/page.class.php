<?php
/**
 * @author xiaojh@
 * @date 2012-07-24
 * use:
 * $page = new lib_page(10, 100, 2, 9, "test.php?page=");
 * $page->showPageHtml();
 * 
 * @modify xiaojh@  2012-07-24
 * every page max: $_maxNums;
 * 
 *   
 */
class lib_page {
	/**
	 * 每页显示条数
	 * @var int
	 */
	private $_pageCount = 0;
	
	/**
	 * 总条目数
	 * @var int
	 */
	private $_num		= 0;
	
	/**
	 * 分页总数
	 * @var int
	 */
	private $_pageNum	= 0;
	
	/**
	 * 当前显示的页数
	 * @var int
	 */
	private $_currentPage = 0;
	
	
	/**
	 * 分页链接
	 * @var string
	 */
	private $_pageLink 	= '';
	
	/**
	 * 每次显示数字[1][2][3][4]..数量
	 * @var int
	 */
	private $_showNum	= 0;
	
    
	/**
	 * 最大显示总数
	 * @var int
	 */
	private $_maxNums	= 10000;    
    
	/**
	 * 初始化分页
	 * @param int $pageCount 每页显示条目：10
	 * @param int $nums		        总记录条数：1024
	 * @param int $currentPage 当前的页码：8
	 * @param int $showNum	   每次显示数字的数量：10
	 * @param string $pageLink 分页的连接:如test.php?page=
	 */
	public function __construct($pageCount, $nums, $currentPage, $showNum, $pageLink) {
	   
            $this->_pageCount 	= intval($pageCount);
            $this->_num         = intval($nums);

            $this->_showNum	= intval($showNum);
            $this->_pageNum	= ceil($this->_num / $pageCount);
            $this->_pageLink 	= $pageLink;

            if (!$currentPage) {
                $this->_currentPage = 1;
            } else {
                $this->_currentPage = intval($currentPage) > $this->_pageNum ? $this->_pageNum : intval($currentPage);
            }
	}
	
	/**
	 * 显示分页
	 * @return string
	 */
	public function showPageHtml() {
		
            if ($this->_num==0) return '';

            $pageStr  = '<ul class="pagesNew clearfix">';
            if ($this->_num > $this->_maxNums) {
                $this->_num  =  $this->_maxNums;
                $pageStr .= "<li class=\"page_count\"><em>共搜索到超过{$this->_maxNums}条记录</em></li>";

            } else {
                $pageStr .= "<li class=\"page_count\"><em>共搜索到{$this->_num}条记录</em></li>";
            }
            //$pageStr .= "当前第" . $this->_currentPage . "/" . $this->_pageNum."页 ";

            if ($this->_currentPage > 1) {
                $firstPageUrl = $this->_pageLink . "1";
                $prewPageUrl  = $this->_pageLink . ($this->_currentPage - 1);
                $pageStr .= "<li class=\"first hidden\"><a href='{$firstPageUrl}'>首页</a></li>";
                $pageStr .= "<li class=\"previous hidden\"><a href='{$prewPageUrl}'>上一页</a></li>";
            } else {
                $pageStr .= "<li class=\"first \"><a href=\"javascript:void(0);\">首页</a></li>";
                $pageStr .= "<li class=\"previous \"><a href=\"javascript:void(0);\">上一页</a></li>";
            }

            $a = $this->_initNumPage();
            for ($i=0; $i<count($a); $i++) {
                $s = $a[$i];
                if ($s == $this->_currentPage ) {
                    $pageStr .= "<li class=\"page selected\">".$s."</li>";
                } else {
                    $url = $this->_pageLink . $s;
                    $pageStr .= "<li class=\"page\" ><a href='{$url}'>".$s."</a></li>";
                }
            }

            if ($this->_currentPage < $this->_pageNum) {
                $lastPageUrl = $this->_pageLink . $this->_pageNum;
                $nextPageUrl = $this->_pageLink . ($this->_currentPage + 1);
                $pageStr .= "<li class=\"next\"><a href='{$nextPageUrl}'>下一页</a></li> ";
                $pageStr .= "<li class=\"last\"><a href='{$lastPageUrl}'>尾页</a></li> ";
            } else {
                $pageStr .= "<li class=\"next\"><a href=\"javascript:void(0);\">下一页</a></li>";
                $pageStr .= "<li class=\"last\"><a href=\"javascript:void(0);\">尾页</a></li> ";
            }

            $pageStr .= '</ul>';
            return $pageStr;
	}
	
	/**
	 * 显示分页
	 * @return string
	 * 新版本分页样式
	 */
	public function showPageHtml2() {
            $pageStr  = '<ul>';

            if ($this->_pageNum > 1) {
                $a = $this->_initNumPage();
                for ($i=0; $i<count($a); $i++) {
                    $s = $a[$i];
                    if ($s == $this->_currentPage ) {
                        $pageStr .= "<li class=\"page seleced\">".$s."</li>";
                    } else {
                        $url = $this->_pageLink . $s;
                        $pageStr .= "<li><a href='{$url}'>".$s."</a></li>";
                    }
                }

                if ($this->_currentPage < $this->_pageNum) {
                    $lastPageUrl = $this->_pageLink . $this->_pageNum;
                    $nextPageUrl = $this->_pageLink . ($this->_currentPage + 1);
                    $pageStr .= "<li><a href='{$nextPageUrl}'>下一页</a></li> ";
                } else {
                    $pageStr .= "<li><a href=\"javascript:void(0);\">下一页</a></li>";
                }
                $pageStr .= '<li style="margin-left:10px;">共<span class="orange">' . $this->_pageNum . '</span>页</li>';
            }

            $pageStr .= '</ul>';
            return $pageStr;
	
	}
	
	/**
	 * 用来给建立分页的数组初始化的函数
	 * @return multitype:number
	 */
	private function _initArray() {
            $array = array();

            for ($i=0; $i<$this->_showNum; $i++) {
                $array[$i] = $i;
            }
            return $array;
	}
	
	/**
	 * 初始化分页数字的数组
	 * @return multitype:number
	 */
	private function _initNumPage() {
	// 		var_dump($this->_pageNum);
	// 		var_dump($this->_currentPage);
	// 		var_dump($this->_showNum);
            if($this->_pageNum < $this->_showNum) {
                $current_array = array();
                for ($i=0; $i<$this->_pageNum; $i++) {
                    $current_array[$i] = $i + 1;
                }
            } else {
                $current_array = $this->_initArray();
                if ($this->_currentPage <= 3) {
                    for ($i=0; $i<count($current_array); $i++) {
                        $current_array[$i] = $i + 1;
                    }
                } elseif ($this->_currentPage <= $this->_pageNum 
                                && $this->_currentPage > $this->_pageNum - $this->_showNum + 1 ) {
                    for ($i=0; $i<count($current_array); $i++) {
                        $current_array[$i] = ($this->_pageNum)-($this->_showNum) + 1 + $i;
                    }
                } else {
                    for ($i=0; $i<count($current_array); $i++) {
                        $current_array[$i] = $this->_currentPage - 2 + $i;
                    }
                }
            }

            return $current_array;
	}
	
	/**
	 * 将临时变量置空
	 */
	public function __destruct() {
            unset($pageCount);
            unset($nums);
            unset($currentNum);
            unset($showNum);
            unset($pageLink);
	}
}

