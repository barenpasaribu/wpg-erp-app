<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$ev = $_GET['ev'];
$thnbudget = $_POST['thnbudget'];
$kdWs = $_POST['kdWs'];
$kdTrak = $_POST['kdTrak'];
$brsKe = $_POST['brsKe'];
$_POST['kdeWs'] == '' ? $kdeWs = $_GET['kdeWs'] : $kdeWs = $_POST['kdeWs'];
$_POST['kodeTraksi'] == '' ? $kodeTraksi = $_GET['kodeTraksi'] : $kodeTraksi = $_POST['kodeTraksi'];
$_POST['thnBudget'] == '' ? $thnBudget = $_GET['thnBudget'] : $thnBudget = $_POST['thnBudget'];
$_POST['thnbudget'] == '' ? $thnbudget = $_GET['thnbudget'] : $thnbudget = $_POST['thnbudget'];
$_POST['kdWs'] == '' ? $kdWs = $_GET['kdWs'] : $kdWs = $_POST['kdWs'];
$str = 'select namakaryawan,karyawanid from ' . $dbname . '.datakaryawan where karyawanid=' . $_SESSION['standard']['userid'] . '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namakar[$bar->karyawanid] = $bar->namakaryawan;
}

$data = '<table border=0 cellspacing=0><tr><td colspan=2 scope=row>Summary ' . $_SESSION['lang']['laporanrpjambengkel'] . '</td></tr><tr><td colspan=5 scope=row>' . $_SESSION['lang']['tahun'] . ' : ' . $thnbudget . ' <td></tr><tr><td colspan=2>' . $_SESSION['lang']['unit'] . ' : ' . $kdWs . '</td></tr></table>';
$data .= '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '          <thead>' . "\r\n" . '          <tr class=rowheader>' . "\r\n\t\t\t" . '<td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\t\t" . '  ' . "\r\n\t\t" . '    <td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['workshop'] . '</td>' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['rpperthn'] . '</td> ' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['jamperthn'] . '</td> ' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['rpperjam'] . '</td>' . "\r\n" . '          </tr>' . "\r\n" . '          </thead>';
$no = 0;
$sList = 'select * from ' . $dbname . '.bgt_biaya_ws_per_jam where tahunbudget=\'' . $thnbudget . '\' and kodews=\'' . $kdWs . '\' order by kodews ';

#exit(mysql_error());
($qList = mysql_query($sList)) || true;

while ($bar = mysql_fetch_object($qList)) {
	$no += 1;
	$stat = ' onclick=getDet(' . $no . ') style="cursor: pointer;"';
	$data .= '<tr class=rowcontent id=row_' . $no . ' ' . $stat . ' >' . "\r\n" . '                <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td id=kdTrak_' . $no . ' value=\'' . $bar->kodetraksi . '\'>' . $bar->kodetraksi . '</td>    ' . "\r\n\t\t\t\t" . '<td id=kdeWs_' . $no . ' value=\'' . $bar->kodews . '\'>' . $bar->kodews . '</td>    ' . "\r\n\t\t\t\t" . '<td>' . $bar->rppertahun . '</td>    ' . "\r\n\t\t\t\t" . '<td>' . $bar->jampertahun . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($bar->rpperjam, 2) . '</td></tr><tr><td colspan=5><div id=detail_' . $no . '></div></td></tr>';
}

$data .= '</tbody></table>';
$data4 = '<table border=0 cellspacing=0><tr><td colspan=5 scope=row>Summary ' . $_SESSION['lang']['laporanrpjambengkel'] . '</td></tr><tr><td colspan=5 scope=row>' . $_SESSION['lang']['tahun'] . ' : ' . $thnbudget . ' </td></tr><tr><td colspan=5>' . $_SESSION['lang']['unit'] . ' : ' . $kdWs . '</td></tr></table>';
$data4 .= '<table class=sortable cellspacing=1 border=1>' . "\r\n" . '          <thead>' . "\r\n" . '          <tr class=rowheader bgcolor=#CCCCCC>' . "\r\n\t\t\t" . '<td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n\t\t" . '    <td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['workshop'] . '</td>' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['rpperthn'] . '</td> ' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['jamperthn'] . '</td> ' . "\r\n\t\t\t" . '<td align=center>' . $_SESSION['lang']['rpperjamKend'] . '</td>' . "\r\n" . '          </tr>' . "\r\n" . '          </thead>';
$no = 0;
$sList = 'select * from ' . $dbname . '.bgt_biaya_ws_per_jam where tahunbudget=\'' . $thnbudget . '\' and kodews=\'' . $kdWs . '\' order by kodews ';

