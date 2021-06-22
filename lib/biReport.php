<?php



class biReport
{
    public $_id;
    public $_name;
    public $_column;
    public $_data;
    public $_dataShow;

    public function biReport($cId, $cName = null, $cCols = null, $cData = null, $cDataShow = null)
    {
        $this->_id = $cId;
        null === $cName;
        (null === $cName ? ($this->_name = $cId) : ($this->_name = $cName));
        null === $cCols;
        (null === $cCols ? ($this->_column = []) : ($this->_column = $cCols));
        null === $cData;
        (null === $cData ? ($this->_data = []) : ($this->_data = $cData));
        null === $cDataShow;
        (null === $cDataShow ? ($this->_dataShow = []) : ($this->_dataShow = $cDataShow));
    }

    public function prep()
    {
        $tab = "<table id='".$this->_id."' class='sortable'>";
        $tab .= "<thead><tr class='rowheader'>";
        foreach ($this->_column as $head) {
            $tab .= '<td>'.$head.'</td>';
        }
        $tab .= '</tr></thead>';
        $tab .= '<tbody>';
        foreach ($this->_data as $key => $row) {
            $tab .= "<tr class='rowcontent'>";
            foreach ($row as $field => $cont) {
                $tab .= "<td id='".$field.'_'.$key."' value='".$cont."'>".$this->_dataShow[$key][$field].'</td>';
            }
            $tab .= '</tr>';
        }
        $tab .= '</tbody>';
        $tab .= '</table>';

        return $tab;
    }

    public function render()
    {
        echo $this->prep();
    }
}

?>