<?php



include_once 'lib/zForm.php';
include_once 'lib/uElement.php';

class uForm
{
    public $_id;
    public $_name;
    public $_elements;
    public $_width;

    public function uForm($cId, $cName, $cWidth = null, $cEls = null)
    {
        $this->_id = $cId;
        $this->_name = $cName;
        null === $cEls;
        (null === $cEls ? ($this->_elements = []) : ($this->_elements = $cEls));
        null === $cWidth;
        (null === $cWidth ? ($this->_width = 1) : ($this->_width = $cWidth));
    }

    public function addEls($cId, $cName, $cCont = null, $cType = null, $cAlign = null, $cLength = null, $cRefer = null, $cCont2 = null, $cTSatuan = null, $cTHarga = null, $cParent = null)
    {
        $this->_elements[] = new uElement($cId, $cName, $cCont, $cType, $cAlign, $cLength, $cRefer, $cCont2, $cTSatuan, $cTHarga, $cParent);
    }
}

class uTable
{
    public $_id;
    public $_name;
    public $_data;
    public $_dataShow;

    public function uTable($cId, $cName, $cCols = null, $cData = null, $cDataShow = null)
    {
        $this->_id = $cId;
        $this->_name = $cName;
        null === $cData;
        (null === $cData ? ($this->_data = []) : ($this->_data = $cData));
        null === $cDataShow;
        (null === $cDataShow ? ($this->_dataShow = $this->_data) : ($this->_dataShow = $cDataShow));
    }
}

class uFormTable
{
    public $_tableWidth;
    public $_target;
    public $_nourut;
    public $_noaction;
    public $_onedata;
    public $_noClearField;
    public $_noEnable;
    public $_detailFieldset;
    public $_addActions;
    public $_numberFormat;
    public $_afterCrud;
    public $_beforeEditMode;
    private $_id;
    private $_form;
    private $_table;
    private $_elements;
    private $_addElements;
    private $_freezeEls;

    public function uFormTable($cId, $cForm, $cTable, $cEls = null, $cAddEls = null)
    {
        $this->_id = $cId;
        $this->_tableWidth = 200;
        $this->_target = 'slave_ft';
        $this->_freezeEls = null;
        $this->_nourut = false;
        $this->_noaction = false;
        $this->_onedata = false;
        $this->_noClearField = '##';
        $this->_noEnable = '##';
        $this->_defValue = '##';
        $this->_numberFormat = '##';
        $this->_detailFieldset = [];
        $this->_addActions = [];
        $this->_afterCrud = '';
        $this->_beforeEditMode = '';
        is_object($cForm);
        (is_object($cForm) ? ($this->_form = $cForm) : false);
        is_object($cTable);
        (is_object($cTable) ? ($this->_table = $cTable) : false);
        if (null !== $cEls) {
            $this->_elements = $cEls;
        } else {
            $this->_elements = [];
            if (is_object($cForm)) {
                foreach ($this->_form->_elements as $els) {
                    $this->_elements[] = $els->_id;
                }
            }
        }

        null === $cAddEls;
        (null === $cAddEls ? ($this->_addElements = $this->_elements) : ($this->_addElements = $cAddEls));
    }

    public function setFreezeEls($cFreeze)
    {
        if (is_array($cFreeze)) {
            $tmpStr = '';
            foreach ($cFreeze as $row) {
                $tmpStr .= '##'.$row;
            }
            $this->_freezeEls = $tmpStr;
        } else {
            $this->_freezeEls = $cFreeze;
        }
    }

