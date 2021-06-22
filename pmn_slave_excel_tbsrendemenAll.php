<head>
    <title> Rendemen</title>
</head>
<body>
<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rendemenAll" . $_GET['notransaksi'] . ".xls");
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//	$pt=$_POST['pt'];
$para=$_GET['notransaksi'];
	$sqlDefaultFill = "SELECT * FROM pmn_rendemendtall WHERE koderendemen='".$para."'";
	//echo $sqlDefaultFill;
	$exe = mysql_query($sqlDefaultFill);
	while($resdt=mysql_fetch_object($exe)){
		$dt[$resdt->kodebarang][$resdt->kodelist]= $resdt->amount;
	}
	$CPOsoldPrice= $dt['40000001']['000'];
	$PKsoldPrice = $dt['40000002']['000'];
	$CksoldPrice= $dt['40000004']['000'];
	$cpoPrice=$dt['40000001']['000'];
	$PKPrice = $dt['40000002']['000'];
	$CkPrice= $dt['40000004']['000'];	
	$costCPO=$dt['40000001']['001'];
	$costPK=$dt['40000002']['001'];
	$costCk=$dt['40000004']['001'];		
	$realCPO=$dt['40000001']['201'];
	$realPK=$dt['40000002']['201'];
	$realCk=$dt['40000004']['201'];	
	$transCPO=$dt['40000001']['002'];
	$transPK=$dt['40000002']['002'];
	$transCk=$dt['40000004']['002'];	
	$oerCPO=$dt['40000001']['003'];
	$oerPK=$dt['40000002']['003'];
	$oerCk=$dt['40000004']['003'];	
	$OERpabrikCPO=$dt['40000001']['003'];
	$OERpabrikpk=$dt['40000002']['003'];
	$OERpabrikcangkang=$dt['40000004']['003'];
	$ppncpo=$dt['40000001']['004'];
	$ppnpk=$dt['40000002']['004'];
	$ppnck=$dt['40000004']['004'];
echo "<table border=2>
<tr style='background: gray;'><td align=left> PRODUK </td>
<td align=center> HARGA TENDER (RP) </td>
<td align=center> HARGA JUAL (RP) </td>
<td align=center> BIAYA OLAH (RP) </td>
<td align=center> REAL BY OLAH (RP) </td>
<td align=center> ONGKOS ANGKUT (RP) </td>
<td align=center> RENDEMEN (%) </td>
<td align=left> REMARK </td></tr>";
echo "
<tr><td>CPO</td>
<td align=center><div data-tip='Harga Kontrak CPO'>".$cpoPrice."</div></td>
<td align=center><div data-tip='Harga Jual CPO'>".$CPOsoldPrice."</div></td>
<td align=center><div data-tip='Biaya olah CPO'>". $costCPO."</div></td>
<td align=center><div data-tip='Biaya olah Real CPO'>". $realCPO."</div></td>
<td align=center><div data-tip='Biaya Angkut CPO'>". $transCPO."</div></td>
<td align=center><div data-tip='Rendemen CPO'>". $oerCPO."</div></td>
<td> Harga CPO include PPN</td></tr>";
echo "
<tr><td>PK</td>
<td align=center><div data-tip='Harga Kontrak PK'>".$PKPrice."</div></td>
<td align=center><div data-tip='Harga Jual PK'>".$PKsoldPrice."</div></td>
<td align=center><div data-tip='Biaya olah PK'>". $costPK."</div></td>
<td align=center><div data-tip='Biaya olah Real PK'>". $realPK."</div></td>
<td align=center><div data-tip='Biaya Angkut PK'>". $transPK."</div></td>
<td align=center><div data-tip='Rendemen PK'>". $oerPK."</div></td>
<td> Harga PK exclude PPN</td></tr>";
echo "
<tr><td>Cangkang</td>
<td align=center><div data-tip='Harga Kontrak Cangkang'>".$CkPrice."</div></td>
<td align=center><div data-tip='Harga Jual Cangkang'>".$CksoldPrice."</div></td>
<td align=center><div data-tip='Biaya olah Cangkang'>". $costCk."</div></td>
<td align=center><div data-tip='Biaya olah Real Cangkang'>". $realCk."</div></td>
<td align=center><div data-tip='Biaya Angkut Cangkang'>". $transCk."</div></td>
<td align=center><div data-tip='Rendemen Cangkang'>". $oerCk."</div></td>
<td> Harga Cangkang exclude PPN</td></tr>";
echo "
<tr><td colspan=8><div style='display: block; height: 25px'></div></td></tr></table>";

