<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['mutasiLahan'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\" src=\"js/kebun_mutasiLahan.js\"></script>\r\n\r\n";
for ($x = 0; $x <= 24; ++$x) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
$lokasi = $_SESSION['empl']['lokasitugas'];
$sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi='".$lokasi."'";
$query = mysql_query($sql) ;
while ($res = mysql_fetch_assoc($query)) {
    $optOrg .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
echo "<div id=\"headher\">\r\n\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['entryForm'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kodeOrg\" name=\"kodeOrg\" style=\"width:170px;\" onchange=\"getAfdeling(0,0)\" ><option value=\"\"></option>";
echo $optOrg;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['afdeling'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kodeAfdeling\" name=\"kodeAfdeling\" style=\"width:170px;\" onchange=\"getBlok(0,0)\" ><option value=\"\"></option></select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['blok'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kodeBlok\" name=\"kodeBlok\" style=\"width:170px;\"><option value=\"\"></option></select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['periodeTm'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"periodetm\"  onkeypress=\"return tanpa_kutip(event)\" size=\"10\" maxlength=\"7\" value=\"00-0000\" style=\"width:170px;\" /> e.g:12-2010 (bulan dan tahun)</td>\r\n</tr>\r\n\r\n<tr>\r\n<td colspan=\"3\" id=\"tmbLheader\">\r\n<button class=\"mybutton\" id=\"dtlAbn\" onclick=\"saveData()\">";
echo $_SESSION['lang']['save'];
echo '</button><button class="mybutton" id="cancelAbn" onclick="cancelSave()">';
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n</td>\r\n</tr>\r\n</table><input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n</fieldset>\r\n\r\n</div>\r\n";
CLOSE_BOX();
echo "<div id=\"listData\">\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n\r\n<table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n<thead>\r\n<tr class=\"rowheader\">\r\n<td>No.</td>\r\n<td>";
echo $_SESSION['lang']['kebun'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['afdeling'];
echo "</td> \r\n<td>";
echo $_SESSION['lang']['blok'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['periodeTm'];
echo "</td>\t \r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contain\">\r\n<script>loadData()</script>\r\n\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n\r\n\r\n\r\n";
echo close_body();

?>