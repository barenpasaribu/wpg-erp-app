<?
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>

<script language='javascript' src='js/sdm_pengaturan_cutigolongan.js'></script>
<?
#ambil komponen gaji
 include('master_mainMenu.php');

OPEN_BOX_HRD('',$_SESSION['lang']['hakcutipergl']);

//lokasi tugas
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct a.lokasitugas as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
	  left join ".$dbname.".organisasi b on a.lokasitugas=b.kodeorganisasi order by namaorganisasi asc";
$qOrg=mysql_query($sOrg);
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$sOrg="select distinct a.subbagian as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
	  inner join ".$dbname.".organisasi b on a.subbagian=b.kodeorganisasi order by namaorganisasi asc";
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



echo"<div style='height:530px;overflow:scroll;'><fieldset style='width:500px;'><table>
		  <tr><td>".$_SESSION['lang']['lokasitugas']."</td><td><select id=org style=width:150px>".$optOrg."</select></td></tr>
		  <tr><td>".$_SESSION['lang']['kodegolongan']."</td><td><select id=gol style=width:150px>".$optGol."</select></td></tr>
          <tr><td>".$_SESSION['lang']['hakcuti']."</td><td><input type=text class=myinputtext id=hakcuti   onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:120px></td></tr>  
          <tr><td>".$_SESSION['lang']['masatunggu']."</td><td><input type=text class=myinputtext id=masatunggu onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:120px>".$_SESSION['lang']['bulan']."</td></tr>        
          <tr><td>".$_SESSION['lang']['sisacutibr']."</td><td><input type=text class=myinputtext id=sisaberlaku onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:120px>".$_SESSION['lang']['bulan']."</td></tr>        
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
					<td>".$_SESSION['lang']['hakcuti']."</td> 
					<td>".$_SESSION['lang']['masatunggu']." (".$_SESSION['lang']['bulan'].")</td> 
					<td>".$_SESSION['lang']['sisacutibr']." (".$_SESSION['lang']['bulan'].")</td> 
                                        
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