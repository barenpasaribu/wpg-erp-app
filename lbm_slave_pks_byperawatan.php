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

$_GET['kdOrg'] == '' ? $kdOrg = $_POST['kdOrg'] : $kdOrg = $_GET['kdOrg'];
$_GET['periode'] == '' ? $periode = $_POST['periode'] : $periode = $_GET['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$thn = explode('-', $periode);
$bln = intval($thn[1]);
$thnLalu = $thn[0];

if (strlen($bln) < 2) {
	$bulan = '0' . $bln;
}
else {
	$bulan = $bln;
}

if (strlen($thn[1]) < 2) {
	$field = 'olah0' . $thn[1];
	$fld = 'kgcpo0' . $thn[1];
	$fld_st = 'rp0' . $thn[1];
}
else {
	$field = 'olah' . $thn[1];
	$fld = 'kgcpo' . $thn[1];
	$fld_st = 'rp' . $thn[1];
}

$asr5 = 1;

while ($asr5 <= $thn[1]) {
	if (strlen($asr5) < 2) {
		if ($asr5 == 1) {
			$field5 = 'olah0' . $asr5;
			$fld5 = 'kgcpo0' . $asr5;
			$fld_st5 = 'rp0' . $asr5;
		}
		else {
			$field5 .= '+olah0' . $asr5;
			$fld5 .= '+kgcpo0' . $asr5;
			$fld_st5 .= '+rp0' . $asr5;
		}
	}
	else {
		$field5 .= '+olah' . $asr5;
		$fld5 .= '+kgcpo' . $asr5;
		$fld_st5 .= '+rp' . $asr5;
	}

	++$asr5;
}

$s_birealtbs = 'select sum(tbsdiolah) as realtbs from ' . $dbname . '.pabrik_produksi' . "\r\n" . '       where kodeorg=\'' . $kdOrg . '\' and tanggal like \'' . $periode . '%\' ';

#exit(mysql_error($conn));
($q_birealtbs = mysql_query($s_birealtbs)) || true;

while ($r_birealtbs = mysql_fetch_assoc($q_birealtbs)) {
	$bireal_tbsdiolah = $r_birealtbs['realtbs'];
}

$s_bibudtbs = 'select sum(olah' . $bulan . ') as budgettbs from ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '             where millcode=\'' . $kdOrg . '\' and tahunbudget=\'' . substr($periode, 0, 4) . '\' ';

#exit(mysql_error($conn));
($q_bibudtbs = mysql_query($s_bibudtbs)) || true;

while ($r_bibudtbs = mysql_fetch_assoc($q_bibudtbs)) {
	$bibudget_tbsdiolah = $r_bibudtbs['budgettbs'];
}

$s_sdbirealtbs = 'select sum(tbsdiolah) as realtbs from ' . $dbname . '.pabrik_produksi' . "\r\n" . '                where kodeorg=\'' . $kdOrg . '\' and tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') ';

#exit(mysql_error($conn));
($q_sdbirealtbs = mysql_query($s_sdbirealtbs)) || true;

while ($r_sdbirealtbs = mysql_fetch_assoc($q_sdbirealtbs)) {
	$sdbireal_tbsdiolah = $r_sdbirealtbs['realtbs'];
}

$s_sdbibudtbs = 'select sum(' . $field5 . ') as budgettbs from ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '               where millcode=\'' . $kdOrg . '\' and tahunbudget=\'' . substr($periode, 0, 4) . '\' ';

#exit(mysql_error($conn));
($q_sdbibudtbs = mysql_query($s_sdbibudtbs)) || true;

while ($r_sdbibudtbs = mysql_fetch_assoc($q_sdbibudtbs)) {
	$sdbibudget_tbsdiolah = $r_sdbibudtbs['budgettbs'];
}

$s_birealcpo = 'select sum(oer) as realcpo from ' . $dbname . '.pabrik_produksi' . "\r\n" . '       where kodeorg=\'' . $kdOrg . '\' and tanggal like \'' . $periode . '%\' ';

#exit(mysql_error($conn));
($q_birealcpo = mysql_query($s_birealcpo)) || true;

while ($r_birealcpo = mysql_fetch_assoc($q_birealcpo)) {
	$bireal_cpo = $r_birealcpo['realcpo'];
}

$s_bibudcpo = 'select sum(kgcpo' . $bulan . ') as budgetcpo from ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '             where millcode=\'' . $kdOrg . '\' and tahunbudget=\'' . substr($periode, 0, 4) . '\' ';

#exit(mysql_error($conn));
($q_bibudcpo = mysql_query($s_bibudcpo)) || true;

while ($r_bibudcpo = mysql_fetch_assoc($q_bibudcpo)) {
	$bibudget_cpo = $r_bibudcpo['budgetcpo'];
}

$s_sdbirealcpo = 'select sum(oer) as realcpo from ' . $dbname . '.pabrik_produksi' . "\r\n" . '                where kodeorg=\'' . $kdOrg . '\' and tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') ';

