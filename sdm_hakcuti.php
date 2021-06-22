<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>

<script language=javascript1.2 src=js/sdm_hakcuti.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX_HRD('',$_SESSION['lang']['hakcuti']);

//untuk tampilkan pilihan tahun
$opttahun='';
for($x=-2;$x<2;$x++)
{
	$dt=date('Y')-$x;
	$opttahun.="<option value='".$dt."'>".$dt."</option>";
}

//Untuk tampilkan pilihan nama karyawan
$today = date("Y-m-d");
$strkry="select karyawanid, namakaryawan from ".$dbname.".datakaryawan 
        where (tanggalkeluar > '".$today."' or tanggalkeluar = '0000-00-00') and lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'"; 
$res=mysql_query($strkry);
while($bar=mysql_fetch_object($res))
{
	$carikrys ="select karyawanid from ".$dbname.".sdm_cutiht where karyawanid ='".$bar->karyawanid."'";
	$reskrys=mysql_query($carikrys);

	$num_rows = mysql_num_rows($reskrys);
	
	if ($num_rows <1){
		$tampilan=$bar->namakaryawan;
		$optskry.="<option value='".$bar->karyawanid."'>".$tampilan."</option>";
	}
	
}

echo"<br>
	<br>Hak cuti yang dihasilkan adalah hak cuti untuk karyawan baru yang belum memiliki hak cuti<br>
	<fieldset style='width:500px;'><table>
	 <tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=idkry>".$optskry."</select></td></tr>
     <tr><td>".$_SESSION['lang']['tahunhakcuti']."</td><td><select id=tahuns>".$opttahun."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['periodehakcuti']."</td><td><input type=text class=myinputtext id=hakcutidari onmousemove=setCalendar(this.id) onkeypress=\"return false;\"  size=10 maxlength=10 />
	 &nbsp;- <input type=text class=myinputtext id=hakcutisampai onmousemove=setCalendar(this.id) onkeypress=\"return false;\"  size=10 maxlength=10 /></td></tr>
	 <tr><td>".$_SESSION['lang']['jumlahhakcuti']."</td><td><input type=text id=jumlahhakcuti size=10 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>

     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=generateCuti()>".$_SESSION['lang']['generate']."</button>
	 <button class=mybutton onclick=batalGenerate()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";

CLOSE_BOX_HRD();
echo close_body_hrd();
?>