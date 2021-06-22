<?php



function makeForm($id, $action, $elements = [], $method = 'POST')
{
    $form = "<form id='".$id."' name='".$id."' method='".$method."' action='".$action.".php'>";
    foreach ($elements as $el) {
        $form .= $el;
    }
    $form .= '</form>';

    return $form;
}

function genElement($elements = null, $padding = '1')
{
    if (null == $elements) {
        return null;
    }

    $els = "<table border='0' cellspacing='0' cellpadding='".$padding."'>";
    $maxL = 0;
    foreach ($elements as $key => $row1) {
        if ($maxL < count($row1)) {
            $maxL = count($row1);
        }

        $els .= '<tr>';
        foreach ($row1 as $row2) {
            if ('submit' == $key || 'button' == $key || 'btn' == $key) {
                $els .= "<td align='left' colspan='".$maxL."'>".$row2.'</td>';
            } else {
                $els .= "<td align='left'>".$row2.'</td>';
            }
        }
        $els .= '</tr>';
    }
    $els .= '</table>';

    return $els;
}

function genElTitle($title = 'Form', $elements = null, $padding = '1')
{
    if (null == $elements) {
        return null;
    }

    $els = "<fieldset style='float:left'><legend><b>".$title.'</b></legend>';
    $els .= "<table border='0' cellspacing='0' cellpadding='".$padding."'>";
    $maxL = 0;
    foreach ($elements as $key => $row1) {
        if ($maxL < count($row1)) {
            $maxL = count($row1);
        }

        $els .= '<tr>';
        foreach ($row1 as $row2) {
            if ('submit' == $key || 'button' == $key || 'btn' == $key) {
                $els .= "<td align='left' colspan='".$maxL."'>".$row2.'</td>';
            } else {
                $els .= "<td align='left'>".$row2.'</td>';
            }
        }
        $els .= '</tr>';
    }
    $els .= '</table></fieldset>';

    return $els;
}

function genElementMultiDim($title, $elements, $width = 1, $height = null, $padding = '1', $plain = false)
{
    if (isset($elements['submit']) && null != $elements['submit']) {
        $button = $elements['submit'][0];
    }

    if (isset($elements['button']) && null != $elements['button']) {
        $button = $elements['button'][0];
    }

    if (isset($elements['btn']) && null != $elements['btn']) {
        $button = $elements['btn'][0];
    }

    unset($elements['submit'], $elements['button'], $elements['btn']);

    $numEls = count($elements);
    if (null == $height) {
        $height = ceil($numEls / $width);
    }

    while ($width * $height < $numEls) {
        $height++;
    }
    $resEls = [];
    $w = 0;
    $h = 0;
    foreach ($elements as $el) {
        if ($h == $height) {
            $w++;
            $h = 0;
        }

        $resEls[$h][$w] = $el;
        $h++;
    }
    $els = '';
    if (!$plain) {
        $els .= "<fieldset style='float:left'><legend id='title_Form'><b>".$title.'</b></legend>';
    }

    $els .= "<div id='".$title."'><table border='0' cellspacing='0' cellpadding='".$padding."'>";
    $maxL = 0;
    foreach ($resEls as $h => $in1) {
        $els .= '<tr>';
        foreach ($in1 as $w => $content) {
            foreach ($content as $ni) {
                $els .= "<td style='padding-right:20px;font-size:12px'>".$ni.'</td>';
            }
        }
        $els .= '</tr>';
    }
    if (isset($button)) {
        $els .= "<tr><td colspan='".$width * 2 ."'>".$button.'</td></tr>';
    }

    $els .= '</table></div>';
    if (!$plain) {
        $els .= '</fieldset>';
    }

    return $els;
}

