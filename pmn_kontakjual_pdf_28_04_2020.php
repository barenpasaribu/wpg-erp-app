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

// sementara $mutu tidak dipakai dimana mana
if (($d['kodebarang'] == '40000001') && ($d['kodept'] == 'SSP')) {
	$mutu = 'FFA ' . $ffa . '% max; M&I ' . $mi . '% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Semunai Sawit Perkasa dari campuran contoh CPO yang diambil dari bagian atas, tengah dan bawah tanki timbul bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ' . $ffa . '% ';
	$dasarBerat = 'Berat final berdasarkan data hasil sounding tangki timbun PT. Semunai Sawit Perkasa di Kumaligon, dengan menggunakan TABEL DENSITY yang dikeluarkan oleh Surveyor SUCOFINDO.';
}
else if (($d['kodebarang'] == '40000001') && ($d['kodept'] == 'MJR')) {
	$mutu = 'FFA ' . $ffa . '% max; M&I ' . $mi . '% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Hexa Sawita bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ' . $ffa . '% ';
	$dasarBerat = 'Berat final berdasarkan laporan hasil penimbangan di PMKS PT. Hexa Sawita, Kab. _____, ________.';
}
else if (($d['kodebarang'] == '40000002') && ($d['kodept'] == 'SSP')) {
	$mutu = 'FFA ' . $ffa . '% max; M&I total ' . $mi . '% max; berdasarkan hasil laboratorium PMKS PT. Semunai Sawit Perkasa disaksikan dari wakil pihak Pembeli';
	$dasarBerat = '';
}
else if (($d['kodebarang'] == '40000002') && ($d['kodept'] == 'MJR')) {
	$mutu = 'FFA ' . $ffa . '% max; M&I total ' . $mi . '% max; berdasarkan hasil laboratorium PMKS PT. Merbaujaya Indahraya disaksikan dari wakil pihak Pembeli';
	$dasarBerat = '';
}

$derajat = 'º';
$derajat = em($derajat);
$derajat = urldecode($derajat);

// sementara belum dipakai dimana mana
if ($d['kodept'] == 'SSP') {
	$harga1 = 'PKS PT. Semunai Sawit Perkasa';
	$pelmuat = '';
	$lokasiTtd = 'Pekanbaru';
	$nmPt = 'PT. Semunai Sawit Perkasa';
	$jbtnTdd = 'Kuasa Direksi';
}
else if ($d['kodept'] == 'LSP') {
	$harga1 = 'PKS PT. Libo Sawit Perkasa';
	$pelmuat = '';
	$lokasiTtd = 'Pekanbaru';
	$nmPt = 'PT. Libo Sawit Perkasa';
	$jbtnTdd = 'Kuasa Direksi';
}
$pel = explode('.', $pelmuat);

if ($d['kodebarang'] == '40000001') {
	$klaimMutu = 'Apabila FFA CPO di atas standar maka akan diklaim secara proporsional';
	$barang = 'CPO (CRUDE PALM OIL)';
}
else if ($d['kodebarang'] == '40000002') {
	$klaimMutu = 'Apabila FFA PK di atas standar maka akan diklaim secara proporsional';
	$barang = 'PK (PALM KERNEL)';
}

$str1 = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';
$res1 = mysql_query($str1);
$bar1 = mysql_fetch_object($res1);
$namapt = $bar1->namaorganisasi;
$alamatpt = $bar1->alamat;
$kotapt= $bar1->wilayahkota;
$telp = $bar1->telepon;
$logo = $bar1->logo;
$kotatelp = $kotapt."    Telp. ".$telp;

