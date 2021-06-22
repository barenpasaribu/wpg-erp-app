<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
	$unit=$_POST['unit'];
if($unit=='')$unit=$_GET['unit'];
	$periode=$_POST['periode'];
if($periode=='')$periode=$_GET['periode'];
	$excel=$_POST['excel'];
if($excel=='')$excel=$_GET['excel'];

//echo "unit: ".$unit." periode: ".$periode." excel: ".$excel; exit;

if($unit==''){
	echo "Warning: silakan mengisi gudang"; exit;
}
/*
if($periode==''){
	echo "Warning: silakan mengisi periode"; exit;
}

*/
//namabarang
	$sData="select kodebarang, namabarang from ".$dbname.".log_5masterbarang";
	$qData=mysql_query($sData);// or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		$nabar[$rData['kodebarang']]=$rData['namabarang'];  
	}
//periode
	$sData="select tanggal from ".$dbname.".log_transaksi_vw order by tanggal";
	$qData=mysql_query($sData);// or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		$per=substr($rData['tanggal'],0,7);
		$perio[$per]=$per;  
	}

	$sPeriode = "
	SELECT
		*, SUBSTR(periode FROM 1 FOR 4) as periodetahun
	FROM
		`setup_periodeakuntansi`
	WHERE
		kodeorg = '".$unit."'
		AND SUBSTR(periode FROM 6 FOR 7) = 12
	GROUP BY
		SUBSTR(periode FROM 1 FOR 4)
	ORDER BY periode DESC LIMIT 1;
	";
	$qPeriode = mysql_query($sPeriode);
	while($rData=mysql_fetch_assoc($qPeriode)){
		$PTL = $rData['periode'];  
	}
	if($PTL == ''){$PTL = "2017-12";}
	
	$sData="select kodebarang, sum(saldoakhirqty) as saldo from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."' 
	and periode = '".$PTL."' group by kodebarang";
	$qData=mysql_query($sData);// or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		$resData[$rData['kodebarang']]['kobar']=$rData['kodebarang'];  
		$resData[$rData['kodebarang']][$PTL]=$rData['saldo'];  
		$resData[$rData['kodebarang']]['saldoakhir']=$rData['saldo'];  
	}
//saldo + transaksi per periode
	foreach($perio as $per)
	{
		$sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang = '".$unit."' 
		and tanggal like '".$per."-%' and notransaksi like '%GR%' and post = '1' group by kodebarang";
		$qData=mysql_query($sData);// or die(mysql_error());
		while($rData=mysql_fetch_assoc($qData))
		{
			$resData[$rData['kodebarang']]['kobar']=$rData['kodebarang'];
			$terima=$per."R";  
			$resData[$rData['kodebarang']][$terima]=$rData['saldo'];  
			$resData[$rData['kodebarang']]['saldoakhir']+=$rData['saldo'];
			$sama=$per."S";  
			$resData[$rData['kodebarang']][$sama]=$resData[$rData['kodebarang']]['saldoakhir'];  
		}
		$sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang = '".$unit."' 
		and tanggal like '".$per."-%' and notransaksi like '%GI%' and post = '1' group by kodebarang";
		$qData=mysql_query($sData);// or die(mysql_error());
		while($rData=mysql_fetch_assoc($qData))
		{
			$resData[$rData['kodebarang']]['kobar']=$rData['kodebarang'];  
			$kasih=$per."I";  
			$resData[$rData['kodebarang']][$kasih]=$rData['saldo'];  
			$resData[$rData['kodebarang']]['saldoakhir']-=$rData['saldo'];
			$sama=$per."S";  
			$resData[$rData['kodebarang']][$sama]=$resData[$rData['kodebarang']]['saldoakhir'];  
		}
		$sData="select kodebarang, sum(saldoakhirqty) as saldo from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."' 
		and periode = '".$per."' group by kodebarang";
		$qData=mysql_query($sData);// or die(mysql_error());
		while($rData=mysql_fetch_assoc($qData))
		{
			$resData[$rData['kodebarang']]['kobar']=$rData['kodebarang'];  
			$resData[$rData['kodebarang']][$per]=$rData['saldo'];  
			$sama=$per."S";  
			$resData[$rData['kodebarang']][$sama]=$resData[$rData['kodebarang']]['saldoakhir'];  
		}
	}
//saldo akhir	
	$sData="select kodebarang, saldoqty as saldo from ".$dbname.".log_5masterbarangdt where kodegudang = '".$unit."'";
	$qData=mysql_query($sData);// or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		$resData[$rData['kodebarang']]['datasaldoakhir']=$rData['saldo'];  
	}

/*
echo "<pre>";	
print_r($resData);
echo "</pre>";
exit;	

*/
//echo"<br>str :".$str; exit;
//=================================================
	 
	$no=0; $tab='';
		    $tab.="<table><thead><tr>
			  <td align=center>No.</td>";
			  $tab.="<td align=center>Kode Barang</td>";
			  $tab.="<td align=center>Nama Barang</td>";
			  $tab.="<td align=center>".$PTL."<br>Saldo</td>";
	foreach($perio as $per)
	{
			  $tab.="<td align=center>".$per."<br>Received</td>";
			  $tab.="<td align=center>".$per."<br>Issued</td>";
			  $tab.="<td align=center>".$per."<br>ShouldBe</td>";
			  $tab.="<td align=center>".$per."<br>Saldo</td>";
	}
			  $tab.="<td align=center>Saldo</td>";
			$tab.="</tr></thead><tbody>";  
	foreach($resData as $ar)
	{
		$no+=1;
		    $tab.="<tr class=rowcontent>
			  <td align=center>".$no."</td>";
			  $tab.="<td align=right>".$ar['kobar']."</td>";
			  $tab.="<td align=left>".$nabar[$ar['kobar']]."</td>";
			  $tab.="<td align=right>".number_format($ar[$PTL])."</td>";
	foreach($perio as $per)
	{
		$terima=$per."R";
		$kasih=$per."I";
		$sama=$per."S";
			  $tab.="<td bgcolor='AAFFAA' align=right>".number_format($ar[$terima])."</td>";
			  $tab.="<td bgcolor='FFAAAA' align=right>".number_format($ar[$kasih])."</td>";
			  $tab.="<td bgcolor='AAAAFF' align=right>".number_format($ar[$sama])."</td>";
			  $tab.="<td align=right>".number_format($ar[$per])."</td>";
	}
			  $tab.="<td bgcolor='9999FF' align=right>".number_format($ar['datasaldoakhir'])."</td>";
			$tab.="</tr>";  
	}
			$tab.="</tbody><tfoot></tfoot></table>";
if($excel!='excel'){
	echo $tab;
}else{
$nop_="RekalkulasiStock_".$unit;
if(strlen($tab)>0)
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
 if(!fwrite($handle,$tab))
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
}			

?>