#exit(mysql_error($conn));
($q_sdbirealcpo = mysql_query($s_sdbirealcpo)) || true;

while ($r_sdbirealcpo = mysql_fetch_assoc($q_sdbirealcpo)) {
	$sdbireal_cpo = $r_sdbirealcpo['realcpo'];
}

$s_sdbibudcpo = 'select sum(' . $fld5 . ') as budgetcpo from ' . $dbname . '.bgt_produksi_pks_vw' . "\r\n" . '               where millcode=\'' . $kdOrg . '\' and tahunbudget=\'' . substr($periode, 0, 4) . '\' ';

#exit(mysql_error($conn));
($q_sdbibudcpo = mysql_query($s_sdbibudcpo)) || true;

while ($r_sdbibudcpo = mysql_fetch_assoc($q_sdbibudcpo)) {
	$sdbibudget_cpo = $r_sdbibudcpo['budgetcpo'];
}

$s_station = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '             where tipe=\'STATION\' and kodeorganisasi like \'' . $kdOrg . '%\' ';

#exit(mysql_error($conn));
($q_station = mysql_query($s_station)) || true;

while ($r_station = mysql_fetch_assoc($q_station)) {
	$kodeorg[] = $r_station['kodeorganisasi'];
	$station[$r_station['kodeorganisasi']] = $r_station['namaorganisasi'];
}

$s_realstbi = 'select left(kodeblok,6) as blok,sum(jumlah) as realst from ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '              where noakun like \'632%\' and kodeblok like \'' . $kdOrg . '%\' and tanggal like \'' . $periode . '%\' group by left(kodeblok,6)';

#exit(mysql_error($conn));
($q_realstbi = mysql_query($s_realstbi)) || true;

while ($r_realstbi = mysql_fetch_assoc($q_realstbi)) {
	$kodeblok[] = $r_realstbi['blok'];
	$bi_realst[$r_realstbi['blok']] = $r_realstbi['realst'];
}

$s_budstbi = 'select left(kodeorg,6) as kode,sum(rp' . $bulan . ') as budget_st from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '            where noakun like \'632%\' and kodeorg like \'' . $kdOrg . '%\' and tahunbudget=\'' . substr($periode, 0, 4) . '\' ' . "\r\n" . '            group by left(kodeorg,6)';

#exit(mysql_error($conn));
($q_budstbi = mysql_query($s_budstbi)) || true;

while ($r_budstbi = mysql_fetch_assoc($q_budstbi)) {
	$kode[] = $r_budstbi['kode'];
	$bi_budst[$r_budstbi['kode']] = $r_budstbi['budget_st'];
}

$s_realstsdbi = 'select left(kodeblok,6) as kdblok,sum(jumlah) as realst_sdbi from ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '                where noakun like \'632%\' and kodeblok like \'' . $kdOrg . '%\' and tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') ' . "\r\n" . '                group by left(kodeblok,6)';

#exit(mysql_error($conn));
($q_realstsdbi = mysql_query($s_realstsdbi)) || true;

while ($r_realstsdbi = mysql_fetch_assoc($q_realstsdbi)) {
	$kodeblok[] = $r_realstsdbi['kdblok'];
	$sdbi_realst[$r_realstsdbi['kdblok']] = $r_realstsdbi['realst_sdbi'];
}

$s_budstsdbi = 'select left(kodeorg,6) as korg,sum(' . $fld_st5 . ') as bgt_st from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '              where noakun like \'632%\' and kodeorg like \'' . $kdOrg . '%\' and tahunbudget=\'' . substr($periode, 0, 4) . '\' ' . "\r\n" . '              group by left(kodeorg,6)';

#exit(mysql_error($conn));
($q_budstsdbi = mysql_query($s_budstsdbi)) || true;

while ($r_budstsdbi = mysql_fetch_assoc($q_budstsdbi)) {
	$kode[] = $r_budstsdbi['korg'];
	$sdbi_budst[$r_budstsdbi['korg']] = $r_budstsdbi['bgt_st'];
}

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=7 align=center><b>' . $_GET['judul'] . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=3 align=left><b>' . $_SESSION['lang']['organisasi'] . ' : ' . $kdOrg . '</b></td>' . "\r\n" . '        <td colspan=4 align=right><b>' . $_SESSION['lang']['periode'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=7 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}
else {
	$brdr = 0;
}

