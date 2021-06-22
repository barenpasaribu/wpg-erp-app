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

$kdGudang = $_POST['kdGudang'];
$periode = $_POST['periode'];

 $optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

switch ($proses) {
case 'getPeriode':
	$optorg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sOrg = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';//where kodeorg=\'' . $kdGudang . '\'';

	#exit(mysql_error());
	($qOrg = mysql_query($sOrg)) || true;

	while ($rOrg = mysql_fetch_assoc($qOrg)) {
		$optorg .= '<option value=' . $rOrg['periode'] . '>' . substr(tanggalnormal($rOrg['periode']), 1, 8) . '</option>';
	}

	echo $optorg;
	break;

case 'preview':
	if ($_SESSION['language'] == 'EN') {
		$zz = 'namaakun1 as namaakun';
	}
	else {
		$zz = 'namaakun';
	}

	$sAkun = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun order by noakun';

	#exit(mysql_error($conn));
	($qAkun = mysql_query($sAkun)) || true;

	while ($rAkun = mysql_fetch_assoc($qAkun)) {
		$hslAkun[$rAkun['noakun']] = $rAkun['namaakun'];
	}

	if (($kdGudang == '') || ($periode == '')) {
		echo 'warning:Gudang  dan Periode Tidak Boleh Kosong';
		exit();
	}

	$tab .= '<table cellspacing=1 border=0 class=sortable>' . "\r\n" . '        <thead class=rowheader>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>No.</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['nojurnal'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['noakundisplay'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['akun'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['debet'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kredit'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['blok'] . '</td><td>Nama Blok</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kodevhc'] . '</td>' . "\r\n\r\n" . '        </tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody>';
	$where = ' tanggal like \'%' . $periode . '%\' and noreferensi like \'%' . $kdGudang . '%\' ' . "\r\n" . '                and noreferensi in(select distinct notransaksi from ' . $dbname . '.log_transaksi_vw where kodegudang=\'' . $kdGudang . '\' and kodeorg=\'' . substr($kdGudang, 0, 4) . '\')';
	
	$sData = 'select nojurnal,tanggal,noreferensi,keterangan,noakun,debet,kredit,kodeblok,kodevhc from ' . $dbname . '.keu_jurnaldt_vw ' . "\r\n" . '            where ' . $where . '';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>' . "\r\n" . '                <td>' . $no . '</td>' . "\r\n" . '                <td>' . $rData['nojurnal'] . '</td>' . "\r\n" . '                <td>' . tanggalnormal($rData['tanggal']) . '</td>' . "\r\n" . '                <td>' . $rData['noreferensi'] . '</td>' . "\r\n" . '                <td>' . $rData['keterangan'] . '</td>' . "\r\n" . '                <td>' . $rData['noakun'] . '</td>' . "\r\n" . '                <td>' . $hslAkun[$rData['noakun']] . '</td>' . "\r\n" . '                <td align=right>' . number_format($rData['debet']) . '</td>' . "\r\n" . '                <td align=right>' . number_format($rData['kredit']) . '</td><td>' . $rData['kodeblok'] . '</td><td>'.$optNm[$rData['kodeblok']]. '</td><td>' . $rData['kodevhc'] . '</td>' . "\r\n\r\n" . '        </tr>';
		$totalDebet += $rData['debet'];
		$totalKredit += $rData['kredit'];
	}

	$tab .= '<tr><td colspan=\'7\' align=right>Total</td><td align=right>' . number_format($totalDebet, 2) . '</td><td align=right>' . number_format($totalDebet, 2) . '</td>';
	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'pdf':
	$kdGudang = $_GET['kdGudang'];
	$periode = $_GET['periode'];

	if ($_SESSION['language'] == 'EN') {
		$zz = 'namaakun1 as namaakun';
	}
	else {
		$zz = 'namaakun';
	}

	$sAkun = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun order by noakun';

	#exit(mysql_error($conn));
	($qAkun = mysql_query($sAkun)) || true;

	while ($rAkun = mysql_fetch_assoc($qAkun)) {
		$hslAkun[$rAkun['noakun']] = $rAkun['namaakun'];
	}
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
			global $periode;
			global $kdGudang;
			global $hslAkun;
			global $where;
			$sAlmat = 'select namaorganisasi,alamat,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';

			#exit(mysql_error());
			($qAlamat = mysql_query($sAlmat)) || true;
			$rAlamat = mysql_fetch_assoc($qAlamat);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 11;

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
			$this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $rAlamat['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 11);
			$this->Cell($width, $height, $_SESSION['lang']['lapAlokasiBrg'], 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ': ' . $_GET['kdGudang'], 0, 1, 'C');
			$this->Cell($width, $height, $_SESSION['lang']['periode'] . ': ' . $_GET['periode'], 0, 1, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((2 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['nojurnal'], 1, 0, 'C', 1);
			$this->Cell((5 / 100) * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['notransaksi'], 1, 0, 'C', 1);
			$this->Cell((21 / 100) * $width, $height, $_SESSION['lang']['keterangan'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['noakundisplay'], 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['akun'], 1, 0, 'C', 1);
			$this->Cell((5 / 100) * $width, $height, $_SESSION['lang']['debet'], 1, 0, 'C', 1);
			$this->Cell((5 / 100) * $width, $height, $_SESSION['lang']['kredit'], 1, 0, 'C', 1);
			$this->Cell((7 / 100) * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
			$this->Cell((7 / 100) * $width, $height, 'Nama Blok', 1, 0, 'C', 1);
			$this->Cell((7 / 100) * $width, $height, $_SESSION['lang']['kodevhc'], 1, 1, 'C', 1);
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
	$height = 9;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$where = ' tanggal like \'%' . $periode . '%\' and noreferensi like \'%' . $kdGudang . '%\' ' . "\r\n" . '                and noreferensi in(select distinct notransaksi from ' . $dbname . '.log_transaksi_vw where kodegudang=\'' . $kdGudang . '\' and kodeorg=\'' . substr($kdGudang, 0, 4) . '\')';
	$sData = 'select nojurnal,tanggal,noreferensi,keterangan,noakun,debet,kredit,kodeblok,kodevhc from ' . $dbname . '.keu_jurnaldt_vw ' . "\r\n" . '            where ' . $where . '';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$no += 1;
		$pdf->Cell((2 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((13 / 100) * $width, $height, $rData['nojurnal'], 1, 0, 'L', 1);
		$pdf->Cell((5 / 100) * $width, $height, tanggalnormal($rData['tanggal']), 1, 0, 'C', 1);
		$pdf->Cell((12 / 100) * $width, $height, $rData['noreferensi'], 1, 0, 'L', 1);
		$pdf->Cell((21 / 100) * $width, $height, $rData['keterangan'], 1, 0, 'L', 1);
		$pdf->Cell((6 / 100) * $width, $height, $rData['noakun'], 1, 0, 'C', 1);
		$pdf->Cell((13 / 100) * $width, $height, $hslAkun[$rData['noakun']], 1, 0, 'L', 1);
		$pdf->Cell((5 / 100) * $width, $height, number_format($rData['debet']), 1, 0, 'R', 1);
		$pdf->Cell((5 / 100) * $width, $height, number_format($rData['kredit']), 1, 0, 'R', 1);
		$pdf->Cell((7 / 100) * $width, $height, $rData['kodeblok'], 1, 0, 'L', 1);
		$pdf->Cell((7 / 100) * $width, $height, $optNm[$rData['kodeblok']], 1, 0, 'L', 1);
		$pdf->Cell((7 / 100) * $width, $height, $rData['kodevhc'], 1, 1, 'L', 1);
		+($totalDebet += $rData['debet']);
		$totalKredit += $rData['kredit'];
	}

	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell((74 / 100) * $width, $height, 'Total', 1, 0, 'R', 1);
	$pdf->Cell((7 / 100) * $width, $height, number_format($totalDebet, 2), 1, 0, 'R', 1);
	$pdf->Cell((7 / 100) * $width, $height, number_format($totalKredit, 2), 1, 0, 'R', 1);
	$pdf->Cell((14 / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Output();
	break;

case 'excel':
	$kdGudang = $_GET['kdGudang'];
	$periode = $_GET['periode'];

	if ($_SESSION['language'] == 'EN') {
		$zz = 'namaakun1 as namaakun';
	}
	else {
		$zz = 'namaakun';
	}

	$sAkun = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun order by noakun';

	#exit(mysql_error($conn));
	($qAkun = mysql_query($sAkun)) || true;

	while ($rAkun = mysql_fetch_assoc($qAkun)) {
		$hslAkun[$rAkun['noakun']] = $rAkun['namaakun'];
	}

	if (($kdGudang == '') || ($periode == '')) {
		echo 'warning:Gudang  dan Periode Tidak Boleh Kosong';
		exit();
	}

	$tab .= '<table border=0 cellpading=1 ><tr><td colspan=11 align=center>' . $_SESSION['lang']['lapAlokasiBrg'] . '</td></tr>' . "\r\n" . '                <tr><td colspan=3>' . $_SESSION['lang']['periode'] . '</td><td colspan=4 align=left>' . substr(tanggalnormal($periode), 1, 9) . '</td></tr>    ' . "\r\n" . '                <tr><td colspan=3>' . $_SESSION['lang']['unit'] . '</td><td colspan=4 align=left>' . ($kdGudang != '' ? $kdGudang : $_SESSION['lang']['all']) . '</td></tr>' . "\r\n\r\n" . '                </table>';
	$tab .= '<table cellspacing=1 border=1 class=sortable>' . "\r\n" . '        <thead class=rowheader>' . "\r\n" . '        <tr>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>No.</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nojurnal'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['noakundisplay'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['akun'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['debet'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kredit'] . '</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['blok'] . '</td>' . "\r\n" . '
						<td bgcolor=#DEDEDE align=center>Nama Blok</td>' . "\r\n" . '
						<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodevhc'] . '</td>' . "\r\n\r\n" . '        </tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody>';
	$where = ' tanggal like \'%' . $periode . '%\' and noreferensi like \'%' . $kdGudang . '%\' ' . "\r\n" . '                and noreferensi in(select distinct notransaksi from ' . $dbname . '.log_transaksi_vw where kodegudang=\'' . $kdGudang . '\' and kodeorg=\'' . substr($kdGudang, 0, 4) . '\')';
	$sData = 'select nojurnal,tanggal,noreferensi,keterangan,noakun,debet,kredit,kodeblok,kodevhc from ' . $dbname . '.keu_jurnaldt_vw ' . "\r\n" . '            where ' . $where . '';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>' . "\r\n" . '                <td>' . $no . '</td>' . "\r\n" . '                <td>' . $rData['nojurnal'] . '</td>' . "\r\n" . '                <td>' . tanggalnormal($rData['tanggal']) . '</td>' . "\r\n" . '                <td>' . $rData['noreferensi'] . '</td>' . "\r\n" . '                <td>' . $rData['keterangan'] . '</td>' . "\r\n" . '                <td>' . $rData['noakun'] . '</td>' . "\r\n" . '                <td>' . $hslAkun[$rData['noakun']] . '</td>' . "\r\n" . '                <td align=right>' . number_format($rData['debet']) . '</td>' . "\r\n" . '                <td align=right>' . number_format($rData['kredit']) . '</td>' . "\r\n" . '<td>' . $rData['kodeblok'] . '</td><td>' . $optNm[$rData['kodeblok']] . '</td> <td>' . $rData['kodevhc'] . '</td>' . "\r\n\r\n" . '        </tr>';
		$totalDebet += $rData['debet'];
		$totalKredit += $rData['kredit'];
	}

	$tab .= '<tr><td colspan=\'7\' align=right>Total</td><td align=right>' . number_format($totalDebet, 2) . '</td><td align=right>' . number_format($totalDebet, 2) . '</td>';
	$tab .= '</tbody></table>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$tglSkrng = date('Ymd');
	$nop_ = 'Laporan_Alokasi_Pemakai_Brg' . $tglSkrng;

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
			echo '<script language=javascript1.2>' . "\r\n" . '                        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                        </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                        </script>';
		}

		closedir($handle);
	}

	break;
}

?>
