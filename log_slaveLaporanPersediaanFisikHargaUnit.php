<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['unitDt'] == '' ? $unitDt = $_GET['unitDt'] : $unitDt = $_POST['unitDt'];
$_POST['gudang'] == '' ? $gudang = $_GET['gudang'] : $gudang = $_POST['gudang'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmSat = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

if ($proses != 'getGudang') {
	if ($unitDt == '') {
		exit('Error:Unit Tidak Boleh Kosong');
	}

	if ($gudang != '') {
		$where .= 'and kodegudang like \'' . $gudang . '%\'';
	}

	if ($periode != '') {
		$where .= ' and periode=\'' . $periode . '\'';
	}

	if ($proses == 'excel') {
		$tab .= '<table class=sortable cellspacing=1 border=1 width=100%>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . '    <tr>' . "\r\n\t\t\t" . '  <td rowspan=2  bgcolor=#DEDEDE align=center>No.</td>' . "\r\n\t\t\t" . '  <td rowspan=2  bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n\t\t\t" . '  <td rowspan=2  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t" . '  <td rowspan=2  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '  <td rowspan=2  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t" . '  <td colspan=3  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n\t\t\t" . '  <td colspan=3  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n\t\t\t" . '  <td colspan=3  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n\t\t\t" . '  <td colspan=3  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['saldoakhir'] . '</td>' . "\r\n\t\t\t" . '</tr>' . "\r\n\t\t\t" . '<tr>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t" . '   <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n\t\t\t" . '</tr>   ' . "\r\n\t\t" . ' </thead><tbody>';
	}

	if ($periode == '') {
		$sData = 'select distinct sum(saldoawalqty) as saldoawalqty,sum(hargaratasaldoawal) as hargaratasaldoawal,' . "\r\n" . '                sum(nilaisaldoawal) as nilaisaldoawal,sum(qtymasuk) as qtymasuk,sum(qtymasukxharga) as qtymasukxharga,' . "\r\n" . '                sum(qtykeluar) as qtykeluar,sum(saldoakhirqty) as saldoakhirqty,sum(saldoakhirqty) as saldoakhirqty,' . "\r\n" . '                sum(nilaisaldoakhir) as nilaisaldoakhir,periode,kodebarang from ' . $dbname . '.log_5saldobulanan where kodegudang!=\'\' ' . $where . "\r\n" . '                group by periode,kodebarang';

		#exit(mysql_error($conn));
		($qData = mysql_query($sData)) || true;

		while ($rData = mysql_fetch_assoc($qData)) {
			$dtPeriode[$rData['periode']] = $rData['periode'];
			$lstKdBrg[$rData['kodebarang']] = $rData['kodebarang'];
			$dtKdBarang[$rData['periode']][$rData['kodebarang']] = $rData['kodebarang'];
			$dtAwal[$rData['periode'] . $rData['kodebarang']] = $rData['saldoawalqty'];
			$dtNilAwal[$rData['periode'] . $rData['kodebarang']] = $rData['nilaisaldoawal'];
			@$dtHrgAwal[$rData['periode'] . $rData['kodebarang']] = $dtNilAwal[$rData['periode'] . $rData['kodebarang']] / $dtAwal[$rData['periode'] . $rData['kodebarang']];
			$dtMasuk[$rData['periode'] . $rData['kodebarang']] = $rData['qtymasuk'];
			$dtNilMasuk[$rData['periode'] . $rData['kodebarang']] = $rData['qtymasukxharga'];
			@$dtHrgMasuk[$rData['periode'] . $rData['kodebarang']] = $dtNilMasuk[$rData['periode'] . $rData['kodebarang']] / $dtMasuk[$rData['periode'] . $rData['kodebarang']];
			$dtKeluar[$rData['periode'] . $rData['kodebarang']] = $rData['qtykeluar'];
			$dtNilKeluar[$rData['periode'] . $rData['kodebarang']] = $rData['saldoakhirqty'];
			@$dtHrgKeluar[$rData['periode'] . $rData['kodebarang']] = $dtNilKeluar[$rData['periode'] . $rData['kodebarang']] / $dtKeluar[$rData['periode'] . $rData['kodebarang']];
			$dtAkhir[$rData['periode'] . $rData['kodebarang']] = $rData['saldoakhirqty'];
			$dtNilAkhir[$rData['periode'] . $rData['kodebarang']] = $rData['nilaisaldoakhir'];
			@$dtHrgAkhir[$rData['periode'] . $rData['kodebarang']] = $dtNilAkhir[$rData['periode'] . $rData['kodebarang']] / $dtAkhir[$rData['periode'] . $rData['kodebarang']];
		}
	}
	else {
		$sData = 'select distinct * from ' . $dbname . '.log_5saldobulanan where kodegudang!=\'\' ' . $where . '';

		#exit(mysql_error($conn));
		($qData = mysql_query($sData)) || true;

		while ($rData = mysql_fetch_assoc($qData)) {
			$dtPeriode[$rData['periode']] = $rData['periode'];
			$lstKdBrg[$rData['kodebarang']] = $rData['kodebarang'];
			$dtKdBarang[$rData['periode']][$rData['kodebarang']] = $rData['kodebarang'];
			$dtAwal[$rData['periode'] . $rData['kodebarang']] = $rData['saldoawalqty'];
			$dtNilAwal[$rData['periode'] . $rData['kodebarang']] = $rData['nilaisaldoawal'];
			$dtHrgAwal[$rData['periode'] . $rData['kodebarang']] = $rData['hargaratasaldoawal'];
			$dtMasuk[$rData['periode'] . $rData['kodebarang']] = $rData['qtymasuk'];
			$dtNilMasuk[$rData['periode'] . $rData['kodebarang']] = $rData['qtymasukxharga'];
			@$dtHrgMasuk[$rData['periode'] . $rData['kodebarang']] = $dtNilMasuk[$rData['periode'] . $rData['kodebarang']] / $dtMasuk[$rData['periode'] . $rData['kodebarang']];
			$dtKeluar[$rData['periode'] . $rData['kodebarang']] = $rData['qtykeluar'];
			$dtNilKeluar[$rData['periode'] . $rData['kodebarang']] = $rData['saldoakhirqty'];
			@$dtHrgKeluar[$rData['periode'] . $rData['kodebarang']] = $dtNilKeluar[$rData['periode'] . $rData['kodebarang']] / $dtKeluar[$rData['periode'] . $rData['kodebarang']];
			$dtAkhir[$rData['periode'] . $rData['kodebarang']] = $rData['saldoakhirqty'];
			$dtNilAkhir[$rData['periode'] . $rData['kodebarang']] = $rData['nilaisaldoakhir'];
			@$dtHrgAkhir[$rData['periode'] . $rData['kodebarang']] = $rData['nilaisaldoakhir'];
		}
	}

	$chekDt = count($dtPeriode);

	if ($chekDt == 0) {
		exit('Error:Data Kosong');
	}

	foreach ($dtPeriode as $dtIsi) {
		foreach ($lstKdBrg as $dtBrg) {
			if ($dtKdBarang[$dtIsi][$dtBrg] != '') {
				$no += 1;
				$tab .= '<tr class=rowcontent>';
				$tab .= '<td>' . $no . '</td>';
				$tab .= '<td>' . $dtIsi . '</td>';
				$tab .= '<td>' . $dtKdBarang[$dtIsi][$dtBrg] . '</td>';
				$tab .= '<td>' . $optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td>' . $optNmSat[$dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right class=firsttd>' . $dtAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right>' . number_format($dtHrgAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($dtNilAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right>' . $dtMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right>' . number_format($dtHrgMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($dtNilMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right  class=firsttd>' . $dtKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right>' . number_format($dtHrgKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($dtNilKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right  class=firsttd>' . $dtAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right>' . number_format($dtHrgAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($dtNilAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2) . '</td>';
			}
		}
	}
}

switch ($proses) {
case 'getGudang':
	//$optUnit = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$optUnit = '<option value=\'\'>--</option>';
	$sUnit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where ' . "\r\n" . '            induk like \'' . $unitDt . '%\' and tipe like \'GUDANG%\' order by namaorganisasi asc';

	#exit(mysql_error($conn));
	($qUnit = mysql_query($sUnit)) || true;

	while ($rUnit = mysql_fetch_assoc($qUnit)) {
		$optUnit .= '<option value=\'' . $rUnit['kodeorganisasi'] . '\'>' . $rUnit['namaorganisasi'] . '</option>';
	}

	echo $optUnit;
	break;

case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= '</tbody></table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'lapPersediaanFisikUnitHrg_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n\t" . '</script>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $namapt;
			global $pt;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(20, 5, $namapt, '', 1, 'L');
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(190, 5, strtoupper($_SESSION['lang']['laporanstok']), 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell(140, 5, ' ', '', 0, 'R');
			$this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
			$this->Cell(140, 5, ' ', '', 0, 'R');
			$this->Cell(15, 5, $_SESSION['lang']['page'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
			$this->Cell(140, 5, 'UNIT:' . $pt . '-' . $gudang, '', 0, 'L');
			$this->Cell(15, 5, 'User', '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
			$this->SetFont('Arial', '', 4);
			$this->Cell(5, 8, 'No.', 1, 0, 'C');
			$this->Cell(15, 8, $_SESSION['lang']['periode'], 1, 0, 'C');
			$this->Cell(15, 8, $_SESSION['lang']['kodebarang'], 1, 0, 'C');
			$this->Cell(40, 8, substr($_SESSION['lang']['namabarang'], 0, 30), 1, 0, 'C');
			$this->Cell(5, 8, $_SESSION['lang']['satuan'], 1, 0, 'C');
			$this->Cell(27, 4, $_SESSION['lang']['saldoawal'], 1, 0, 'C');
			$this->Cell(27, 4, $_SESSION['lang']['masuk'], 1, 0, 'C');
			$this->Cell(27, 4, $_SESSION['lang']['keluar'], 1, 0, 'C');
			$this->Cell(27, 4, $_SESSION['lang']['saldo'], 1, 1, 'C');
			$this->SetX(90);
			$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
			$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 1, 'C');
		}
	}

	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddPage();

	foreach ($dtPeriode as $dtIsi) {
		foreach ($lstKdBrg as $dtBrg) {
			if ($dtKdBarang[$dtIsi][$dtBrg] != '') {
				$nor += 1;
				$pdf->Cell(5, 4, $nor, 0, 0, 'C');
				$pdf->Cell(15, 4, $dtIsi, 0, 0, 'C');
				$pdf->Cell(15, 4, $dtKdBarang[$dtIsi][$dtBrg], 0, 0, 'L');
				$pdf->Cell(40, 4, $optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]], 0, 0, 'L');
				$pdf->Cell(5, 4, $optNmSat[$dtKdBarang[$dtIsi][$dtBrg]], 0, 0, 'L');
				$pdf->Cell(9, 4, number_format($dtAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtHrgAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtNilAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtHrgMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtNilMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtHrgKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtNilKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtHrgAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 0, 'R');
				$pdf->Cell(9, 4, number_format($dtNilAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 0, 1, 'R');
			}
		}
	}

	$pdf->Output();
	break;
}

?>