echo "<table id=result style='display: none' border=2>
<tr style='background: gray;'>
<td align=center style='width: 180px;'>Keterangan</td>
<td align=center style='width: 120px;'>Harga Jual</td>
<td align=center style='width: 30px;'>:</td>
<td align=center style='width: 120px;'>PPN</td>
<td align=center style='width: 30px;'>-</td>
<td align=center style='width: 120px;'>BIAYA OLAH</td>
<td align=center style='width: 30px;'>-</td>
<td align=center style='width: 120px;'>ONGKOS ANGKUT</td>
<td align=center style='width: 30px;'>x</td>
<td align=center style='width: 120px;'>OER</td>
<td align=center style='width: 30px;'>=</td>
<td align=center style='width: 120px;'>HARGA TBS</td>
<td align=center style='width: 200px;'>REMARK</td>
</tr>";
$hargatbsCPO[1]=($CPOsoldPrice/1.1 - $costCPO - $transCPO) * $oerCPO / 100;
$hargatbsCPO[2]=($CPOsoldPrice/1 - $costCPO - $transCPO) * $oerCPO / 100;
$hargatbsCPO[3]=($CPOsoldPrice/1.1- $realCPO) * $oerCPO / 100;
$hargatbsPK[1]=($PKsoldPrice/1.1 - $costPK - $transPK) * $oerPK / 100;
$hargatbsPK[2]=($PKsoldPrice/1 - $costPK - $transPK) * $oerPK / 100;
$hargatbsPK[3]=($PKsoldPrice/1- $realPK) * $oerPK / 100;
$hargatbsCk[1]=($CksoldPrice/1.1 - $costCk - $transCk) *$oerCk / 100;
$hargatbsCk[2]=($CksoldPrice/1 - $costCk - $transCk) *$oerCk / 100;
$hargatbsCk[3]=($CksoldPrice/1- $realCk) *$oerCk / 100;
		$ppn=1.1;
		$rmk="Excl. PPN";
		$item = 'CPO';
		$price =$cpoPrice;
		$cost =$costCPO;
		$trans =$transCPO;
		$totTbs = $hargatbsCPO[1];
		$oer = $oerCPO;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
		$item = 'PK';
		$price=$PKPrice;
		$cost=$costPK;
		$trans=$transPK;
		$ppn = 1;
		$totTbs = $hargatbsPK[1];
		$oer = $oerPK;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
		$item = 'CANGKANG';
		$price=$CkPrice;
		$cost=$costCk;
		$trans=$transCk;
		$ppn = 1;
		$totTbs = $hargatbsCk[1];
		$oer = $oerCk;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
echo "
<tr><td colspan=13><div style='display: block; height: 25px'></div></td></tr>";
		$ppn=1;
		$rmk="Incl. PPN";
		$item = 'CPO';
		$price =$cpoPrice;
		$cost =$costCPO;
		$trans =$transCPO;
		$totTbs = $hargatbsCPO[2];
		$oer = $oerCPO;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
		$item = 'PK';
		$price=$PKPrice;
		$cost=$costPK;
		$trans=$transPK;
		$ppn = 1;
		$totTbs = $hargatbsPK[2];
		$oer = $oerPK;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
		$item = 'CANGKANG';
		$price=$CkPrice;
		$cost=$costCk;
		$trans=$transCk;
		$ppn = 1;
		$totTbs = $hargatbsCk[2];
		$oer = $oerCk;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
