<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['regional'] == '' ? $regional = $_GET['regional'] : $regional = $_POST['regional'];
$_POST['unit'] == '' ? $unit = $_GET['unit'] : $unit = $_POST['unit'];
$optNmorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$thn = substr($periode, 0, 4);
$bln = substr($periode, 5, 2);
$start = $thn . '-01-01';
$wktu = mktime(0, 0, 0, $bln, 15, $thn);
$end = $thn . '-' . $bln . '-' . date('t', $wktu);

if ($unit != '') {
	$wher = ' and a.kodeorg=\'' . $unit . '\'';
	$whr = ' and substr(kodeblok,1,4)=\'' . $unit . '\'';
}
else {
	$wher = 'and a.kodeorg in (';
	$whr = ' and substr(kodeblok,1,4) in (';
	$str = 'select distinct * from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $regional . '\'' . "\r\n" . '    order by regional desc';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_assoc($res)) {
		$areder += 1;

		if ($areder == 1) {
			$wher .= '\'' . $bar['kodeunit'] . '\'';
			$whr .= '\'' . $bar['kodeunit'] . '\'';
		}
		else {
			$wher .= ',\'' . $bar['kodeunit'] . '\'';
			$whr .= ',\'' . $bar['kodeunit'] . '\'';
		}
	}

	$wher .= ')';
	$whr .= ')';
}

if ($_SESSION['language'] == 'EN') {
	$zz = 'namakegiatan1 as namakegiatan';
}
else {
	$zz = 'namakegiatan';
}

$str = 'select a.notransaksi,a.tanggal,a.nilaikontrak,b.namasupplier,d.' . $zz . "\r\n" . '        from ' . $dbname . '.log_spkht a ' . "\r\n" . '            left join ' . $dbname . '.log_5supplier b on a.koderekanan = b.supplierid' . "\r\n" . '            left JOIN ' . $dbname . '.log_spkdt c on c.notransaksi = a.notransaksi' . "\r\n" . '            left JOIN ' . $dbname . '.setup_kegiatan d on d.kodekegiatan = c.kodekegiatan' . "\r\n" . '        where a.tanggal  between \'' . $start . '\' and \'' . $end . '\' ' . $wher . '  group by a.notransaksi';

#exit(mysql_error());
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$nospk[$res['notransaksi']] = $res['notransaksi'];
	$tgl[$res['notransaksi']] = $res['tanggal'];
	$kontraktor[$res['notransaksi']] = $res['namasupplier'];
	$rpkontrak[$res['notransaksi']] = $res['nilaikontrak'];
	$kegiatan[$res['notransaksi']] = $res['namakegiatan'];
}

$sDataBi = 'select notransaksi,sum(jumlahrealisasi) as jml,sum(hkrealisasi) as fisik' . "\r\n" . '           from ' . $dbname . '.log_baspk ' . "\r\n" . '           where tanggal like \'' . $periode . '%\' ' . $whr . ' group by notransaksi';

#exit(mysql_error());
($qDataBi = mysql_query($sDataBi)) || true;

while ($rDataBi = mysql_fetch_assoc($qDataBi)) {
	$DtBi[$rDataBi['notransaksi']] = $rDataBi['jml'];
	$HkBi[$rDataBi['notransaksi']] = $rDataBi['fisik'];
}

$sDataSBi = 'select notransaksi,sum(jumlahrealisasi) as jml,sum(hkrealisasi) as fisik' . "\r\n" . '           from ' . $dbname . '.log_baspk ' . "\r\n" . '           where tanggal between \'' . $start . '\' and \'' . $end . '\'  ' . $whr . ' group by notransaksi';

#exit(mysql_error());
($qDataSBi = mysql_query($sDataSBi)) || true;

