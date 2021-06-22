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

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td colspan=7 ' . $bgcoloraja . ' align=center>AQC REPORT</td></tr><tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=2>MUTU ANGKUT DI TPH</td><td ' . $bgcoloraja . ' colspan=5>KUALITAS ANCAK</td</tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>BUAH TINGGAL (%)</td><td ' . $bgcoloraja . '>BRONDOLAN TIDAK DIKUTIP    (%)</td>';
$tab .= '<td ' . $bgcoloraja . '>BRONDOLAN TIDAK DIKUTIP (%)</td>';
$tab .= ' <td ' . $bgcoloraja . '>BUAH TINGGAL DI POKOK (%)</td><td ' . $bgcoloraja . '>BUAH TINGGAL DI LAPANGAN (%)</td><td ' . $bgcoloraja . '>TUNASAN LALAI (Pkk)</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$sData = 'select distinct sum(jjgtinggal) as jjgtinggal,sum(bdrtinggal) as bdrtinggal,sum(totjjgtph) as totjjgtph, ' . "\r\n" . '                sum(totbrdtph) as totbrdtph from ' . $dbname . '.kebun_qc_ancakht' . "\r\n" . '                where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\'';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;
$rData = mysql_fetch_assoc($qData);
@$xbhtinggal = ($rData['jjgtinggal'] / $rData['totjjgtph']) * 100;
@$xbdrtinggal = ($rData['bdrtinggal'] / $rData['totbrdtph']) * 100;
$sData2 = 'select distinct sum(brdpikul+brdpirki+ brdpirka+ brdlpki+brdlpka) as sma,sum(buahpkkki+buahpkkka) as bhTinggalpkk,' . "\r\n" . '                sum(bhgwki+bhgwka) bhTinggallap,sum(tunasl) as tunasl' . "\r\n" . '                from ' . $dbname . '.kebun_qc_ancak_vw where' . "\r\n" . '                substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\'';

#exit(mysql_error());
($qData2 = mysql_query($sData2)) || true;
$rData2 = mysql_fetch_assoc($qData2);
@$xbdrtdkdkutip = ($rData2['sma'] / $rData['totbrdtph']) * 100;
@$xbhTinggal = ($rData2['bhTinggalpkk'] / $rData['totjjgtph']) * 100;
@$xbhTinggalLap = ($rData2['bhTinggallap'] / $rData['totjjgtph']) * 100;
$tab .= '<tr class=rowcontent><td>' . substr(tanggalnormal($periode), 1, 7) . '</td>';
$tab .= '<td align=right>' . number_format($xbhtinggal, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbdrtinggal, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbdrtdkdkutip, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhTinggal, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhTinggalLap, 2) . '</td>';
$tab .= '<td align=right>' . number_format($rData2['tunasl'], 2) . '</td></tr>';
$sDatasbi = 'select distinct sum(jjgtinggal) as jjgtinggal,sum(bdrtinggal) as bdrtinggal,sum(totjjgtph) as totjjgtph, ' . "\r\n" . '                sum(totbrdtph) as totbrdtph from ' . $dbname . '.kebun_qc_ancakht' . "\r\n" . '                where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'';

#exit(mysql_error());
($qDatasbi = mysql_query($sDatasbi)) || true;
$rDatasbi = mysql_fetch_assoc($qDatasbi);
@$xbhtinggalSbi = ($rDatasbi['jjgtinggal'] / $rDatasbi['totjjgtph']) * 100;
@$xbdrtinggalSbi = ($rDatasbi['bdrtinggal'] / $rDatasbi['totbrdtph']) * 100;
$sData3 = 'select distinct sum(brdpikul+brdpirki+ brdpirka+ brdlpki+brdlpka) as sma,sum(buahpkkki+buahpkkka) as bhTinggalpkk,' . "\r\n" . '                sum(bhgwki+bhgwka) bhTinggallap,sum(tunasl) as tunasl2' . "\r\n" . '                from ' . $dbname . '.kebun_qc_ancak_vw where' . "\r\n" . '                substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'';

