<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$tahun = $_GET['tahun'];
$pabrik = $_GET['pabrik'];

if ($tahun == '') {
	echo 'WARNING: silakan mengisi tahun.';
	exit();
}

if ($pabrik == '') {
	echo 'WARNING: silakan mengisi pabrik.';
	exit();
}

$isidata = array();
$str = 'select * from ' . $dbname . '.bgt_produksi_pks_vw where tahunbudget = \'' . $tahun . '\' and millcode = \'' . $pabrik . '\' order by kodeunit';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$isidata[$bar->kodeunit][tbstotal] = $bar->kgolah;
	$isidata[$bar->kodeunit][tbs01] = $bar->olah01;
	$isidata[$bar->kodeunit][tbs02] = $bar->olah02;
	$isidata[$bar->kodeunit][tbs03] = $bar->olah03;
	$isidata[$bar->kodeunit][tbs04] = $bar->olah04;
	$isidata[$bar->kodeunit][tbs05] = $bar->olah05;
	$isidata[$bar->kodeunit][tbs06] = $bar->olah06;
	$isidata[$bar->kodeunit][tbs07] = $bar->olah07;
	$isidata[$bar->kodeunit][tbs08] = $bar->olah08;
	$isidata[$bar->kodeunit][tbs09] = $bar->olah09;
	$isidata[$bar->kodeunit][tbs10] = $bar->olah10;
	$isidata[$bar->kodeunit][tbs11] = $bar->olah11;
	$isidata[$bar->kodeunit][tbs12] = $bar->olah12;
	$isidata[$bar->kodeunit][cpototal] = $bar->kgcpo;
	$isidata[$bar->kodeunit][cpo01] = $bar->kgcpo01;
	$isidata[$bar->kodeunit][cpo02] = $bar->kgcpo02;
	$isidata[$bar->kodeunit][cpo03] = $bar->kgcpo03;
	$isidata[$bar->kodeunit][cpo04] = $bar->kgcpo04;
	$isidata[$bar->kodeunit][cpo05] = $bar->kgcpo05;
	$isidata[$bar->kodeunit][cpo06] = $bar->kgcpo06;
	$isidata[$bar->kodeunit][cpo07] = $bar->kgcpo07;
	$isidata[$bar->kodeunit][cpo08] = $bar->kgcpo08;
	$isidata[$bar->kodeunit][cpo09] = $bar->kgcpo09;
	$isidata[$bar->kodeunit][cpo10] = $bar->kgcpo10;
	$isidata[$bar->kodeunit][cpo11] = $bar->kgcpo11;
	$isidata[$bar->kodeunit][cpo12] = $bar->kgcpo12;
	$isidata[$bar->kodeunit][kertotal] = $bar->kgkernel;
	$isidata[$bar->kodeunit][ker01] = $bar->kgker01;
	$isidata[$bar->kodeunit][ker02] = $bar->kgker02;
	$isidata[$bar->kodeunit][ker03] = $bar->kgker03;
	$isidata[$bar->kodeunit][ker04] = $bar->kgker04;
	$isidata[$bar->kodeunit][ker05] = $bar->kgker05;
	$isidata[$bar->kodeunit][ker06] = $bar->kgker06;
	$isidata[$bar->kodeunit][ker07] = $bar->kgker07;
	$isidata[$bar->kodeunit][ker08] = $bar->kgker08;
	$isidata[$bar->kodeunit][ker09] = $bar->kgker09;
	$isidata[$bar->kodeunit][ker10] = $bar->kgker10;
	$isidata[$bar->kodeunit][ker11] = $bar->kgker11;
	$isidata[$bar->kodeunit][ker12] = $bar->kgker12;
}
class PDF extends FPDF
{
	public function Header()
	{
		global $tahun;
		global $pabrik;
		global $dbname;
		global $isidata;
		global $lebarno;
		global $lebarasal;
		global $lebaruraian;
		global $lebarsatuan;
		global $lebarbulan;
		global $lebartotal;
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
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['budgetyear'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((70 / 100) * $width, $height, $tahun, '', 0, 'L');
		$this->Cell(((7 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((70 / 100) * $width, $height, $pabrik, '', 0, 'L');
		$this->Cell(((7 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
		$title = $_SESSION['lang']['distribusiproduksi'];
		$this->Ln();
		$this->SetFont('Arial', 'U', 12);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 10);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(($lebarno / 100) * $width, $height, 'No', 1, 0, 'C', 1);
		$this->Cell(($lebarasal / 100) * $width, $height, $_SESSION['lang']['asaltbs'], 1, 0, 'C', 1);
		$this->Cell(($lebaruraian / 100) * $width, $height, $_SESSION['lang']['uraian'], 1, 0, 'C', 1);
		$this->Cell(($lebarsatuan / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Jan', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Feb', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Mar', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Apr', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'May', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Jun', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Jul', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Aug', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Sep', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Oct', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Nov', 1, 0, 'C', 1);
		$this->Cell(($lebarbulan / 100) * $width, $height, 'Dec', 1, 0, 'C', 1);
		$this->Cell(($lebartotal / 100) * $width, $height, $_SESSION['lang']['total'], 1, 1, 'C', 1);
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}
}


$lebarno = 3;
$lebarasal = 7;
$lebaruraian = 5;
$lebarsatuan = 5;
$lebarbulan = 6;
$lebartotal = 7;
$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$str = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk<>\'' . $_SESSION['org']['kodeorganisasi'] . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$afiliasi[$bar->kodeorganisasi] = $bar->kodeorganisasi;
}

$str = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$internal[$bar->kodeorganisasi] = $bar->kodeorganisasi;
}

$str = 'select distinct supplierid from ' . $dbname . '.log_5supplier' . "\r\n" . '                  order by supplierid';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$eksternal[$bar->supplierid] = $bar->supplierid;
}

$no = 1;

if (!empty($internal)) {
	$olahdata[internal] += tbstotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$olahdata[internal] += $ii;
		++$i;
	}

	$olahdata[internal] += cpototal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$olahdata[internal] += $ii;
		++$i;
	}

	$olahdata[internal] += kertotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$olahdata[internal] += $ii;
		++$i;
	}

	$olahdata[internal] += paltotal;
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
		$jj = 'cpo0' . $i;
		$kk = 'ker0' . $i;
	}
	else {
		$ii = 'pal' . $i;
		$jj = 'cpo' . $i;
		$kk = 'ker' . $i;
	}