echo "
<tr><td colspan=13><div style='display: block; height: 25px'></div></td></tr>";
		$ppn=1.1;
		$rmk ="Harga TBS Per KG ( Real by.olah )";
		$item = 'CPO';
		$price =$cpoPrice;
		$trans =0;$cost=$realCPO;
		$totTbs = $hargatbsCPO[3];
		$oer = $oerCPO;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
		$item = 'PK';
		$price=$PKPrice;
		$cost=$realCk;
		$trans=0;
		$ppn = 1;
		$totTbs = $hargatbsPK[3];
		$oer = $oerPK;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
		$item = 'CANGKANG';
		$price=$CkPrice;
		$cost=$realCk;
		$trans=0;
		$ppn = 1;
		$totTbs = $hargatbsCk[3];
		$oer = $oerCk;
		echo "
		<tr><td align=center>HARGA ".$item."</td>
		<td align=center>".number_format($price,2)."</td>
		<td align=center>:</td>
		<td align=center>".$ppn."</td>
		<td align=center>-</td>
		<td align=center>".number_format($cost,2)."</td>
		<td align=center>-</td>
		<td align=center>".number_format($trans,2)."</td>
		<td align=center>x</td>
		<td align=center>".$oer." %</td>
		<td align=center>=</td>
		<td align=center>".number_format($totTbs,2)."</td>
		<td align=center>".$rmk."</td>
		</tr> ";
echo "
<tr><td colspan=13><div style='display: block; height: 25px'></div></td></tr>";
echo "</table>";
echo "
<table id=result2 border=1>
<thead><tr><td align=center style='width: 90px;' rowspan=2>Rendemen</td>
<td align=center style='width: 120px;' rowspan=2>Harga TBS</br> (excl.PK & Cangkang) </td>
<td align=center style='width: 120px;' rowspan=2>Harga PK</td>
<td align=center style='width: 120px;' rowspan=2>Harga Cangkang</td>
<td align=center colspan=3>Harga TBS Per Kilogram</td></tr>
<tr><td align=center style='width: 120px;'>Excl. PPN</td>
<td align=center style='width: 120px;'>Incl. PPN</td>
<td align=center style='width: 120px;'>Real By.Olah	</td>
</tr><thead><tbody id=rendemenresult>";
$baseoer = $oerCPO;
$tpricecpo = $hargatbsCPO[1];
$ppnpricecpo =$hargatbsCPO[2];
$costpricecpo = $hargatbsCPO[3];
$tpricepk = $hargatbsPK[1];
$tpricecangkang = $hargatbsCk[1];
$tpricepk2 = $hargatbsPK[3];
$tpricecangkang2 = $hargatbsCk[3];
$npricecpo;
$sqlDefaultFill = "SELECT * FROM pmn_rendemendiff WHERE koderendemen='".$para."' ORDER BY persen ASC";
//echo $sqlDefaultFill;
$exe = mysql_query($sqlDefaultFill);
while($resdt=mysql_fetch_assoc($exe)){
$baseoer=$resdt['persen'];
$npricecpo = $tpricecpo * $baseoer /100;
$exclppn = $npricecpo + $tpricepk + $tpricecangkang;
$inclppn = ($ppnpricecpo * $baseoer /100) + $tpricepk + $tpricecangkang;
$realbyolah = ($costpricecpo * $baseoer / 100) + $tpricepk2 + $tpricecangkang2;
echo '<tr><td style="background: gray">'.$baseoer.'%</td><td>'.number_format($npricecpo,2).'</td><td>'.$tpricepk.'</td>
<td>'.$tpricecangkang .'</td><td>'.$exclppn.'</td><td>'.$inclppn .'</td><td>'.$realbyolah .'</td></tr>
</tr>';
}
if($oerCPO>=100){
$baseoer = 16 + 2.5;
}else{
$baseoer = $oerCPO + 2.5;
}
//$baseoer = $oerCPO + 2.5;
for($r=1; $r <= 50; $r++){
$npricecpo = $tpricecpo * $baseoer /100;
$exclppn = $npricecpo + $tpricepk + $tpricecangkang;
$inclppn = ($ppnpricecpo * $baseoer /100) + $tpricepk + $tpricecangkang;
$realbyolah = ($costpricecpo * $baseoer / 100) + $tpricepk2 + $tpricecangkang2;
if($baseoer == $oerCPO){
echo '
<tr><td style="background: yellow">'.$baseoer.'%</td><td>'.number_format($npricecpo,2).'</td><td>'.number_format($tpricepk,2).'</td>
<td>'.number_format($tpricecangkang, 2) .'</td><td>'.number_format($exclppn, 2).'</td><td>'.number_format($inclppn, 2) .'</td><td>'.number_format($realbyolah,2) .'</td></tr>
</tr></tbody></table></td></tr>';
}else{
echo '
<tr><td>'.$baseoer.'%</td><td>'.number_format($npricecpo,2).'</td><td>'.number_format($tpricepk, 2).'</td>
<td>'.number_format($tpricecangkang,2) .'</td><td>'.number_format($exclppn,2).'</td><td>'.number_format($inclppn,2).'</td><td>'.number_format($realbyolah,2) .'</td></tr>';
}
$baseoer = $baseoer - 0.1;
}


