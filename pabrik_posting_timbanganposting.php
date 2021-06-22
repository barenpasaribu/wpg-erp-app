<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script>\r\ndtAll='##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer';\r\n</script>\r\n";
for ($x = 0; $x <= 24; $x++) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
$sLokasi = "select distinct nosipb from ".$dbname.".pabrik_timbangan where nosipb !='' order by nosipb asc";
$qLokasi = mysql_query($sLokasi);
while ($rLokasi = mysql_fetch_assoc($qLokasi)) {
    $optDo .= '<option value='.$rLokasi['nosipb'].'>'.$rLokasi['nosipb'].'</option>';
}
$lokasiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$tgl=date('d-m-Y');
$arr = '##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src=js/pabrik_3uploadtimbangan.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo 'Posting Pabrik Timbangan';
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo '';
echo "</label></td><td></td><td>\r\n<input type='hidden' id='nodo' class='myinputtext' name='nodo' >\r\n";
echo "</td></tr>";
echo "<tr><td><label>";
echo 'Tanggal';
echo "</label></td><td>:</td><td>\r\n<input type='text' id='tgl' value='".$tgl."' class='myinputtext' autocomplete=off onmousemove=setCalendar(this.id) onkeypress=return false; name='tgl' >\r\n";
echo "</td></tr>";
echo "\r\n<tr><td><label>";




echo "\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"getDataDo()\" class=\"mybutton\" name=\"preview\" id=\"preview\">Cari</button>";
echo "<!--<button onclick=\"zPdf('kebun_slave_2pengangkutan','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'kebun_slave_2pengangkutan.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>--></td></tr>\r\n</table>\r\n<input type=\"hidden\" name=\"dbnm\" id=\"dbnm\" />\r\n<input type=\"hidden\" name=\"prt\" id=\"prt\" />\r\n<input type=\"hidden\" name=\"pswrd\" id=\"pswrd\" />\r\n<input type=\"hidden\" name=\"ipAdd\" id=\"ipAdd\" />\r\n<input type=\"hidden\" name=\"usrName\" id=\"usrName\" />\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Data Tiket Timbangan </b></legend>\r\n<div id='printContainer'>\r\n\r\n</div></fieldset>\r\n";
CLOSE_BOX();
echo close_body();

?>