<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kel = $_POST['kel'];

if ($proses == 'excel') {
	$kel = $_GET['kel'];
}

$arr2 = '##kdorg##kdkeg##per##kdbarang';
$st = array('Aktif', 'Tidak Aktif');
$nmkl = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');

if ($kel != '') {
	$where = 'where kelompokbarang=\'' . $kel . '\'';
}
else {
	$where = '';
}

$nmbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$satuanbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

if ($proses == 'excel') {
	$stream = '<table class=sortable cellspacing=1 border=1>';
}
else {
	$stream = '<table class=sortable cellspacing=1>';
}

$stream .= '<thead class=rowheader>' . "\r\n" . '                 <tr   class=rowheader>' . "\r\n\t\t\t\t" . ' ' . "\t" . '<td bgcolor=#CCCCCC align=center>No</td>' . "\r\n\t\t\t\t\t" . '<td bgcolor=#CCCCCC align=center>Kelompok</td>' . "\r\n\t\t\t\t\t" . '<td bgcolor=#CCCCCC align=center>Nama Kelompok</td>' . "\r\n\t\t\t\t\t" . '<td bgcolor=#CCCCCC align=center>Kode Barang</td>' . "\r\n\t\t\t\t\t" . '<td bgcolor=#CCCCCC align=center>Nama Barang</td>' . "\r\n\t\t\t\t\t" . '<td bgcolor=#CCCCCC align=center>Satuan</td>' . "\r\n\t\t\t\t\t" . '<td bgcolor=#CCCCCC align=center>Status</td>' . "\r\n" . '  ' . "\t\t\t\t" . '</tr></thead>';
$sql = 'select * from ' . $dbname . '.log_5masterbarang ' . $where . ' order by kelompokbarang';
#exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($bar = mysql_fetch_assoc($qry)) {
	$no += 1;
	$stream .= '<tr class=rowcontent>' . "\r\n\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t" . '<td>' . $bar['kelompokbarang'] . '</td>' . "\r\n\t\t" . '<td>' . $nmkl[$bar['kelompokbarang']] . '</td>' . "\r\n\t\t" . '<td>\'' . $bar['kodebarang'] . '</td>' . "\r\n\t\t" . '<td>' . $bar['namabarang'] . '</td>' . "\r\n\t\t" . '<td>' . $bar['satuan'] . '</td>' . "\r\n\t\t" . '<td>' . $st[$bar['inactive']] . '</td>' . "\r\n\r\n\t\t" . '</tr>';
}

$stream .= '<tbody></table>';

switch ($proses) {
case 'preview':
	echo $stream;
	break;

case 'excel':
	$tglSkrg = date('Ymd');
	$nop_ = 'lapora_daftar_barang' . $tglSkrg;

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
			global $kdorg;
			global $kdAfd;
			global $tgl1;
			global $tgl2;
			global $where;
			global $nmOrg;
			global $lok;
			global $notrans;
			global $bulan;
			global $ang;
			global $kar;
			global $namaang;
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

			$this->Image($path, 30, 15, 55);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(90);
			$this->Cell($width - 80, 12, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
			$this->SetX(90);
			$this->SetFont('Arial', '', 9);
			$height = 15;
			$this->Cell($width - 80, $height, $orgData[0]['alamat'], 0, 1, 'L');
			$this->SetX(90);
			$this->Cell($width - 80, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
			$this->Ln();
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->SetFont('Arial', 'B', 12);
			$this->Ln();
			$height = 15;
			$this->Cell($width, $height, 'Laporan Stock Opname', '', 0, 'C');
			$this->Ln();
			$this->SetFont('Arial', '', 10);
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, 15, substr($_SESSION['lang']['nomor'], 0, 2), 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, 15, 'Kode Barang', 1, 0, 'C', 1);
			$this->Cell((35 / 100) * $width, 15, 'Nama Barang', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, 15, 'Saldo Fisik e-Agro', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, 15, 'Saldo Fisik Gudang', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, 15, 'Selisih', 1, 1, 'C', 1);
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
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);

	exit('SQL ERR : ' . mysql_error());
	($qry = mysql_query($sql)) || true;
	$no = 0;

	while ($bar = mysql_fetch_assoc($qry)) {
		$no += 1;
		$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((15 / 100) * $width, $height, $bar['kodebarang'], 1, 0, 'R', 1);
		$pdf->Cell((35 / 100) * $width, $height, $nmbarang[$bar['kodebarang']], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, number_format($bar['saldoqty']), 1, 0, 'R', 1);
		$pdf->Cell((15 / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell((15 / 100) * $width, $height, '', 1, 1, 'R', 1);
	}

	$pdf->Output();
	break;
}

?>
