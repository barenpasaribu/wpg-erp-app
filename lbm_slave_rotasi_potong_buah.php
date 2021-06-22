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

$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];

if ($bulan == '01') {
	$tahunlalu = $tahun - 1;
	$bulanlalu = '12';
}
else {
	$tahunlalu = $tahun;
	$bulanlalu = $bulan - 1;

	if (strlen($bulanlalu) == 1) {
		$bulanlalu = '0' . $bulanlalu;
	}
}

$periodelalu = $tahunlalu . '-' . $bulanlalu;

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$tanggalakhir = strtotime('-2 month', strtotime($periode . '-01'));
$tanggalakhir = date('Y-m-t', $tanggalakhir);
$panen = 'select kodeorg,tanggal from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '    where  tanggal = \'' . $tanggalakhir . '\' and kodeorg like \'' . $kdUnit . '%\'';

if ($afdId != '') {
	$panen = 'select kodeorg,tanggal from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '            where  tanggal = \'' . $tanggalakhir . '\' and kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($query = mysql_query($panen)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodeorg']][$res['tanggal']] = 'P';
}

$panen = 'select kodeorg,tanggal from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '    where  tanggal like \'' . $periodelalu . '%\' and kodeorg like \'' . $kdUnit . '%\'';

if ($afdId != '') {
	$panen = 'select kodeorg,tanggal from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '            where  tanggal like \'' . $periodelalu . '%\' and kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($query = mysql_query($panen)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kodeorgArr[$res['kodeorg']] = $res['kodeorg'];
	$tanggallaluArr[$res['tanggal']] = $res['tanggal'];
	$dzArr[$res['kodeorg']][$res['tanggal']] = 'P';
}

$panen = 'select kodeorg,tanggal from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '    where  tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\'';

if ($afdId != '') {
	$panen = 'select kodeorg,tanggal from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '    where  tanggal like \'' . $periode . '%\' and kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($query = mysql_query($panen)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$tanggalArr[$res['tanggal']] = $res['tanggal'];
}

