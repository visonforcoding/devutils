<?php

/**
 * 	作者：戚银
 * 	网名：诺天
 * 	QQ：804603662
 * 	邮箱：804603662@qq.com    noxiaotian@sina.com
 * 	版权声明：完全开源
 * 	作者声明：我把这个分页类暂时命名为史上最强大的分页类，有点夸大，不喜勿喷。
 * 	作者期望：给分页类还存在一些不足之处，如果各位网友有好的意见或改进的方案，都可QQ联系作者予以讨论和交流
 */
/* * *
 *   
 */

namespace Utils;

class Page {

    private $total;   //总数量
    private $limit;   //返回mysql的limit语句
    public $pageStart;  //开始的数值
    private $pageStop;  //结束的数值
    private $pageNumber;         //显示分页数字的数量
    private $page;   //当前页
    public $pageSize;  //每页显示的数量
    private $pageToatl;  //分页的总数量
    private $pageParam;  //分页变量
    private $uri;   //URL参数
    /**
     * 分页设置样式 不区分大小写
     * %pageToatl%  //总页数
     * %page%		//当前页
     * %pageSize% 	//当前页显示数据条数
     * %pageStart%	//本页起始条数
     * %pageStop%	//本页结束条数
     * %total%		//总数据条数
     * %first%		//首页
     * %ending%		//尾页
     * %up%			//上一页
     * %down%		//下一页
     * %F%			//起始页
     * %E%			//结束页
     * %omitFA%		//前省略加跳转
     * %omitEA%		//后省略加跳转
     * %omitF%		//前省略
     * %omitE%		//后省略
     * %numberF%	//固定数量的数字分页
     * %numberD%	//左右对等的数字分页
     * %input%		//跳转输入框
     * %GoTo%			//跳转按钮
     */
    private $pageType = '<span class="pageher">第%page%页/共%pageToatl%页</span>%first%%up%%F%%omitFA%%numberF%%omitEA%%E%%down%%ending%';
    //显示值设置
    private $pageShow = array('first' => '首页', 'ending' => '尾页', 'up' => '上一页', 'down' => '下一页', 'GoTo' => 'GO');

    /**
     * 初始化数据,构造方法
     * @access public
     * @param int $total 		数据总数量
     * @param int $pageSize 	每页显示条数
     * @param int $pageNumber 	分页数字显示数量(使用%numberF%和使用%numberD%会有不同的效果)
     * @param string $pageParam	分页变量
     * @return void
     */
    public function __construct($total, $pageSize = 10, $pageNumber = 5, $pageParam = 'p') {
        $this->total = $total < 0 ? 0 : $total;
        $this->pageSize = $pageSize < 0 ? 0 : $pageSize;
        $this->pageNumber = $pageNumber < 0 ? 0 : $pageNumber;
        $this->pageParam = $pageParam;
        $this->calculate();
    }

