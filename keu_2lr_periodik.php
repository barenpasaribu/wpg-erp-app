<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/keu_laporan.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['laporanrugilabaperiodik']).'</b>');
$str = 'select distinct substr(periode,1,4) as periode from '.$dbname.".setup_periodeakuntansi\r\n      order by periode desc";
$res = mysql_query($str);
$optper = '';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str = "select kodeorganisasi,namaorganisasi from organisasi where tipe='PT' and length(kodeorganisasi)<=3 and kodeorganisasi like '".$_SESSION['empl']['kodeorganisasi']."' order by namaorganisasi desc";
    $res = mysql_query($str);
    $optpt = '';
    while ($bar = mysql_fetch_object($res)) {
        $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL' or tipe='HOLDING') and induk like '".$_SESSION['empl']['kodeorganisasi']."'";
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

echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['laporanrugilabaperiodik']."</legend>\r\n\t ".$_SESSION['lang']['pt'].' : '."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>\r\n\t ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>\r\n\t ".$_SESSION['lang']['periode'].' : '."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n\t <button class=mybutton onclick=getLaporanRugiLabaPeriodik()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=fisikKeExcel(event,'keu_laporanRugiLabaPeriodik_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=fisikKePDF(event,'keu_laporanRugiLabaPeriodik_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span>    \r\n\t <div style='width:100%;height:50%;overflow:scroll;' id=container>\r\n     </div>";
CLOSE_BOX();
close_body();

?>