function genFormBtn($field, $table, $id, $page = null, $freeze = null, $empty = null, $pageEdit = null, $emptyField = '##', $disabled = '##', $opt = '{}')
{
    if (null == $empty) {
        $empty = ',false';
    } else {
        $empty = ',true';
    }

    if (null == $page) {
        if (null == $freeze) {
            if (null == $pageEdit) {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."',null,null".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."',null,'".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            } else {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."',null,null".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            }
        } else {
            if (null == $pageEdit) {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."',null,'".$freeze."'".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."',null,'".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            } else {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."',null,'".$freeze."'".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            }
        }
    } else {
        if (null == $freeze) {
            if (null == $pageEdit) {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."','".$page."',null".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."',null,'".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            } else {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."','".$page."',null".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            }
        } else {
            if (null == $pageEdit) {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."','".$page."','".$freeze."'".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."',null,'".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            } else {
                $formBtn = makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "addData('".$field."','".$id."','".$table."','".$page."','".$freeze."'".$empty.",'".$emptyField."','".$opt."')"]).makeElement('edit', 'btn', $_SESSION['lang']['save'], ['style' => 'display:none', 'onclick' => "editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')"]).makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "clearData('".$field."','".$disabled."')"]);
            }
        }
    }

    $formBtn .= makeElement('currRow', 'hidden', '0');

    return $formBtn;
} 

