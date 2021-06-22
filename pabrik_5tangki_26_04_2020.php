<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['tangki'].'</b>');
echo "
<script language=javascript src=js/pabrik_5tangki.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";
$where = "`tipe`='PABRIK' AND kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
$whereKary = '';
$i = 0;
foreach ($optOrg as $key => $row) {
    if (0 === $i) {
        $whereKary .= "lokasitugas='".$key."'";
    } else {
        $whereKary .= " or lokasitugas='".$key."'";
    }

    ++$i;
}
$optKary = makeOption($dbname, 'datakaryawan', 'nik,namakaryawan', $whereKary, '0');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('kodetangki', 'label', $_SESSION['lang']['kodetangki']), makeElement('kodetangki', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:200px', 'maxlength' => '50', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('komoditi', 'label', $_SESSION['lang']['komoditi']), makeElement('komoditi', 'text', '', ['style' => 'width:200px', 'maxlength' => '3', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('stsuhu', 'label', $_SESSION['lang']['stsuhu']), makeElement('stsuhu', 'textnum', '', ['style' => 'width:200px', 'maxlength' => '3', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('luaspenampang', 'label', $_SESSION['lang']['luaspenampang']), makeElement('luaspenampang', 'textnum', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('satuanpenampang', 'label', $_SESSION['lang']['satuanpenampang']), makeElement('satuanpenampang', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('volumekerucut', 'label', $_SESSION['lang']['volumekerucut']), makeElement('volumekerucut', 'textnum', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('satuankerucut', 'label', $_SESSION['lang']['satuankerucut']), makeElement('satuankerucut', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$fieldStr = '##kodeorg##kodetangki##keterangan##komoditi##stsuhu##luaspenampang##satuanpenampang##volumekerucut##satuankerucut';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'pabrik_5tangki', '##kodeorg##kodetangki', null, 'kodeorg##kodetangki')];
echo genElTitle($_SESSION['lang']['form'], $els);
echo "</div><div style='clear:both;float:left'>";
$table = 'pabrik_5tangki';
$where = "1 AND kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
$query = 'select * from '.$dbname.'.'.$table.' where '.$where;
$res = mysql_query($query);
$j = mysql_num_fields($res);
$i = 0;
$field = [];
$fieldStr = '';
$primary = [];
for ($primaryStr = ''; $i < $j; ++$i) {
    $meta = mysql_fetch_field($res, $i);
    $field[] = strtolower($meta->name);
    $fieldStr .= '##'.strtolower($meta->name);
    if ('1' === $meta->primary_key) {
        $primary[] = strtolower($meta->name);
        $primaryStr .= '##'.strtolower($meta->name);
    }
}
$fForm = $field;
$result = [];
while ($bar = mysql_fetch_assoc($res)) {
    $result[] = $bar;
}
$tables = '<fieldset><legend>'.$_SESSION['lang']['list'].'</legend>';
$tables .= "<img src='images/pdf.jpg' title='PDF Format'\r\n  style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','*',null,'slave_master_pdf',event)\">&nbsp;";
$tables .= "<img src='images/printer.png' title='Print Page'\r\n  style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";
$tables .= "<div style='overflow:auto'>";
$tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";
$tables .= "<thead><tr class='rowheader'>";
foreach ($field as $hName) {
    $tables .= '<td>'.$_SESSION['lang'][$hName].'</td>';
}
$tables .= "<td colspan='3'></td>";
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
        if (3 === count($tmpC)) {
            $c = $tmpC[2].'-'.$tmpC[1].'-'.$tmpC[0];
        }

        if ('mandor' === $fForm[$j] || 'asisten' === $fForm[$j]) {
            $tables .= "<td id='".$fForm[$j].'_'.$i."' value='".$c."'>".$optKary[$c].'</td>';
        } else {
            $tables .= "<td id='".$fForm[$j].'_'.$i."' value='".$c."'>".$c.'</td>';
        }

        $tmpVal .= '##'.$c;
        if (in_array($fForm[$j], $primary, true)) {
            $tmpKey .= '##'.$c;
        }

        ++$j;
    }
    $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow('".$row['kodeorg']."','".$row['kodetangki']."','".$row['keterangan']."','".$row['komoditi']."',".$row['stsuhu'].", '".$row['luaspenampang']."','".$row['satuanpenampang']."','".$row['volumekerucut']."','".$row['satuankerucut']."')\"\r\n    class='zImgBtn' src='images/001_45.png' /></td>";
    $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow('".$row['kodeorg']."','".$row['kodetangki']."')\"\r\n    class='zImgBtn' src='images/delete_32.png' /></td>";    
    $tables .= '</tr>';
    ++$i;
}
$tables .= '</tbody>';
$tables .= '<tfoot></tfoot>';
$tables .= '</table></div></fieldset>';
echo "<div style='clear:both;float:left'>";
echo $tables;
echo '</div>';

echo '</div>';
CLOSE_BOX();
echo close_body();

?>