$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>';
$tab .= '<tr align=center>';
$tab .= '<td rowspan=2>' . $_SESSION['lang']['uraian'] . '</td>';
$tab .= '<td colspan=3>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td colspan=3>' . $_SESSION['lang']['sdbulanini'] . '</td></tr>';
$tab .= '<tr align=center><td>' . $_SESSION['lang']['realisasi'] . '</td><td>' . $_SESSION['lang']['anggaran'] . '</td><td>' . $_SESSION['lang']['selisih'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['realisasi'] . '</td><td>' . $_SESSION['lang']['anggaran'] . '</td><td>' . $_SESSION['lang']['selisih'] . '</td>';
$tab .= '</tr></thead>';

if (!empty($kodeorg)) {
	$total_bi_realst = 0;

	foreach ($kodeorg as $lst_station) {
		$derclick = ' style=cursor:pointer; onclick=getDetail(\'' . $lst_station . '\',\'' . $periode . '\',\'lbm_slave_pks_byperawatandetail\')';
		$tab .= '<tr class=rowcontent ' . $derclick . '>';
		$tab .= '<td>' . $station[$lst_station] . '</td>';
		$tab .= '<td align=right>' . number_format($bi_realst[$lst_station], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($bi_budst[$lst_station], 0) . '</td>';
		$biselisih_st = $bi_budst[$lst_station] - $bi_realst[$lst_station];
		$tab .= '<td align=right>' . number_format($biselisih_st, 0) . '</td>';
		$tab .= '<td align=right>' . number_format($sdbi_realst[$lst_station], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($sdbi_budst[$lst_station], 0) . '</td>';
		$sdbiselisih_st = $sdbi_budst[$lst_station] - $sdbi_realst[$lst_station];
		$tab .= '<td align=right>' . number_format($sdbiselisih_st, 0) . '</td>';
		$tab .= '</tr>';
		$total_bi_realst += $bi_realst[$lst_station];
		$total_bi_budst += $bi_budst[$lst_station];
		$total_bi_selisih += $biselisih_st;
		$total_sdbi_realst += $sdbi_realst[$lst_station];
		$total_sdbi_budst += $sdbi_budst[$lst_station];
		$total_sdbi_selisih += $sdbiselisih_st;
	}
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Undefined Station</td>';
$tab .= '<td align=right>' . number_format($bi_realst[''], 0) . '</td>';
$tab .= '<td align=right>' . number_format($bi_budst[''], 0) . '</td>';
$biselisih_undef = $bi_budst[''] - $bi_realst[''];
$tab .= '<td align=right>' . number_format($biselisih_undef, 0) . '</td>';
$tab .= '<td align=right>' . number_format($sdbi_realst[''], 0) . '</td>';
$tab .= '<td align=right>' . number_format($sdbi_budst[''], 0) . '</td>';
$sdbiselisih_undef = $sdbi_budst[''] - $sdbi_realst[''];
$tab .= '<td align=right>' . number_format($sdbiselisih_undef, 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left><b>Total Mill Maintenance Cost</b></td>';
$total_bi_real = $total_bi_realst + $bi_realst[''];
$total_bi_bgt = $total_bi_budst + $bi_budst[''];
$total_bi_selisih = $total_bi_selisih + $biselisih_undef;
$total_sdbi_real = $total_sdbi_realst + $sdbi_realst[''];
$total_sdbi_bgt = $total_sdbi_budst + $sdbi_budst[''];
$total_sdbi_selisih = $total_sdbi_selisih + $sdbiselisih_undef;
$tab .= '<td align=right><b>' . number_format($total_bi_real, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_bi_bgt, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_bi_selisih, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_sdbi_real, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_sdbi_bgt, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_sdbi_selisih, 0) . '</b></td>';
$tab .= '</b></tr>';
$tab .= '</table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'BiayaPerawatan' . $dte;

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
			echo '<script language=javascript1.2>' . "\r\n" . '                    window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                    </script>';
		}

		closedir($handle);
	}

	break;

case 'pdf':
	if (($kdOrg == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $judul;
			global $kdOrg;
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
			$this->Cell($width, $height, $judul, NULL, 0, 'C', 1);
			$this->Ln();
			$this->Cell($width / 2, $height, $_SESSION['lang']['organisasi'] . ' : ' . $kdOrg . ' ', NULL, 0, 'L', 1);
			$this->Cell($width / 2, $height, $_SESSION['lang']['periode'] . ' : ' . $periode . ' ', NULL, 0, 'R', 1);
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

	if (!empty($kodeorg)) {
		foreach ($kodeorg as $lst_station) {
			$biselisih_st = $bi_budst[$lst_station] - $bi_realst[$lst_station];
			$sdbiselisih_st = $sdbi_budst[$lst_station] - $sdbi_realst[$lst_station];
			$pdf->Cell(($wkiri / 100) * $width, $height, $station[$lst_station], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($bi_realst[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($bi_budst[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($biselisih_st, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbi_realst[$lst_station]), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbi_budst[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbiselisih_st, 0), 1, 0, 'R', 1);
			$pdf->Ln();
		}
	}

	$pdf->Cell(($wkiri / 100) * $width, $height, 'Undefined Station', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($bi_realst[''], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($bi_budst[''], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($biselisih_undef, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbi_realst[''], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbi_budst[''], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbiselisih_undef, 0), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Total Mill Maintenance Cost', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_bi_real, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_bi_bgt, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_bi_selisih, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_sdbi_real, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_sdbi_bgt, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_sdbi_selisih, 0), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Output();
	break;
}

?>
