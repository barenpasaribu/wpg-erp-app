<?php



require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/zFunction.php';

include_once 'lib/zLib.php';

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

		global $penerima;

		global $kodegudang;

		global $untukpt;

		global $untukunit;

		global $catatan;

		global $periode;

		global $kodept;

		global $namapenerima;

		$pt = '';

		$namapt = '';

		$alamatpt = '';

		$telp = '';

		$kodegudang = '';

		$status = 0;

		$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $_GET['notransaksi'] . '\'';

		$res = mysql_query($str);



		if ($bar = mysql_fetch_object($res)) {

			$kodept = $bar->kodept;

			$kodegudang = $bar->kodegudang;

			$userid = $bar->user;

			$posted = $bar->postedby;

			$namapenerima = $bar->namapenerima;

			$status = $bar->post;

			$tanggal = $bar->tanggal;

			$periode = substr($bar->tanggal, 0, 7);

			$penerima = $bar->namapenerima;

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

		if($path!=""){
			$this->Image($path, 15, 5, 20);
		}

		$path = 'images/SSP_logo.jpg';

		$this->Image($path, 5, 10, 30, 15);

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

		$this->Cell(190, 5, strtoupper($_SESSION['lang']['bast']), 0, 1, 'C');

		$this->SetFont('Arial', '', 6);

		$this->SetY(27);

		$this->SetX(163);

		$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');

		$this->Line(10, 27, 200, 27);

		$this->SetY(42);

		$this->SetFont('Arial', '', 7);

		$this->Cell(30, 4, $_SESSION['lang']['sloc'], 0, 0, 'L');

		$this->Cell(90, 4, ': ' . $kodegudang, 0, 0, 'L');

		$this->Cell(30, 4, $_SESSION['lang']['docstatus'], 0, 0, 'L');

		$this->Cell(40, 4, ': ' . $status, 0, 1, 'L');

		$this->Cell(30, 4, $_SESSION['lang']['docbast'], 0, 0, 'L');

		$this->Cell(90, 4, ': ' . $nodok, 0, 0, 'L');

		$tanggal = tanggalnormal($tanggal);

		$this->Cell(30, 4, $_SESSION['lang']['tanggal'], 0, 0, 'L');

		$this->Cell(40, 4, ': ' . $tanggal, 0, 1, 'L');

	}

}



require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/zFunction.php';

include_once 'lib/zLib.php';

require_once 'lib/fpdf.php';

$nodok = $_GET['notransaksi'];

$pdf = new PDF('P', 'mm', 'A4');

$pdf->AddPage();

$hari = hari($tanggal, $_SESSION['language']);

$pdf->Ln(2);

