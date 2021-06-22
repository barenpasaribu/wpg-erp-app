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
$aresta = 'SELECT sum(tbsdiolah) as tbs FROM ' . $dbname . '.pabrik_produksi' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$tbs = $res['tbs'];
}

$aresta = 'SELECT sum(olah' . $bulan . ') as tbsbudget FROM ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '    WHERE millcode like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$tbsbudget = $res['tbsbudget'];
}

$tbsselisih = $tbsbudget - $tbs;
$aresta = 'SELECT sum(tbsdiolah) as tbs FROM ' . $dbname . '.pabrik_produksi' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$tbssd = $res['tbs'];
}

$addstr = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'olah0' . $W;
	}
	else {
		$jack = 'olah' . $W;
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
$aresta = 'SELECT sum(' . $addstr . ') as tbsbudget FROM ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '    WHERE millcode like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$tbsbudgetsd = $res['tbsbudget'];
}

$tbsselisihsd = $tbsbudgetsd - $tbssd;
$aresta = 'SELECT sum(oer) as cpo FROM ' . $dbname . '.pabrik_produksi' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$cpo = $res['cpo'];
}

$aresta = 'SELECT sum(kgcpo' . $bulan . ') as cpobudget FROM ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '    WHERE millcode like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$cpobudget = $res['cpobudget'];
}

$cposelisih = $cpobudget - $cpo;
$aresta = 'SELECT sum(oer) as cpo FROM ' . $dbname . '.pabrik_produksi' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$cposd = $res['cpo'];
}

$addstr = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'kgcpo0' . $W;
	}
	else {
		$jack = 'kgcpo' . $W;
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
$aresta = 'SELECT sum(' . $addstr . ') as cpobudget FROM ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '    WHERE millcode like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$cpobudgetsd = $res['cpobudget'];
}

$cposelisihsd = $cpobudgetsd - $cposd;
$aresta = 'SELECT noakun,sum(jumlah) as biaya FROM ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\' and (noakun like \'631%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$akun[$res['noakun']] = $res['noakun'];
	$dzArr[$res['noakun']]['biaya'] = $res['biaya'];
}

$aresta = 'SELECT noakun,sum(rp' . $bulan . ') as budget FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\' and (noakun like \'631%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$akun[$res['noakun']] = $res['noakun'];
	$dzArr[$res['noakun']]['budget'] = $res['budget'];
}

$aresta = 'SELECT noakun,sum(jumlah) as biaya FROM ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') and (noakun like \'631%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$akun[$res['noakun']] = $res['noakun'];
	$dzArr[$res['noakun']]['biayasd'] = $res['biaya'];
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
$aresta = 'SELECT noakun,sum(' . $addstr . ') as budget FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\' and (noakun like \'631%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$akun[$res['noakun']] = $res['noakun'];
	$dzArr[$res['noakun']]['budgetsd'] = $res['budget'];
}

$aresta = 'SELECT noakun, namaakun FROM ' . $dbname . '.keu_5akun' . "\r\n" . '    WHERE length(noakun)=7 and (noakun like \'631%\')' . "\r\n" . '    ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kamusakun[$res['noakun']]['no'] = $res['noakun'];
	$kamusakun[$res['noakun']]['nama'] = $res['namaakun'];
}

if (!empty($akun)) {
	foreach ($akun as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total += 'biaya';
		$total += 'budget';
		$total += 'selisih';
		$total += 'biayasd';
		$total += 'budgetsd';
		$total += 'selisihsd';
	}
}

if (!empty($akun)) {
	asort($akun);
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>' . $judul . '</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>   ' . "\r\n" . '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>Uraian</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['selisih'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['selisih'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';
$no = 1;
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>' . $_SESSION['lang']['tbsdiolah'] . ' (Ton)</td>';
$tab .= '<td align=right>' . number_format($tbs / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsbudget / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsselisih / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbssd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsbudgetsd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsselisihsd / 1000) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>' . $_SESSION['lang']['cpokuantitas'] . ' (Ton)</td>';
$tab .= '<td align=right>' . number_format($cpo / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cpobudget / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cposelisih / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cposd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cpobudgetsd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cposelisihsd / 1000) . '</td>';
$tab .= '</tr><tr><td colspan=7>&nbsp;</td></tr>';

if (!empty($akun)) {
	foreach ($akun as $akyun) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $akyun . ' - ' . $kamusakun[$akyun]['nama'] . '</td>';
		$tab .= '<td align=right>' . number_format($dzArr[$akyun]['biaya']) . '</td>';
		$tab .= '<td align=right>' . number_format($dzArr[$akyun]['budget']) . '</td>';
		$tab .= '<td align=right>' . number_format($dzArr[$akyun]['selisih']) . '</td>';
		$tab .= '<td align=right>' . number_format($dzArr[$akyun]['biayasd']) . '</td>';
		$tab .= '<td align=right>' . number_format($dzArr[$akyun]['budgetsd']) . '</td>';
		$tab .= '<td align=right>' . number_format($dzArr[$akyun]['selisihsd']) . '</td>';
		$tab .= '</tr>';
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=center>Total</td>';
	$tab .= '<td align=right>' . number_format($total['biaya']) . '</td>';
	$tab .= '<td align=right>' . number_format($total['budget']) . '</td>';
	$tab .= '<td align=right>' . number_format($total['selisih']) . '</td>';
	$tab .= '<td align=right>' . number_format($total['biayasd']) . '</td>';
	$tab .= '<td align=right>' . number_format($total['budgetsd']) . '</td>';
	$tab .= '<td align=right>' . number_format($total['selisihsd']) . '</td>';
	$tab .= '</tr>';
}
else {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=7>Data Empty</td>';
	$tab .= '</tr>';
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
	$no = 1;
	$pdf->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['tbsdiolah'] . ' (Ton)', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbs / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsbudget / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsselisih / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbssd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsbudgetsd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsselisihsd / 1000), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['cpokuantitas'] . ' (Ton)', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cpo / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cpobudget / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cposelisih / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cposd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cpobudgetsd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cposelisihsd / 1000), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, '', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Ln();

	if (!empty($akun)) {
		foreach ($akun as $akyun) {
			$pdf->Cell(($wkiri / 100) * $width, $height, $akyun . ' - ' . $kamusakun[$akyun]['nama'], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($dzArr[$akyun]['biaya']), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($dzArr[$akyun]['budget']), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($dzArr[$akyun]['selisih']), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($dzArr[$akyun]['biayasd']), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($dzArr[$akyun]['budgetsd']), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($dzArr[$akyun]['selisihsd']), 1, 0, 'R', 1);
			$pdf->Ln();
		}

		$pdf->Cell(($wkiri / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['biaya']), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['budget']), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['selisih']), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['biayasd']), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['budgetsd']), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['selisihsd']), 1, 0, 'R', 1);
		$pdf->Ln();
	}
	else {
		$pdf->Cell(($wkiri / 100) * $width, $height, 'Data Empty', 1, 0, 'L', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
	}

	$pdf->Output();
	break;
}

?>
