<?php



include_once 'lib/zForm.php';

class zPosting
{
    public $_id;
    public $_name;
    public $_title;
    public $_header;
    public $_cols;
    public $_colsStr;
    public $_data;
    public $_dataShow;
    public $_click;

    public function zPosting($cId, $cName, $cCols, $cHeader, $cData, $cDataShow = null)
    {
        $this->_id = $cId;
        $this->_cols = $cCols;
        $this->_data = $cData;
        $this->_click = null;
        $this->_title = ucfirst($cId);
        null === $cName;
        (null === $cName ? ($this->_name = $cId) : ($this->_name = $cName));
        null === $cHeader;
        (null === $cHeader ? ($this->_header = $cCols) : ($this->_header = $cHeader));
        null === $cDataShow;
        (null === $cDataShow ? ($this->_dataShow = $cData) : ($this->_dataShow = $cDataShow));
        $this->_colsStr = '';
        foreach ($cCols as $col) {
            $this->_colsStr .= '##'.$col;
        }
    }

    public function prep()
    {
        $table = '';
        $table .= "<table id='".$this->_id."' name='".$this->_name."' class='sortable'>";
        $table .= "<thead><tr class='rowheader'>";
        $table .= '<td>Status</td>';
        foreach ($this->_header as $head) {
            $table .= '<td>'.$head.'</td>';
        }
        $table .= '</tr></thead>';
        $table .= "<tbody id='tbody_".$this->_id."'>";
        foreach ($this->_data as $key => $row) {
            $table .= "<tr id='tr_".$this->_id.'_'.$key."' class='rowcontent'>";
            $table .= "<td><img id='status_".$this->_id.'_'.$key."' class='zImgOffBtn' ";
            $table .= "src='images/".$_SESSION['theme']."/posting.png'></td>";
            foreach ($row as $head => $cont) {
                $table .= "<td id='".$this->_id.'_'.$head.'_'.$key."' ";
                $table .= "value='".$cont."'>".$this->_dataShow[$key][$head];
                $table .= '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody>';
        $table .= "<tfoot><tr><td align='center' colspan='".(count($this->_header) + 1)."'>";
        if (empty($this->_data)) {
            $table .= makeElement('btnPosting_'.$this->_id, 'btn', $_SESSION['lang']['posting'], ['disabled' => 'disabled']);
        } else {
            $table .= makeElement('btnPosting_'.$this->_id, 'btn', $_SESSION['lang']['posting'], ['onclick' => "post('".$this->_id."',0,'".$this->_colsStr."','".$_SESSION['theme']."')"]);
        }

        $table .= '</td></tr></tfoot>';
        $table .= '</table>';
        $fieldset = makeFieldset($this->_title, 'fs_'.$this->_id, $table);

        return $fieldset;
    }

    public function render()
    {
        echo $this->prep();
    }
}

?>