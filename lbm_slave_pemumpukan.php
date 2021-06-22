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

$sKlmpk = 'select kode,kelompok from ' . $dbname . '.log_5klbarang order by kode';

#exit(mysql_error());
($qKlmpk = mysql_query($sKlmpk)) || true;

while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
	$rKelompok[$rKlmpk['kode']] = $rKlmpk['kelompok'];
}

$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKelompok = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$thn = explode('-', $periode);
$bln = intval($thn[1]);

if (strlen($bln) < 2) {
	if ($thn[1] == '1') {
		$blnLalu = 12;
		$thnLalu = $thn[0] - 1;
	}
	else {
		$blnLalu = '0' . $bln;
	}
}
else {
	$blnLalu = $bln - 1;
}

$thnLalu = $thn[0] - 1;
$sGetBrg = 'select distinct kodebarang from ' . $dbname . '.bgt_lbm_material_vw ' . "\r\n" . '          where tahunbudget=\'' . $thn[0] . '\' and substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(kodebarang,1,3)=\'311\' order by kodebarang asc';

if ($afdId != '') {
	$sGetBrg = 'select distinct kodebarang from ' . $dbname . '.bgt_lbm_material_vw ' . "\r\n" . '          where tahunbudget=\'' . $thn[0] . '\' and substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(kodebarang,1,3)=\'311\' order by kodebarang asc';
}

#exit(mysql_error());
($qGetBrg = mysql_query($sGetBrg)) || true;

while ($rGetBrg = mysql_fetch_assoc($qGetBrg)) {
	$dtBarang[] = $rGetBrg['kodebarang'];
}

$sThTnm = 'select distinct tahuntanam from ' . $dbname . '.setup_blok where statusblok=\'TM\' and substr(kodeorg,1,4)=\'' . $kdUnit . '\' order by tahuntanam asc';

if ($afdId != '') {
	$sThTnm = 'select distinct tahuntanam from ' . $dbname . '.setup_blok where statusblok=\'TM\' and substr(kodeorg,1,6)=\'' . $afdId . '\' order by tahuntanam asc';
}

#exit(mysql_error());
($qThTnm = mysql_query($sThTnm)) || true;

while ($rThTnm = mysql_fetch_assoc($qThTnm)) {
	$thnTanamTm[] = $rThTnm['tahuntanam'];
}

$sThTnm = 'select distinct tahuntanam from ' . $dbname . '.setup_blok where statusblok=\'TBM\' and substr(kodeorg,1,4)=\'' . $kdUnit . '\' order by tahuntanam asc';

if ($afdId != '') {
	$sThTnm = 'select distinct tahuntanam from ' . $dbname . '.setup_blok where statusblok=\'TBM\' and substr(kodeorg,1,6)=\'' . $afdId . '\' order by tahuntanam asc';
}

#exit(mysql_error());
($qThTnm = mysql_query($sThTnm)) || true;

while ($rThTnm = mysql_fetch_assoc($qThTnm)) {
	$thnTanamTbm[] = $rThTnm['tahuntanam'];
}

$sThTnm = 'select distinct tahuntanam from ' . $dbname . '.setup_blok where statusblok=\'TB\' and substr(kodeorg,1,4)=\'' . $kdUnit . '\' order by tahuntanam asc';

if ($afdId != '') {
	$sThTnm = 'select distinct tahuntanam from ' . $dbname . '.setup_blok where statusblok=\'TB\' and substr(kodeorg,1,6)=\'' . $afdId . '\' order by tahuntanam asc';
}

#exit(mysql_error());
($qThTnm = mysql_query($sThTnm)) || true;

while ($rThTnm = mysql_fetch_assoc($qThTnm)) {
	$thnTanamTb[] = $rThTnm['tahuntanam'];
}

