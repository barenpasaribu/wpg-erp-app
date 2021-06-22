<?php



include_once 'lib/zForm.php';

class rElement
{
    public $_id;
    public $_name;
    public $_content;
    public $_type;
    public $_length;
    public $_refer;
    public $_attr;

    public function rElement($cId, $cName, $cCont = null, $cType = null, $cAlign = null, $cLength = null, $cRefer = null)
    {
        null === $cCont;
        (null === $cCont ? ($this->_content = '-') : ($this->_content = $cCont));
        null === $cType;
        (null === $cType ? ($this->_type = 'text') : ($this->_type = $cType));
        null === $cLength;
        (null === $cLength ? ($this->_length = 40) : ($this->_length = $cLength));
        null === $cRefer;
        (null === $cRefer ? ($this->_refer = []) : ($this->_refer = $cRefer));
        null === $cAlign;
        (null === $cAlign ? ($this->_align = []) : ($this->_align = $cAlign));
        $this->_id = $cId;
        $this->_name = $cName;
        $this->_attr = [];
    }

    public function genEls()
    {
        return makeElement($this->_id, $this->_type, $this->_content, $this->_attr, $this->_refer);
    }
}

class rGrid
{
    public $_tr;
    private $_id;
    private $_name;
    private $_elements;
    private $_data;

    public function rGrid($cId, $cName, $cData = null)
    {
        $this->_id = $cId;
        $this->_name = $cName;
        $this->_elements = [];
        $this->_tr = 'tr';
        null === $cData;
        (null === $cData ? ($this->_data = []) : ($this->_data = $cData));
    }

    public function addEls($cId, $cName, $cCont = null, $cType = null, $cAlign = null, $cLength = null, $cRefer = null)
    {
        $this->_elements[$cId] = new rElement($cId, $cName, $cCont, $cType, $cAlign, $cLength, $cRefer);
    }

    public function prepGrid()
    {
        $grid = "<div id='".$this->_id."_elements' style='display:none'>";
        $grid .= json_encode($this->_elements).'</div>';
        $grid .= "<table id='".$this->_id."' name='".$this->_name."' class='sortable' ";
        $grid .= 'cellpadding=0 cellspacing=1 >';
        $grid .= "<thead id='thead_".$this->_id."'>";
        $grid .= "<tr class='rowheader'>";
        $i = 0;
        foreach ($this->_elements as $els) {
            $grid .= "<td id='".$els->_id."_header' align='center' style='width:".$els->_length * 10 ."px'>".$els->_name.'</td>';
            ++$i;
        }
        $grid .= "<td colspan='2'>Action</td>";
        $grid .= '</tr>';
        $grid .= '</thead>';
        $grid .= "<tbody id='tbody_".$this->_id."'>";
        if (!empty($this->_data)) {
            foreach ($this->_data as $key => $row) {
                $grid .= "<tr id='".$this->_tr.'_'.$key."' class='rowcontent'>";
                foreach ($row as $id => $cont) {
                    $grid .= "<td id='".$this->_elements[$id]->_id.'_'.$key."'";
                    $grid .= '>';
                    $grid .= $cont;
                    $grid .= '</td>';
                }
                $grid .= '<td>';
                $grid .= "<img id='makeEdit_".$key."' src='images/".$_SESSION['theme']."/edit.png' class='zImgBtn' ";
                $grid .= 'onclick="theRGrid.editElement('.$key.",'".$this->_id."_elements')\">";
                $grid .= "<img id='editData_".$key."' src='images/".$_SESSION['theme']."/edit.png' class='zImgBtn' ";
                $grid .= "style='display:none' ";
                $grid .= 'onclick="theRGrid.editData('.$key.",'".$this->_id."_elements')\">";
                $grid .= '</td>';
                $grid .= "<td id='delDataTd_".$key."' style='display:none'>";
                $grid .= "<img id='delData_".$key."' src='images/".$_SESSION['theme']."/delete.png' class='zImgBtn' ";
                $grid .= "style='display:none' ";
                $grid .= 'onclick="theRGrid.delData('.$key.",'".$this->_id."_elements')\">";
                $grid .= '</td>';
                $grid .= '</tr>';
            }
        }

        $grid .= '</tbody>';
        $grid .= "<tfoot id='tfoot_".$this->_id."'>";
        $grid .= '</tfoot>';
        $grid .= '</table>';

        return $grid;
    }

    public function showGrid()
    {
        echo $this->prepGrid();
    }
}

?>