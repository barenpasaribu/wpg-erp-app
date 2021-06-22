<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['tipe'] == '' ? $tipe = $_GET['tipe'] : $tipe = $_POST['tipe'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['statKurs'] == '' ? $statKurs = $_GET['statKurs'] : $statKurs = $_POST['statKurs'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['klmpkBrg'] == '' ? $klmpkBrg = $_GET['klmpkBrg'] : $klmpkBrg = $_POST['klmpkBrg'];
$_POST['mtuang'] == '' ? $mtuang = $_GET['mtuang'] : $mtuang = $_POST['mtuang'];
$_POST['pt'] == '' ? $pt = $_GET['pt'] : $pt = $_POST['pt'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
$dfrIdr = 'IDR';
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKlmpk = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optKlmpk['LAIN'] = 'SUPPLIER LAIN';

if ($periode == '') {
	exit('Error:Field Tidak Boleh Kosong');
}

$arr = '##periode##judul';
$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
$sKurs = 'select distinct kode from ' . $dbname . '.setup_matauang where kode!=\'IDR\' order by kode desc';

#exit(mysql_error($conn));
($qKurs = mysql_query($sKurs)) || true;

while ($rKurs = mysql_fetch_assoc($qKurs)) {
	$ard += 1;
	$arr .= '##mtUang_' . $ard . '';
	$arr .= '##kurs_' . $ard . '';
	$_POST['mtUang_' . $ard] == '' ? $_POST['mtUang_' . $ard] = $_GET['mtUang_' . $ard] : $_POST['mtUang_' . $ard] = $_POST['mtUang_' . $ard];
	$_POST['kurs_' . $ard] == '' ? $_POST['kurs_' . $ard] = $_GET['kurs_' . $ard] : $_POST['kurs_' . $ard] = $_POST['kurs_' . $ard];

	if ($_POST['mtUang_' . $ard] == $rKurs['kode']) {
		$krsDt[$rKurs['kode']] = $_POST['kurs_' . $ard];
	}
}

$ard = 0;
$sTot = 'select distinct sum(hargasatuan*jumlahpesan) as total,matauang,kodesupplier  from ' . "\r\n" . '           ' . $dbname . '.log_po_vw where tanggal like \'' . $periode . '%\' ' . "\r\n" . '           group by kodesupplier,matauang order by sum(hargasatuan*jumlahpesan*kurs)  desc';

exit(mysql_error($sTot));
($qTot = mysql_query($sTot)) || true;
$rRow = mysql_num_rows($qTot);

while ($rTot = mysql_fetch_assoc($qTot)) {
	$ard += 1;

	if ($rTot['total'] != '') {
		$nilBrg += $rTot['kodesupplier'] . $rTot['matauang'];
		$totSma += $rTot['matauang'];
		$dtBrg[$rTot['kodesupplier']] = $rTot['kodesupplier'];

		if ($rTot['matauang'] != '') {
			$mtUang[$rTot['matauang']] = $rTot['matauang'];
		}

		if ($ard == 1) {
			$drt = '\'' . $rTot['kodesupplier'] . '\'';
		}
		else {
			$drt .= ',\'' . $rTot['kodesupplier'] . '\'';
		}
	}
}

$sDt = 'select distinct sum(hargasatuan*jumlahpesan) as total,matauang from ' . "\r\n" . '      ' . $dbname . '.log_po_vw where kodesupplier not in (' . $drt . ') and tanggal like \'' . $periode . '%\'' . "\r\n" . '      group by matauang order by sum(hargasatuan*jumlahpesan*kurs) desc';

#exit(mysql_error($conn));
($qDt = mysql_query($sDt)) || true;

while ($rDt = mysql_fetch_assoc($qDt)) {
	if ($rDt['total'] != '') {
		$rDt['kodesupplier'] = 'LAIN';
		$dtBrg[$rDt['kodesupplier']] = $rDt['kodesupplier'];
		$nilBrg += $rDt['kodesupplier'] . $rDt['matauang'];
		$totSma += $rDt['matauang'];

		if ($rDt['matauang'] != '') {
			$mtUang[$rDt['matauang']] = $rDt['matauang'];
		}
	}
}

$colJum = count($mtUang);
$bg = '';
$brdr = 0;

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>' . $judul . '</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr>    ' . "\r\n" . '</table>';
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['kelompokbarang'] . '</td>' . "\r\n" . '    <td align=center colspan=\'' . $colJum . '\' ' . $bg . '>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>% IDR</td>' . "\r\n" . '    </tr>';
$tab .= "\r\n" . '    <tr>';

foreach ($mtUang as $dtMat) {
	$tab .= '<td align=center ' . $bg . '>' . $dtMat . '</td>';
}

$tab .= '</tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";

foreach ($dtBrg as $dtKlmpBrg) {
	$ert += 1;
	$tab .= '<tr class=rowcontent onclick=getDet(\'lbm_slave_proc_supplier\',\'' . $dtKlmpBrg . '\',\'' . $periode . '\') style=cursor:pointer>';
	$tab .= '<td>' . $ert . '</td>';
	$tab .= '<td>' . $optKlmpk[$dtKlmpBrg] . '</td>';

	foreach ($mtUang as $dtMat2) {
		$tab .= '<td align=right>' . number_format($nilBrg[$dtKlmpBrg . $dtMat2], 0) . '</td>';
		@$prsn[$dtKlmpBrg] = ($nilBrg[$dtKlmpBrg . $dfrIdr] / $totSma[$dfrIdr]) * 100;
	}

	$tab .= '<td align=right>' . number_format($prsn[$dtKlmpBrg], 2) . '</td>';
	$tab .= '</tr>';
	$totPersen += $prsn[$dtKlmpBrg];
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';

foreach ($mtUang as $dtMat3) {
	$tab .= '<td align=right>' . number_format($totSma[$dtMat3], 0) . '</td>';
}

$tab .= '<td align=right>' . $totPersen . '</td>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	echo $tab;
	break;

case 'excel':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'nilaiPembelian_' . $dte;

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
			global $judul;
			global $unit;
			global $optNm;
			global $optBulan;
			global $tahun;
			global $bulan;
			global $dbname;
			global $luas;
			global $wkiri;
			global $wlain;
			global $luasbudg;
			global $luasreal;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width / 2, $height, $judul, NULL, 0, 'L', 1);
			$this->Cell($width / 2, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln(35);
			$height = 15;
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(($wkiri / 100) * $width, $height, 'Uraian', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 0, 'C', 1);
			$this->Ln();
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$cols = 247.5;
	$wkiri = 30;
	$wlain = 11.5;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 9);
	$pdf->Output();
	break;

case 'getDetail':
	$judul = strtoupper('Detail Pembelian ' . $optKlmpk[$klmpkBrg]);
	$sData = 'select distinct sum(jumlahpesan*hargasatuan) as total,matauang ,kodesupplier,kodebarang,sum(jumlahpesan) as jumlahpesan' . "\r\n" . '                from ' . $dbname . '.log_po_vw where kodesupplier=\'' . $klmpkBrg . '\'' . "\r\n" . '                and tanggal like \'' . $periode . '%\'    ' . "\r\n" . '                group by matauang,kodesupplier,kodebarang order by (jumlahpesan*hargasatuan) desc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		if ($rData['total'] != 0) {
			$dtMatuang[$rData['matauang']] = $rData['matauang'];
			$dtOrg[$rData['kodebarang']] = $rData['kodebarang'];
			$nilBrg[$rData['kodebarang'] . $rData['matauang']] = $rData['total'];
			$jmlhPsn[$rData['kodebarang']] = $rData['jumlahpesan'];
		}
	}

	$colsl = count($dtMatuang);
	$org = count($dtOrg);
	$smua = $colsl * $org;
	$tabc .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tabc .= '<tr>';
	$tabc .= '<td>No.</td><td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tabc .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';

	foreach ($dtMatuang as $lstMataung) {
		$tabc .= '<td>' . $lstMataung . '</td>';
	}

	$tabc .= '</tr></thead><tbody>';

	foreach ($dtOrg as $lstOrg2) {
		$artt += 1;
		$tabc .= '<tr class=rowcontent>';
		$tabc .= '<td>' . $artt . '</td>';
		$tabc .= '<td>' . $optNmBrg[$lstOrg2] . '</td>';
		$tabc .= '<td align=right>' . number_format($jmlhPsn[$lstOrg2], 0) . '</td>';

		foreach ($dtMatuang as $lstMataung) {
			$tabc .= '<td align=right>' . number_format($nilBrg[$lstOrg2 . $lstMataung], 0) . '</td>';
			$total += $lstMataung;
		}

		$tabc .= '</tr>';
	}

	$tabc .= '<tr class=rowcontent><td colspan=3>' . $_SESSION['lang']['total'] . '</td>';

	foreach ($dtMatuang as $lstMataung) {
		$tabc .= '<td align=right>' . number_format($total[$lstMataung], 0) . '</td>';
	}

	$tabc .= '</tr><tr><td colspan=' . (3 + $colsl) . '>';
	$tabc .= '<button class=mybutton onclick=zBack()>Back</button>';
	$tabc .= '<button onclick="zExcel2(event,\'lbm_slave_proc_supplier.php\',\'getDetPtEx\',\'' . $lstOrg3 . '\',\'' . $klmpkBrg . '\',\'' . $lstMatauang2 . '\',\'' . $periode . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . "\r\n" . '               ' . $_SESSION['lang']['excel'] . '</button></tr> ';
	$tabc .= '</tbody></table>';
	echo $tabc . '###' . $judul;
	break;

case 'getDetPtEx':
	$judul = strtoupper('Detail Pembelian ' . $optKlmpk[$klmpkBrg]);
	$sData = 'select distinct sum(jumlahpesan*hargasatuan) as total,matauang ,kodesupplier,kodebarang,jumlahpesan' . "\r\n" . '                from ' . $dbname . '.log_po_vw where kodesupplier=\'' . $klmpkBrg . '\'' . "\r\n" . '                and tanggal like \'' . $periode . '%\'    ' . "\r\n" . '                group by matauang,kodesupplier,kodebarang order by (jumlahpesan*hargasatuan) desc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		if ($rData['total'] != '0') {
			$dtMatuang[$rData['matauang']] = $rData['matauang'];
			$dtOrg[$rData['kodebarang']] = $rData['kodebarang'];
			$nilBrg[$rData['kodebarang'] . $rData['matauang']] = $rData['total'];
			$jmlhPsn[$rData['kodebarang']] = $rData['jumlahpesan'];
		}
	}

	$colsl = count($dtMatuang);
	$org = count($dtOrg);
	$smua = $colsl * $org;
	$tabc .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
	$tabc .= '<tr>';
	$tabc .= '<td  bgcolor=#DEDEDE>No.</td><td bgcolor=#DEDEDE>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['jumlah'] . '</td>';

	foreach ($dtMatuang as $lstMataung) {
		$tabc .= '<td bgcolor=#DEDEDE>' . $lstMataung . '</td>';
	}

	$tabc .= '</tr></thead><tbody>';

	foreach ($dtOrg as $lstOrg2) {
		$artt += 1;
		$tabc .= '<tr class=rowcontent>';
		$tabc .= '<td>' . $artt . '</td>';
		$tabc .= '<td>' . $optNmBrg[$lstOrg2] . '</td>';
		$tabc .= '<td align=right>' . number_format($jmlhPsn[$lstOrg2], 0) . '</td>';

		foreach ($dtMatuang as $lstMataung) {
			$tabc .= '<td align=right>' . number_format($nilBrg[$lstOrg2 . $lstMataung], 0) . '</td>';
			$total += $lstMataung;
		}

		$tabc .= '</tr>';
	}

	$tabc .= '<tr class=rowcontent><td colspan=3>' . $_SESSION['lang']['total'] . '</td>';

	foreach ($dtMatuang as $lstMataung) {
		$tabc .= '<td align=right>' . number_format($total[$lstMataung], 0) . '</td>';
	}

	$tabc .= '</tr>';
	$tabc .= '</tbody></table>';
	$tabc .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'detailSupp_' . $dte;

	if (0 < strlen($tabc)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tabc)) {
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;

case 'getDetPt':
	$judul = strtoupper('Detail Nilai Pembeliaan ' . $optKlmpk[$klmpkBrg]);
	$sData = 'select distinct sum(jumlahpesan*hargasatuan) as total, substr(nopp,16,4) as unit,kodeorg from' . "\r\n" . '            ' . $dbname . '.log_po_vw where kodeorg=\'' . $pt . '\' and substr(kodebarang,1,3)=\'' . $klmpkBrg . '\'' . "\r\n" . '            and matauang=\'' . $mtuang . '\' and tanggal like \'' . $periode . '%\' group by  substr(nopp,16,4) order by substr(nopp,16,4) desc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		if ($rData['unit'] != '') {
			$untLa = $rData['unit'];
		}
		else {
			$rData['unit'] = $untLa;
		}

		$nilDt += $rData['unit'];
		$unitDt[$rData['unit']] = $rData['unit'];
	}

	$tabd .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tabd .= '<thead><tr>';

	foreach ($unitDt as $lsUnit) {
		$tabd .= '<td>' . $lsUnit . '</td>';
	}

	$tabd .= '<td>' . $_SESSION['lang']['total'] . '</td></tr></thead><tbody><tr class=rowcontent>';

	foreach ($unitDt as $lsUnit2) {
		$tabd .= '<td align=right>' . number_format($nilDt[$lsUnit2], 0) . '</td>';
		$tot += $nilDt[$lsUnit2];
	}

	$tabd .= '<td align=right>' . number_format($tot, 0) . '</td></tr>';
	$tabd .= '<tr><td>';
	$tabd .= '<button class=mybutton onclick=zBack2()>Back</button>' . "\r\n" . '        <button onclick="zExcel2(event,\'lbm_slave_proc_nilai_per_kelompok.php\',\'getDetPtExc\',\'' . $pt . '\',\'' . $klmpkBrg . '\',\'' . $mtuang . '\',\'' . $periode . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . "\r\n" . '               ' . $_SESSION['lang']['excel'] . '</button></td></tr>';
	$tabd .= '</tbody></table>';
	echo $tabd . '###' . $judul;
	break;

