<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."'";
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$optBag = "<option value=''>".$_SESSION['lang']['all'].'</option>';
//$sBag = 'select kode,nama from '.$dbname.'.sdm_5departemen order by nama asc';
//$sBag ="select  b.nama,b.kode
//from datakaryawan a
//inner join sdm_5departemen b on (a.bagian=b.kode or left(b.kode,2)=a.bagian)
//INNER JOIN user u ON a.karyawanid=u.karyawanid
//WHERE  u.namauser='" . $_SESSION['standard']['username'] . "'";
//$qBag = mysql_query($sBag);
//while ($rBag = mysql_fetch_assoc($qBag)) {
//    $optBag .= '<option value='.$rBag['kode'].'>'.$rBag['nama'].'</option>';
//}

$select = "<select id=kdOrg name=kdOrg onchange=getPeriode() style='width:150px;' >";
$optOrg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optOrg = $select.$optOrg."</select>";
$optSisGaji = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$arrSisGaji = ['Harian', 'Bulanan'];
foreach ($arrSisGaji as $dt => $isi) {
    $optSisGaji .= '<option value='.$isi.'>'.$_SESSION['lang'][strtolower($isi)].'</option>';
}
$arr = '##kdOrg##periode##kdBag##tgl1##tgl2##sisGaji';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/sdm_2rekapabsen.js'></script>\r\n<link rel=stylesheet type='text/css href=style/zTable.css'>\r\n<script>\r\nfunction bersihForm()\r\n{\r\n    document.getElementById('tgl1').value='';\r\n    document.getElementById('tgl2').value='';\r\n    document.getElementById('tgl1').disabled=false;\r\n    document.getElementById('tgl2').disabled=false;\r\n    document.getElementById('kdOrg').value='';\r\n    document.getElementById('sisGaji').value='';\r\n    document.getElementById('kdBag').value='';\r\n    document.getElementById('periode').value='';\r\n    document.getElementById('printContainer').innerHTML='';\r\n}\r\n</script>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rinciGajiBag'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td>';
echo $optOrg;
echo "</td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['bagian'];
echo '</label></td><td><select id="kdBag" name="kdBag" style="width:150px"   >';
echo $optBag;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['sistemgaji'];
echo "</label></td><td>\r\n        <select id=\"sisGaji\" name=\"sisGaji\" style=\"width:150px\">\r\n        ";
echo $optSisGaji;
echo "        </select></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\"><input type=\"hidden\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n<input type=\"hidden\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rincianGajiBagian','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rincianGajiBagian','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rincianGajiBagian.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"bersihForm()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>