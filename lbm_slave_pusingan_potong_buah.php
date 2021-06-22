<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$sKlmpk = 'select kode,kelompok from ' . $dbname . '.log_5klbarang order by kode';

#exit(mysql_error());
($qKlmpk = mysql_query($sKlmpk)) || true;

while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
	$rKelompok[$rKlmpk['kode']] = $rKlmpk['kelompok'];
}

$optNmOrang = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$thn = explode('-', $periode);
$bln = intval($thn[1]);

if (strlen($bln) < 2) {
	if ($thn[1] == '1') {
		$blnLalu = 12;
		$thn[0] = $thn[0] - 1;
	}
	else {
		$blnLalu = '0' . $bln;
	}
}
else {
	$blnLalu = $bln - 1;
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\' group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $afdId . '%\' group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuas += $rLuas['tahuntanam'];
		$dtThnTnm[] = $rLuas['tahuntanam'];
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih<=9 group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $afdId . '%\' and selisih<=9 group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur += $rLuas['tahuntanam'];
		@$xpertama[$rLuas['tahuntanam']] = ($dtLuasumur[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih<=9 group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $afdId . '%\' and selisih<=9 group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumurL += $rLuas['tahuntanam'];
		@$xpertama2[$rLuas['tahuntanam']] = ($dtLuasumurL[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih>=10 and selisih<=15 group by tahuntanam';

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur25 += $rLuas['tahuntanam'];
		@$xkedua[$rLuas['tahuntanam']] = ($dtLuasumur25[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih>=10 and selisih<=15 group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $afdId . '%\' and selisih>=10 and selisih<=15 group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur25L += $rLuas['tahuntanam'];
		@$xkedua2[$rLuas['tahuntanam']] = ($dtLuasumur25L[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih>=16 and selisih<=20 group by tahuntanam';

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur30 += $rLuas['tahuntanam'];
		@$xketiga[$rLuas['tahuntanam']] = ($dtLuasumur30[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih>=16 and selisih<=20 group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '            where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $afdId . '%\' and selisih>=16 and selisih<=20 group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur30L += $rLuas['tahuntanam'];
		@$xketiga2[$rLuas['tahuntanam']] = ($dtLuasumur30L[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih>20  group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $periode . '\' and kodeorg like \'' . $afdId . '%\' and selisih>20  group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur20 += $rLuas['tahuntanam'];
		@$xkeempat[$rLuas['tahuntanam']] = ($dtLuasumur20[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $kdUnit . '%\' and selisih>20  group by tahuntanam';

if ($afdId != '') {
	$sLuas = 'select tahuntanam,sum(luasareaproduktif) as luas from ' . $dbname . '.kebun_interval_panen1_vw ' . "\r\n" . '       where periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodeorg like \'' . $afdId . '%\' and selisih>20  group by tahuntanam';
}

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	if (($rLuas['tahuntanam'] != '') || ($rLuas['tahuntanam'] != '0')) {
		$dtLuasumur202 += $rLuas['tahuntanam'];
		@$xkeempat2[$rLuas['tahuntanam']] = ($dtLuasumur202[$rLuas['tahuntanam']] / $dtLuas[$rLuas['tahuntanam']]) * 100;
	}
}

$jmlHari = cal_days_in_month(CAL_GREGORIAN, $thn[1], $thn[0]);
$varCek = count($dtThnTnm);

if ($varCek < 1) {
	exit('Error: No data found');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>07. ' . $_SESSION['lang']['pusingan'] . ' ' . $_SESSION['lang']['panen'] . '</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=5 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3>' . $_SESSION['lang']['thntnm'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3>' . $_SESSION['lang']['luas'] . ' (HA)</td><td ' . $bgcoloraja . ' colspan=8>' . $_SESSION['lang']['persen'] . '</td</tr>';
$tab .= '<tr><td ' . $bgcoloraja . ' colspan=2>< 9 ' . $_SESSION['lang']['hari'] . '</td><td ' . $bgcoloraja . ' colspan=2>10-15 ' . $_SESSION['lang']['hari'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=2>16-20 ' . $_SESSION['lang']['hari'] . '</td>';
$tab .= ' <td ' . $bgcoloraja . ' colspan=2> >20 ' . $_SESSION['lang']['hari'] . '</td></tr>';
$tab .= '<tr><td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['bulanlalu'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['bulanlalu'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['bulanlalu'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['bulanlalu'] . '</td>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';

foreach ($dtThnTnm as $lstLuas) {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $lstLuas . '</td>';
	$tab .= '<td align=right>' . $dtLuas[$lstLuas] . '</td>';
	$tab .= '<td align=right>' . number_format($xpertama[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xpertama2[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xkedua[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xkedua2[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xketiga[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xketiga2[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xkeempat[$lstLuas], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($xkeempat2[$lstLuas], 2) . '</td>';
	$tab .= '</tr>';
	$totLuas += $dtLuas[$lstLuas];
	$totBi1 += $xpertama[$lstLuas] * $dtLuas[$lstLuas];
	$totBl1 += $xpertama2[$lstLuas] * $dtLuas[$lstLuas];
	$totBi2 += $xkedua[$lstLuas] * $dtLuas[$lstLuas];
	$totBl2 += $xkedua2[$lstLuas] * $dtLuas[$lstLuas];
	$totBi3 += $xketiga[$lstLuas] * $dtLuas[$lstLuas];
	$totBl3 += $xketiga2[$lstLuas] * $dtLuas[$lstLuas];
	$totBi4 += $xkeempat[$lstLuas] * $dtLuas[$lstLuas];
	$totBl4 += $xkeempat2[$lstLuas] * $dtLuas[$lstLuas];
}

@$bi1 = $totBi1 / $totLuas;
@$bl1 = $totBl1 / $totLuas;
@$bi2 = $totBi2 / $totLuas;
@$bl2 = $totBl2 / $totLuas;
@$bi3 = $totBi3 / $totLuas;
@$bl3 = $totBl3 / $totLuas;
@$bi4 = $totBi4 / $totLuas;
@$bl4 = $totBl4 / $totLuas;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '<td align=right>' . number_format($totLuas, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bi1, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bl1, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bi2, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bl2, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bi3, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bl3, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bi4, 2) . '</td>';
$tab .= '<td align=right>' . number_format($bl4, 2) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'mutasi_karyawan_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('07. ' . $_SESSION['lang']['pusingan'] . ' ' . $_SESSION['lang']['panen']), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');

			if ($afdId != '') {
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId], 0, 1, 'L');
			}

			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(60, $height, $_SESSION['lang']['tahun'], TLR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['luas'], TLR, 0, 'C', 1);
			$this->Cell(400, $height, 'INTERVAL ' . $_SESSION['lang']['panen'] . ' (%)', TLR, 1, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['tanam'], LR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['panen'], LR, 0, 'C', 1);
			$this->Cell(100, $height, '< 9 ' . $_SESSION['lang']['hari'], TLR, 0, 'C', 1);
			$this->Cell(100, $height, '10-15 ' . $_SESSION['lang']['hari'], TLR, 0, 'C', 1);
			$this->Cell(100, $height, '16-20 ' . $_SESSION['lang']['hari'], TLR, 0, 'C', 1);
			$this->Cell(100, $height, '>20 ' . $_SESSION['lang']['hari'], TLR, 1, 'C', 1);
			$this->Cell(60, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(60, $height, '(HA)', BLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bulanlalu'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bulanlalu'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bulanlalu'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bulanlalu'], TBLR, 1, 'C', 1);
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
	$height = 10;
	$tnggi = $jmlHari * $height;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);
	$totLuas += $dtLuas[$lstLuas];
	$totBi1 += $xpertama[$lstLuas];
	$totBl1 += $xpertama2[$lstLuas];
	$totBi2 += $xkedua[$lstLuas];
	$totBl2 += $xkedua2[$lstLuas];
	$totBi3 += $xketiga[$lstLuas];
	$totBl3 += $xketiga2[$lstLuas];
	$totBi4 += $xkeempat[$lstLuas];
	$totBl4 += $xkeempat2[$lstLuas];

	foreach ($dtThnTnm as $lstLuas) {
		$pdf->Cell(60, $height, $lstLuas, TBLR, 0, 'C', 1);
		$pdf->Cell(60, $height, $dtLuas[$lstLuas], TBLR, 0, 'C', 1);
		$pdf->Cell(50, $height, number_format($xpertama[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xpertama2[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xkedua[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xkedua2[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xketiga[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xketiga2[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xkeempat[$lstLuas], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($xkeempat2[$lstLuas], 2), TBLR, 1, 'R', 1);
	}

	$pdf->Cell(60, $height, $_SESSION['lang']['total'], TBLR, 0, 'C', 1);
	$pdf->Cell(60, $height, number_format($totLuas, 2), TBLR, 0, 'C', 1);
	$pdf->Cell(50, $height, number_format($bi1, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bl1, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bi2, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bl2, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bi3, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bl3, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bi4, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($bl4, 2), TBLR, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
