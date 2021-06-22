<?php


function numberformat($qwe, $asd)
{
	if ($qwe == 0) {
		$zxc = '0';
	}
	else {
		$zxc = number_format($qwe, $asd);
	}

	return $zxc;
}

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
	exit('Error:Field required');
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
$dzArr = array();
$aresta = 'SELECT sum(luasareaproduktif) as luasareal FROM ' . $dbname . '.setup_blok' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$luasreal = $res['luasareal'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun' . "\r\n" . '    WHERE length(noakun)=7 and noakun like \'82212%\'' . "\r\n" . '    ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}
}

$str = 'SELECT noakun, setahun FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\' and noakun like \'82212%\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][111] = $res['setahun'];
	@$dzArr[$res['noakun']][112] = $res['setahun'] / $luasreal;
}

$str = 'SELECT noakun, rp' . $bulan . ' as bi FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\' and noakun like \'82212%\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][121] = $res['bi'];
	@$dzArr[$res['noakun']][122] = $res['bi'] / $luasreal;
}

$addstr = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'rp0' . $W;
	}
	else {
		$jack = 'rp' . $W;
	}

	if ($W < intval($bulan)) {
		$addstr .= $jack . '+';
	}
	else {
		$addstr .= $jack;
	}

	++$W;
}

$addstr .= ')';
$str = 'SELECT noakun, ' . $addstr . ' as jumlah FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\' and noakun like \'82212%\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][131] = $res['jumlah'];
	@$dzArr[$res['noakun']][132] = $res['jumlah'] / $luasreal;
}

$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and nojurnal like \'%' . $unit . '%\' and noakun like \'82212%\'' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][211] = $res['jumlah'];
	@$dzArr[$res['noakun']][212] = $res['jumlah'] / $luasreal;
}

$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and nojurnal like \'%' . $unit . '%\' and noakun like \'82212%\'' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][221] = $res['jumlah'];
	@$dzArr[$res['noakun']][222] = $res['jumlah'] / $luasreal;
}

if (!empty($dzArr)) {
	foreach ($dzArr as $keg) {
		@$dzArr[$keg['noakun']][311] = (100 * $keg[221]) / $keg[111];
		@$dzArr[$keg['noakun']][312] = (100 * $keg[221]) / $keg[131];
	}
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=8 align=left><font size=3>23. BIAYA KAPITAL NON TANAMAN</font></td>' . "\r\n" . '        <td colspan=6 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>   ' . "\r\n" . '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>Areal diusahakan:</td>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . numberformat($luasreal, 2) . '</td>' . "\r\n" . '    <td align=left colspan=10 ' . $bg . '>Ha</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>' . $_SESSION['lang']['pekerjaan'] . '</td>' . "\r\n" . '    <td align=center colspan=6 ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 colspan=2 ' . $bg . '>% ' . $_SESSION['lang']['pencapaian'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./Ha</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./Ha</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./Ha</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./Ha</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./Ha</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';
$no = 1;

if (empty($dzArr)) {
	$tab .= '<tr class=rowcontent><td colspan=14>Data Empty.</td></tr>';
}
else if (!empty($dzArr)) {
	foreach ($dzArr as $keg) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=right>' . $no . '</td>';
		$tab .= '<td>' . $keg['namaakun'] . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[111] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[112], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[121] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[122], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[131] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[132], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[211] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[212], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[221] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[222], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[311], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($keg[312], 2) . '</td>';
		$tab .= '</tr>';
		$no += 1;
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
	$nop_ = 'lbm_biayakapitalnontanaman_' . $unit . $periode;

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
			$this->Cell($width / 2, $height, '23. BIAYA KAPITAL NON TANAMAN', NULL, 0, 'L', 1);
			$this->Cell($width / 2, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);
			$this->Ln();
			$this->Ln();
			$height = 15;
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Areal diusahakan:', 0, 0, 'R', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($luasreal, 2) . ' Ha', 0, 0, 'C', 1);
			$this->Cell((($wlain * 10) / 100) * $width, $height, '', 0, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 6) / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, 'No.', RL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], RL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['pencapaian'], BRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./Ha', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./Ha', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./Ha', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./Ha', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./Ha', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
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
	$wkiri = 24;
	$wlain = 6;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$no = 1;

	if (!empty($dzArr)) {
		foreach ($dzArr as $keg) {
			$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
			$pdf->Cell(($wkiri / 100) * $width, $height, $keg['namaakun'], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[111] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[112], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[121] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[122], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[131] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[132], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[211] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[212], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[221] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[222], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[311], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($keg[312], 2), 1, 0, 'R', 1);
			$no += 1;
			$pdf->Ln();
		}
	}
	else {
		echo 'Data Empty.';
		exit();
	}

	$pdf->Output();
	break;
}

?>