    public function prep()
    {
        $theEls = $this->_form->_elements;
        $align = [];
        foreach ($this->_form->_elements as $el) {
            $align[] = $el->_align;
        }
        $elsWidth = $this->_form->_width;
        $newEls = [];
        $i = 0;
        $j = 0;
        $maxHeight = ceil(count($theEls) / $elsWidth);
        foreach ($theEls as $els) {
            $newEls[$i][$j] = $els;
            ++$i;
            if ($i === $maxHeight) {
                ++$j;
                $i = 0;
            }
        }
        $numFormatArr = explode('##', $this->_numberFormat);
        $addActionJs = str_replace('"', '##', json_encode($this->_addActions));
        $elsParam = '';
        $addElsParam = '';
        $alignParam = '';
        foreach ($this->_elements as $elId) {
            $elsParam .= '##'.$elId;
        }
        foreach ($this->_addElements as $addEl) {
            $addElsParam .= '##'.$addEl;
        }
        foreach ($align as $alg) {
            $alignParam .= '##'.$alg;
        }
        $formTab = '';
        $formTab .= "<div id='".$this->_id."'>";
        $formTab .= "<div id='form_".$this->_id."'";
        if (true === $this->_noaction) {
            $formTab .= " style='display:none'";
        }

        $formTab .= '>';
        $formTab .= '<fieldset>';
        $formTab .= "<legend id='form_".$this->_id."_title'>";
        $formTab .= '<b>'.$this->_form->_name." : <span id='form_".$this->_id."_mode'>".$_SESSION['lang']['addmode'].'</span></b></legend>';
        $formTab .= '<table>';
        foreach ($newEls as $row) {
            $formTab .= '<tr>';
            foreach ($row as $els) {
                if (empty($this->_detailFieldset)) {
                    $formTab .= '<td>'.makeElement($els->_id, 'label', $els->_name).'</td>';
                    $formTab .= '<td>:</td>';
                    $formTab .= "<td id='".$this->_id.'_'.$els->_id."'>".$els->genEls().'</td>';
                } else {
                    $notShow = false;
                    foreach ($this->_detailFieldset as $keyDet => $rowDet) {
                        foreach ($rowDet['element'] as $keyEl => $rowEl) {
                            if ($els->_id === $rowEl) {
                                $this->_detailFieldset[$keyDet]['element'][$keyEl] = $els;
                                $notShow = true;
                            }
                        }
                    }
                    if (false === $notShow) {
                        $formTab .= '<td>'.makeElement($els->_id, 'label', $els->_name).'</td>';
                        $formTab .= '<td>:</td>';
                        $formTab .= "<td id='".$this->_id.'_'.$els->_id."'>".$els->genEls().'</td>';
                    }
                }
            }
            $formTab .= '</tr>';
        }
        if (!empty($this->_detailFieldset)) {
            $formTab .= "<tr><td colspan='".$elsWidth * 3 ."'>";
            foreach ($this->_detailFieldset as $rowDet) {
                $formTab .= '<fieldset><legend><b>'.$rowDet['name'].'</b></legend><table>';
                foreach ($rowDet['element'] as $rowEl) {
                    $formTab .= '<tr>';
                    $formTab .= '<td>'.makeElement($rowEl->_id, 'label', $rowEl->_name).'</td>';
                    $formTab .= '<td>:</td>';
                    $formTab .= "<td id='".$this->_id.'_'.$rowEl->_id."'>".$rowEl->genEls().'</td>';
                    $formTab .= '</tr>';
                }
                $formTab .= '</table></fieldset>';
            }
            $formTab .= '</td></tr>';
        }

        $formTab .= "<tr><td colspan='".$elsWidth * 3 ."'>";
        $formTab .= makeElement($this->_id.'_numRow', 'hidden', '0');
        if (false === $this->_onedata) {
            $formTab .= makeElement('addFTBtn_'.$this->_id, 'btn', $_SESSION['lang']['save'], ['onclick' => "theFT.addFT('".$this->_id."','".$elsParam."','".$addElsParam."','".$this->_target."','".$alignParam."','".$_SESSION['lang']['editmode']."',false,'".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."','".$addActionJs."','".$this->_freezeEls."','".$this->_numberFormat."')"]);
            $formTab .= makeElement('clearFTBtn_'.$this->_id, 'btn', $_SESSION['lang']['cancel'], ['onclick' => "theFT.clearFT('".$this->_id."','".$elsParam."','".$addElsParam."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')", 'style' => 'display:none']);
        } else {
            $formTab .= makeElement('addFTBtn_'.$this->_id, 'btn', $_SESSION['lang']['save'], ['onclick' => "theFT.addFT('".$this->_id."','".$elsParam."','".$addElsParam."','".$this->_target."','".$alignParam."','".$_SESSION['lang']['editmode']."',true,'".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."','".$addActionJs."','".$this->_freezeEls."','".$this->_numberFormat."')"]);
        }

        $formTab .= makeElement('editFTBtn_'.$this->_id, 'btn', $_SESSION['lang']['save'], ['onclick' => "theFT.editFT('".$this->_id."','".$elsParam."','".$addElsParam."','".$this->_target."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')", 'style' => 'display:none']);
        $formTab .= makeElement('clearFTBtn_'.$this->_id, 'btn', $_SESSION['lang']['cancel'], ['onclick' => "theFT.clearFT('".$this->_id."','".$elsParam."','".$addElsParam."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')"]);
        $formTab .= '</td></tr>';
        $formTab .= '</table>';
        $formTab .= '</fieldset>';
        $formTab .= '</div>';
        $formTab .= "<div id='table_".$this->_id."'>";
        $formTab .= '<fieldset>';
        $formTab .= "<legend id='table_".$this->_id."_title'>";
        $formTab .= '<b>'.$this->_table->_name.'</b></legend>';
        $formTab .= "<div style='max-height:".$this->_tableWidth."px;overflow:auto'>";
        $formTab .= "<table class='sortable' cellspacing='1' border='0' ";
        $formTab .= "id='".$this->_table->_id."'>";
        $formTab .= "<thead id='thead_".$this->_id."'><tr class='rowheader'>";
        if (true === $this->_nourut) {
            $formTab .= '<td>#</td>';
        }

        if (false === $this->_noaction && false === $this->_onedata) {
            $formTab .= "<td colspan='".(2 + count($this->_addActions))."'>".$_SESSION['lang']['action'].'</td>';
        }

        foreach ($this->_form->_elements as $cols) {
            $formTab .= "<td id='head_".$cols->_id."' align='center' ";
            $formTab .= "style='width:".$cols->_length * 10 ."px'>".$cols->_name.'</td>';
        }
        $formTab .= '</tr></thead>';
        $formTab .= "<tbody id='tbody_".$this->_id."'>";
        foreach ($this->_table->_data as $key => $row) {
            $formTab .= "<tr id='tr_".$this->_id.'_'.$key."' class='rowcontent'>";
            if (true === $this->_nourut) {
                $formTab .= '<td>'.$key.'</td>';
            }

            if (false === $this->_noaction) {
                $formTab .= "<td><img id='editmodeFTBtn' class='zImgBtn' ";
                $formTab .= "src='images/".$_SESSION['theme']."/edit.png' ";
                if (empty($this->_beforeEditMode)) {
                    $formTab .= 'onclick="theFT.editmodeFT('.$key.",'".$this->_id."','".$elsParam."','".$addElsParam."','".$_SESSION['lang']['editmode']."','".$this->_freezeEls."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')\"></td>";
                } else {
                    $formTab .= 'onclick="theFT.editmodeFT('.$key.",'".$this->_id."','".$elsParam."','".$addElsParam."','".$_SESSION['lang']['editmode']."','".$this->_freezeEls."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."');".$this->_beforeEditMode.'('.$key.",'".$this->_id."','".$elsParam."','".$addElsParam."','".$_SESSION['lang']['editmode']."','".$this->_freezeEls."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')\"></td>";
                }

                $formTab .= "<td><img id='delFTBtn' class='zImgBtn' ";
                $formTab .= "src='images/".$_SESSION['theme']."/delete.png' ";
                $formTab .= 'onclick="theFT.delFT('.$key.",'".$this->_id."','".$elsParam."','".$addElsParam."','".$this->_target."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')\"></td>";
                foreach ($this->_addActions as $id => $attr) {
                    $formTab .= "<td><img id='".$id."' class='zImgBtn' ";
                    $formTab .= "src='images/".$_SESSION['theme'].'/'.$attr['img']."' ";
                    $formTab .= 'onclick="'.$attr['onclick'].'('.$key.',event)"></td>';
                }
            }

            $i = 0;
            foreach ($row as $id => $cont) {
                $isNF = false;
                foreach ($numFormatArr as $rowNF) {
                    if ($id === $rowNF) {
                        $isNF = true;
                    }
                }
                $formTab .= "<td id='".$this->_id.'_'.$id.'_'.$key."' ";
                $formTab .= "align='".$this->_form->_elements[$i]->_align."' ";
                $formTab .= "style='width:".$this->_form->_elements[$i]->_length."px' ";
                $formTab .= "value='".$cont."'>";
                if (true === $isNF) {
                    $formTab .= number_format($this->_table->_dataShow[$key][$id], 2);
                } else {
                    $formTab .= $this->_table->_dataShow[$key][$id];
                }

                $formTab .= '</td>';
                ++$i;
            }
            $formTab .= '</tr>';
        }
        $formTab .= '</tbody>';
        $formTab .= '<tfoot>';
        $formTab .= '</tfoot>';
        $formTab .= '</table>';
        $formTab .= '</div>';
        $formTab .= '</fieldset>';
        $formTab .= '</div>';
        $formTab .= '</div>';
        if (!empty($this->_afterCrud)) {
            $formTab .= "<script>theFT.afterCrud='".$this->_afterCrud."';console.log(theFT);</script>";
        }

        return $formTab;
    }

    public function render()
    {
        echo $this->prep();
    }
}

?>