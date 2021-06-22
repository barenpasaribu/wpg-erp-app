<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['kontaktor'] == '' ? $kontaktor = $_GET['kontaktor'] : $kontaktor = $_POST['kontaktor'];
$_POST['tahun'] == '' ? $tahun = $_GET['tahun'] : $tahun = $_POST['tahun'];
$eret = ' kodekelompok=\'K001\'';
$optNmorg = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', $eret);
$optNmKdorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKeg = makeOption($dbname, 'log_baspk', 'notransaksi,kodekegiatan');
$optNmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$thn = substr($periode, 0, 4);
$bln = substr($periode, 5, 2);
$start = $thn . '-01-01';
$wktu = mktime(0, 0, 0, $bln, 15, $thn);
$end = $thn . '-' . $bln . '-' . date('t', $wktu);

if ($proses != 'getUnit') {
	if ($kontaktor != '') {
		$hwr = 'and koderekanan=\'' . $kontaktor . '\'';
		$hwr1 = ' and kodesupplier=\'' . $kontaktor . '\' and kodesupplier!=\'\'';
		$hwr2 = ' and a.kodesupplier=\'' . $kontaktor . '\' and b.kodesupplier!=\'\'';
	}

	$sSpk = 'select distinct a.notransaksi,b.namasupplier,a.keterangan,a.kodeorg,c.kodekegiatan,nilaikontrak' . "\r\n" . '        from ' . $dbname . '.log_spkht a ' . "\r\n" . '            left join ' . $dbname . '.log_5supplier b on a.koderekanan = b.supplierid' . "\r\n" . '            left JOIN ' . $dbname . '.log_spkdt c on c.notransaksi = a.notransaksi' . "\r\n" . '            left JOIN ' . $dbname . '.setup_kegiatan d on d.kodekegiatan = c.kodekegiatan' . "\r\n" . ' where left(a.tanggal,4)=\'' . $tahun . '\'  ' . $hwr . ' and a.kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' group by a.notransaksi';

	#exit(mysql_error($conn));
	($qSpk = mysql_query($sSpk)) || true;

	while ($rSpk = mysql_fetch_assoc($qSpk)) {
		$noTrans[$rSpk['notransaksi']] = $rSpk['notransaksi'];
		$noSupp[$rSpk['notransaksi']] = $rSpk['namasupplier'];
		$noKeg[$rSpk['notransaksi']] = $rSpk['keterangan'];
		$noUnit[$rSpk['notransaksi']] = $rSpk['kodeorg'];
		$nilKontrak[$rSpk['notransaksi']] = $rSpk['nilaikontrak'];
	}

	$sBspk = 'select distinct sum(jumlahrealisasi) as nilaiKont,tanggal,kodekegiatan, notransaksi ' . "\r\n" . '            from ' . $dbname . '.log_baspk where left(tanggal,4)=\'' . $tahun . '\'  ' . "\r\n" . 'and kodeblok like \''.$_SESSION['empl']['kodeorganisasi'].'%\' group by notransaksi';

	#exit(mysql_error($conn));
	($qBspk = mysql_query($sBspk)) || true;

	while ($rBspk = mysql_fetch_assoc($qBspk)) {
		$noUang[$rBspk['notransaksi']] = $rBspk['nilaiKont'];
		$noTglBpp[$rBspk['notransaksi']] = $rBspk['tanggal'];
	}

	$sInvoice = 'select distinct noinvoice,nopo,sum(nilaiinvoice+nilaippn) as jmlhTag,jatuhtempo,keterangan from ' . $dbname . '.keu_tagihanht where ' . "\r\n" . '               tipeinvoice=\'K\' and left(tanggal,4)=\'' . $tahun . '\' and  posting=1 ' . $hwr1 . ' and kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' group by noinvoice,nopo order by nopo asc';
	$ert = '';

	#exit(mysql_error($conn));
	($qInvoice = mysql_query($sInvoice)) || true;

	while ($rInvoice = mysql_fetch_assoc($qInvoice)) {
		if ($ert != $rInvoice['nopo']) {
			$ert = $rInvoice['nopo'];
			$totRw2 = 1;
		}

		$noInvoice[$rInvoice['nopo']][$totRw2] = $rInvoice['noinvoice'];
		$noInvoiceSupp[$rInvoice['nopo']][$totRw2] = $rInvoice['keterangan'];
		$noTglJthtem[$rInvoice['nopo']][$totRw2] = $rInvoice['jatuhtempo'];
		$noNilInv[$rInvoice['nopo']][$totRw2] = $rInvoice['jmlhTag'];
		$noTrans[$rInvoice['nopo']] = $rInvoice['nopo'];
		$jmlhRow2[$rInvoice['nopo']] = $totRw2;
		$totRw2 += 1;
	}

	$sJur = 'select distinct noinvoice,nopo,sum(b.jumlah) as jmlhTag,c.tanggal from ' . $dbname . '.keu_tagihanht a' . "\r\n" . '           left join ' . $dbname . '.keu_kasbankdt b on a.noinvoice=b.keterangan1' . "\r\n" . '           left join ' . $dbname . '.keu_kasbankht c on b.notransaksi=c.notransaksi' . "\r\n" . '           where  tipeinvoice=\'K\' and left(a.tanggal,4)=\'' . $tahun . '\' and a.posting=1' . "\r\n" . '           and (keterangan1!=\'\' and keterangan1!=0) and c.posting=1' . "\r\n" . '           and b.tipetransaksi !=\'M\'  ' . $hwr2 . '  and c.kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' group by noinvoice order by nopo asc';

	#exit(mysql_error($conn));
	($qJur = mysql_query($sJur)) || true;

	while ($rJur = mysql_fetch_assoc($qJur)) {
		$arrtglbyr[$rJur['nopo']][$rJur['noinvoice']] = $rJur['tanggal'];
		$dByr[$rJur['nopo']][$rJur['noinvoice']] = $rJur['jmlhTag'];
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

	$tab .= '<table class=sortable cellspacing=1 border=' . $brdr . ' width=100%>' . "\r\n" . '        <thead>' . "\r\n" . '            <tr>' . "\r\n" . '               ' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['nospk'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['kontraktor'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['nilaikontrak'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['lokasi'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['pt'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['kegiatan'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['kontrak'] . ' ' . $_SESSION['lang']['jumlah'] . '</td>  ' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['noinvoice'] . ' ' . $_SESSION['lang']['supplier'] . ' </td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['noinvoice'] . ' Internal</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['nilaiinvoice'] . ' (Rp.)</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['tgljatuhtempo'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['pembayaran'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['pembayaran'] . '</td>' . "\r\n" . '                <td align=center>' . $_SESSION['lang']['sisa'] . '</td> ' . "\r\n" . '            </tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody id=container>';

	foreach ($noTrans as $der => $isi) {
		$aawal = 1;

		if ($jmlhRow2[$isi] == '') {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $isi . '</td>';
			$tab .= '<td>' . $noSupp[$isi] . '</td>';
			$tab .= '<td align=right>' . number_format($nilKontrak[$isi], 0) . '</td>';
			$tab .= '<td>' . $noUnit[$isi] . '</td>';
			$sInduk = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $noUnit[$isi] . '\'';

			#exit(mysql_error($conn));
			($qInduk = mysql_query($sInduk)) || true;
			$rInduk = mysql_fetch_assoc($qInduk);
			$tab .= '<td>' . $optNmKdorg[$rInduk['induk']] . '</td>';

			if ($noKeg[$isi] == '') {
				$tab .= '<td>' . $noInvoiceSupp[$isi][$aawal] . '</td>';
			}
			else {
				$tab .= '<td>' . $noKeg[$isi] . '</td>';
			}

			$tab .= '<td>' . $noTglBpp[$isi] . '</td>';
			$tab .= '<td align=right>' . number_format($noUang[$isi], 0) . '</td>';
			$tab .= '<td>\'' . $noInvoiceSupp[$isi][$aawal] . '</td>';

			if ($noInvoice[$isi][$aawal] != '') {
				$tab .= '<td>\'' . $noInvoice[$isi][$aawal] . '</td>';
			}
			else {
				$tab .= '<td>' . $noInvoice[$isi][$aawal] . '</td>';
			}

			$inv = $noInvoice[$isi][$aawal];
			$tab .= '<td align=right>' . number_format($noNilInv[$isi][$aawal], 0) . '</td>';
			$tab .= '<td>' . $noTglJthtem[$isi][$aawal] . '</td>';
			$sisa = $noNilInv[$isi][$aawal] - $dByr[$isi][$inv];
			$tab .= '<td align=right>' . number_format($dByr[$isi][$inv], 0) . '</td>';
			$tab .= '<td>' . $arrtglbyr[$isi][$inv] . '</td>';
			$tab .= '<td align=right>' . number_format($sisa, 0) . '</td>';
			$tab .= '</tr>';
		}
		else {
			$aawal = 1;

			while ($aawal <= $jmlhRow2[$isi]) {
				$sInduk = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $noUnit[$isi] . '\'';

				#exit(mysql_error($conn));
				($qInduk = mysql_query($sInduk)) || true;
				$rInduk = mysql_fetch_assoc($qInduk);
				$tab .= '<tr class=rowcontent>';

				if ($aawal == 1) {
					$tab .= '<td>' . $isi . '</td>';
					$tab .= '<td>' . $noSupp[$isi] . '</td>';
					$tab .= '<td align=right>' . number_format($nilKontrak[$isi], 0) . '</td>';
					$tab .= '<td>' . $noUnit[$isi] . '</td>';
					$tab .= '<td>' . $optNmKdorg[$rInduk['induk']] . '</td>';
				}
				else if ($aawal == 2) {
					$tab .= '<td rowspan=\'' . ($jmlhRow2[$isi] - 1) . '\' colspan=5>&nbsp;</td>';
				}

				if ($noKeg[$isi] == '') {
					$tab .= '<td>' . $noInvoiceSupp[$isi][$aawal] . '</td>';
				}
				else {
					$tab .= '<td>' . $noKeg[$isi] . '</td>';
				}

				if ($tglBapp != $noTglBpp[$isi]) {
					$tglBapp = $noTglBpp[$isi];
					$tab .= '<td>' . $noTglBpp[$isi] . '</td>';
					$tab .= '<td align=right>' . number_format($noUang[$isi], 0) . '</td>';
				}
				else {
					$tab .= '<td colspan=2>&nbsp</td>';
				}

				$tab .= '<td>\'' . $noInvoiceSupp[$isi][$aawal] . '</td>';

				if ($noInvoice[$isi][$aawal] != '') {
					$tab .= '<td>\'' . $noInvoice[$isi][$aawal] . '</td>';
				}
				else {
					$tab .= '<td>' . $noInvoice[$isi][$aawal] . '</td>';
				}

				$inv = $noInvoice[$isi][$aawal];
				$tab .= '<td align=right>' . number_format($noNilInv[$isi][$aawal], 0) . '</td>';
				$tab .= '<td>' . $noTglJthtem[$isi][$aawal] . '</td>';
				$sisa = $noNilInv[$isi][$aawal] - $dByr[$isi][$inv];
				$tab .= '<td align=right>' . number_format($dByr[$isi][$inv], 0) . '</td>';
				$tab .= '<td>' . $arrtglbyr[$isi][$inv] . '</td>';
				$tab .= '<td align=right>' . number_format($sisa, 0) . '</td>';
				$tab .= '</tr>';
				++$aawal;
			}
		}

		$sisa = 0;
	}

	$tab .= ' </tbody></table>';
}

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'Summary_Progress_SPK_2' . $periode;

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
			$this->Cell($width, $height, strtoupper('SUMMARY PROGRESS SPK'), 0, 1, 'L');
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
	$sUnit = 'select distinct koderekanan,namasupplier from ' . "\r\n" . '            ' . $dbname . '.log_spkht a left join ' . $dbname . '.log_5supplier b' . "\r\n" . '            on a.koderekanan=b.supplierid where left(a.tanggal,4)=\'' . $tahun . '\' order by namasupplier asc';

	#exit(mysql_error($conn));
	($qUnit = mysql_query($sUnit)) || true;

	while ($rUnit = mysql_fetch_assoc($qUnit)) {
		$optUnit .= '<option value=\'' . $rUnit['koderekanan'] . '\'>' . $rUnit['namasupplier'] . '</option>';
	}

	echo $optUnit;
	break;
}

?>
