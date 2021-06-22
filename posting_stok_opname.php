<?
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/posting_stok_opname.js?v=<?php echo date('YmdHis'); ?>'></script>
<script language=javascript1.2 src='jquery/jquery-3.1.1.min.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.$_SESSION['lang']['postingstokopname'].'</b>');


## GET UNIT SELECT NAME ##
$whr = "namaorganisasi!=''";
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);

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
$sPeriodeAkuntansi = "SELECT periode FROM ".$dbname.".setup_periodeakuntansi WHERE tutupbuku = 0 and kodeorg = '".$_SESSION['empl']['lokasitugas']."' order by periode desc limit 1";
#echo $sPeriodeAkuntansi;
$qPeriodeAkuntansi = fetchData($sPeriodeAkuntansi);
$optPeriode.="<option value='".$qPeriodeAkuntansi[0]['periode']."'>".$qPeriodeAkuntansi[0]['periode']."</option>";

## END OF SET VARIABLE ##
?>
<br />
<fieldset>
	<legend><?php echo $_SESSION['lang']['filter'].' '.$_SESSION['lang']['data']; ?></legend>
	<table cellpadding=1 cellspacing=1 border=0 id='FilterData'>
	<tr>
		<td><? echo $_SESSION['lang']['unit']?></td>
		<td><select id=unitDt style='width:150px;' onchange=getGudangDt()><? echo $optUnit?></select></td>
	</tr>
	<tr>
		<td><? echo $_SESSION['lang']['sloc'] ?></td>
		<td><select id=gudang2 style='width:150px;' onchange=hideById('printPanel2')><? echo $optGdng; ?></select></td>
	</tr>
	<tr>
		<td><? echo $_SESSION['lang']['periode']?></td>
		<td><select id=periode2 onchange=hideById('printPanel2')><? echo $optPeriode ?></select></td>
	</tr>
	<tr>
		<td>
			<button class=mybutton onclick=CariData()><? echo $_SESSION['lang']['pilih']?></button>
			<button onclick="javascript:location.reload()" class="mybutton" name="reset" id="reset">Batal</button>
		</td>
	</tr>
	</table>
</fieldset>

<fieldset><legend><?php echo $_SESSION['lang']['header'].' '.$_SESSION['lang']['data'];?></legend><div id='fieldsetheader' style='width:100%;height:150px;overflow:scroll;'>
	<table class=sortable cellspacing=1 border=0 width=100%>
	<thead>
		<tr>
			<td align=center><?php echo $_SESSION['lang']['no'];?></td>
			<td align=center><?php echo $_SESSION['lang']['tanggal'];?></td>
			<td align=center>No Referensi</td>
			<td align=center>No Stok Opname</td>
			<td align=center><?php echo $_SESSION['lang']['unit'];?></td>
			<td align=center><?php echo $_SESSION['lang']['gudang'];?></td>
			<td align=center><?php echo $_SESSION['lang']['keterangan'];?></td>
			<td align=center><?php echo $_SESSION['lang']['status'];?></td>
		</tr>  
	</thead>
	<tbody id=tbodyheader>
	</tbody>
	<tfoot>	
	</tfoot>		 
	</table>
</div></fieldset>
<fieldset><legend><?php echo $_SESSION['lang']['detailPembelian'].' '.$_SESSION['lang']['data'];?></legend><div id='fieldsetdetails' style='width:100%;height:200px;overflow:scroll;display:none;'>
	<table class=sortable cellspacing=1 border=0 width=100%>
	<thead>
		<tr>
			<td align=center><?php echo $_SESSION['lang']['no'];?></td>
			<td align=center>No Referensi</td>
			<td align=center><?php echo $_SESSION['lang']['kodebarang'];?></td>
			<td align=center><?php echo $_SESSION['lang']['namabarang'];?></td>
			<td align=center><?php echo $_SESSION['lang']['satuan'];?></td>
			<td align=center>Stok Sistem</td>
			<td align=center>Stok Opname</td>
			<td align=center>Stok Selisih</td>
		</tr>  
	</thead>
	<tbody id=tbodydetail>
	</tbody>
	<tfoot>	
	</tfoot>		 
	</table>
</div></fieldset>
<?
CLOSE_BOX();
close_body();
?>