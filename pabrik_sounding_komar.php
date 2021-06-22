<?php

require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX('', '<b>'.$_SESSION['lang']['sounding'].'Sounding</b>');

echo "<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/formTable.js></script>\r\n<script language=javascript src=js/pabrik_5shiftKomar.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";

$where = "`tipe`='PABRIK'";

$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');


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

$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKary, '0');

echo "<div style='margin-bottom:30px'>";

$els = [];
/*
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];

$els[] = [makeElement('shift', 'label', $_SESSION['lang']['shift']), makeElement('shift', 'textnum', '1', ['style' => 'width:200px', 'maxlength' => '1', 'onkeypress' => 'return angka_doang(event)'])];

$els[] = [makeElement('mandor', 'label', $_SESSION['lang']['mandor']), makeElement('mandor', 'select', '', ['style' => 'width:300px'], $optKary)];

$els[] = [makeElement('asisten', 'label', $_SESSION['lang']['asisten']), makeElement('asisten', 'select', '', ['style' => 'width:300px'], $optKary)];
*/


$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' and tipe='PABRIK'";

$res = mysql_query($str);

$optorg = '';

while ($bar = mysql_fetch_object($res)) {

    $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';

}

echo "<fieldset >\r\n        <legend>".$_SESSION['lang']['form']."</legend>\r\n\t\t<table><tr><td>\r\n\t\t\r\n\t\t<table>\r\n\t\t   <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['kodeorganisasi']."\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t    <select id=kodeorg style='width:100%;'>".$optorg."</select>\r\n\t\t\t </td>\r\n\t\t   </tr>\r\n\t\t   <tr> \r\n\t\t\t <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t <td><input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) onchange=gettbs() maxlength=10 onkeypress=\"return false;\">\r\n\t\t\t </td>\t\r\n\t\t     <td>\t\t \r\n\t\t </tr>\r\n\t\t   <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['sisatbskemarin']."\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t    <input type=text id=sisatbskemarin value=0 class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg.\r\n\t\t\t </td>\r\n\t\t   </tr>\r\n\t\t   <tr> \r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['tbsmasuk']."\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t    <input type=text id=tbsmasuk value=0  class=myinputtextnumber onblur=hitungSisa()  maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg. \r\n\t\t\t </td>\t \t\t \r\n\t\t </tr>\t\t\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['tbsdiolah']."\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t    <input type=text id=tbsdiolah value=0  class=myinputtextnumber onblur=hitungSisa()  maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg. \r\n\t\t\t </td>\t\t \r\n\t\t </tr>\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['sisa']."\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t    <input type=text id=sisa  value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>Kg. \r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t";

$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', array('style' => 'width:250px', 'onchange' => 'ambilkegiatan()'), $optOrg));
$els[] = [makeElement('loriolah', 'label', $_SESSION['lang']['loriolah']."Lori Olah :"), makeElement('loriolah', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'ambilkegiatan()'])];

//$els[] = array(makeElement('noakun', 'label', $_SESSION['lang']['noakun']), makeElement('noakun', 'select', '', array('style' => 'width:250px', 'onchange' => 'ambilkegiatan()'), $optAkun));

//$els[] = array(makeElement('kodekegiatan', 'label', $_SESSION['lang']['kodekegiatan']), makeElement('kodekegiatan', 'text', '', array('style' => 'width:60px', 'maxlength' => '9')));

$els[] = [makeElement('loridalamrebusan', 'label', $_SESSION['lang']['loridalamrebusan']."Lori Dalam Rebusan :"), makeElement('loridalamrebusan', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('lorirestandepanrebusan', 'label', $_SESSION['lang']['lorirestandepanrebusan']."Lori Restan Depan Rebusan :"), makeElement('lorirestandepanrebusan', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('lorirestanbelakangrebusan', 'label', $_SESSION['lang']['lorirestanbelakangrebusan']."Lori Restan Belakang Rebusan :"), makeElement('lorirestanbelakangrebusan', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('estimasidiperon', 'label', $_SESSION['lang']['estimasidiperon']."Estimasi Di Peron :"), makeElement('estimasidiperon', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('sisaawal', 'label', $_SESSION['lang']['sisaawal']."Sisa Awal :"), makeElement('sisaawal', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('tbsmasuk', 'label', $_SESSION['lang']['tbsmasuk'].":"), makeElement('tbsmasuk', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('potongankg', 'label', $_SESSION['lang']['potongankg'].":"), makeElement('potongankg', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('potonganpersen', 'label', $_SESSION['lang']['potonganpersen']."Potongan (%):"), makeElement('potonganpersen', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('total', 'label', $_SESSION['lang']['total'].":"), makeElement('total', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('rataratabuahperlori', 'label', $_SESSION['lang']['rataratabuahperlori']."Rata-Rata Buah Perlori:"), makeElement('rataratabuahperlori', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('tbsolah', 'label', $_SESSION['lang']['tbsolah']."TBS Olah:"), makeElement('tbsolah', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('sisaakhir', 'label', $_SESSION['lang']['sisaakhir']."Sisa Akhir:"), makeElement('sisaakhir', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];


$fieldStr = '##kodeorg##LoriOlah##LoriDalamRebusan##LoriRestanDepanRebusan##LoriRestanBelakangRebusan';

$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));

$els['btn'] = [genFormBtn($fieldStr, 'pabrik_5shiftKomar', '##kodekegiatan##', null, 'kodeorg')];;
echo genElTitle('Form', $els);

echo '</div>';

$table = 'pabrik_5shiftKomar';

$query = 'select * from '.$dbname.'.'.$table;

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

//$tables .= "<img src='images/pdf.jpg' title='PDF Format'\r\n  style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','*',null,'slave_master_pdf',event)\">&nbsp;";

//$tables .= "<img src='images/printer.png' title='Print Page'\r\n  style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";

$tables .= "<div style='overflow:auto'>";

$tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";

$tables .= "<thead><tr class='rowheader'>";
/*
foreach ($field as $hName) {

    $tables .= '<td>'.$_SESSION['lang'][$hName].'</td>';

}
*/
$tables .= '<td>Kode Organisasi</td>';
$tables .= '<td>lori1</td>';
$tables .= '<td>lori2</td>';
$tables .= '<td>lori3</td>';
$tables .= '<td>lori4</td>';
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

    /*
    $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','kodeorg##shift')\"\r\n    class='zImgBtn' src='images/001_45.png' /></td>";

    $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow('".$row['kodeorg']."','".$row['shift']."')\"\r\n    class='zImgBtn' src='images/delete_32.png' /></td>";

    $tables .= "<td><img id='detail".$i."' title='Edit Detail' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','kodeorg##shift');".'showDetail('.$i.",'".$primaryStr."##kodeorg##shift',event)\"\r\n    class='zImgBtn' src='images/application/application_view_xp.png' /></td>";

    $tables .= '</tr>';
    */
    ++$i;

}

$tables .= '</tbody>';

$tables .= '<tfoot></tfoot>';

$tables .= '</table></div></fieldset>';

echo "<div style='clear:both;float:left'>";

echo $tables;

echo '</div>';

CLOSE_BOX();

echo close_body();



?>