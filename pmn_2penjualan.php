<?

require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); 
?>
<?php
$optPeriode = "";
$str="select distinct periode from ".$dbname.".log_5saldobulanan order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optPeriode.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}

$optPeriodeTahun = "";
$str="select distinct SUBSTRING(periode, 1, 4) as periode from ".$dbname.".log_5saldobulanan order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optPeriodeTahun.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}	

$sPabrik="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qPabrik=mysql_query($sPabrik) or die(mysql_error());
while($rPabrik=mysql_fetch_assoc($qPabrik))
{
	$optPabrik.="<option value=".$rPabrik['kodeorganisasi'].">".$rPabrik['namaorganisasi']."</option>";
}

//nama pelanggan
$optPelanggan = '<option value="">ALL</option>';
$sCust = 'select namacustomer,kodecustomer  from ' . $dbname . '.pmn_4customer order by namacustomer ASC';
$qPabrik=mysql_query($sCust) or die(mysql_error());
while($rPabrik=mysql_fetch_assoc($qPabrik))
{
	$optPelanggan.="<option value=".$rPabrik['kodecustomer'].">".$rPabrik['namacustomer']."(".$rPabrik['kodecustomer'].")</option>";
}

$optPabrik1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOpt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOpt=mysql_query($sOpt) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qOpt))
{
	$optPabrik1.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
}

$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}
$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang in ('40000001', '40000002')";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$optBrg1.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}

$arr="##periode##idPabrik##kdBrg##idPelanggan";
$arr1="##kodeorg1##kodebarang1##tgl1_1##tgl2_1";
$arr2="##periodeTahun##idPabrikTahun##kdBrgTahun##idPelangganTahun";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/pmn_2penjualan.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?    
//print_r($_SESSION);  
$frm[0].="
<div style='height:480px;overflow:scroll;'>
<div style=\"margin-bottom: 30px;\">
<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['laporanPenjualan']." Tahunan</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periodeTahun\" name=\"periodeTahun\" style=\"width:150px\">".$optPeriodeTahun."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['nm_perusahaan']."</label></td><td><select id=\"idPabrikTahun\" name=\"idPabrikTahun\" style=\"width:150px\">".$optPabrik."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['komoditi']."</label></td><td><select id=\"kdBrgTahun\" name=\"kdBrgTahun\" style=\"width:150px\">".$optBrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['nmcust']."</label></td><td><select id=\"idPelangganTahun\" name=\"idPelangganTahun\" style=\"width:150px\">".$optPelanggan."</select></td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('pmn_slave_2penjualan_tahunan','".$arr2."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zPdf('pmn_slave_2penjualan_tahunan','".$arr2."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>
    <button onclick=\"zExcel(event,'pmn_slave_2penjualan_tahunan.php','".$arr2."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset></div>";

$frm[1].="
<div style='height:480px;overflow:scroll;'>
<div style=\"margin-bottom: 30px;\">
<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['laporanPenjualan']." ".$_SESSION['lang']['bulanan']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['nm_perusahaan']."</label></td><td><select id=\"idPabrik\" name=\"idPabrik\" style=\"width:150px\">".$optPabrik."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['komoditi']."</label></td><td><select id=\"kdBrg\" name=\"kdBrg\" style=\"width:150px\">".$optBrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['nmcust']."</label></td><td><select id=\"idPelanggan\" name=\"idPelanggan\" style=\"width:150px\">".$optPelanggan."</select></td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('pmn_slave_2penjualan','".$arr."','printContainer1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zPdf('pmn_slave_2penjualan','".$arr."','printContainer1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>
    <button onclick=\"zExcel(event,'pmn_slave_2penjualan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer1' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset></div>";

$frm[2].="
<div style='height:480px;overflow:scroll;'>
<div style=\"margin-bottom: 30px;\">
<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['laporanPenjualan']." ".$_SESSION['lang']['harian']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['pabrik']."</label></td><td><select id=\"kodeorg1\" name=\"kodeorg1\" style=\"width:150px\">".$optPabrik1."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['komoditi']."</label></td><td><select id=\"kodebarang1\" name=\"kodebarang1\" style=\"width:150px\">".$optBrg1."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td>
<input type=text class=myinputtext id=tgl1_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - 
<input type=text class=myinputtext id=tgl2_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('pmn_slave_2penjualan_harian','".$arr1."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zExcel(event,'pmn_slave_2penjualan_harian.php','".$arr1."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset></div>";
//    <button onclick=\"zPdf('pmn_slave_2penjualan_harian','".$arr1."','printContainer1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>

//========================
$hfrm[0]=$_SESSION['lang']['laporanPenjualan']." Tahunan";
$hfrm[1]=$_SESSION['lang']['laporanPenjualan']." ".$_SESSION['lang']['bulanan'];
$hfrm[2]=$_SESSION['lang']['laporanPenjualan']." ".$_SESSION['lang']['harian'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================

CLOSE_BOX();
echo close_body();
?>