	$olahdata[internal] += $ii;
	++$i;
}

if (!empty($afiliasi)) {
	$olahdata[afiliasi] += tbstotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$olahdata[afiliasi] += $ii;
		++$i;
	}

	$olahdata[afiliasi] += cpototal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$olahdata[afiliasi] += $ii;
		++$i;
	}

	$olahdata[afiliasi] += kertotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$olahdata[afiliasi] += $ii;
		++$i;
	}

	$olahdata[afiliasi] += paltotal;
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
		$jj = 'cpo0' . $i;
		$kk = 'ker0' . $i;
	}
	else {
		$ii = 'pal' . $i;
		$jj = 'cpo' . $i;
		$kk = 'ker' . $i;
	}

	$olahdata[afiliasi] += $ii;
	++$i;
}

if (!empty($eksternal)) {
	$olahdata[eksternal] += tbstotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$olahdata[eksternal] += $ii;
		++$i;
	}

	$olahdata[eksternal] += cpototal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$olahdata[eksternal] += $ii;
		++$i;
	}

	$olahdata[eksternal] += kertotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$olahdata[eksternal] += $ii;
		++$i;
	}

	$olahdata[eksternal] += paltotal;
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
		$jj = 'cpo0' . $i;
		$kk = 'ker0' . $i;
	}
	else {
		$ii = 'pal' . $i;
		$jj = 'cpo' . $i;
		$kk = 'ker' . $i;
	}

	$olahdata[eksternal] += $ii;
	++$i;
}

