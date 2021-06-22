<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

//+++++++++++++++++++++++++++++++++++++++++++++
/*if ($_SESSION['empl']['pusat']==1){
  $wherelk="";
}
else {
  $wherelk="and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
}*/
if($_GET['lktgs']!='')
{
 $wherelk="and kodeorg='".$_GET['lktgs']."'";
} 
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and 
     (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."') order by namakaryawan";
$res=mysql_query($str);
//$optKar="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
    //$optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
    $nam[$bar->karyawanid]=$bar->namakaryawan;
}	

$head="".$_SESSION['lang']['daftarpdinas']."
        <table border=1>
        <thead><tr>
	   	<td class=firsttd>No.</td>
		<td align=center>".$_SESSION['lang']['notransaksi']."</td>
		<td align=center>".$_SESSION['lang']['karyawan']."</td>
		<td align=center>".$_SESSION['lang']['tanggalsurat']."</td>
		<td align=center>".$_SESSION['lang']['tujuan']."</td>
		<td align=center>".$_SESSION['lang']['atasan']."</td>
		<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>
		<td align=center>".$_SESSION['lang']['hrd']."</td>
		<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd']."</td>
		<td align=center>".$_SESSION['lang']['statuspdinas']."</td>";
		
$head.="</tr></thead><tbody>";	

//get All user id from employee table
//$str1="select * from ".$dbname.".sdm_pjdinasht order by tanggalbuat desc,notransaksi desc ";	 
 $str1="select * from ".$dbname.".sdm_pjdinasht 
        where 1=1
		".$wherelk." order by notransaksi desc, tanggalbuat desc";
//echo "warning: ".$str1;
$res1=mysql_query($str1);
$no=0;
$grandTotal=0;
while($bar1=mysql_fetch_object($res1))
{
	$no+=1;
	$namakaryawan='';
	  $strx="select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$bar1->karyawanid;

	  $resx=mysql_query($strx);
	  while($barx=mysql_fetch_object($resx))
	  {
	  	$namakaryawan=$barx->namakaryawan;
	  }
   if($bar1->statuspersetujuan==2)
     $stpersetujuan=$_SESSION['lang']['ditolak'];
   else if($bar1->statuspersetujuan==1)
    $stpersetujuan=$_SESSION['lang']['disetujui'];
   else {
    $stpersetujuan=$_SESSION['lang']['wait_approve'];	
	
   }

   if($bar1->statushrd==2)
     $sthrd=$_SESSION['lang']['ditolak'];
  else if($bar1->statushrd==1)
     $sthrd=$_SESSION['lang']['disetujui'];
  else{
     $sthrd=$_SESSION['lang']['wait_approve'];
	
  }
	//cek tujuan yang terisi ==Jo 12-01-2016==
	$tujuan='';
  if ($bar1->tujuan1==''){
	if ($bar1->tujuan2==''){
		if ($bar1->tujuan3==''){
			if ($bar1->tujuanlain==''){
				$tujuan='';	
			}
			else {
			 $tujuan= $bar1->tujuanlain;
			}
		}
		else {
		 $tujuan= $bar1->tujuan3;
		}
	}
	else {
	 $tujuan= $bar1->tujuan2;
	}
  }
  else {
	 $tujuan= $bar1->tujuan1;
  }
  
  $pdstat='';
	if ($bar1->statuspertanggungjawaban==0){
		if (date('Y-m-d',strtotime($bar1->tanggalperjalanan))<date('Y-m-d')){
			//belum tanggal dinas
			$pdstat='';
		}
		else if ((date('Y-m-d')>=date('Y-m-d',strtotime($bar1->tanggalperjalanan))) and (date('Y-m-d')<=date('Y-m-d',strtotime($bar1->tanggalperjalanan))) and $bar1->statuspersetujuan==1 and $bar1->statushrd==1){//tanggal dinas
			$pdstat=$_SESSION['lang']['dalamperjalanan'];
		}
		
	}
	else if ($bar1->statuspertanggungjawaban==1) {
		$sljn="select nama from setup_5parameter where kode='".$bar1->jenispertanggungjawaban."'";
		$ressljn=$eksi->sSQL($sljn);
		foreach($ressljn as $barsljn){
			$pdstat=$_SESSION['lang'][$barsljn['nama']];
		}
	}
  
$head.="<tr>
        <td class=firsttd>".$no."</td>
		<td>'".$bar1->notransaksi."</td>
		<td>".$namakaryawan."</td>
		<td>".tanggalnormal_hrd($bar1->tanggalbuat)."</td>	
		<td>".$tujuan."</td>		
		<td>".$nam[$bar1->persetujuan]."</td>
		<td>".$stpersetujuan."</td>
		<td>".$nam[$bar1->hrd]."</td>
		<td>".$sthrd."</td>
		<td>".$pdstat."</td>";
	
$head.="</tr>";
//add terbilang below value row
/*
$terbilang='-';
$str3="select terbilang from ".$dbname1.".payrollterbilang
        where userid=".$bar1[0]." and `type`='".$tipe."'
		and periode='".$periode."' limit 1";	
$res3=mysql_query($str3);
while($bar3=mysql_fetch_object($res3))
{
	$terbilang=$bar3->terbilang;
}		
$head.="<tr><td bgcolor=#ffffff colspan='".(count($arrVal)+1)."'>".$terbilang."</td></tr>"; 
*/
}	     
$head.="</tbody><tfoot>
       
		</tfoot></table>";
		
$stream=$head;		
$nop_=$_SESSION['lang']['daftarpdinas'];
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
} 
?>