$pdf->Cell(30, 4, $_SESSION['lang']['pt'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $untukpt, 0, 1, 'L');

$pdf->Cell(30, 4, $_SESSION['lang']['unit'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $untukunit, 0, 1, 'L');

$pdf->Cell(60, 4, $_SESSION['lang']['detailsbb'] . ':', 0, 1, 'L');

$pdf->Ln();

$pdf->SetFont('Arial', 'B', 7);

$pdf->SetFillColor(220, 220, 220);

$str3 = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $_GET['notransaksi'] . '\' and left(kodebarang,3)=\'030\'';



#exit(mysql_error($conn));

($qStr = mysql_query($str3)) || true;

$rBrs = mysql_num_rows($qStr);

$wdth = 70;



if (0 < $rBrs) {

	$wdth = 60;

}



$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);

$pdf->Cell(30, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);

$pdf->Cell($wdth, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);

$pdf->Cell(15, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);

$pdf->Cell(18, 5, $_SESSION['lang']['kuantitas'], 1, 0, 'C', 1);



if (0 < $rBrs) {

	$pdf->Cell(15, 5, 'Harga', 1, 0, 'C', 1);

	$pdf->Cell(20, 5, $_SESSION['lang']['total'], 1, 0, 'C', 1);

}



$pdf->Cell(45, 5, 'Blok', 1, 1, 'C', 1);

$pdf->SetFillColor(255, 255, 255);

$pdf->SetFont('Arial', '', 7);

$str = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $_GET['notransaksi'] . '\'';

$res = mysql_query($str);

$no = 0;



while ($bar = mysql_fetch_object($res)) {

	$no += 1;

	$kodebarang = $bar->kodebarang;

	$satuan = $bar->satuan;

	$jumlah = $bar->jumlah;

	$namabarang = '';

	$strv = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';

	$resv = mysql_query($strv);

	$barv = mysql_fetch_object($resv);

	$namabarang = $barv->namabarang;

	$pdf->Cell(8, 5, $no, 1, 0, 'L', 1);

	$pdf->Cell(30, 5, $kodebarang, 1, 0, 'L', 1);

	$pdf->Cell($wdth, 5, $namabarang, 1, 0, 'L', 1);

	$pdf->Cell(15, 5, $satuan, 1, 0, 'L', 1);

	$pdf->Cell(18, 5, number_format($jumlah, 2, '.', ','), 1, 0, 'R', 1);



	if (substr($bar->kodebarang, 0, 3) == '030') {

		$whrdt = 'periode=\'' . $periode . '\' and kodegudang=\'' . $kodegudang . '\' and kodebarang=\'' . $bar->kodebarang . '\' and kodeorg=\'' . $kodept . '\'';

		$hrgSat = makeOption($dbname, 'log_5saldobulanan', 'kodebarang,hargarata', $whrdt);

		$pdf->Cell(15, 5, number_format($hrgSat[$bar->kodebarang], 2, '.', ','), 1, 0, 'R', 1);

		$pdf->Cell(20, 5, number_format($hrgSat[$bar->kodebarang] * $jumlah, 2, '.', ','), 1, 0, 'R', 1);

		$total += $hrgSat[$bar->kodebarang] * $jumlah;

	}



	$strz = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeblok . '\'';



	$resz = mysql_query($strz);





	while ($barz = mysql_fetch_object($resz)) {



		$namaorganiasi = $barz->namaorganisasi;



	}





	$pdf->Cell(45, 5, $namaorganiasi, 1, 1, 'L', 1);

}



if (0 < $rBrs) {

	$pdf->Cell(151, 5, $_SESSION['lang']['total'], 1, 0, 'R', 1);

	$pdf->Cell(20, 5, number_format($total, 2, '.', ','), LTB, 0, 'R', 1);

	$pdf->Cell(25, 5, '', RTB, 1, 'C', 1);

}



$pdf->MultiCell(170, 5, 'Note: ' . $catatan, 0, 'L');



if ($posted != '') {

	$posted = namakaryawan($dbname, $conn, $posted);

}



if ($namapenerima != '') {

	$namapenerima = namakaryawan($dbname, $conn, $namapenerima);

}



$pdf->SetFont('Arial', '', 7);

$brsAkhir = $pdf->GetY();

$pdf->SetY($brsAkhir + 2);

$pdf->Cell(2, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, $_SESSION['lang']['penerima'], 0, 0, 'C');

$pdf->Cell(20, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, $_SESSION['lang']['supervisor'], 0, 0, 'C');

$pdf->Cell(20, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, $_SESSION['lang']['petugasgudang'], 0, 0, 'C');

$pdf->Cell(20, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, $_SESSION['lang']['penerimagudang'], 0, 0, 'C');

$pdf->Cell(10, 4, '', 0, 0, 'L');

$pdf->Ln(15);

$pdf->Cell(2, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, $namapenerima, T, 0, 'C');

$pdf->Cell(20, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, '', T, 0, 'C');

$pdf->Cell(20, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, '', T, 0, 'C');

$pdf->Cell(20, 4, '', 0, 0, 'L');

$pdf->Cell(30, 4, $posted, T, 0, 'C');

$pdf->Cell(10, 4, '', 0, 0, 'L');

$pdf->Output();



?>