if(!empty($logo)){
	$pdf->Image($logo, 15, 5, 35, 20);
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetX(55);
$pdf->Cell(60, 5, $namapt, 0, 1, 'L');
$pdf->SetX(55);
$pdf->Cell(60, 5, $alamatpt, 0, 1, 'L');
$pdf->SetX(55);
$pdf->Cell(60, 5, $kotatelp, 0, 1, 'L');
//$pdf->SetX(55);
//$pdf->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
$pdf->SetFont('Arial', '', 15);
$pdf->SetY(30);
		
$pdf->Ln(20);
$pdf->SetFont('Arial', 'BU', '14');
//$pdf->Cell(200, 5, 'SURAT PERJANJIAN JUAL BELI', 0, 1, 'C');
$pdf->Cell(200, 5, 'KONTRAK JUAL BELI', 0, 1, 'C');
$pdf->SetFont('Arial', '', '14');
$pdf->Cell(200, 5, 'No. ' . $d['nokontrak'], 0, 1, 'C');
$pdf->Ln();
$x = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';
$y = mysql_query($x);
$z = mysql_fetch_assoc($y);
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'PENJUAL', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, $z['namaorganisasi'], 0, 1, 'L');
$pdf->Cell(40, 5, 'ALAMAT', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $z['alamat'], 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(45, 5, '', 0, 0, 'L');
$pdf->Cell(10, 5, $z['wilayahkota'], 0, 1, 'L');
$xx = 'select * from ' . $dbname . '.setup_org_npwp where kodeorg=\'' . $d['kodept'] . '\'';
$yy = mysql_query($xx);
$zz = mysql_fetch_assoc($yy);
$pdf->Cell(40, 5, 'NPWP', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $zz['npwp'], 0, 1, 'L');
$o = 'select * from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $d['koderekanan'] . '\'';
$p = mysql_query($o);
$q = mysql_fetch_assoc($p);
$pdf->Cell(40, 5, 'PEMBELI', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $q['namacustomer'], 0, 1, 'L');
$pdf->Cell(40, 5, 'ALAMAT', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $q['alamat'], 0, 1, 'L');
$pdf->Cell(40, 5, 'NPWP', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $q['npwp'], 0, 1, 'L');
$pdf->Cell(40, 5, 'JENIS BARANG', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, $barang, 0, 1, 'L');
$pdf->Cell(40, 5, 'MUTU', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, str_replace("dan", "&", $d['kualitas']), 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'BANYAKNYA (Kwantiti)', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, number_format($d['kuantitaskontrak']) . ' ' . $d['satuan'], 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'NO. DO', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->MultiCell(125, 5, $d['nodo'], 0, 'L');

if ($d['ppn'] != '0') {
	$descppn = ' / (Inc. PPN '.$d['ppn'].'%)';
	$ppnx = $d['hargasatuan']+($d['hargasatuan']*$d['ppn']/100);
}
else {
	$descppn = ' / (Exc. PPN 10%)';
	$ppnx = number_format($d['hargasatuan']);
}

$pdf->Cell(40, 5, 'HARGA SATUAN', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, 'Rp. ' . $ppnx . ' ' . $descppn, 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'LOKASI PENYERAHAN', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->MultiCell(125, 5, $d['pelabuhan'], 0, 'J');
$pdf->Cell(40, 5, 'WAKTU PENYERAHAN', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');

$pdf->SetFont('Arial', '', '10');
$tglKirim = explode('-', $d['tanggalkirim']);
$tglSd = explode('-', $d['sdtanggal']);
$nmBlnKirim = numToMonth($tglKirim[1], 'I', 'long');
$nmBlnSd = numToMonth($tglSd[1], 'I', 'long');
$tglisiKirim = $tglKirim[2] . ' ' . $nmBlnKirim . ' ' . $tglKirim[0];
$tglisiSd = $tglSd[2] . ' ' . $nmBlnSd . ' ' . $tglSd[0];
//$pdf->Cell(10, 5, date('d F Y', strtotime($tglisiKirim[3])), 0, 1, 'L');
$pdf->Cell(10, 5, tanggalnormal($d['tanggalkirim']).' s/d. '.tanggalnormal($d['sdtanggal']), 0, 1, 'L');

$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'PEMBAYARAN', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(60, 5, str_replace("dan", "&", $d['syratpembayaran']), 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(45, 5, '', 0, 0, 'L');
$pdf->Cell(10, 5, str_replace("dan", "&", $d['syratpembayaran2']), 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'JUMLAH HARGA', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(60, 5, "Rp ".number_format($d['grand_total'],2), 0, 1, 'L');
$pdf->Cell(45, 5, '', 0, 0, 'L');
$pdf->Cell(10, 5, 'TERBILANG : '. ucwords(strtolower($d['terbilang'] . ' RUPIAH')), 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'TOLERANSI SUSUT', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
if( $d['toleransi'] == 0){
	$pdf->Cell(60, 5, '-', 0, 1, 'L');
}else{
	$pdf->Cell(60, 5, $d['toleransi'] . '%, akan diklaim full apa bila susut di atas ' . $d['toleransi'] . '% per truck', 0, 1, 'L');
}

$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'CATATAN', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
if($d['catatan1']!="") {$pdf->Cell(65, 5, str_replace("dan", "&", $d['catatan1']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan2']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan2']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan3']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan3']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan4']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan4']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan5']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan5']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan6']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan6']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan7']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan7']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan8']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan8']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan9']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan9']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan10']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan10']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan11']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan11']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan12']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan12']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan13']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan13']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan14']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan14']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
if($d['catatan15']!="") {$pdf->Cell(10, 5, str_replace("dan", "&", $d['catatan15']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(10, 5, 'TRANSFER KE REKENING:', 0, 1, 'L');
$pdf->Cell(10, 5, 'a/n : PT. SEMUNAI SAWIT PERKASA', 0, 1, 'L');
$pdf->Cell(10, 5, 'BANK MANDIRI', 0, 1, 'L');
$pdf->Cell(10, 5, 'CABANG PEKANBARU', 0, 1, 'L');
$pdf->Cell(10, 5, 'AC 123-00-1234567-8', 0, 1, 'L');
$pdf->Ln();
$pdf->Ln();
$tglTtd = explode('-', $d['tanggalkontrak']);
$nmBlnTtd = numToMonth($tglTtd[1], 'I', 'long');
$tglisiTtd = $tglTtd[2] . ' ' . date('F', strtotime($tglTtd[0])) . ' ' . $tglTtd[0];
$pdf->SetX(120);
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $lokasiTtd . ', ' . tanggalnormal($d['tanggalkontrak']), 0, 1, 'R');
$pdf->Ln();
$pdf->Cell(10, 5, 'PIHAK PEMBELI', 0, 0, 'L');
$pdf->SetX(155);
$pdf->Cell(10, 5, 'PIHAK PENJUAL', 0, 1, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $q['namacustomer'], 0, 0, 'L');
$pdf->SetX(155);
$pdf->Cell(10, 5, $nmPt, 0, 1, 'L');
$ysekarang = $pdf->GetY();
$pdf->Ln(25);
$pdf->SetFont('Arial', 'U', '10');
$pdf->Cell(10, 5, $q['pk'], 0, 0, 'L');
$pdf->SetX(155);
$pdf->Cell(10, 5, $d['penandatangan'], 0, 0, 'L');
$pdf->Output();

?>
