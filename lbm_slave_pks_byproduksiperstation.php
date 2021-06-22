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
	$fld_st = 'rp0' . $thn[1];
}
else {
	$fld_st = 'rp' . $thn[1];
}

$asr5 = 1;

while ($asr5 <= $thn[1]) {
	if (strlen($asr5) < 2) {
		if ($asr5 == 1) {
			$fld_st5 = 'rp0' . $asr5;
		}
		else {
			$fld_st5 .= '+rp0' . $asr5;
		}
	}
	else {
		$fld_st5 .= '+rp' . $asr5;
	}

	++$asr5;
}

$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$sJmlhCpo = 'select sum(cpo_produksi) as jmlhcpo,sum(kernel_produksi) as jmlhkernel from ' . $dbname . '.pabrik_produksi' . "\r\n" . '           where kodeorg=\'' . substr($kdOrg, 0, 4) . '\' and left(tanggal,7)=\'' . $periode . '\'';

#exit(mysql_error($conn));
($qJmlhCpo = mysql_query($sJmlhCpo)) || true;
$rJmlhCpo = mysql_fetch_assoc($qJmlhCpo);
$sJmlhCpoSbi = 'select sum(cpo_produksi) as jmlhcposbi,sum(kernel_produksi) as jmlhkernelsbi from ' . $dbname . '.pabrik_produksi' . "\r\n" . '           where kodeorg=\'' . substr($kdOrg, 0, 4) . '\' and tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')';

#exit(mysql_error($conn));
($qJmlhCpoSbi = mysql_query($sJmlhCpoSbi)) || true;
$rJmlhCpoSbi = mysql_fetch_assoc($qJmlhCpoSbi);
$srealisasiBln = 'select sum(jumlah) as jumlah,if(kodeblok=\'\',left(kodevhc,6),left(kodeblok,6)) as station from ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '              where tanggal like \'' . $periode . '%\' and (kodeblok like \'' . $kdOrg . '%\' OR kodevhc like \'' . $kdOrg . '%\') and left(noakun,3) in (\'631\',\'632\')' . "\r\n" . '              group by left(kodeblok,6) order by left(kodeblok,6) asc ';

#exit(mysql_error($conn));
($qRealisasiBln = mysql_query($srealisasiBln)) || true;

while ($rRealisasiBln = mysql_fetch_assoc($qRealisasiBln)) {
	if ($rRealisasiBln['station'] == '') {
		$stasiundt = 'undefined';
		$byStation[$stasiundt] = $rRealisasiBln['jumlah'];
	}
	else {
		$byStation[$rRealisasiBln['station']] = $rRealisasiBln['jumlah'];
	}
}

$srealisasiBlnSbi = 'select sum(jumlah) as jumlah, if(kodeblok=\'\',left(kodevhc,6),left(kodeblok,6)) as station from ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '                   where tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') ' . "\r\n" . '                  and (kodeblok like \'' . $kdOrg . '%\' OR kodevhc like \'' . $kdOrg . '%\') and left(noakun,3) in (\'631\',\'632\')' . "\r\n" . '                   group by left(kodeblok,6) order by left(kodeblok,6) asc ';

#exit(mysql_error($conn));
($qRealisasiBlnSbi = mysql_query($srealisasiBlnSbi)) || true;

while ($rRealisasiBlnSbi = mysql_fetch_assoc($qRealisasiBlnSbi)) {
	if ($rRealisasiBlnSbi['station'] == '') {
		$stasiundt = 'undefined';
		$byStationSbi[$stasiundt] = $rRealisasiBlnSbi['jumlah'];
	}
	else {
		$byStationSbi[$rRealisasiBlnSbi['station']] = $rRealisasiBlnSbi['jumlah'];
	}
}

