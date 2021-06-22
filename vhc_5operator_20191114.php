<?



require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
include('lib/jAddition.php');
OPEN_BOX();
?>
<script type="text/javascript" src="js/vhc_5operator.js" /></script>
<?php
$optKary='';
$skary="select karyawanid,namakaryawan,lokasitugas from ".$dbname.".datakaryawan where tipekaryawan!='0'  order by namakaryawan asc";//echo $skary;
$qkary=mysql_query($skary) or die(mysql_error());
while($rkary=mysql_fetch_assoc($qkary))
{
	$optKary.="<option value=".$rkary['karyawanid'].">".$rkary['namakaryawan']."- [".$rkary['karyawanid']."]</option>";
}
$arrPos=array("0"=>"NonAktif","1"=>"Aktif");
$optStatus="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrPos as $brs => $isi)
{
	$optStatus.="<option value=".$brs.">".$isi."</option>";
}
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sNm="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','KANWIL','PABRIK')";
$qNm=mysql_query($sNm) or die(mysql_error());
while($rNm=mysql_fetch_assoc($qNm))
{    
    $optOrg.="<option value=".$rNm['kodeorganisasi'].">".$rNm['namaorganisasi']."</option>";
}
$optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sDtkry="select namakaryawan,karyawanid,lokasitugas, nik from ".$dbname.".datakaryawan where alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start']." OR ISNULL(tanggalkeluar))  order by namakaryawan asc ";
$qDtkry=mysql_query($sDtkry) or die(mysql_error());
while($rDtkry=mysql_fetch_assoc($qDtkry))
{
        $optKry.="<option value=".$rDtkry['karyawanid']." >".$rDtkry['namakaryawan']."-"."[".$rDtkry['lokasitugas']."]-"."[".$rDtkry['nik']."]
		</option>";
}
$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKendaran="select kodevhc,kodetraksi from ".$dbname.".vhc_5master where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' order by kodevhc desc";
$qKendaran=mysql_query($sKendaran) or die(mysql_error($conn));
while($rKendaran=mysql_fetch_assoc($qKendaran))
{
    $optKendaran.="<option value=".$rKendaran['kodevhc'].">".$rKendaran['kodevhc']." [".$rKendaran['kodetraksi']."]</option>";
}
?>

<fieldset>
	<legend><?php echo $_SESSION['lang']['vhc_operator']?></legend>
	<table cellspacing="1" border="0">
        <tr>
			<td><?php echo $_SESSION['lang']['namakaryawan']?></td>
			<td>:</td>
			<td>
		
			<select id="kd_karyawan" name="kd_karyawan" style='width:150px;'><?php echo $optKry; ?></select></td></tr>
		<tr>
			<td><?php echo $_SESSION['lang']['kodevhc']?></td>
			<td>:</td>
			<td><select id="kdVhc" name="kdVhc"  style='width:150px;'><?php echo $optKendaran; ?></select></td>
		</tr
                <tr>
			<td><?php echo $_SESSION['lang']['status']?></td>
			<td>:</td>
			<td><select id="status" name="status"  style='width:150px;'><?php echo $optStatus; ?></select></td>
		</tr>
	
		<input type="hidden" id="proses" value="insert_karyawan" />
		<tr>
			<td colspan="3">
			<button class=mybutton onclick=simpanOpt()><?php echo $_SESSION['lang']['save']?></button>
			<button class=mybutton onclick=batalOpt()><?php echo $_SESSION['lang']['cancel']?></button></td>
		</tr>
	</table>
</fieldset>
<?php //CLOSE_BOX();
 //OPEN_BOX();
?>
<div style='height:400px;overflow:scroll;'>
<fieldset>
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['nokaryawan']?></td>
	 <td><?php echo $_SESSION['lang']['namakaryawan'];?></td> 
	 <td><?php echo $_SESSION['lang']['status'];?></td>
         <td><?php echo $_SESSION['lang']['kodevhc'];?></td>
	 <td>Action</td>
	 </tr>
	 </thead>
	 <tbody id="container">
	 <?php 
         $optLtgs=makeOption($dbname, 'datakaryawan','karyawanid,lokasitugas');
	$limit=25;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_5operator where karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by nama asc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}

	$str="select * from ".$dbname.".vhc_5operator where karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by nama asc limit ".$offset.",".$limit."";
	if($res=mysql_query($str))
	{
            while($bar=mysql_fetch_object($res))
            {

            $no+=1;
            //echo $minute_selesai; exit();
           
                echo"<tr class=rowcontent id='tr_".$no."'>
                <td>".$no."</td>
                <td>".$bar->karyawanid."</td>
                <td>".$bar->nama."</td>
                <td>".$arrPos[$bar->aktif]."</td>
                <td>".$bar->vhc."</td>
                <td>
                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->karyawanid."','".$bar->aktif."','".$bar->vhc."');\">		
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delOpt('".$bar->karyawanid."');\">
                </td>
                </tr>";
            
           }
            echo"
            <tr class=rowheader><td colspan=6 align=center>
            ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
            <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
            <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
            </td>
            </tr>";	
             
        }
	else
	{
	echo " Gagal,".(mysql_error($conn));
	}	
	
	 ?>
	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table>
</fieldset></div>
<?
CLOSE_BOX();
echo close_body();
?>