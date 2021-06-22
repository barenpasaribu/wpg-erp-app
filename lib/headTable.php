<?php



include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

class headAction
{
    public $_name;
    public $_title;
    public $_img;
    public $_switchImg;
    private $_attr;
    private $_altImg;

    public function headAction($cName, $cTitle, $cImg = null, $cAttr = null)
    {
        $this->_name = $cName;
        $this->_title = $cTitle;
        $this->_switchImg = false;
        null === $cAttr;
        (null === $cAttr ? ($this->_attr = []) : ($this->_attr = $cAttr));
        null === $cImg;
        (null === $cImg ? ($this->_img = '/images/default.png') : ($this->_img = $cImg));
        null === $cImg;
        (null === $cImg ? ($this->_altImg = '/images/default.png') : ($this->_altImg = $cImg));
    }

    public function setAltImg($imgPath)
    {
        $this->_switchImg = true;
        $this->_altImg = $imgPath;
    }

    public function setAttr($cAttr)
    {
        if (is_array($cAttr)) {
            $this->_attr = $cAttr;
        } else {
            return false;
        }
    }

    public function getAttr()
    {
        return $this->_attr;
    }

    public function addAttr($anAttr)
    {
        array_push($this->_attr, $anAttr);
    }
}

class headTable
{
    public $_headers;
    public $_content;
    public $_footer;
    public $_actions;
    public $_tr;
    public $_print;
    public $_fullwidth;
    private $_id;
    private $_idBody;
    private $_name;
    private $_width;
    private $_align;
    private $_page;
    private $_totalPage;
    private $_rowPerPage;
    private $_where;

    public function headTable($cId, $cName = null, $cBody = null, $cHead = null, $cCont = null, $cFoot = null)
    {
        $this->_id = $cId;
        $this->_actions = [];
        $this->_tr = 'tr';
        $this->_print = true;
        $this->_page = 1;
        $this->_totalPage = 1;
        $this->_rowPerPage = 10;
        $this->_where = null;
        $this->_fullwidth = true;
        null === $cName;
        (null === $cName ? ($this->_name = $cId) : ($this->_name = $cName));
        null === $cBody;
        (null === $cBody ? ($this->_idBody = []) : ($this->_idBody = $cBody));
        null === $cHead;
        (null === $cHead ? ($this->_headers = []) : ($this->_headers = $cHead));
        null === $cCont;
        (null === $cCont ? ($this->_content = []) : ($this->_content = $cCont));
        null === $cFoot;
        (null === $cFoot ? ($this->_footer = []) : ($this->_footer = $cFoot));
        $this->_width = [];
        $sumHead = count($this->_headers);
        for ($i = 0; $i < $sumHead; ++$i) {
            $this->_width[] = floor(100 / $sumHead * 0.9);
        }
        $this->_align = [];
        for ($i = 0; $i < $sumHead; ++$i) {
            $this->_align[] = 'center';
        }
    }

    public function setWhere($where)
    {
        if (is_array($where)) {
            $this->_where = $where;
        } else {
            return false;
        }
    }

    public function pageSetting($cPage, $cTotalRow, $cRowPerPage)
    {
        $this->_page = $cPage;
        $this->_totalPage = ceil($cTotalRow / $cRowPerPage);
        $this->_rowPerPage = $cRowPerPage;
    }

