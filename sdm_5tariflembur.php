<?
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>

<script language='javascript' src='js/sdm_5tariflembur.js'></script>
<?
#ambil komponen gaji
 include('master_mainMenu.php');
$optKomponen="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sAkun="select  id,name,plus from ".$dbname.".sdm_ho_component order by name";
$qAkun=mysql_query($sAkun) or die(mysql_error($conn));
while($rAkun=mysql_fetch_assoc($qAkun))
{
	if ($rAkun['plus']==0){
		$signs = '-';
	}
	else {
		$signs = '+';
	}
    $optKomponen.="<option value='".$rAkun['id']."'>".$rAkun['name']."(".$signs.")</option>";
}
OPEN_BOX_HRD('',$_SESSION['lang']['tarifdasarlembur']);
if($_SESSION['language']=='EN'){
        $zz="namaakun1 as namaakun";
}
else{
    
        $zz="namaakun";
}

//tambah filter lokasi tugas ==Jo 06-05-2017==
if($_SESSION['empl']['pusat']==1){
	$whrorg="";
}
else{
	$whrorg="where b.kodeorganisasi ='".$_SESSION['empl']['lokasitugas']."'";
}

//lokasi tugas
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct a.lokasitugas as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
	  left join ".$dbname.".organisasi b on a.lokasitugas=b.kodeorganisasi ".$whrorg." order by namaorganisasi asc";
$qOrg=mysql_query($sOrg);
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
/*$sOrg="select distinct a.subbagian as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
	  inner join ".$dbname.".organisasi b on a.subbagian=b.kodeorganisasi order by namaorganisasi asc";
$qOrg=mysql_query($sOrg);
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}*/


$optGol="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGol="select kodegolongan,namagolongan from ".$dbname.".sdm_5golongan";
$qGol=mysql_query($sGol) or die(mysql_error($conn));
while($rGol=mysql_fetch_assoc($qGol))
{
    $optGol.="<option value='".$rGol['kodegolongan']."'>".$rGol['namagolongan']."</option>";
}



echo"<div style='height:530px;overflow:scroll;'><fieldset style='width:500px;'><table>
     
		  <tr><td>".$_SESSION['lang']['lokasitugas']."</td><td><select id=org style=width:150px>".$optOrg."</select></td></tr>
		  <tr><td>".$_SESSION['lang']['kodegolongan']."</td><td><select id=gol style=width:150px>".$optGol."</select></td></tr>
          <tr><td>".$_SESSION['lang']['tarifdasar']."</td><td><input type=text id=tarif class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);\"></td></tr>     
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
echo "<div>
        <table class=sortable cellspacing=1 border=1>
             <thead>
                 <tr class=rowheader>
					<td>".$_SESSION['lang']['lokasitugas']."</td>                     
                    <td>".$_SESSION['lang']['kodegolongan']."</td> 
                    <td>".$_SESSION['lang']['tarifdasar']."</td>
                                        
                    <td style='width:60px;'>".$_SESSION['lang']['action']."</td></tr>
                 </thead>
                 <tbody id=container>"; 
                echo"<script>loadData()</script>";
                echo" </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
echo "</div></div>";
CLOSE_BOX_HRD();
echo close_body_hrd();
?>