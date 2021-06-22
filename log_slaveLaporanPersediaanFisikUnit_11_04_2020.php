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
	else {
		exit('Error:Gudang Tidak Boleh Kosong');
	}

	if ($periode != '') {
		$where .= ' and periode=\'' . $periode . '\'';
	}

	if ($proses == 'excel') {
		$tab .= ' <table class=sortable cellspacing=1 border=1 width=100%>' . "\r\n\t" . '     <thead>' . "\r\n" . '                <tr>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>No.</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['sloc'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n" . '                      <td  bgcolor=#DEDEDE  align=center>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                    </tr>  ' . "\r\n" . '             </thead><tbody>';
	}

	$sData = 'select distinct * from ' . $dbname . '.log_5saldobulanan where kodegudang!=\'\' ' . $where . '';
	
	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;
	while ($rData = mysql_fetch_assoc($qData)) {
		$dtPeriode[$rData['periode']] = $rData['periode'];
		$lstKdBrg[$rData['kodebarang']] = $rData['kodebarang'];
		$dtKdBarang[$rData['periode']][$rData['kodebarang']] = $rData['kodebarang'];
		$dtAwal[$rData['periode'] . $rData['kodebarang']] = $rData['saldoawalqty'];
		$dtMasuk[$rData['periode'] . $rData['kodebarang']] = $rData['qtymasuk'];
		$dtKeluar[$rData['periode'] . $rData['kodebarang']] = $rData['qtykeluar'];
		$dtAkhir[$rData['periode'] . $rData['kodebarang']] = $rData['saldoakhirqty'];
	}
	// echo "<br>";
	// print_r($dtAwal);
	// die();

	$chekDt = count($dtPeriode);
	if ($chekDt == 0) {
		exit('Error:Data Kosong');
	}

	foreach ($dtPeriode as $dtIsi) {
		foreach ($lstKdBrg as $dtBrg) {
			if ($dtKdBarang[$dtIsi][$dtBrg] != '') {
				$no += 1;
				$tglSkrg = date('Y-m-d H:i:s');
				$tab .= '<tr class=rowcontent style=\'cursor:pointer;\' title=\'Click\' onclick="detailMutasiBarang2(event,\'' . $unitDt . '\',\'' . $dtIsi . '\',\'' . $gudang . '\',\'' . $dtKdBarang[$dtIsi][$dtBrg] . '\',\'' . $optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]] . '\',\'' . $optNmSat[$dtKdBarang[$dtIsi][$dtBrg]] . '\');">';
				$tab .= '<td>' . $no . '</td>';
				$tab .= '<td>' . $unitDt . '</td>';
				$tab .= '<td>' . $gudang . '</td>';
				$tab .= '<td>' . $dtIsi . '</td>';
				$tab .= '<td>' . $dtKdBarang[$dtIsi][$dtBrg] . '</td>';
				$tab .= '<td>' . $optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td>' . $optNmSat[$dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right class=firsttd>' . $dtAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]]. '</td>';
				$tab .= '<td align=right>' . $dtMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right  class=firsttd>' . $dtKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
				$tab .= '<td align=right  class=firsttd>' . $dtAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]] . '</td>';
			}
		}
	}
}



