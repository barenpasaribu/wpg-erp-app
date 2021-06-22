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
$aresta = 'SELECT noakun,sum(jumlah) as biaya FROM ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\' and ' . "\r\n" . '        (noakun like \'7%\' or noakun like \'63%\' or noakun like \'64%\' or noakun like \'631%\' or noakun like \'632%\' or noakun like \'8%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	if (substr($res['noakun'], 0, 1) == '7') {
		$akun7[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '63') {
		$akun63[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '64') {
		$akun64[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '631') {
		$akun631[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '632') {
		$akun632[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 1) == '8') {
		$akun8[$res['noakun']] = $res['noakun'];
	}

	$dzArr[$res['noakun']]['biaya'] = $res['biaya'];
}

$aresta = 'SELECT noakun,sum(rp' . $bulan . ') as budget FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\' and ' . "\r\n" . '        (noakun like \'7%\' or noakun like \'63%\' or noakun like \'64%\' or noakun like \'631%\' or noakun like \'632%\' or noakun like \'8%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	if (substr($res['noakun'], 0, 1) == '7') {
		$akun7[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '63') {
		$akun63[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '64') {
		$akun64[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '631') {
		$akun631[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '632') {
		$akun632[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 1) == '8') {
		$akun8[$res['noakun']] = $res['noakun'];
	}

	$dzArr[$res['noakun']]['budget'] = $res['budget'];
}

$aresta = 'SELECT noakun,sum(jumlah) as biaya FROM ' . $dbname . '.keu_jurnaldt_vw' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') and ' . "\r\n" . '        (noakun like \'7%\' or noakun like \'63%\' or noakun like \'64%\' or noakun like \'631%\' or noakun like \'632%\' or noakun like \'8%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	if (substr($res['noakun'], 0, 1) == '7') {
		$akun7[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '63') {
		$akun63[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '64') {
		$akun64[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '631') {
		$akun631[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '632') {
		$akun632[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 1) == '8') {
		$akun8[$res['noakun']] = $res['noakun'];
	}

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
$aresta = 'SELECT noakun,sum(' . $addstr . ') as budget FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\' and ' . "\r\n" . '        (noakun like \'7%\' or noakun like \'63%\' or noakun like \'64%\' or noakun like \'631%\' or noakun like \'632%\' or noakun like \'8%\')' . "\r\n" . '    GROUP BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	if (substr($res['noakun'], 0, 1) == '7') {
		$akun7[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '63') {
		$akun63[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 2) == '64') {
		$akun64[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '631') {
		$akun631[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 3) == '632') {
		$akun632[$res['noakun']] = $res['noakun'];
	}

	if (substr($res['noakun'], 0, 1) == '8') {
		$akun8[$res['noakun']] = $res['noakun'];
	}

	$dzArr[$res['noakun']]['budgetsd'] = $res['budget'];
}

$aresta = 'SELECT noakun, namaakun FROM ' . $dbname . '.keu_5akun' . "\r\n" . '    WHERE length(noakun)=7 and ' . "\r\n" . '        (noakun like \'7%\' or noakun like \'63%\' or noakun like \'64%\' or noakun like \'631%\' or noakun like \'632%\' or noakun like \'8%\')' . "\r\n" . '    ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kamusakun[$res['noakun']]['no'] = $res['noakun'];
	$kamusakun[$res['noakun']]['nama'] = $res['namaakun'];
}

if (!empty($akun7)) {
	foreach ($akun7 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total7 += 'biaya';
		$total7 += 'budget';
		$total7 += 'selisih';
		$total7 += 'biayasd';
		$total7 += 'budgetsd';
		$total7 += 'selisihsd';
		$subtotal += 'biaya';
		$subtotal += 'budget';
		$subtotal += 'selisih';
		$subtotal += 'biayasd';
		$subtotal += 'budgetsd';
		$subtotal += 'selisihsd';
	}
}

if (!empty($akun63)) {
	foreach ($akun63 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total6364 += 'biaya';
		$total6364 += 'budget';
		$total6364 += 'selisih';
		$total6364 += 'biayasd';
		$total6364 += 'budgetsd';
		$total6364 += 'selisihsd';
		$subtotal += 'biaya';
		$subtotal += 'budget';
		$subtotal += 'selisih';
		$subtotal += 'biayasd';
		$subtotal += 'budgetsd';
		$subtotal += 'selisihsd';
	}
}

if (!empty($akun64)) {
	foreach ($akun64 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total6364 += 'biaya';
		$total6364 += 'budget';
		$total6364 += 'selisih';
		$total6364 += 'biayasd';
		$total6364 += 'budgetsd';
		$total6364 += 'selisihsd';
		$subtotal += 'biaya';
		$subtotal += 'budget';
		$subtotal += 'selisih';
		$subtotal += 'biayasd';
		$subtotal += 'budgetsd';
		$subtotal += 'selisihsd';
	}
}

if (!empty($akun631)) {
	foreach ($akun631 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total631 += 'biaya';
		$total631 += 'budget';
		$total631 += 'selisih';
		$total631 += 'biayasd';
		$total631 += 'budgetsd';
		$total631 += 'selisihsd';
	}
}

if (!empty($akun632)) {
	foreach ($akun632 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total632 += 'biaya';
		$total632 += 'budget';
		$total632 += 'selisih';
		$total632 += 'biayasd';
		$total632 += 'budgetsd';
		$total632 += 'selisihsd';
	}
}

if (!empty($akun64)) {
	foreach ($akun64 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total64 += 'biaya';
		$total64 += 'budget';
		$total64 += 'selisih';
		$total64 += 'biayasd';
		$total64 += 'budgetsd';
		$total64 += 'selisihsd';
	}
}

$total['biaya'] = $subtotal['biaya'];
$total['budget'] = $subtotal['budget'];
$total['selisih'] = $subtotal['selisih'];
$total['biayasd'] = $subtotal['biayasd'];
$total['budgetsd'] = $subtotal['budgetsd'];
$total['selisihsd'] = $subtotal['selisihsd'];

if (!empty($akun8)) {
	foreach ($akun8 as $akyun) {
		$dzArr[$akyun]['selisih'] = $dzArr[$akyun]['budget'] - $dzArr[$akyun]['biaya'];
		$dzArr[$akyun]['selisihsd'] = $dzArr[$akyun]['budgetsd'] - $dzArr[$akyun]['biayasd'];
		$total8 += 'biaya';
		$total8 += 'budget';
		$total8 += 'selisih';
		$total8 += 'biayasd';
		$total8 += 'budgetsd';
		$total8 += 'selisihsd';
		$total += 'biaya';
		$total += 'budget';
		$total += 'selisih';
		$total += 'biayasd';
		$total += 'budgetsd';
		$total += 'selisihsd';
	}
}

@$costpertbs['biaya'] = $total['biaya'] / $tbs;
@$costpertbs['budget'] = $total['budget'] / $tbsbudget;
@$costpertbs['selisih'] = $costpertbs['budget'] - $costpertbs['biaya'];
@$costpertbs['biayasd'] = $total['biayasd'] / $tbssd;
@$costpertbs['budgetsd'] = $total['budgetsd'] / $tbsbudgetsd;
@$costpertbs['selisihsd'] = $costpertbs['budgetsd'] - $costpertbs['biayasd'];
@$costpercpo['biaya'] = $total['biaya'] / $cpo;
@$costpercpo['budget'] = $total['budget'] / $cpobudget;
@$costpercpo['selisih'] = $costpercpo['budget'] - $costpercpo['biaya'];
@$costpercpo['biayasd'] = $total['biayasd'] / $cposd;
@$costpercpo['budgetsd'] = $total['budgetsd'] / $cpobudgetsd;
@$costpercpo['selisihsd'] = $costpercpo['budgetsd'] - $costpercpo['biayasd'];
@$admpertbs['biaya'] = $total7['biaya'] / $tbs;
@$admpertbs['budget'] = $total7['budget'] / $tbsbudget;
@$admpertbs['selisih'] = $admpertbs['budget'] - $admpertbs['biaya'];
@$admpertbs['biayasd'] = $total7['biayasd'] / $tbssd;
@$admpertbs['budgetsd'] = $total7['budgetsd'] / $tbsbudgetsd;
@$admpertbs['selisihsd'] = $admpertbs['budgetsd'] - $admpertbs['biayasd'];
@$procpertbs['biaya'] = $total631['biaya'] / $tbs;
@$procpertbs['budget'] = $total631['budget'] / $tbsbudget;
@$procpertbs['selisih'] = $procpertbs['budget'] - $procpertbs['biaya'];
@$procpertbs['biayasd'] = $total631['biayasd'] / $tbssd;
@$procpertbs['budgetsd'] = $total631['budgetsd'] / $tbsbudgetsd;
@$procpertbs['selisihsd'] = $procpertbs['budgetsd'] - $procpertbs['biayasd'];
@$mainpertbs['biaya'] = $total632['biaya'] / $tbs;
@$mainpertbs['budget'] = $total632['budget'] / $tbsbudget;
@$mainpertbs['selisih'] = $mainpertbs['budget'] - $mainpertbs['biaya'];
@$mainpertbs['biayasd'] = $total632['biayasd'] / $tbssd;
@$mainpertbs['budgetsd'] = $total632['budgetsd'] / $tbsbudgetsd;
@$mainpertbs['selisihsd'] = $mainpertbs['budgetsd'] - $mainpertbs['biayasd'];
@$salespertbs['biaya'] = $total8['biaya'] / $tbs;
@$salespertbs['budget'] = $total8['budget'] / $tbsbudget;
@$salespertbs['selisih'] = $salespertbs['budget'] - $salespertbs['biaya'];
@$salespertbs['biayasd'] = $total8['biayasd'] / $tbssd;
@$salespertbs['budgetsd'] = $total8['budgetsd'] / $tbsbudgetsd;
@$salespertbs['selisihsd'] = $salespertbs['budgetsd'] - $salespertbs['biayasd'];
@$psTbsOlah = ($tbs / 1000 / $tbsbudget / 1000) * 100;
@$psTbsOlahSd = ($tbssd / 1000 / $tbsbudgetsd / 1000) * 100;
@$psCpoOlah = ($cpo / 1000 / $cpobudget / 1000) * 100;
@$psCpoOlahSd = ($cposd / 1000 / $cpobudgetsd / 1000) * 100;
@$psTotal7 = ($total7['biaya'] / $total7['budget']) * 100;
@$pstotal7Sd = ($total7['biayasd'] / $total7['budgetsd']) * 100;
@$pstotal6364 = ($total6364['biaya'] / $total6364['budget']) * 100;
@$pstotal6364Sd = ($total6364['biayasd'] / $total6364['budgetsd']) * 100;
@$pstotal631 = ($total631['biaya'] / $total631['budget']) * 100;
@$pstotal631Sd = ($total631['biayasd'] / $total631['budgetsd']) * 100;
@$pstotal632 = ($total632['biaya'] / $total632['budget']) * 100;
@$pstotal632Sd = ($total632['biayasd'] / $total632['budgetsd']) * 100;
@$pstotal64 = ($total64['biaya'] / $total64['budget']) * 100;
@$pstotal64Sd = ($total64['biayasd'] / $total64['budgetsd']) * 100;
@$pssubtotal = ($subtotal['biaya'] / $subtotal['budget']) * 100;
@$pssubtotalSd = ($subtotal['biayasd'] / $subtotal['budgetsd']) * 100;
@$pstotal8 = ($total8['biaya'] / $total8['budget']) * 100;
@$pstotal8Sd = ($subtotal['biayasd'] / $subtotal['budgetsd']) * 100;
@$pstotal = ($total['biaya'] / $total['budget']) * 100;
@$pstotalSd = ($subtotal['biayasd'] / $subtotal['budgetsd']) * 100;
@$pscostpertbs = (($costpertbs['biaya'] * 1000) / ($costpertbs['budget'] * 1000)) * 100;
@$pscostpertbsSd = (($subtotal['biayasd'] * 1000) / ($subtotal['budgetsd'] * 1000)) * 100;
@$pscostpercpo = (($costpercpo['biaya'] * 1000) / ($costpercpo['budget'] * 1000)) * 100;
@$pscostpercpoSd = (($costpercpo['biayasd'] * 1000) / ($costpercpo['budgetsd'] * 1000)) * 100;
@$psadmpertbs = (($admpertbs['biaya'] * 1000) / ($admpertbs['budget'] * 1000)) * 100;
@$psadmpertbsSd = (($admpertbs['biayasd'] * 1000) / ($admpertbs['budgetsd'] * 1000)) * 100;
@$psprocpertbs = (($procpertbs['biaya'] * 1000) / ($procpertbs['budget'] * 1000)) * 100;
@$psprocpertbsSd = (($procpertbs['biayasd'] * 1000) / ($procpertbs['budgetsd'] * 1000)) * 100;
@$psmainpertbs = (($mainpertbs['biaya'] * 1000) / ($mainpertbs['budget'] * 1000)) * 100;
@$psmainpertbsSd = (($mainpertbs['biayasd'] * 1000) / ($mainpertbs['budgetsd'] * 1000)) * 100;
@$pssalespertbs = (($salespertbs['biaya'] * 1000) / ($salespertbs['budget'] * 1000)) * 100;
@$pssalespertbsSd = (($salespertbs['biayasd'] * 1000) / ($salespertbs['budgetsd'] * 1000)) * 100;

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

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>Uraian</td>' . "\r\n" . '    <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center  rowspan=2 ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center colspan=2  ' . $bg . '>' . $_SESSION['lang']['selisih'] . '</td>' . "\r\n" . '    <td align=center rowspan=2  ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center  colspan=2 ' . $bg . '>' . $_SESSION['lang']['selisih'] . '</td>' . "\r\n" . '    </tr>' . "\r\n\t\r\n\t" . '<tr>' . "\r\n\t" . '<td ' . $bg . '>' . $_SESSION['lang']['nilai'] . '</td>' . "\r\n\t" . '<td ' . $bg . '>%</td>' . "\r\n\t" . '<td ' . $bg . '>' . $_SESSION['lang']['nilai'] . '</td>' . "\r\n\t" . '<td ' . $bg . '>%</td>' . "\r\n\t" . '</tr>' . "\r\n\t\r\n\t\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';
$no = 1;
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>' . $_SESSION['lang']['tbsdiolah'] . ' (Ton)</td>';
$tab .= '<td align=right>' . number_format($tbs / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsbudget / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsselisih / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psTbsOlah, 2) . '</td>';
$tab .= '<td align=right>' . number_format($tbssd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsbudgetsd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($tbsselisihsd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psTbsOlahSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>' . $_SESSION['lang']['cpokuantitas'] . ' (Ton)</td>';
$tab .= '<td align=right>' . number_format($cpo / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cpobudget / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cposelisih / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psCpoOlah, 2) . '</td>';
$tab .= '<td align=right>' . number_format($cposd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cpobudgetsd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($cposelisihsd / 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psCpoOlahSd, 2) . '</td>';
$tab .= '</tr><tr><td colspan=7>&nbsp;</td></tr>';
$derclick = ' style=cursor:pointer; onclick=getDetail(\'7###' . $unit . '\',\'' . $periode . '\',\'lbm_slave_pks_rekap_byproduksidet\')';
$tab .= '<tr class=rowcontent ' . $derclick . '>';
$tab .= '<td align=left>Mill Administration</td>';
$tab .= '<td align=right>' . number_format($total7['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total7['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total7['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($psTotal7, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total7['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total7['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total7['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal7Sd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent ' . $derclick2 . '>';
$tab .= '<td align=left>Mill Manufacturing</td>';
$tab .= '<td align=right>' . number_format($total6364['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total6364['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total6364['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal6364, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total6364['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total6364['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total6364['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal6364Sd, 2) . '</td>';
$tab .= '</tr>';
$derclick2 = '';
$derclick2 = ' style=cursor:pointer; onclick=getDetail(\'631###' . $unit . '\',\'' . $periode . '\',\'lbm_slave_pks_rekap_byproduksidet\')';
$tab .= '<tr class=rowcontent ' . $derclick2 . '>';
$tab .= '<td align=left>- Processing</td>';
$tab .= '<td align=right>' . number_format($total631['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total631['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total631['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal631, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total631['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total631['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total631['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal631Sd, 2) . '</td>';
$tab .= '</tr>';
$derclick2 = '';
$derclick2 = ' style=cursor:pointer; onclick=getDetail(\'632###' . $unit . '\',\'' . $periode . '\',\'lbm_slave_pks_rekap_byproduksidet\')';
$tab .= '<tr class=rowcontent ' . $derclick2 . '>';
$tab .= '<td align=left>- Maintenance</td>';
$tab .= '<td align=right>' . number_format($total632['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total632['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total632['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal632, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total632['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total632['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total632['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal632Sd, 2) . '</td>';
$tab .= '</tr>';
$derclick2 = '';
$derclick2 = ' style=cursor:pointer; onclick=getDetail(\'64###' . $unit . '\',\'' . $periode . '\',\'lbm_slave_pks_rekap_byproduksidet\')';
$tab .= '<tr class=rowcontent ' . $derclick2 . '>';
$tab .= '<td align=left>- Bahan Baku</td>';
$tab .= '<td align=right>' . number_format($total64['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total64['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total64['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal64, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total64['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total64['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total64['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal64Sd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=title>';
$tab .= '<td align=left>Sub Total</td>';
$tab .= '<td align=right>' . number_format($subtotal['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($subtotal['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($subtotal['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pssubtotal, 2) . '</td>';
$tab .= '<td align=right>' . number_format($subtotal['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($subtotal['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($subtotal['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pssubtotalSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Sales Expenses</td>';
$tab .= '<td align=right>' . number_format($total8['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total8['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total8['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal8, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total8['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total8['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total8['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal8Sd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=title>';
$tab .= '<td align=left>Total</td>';
$tab .= '<td align=right>' . number_format($total['biaya']) . '</td>';
$tab .= '<td align=right>' . number_format($total['budget']) . '</td>';
$tab .= '<td align=right>' . number_format($total['selisih']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotal, 2) . '</td>';
$tab .= '<td align=right>' . number_format($total['biayasd']) . '</td>';
$tab .= '<td align=right>' . number_format($total['budgetsd']) . '</td>';
$tab .= '<td align=right>' . number_format($total['selisihsd']) . '</td>';
$tab .= '<td align=right>' . number_format($pstotalSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Total Cost / Ton TBS</td>';
$tab .= '<td align=right>' . number_format($costpertbs['biaya'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpertbs['budget'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpertbs['selisih'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($pscostpertbs, 2) . '</td>';
$tab .= '<td align=right>' . number_format($costpertbs['biayasd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpertbs['budgetsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpertbs['selisihsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($pscostpertbsSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Total Cost / Ton CPO-Inti</td>';
$tab .= '<td align=right>' . number_format($costpercpo['biaya'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpercpo['budget'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpercpo['selisih'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($pscostpercpo, 2) . '</td>';
$tab .= '<td align=right>' . number_format($costpercpo['biayasd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpercpo['budgetsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($costpercpo['selisihsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($pscostpercpoSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Mill Adm / Ton TBS</td>';
$tab .= '<td align=right>' . number_format($admpertbs['biaya'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($admpertbs['budget'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($admpertbs['selisih'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psadmpertbs, 2) . '</td>';
$tab .= '<td align=right>' . number_format($admpertbs['biayasd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($admpertbs['budgetsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($admpertbs['selisihsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psadmpertbsSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Mill Proc / Ton TBS</td>';
$tab .= '<td align=right>' . number_format($procpertbs['biaya'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($procpertbs['budget'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($procpertbs['selisih'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psprocpertbs, 2) . '</td>';
$tab .= '<td align=right>' . number_format($procpertbs['biayasd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($procpertbs['budgetsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($procpertbs['selisihsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psprocpertbsSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Mill Maint / Ton TBS</td>';
$tab .= '<td align=right>' . number_format($mainpertbs['biaya'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($mainpertbs['budget'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($mainpertbs['selisih'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psmainpertbs, 2) . '</td>';
$tab .= '<td align=right>' . number_format($mainpertbs['biayasd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($mainpertbs['budgetsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($mainpertbs['selisihsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($psmainpertbsSd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td align=left>Sales Exp / Ton TBS</td>';
$tab .= '<td align=right>' . number_format($salespertbs['biaya'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($salespertbs['budget'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($salespertbs['selisih'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($pssalespertbs, 2) . '</td>';
$tab .= '<td align=right>' . number_format($salespertbs['biayasd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($salespertbs['budgetsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($salespertbs['selisihsd'] * 1000) . '</td>';
$tab .= '<td align=right>' . number_format($pssalespertbsSd, 2) . '</td>';
$tab .= '</tr>';
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
			$this->Cell((($wlain * 3.5) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 3.5) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 1, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 1, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['nilai'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 0.5) / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['nilai'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 0.5) / 100) * $width, $height, $_SESSION['lang']['selisih'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$cols = 247.5;
	$wlain = 11;
	$wkiri = $wlain * 2;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 9);
	$no = 1;
	$psTbsOlah = ($tbs / 1000 / $tbsbudget / 1000) * 100;
	$psTbsOlahSd = ($tbssd / 1000 / $tbsbudgetsd / 1000) * 100;
	$pdf->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['tbsdiolah'] . ' (Ton)', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbs / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsbudget / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsselisih / 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psTbsOlah, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbssd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsbudgetsd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($tbsselisihsd / 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psTbsOlahSd), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['cpokuantitas'] . ' (Ton)', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cpo / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cpobudget / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cposelisih / 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psCpoOlah, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cposd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cpobudgetsd / 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($cposelisihsd / 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psCpoOlahSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell((($wlain * 9) / 100) * $width, $height, '', 1, 0, 'L', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Mill Administration', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total7['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total7['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total7['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psTotal7, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total7['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total7['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total7['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psTotal7Sd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Mill Manufacturing', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total6364['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total6364['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total6364['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal6364, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total6364['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total6364['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total6364['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal6364Sd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, '- Processing', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total631['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total631['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total631['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal631, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total631['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total631['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total631['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal631Sd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, '- Maintenance', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total632['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total632['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total632['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal632, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total632['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total632['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total632['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal632Sd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, '- Bahan Baku', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total64['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total64['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total64['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal64, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total64['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total64['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total64['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal64Sd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Sub Total', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($subtotal['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($subtotal['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($subtotal['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pssubtotal, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($subtotal['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($subtotal['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($subtotal['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pssubtotalSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Sales Expenses', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total8['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total8['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total8['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal8, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total8['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total8['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total8['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal8Sd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Total', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['biaya']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['budget']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['selisih']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotal, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['biayasd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['budgetsd']), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($total['selisihsd']), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pstotalSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Total Cost / Ton TBS', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpertbs['biaya'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpertbs['budget'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpertbs['selisih'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pscostpertbs, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpertbs['biayasd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpertbs['budgetsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpertbs['selisihsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pscostpertbsSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Total Cost / Ton CPO', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpercpo['biaya'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpercpo['budget'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpercpo['selisih'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pscostpercpo, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpercpo['biayasd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpercpo['budgetsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($costpercpo['selisihsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pscostpercpoSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Mill Adm / Ton TBS', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($admpertbs['biaya'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($admpertbs['budget'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($admpertbs['selisih'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psadmpertbs, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($admpertbs['biayasd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($admpertbs['budgetsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($admpertbs['selisihsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psadmpertbsSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Mill Proc / Ton TBS', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($procpertbs['biaya'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($procpertbs['budget'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($procpertbs['selisih'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psprocpertbs, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($procpertbs['biayasd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($procpertbs['budgetsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($procpertbs['selisihsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psprocpertbsSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Mill Maint / Ton TBS', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($mainpertbs['biaya'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($mainpertbs['budget'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($mainpertbs['selisih'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psmainpertbs, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($mainpertbs['biayasd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($mainpertbs['budgetsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($mainpertbs['selisihsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($psmainpertbsSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, 'Sales Exp / Ton TBS', 1, 0, 'L', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($salespertbs['biaya'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($salespertbs['budget'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($salespertbs['selisih'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pssalespertbs, 2), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($salespertbs['biayasd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($salespertbs['budgetsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, number_format($salespertbs['selisihsd'] * 1000), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 0.5) / 100) * $width, $height, number_format($pssalespertbsSd, 2), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Output();
	break;
}

?>
