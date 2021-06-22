<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$comId=$_GET['pt'];
	$kdVhc=$_GET['kdVhc'];
	$jnsVhc=$_GET['jnsVhc'];
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
	if(($comId=='')&&($jnsVhc=='')&&($kdVhc=='')&&($period==''))
		{
			$where=" order by a.tanggal asc";
		}
		elseif(($comId!='')&&($jnsVhc=='')&&($kdVhc=='')&&($period==''))
		{
			$where=" where a.kodeorg='".$comId."' order by a.tanggal asc";	
		}
		elseif(($comId=='')&&($jnsVhc!='')&&($kdVhc=='')&&($period==''))
		{
			$where=" where a.`jenisvhc`='".$jnsVhc."' order by a.tanggal asc";	
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc=='')&&($period==''))
		{
			$where=" where a.kodeorg='".$comId."' and a.`jenisvhc`='".$jnsVhc."' order by a.tanggal asc";	
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc!='')&&($period==''))
		{
			$where=" where a.kodeorg='".$comId."' and a.`jenisvhc`='".$jnsVhc."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";	
		}
		elseif(($comId=='')&&($jnsVhc!='')&&($kdVhc=='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' order by a.tanggal asc";	
		}
		elseif(($comId!='')&&($jnsVhc=='')&&($kdVhc=='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' and a.kodeorg='".$comId."' order by a.tanggal asc";
		}
		elseif(($comId=='')&&($jnsVhc!='')&&($kdVhc!='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' and a.jenisvhc='".$jnsVhc."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc=='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' and a.kodeorg='".$comId."' and a.jenisvhc='".$jnsVhc."' order by a.tanggal asc";
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc!='')&&($period!=''))
		{
			$where="where a.tanggal like '%".$period."%' and a.kodeorg='".$comId."' and a.`jenisvhc`='".$jnsVhc."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";
		}
	
			$stream.="
			<table>
			<tr><td colspan=15 align=center>".$_SESSION['lang']['laporanPekerjaan']."</td></tr>";
			if($comId!='')
			{
				$stream.="
			<tr><td colspan=6>".$_SESSION['lang']['unit'].":".$namapt."</td></tr>";
			}
			if($period!='')
			{
			$stream.="
			<tr><td colspan=6>".$_SESSION['lang']['periode'].":".$period."</td></tr>";
			}
			$stream.="
			<tr><td colspan=6>&nbsp;</td></tr>
			</table>
			<table border=1>
						<tr>
						<td bgcolor=#DEDEDE align=center valign=middle rowspan=2>No.</td>
						<td bgcolor=#DEDEDE align=center valign=middle rowspan=2 colspan='9'>".$_SESSION['lang']['header']."
						<table width='100%'>
						<tr>
						<td align=center>".$_SESSION['lang']['notransaksi']."</td>
						<td align=center>".$_SESSION['lang']['tanggal']." </td>
						<td align=center>".$_SESSION['lang']['jenisvch']."</td>
						<td align=center>".$_SESSION['lang']['kodevhc']."</td>
						<td align=center>".$_SESSION['lang']['satuan']."</td>
						<td align=center>". $_SESSION['lang']['vhc_kmhm_awal']."</td>
						<td align=center>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
						<td align=center>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
						<td align=center>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
						</tr>
						</table></td>
						<td bgcolor=#DEDEDE align=center valign=middle rowspan=2 colspan='4'>".$_SESSION['lang']['vhc_detail_pekerjaan']."
						<table width='100%'>
						<tr>
						<td align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
						<td align=center>".$_SESSION['lang']['alokasibiaya']."</td>
						<td align=center>".$_SESSION['lang']['vhc_berat_muatan']." (Ton)</td>
						<td align=center>".$_SESSION['lang']['jumlahrit']."</td>
						<td align=center>".$_SESSION['lang']['biaya']."</td>
						</tr>
						</table>
						</td>
						<td bgcolor=#DEDEDE align=center valign=middle rowspan=2 colspan='3'>".$_SESSION['lang']['vhc_detail_operator']."
						<table width='100%'>
						<tr>
						<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
						<td align=center>".$_SESSION['lang']['vhc_posisi']."</td>
						<td align=center>".$_SESSION['lang']['upahkerja']."</td>
						</tr>
						</table>
						</td>
						</tr>
						</table>						
						";
						
						/*<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['notransaksi']."</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tanggal']."</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['namakaryawan']."</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['kodevhc']."</td>
						<td bgcolor=#DEDEDE align=center colspan=2><table width=100% border=1><tr><td colspan=2 align=center>HM</td></tr>
						<tr><td>".$_SESSION['lang']['vhc_kmhm_awal']."</td><td>".$_SESSION['lang']['vhc_kmhm_akhir'] ."</td></tr>
						</table></td>
						<td bgcolor=#DEDEDE align=center valign=middle>HM</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['keterangan']."</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jumlahrit']."</td>
						<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>*/
						$stream.="<table border='1'>";
		$sql="select a.*,b.*,c.idkaryawan,c.upah,c.posisi from ".$dbname.".vhc_runht a left join ".$dbname.".vhc_rundt b on a.notransaksi=b.notransaksi left join ".$dbname.".vhc_runhk c on b.notransaksi=c.notransaksi ".$where."";
		$resx=mysql_query($sql);
		$no=0;
		$arrPos=array("Sopir","Kondektur");
		while($res=mysql_fetch_assoc($resx))
		{
			$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
			$qbrg=mysql_query($sbrg) or die(mysql_error());
			$rbrg=mysql_fetch_assoc($qbrg);
				
			$skry="select `namakaryawan` from ".$dbname.".datakaryawan where karyawanid='".$res['idkaryawan']."'";
			$qkry=mysql_query($skry) or die(mysql_error());
			$rkry=mysql_fetch_assoc($qkry);
			
			$sJns="select namakegiatan  from ".$dbname.".vhc_kegiatan where kodekegiatan='".$res['jenispekerjaan']."'";
			$qJns=mysql_query($sJns) or die(mysql_error());
			$rJns=mysql_fetch_assoc($qJns);	
			
			$no+=1;	
			$stream.="	<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$res['notransaksi']."</td>
				<td>".tanggalnormal($res['tanggal'])."</td>
				<td>".$res['jenisvhc']."</td>
				<td>".$res['kodevhc']."</td>
				<td>".$res['satuan']."</td>
				<td>".$res['kmhmawal']."</td>
				<td>".$res['kmhmakhir']."</td>
				<td>".$rbrg['namabarang']."</td>
				<td>".$res['jlhbbm']."</td>
				<td>".$rJns['namakegiatan']."</td>
				<td>".$res['alokasibiaya']."</td>
				<td>".$res['beratmuatan']."</td>
				<td>".$res['jumlahrit']."</td>
				<td>".$res['biaya']."</td>
				<td>".$rkry['namakaryawan']."</td>
				<td>".$arrPos[$res['posisi']]."</td>
				<td>".number_format($res['upah'],2)."</td>
				</tr>";
		}

	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="ReportVehicleUsage";
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