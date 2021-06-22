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

$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKelompok = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
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
		$thnLalu = $thn[0] - 1;
	}
	else {
		$blnLalu = '0' . $bln;
	}
}
else {
	$blnLalu = $bln - 1;
}

$thnLalu = $thn[0] - 1;
$sKodeorg = 'select distinct substr(kodeorg,1,6) as afd from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '          where substr(tanggal,1,7)=\'' . $periode . '\' and substr(kodeorg,1,4)=\'' . $kdUnit . '\' order by kodeorg  asc';

if ($afdId != '') {
	$sKodeorg = 'select distinct substr(kodeorg,1,6) as afd from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '          where substr(tanggal,1,7)=\'' . $periode . '\' and substr(kodeorg,1,6)=\'' . $afdId . '\' order by kodeorg  asc';
}

#exit(mysql_error());
($qKodeorg = mysql_query($sKodeorg)) || true;

while ($rKodeorg = mysql_fetch_assoc($qKodeorg)) {
	$dtKodeorg[] = $rKodeorg['afd'];
}

$sKodeorg = 'select distinct kodebarang from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '          where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(kodebarang,1,3) in (\'311\', \'312\', \'313\')  order by kodebarang asc';

if ($afdId != '') {
	$sKodeorg = 'select distinct kodebarang from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '          where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(kodebarang,1,3) in (\'311\', \'312\', \'313\')  order by kodebarang asc';
}

#exit(mysql_error());
($qKodeorg = mysql_query($sKodeorg)) || true;

while ($rKodeorg = mysql_fetch_assoc($qKodeorg)) {
	$dtBarang[] = $rKodeorg['kodebarang'];
}

$sBi = 'select left(kodeorg,6) as afd, sum(kwantitas) as jumlah,kodebarang from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '      where kodeorg like \'' . $kdUnit . '%\' and tanggal like \'' . $periode . '%\' group by afd,kodebarang order by kodeorg asc';

if ($afdId != '') {
	$sBi = 'select left(kodeorg,6) as afd, sum(kwantitas) as jumlah,kodebarang from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '      where kodeorg like \'' . $afdId . '%\' and tanggal like \'' . $periode . '%\' group by afd,kodebarang order by kodeorg asc';
}

#exit(mysql_error());
($qBi = mysql_query($sBi)) || true;

while ($rBi = mysql_fetch_assoc($qBi)) {
	$dtBrgBI[$rBi['afd']] += $rBi['kodebarang'];
}

$sSBi = 'select left(kodeorg,6) as afd, sum(kwantitas) as jumlah,kodebarang from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '       where kodeorg like \'' . $kdUnit . '%\' and (tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) group by afd,kodebarang';

if ($afdId != '') {
	$sSBi = 'select left(kodeorg,6) as afd, sum(kwantitas) as jumlah,kodebarang from ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '       where kodeorg like \'' . $afdId . '%\' and (tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) group by afd,kodebarang';
}

#exit(mysql_error());
($qSBi = mysql_query($sSBi)) || true;

while ($rSBi = mysql_fetch_assoc($qSBi)) {
	$dtBrgSBI[$rSBi['afd']] += $rSBi['kodebarang'];
}

$varCek = count($dtBarang);

if ($varCek < 1) {
	exit('Error:No data found');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dtKodeorg);
$da = ($cols * 2) + 1;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>13.2 :' . strtoupper($_SESSION['lang']['distribusi']) . ' ' . strtoupper($_SESSION['lang']['pupuk']) . ', AGROCHEMICAL</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=5 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td colspan=' . $cols . '>' . $_SESSION['lang']['bi'] . '</td>' . "\r\n" . '        <td colspan=' . $cols . '>' . $_SESSION['lang']['sbi'] . '</td>' . "\r\n" . '        </tr><tr>';

if (!empty($dtKodeorg)) {
	foreach ($dtKodeorg as $lstKodeOrg) {
		$tab .= '<td>' . $lstKodeOrg . '</td>';
	}
}

if (!empty($dtKodeorg)) {
	foreach ($dtKodeorg as $lstKodeOrg5) {
		$tab .= '<td>' . $lstKodeOrg5 . '</td>';
	}
}

$tab .= '</tr></thead>' . "\r\n\t" . '<tbody>';

if (!empty($dtBarang)) {
	foreach ($dtBarang as $lstBarang => $lstDtBarang) {
		if ($klmprBrg != substr($lstDtBarang, 0, 3)) {
			$klmprBrg = substr($lstDtBarang, 0, 3);
			$tab .= '<tr class=rowcontent><td>';
			$tab .= '' . $optKelompok[$klmprBrg] . '</td><td colspan=' . $da . '>&nbsp;</td></tr>';
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $optNmBrg[$lstDtBarang] . '</td>';
		$tab .= '<td>' . $optSatuan[$lstDtBarang] . '</td>';

		if (!empty($dtKodeorg)) {
			foreach ($dtKodeorg as $dataKodeorg) {
				$tab .= '<td align=right>' . number_format($dtBrgBI[$dataKodeorg][$lstDtBarang], 2) . '</td>';
			}
		}

		if (!empty($dtKodeorg)) {
			foreach ($dtKodeorg as $dataKodeorg) {
				$tab .= '<td align=right>' . number_format($dtBrgSBI[$dataKodeorg][$lstDtBarang], 2) . '</td>';
			}
		}

		$tab .= '</tr>';
	}
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'distribusi_pupuk_' . $dte;
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
			global $dtBarang;
			global $cols;
			global $lstKodeOrg9;
			global $lstKodeOrg8;
			global $dtKodeorg;
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('13.3 ' . strtoupper($_SESSION['lang']['distribusi']) . ' ' . strtoupper($_SESSION['lang']['pupuk']) . ', AGROCHEMICAL'), 0, 1, 'L');
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
			$wit = 45 * $cols;
			$witda = 45 * $cols;
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(120, $height, $_SESSION['lang']['nama'], TLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['satuan'], TLR, 0, 'C', 1);
			$this->Cell($wit, $height, $_SESSION['lang']['bi'], TLR, 0, 'C', 1);
			$this->Cell($wit, $height, $_SESSION['lang']['sbi'], TLR, 1, 'C', 1);
			$this->Cell(120, $height, $_SESSION['lang']['material'], BLR, 0, 'C', 1);
			$this->Cell(35, $height, ' ', BLR, 0, 'C', 1);

			if (!empty($dtKodeorg)) {
				foreach ($dtKodeorg as $lstKodeOrg8) {
					$this->Cell(45, $height, $lstKodeOrg8, TBLR, 0, 'C', 1);
				}
			}

			if (!empty($dtKodeorg)) {
				foreach ($dtKodeorg as $lstKodeOrg9) {
					$ar += 1;

					if ($ar != $cols) {
						$this->Cell(45, $height, $lstKodeOrg9, TBLR, 0, 'C', 1);
					}
					else {
						$this->Cell(45, $height, $lstKodeOrg9, TBLR, 1, 'C', 1);
					}
				}
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
	$height = 10;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstBarang => $lstDtBarang) {
			if ($klmprBrg2 != substr($lstDtBarang, 0, 3)) {
				$klmprBrg2 = substr($lstDtBarang, 0, 3);
				$pdf->Cell(120, $height, $optKelompok[$klmprBrg2], TBLR, 0, 'L', 1);
				$pdf->Cell(35 + ($cols * 90), $height, $witda, TBLR, 1, 'L', 1);
			}

			$pdf->Cell(120, $height, $optNmBrg[$lstDtBarang], TBLR, 0, 'L', 1);
			$pdf->Cell(35, $height, $optSatuan[$lstDtBarang], TBLR, 0, 'C', 1);
			$ard = 0;

			if (!empty($dtKodeorg)) {
				foreach ($dtKodeorg as $dataKodeorg) {
					$ard += 1;

					if ($ard != $cols) {
						$pdf->Cell(45, $height, number_format($dtBrgBI[$dataKodeorg][$lstDtBarang], 2), TBLR, 0, 'R', 1);
					}
					else {
						$pdf->Cell(45, $height, number_format($dtBrgBI[$dataKodeorg][$lstDtBarang], 2), TBLR, 0, 'R', 1);
					}
				}
			}

			if (!empty($dtKodeorg)) {
				foreach ($dtKodeorg as $dataKodeorg) {
					$ard += 1;

					if ($ard != $cols) {
						$pdf->Cell(45, $height, number_format($dtBrgSBI[$dataKodeorg][$lstDtBarang], 2), TBLR, 0, 'R', 1);
					}
					else {
						$pdf->Cell(45, $height, number_format($dtBrgSBI[$dataKodeorg][$lstDtBarang], 2), TBLR, 1, 'R', 1);
					}
				}
			}

			$pdf->ln();
		}
	}

	$pdf->ln();
	$pdf->Cell(45, $height, $_SESSION['lang']['keterangan'] . ' :', 0, 1, 'L', 1);

	if (!empty($dtKodeorg)) {
		foreach ($dtKodeorg as $dataKodeorg) {
			$pdf->Cell(35, $height, $dataKodeorg . ' = ', 0, 0, 'L', 1);
			$pdf->Cell(45, $height, $optNmOrg[$dataKodeorg], 0, 1, 'L', 1);
		}
	}

	$pdf->Output();
	break;
}

?>
