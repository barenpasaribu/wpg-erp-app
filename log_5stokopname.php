<?
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_stokopname.js?v=<?php echo date('YmdHis'); ?>'></script>
<script language=javascript1.2 src='jquery/jquery-3.1.1.min.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['stokopname']).'</b>');

## GET UNIT SELECT NAME ##
$whr = "namaorganisasi!=''";
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);
#pre($_SESSION['empl']);
## GET UNIT VALUE SELECT ##
$optUnit = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optUnit2 = $optGdng = $optUnit;
$sUnit="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit)){
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$optNmOrg[$rUnit['kodeorganisasi']]."</option>";
}

## GET SELECT PERIODE ##
$sUnit2="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ".$dbname.".organisasi
        where tipe like 'GUDANG%' and namaorganisasi!='' order by namaorganisasi asc";
$qUnit2=mysql_query($sUnit2) or die(mysql_error($conn));
while($rUnit2=mysql_fetch_assoc($qUnit2))
{
    $optUnit2.="<option value='".$rUnit2['kodeorganisasi']."'>".$optNmOrg[$rUnit2['kodeorganisasi']]."</option>";
}
$optPeriode="<option value=''>".$_SESSION['lang']['pilihselect']."</option>";
$sPeriodeAkuntansi = "SELECT periode FROM ".$dbname.".setup_periodeakuntansi WHERE tutupbuku = 0 and kodeorg = '".$_SESSION['empl']['lokasitugas']."'";
$qPeriodeAkuntansi = fetchData($sPeriodeAkuntansi);
$optPeriode.="<option value='".$qPeriodeAkuntansi[0]['periode']."'>".$qPeriodeAkuntansi[0]['periode']."</option>";

for($x=0;$x<13;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	
    $optPeriode2.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
}
## END OF SET VARIABLE ##
?>
<br />
<fieldset style=width:250px;float:left;>
	<legend><? echo $_SESSION['lang']['persediaanfisik'].' Per '.$_SESSION['lang']['sloc'] ?></legend>
	<table cellpadding=1 cellspacing=1 border=0>
	<tr>
		<td><? echo $_SESSION['lang']['unit']?></td>
		<td><select id=unitDt style='width:150px;' onchange=getGudangDt()><? echo $optUnit?></select></td>
	</tr>
	<tr>
		<td><? echo $_SESSION['lang']['sloc'] ?></td>
		<td><select id=gudang2 style='width:150px;' onchange=getPeriodeGudang()><? echo $optGdng; ?></select></td>
	</tr>
	<tr>
		<td><? echo $_SESSION['lang']['periode']?></td>
		<td><select id=periode2 onchange=hideById('printPanel2')><? echo $optGdng ?></select></td>
	</tr>
	<tr>
		<td><button class=mybutton onclick=getLaporanFisik2()><? echo $_SESSION['lang']['pilih']?></button></td>
		<td><button onclick="javascript:location.reload()" class="mybutton" name="reset" id="reset">Batal</button></td>
	</tr>
	</table>
</fieldset>
<fieldset style=width:300px;float:left;>
	<legend><? echo $_SESSION['lang']['headerstokopname']?></legend>
	<table cellpadding=1 cellspacing=1 border=0>
	<tr>
		<td><? echo $_SESSION['lang']['tanggal']?></td>
		<td><input name=tgl01 type=text class=myinputtext id=tgl01 onchange=bersih0() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10 value='<?php echo date('d-m-Y'); ?>' disabled></td>
	</tr>
	<tr>
		<td>No Stokopname</td>
		<td>
			<input type='text' id='nostokopname' name='nostokopname'>
			<img id='buttoncari' style='display:none;' src=images/zoom.png title='<? echo $_SESSION['lang']['find'] ?>' class=resicon onclick=CariSO('<? echo $_SESSION['lang']['find'] ?>',event)>
		</td>
	</tr>
	<tr>
		<td><? echo $_SESSION['lang']['keterangan'] ?></td>
		<td><input type='text' id='keterangan' name='keterangan'></td>
	</tr>
	<input type='hidden' id='reffno' name='reffno' value='0'>
	</table>
</fieldset>
<fieldset style=width:50px;>
	<legend>Transaksi</legend>
	<table cellpadding=1 cellspacing=1 border=0>
	<tr>
		<td>
			<span id=printPanel2 style='display:none;'>
				<img onclick=fisikKeExcel2(event,'log_slaveStokOpname.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
				<!--<img onclick=fisikKePDF2(event,'log_slaveStokOpname.php') title='PDF' class=resicon src=images/pdf.jpg>-->
			</span>
		</td>
	</tr>
	</table>
</fieldset>

	

<div style='width:100%;height:359px;overflow:scroll;'>
	<table class=sortable cellspacing=1 border=0 width=100%>
	<thead>
		<tr>
			<td align=center>No.</td>
			<td align=center><? echo $_SESSION['lang']['pt'] ?></td>
			<td align=center><? echo $_SESSION['lang']['sloc'] ?></td>
			<td align=center><? echo $_SESSION['lang']['periode'] ?></td>
			<td align=center><? echo $_SESSION['lang']['kodebarang'] ?></td>
			<td align=center><? echo $_SESSION['lang']['namabarang'] ?></td>
			<td align=center><? echo $_SESSION['lang']['satuan'] ?></td>
			<td align=center>Stok Sistem</td>
			<td align=center>Stok Opname</td>
			<td align=center><?php echo $_SESSION['lang']['selisih'];?></td>
		</tr>  
	</thead>
	<tbody id=container>
	</tbody>
	<tfoot>	
	</tfoot>		 
	</table>
</div>
<?
CLOSE_BOX();
close_body();
?>