<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['kdUnit'] == '' ? $kodeOrg = $_GET['kdUnit'] : $kodeOrg = $_POST['kdUnit'];
$_POST['thnBudget'] == '' ? $thnBudget = $_GET['thnBudget'] : $thnBudget = $_POST['thnBudget'];
$_POST['kdTraksi'] == '' ? $kdTraksi = $_GET['kdTraksi'] : $kdTraksi = $_POST['kdTraksi'];
$_POST['kdVhc'] == '' ? $kdVhc = $_GET['kdVhc'] : $kdVhc = $_POST['kdVhc'];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmbrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$str = 'select namakaryawan,karyawanid from ' . $dbname . '.datakaryawan where karyawanid=' . $_SESSION['standard']['userid'] . '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namakar[$bar->karyawanid] = $bar->namakaryawan;
}

$where = ' kodetraksi=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\'';
$sKodeOrg = 'select * from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi where  ' . $where . ' order by tahunbudget asc';

#exit(mysql_error($conn));
($qKodeOrg = mysql_query($sKodeOrg)) || true;

while ($rKode = mysql_fetch_assoc($qKodeOrg)) {
	$dtKdtraksi[] = $rKode['kodetraksi'];
	$dtKdvhc[] = $rKode['kodevhc'];
	$dtRpSthn[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['rpsetahun'];
	$dtJamSthn[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['jamsetahun'];
	$dtRpJam[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['rpperjam'];
	$dtAlokasi[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['teralokasi'];
}

$cek = count($dtKdtraksi);

switch ($proses) {
case 'preview':
	if (($kodeOrg == '') || ($thnBudget == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	if ($cek == 0) {
		exit('Error: Data Kosong');
	}

	$tab = '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td align=center>No.</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['kodevhc'] . '</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['jamperthn'] . '</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['rpperthn'] . '</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['kmperthn'] . '</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['alokasijam'] . '</td>';
	$tab .= '<td align=center>' . $_SESSION['lang']['action'] . '</td>';
	$tab .= '</tr></thead><tbody>';
	$terAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]] = $dtAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]] * $dtRpJam[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]];

	foreach ($dtKdvhc as $lisTraksi) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=center>' . $no . '</td>';
		$tab .= '<td align=center>' . $kodeOrg . '</td>';
		$tab .= '<td align=center>' . $lisTraksi . '</td>';
		$tab .= '<td align=right>' . number_format($dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRpJam[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab .= '<td align=center>' . "\r\n" . '                       <button class="mybutton" name="preview" id="preview" onclick="getAlokasi(\'' . $kodeOrg . '\',\'' . $lisTraksi . '\',\'' . $thnBudget . '\')">' . $_SESSION['lang']['alokasi'] . '</button>' . "\r\n" . '                           <button class="mybutton" name="preview" id="preview" onclick="getBiaya(\'' . $kodeOrg . '\',\'' . $lisTraksi . '\',\'' . $thnBudget . '\')">' . $_SESSION['lang']['biayaRinci'] . '</button>' . "\r\n" . '                       </td>';
		$tab .= '</tr>';
		$totJam += $dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi];
		$totRup += $dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi];
		$totKmThn += $dtRpJam[$thnBudget][$kodeOrg][$lisTraksi];
		$totAlokasiJam += $dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
	}

	$tab .= '</tbody><thead><tr class=rowheader>';
	$tab .= '<td align=center  colspan=3 align=center>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totJam, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totRup, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totKmThn, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totAlokasiJam, 2) . '</td>';
	$tab .= '<td align=right>&nbsp</td>';
	$tab .= '</tr>';
	$tab .= '</thead></table>';
	echo $tab;
	break;

case 'excel':
	if ($thnBudget == '') {
		echo 'warning : Tahun masih kosong';
		exit();
	}
	else if ($kodeOrg == '') {
		echo 'warning : Kode organisasi masih kosong';
		exit();
	}

	$tab2 = 'Laporan Rp/Jam per Kendaraan <br>';
	$tab2 .= ' ' . $optNm[$kodeOrg] . '  tahun ' . $thnBudget . ' ';
	$tab2 .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
	$tab2 .= '<tr class=rowheader bgcolor=#CCCCCC>';
	$tab2 .= '<td align=center>No.</td>';
	$tab2 .= '<td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>';
	$tab2 .= '<td align=center>' . $_SESSION['lang']['kodevhc'] . '</td>';
	$tab2 .= '<td align=center>' . $_SESSION['lang']['jamperthn'] . '</td>';
	$tab2 .= '<td align=center>' . $_SESSION['lang']['rpperthn'] . '</td>';
	$tab2 .= '<td align=center>' . $_SESSION['lang']['kmperthn'] . '</td>';
	$tab2 .= '<td align=center>' . $_SESSION['lang']['alokasijam'] . '</td>';
	$tab2 .= '</tr></thead><tbody>';
	$terAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]] = $dtAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]] * $dtRpJam[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]];

	foreach ($dtKdvhc as $lisTraksi) {
		$no += 1;
		$tab2 .= '<tr class=rowcontent>';
		$tab2 .= '<td align=center>' . $no . '</td>';
		$tab2 .= '<td align=center>' . $kodeOrg . '</td>';
		$tab2 .= '<td align=center>' . $lisTraksi . '</td>';
		$tab2 .= '<td align=right>' . number_format($dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab2 .= '<td align=right>' . number_format($dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab2 .= '<td align=right>' . number_format($dtRpJam[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab2 .= '<td align=right>' . number_format($dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
		$tab2 .= '</tr>';
		$totJam += $dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi];
		$totRup += $dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi];
		$totKmThn += $dtRpJam[$thnBudget][$kodeOrg][$lisTraksi];
		$totAlokasiJam += $dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
	}

	$tab2 .= '</tbody><thead><tr class=rowheader bgcolor=#CCCCCC>';
	$tab2 .= '<td align=center  colspan=3 align=center>' . $_SESSION['lang']['total'] . '</td>';
	$tab2 .= '<td align=right>' . number_format($totJam, 2) . '</td>';
	$tab2 .= '<td align=right>' . number_format($totRup, 2) . '</td>';
	$tab2 .= '<td align=right>' . number_format($totKmThn, 2) . '</td>';
	$tab2 .= '<td align=right>' . number_format($totAlokasiJam, 2) . '</td>';
	$tab2 .= '</tr>';
	$tab2 .= '</thead></table>';
	$tglSkrg = date('Ymd');
	$nop_ = 'Laporan_Exel_' . $tglSkrg;

	if (0 < strlen($tab2)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab2)) {
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
	if ($thnBudget == '') {
		echo 'warning : Tahun masih kosong';
		exit();
	}
	else if ($kodeOrg == '') {
		echo 'warning : Kode organisasi masih kosong';
		exit();
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $nmOrg;
			global $optNm;
			global $thnBudget;
			global $kodeOrg;
			global $kdUnit;
			global $totRp;
			global $conn;
			global $dbname;
			global $align;
			global $length;
			global $colArr;
			global $title;
			global $total;
			global $namakar;
			global $totJam;
			global $totRup;
			global $totKmThn;
			global $totAlokasiJam;
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
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(((20 / 100) * $width) - 5, $height, 'Biaya Kendaraan', '', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 10);
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Printed By : ' . $namakar[$_SESSION['standard']['userid']], '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Date : ' . date('d-m-Y'), '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Time : ' . date('h:i:s'), '', 0, 'R');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width, $height, strtoupper('Biaya Kendaraan ' . $optNm[$kodeOrg]), '', 0, 'C');
			$this->Ln();
			$this->Cell($width, $height, strtoupper('Tahun ' . $thnBudget), '', 0, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((2 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['kodetraksi'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['kodevhc'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['jamperthn'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['rpperthn'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['kmperthn'], 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['alokasijam'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$pdf = new PDF('L', 'pt', 'Legal');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 10);
	$sql = 'select * from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi where tahunbudget=\'' . $thnBudget . '\' and kodetraksi=\'' . $kodeOrg . '\' ';

	#exit(mysql_error());
	($qDet = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->Cell((2 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((13 / 100) * $width, $height, $res['kodetraksi'], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, $res['kodevhc'], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, number_format($res['jamsetahun'], 2), 1, 0, 'R', 1);
		$pdf->Cell((15 / 100) * $width, $height, number_format($res['rpsetahun'], 2), 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['rpperjam'], 2), 1, 0, 'R', 1);
		$pdf->Cell((13 / 100) * $width, $height, number_format($res['teralokasi'], 2), 1, 0, 'R', 1);
		$pdf->Ln();
		$totJam += $res['jamsetahun'];
		$totRup += $res['rpsetahun'];
		$totKmThn += $res['rpperjam'];
		$totAlokasiJam += $res['teralokasi'];
	}

	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->Cell((30 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFontSize(10);
	$pdf->Cell((15 / 100) * $width, $height, number_format($totJam, 2), 1, 0, 'R', 1);
	$pdf->Cell((15 / 100) * $width, $height, number_format($totRup, 2), 1, 0, 'R', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($totKmThn, 2), 1, 0, 'R', 1);
	$pdf->Cell((13 / 100) * $width, $height, number_format($totAlokasiJam, 2), 1, 0, 'R', 1);
	$pdf->Output();
	break;

case 'getAlokasi':
	$tab = '<fieldset><legend>' . $_SESSION['lang']['alokasi'] . ' ' . $kdVhc . ' ' . $_SESSION['lang']['budgetyear'] . ': ' . $thnBudget . '</legend>';
	$tab .= '<img title="MS.Excel" class="resicon" src="images/excel.jpg" onclick="dataKeExcelAlokasi(event,\'' . $kdTraksi . '\',\'' . $kdVhc . '\',\'' . $thnBudget . '\')">' . "\r\n\t\t\t\t" . '   <img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="dataKePdfAlokasi(event,\'' . $kdTraksi . '\',\'' . $kdVhc . '\',\'' . $thnBudget . '\');"> ';
	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td>No</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeorg'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jam'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '</td></tr></thead><tbody>';
	$sDetail = 'select jumlah,kodeorg,rupiah from ' . $dbname . '.bgt_budget where tipebudget<>\'TRK\' and kodevhc=\'' . $kdVhc . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error($conn));
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $rDetail['kodeorg'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['jumlah'], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['rupiah'], 2) . '</td>';
		$tab .= '</tr>';
		$totRupiahDet += $rDetail['rupiah'];
		$totJamDet += $rDetail['jumlah'];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=center colspan=2>Total</td>';
	$tab .= '<td align=right>' . number_format($totJamDet, 2) . '</td>';
	$tab .= '<td  align=right>' . number_format($totRupiahDet, 2) . '</td>';
	$tab .= '</tbody></table></fieldset';
	echo $tab;
	break;

case 'excelAlokasi':
	$tab .= '<table>' . "\r\n" . '             <tr><td colspan=4 align=left>' . $optNm[$kdTraksi] . '</td></tr>   ' . "\r\n" . '             <tr><td colspan=4>' . $_SESSION['lang']['alokasi'] . ' ' . $kdVhc . ' ' . $_SESSION['lang']['budgetyear'] . ': ' . $thnBudget . '</td></tr>   ' . "\r\n" . '             </table>';
	$tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td bgcolor=#DEDEDE align=center>No</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodeorg'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jam'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['rp'] . '</td></tr></thead><tbody>';
	$sDetail = 'select jumlah,kodeorg,rupiah from ' . $dbname . '.bgt_budget where tipebudget<>\'TRK\' and kodevhc=\'' . $kdVhc . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error($conn));
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $rDetail['kodeorg'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['jumlah'], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['rupiah'], 2) . '</td>';
		$tab .= '</tr>';
		$totRupiahDet += $rDetail['rupiah'];
		$totJamDet += $rDetail['jumlah'];
	}

	$tab .= '<tr class=rowcontent bgcolor=#CCCCCC>';
	$tab .= '<td align=center colspan=2>Total</td>';
	$tab .= '<td align=right>' . number_format($totJamDet, 2) . '</td>';
	$tab .= '<td  align=right>' . number_format($totRupiahDet, 2) . '</td>';
	$tab .= '</tbody></table>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'detailAlokasi';

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
			echo '<script language=javascript1.2>' . "\r\n" . '                    parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                    </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                    window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                    </script>';
		}

		closedir($handle);
	}

	break;

case 'pdfAlokasi':
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
			global $kodeTraksi;
			global $kdTraksi;
			global $kdVhc;
			global $kdkend;
			global $thnBudget;
			global $thnbdget;
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
			$this->SetFont('Arial', 'B', 10);
			$this->Ln();
			$this->Cell(((20 / 100) * $width) - 5, $height, 'Detail Laporan Rp Jam/Kendaraan', '', 0, 'L');
			$this->Ln();
			$this->Ln();
			$this->Cell($width, $height, strtoupper('Detail Laporan Rp Jam/Kendaraan ' . $kdVhc), '', 0, 'C');
			$this->Ln();
			$this->Cell($width, $height, strtoupper('Tahun ' . $thnBudget), '', 0, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((5 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['jam'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 1, 'C', 1);
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
	$sql = 'select jumlah,kodeorg,rupiah from ' . $dbname . '.bgt_budget where tipebudget!=\'TRK\' and kodevhc=\'' . $kdVhc . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->Cell((5 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((15 / 100) * $width, $height, $res['kodeorg'], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, $res['jumlah'], 1, 0, 'R', 1);
		$pdf->Cell((15 / 100) * $width, $height, number_format($res['rupiah'], 2), 1, 0, 'R', 1);
		$pdf->Ln();
		$totDetailPdfJam += $res['jumlah'];
		$totDetailPdfRp += $res['rupiah'];
	}

	$pdf->Cell((20 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
	$pdf->Cell((15 / 100) * $width, $height, number_format($totDetailPdfJam, 2), 1, 0, 'R', 1);
	$pdf->Cell((15 / 100) * $width, $height, number_format($totDetailPdfRp, 2), 1, 0, 'R', 1);
	$pdf->Output();
	break;

case 'getBiaya':
	$tab = '<fieldset><legend>' . $_SESSION['lang']['biayaRinci'] . ' ' . $kdVhc . ' ' . $_SESSION['lang']['budgetyear'] . ': ' . $thnBudget . '</legend>';
	$tab .= '<img title="MS.Excel" class="resicon" src="images/excel.jpg" onclick="dataKeExcel(event,\'' . $kdTraksi . '\',\'' . $kdVhc . '\',\'' . $thnBudget . '\')">' . "\r\n\t\t\t" . ' ' . "\t" . '   <img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="dataKePdfBiaya(event,\'' . $kdTraksi . '\',\'' . $kdVhc . '\',\'' . $thnBudget . '\');"> ';
	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td>No</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeorg'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeanggaran'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['volume'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '</td></tr></thead><tbody>';
	$sDetail = 'select kodeorg,kodebudget,kodebarang,volume,satuanv,jumlah,satuanj ,rupiah from ' . $dbname . '.bgt_budget where tipebudget=\'TRK\' and kodevhc=\'' . $kdVhc . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error($conn));
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $rDetail['kodeorg'] . '</td>';
		$tab .= '<td>' . $rDetail['kodebudget'] . '</td>';
		$tab .= '<td>' . $rDetail['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmbrg[$rDetail['kodebarang']] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['volume'], 2) . '</td>';
		$tab .= '<td>' . $rDetail['satuanv'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['jumlah'], 2) . '</td>';
		$tab .= '<td>' . $rDetail['satuanj'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['rupiah'], 2) . '</td>';
		$tab .= '</tr>';
		$totVol += $rDetail['volume'];
		$totJum += $rDetail['jumlah'];
		$totRp += $rDetail['rupiah'];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=center colspan=5>Total</td>';
	$tab .= '<td  align=right>' . number_format($totVol, 2) . '</td>';
	$tab .= '<td  align=right>&nbsp;</td>';
	$tab .= '<td  align=right>' . number_format($totJum, 2) . '</td>';
	$tab .= '<td  align=right>&nbsp;</td>';
	$tab .= '<td  align=right>' . number_format($totRp, 2) . '</td>';
	$tab .= '</tbody></table></fieldset';
	echo $tab;
	break;

case 'excelBiaya':
	$tab .= '<table>' . "\r\n" . '             <tr><td colspan=4 align=left>' . $optNm[$kdTraksi] . '</td></tr>   ' . "\r\n" . '             <tr><td colspan=4>' . $_SESSION['lang']['biayaRinci'] . ' ' . $kdVhc . ' ' . $_SESSION['lang']['budgetyear'] . ': ' . $thnBudget . '</td></tr>   ' . "\r\n" . '             </table>';
	$tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td bgcolor=#DEDEDE align=center>No</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodeorg'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodeanggaran'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['volume'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['rp'] . '</td></tr></thead><tbody>';
	$sDetail = 'select kodeorg,kodebudget,kodebarang,volume,satuanv,jumlah,satuanj ,rupiah from ' . $dbname . '.bgt_budget where tipebudget=\'TRK\' and kodevhc=\'' . $kdVhc . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error($conn));
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $rDetail['kodeorg'] . '</td>';
		$tab .= '<td>' . $rDetail['kodebudget'] . '</td>';
		$tab .= '<td>' . $rDetail['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmbrg[$rDetail['kodebarang']] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['volume'], 2) . '</td>';
		$tab .= '<td>' . $rDetail['satuanv'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['jumlah'], 2) . '</td>';
		$tab .= '<td>' . $rDetail['satuanj'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['rupiah'], 2) . '</td>';
		$tab .= '</tr>';
		$totVol += $rDetail['volume'];
		$totJum += $rDetail['jumlah'];
		$totRp += $rDetail['rupiah'];
	}

	$tab .= '<tr class=rowcontent bgcolor=#CCCCCC>';
	$tab .= '<td align=center colspan=5>Total</td>';
	$tab .= '<td  align=right>' . number_format($totVol, 2) . '</td>';
	$tab .= '<td  align=right>&nbsp;</td>';
	$tab .= '<td  align=right>' . number_format($totJum, 2) . '</td>';
	$tab .= '<td  align=right>&nbsp;</td>';
	$tab .= '<td  align=right>' . number_format($totRp, 2) . '</td>';
	$tab .= '</tbody></table>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'detailRincianBiaya';

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
			echo '<script language=javascript1.2>' . "\r\n" . '                    parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                    </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                    window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                    </script>';
		}

		closedir($handle);
	}

	break;

case 'pdfBiaya':
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
			global $kodeTraksi;
			global $kdTraksi;
			global $kdVhc;
			global $kdkend;
			global $thnBudget;
			global $thnbdget;
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
			$this->SetFont('Arial', 'B', 10);
			$this->Ln();
			$this->Cell(((20 / 100) * $width) - 5, $height, 'Biaya Rinci Kendaraan', '', 0, 'L');
			$this->Ln();
			$this->Ln();
			$this->Cell($width, $height, strtoupper('Biaya Rinci Kendaraan ' . $kdVhc), '', 0, 'C');
			$this->Ln();
			$this->Cell($width, $height, strtoupper('Tahun ' . $thnBudget), '', 0, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((5 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['kodeanggaran'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['volume'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('L', 'pt', 'Legal');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$no = 0;
	$sql = 'select kodeorg,kodebudget,kodebarang,volume,satuanv,jumlah,satuanj ,rupiah from ' . $dbname . '.bgt_budget where tipebudget=\'TRK\' and kodevhc=\'' . $kdVhc . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->Cell((5 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((15 / 100) * $width, $height, $res['kodeorg'], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, $res['kodebudget'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodebarang'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['volume'], 2), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, $res['satuanv'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['jumlah'], 2), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, $res['satuanj'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['rupiah'], 2), 1, 0, 'R', 1);
		$pdf->Ln();
		$tota += $res['volume'];
		$totb += $res['jumlah'];
		$totc += $res['rupiah'];
	}

	$pdf->SetFont('Arial', '', 9);
	$pdf->Cell((35 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
	$pdf->SetFont('Arial', '', 7);
	$pdf->Cell((10 / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($tota, 2), 1, 0, 'R', 1);
	$pdf->Cell((8 / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($totb, 2), 1, 0, 'R', 1);
	$pdf->Cell((8 / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($totc, 2), 1, 0, 'R', 1);
	$pdf->Output();
	break;
}

?>
