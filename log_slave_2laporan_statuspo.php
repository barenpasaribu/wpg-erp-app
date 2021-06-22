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

$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$arrDt = array('Head Office', 'Local');
$statClose = makeOption($dbname, 'log_poht', 'nopo,closed');
$_POST['ptId'] == '' ? $ptId = $_GET['ptId'] : $ptId = $_POST['ptId'];
$_POST['statId'] == '' ? $statId = $_GET['statId'] : $statId = $_POST['statId'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];

if ($ptId != '') {
	$whr .= ' and kodeorg=\'' . $ptId . '\'';
	$transdt .= 'and kodept=\'' . $ptId . '\'';
}
else {
	exit('Error:' . $_SESSION['lang']['pt'] . ' Tidak boleh kosong');
}

if ($statId != '') {
	$whr .= ' and lokalpusat=\'' . $statId . '\'';
}

if ($periode != '') {
	$whr .= ' and substr(tanggal,1,7)=\'' . $periode . '\'';
	$transdt .= 'and substr(tanggal,1,7)=\'' . $periode . '\'';
}
else {
	exit('Error:' . $_SESSION['lang']['periode'] . ' Tidak boleh kosong');
}

$sdtPo = 'select distinct nopo,tanggal,lokalpusat from ' . $dbname . '.log_poht' . "\r\n" . '        where nopo!=\'\' ' . $whr . ' order by nopo asc';

#exit(mysql_error($conn));
($qdtPo = mysql_query($sdtPo)) || true;

while ($rGetPo = mysql_fetch_assoc($qdtPo)) {
	$data[] = $rGetPo['nopo'];
	$lstTgl[$rGetPo['nopo']] = $rGetPo['tanggal'];
	$lstStat[$rGetPo['nopo']] = $rGetPo['lokalpusat'];
}

$sBapb = 'select sum(jumlah) as jumlah,notransaksi,nopo,kodebarang from ' . $dbname . '.log_transaksi_vw where `notransaksi` like \'%GR%\' ' . $transdt . ' group by nopo,kodebarang order by nopo asc';

#exit(mysql_error($conn));
($qBapb = mysql_query($sBapb)) || true;

while ($rBapb = mysql_fetch_assoc($qBapb)) {
	$lstJmlh[$rBapb['nopo']][$rBapb['kodebarang']] = $rBapb['jumlah'];
	$lstNotrans[$rBapb['nopo']][$rBapb['kodebarang']] = $rBapb['notransaksi'];
}

$brsdt = count($data);
$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
}

