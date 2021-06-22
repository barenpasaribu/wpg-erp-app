<?php

require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/fpdf.php';

require_once 'lib/zLib.php';

include_once 'lib/zMysql.php';

include_once 'lib/terbilang.php';

include_once 'lib/spesialCharacter.php';



class PDF extends FPDF

{

	public function Footer()

	{

		$this->SetY(-15);

		$this->SetFont('Arial', '', 8);

	}

}



$table = $_GET['table'];

$column = $_GET['column'];

$where = $_GET['cond'];

$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

$mtUang = makeOption($dbname, 'setup_matauang', 'kode,simbol');



$pdf = new PDF('P', 'mm', 'A4');

$pdf->AddFont('Calibri','','calibri.php');

$pdf->AddPage();

$i = 'select * from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $_GET['column'] . '\' ';



($n = mysql_query($i)) || true;

$d = mysql_fetch_assoc($n);



if ($d['ppn'] == '0') {

	$ppn = 'tidak termasuk PPN 10%';

}

else {

	$ppn = 'termasuk PPN '.$d['ppn'].'% ';

}



$isiKualitas = explode(' ', $d['kualitas']);

$ffa = $isiKualitas[0];

$mi = $isiKualitas[0];



if (($d['kodebarang'] == '40000001') && ($d['kodept'] == 'SSP')) {

	$mutu = 'FFA ' . $ffa . '% max; M&I ' . $mi . '% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Swadaya Sapta Putra dari campuran contoh CPO yang diambil dari bagian atas, tengah dan bawah tanki timbul bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ' . $ffa . '% ';

	$dasarBerat = 'Berat final berdasarkan data hasil sounding tangki timbun PT. Swadaya Sapta Putra di Kumaligon, dengan menggunakan TABEL DENSITY yang dikeluarkan oleh Surveyor SUCOFINDO.';

}

else if (($d['kodebarang'] == '40000001') && ($d['kodept'] == 'MJR')) {

	$mutu = 'FFA ' . $ffa . '% max; M&I ' . $mi . '% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Hexa Sawita bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ' . $ffa . '% ';

	$dasarBerat = 'Berat final berdasarkan laporan hasil penimbangan di PMKS PT. Hexa Sawita, Kab. _____, ________.';

}

else if (($d['kodebarang'] == '40000002') && ($d['kodept'] == 'SSP')) {

	$mutu = 'FFA ' . $ffa . '% max; M&I total ' . $mi . '% max; berdasarkan hasil laboratorium PMKS PT. Swadaya Sapta Putra disaksikan dari wakil pihak Pembeli';

	$dasarBerat = '';

}

else if (($d['kodebarang'] == '40000002') && ($d['kodept'] == 'MJR')) {

	$mutu = 'FFA ' . $ffa . '% max; M&I total ' . $mi . '% max; berdasarkan hasil laboratorium PMKS PT. Merbaujaya Indahraya disaksikan dari wakil pihak Pembeli';

	$dasarBerat = '';

}



$derajat = 'º';

$derajat = em($derajat);

$derajat = urldecode($derajat);



if ($d['kodept'] == 'SSP') {

	$harga1 = 'PKS PT. Swadaya Sapta Putra';

	$pelmuat = 'Desa Bejarau, Kotawaringin Timur, Kalimantan Tengah. 01' . $derajat . ' 14\' 01\'\' LU, dan 121' . $derajat . ' 25\' 12\'\' BT';

	$lokasiTtd = 'Jakarta';

	$nmPt = 'PT. Swadaya Sapta Putra';

	$jbtnTdd = 'Direktur';

}

else if ($d['kodept'] == 'MJR') {

	$harga1 = 'PKS PT. Merbaujaya Indahraya';

	$pelmuat = 'Pelabuhan Sebakis, Desa Pembilangan, Nunukan, Kalimantan Timur. 04' . $derajat . ' 04\' 59\'\' LU dan 117' . $derajat . ' 16\' 32\'\' BT';

	$lokasiTtd = 'Jakarta';

	$nmPt = 'PT. MERBAUJAYA INDAHRAYA';

	$jbtnTdd = 'Kuasa Direksi';

}



$pel = explode('.', $pelmuat);



if ($d['kodebarang'] == '40000001') {

	$klaimMutu = 'Apabila FFA CPO di atas standard maka akan diklaim secara proporsional';

	$barang = 'CPO (MINYAK SAWIT)';

}

else if ($d['kodebarang'] == '40000002') {

	$klaimMutu = 'Apabila FFA Kernel di atas standard maka akan diklaim secara proporsional';

	$barang = 'KERNEL (INTI SAWIT)';

}