case 'getDetPtExc':
	$sData = 'select distinct sum(jumlahpesan*hargasatuan) as total, substr(nopp,16,4) as unit,kodeorg from' . "\r\n" . '            ' . $dbname . '.log_po_vw where kodeorg=\'' . $pt . '\' and substr(kodebarang,1,3)=\'' . $klmpkBrg . '\'' . "\r\n" . '            and matauang=\'' . $mtuang . '\' and tanggal like \'' . $periode . '%\' group by  substr(nopp,16,4) order by substr(nopp,16,4) desc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		if ($rData['unit'] != '') {
			$untLa = $rData['unit'];
		}
		else {
			$rData['unit'] = $untLa;
		}

		$nilDt += $rData['unit'];
		$unitDt[$rData['unit']] = $rData['unit'];
	}

	$tabd .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable>';
	$tabd .= '<thead><tr>';

	foreach ($unitDt as $lsUnit) {
		$tabd .= '<td   bgcolor=#DEDEDE>' . $lsUnit . '</td>';
	}

	$tabd .= '<td   bgcolor=#DEDEDE>' . $_SESSION['lang']['total'] . '</td></tr></thead><tbody><tr class=rowcontent>';

	foreach ($unitDt as $lsUnit2) {
		$tabd .= '<td align=right>' . number_format($nilDt[$lsUnit2], 0) . '</td>';
		$tot += $nilDt[$lsUnit2];
	}

	$tabd .= '<td align=right>' . number_format($tot, 0) . '</td></tr>';
	$tabd .= '</tbody></table>';
	$tabd .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'detailPt_' . $dte;

	if (0 < strlen($tabd)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tabd)) {
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;
}

?>
