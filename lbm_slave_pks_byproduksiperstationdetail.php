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

$_GET['subbagian'] == '' ? $kdOrg = $_POST['subbagian'] : $kdOrg = $_GET['subbagian'];
$_GET['periode'] == '' ? $periode = $_POST['periode'] : $periode = $_GET['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_GET['noakun'] == '' ? $noakun = $_POST['noakun'] : $noakun = $_GET['noakun'];
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
$srealisasiBln = 'select sum(jumlah) as jumlah,noakun as station from ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '              where tanggal like \'' . $periode . '%\' and (kodeblok like \'' . $kdOrg . '%\' OR kodevhc like \'' . $kdOrg . '%\') and left(noakun,3) in (\'631\',\'632\')' . "\r\n" . '              group by noakun order by noakun asc ';

#exit(mysql_error($conn));
($qRealisasiBln = mysql_query($srealisasiBln)) || true;

while ($rRealisasiBln = mysql_fetch_assoc($qRealisasiBln)) {
	$byStation[$rRealisasiBln['station']] = $rRealisasiBln['jumlah'];
}

$srealisasiBlnSbi = 'select sum(jumlah) as jumlah,noakun as station from ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '                   where tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') ' . "\r\n" . '                   and (kodeblok like \'' . $kdOrg . '%\' OR kodevhc like \'' . $kdOrg . '%\') and left(noakun,3) in (\'631\',\'632\')' . "\r\n" . '                   group by noakun order by noakun asc ';

#exit(mysql_error($conn));
($qRealisasiBlnSbi = mysql_query($srealisasiBlnSbi)) || true;

while ($rRealisasiBlnSbi = mysql_fetch_assoc($qRealisasiBlnSbi)) {
	$byStationSbi[$rRealisasiBlnSbi['station']] = $rRealisasiBlnSbi['jumlah'];
}

$sBgt = 'select distinct sum(rp' . $bulan . ') as budgetProd,noakun as station from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '       where left(noakun,3) in (\'631\',\'632\') and kodeorg like \'' . $kdOrg . '%\' and  tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '       group by noakun order by noakun asc';

#exit(mysql_error($conn));
($qBgt = mysql_query($sBgt)) || true;

while ($rBgt = mysql_fetch_assoc($qBgt)) {
	$byBgt[$rBgt['station']] = $rBgt['budgetProd'];
}

$sBgtSbi = 'select distinct sum(' . $fld_st5 . ') as budgetProd,noakun as station from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '       where left(noakun,3) in (\'631\',\'632\') and kodeorg like \'' . $kdOrg . '%\' and  tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '       group by noakun order by noakun asc';

#exit(mysql_error($conn));
($qBgtSbi = mysql_query($sBgtSbi)) || true;

while ($rBgtSbi = mysql_fetch_assoc($qBgtSbi)) {
	$byBgtSbi[$rBgtSbi['station']] = $rBgtSbi['budgetProd'];
}

$s_station = 'select noakun,namaakun from ' . $dbname . '.keu_5akun' . "\r\n" . '             where left(noakun,3) in (\'631\',\'632\') and char_length(noakun)!=3';

#exit(mysql_error($conn));
($q_station = mysql_query($s_station)) || true;

while ($r_station = mysql_fetch_assoc($q_station)) {
	$kodeorg[] = $r_station['noakun'];
	$station[$r_station['noakun']] = $r_station['namaakun'];
}

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=7 align=center><b>' . $_GET['judul'] . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=3 align=left><b>' . $_SESSION['lang']['organisasi'] . ' : ' . $kdOrg . '</b></td>' . "\r\n" . '        <td colspan=4 align=right><b>' . $_SESSION['lang']['periode'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=7 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}
else {
	$brdr = 0;
}

if (($proses == 'getDetail') || ($proses == 'getExccel')) {
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
	$tab .= '<td rowspan=2>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td rowspan=2>' . $_SESSION['lang']['namaakun'] . '</td>';
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
				$derclick = ' style=cursor:pointer; onclick=getDetail2(\'' . $kdOrg . '\',\'' . $periode . '\',\'' . $lst_station . '\',\'lbm_slave_pks_byproduksiperstationdetail\')';
			}

			$tab .= '<tr class=rowcontent ' . $derclick . '>';
			$tab .= '<td>' . $lst_station . '</td>';
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

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=left colspan=2><b>Total Mill Production Cost</b></td>';
	$total_bi_real = $total_bi_realst;
	$total_bi_bgt = $total_bi_budst;
	$total_bi_selisih = $total_bi_selisih;
	$total_rp_per_kg = $total_rp_bi;
	$total_sdbi_real = $total_sdbi_realst;
	$total_sdbi_bgt = $total_sdbi_budst;
	$total_sdbi_selisih = $total_sdbi_selisih;
	$total_rp_per_kgsbi = $total_rp_sbi;
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

	if ($proses == 'getDetail') {
		$arr = '##subbagian##periode';
		$tab .= "\r\n" . '                <input type=hidden id=subbagian value=\'' . $kdOrg . '\' />' . "\r\n" . '                <input type=hidden id=periode value=\'' . $periode . '\' />' . "\r\n" . '                <button style=cursor:pointer; onclick=getBack1() class=mybutton>' . $_SESSION['lang']['back'] . '</button>' . "\r\n" . '                <button onclick="zExcel(event,\'lbm_slave_pks_byproduksiperstationdetail.php\',\'' . $arr . '\',\'getExccel\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>';
	}
}

switch ($proses) {
case 'getDetail':
	echo $tab;
	break;

case 'getExccel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'BiayaProduksiDetailStasiun' . $dte;

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

case 'getDetail2':
	$sreal = 'select distinct sum(jumlah) as rupiah,kodebarang,kodekegiatan,keterangan,nojurnal' . "\r\n" . '                    from ' . $dbname . '.keu_jurnaldt_vw where left(tanggal,7)=\'' . $periode . '\'' . "\r\n" . '                    and noakun=\'' . $noakun . '\' and (kodeblok like \'' . $kdOrg . '%\' OR kodevhc like \'' . $kdOrg . '%\')  ' . "\r\n" . '                     group by nojurnal';

	#exit(mysql_error($conn));
	($qReal = mysql_query($sreal)) || true;

	while ($rreal = mysql_fetch_assoc($qReal)) {
		$dtNojurnal[$rreal['nojurnal']] = $rreal['nojurnal'];
		$dtKegiatan[$rreal['nojurnal']] = $rreal['kodekegiatan'];
		$dtBrg[$rreal['nojurnal']] = $rreal['kodebarang'];
		$dtRupiah[$rreal['nojurnal']] = $rreal['rupiah'];
		$dtKet[$rreal['nojurnal']] = $rreal['keterangan'];
	}

	$brd = 0;
	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tab .= '<thead><tr>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nojurnal'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['keterangan'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['kodekegiatan'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['namakegiatan'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['rp'] . ' BI</td>';
	$tab .= '</tr></thead><tbody>';

	if (empty($dtNojurnal)) {
		$tab .= '<tr class=rowcontent>' . "\r\n" . '                       <td colspan=11>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}
	else {
		foreach ($dtNojurnal as $lstJurnal) {
			$tab .= '<tr class=rowcontent>' . "\r\n" . '                                  <td>' . $lstJurnal . '</td>';
			$tab .= '<td>' . $dtKet[$lstJurnal] . '</td>';
			$tab .= '<td>' . $dtBrg[$lstJurnal] . '</td>';
			$tab .= '<td>' . $optNmBrg[$dtBrg[$lstJurnal]] . '</td>';
			$tab .= '<td>' . $dtKegiatan[$lstJurnal] . '</td>';
			$tab .= '<td>' . $optNmKeg[$dtKegiatan[$lstJurnal]] . '</td>';
			$tab .= '<td align=right>' . number_format($dtRupiah[$lstJurnal], 2) . '</td></tr>';
			$sbtot += $dtRupiah[$lstJurnal];
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td colspan=6 align=right>' . $_SESSION['lang']['total'] . '</td>';
		$tab .= '<td align=right>' . number_format($sbtot, 2) . '</td></tr>';
		$tab .= '</tbody></table>';
	}

	$arr = '##subbagian##periode##noakun';
	$tab .= "\r\n" . '                    <input type=hidden id=noakun value=\'' . $noakun . '\' />' . "\r\n" . '                    <button style=cursor:pointer;  class=mybutton onclick=getBack2()>' . $_SESSION['lang']['back'] . '</button>' . "\r\n" . '                    <button onclick="zExcel(event,\'lbm_slave_pks_byproduksiperstationdetail.php\',\'' . $arr . '\',\'getExccel2\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>';
	echo $tab;
	break;

case 'getExccel2':
	$sreal = 'select distinct sum(jumlah) as rupiah,kodebarang,kodekegiatan,keterangan,nojurnal' . "\r\n" . '                    from ' . $dbname . '.keu_jurnaldt_vw where left(tanggal,7)=\'' . $periode . '\'' . "\r\n" . '                    and noakun=\'' . $noakun . '\' and kodeblok like \'' . $kdOrg . '%\'  ' . "\r\n" . '                     group by nojurnal';

	#exit(mysql_error($conn));
	($qReal = mysql_query($sreal)) || true;

	while ($rreal = mysql_fetch_assoc($qReal)) {
		$dtNojurnal[$rreal['nojurnal']] = $rreal['nojurnal'];
		$dtKegiatan[$rreal['nojurnal']] = $rreal['kodekegiatan'];
		$dtBrg[$rreal['nojurnal']] = $rreal['kodebarang'];
		$dtRupiah[$rreal['nojurnal']] = $rreal['rupiah'];
		$dtKet[$rreal['nojurnal']] = $rreal['keterangan'];
	}

	$brd = 1;
	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brd . ' class=sortable>';
	$tab .= '<thead><tr>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nojurnal'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['keterangan'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['kodekegiatan'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['namakegiatan'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['rp'] . ' BI</td>';
	$tab .= '</tr></thead><tbody>';

	if (empty($dtNojurnal)) {
		$tab .= '<tr class=rowcontent>' . "\r\n" . '                       <td colspan=11>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}
	else {
		foreach ($dtNojurnal as $lstJurnal) {
			$tab .= '<tr class=rowcontent>' . "\r\n" . '                                  <td>' . $lstJurnal . '</td>';
			$tab .= '<td>' . $dtKet[$lstJurnal] . '</td>';
			$tab .= '<td>' . $dtBrg[$lstJurnal] . '</td>';
			$tab .= '<td>' . $optNmBrg[$dtBrg[$lstJurnal]] . '</td>';
			$tab .= '<td>' . $dtKegiatan[$lstJurnal] . '</td>';
			$tab .= '<td>' . $optNmKeg[$dtKegiatan[$lstJurnal]] . '</td>';
			$tab .= '<td align=right>' . number_format($dtRupiah[$lstJurnal], 2) . '</td></tr>';
			$sbtot += $dtRupiah[$lstJurnal];
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td colspan=6 align=right>' . $_SESSION['lang']['total'] . '</td>';
		$tab .= '<td align=right>' . number_format($sbtot, 2) . '</td></tr>';
		$tab .= '</tbody></table>';
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'BiayaProductionDetail2' . $dte;

	if (0 < strlen($tab)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $tab);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                </script>';
	}

	break;
}

?>
