<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['unit'] == '' ? $unit = $_GET['unit'] : $unit = $_POST['unit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (($unit == '') || ($periode == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

$thn = explode('-', $periode);
$bln = intval($thn[1]);
$thnLalu = $thn[0];

if (strlen($bln) < 2) {
	if ($thn[1] == '1') {
		$blnLalu = 12;
		$thnLalu = $thn[0] - 1;
	}
	else {
		$bln = $bln - 1;
		$blnLalu = '0' . $bln;
	}
}
else {
	$blnLalu = $bln - 1;
}

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
$sKimia = 'SELECT sum(a.jumlah) as rupiah,sum(b.jumlah) as kuantitas,a.kodebarang,c.namabarang,c.satuan ' . "\r\n" . '         FROM ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.log_transaksidt ' . "\r\n" . '         b on a.noreferensi=b.notransaksi and a.kodebarang=b.kodebarang ' . "\r\n" . '         left join ' . $dbname . '.log_5masterbarang c on a.kodebarang=c.kodebarang ' . "\r\n" . '         where a.kodebarang like \'371%\' and a.nojurnal like \'%' . $unit . '%\' and a.jumlah>0 ' . "\r\n" . '         and a.noreferensi like \'%-GI-%\' and a.periode=\'' . $periode . '\' group by kodebarang order by c.namabarang asc';

#exit(mysql_error($conn));
($qKimia = mysql_query($sKimia)) || true;

while ($rKimia = mysql_fetch_assoc($qKimia)) {
	$dtKodebarang[$rKimia['kodebarang']] = $rKimia['kodebarang'];
	$nmUraian[$rKimia['kodebarang']] = $rKimia['namabarang'];
	$lstRupiah[$rKimia['kodebarang']] = $rKimia['rupiah'];
	$lstJumlah[$rKimia['kodebarang']] = $rKimia['kuantitas'];
	$nmSatuan[$rKimia['kodebarang']] = $rKimia['satuan'];
}

$sKimiaSbi = 'SELECT sum(a.jumlah) as rupiah,sum(b.jumlah) as kuantitas,a.kodebarang,c.namabarang,c.satuan ' . "\r\n" . '         FROM ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.log_transaksidt ' . "\r\n" . '         b on a.noreferensi=b.notransaksi and a.kodebarang=b.kodebarang ' . "\r\n" . '         left join ' . $dbname . '.log_5masterbarang c on a.kodebarang=c.kodebarang ' . "\r\n" . '         where a.kodebarang like \'371%\' and a.nojurnal like \'%' . $unit . '%\' and a.jumlah>0 ' . "\r\n" . '         and a.noreferensi like \'%-GI-%\' and a.periode between \'' . $thnLalu . '-01\' and \'' . $periode . '\' group by kodebarang order by c.namabarang asc';

#exit(mysql_error($conn));
($qKimiSbi = mysql_query($sKimiaSbi)) || true;

while ($rKimiaSbi = mysql_fetch_assoc($qKimiSbi)) {
	$dtKodebarang[$rKimiaSbi['kodebarang']] = $rKimiaSbi['kodebarang'];
	$nmUraian[$rKimiaSbi['kodebarang']] = $rKimiaSbi['namabarang'];
	$lstRupiahSbi[$rKimiaSbi['kodebarang']] = $rKimiaSbi['rupiah'];
	$lstJumlahSbi[$rKimiaSbi['kodebarang']] = $rKimiaSbi['kuantitas'];
	$nmSatuan[$rKimiaSbi['kodebarang']] = $rKimiaSbi['satuan'];
}

$sConsumble = 'SELECT sum(a.jumlah) as rupiah,sum(b.jumlah) as kuantitas,a.kodebarang,c.namabarang,c.satuan ' . "\r\n" . '             FROM ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.log_transaksidt b on a.noreferensi=b.notransaksi and a.kodebarang=b.kodebarang ' . "\r\n" . '             left join ' . $dbname . '.log_5masterbarang c on a.kodebarang=c.kodebarang ' . "\r\n" . '             where a.kodebarang like \'351%\' and a.nojurnal like \'%' . $unit . '%\' and a.jumlah>0 ' . "\r\n" . '             and a.noreferensi like \'%-GI-%\' and a.periode=\'' . $periode . '\' group by kodebarang order by c.namabarang asc';

#exit(mysql_error($conn));
($qConsumble = mysql_query($sConsumble)) || true;

while ($rConsumble = mysql_fetch_assoc($qConsumble)) {
	$dtKdbarang[$rConsumble['kodebarang']] = $rConsumble['kodebarang'];
	$nmBarang[$rConsumble['kodebarang']] = $rConsumble['namabarang'];
	$lstRph[$rConsumble['kodebarang']] = $rConsumble['rupiah'];
	$lstJmlh[$rConsumble['kodebarang']] = $rConsumble['kuantitas'];
	$nmStn[$rConsumble['kodebarang']] = $rConsumble['satuan'];
}

$sConsumbleSbi = 'SELECT sum(a.jumlah) as rupiah,sum(b.jumlah) as kuantitas,a.kodebarang,c.namabarang,c.satuan ' . "\r\n" . '             FROM ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.log_transaksidt b on a.noreferensi=b.notransaksi and a.kodebarang=b.kodebarang ' . "\r\n" . '             left join ' . $dbname . '.log_5masterbarang c on a.kodebarang=c.kodebarang ' . "\r\n" . '             where a.kodebarang like \'351%\' and a.nojurnal like \'%' . $unit . '%\' and a.jumlah>0 ' . "\r\n" . '             and a.noreferensi like \'%-GI-%\' and a.periode between \'' . $thnLalu . '-01\' and \'' . $periode . '\' group by kodebarang order by c.namabarang asc';

#exit(mysql_error($conn));
($qConsumbleSbi = mysql_query($sConsumbleSbi)) || true;

while ($rConsumbleSbi = mysql_fetch_assoc($qConsumbleSbi)) {
	$dtKdbarang[$rConsumbleSbi['kodebarang']] = $rConsumbleSbi['kodebarang'];
	$nmBarang[$rConsumbleSbi['kodebarang']] = $rConsumbleSbi['namabarang'];
	$lstRphSbi[$rConsumbleSbi['kodebarang']] = $rConsumbleSbi['rupiah'];
	$lstJmlhSbi[$rConsumbleSbi['kodebarang']] = $rConsumbleSbi['kuantitas'];
	$nmStn[$rConsumbleSbi['kodebarang']] = $rConsumbleSbi['satuan'];
}

$varCek = count($dtKodebarang);
$varCek2 = count($dtKdbarang);

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>PEMAKAIAN BAHAN KIMIA & CONSUMABLE PRODUKSI</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>   ' . "\r\n" . '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>Uraian</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>Satuan</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>Kuantitas</td>' . "\r\n" . '    <td align=center ' . $bg . '>RP.</td>' . "\r\n" . '    <td align=center ' . $bg . '>Kuantitas</td>' . "\r\n" . '    <td align=center ' . $bg . '>RP.</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>';

if ($varCek == 0) {
	$tab .= '<tr class=rowcontent><td colspan=7>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}
else {
	foreach ($dtKodebarang as $dtKdbrg) {
		++$no;
		$tab .= '<tr class=rowcontent><td>' . $no . '</td>';
		$tab .= '<td>' . $nmUraian[$dtKdbrg] . '</td>';
		$tab .= '<td>' . $nmSatuan[$dtKdbrg] . '</td>';
		$tab .= '<td align=right>' . number_format($lstJumlah[$dtKdbrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($lstRupiah[$dtKdbrg], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($lstJumlahSbi[$dtKdbrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($lstRupiahSbi[$dtKdbrg], 2) . '</td>';
		$tab .= '</tr>';
	}
}

$tab .= '</tbody></table><table><tr><td colspan=7>&nbsp;<td></tr></table>';
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>Uraian</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['rp'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['rp'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>';

if ($varCek2 == 0) {
	$tab .= '<tr class=rowcontent><td colspan=7>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}
else {
	foreach ($dtKdbarang as $dtKodebrg) {
		++$no5;
		$tab .= '<tr class=rowcontent><td>' . $no5 . '</td>';
		$tab .= '<td>' . $nmBarang[$dtKodebrg] . '</td>';
		$tab .= '<td>' . $nmStn[$dtKodebrg] . '</td>';
		$tab .= '<td align=right>' . number_format($lstJmlh[$dtKodebrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($lstRph[$dtKodebrg], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($lstJmlhSbi[$dtKodebrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($lstRphSbi[$dtKodebrg], 2) . '</td>';
		$tab .= '</tr>';
	}
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	echo $tab;
	break;

case 'excel':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = $judul . '_' . $unit . '_' . $periode;

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
	if (($unit == '') || ($periode == '')) {
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
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);
			$this->Ln();
			$this->Ln();
			$height = 15;
			$this->SetFont('Arial', 'B', 10);
			$this->Cell((8 / 100) * $width, $height, 'No.', TRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, 'Uraian', TRL, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 1, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, ' ', BRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['kuantitas'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['kuantitas'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 1, 'C', 1);
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
	$no = 1;

	if ($varCek == 0) {
		$pdf->Cell((($cols + 23) / 100) * $width, $height, $_SESSION['lang']['dataempty'], TBRL, 1, 'C', 1);
	}
	else {
		foreach ($dtKodebarang as $dtKdbrg) {
			++$nor;
			$pdf->Cell((8 / 100) * $width, $height, $nor, TBRL, 0, 'C', 1);
			$pdf->Cell(($wkiri / 100) * $width, $height, $nmUraian[$dtKdbrg], TBRL, 0, 'L', 1);
			$pdf->Cell((15 / 100) * $width, $height, $nmSatuan[$dtKdbrg], TBRL, 0, 'C', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstJumlah[$dtKdbrg], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstRupiah[$dtKdbrg], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstJumlahSbi[$dtKdbrg], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstRupiahSbi[$dtKdbrg], 2), 1, 1, 'R', 1);
		}
	}

	$pdf->ln(20);

	if ($varCek2 == 0) {
		$pdf->Cell((($cols + 23) / 100) * $width, $height, $_SESSION['lang']['dataempty'], TBRL, 1, 'C', 1);
	}
	else {
		$height = 15;
		$height = 20;
		$pdf->SetFillColor(220, 220, 220);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell((8 / 100) * $width, $height, 'No.', TRL, 0, 'C', 1);
		$pdf->Cell(($wkiri / 100) * $width, $height, 'Uraian', TRL, 0, 'C', 1);
		$pdf->Cell((15 / 100) * $width, $height, $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
		$pdf->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 1, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, ' ', BRL, 0, 'C', 1);
		$pdf->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell((15 / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['kuantitas'], 1, 0, 'C', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 0, 'C', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['kuantitas'], 1, 0, 'C', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 1, 'C', 1);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', '', 9);

		foreach ($dtKdbarang as $dtKodebrg) {
			++$nor5;
			$pdf->Cell((8 / 100) * $width, $height, $nor5, TBRL, 0, 'C', 1);
			$pdf->Cell(($wkiri / 100) * $width, $height, $nmBarang[$dtKodebrg], TBRL, 0, 'L', 1);
			$pdf->Cell((15 / 100) * $width, $height, $nmStn[$dtKodebrg], TBRL, 0, 'C', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstJmlh[$dtKodebrg], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstRph[$dtKodebrg], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstJmlhSbi[$dtKodebrg], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($lstRphSbi[$dtKodebrg], 2), 1, 1, 'R', 1);
		}
	}

	$pdf->Output();
	break;
}

?>
