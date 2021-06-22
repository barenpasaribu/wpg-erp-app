<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/fpdf.php';
$nodok = $_GET['notransaksi'];
class PDF extends FPDF
{
	public function Header()
	{
		global $conn;
		global $dbname;
		global $nodok;
		global $userid;
		global $posted;
		global $tanggal;
		global $dibuat;
		global $kodegudang;
		global $untukpt;
		global $untukunit;
		global $catatan;
		global $mengetahui;
		$paratgl = substr($nodok, 0, 10);
		$orgvalue = substr($nodok, 10, 13);

		$pt = '';
		$namapt = '';
		$alamatpt = '';
		$telp = '';
		$kodegudang = '';
		$status = 0;
		$str = 'select * from ' . $dbname . '.log_mrisht where notransaksi=\'' . $_GET['notransaksi'] . '\'';
		$res = mysql_query($str);
		if ($bar = mysql_fetch_object($res)) {
			$kodept = $bar->kodept;
			$kodegudang = $bar->kodegudang;
			$userid = $bar->user;
			$posted = $bar->postedby;
			$status = $bar->post;
			$tanggal = $bar->tanggal;
			$dibuat = $bar->dibuat;
			$mengetahui = $bar->mengetahui;
			$untukpt = $bar->untukpt;
			$untukunit = $bar->untukunit;
			$catatan = $bar->keterangan;
			if ($status == 0) {
				$status = 'Not Confirm';
			}
			else {
				$status = 'Confirmed';
			}
			$str1 = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kodept . '\'';
			$res1 = mysql_query($str1);
			$res2=mysql_fetch_object($res1);
			$namapt = $res2->namaorganisasi;
			$alamatpt = $res2->alamat . ', ' . $res2->wilayahkota;
			$telp = $res2->telepon;
		}
		if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
			$path = 'images/SSP_logo.jpg';
		}
		if ($_SESSION['org']['kodeorganisasi'] == 'SPS') {
			$path = 'images/SSP_logo.jpg';
		}
		else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
			$path = 'images/MI_logo.jpg';
		}
		else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
			$path = 'images/HS_logo.jpg';
		}
		else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
			$path = 'images/BM_logo.jpg';
		}
		$path = 'images/SSP_logo.jpg';
		$this->Image($path, 15, 5, 20);
		$this->SetFont('Arial', 'B', 8);
		$this->SetFillColor(255, 255, 255);
		$this->SetY(8);
		$this->SetX(40);
/* 		$this->Cell(60, 5, $namapt, 0, 1, 'L');
		$this->SetX(40);
		$this->Cell(60, 5, $alamat, 0, 1, 'L');
		$this->SetX(40);
		$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L'); */
		$this->SetFont('Arial', '', 6);
		$this->SetY(28);
		$this->SetX(163);
		$this->Cell(30, 4, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');
		$this->Line(10, 27, 200, 27);
		$this->Ln();
	}


	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}



}