$olahdata[all][tbstotal] = $olahdata[internal][tbstotal] + $olahdata[afiliasi][tbstotal] + $olahdata[eksternal][tbstotal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'tbs0' . $i;
	}
	else {
		$ii = 'tbs' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

$olahdata[all][cpototal] = $olahdata[internal][cpototal] + $olahdata[afiliasi][cpototal] + $olahdata[eksternal][cpototal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'cpo0' . $i;
	}
	else {
		$ii = 'cpo' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

$olahdata[all][kertotal] = $olahdata[internal][kertotal] + $olahdata[afiliasi][kertotal] + $olahdata[eksternal][kertotal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'ker0' . $i;
	}
	else {
		$ii = 'ker' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

$olahdata[all][paltotal] = $olahdata[internal][paltotal] + $olahdata[afiliasi][paltotal] + $olahdata[eksternal][paltotal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
	}
	else {
		$ii = 'pal' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

if (!empty($olahdata)) {
	$pdf->Cell(($lebarno / 100) * $width, $height, '1', RLT, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, 'Internal', RLT, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'TBS', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[internal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[internal] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[internal][tbstotal] = $jmlhSma[internal][tbstotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[internal][tbstotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'CPO', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[internal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[internal] += cpototal;
		++$i;
	}

	@$toNjmlhSma[internal][cpototal] = $jmlhSma[internal][cpototal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[internal][cpototal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Kernel', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[internal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[internal] += kertotal;
		++$i;
	}

	@$toNjmlhSma[internal][kertotal] = $jmlhSma[internal][kertotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[internal][kertotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Palm', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[internal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[internal] += paltotal;
		++$i;
	}

	@$toNjmlhSma[internal][paltotal] = $olahdata[internal][$ii] = $jmlhSma[internal][paltotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[internal][paltotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '2', RLT, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, 'Afiliasi', RLT, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'TBS', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[afiliasi][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[afiliasi] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][tbstotal] = $jmlhSma[afiliasi][tbstotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[afiliasi][tbstotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'CPO', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[afiliasi][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[afiliasi] += cpototal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][cpototal] = $jmlhSma[afiliasi][cpototal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[afiliasi][cpototal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Kernel', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[afiliasi][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[afiliasi] += kertotal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][kertotal] = $jmlhSma[afiliasi][kertotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[afiliasi][kertotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Palm', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[afiliasi][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[afiliasi] += paltotal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][paltotal] = $jmlhSma[afiliasi][paltotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[afiliasi][paltotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '3', RLT, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, 'Eksternal', RLT, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'TBS', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[eksternal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[eksternal] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[eksternal][tbstotal] = $jmlhSma[eksternal][tbstotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[eksternal][tbstotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'CPO', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[eksternal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[eksternal] += cpototal;
		++$i;
	}

	@$toNjmlhSma[eksternal][cpototal] = $jmlhSma[eksternal][cpototal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[eksternal][cpototal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Kernel', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[eksternal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[eksternal] += kertotal;
		++$i;
	}

	@$toNjmlhSma[eksternal][kertotal] = $jmlhSma[eksternal][kertotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[eksternal][kertotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Palm', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[eksternal][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[eksternal] += paltotal;
		++$i;
	}

	@$toNjmlhSma[eksternal][paltotal] = $jmlhSma[eksternal][paltotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[eksternal][paltotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RLT, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, 'Grand Total', RLT, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'TBS', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[all][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[all] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[all][tbstotal] = $jmlhSma[all][tbstotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[all][tbstotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'CPO', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[all][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[all] += cpototal;
		++$i;
	}

	@$toNjmlhSma[all][cpototal] = $jmlhSma[all][cpototal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[all][cpototal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RL, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Kernel', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[all][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[all] += kertotal;
		++$i;
	}

	@$toNjmlhSma[all][kertotal] = $jmlhSma[all][kertotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[all][kertotal], 2), 1, 1, 'R', 1);
	$pdf->Cell(($lebarno / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebarasal / 100) * $width, $height, '', RLB, 0, 'C', 1);
	$pdf->Cell(($lebaruraian / 100) * $width, $height, 'Palm', 1, 0, 'C', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($toNolahdata[all][$ii], 2), 1, 0, 'R', 1);
		$jmlhSma[all] += paltotal;
		++$i;
	}

	@$toNjmlhSma[all][paltotal] = $jmlhSma[all][paltotal] / 1000;
	$pdf->Cell(($lebartotal / 100) * $width, $height, number_format($toNjmlhSma[all][paltotal], 2), 1, 1, 'R', 1);
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td rowspan=4 valign=middle align=right>&nbsp;</td>';
	$stream .= '<td rowspan=4 valign=middle align=left>Grand Total</td>';
	$stream .= '<td align=left>TBS</td>';
	$stream .= '<td align=left>Ton</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][tbstotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii], 2) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][tbstotal], 2) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>CPO</td>';
	$stream .= '<td align=left>Ton</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][cpototal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii], 2) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][cpototal], 2) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Kernel</td>';
	$stream .= '<td align=left>Ton</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][kertotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii], 2) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][kertotal], 2) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Palm</td>';
	$stream .= '<td align=left>Ton</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][paltotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii], 2) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][paltotal], 2) . '</td>';
	$stream .= '</tr>';
}

$pdf->Output();

?>