    /**
     * 显示分页
     * @access public 
     * @return string HTML分页字符串
     */
    public function pageShow() {
        $this->uri();
        if ($this->pageToatl > 1) {
            if ($this->page == 1) {
                $first = '<li><a href="javascript:void(0);" class="lastpage">' . $this->pageShow['first'] . '</a></li>';
                $up = '<li><a href="javascript:void(0);" class="lastpage">' . $this->pageShow['up'] . '</a></li>';
            } else {
                $first = '<li><a href="' . $this->uri . '&' . $this->pageParam . '=1" class="lastpage">' . $this->pageShow['first'] . '</a></li>';
                $up = '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . ($this->page - 1) . '" class="lastpage">' . $this->pageShow['up'] . '</a></li>';
            }
            if ($this->page >= $this->pageToatl) {
                $ending = '<li><a href="javascript:void(0);" class="nextpage">' . $this->pageShow['ending'] . '</a></li>';
                $down = '<li><a href="javascript:void(0);" class="nextpage">' . $this->pageShow['down'] . '</a></li>';
            } else {
                $ending = '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . $this->pageToatl . '" class="nextpage">' . $this->pageShow['ending'] . '</a></li>';
                $down = '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . ($this->page + 1) . '" class="nextpage">' . $this->pageShow['down'] . '</a></li>';
            }
            $input = '<input id="pageInput" style=" height:26px; line-height:26px; width:40px; border:1px solid #ccc;" type="text" value="' . $this->page . '" onkeydown="javascript: if(event.keyCode==13){var value = parseInt(this.value); var page=(value>' . $this->pageToatl . ') ? ' . $this->pageToatl . ' : value;  location=\'' . $this->uri . '&' . $this->pageParam . '=\'+page+\'\'; return false;}">';
            $GoTo = '<a  onclick="javascript:var value=document.getElementById(\'pageInput\').value; var page=(value>' . $this->pageToatl . ') ? ' . $this->pageToatl . ' : value;  location=\'' . $this->uri . '&' . $this->pageParam . '=\'+page+\'\' ; return false;">' . $this->pageShow['GoTo'] . '</a>';
        } else {
            $first = '<li><a href="javascript:void(0);" class="lastpage">' . $this->pageShow['first'] . '</a></li>';
            $up = '<li><a href="javascript:void(0);" class="lastpage">' . $this->pageShow['up'] . '</a></li>';
            $ending = '<li><a href="javascript:void(0);" class="nextpage">' . $this->pageShow['ending'] . '</a></li>';
            $down = '<li><a href="javascript:void(0);" class="nextpage">' . $this->pageShow['down'] . '</a></li>';
            $input = '<input id="pageInput" style=" height:26px; line-height:26px; width:40px; border:1px solid #ccc;" type="text" value="' . $this->page . '" onkeydown="javascript: if(event.keyCode==13){var value = parseInt(this.value); var page=(value>' . $this->pageToatl . ') ? ' . $this->pageToatl . ' : value;  location=\'' . $this->uri . '&' . $this->pageParam . '=\'+page+\'\'; return false;}">';
            $GoTo = '<a  onclick="javascript:var value=document.getElementById(\'pageInput\').value; var page=(value>' . $this->pageToatl . ') ? ' . $this->pageToatl . ' : value;  location=\'' . $this->uri . '&' . $this->pageParam . '=\'+page+\'\' ; return false;">' . $this->pageShow['GoTo'] . '</a>';
        }
        $this->numberF();
        return str_ireplace(array('%pageToatl%', '%page%', '%pageSize%', '%pageStart%', '%pageStop%', '%total%', '%first%', '%ending%', '%up%', '%down%', '%input%', '%GoTo%'), array($this->pageToatl, $this->page, $this->pageStop - $this->pageStart, $this->pageStart, $this->pageStop, $this->total, $first, $ending, $up, $down, $input, $GoTo), $this->pageType);
    }