function countLine($maxline, $numrow){
	if($maxline != $numrow){
		$hRow = $maxline-$numrow; //12
		$row = $hRow * 4; //4
		$dif = $row / $numrow; //2
		$h = $dif + 4;
	}else{
		$h = 4;
	}
	return $h;
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';

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
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
$pdf->SetY(35);
$pdf->Cell(190, 5, strtoupper('PERHITUNGAN RENDEMENT'), 0, 1, 'C');
//$pdf->Cell(190, 5, strtoupper('TANGGAL '.$tgl), 0, 1, 'C');
$pdf->SetFont('Arial', '', 6);
$hari = hari($tanggal, $_SESSION['language']);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(20, 4, 'No. Rendement', 0, 0, 'L');
$pdf->Cell(100, 4, ': ' . $nodok, 0, 0, 'L');
$pdf->Ln();
/* <tr style='background: gray;'><td align=left> PRODUK </td>
<td align=center> HARGA TENDER (RP) </td>
<td align=center> HARGA JUAL (RP) </td>
<td align=center> BIAYA OLAH (RP) </td>
<td align=center> REAL BY OLAH (RP) </td>
<td align=center> ONGKOS ANGKUT (RP) </td>
<td align=center> RENDEMEN (%) </td>
<td align=left> REMARK </td></tr>"; */
$pdf->SetFont('Arial', 'B', 5);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(32, 5, 'PRODUK', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'HARGA TENDER (RP)', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'HARGA JUAL (RP)', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'BIAYA OLAH (RP)', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'REAL BY OLAH (RP)', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'ONGKOS ANGKUT (RP)', 1, 0, 'C', 1);
$pdf->Cell(18, 5, 'RENDEMENT (%)', 1, 0, 'C', 1);
$pdf->Cell(30, 5, 'REMARK', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(32, 5, 'CPO', 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($cpoPrice, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($CPOsoldPrice, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($costCPO, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($realCPO, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($transCPO, 2), 1, 0, 'C', 1);
$pdf->Cell(18, 5, number_format($oerCPO, 2).' %', 1, 0, 'C', 1);
$pdf->Cell(30, 5, ' Harga CPO include PPN', 1, 1, 'C', 1);
$pdf->Cell(32, 5, 'PK', 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($PKPrice, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($PKsoldPrice, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($costPK, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($realPK, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($transPK, 2), 1, 0, 'C', 1);
$pdf->Cell(18, 5, number_format($oerPK, 2).' %', 1, 0, 'C', 1);
$pdf->Cell(30, 5, 'Harga PK exclude PPN', 1, 1, 'C', 1);
$pdf->Cell(32, 5, 'Cangkang', 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($CkPrice, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($CksoldPrice, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($costCk, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($realCk, 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, number_format($transCk, 2), 1, 0, 'C', 1);
$pdf->Cell(18, 5, number_format($oerCk, 2).' %', 1, 0, 'C', 1);
$pdf->Cell(30, 5, 'Harga Cangkang exc.PPN', 1, 1, 'C', 1);
$width = 170;
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 5);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(28, 5, 'Keterangan', 1, 0, 'C', 1);
$pdf->Cell(19, 5, 'Harga Jual', 1, 0, 'C', 1);
$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
$pdf->Cell(19, 5, 'PPN', 1, 0, 'C', 1);
$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
$pdf->Cell(19, 5, 'BIAYA OLAH', 1, 0, 'C', 1);
$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
$pdf->Cell(19, 5, 'ONGKOS ANGKUT', 1, 0, 'C', 1);
$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
$pdf->Cell(19, 5, 'OER', 1, 0, 'C', 1);
$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
$pdf->Cell(24, 5, 'HARGA TBS', 1, 0, 'C', 1);
$pdf->Cell(28, 5, 'REMARK', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 6);

$hargatbsCPO[1]=($CPOsoldPrice/1.1 - $costCPO - $transCPO) * $oerCPO / 100;
$hargatbsCPO[2]=($CPOsoldPrice/1 - $costCPO - $transCPO) * $oerCPO / 100;
$hargatbsCPO[3]=($CPOsoldPrice/1.1 - $realCPO) * $oerCPO / 100;
$hargatbsPK[1]=($PKsoldPrice/1 - $costPK - $transPK) * $oerPK / 100;
$hargatbsPK[2]=($PKsoldPrice/1 - $costPK - $transPK) * $oerPK / 100;
$hargatbsPK[3]=($PKsoldPrice/1- $realPK) * $oerPK / 100;
$hargatbsCk[1]=($CksoldPrice/1 - $costCk - $transCk) *$oerCk / 100;
$hargatbsCk[2]=($CksoldPrice/1 - $costCk - $transCk) *$oerCk / 100;
$hargatbsCk[3]=($CksoldPrice/1- $realCk) *$oerCk / 100;
//1
		$ppn=1.1;
		$rmk="Excl. PPN";
		$item = 'CPO';
		$price =$cpoPrice;
		$cost =$costCPO;
		$trans =$transCPO;
		$ppn = 1.1;
		$totTbs = $hargatbsCPO[1];
		$oer = $oerCPO;
		$pdf->Cell(28, 5, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 5, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->Cell(28, 5, $rmk, 1, 1, 'C', 1);
		$item = 'PK';
		$price=$PKPrice;
		$cost=$costPK;
		$trans=$transPK;
		$ppn = 1;
		$totTbs = $hargatbsPK[1];
		$oer = $oerPK;
		$pdf->Cell(28, 5, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 5, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->Cell(28, 5, $rmk, 1, 1, 'C', 1);
		$item = 'CANGKANG';
		$price=$CkPrice;
		$cost=$costCk;
		$trans=$transCk;
		$ppn = 1;
		$totTbs = $hargatbsCk[1];
		$oer = $oerCk;
		$pdf->Cell(28, 5, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 5, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->Cell(28, 5, $rmk, 1, 1, 'C', 1);
		$pdf->Ln();
//2
		$ppn=1;
		$rmk="Incl. PPN";
		$item = 'CPO';
		$price =$cpoPrice;
		$cost =$costCPO;
		$trans =$transCPO;
		$totTbs = $hargatbsCPO[2];
		$oer = $oerCPO;
		$pdf->Cell(28, 5, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 5, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->Cell(28, 5, $rmk, 1, 1, 'C', 1);
		$item = 'PK';
		$price=$PKPrice;
		$cost=$costPK;
		$trans=$transPK;
		$ppn = 1;
		$totTbs = $hargatbsPK[2];
		$oer = $oerPK;
		$pdf->Cell(28, 5, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 5, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->Cell(28, 5, $rmk, 1, 1, 'C', 1);
		$item = 'CANGKANG';
		$price=$CkPrice;
		$cost=$costCk;
		$trans=$transCk;
		$ppn = 1;
		$totTbs = $hargatbsCk[2];
		$oer = $oerCk;
		$pdf->Cell(28, 5, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 5, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 5, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 5, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 5, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 5, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->Cell(28, 5, $rmk, 1, 1, 'C', 1);
		$pdf->Ln();
//3		
		$ppn=1.1;
		$rmk ="Harga TBS Per KG ( Real by.olah )";
		$item = 'CPO';
		$price =$cpoPrice;
		$trans =0;
		$cost=$realCPO;
		$totTbs = $hargatbsCPO[3];
		$oer = $oerCPO;
		$pdf->Cell(28, 10, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 10, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 10, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 10, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 10, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->MultiCell(28, 5, $rmk, 1, 1, 'C', 1);
		$item = 'PK';
		$price=$PKPrice;
		$cost=$realPK;
		$trans=0;
		$ppn = 1;
		$totTbs = $hargatbsPK[3];
		$oer = $oerPK;
		$pdf->Cell(28, 10, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 10, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 10, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 10, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 10, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->MultiCell(28, 5, $rmk, 1, 1, 'C', 1);
		$item = 'CANGKANG';
		$price=$CkPrice;
		$cost=$realCk;
		$trans=0;
		$ppn = 1;
		$totTbs = $hargatbsCk[3];
		$oer = $oerCk;
		$pdf->Cell(28, 10, "HARGA ".$item, 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($price,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, ' : ' , 1, 0, 'C', 1);
		$pdf->Cell(19, 10, $ppn, 1, 0, 'C', 1);
		$pdf->Cell(3, 10, ' - ', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($cost,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, '-', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, number_format($trans,2), 1, 0, 'C', 1);
		$pdf->Cell(3, 10, 'x', 1, 0, 'C', 1);
		$pdf->Cell(19, 10, $oer." %", 1, 0, 'C', 1);
		$pdf->Cell(3, 10, '=', 1, 0, 'C', 1);
		$pdf->Cell(24, 10, number_format($totTbs,2), 1, 0, 'C', 1);
		$pdf->MultiCell(28, 5, $rmk, 1, 1, 'C', 1);
		
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 5);
$pdf->SetFillColor(220, 220, 220);	
		$pdf->SetY(148);
		$pdf->SetX(10);
		$pdf->Cell(12, 8, "Rendemen", 1, 0, 'C', 1);
		$pdf->SetY(148);
		$pdf->SetX(22);
		$pdf->Cell(19, 8, "", 1, 0, 'C', 1);
		$pdf->SetY(149);
		$pdf->SetX(23);
		$pdf->Cell(17, 4, "Harga TBS", 0, 0, 'C', 1);
		$pdf->SetY(152);
		$pdf->SetX(23);
$pdf->SetFont('Arial', 'B', 4);
$pdf->SetFillColor(220, 220, 220);	
		$pdf->Cell(17, 3, "(Excl. PK & Cangkang)", 0, 0, 'C', 1);
$pdf->SetFont('Arial', 'B', 5);
$pdf->SetFillColor(220, 220, 220);	
		$pdf->SetY(148);
		$pdf->SetX(41);
		$pdf->Cell(19, 8,'Harga PK', 1, 0, 'C', 1);
		$pdf->SetY(148);
		$pdf->SetX(60);
		$pdf->Cell(19, 8, 'Harga Cangkang', 1, 0, 'C', 1);
		$pdf->SetY(148);
		$pdf->SetX(79);
		$pdf->Cell(36, 4, 'Harga TBS Per Kilogram', 1, 0, 'C', 1);
		$posY=$posY+4;
		$pdf->SetY(152);
		$pdf->SetX(79);
		$pdf->Cell(12, 4, 'Excl. PPN', 1, 0, 'C', 1);
		$pdf->Cell(12, 4, 'Incl. PPN', 1, 0, 'C', 1);
		$pdf->Cell(12, 4, 'Real By.Olah', 1, 1, 'C', 1);
$pdf->SetY(156);
$pdf->SetX(10);
$pdf->SetFillColor(255, 255, 255);	
if($oerCPO=100){
	$baseoer=16+2.5;
}else{
$baseoer = $oerCPO + 2.5;
}
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
	$pdf->Cell(12, 4, $baseoer.'%', 1, 0, 'C', 1);
	$pdf->Cell(19, 4, number_format($npricecpo,2), 1, 0, 'C', 1);
	$pdf->Cell(19, 4,number_format($tpricepk,2), 1, 0, 'C', 1);
	$pdf->Cell(19, 4, number_format($tpricecangkang,2), 1, 0, 'C', 1);
	$pdf->Cell(12, 4, number_format($exclppn,2), 1, 0, 'C', 1);
	$pdf->Cell(12, 4, number_format($inclppn,2), 1, 0, 'C', 1);
	$pdf->Cell(12, 4, number_format($realbyolah, 2), 1, 1, 'C', 1);
}
if($oerCPO>=100){
$baseoer = 16 + 2.5;
}else{
$baseoer = $oerCPO + 2.5;
}
for($r=1; $r <= 50; $r++){
$npricecpo = $tpricecpo * $baseoer /100;
$exclppn = $npricecpo + $tpricepk + $tpricecangkang;
$inclppn = ($ppnpricecpo * $baseoer /100) + $tpricepk + $tpricecangkang;
$realbyolah = ($costpricecpo * $baseoer / 100) + $tpricepk2 + $tpricecangkang2;
	$pdf->Cell(12, 4, $baseoer.'%', 1, 0, 'C', 1);
	$pdf->Cell(19, 4, number_format($npricecpo,2), 1, 0, 'C', 1);
	$pdf->Cell(19, 4,number_format($tpricepk,2), 1, 0, 'C', 1);
	$pdf->Cell(19, 4, number_format($tpricecangkang,2), 1, 0, 'C', 1);
	$pdf->Cell(12, 4, number_format($exclppn,2), 1, 0, 'C', 1);
	$pdf->Cell(12, 4, number_format($inclppn,2), 1, 0, 'C', 1);
	$pdf->Cell(12, 4, number_format($realbyolah, 2), 1, 1, 'C', 1);
$baseoer = $baseoer - 0.1;
}




$pdf->Output();

?>