    public function setWidth($width)
    {
        if (count($width) === count($this->_headers)) {
            $sumLen = 0;
            $newWidth = [];
            foreach ($width as $len) {
                $sumLen += $len;
                $newWidth = 0.9 * $len;
            }
            if ($sumLen <= 100) {
                $this->_width = $newWidth;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setAlign($align)
    {
        if (count($align) === count($this->_headers)) {
            foreach ($align as $key => $alg) {
                switch ($alg) {
                    case 'center':
                    case 'ctr':
                    case 'c':
                    case 'C':
                        $align[$key] = 'center';

                        break;
                    case 'left':
                    case 'lt':
                    case 'l':
                    case 'L':
                        $align[$key] = 'left';

                        break;
                    case 'right':
                    case 'rt':
                    case 'r':
                    case 'R':
                        $align[$key] = 'right';

                        break;
                    default:
                        $align[$key] = 'center';

                        break;
                }
            }
            $this->_align = $align;
        } else {
            return false;
        }
    }

    public function addAction($cAct, $cTitle, $cAttr = null)
    {
        if (null === $cAttr) {
            if (is_object($cAct)) {
                $this->_actions[] = $cAct;
            } else {
                $this->_actions[] = new headAction($cAct, $cTitle);
            }
        } else {
            $this->_actions[] = new headAction($cAct, $cTitle, $cAttr);
        }
    }

    public function prep()
    {
        $optPage = [];
        ($this->_totalPage < 1 ? ($this->_totalPage = 1) : null);
        for ($i = 1; $i <= $this->_totalPage; ++$i) {
            $optPage[$i] = $i;
        }
        $where = "'[";
        if (!empty($this->_where)) {
            foreach ($this->_where as $r1) {
                $where .= '[';
                $i = 0;
                foreach ($r1 as $r2) {
                    if (0 < $i) {
                        $where .= ',';
                    }

                    if (is_int($r2)) {
                        $where .= $r2;
                    } else {
                        $where .= "\\'".$r2."\\'";
                    }

                    ++$i;
                }
                $where .= ']';
            }
        }

        $where .= "]'";
        $theTable = OPEN_BOX2();
        $theTable .= "<div align='center'><h3>".$this->_name.'</h3></div>';
        $theTable .= "<div><table align='center'><tr>";
        $theTable .= "<td v-align='middle' style='min-width:100px'>";
        $theTable .= "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"theHTab.showAdd()\"><br><span align='center'>".$_SESSION['lang']['new'].'</span></div>';
        $theTable .= '</td>';
        $theTable .= "<td v-align='middle' style='min-width:100px'>";
        $theTable .= "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"theHTab.defaultList()\"><br><span align='center'>".$_SESSION['lang']['list'].'</span></div>';
        $theTable .= '</td>';
        $theTable .= "<td v-align='middle' style='min-width:100px'>";
        $theTable .= '<fieldset><legend><b>'.$_SESSION['lang']['find'].'</b></legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['notransaksi']).makeElement('sNoTrans', 'text', '').makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'theHTab.searchTrans()']).'</fieldset>';
        $theTable .= '</td>';
        $theTable .= '</tr></table></div>';
        $theTable .= CLOSE_BOX2();
        $theTable .= OPEN_BOX2();
        $theTable .= "<div id='workField'>";
        if ($this->_print) {
            $theTable .= "<fieldset style='float:left;clear:right'>";
            $theTable .= '<legend><b>'.$_SESSION['lang']['print'].'</b></legend>';
            $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/print.png'"."style='cursor:pointer' onclick='print()' title='Print Page' />&nbsp;&nbsp;";
            $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/pdf.jpg'"."style='cursor:pointer' onclick='printPDF()' title='Print PDF' />";
            $theTable .= '</fieldset>';
        }

        $theTable .= "<fieldset style='clear:left'>";
        $theTable .= '<legend><b>'.$_SESSION['lang']['list'].'</b></legend>';
        $theTable .= "<table id='".$this->_id."' class='sortable' cellspacing='1' "."style='width:100%' border='0'>";
        $theTable .= "<thead><tr class='rowheader'>";
        foreach ($this->_headers as $key => $head) {
            $theTable .= "<td align='center' style='width:".$this->_width[$key]."%'>".$head.'</td>';
        }
        $theTable .= "<td align='center' style='width:10%' colspan='".count($this->_actions)."'>".$_SESSION['lang']['action'].'</td>';
        $theTable .= '</tr></thead>';
        $theTable .= "<tbody id='".$this->_idBody."'>";
        if (empty($this->_content)) {
            $theTable .= "<tr id='".$this->_tr."_empty' class='rowcontent'>";
            $theTable .= "<td align='center' colspan='".(count($this->_headers) + 1)."'>".$_SESSION['lang']['dataempty'].'</td>';
            $theTable .= '</tr>';
        } else {
            foreach ($this->_content as $key => $row) {
                $theTable .= "<tr id='".$this->_tr.'_'.$key."' class='rowcontent'>";
                $ct = 0;
                foreach ($row as $id => $val) {
                    if ('switched' !== $id) {
                        $theTable .= "<td align='".$this->_align[$ct]."' id='".$id.'_'.$key."'>".$val.'</td>';
                        ++$ct;
                    }
                }
                foreach ($this->_actions as $act) {
                    if (isset($row['switched'])) {
                        $theTable .= "<td><img src='".$act->_altImg."' class='zImgBtn'".'onclick="'.$act->_name.'('.$key;
                    } else {
                        $theTable .= "<td><img src='".$act->_img."' class='zImgBtn'".'onclick="'.$act->_name.'('.$key;
                    }

                    $tmpAttr = $act->getAttr();
                    if (!empty($tmpAttr)) {
                        foreach ($tmpAttr as $attr) {
                            $theTable .= ",'".$attr."'";
                        }
                    }

                    $theTable .= ")\" title='".$act->_title."' style='cursor:pointer' /></td>";
                }
                $theTable .= '</tr>';
            }
        }

        $theTable .= '</tbody>';
        $theTable .= '<tfoot><tr>';
        $theTable .= "<td colspan='".(count($this->_headers) + count($this->_actions))."' align='center'>";
        $theTable .= "<img src='images/".$_SESSION['theme']."/first.png'";
        if (1 < $this->_page) {
            $theTable .= " style='cursor:pointer' onclick=\"goToPages(1,".$this->_rowPerPage.','.$where.')"';
        }

        $theTable .= '>&nbsp;';
        $theTable .= "<img src='images/".$_SESSION['theme']."/prev.png'";
        if (1 < $this->_page) {
            $theTable .= " style='cursor:pointer' onclick=\"goToPages(".($this->_page - 1).','.$this->_rowPerPage.','.$where.')"';
        }

        $theTable .= '>&nbsp;';
        $theTable .= makeElement('pages', 'select', $this->_page, ['style' => 'width:50px', 'onchange' => 'choosePage(this,'.$this->_rowPerPage.','.$where.')'], $optPage).'&nbsp;';
        $theTable .= "<img src='images/".$_SESSION['theme']."/next.png'";
        if ($this->_page < $this->_totalPage) {
            $theTable .= " style='cursor:pointer' onclick=\"goToPages(".($this->_page + 1).','.$this->_rowPerPage.','.$where.')"';
        }

        $theTable .= '>&nbsp;';
        $theTable .= "<img src='images/".$_SESSION['theme']."/last.png'";
        if ($this->_page < $this->_totalPage) {
            $theTable .= " style='cursor:pointer' onclick=\"goToPages(".$this->_totalPage.','.$this->_rowPerPage.','.$where.')"';
        }

        $theTable .= '>';
        $theTable .= '</td>';
        $theTable .= '</tr></tfoot>';
        $theTable .= '</table>';
        $theTable .= '</fieldset>';
        $theTable .= '</div>';
        $theTable .= CLOSE_BOX2();

        return $theTable;
    }

    public function render()
    {
        $theTable = $this->prep();
        echo $theTable;
    }
}

?>