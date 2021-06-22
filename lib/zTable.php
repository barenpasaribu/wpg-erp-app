<?php

include_once 'lib/eagrolib.php';
function makeTable($id, $bodyId = '', $header = [], $content = [], $footer = [], $sortable = false, $tr = 'tr', $click = null)
{
    if ($sortable) {
        $tables = "<table id='".$id."' name='".$id."' class='sortable' cellspacing='1' border='0'>";
    } else {
        $tables = "<table id='".$id."' name='".$id."' class='data' cellspacing='1' border='0'>";
    }

    $tables .= "<thead><tr class='rowheader'>";
    foreach ($header as $hName) {
        $tables .= '<td>'.$hName.'</td>';
    }
    $tables .= '</tr></thead>';
    $tables .= "<tbody id='".$bodyId."'>";
    foreach ($content as $key => $row) {
        if ($click != null) {
            $tables .= "<tr id='".$tr.'_'.$key."' class='rowcontent' onclick='".$click.'('.$key.")'>";
        } else {
            $tables .= "<tr id='".$tr.'_'.$key."' class='rowcontent'>";
        }

        $i = 0;
        foreach ($row as $c) {
            $tables .= "<td id='col_".$header[$i].'_'.$key."'>".$c.'</td>';
            $i++;
        }
        $tables .= '</tr>';
    }
    $tables .= '</tbody>';
    $tables .= '<tfoot>';
    foreach ($footer as $fName) {
        $tables .= '<td>'.$hName.'</td>';
    }
    $tables .= '</tfoot>';
    $tables .= '</table>';

    return $tables;
}

function makeCompleteTable($id, $bodyId = '', $header = [], $content = [], $footer = [], $sortable = false, $tr = 'tr', $click = null)
{
    if ($sortable) {
        $tables = "<table id='".$id."' name='".$id."' class='sortable' cellspacing='1' border='0'>";
    } else {
        $tables = "<table id='".$id."' name='".$id."' class='data' cellspacing='1' border='0'>";
    }

    $tables .= "<thead><tr class='rowheader'>";
    $field = [];
    foreach ($header as $hField => $hName) {
        $tables .= '<td>'.$hName.'</td>';
        $field[] = $hField;
    }
    $tables .= '</tr></thead>';
    $tables .= "<tbody id='".$bodyId."'>";
    foreach ($content as $key => $row) {
        if ($click != null) {
            $tables .= "<tr id='".$tr.'_'.$key."' class='rowcontent' onclick='".$click."'>";
        } else {
            $tables .= "<tr id='".$tr.'_'.$key."' class='rowcontent'>";
        }

        $i = 0;
        foreach ($row as $c) {
            $tables .= "<td id='col_".$field[$i].'_'.$key."'>".$c.'</td>';
            $i++;
        }
        $tables .= '</tr>';
    }
    $tables .= '</tbody>';
    $tables .= '<tfoot>';
    foreach ($footer as $fName) {
        $tables .= '<td>'.$hName.'</td>';
    }
    $tables .= '</tfoot>';
    $tables .= '</table>';

    return $tables;
}

