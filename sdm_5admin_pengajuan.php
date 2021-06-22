<?
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('lib/eksilib.php');
echo OPEN_BODY();
?>

<script language='javascript' src='js/sdm_5admin_pengajuan.js'></script>
<?
#ambil komponen gaji
 include('master_mainMenu.php');

OPEN_BOX_HRD();

//tambah filter lokasi tugas ==Jo 07-07-2017==
if($_SESSION['empl']['pusat']==1){
	$whr="";
}
else{
	$whr="where b.kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}

//lokasi tugas
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct a.lokasitugas as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
	  left join ".$dbname.".organisasi b on a.lokasitugas=b.kodeorganisasi ".$whr." order by namaorganisasi asc";
$qOrg=mysql_query($sOrg);
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}



$optGol="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGol="select kodegolongan,namagolongan from ".$dbname.".sdm_5golongan";
$qGol=mysql_query($sGol) or die(mysql_error($conn));
while($rGol=mysql_fetch_assoc($qGol))
{
    $optGol.="<option value='".$rGol['kodegolongan']."'>".$rGol['namagolongan']."</option>";
}

$optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKry="select karyawanid,namakaryawan from datakaryawan order by namakaryawan";
$resKry=$eksi->sSQL($sKry);
foreach($resKry as $barKry){
	$optKry.="<option value='".$barKry['karyawanid']."'>".$barKry['namakaryawan']."</option>";
}

$optUsr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sUsr="select a.namauser, a.karyawanid, b.namakaryawan from user a
left join datakaryawan b on a.karyawanid=b.karyawanid order by b.namakaryawan";
$resUsr=$eksi->sSQL($sUsr);
foreach($resUsr as $barUsr){
	$optUsr.="<option value='".$barUsr['karyawanid']."'>".$barUsr['namakaryawan']." (".$barUsr['namauser'].")</option>";
}

echo "<fieldset>
<legend><b>".$_SESSION['lang']['admincutipd']."</b></legend>
<table>
<tr>
<td width='80px'><img class='delliconBig' onclick='baru();' src='images/newfile.png' title='".$_SESSION['lang']['new']."'></td>
<td><img class='delliconBig' onclick='listdata();' src='images/orgicon.png' title='".$_SESSION['lang']['list']."'></td>
</tr>
<tr>
<td>".$_SESSION['lang']['new']."</td>
<td>".$_SESSION['lang']['list']."</td>
</tr>
</table>
</fieldset>";

echo"<fieldset id=form style='width:500px;display:none;' >
		<legend><b>".$_SESSION['lang']['form']."</b></legend>
		<table>
     
		  <tr><td>".$_SESSION['lang']['lokasitugas']."</td><td><select id=org style=width:150px onchange=getKaryawan()>".$optOrg."</select></td></tr>
		  <tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=kryw style=width:150px>".$optKry."</select></td></tr>
 		  <tr><td>".$_SESSION['lang']['userlogin']."</td><td><select id=user style=width:150px>".$optUsr."</select></td></tr>
              
         </table>
         <input type=hidden id=method value='insert'>
         <input type=hidden id=cnfsmpn value='".$_SESSION['lang']['alertqinsert']."'>
         <input type=hidden id=cnfhps value=".$_SESSION['lang']['confirmhapus'].">
         <input type=hidden id=tdklkp value=".$_SESSION['lang']['datatidaklengkap'].">
         <input type=hidden id=dtsmpn value='".$_SESSION['lang']['alertinsert']."'>
         <input type=hidden id=ids >
         <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
         <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
         </fieldset>";
echo "
		<fieldset id=listdata >
		<legend><b>".$_SESSION['lang']['list']."</b></legend>
		<select id='filter'>
		<option value=''>".$_SESSION['lang']['all']."</option>
		<option value='namaorganisasi'>".$_SESSION['lang']['lokasitugas']."</option>
		<option value='namakaryawan'>".$_SESSION['lang']['namakaryawan']."</option>
		<option value='namakaryawan2'>".$_SESSION['lang']['userlogin']."</option>
		</select>
		<span id='findword'>".$_SESSION['lang']['keyword']." : </span>
		<input type='text' id='keyword' />
		<button class='mybutton' onclick='search()'>".$_SESSION['lang']['find']."</button>
		<br/><br/>
        <table class=sortable cellspacing=1 border=1>
             <thead>
                 <tr class=rowheader>
					<td>".$_SESSION['lang']['lokasitugas']."</td>                     
                    <td>".$_SESSION['lang']['namakaryawan']."</td> 
                    <td>".$_SESSION['lang']['userlogin']."</td>
                                        
                    <td style='width:60px;'>".$_SESSION['lang']['action']."</td></tr>
                 </thead>
                 <tbody id=container>"; 
                echo"<script>loadData()</script>";
                echo" </tbody>
                 <tfoot>
                 </tfoot>
                 </table>
				 </fieldset>";
echo "";
CLOSE_BOX_HRD();
echo close_body_hrd();
?>