<head>
    <title> Rendemen</title>
</head>
<body>
<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rendemen" . $_GET['notransaksi'] . ".xls");
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//	$pt=$_POST['pt'];
$para=$_GET['notransaksi'];
$sql = "select * from pmn_rendemenht where koderendemen='". $para. "'";
$str = mysql_query($sql);
while($resht=mysql_fetch_array($str, MYSQL_ASSOC)){
	$decreas=$resht['decreas'];
	$biayakandir=$resht['kandir'];
	$totalBiaya=$resht['totalbiaya'];
	$totalResult=$resht['totalhasil'];
	$totalPurch=$resht['totalpurchase'];
	$labaRugi=$resht['labarugi'];
	$tonaseht=$resht['tonase'];
	$pricekg=$resht['pricekg'];
	$koderendemen=$resht['koderendemen'];
	$tglrendemen=$resht['tglrendemen'];
	$margin=$resht['margin'];
}
$sqlDefaultFill = "SELECT * FROM pmn_rendemendt WHERE koderendemen='". $koderendemen ."'";
		//echo $sql;
$exe = mysql_query($sqlDefaultFill);
while($resdt=mysql_fetch_object($exe)){
	$dt[$resdt->kodebarang][$resdt->kodelist]= $resdt->amount;
}

$cpoPrice= $dt['40000001']['000'];
$cangkangPrice= $dt['40000004']['000'];
$pkPrice = $dt['40000002']['000'];
$costCPO=$dt['40000001']['001'];
$TransCPOdt=$dt['40000001']['002'];
$costpk=$dt['40000002']['001'];
$Transpkdt=$dt['40000002']['002'];
$costcangkang=$dt['40000004']['001'];
$Transcangkangdt=$dt['40000004']['002'];
$OERpabrikCPO=$dt['40000001']['003'];
$OERpabrikpk=$dt['40000002']['003'];
$OERpabrikcangkang=$dt['40000004']['003'];
$actualOer=$dt['40000001']['301'];
$actualPK=$dt['40000002']['301'];
$actualCk=$dt['40000004']['301'];
$ppncpo=$dt['40000001']['004'];
$ppnpk=$dt['40000002']['004'];
$ppnck=$dt['40000004']['004'];
$resultCPO=($cpoPrice / $ppncpo)-$costCPO-$TransCPOdt;
$resultCPO=round($resultCPO,2);
if($OERpabrikCPO == "" or $OERpabrikCPO== 0){
	$resCPO = $resultCPO;
}else{
	$resCPO= $resultCPO * ($OERpabrikCPO / 100);
}
$resCPO = round($resCPO,2);

$resultpk=($pkPrice / $ppnpk)-$costpk-$Transpkdt;
if($OERpabrikpk == ""){
	$respk = $resultpk;
}else{
	$respk=$resultpk * ($OERpabrikpk / 100);
}
$respk = round($respk,2);
$dppOer= $cpoPrice / $ppncpo;
$dppPK=$pkPrice / $ppnpk;
$dppCk=$cangkangPrice / $ppnck;
$resultcangkang=($cangkangPrice / $ppnck) -$costcangkang - $Transcangkangdt;
$resultcangkang=round($resultcangkang, 2);
if($OERpabrikcangkang == "" OR $OERpabrikcangkang == 0){
	$rescangkang=$resultcangkang;
}else{
	$rescangkang=$resultcangkang * ($OERpabrikcangkang / 100);
}
$rescangkang = round($rescangkang,2);
$orgvalue=substr($_SESSION['empl']['namalokasitugas'], 0 ,3);
$sql="select * from pmn_tbl_rendemen_vw2 
	where tgltimbang ='". substr($para,0,10) ."' and 
	SUBSTRING(klsupplier, CHAR_LENGTH(klsupplier)-2, 3) = '". substr($orgvalue,0,3) ."'";
$res=mysql_query($sql);
echo '<table id="tableOer"  class=sortable cellspacing=1 border=0 width=600>
	     <thead><tr><td align=center>Supplier </td><td align=center>Price A /kg</td><td align=center>Fee /kg</td><td align=center>Harga /Kg</td><td align=center>Tonase</td><td align=center>Total</td><td align=center>OER</td></tr>  
		 </thead>';
