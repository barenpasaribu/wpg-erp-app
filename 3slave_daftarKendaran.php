<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdKbn = $_POST['kdKbn'];
$klpmkVhc = $_POST['klpmkVhc'];

if ($_SESSION['language'] == 'EN') {
	$arrKlmpk = array('KD' => 'Vehicle', 'MS' => 'Machinery', 'AB' => 'Heavy Equipment');
}
else {
	$arrKlmpk = array('KD' => 'Kendaraan', 'MS' => 'Mesin', 'AB' => 'Alat Berat');
}

switch ($proses) {
case 'preview':
	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n\t" . '<thead>' . "\r\n" . ' ' . "\t\t" . '<tr class=rowheader>' . "\r\n\t\t" . '  <td>No</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['kodeorganisasi'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . '   <td align=center>' . str_replace(' ', '<br>', $_SESSION['lang']['jenkendabmes']) . '</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['kodenopol'] . '</td>' . "\t\t\r\n" . '           <td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\t\t\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['tahunperolehan'] . '</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['beratkosong'] . '</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['nomorrangka'] . '</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['nomormesin'] . '</td>' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['detail'] . '</td>' . "\t" . '   ' . "\r\n\t\t" . '   <td align=center>' . $_SESSION['lang']['kepemilikan'] . '</td>' . "\r\n\t\t" . '  </tr>' . "\r\n\t\t" . '   </thead><tbody>' . "\r\n\t";

	if (($kdKbn != '0') && ($klpmkVhc != '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master where kodeorg=\'' . $kdKbn . '\' and kelompokvhc=\'' . $klpmkVhc . '\' order by kodeorg,kodevhc';
	}
	else if (($kdKbn != '0') && ($klpmkVhc == '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master where kodeorg=\'' . $kdKbn . '\' order by kodeorg,kodevhc';
	}
	else if (($kdKbn == '0') && ($klpmkVhc != '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master where kelompokvhc=\'' . $klpmkVhc . '\' order by kodeorg,kodevhc';
	}
	else if (($kdKbn == '0') && ($klpmkVhc == '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master order by kodeorg,kodevhc';
	}

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$row = mysql_num_rows($query);

	if (0 < $row) {
		while ($res = mysql_fetch_assoc($query)) {
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$no += 1;
			$namabarang = $rBrg['namabarang'];
			$sJnsvhc = 'select namajenisvhc from ' . $dbname . '.vhc_5jenisvhc where jenisvhc=\'' . $res['jenisvhc'] . '\'';

			#exit(mysql_error());
			($qJnsVhc = mysql_query($sJnsvhc)) || true;
			$rJnsVhc = mysql_fetch_assoc($qJnsVhc);

			if ($res['kepemilikan'] == 1) {
				$dptk = $_SESSION['lang']['miliksendiri'];
			}
			else {
				$dptk = $_SESSION['lang']['sewa'];
			}

			echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . ' <td>' . $no . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['kodeorg'] . '</td>' . "\t\t\t\t" . ' ' . "\r\n\t\t\t\t" . ' <td>' . $rJnsVhc['namajenisvhc'] . '</td>' . "\t\t\t" . ' ' . "\t\t\r\n\t\t\t\t" . ' <td>' . $res['kodevhc'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $namabarang . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['tahunperolehan'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['noakun'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['beratkosong'] . '</td>' . "\t\t\r\n\t\t\t\t" . ' <td>' . $res['nomorrangka'] . '</td>' . "\t\r\n\t\t\t\t" . ' <td>' . $res['nomormesin'] . '</td> ' . "\r\n\t\t\t\t" . ' <td>' . $res['detailvhc'] . '</td> ' . "\t\r\n\t\t\t\t" . ' <td>' . $dptk . '</td>' . "\t\t\r\n\t\t\t" . '</tr>' . "\r\n\t\t\t";
		}
	}
	else {
		echo '<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>';
	}

	echo '</tbody></table>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $conn;
			global $dbname;
			global $align;
			global $length;
			global $colArr;
			global $title;
			global $kdKbn;
			global $klpmkVhc;
			global $sDet;
			global $arrKlmpk;
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
			$orgData = fetchData($query);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 15;

			if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
				$path = 'images/SSP_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
				$path = 'images/MI_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
				$path = 'images/HS_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
				$path = 'images/BM_logo.jpg';
			}

			$this->Image($path, $this->lMargin, $this->tMargin, 70);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(100);
			$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->SetFont('Arial', 'B', 12);
			$this->SetFont('Arial', '', 8);

			if (($kdKbn != '0') && ($klpmkVhc != '0')) {
				$sDet = 'select * from ' . $dbname . '.vhc_5master where kodeorg=\'' . $kdKbn . '\' and kelompokvhc=\'' . $klpmkVhc . '\' order by kodeorg,kodevhc';
				$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['unit'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((45 / 100) * $width, $height, $kdKbn, '', 0, 'L');
				$this->Ln();
				$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['kodekelompok'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((45 / 100) * $width, $height, $arrKlmpk[$klpmkVhc], '', 0, 'L');
				$this->Ln();
			}
			else if (($kdKbn != '0') && ($klpmkVhc == '0')) {
				$sDet = 'select * from ' . $dbname . '.vhc_5master where kodeorg=\'' . $kdKbn . '\' order by kodeorg,kodevhc';
				$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['unit'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((45 / 100) * $width, $height, $kdKbn, '', 0, 'L');
				$this->Ln();
			}
			else if (($kdKbn == '0') && ($klpmkVhc != '0')) {
				$sDet = 'select * from ' . $dbname . '.vhc_5master where kelompokvhc=\'' . $klpmkVhc . '\' order by kodeorg,kodevhc';
				$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['kodekelompok'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((45 / 100) * $width, $height, $arrKlmpk[$klpmkVhc], '', 0, 'L');
				$this->Ln();
			}
			else if (($kdKbn == '0') && ($klpmkVhc == '0')) {
				$sDet = 'select * from ' . $dbname . '.vhc_5master order by kodeorg,kodevhc';
			}

			$this->SetFont('Arial', 'U', 12);
			$this->Cell($width, $height, $_SESSION['lang']['laporanKendAb'], 0, 1, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);

			if ($kdKbn == '0') {
				$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['kodeorganisasi'], 1, 0, 'C', 1);
			}

			if ($klpmkVhc == '0') {
				$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['kodekelompok'], 1, 0, 'C', 1);
			}

			$this->Cell((17 / 100) * $width, $height, $_SESSION['lang']['jenkendabmes'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['kodenopol'], 1, 0, 'C', 1);
			$this->Cell((11 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['tahunperolehan'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['beratkosong'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['nomorrangka'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['nomormesin'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['kepemilikan'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$kdKbn = $_GET['kdKbn'];
	$klpmkVhc = $_GET['klpmkVhc'];
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;

	while ($rDet = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rDet['kodebarang'] . '\'';

		#exit(mysql_error());
		($qBrg = mysql_query($sBrg)) || true;
		$rBrg = mysql_fetch_assoc($qBrg);
		$sJnsvhc = 'select namajenisvhc from ' . $dbname . '.vhc_5jenisvhc where jenisvhc=\'' . $rDet['jenisvhc'] . '\'';

		#exit(mysql_error());
		($qJnsVhc = mysql_query($sJnsvhc)) || true;
		$rJnsVhc = mysql_fetch_assoc($qJnsVhc);
		$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);

		if ($kdKbn == '0') {
			$pdf->Cell((8 / 100) * $width, $height, $rDet['kodeorg'], 1, 0, 'C', 1);
		}

		if ($klpmkVhc == '0') {
			$pdf->Cell((8 / 100) * $width, $height, $arrKlmpk[$rDet['kelompokvhc']], 1, 0, 'C', 1);
		}

		if ($res['kepemilikan'] == 1) {
			$dptk = $_SESSION['lang']['miliksendiri'];
		}
		else {
			$dptk = $_SESSION['lang']['sewa'];
		}

		$pdf->Cell((17 / 100) * $width, $height, $rJnsVhc['namajenisvhc'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $rDet['kodevhc'], 1, 0, 'C', 1);
		$pdf->Cell((11 / 100) * $width, $height, $rBrg['namabarang'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $rDet['tahunperolehan'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $rDet['noakun'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $rDet['beratkosong'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $rDet['nomorrangka'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $rDet['nomormesin'], 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $dptk, 1, 1, 'C', 1);
	}

	$pdf->Output();
	break;

case 'excel':
	$kdKbn = $_GET['kdKbn'];
	$klpmkVhc = $_GET['klpmkVhc'];

	if (($kdKbn != '0') && ($klpmkVhc != '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master where kodeorg=\'' . $kdKbn . '\' and kelompokvhc=\'' . $klpmkVhc . '\' order by kodeorg,kodevhc';
		$tbl = '<tr><td colspan=3>' . $_SESSION['lang']['unit'] . '</td><td>' . $kdKbn . '</td></tr>' . "\r\n\t\t\t" . '<tr><td colspan=3>' . $_SESSION['lang']['kodekelompok'] . '</td><td>' . $klpmkVhc . '</td></tr>';
	}
	else if (($kdKbn != '0') && ($klpmkVhc == '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master where kodeorg=\'' . $kdKbn . '\' order by kodeorg,kodevhc';
		$tbl = '<tr><td colspan=3>' . $_SESSION['lang']['unit'] . '</td><td>' . $kdKbn . '</td></tr>';
	}
	else if (($kdKbn == '0') && ($klpmkVhc != '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master where kelompokvhc=\'' . $klpmkVhc . '\' order by kodeorg,kodevhc';
		$tbl = '<tr><td colspan=3>' . $_SESSION['lang']['kodekelompok'] . '</td><td>' . $klpmkVhc . '</td></tr>';
	}
	else if (($kdKbn == '0') && ($klpmkVhc == '0')) {
		$sql = 'select * from ' . $dbname . '.vhc_5master order by kodeorg,kodevhc';
		$tbl = '';
	}

	$stream .= "\r\n\t\t\t" . '<table>' . "\r\n\t\t\t" . '<tr><td colspan=13 align=center>' . $_SESSION['lang']['laporanKendAb'] . '</td></tr>' . "\r\n\t\t\t" . $tbl . "\r\n\t\t\t" . '<tr><td colspan=3></td><td></td></tr>' . "\r\n\t\t\t" . '</table>' . "\r\n\t\t\t" . '<table border=1>' . "\r\n\t\t\t" . '<tr>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>No.</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['kodeorganisasi'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['kodekelompok'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . str_replace(' ', '<br>', $_SESSION['lang']['jenkendabmes']) . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['kodenopol'] . '</td>' . "\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['namabarang'] . '</td>' . "\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['tahunperolehan'] . '</td>' . "\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['noakun'] . '</td>' . "\t\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['beratkosong'] . '</td>' . "\t\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['nomorrangka'] . '</td>' . "\t\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['nomormesin'] . '</td>' . "\t\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['detail'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center valign=top>' . $_SESSION['lang']['kepemilikan'] . '</td>' . "\t\r\n\t\t\t" . '</tr>';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$row = mysql_num_rows($query);

	if (0 < $row) {
		while ($res = mysql_fetch_assoc($query)) {
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$no += 1;
			$namabarang = $rBrg['namabarang'];

			if ($res['kepemilikan'] == 1) {
				$dptk = $_SESSION['lang']['miliksendiri'];
			}
			else {
				$dptk = $_SESSION['lang']['sewa'];
			}

			$stream .= '<tr class=rowcontent>' . "\r\n\t\t\t\t" . ' <td>' . $no . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['kodeorg'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $arrKlmpk[$res['kelompokvhc']] . '</td>' . "\t\t\t\t" . ' ' . "\r\n\t\t\t\t" . ' <td>' . $res['jenisvhc'] . '</td>' . "\t\t\t" . ' ' . "\t\t\r\n\t\t\t\t" . ' <td>' . $res['kodevhc'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $namabarang . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['tahunperolehan'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['noakun'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $res['beratkosong'] . '</td>' . "\t\t\r\n\t\t\t\t" . ' <td>' . $res['nomorrangka'] . '</td>' . "\t\r\n\t\t\t\t" . ' <td>' . $res['nomormesin'] . '</td> ' . "\r\n\t\t\t\t" . ' <td>' . $res['detailvhc'] . '</td> ' . "\t\r\n\t\t\t\t" . ' <td>' . $dptk . '</td>' . "\t\t\r\n\t\t\t" . '</tr>' . "\r\n\t\t\t";
		}
	}
	else {
		$stream .= '<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>';
	}

	$stream .= '</tbody></table>';
	$stream .= 'Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'daftarKendaraan';

	if (0 < strlen($stream)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $stream)) {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;

case 'getDetail':
	echo '<link rel=stylesheet type=text/css href=style/generic.css>';
	$nokontrak = $_GET['nokontrak'];
	$sHed = 'select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ' . $dbname . '.pmn_kontrakjual a where a.nokontrak=\'' . $nokontrak . '\'';

	#exit(mysql_error());
	($qHead = mysql_query($sHed)) || true;
	$rHead = mysql_fetch_assoc($qHead);
	$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rHead['kodebarang'] . '\'';

	#exit(mysql_error());
	($qBrg = mysql_query($sBrg)) || true;
	$rBrg = mysql_fetch_assoc($qBrg);
	$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $rHead['koderekanan'] . '\'';

	#exit(mysql_error());
	($qCust = mysql_query($sCust)) || true;
	$rCust = mysql_fetch_assoc($qCust);
	echo '<fieldset><legend>' . $_SESSION['lang']['detailPengiriman'] . '</legend>' . "\r\n\t" . '<table cellspacing=1 border=0 class=myinputtext>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['NoKontrak'] . '</td><td>:</td><td>' . $nokontrak . '</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['tglKontrak'] . '</td><td>:</td><td>' . tanggalnormal($rHead['tanggalkontrak']) . '</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['komoditi'] . '</td><td>:</td><td>' . $rBrg['namabarang'] . '</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['Pembeli'] . '</td><td>:</td><td>' . $rCust['namacustomer'] . '</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '</table><br />' . "\r\n\t" . '<table cellspacing=1 border=0 class=sortable><thead>' . "\r\n\t" . '<tr class=data>' . "\r\n\t" . '<td>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['nodo'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['nosipb'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['beratnormal'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['kodenopol'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['sopir'] . '</td>' . "\r\n\t" . '</tr></thead><tbody>' . "\r\n\t";
	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $nokontrak . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$rCek = mysql_num_rows($qDet);

	if (0 < $rCek) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			echo '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td>' . $rDet['notransaksi'] . '</td>' . "\r\n\t\t\t" . '<td>' . tanggalnormal($rDet['tanggal']) . '</td>' . "\r\n\t\t\t" . '<td>' . $rDet['nodo'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rDet['nosipb'] . '</td>' . "\r\n\t\t\t" . '<td align=right>' . number_format($rDet['beratbersih'], 2) . '</td>' . "\r\n\t\t\t" . '<td>' . $rDet['nokendaraan'] . '</td>' . "\r\n\t\t\t" . '<td>' . ucfirst($rDet['supir']) . '</td>' . "\r\n\t\t\t" . '</tr>';
		}
	}
	else {
		echo '<tr><td colspan=7>Not Found</td></tr>';
	}

	echo '</tbody></table></fieldset>';
	break;
}

?>