$panen = 'select kodeorg,tanggal,tahuntanam from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '    where (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $kdUnit . '%\'';

if ($afdId != '') {
	$panen = 'select kodeorg,tanggal,tahuntanam from ' . $dbname . '.kebun_interval_panen_vw' . "\r\n" . '    where (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($query = mysql_query($panen)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kodeorgArr[$res['kodeorg']] = $res['kodeorg'];
	$tanggalsdArr[$res['tanggal']] = $res['tanggal'];
	$dzArr[$res['kodeorg']][$res['tanggal']] = 'P';
	$ttArr[$res['kodeorg']] = $res['tahuntanam'];
}

$dzRot = array();

if (!empty($kodeorgArr)) {
	foreach ($kodeorgArr as $koko) {
		if (!empty($tanggallaluArr)) {
			foreach ($tanggallaluArr as $tata) {
				$kemarin = strtotime('-1 day', strtotime($tata));
				$kemarin = date('Y-m-d', $kemarin);

				if (($dzArr[$koko][$tata] == 'P') && ($dzArr[$koko][$kemarin] != 'P')) {
					$dzRot[$koko] += 'bl';
				}
			}
		}

		if (!empty($tanggalArr)) {
			foreach ($tanggalArr as $tata) {
				$kemarin = strtotime('-1 day', strtotime($tata));
				$kemarin = date('Y-m-d', $kemarin);

				if (($dzArr[$koko][$tata] == 'P') && ($dzArr[$koko][$kemarin] != 'P')) {
					$dzRot[$koko] += 'bi';
				}
			}
		}

		if (!empty($tanggalsdArr)) {
			foreach ($tanggalsdArr as $tata) {
				$kemarin = strtotime('-1 day', strtotime($tata));
				$kemarin = date('Y-m-d', $kemarin);

				if (($dzArr[$koko][$tata] == 'P') && ($dzArr[$koko][$kemarin] != 'P')) {
					$dzRot[$koko] += 'sd';
				}
			}
		}
	}
}

$sRotasiBudget = 'select distinct rotasi,kodeorg from ' . $dbname . '.bgt_budget ' . "\r\n" . '                where tahunbudget=\'' . $tahun . '\' and kodeorg like \'' . $kdUnit . '%\' and kegiatan=611010101';

if ($afdId != '') {
	$sRotasiBudget = 'select distinct rotasi,kodeorg from ' . $dbname . '.bgt_budget ' . "\r\n" . '                where tahunbudget=\'' . $tahun . '\' and kodeorg like \'' . $afdId . '%\' and kegiatan=611010101';
}

#exit(mysql_error());
($qRotasiBudget = mysql_query($sRotasiBudget)) || true;

while ($rRotasiBudget = mysql_fetch_assoc($qRotasiBudget)) {
	@$dzRot[$rRotasiBudget['kodeorg']] += 'bib';
	$dzRot[$rRotasiBudget['kodeorg']] += 'sdb';
}

if (!empty($kodeorgArr)) {
	asort($kodeorgArr);
}

$varCek = count($kodeorgArr);

if ($varCek < 1) {
	exit('Error: No data found');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>07. ' . $_SESSION['lang']['rotasi'] . ' ' . $_SESSION['lang']['panen'] . '</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=5 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=8>' . $_SESSION['lang']['rotasi'] . ' ' . $_SESSION['lang']['panen'] . ' (KALI(Times))</td></tr>';
$tab .= '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['blok'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['thntanam'] . '</td><td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['bulanlalu'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=2>' . $_SESSION['lang']['budget'] . '</td><td ' . $bgcoloraja . ' colspan=2>' . $_SESSION['lang']['realisasi'] . '</td>';
$tab .= '<td  ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['ratarata'] . ' ' . $_SESSION['lang']['rotasi'] . '/' . $_SESSION['lang']['bulan'] . '</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . ' </td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . ' </td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . ' </td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . ' </td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';

foreach ($kodeorgArr as $lsBlok) {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $lsBlok . '</td>';
	$tab .= '<td align=right>' . $ttArr[$lsBlok] . '</td>';
	$tab .= '<td align=right>' . number_format($dzRot[$lsBlok]['bl'], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dzRot[$lsBlok]['bib'], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dzRot[$lsBlok]['sdb'], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dzRot[$lsBlok]['bi'], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dzRot[$lsBlok]['sd'], 2) . '</td>';
	@$rtBln[$lsBlok] = $dzRot[$lsBlok]['sd'] / intval($bulan);
	$tab .= '<td align=right>' . number_format($rtBln[$lsBlok], 2) . '</td>';
	$totBlnLalu += $dzRot[$lsBlok]['bl'];
	$totBudgetBi += $dzRot[$lsBlok]['bib'];
	$totBudgetSbi += $dzRot[$lsBlok]['sdb'];
	$totRealBi += $dzRot[$lsBlok]['bi'];
	$totRealSbi += $dzRot[$lsBlok]['sd'];
	$totRata += $rtBln[$lsBlok];
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '<td  align=right>' . number_format($totBlnLalu, 2) . '</td>';
$tab .= '<td  align=right>' . number_format($totBudgetBi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totBudgetSbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totRealBi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totRealSbi, 2) . '</td>';
$tab .= '<td align=right></td>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'rotasipotongbuah_' . $dte;
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
			$this->Cell($width, $height, strtoupper('07. ' . $_SESSION['lang']['rotasi'] . ' ' . $_SESSION['lang']['panen']), 0, 1, 'L');
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
			$this->Cell(360, $height, $_SESSION['lang']['rotasi'] . ' ' . $_SESSION['lang']['panen'] . '(kali(Times))', TBLR, 1, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['blok'], TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['tahun'], TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bulanlalu'], TLR, 0, 'C', 1);
			$this->Cell(70, $height, $_SESSION['lang']['anggaran'], TBLR, 0, 'C', 1);
			$this->Cell(70, $height, $_SESSION['lang']['realisasi'], TBLR, 0, 'C', 1);
			$this->Cell(70, $height, $_SESSION['lang']['ratarata'], TLR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['tanam'], BLR, 0, 'C', 1);
			$this->Cell(50, $height, '', BLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(70, $height, $_SESSION['lang']['ratarata'] . '/' . $_SESSION['lang']['bulan'], BLR, 1, 'C', 1);
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

	foreach ($kodeorgArr as $lsBlok) {
		$pdf->Cell(50, $height, $lsBlok, TBLR, 0, 'L', 1);
		$pdf->Cell(50, $height, $ttArr[$lsBlok], TBLR, 0, 'C', 1);
		$pdf->Cell(50, $height, number_format($dzRot[$lsBlok]['bl'], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(35, $height, number_format($dzRot[$lsBlok]['bib'], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(35, $height, number_format($dzRot[$lsBlok]['sdb'], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(35, $height, number_format($dzRot[$lsBlok]['bi'], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(35, $height, number_format($dzRot[$lsBlok]['sd'], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(70, $height, number_format($rtBln[$lsBlok], 2), TBLR, 1, 'R', 1);
	}

	$pdf->Cell(100, $height, $_SESSION['lang']['total'], TBLR, 0, 'L', 1);
	$pdf->Cell(50, $height, number_format($totBlnLalu, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(35, $height, number_format($totBudgetBi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(35, $height, number_format($totBudgetSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(35, $height, number_format($totRealBi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(35, $height, number_format($totRealSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(70, $height, '', TBLR, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