$sThnTnm = 'select distinct tahuntanam,sum(luasareaproduktif) as luas,sum(jumlah) as jumlah,kodebarang from ' . $dbname . '.setup_blok  a ' . "\r\n" . '    left join ' . $dbname . '. bgt_lbm_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,4)=\'' . $kdUnit . '\' and statusblok=\'TM\' and tahunbudget=\'' . $thn[0] . '\' and substr(kodebarang,1,3)=\'311\' ' . "\r\n" . '    group by tahuntanam,kodebarang order by a.tahuntanam asc';

if ($afdId != '') {
	$sThnTnm = 'select distinct tahuntanam,sum(luasareaproduktif) as luas,sum(jumlah) as jumlah,kodebarang from ' . $dbname . '.setup_blok  a ' . "\r\n" . '    left join ' . $dbname . '. bgt_lbm_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,6)=\'' . $afdId . '\' and statusblok=\'TM\' and tahunbudget=\'' . $thn[0] . '\' and substr(kodebarang,1,3)=\'311\' ' . "\r\n" . '    group by tahuntanam,kodebarang order by a.tahuntanam asc';
}

#exit(mysql_error());
($qThnTnm = mysql_query($sThnTnm)) || true;

while ($rThnTnm = mysql_fetch_assoc($qThnTnm)) {
	$dtBarangTm[$rThnTnm['kodebarang']] = $rThnTnm['kodebarang'];
	$lsProduktifTm[$rThnTnm['tahuntanam']] = $rThnTnm['luas'];
	$jmTm[$rThnTnm['tahuntanam']][$rThnTnm['kodebarang']] = $rThnTnm['jumlah'];
}

$sRealisasi = 'select distinct kodebarang,sum(kwantitas) as jumlah,tahuntanam from ' . $dbname . '.kebun_pakai_material_vw a' . "\r\n" . '             left join ' . $dbname . '.setup_blok b on a.kodeorg=b.kodeorg where ' . "\r\n" . '             a.kodeorg like \'' . $kdUnit . '%\' and notransaksi like \'%TM%\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'' . "\r\n" . '             and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by tahuntanam asc';

if ($afdId != '') {
	$sRealisasi = 'select distinct kodebarang,sum(kwantitas) as jumlah,tahuntanam from ' . $dbname . '.kebun_pakai_material_vw a' . "\r\n" . '             left join ' . $dbname . '.setup_blok b on a.kodeorg=b.kodeorg where ' . "\r\n" . '             a.kodeorg like \'' . $kdUnit . '%\' and notransaksi like \'%TM%\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\'' . "\r\n" . '             and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by tahuntanam asc';
}

#exit(mysql_error());
($qRealisasi = mysql_query($sRealisasi)) || true;

while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$jmTmRi[$rRealisasi['tahuntanam']][$rRealisasi['kodebarang']] = $rRealisasi['jumlah'];
}

$sThnTnm = 'select distinct tahuntanam,sum(luasareaproduktif) as luas,sum(jumlah) as jumlah,kodebarang from ' . $dbname . '.setup_blok  a ' . "\r\n" . '    left join ' . $dbname . '. bgt_lbm_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,4)=\'' . $kdUnit . '\' and statusblok=\'TBM\' and tahunbudget=\'' . $thn[0] . '\' and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang  order by a.kodeorg asc';

if ($afdId != '') {
	$sThnTnm = 'select distinct tahuntanam,sum(luasareaproduktif) as luas,sum(jumlah) as jumlah,kodebarang from ' . $dbname . '.setup_blok  a ' . "\r\n" . '    left join ' . $dbname . '. bgt_lbm_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,6)=\'' . $afdId . '\' and statusblok=\'TBM\' and tahunbudget=\'' . $thn[0] . '\' and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang  order by a.kodeorg asc';
}

#exit(mysql_error());
($qThnTnm = mysql_query($sThnTnm)) || true;

while ($rThnTnm = mysql_fetch_assoc($qThnTnm)) {
	$dtBarangTbm[$rThnTnm['kodebarang']] = $rThnTnm['kodebarang'];
	$lsProduktifTbm += $rThnTnm['tahuntanam'];
	$jmTbm[$rThnTnm['tahuntanam']] += $rThnTnm['kodebarang'];
}