echo'</tbody></table></td></tr>';	
/* 	for($i=1; $i <= 3; $i++){
		document.getElementById('priceCPO['+i+']').innerHTML = CPOsoldPrice;
		document.getElementById('pricePK['+i+']').innerHTML = PKsoldPrice;
		document.getElementById('priceCANGKANG['+i+']').innerHTML = CksoldPrice;
		document.getElementById('oerCPO['+i+']').innerHTML = oerCPO;
		document.getElementById('oerPK['+i+']').innerHTML = oerPK;
		document.getElementById('oerCANGKANG['+i+']').innerHTML = oerCk;
		switch($i){
			case 3:
				document.getElementById('olahCPO['+i+']').innerHTML = realCPO;
				document.getElementById('olahPK['+i+']').innerHTML = realPK;
				document.getElementById('olahCANGKANG['+i+']').innerHTML = realCk;
				document.getElementById('angkutCPO['+i+']').innerHTML = '-';
				document.getElementById('angkutPK['+i+']').innerHTML = '-';
				document.getElementById('angkutCANGKANG['+i+']').innerHTML = '-';
				cpo = document.getElementById('priceCPO['+i+']').innerHTML / document.getElementById('ppnCPO['+i+']').innerHTML;
				cpo = cpo - document.getElementById('olahCPO['+i+']').innerHTML;
				cpo = cpo * document.getElementById('oerCPO['+i+']').innerHTML / 100;
				pk = document.getElementById('pricePK['+i+']').innerHTML / document.getElementById('ppnPK['+i+']').innerHTML;
				pk = pk - document.getElementById('olahPK['+i+']').innerHTML;
				pk = pk * document.getElementById('oerPK['+i+']').innerHTML /100;
				ck = document.getElementById('priceCANGKANG['+i+']').innerHTML / document.getElementById('ppnCANGKANG['+i+']').innerHTML;
				ck = ck - document.getElementById('olahCANGKANG['+i+']').innerHTML;
				ck = ck * document.getElementById('oerCANGKANG['+i+']').innerHTML / 100;
			break;

		  default:
				document.getElementById('olahCPO['+i+']').innerHTML = costCPO;
				document.getElementById('olahPK['+i+']').innerHTML = costPK;
				document.getElementById('olahCANGKANG['+i+']').innerHTML = costCk;
				document.getElementById('angkutCPO['+i+']').innerHTML = transCPO;
				document.getElementById('angkutPK['+i+']').innerHTML = transPK;
				document.getElementById('angkutCANGKANG['+i+']').innerHTML = transCk;
				cpo = document.getElementById('priceCPO['+i+']').innerHTML / document.getElementById('ppnCPO['+i+']').innerHTML;
				cpo = cpo - document.getElementById('olahCPO['+i+']').innerHTML;
				cpo = cpo - document.getElementById('angkutCPO['+i+']').innerHTML;
				cpo = cpo * document.getElementById('oerCPO['+i+']').innerHTML / 100;
				pk = document.getElementById('pricePK['+i+']').innerHTML / document.getElementById('ppnPK['+i+']').innerHTML;
				pk = pk - document.getElementById('olahPK['+i+']').innerHTML;
				pk = pk - document.getElementById('angkutPK['+i+']').innerHTML;
				pk = pk * document.getElementById('oerPK['+i+']').innerHTML /100;
				ck = document.getElementById('priceCANGKANG['+i+']').innerHTML / document.getElementById('ppnCANGKANG['+i+']').innerHTML;
				ck = ck - document.getElementById('olahCANGKANG['+i+']').innerHTML;
				ck = ck - document.getElementById('angkutCANGKANG['+i+']').innerHTML;
				ck = ck * document.getElementById('oerCANGKANG['+i+']').innerHTML /100;
		  break;
		}
		document.getElementById('hargatbsCPO['+i+']').innerHTML = cpo.toFixed(2);
		document.getElementById('hargatbsPK['+i+']').innerHTML = pk.toFixed(2);
		document.getElementById('hargatbsCANGKANG['+i+']').innerHTML = ck.toFixed(2);
	}
 */