$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>';
$tab .= '<tr>';
$tab .= '<td ' . $bgcoloraja . '>No.</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nopo'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['status'] . ' PO</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . ' PO</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['status'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['namabarang'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jumlah'] . ' PO</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . ' </td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bapb'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jumlah'] . ' ' . $_SESSION['lang']['diterima'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['sisa'] . '</td>';
$tab .= '</tr></thead><tbody>';

if ($brsdt != 0) {
	foreach ($data as $dataNopo) {
		$no += 1;
		$afdC = false;
		$blankC = false;
		$sdtPo = 'select distinct nopo,namabarang,kodebarang,lokalpusat,jumlahpesan,nopo,tanggal,satuan from ' . $dbname . '.log_po_vw where nopo=\'' . $dataNopo . '\'';

		#exit(mysql_error($conn));
		($qdtPo = mysql_query($sdtPo)) || true;

		while ($rdtPo = mysql_fetch_assoc($qdtPo)) {
			$sRow = 'select nopo from ' . $dbname . '.log_podt where nopo=\'' . $dataNopo . '\'';

			#exit(mysql_error());
			($qRow = mysql_query($sRow)) || true;
			$rRow = mysql_num_rows($qRow);
			$diIsi = $_SESSION['lang']['open'];

			if ($statClose[$dataNopo] == 1) {
				$diIsi = $_SESSION['lang']['tutup'];
			}

			$tmpRow = $rRow - 1;
			$tab .= '<tr class=\'rowcontent\'>';

			if ($afdC == false) {
				$tab .= '<td>' . $no . '</td>';
				$tab .= '<td>' . $dataNopo . '</td>';
				$tab .= '<td>' . $diIsi . '</td>';
				$tab .= '<td>' . tanggalnormal($lstTgl[$dataNopo]) . '</td>';
				$tab .= '<td>' . $arrDt[$lstStat[$dataNopo]] . '</td>';
				$afdC = true;
			}
			else if ($blankC == false) {
				$tab .= '<td rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$tab .= '<td  rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$tab .= '<td  rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$tab .= '<td  rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$tab .= '<td  rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$blankC = true;
			}

			$sisaJmlh[$rdtPo['nopo']][$rdtPo['kodebarang']] = $rdtPo['jumlahpesan'] - $lstJmlh[$rdtPo['nopo']][$rdtPo['kodebarang']];
			$tab .= '<td>' . $rdtPo['kodebarang'] . '</td>';
			$tab .= '<td>' . $rdtPo['namabarang'] . '</td>';
			$tab .= '<td align=right>' . $rdtPo['jumlahpesan'] . '</td>';
			$tab .= '<td align=center>' . $rdtPo['satuan'] . '</td>';
			$tab .= '<td>' . $lstNotrans[$rdtPo['nopo']][$rdtPo['kodebarang']] . '</td>';
			$tab .= '<td  align=right>' . $lstJmlh[$rdtPo['nopo']][$rdtPo['kodebarang']] . '</td>';
			$tab .= '<td  align=right>' . $sisaJmlh[$rdtPo['nopo']][$rdtPo['kodebarang']] . '</td>';
			$tab .= '</tr>';
		}
	}
}
else {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=12>' . $_SESSION['lang']['dataempty'] . '</td>';
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
	$nop_ = 'daftrpo_' . $dte;
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
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $tot;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('25.3 TRANSIT KENDARAAN'), 0, 1, 'L');
			$this->Cell($width, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$height = 10;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 5);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(15, $height, 'No.', TLR, 0, 'C', 1);
			$this->Cell(35, $height, 'Jenis', TLR, 0, 'C', 1);
			$this->Cell(80, $height, 'Nama KEND', TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'KODE MESIN', TLR, 0, 'C', 1);
			$this->Cell(25, $height, 'THN ', TLR, 0, 'C', 1);
			$this->Cell(45, $height, 'ANGGARAN ', TLR, 0, 'C', 1);
			$this->Cell(80, $height, 'REALISASI  PEMAKAIAN  FISIK', TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'RASIO ', TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'ANGGARAN ', TLR, 0, 'C', 1);
			$this->Cell(280, $height, 'Realisasi Biaya Operasi (000) ', TLR, 0, 'C', 1);
			$this->Cell(80, $height, 'COST / UNIT', TLR, 1, 'C', 1);
			$this->Cell(15, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(35, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(80, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(50, $height, 'MESIN/KEND ', LR, 0, 'C', 1);
			$this->Cell(25, $height, 'PER', LR, 0, 'C', 1);
			$this->Cell(45, $height, 'SETAHUN ', LR, 0, 'C', 1);
			$this->Cell(40, $height, 'KM', TLR, 0, 'C', 1);
			$this->Cell(40, $height, 'LTR', TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'S/D BLN INI', LR, 0, 'C', 1);
			$this->Cell(50, $height, 'SETAHUN', LR, 0, 'C', 1);
			$this->Cell(140, $height, 'BI', TLR, 0, 'C', 1);
			$this->Cell(140, $height, 'SBI', TLR, 0, 'C', 1);
			$this->Cell(40, $height, 'ANGGARAN', TLR, 0, 'C', 1);
			$this->Cell(40, $height, 'REALISASI', TLR, 1, 'C', 1);
			$this->Cell(15, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(35, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(80, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, '', BLR, 0, 'C', 1);
			$this->Cell(25, $height, 'OLEHAN', BLR, 0, 'C', 1);
			$this->Cell(45, $height, '(KM)', BLR, 0, 'C', 1);
			$this->Cell(20, $height, 'BI', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'SBI', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'BI', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'SBI', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, '(LTR/KM)', BLR, 0, 'C', 1);
			$this->Cell(50, $height, ' (Rp.000,-) ', BLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Gaji', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 3.5);
			$this->Cell(20, $height, 'Pre/Lembur', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'BBM/Plumas', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'S.Cadang', TBLR, 0, 'L', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Reprasi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Asuransi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Total', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Gaji', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 3.5);
			$this->Cell(20, $height, 'Pre/Lembur', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'BBM/Plumas', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'S.Cadang', TBLR, 0, 'L', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Reprasi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Asuransi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Total', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 5);
			$this->Cell(40, $height, 'SETAHUN ', BLR, 0, 'C', 1);
			$this->Cell(20, $height, 'BI', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'SBI', TBLR, 1, 'C', 1);
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
	$pdf->SetFont('Arial', 'B', 5);
	$i = 0;

	foreach ($dtKend as $lstKend) {
		$i += 1;
		$pdf->Cell(15, $height, $i, TBLR, 0, 'C', 1);
		$pdf->Cell(35, $height, $lsJenis[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(80, $height, $lsNama[$lstKend], TBLR, 0, 'L', 1);
		$pdf->Cell(50, $height, $lstKend, TBLR, 0, 'L', 1);
		$pdf->Cell(25, $height, $lsThnPerolehan[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(45, $height, number_format($lsKm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtBi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtSbi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($jmlhBbm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($jmlhBbmSbi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($rasioDt[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtAnggrn[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biGaji[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biLembur[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biBbm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biSukuCdng[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biReparasi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biAsuransi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($rTotal[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiGaji[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiLembur[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiBbm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiSukuCdng[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiReparasi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiAsuransi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($rTotalSbi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(40, $height, number_format($zData[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($aaDta[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($abDta[$lstKend], 0), TBLR, 1, 'R', 1);
	}

	$pdf->Output();
	break;
}

?>
