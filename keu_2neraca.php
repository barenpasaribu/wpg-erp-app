<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/keu_laporan.js'></script>\r\n<script language=javascript1.2 src='js/statusinput.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['neraca']).'</b>');
$str = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
$res = mysql_query($str);
$optper = '';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
$optrev .= "<option value='0'>0</option>";
$optrev .= "<option value='1'>1</option>";
$optrev .= "<option value='2'>2</option>";
$optrev .= "<option value='3'>3</option>";
$optrev .= "<option value='4'>4</option>";
$optrev .= "<option value='5'>5</option>";

if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = "select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' and kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."' order by namaorganisasi desc";
   
    $res = mysql_query($str);
    $optpt = '';
    while ($bar = mysql_fetch_object($res)) {
        $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }

    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n  where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n  or tipe='HOLDING')  and induk!='' and induk='".$_SESSION['org']['kodeorganisasi']."' ";
    $res = mysql_query($str);
    $optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
} else {
    $optpt = '';
    $optpt .= "<option value='".$_SESSION['empl']['kodeorganisasi']."'>".$_SESSION['empl']['kodeorganisasi'].'</option>';
    $optgudang .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas'].'</option>';
}
/*
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PT' order by namaorganisasi desc";
    $res = mysql_query($str);
    $optpt = '';
    while ($bar = mysql_fetch_object($res)) {
        $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where length(kodeorganisasi)=4";
    $res = mysql_query($str);
    $optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
} else {
    $optpt = '';
    $optpt .= "<option value='".$_SESSION['empl']['kodeorganisasi']."'>".$_SESSION['empl']['kodeorganisasi'].'</option>';
    $optgudang .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas'].'</option>';
}
*/
$optper1 = "<option value='akhir'>".$_SESSION['lang']['akhirtahun'].'</option>';
$optper1 .= "<option value='lalu'>".$_SESSION['lang']['tahunlalu'].'</option>';
echo "<fieldset>\r\n    <legend>".$_SESSION['lang']['neraca']."</legend>\r\n        ".$_SESSION['lang']['pt'].' : '."<select id=pt style='width:200px;' onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>\r\n        ".$_SESSION['lang']['']."<select id=gudang style='width:150px;'>".$optgudang."</select>\r\n        ".$_SESSION['lang']['periode'].' : '."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n         : "."<select id=periode1 onchange=hideById('printPanel')>".$optper1."</select>\r\n        ".$_SESSION['lang']['revisi'].' : '."<select id=revisi onchange=hideById('printPanel')>".$optrev."</select>     \r\n        <button class=mybutton onclick=getLaporanNeraca()>".$_SESSION['lang']['proses']."</button>\r\n    </fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n        <img onclick=fisikKeExcel(event,'keu_laporanNeraca_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n        <img onclick=fisikKePDF(event,'keu_laporanNeraca_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n    </span>    \r\n    <div id=container style='width:100%;height:25%;overflow:auto;'>\r\n    </div>";
CLOSE_BOX();
close_body();

?>