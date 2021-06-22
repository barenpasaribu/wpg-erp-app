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

		$this->SetFont('Arial', 'B', 7);

		$this->SetFillColor(255, 255, 255);

		$this->SetY(8);

		$this->SetX(40);

		$this->Cell(60, 5, $namapt, 0, 1, 'L');

		$this->SetX(40);

		$this->Cell(60, 5, $alamatpt, 0, 1, 'L');

		$this->SetX(40);

		$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');

		$this->SetFont('Arial', '', 10);

		$this->SetY(35);

		$this->Cell(190, 5, strtoupper('permintaan pengeluaran barang gudang [MRIS]'), 0, 1, 'C');

		$this->SetFont('Arial', '', 6);

		$this->SetY(27);

		$this->SetX(163);

		$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');

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

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 6);
$hari = hari($tanggal, $_SESSION['language']);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(30, 4, $_SESSION['lang']['sloc'], 0, 0, 'L');
$pdf->Cell(100, 4, ': ' . $kodegudang, 0, 0, 'L');
$tanggal = tanggalnormal($tanggal);
$pdf->Cell(30, 4, $_SESSION['lang']['tanggal'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $tanggal, 0, 1, 'L');
$pdf->Cell(30, 4, 'No. MRIS', 0, 0, 'L');
$pdf->Cell(100, 4, ': ' . $nodok, 0, 0, 'L');
$pdf->Cell(30, 4, $_SESSION['lang']['pt'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $untukpt, 0, 1, 'L');
$pdf->Cell(30, 4, $_SESSION['lang']['docstatus'], 0, 0, 'L');
$pdf->Cell(100, 4, ': ' . $status, 0, 0, 'L');
$pdf->Cell(30, 4, $_SESSION['lang']['unit'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $untukunit, 0, 1, 'L');
$pdf->Cell(60, 4, $_SESSION['lang']['detailsbb'] . ':', 0, 1, 'L');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 7);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(6, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(16, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
$pdf->Cell(49, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
$pdf->Cell(12, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
$pdf->Cell(10, 5, 'Qty', 1, 0, 'C', 1);
#$pdf->Cell(32, 5, 'Blok', 1, 0, 'C', 1);
$pdf->Cell(51, 5, $_SESSION['lang']['kegiatan'], 1, 0, 'C', 1);
$pdf->Cell(47, 5, 'Blok / Mesin / Kendaraan', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 7);

$str = 'select * from ' . $dbname . '.log_mrisdt where notransaksi=\'' . $_GET['notransaksi'] . '\'';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$kodebarang = $bar->kodebarang;
	$satuan = $bar->satuan;
	$jumlah = $bar->jumlah;
	$namabarang = '';
	$namakegiatan = "";
	$namaorganisasi = "";
	$namakendaraan = "";
	$perawatan = "";

	$h=0;
	$strv = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang="' . $bar->kodebarang . '"';
	$resv = mysql_query($strv);
	while ($barv = mysql_fetch_object($resv)) {
		$namabarang = $barv->namabarang;
	}

	$strz = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi="' . $bar->kodeblok . '"';
	$resz = mysql_query($strz);
	while ($barz = mysql_fetch_object($resz)) {
		$namaorganisasi = $barz->namaorganisasi;
	}
	
	$stry = 'select namakegiatan from '.$dbname.".setup_kegiatan where kodekegiatan LIKE '". $bar->kodekegiatan ."'";
	$resy = mysql_query($stry);
	while ($bary = mysql_fetch_object($resy)) {
		$namakegiatan = $bary->namakegiatan;
	}

    $strx = 'select jenisvhc from '.$dbname.".vhc_5master where kodevhc='".$bar->kodemesin ."'";
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_object($resx)) {
            $namakendaraan = $barx->jenisvhc;
    }
	
	if($namaorganisasi <> "" and $namakendaraan <> ""){
		$perawatan = $namaorganisasi . " / " . $namakendaraan;
	}else{
		$perawatan = $namaorganisasi . $namakendaraan;
	}
 	$lenperawatan = strlen($perawatan);
	$lenkegiatan =strlen($namakegiatan);
	$lenbarang =strlen($namabarang);
	if( $lenkegiatan >= 31 or $lenperawatan >= 31 or $lenbarang >=31 ){
		if($lenkegiatan >= $lenbarang and $lenkegiatan >= $lenperawatan){
			$maxline = ceil($lenkegiatan / 30);
			$linerwt = ceil($lenperawatan / 30);
			$linebrg = ceil($lenbarang / 30);
			$hBarang = countLine($maxline, $linebrg);
			$hrwt = countLine($maxline, $linerwt);
			$hkeg = 4;
		}
		if($lenbarang >= $lenkegiatan and $lenbarang >= $lenperawatan){
			$maxline = ceil($lenbarang / 30);
			$linekeg = ceil($lenkegiatan / 30);
			$linerwt = ceil($lenperawatan / 30);
			$hkeg = countLine($maxline, $linekeg);
			$hrwt = countLine($maxline, $linerwt);
			$hBarang = 4;
		}
		if($lenperawatan >= $lenkegiatan and $lenperawatan >= $lenbarang){
			$maxline = ceil($lenperawatan / 30);
			$linekeg = ceil($lenkegiatan / 30);
			$linebrg = ceil($lenbarang / 30);
			$hkeg = countLine($maxline, $linekeg);
			$hBarang = countLine($maxline, $linebrg);
			$hrwt = 4;
		}
		$h= 4 * $maxline;
	}else{
		$h = 4;
	}

	$pdf->Cell(6, $h, $no, 1);
	$pdf->Cell(16, $h, $kodebarang, 1);
	if($lenbarang >= 34){
			$current_y = $pdf->GetY();
			$current_x = $pdf->GetX();
			$pdf->MultiCell(49, $hBarang, $namabarang, 1);
	}else{
		$current_y = $pdf->GetY();
		$current_x = $pdf->GetX();
		$pdf->Cell(49, $h, $namabarang, 1);
	}
	$pdf->SetXY($current_x+49, $current_y);
	$pdf->Cell(12, $h, $satuan, 1, 0, 'L', 1);
	$pdf->Cell(10, $h, number_format($jumlah, 1, '.', ','), 1);
	if($lenkegiatan >= 34){
		$current_y = $pdf->GetY();
		$current_x = $pdf->GetX();
		$pdf->MultiCell(51, $hkeg, $namakegiatan, 1);
	}else{
		$current_y = $pdf->GetY();
		$current_x = $pdf->GetX();
		$pdf->Cell(51, $h, $namakegiatan, 1);
	}
	$pdf->SetXY($current_x+51, $current_y);
	if($lenperawatan >= 34){
		$pdf->MultiCell(47, $hrwt, $perawatan, 1);
	}else{
		$pdf->Cell(47, $h, $perawatan, 1);
	}
	$pdf->SetXY(10, $current_y + $h);
}

$pdf->MultiCell(170, 5, 'Note: ' . $catatan, 0, 'L');
$pdf->Ln();

if ($posted != '') {
	$posted = namakaryawan($dbname, $conn, $posted);
}

$namakaryawan = namakaryawan($dbname, $conn, $dibuat);
$namakaryawan2 = namakaryawan($dbname, $conn, $mengetahui);
$nik = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$pdf->SetFont('Arial', '', 7);
$brsAkhir = $pdf->GetY();
$pdf->SetY($brsAkhir + 10);
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, $_SESSION['lang']['dibuat'], 0, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, 'Diperiksa', 0, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, 'Disetujui', 0, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, 'Penerima', 0, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0, 'L');
$pdf->Ln(20);
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, ucwords($namakaryawan), T, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, '', T, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, '', T, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, '', T, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, $nik[$dibuat], 0, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, '', 0, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, '', 0, 0, 'C');
$pdf->Cell(15, 4, '', 0, 0, 'L');
$pdf->Cell(30, 4, '', 0, 0, 'C');

$pdf->Output();

?>



