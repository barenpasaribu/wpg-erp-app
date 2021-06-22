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
$nodok = $_GET['notransaksi'];
$paratgl = substr($nodok, 0, 10);
$orgvalue = substr($nodok, 10, 3);
$tgl = substr($paratgl,8, 2).'-'. substr($paratgl,5,2). '-' . substr($paratgl, 0,4);
$sqlht="select * from pmn_rendemenht where koderendemen='".$nodok."'";
$sqldt = "SELECT * FROM pmn_rendemendt WHERE koderendemen='".$nodok."'";
$exe = mysql_query($sqldt);
while($resdt=mysql_fetch_object($exe)){
	$dt[$resdt->kodebarang][$resdt->kodelist]= $resdt->amount;
}
$str = mysql_query($sqlht);
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

$sql="select * from pmn_tbl_rendemen_vw2 
		where tgltimbang ='". $paratgl ."' and 
		SUBSTRING(klsupplier, CHAR_LENGTH(klsupplier)-2, 3) = '". $orgvalue ."'";
$res = mysql_query($sql);
$no = 0;
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
$pdf->SetY(35);
$pdf->Cell(190, 5, strtoupper('PEMBELIAN TBS'), 0, 1, 'C');
$pdf->Cell(190, 5, strtoupper('TANGGAL '.$tgl), 0, 1, 'C');
$pdf->SetFont('Arial', '', 6);
$hari = hari($tanggal, $_SESSION['language']);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(20, 4, 'No. Rendemen', 0, 0, 'L');
$pdf->Cell(100, 4, ': ' . $nodok, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 7);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(50, 5, 'Supplier', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'Price /kg', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'Fee /kg', 1, 0, 'C', 1);
$pdf->Cell(22, 5, 'Harga /kg', 1, 0, 'C', 1);
$pdf->Cell(20, 5, 'Tonase', 1, 0, 'C', 1);
$pdf->Cell(30, 5, 'Total', 1, 0, 'C', 1);
$pdf->Cell(16, 5, 'OER', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 7);
while ($bar = mysql_fetch_object($res)) {
	$kontrakOER = ($bar->harga_akhir - $respk)/ $resCPO * 100;
	$kontrakOER = round($kontrakOER, 2);
	$no++;
	$pdf->Cell(8, 5, $no, 1, 0, 'C', 1);
	$pdf->Cell(50, 5, $bar->namasupplier, 1, 0, 'C', 1);
	$pdf->Cell(22, 5, number_format($bar->harga_harian, 2), 1, 0, 'C', 1);
	$pdf->Cell(22, 5, number_format($bar->fee, 2), 1, 0, 'C', 1);
	$pdf->Cell(22, 5, number_format($bar->harga_akhir + $bar->fee, 2), 1, 0, 'C', 1);
	$pdf->Cell(20, 5, number_format($bar->totalkg), 1, 0, 'C', 1);
	$pdf->Cell(30, 5, number_format($bar->tot_tgh, 2), 1, 0, 'C', 1);
	$pdf->Cell(16, 5, number_format($kontrakOER,2).'%', 1, 1, 'C', 1);
	$tonase = $tonase + $bar->totalkg;
	$kontraktot = $kontraktot + $bar->tot_tgh;	
}
$pricekg=$kontraktot/$tonase;
$pricekg=round($pricekg,2);
$totalOer=($pricekg-$respk)/$resCPO * 100;
$totalOer=round($totalOer,2);
$marginper=$actualOer-$totalOer;
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(102, 5, 'TOTAL', 1, 0, 'C', 1);
/* $pdf->Cell(22, 5, '', 2), 1, 0, 'C', 1);
$pdf->Cell(22, 5, '', 1, 0, 'C', 1); */
$pdf->Cell(22, 5, number_format($pricekg, 2), 1, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($tonase,0), 1, 0, 'C', 1);
$pdf->Cell(30, 5, number_format($kontraktot, 2), 1, 0, 'C', 1);
$pdf->Cell(16, 5, number_format($totalOer,2).'%', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 7);
$pdf->MultiCell(170, 5, 'Note: ' . $catatan, 0, 'L');
$pdf->Ln();

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
$pdf->AddPage();
$pdf->SetFont('Arial', '', 6);
$hari = hari($tanggal, $_SESSION['language']);
$pdf->SetFont('Arial', '', 7);
$pdf->SetY(28);
$pdf->Cell(20, 4, 'No. Rendemen', 0, 0, 'L');
$pdf->Cell(100, 4, ': ' . $nodok, 0, 0, 'L');
$pdf->Ln();
$pdf->SetFont('Arial', '', 9);
$pdf->SetY(35);
$pdf->Cell(190, 5, strtoupper('Harga tender tanggal '.$tgl), 0, 1, 'C');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$brsAkhir = $pdf->GetY();
$pdf->SetY($brsAkhir + 8);
$pdf->Cell(24, 5, 'CPO ', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($cpoPrice, 2), 0, 0, 'C');
$pdf->Cell(4, 5, ':', 0, 0, 'C', 1);
$pdf->Cell(12, 5, $ppncpo, 0, 0, 'C', 1);
$pdf->Cell(4, 5, '-', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($costCPO, 2) , 0, 0, 'C', 1);
$pdf->Cell(4, 5, '-', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($TransCPOdt, 2), 0, 0, 'C', 1);
$pdf->Cell(4, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($resultCPO, 2) , 0, 0, 'C', 1);
$kolom = $pdf->GetX();
$pdf->SetX($kolom + 16);
$pdf->Cell(4, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(34, 5, number_format($resCPO, 2), 0, 1, 'R', 1);
$pdf->Cell(24, 5, 'PK ', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($pkPrice, 2), 0, 0, 'C');
$pdf->Cell(4, 5, ':', 0, 0, 'C', 1);
$pdf->Cell(12, 5, $ppnpk, 0, 0, 'C', 1);
$pdf->Cell(4, 5, '-', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($costpk, 2) , 0, 0, 'C', 1);
$pdf->Cell(4, 5, '-', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($Transpkdt, 2), 0, 0, 'C', 1);
$pdf->Cell(4, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($resultpk, 2), 0, 0, 'C', 1);
$pdf->Cell(4, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(12, 5, $OERpabrikpk . ' %', 0, 0, 'C', 1);
$pdf->Cell(4, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(34, 5, number_format($respk, 2), 0, 1, 'R', 1);
$pdf->Cell(24, 5, 'Cangkang ', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($cangkangPrice, 2), 0, 0, 'C');
$pdf->Cell(4, 5, ':', 0, 0, 'C', 1);
$pdf->Cell(12, 5, $ppnck, 0, 0, 'C', 1);
$pdf->Cell(4, 5, '-', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($costcangkang, 2) , 0, 0, 'C', 1);
$pdf->Cell(4, 5, '-', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($Transcangkangdt, 2), 0, 0, 'C', 1);
$pdf->Cell(4, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($resultcangkang, 2), 0, 0, 'C', 1);
$pdf->Cell(4, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(12, 5, $OERpabrikcangkang . ' %', 0, 0, 'C', 1);
$pdf->Cell(4, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(34, 5, number_format($rescangkang, 2), 0, 1, 'R', 1);
$pdf->Cell(44, 5, 'Margin ', 0, 0, 'L');
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($marginper, 2) .' %', 0, 1, 'C', 1);
$pdf->Cell(44, 5, 'Pembelian OER ', 0, 0, 'L');
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, $totalOer.' %', 0, 0, 'C', 1);
$pdf->Cell(33, 5, number_format($tonase, 0) , 0, 0, 'C', 1);
$pdf->Cell(33, 5, number_format($pricekg, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totalPurch, 2), 0, 1, 'R', 1);
$pdf->SetFont('Arial', 'U', 10);
$brsAkhir = $pdf->GetY();
$pdf->SetY($brsAkhir + 6);
$pdf->Cell(24, 5, 'HASIL ', 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
//$brsAkhir = $pdf->GetY();
//$pdf->SetY($brsAkhir + 8);
$pdf->Cell(44, 5, 'OER ACTUAL ', 0, 0, 'L');
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(14, 5, $actualOer.' %', 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($tonase, 0) , 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($amountOer, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($dppOer, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($jualOer, 2), 0, 1, 'R', 1);
$pdf->Cell(44, 5, 'PK ACTUAL ', 0, 0, 'L');
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(14, 5, $actualPK.' %', 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($tonase, 2) , 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($amountPK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($dppPK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($jualPK, 2), 0, 1, 'R', 1);
$pdf->Cell(44, 5, 'Cangkang Estimasi ', 0, 0, 'L');
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(14, 5, $actualCk.' %', 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($tonase, 0) , 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(20, 5, number_format($amountCk, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($dppCk, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($jualCk, 2), 0, 1, 'R', 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(136, 5, 'TOTAL HASIL ', 0, 0, 'L', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totalResult, 2), 0, 1, 'R', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'U', 10);
$brsAkhir = $pdf->GetY();
$pdf->SetY($brsAkhir + 6);
$pdf->Cell(24, 5, 'BIAYA ', 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(96, 5, 'Pembelian TBS', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($tonase, 0), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($pricekg, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totalPurch, 2), 0, 1, 'R', 1);
$pdf->Cell(96, 5, 'Biaya Olah CPO', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($amountOer, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($biayaCPO, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($cpoprodcost, 2), 0, 1, 'R', 1);
$pdf->Cell(96, 5, 'Biaya Olah PK', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($amountPK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($biayaPK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($pkprodcost, 2), 0, 1, 'R', 1);
/* $pdf->Cell(96, 5, 'Biaya Olah Cangkang', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($amountCk, 0), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($biayaCK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($ckprodcost, 2), 0, 1, 'R', 1); */
$pdf->Cell(96, 5, 'Biaya KANDIR', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($tonase, 0), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($biayakandir, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($kandircost, 2), 0, 1, 'R', 1);
$pdf->Cell(96, 5, 'Biaya Pengangkutan CPO', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($amountOer, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($transCPO, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totcostCpo, 2), 0, 1, 'R', 1);
$pdf->Cell(96, 5, 'Biaya Pengangkutan PK', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($amountPK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($transPK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totcostPK, 2), 0, 1, 'R', 1);
/* $pdf->Cell(96, 5, 'Biaya Pengangkutan Cangkang', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($amountCk, 0), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($transCK, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totcostCk, 2), 0, 1, 'R', 1); */
$pdf->Cell(96, 5, 'Biaya Penyusutan', 0, 0, 'L');
$pdf->Cell(20, 5, number_format($tonase, 0), 0, 0, 'C', 1);
$pdf->Cell(6, 5, 'x', 0, 0, 'C', 1);
$pdf->Cell(14, 5, number_format($decreas, 2), 0, 0, 'C', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($decreasCost, 2), 0, 1, 'R', 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(136, 5, 'TOTAL BIAYA ', 0, 0, 'L', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($totalBiaya, 2), 0, 1, 'R', 1);
$brsAkhir = $pdf->GetY();
$pdf->SetY($brsAkhir + 5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(136, 5, 'LABA (RUGI) ', 0, 0, 'L', 1);
$pdf->Cell(6, 5, '=', 0, 0, 'C', 1);
$pdf->Cell(44, 5, number_format($labaRugi, 2), 0, 1, 'R', 1);
if ($posted != '') {
	$posted = namakaryawan($dbname, $conn, $posted); 
}

/* $namakaryawan = namakaryawan($dbname, $conn, $dibuat);
$namakaryawan2 = namakaryawan($dbname, $conn, $mengetahui);
$nik = makeOption($dbname, 'datakaryawan', 'karyawanid,nik'); */
$pdf->Ln(20);


$pdf->Output();

?>