#exit(mysql_error());
($qData3 = mysql_query($sData3)) || true;
$rData3 = mysql_fetch_assoc($qData3);
@$xbdrtdkdkutipSbi = ($rData3['sma'] / $rDatasbi['totbrdtph']) * 100;
@$xbhTinggalSbi = ($rData3['bhTinggalpkk'] / $rDatasbi['totjjgtph']) * 100;
@$xbhTinggalLapSbi = ($rData3['bhTinggallap'] / $rDatasbi['totjjgtph']) * 100;
$tab .= '<tr class=rowcontent><td>' . substr(tanggalnormal($periode), 1, 7) . '</td>';
$tab .= '<td align=right>' . number_format($xbhtinggalSbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbdrtinggalSbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbdrtdkdkutipSbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhTinggalSbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($xbhTinggalLapSbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($rData3['tunasl2'], 2) . '</td>';
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
			$this->Cell($width, $height, strtoupper('09. KUALITAS ANCAK dan ANGKUT BUAH'), 0, 1, 'L');
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
			$this->Cell(130, $height, 'MUTU ANGKUT DI TPH', TLR, 0, 'C', 1);
			$this->Cell(310, $height, 'KUALITAS ANCAK', TLR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(55, $height, 'BUAH ', TLR, 0, 'C', 1);
			$this->Cell(75, $height, 'BRONDOLAN', TLR, 0, 'C', 1);
			$this->Cell(65, $height, 'BRONDOLAN', TLR, 0, 'C', 1);
			$this->Cell(115, $height, 'BUAH TINGGAL', TLR, 0, 'C', 1);
			$this->Cell(65, $height, 'BUAH TINGGAL', TLR, 0, 'C', 1);
			$this->Cell(65, $height, 'TUNASAN', TLR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(55, $height, 'TINGGAL', LR, 0, 'C', 1);
			$this->Cell(75, $height, 'TIDAK DIKUTIP', LR, 0, 'C', 1);
			$this->Cell(65, $height, 'TIDAK DIKUTIP', LR, 0, 'C', 1);
			$this->Cell(115, $height, 'DI POKOK ', LR, 0, 'C', 1);
			$this->Cell(65, $height, 'DI LAPANGAN', LR, 0, 'C', 1);
			$this->Cell(65, $height, 'LALAI ', LR, 1, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(55, $height, '(%)', BLR, 0, 'C', 1);
			$this->Cell(75, $height, '(%)', BLR, 0, 'C', 1);
			$this->Cell(65, $height, '(%)', BLR, 0, 'C', 1);
			$this->Cell(115, $height, '(%)', BLR, 0, 'C', 1);
			$this->Cell(65, $height, '(%)', BLR, 0, 'C', 1);
			$this->Cell(65, $height, '(Pkk)', BLR, 1, 'C', 1);
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
	$pdf->Cell(50, $height, substr(tanggalnormal($periode), 1, 7), TBLR, 0, 'C', 1);
	$pdf->Cell(55, $height, number_format($xbhtinggal, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(75, $height, number_format($xbdrtinggal, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbdrtdkdkutip, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(115, $height, number_format($xbhTinggal, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhTinggalLap, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($rData2['tunasl'], 2), TBLR, 1, 'R', 1);
	$pdf->Cell(50, $height, substr(tanggalnormal($periode), 1, 7), TBLR, 0, 'C', 1);
	$pdf->Cell(55, $height, number_format($xbhtinggalSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(75, $height, number_format($xbdrtinggalSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbdrtdkdkutipSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(115, $height, number_format($xbhTinggalSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($xbhTinggalLapSbi, 2), TBLR, 0, 'R', 1);
	$pdf->Cell(65, $height, number_format($rData3['tunasl2'], 2), TBLR, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