function makeElement($id, $type, $value = '', $attr = [], $options = [], $nameValue = null, $targetSatuan = null, $targetHarga = null, $parentEl = null)
{
    $el = '';
    null == $targetSatuan;
    (null == $targetSatuan ? ($targetSatuan = '') : null);
    null == $targetHarga;
    (null == $targetHarga ? ($targetHarga = '') : null);
    switch ($type) {
        case 'label':
            $el .= "<label for='".$id."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '>'.$value.'</label>';

            break;
        case 'tanggal':
        case 'date':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' type='text'";
            $el .= " onmousemove='setCalendar(this.id)' readonly='readonly' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= " style='cursor:pointer' />";

            break;
        case 'rangedate':
        case 'period':
        case 'periode':
            $el .= "<input id='".$id."_from' name='".$id."_from' class='myinputtext' type='text'";
            $el .= " onmousemove='setCalendar(this.id)' readonly='readonly' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= " style='cursor:pointer' />";
            $el .= ' s/d ';
            $el .= "<input id='".$id."_until' name='".$id."_until' class='myinputtext' type='text'";
            $el .= " onmousemove='setCalendar(this.id)' readonly='readonly' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= " style='cursor:pointer' />";

            break;
        case 'txt':
        case 'text':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' type='text'";
            if (!empty($options)) {
                $el .= " list='".$id."_list'";
            }

            $el .= " onkeypress='return tanpa_kutip(event)' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';
            if (!empty($options)) {
                $el .= "<datalist id='".$id."_list'>";
                foreach ($options as $val => $name) {
                    $el .= "<option value='".$name."'>";
                }
                $el .= '</datalist>';
            }

            break;
        case 'textupper':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtextuppercase' type='text' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'textlower':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtextlowercase' type='text' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'textnum':
        case 'textnumber':
        case 'textnumeric':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' ";
            $el .= "onkeypress='return angka_doang(event)' type='text' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'textnumw-':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' ";
            $el .= "onkeypress='return tanpa_kutip_dan_sepasi(event)' type='text' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'textnumwsatuan':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' ";
            $el .= "onkeypress='return angka_doang(event)' type='text' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>&nbsp;';
            $el .= "<input id='".$id."_satuan' name='".$id."_satuan' class='myinputtext' type='text'";
            $el .= " disabled='disabled' value='".$nameValue."' style='width:30px'";
            $el .= '/>';

            break;
        case 'select':
        case 'dropdown':
            $el .= "<select id='".$id."' name='".$id."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '>';
            foreach ($options as $val => $name) {
                if ($value == $val) {
                    $el .= "<option value='".$val."' selected>".$name.'</option>';
                } else {
                    $el .= "<option value='".$val."'>".$name.'</option>';
                }
            }
            $el .= '</select>';

            break;
        case 'selectsearch':
            $el .= "<select id='".$id."' name='".$id."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '>';
            foreach ($options as $val => $name) {
                if ($value == $val) {
                    $el .= "<option value='".$val."' selected>".$name.'</option>';
                } else {
                    $el .= "<option value='".$val."'>".$name.'</option>';
                }
            }
            $el .= '</select>';
            $el .= "<img id='".$id."_find' onclick='z.elSearch(\"".$id.'",event';
            if (null != $parentEl) {
                $el .= ',"'.$parentEl.'"';
            }

            $el .= ")' ";
            $el .= "class=zImgBtn src='images/onebit_02.png' style='position:relative;top:5px'>";

            break;
        case 'dk':
            $el .= "<select id='".$id."_dk' name='".$id."_dk' style='width:70px'>";
            $el .= "<option value='D' ";
            if (0 <= $value) {
                $el .= 'selected';
            }

            $el .= '>'.$_SESSION['lang']['debet'].'</option>';
            $el .= "<option value='K' ";
            if ($value < 0) {
                $el .= 'selected';
            }

            $el .= '>'.$_SESSION['lang']['kredit'].'</option>';
            $el .= '</select>';
            $el .= "<input id='".$id."_nilai' name='".$id."_nilai' class='myinputtextnumber' ";
            $el .= "onkeypress='return angka_doang(event)' type='text' value='".abs($value)."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'chk':
        case 'check':
        case 'checkbox':
            $el .= "<input id='".$id."' name='".$id."' type='checkbox'";
            $el .= " value='".$value."'";
            if (1 == $value) {
                $el .= ' checked';
            }

            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'password':
        case 'pwd':
            $el .= "<input id='".$id."' name='".$id."' type='password' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'button':
        case 'btn':
            $el .= "<button id='".$id."' name='".$id."' class='mybutton'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '>'.$value.'</button>';

            break;
        case 'submit':
            $el .= "<input id='".$id."' name='".$id."' type='submit' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'hidden':
        case 'hid':
            $el .= "<input id='".$id."' name='".$id."' type='hidden' value='".$value."'";
            if (is_array($attr) && $attr != []) {
                foreach ($attr as $key => $row) {
                    $el .= ' '.$key.'="'.$row.'"';
                }
            }

            $el .= '/>';

            break;
        case 'searchBarang':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
            $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
            $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
            $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
            $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
            if (isset($attr['disabled']) && 'disabled' == $attr['disabled']) {
                $el .= 'disabled ';
            }

            $el .= "onclick=\"getInvName(event,'".$id."','".$targetSatuan."','".$targetHarga."')\"/>".$_SESSION['lang']['find'].'</button>';

            break;
        case 'searchKegiatan':
        case 'searchKeg':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
            $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
            $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
            $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
            $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
            if (isset($attr['disabled']) && 'disabled' == $attr['disabled']) {
                $el .= 'disabled ';
            }

            $el .= "onclick=\"getSearch(event,'".$id."','kegiatan')\"/>".$_SESSION['lang']['find'].'</button>';

            break;
        case 'searchAsset':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
            $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
            $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
            $el .= "value='".htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8')."' type='text' style='width:170px' disabled />&nbsp;";
            $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
            if (isset($attr['disabled']) && 'disabled' == $attr['disabled']) {
                $el .= 'disabled ';
            }

            $el .= "onclick=\"getSearch(event,'".$id."','asset')\"/>".$_SESSION['lang']['find'].'</button>&nbsp;&nbsp;';
            $el .= "<button id='".$id."_clear' name='".$id."_clear' class='mybutton'";
            $el .= "onclick=\"getCleared('".$id."','".$id."_name');\"/>".$_SESSION['lang']['delete'].'</button>';

            break;
        case 'searchCustomer':
        case 'searchCust':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
            $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
            $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
            $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
            $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
            if (isset($attr['disabled']) && 'disabled' == $attr['disabled']) {
                $el .= 'disabled ';
            }

            $el .= "onclick=\"getSearch(event,'".$id."','customer')\"/>".$_SESSION['lang']['find'].'</button>';

            break;
        case 'searchSupplier':
        case 'searchSupl':
            $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
            $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
            $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
            $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
            $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
            if (isset($attr['disabled']) && 'disabled' == $attr['disabled']) {
                $el .= 'disabled ';
            }

            $el .= "onclick=\"getSearch(event,'".$id."','supplier')\"/>".$_SESSION['lang']['find'].'</button>';

            break;
        case 'jammenit':
            $optJam = [];
            $optMenit = [];
            $tmpVal = explode(':', $value);
            $valueJam = $tmpVal[0];
            if (1 < count($tmpVal)) {
                $valueMenit = $tmpVal[1];
            } else {
                $valueMenit = '00';
            }

            for ($i = 0; $i < 60; $i++) {
                if ($i < 24) {
                    $optJam[addZero($i, 2)] = addZero($i, 2);
                }

                $optMenit[addZero($i, 2)] = addZero($i, 2);
            }
            $el .= "<select id='".$id."_jam' name='".$id."'_jam";
            $el .= '>';
            foreach ($optJam as $val) {
                if ($valueJam == $val) {
                    $el .= "<option value='".$val."' selected>".$val.'</option>';
                } else {
                    $el .= "<option value='".$val."'>".$val.'</option>';
                }
            }
            $el .= '</select>';
            $el .= ':';
            $el .= "<select id='".$id."_menit' name='".$id."'_menit";
            $el .= '>';
            foreach ($optMenit as $val) {
                if ($valueMenit == $val) {
                    $el .= "<option value='".$val."' selected>".$val.'</option>';
                } else {
                    $el .= "<option value='".$val."'>".$val.'</option>';
                }
            }
            $el .= '</select>';

            break;
        case 'bulantahun':
            $optBulan = [];
            for ($i = 1; $i < 13; $i++) {
                $optBulan[$i] = numToMonth($i, substr($_SESSION['language'], 0, 1), 'long');
            }
            $tmpVal = explode('-', $value);
            $valueBulan = $tmpVal[0];
            if (1 < count($tmpVal)) {
                $valueTahun = $tmpVal[1];
            } else {
                $valueTahun = date('Y');
            }

            $el .= "<select id='".$id."_bulan' name='".$id."'_bulan";
            $el .= '>';
            foreach ($optBulan as $val => $text) {
                if ($valueBulan == $val) {
                    $el .= "<option value='".$val."' selected>".$text.'</option>';
                } else {
                    $el .= "<option value='".$val."'>".$text.'</option>';
                }
            }
            $el .= '</select>';
            $el .= "<input id='".$id."_tahun' name='".$id."_tahun' class='myinputtextnumber' ";
            $el .= "onkeypress='return angka_doang(event)' type='text' value='".$valueTahun."' ";
            $el .= "maxlength='4' style='width:40px'";
            $el .= '/>';

            break;
        default:
            break;
    }

    return $el;
}

