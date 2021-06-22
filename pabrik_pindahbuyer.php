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
$sLokasi = 'select id,lokasi from '.$dbname.'.setup_remotetimbangan order by lokasi asc';
$qLokasi = mysql_query($sLokasi);
while ($rLokasi = mysql_fetch_assoc($qLokasi)) {
    $optLksi .= '<option value='.$rLokasi['id'].'>'.$rLokasi['lokasi'].'</option>';
}
$sBuyer = 'select kodecustomer,namacustomer from '.$dbname.'.pmn_4customer order by namacustomer asc';
$qBuyer = mysql_query($sBuyer);
while ($rBuyer = mysql_fetch_assoc($qBuyer)) {
    $optBuyer .= '<option value='.$rBuyer['kodecustomer'].'>'.$rBuyer['namacustomer'].'</option>';
}
$sTrp = "select * from ".$dbname.".log_5supplier where kodekelompok='S006' order by namasupplier asc";
$qTrp = mysql_query($sTrp);
while ($rTrp = mysql_fetch_assoc($qTrp)) {
    $optTransporter .= '<option value='.$rTrp['supplierid'].'>'.$rTrp['namasupplier'].'</option>';
}
//$sSipb = "select * from ".$dbname.".pmn_kontrakjual  order by nodo asc";
//$qSipb = mysql_query($sSipb);
//while ($rSipb = mysql_fetch_assoc($qSipb)) {
//    $optSIPB .= '<option value='.$rSipb['nodo'].'>'.$rSipb['nodo'].'</option>';
//}

$lokasiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);

$arr = '##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src=js/pabrik_3uploadtimbangan.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo 'Pindah Buyer';
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['lokasi'];
echo "</label></td><td>:</td><td>\r\n<select id=\"lksiServer\" name=\"lksiServer\" style=\"width:150px\" onchange=\"getTiket()\"><option value=\"\"></option>\r\n";
echo $optLksi;
echo "</select></td></tr>\r\n<tr><td><label>";
echo 'No Tiket';
echo "</label></td><td>:</td><td>\r\n<select id=tiket onchange=\"getTiketDetail()\"  style=width:150px;>".$optTiket."</select>\r\n</td></tr><tr><td><label>";

echo 'No SIPB';
echo "</label></td><td>:</td><td>\r\n<select id=nosipb onchange=\"getRubahDo()\"  style=width:150px; disabled></select>\r\n</td></tr><tr><td><label>";

echo 'Vendor Buyer';
echo "</label></td><td>:</td><td>\r\n<select id=vendorbuyer  style=width:150px; disabled>".$optBuyer."</select>\r\n</td></tr><tr><td><label>";

echo 'Transporter';
echo "</label></td><td>:</td><td>\r\n<select id=transporter  style=width:150px; disabled>".$optTransporter."</select>\r\n</td></tr><tr><td><label>";
echo 'Tujuan';
echo "</label></td><td>:</td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tujuan\" name=\"tujuan\" maxlength=\"10\" style=\"width:150px;\" />\r\n</td></tr><tr><td><label>";
echo 'Berat 1';
echo "</label></td><td>:</td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"berat1\" name=\"berat1\" maxlength=\"10\" style=\"width:150px;\" readonly/>\r\n</td></tr><tr><td><label>";
echo 'Berat 2';
echo "</label></td><td>:</td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"berat2\" name=\"berat2\" maxlength=\"10\" style=\"width:150px;\" readonly/>\r\n</td></tr><tr><td><label>";
echo 'Netto';
echo "</label></td><td>:</td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"netto\" name=\"netto\" maxlength=\"10\" style=\"width:150px;\" readonly/>\r\n</td></tr>";
echo "\r\n\r\n<tr><td><label></td></tr>\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"SimpanPB()\" class=\"mybutton\" name=\"simpan\" id=\"simpan\">Pindah Buyer</button><button onclick=\"unLockForm()\" class=\"mybutton\" name=\"cancel\" id=\"cancel\"> ";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>";
echo "<input type=\"hidden\" name=\"dbnm\" id=\"dbnm\" />\r\n<input type=\"hidden\" name=\"prt\" id=\"prt\" />\r\n<input type=\"hidden\" name=\"pswrd\" id=\"pswrd\" />\r\n<input type=\"hidden\" name=\"ipAdd\" id=\"ipAdd\" />\r\n<input type=\"hidden\" name=\"usrName\" id=\"usrName\" />\r\n</div>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>