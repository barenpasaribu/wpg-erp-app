<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$periode = $_POST['periode'];

switch ($proses) {
case 'preview':
	echo '<table cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>' . "\r\n\t" . '<td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['pt'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['untukunit'] . '</td>' . "\r\n\t" . '<td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['detail'] . '</td>' . "\r\n\t" . '</tr></thead><tbody>';
	$sPemakaian = 'select kodept,untukpt,untukunit from ' . $dbname . '.log_transaksi_vw where tanggal like \'%' . $periode . '%\'  group by untukunit';

	#exit(mysql_error());
	($qPemakaian = mysql_query($sPemakaian)) || true;
	$row = mysql_num_rows($qPemakaian);

	if (0 < $row) {
		while ($rPemakaian = mysql_fetch_assoc($qPemakaian)) {
			$sJmlh = 'select sum(jumlah) as jmlh from ' . $dbname . '.log_transaksi_vw where untukunit=\'' . $rPemakaian['untukunit'] . '\' and tanggal like \'%' . $periode . '%\' and tipetransaksi=\'5\'';

			#exit(mysql_error());
			($qJmlh = mysql_query($sJmlh)) || true;
			$rJmlh = mysql_fetch_assoc($qJmlh);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['kodept'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp = mysql_fetch_assoc($qComp);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['untukpt'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp2 = mysql_fetch_assoc($qComp);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['untukunit'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp3 = mysql_fetch_assoc($qComp);
			$test = 'kdunit' . '##' . $rPemakaian['untukunit'] . '##periode' . '##' . $periode;
			echo '<tr class=rowcontent  title=Click>' . "\r\n\t\t\t" . '<td onclick="zPdfInputan(\'log_slave_2alokasibiayaPemakaian\',\'' . $test . '\',\'printContainer2\')">' . $rComp['namaorganisasi'] . '</td>' . "\r\n\t\t\t" . '<td onclick="zPdfInputan(\'log_slave_2alokasibiayaPemakaian\',\'' . $test . '\',\'printContainer2\')">' . $rComp2['namaorganisasi'] . '</td>' . "\r\n\t\t\t" . '<td onclick="zPdfInputan(\'log_slave_2alokasibiayaPemakaian\',\'' . $test . '\',\'printContainer2\')">' . $rComp3['namaorganisasi'] . '</td>' . "\r\n\t\t\t" . '<td onclick="zPdfInputan(\'log_slave_2alokasibiayaPemakaian\',\'' . $test . '\',\'printContainer2\')" align=right>' . number_format($rJmlh['jmlh'], 2) . '</td>' . "\r\n" . '                        <td><button onclick="zExceldetail(event,\'log_slave_2alokasibiayaPemakaian.php\',\'' . $test . '\',\'printContainer2\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>  </td>        ' . "\r\n" . '                        </tr>';
		}
	}
	else {
		echo '<tr class=rowcontent><td colspan=4 align=center>Not Found</td></tr>';
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
			global $kdUnit;
			global $periode;
			$str1 = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kdUnit . '\'';
			$res1 = mysql_query($str1);
			$bar1 = mysql_fetch_object($res1);
			$sComp = 'select namaorganisasi,alamat,wilayahkota,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar1->induk . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp = mysql_fetch_object($qComp);
			$namapt = $rComp->namaorganisasi;
			$alamatpt = $rComp->alamat . ', ' . $rComp->wilayahkota;
			$telp = $rComp->telepon;

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

			$this->Image($path, 15, 5, 35, 20);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(55);
			$this->Cell(60, 5, $namapt, 0, 1, 'L');
			$this->SetX(55);
			$this->Cell(60, 5, $alamatpt, 0, 1, 'L');
			$this->SetX(55);
			$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
			$this->Ln();
			$this->Cell(35, 4, $_SESSION['lang']['untukunit'], 0, 0, 'L');
			$this->Cell(40, 4, ': ' . $kdUnit, 0, 1, 'L');
			$this->Cell(35, 4, $_SESSION['lang']['periode'], 0, 0, 'L');
			$this->Cell(40, 4, ': ' . $periode, 0, 1, 'L');
			$this->SetFont('Arial', 'U', 12);
			$this->SetY(40);
			$this->Cell(190, 5, strtoupper($_SESSION['lang']['pemakaianBarang']), 0, 1, 'C');
			$this->SetFont('Arial', '', 6);
			$this->SetY(27);
			$this->SetX(163);
			$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');
			$this->Line(10, 27, 200, 27);
			$this->SetY(40);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$kdUnit = $_GET['kdunit'];
	$periode = $_GET['periode'];
	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->Ln();
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
	$pdf->Cell(20, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
	$pdf->Cell(30, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
	$pdf->Cell(15, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
	$pdf->Cell(10, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
	$pdf->Cell(36, 5, $_SESSION['lang']['kalkulasihargarata'], 1, 0, 'C', 1);
	$pdf->Cell(25, 5, $_SESSION['lang']['kodeblok'], 1, 0, 'C', 1);
	$pdf->Cell(50, 5, $_SESSION['lang']['keterangan'], 1, 1, 'C', 1);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$str = 'select kodebarang,kodeblok,hargarata,jumlah,satuan,tanggal  from ' . $dbname . '.log_transaksi_vw where untukunit=\'' . $kdUnit . '\' and tanggal like \'%' . $periode . '%\' and tipetransaksi=\'5\'';
	$re = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_assoc($re)) {
		$no += 1;
		$sComp = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar['kodebarang'] . '\'';

		#exit(mysql_error());
		($qComp = mysql_query($sComp)) || true;
		$rComp3 = mysql_fetch_assoc($qComp);
		$pdf->Cell(8, 5, $no, 1, 0, 'L', 1);
		$pdf->Cell(20, 5, tanggalnormal($bar['tanggal']), 1, 0, 'C', 1);
		$pdf->Cell(30, 5, substr($rComp3['namabarang'], 0, 30), 1, 0, 'L', 1);
		$pdf->Cell(15, 5, number_format($bar['jumlah'], 2), 1, 0, 'R', 1);
		$pdf->Cell(10, 5, $bar['satuan'], 1, 0, 'C', 1);
		$pdf->Cell(36, 5, number_format($bar['hargarata'], 2), 1, 0, 'C', 1);
		$pdf->Cell(25, 5, $bar['kodeblok'], 1, 0, 'L', 1);
		$pdf->Cell(50, 5, substr($bar['keterangan'], 0, 50), 1, 1, 'L', 1);
	}

	$pdf->Output();
	break;

case 'excel':
	$periode = $_GET['periode'];
	$strx = 'select kodebarang,kodeblok,hargarata,jumlah,satuan,tanggal,kodept,untukpt,untukunit  from ' . $dbname . '.log_transaksi_vw where tanggal like \'%' . $periode . '%\' and tipetransaksi=\'5\' order by untukunit asc';
	$stream .= "\r\n\t\t\t" . '<table>' . "\r\n\t\t\t" . '<tr><td colspan=9 align=center>' . $_SESSION['lang']['list'] . ' ' . $_SESSION['lang']['pemakaianBarang'] . '</td></tr>' . "\r\n\t\t\t" . '<tr><td colspan=5 align=center>Periode : ' . $periode . '</td></tr>' . "\r\n\t\t\t" . '</table>' . "\r\n\t\t\t" . '<table border=1>' . "\r\n\t\t\t" . '<tr>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >No.</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['pt'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['untukunit'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['kalkulasihargarata'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['jumlah'] . '</td>';
	$stream .= '</tr>';

	#exit(mysql_error());
	($query = mysql_query($strx)) || true;
	$row = mysql_num_rows($query);

	if (0 < $row) {
		while ($rPemakaian = mysql_fetch_assoc($query)) {
			$no += 1;
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['kodept'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp = mysql_fetch_assoc($qComp);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['untukpt'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp2 = mysql_fetch_assoc($qComp);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['untukunit'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp3 = mysql_fetch_assoc($qComp);
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rPemakaian['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$stream .= '<tr class=rowcontent onclick="masterPDF(\'log_transaksiht\',\'' . $rPemakaian['notransaksi'] . '\',\'\',\'log_slave_2alokasibiayaPemakaian\',event);">' . "\r\n\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t" . '<td>' . $rComp['namaorganisasi'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rComp2['namaorganisasi'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rComp3['namaorganisasi'] . '</td>' . "\r\n\t\t\t" . '<td>' . tanggalnormal($rPemakaian['tanggal']) . '</td>' . "\r\n\t\t\t" . '<td>' . $rBrg['namabarang'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rPemakaian['satuan'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rPemakaian['hargarata'] . '</td>' . "\r\n\t\t\t" . '<td align=right>' . number_format($rPemakaian['jumlah'], 2) . '</td>' . "\r\n\t\t\t" . '</tr>';
		}
	}
	else {
		$stream .= '<tr><td colpsan=9>Not Found</td></tr>';
	}

	$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'PemakaianBarang';

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

case 'exceldetail':
	$kdUnit = $_GET['kdunit'];
	$periode = $_GET['periode'];
	$str = 'select kodebarang,kodeblok,hargarata,jumlah,satuan,tanggal  from ' . $dbname . '.log_transaksi_vw where untukunit=\'' . $kdUnit . '\' and tanggal like \'%' . $periode . '%\' and tipetransaksi=\'5\'';
	$stream .= "\r\n\t\t\t" . '<table>' . "\r\n\t\t\t" . '<tr><td colspan=9 align=center>' . $_SESSION['lang']['list'] . ' ' . $_SESSION['lang']['pemakaianBarang'] . '</td></tr>' . "\r\n\t\t\t" . '<tr><td colspan=5 align=center>Periode : ' . $periode . '</td></tr>' . "\r\n\t\t\t" . '</table>' . "\r\n\t\t\t" . '<table border=1>' . "\r\n\t\t\t" . '<tr>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >No.</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['kalkulasihargarata'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['kodeblok'] . '</td>' . "\r\n\t\t\t\t" . '<td bgcolor=#DEDEDE align=center >' . $_SESSION['lang']['keterangan'] . '</td>';
	$stream .= '</tr>';

	#exit(mysql_error());
	($query = mysql_query($str)) || true;
	$row = mysql_num_rows($query);

	if (0 < $row) {
		while ($rPemakaian = mysql_fetch_assoc($query)) {
			$no += 1;
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['kodept'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp = mysql_fetch_assoc($qComp);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['untukpt'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp2 = mysql_fetch_assoc($qComp);
			$sComp = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rPemakaian['untukunit'] . '\'';

			#exit(mysql_error());
			($qComp = mysql_query($sComp)) || true;
			$rComp3 = mysql_fetch_assoc($qComp);
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rPemakaian['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$stream .= '<tr class=rowcontent onclick="masterPDF(\'log_transaksiht\',\'' . $rPemakaian['notransaksi'] . '\',\'\',\'log_slave_2alokasibiayaPemakaian\',event);">' . "\r\n\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t" . '<td>' . $rPemakaian['tanggal'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rBrg['namabarang'] . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $rPemakaian['jumlah'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rPemakaian['satuan'] . '</td>' . "\r\n\t\t\t" . '<td align=right>' . number_format($rPemakaian['hargarata'], 2) . '</td>' . "\r\n\t\t\t" . '<td>' . $rPemakaian['kodeblok'] . '</td>' . "\r\n\t\t\t" . '<td>' . $rPemakaian['keterangan'] . '</td>' . "\r\n\t\t\t" . '</tr>';
		}
	}
	else {
		$stream .= '<tr><td colpsan=9>Not Found</td></tr>';
	}

	$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'PemakaianBarangDetail';

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
}

?>