function makeOption($dbname, $tableName, $column, $where = null, $mode = '0', $empty = false)
{
    $cols = explode(',', $column);
    if (true == $empty) {
        $option = ['' => ''];
    } else {
        $option = [];
    }

    switch ($mode) {
        case '1':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]].' ('.$row[$cols[0]].')';
            }

            break;
        case '11':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]];
            }

            break;
        case '2':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[0], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                if (isset($cols[2])) {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]].' - '.$row[$cols[2]];
                } else {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]];
                }
            }

            break;
        case '3':
            foreach ($column as $row => $isi) {
                $option[$row] = $isi;
            }

            break;
        case '4':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]].' ('.$row[$cols[2]].')';
            }

            break;
        case '5':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]].' - '.$row[$cols[2]];
            }

            break;
        case '6':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[3], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[3]].' - '.$row[$cols[1]].' ('.$row[$cols[2]].')';
            }

            break;
        case '8':
            $namaOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
            if (isset($cols[2])) {
                $order = $cols[2];
            } else {
                $order = $cols[1];
            }

            $query = selectQuery($dbname, $tableName, $column, $where, $order, true);
            $data = fetchData($query);
            foreach ($data as $row) {
                if (isset($cols[2])) {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]].' ('.$namaOrg[$row[$cols[2]]].')';
                } else {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]];
                }
            }

            break;
        case '7':
            $query = ' SHOW COLUMNS FROM `'.$dbname.'`.`'.$tableName."` LIKE '".$cols[0]."' ";
            $result = mysql_query($query) || exit('error getting enum field '.mysql_error());
            $row = mysql_fetch_array($result, MYSQL_NUM);
            $regex = "/'(.*?)'/";
            preg_match_all($regex, $row[1], $enum_array);
            $enum_fields = [];
            foreach ($enum_array[1] as $row) {
                $enum_fields[$row] = $row;
            }
            foreach ($enum_fields as $row) {
                $option[$row] = $row;
            }

            break;
        case '9':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[2]].' - '.$row[$cols[1]];
            }

            break;
        default:
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]];
            }

            break;
    }
    return $option;
}