/*
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,6,'NO',1,0);
$pdf->Cell(50,6,'NAMA MOTOR',1,0);
$pdf->Cell(35,6,'WARNA',1,0);
$pdf->Cell(30,6,'BRAND',1,0);
$pdf->Cell(25,6,'HARGA',1,0);
$pdf->Cell(25,6,'CICILAN',1,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,6,'NO',1,0);
$pdf->Cell(50,6,'NAMA MOTOR',1,0);
$pdf->Cell(35,6,'WARNA',1,0);
$pdf->Cell(30,6,'BRAND',1,0);
$pdf->Cell(25,6,'HARGA',1,0);
$pdf->Cell(25,6,'CICILAN',1,1);
*/
//





$pdf->Ln(20);

$pdf->SetFont('Arial', 'BU', '14');
$pdf->Cell(200, 20, 'DELIVERY ORDER', 0, 1, 'C');

$pdf->SetFont('Arial', '', '10');

$pdf->Cell(20, 5, 'No.               :    '. $d['nokontrak'], 0, 0, 'L');

$tglTtd = explode('-', $d['tanggalkontrak']);

$nmBlnTtd = numToMonth($tglTtd[1], 'I', 'long');
$tglisiTtd = $tglTtd[2] . ' ' . date('F', strtotime($tglTtd[0])) . ' ' . $tglTtd[0];
$pdf->SetX(120);
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5,'Tgl : '.$tglisiTtd, 0, 1, 'L');

$o = 'select * from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $d['koderekanan'] . '\'';

$p = mysql_query($o);

$q = mysql_fetch_assoc($p);


$x = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';

$y = mysql_query($x);

$z = mysql_fetch_assoc($y);

$pdf->Cell(20, 5, 'Kepada   ', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');

$pdf->SetFont('Arial', '', '10');

$pdf->Cell(10, 5, $q['namacustomer'], 0, 1, 'L');
$pdf->Cell(20, 5, 'Alamat', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, $q['alamat'], 0, 1, 'L');
$pdf->Ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,6,'NO   KODE',1,0,'C');
$pdf->Cell(60,6,'KETERANGAN',1,0,'C');
$pdf->Cell(45,6,'BANYAKNYA',1,0,'C');
$pdf->Cell(40,6,'SATUAN',1,0,'C');
$pdf->Ln();

$pdf->SetFont('Arial','B',10);
$pdf->SetY(76);
$pdf->SetX(10);
//$pdf->MultiCell(30,30,$barang,1,0);
$pdf->MultiCell(40,60,"",1,'L',false);


$pdf->SetY(76);
$pdf->SetX(50);
$pdf->MultiCell(60,30,$barang."\n".$barang,1,'L',false);

$pdf->SetY(76);
$pdf->SetX(110);
$pdf->MultiCell(45, 30, number_format($d['kuantitaskontrak']) ."\n".number_format($d['kuantitaskontrak']), 1,'L',false);

$pdf->SetY(76);
$pdf->SetX(155);
$pdf->MultiCell(40, 30,$d['satuan']."\n".$d['satuan'], 1,'L',false);

$pdf->SetY(135);
$pdf->SetX(10);
$pdf->SetFont('Arial', 'BU', '10');
$pdf->Cell(10, 20, 'PEMBAYARAN DENGAN CEK/GIRO BARU DINYATAKAN LUNAS BILA TELAH DITERIMA OLEH BANK ', 0, 1, 'L');
$pdf->Ln();

$pdf->SetFont('Arial','B',10);
$pdf->SetY(150);
$pdf->SetX(10);
//$pdf->MultiCell(60,30,"",1,0);
$pdf->MultiCell(60,15,"DIBUAT OLEH\n\nKOMAR",1,'C',false);

$pdf->SetFont('Arial','B',10);
$pdf->SetY(150);
$pdf->SetX(70);
$pdf->MultiCell(60,15,"DIPERIKSA OLEH\n\nKOMAR",1,'C',false);

$pdf->SetFont('Arial','B',10);
$pdf->SetY(150);
$pdf->SetX(130);
$pdf->MultiCell(65,15,"DISETUJUI OLEH\n\nKOMAR",1,'C',false);
$pdf->Ln();
$pdf->Cell(10, 5, 'LEMBAR 1 : PENGAMBILAN BARANG', 0, 1, 'L');
$pdf->Cell(10, 5, 'LEMBAR 2 : PEMBELI', 0, 1, 'L');
$pdf->Output();



?>

