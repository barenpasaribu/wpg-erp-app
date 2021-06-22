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
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
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
$whre = 'and substr(kodebarang,1,3) in (\'311\', \'312\', \'313\') and kodegudang=\'' . $kdUnit . '\'' . "\r\n" . ' group by kodebarang order by kodebarang asc';
$sData = 'select sum(qtymasuk) as penerimaan,sum(qtykeluar) as pengeluaran,kodebarang from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . 'where periode=\'' . $periode . '\' ' . $whre . ' ';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$dtBarang[] = $rData['kodebarang'];
	$dtTerima += $rData['kodebarang'];
	$dtKeluar += $rData['kodebarang'];
}

$sData2 = 'select sum(qtymasuk) as penerimaan,sum(qtykeluar) as pengeluaran,kodebarang from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . 'where periode between \'' . $thn[0] . '-01\' and \'' . $periode . '\' ' . $whre . ' ';

#exit(mysql_error());
($qData2 = mysql_query($sData2)) || true;

while ($rData2 = mysql_fetch_assoc($qData2)) {
	$dtBarangSbi[] = $rData2['kodebarang'];
	$dtTerimaSbi += $rData2['kodebarang'];
	$dtKeluarSbi += $rData2['kodebarang'];
}

$sPosAwalThn = 'SELECT sum( `saldoakhirqty` ) AS saldoAkhir, kodebarang from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . 'where substr(kodebarang,1,3) in (\'311\', \'312\', \'313\') and periode=\'' . $periode . '\' and kodegudang=\'' . $kdUnit . '\'' . "\r\n" . ' group by kodebarang order by kodebarang asc';

#exit(mysql_error());
($qPosAwalThn = mysql_query($sPosAwalThn)) || true;

while ($rPosAwalThn = mysql_fetch_assoc($qPosAwalThn)) {
	$dtAkhrBln += $rPosAwalThn['kodebarang'];
}

$sPosAwalThn = 'SELECT sum( `saldoakhirqty` ) AS saldoAkhir, kodebarang from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . 'where substr(kodebarang,1,3) in (\'311\', \'312\', \'313\') and periode=\'' . $thn[0] . '-' . $blnLalu . '\' and kodegudang=\'' . $kdUnit . '\'' . "\r\n" . ' group by kodebarang order by kodebarang asc';

#exit(mysql_error());
($qPosAwalThn = mysql_query($sPosAwalThn)) || true;

while ($rPosAwalThn = mysql_fetch_assoc($qPosAwalThn)) {
	$dtBlnLalu += $rPosAwalThn['kodebarang'];
}

$sPosAwalThn = 'SELECT sum( `saldoakhirqty` ) AS saldoAkhir, kodebarang from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . 'where substr(kodebarang,1,3) in (\'311\', \'312\', \'313\') and periode=\'' . $thnLalu . '-12\' and kodegudang=\'' . $kdUnit . '\'' . "\r\n" . ' group by kodebarang order by kodebarang asc';

#exit(mysql_error());
($qPosAwalThn = mysql_query($sPosAwalThn)) || true;

while ($rPosAwalThn = mysql_fetch_assoc($qPosAwalThn)) {
	$dtAwalThn += $rPosAwalThn['kodebarang'];
}

$varCek = count($dtBarang);

if ($varCek < 1) {
	exit('Error:No data found');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>13.2 :' . strtoupper($_SESSION['lang']['stok']) . ' ' . strtoupper($_SESSION['lang']['pupuk']) . ', AGROCHEMICAL</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['posisiawaltahun'] . '</td>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['sdbulanlalu'] . '</td>' . "\r\n" . '        <td colspan=2>' . $_SESSION['lang']['penerimaanbarang'] . '</td>' . "\r\n" . '        <td colspan=2>' . $_SESSION['lang']['pemakaianBarang'] . '</td>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['saldoakhir'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr><td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['sbi'] . '</td><td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['sbi'] . '</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';

foreach ($dtBarang as $lstBarang => $lstDtBarang) {
	if ($klmprBrg != substr($lstDtBarang, 0, 3)) {
		$klmprBrg = substr($lstDtBarang, 0, 3);
		$tab .= '<tr class=rowcontent><td>';
		$tab .= '' . $optKelompok[$klmprBrg] . '</td><td colspan=7>&nbsp;</td></tr>';
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $optNmBrg[$lstDtBarang] . '</td>';
	$tab .= '<td align=right>' . number_format($dtAwalThn[$lstDtBarang], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtBlnLalu[$lstDtBarang], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtTerima[$lstDtBarang], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtTerimaSbi[$lstDtBarang], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtKeluar[$lstDtBarang], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtKeluarSbi[$lstDtBarang], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtAkhrBln[$lstDtBarang], 2) . '</td>';
	$tab .= '</tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'Stock' . $_SESSION['lang']['pupuk'] . $dte;
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
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('13.2 ' . strtoupper($_SESSION['lang']['stok']) . ' ' . strtoupper($_SESSION['lang']['pupuk']) . ', AGROCHEMICAL'), 0, 1, 'L');
			$this->Cell(485, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(120, $height, $_SESSION['lang']['namabarang'], TLR, 0, 'C', 1);
			$this->Cell(80, $height, $_SESSION['lang']['posisiawaltahun'], TLR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['sdbulanlalu'], TLR, 0, 'C', 1);
			$this->Cell(100, $height, $_SESSION['lang']['penerimaanbarang'], TLR, 0, 'C', 1);
			$this->Cell(100, $height, $_SESSION['lang']['pemakaianBarang'], TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['saldoakhir'], TLR, 1, 'C', 1);
			$this->Cell(120, $height, '', BLR, 0, 'C', 1);
			$this->Cell(80, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(60, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['saldoakhir'], BLR, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('P', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);

	foreach ($dtBarang as $lstBarang => $lstBarangDt) {
		if ($klmprBrg2 != substr($lstBarangDt, 0, 3)) {
			$klmprBrg2 = substr($lstBarangDt, 0, 3);
			$pdf->Cell(120, $height, $optKelompok[$klmprBrg2], TBLR, 0, 'L', 1);
			$pdf->Cell(390, $height, ' ', TBLR, 1, 'C', 1);
		}

		$pdf->Cell(120, $height, $optNmBrg[$lstBarangDt], TBLR, 0, 'L', 1);
		$pdf->Cell(80, $height, number_format($dtAwalThn[$lstBarangDt], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(60, $height, number_format($dtBlnLalu[$lstBarangDt], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtTerima[$lstBarangDt], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtTerimaSbi[$lstBarangDt], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtKeluar[$lstBarangDt], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtKeluarSbi[$lstBarangDt], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtAkhrBln[$lstBarangDt], 2), TBLR, 1, 'R', 1);
	}

	$pdf->Output();
	break;
}

?>
