<?php
require_once('master_validation.php');
include('lib/eagrolib.php');
require_once('lib/eksilib.php');
echo OPEN_BODY();
?>
<script language='javascript' src='js/approval_hrd.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','');
$str = "select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi a
left join organisasitipe b on a.tipe=b.tipe where a.kodeorganisasi like '%".$_SESSION['empl']['kodeorganisasi']."%' and a.deptkeu=1 and (b.isHolding=1 or b.isKebun=1) order by a.namaorganisasi";
$res = mysql_query($str);
$optOrg = "<option value=''>".$_SESSION['lang']['pilih']."</option>";
while($bar=mysql_fetch_object($res)){
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
$str="select karyawanid,namakaryawan, lokasitugas from ".$dbname.".datakaryawan order by namakaryawan asc";
$res=mysql_query($str);
$optkar = "<option value=''>".$_SESSION['lang']['pilih']."</option>";
while($bar=mysql_fetch_object($res)){
    $optkar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." - ".$bar->lokasitugas."</option>";
}

//rubah hard code jadi panggil tabel parameter ==Jo 24-01-2017==
$optapp="<option value=''>".$_SESSION['lang']['pilih']."</option>";
$slapp="select kode,nama from setup_5parameter where flag='apprvh' ";
$resapp=$eksi->sSQL($slapp);

foreach($resapp as $barapp){
	$optapp.="<option value='".$barapp['kode']."'>".$_SESSION['lang'][$barapp['nama']]."</option>";
}
?>

<fieldset>
<legend><b><?php echo $_SESSION['lang']['setupapp'];?></b></legend>
<table>
<tr>
<td width='80px'><img class='delliconBig' onclick='baru();' src='images/newfile.png' title='<?php echo $_SESSION['lang']['new'];?>'></td>
<td><img class='delliconBig' onclick='listdata();' src='images/orgicon.png' title='<?php echo $_SESSION['lang']['list'];?>'></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['new'];?></td>
<td><?php echo $_SESSION['lang']['list'];?></td>
</tr>
</table>
</fieldset>

<fieldset id='form' style='display:none;'>
	<legend><b><?php echo $_SESSION['lang']['form'];?></b></legend>
	 <table>
	 <tr>
	 <td><?php echo $_SESSION['lang']['kodeapp'];?> <img src='images/obl.png'/></td>
	 <td>
	 <select id='app'>
	 <!--rubah hard code jadi panggil tabel parameter ==Jo 24-01-2017== -->
	 <!--<option value=''><?php echo $_SESSION['lang']['pilih'];?></option>
	 <option value='PP'>Approval 1</option>
	 <option value='PP2'>Approval 2</option>
	 <option value='PO'>Purchaser</option>
	 <option value='PO2'>Approval <?php echo $_SESSION['lang']['lokal'];?></option>
	 <option value='SG'>Approval PO <?php echo $_SESSION['lang']['ho'];?> </option>
	 <option value='CA'><?php echo $_SESSION['lang']['atasan']." ".$_SESSION['lang']['cuti'];?></option>
	 <option value='CH'><?php echo $_SESSION['lang']['hrd']." ".$_SESSION['lang']['cuti'];?></option>
	 <option value='PDA'><?php echo $_SESSION['lang']['atasan']." ".$_SESSION['lang']['perjalanandinas'];?></option>
	 <option value='PDH'><?php echo $_SESSION['lang']['hrd']." ".$_SESSION['lang']['perjalanandinas'] ;?></option>
	 <option value='PLTA'><?php echo $_SESSION['lang']['atasan']." ".$_SESSION['lang']['kursus'] ;?></option>
	 <option value='PLTH'><?php echo $_SESSION['lang']['hrd']." ".$_SESSION['lang']['kursus'] ;?></option>-->
	 <?php echo $optapp;?>
	 </select>
	 </td>
	 </tr>
	 
     <tr>
	 <td><?php echo $_SESSION['lang']['kodeorg'];?> <img src='images/obl.png'/></td>
	 <td><select id='kodeorg' onchange="getKaryawan(this.options[this.selectedIndex].value)"><?php echo $optOrg;?></select></td>
	 </tr>
	 	 
     <tr>
	 <td><?php echo $_SESSION['lang']['persetujuan'];?> <img src='images/obl.png'/></td>
	 <td><select id='karyawanid' disabled='disabled'><?php echo $optkar;?></select></td>
	 </tr>
	 
	 <tr>
	 <td></td>
	 <td>
	 <input type='hidden' id='method' value='insert'>
	 <button class='mybutton' onclick='simpanDep()'><?php echo $_SESSION['lang']['save'];?></button>
	 <button class='mybutton' onclick='cancelDep()'><?php echo $_SESSION['lang']['cancel'];?></button>	 
	 </td>
	 </tr>	
     </table>
</fieldset>
	 
<fieldset id='listdata'>
<legend><b><?php echo $_SESSION['lang']['list'];?></b></legend>
<?php echo $_SESSION['lang']['find']; ?> :
<select id='filter'>
<option value=''><?php echo $_SESSION['lang']['all'];?></option>
<!-- <option value='applikasi'><?php echo $_SESSION['lang']['kodeapp'];?></option> -->
<option value='kodeunit'><?php echo $_SESSION['lang']['kodeorg'];?></option>
<option value='namakaryawan'><?php echo $_SESSION['lang']['persetujuan'];?></option>
</select>
<span id='findword'><?php echo $_SESSION['lang']['keyword']; ?> : </span>
<input type='text' id='keyword' />
<button class='mybutton' onclick='search()'><?php echo $_SESSION['lang']['find']; ?></button>
<br/><br/>
<table class='sortable' cellspacing='1' border='0'>
<thead>
<tr class='rowheader'>
<td>No</td>
<td><?php echo $_SESSION['lang']['kodeapp'];?></td>
<td style='width:150px;'><?php echo $_SESSION['lang']['kodeorg'];?></td>
<td><?php echo $_SESSION['lang']['persetujuan'];?></td>
<td style='width:30px;'><?php echo $_SESSION['lang']['action'];?></td>
</tr>
</thead>
<tbody id='contain'>
<script>loadData();</script>
</tbody>
</table>
</fieldset>
<?php 
CLOSE_BOX();
echo close_body();
?>