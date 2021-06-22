<?
require_once('master_validation.php');
//include('lib/nangkoelib.php');
include('lib/eagrolib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_2laporanGaji.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper('Laporan Gaji').'</b>');

//=================ambil unit;  
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING') {

	$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where induk = '".$_SESSION['empl']['kodeorganisasi']."' ";

	$str1="select distinct periode from ".$dbname.".sdm_5periodegaji where 
	kodeorg like '".$_SESSION['empl']['kodeorganisasi']."%' order by periode desc";

  $str4="select distinct a.kodeorganisasi,namaorganisasi from ".$dbname.".organisasi a inner join ".$dbname.".datakaryawan b ON a.kodeorganisasi=b.subbagian AND a.kodeorganisasi LIKE '".$_SESSION['empl']['kodeorganisasi']."%' ";

} else {
	$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."' ";

	$str1="select distinct periode from ".$dbname.".sdm_5periodegaji where 
	kodeorg like '".$_SESSION['empl']['lokasitugas']."' order by periode desc";

  $str4="select distinct a.kodeorganisasi,namaorganisasi from ".$dbname.".organisasi a inner join ".$dbname.".datakaryawan b ON a.kodeorganisasi=b.subbagian AND a.kodeorganisasi LIKE '".$_SESSION['empl']['lokasitugas']."%' ";
}

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
	$optperiode.="<option value='".$bar1->periode."'>".$bar1->periode."</option>";
}

  $str2="select distinct kode,nama from ".$dbname.".sdm_5departemen";
  $res2=mysql_query($str2);
  $optdep="<option value=''>Seluruhnya</option>";
  while($bar2=mysql_fetch_object($res2))
  {
    $optdep.="<option value='".$bar2->kode."'>".$bar2->nama."</option>";
  }
	
  $str3="select distinct kodegolongan,namagolongan from ".$dbname.".sdm_5golongan";
  $res3=mysql_query($str3);
  $optgol="<option value=''>Seluruhnya</option>";
  while($bar3=mysql_fetch_object($res3))
  {
    $optgol.="<option value='".$bar3->kodegolongan."'>".$bar3->namagolongan."</option>";
  }

  $res4=mysql_query($str4);
  $optsubunit="<option value='ALL'>Seluruhnya</option>";
  $optsubunit.="<option value=''>UMUM</option>";
  while($bar4=mysql_fetch_object($res4))
  {
    $optsubunit.="<option value='".$bar4->kodeorganisasi."'>".$bar4->namaorganisasi."</option>";
  }
$opttipe.="<option value=''>Seluruhnya</option>";
$opttipe.="<option value='Bulanan'>Bulanan</option>";
$opttipe.="<option value='Harian'>Harian</option>";

echo"<fieldset>
     <legend>Laporan Gaji</legend>
	 ".$_SESSION['lang']['unit']."<select id=unit style='width:150px;'>".$optunit."</select>
   ".$_SESSION['lang']['subunit']."<select id=subunit style='width:150px;'>".$optsubunit."</select>
	 	 ".$_SESSION['lang']['jenis']."<select id=jenis style='width:150px;'>".$opttipe."</select>
	 	 	 ".$_SESSION['lang']['periodegaji']."<select id=periode style='width:170px;'>".$optperiode."</select>
       Departemen <select id=dept style='width:170px;'>".$optdep."</select>
       Golongan <select id=gol style='width:170px;'>".$optgol."</select>
	 <button class=mybutton onclick=getlaporanGaji()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX();
//	 <img onclick=hutangSupplierKePDF(event,'log_laporanhutangsupplier_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>

echo"<span id=printPanel style='display:none;'>
     <img onclick=laporanGajiKeExcel(event,'sdm_slave_2laporanGaji_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:100%px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=600>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>Periode</td>
			  <td align=center>Karyawanid</td>
              <td align=center>NIK</td>  
              <td align=center>Nama Karyawan</td>    
              <td align=center>Nama Jabaatan</td>
              <td align=center>STATUS</td>
              <td align=center>Golongan</td>
              <td align=center  bgcolor='f8ea06'>Gaji Pokok</td>
              <td align=center  bgcolor='f8ea06'>Tunjangan Golongan</td>
              <td align=center  bgcolor='f8ea06'>Tunjangan Jabatan</td>
              <td align=center  bgcolor='f8ea06'>Natura Pekerja</td>
              <td align=center  bgcolor='f8ea06'>Natura Keluarga</td>
              <td align=center  bgcolor='f8ea06'>Tunjangan Prestasi</td>
              <td align=center  bgcolor='f8ea06'>Total Upah</td>

              <td align=center  bgcolor='f8ea06'>Lembur</td>
              <td align=center  bgcolor='f8ea06'>Premi BKM</td>
              <td align=center  bgcolor='f8ea06'>Pendapatan lain</td>
              <td align=center  bgcolor='f8ea06'>Tunj. Kehadiran</td>
              <td align=center  bgcolor='f8ea06'>Tunj. Harian</td>
              <td align=center  bgcolor='f8ea06'>Tunj. Lainnya</td>

              <td align=center  bgcolor='f8ea06'>Potongan HK</td>
              <td align=center  bgcolor='f8ea06'>Denda BKM</td>
              <td align=center  bgcolor='f8ea06'>Potongan Absen</td>
              <td align=center  bgcolor='f8ea06'>Potongan Lainnya</td>

              <td align=center  bgcolor='f8ea06'>GROSS</td>

              <td align=center  bgcolor='#66FFFF'>JKK</td>
              <td align=center  bgcolor='#66FFFF'>JKM</td>
              <td align=center  bgcolor='#66FFFF'>BPJS Kes.</td>

              <td align=center  bgcolor='#66FFFF'>TOTAL GAJI (BRUTO)</td>

              <td align=center  bgcolor='#66FFFF'>Rapel Kenaikan</td>
              <td align=center  bgcolor='#66FFFF'>THR</td>
              <td align=center  bgcolor='#66FFFF'>Bonus</td>

              <td align=center  bgcolor='#66FFFF'>Biaya Jabatan</td>
              <td align=center  bgcolor='#66FFFF'>JHT Kar.</td>
              <td align=center  bgcolor='#66FFFF'>JP Kar.</td>

              <td align=center  bgcolor='#66FFFF'>Gaji Netto sebulan</td>
              <td align=center  bgcolor='#66FFFF'>Gaji Netto setahun</td>

              <td align=center  bgcolor='#FFFF99'>Pph 21</td>
              <td align=center  bgcolor='#FFFF99'>Pph 21 THR</td>
              <td align=center  bgcolor='#FFFF99'>Pph 21 Bonus</td>

              <td align=center  bgcolor='#CCCCCC'>THP BRUTO</td>
              <td align=center  bgcolor='#FFFF99'>Insentif Pph 21</td>
              <td align=center  bgcolor='#FFFF99'>JP + JHT + BPJS Karyawan </td>
              
              <td align=center  bgcolor='#FFFF99'>Angsuran Pinjaman</td>
              <td align=center  bgcolor='#FFFF99'>Angsuran Egrek</td>
              <td align=center  bgcolor='#FFFF99'>Angsuran Angkong</td>

              <td align=center  bgcolor='#CCCCCC'>THP NETTO</td>


			</tr>  
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
  //<td align=center>".$_SESSION['lang']['periode']."</td>
?>