<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$arr = '##unit##periode##judul##afdId';
('' === $_POST['judul'] ? ($judul = $_GET['judul']) : ($judul = $_POST['judul']));
$optunit = "<option value=''></option>";
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' \r\n    and (tipe = 'kebun' or tipe = 'pabrik') order by namaorganisasi asc";
$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optunit .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$optperiode = "<option value=''></option>";
$sOrg = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optperiode .= '<option value='.$rOrg['periode'].'>'.$rOrg['periode'].'</option>';
}
$optafd = "<option value=''> </option>";
echo "\r\n<table cellspacing=\"1\" border=\"0\" >\r\n    <tr><td colspan=2>".$judul."</td></tr>\r\n   \r\n    <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:200px;\">".$optperiode."</select></td></tr>\r\n        <tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id='unit' style=\"width:200px;\" onchange=getAfd(this)>".$optunit."</select></td></tr>    \r\n    <tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id='afdId' style=\"width:200px\">".$optafd."</select></td></tr>\r\n    <tr height=\"20\"><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$judul."'></td></tr>\r\n    <tr><td colspan=\"2\"> \r\n    <button onclick=\"zPreview('lbm_slave_biaya_produksi_kebun','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>\r\n    <button onclick=\"zExcel(event,'lbm_slave_biaya_produksi_kebun.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    \r\n    <button onclick=\"zPdf('lbm_slave_biaya_produksi_kebun','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">".$_SESSION['lang']['pdf']."</button>\r\n    <!--<button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>--></td></tr>\r\n</table>\r\n";

?>