while ($rDataSBi = mysql_fetch_assoc($qDataSBi)) {
	$DtSBi[$rDataSBi['notransaksi']] = $rDataSBi['jml'];
	$HkSBi[$rDataSBi['notransaksi']] = $rDataSBi['fisik'];
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=center><b>Contract Progress Summary</b></td><td colspan=6 align=right><b>' . $_SESSION['lang']['periode'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

if ($proses != 'getUnit') {
	$tab .= '<table class=sortable cellspacing=1 border=' . $brdr . ' width=100%>' . "\r\n" . '        <thead>' . "\r\n" . '            <tr>' . "\r\n" . '                <td align=center rowspan=2>No.</td>' . "\r\n" . '                <td align=center rowspan=2>' . $_SESSION['lang']['nospk'] . '</td>' . "\r\n" . '                <td align=center rowspan=2>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                <td align=center rowspan=2>' . $_SESSION['lang']['kegiatan'] . '</td>' . "\r\n" . '                <td align=center rowspan=2>' . $_SESSION['lang']['kontraktor'] . '</td>' . "\r\n" . '                <td align=center rowspan=2>' . $_SESSION['lang']['rpkontrak'] . '</td>' . "\r\n" . '                <td align=center colspan=2>' . $_SESSION['lang']['rprealisasi'] . '</td>' . "\r\n" . '                <td align=center colspan=2>' . $_SESSION['lang']['fisik'] . ' ' . $_SESSION['lang']['realisasi'] . '</td>    ' . "\r\n" . '                <td align=center rowspan=2>' . $_SESSION['lang']['%'] . '</td>' . "\r\n" . '            </tr>  ' . "\r\n" . '            <tr>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['bi'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['sbi'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['bi'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['sbi'] . '</td> ' . "\r\n" . '            </tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody id=container>';
	$i = 0;

	foreach ($nospk as $spk => $lsspk) {
		++$i;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=center>' . $i . '</td>';
		$tab .= '<td align=left>' . $lsspk . '</td>';
		$tab .= '<td align=center>' . tanggalnormal($tgl[$lsspk]) . '</td>';
		$tab .= '<td align=left>' . $kegiatan[$lsspk] . '</td>';
		$tab .= '<td align=left>' . $kontraktor[$lsspk] . '</td>';
		$tab .= '<td align=right>' . number_format($rpkontrak[$lsspk], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($DtBi[$lsspk], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($DtSBi[$lsspk], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($HkBi[$lsspk], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($HkSBi[$lsspk], 0) . '</td>';
		@$persen = number_format(($DtSBi[$lsspk] / $rpkontrak[$lsspk]) * 100, 2);
		$tab .= '<td align=right>' . $persen . '</td>';
		$tab .= '</tr>';
	}

	$tab .= ' </tbody></table>';
}

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'Summary_Progress_SPK_' . $periode;

	if (0 < strlen($tab)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab)) {
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;

case 'pdf':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $dbname;
			global $wkiri;
			global $wlain;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('CONTRACT PROGRESS SUMMARY'), 0, 1, 'L');
			$this->Cell($width, $height, $_SESSION['lang']['periode'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 8);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(15, $height, 'No.', TLR, 0, 'C', 1);
			$this->Cell(110, $height, 'No SPK', TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'Tanggal', TLR, 0, 'C', 1);
			$this->Cell(200, $height, 'Kegiatan', TLR, 0, 'C', 1);
			$this->Cell(100, $height, 'Kontraktor', TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'Rp. Kontrak', TLR, 0, 'C', 1);
			$this->Cell(100, $height, 'Rp. Realisasi', TLR, 0, 'C', 1);
			$this->Cell(100, $height, 'Fisik Realisasi', TLR, 0, 'C', 1);
			$this->Cell(50, $height, '%', TLR, 1, 'C', 1);
			$this->Cell(15, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(110, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(200, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(100, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, 'BI', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, 'S/d BI', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, 'BI', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, 'S/d BI', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 11);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$cols = 247.5;
	$wkiri = 50;
	$wlain = 11;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$i = 0;

	foreach ($nospk as $spk => $lsspk) {
		++$i;
		$pdf->Cell(15, $height, $i, TBLR, 0, 'L', 1);
		$pdf->Cell(110, $height, $lsspk, TBLR, 0, 'L', 1);
		$pdf->Cell(50, $height, tanggalnormal($tgl[$lsspk]), TBLR, 0, 'C', 1);
		$pdf->Cell(200, $height, $kegiatan[$lsspk], TBLR, 0, 'L', 1);
		$pdf->Cell(100, $height, $kontraktor[$lsspk], TBLR, 0, 'L', 1);
		$pdf->Cell(50, $height, number_format($rpkontrak[$lsspk], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($DtBi[$lsspk], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($DtSBi[$lsspk], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($HkBi[$lsspk], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($HkSBi[$lsspk], 0), TBLR, 0, 'R', 1);
		@$persen = number_format(($DtSBi[$lsspk] / $rpkontrak[$lsspk]) * 100, 2);
		$pdf->Cell(50, $height, $persen, TBLR, 1, 'R', 1);
	}

	$pdf->Output();
	break;

case 'getUnit':
	$optUnit = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sUnit = 'select distinct * from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $regional . '\'';

	#exit(mysql_error($conn));
	($qUnit = mysql_query($sUnit)) || true;

	while ($rUnit = mysql_fetch_assoc($qUnit)) {
		$optUnit .= '<option value=\'' . $rUnit['kodeunit'] . '\'>' . $optNmorg[$rUnit['kodeunit']] . '</option>';
	}

	echo $optUnit;
	break;
}

?>
