<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_GET['kodeorg'];
$thnbudget = $_GET['thnbudget'];
$jenis = $_GET['jenis'];
$lebar1 = 3;
$lebar2 = 6;
$lebar3 = 10;
$lebar4 = 7;
$lebar5 = 5;
$lebar6 = 5.5;
$luas = 0;
$produksi = 0;
$str = 'select sum(kgsetahun) as produksi from ' . $dbname . '.bgt_produksi_kbn_kg_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and kodeunit=\'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$produksi = $bar->produksi;
}

$luastm = 0;
$luastbm = 0;
$luasxtm = 0;
$luasxtbm = 0;
$str = 'select hathnini as luas,statusblok from ' . $dbname . '.bgt_blok ' . "\r\n" . '          where tahunbudget=' . $thnbudget . ' and kodeblok like \'' . $kodeorg . '%\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if ($bar->statusblok == 'TM') {
		$luasxtm += $bar->luas;
	}
	else {
		$luasxtbm += $bar->luas;
	}
}

if ($jenis == 'LANGSUNG') {
	$str = 'select hathnini as luas from ' . $dbname . '.bgt_blok ' . "\r\n" . '          where tahunbudget=' . $thnbudget . ' and kodeblok like \'' . $kodeorg . '%\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		if ($bar->statusblok == 'TM') {
			$luastm = $bar->luas;
		}
		else {
			$luastbm = $bar->luas;
		}
	}
}
else {
	$str = 'select sum(hathnini) as luas from ' . $dbname . '.bgt_areal_per_afd_vw ' . "\r\n" . '          where tahunbudget=' . $thnbudget . ' and afdeling like \'' . $kodeorg . '%\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luastm = $bar->luas;
		$luastbm = $bar->luas;
	}
}
class PDF extends FPDF
{
	public function Header()
	{
		global $kodeorg;
		global $thnbudget;
		global $jenis;
		global $lebar1;
		global $lebar2;
		global $lebar3;
		global $lebar4;
		global $lebar5;
		global $lebar6;
		global $dbname;
		global $luas;
		global $produksi;
		global $jenis;
		global $luastm;
		global $luastbm;
		global $luasxtm;
		global $luasxtbm;
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
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['luas'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((12 / 100) * $width, $height, 'TM:' . number_format($luasxtm, 0, '.', ',') . ' ha, TBM:' . number_format($luasxtbm, 0, '.', ',') . ' ha', '', 0, 'R');
		$this->Cell((52 / 100) * $width, $height, '', '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['totalkg'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((12 / 100) * $width, $height, number_format($produksi, 0, '.', ',') . ' kg', '', 0, 'R');
		$this->Cell((52 / 100) * $width, '', '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
		$title = 'Budget Biaya ' . $jenis . ' Kebun ' . $kodeorg . ' tahun budget: ' . $thnbudget;
		$this->Ln();
		$this->SetFont('Arial', 'U', 10);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 8);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(($lebar1 / 100) * $width, $height, $_SESSION['lang']['nourut'], 1, 0, 'C', 1);
		$this->Cell(($lebar2 / 100) * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
		$this->Cell(($lebar3 / 100) * $width, $height, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);
		$this->Cell(($lebar4 / 100) * $width, $height, $_SESSION['lang']['jumlahrp'], 1, 0, 'C', 1);
		$this->Cell(($lebar5 / 100) * $width, $height, $_SESSION['lang']['rpperkg'], 1, 0, 'C', 1);
		$this->Cell(($lebar5 / 100) * $width, $height, $_SESSION['lang']['rpperha'], 1, 0, 'C', 1);
		$i = 1;

		while ($i <= 12) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			if ($i != 12) {
				$this->Cell(($lebar6 / 100) * $width, $height, $ii . '(Rp)', 1, 0, 'C', 1);
			}
			else {
				$this->Cell(($lebar6 / 100) * $width, $height, $ii . '(Rp)', 1, 1, 'C', 1);
			}

			++$i;
		}
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
$pdf->SetFont('Arial', '', 6);
$adq = 'a.noakun, sum(a.rupiah) as rupiah,sum(a.rp01) as rp01,' . "\r\n" . '      sum(a.rp02) as rp02,sum(a.rp03) as rp03,' . "\r\n" . '      sum(a.rp04) as rp04,sum(a.rp05) as rp05,' . "\r\n" . '      sum(a.rp06) as rp06,sum(a.rp07) as rp07,' . "\r\n" . '      sum(a.rp08) as rp08,sum(a.rp09) as rp09,' . "\r\n" . '      sum(a.rp10) as rp10,sum(a.rp11) as rp11,' . "\r\n" . '      sum(a.rp12) as rp12';

if ($jenis == 'UMUM') {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget=\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'ESTATE\'     ' . "\r\n" . '      group by a.noakun';
}
else if ($jenis == 'LANGSUNG') {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget<>\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'ESTATE\'     ' . "\r\n" . '      group by a.noakun';
}
else {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where  tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'ESTATE\'     ' . "\r\n" . '      group by a.noakun';
}

$strx = 'select * from ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $thnbudget . '\' and' . "\r\n" . 'kodeunit like \'' . $kodeorg . '%\' order by namatipe';
$resx1 = mysql_query($strx);
$res = mysql_query($str);
$res1 = mysql_query($str);
$res2 = mysql_query($str);
$res3 = mysql_query($str);
$no = 0;
$rpperha = 0;
$rpperkg = 0;
$ttrp = 0;

if ($bar = mysql_fetch_object($res3)) {
	$no += 1;
	@$rpperkg = 0;
	@$rpperha = $bar->rupiah / $luasxtbm;
	@$rpperhatbm += $bar->rupiah / $luasxtbm;
	@$rpperkgtbm = 0;
	$tt01tbm += $bar->rp01;
	$tt02tbm += $bar->rp02;
	$tt03tbm += $bar->rp03;
	$tt04tbm += $bar->rp04;
	$tt05tbm += $bar->rp05;
	$tt06tbm += $bar->rp06;
	$tt07tbm += $bar->rp07;
	$tt08tbm += $bar->rp08;
	$tt09tbm += $bar->rp09;
	$tt10tbm += $bar->rp10;
	$tt11tbm += $bar->rp11;
	$tt12tbm += $bar->rp12;
	$ttrptbm += $bar->rupiah;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->noakun, 1, 0, 'C', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $bar->namaakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format(0, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format(0, 0, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'rp0' . $i;
	}
	else {
		$ii = 'rp' . $i;
	}

	if ($i != 12) {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
	}
	else {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
	}

	++$i;
}

$gttperha += 0;
$gttperkg += 0;
$gtt01 += $tt01tbm;
$gtt02 += $tt02tbm;
$gtt03 += $tt03tbm;
$gtt04 += $tt04tbm;
$gtt05 += $tt05tbm;
$gtt06 += $tt06tbm;
$gtt07 += $tt07tbm;
$gtt08 += $tt08tbm;
$gtt09 += $tt09tbm;
$gtt10 += $tt10tbm;
$gtt11 += $tt11tbm;
$gtt12 += $tt12tbm;
$gtt += $bar->rupiah;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'Total BBT', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($ttrptbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format(0, 2, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format(0, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt01tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt02tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt03tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt04tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt05tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt06tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt07tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt08tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt09tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt10tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt11tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt12tbm, 0, '.', ','), 1, 1, 'R', 1);
$rpperha = 0;
$rpperkg = 0;
$ttrp = 0;

if ($bar = mysql_fetch_object($res)) {
	$no += 1;
	@$rpperkg = 0;
	@$rpperha = $bar->rupiah / $luasxtbm;
	@$rpperhatbm += $bar->rupiah / $luasxtbm;
	@$rpperkgtbm = 0;
	$tt01tbm += $bar->rp01;
	$tt02tbm += $bar->rp02;
	$tt03tbm += $bar->rp03;
	$tt04tbm += $bar->rp04;
	$tt05tbm += $bar->rp05;
	$tt06tbm += $bar->rp06;
	$tt07tbm += $bar->rp07;
	$tt08tbm += $bar->rp08;
	$tt09tbm += $bar->rp09;
	$tt10tbm += $bar->rp10;
	$tt11tbm += $bar->rp11;
	$tt12tbm += $bar->rp12;
	$ttrptbm1 += $bar->rupiah;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->noakun, 1, 0, 'C', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $bar->namaakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperkg, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperha, 0, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'rp0' . $i;
	}
	else {
		$ii = 'rp' . $i;
	}

	if ($i != 12) {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
	}
	else {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
	}

	++$i;
}

$gttperha += $rpperhatbm;
$gttperkg += 0;
$gtt01 += $tt01tbm;
$gtt02 += $tt02tbm;
$gtt03 += $tt03tbm;
$gtt04 += $tt04tbm;
$gtt05 += $tt05tbm;
$gtt06 += $tt06tbm;
$gtt07 += $tt07tbm;
$gtt08 += $tt08tbm;
$gtt09 += $tt09tbm;
$gtt10 += $tt10tbm;
$gtt11 += $tt11tbm;
$gtt12 += $tt12tbm;
$gtt += $bar->rupiah;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'Total TBM', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($ttrptbm1, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperkgtbm, 2, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperhatbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt01tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt02tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt03tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt04tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt05tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt06tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt07tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt08tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt09tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt10tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt11tbm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt12tbm, 0, '.', ','), 1, 1, 'R', 1);

if ($bar = mysql_fetch_object($res1)) {
	$no += 1;
	@$rpperkg = 0;
	@$rpperkg = $bar->rupiah / $produksi;
	@$rpperha = $bar->rupiah / $luasxtm;
	@$rpperhatm += $bar->rupiah / $luasxtm;
	@$rpperkgtm += $bar->rupiah / $produksi;
	$tt01tm += $bar->rp01;
	$tt02tm += $bar->rp02;
	$tt03tm += $bar->rp03;
	$tt04tm += $bar->rp04;
	$tt05tm += $bar->rp05;
	$tt06tm += $bar->rp06;
	$tt07tm += $bar->rp07;
	$tt08tm += $bar->rp08;
	$tt09tm += $bar->rp09;
	$tt10tm += $bar->rp10;
	$tt11tm += $bar->rp11;
	$tt12tm += $bar->rp12;
	$ttrptm += $bar->rupiah;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->noakun, 1, 0, 'C', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $bar->namaakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperkg, 2, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperha, 0, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'rp0' . $i;
	}
	else {
		$ii = 'rp' . $i;
	}

	if ($i != 12) {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
	}
	else {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
	}

	++$i;
}

$gttperha += $rpperhatm;
$gttperkg += $rpperkgtm;
$gtt01 += $tt01tm;
$gtt02 += $tt02tm;
$gtt03 += $tt03tm;
$gtt04 += $tt04tm;
$gtt05 += $tt05tm;
$gtt06 += $tt06tm;
$gtt07 += $tt07tm;
$gtt08 += $tt08tm;
$gtt09 += $tt09tm;
$gtt10 += $tt10tm;
$gtt11 += $tt11tm;
$gtt12 += $tt12tm;
$gtt += $bar->rupiah;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'Total TM', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($ttrptm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperkgtm, 2, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperhatm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt01tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt02tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt03tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt04tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt05tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt06tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt07tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt08tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt09tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt10tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt11tm, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt12tm, 0, '.', ','), 1, 1, 'R', 1);

if ($bar = mysql_fetch_object($res2)) {
	$no += 1;
	@$rpperkg = 0;
	@$rpperkg = $bar->rupiah / $produksi;
	@$rpperha = $bar->rupiah / ($luasxtbm + $luasxtm);
	@$rpperhaum += $bar->rupiah / ($luasxtbm + $luasxtm);
	@$rpperkgum += $bar->rupiah / $produksi;
	$tt01um += $bar->rp01;
	$tt02um += $bar->rp02;
	$tt03um += $bar->rp03;
	$tt04um += $bar->rp04;
	$tt05um += $bar->rp05;
	$tt06um += $bar->rp06;
	$tt07um += $bar->rp07;
	$tt08um += $bar->rp08;
	$tt09um += $bar->rp09;
	$tt10um += $bar->rp10;
	$tt11um += $bar->rp11;
	$tt12um += $bar->rp12;
	$ttrpum += $bar->rupiah;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->noakun, 1, 0, 'C', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $bar->namaakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperkg, 2, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperha, 0, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'rp0' . $i;
	}
	else {
		$ii = 'rp' . $i;
	}

	if ($i != 12) {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
	}
	else {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
	}

	++$i;
}

$gttperha += $rpperhaum;
$gttperkg += $rpperkgum;
$gtt01 += $tt01um;
$gtt02 += $tt02um;
$gtt03 += $tt03um;
$gtt04 += $tt04um;
$gtt05 += $tt05um;
$gtt06 += $tt06um;
$gtt07 += $tt07um;
$gtt08 += $tt08um;
$gtt09 += $tt09um;
$gtt10 += $tt10um;
$gtt11 += $tt11um;
$gtt12 += $tt12um;
$gtt += $bar->rupiah;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'Total Biaya Umum', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($ttrpum, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperkgum, 2, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperhaum, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt01um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt02um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt03um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt04um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt05um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt06um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt07um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt08um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt09um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt10um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt11um, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt12um, 0, '.', ','), 1, 1, 'R', 1);

