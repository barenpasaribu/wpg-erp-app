<?
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body_hrd();
?>
<!-- Includes -->
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/sdm_pembatalan_cuti.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?
#====== Controller ======
# Options
# Ambil option untuk periode absen 
#$optPeriodeAbsen = makeOption($dbname,'sdm_absensiht','periode,periode',null);
$optPeriodeAbsen = array();
$SoptPeriodeAbsen = "SELECT substring(daritanggal,1,7) as tanggal FROM `sdm_cutidt` GROUP BY substring(daritanggal,1,7);";
$RoptPeriodeAbsen = fetchData($SoptPeriodeAbsen);
foreach($RoptPeriodeAbsen as $Key => $Value){
	$optPeriodeAbsen[$Value['tanggal']] = $Value['tanggal'];
}
$SoptPeriodeAbsen = "SELECT substring(sampaitanggal,1,7) as tanggal FROM `sdm_cutidt` GROUP BY substring(sampaitanggal,1,7);";
$RoptPeriodeAbsen = fetchData($SoptPeriodeAbsen);
foreach($RoptPeriodeAbsen as $Key => $Value){
	$optPeriodeAbsen[$Value['tanggal']] = $Value['tanggal'];
}

# Ambil option untuk perusahaan
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");

# Ambil option untuk bagian
$sBagian = "Select * from ".$dbname.".sdm_5departemen
where kode !='---' order by nama";
$rBagian = fetchData($sBagian);
$optBagian = array("" => $_SESSION['lang']['all']);
foreach($rBagian as $row => $kar)
{
    $optBagian[$kar['kode']] = $kar['nama'];
} 

# Ambil option untuk karyawan
$sKaryawan = "select * from ".$dbname.".datakaryawan 
where lokasitugas like '".$_SESSION['empl']['lokasitugas']."' 
order by namakaryawan asc";    
$rkaryawan = fetchData($sKaryawan);
$optkaryawan = array("" => $_SESSION['lang']['all']);
foreach($rkaryawan as $row => $kar)
{
    $optkaryawan[$kar['karyawanid']] = $kar['namakaryawan'];
} 

# Fields
$els = array();
$els[] = array(
  makeElement('periodeabsen','label','Periode Absen'),
  makeElement('periodeabsen','select','',array('style'=>'width:70px'),$optPeriodeAbsen)
);
$els[] = array(
  makeElement('perusahaan','label',$_SESSION['lang']['perusahaan']),
  makeElement('perusahaan','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('bagian','label','Departemen'),
  makeElement('bagian','select','',array('style'=>'width:200px', 'onchange'=>'KaryawanByDepartemen()'),$optBagian)
);
$els[] = array(
  makeElement('karyawan','label','Karyawan'),
  makeElement('karyawan','select','',array('style'=>'width:200px'),$optkaryawan)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button','Cari Data',
    array('onclick'=>'PilihData()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX_HRD();
echo genElTitle('Filter Pembatalan Cuti',$els);
#pre($sKaryawan);exit();
?>
<fieldset style='clear:both'>
<legend>
	<b>Daftar Pembatalan Cuti</b>
	<!--<img onclick=DownloadExcel(event) src=images/excel.jpg class=resicon title='MS.Excel'>-->
</legend>
<div id='printContainer' style='overflow:auto;height:350px;'>
<?php
#pre($optPeriodeAbsen);
?>
</div></fieldset>
<?php 
CLOSE_BOX_HRD();
echo close_body_hrd();
?>