function makeOptionX($dbname, $tableName, $column, $where = null, $mode = '0', $empty = false)
{
    $cols = explode(',', $column);
    if (true == $empty) {
        $option = ['' => ''];
    } else {
        $option = [];
    }

    switch ($mode) {
        case '1':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]].' ('.$row[$cols[0]].')';
            }

            break;
        case '2':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[0], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                if (isset($cols[2])) {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]].' - '.$row[$cols[2]];
                } else {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]];
                }
            }

            break;
        case '3':
            foreach ($column as $row => $isi) {
                $option[$row] = $isi;
            }

            break;
        case '4':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]].' ('.$row[$cols[2]].')';
            }

            break;
        case '5':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]].' - '.$row[$cols[2]];
            }

            break;
        case '6':
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[3], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[3]].' - '.$row[$cols[1]].' ('.$row[$cols[2]].')';
            }

            break;
        case '8':
            $namaOrg = makeoption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
            if (isset($cols[2])) {
                $order = $cols[2];
            } else {
                $order = $cols[1];
            }

            $query = selectQuery($dbname, $tableName, $column, $where, $order, true);
            $data = fetchData($query);
            foreach ($data as $row) {
                if (isset($cols[2])) {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]].' ('.$namaOrg[$row[$cols[2]]].')';
                } else {
                    $option[$row[$cols[0]]] = $row[$cols[0]].' - '.$row[$cols[1]];
                }
            }

            break;
        case '7':
            $query = ' SHOW COLUMNS FROM `'.$dbname.'`.`'.$tableName."` LIKE '".$cols[0]."' ";
            $result = mysql_query($query) || exit('error getting enum field '.mysql_error());
            $row = mysql_fetch_array($result, MYSQL_NUM);
            $regex = "/'(.*?)'/";
            preg_match_all($regex, $row[1], $enum_array);
            $enum_fields = [];
            foreach ($enum_array[1] as $row) {
                $enum_fields[$row] = $row;
            }
            foreach ($enum_fields as $row) {
                $option[$row] = $row;
            }

            break;
        default:
            $query = selectQuery($dbname, $tableName, $column, $where, $cols[1], true);
            $data = fetchData($query);
            foreach ($data as $row) {
                $option[$row[$cols[0]]] = $row[$cols[1]];
            }

            break;
    }

    return $option;
}

function makeOptionAkun($dbname, $where = null, $empty = false)
{
    if (true == $empty) {
        $option = ['' => ''];
    } else {
        $option = [];
    }

    $optKel = makeoption($dbname, 'keu_5akun', 'noakun,namaakun', 'length(trim(noakun))=3');
    if (null != $where) {
        $where = 'detail=1 and '.$where;
    } else {
        $where = 'detail=1';
    }

    $query = selectQuery($dbname, 'keu_5akun', 'noakun,namaakun', $where, 'noakun', true);
    $data = fetchData($query);
    foreach ($data as $row) {
        if (isset($optKel[substr($row['noakun'], 0, 3)])) {
            $option[$row['noakun']] = $row['noakun'].' - '.$row['namaakun'].' ('.$optKel[substr($row['noakun'], 0, 3)].')';
        }
    }

    return $option;
}

function optionMonth($langcode = 'E', $format = 'short')
{
    $month = [];
    for ($i = 1; $i <= 12; $i++) {
        $month[$i] = numToMonth($i, $langcode, $format);
    }

    return $month;
}

function addZero($tmpNum, $maxNum)
{
    $len = $maxNum;
    while (strlen($tmpNum) < $len) {
        $tmpNum = '0'.$tmpNum;
    }

    return $tmpNum;
}

function optionNum($arrEl)
{
    $len = strlen($arrEl);
    $strArr = $len;
    $num = [];
    for ($i = 0; $i < $arrEl; $i++) {
        $tmpI = (string) $i;
        $tmpNo = addzero($tmpI, $strArr);
        $num[$i] = $tmpNo;
    }

    return $num;
}

function makeFieldset($title, $id, $cont = null, $bold = null)
{
    $fs = '<fieldset>';
    $fs .= '<legend>';
    null == $bold;
    (null != $bold ? ($fs .= '<b>') : null);
    $fs .= $title;
    null == $bold;
    (null != $bold ? ($fs .= '</b>') : null);
    $fs .= '</legend>';
    $fs .= "<div id='".$id."'>";
    $fs .= $cont;
    $fs .= '</div></fieldset>';

    return $fs;
}

function getFirstKey($arr)
{
    return end(array_reverse(array_keys($arr)));
}

function getFirstContent($arr)
{
    return end(array_reverse(array_values($arr)));
}

?>