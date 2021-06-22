<?

require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body_hrd();
include('master_mainMenu.php');
?>
<script language=javascript src='js/sdm_daftarPengajuanMatriksTraining.js'></script>
<?
OPEN_BOX_HRD('',$_SESSION['lang']['listmatrixtraining']);

//filter kondisi sesuai pusat/site ==Jo 31-03-2017==
if($_SESSION['empl']['pusat']==1){
	$optkodeorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	$loktug="";
}
else {
	$loktug="and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
}


//ambil karyawan permanen yang belum keluar
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."')  order by namakaryawan";
$optKar="<option value=''></option>";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    //$optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
    $nam[$bar->karyawanid]=$bar->namakaryawan;
}  	
$sJabat="select * from ".$dbname.".sdm_5matriktraining ";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusKategori[$rJabat['matrixid']]=$rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']]=$rJabat['topik'];
}
$statss[0]=$_SESSION['lang']['notproses'];
$statss[1]=$_SESSION['lang']['alertsdhproses'];

$frm[0]="<fieldset>
	   <legend></legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['find']." ".$_SESSION['lang']['employeename']." : 
	  <!--<select id=pilihkaryawan onchange=loadList()>".$optKar."</select>-->
	  <input type='text' class='myinputtext' id='namaKry' onkeypress=\"return tanpa_kutip(event);\"  size='20' maxlength='20' />
	  <button class=mybutton onclick=loadListKry();>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0 width=1000>
      <thead>
	  <tr class=rowheader style='text-align:center;'>
	  <td>No.</td>
		<td align=center>".$_SESSION['lang']['namatraining']."</td>
		<td align=center>".$_SESSION['lang']['tanggalmulai']."</td>
		<td align=center>".$_SESSION['lang']['tanggalsampai']."</td>
		<td align=center>".$_SESSION['lang']['atasan']."</td>
		<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>
		<td align=center>".$_SESSION['lang']['hrd']."</td>  
		<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd']."</td>  
	  <td>".$_SESSION['lang']['action']."</td>
	  </tr>
	  </head>
	   <tbody id=containerlist>";
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
 if(isset($_POST['namakry']))
  {
  	$namakry=$_POST['namakry'];
  }
$str="select count(distinct a.id) as jlhbrs from ".$dbname.".sdm_matriktraininght a 
left join ".$dbname.".sdm_matriktrainingdt b on a.id = b.headerid
where b.karyawanid in (select karyawanid from ".$dbname.".datakaryawan
where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."') and namakaryawan like '%".$namakry."%' ".$loktug.") order by jlhbrs desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$jlhbrs=$bar->jlhbrs;
}		
//==================
		 
  if(isset($_POST['page']))
     {
	 	$page=$_POST['page'];
	    if($page<0)
		  $page=0;
	 }
	 
  
  $offset=$page*$limit;

  $saya=$_SESSION['standard']['userid'];

  $str="select distinct a.* from ".$dbname.".sdm_matriktraininght a  
  left join ".$dbname.".sdm_matriktrainingdt b on a.id = b.headerid
  where b.karyawanid in (select karyawanid from ".$dbname.".datakaryawan
where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."') and namakaryawan like '%".$namakry."%' ".$loktug.") 
  order by a.tanggaltraining desc  limit ".$offset.",20";	
  $res=mysql_query($str);
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
      if($bar->persetujuan1==$saya)$sayaadalah='atasan';
      if($bar->persetujuanhrd==$saya)$sayaadalah='hrd';
  	$no+=1;
	$frm[1].="<tr class=rowcontent >
	  <td>".$no."</td>
	  <td>".$kamusTopik[$bar->matrikxid]."</td>
	  <td >".tanggalnormal_hrd($bar->tanggaltraining)."</td>	  
	  <td >".tanggalnormal_hrd($bar->sampaitanggal)."</td>
	  <td >".$nam[$bar->persetujuan1]."</td>
	  <td>".$statss[$bar->prs1]."</td>
	  <td >".$nam[$bar->persetujuanhrd]."</td>
	  <td >".$statss[$bar->prshrd]."</td>	  
	  <td align=center>
            <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$bar->id."',event);\">";
             /*if((($bar->persetujuan1==$saya)and($bar->stpersetujuan1==0))or(($bar->persetujuanhrd==$saya)and($bar->sthrd==0)))
             $frm[1].="<button class=mybutton onclick=tolak('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['tolak']."</button>
             <button class=mybutton onclick=setuju('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['setuju']."</button>";*/
	  $frm[1].="</td>
	  </tr>"; // dz note: pdf tembak langsung ke file Pengajuan Training
  }
  $frm[1].="<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	   
$frm[1].="</tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>";

$hfrm[0]=$_SESSION['lang']['list'];
 	 
drawTab_hrd('FRM',$hfrm,$frm,100,900);
CLOSE_BOX_HRD();
echo close_body_hrd('');
?>