while($bar=mysql_fetch_object($res)){
	$kontrakOER = ($bar->harga_akhir - $respk)/ $resCPO * 100;
	$kontrakOER = round($kontrakOER, 2);
	if($num % 2 == 0){ 
		$color = "#84B4DF";
	} else{ 
		$color = "#FFFFFF";
	}
	echo "<tr style='background:". $color .";'><td>". $bar->namasupplier ."</td><td align='right'>". number_format($bar->harga_harian, 2) ."</td><td align='right'>". number_format($bar->fee, 2) ."</td>
	<td  align='right'>". number_format($bar->harga_akhir+$bar->fee, 2) ."</td><td align='right'>".number_format($bar->totalkg) ."</td><td align='right'>". number_format($bar->tot_tgh, 2) ."</td><td id=oerkontrak[".$num."] >". $kontrakOER ." % </td></tr> ";
	$num++;
	$tonase = $tonase + $bar->totalkg;
	$kontraktot = $kontraktot + $bar->tot_tgh;
}
$pricekg=$kontraktot/$tonase;
$pricekg=round($pricekg,2);
$totalOer=($pricekg-$respk)/$resCPO * 100;
$totalOer=round($totalOer,2);
$marginper=$actualOer-$totalOer;
$amountOer=round($actualOer*$tonase/100, 2);
$amountPK=round($actualPK*$tonase/100,2);
$amountCk=round($actualCk*$tonase/100, 2);
$jualOer=round($amountOer, 2)*round($dppOer, 2);
$jualPK=round($amountPK,2)*round($dppPK,2);
$jualCk=round($amountCk,2)*round($dppCk,2);
$totalHasil=$jualOer+$jualPK+$jualCk;
$totalPurchase=$tonase * $pricekg;
$biayaCPO=$dt['40000001']['201'];
$biayaPK=$dt['40000002']['201'];
$biayaCK=$dt['40000004']['201'];
$cpoprodcost =$biayaCPO*$amountOer;
$pkprodcost =$biayaPK*$amountPK;
$ckprodcost =$biayaCK*$amountCk;

$transCPO=$dt['40000001']['202'];
$transPK=$dt['40000002']['202'];
$transCK=$dt['40000004']['202'];
$totcostCpo=$transCPO*$amountOer;
$totcostPK=$transPK*$amountPK;
$totcostCk=$transCK*$amountCk;
$decreasCost= $tonase*$decreas;
$kandircost=$tonase*$biayakandir;
$totalHasil= $totcostCpo+$totcostPK+$totcostCk+$decreasCost+$kandircost+$cpoprodcost+$pkprodcost+$ckprodcost;
?>
<tfoot id="confoot" style="background:#01A9DB;" >
	<tr><td align=center> Total </td><td></td><td></td>
	<td align=right><?php echo number_format($pricekg, 2); ?></td>
	<td align=right><?php echo number_format($tonase, 2); ?></td>
	<td align=right><?php echo number_format($kontraktot, 2); ?></td>
	<td align=right id="oerTotal"> <?php echo number_format($totalOer,2); ?> %</td></tr> 
</tfoot>				
</tbody>
</table>
<table>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr>
	   <td style="height:24px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
