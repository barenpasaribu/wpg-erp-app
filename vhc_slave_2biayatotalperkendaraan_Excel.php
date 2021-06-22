<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt'];
	$unit=$_GET['unit'];
	$periode=$_GET['periode'];
        $tglAwal=tanggalsystem($_GET['tglAwal']);
        $tglAkhir=tanggalsystem($_GET['tglAkhir']);
if($unit=='')
{
    echo"warning:Unit tidak boleh kosong";exit();
}
if($tglAwal==''||$tglAkhir==''){
	echo "Warning: silakan mengisi tanggal"; exit;
}

	$kdOrg=substr($unit,0,4);
        $akunkdari='';
  $akunksampai='';
  $strh="select distinct noakundebet,sampaidebet  from ".$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
  $resh=mysql_query($strh) or die(mysql_error($conn));
  while($barh=mysql_fetch_object($resh))
  {
      $akunkdari=$barh->noakundebet;
      $akunksampai=$barh->sampaidebet;
  }
  if($akunkdari=='' or $akunksampai=='')
  {
      exit("Error: parameter jurnal untuk LPVHC(by kendaraan) belum dibuat");
  }
  

	$str="select sum(debet) as jumlah, kodevhc from ".$dbname.".keu_jurnaldt_vw where
		  kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".substr($unit,0,4)."%')
		  and tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and nojurnal like '%".substr($unit,0,4)."%'
		  and (noakun between '".$akunkdari."' and '".$akunksampai."') 
                  and (noreferensi not like '%ALK_KERJA_AB%' or noreferensi is NULL)
                  group by kodevhc";
//=======================================================        
//	$str="select sum(debet) as jumlah, kodevhc from ".$dbname.".keu_jurnaldt_vw where
//		  kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".$kdOrg."%')
//		  and tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and nojurnal like '%".$kdOrg."%'
//		  and noreferensi not like '%ALK_KERJA_AB%'
//                  group by kodevhc";
//exit("Error".$str);

//=================================================
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		$stream.=$_SESSION['lang']['biayatotalperkendaraan'].": ".$unit." : ".$periode." (".tanggalnormal($tglAwal)." - ".tanggalnormal($tglAkhir).")<br>
		<table border=1>
				    <tr>
			  <td bgcolor=#DEDEDE align=center>No.</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['periode']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmljamkerja']."</td>  
                          <td bgcolor=#DEDEDE align=center>Price/Unit</td>                                 
					</tr>";
#ambil jumlah jam per kendaraan
   $str1="select sum(jumlah) as jumlah,kodevhc from ".$dbname.".vhc_rundt a left join 
       ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
      where tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and kodevhc in (select kodevhc from ".$dbname.".vhc_5master
      where kodetraksi like '".$unit."%')
      group by kodevhc";
/*   
$str1="select sum(jumlah) as jumlah,kodevhc from ".$dbname.".vhc_rundt_vw
      where tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and kodevhc in (select kodevhc from ".$dbname.".vhc_5master
      where kodetraksi like '".$unit."%')
      group by kodevhc";
 * 
 */
   $res1=mysql_query($str1); 
   $jumlahjam=Array();
   while($bar1=mysql_fetch_object($res1))
   {
       $jumlahjam[$bar1->kodevhc]=$bar1->jumlah;
   }
   
  while($bar=mysql_fetch_object($res))
	{
		$no+=1; $total=0;
                if($jumlahjam[$bar->kodevhc]>0)
                    $rpunit=$bar->jumlah/$jumlahjam[$bar->kodevhc];
                else
                    $rpunit=0;                
		$stream.="<tr>
                          <td align=right>".$no."</td>
                          <td>".$bar->kodevhc."</td>
                          <td align=right>".$periode."</td>
                          <td align=right>".number_format($bar->jumlah)."</td>
                          <td align=right>".$jumlahjam[$bar->kodevhc]."</td> 
                          <td align=right>".number_format($rpunit)."</td>                              
			</tr>"; 	
	}

	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
  }
	
$nop_="BiayaTotalPerKendaraan_".$unit."_".$periode;
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