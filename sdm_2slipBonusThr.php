<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$optPeriod = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKode = $optJns2 = $optJns = $optPeriod;
$sPeriod = 'select distinct substr(periode,1,4) as thn from '.$dbname.'.sdm_5periodegaji order by periode asc';
$qPeriod = mysql_query($sPeriod);
while ($rPeriod = mysql_fetch_assoc($qPeriod)) {
    $optPeriod .= '<option value='.$rPeriod['thn'].'>'.$rPeriod['thn'].'</option>';
}
$re = [28 => 'THR', 26 => 'Bonus'];
$tre = ['Bulanan' => $_SESSION['lang']['bulanan'], 'Harian' => $_SESSION['lang']['harian']];
foreach ($re as $dtr => $lst) {
    $optJns .= "<option value='".$dtr."'>".$lst.'</option>';
}
foreach ($tre as $dtr2) {
    $optJns2 .= "<option value='".$dtr2."'>".$dtr2.'</option>';
}
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $sData = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n            where char_length(kodeorganisasi)=4 and tipe in ('KEBUN','PABRIK','TRAKSI','KANWIL') order by namaorganisasi asc ";
} else {
    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
        $sData = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."' order by namaorganisasi asc ";
        if ('PMO' == $_SESSION['org']['kodeorganisasi']) {
            $sData = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk in ('PMO','GMJ','KUD') order by namaorganisasi asc ";
        }
    } else {
        $sData = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    }
}

$qData = mysql_query($sData);
while ($rData = mysql_fetch_assoc($qData)) {
    $optKode .= "<option value='".$rData['kodeorganisasi']."'>".$rData['namaorganisasi'].'</option>';
}
$arrData = '##kdOrg##periodegaji##jenis##jnsGaji';
$frm = '<fieldset style=width:250px><legend>Slyip Bonus/THR</legend>';
$frm .= '<table cellpadding=1 cellspacing=1 border=0>';
$frm .= '<tr><td>'.$_SESSION['lang']['unit'].'</td>';
$frm .= "<td><select id=kdOrg style='width:150px;'>".$optKode.'</select></td></tr>';
$frm .= '<tr><td>'.$_SESSION['lang']['periodebonus'].'</td>';
$frm .= "<td><select id=periodegaji style='width:150px;'>".$optPeriod.'</select></td></tr>';
$frm .= '<tr><td>'.$_SESSION['lang']['jenis'].'</td>';
$frm .= "<td><select id=jenis style='width:150px;'>".$optJns.'</select></td></tr>';
$frm .= '<tr><td>'.$_SESSION['lang']['sistemgaji'].'</td>';
$frm .= "<td><select id=jnsGaji style='width:150px;'>".$optJns2.'</select></td></tr>';
$frm .= "<tr><td colspan=2><button class=mybutton onclick=zPreview('sdm_slave_2slipBonusThr','".$arrData."','listPosting')>".$_SESSION['lang']['preview'].'</button>';
$frm .= "<button class=mybutton onclick=zPdf('sdm_slave_2slipBonusThr','".$arrData."','listPosting')>".$_SESSION['lang']['pdf'].'</button>';
$frm .= "<button class=mybutton onclick=zExcel(event,'sdm_slave_2slipBonusThr.php','".$arrData."')>".$_SESSION['lang']['excel'].'</button></td></tr>';
$frm .= '</table>';
$form = '';
$form .= "<h3 align='left'>".$_SESSION['lang']['bonus'].'</h3>';
OPEN_BOX();
echo $frm;
CLOSE_BOX();
OPEN_BOX();
echo "\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='listPosting' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>