$sBgt = 'select distinct sum(rp' . $bulan . ') as budgetProd,left(kodeorg,6) as station from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '       where left(noakun,3) in (\'631\',\'632\') and kodeorg like \'' . $kdOrg . '%\' and  tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '       group by left(kodeorg,6) order by left(kodeorg,6) asc';

#exit(mysql_error($conn));
($qBgt = mysql_query($sBgt)) || true;

while ($rBgt = mysql_fetch_assoc($qBgt)) {
	$byBgt[$rBgt['station']] = $rBgt['budgetProd'];
}

$sBgtSbi = 'select distinct sum(' . $fld_st5 . ') as budgetProd,left(kodeorg,6) as station from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '       where left(noakun,3) in (\'631\',\'632\') and kodeorg like \'' . $kdOrg . '%\' and  tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '       group by left(kodeorg,6) order by left(kodeorg,6) asc';

#exit(mysql_error($conn));
($qBgtSbi = mysql_query($sBgtSbi)) || true;

while ($rBgtSbi = mysql_fetch_assoc($qBgtSbi)) {
	$byBgtSbi[$rBgtSbi['station']] = $rBgtSbi['budgetProd'];
}

$s_station = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '             where tipe=\'STATION\' and kodeorganisasi like \'' . $kdOrg . '%\' ';

#exit(mysql_error($conn));
($q_station = mysql_query($s_station)) || true;

while ($r_station = mysql_fetch_assoc($q_station)) {
	$kodeorg[] = $r_station['kodeorganisasi'];
	$station[$r_station['kodeorganisasi']] = $r_station['namaorganisasi'];
}

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=7 align=center><b>' . $_GET['judul'] . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=3 align=left><b>' . $_SESSION['lang']['organisasi'] . ' : ' . $kdOrg . '</b></td>' . "\r\n" . '        <td colspan=4 align=right><b>' . $_SESSION['lang']['periode'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=7 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}
else {
	$brdr = 0;
}

$stasiundt = 'undefined';
$tab .= '<table><tr><td>';
$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>';
$tab .= '<thead><tr ' . $bgcoloraja . '>';
$tab .= '<td rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>';
$tab .= '<td colspan=2>' . $_SESSION['lang']['jumlahproduksi'] . '</td>';
$tab .= '</tr><tr>';
$tab .= '<td>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['sdbulanini'] . '</td></tr></thead><tbody><tr class=rowcontent>';
$tab .= '<td>' . $optNmBrg[40000001] . '</td>';
$tab .= '<td align=right>' . number_format($rJmlhCpo['jmlhcpo'], 0) . '</td>';
$tab .= '<td align=right>' . number_format($rJmlhCpoSbi['jmlhcposbi'], 0) . '</td></tr>';
$tab .= '<tr class=rowcontent><td>' . $optNmBrg[40000002] . '</td>';
$tab .= '<td align=right>' . number_format($rJmlhCpo['jmlhkernel'], 0) . '</td>';
$tab .= '<td align=right>' . number_format($rJmlhCpoSbi['jmlhkernelsbi'], 0) . '</td></tr>';
$tab .= '<tr><td colspan=2></td></tr>';
$tab .= '</tbody></table></td></tr><tr><td>';
$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader ' . $bgcoloraja . '>';
$tab .= '<tr align=center>';
$tab .= '<td rowspan=2>' . $_SESSION['lang']['uraian'] . '</td>';
$tab .= '<td colspan=4>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td colspan=4>' . $_SESSION['lang']['sdbulanini'] . '</td></tr>';
$tab .= '<tr align=center>' . "\r\n" . '               <td>' . $_SESSION['lang']['realisasi'] . '</td><td>' . $_SESSION['lang']['anggaran'] . '</td><td>' . $_SESSION['lang']['selisih'] . '</td><td>' . $_SESSION['lang']['rpperkg'] . '</td>';
$tab .= '<td>' . $_SESSION['lang']['realisasi'] . '</td><td>' . $_SESSION['lang']['anggaran'] . '</td><td>' . $_SESSION['lang']['selisih'] . '</td><td>' . $_SESSION['lang']['rpperkg'] . '</td>';
$tab .= '</tr></thead>';

if (!empty($kodeorg)) {
	$total_bi_realst = 0;

	foreach ($kodeorg as $lst_station) {
		$derclick = '';
		if (($byStation[$lst_station] != 0) || ($byStation[$lst_station] != '')) {
			$derclick = ' style=cursor:pointer; onclick=getDetail(\'' . $lst_station . '\',\'' . $periode . '\',\'lbm_slave_pks_byproduksiperstationdetail\')';
		}

		$tab .= '<tr class=rowcontent ' . $derclick . '>';
		$tab .= '<td>' . $station[$lst_station] . '</td>';
		$tab .= '<td align=right>' . number_format($byStation[$lst_station], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($byBgt[$lst_station], 0) . '</td>';
		$biselisih_st = $byBgt[$lst_station] - $byStation[$lst_station];
		@$rpperkgbi[$lst_station] = $byStation[$lst_station] / $rJmlhCpo['jmlhcpo'];
		$tab .= '<td align=right>' . number_format($biselisih_st, 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rpperkgbi[$lst_station], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($byStationSbi[$lst_station], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($byBgtSbi[$lst_station], 0) . '</td>';
		$sdbiselisih_st = $byBgtSbi[$lst_station] - $byStationSbi[$lst_station];
		@$rpperkgsbi[$lst_station] = $byStationSbi[$lst_station] / $rJmlhCpoSbi['jmlhcposbi'];
		$tab .= '<td align=right>' . number_format($sdbiselisih_st, 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rpperkgsbi[$lst_station], 0) . '</td>';
		$tab .= '</tr>';
		$total_bi_realst += $byStation[$lst_station];
		$total_bi_budst += $byBgt[$lst_station];
		$total_bi_selisih += $biselisih_st;
		$total_sdbi_realst += $byStationSbi[$lst_station];
		$total_sdbi_budst += $byBgtSbi[$lst_station];
		$total_sdbi_selisih += $sdbiselisih_st;
		$total_rp_bi += $rpperkgbi[$lst_station];
		$total_rp_sbi += $rpperkgsbi[$lst_station];
	}
}

$derclick = '';
if (($byStation[$stasiundt] != 0) || ($byStation[$stasiundt] != '')) {
	$derclick = ' style=cursor:pointer; onclick=getDetail(\'' . $lst_station . '\',\'' . $periode . '\',\'lbm_slave_pks_byproduksiperstationdetail\')';
}

$tab .= '<tr class=rowcontent ' . $derclick . '>';
$tab .= '<td>' . strtoupper($stasiundt) . '</td>';
$tab .= '<td align=right>' . number_format($byStation[$stasiundt], 0) . '</td>';
$tab .= '<td align=right>' . number_format($byBgt[$stasiundt], 0) . '</td>';
$biselisih_st = $byBgt[$stasiundt] - $byStation[$stasiundt];
@$rpperkgbi[$stasiundt] = $byStation[$stasiundt] / $rJmlhCpo['jmlhcpo'];
$tab .= '<td align=right>' . number_format($biselisih_st, 0) . '</td>';
$tab .= '<td align=right>' . number_format($rpperkgbi[$stasiundt], 0) . '</td>';
$tab .= '<td align=right>' . number_format($byStationSbi[$stasiundt], 0) . '</td>';
$tab .= '<td align=right>' . number_format($byBgtSbi[$stasiundt], 0) . '</td>';
$sdbiselisih_st = $byBgtSbi[$stasiundt] - $byStationSbi[$stasiundt];
@$rpperkgsbi[$stasiundt] = $byStationSbi[$stasiundt] / $rJmlhCpoSbi['jmlhcposbi'];
$tab .= '<td align=right>' . number_format($sdbiselisih_st, 0) . '</td>';
$tab .= '<td align=right>' . number_format($rpperkgsbi[$stasiundt], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left><b>Total Mill Production Cost</b></td>';
$total_bi_real = $total_bi_realst + $byStation[$stasiundt];
$total_bi_bgt = $total_bi_budst + $byBgt[$stasiundt];
$total_bi_selisih = $total_bi_selisih + $biselisih_st;
$total_rp_per_kg = $total_rp_bi + $rpperkgbi[$stasiundt];
$total_sdbi_real = $total_sdbi_realst + $byStationSbi[$stasiundt];
$total_sdbi_bgt = $total_sdbi_budst + $byBgtSbi[$stasiundt];
$total_sdbi_selisih = $total_sdbi_selisih + $sdbiselisih_st;
$total_rp_per_kgsbi = $total_rp_sbi + $rpperkgsbi[$stasiundt];
$tab .= '<td align=right><b>' . number_format($total_bi_real, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_bi_bgt, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_bi_selisih, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_rp_per_kg, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_sdbi_real, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_sdbi_bgt, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_sdbi_selisih, 0) . '</b></td>';
$tab .= '<td align=right><b>' . number_format($total_rp_per_kgsbi, 0) . '</b></td>';
$tab .= '</b></tr>';
$tab .= '</table></td></tr></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'BiayaProduksi' . $dte;

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
			global $stasiundt;
			global $rJmlhCpo;
			global $rJmlhCpoSbi;
			global $optNmBrg;
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
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['namabarang'], TRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['jumlahproduksi'], TRL, 0, 'C', 1);
			$this->Ln();
			$this->SetFont('Arial', '', 8);
			$this->Cell(($wkiri / 100) * $width, $height, '', RL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Ln();
			$this->SetFillColor(255, 255, 255);
			$this->Cell(($wkiri / 100) * $width, $height, $optNmBrg[40000001], TRL, 0, 'L', 1);
			$this->Cell(($wlain / 100) * $width, $height, number_format($rJmlhCpo['jmlhcpo'], 0), 1, 0, 'R', 1);
			$this->Cell(($wlain / 100) * $width, $height, number_format($rJmlhCpoSbi['jmlhcposbi'], 0), 1, 0, 'R', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, $optNmBrg[40000002], TBRL, 0, 'L', 1);
			$this->Cell(($wlain / 100) * $width, $height, number_format($rJmlhCpo['jmlhkernel'], 0), 1, 0, 'R', 1);
			$this->Cell(($wlain / 100) * $width, $height, number_format($rJmlhCpoSbi['jmlhkernelsbi'], 0), 1, 0, 'R', 1);
			$this->Ln();
			$this->Ln();
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['uraian'], TRL, 0, 'C', 1);
			$this->Cell(((($wlain * 3) + 5.5) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(((($wlain * 3) + 5.5) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 0, 'C', 1);
			$this->Cell((5.5 / 100) * $width, $height, $_SESSION['lang']['rpperkg'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 0, 'C', 1);
			$this->Cell((5.5 / 100) * $width, $height, $_SESSION['lang']['rpperkg'], 1, 0, 'C', 1);
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
	$wkiri = 22.5;
	$wlain = 11;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 9);
	$no = 1;

	if (!empty($kodeorg)) {
		foreach ($kodeorg as $lst_station) {
			$biselisih_st = $byBgt[$lst_station] - $byStation[$lst_station];
			@$rpperkgbi[$lst_station] = $byStation[$lst_station] / $rJmlhCpo['jmlhcpo'];
			$sdbiselisih_st = $byBgtSbi[$lst_station] - $byStationSbi[$lst_station];
			@$rpperkgsbi[$lst_station] = $byStationSbi[$lst_station] / $rJmlhCpoSbi['jmlhcposbi'];
			$pdf->Cell(($wkiri / 100) * $width, $height, $station[$lst_station], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($byStation[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($byBgt[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($biselisih_st, 0), 1, 0, 'R', 1);
			$pdf->Cell((5.5 / 100) * $width, $height, number_format($rpperkgbi[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($byStationSbi[$lst_station]), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($byBgtSbi[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbiselisih_st, 0), 1, 0, 'R', 1);
			$pdf->Cell((5.5 / 100) * $width, $height, number_format($rpperkgsbi[$lst_station], 0), 1, 0, 'R', 1);
			$pdf->Ln();
		}
	}

	$biselisih_st = $byBgt[$stasiundt] - $byStation[$stasiundt];
	@$rpperkgbi[$stasiundt] = $byStation[$stasiundt] / $rJmlhCpo['jmlhcpo'];
	$sdbiselisih_st = $byBgtSbi[$stasiundt] - $byStationSbi[$stasiundt];
	@$rpperkgsbi[$stasiundt] = $byStationSbi[$stasiundt] / $rJmlhCpoSbi['jmlhcposbi'];
	$pdf->Cell(($wkiri / 100) * $width, $height, strtoupper($stasiundt), 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($byStation[$stasiundt], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($byBgt[$stasiundt], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($biselisih_st, 0), 1, 0, 'R', 1);
	$pdf->Cell((5.5 / 100) * $width, $height, number_format($rpperkgbi[$stasiundt], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($byStationSbi[$stasiundt], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($byBgtSbi[$stasiundt], 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($sdbiselisih_st, 0), 1, 0, 'R', 1);
	$pdf->Cell((5.5 / 100) * $width, $height, number_format($rpperkgsbi[$stasiundt], 0), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Total Mill Prodcution Cost', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_bi_real, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_bi_bgt, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_bi_selisih, 0), 1, 0, 'R', 1);
	$pdf->Cell((5.5 / 100) * $width, $height, number_format($total_rp_per_kg, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_sdbi_real, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_sdbi_bgt, 0), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total_sdbi_selisih, 0), 1, 0, 'R', 1);
	$pdf->Cell((5.5 / 100) * $width, $height, number_format($total_rp_per_kgsbi, 0), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Output();
	break;
}

?>