#exit(mysql_error());
($qList = mysql_query($sList)) || true;

while ($bar = mysql_fetch_object($qList)) {
	$no += 1;
	$data4 .= '<tr class=rowcontent >' . "\r\n" . '                <td align=center>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodetraksi . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodews . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->rppertahun . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->jampertahun . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($bar->rpperjam, 2) . '</td></tr>';
}

$data4 .= '</tbody></table>';
$data4 .= 'Print Time : ' . date('H:i:s, d/m/Y') . '<br>By : ' . $_SESSION['empl']['name'];
$data3 = ' ' . $_SESSION['lang']['laporandetail'] . ' ' . $_SESSION['lang']['laporanrpjambengkel'] . ' <br>';
$data3 .= ' ' . $_SESSION['lang']['tahun'] . ' : ' . $thnbudget . '   <br>';
$data3 .= ' ' . $_SESSION['lang']['unit'] . ' : ' . $kdWs . ' <br>';
$data3 .= '<table cellspacing=1 border=1 class=sortable width=109%>' . "\r\n\t\t" . '<thead>' . "\r\n\t\t\t" . '<tr class=rowheader bgcolor=#CCCCCC>' . "\r\n\t\t\t\t" . '<td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['workshop'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['noakun'] . '</td> ' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['volume'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuanvolume'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuanjumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['rp'] . '</td>' . "\r\n\t\t\t" . '</tr>' . "\r\n\t\t\t\r\n\t\t" . '</thead><tbody>';
$no = 0;
$sList = 'select * from ' . $dbname . '.bgt_budget b' . "\r\n\t\t" . ' where  kodeorg=\'' . $kdWs . '\'  and tahunbudget=\'' . $_GET['thnbudget'] . '\'  and tipebudget=\'WS\' ';

#exit(mysql_error());
($qList = mysql_query($sList)) || true;

while ($bar = mysql_fetch_object($qList)) {
	$no += 1;
	$data3 .= '<tr class=rowcontent width=100%>' . "\r\n" . '                <td align=center>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodetraksi . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodews . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodebudget . '</td>' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->noakun . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->volume . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->satuanv . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->jumlah . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->satuanj . '</td>        ' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($bar->rupiah, 2) . '</td></tr>';
}

$data3 .= '</table>';
$data3 .= 'Print Time : ' . date('H:i:s, d/m/Y') . '<br>By : ' . $_SESSION['empl']['name'];

