<?php



class mColumn
{
    public $_id;
    public $_width;

    public function mColumn($cId, $cWidth = null)
    {
        null === $cWidth;
        (null === $cWidth ? ($this->_width = 0) : ($this->_width = $cWidth));
    }
}

class mTable
{
    public $_id;
    public $_idBody;
    public $_tr;
    public $_print;
    public $_fullwidth;
    public $_pdfLink;
    private $_columns;
    private $_headers;
    private $_content;
    private $_footers;

    public function mTable($cId, $cBody = null, $cHead = null, $cCont = null, $cFoot = null)
    {
        $this->_id = $cId;
        $this->_actions = [];
        $this->_tr = 'tr';
        $this->_print = true;
        $this->_pdfLink = '#';
        $this->_fullwidth = false;
        null === $cBody;
        (null === $cBody ? ($this->_idBody = []) : ($this->_idBody = $cBody));
        null === $cHead;
        (null === $cHead ? ($this->_headers = []) : ($this->_headers = $cHead));
        null === $cCont;
        (null === $cCont ? ($this->_content = []) : ($this->_content = $cCont));
        null === $cFoot;
        (null === $cFoot ? ($this->_footers = []) : ($this->_footers = $cFoot));
    }

    public function setColumn($arrId, $arrWidth = [])
    {
        foreach ($arrId as $key => $id) {
            if (empty($arrWidth)) {
                $this->_columns[$key] = new mColumn($id);
            } else {
                $this->_columns[$key] = new mColumn($id, $arrWidth[$key]);
            }
        }
    }

    public function prepTable()
    {
        $optPage = [];
        ($this->_totalPage < 1 ? ($this->_totalPage = 1) : null);
        for ($i = 1; $i <= $this->_totalPage; ++$i) {
            $optPage[$i] = $i;
        }
        $where = "'[";
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
        $where .= "]'";
        $theTable = '';
        if ($this->_print) {
            $theTable .= "<fieldset style='float:left;clear:right'>";
            $theTable .= '<legend><b>'.$_SESSION['lang']['print'].'</b></legend>';
            $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/print.png'"."style='cursor:pointer' onclick='print()' title='Print Page' />&nbsp;&nbsp;";
            $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/pdf.jpg'"."style='cursor:pointer' onclick='printPDF()' title='Print PDF' />";
            $theTable .= '</fieldset>';
        }

        $theTable .= "<fieldset style='clear:left'>";
        $theTable .= '<legend><b>'.$_SESSION['lang']['list'].'</b></legend>';
        $theTable .= "<table id='".$this->_id."' class='sortable' cellspacing='1' ";
        if ($this->_fullwidth) {
            $theTable .= "style='width:100%' ";
        }

        $theTable .= "border='0'>";
        $theTable .= "<thead><tr class='rowheader'>";
        foreach ($this->_headers as $key => $head) {
            $theTable .= "<td align='center' style='width:".$this->_columns[$key]->_width."%'>".$head.'</td>';
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
        $theTable .= '<tfoot>';
        $theTable .= '</tfoot>';
        $theTable .= '</table>';
        $theTable .= '</fieldset>';

        return $theTable;
    }

    public function renderTable()
    {
        $theTable = $this->prepTable();
        echo $theTable;
    }
}

?>