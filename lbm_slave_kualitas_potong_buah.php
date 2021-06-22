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
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' tidak boleh kosong');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' Tidak boleh kosong');
}

$thn = explode('-', $periode);
$bln = intval($thn[1]);
$thnLalu = $thn[0];

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

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>08. KUALITAS POTONG BUAH</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td colspan=7 ' . $bgcoloraja . ' align=center>AQC REPORT</td></tr><tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=2>MUTU ANCAK</td><td ' . $bgcoloraja . ' colspan=5>MUTU BUAH</td</tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>BUAH  MENTAH  (Std. 0%)</td><td ' . $bgcoloraja . '>BUAH KURANG MATANG (Std. <8%)</td>';
$tab .= '<td ' . $bgcoloraja . '>BUAH            MATANG              (Std. >85%)</td>';
$tab .= ' <td ' . $bgcoloraja . '>BUAH TERLALU MATANG           (Std. <7%)</td><td ' . $bgcoloraja . '>JANJANG KOSONG</td><td ' . $bgcoloraja . '>TANGKAI PANJANG</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$sData = 'select distinct sum(mentah) as bhMentah,sum(kmatang) kmatang,sum(matang) as bhMatang,' . "\r\n" . '        sum(lmatang) as bhLmatang,sum(jkosong) as jkosong,sum(tpanjang) as tpanjang,substr(tanggal,1,7) as periode,' . "\r\n" . '        sum(diperiksa) as diperiksa from ' . $dbname . '.kebun_qc_panen_vw' . "\r\n" . '        where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\'';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;
$rData = mysql_fetch_assoc($qData);
@$xbhmentah = ($rData['bhMentah'] / $rData['diperiksa']) * 100;
@$xbhkmatang = ($rData['kmatang'] / $rData['diperiksa']) * 100;
@$xbhmatang = ($rData['bhMatang'] / $rData['diperiksa']) * 100;
@$xbhLmatang = ($rData['bhLmatang'] / $rData['diperiksa']) * 100;
@$xbhJksng = ($rData['jkosong'] / $rData['diperiksa']) * 100;
@$xbhtpanjang = ($rData['tpanjang'] / $rData['diperiksa']) * 100;
$tab .= '<tr class=rowcontent><td>' . substr(tanggalnormal($rData['periode']), 1, 7) . '</td>';
$tab .= '<td align=right>' . number_format($xbhmentah, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhkmatang, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhmatang, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhLmatang, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhJksng, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhtpanjang, 2) . '</td></tr>';
$sData = 'select distinct sum(mentah) as bhMentah,sum(kmatang) kmatang,sum(matang) as bhMatang,' . "\r\n" . '        sum(lmatang) as bhLmatang,sum(jkosong) as jkosong,sum(tpanjang) as tpanjang,substr(tanggal,1,7) as periode,' . "\r\n" . '        sum(diperiksa) as diperiksa from ' . $dbname . '.kebun_qc_panen_vw' . "\r\n" . '        where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;
$rData = mysql_fetch_assoc($qData);
@$xbhmentahsbi = ($rData['bhMentah'] / $rData['diperiksa']) * 100;
@$xbhkmatangsbi = ($rData['kmatang'] / $rData['diperiksa']) * 100;
@$xbhmatangsbi = ($rData['bhMatang'] / $rData['diperiksa']) * 100;
@$xbhLmatangsbi = ($rData['bhLmatang'] / $rData['diperiksa']) * 100;
@$xbhJksngsbi = ($rData['jkosong'] / $rData['diperiksa']) * 100;
@$xbhtpanjangsbi = ($rData['tpanjang'] / $rData['diperiksa']) * 100;
$tab .= '<tr class=rowcontent><td>' . substr(tanggalnormal($rData['periode']), 1, 7) . '</td>';
$tab .= '<td align=right>' . number_format($xbhmentahsbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhkmatangsbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhmatangsbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhLmatangsbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhJksngsbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhtpanjangsbi, 2) . '</td>';
$tab .= '</tr></tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'kualitas_potong_buah' . $dte;
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
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('08. KUALITAS POTONG BUAH'), 0, 1, 'L');
			$this->Cell($width, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
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
			$this->Cell(490, $height, 'AQC REPORT', TBLR, 1, 'C', 1);
			$this->Cell(50, $height, 'Periode', TLR, 0, 'C', 1);
			$this->Cell(130, $height, 'MUTU ANCAK ', TLR, 0, 'C', 1);
			$this->Cell(310, $height, 'MUTU BUAH', TLR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(55, $height, 'BUAH ', TLR, 0, 'C', 1);
			$this->Cell(75, $height, 'BUAH KURANG ', TLR, 0, 'C', 1);
			$this->Cell(65, $height, 'BUAH ', TLR, 0, 'C', 1);
			$this->Cell(115, $height, 'BUAH TERLALU ', TLR, 0, 'C', 1);
			$this->Cell(65, $height, 'JANJANG  ', TLR, 0, 'C', 1);
			$this->Cell(65, $height, 'TANGKAI  ', TLR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(55, $height, 'MENTAH ', LR, 0, 'C', 1);
			$this->Cell(75, $height, 'MATANG', LR, 0, 'C', 1);
			$this->Cell(65, $height, 'MATANG', LR, 0, 'C', 1);
			$this->Cell(115, $height, 'MATANG', LR, 0, 'C', 1);
			$this->Cell(65, $height, 'KOSONG', LR, 0, 'C', 1);
			$this->Cell(65, $height, 'PANJANG  ', LR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(55, $height, '(Std. 0%) ', BLR, 0, 'C', 1);
			$this->Cell(75, $height, '(Std. <8%)', BLR, 0, 'C', 1);
			$this->Cell(65, $height, '(Std. >85%)', BLR, 0, 'C', 1);
			$this->Cell(115, $height, '(Std. <7%)', BLR, 0, 'C', 1);
			$this->Cell(65, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(65, $height, '   ', BLR, 1, 'C', 1);
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
	$tnggi = $jmlHari * $height;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(50, $height, substr(tanggalnormal($rData['periode']), 1, 7), TBLR, 0, 'C', 1);
	$pdf->Cell(55, $height, number_format($xbhmentah, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(75, $height, number_format($xbhkmatang, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhmatang, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(115, $height, number_format($xbhLmatang, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhJksng, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhtpanjang, 2), TBLR, 1, 'R', 1);
	$pdf->Cell(50, $height, substr(tanggalnormal($rData['periode']), 1, 7), TBLR, 0, 'C', 1);
	$pdf->Cell(55, $height, number_format($xbhmentahsbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(75, $height, number_format($xbhkmatangsbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhmatangsbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(115, $height, number_format($xbhLmatangsbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhJksngsbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhtpanjangsbi, 2), TBLR, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
