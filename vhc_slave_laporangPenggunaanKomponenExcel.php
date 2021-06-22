<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	
	$proses=$_GET['proses'];
	$comId=$_GET['pt'];
	$kdVhc=$_GET['kdVhc'];
	$period=$_GET['periode'];
	
/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================

  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$comId."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
	
	$stream.="
	<table>
	<tr><td colspan=8 align=center>".$_SESSION['lang']['laporanPenggunaanKomponen']."</td></tr>";
	if($comId!='')
	{
		$stream.="<tr><td colspan=3>".$_SESSION['lang']['unit'].":".$namapt."</td></tr>";
	}
	if($kdVhc!='')
	{
		$stream.="<tr><td colspan=3>".$_SESSION['lang']['kodevhc'].":".$kdVhc."</td></tr>";
	}
	if($period!='')
	{
		$stream.="<tr><td colspan=3>".$_SESSION['lang']['periode'].":".$period."</td></tr>";
	}
	$stream.="<tr><td colspan=3>&nbsp;</td></tr>
	</table>
	<table border=1>
	<tr>
	<td bgcolor=#DEDEDE align=center>No.</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
	";
	$stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>	
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>	
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>	
	</tr>";
	
	if(($comId=='')&&($kdVhc=='')&&($period==''))
	{
		$where=" order by a.tanggal asc";
	}
	elseif(($comId!='')&&($kdVhc=='')&&($period==''))
	{
		$where=" where a.kodeorg='".$comId."' order by a.tanggal asc";
	}
	elseif(($comId!='')&&($kdVhc!='')&&($period==''))
	{
		$where=" where a.kodeorg='".$comId."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";	
	}
	elseif(($comId=='')&&($kdVhc!='')&&($period==''))
	{
		$where=" where a.kodevhc='".$kdVhc."' order by a.tanggal asc";	
	}
	elseif(($comId!='')&&($kdVhc!='')&&($period!=''))
	{
		$where=" where  a.kodeorg='".$comId."' and a.kodevhc='".$kdVhc."' and  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	elseif(($comId=='')&&($kdVhc=='')&&($period!=''))
	{
		$where=" where  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	elseif(($comId!='')&&($kdVhc=='')&&($period!=''))
	{
		$where=" where  a.kodeorg='".$comId."'  and  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	elseif(($comId=='')&&($kdVhc!='')&&($period!=''))
	{
		$where=" where a.kodevhc='".$kdVhc."' and  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	$sql="select a.tanggal,a.kodevhc,b.* from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi ".$where."";
	
	$qRvhc=mysql_query($sql) or die(mysql_error());
	$row=mysql_num_rows($qRvhc);
	if($row>1)
	{
		$no=0;
		while($rRvhc=mysql_fetch_assoc($qRvhc))
		{
			$no+=1;
			$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rRvhc['kodebarang']."'";
			$qbrg=mysql_query($sbrg) or die(mysql_error());
			$rbrg=mysql_fetch_assoc($qbrg);	
			$stream.="	<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$rRvhc['notransaksi']."</td>
					<td>".tanggalnormal($rRvhc['tanggal'])."</td>
					<td>".$rRvhc['kodevhc']."</td>
					<td>".$rbrg['namabarang']."</td>
					<td>".$rRvhc['satuan']."</td>
					<td>".$rRvhc['jumlah']."</td>
					<td>".$rRvhc['keterangan']."</td>
				</tr>";
		}
	}
	else
	{
		$stream.="<tr class=rowcontent><td colspan=9>Not Found</td></tr>";
	}

	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	

$nop_="ReportComponentUsage";
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