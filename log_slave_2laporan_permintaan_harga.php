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

$_POST['kdBrg'] == '' ? $kdBrg = $_GET['kdBrg'] : $kdBrg = $_POST['kdBrg'];
$_POST['tglDr'] == '' ? $tglDr = tanggalsystem($_GET['tglDr']) : $tglDr = tanggalsystem($_POST['tglDr']);
$_POST['tglSmp'] == '' ? $tglSmp = tanggalsystem($_GET['tglSmp']) : $tglSmp = tanggalsystem($_POST['tglSmp']);
$sBrg = 'select kodebarang,namabarang,satuan from ' . $dbname . '.log_5masterbarang order by namabarang asc';
$qBrg = fetchData($sBrg);

foreach ($qBrg as $brsBrg => $arrBrg) {
	$rBrg[$arrBrg['kodebarang']] = $arrBrg['namabarang'];
	$rSat[$arrBrg['kodebarang']] = $arrBrg['satuan'];
}

$sSup = 'select supplierid,namasupplier from ' . $dbname . '.log_5supplier order by namasupplier asc';
$qSup = fetchData($sSup);

foreach ($qSup as $brsSup => $arrSup) {
	$rNmsup[$arrSup['supplierid']] = $arrSup['namasupplier'];
}

switch ($proses) {
case 'preview':
	if (($tglDr == '') || ($tglSmp == '') || ($kdBrg == '')) {
		echo 'warning:Field Data Tidak Boleh Kosong';
		exit();
	}

	$tab .= '<table cellspacing=1 border=0 class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td rowspan=2>No.</td>' . "\r\n\t\t" . '<td rowspan=2>' . $_SESSION['lang']['nopermintaan'] . '</td>' . "\r\n\t\t" . '<td rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t" . '<td rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n\t\t" . '<td rowspan=2>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t" . '<td align=center colspan=2>' . $_SESSION['lang']['masaberlaku'] . "\r\n" . '                    <table><tr><td align=center>' . $_SESSION['lang']['tgldari'] . '</td><td align=center>' . $_SESSION['lang']['tglsmp'] . '</td></tr></table></td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';
	$sData = 'select distinct a.tanggal,a.supplierid,a.purchaser,b.* from ' . $dbname . '.log_perintaanhargaht a left join ' . $dbname . '.log_permintaanhargadt b on a.nomor=b.nomor' . "\r\n" . '            where kodebarang=\'' . $kdBrg . '\' and (tgldari<=\'' . $tglDr . '\' or tgldari!=\'0000-00-00\') and (tglsmp<=\'' . $tglSmp . '\'  or tgldari!=\'0000-00-00\')';
	$data = fetchData($sData);

	foreach ($data as $row) {
		if ($row['nomor'] != '') {
			$no += 1;
			$tab .= '<tr class=\'rowcontent\'>';
			$tab .= '<td>' . $no . '</td>';
			$tab .= '<td>' . $row['nomor'] . '</td>';
			$tab .= '<td>' . $rBrg[$row['kodebarang']] . '</td>';
			$tab .= '<td>' . $rSat[$row['kodebarang']] . '</td>';
			$tab .= '<td>' . $rNmsup[$row['supplierid']] . '</td>';
			$tab .= '<td align=right>' . number_format($row['harga'], 2) . '</td>';
			$tab .= '<td align=right>' . tanggalnormal($row['tgldari']) . '</td>';
			$tab .= '<td align=center>' . tanggalnormal($row['tglsmp']) . '</td>';
			$tab .= '</tr>';
		}
	}

	echo $tab;
	break;

case 'pdf':
	if (($tglDr == '') || ($tglSmp == '') || ($kdBrg == '')) {
		echo 'warning:Field Data Tidak Boleh Kosong';
		exit();
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
			global $kdBrg;
			global $tglDr;
			global $tglSmp;
			$sAlmat = 'select namaorganisasi,alamat,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';

			#exit(mysql_error());
			($qAlamat = mysql_query($sAlmat)) || true;
			$rAlamat = mysql_fetch_assoc($qAlamat);
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
			$this->Cell($width, $height, $_SESSION['lang']['lapPenawaran'], 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell($width, $height, $_SESSION['lang']['masaberlaku'] . ' : ' . $_GET['tglDr'] . ' s.d. ' . $_GET['tglSmp'], 0, 1, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['nopermintaan'], 1, 0, 'C', 1);
			$this->Cell((22 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((22 / 100) * $width, $height, $_SESSION['lang']['namasupplier'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['hargasatuan'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['tgldari'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['tglsmp'], 1, 1, 'C', 1);
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
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$sData = 'select distinct a.tanggal,a.supplierid,a.purchaser,b.* from ' . $dbname . '.log_perintaanhargaht a left join ' . $dbname . '.log_permintaanhargadt b on a.nomor=b.nomor' . "\r\n" . '                where kodebarang=\'' . $kdBrg . '\' and (tgldari<=\'' . $tglDr . '\' or tgldari!=\'0000-00-00\') and (tglsmp<=\'' . $tglSmp . '\'  or tgldari!=\'0000-00-00\')';
	$data = fetchData($sData);

	foreach ($data as $row) {
		$no += 1;

		if ($row['nomor'] != '') {
			$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
			$pdf->Cell((13 / 100) * $width, $height, $row['nomor'], 1, 0, 'C', 1);
			$pdf->Cell((22 / 100) * $width, $height, $rBrg[$row['kodebarang']], 1, 0, 'L', 1);
			$pdf->Cell((6 / 100) * $width, $height, $rSat[$row['kodebarang']], 1, 0, 'C', 1);
			$pdf->Cell((22 / 100) * $width, $height, $rNmsup[$row['supplierid']], 1, 0, 'L', 1);
			$pdf->Cell((10 / 100) * $width, $height, number_format($row['harga'], 2), 1, 0, 'R', 1);
			$pdf->Cell((10 / 100) * $width, $height, tanggalnormal($row['tgldari']), 1, 0, 'C', 1);
			$pdf->Cell((10 / 100) * $width, $height, tanggalnormal($row['tglsmp']), 1, 1, 'C', 1);
		}
	}

	$pdf->Output();
	break;

case 'excel':
	if (($tglDr == '') || ($tglSmp == '') || ($kdBrg == '')) {
		echo 'warning:Field Data Tidak Boleh Kosong';
		exit();
	}

	$tab .= '<table cellspacing=1 border=1 class=sortable>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center rowspan=2>No.</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center rowspan=2>' . $_SESSION['lang']['nopermintaan'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center rowspan=2>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center colspan=2 rowspan=2>' . $_SESSION['lang']['masaberlaku'] . "\r\n" . '                    <table><tr>' . "\r\n" . '                        <td align=center>' . $_SESSION['lang']['tgldari'] . '</td>' . "\r\n" . '                        <td align=center>' . $_SESSION['lang']['tglsmp'] . '</td>' . "\r\n" . '                            </tr>' . "\r\n" . '                            </table></td>' . "\r\n\t" . '</tr></table><table cellspacing=1 border=1 class=sortable>' . "\r\n\t";
	$sData = 'select distinct a.tanggal,a.supplierid,a.purchaser,b.* from ' . $dbname . '.log_perintaanhargaht a left join ' . $dbname . '.log_permintaanhargadt b on a.nomor=b.nomor' . "\r\n" . '            where kodebarang=\'' . $kdBrg . '\' and (tgldari<=\'' . $tglDr . '\' or tgldari!=\'0000-00-00\') and (tglsmp<=\'' . $tglSmp . '\'  or tgldari!=\'0000-00-00\')';
	$data = fetchData($sData);

	foreach ($data as $row) {
		if ($row['nomor'] != '') {
			$no += 1;
			$tab .= '<tr class=\'rowcontent\'>';
			$tab .= '<td>' . $no . '</td>';
			$tab .= '<td>' . $row['nomor'] . '</td>';
			$tab .= '<td>' . $rBrg[$row['kodebarang']] . '</td>';
			$tab .= '<td>' . $rSat[$row['kodebarang']] . '</td>';
			$tab .= '<td>' . $rNmsup[$row['supplierid']] . '</td>';
			$tab .= '<td align=right>' . number_format($row['harga'], 2) . '</td>';
			$tab .= '<td align=right>' . $row['tgldari'] . '</td>';
			$tab .= '<td align=center>' . $row['tglsmp'] . '</td>';
			$tab .= '</tr>';
		}
	}

	$tab .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'laporanPermintaan' . $dte;

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
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;

case 'getTgl':
	if ($periode != '') {
		$tgl = $periode;
		$tanggal = $tgl[0] . '-' . $tgl[1];
	}
	else if ($period != '') {
		$tgl = $period;
		$tanggal = $tgl[0] . '-' . $tgl[1];
	}

	if ($kdUnit == '') {
		$kdUnit = $_SESSION['lang']['lokasitugas'];
	}

	$sTgl = 'select distinct tanggalmulai,tanggalsampai from ' . $dbname . '.sdm_5periodegaji where kodeorg=\'' . substr($kdUnit, 0, 4) . '\' and periode=\'' . $tanggal . '\' ';

	#exit(mysql_error());
	($qTgl = mysql_query($sTgl)) || true;
	$rTgl = mysql_fetch_assoc($qTgl);
	echo tanggalnormal($rTgl['tanggalmulai']) . '###' . tanggalnormal($rTgl['tanggalsampai']);
	break;
}

?>