<tr><td align=center colspan="14">HARGA TENDER TANGGAL <?php echo $para; ?></td></tr>  
	 <tr>
	   <td>CPO</td>
	   <td><?php echo number_format($cpoPrice,2); ?></td>
	   <td>:</td>
	   <td><?php echo $ppncpo; ?></td>
	   <td>-</td>
	   <td><?php echo number_format($dt['40000001']['001'],2); ?></td>
	   <td>-</td>
	   <td><?php echo number_format($dt['40000001']['002'],2); ?></td>
	   <td>=</td>
	   <td><?php echo number_format($resultCPO),2; ?></td>
	   <td></td>
	   <td></td>
	   <td>=</td>
	   <td><?php echo number_format($resCPO,2); ?></td>
	 </tr>
	 <tr>
	   <td>PK</td>
	   <td><?php echo number_format($pkPrice,2); ?></td>
	   <td>:</td>
	   <td><?php echo $ppnpk; ?></td>
	   <td>-</td>
	   <td><?php echo $dt['40000002']['001']; ?></td>
	   <td>-</td>
	   <td><?php echo $dt['40000002']['002']; ?></td>
	   <td>=</td>
	   <td><?php echo number_format($resultpk,2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($dt['40000002']['003'],2)." %"; ?></td>
	   <td>=</td>
	   <td><?php echo number_format($respk,2); ?></td>
	 </tr>
	 <tr>
	   <td>Cangkang</td>
	   <td><?php echo number_format($cangkangPrice,2); ?></td>
	   <td>:</td>
	   <td><?php echo $ppnck; ?></td>
	   <td>-</td>
	   <td><?php echo $dt['40000004']['001']; ?></td>
	   <td>-</td>
	   <td><?php echo $dt['40000004']['002']; ?></td>
	   <td>=</td>
	   <td><?php echo number_format($resultcangkang,2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($dt['40000004']['003'],2)." %"; ?></td>
	   <td>=</td>
	   <td><?php echo number_format($rescangkang,2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="3">MARGIN</td><td></td>
	   <td>=</td>
	   <td><?php echo $margin. ' %'; ?></td>
	   <td></td>	   <td></td>	   <td></td>	   <td></td>	   <td></td>   <td></td>   <td></td>
	 </tr>
	 <tr>
	   <td colspan="4" >Pembelian OER</td>
	   <td>=</td>
	   <td><?php echo $totalOer. ' %'; ?></td>
	   <td></td>
	   <td><?php echo number_format($tonase, 0); ?></td>
	   <td> </td>
	   <td><?php echo number_format($pricekg, 2); ?></td>
	   <td></td>
	   <td></td>
	   <td>=</td><td><?php echo number_format($kontraktot, 2); ?></td>
	 </tr>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr>
	   <td style="background: #01A9DB;" colspan="14"><b>HASIL</b></td>
	 </tr>
	 <tr>
	   <td colspan="4"><b>OER ACTUAL</b></td>
	   <td>=</td>
	   <td><?php echo $dt['40000001']['301']." %"; ?></div></td>
	   <td>x</td>
	   <td><?php echo number_format($tonase, 0); ?></td>
	   <td>=</td>
	   <td><?php echo number_format($amountOer, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($dppOer, 2); ?></td>
	   <td>=</td>
	   <td><?php echo number_format($jualOer, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="4"><b>PK ACTUAL</b></td>
	   <td>=</td>
	   <td><?php echo $dt['40000002']['301'].' %'; ?></div></td>
	   <td>x</td>
	   <td><?php echo number_format($tonase, 0); ?></td>
	   <td>=</td>
	   <td><?php echo number_format($amountPK, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($dpppk, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($jualPK, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="4"><b>Cangkang Estimasi</b></td>
	   <td>=</td>
	   <td><?php echo $dt['40000002']['301'].' %'; ?>"</div></td>
	   <td>x</td>
	   <td><?php echo number_format($tonase, 0); ?></td>
	   <td>=</td>
	   <td><?php echo number_format($amountCk, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($dppCk, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($jualCk, 2); ?></td>
	 </tr>
	 <tr style="background: #01A9DB;" >
	   <td colspan="12"><b> TOTAL HASIL</b></td>
	   <td>=</td>
	   <td><?php echo number_format($totalResult,2); ?></td>
	 </tr>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr>
	   <td style="background: #01A9DB;" colspan="14"><b>BIAYA</b></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> PEMBELIAN TBS</b></td>
	   <td><?php echo number_format($tonase, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($pricekg, 2); ?></td>
	   <td>=</td>
	   <td><?php echo number_format($totalPurch, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA OLAH CPO</b></td>
	   <td><?php echo number_format($amountOer,2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($biayaCPO, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($cpoprodcost, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b>BIAYA OLAH PK</b></td>
	   <td><?php echo number_format($amountPK, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($biayaPK, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($pkprodcost, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b>BIAYA KANDIR</b></td>
	   <td><?php echo number_format($tonase, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($biayakandir, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($kandircost, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b>BIAYA PENGANGKUTAN CPO</b></td>
	   <td><?php echo number_format($amountOer, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($transCPO, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($totcostCpo, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b>BIAYA PENGANGKUTAN PK</b></td>
	   <td><?php echo number_format($amountPK, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($transPK, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($totcostPK, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b>BIAYA PENYUSUTAN</b></td>
	   <td><?php echo number_format($tonase, 2); ?></td>
	   <td>x</td>
	   <td><?php echo number_format($decreas, 2); ?></div></td>
	   <td>=</td>
	   <td><?php echo number_format($decreasCost, 2); ?></td>
	 </tr>
	 <tr>
	   <td colspan="12"><b>TOTAL BIAYA</b></td>
	   <td>=</td>
	   <td><?php echo number_format($totalBiaya, 2); ?></td>
	 </tr>
	 <tr>
	   <td style="height:12px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr style="background: #01A9DB;" >
	   <td colspan="13"><b>LABA (RUGI)</b></td>
	   <td><?php echo number_format($labaRugi, 2); ?></td>
	 </tr>
	 <tr>
</table>
<?

	
/* 		$nop_="Detail_Rendemen_".$para;
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
        } */