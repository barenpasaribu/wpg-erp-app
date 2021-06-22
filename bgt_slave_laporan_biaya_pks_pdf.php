<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_GET['kodeorg'];
$thnbudget = $_GET['thnbudget'];
$lebar1 = 5;
$lebar2 = 23;
$lebar3 = 33;
$lebar4 = 15;
$lebar5 = 10;
$lebar6 = 10;
$tbs = 0;
$cpo = 0;
$pk = 0;
$str = 'select sum(kgolah) as tbs,sum(kgcpo) as cpo,sum(kgkernel) as kernel from ' . $dbname . '.bgt_produksi_pks_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and millcode = \'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$tbs = $bar->tbs;
	$cpo = $bar->cpo;
	$pk = $bar->kernel;
}
class PDF extends FPDF
{
	public function Header()
	{
		global $kodeorg;
		global $thnbudget;
		global $lebar1;
		global $lebar2;
		global $lebar3;
		global $lebar4;
		global $lebar5;
		global $lebar6;
		global $dbname;
		global $tbs;
		global $cpo;
		global $pk;
		$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
		$orgData = fetchData($query);
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 15;

		if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
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

		$this->Image($path, $this->lMargin, $this->tMargin, 70);
		$this->SetFont('Arial', 'B', 9);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(100);
		$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
		$this->SetX(100);
		$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
		$this->SetX(100);
		$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
		$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
		$this->Ln();
		$this->SetFont('Arial', '', 8);
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['tbsdiolah'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((12 / 100) * $width, $height, number_format($tbs, 0, '.', ',') . ' ' . $_SESSION['lang']['ton'], '', 0, 'R');
		$this->Cell((52 / 100) * $width, $height, '', '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['cpo'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((12 / 100) * $width, $height, number_format($cpo, 0, '.', ',') . ' ' . $_SESSION['lang']['ton'], '', 0, 'R');
		$this->Cell((52 / 100) * $width, '', '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['kernel'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((12 / 100) * $width, $height, number_format($pk, 0, '.', ',') . ' ' . $_SESSION['lang']['ton'], '', 1, 'R');
		$title = strtoupper($_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['langsung']) . ' ' . $kodeorg . ' ' . $_SESSION['lang']['tahun'] . ' ' . $thnbudget;
		$this->Ln();
		$this->SetFont('Arial', 'U', 10);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 9);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(($lebar1 / 100) * $width, $height, $_SESSION['lang']['nourut'], 1, 0, 'C', 1);
		$this->Cell(($lebar2 / 100) * $width, $height, $_SESSION['lang']['station'], 1, 0, 'C', 1);
		$this->Cell(($lebar3 / 100) * $width, $height, $_SESSION['lang']['kodeabs'], 1, 0, 'C', 1);
		$this->Cell(($lebar4 / 100) * $width, $height, $_SESSION['lang']['jumlahrp'], 1, 0, 'C', 1);
		$this->Cell(($lebar5 / 100) * $width, $height, $_SESSION['lang']['rpperkg'] . '-PP', 1, 0, 'C', 1);
		$this->Cell(($lebar6 / 100) * $width, $height, $_SESSION['lang']['rpperkg'] . '-' . strtoupper($_SESSION['lang']['tbs']), 1, 1, 'C', 1);
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}
}


$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$str = 'select a.*,b.namaorganisasi,c.nama from ' . $dbname . '.bgt_pks_station_vw a left join' . "\r\n" . '      ' . $dbname . '.organisasi b on a.station=b.kodeorganisasi left join ' . $dbname . '.bgt_kode c on a.kdbudget=c.kodebudget' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and a.station like \'' . $kodeorg . '%\'' . "\r\n" . '      ';
$res = mysql_query($str);
$no = 0;
$rpperha = 0;
$old = '';
$jumlah = 0;
$grandtt = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$new = $bar->station;
	$jumlah += $bar->rupiah;
	$grandtt += $bar->rupiah;

	if ($bar->kdbudget == 'M') {
		$nama_komponen = 'Material';
	}
	else {
		$nama_komponen = $bar->nama;
	}

	if (($old != '') && ($old != $new)) {
		@$jumlahpercpo = $jumlah / ($cpo + $pk);
		@$jumlahpertbs = $jumlah / $tbs;
		$pdf->SetFillColor(220, 220, 220);
		$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
		$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($jumlah, 0, '.', ','), 1, 0, 'R', 1);
		$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($jumlahpercpo, 0, '.', ','), 1, 0, 'R', 1);
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($jumlahpertbs, 0, '.', ','), 1, 1, 'R', 1);
		$pdf->SetFillColor(255, 255, 255);
		$jumlah = 0;
	}

	@$rupiahpercpo = $bar->rupiah / ($cpo + $pk);
	@$rupiahpertbs = $bar->rupiah / $tbs;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->namaorganisasi, 1, 0, 'L', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $nama_komponen, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rupiahpercpo, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($rupiahpertbs, 0, '.', ','), 1, 1, 'R', 1);
	$old = $bar->station;
}

@$jumlahpercpo = $jumlah / ($cpo + $pk);
@$jumlahpertbs = $jumlah / $tbs;
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($jumlah, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($jumlahpercpo, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($jumlahpertbs, 0, '.', ','), 1, 1, 'R', 1);
$pdf->SetFillColor(255, 255, 255);
@$grandttpercpo = $grandtt / ($cpo + $pk);
@$grandttpertbs = $grandtt / $tbs;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, $_SESSION['lang']['grnd_total'], 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($grandtt, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($grandttpercpo, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($grandttpertbs, 0, '.', ','), 1, 1, 'R', 1);
$pdf->Output();

?>