function masterTable($dbname, $table, $column = '*', $headerSetting = [], $dataSetting = [], $cond = [], $fForm = [], $printTo = null, $freezeField = null, $printShow = true, $postTo = null, $opt = [], $listName = null, $test = null)
{
    if ($postTo == null) {
        $postTo = 'null';
    }

    if ($printTo == null) {
        $printTo = 'null';
    }

    $query = 'select ';
    $colStr = '';
    if (is_array($column) && $column != []) {
        for ($i = 0; $i < count($column); $i++) {
            if ($i == 0) {
                $query .= $column[$i];
                $colStr .= $column[$i];
            } else {
                $query .= ','.$column[$i];
                $colStr .= ','.$column[$i];
            }
        }
    } else {
        $query .= '*';
    }

    $query .= ' from '.$dbname.'.'.$table;
    if ($cond != null) {
        $condStr = '';
        if (is_array($cond)) {
            $condPdf = $cond['sep'].'^^';
            unset($cond['sep']);
            foreach ($cond as $row) {
                foreach ($row as $attr => $val) {
                    if ($row == end($cond)) {
                        $condPdf .= $attr.'**'.$val;
                        if (is_string($val)) {
                            $condStr .= $attr."='".$val."'";
                        } else {
                            $condStr .= $attr.'='.$val;
                        }
                    } else {
                        $condPdf .= $attr.'**'.$val.'~~';
                        if (is_string($val)) {
                            $condStr .= $attr."='".$val."' OR ";
                        } else {
                            $condStr .= $attr.'='.$val.' OR ';
                        }
                    }
                }
            }
        } else {
            $condPdf = $cond;
            $condStr = $cond;
        }

        $query .= ' where '.$condStr;
    } else {
        $condPdf = null;
    }

    $res = mysql_query($query);
    $j = mysql_num_fields($res);
    $i = 0;
    $field = [];
    $fieldStr = '';
    $primary = [];
    for ($primaryStr = ''; $i < $j; $i++) {
        $meta = mysql_fetch_field($res, $i);
        $field[] = strtolower($meta->name);
        $fieldStr .= '##'.strtolower($meta->name);
        if ($meta->primary_key == '1') {
            $primary[] = strtolower($meta->name);
            $primaryStr .= '##'.strtolower($meta->name);
        }
    }
    if ($fForm == []) {
        $fForm = $field;
    }

    $result = [];
    while ($bar = mysql_fetch_assoc($res)) {
        $result[] = $bar;
    }
    $tables = '<fieldset><legend><b>'.$_SESSION['lang']['list'].' : ';
    if ($listName == null) {
        $tables .= $table;
    } else {
        $tables .= $listName;
    }

    $tables .= '</b></legend>';
    $tables .= "<img src='images/pdf.jpg' title='PDF Format'\r\n\t     style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','".$colStr."','".$condPdf."','".$printTo."',event)\">&nbsp;";
    $tables .= "<img src='images/printer.png' title='Print Page'\r\n\t     style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";
    if ($test == '1') {
        $tables .= '&nbsp<img title="MS.Excel" class="resicon" src="images/excel.jpg" onclick="dataKeExcel(event)">';
    }

    if ($printShow) {
        $tables .= "<div style='height:170px;overflow:auto'>";
    }

    $tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";
    $tables .= "<thead><tr class='rowheader'>";
    if ($headerSetting == null) {
        foreach ($field as $hName) {
            $tables .= '<td>'.$_SESSION['lang'][$hName].'</td>';
        }
    } else {
        foreach ($headerSetting as $hSet) {
            if (!isset($hSet['span'])) {
                $hSet['span'] = '0';
            }

            if (!isset($hSet['align'])) {
                $hSet['align'] = 'left';
            }

            $tables .= "<td colspan='".$hSet['span']."' align='".$hSet['align']."'>".$hSet['name'].'</td>';
        }
    }

    $tables .= "<td colspan='2'>".$_SESSION['lang']['action'].'</td>';
    $tables .= '</tr></thead>';
    $tables .= "<tbody id='mTabBody'>";
    $i = 0;
    foreach ($result as $row) {
        $tables .= "<tr id='tr_".$i."' class='rowcontent'>";
        $tmpVal = '';
        $tmpKey = '';
        $j = 0;
        foreach ($row as $b => $c) {
            $tmpC = explode('-', $c);
            if (count($tmpC) == 3) {
                $c = $tmpC[2].'-'.$tmpC[1].'-'.$tmpC[0];
            }

            if (!isset($dataSetting[$b]['type'])) {
                $dataSetting[$b]['type'] = 'default';
            }

            if (isset($opt[$fForm[$j]])) {
                $theVal = $opt[$fForm[$j]][$c];
            } else {
                $theVal = $c;
            }

            switch ($dataSetting[$b]['type']) {
                case 'numeric':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='right' value='".$c."'>".number_format($theVal, 0).'</td>';

                    break;
                case 'currency':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='right' value='".$c."'>".number_format($theVal, 2).'</td>';

                    break;
                case 'string':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='left' value='".$c."'>".$theVal.'</td>';

                    break;
                default:
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' value='".$c."'>".$theVal.'</td>';

                    break;
            }
            $tmpVal .= '##'.$c;
            if (in_array($fForm[$j], $primary, true)) {
                $tmpKey .= '##'.$c;
            }

            $j++;
        }
        if ($freezeField != null)  {
            $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','".$freezeField."')\"\r\n\t\t     class='zImgBtn' src='images/001_45.png' /></td>";
        } else {
            $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."')\"\r\n\t\t     class='zImgBtn' src='images/001_45.png' /></td>";
        }

        if ($postTo == 'null') {
            $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."',null,'".$table."')\"\r\n\t\t\t    class='zImgBtn' src='images/delete_32.png' /></td>";
        } else {
            $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."','".$postTo."','".$table."')\"\r\n\t\t\t    class='zImgBtn' src='images/delete_32.png' /></td>";
        }

        $tables .= '</tr>';
        $i++;
    }
    $tables .= '</tbody>';
    $tables .= '<tfoot>';
    $tables .= '</tfoot>';
    $tables .= '</table>';
    if ($printShow) {
        $tables .= '</div>';
    }

    $tables .= '</fieldset>';

    return $tables;
}

