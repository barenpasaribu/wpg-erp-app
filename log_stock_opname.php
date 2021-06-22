<?php
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<!-- Includes -->
<script language=javascript src='js/log_stock_opname.js?v=<?php echo date('YmdHis'); ?>'></script>
<script language="javascript" src="js/zMaster.js"></script>
<?php 
## GET LOKASI ##
$OptLokasi = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['pusat'] == 1){
	
	$sLokasi="select c.* from organisasi a
	inner join organisasi b on a.induk=b.kodeorganisasi
	INNER JOIN organisasi c on c.induk=b.kodeorganisasi
	where a.kodeorganisasi='".$_SESSION['empl']['lokasitugas']."';";
} else {
	$sLokasi = "select a.* from organisasi a
	where a.kodeorganisasi='".$_SESSION['empl']['lokasitugas']."';";
}
$qLokasi=mysql_query($sLokasi) or die(mysql_error($conn));
while($rLokasi=mysql_fetch_assoc($qLokasi)){
    $OptLokasi.="<option value='".$rLokasi['kodeorganisasi']."'>".$rLokasi['namaorganisasi']."</option>";
}

## GET GUDANG ##
$OptGudang = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

## GET PERIODE ##
$optPeriode="<option value=''>".$_SESSION['lang']['pilihselect']."</option>";

for($x=0;$x<13;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
    $optPeriode2.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
}
?>
<fieldset style='clear:both' >
	<legend><b><?php echo $_SESSION['lang']['find']; ?></b></legend>
	
	<table cellpadding=1 cellspacing=1>
		<tr>
			<td>Unit</td>
			<td>:</td>
			<td colspan=4><select id="lokasi" onchange="GetGudang()"><? echo $OptLokasi; ?></select></td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['gudang']; ?></td>
			<td>:</td>
			<td colspan=4><select id="gudang"><? echo $OptGudang; ?></select></td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['periode']; ?></td>
			<td>:</td>
			<td colspan=4><select id="periode"><? echo $optPeriode; ?></select></td>
		</tr>
		<tr>
			<td>Nomor Stok Opname</td>
			<td>:</td>
			<td colspan=4>
				<input type='text' id='nostokopname' name='nostokopname'>
				<img id='buttoncari' style='display:none;' src=images/zoom.png title='<? echo $_SESSION['lang']['find'] ?>' class=resicon onclick=CariSO('<? echo $_SESSION['lang']['find'] ?>',event)>
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td><button class="mybutton" onclick="DisplayList()"><?php echo $_SESSION['lang']['pilih']; ?></button>
				<button class="mybutton" onclick="window.location.reload()" id='Clear'><?php echo $_SESSION['lang']['clear']; ?></button>
				<!--<img onclick=DownloadExcel(event) src=images/pdf.jpg class=resicon title='MS.Excel'></td>
			<td><img onclick=DownloadExcel(event) src=images/excel.jpg class=resicon title='MS.Excel'> </td>
			<td>-->
		</tr>
	</table>
</fieldset>

<fieldset style='clear:both' id='ViewListHeader'>
	<legend><b><?php echo $_SESSION['lang']['list']; ?></b></legend>
	<div id='ListContainer' style='overflow:auto;height:350px;'>
	</div>
</fieldset>
<?
CLOSE_BOX();
echo close_body();
?>