switch ($proses) {

case 'getGudang':

	$optUnit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

	$sUnit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where ' . "\r\n" . '            kodeorganisasi like \'' . $unitDt . '%\' and tipe like \'GUDANG%\' order by namaorganisasi asc';



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

	$nop_ = 'lapPersediaanFisikUnit_' . $dte;

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

			$this->Cell(140, 5, ' ', '', 0, 'R');

			$this->Cell(15, 5, 'User', '', 0, 'L');

			$this->Cell(2, 5, ':', '', 0, 'L');

			$this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');

			$this->Ln();

			$this->SetFont('Arial', '', 6);

			$this->Cell(5, 5, 'No.', 1, 0, 'C');

			$this->Cell(15, 5, $_SESSION['lang']['unit'], 1, 0, 'C');

			$this->Cell(20, 5, $_SESSION['lang']['sloc'], 1, 0, 'C');

			$this->Cell(17, 5, $_SESSION['lang']['periode'], 1, 0, 'C');

			$this->Cell(18, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C');

			$this->Cell(45, 5, substr($_SESSION['lang']['namabarang'], 0, 30), 1, 0, 'C');

			$this->Cell(8, 5, $_SESSION['lang']['satuan'], 1, 0, 'C');

			$this->Cell(20, 5, $_SESSION['lang']['saldoawal'], 1, 0, 'C');

			$this->Cell(15, 5, $_SESSION['lang']['masuk'], 1, 0, 'C');

			$this->Cell(15, 5, $_SESSION['lang']['keluar'], 1, 0, 'C');

			$this->Cell(15, 5, $_SESSION['lang']['saldo'], 1, 1, 'C');

		}

	}



	$pdf = new PDF('P', 'mm', 'A4');

	$pdf->AddPage();



	foreach ($dtPeriode as $dtIsi) {

		foreach ($lstKdBrg as $dtBrg) {

			if ($dtKdBarang[$dtIsi][$dtBrg] != '') {

				$nor += 1;

				$pdf->Cell(5, 5, $nor, 1, 0, 'C');

				$pdf->Cell(15, 5, $unitDt, 1, 0, 'C');

				$pdf->Cell(20, 5, $gudang, 1, 0, 'C');

				$pdf->Cell(17, 5, $dtIsi, 1, 0, 'C');

				$pdf->Cell(18, 5, $dtKdBarang[$dtIsi][$dtBrg], 1, 0, 'L');

				$pdf->Cell(45, 5, $optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]], 1, 0, 'L');

				$pdf->Cell(8, 5, $optNmSat[$dtKdBarang[$dtIsi][$dtBrg]], 1, 0, 'L');

				$pdf->Cell(20, 5, number_format($dtAwal[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 1, 0, 'R');

				$pdf->Cell(15, 5, number_format($dtMasuk[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 1, 0, 'R');

				$pdf->Cell(15, 5, number_format($dtKeluar[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 1, 0, 'R');

				$pdf->Cell(15, 5, number_format($dtAkhir[$dtIsi . $dtKdBarang[$dtIsi][$dtBrg]], 2, '.', ','), 1, 1, 'R');

			}

		}

	}



	$pdf->Output();

	break;



case 'detailData':
	$pt = $_GET['unitDt'];
	$gudang = $_GET['gudang'];
	$periode = $_GET['periode'];
	$kodebarang = $_GET['kodebarang'];
	$namabarang = $_GET['namabarang'];
	$satuan = $_GET['satuan'];
	$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';
	$namapt = 'COMPANY NAME';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$namapt = strtoupper($bar->namaorganisasi);
	}

	$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '      where kodeorg=\'' . $gudang . '\' and periode=\'' . $periode . '\'';
	#echo $str;
	$awal = '';
	$akhir = '';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$awal = $bar->tanggalmulai;
		$akhir = $bar->tanggalsampai;
	}
	
	$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';
	#exit(mysql_error($conn));
	($qPt = mysql_query($sPt)) || true;
	$rPt = mysql_fetch_assoc($qPt);
	// print_r($);
	
	// if ($gudang == '') {
	// 	$str = 'select  sum(saldoawalqty) as sawal,' . "\r\n\t\t" . '  ' . "\t\t" . 'sum(nilaisaldoawal) as sawalrp from ' . "\r\n\t\t\t\t" . $dbname . '.log_5saldobulanan' . "\r\n\t\t\t\t" . 'where kodebarang=\'' . $kodebarang . '\'' . "\r\n\t\t\t\t" . 'and periode=\'' . $periode . '\'';
	// 	$strx = 'select a.*,b.idsupplier,b.tanggal,b.kodegudang,' . "\r\n\t\t" . '      b.tipetransaksi ' . "\r\n\t\t" . '      from ' . $dbname . '.log_transaksidt a' . "\r\n\t\t" . '      left join ' . $dbname . '.log_transaksiht b' . "\r\n\t\t\t" . '  on a.notransaksi=b.notransaksi' . "\r\n\t\t\t" . '  where kodebarang=\'' . $kodebarang . '\'' . "\r\n\t\t\t" . '  and kodept=\'' . $rPt['induk'] . '\'' . "\r\n\t\t\t" . '  and b.tanggal>=\'' . $awal . '\'' . "\r\n\t\t\t" . '  and b.tanggal<=\'' . $akhir . '\'' . "\r\n\t\t\t" . '  and b.post=1' . "\r\n\t\t\t" . '  order by tanggal,waktutransaksi';
	// }else {
	// 	$str = 'select  sum(saldoawalqty) as sawal,' . "\r\n\t\t" . '  ' . "\t\t" . 'sum(nilaisaldoawal) as sawalrp from ' . "\r\n\t\t\t\t" . $dbname . '.log_5saldobulanan' . "\r\n\t\t\t\t" . 'where kodebarang=\'' . $kodebarang . '\'' . "\r\n\t\t\t\t" . 'and periode=\'' . $periode . '\'' . "\r\n\t\t\t\t" . 'and kodegudang=\'' . $gudang . '\'';
	// 	$strx = 'select a.*,b.idsupplier,b.tanggal,b.kodegudang,' . "\r\n\t\t" . '      b.tipetransaksi' . "\r\n\t\t\t" . '  from ' . $dbname . '.log_transaksidt a' . "\r\n\t\t" . '      left join ' . $dbname . '.log_transaksiht b' . "\r\n\t\t\t" . '  on a.notransaksi=b.notransaksi' . "\r\n\t\t\t" . '  where kodebarang=\'' . $kodebarang . '\'' . "\r\n\t\t\t" . '  and kodept=\'' . $rPt['induk'] . '\'' . "\r\n\t\t\t" . '  and kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t" . '  and b.tanggal>=\'' . $awal . '\'' . "\r\n\t\t\t" . '  and b.tanggal<=\'' . $akhir . '\'' . "\r\n\t\t\t" . '  and b.post=1' . "\r\n\t\t\t" . '  order by tanggal,waktutransaksi';
	// }
	if ($gudang == '') {
		$str = "select sum(saldoawalqty) as sawal, sum(nilaisaldoawal) as sawalrp ".
			   "from $dbname.log_5saldobulanan ".
			   "where kodebarang='" . $kodebarang ."' and periode='" . $periode . "'";
		$strx = "select a.*,b.idsupplier,b.tanggal,b.kodegudang, b.tipetransaksi ".
				"from $dbname.log_transaksidt a ".
				"left join $dbname.log_transaksiht b on a.notransaksi=b.notransaksi ".
				"where kodebarang='" . $kodebarang . "' and kodept='" . $pt . "' and ".
				"b.tanggal>='" . $awal . "' and b.tanggal<='" . $akhir . "' and b.post=1 ".
				"order by tanggal,waktutransaksi";
		
	}
	else {
	
		$str = "select  sum(saldoawalqty) as sawal, sum(nilaisaldoawal) as sawalrp ".
			"from $dbname.log_5saldobulanan ".
			"where kodebarang='" . $kodebarang . "' and periode='" . $periode . "' ".
			"and kodegudang='" . $gudang . "'";
	
		$strx = "select a.*,b.idsupplier,b.tanggal,b.kodegudang, b.tipetransaksi ".
			"from $dbname.log_transaksidt a ".
			"left join $dbname.log_transaksiht b on a.notransaksi=b.notransaksi ".
			"where kodebarang='" . $kodebarang . "' and kodegudang='" . $gudang . "' and ".
			"b.tanggal>='" . $awal . "' and b.tanggal<='" . $akhir . "' and b.post=1 ".
			"order by tanggal,waktutransaksi";
	}
	$sawal = 0;
	$sawalrp = 0;
	$hargasawal = 0;
	$res = mysql_query($str);
	while ($bar = mysql_fetch_object($res)) {
		$sawal = $bar->sawal;
		$sawalrp = $bar->sawalrp;
	}
	if (0 < $sawal) {
		$hargasawal = $sawalrp / $sawal;
	}

	class PDF extends FPDF
	{
		public function Header()
		{
			global $namapt;
			global $pt;
			global $gudang;
			global $periode;
			global $kodebarang;
			global $namabarang;
			global $satuan;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(20, 5, $namapt, '', 1, 'L');
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(190, 5, strtoupper($_SESSION['lang']['detailtransaksibarang']), 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell(35, 5, $_SESSION['lang']['unit'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(100, 5, $pt, '', 0, 'L');
			$this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
			$this->Cell(35, 5, $_SESSION['lang']['namabarang'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(100, 5, '[' . $kodebarang . ']' . $namabarang . '(' . $satuan . ')', '', 0, 'L');
			$this->Cell(15, 5, $_SESSION['lang']['page'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
			$this->Cell(35, 5, $_SESSION['lang']['periode'], '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(100, 5, $periode, '', 0, 'L');
			$this->Cell(15, 5, 'User', '', 0, 'L');
			$this->Cell(2, 5, ':', '', 0, 'L');
			$this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
			$this->SetFont('Arial', '', 6);
			$this->Cell(5, 5, 'No.', 1, 0, 'C');
			$this->Cell(35, 5, $_SESSION['lang']['sloc'], 1, 0, 'C');
			$this->Cell(20, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C');
			$this->Cell(25, 5, $_SESSION['lang']['tipe'], 1, 0, 'C');
			$this->Cell(25, 5, $_SESSION['lang']['saldoawal'], 1, 0, 'C');
			$this->Cell(25, 5, $_SESSION['lang']['masuk'], 1, 0, 'C');
			$this->Cell(25, 5, $_SESSION['lang']['keluar'], 1, 0, 'C');
			$this->Cell(25, 5, $_SESSION['lang']['saldo'], 1, 1, 'C');
		}
	}

	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$resx = mysql_query($strx);
	$no = 0;
	$saldo = $sawal;
	$masuk = 0;
	$keluar = 0;
	while ($barx = mysql_fetch_object($resx)) {
		$no += 1;
		if ($barx->tipetransaksi < 5) {
			$saldo = $saldo + $barx->jumlah;
			$masuk = $barx->jumlah;
			$keluar = 0;
		}
		else {
			$saldo = $saldo - $barx->jumlah;
			$keluar = $barx->jumlah;
			$masuk = 0;
		}
		$pdf->Cell(5, 5, $no, 0, 0, 'C');
		$pdf->Cell(35, 5, $barx->kodegudang, 0, 0, 'C');
		$pdf->Cell(20, 5, tanggalnormal($barx->tanggal), 0, 0, 'C');
		$pdf->Cell(25, 5, $barx->tipetransaksi, 0, 0, 'C');
		$pdf->Cell(25, 5, number_format($sawal, 2, '.', ','), 0, 0, 'R');
		$pdf->Cell(25, 5, number_format($masuk, 2, '.', ','), 0, 0, 'R');
		$pdf->Cell(25, 5, number_format($keluar, 2, '.', ','), 0, 0, 'R');
		$pdf->Cell(25, 5, number_format($saldo, 2, '.', ','), 0, 1, 'R');
		$sawal = $saldo;
	}

	$pdf->Output();
	break;
}



?>