function masterTableBlok($dbname, $table, $tot, $column = '*', $headerSetting = [], $dataSetting = [], $cond = [], $fForm = [], $printTo = null, $freezeField = null, $printShow = true, $postTo = null, $opt = [], $listName = null)
{
    if ($postTo == null) {
        $postTo = 'null';
    }

    if ($printTo == null) {
        $printTo = 'null';
    }

    $query = 'select ';
    $colStr = '';
    if (is_array($column) && $column != []) {
        for ($i = 0; $i < count($column); $i++) {
            if ($i == 0) {
                $query .= $column[$i];
                $colStr .= $column[$i];
            } else {
                $query .= ','.$column[$i];
                $colStr .= ','.$column[$i];
            }
        }
    } else {
        $query .= '*';
    }

    $query .= ' from '.$dbname.'.'.$table;
    if ($cond != null) {
        $condStr = '';
        if (is_array($cond)) {
            $condPdf = $cond['sep'].'^^';
            unset($cond['sep']);
            foreach ($cond as $row) {
                foreach ($row as $attr => $val) {
                    if ($row == end($cond)) {
                        $condPdf .= $attr.'**'.$val;
                        if (is_string($val)) {
                            $condStr .= $attr."='".$val."'";
                        } else {
                            $condStr .= $attr.'='.$val;
                        }
                    } else {
                        $condPdf .= $attr.'**'.$val.'~~';
                        if (is_string($val)) {
                            $condStr .= $attr."='".$val."' OR ";
                        } else {
                            $condStr .= $attr.'='.$val.' OR ';
                        }
                    }
                }
            }
        } else {
            $condPdf = $cond;
            $condStr = $cond;
        }

        $query .= ' where '.$condStr;
    } else {
        $condPdf = null;
    }

    $res = mysql_query($query);
    $j = mysql_num_fields($res);
    $i = 0;
    $field = [];
    $fieldStr = '';
    $primary = [];
    for ($primaryStr = ''; $i < $j; $i++) {
        $meta = mysql_fetch_field($res, $i);
        $field[] = strtolower($meta->name);
        $fieldStr .= '##'.strtolower($meta->name);
        if ($meta->primary_key == '1') {
            $primary[] = strtolower($meta->name);
            $primaryStr .= '##'.strtolower($meta->name);
        }
    }
    if ($fForm == []) {
        $fForm = $field;
    }

    $result = [];
    while ($bar = mysql_fetch_assoc($res)) {
        $result[] = $bar;
    }
    $tables = '<fieldset><legend><b>'.$_SESSION['lang']['list'].' : ';
    if ($listName == null) {
        $tables .= $table;
    } else {
        $tables .= $listName;
    }

    $tables .= '</b></legend>';
    $tables .= "<img src='images/pdf.jpg' title='PDF Format'\r\n\t     style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','".$colStr."','".$condPdf."','".$printTo."',event)\">&nbsp;";
    $tables .= "<img src='images/printer.png' title='Print Page'\r\n\t     style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";
    if ($printShow) {
        $tables .= "<div style='height:170px;overflow:auto'>";
    }

    $tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";
    $tables .= "<thead><tr class='rowheader'>";
    if ($headerSetting == null) {
        foreach ($field as $hName) {
            $tables .= '<td>'.$_SESSION['lang'][$hName].'</td>';
        }
    } else {
        foreach ($headerSetting as $hSet) {
            if (!isset($hSet['span'])) {
                $hSet['span'] = '0';
            }

            if (!isset($hSet['align'])) {
                $hSet['align'] = 'left';
            }

            $tables .= "<td colspan='".$hSet['span']."' align='".$hSet['align']."'>".$hSet['name'].'</td>';
        }
    }

    $tables .= "<td colspan='2'>".$_SESSION['lang']['action'].'</td>';
    $tables .= '</tr></thead>';
    $tables .= "<tbody id='mTabBody'>";
    $i = 0;
    foreach ($result as $row) {
        $tables .= "<tr id='tr_".$i."' class='rowcontent'>";
        $tmpVal = '';
        $tmpKey = '';
        $j = 0;
        foreach ($row as $b => $c) {
            $tmpC = explode('-', $c);
            if (count($tmpC == 3)) {
                $c = $tmpC[2].'-'.$tmpC[1].'-'.$tmpC[0];
            }

            if (!isset($dataSetting[$b]['type'])) {
                $dataSetting[$b]['type'] = 'default';
            }

            if (isset($opt[$fForm[$j]])) {
                $theVal = $opt[$fForm[$j]][$c];
            } else {
                $theVal = $c;
            }

            switch ($dataSetting[$b]['type']) {
                case 'numeric':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='right' value='".$c."'>".number_format($theVal, 2).'</td>';
                    $total[$b] += $theVal;

                    break;
                case 'currency':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='right' value='".$c."'>".number_format($theVal, 2).'</td>';
                    $total[$b] += $theVal;

                    break;
                case 'string':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='left' value='".$c."'>".$theVal.'</td>';

                    break;
                case 'month':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='left' value='".$c."'>".numToMonth($theVal).'</td>';

                    break;
                default:
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' value='".$c."'>".$theVal.'</td>';

                    break;
            }
            $tmpVal .= '##'.$c;
            if (in_array($fForm[$j], $primary, true)) {
                $tmpKey .= '##'.$c;
            }

            $j++;
        }
        if ($freezeField != null) {
            $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','".$freezeField."')\"\r\n\t\t     class='zImgBtn' src='images/001_45.png' /></td>";
        } else {
            $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."')\"\r\n\t\t     class='zImgBtn' src='images/001_45.png' /></td>";
        }

        if ($postTo == 'null') {
            $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."',null,'".$table."')\"\r\n\t\t\t    class='zImgBtn' src='images/delete_32.png' /></td>";
        } else {
            $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."','".$postTo."','".$table."')\"\r\n\t\t\t    class='zImgBtn' src='images/delete_32.png' /></td>";
        }

        $tables .= '</tr>';
        $i++;
    }
    if ($tot == 1) {
        $rt = count($column);
        $tables .= '<thead><tr class=rowheader>';
        foreach ($column as $brsDt) {
            if ($total[$brsDt] == '') {
                $tables .= "<td colspan='".$sr."'>&nbsp;</td>";
            } else {
                $tables .= '<td align=right>'.number_format($total[$brsDt], 2).'</td>';
            }
        }
        $tables .= '<td colspan=2>&nbsp;</td>';
        $tables .= '</tr></thead>';
    }

    $tables .= '</tbody>';
    $tables .= '<tfoot>';
    $tables .= '</tfoot>';
    $tables .= '</table>';
    if ($printShow) {
        $tables .= '</div>';
    }

    $tables .= '</fieldset>';

    return $tables;
}

