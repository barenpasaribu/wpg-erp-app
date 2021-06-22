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
$_GET['noakun'] == '' ? $noakun = $_POST['noakun'] : $noakun = $_GET['noakun'];
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

$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$bgcoloraja = 'bgcolor=#DEDEDE align=center';

switch ($proses) {
case 'getDetail':
	$sdgaji = 'select noakun,sum(jumlah) as realst from ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '            where noakun like \'632%\' and left(kodeblok,6)=\'' . $kdOrg . '\' ' . "\r\n" . '            and left(tanggal,7)=\'' . $periode . '\'  ' . "\r\n" . '            group by noakun asc';

	#exit(mysql_error($conn));
	($qdgaji = mysql_query($sdgaji)) || true;

	while ($rdgaji = mysql_fetch_assoc($qdgaji)) {
		$dtAkun[$rdgaji['noakun']] = $rdgaji['noakun'];
		$dtRealis[$rdgaji['noakun']] = $rdgaji['realst'];
	}

	$sdgajisbi = 'select noakun,sum(jumlah) as realst from ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '            where noakun like \'632%\' and kodeblok like \'' . $kdOrg . '%\' ' . "\r\n" . '            and left(tanggal,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'' . "\r\n" . '            group by noakun asc';

	#exit(mysql_error($conn));
	($qdgajisbi = mysql_query($sdgajisbi)) || true;

	while ($rdgajisbi = mysql_fetch_assoc($qdgajisbi)) {
		$dtAkun[$rdgajisbi['noakun']] = $rdgajisbi['noakun'];
		$dtRealissbi[$rdgajisbi['noakun']] = $rdgajisbi['realst'];
	}

	$s_budstbi = 'select noakun,sum(rp' . $bulan . ') as budget_st from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '            where noakun like \'632%\' and kodeorg like \'' . $kdOrg . '%\' ' . "\r\n" . '            and tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '            group by noakun';

	#exit(mysql_error($conn));
	($q_budstbi = mysql_query($s_budstbi)) || true;

	while ($r_budstbi = mysql_fetch_assoc($q_budstbi)) {
		$dtAkun[$r_budstbi['noakun']] = $r_budstbi['noakun'];
		$bi_budst[$r_budstbi['noakun']] = $r_budstbi['budget_st'];
	}

	$s_budstsdbi = 'select noakun,sum(' . $fld_st5 . ') as bgt_st from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '              where noakun like \'632%\' and kodeorg like \'' . $kdOrg . '%\' and tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '              group by noakun';

	#exit(mysql_error($conn));
	($q_budstsdbi = mysql_query($s_budstsdbi)) || true;

	while ($r_budstsdbi = mysql_fetch_assoc($q_budstsdbi)) {
		$dtAkun[$r_budstsdbi['noakun']] = $r_budstsdbi['noakun'];
		$sdbi_budst[$r_budstsdbi['noakun']] = $r_budstsdbi['bgt_st'];
	}

	$optNmakun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
	$brd = 0;
	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brd . ' class=sortable>';
	$tab .= '<thead><tr>';
	$tab .= '<td rowspan=2>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td rowspan=2>' . $_SESSION['lang']['namaakun'] . '</td>';
	$tab .= '<td colspan=3>BI</td>';
	$tab .= '<td colspan=3>S.D BI</td>';
	$tab .= '</tr><tr>';
	$tab .= '<td>' . $_SESSION['lang']['realisasi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['budget'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['selisih'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['realisasi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['budget'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['selisih'] . '</td>' . "\r\n" . '            </tr></thead><tbody>';
	$totalReal = 0;
	$totalBudget = 0;
	$totalRealSi = 0;
	$totalBudgetSbi = 0;

	foreach ($dtAkun as $lstNoakun) {
		$derclick = ' style=cursor:pointer; onclick=getDetail2(\'' . $kdOrg . '\',\'' . $periode . '\',\'' . $lstNoakun . '\',\'lbm_slave_pks_byperawatandetail\')';
		$tab .= '<tr class=rowcontent ' . $derclick . '>';
		$tab .= '<td>' . $lstNoakun . '</td>';
		$tab .= '<td>' . $optNmakun[$lstNoakun] . '</td>';
		$slisih[$lstNoakun] = $dtRealis[$lstNoakun] - $bi_budst[$lstNoakun];
		$slisihSbi[$lstNoakun] = $dtRealissbi[$lstNoakun] - $sdbi_budst[$lstNoakun];
		$tab .= '<td align=right>' . number_format($dtRealis[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($bi_budst[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($slisih[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRealissbi[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($sdbi_budst[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($slisihSbi[$lstNoakun], 2) . '</td>';
		$tab .= '</tr>';
		$totalReal += $dtRealis[$lstNoakun];
		$totalBudget += $bi_budst[$lstNoakun];
		$totalRealSi += $dtRealissbi[$lstNoakun];
		$totalBudgetSbi += $sdbi_budst[$lstNoakun];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
	$slisihbi = $totalReal - $totalBudget;
	$slisihSbiini = $totalRealSi - $totalBudgetSbi;
	$tab .= '<td align=right>' . number_format($totalReal, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totalBudget, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($slisihbi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totalRealSi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totalBudgetSbi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($slisihSbiini, 2) . '</td>';
	$tab .= '</tr>';
	$arr = '##subbagian##periode';
	$tab .= '</tbody></table>' . "\r\n" . '                <input type=hidden id=subbagian value=\'' . $kdOrg . '\' />' . "\r\n" . '                <input type=hidden id=periode value=\'' . $periode . '\' />' . "\r\n" . '                <button style=cursor:pointer; onclick=getBack1() class=mybutton>' . $_SESSION['lang']['back'] . '</button>' . "\r\n" . '                <button onclick="zExcel(event,\'lbm_slave_pks_byperawatandetail.php\',\'' . $arr . '\',\'getExccel\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>';
	echo $tab;
	break;

case 'getExccel':
	$sdgaji = 'select noakun,sum(jumlah) as realst from ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '            where noakun like \'632%\' and left(kodeblok,6)=\'' . $kdOrg . '\' ' . "\r\n" . '            and left(tanggal,7)=\'' . $periode . '\'  ' . "\r\n" . '            group by noakun asc';

	#exit(mysql_error($conn));
	($qdgaji = mysql_query($sdgaji)) || true;

	while ($rdgaji = mysql_fetch_assoc($qdgaji)) {
		$dtAkun[$rdgaji['noakun']] = $rdgaji['noakun'];
		$dtRealis[$rdgaji['noakun']] = $rdgaji['realst'];
	}

	$sdgajisbi = 'select noakun,sum(jumlah) as realst from ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '            where noakun like \'632%\' and kodeblok like \'' . $kdOrg . '%\' ' . "\r\n" . '            and left(tanggal,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'' . "\r\n" . '            group by noakun asc';

	#exit(mysql_error($conn));
	($qdgajisbi = mysql_query($sdgajisbi)) || true;

	while ($rdgajisbi = mysql_fetch_assoc($qdgajisbi)) {
		$dtAkun[$rdgajisbi['noakun']] = $rdgajisbi['noakun'];
		$dtRealissbi[$rdgajisbi['noakun']] = $rdgajisbi['realst'];
	}

	$s_budstbi = 'select noakun,sum(rp' . $bulan . ') as budget_st from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '            where noakun like \'632%\' and kodeorg like \'' . $kdOrg . '%\' ' . "\r\n" . '            and tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '            group by noakun';

	#exit(mysql_error($conn));
	($q_budstbi = mysql_query($s_budstbi)) || true;

	while ($r_budstbi = mysql_fetch_assoc($q_budstbi)) {
		$dtAkun[$r_budstbi['noakun']] = $r_budstbi['noakun'];
		$bi_budst[$r_budstbi['noakun']] = $r_budstbi['budget_st'];
	}

	$s_budstsdbi = 'select noakun,sum(' . $fld_st5 . ') as bgt_st from ' . $dbname . '.bgt_budget_detail' . "\r\n" . '              where noakun like \'632%\' and kodeorg like \'' . $kdOrg . '%\' and tahunbudget=\'' . $thn[0] . '\' ' . "\r\n" . '              group by noakun';

	#exit(mysql_error($conn));
	($q_budstsdbi = mysql_query($s_budstsdbi)) || true;

	while ($r_budstsdbi = mysql_fetch_assoc($q_budstsdbi)) {
		$dtAkun[$r_budstsdbi['noakun']] = $r_budstsdbi['noakun'];
		$sdbi_budst[$r_budstsdbi['noakun']] = $r_budstsdbi['bgt_st'];
	}

	$optNmakun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
	$brd = 0;
	$tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable>';
	$tab .= '<thead><tr>';
	$tab .= '<td rowspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td rowspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['namaakun'] . '</td>';
	$tab .= '<td colspan=3 ' . $bgcoloraja . '>BI</td>';
	$tab .= '<td colspan=3 ' . $bgcoloraja . '>S.D BI</td>';
	$tab .= '</tr><tr>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['budget'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['selisih'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['budget'] . '</td>' . "\r\n" . '            <td ' . $bgcoloraja . '>' . $_SESSION['lang']['selisih'] . '</td>' . "\r\n" . '            </tr></thead><tbody>';
	$totalReal = 0;
	$totalBudget = 0;
	$totalRealSi = 0;
	$totalBudgetSbi = 0;

	foreach ($dtAkun as $lstNoakun) {
		$derclick = ' style=cursor:pointer; onclick=getDetail2(\'' . $kdOrg . '\',\'' . $periode . '\',\'' . $lstNoakun . '\',\'lbm_slave_pks_byperawatandetail\')';
		$tab .= '<tr class=rowcontent ' . $derclick . '>';
		$tab .= '<td>' . $lstNoakun . '</td>';
		$tab .= '<td>' . $optNmakun[$lstNoakun] . '</td>';
		$slisih[$lstNoakun] = $dtRealis[$lstNoakun] - $bi_budst[$lstNoakun];
		$slisihSbi[$lstNoakun] = $dtRealissbi[$lstNoakun] - $sdbi_budst[$lstNoakun];
		$tab .= '<td align=right>' . number_format($dtRealis[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($bi_budst[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($slisih[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRealissbi[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($sdbi_budst[$lstNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($slisihSbi[$lstNoakun], 2) . '</td>';
		$tab .= '</tr>';
		$totalReal += $dtRealis[$lstNoakun];
		$totalBudget += $bi_budst[$lstNoakun];
		$totalRealSi += $dtRealissbi[$lstNoakun];
		$totalBudgetSbi += $sdbi_budst[$lstNoakun];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
	$slisihbi = $totalReal - $totalBudget;
	$slisihSbiini = $totalRealSi - $totalBudgetSbi;
	$tab .= '<td align=right>' . number_format($totalReal, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totalBudget, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($slisihbi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totalRealSi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totalBudgetSbi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($slisihSbiini, 2) . '</td>';
	$tab .= '</tr>';
	$tab .= '</tbody></table>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'BiayaPerawatanDetail' . $dte;

	if (0 < strlen($tab)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $tab);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                </script>';
	}

	break;

case 'getDetail2':
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
	$tab .= "\r\n" . '                    <input type=hidden id=noakun value=\'' . $noakun . '\' />' . "\r\n" . '                    <button style=cursor:pointer;  class=mybutton onclick=getBack2()>' . $_SESSION['lang']['back'] . '</button>' . "\r\n" . '                    <button onclick="zExcel(event,\'lbm_slave_pks_byperawatandetail.php\',\'' . $arr . '\',\'getExccel2\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>';
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
	$nop_ = 'BiayaPerawatanDetail2' . $dte;

	if (0 < strlen($tab)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $tab);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                </script>';
	}

	break;
}

?>
