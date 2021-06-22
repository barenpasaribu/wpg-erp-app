<?php



echo "<script languange=javascript1.2 src='js/zGrid.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zGrid/";
echo $_SESSION['theme'];
echo ".css'>\r\n";
require_once 'lib/zForm.php';

class ColumnGrid
{
    public $_id;
    public $_name;
    public $_content;
    public $_type;
    public $_length;
    public $_refer;

    public function ColumnGrid($cId = null, $cName = null, $cCont = null, $cType = null, $cAlign = null, $cLength = null, $cRefer = null)
    {
        null === $cId;
        (null === $cId ? ($cId = '-') : null);
        null === $cName;
        (null === $cName ? ($cName = '-') : null);
        null === $cCont;
        (null === $cCont ? ($cCont = '-') : null);
        null === $cType;
        (null === $cType ? ($cType = 'text') : null);
        null === $cLength;
        (null === $cLength ? ($cLength = 40) : null);
        null === $cRefer;
        (null === $cRefer ? ($cRefer = []) : null);
        $this->_id = $cId;
        $this->_name = $cName;
        $this->_type = $cType;
        $this->_content = $cCont;
        $this->_align = $cAlign;
        $this->_length = $cLength;
        $this->_refer = $cRefer;
    }

    public function getInfo()
    {
        $tmpArr = [];
        $tmpArr['id'] = $this->_id;
        $tmpArr['name'] = $this->_name;
        $tmpArr['type'] = $this->_type;
        $tmpArr['length'] = $this->_length;
        $tmpArr['refer'] = $this->_refer;

        return $tmpArr;
    }

    public function gridElement($num, $data, $attr = [])
    {
        $newAttr = ['maxlength' => $this->_length, 'style' => 'margin:0;width:'.(string) ($this->_length * 6.5).'px'];
        $attr = array_merge($attr, $newAttr);

        return makeElement($this->_id.'_'.$num, $this->_type, $data, $attr, $this->_refer);
    }
}

class HeadGrid
{
    private $_name;
    private $_sumColumn;
    private $_column;

    public function HeadGrid($cName = null, $cCols = null)
    {
        null === $cName;
        (null === $cName ? ($cName = 'zGrid') : null);
        null === $cCols;
        (null === $cCols ? ($cCols = []) : null);
        $cSumCol = count($cCols);
        $this->_name = $cName;
        $this->_sumColumn = $cSumCol;
        $this->_column = $cCols;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getColumn()
    {
        return $this->_column;
    }

    public function getSumColumn()
    {
        return $this->_sumColumn;
    }

    public function setColumn($col)
    {
        if (is_array($col)) {
            $this->_column = $col;
        } else {
            echo 'Error : Attribute must array in HeadGrid->setColumn';
        }
    }

    public function insertCol($col = null)
    {
        null === $col;
        (null === $col ? ($col = new ColumnGrid()) : null);
        array_push($this->_column, $col);
        ++$this->_sumColumn;
    }
}

class Grid
{
    private $_num;
    private $_head;
    private $_data;

    public function Grid($cNum = null, $cHead = null, $cData = null)
    {
        null === $cNum;
        (null === $cNum ? ($cNum = 1) : null);
        null === $cHead;
        (null === $cHead ? ($cHead = new HeadGrid()) : null);
        null === $cData;
        (null === $cData ? ($cData = []) : null);
        $this->_num = $cNum;
        $this->_head = $cHead;
        $this->_data = $cData;
    }

    public function prepLayout()
    {
        $num = $this->_num;
        $col = $this->_head->getColumn();
        $grid = "<table class='zGrid' border='1' cellpadding='0' cellspacing='0'>";
        $grid .= '<thead><tr>';
        $tmpCols = $this->_head->getColumn();
        foreach ($tmpCols as $cols) {
            $grid .= "<th style='width:".$cols->_length * 6.5."px'>".$cols->_name.'</th>';
        }
        $grid .= '<th>';
        $grid .= "<img class='zImgBtn'"."src='images/newfile.png' onclick='theGrid[".$num."].addRowGrid()'></img>";
        $grid .= '</th></tr>';
        $grid .= '</thead>';
        $grid .= "<tbody id='zGridBody_".$this->_num."'>";
        $sumCols = $this->_head->getSumColumn();
        if (empty($this->_data)) {
            $grid .= "<tr id='grid_tr_empty' align='center'>";
            $grid .= "<td colspan='".(string) ($sumCols + 1)."'>Data Empty";
            $grid .= '</td></tr>';
        } else {
            $grid .= "<tr id='grid_tr_empty' align='center' style='display:none'>";
            $grid .= "<td colspan='".(string) ($sumCols + 1)."'>Data Empty";
            $grid .= '</td></tr>';
            foreach ($this->_data as $key => $row) {
                $grid .= "<tr id='grid_tr_".$key."'>";
                $i = 0;
                foreach ($row as $name => $content) {
                    $grid .= "<td id='grid_td_".$name.'_'.$key."'>";
                    $grid .= $col[$i]->gridElement($num, $content).'</td>';
                    ++$i;
                }
                $grid .= "<td id='grid_action_".$key."'>";
                $grid .= "<img class='zImgBtn'"."src='images/001_45.png' onclick='theGrid[".$num.'].editRowGrid('.$key.")'></img>&nbsp;";
                $grid .= "<img class='zImgBtn'"."src='images/delete1.png' onclick='theGrid[".$num.'].delRowGrid('.$key.")'></img>";
                $grid .= '</td>';
                $grid .= '</tr>';
            }
        }

        $grid .= '</tbody>';
        $grid .= '</table>';

        return $grid;
    }

    public function showGrid()
    {
        $grid = $this->prepLayout();
        echo $grid;
    }
}

?>