function masterTableGapok($dbname, $table, $column = '*', $headerSetting = [], $dataSetting = [], $cond = [], $fForm = [], $printTo = null, $freezeField = null, $printShow = true, $postTo = null, $opt = [])
{
    if ($postTo == null) {
        $postTo = 'null';
    }

    if ($printTo == null) {
        $printTo = 'null';
    }

    $query = 'select ';
    $colStr = '';
    if (is_array($column) && $column != []) {
        for ($i = 0; $i < count($column); $i++) {
            if ($i == 0) {
                $query .= $column[$i];
                $colStr .= $column[$i];
            } else {
                $query .= ','.$column[$i];
                $colStr .= ','.$column[$i];
            }
        }
    } else {
        $query .= '*';
    }

    $query .= ' from '.$dbname.'.'.$table;
    if ($cond != null) {
        $condStr = '';
        if (is_array($cond)) {
            $condPdf = $cond['sep'].'^^';
            unset($cond['sep']);
            foreach ($cond as $row) {
                foreach ($row as $attr => $val) {
                    if ($row == end($cond)) {
                        $condPdf .= $attr.'**'.$val;
                        if (is_string($val)) {
                            $condStr .= $attr."='".$val."'";
                        } else {
                            $condStr .= $attr.'='.$val;
                        }
                    } else {
                        $condPdf .= $attr.'**'.$val.'~~';
                        if (is_string($val)) {
                            $condStr .= $attr."='".$val."' OR ";
                        } else {
                            $condStr .= $attr.'='.$val.' OR ';
                        }
                    }
                }
            }
        } else {
            $condPdf = $cond;
            $condStr = $cond;
        }

        $query .= ' where '.$condStr;
    } else {
        $condPdf = null;
    }

    $res = mysql_query($query);
    $j = mysql_num_fields($res);
    $i = 0;
    $field = [];
    $fieldStr = '';
    $primary = [];
    for ($primaryStr = ''; $i < $j; $i++) {
        $meta = mysql_fetch_field($res, $i);
        $field[] = strtolower($meta->name);
        $fieldStr .= '##'.strtolower($meta->name);
        if ($meta->primary_key == '1') {
            $primary[] = strtolower($meta->name);
            $primaryStr .= '##'.strtolower($meta->name);
        }
    }
    if ($fForm == []) {
        $fForm = $field;
    }

    $result = [];
    while ($bar = mysql_fetch_assoc($res)) {
        $result[] = $bar;
    }
    $tables = '<fieldset><legend><b>'.$_SESSION['lang']['list'].' : '.$table.'</b></legend>';
    $tables .= "<img src='images/pdf.jpg' title='PDF Format'\r\n\t     style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','".$colStr."','".$condPdf."','slave_master_pdf_2',event)\">&nbsp;";
    $tables .= "<img src='images/printer.png' title='Print Page'\r\n\t     style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";
    if ($printShow) {
        $tables .= "<div style='height:170px;overflow:auto'>";
    }

    $tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";
    $tables .= "<thead><tr class='rowheader'>";
    if ($headerSetting == null) {
        foreach ($field as $hName) {
            $tables .= '<td>'.$_SESSION['lang'][$hName].'</td>';
        }
    } else {
        foreach ($headerSetting as $hSet) {
            if (!isset($hSet['span'])) {
                $hSet['span'] = '0';
            }

            if (!isset($hSet['align'])) {
                $hSet['align'] = 'left';
            }

            $tables .= "<td colspan='".$hSet['span']."' align='".$hSet['align']."'>".$hSet['name'].'</td>';
        }
    }

    $tables .= "<td colspan='2'>".$_SESSION['lang']['action'].'</td>';
    $tables .= '</tr></thead>';
    $tables .= "<tbody id='mTabBody'>";
    $i = 0;
    foreach ($result as $row) {
        $tables .= "<tr id='tr_".$i."' class='rowcontent'>";
        $tmpVal = '';
        $tmpKey = '';
        $j = 0;
        foreach ($row as $b => $c) {
            $tmpC = explode('-', $c);
            if (count($tmpC) == 3) {
                $c = $tmpC[2].'-'.$tmpC[1].'-'.$tmpC[0];
            }

            if (!isset($dataSetting[$b]['type'])) {
                $dataSetting[$b]['type'] = 'default';
            }

            if (isset($opt[$fForm[$j]])) {
                $theVal = $opt[$fForm[$j]][$c];
            } else {
                $theVal = $c;
            }

            switch ($dataSetting[$b]['type']) {
                case 'numeric':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='right' value='".$c."'>".number_format($theVal, 0).'</td>';

                    break;
                case 'currency':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='right' value='".$c."'>".number_format($theVal, 2).'</td>';

                    break;
                case 'string':
                    $tables .= "<td id='".$fForm[$j].'_'.$i."' align='left' value='".$c."'>".$theVal.'</td>';

                    break;
                default:
                    if ($row['karyawanid']) {
                        $sDt = 'select namakaryawan,karyawanid,lokasitugas from '.$dbname.".datakaryawan  where karyawanid='".$row['karyawanid']."'";
                        $qDt = mysql_query($sDt);
                        $rDt = mysql_fetch_assoc($qDt);
                        if ($rDt['karyawanid'] == $c) {
                            $theVal = $rDt['namakaryawan'].'['.$rDt['lokasitugas'].']';
                        }
                    }

                    $tables .= "<td id='".$fForm[$j].'_'.$i."' value='".$c."'>".$theVal.'</td>';

                    break;
            }
            $tmpVal .= '##'.$c;
            if (in_array($fForm[$j], $primary, true)) {
                $tmpKey .= '##'.$c;
            }

            $j++;
        }
        if ($freezeField != null) {
            $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','".$freezeField."')\"\r\n\t\t     class='zImgBtn' src='images/001_45.png' /></td>";
        } else {
            $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."')\"\r\n\t\t     class='zImgBtn' src='images/001_45.png' /></td>";
        }

        if ($postTo == 'null') {
            $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."',null,'".$table."')\"\r\n\t\t\t    class='zImgBtn' src='images/delete_32.png' /></td>";
        } else {
            $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."','".$postTo."','".$table."')\"\r\n\t\t\t    class='zImgBtn' src='images/delete_32.png' /></td>";
        }

        $tables .= '</tr>';
        $i++;
    }
    $tables .= '</tbody>';
    $tables .= '<tfoot>';
    $tables .= '</tfoot>';
    $tables .= '</table>';
    if ($printShow) {
        $tables .= '</div>';
    }

    $tables .= '</fieldset>';

    return $tables;
}

?>