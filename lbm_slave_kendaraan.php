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

$optNmOrang = makeOption($dbname, 'vhc_5operator', 'karyawanid,nama');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$arrMilik = array('Sewa/Kontrak', 'Milik Sendiri');
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
	exit('Error:' . $_SESSION['lang']['unit'] . '  required');
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

$sKend = 'select distinct * from ' . $dbname . '.vhc_5master where kodetraksi=\'' . $kdUnit . '\' order by jenisvhc asc';

#exit(mysql_error($conn));
($qKend = mysql_query($sKend)) || true;

while ($rKend = mysql_fetch_assoc($qKend)) {
	$dtKend[] = $rKend['kodevhc'];
	$dtJns[$rKend['kodevhc']] = $rKend['jenisvhc'];
	$dtNmBrg[$rKend['kodevhc']] = $rKend['kodebarang'];
	$dtThnPerolehan[$rKend['kodevhc']] = $rKend['tahunperolehan'];
	$dtMilik[$rKend['kodevhc']] = $rKend['kepemilikan'];
	$dtUnitMilik[$rKend['kodevhc']] = $rKend['kodeorg'];
	$dtNoRangka[$rKend['kodevhc']] = $rKend['nomorrangka'];
	$dtNoMesin[$rKend['kodevhc']] = $rKend['nomormesin'];
	$dtTraksi[$rKend['kodevhc']] = $rKend['kodetraksi'];
}

$varCek = count($dtKend);

if ($varCek < 1) {
	exit('Error:No data found');
}

$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>25.2 ' . $_SESSION['lang']['kendaraan'] . '</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>';
$tab .= '<tr>';
$tab .= '<td>' . $_SESSION['lang']['jenis'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['nama'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['tahunperolehan'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['kepemilikan'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['unit'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['kodevhc'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['nomorrangka'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['nomormesin'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['user'] . '</td>';
$tab .= '</tr></thead><tbody>';

foreach ($dtKend as $lstKend) {
	$no += 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $dtJns[$lstKend] . '</td>';
	$tab .= '<td>' . $optNmBrg[$dtNmBrg[$lstKend]] . '</td>';
	$tab .= '<td align=center>' . $dtThnPerolehan[$lstKend] . '</td>';
	$tab .= '<td>' . $arrMilik[$dtMilik[$lstKend]] . '</td>';
	$tab .= '<td>' . $dtUnitMilik[$lstKend] . '</td>';
	$tab .= '<td>' . $lstKend . '</td>';
	$tab .= '<td>' . $dtNoRangka[$lstKend] . '</td>';
	$tab .= '<td>' . $dtNoMesin[$lstKend] . '</td>';
	$tab .= '<td>' . $dtTraksi[$lstKend] . '</td>';
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
	$nop_ = 'kendaraan_' . $dte;
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
			global $kbnSndri;
			global $lstPlnggan;
			global $tot;
			$cold2 = count($lstPlnggan) * 55;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('25.2  ' . $_SESSION['lang']['kendaraan']), 0, 1, 'L');
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
			$this->Cell(30, $height, $_SESSION['lang']['jenis'], TLR, 0, 'C', 1);
			$this->Cell(120, $height, $_SESSION['lang']['nama'], TLR, 0, 'C', 1);
			$this->Cell(45, $height, $_SESSION['lang']['tahunperolehan'], TLR, 0, 'C', 1);
			$this->Cell(75, $heigh, '', TLR, 0, 'C', 1);
			$this->Cell(55, $height, $_SESSION['lang']['unit'], TLR, 0, 'C', 1);
			$this->Cell(75, $height, $_SESSION['lang']['kodevhc'], TLR, 0, 'C', 1);
			$this->Cell(200, $height, $_SESSION['lang']['nomor'], TLR, 0, 'C', 1);
			$this->Cell(100, $height, $_SESSION['lang']['user'], TLR, 1, 'C', 1);
			$this->Cell(30, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(120, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(45, $height, '', BLR, 0, 'C', 1);
			$this->Cell(75, $height, $_SESSION['lang']['kepemilikan'], BLR, 0, 'C', 1);
			$this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(75, $height, '', BLR, 0, 'C', 1);
			$this->Cell(100, $height, $_SESSION['lang']['nomorrangka'], TBLR, 0, 'C', 1);
			$this->Cell(100, $height, $_SESSION['lang']['nomormesin'], TBLR, 0, 'C', 1);
			$this->Cell(100, $height, '', BLR, 1, 'C', 1);
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

	foreach ($dtKend as $lstKend) {
		$pdf->Cell(30, $height, $dtJns[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(120, $height, $optNmBrg[$dtNmBrg[$lstKend]], TBLR, 0, 'L', 1);
		$pdf->Cell(45, $height, $dtThnPerolehan[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(75, $height, $arrMilik[$dtMilik[$lstKend]], TBLR, 0, 'L', 1);
		$pdf->Cell(55, $height, $dtUnitMilik[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(75, $height, $lstKend, TBLR, 0, 'L', 1);
		$pdf->Cell(100, $height, $dtNoRangka[$lstKend], TBLR, 0, 'L', 1);
		$pdf->Cell(100, $height, $dtNoMesin[$lstKend], TBLR, 0, 'L', 1);
		$pdf->Cell(100, $height, $dtTraksi[$lstKend], TBLR, 1, 'L', 1);
	}

	$pdf->Output();
	break;
}

?>