if ($bar = mysql_fetch_object($resx1)) {
	@$rpperkg = 0;
	@$rpperha = 0;
	@$rpperkg = $bar->harga / $produksi;
	@$rpperha = $bar->harga / ($luasxtbm + $luasxtm);
	@$rpperhakap += $bar->harga / ($luasxtbm + $luasxtm);
	$rpperkgkap += $rpperkg;
	$tt01kap += $bar->k01;
	$tt02kap += $bar->k02;
	$tt03kap += $bar->k03;
	$tt04kap += $bar->k04;
	$tt05kap += $bar->k05;
	$tt06kap += $bar->k06;
	$tt07kap += $bar->k07;
	$tt08kap += $bar->k08;
	$tt09kap += $bar->k09;
	$tt10kap += $bar->k10;
	$tt11kap += $bar->k11;
	$tt12kap += $bar->k12;
	$ttrpkap += $bar->harga;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->noakun, 1, 0, 'C', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $bar->namatipe, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->harga, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperkg, 2, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperha, 0, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'k0' . $i;
	}
	else {
		$ii = 'k' . $i;
	}

	if ($i != 12) {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
	}
	else {
		$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
	}

	++$i;
}

$gttperha += $rpperhakap;
$gttperkg += $rpperkgkap;
$gtt01 += $tt01kap;
$gtt02 += $tt02kap;
$gtt03 += $tt03kap;
$gtt04 += $tt04kap;
$gtt05 += $tt05kap;
$gtt06 += $tt06kap;
$gtt07 += $tt07kap;
$gtt08 += $tt08kap;
$gtt09 += $tt09kap;
$gtt10 += $tt10kap;
$gtt11 += $tt11kap;
$gtt12 += $tt12kap;
$gtt += $bar->harga;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'Total KAPITAL', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($ttrpkap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperkgkap, 2, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($rpperhakap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt01kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt02kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt03kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt04kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt05kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt06kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt07kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt08kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt09kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt10kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt11kap, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt12kap, 0, '.', ','), 1, 1, 'R', 1);
$gttperkg = $gtt / $produksi;
$gttperha = $gtt / ($luasxtbm + $luasxtm);
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'GRAND TOTAL', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($gtt, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($gttperkg, 2, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($gttperha, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt01, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt02, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt03, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt04, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt05, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt06, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt07, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt08, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt09, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt10, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt11, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($gtt12, 0, '.', ','), 1, 1, 'R', 1);
$pdf->Output();

?>