    /**
     * 数字分页
     */
    private function numberF() {
        $pageF = stripos($this->pageType, '%numberF%');
        $pageD = stripos($this->pageType, '%numberD%');
        $numberF = '';
        $numberD = '';
        $F = '';
        $E = '';
        $omitF = '';
        $omitFA = '';
        $omitE = '';
        $omitEA = '';
        if ($pageF !== false || $pageD !== false) {
            if ($pageF !== false) {
                $number = $this->pageNumber % 2 == 0 ? $this->pageNumber / 2 : ($this->pageNumber + 1) / 2;
                $DStart = $this->page - $number < 0 ? $this->page - $number - 1 : 0;
                if ($this->page + $number - $DStart > $this->pageToatl) {
                    $DStop = ($this->page + $number - $DStart) - $this->pageToatl;
                    $forStop = $this->pageToatl + 1;
                } else {
                    $DStop = 0;
                    $forStop = $this->page + $number - $DStart + 1;
                }
                $forStart = $this->page - $number - $DStop < 1 ? 1 : $this->page - $number - $DStop;
                for ($i = $forStart; $i < $forStop;  ++$i) {
                    if ($i == $this->page) {
                        $numberF .= '<li><a class="selectin" href="javascript:void(0);">' . $i . '</a></li>';
                    } else {
                        $numberF .= '<li><a  href="' . $this->uri . '&' . $this->pageParam . '=' . $i . '">' . $i . '</a></li>';
                    }
                }
            }
            if ($pageD !== false) {
                $number = $this->pageNumber;
                $forStart = $this->page - $number > 0 ? $this->page - $number : 1;
                $forStop = $this->page + $number > $this->pageToatl ? $this->pageToatl + 1 : $this->page + $number + 1;
                for ($i = $forStart; $i < $this->page;  ++$i) {
                    $numberD .= '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . $i . '">' . $i . '</a></li>';
                }
                $numberD .= '<li><a class="selectin" href="javascript:void(0);">' . $this->page . '</a></li>';
                $start = $this->page + 1;
                for ($i = $start; $i < $forStop;  ++$i) {
                    $numberD .= '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . $i . '">' . $i . '</a></li>';
                }
            }
            $F = $forStart > 1 ? '<li><a href="' . $this->uri . '&' . $this->pageParam . '=1">1</a></li>' : '';
            $E = $forStop < $this->pageToatl + 1 ? '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . $this->pageToatl . '">' . $this->pageToatl . '</a></li>' : '';
            if ($forStart > 2) {
                $omitF = '<li><a></li>…</a></li>';
                $startA = $this->page - $number < 1 ? 1 : $this->page - $number;
                $omitFA = '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . $startA . '">…</a></li>';
            }
            if ($forStop < $this->pageToatl) {
                $omitE = '<li><a></li>…</a></li>';
                $stopA = $this->page + $number > $this->pageToatl ? $this->pageToatl : $this->page + $number;
                $omitEA = '<li><a href="' . $this->uri . '&' . $this->pageParam . '=' . $stopA . '">…</a></li>';
            }
        }
        $this->pageType = str_ireplace(array('%F%', '%E%', '%omitFA%', '%omitEA%', '%omitF%', '%omitE%', '%numberF%', '%numberD%'), array($F, $E, $omitFA, $omitEA, $omitF, $omitE, $numberF, $numberD), $this->pageType);
    }

    /**
     * 处理url的方法
     * @access public
     * @param array   $url 	保持URL直对关系数组
     * @return string 		过滤后的url尾参数
     */
    private function uri() {
        $url = $_SERVER["REQUEST_URI"];
        $par = parse_url($url);
        if (isset($par['query'])) {
            parse_str($par['query'], $query);
            if (!is_array($this->uri)) {
                $this->uri = array();
            }
            array_merge($query, $this->uri);
            unset($query[$this->pageParam]);
            while ($key = array_search('', $query)) {
                unset($query[$key]);
            }
            $this->uri = $par['path'] . '?' . http_build_query($query);
        } else {
            $this->uri = $par['path'] . '?';
        }
    }

    /**
     * 设置limit方法及计算起始条数和结束条数
     */
    private function calculate() {
        $this->pageToatl = ceil($this->total / $this->pageSize);
        if (isset($_GET[$this->pageParam])) {
            $page = intval($_GET[$this->pageParam]);
        }

        $this->page = empty($page) ? 1 : $page;

        $this->page = $this->page >= 1 ? $this->page > $this->pageToatl ? $this->pageToatl : $this->page : 1;
        if ($this->page == 0) {
            $this->page = 1;
        }
        $this->pageStart = ($this->page - 1) * $this->pageSize;
        $this->pageStop = $this->pageStart + $this->pageSize;
        $this->pageStop = $this->pageStop > $this->total ? $this->total : $this->pageStop;
        $this->limit = $this->pageStart . ',' . $this->pageSize;
    }

    /**
     * 设置过滤器
     */
    public function __set($name, $value) {
        switch ($name) {
            case 'pageType':
            case 'uri':
                $this->$name = $value;
                return;
            case 'pageShow':
                if (is_array($value)) {
                    $this->pageShow = array_merge($this->pageShow, $value);
                }
                return;
        }
    }

    /**
     * 取值过滤器
     */
    public function __get($name) {
        switch ($name) {
            case 'limit':
            case 'pageStart':
            case 'pageStop':
            case 'pageSize':
                return $this->$name;
            default:
                return;
        }
    }

}