switch ($proses) {
case 'getDetail':
	$data2 = 'Detail ' . "\r\n\t" . '<img onclick="dataKeExcel(event,\'' . $kdTrak . '\',\'' . $kdeWs . '\',\'' . $thnbudget . '\');" src=images/excel.jpg class=resicon name=preview id=preview title=\'MS.Excel Detail\'> ' . "\r\n\t" . '<img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="dataKePdf(\'' . $thnbudget . '\',\'' . $kdTrak . '\',\'' . $kdeWs . '\',event);"> ' . "\r\n\t" . '<img onclick="closeDet(' . $brsKe . ');" title="Tutup" class="resicon" src="images/close.gif">' . "\t\r\n\t" . '<table cellspacing=1 border=0 class=sortable width=107%>' . "\r\n\t\t" . '<thead>' . "\r\n\t\t\t" . '<tr class=rowheader >' . "\r\n\t\t\t\t" . '<td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['workshop'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['noakun'] . '</td> ' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['volume'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuanvolume'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuanjumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['rp'] . '</td>' . "\r\n\t\t\t" . '</tr>' . "\r\n\t\t" . '</thead><tbody>';
	$no = 0;
	$sList = 'select * from ' . $dbname . '.bgt_budget b where  kodeorg=\'' . $kdeWs . '\'  and tahunbudget=\'' . $thnbudget . '\'  and tipebudget=\'WS\' ';

	#exit(mysql_error());
	($qList = mysql_query($sList)) || true;

	while ($bar = mysql_fetch_object($qList)) {
		$no += 1;
		$data2 .= '<tr class=rowcontent width=100%>' . "\r\n" . '                <td align=center>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodetraksi . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodews . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->kodebudget . '</td>' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->noakun . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->volume . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->satuanv . '</td>    ' . "\r\n\t\t\t\t" . '<td align=right>' . $bar->jumlah . '</td>    ' . "\r\n\t\t\t\t" . '<td align=left>' . $bar->satuanj . '</td>        ' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($bar->rupiah, 2) . '</td></tr>';
	}

	$data2 .= '</tbody></table>';
	echo $data2;
	break;

case 'preview':
	if ($thnbudget == '') {
		echo 'warning : Tahun Budget Masih Kosong';
		exit();
	}
	else if ($kdWs == '') {
		echo 'warning : Kode Bengkel Masih Kosong';
		exit();
	}
	else {
		echo $data;
	}

	break;

case 'excel':
	if ($thnbudget == '') {
		echo 'warning : Tahun Budget Masih Kosong';
		exit();
	}
	else if ($kdWs == '') {
		echo 'warning : Kode Bengkel Masih Kosong';
		exit();
	}

	$nop_ = 'Laporan';

	if (0 < strlen($data4)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $data4)) {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;

case 'ExcelAlokasi':
	if ($thnbudget == '') {
		echo 'warning : Tahun Budget Masih Kosong';
		exit();
	}
	else if ($kdWs == '') {
		echo 'warning : Kode Bengkel Masih Kosong';
		exit();
	}

	$nop_ = 'Laporan';

	if (0 < strlen($data3)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $data3)) {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;

case 'pdf':
	if ($thnbudget == '') {
		echo 'warning : Tahun Budget Masih Kosong';
		exit();
	}
	else if ($kdWs == '') {
		echo 'warning : Kode Bengkel Masih Kosong';
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
			global $thnbudget;
			global $kdWs;
			global $kdTrak;
			global $namakar;
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
			$orgData = fetchData($query);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;

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
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['laporanrpjambengkel'], '', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 7);
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Printed By : ' . $namakar[$_SESSION['standard']['userid']], '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Tanggal By : ' . date('d-m-Y'), '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Time By : ' . date('h:i:s'), '', 0, 'R');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['laporanrpjambengkel'] . ' ' . $kdWs), '', 0, 'C');
			$this->Ln();
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['tahun'] . ' ' . $thnbudget), '', 0, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((2 / 100) * $width, $height, substr($_SESSION['lang']['nomor'], 0, 2), 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['kodetraksi'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['workshop'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['rpperthn'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['jamperthn'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['rpperjam'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$pdf = new PDF('P', 'pt', 'Legal');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$no = 0;
	$sql = 'select * from ' . $dbname . '.bgt_biaya_ws_per_jam where tahunbudget=\'' . $thnbudget . '\' and kodews=\'' . $kdWs . '\' order by kodews ';

	#exit(mysql_error());
	($qDet = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->Cell((2 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodetraksi'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodews'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['rppertahun'], 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['jampertahun'], 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['rpperjam'], 2), 1, 0, 'R', 1);
		$pdf->Ln();
	}

	$pdf->Output();
	break;

case 'pdfAlokasi':
	if ($thnbudget == '') {
		echo 'warning : Tahun Budget Masih Kosong';
		exit();
	}
	else if ($kdWs == '') {
		echo 'warning : Kode Bengkel Masih Kosong';
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
			global $thnbudget;
			global $kdWs;
			global $kdTrak;
			global $namakar;
			global $brsKe;
			global $kdeWs;
			global $kodeTraksi;
			global $thnBudget;
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
			$orgData = fetchData($query);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;

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
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['laporanrpjambengkel'], '', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 7);
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Printed By : ' . $namakar[$_SESSION['standard']['userid']], '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Date : ' . date('d-m-Y'), '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Time : ' . date('h:i:s'), '', 0, 'R');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', '', 10);
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['laporandetail'] . $_SESSION['lang']['laporanrpjambengkel'] . ' ' . $kdWs), '', 0, 'C');
			$this->Ln();
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['tahun'] . ' ' . $thnbudget), '', 0, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((2 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['kodetraksi'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['workshop'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['kodebudget'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['volume'], 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['satuanvolume'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['satuanjumlah'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$pdf = new PDF('P', 'pt', 'Legal');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$no = 0;
	$sql = 'select * from ' . $dbname . '.bgt_budget b where  kodeorg=\'' . $kdWs . '\' and tahunbudget=\'' . $thnbudget . '\'  and tipebudget=\'WS\'';

	#exit(mysql_error());
	($qDet = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->Cell((2 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodetraksi'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodews'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodebudget'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['noakun'], 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['volume'], 1, 0, 'R', 1);
		$pdf->Cell((13 / 100) * $width, $height, $res['satuanv'], 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['jumlah'], 1, 0, 'R', 1);
		$pdf->Cell((13 / 100) * $width, $height, $res['satuanj'], 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['rupiah'], 2), 1, 0, 'R', 1);
		$pdf->Ln();
	}

	$pdf->Output();
	break;
}

?>