$sRealisasi = 'select distinct tahuntanam,kodebarang,sum(kwantitas) as jumlah from ' . $dbname . '.setup_blok a  ' . "\r\n" . '    left join ' . $dbname . '.kebun_pakai_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,4)=\'' . $kdUnit . '\' and statusblok=\'TBM\' and substr(notransaksi,15,3)=\'TBM\' and (substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\') and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by a.tahuntanam asc';

#exit(mysql_error());
($qRealisasi = mysql_query($sRealisasi)) || true;

while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$jmTbmRi[$rRealisasi['tahuntanam']] += $rRealisasi['kodebarang'];
}

$sThnTnm = 'select distinct tahuntanam,sum(luasareaproduktif) as luas,sum(jumlah) as jumlah,kodebarang from ' . $dbname . '.setup_blok  a ' . "\r\n" . '    left join ' . $dbname . '. bgt_lbm_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,4)=\'' . $kdUnit . '\' and statusblok=\'TB\' and tahunbudget=\'' . $thn[0] . '\' and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by a.kodeorg asc';

if ($afdId != '') {
	$sThnTnm = 'select distinct tahuntanam,sum(luasareaproduktif) as luas,sum(jumlah) as jumlah,kodebarang from ' . $dbname . '.setup_blok  a ' . "\r\n" . '    left join ' . $dbname . '. bgt_lbm_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,6)=\'' . $afdId . '\' and statusblok=\'TB\' and tahunbudget=\'' . $thn[0] . '\' and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by a.kodeorg asc';
}

#exit(mysql_error());
($qThnTnm = mysql_query($sThnTnm)) || true;

while ($rThnTnm = mysql_fetch_assoc($qThnTnm)) {
	$dtBarangTb[$rThnTnm['kodebarang']] = $rThnTnm['kodebarang'];
	$lsProduktifTb[$rThnTnm['tahuntanam']] = $rThnTnm['luas'];
	$jmTb[$rThnTnm['tahuntanam']] += $rThnTnm['kodebarang'];
}

$sRealisasi = 'select distinct tahuntanam,kodebarang,sum(kwantitas) as jumlah from ' . $dbname . '.setup_blok a  ' . "\r\n" . '    left join ' . $dbname . '.kebun_pakai_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,4)=\'' . $kdUnit . '\' and statusblok=\'TBM\' and substr(notransaksi,15,2)=\'TB\' and (substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\') and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by a.tahuntanam asc';

if ($afdId != '') {
	$sRealisasi = 'select distinct tahuntanam,kodebarang,sum(kwantitas) as jumlah from ' . $dbname . '.setup_blok a  ' . "\r\n" . '    left join ' . $dbname . '.kebun_pakai_material_vw b on a.kodeorg=b.kodeorg' . "\r\n" . '    where substr(a.kodeorg,1,6)=\'' . $afdId . '\' and statusblok=\'TBM\' and substr(notransaksi,15,2)=\'TB\' and (substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\') and substr(kodebarang,1,3)=\'311\' group by tahuntanam,kodebarang order by a.tahuntanam asc';
}

#exit(mysql_error());
($qRealisasi = mysql_query($sRealisasi)) || true;

while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$jmTbRi[$rRealisasi['tahuntanam']] += $rRealisasi['kodebarang'];
}

$varCek = count($dtBarang);

if ($varCek < 1) {
	exit('Error: No data found');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dtBarang);
$colsTbm = count($dtBarangTbm);
$colsTb = count($dtBarangTb);
$da = $cols * 3;
$daTblm = $colsTbm * 3;
$daTb = $colsTb * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>13. ' . strtoupper($_SESSION['lang']['pemupukan']) . '</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=5 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= strtoupper($_SESSION['lang']['TM']) . ' ( TM )<br/><table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n" . '        <td rowspan=2>' . $_SESSION['lang']['luas'] . ' HA</td>' . "\r\n" . '        ';

