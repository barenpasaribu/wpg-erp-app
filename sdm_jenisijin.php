<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>

<script language=javascript1.2 src=js/sdm_jenisijin.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX_HRD('',$_SESSION['lang']['jenisijin']);
$optAbsen='';
$str="select kodeabsen,keterangan from ".$dbname.". sdm_5absensi";
$res=mysql_query($str) or die(mysql_error($conn));
while($bar=mysql_fetch_object($res))
{
	$optAbsen.="<option value='".$bar->kodeabsen."'>".$bar->keterangan."(".$bar->kodeabsen.")</option>";
}
echo"<fieldset style='width:635px;'><table>
     <tr><td>".$_SESSION['lang']['tipe']."</td><td><input type=text id=kodeijin size=3  onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['jenisijin']."</td><td><input type=text id=jenisijin size=60 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['iscuti']."</td><td><input type=checkbox id=iscuti></td></tr>
	 <tr><td>".$_SESSION['lang']['ispotong']."</td><td><input type=checkbox id=ispotong onchange=cekpotong()></td></tr>
	 <tr><td>".$_SESSION['lang']['isdayoff']."</td><td><input type=checkbox id=isdayoff></td></tr>
	 <tr><td>".$_SESSION['lang']['jumlahhari']."</td><td><input type=text id=jumlahhari size=60 onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>
	<tr><td>".$_SESSION['lang']['kodeabsen']."</td><td><select id=kdabsen>".$optAbsen."</select></td></td></tr>
     </table>
	 <input type=hidden id=dttdlkp value=".$_SESSION['lang']['datatidaklengkap'].">
	 <input type=hidden id=cnfdel value=".$_SESSION['lang']['confirmhapus'].">
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJI()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJI()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
//echo open_theme($_SESSION['lang']['availvhc']);
echo "<div id=container>";
	$str1="select * from ".$dbname.".sdm_jenis_ijin_cuti where status = 1 order by 'jenisizin'";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:635px;'>
	     <thead>
		 <tr class=rowheader><td style='width:50px;'>".$_SESSION['lang']['tipe']."</td>
		 <td style='width:150px;'>".$_SESSION['lang']['jenisijin']."</td>
		 <td style='width:50px;'>".$_SESSION['lang']['iscuti']."</td>
		 <td style='width:50px;'>".$_SESSION['lang']['ispotong']."</td>
		 <td style='width:50px;'>".$_SESSION['lang']['isdayoff']."</td>
		 <td style='width:50px;'>".$_SESSION['lang']['jumlahhari']."</td>
		 <td style='width:50px;'>".$_SESSION['lang']['kodeabsen']."</td>
		 <td style='width:50px;'>*</td>
		 </tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		if ($bar1->isCuti){
			$ic = 'checked';
		}
		else {
			$ic = '';
		}
		
		if ($bar1->isPotong==1){
			$ip = 'checked';
		}
		else {
			$ip = '';
		}
		
		if ($bar1->isDayOff==1){
			$idf = 'checked';
		}
		else {
			$idf = '';
		}
		
		echo"<tr class=rowcontent><td align=center>".$bar1->id."</td>
		<td>".$bar1->jenisizincuti."</td>
		<td><input type=checkbox value=".$bar1->isCuti." ".$ic." disabled></td>
		<td><input type=checkbox value=".$bar1->isPotong." ".$ip." disabled></td>
		<td><input type=checkbox value=".$bar1->isDayOff." ".$idf." disabled></td>
		<td>".$bar1->jumlahhari."</td>
		<td>".$bar1->kodeabsen."</td>
		<td align=center>
		<img src=images/application/application_edit.png class=resicon  caption='Edit' title='Edit Data' onclick=\"fillField('".$bar1->id."','".$bar1->jenisizincuti."',".$bar1->isCuti.",".$bar1->isPotong.",".$bar1->isDayOff.",".$bar1->jumlahhari.",'".$bar1->kodeabsen."');\">
		<img src=images/application/application_delete.png class=resicon  title='Hapus Data' onclick=\"hapusJI('".$bar1->id."');\">
		</td>
		</tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
//echo close_theme();
CLOSE_BOX_HRD();
echo close_body_hrd();
?>