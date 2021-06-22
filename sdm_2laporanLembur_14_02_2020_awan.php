<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$sBagian = 'select distinct * from '.$dbname.'.sdm_5departemen order by nama asc';
$qBagian = mysql_query($sBagian);
while ($rBagian = mysql_fetch_assoc($qBagian)) {
    $optBagian .= '<option value='.$rBagian['kode'].'>'.$rBagian['nama'].'</option>';
}
//if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') order by namaorganisasi asc ";
//} else {
//    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi \r\n               where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') or induk='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc ";
//    } else {
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
//    }
//}
//
//$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$qOrg = mysql_query($sOrg);
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}
$optOrg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$arr = '##kdOrg##periode##tgl1##tgl2##pilihan##pilihan2##pilihan3';
$arrDat = '##kdeOrg##period##pilihan_2##pilihan_3##tgl_1##tgl_2';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src='js/sdm_2laporanLembur.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanLembur'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n        ";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    echo '<tr><td><label>';
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px" onchange="getPeriode()">';
    echo $optOrg;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()">';
    echo $optPeriode;
    echo "</select></td></tr>    \r\n";
} else {
    echo '    <tr><td><label>';
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px">';
    echo $optOrg;
    echo "</select></td></tr>\r\n    <tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()">';
    echo $optPeriode;
    echo "</select></td></tr>    \r\n";
}

echo "\r\n<tr><td><label>";
echo $_SESSION['lang']['sistemgaji'];
echo '</label></td><td><select id="pilihan2" name="pilihan2" style="width:150px" onchange="getTgl()"><option value="harian">';
echo $_SESSION['lang']['harian'];
echo "</option></select></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input disabled type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input disabled type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['options'];
echo "</label></td><td><select id=\"pilihan\" name=\"pilihan\" style=\"width:150px\">\r\n\t";
// if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2, 2) || 'HO' == substr($_SESSION['empl']['lokasitugas'], -2, 2)) {
    echo "\t<option value=\"rupiah\">Dalam rupiah/In Rupiahs</option>\r\n\t";
// }

echo "\t<option value=\"jam_aktual\">Dalam jam aktual/Actual Hour</option>\r\n\t<option value=\"jam_lembur\">Dalam jam lembur/Beyond actual hour</option></select></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['bagian'];
echo '</label></td><td><select id="pilihan3" name="pilihan3" style="width:150px"><option value="semua">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optBagian;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2laporanLembur','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2laporanLembur','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2laporanLembur.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n      <div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanLembur'].'/'.$_SESSION['lang']['karyawan'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n        ";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    echo '<tr><td><label>';
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td><select id="kdeOrg" name="kdeOrg" style="width:150px" onchange="getPeriode2()">';
    echo $optOrg;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="period" name="period" style="width:150px" onchange="getTgl2()">';
    echo $optPeriode;
    echo "</select></td></tr>    \r\n";
} else {
    echo '    <tr><td><label>';
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td><select id="kdeOrg" name="kdeOrg" style="width:150px">';
    echo $optOrg;
    echo "</select></td></tr>\r\n    <tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="period" name="period" style="width:150px" onchange="getTgl2()">';
    echo $optPeriode;
    echo "</select></td></tr>    \r\n";
}

echo "\r\n<tr><td><label>";
echo $_SESSION['lang']['sistemgaji'];
echo '</label></td><td><select id="pilihan_2" name="pilihan_2" style="width:150px" onchange="getTgl2()"><option value="harian">';
echo $_SESSION['lang']['harian'];
echo "</option></select></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input disabled type=\"text\" class=\"myinputtext\" id=\"tgl_1\" name=\"tgl_1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input disabled type=\"text\" class=\"myinputtext\" id=\"tgl_2\" name=\"tgl_2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['bagian'];
echo '</label></td><td><select id="pilihan_3" name="pilihan_3" style="width:150px"><option value="semua">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optBagian;
echo "</select></td></tr>\r\n<tr><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr height=\"25\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2laporanLembur_rekap','";
echo $arrDat;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2laporanLembur_rekap','";
echo $arrDat;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2laporanLembur_rekap.php','";
echo $arrDat;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>