if ($cols != 0) {
	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$tab .= '<td colspan=3>' . $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg] . '</td>';
		}
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=' . $da . '>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}

$tab .= '</tr><tr>';
$awr = 1;

while ($awr <= $cols) {
	$tab .= '<td>' . $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'] . '</td><td>' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'] . '</td><td>' . $_SESSION['lang']['sisa'] . '</td>';
	++$awr;
}

$tab .= '</tr></thead>' . "\r\n\t" . '<tbody>';
$thnTnmCekTM = count($thnTanamTm);

if ($thnTnmCekTM != 0) {
	if (!empty($thnTanamTm)) {
		foreach ($thnTanamTm as $dtThnTM) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $dtThnTM . '</td>';
			$tab .= '<td align=right>' . number_format($lsProduktifTm[$dtThnTM], 2) . '</td>';

			if (!empty($dtBarang)) {
				foreach ($dtBarang as $lstKdBrg) {
					$sisa[$dtThnTM][$lstKdBrg] = $jmTm[$dtThnTM][$lstKdBrg] - $jmTmRi[$dtThnTM][$lstKdBrg];
					$tab .= '<td align=right>' . number_format($jmTm[$dtThnTM][$lstKdBrg], 0) . '</td>';
					$tab .= '<td  align=right>' . number_format($jmTmRi[$dtThnTM][$lstKdBrg], 0) . '</td>';
					$tab .= '<td   align=right>' . number_format($sisa[$dtThnTM][$lstKdBrg], 0) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=' . $da . '>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}

$tab .= '</tbody></table><br />';
$tab .= strtoupper($_SESSION['lang']['tbm']) . ' ( TBM )<br/><table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n" . '            <thead class=rowheader>' . "\r\n" . '            <tr>' . "\r\n" . '            <td rowspan=2>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n" . '            <td rowspan=2>' . $_SESSION['lang']['luas'] . ' HA</td>' . "\r\n" . '            ';
$cekBrgTbm = count($dtBarangTbm);

if ($cekBrgTbm != 0) {
	if (!empty($dtBarangTbm)) {
		foreach ($dtBarangTbm as $lstKdBrg) {
			$tab .= '<td colspan=3>' . $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg] . '</td>';
		}
	}
}
else {
	$tab .= '<td colspan=' . $daTblm . '>' . $_SESSION['lang']['dataempty'] . '</td>';
}

$tab .= '</tr><tr>';
$awr = 1;

while ($awr <= $colsTbm) {
	$tab .= '<td>' . $_SESSION['lang']['anggran'] . ' 1 ' . $_SESSION['lang']['tahun'] . '</td><td>' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'] . '</td><td>' . $_SESSION['lang']['sisa'] . '</td>';
	++$awr;
}

$tab .= '</tr></thead>' . "\r\n" . '            <tbody>';
$thnTnmCekTBM = count($thnTanamTbm);

if ($thnTnmCekTBM != 0) {
	if (!empty($thnTanamTbm)) {
		foreach ($thnTanamTbm as $dtThnTbM) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $dtThnTbM . '</td>';
			$tab .= '<td align=right>' . number_format($lsProduktifTbm[$dtThnTbM], 2) . '</td>';

			if (!empty($dtBarangTbm)) {
				foreach ($dtBarangTbm as $lstKdBrg) {
					$sisaTbm[$dtThnTbM][$lstKdBrg] = $jmTbm[$dtThnTbM][$lstKdBrg] - $jmTbmRi[$dtThnTbM][$lstKdBrg];
					$tab .= '<td align=right>' . number_format($jmTbm[$dtThnTbM][$lstKdBrg], 0) . '</td>';
					$tab .= '<td align=right>' . number_format($jmTbmRi[$dtThnTbM][$lstKdBrg], 0) . '</td>';
					$tab .= '<td align=right>' . number_format($sisaTbm[$dtThnTbM][$lstKdBrg], 0) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=' . $daTblm . '>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}

$tab .= '</tbody></table><br />';
$tab .= strtoupper($_SESSION['lang']['tb']) . ' ( TB )<br/><table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n" . '                <thead class=rowheader>' . "\r\n" . '                <tr>' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['luas'] . ' HA</td>' . "\r\n" . '                ';

if ($colsTb != 0) {
	if (!empty($dtBarangTb)) {
		foreach ($dtBarangTb as $lstKdBrg) {
			$tab .= '<td colspan=3>' . $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg] . '</td>';
		}
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=' . $daTb . '>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}

$tab .= '</tr><tr>';
$awr = 1;

while ($awr <= $colsTb) {
	$tab .= '<td>' . $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'] . '</td><td>' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['realisasi'] . '</td><td>' . $_SESSION['lang']['sisa'] . '</td>';
	++$awr;
}

$tab .= '</tr></thead>' . "\r\n" . '                <tbody>';
$thnTnmCekTB = count($thnTanamTb);

if ($thnTnmCekTB != 0) {
	if (!empty($thnTanamTb)) {
		foreach ($thnTanamTb as $dtThnTb) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $dtThnTb . '</td>';
			$tab .= '<td align=right>' . number_format($lsProduktifTb[$dtThnTb], 2) . '</td>';

			if (!empty($dtBarangTb)) {
				foreach ($dtBarangTb as $lstKdBrg) {
					$sisaTb[$dtThnTb][$lstKdBrg] = $jmTb[$dtThnTb][$lstKdBrg] - $jmTbRi[$dtThnTb][$lstKdBrg];
					$tab .= '<td align=right>' . number_format($jmTb[$dtThnTb][$lstKdBrg], 0) . '</td>';
					$tab .= '<td align=right>' . number_format($jmTbRi[$dtThnTb][$lstKdBrg], 0) . '</td>';
					$tab .= '<td align=right>' . number_format($sisaTb[$dtThnTb][$lstKdBrg], 0) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=' . $daTb . '>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'distribusi_pupuk_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $dtBarang;
			global $cols;
			global $lstKodeOrg9;
			global $lstKodeOrg8;
			global $optSatuan;
			global $optNmBrg;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('13. ' . strtoupper($_SESSION['lang']['pemupukan'])), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');

			if ($afdId != '') {
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId], 0, 1, 'L');
			}

			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$this->ln(15);
			$wit = $cols * 25;
			$witda = $wit * 2;
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$wit = $cols * 25;
	$witda = ($cols * $wit) + 90;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Cell(245, $height, strtoupper($_SESSION['lang']['TM']) . ' ( TM )', 0, 1, 'L', 1);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->SetFont('Arial', 'B', 5);
	$pdf->Cell(45, $height, $_SESSION['lang']['tahun'], TLR, 0, 'C', 1);
	$pdf->Cell(45, $height, $_SESSION['lang']['luas'], TLR, 0, 'C', 1);

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$ard += 1;

			if ($ard != $cols) {
				$pdf->Cell($wit, $height, $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg], TLR, 0, 'C', 1);
			}
			else {
				$pdf->Cell($wit, $height, $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg], TLR, 1, 'C', 1);
			}
		}
	}

	$pdf->Cell(45, $height, $_SESSION['lang']['tanam'], BLR, 0, 'C', 1);
	$pdf->Cell(45, $height, '(HA)', BLR, 0, 'C', 1);

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$ard5 += 1;

			if ($ard5 != $cols) {
				$pdf->Cell(45, $height, $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'], TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
				$pdf->Cell(35, $height, $_SESSION['lang']['sisa'], TBLR, 0, 'C', 1);
			}
			else {
				$pdf->Cell(45, $height, $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'], TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
				$pdf->Cell(35, $height, $_SESSION['lang']['sisa'], TBLR, 1, 'C', 1);
			}
		}
	}

	$pdf->SetFillColor(255, 255, 255);
	$thnTnmCekTM = count($thnTanamTm);
	$dtCoba = $cols * 3;

	if ($thnTnmCekTM != 0) {
		if (!empty($thnTanamTm)) {
			foreach ($thnTanamTm as $dtThnTM) {
				$ard6 = 0;
				$pdf->Cell(45, $height, $dtThnTM, TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, number_format($lsProduktifTm[$dtThnTM], 2), TBLR, 0, 'C', 1);

				if (!empty($dtBarang)) {
					foreach ($dtBarang as $lstKdBrg2) {
						$sisa[$dtThnTM][$lstKdBrg2] = $jmTm[$dtThnTM][$lstKdBrg2] - $jmTmRi[$dtThnTM][$lstKdBrg2];
						$ard6 += 1;

						if ($ard6 != $cols) {
							$pdf->Cell(45, $height, number_format($jmTm[$dtThnTM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(45, $height, number_format($jmTmRi[$dtThnTM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(35, $height, number_format($sisa[$dtThnTM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
						}
						else {
							$pdf->Cell(45, $height, number_format($jmTm[$dtThnTM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(45, $height, number_format($jmTmRi[$dtThnTM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(35, $height, number_format($sisa[$dtThnTM][$lstKdBrg2], 0), TBLR, 1, 'R', 1);
						}
					}
				}
			}
		}
	}
	else {
		$pdf->Cell($witda, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
	}

	$samping = $pdf->GetX();
	$tinggi = $pdf->GetY();
	$pdf->SetX($samping);
	$pdf->SetY($tinggi + 10);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Cell(245, $height, strtoupper($_SESSION['lang']['tbm']) . ' ( TBM )', 0, 1, 'L', 1);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->SetFont('Arial', 'B', 5);
	$pdf->Cell(45, $height, $_SESSION['lang']['tahun'], TLR, 0, 'C', 1);
	$pdf->Cell(45, $height, $_SESSION['lang']['luas'], TLR, 0, 'C', 1);
	$ard = 0;

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$ard += 1;

			if ($ard != $cols) {
				$pdf->Cell($wit, $height, $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg], TLR, 0, 'C', 1);
			}
			else {
				$pdf->Cell($wit, $height, $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg], TLR, 1, 'C', 1);
			}
		}
	}

	$pdf->Cell(45, $height, $_SESSION['lang']['tanam'], BLR, 0, 'C', 1);
	$pdf->Cell(45, $height, '(HA)', BLR, 0, 'C', 1);
	$ard5 = 0;

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$ard5 += 1;

			if ($ard5 != $cols) {
				$pdf->Cell(45, $height, $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'], TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
				$pdf->Cell(35, $height, 'Sisa', TBLR, 0, 'C', 1);
			}
			else {
				$pdf->Cell(45, $height, $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'], TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
				$pdf->Cell(35, $height, 'Sisa', TBLR, 1, 'C', 1);
			}
		}
	}

	$pdf->SetFillColor(255, 255, 255);
	$thnTnmCekTBM = count($thnTanamTbm);

	if ($thnTnmCekTBM != 0) {
		if (!empty($thnTanamTbm)) {
			foreach ($thnTanamTbm as $dtThnTbM) {
				$ard6 = 0;
				$pdf->Cell(45, $height, $dtThnTbM, TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, number_format($lsProduktifTbm[$dtThnTbM], 2), TBLR, 0, 'C', 1);

				if (!empty($dtBarang)) {
					foreach ($dtBarang as $lstKdBrg2) {
						$sisaTbm[$dtThnTbM][$lstKdBrg2] = $jmTbm[$dtThnTbM][$lstKdBrg2] - $jmTbmRi[$dtThnTbM][$lstKdBrg2];
						$ard6 += 1;

						if ($ard6 != $cols) {
							$pdf->Cell(45, $height, number_format($jmTbm[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(45, $height, number_format($jmTbmRi[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(35, $height, number_format($sisaTbm[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
						}
						else {
							$pdf->Cell(45, $height, number_format($jmTbm[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(45, $height, number_format($jmTbmRi[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(35, $height, number_format($sisaTbm[$dtThnTbM][$lstKdBrg2], 0), TBLR, 1, 'R', 1);
						}
					}
				}
			}
		}
	}
	else {
		$pdf->Cell($witda, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
	}

	$samping = $pdf->GetX();
	$tinggi = $pdf->GetY();
	$pdf->SetX($samping);
	$pdf->SetY($tinggi + 10);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Cell(245, $height, strtoupper($_SESSION['lang']['tb']) . ' ( TB )', 0, 1, 'L', 1);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->SetFont('Arial', 'B', 5);
	$pdf->Cell(45, $height, $_SESSION['lang']['tahun'], TLR, 0, 'C', 1);
	$pdf->Cell(45, $height, $_SESSION['lang']['luas'], TLR, 0, 'C', 1);
	$ard = 0;

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$ard += 1;

			if ($ard != $cols) {
				$pdf->Cell($wit, $height, $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg], TLR, 0, 'C', 1);
			}
			else {
				$pdf->Cell($wit, $height, $optNmBrg[$lstKdBrg] . ' (' . $lstKdBrg . ')-' . $optSatuan[$lstKdBrg], TLR, 1, 'C', 1);
			}
		}
	}

	$pdf->Cell(45, $height, $_SESSION['lang']['tanam'], BLR, 0, 'C', 1);
	$pdf->Cell(45, $height, '(HA)', BLR, 0, 'C', 1);
	$ard5 = 0;

	if (!empty($dtBarang)) {
		foreach ($dtBarang as $lstKdBrg) {
			$ard5 += 1;

			if ($ard5 != $cols) {
				$pdf->Cell(45, $height, $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'], TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
				$pdf->Cell(35, $height, $_SESSION['lang']['sisa'], TBLR, 0, 'C', 1);
			}
			else {
				$pdf->Cell(45, $height, $_SESSION['lang']['anggaran'] . ' 1 ' . $_SESSION['lang']['tahun'], TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
				$pdf->Cell(35, $height, $_SESSION['lang']['sisa'], TBLR, 1, 'C', 1);
			}
		}
	}

	$pdf->SetFillColor(255, 255, 255);
	$thnTnmCekTB = count($thnTanamTb);

	if ($thnTnmCekTB != 0) {
		if (!empty($thnTanamTb)) {
			foreach ($thnTanamTb as $dtThnTb) {
				$ard6 = 0;
				$pdf->Cell(45, $height, $dtThnTb, TBLR, 0, 'C', 1);
				$pdf->Cell(45, $height, number_format($lsProduktifTbm[$dtThnTb], 2), TBLR, 0, 'C', 1);

				if (!empty($dtBarang)) {
					foreach ($dtBarang as $lstKdBrg2) {
						$sisaTb[$dtThnTb][$lstKdBrg2] = $jmTb[$dtThnTb][$lstKdBrg2] - $jmTbRi[$dtThnTb][$lstKdBrg2];
						$ard6 += 1;

						if ($ard6 != $cols) {
							$pdf->Cell(45, $height, number_format($jmTb[$dtThnTb][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(45, $height, number_format($jmTbRi[$dtThnTb][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(35, $height, number_format($sisaTb[$dtThnTb][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
						}
						else {
							$pdf->Cell(45, $height, number_format($jmTb[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(45, $height, number_format($jmTbRi[$dtThnTbM][$lstKdBrg2], 0), TBLR, 0, 'R', 1);
							$pdf->Cell(35, $height, number_format($sisaTb[$dtThnTbM][$lstKdBrg2], 0), TBLR, 1, 'R', 1);
						}
					}
				}
			}
		}
	}
	else {
		$pdf->Cell($witda, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
	}

	$pdf->Output();
	break;
}

?>
