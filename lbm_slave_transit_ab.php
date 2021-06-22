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

$optNmOrang = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
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
$thnLalu = $thn[0];

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

$sDataH = 'select a.kodevhc, sum(a.jlhbbm) as jlhbbm from ' . $dbname . '.vhc_runht a  ' . "\r\n\t" . ' LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc where' . "\r\n" . '         kodetraksi like \'' . $kdUnit . '%\' and tanggal like \'' . $periode . '%\' group by a.kodevhc';
$sKend = 'select b.jenisvhc,c.namabarang,a.kodevhc,a.tahunperolehan from ' . $dbname . '.vhc_5master a ' . "\r\n" . '        left join ' . $dbname . '.vhc_5jenisvhc b on a.jenisvhc=b.jenisvhc left join ' . "\r\n" . '        ' . $dbname . '.log_5masterbarang c on a.kodebarang=c.kodebarang where a.kodetraksi like \'' . $kdUnit . '%\' ' . "\r\n" . '       and b.jenisvhc in (select distinct jenisvhc from ' . $dbname . '.vhc_5jenisvhc where kelompokvhc=\'AB\')';

#exit(mysql_error());
($qKend = mysql_query($sKend)) || true;

while ($rKend = mysql_fetch_assoc($qKend)) {
	$lsJenis[$rKend['kodevhc']] = $rKend['jenisvhc'];
	$lsNama[$rKend['kodevhc']] = $rKend['namabarang'];
	$lsThnPerolehan[$rKend['kodevhc']] = $rKend['tahunperolehan'];
	$dtKend[] = $rKend['kodevhc'];
}

$sDataE = 'select a.kodevhc,sum(a.jumlahjam) hmsetahun  from ' . $dbname . '.bgt_vhc_jam a ' . "\r\n" . '         left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.tahunbudget=\'' . $thn[0] . '\' group by kodevhc';

#exit(mysql_error());
($qDataE = mysql_query($sDataE)) || true;

while ($rDataE = mysql_fetch_assoc($qDataE)) {
	$lsKm[$rDataE['kodevhc']] = $rDataE['hmsetahun'];
}

$sDataF = 'select b.kodevhc, sum(a.jumlah) as hmkm from ' . $dbname . '.vhc_rundt a ' . "\r\n" . '         left join ' . $dbname . '.vhc_runht c on a.notransaksi=c.notransaksi' . "\r\n" . '         left join ' . $dbname . '.vhc_5master b on b.kodevhc=c.kodevhc' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and c.tanggal like \'' . $periode . '%\' group by b.kodevhc';

#exit(mysql_error());
($qDataF = mysql_query($sDataF)) || true;

while ($rDataF = mysql_fetch_assoc($qDataF)) {
	$dtBi[$rDataF['kodevhc']] = $rDataF['hmkm'];
}

$sDataG = 'select b.kodevhc, sum(a.jumlah) as hmkm from ' . $dbname . '.vhc_rundt a ' . "\r\n" . '         left join ' . $dbname . '.vhc_runht c on a.notransaksi=c.notransaksi' . "\r\n" . '         left join ' . $dbname . '.vhc_5master b on b.kodevhc=c.kodevhc' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and (c.tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) group by b.kodevhc';

#exit(mysql_error());
($qDataG = mysql_query($sDataG)) || true;

while ($rDataG = mysql_fetch_assoc($qDataG)) {
	$dtSbi[$rDataG['kodevhc']] = $rDataG['hmkm'];
}

$sDataH = 'select a.kodevhc, sum(a.jlhbbm) as jlhbbm from ' . $dbname . '.vhc_runht a' . "\r\n\t" . '     LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc where' . "\r\n" . '         kodetraksi like \'' . $kdUnit . '%\' and tanggal like \'' . $periode . '%\' group by a.kodevhc';

#exit(mysql_error());
($qDataH = mysql_query($sDataH)) || true;

while ($rDataH = mysql_fetch_assoc($qDataH)) {
	$jmlhBbm[$rDataH['kodevhc']] = $rDataH['jlhbbm'];
}

$sDataI = 'select a.kodevhc, sum(a.jlhbbm) as jlhbbm from ' . $dbname . '.vhc_runht a ' . "\r\n" . '         LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc ' . "\r\n" . '         where kodetraksi like \'' . $kdUnit . '%\' and (tanggal between \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) group by a.kodevhc';

#exit(mysql_error());
($qDataI = mysql_query($sDataI)) || true;

while ($rDataI = mysql_fetch_assoc($qDataI)) {
	$jmlhBbmSbi[$rDataI['kodevhc']] = $rDataI['jlhbbm'];
}

$sDataK = 'select a.kodevhc, a.rpsetahun/1000 as rp from ' . $dbname . '.bgt_biaya_kend_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.tahunbudget =\'' . $thn[0] . '\' order by kodevhc';

#exit(mysql_error());
($qDataK = mysql_query($sDataK)) || true;

while ($rDataK = mysql_fetch_assoc($qDataK)) {
	$dtAnggrn[$rDataK['kodevhc']] = $rDataK['rp'];
}

$sDataL = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110201\' and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataL = mysql_query($sDataL)) || true;

while ($rDataL = mysql_fetch_assoc($qDataL)) {
	$biGaji[$rDataL['kodevhc']] = $rDataL['gaji'];
}

$sDataM = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110202\'  and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataM = mysql_query($sDataM)) || true;

while ($rDataM = mysql_fetch_assoc($qDataM)) {
	$biLembur[$rDataM['kodevhc']] = $rDataM['gaji'];
}

$sDataN = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110203\'  and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataN = mysql_query($sDataN)) || true;

while ($rDataN = mysql_fetch_assoc($qDataN)) {
	$biBbm[$rDataN['kodevhc']] = $rDataN['gaji'];
}

$sDataO = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110204\'  and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataO = mysql_query($sDataO)) || true;

while ($rDataO = mysql_fetch_assoc($qDataO)) {
	$biSukuCdng[$rDataO['kodevhc']] = $rDataO['gaji'];
}

$sDataP = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110205\'  and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataP = mysql_query($sDataP)) || true;

while ($rDataP = mysql_fetch_assoc($qDataP)) {
	$biReparasi[$rDataP['kodevhc']] = $rDataP['gaji'];
}

$sDataQ = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110206\'  and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataQ = mysql_query($sDataQ)) || true;

while ($rDataQ = mysql_fetch_assoc($qDataQ)) {
	$biAsuransi[$rDataQ['kodevhc']] = $rDataQ['gaji'];
}

$sDataQ = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110207\'  and tanggal like \'' . $periode . '%\' group by kodevhc';

#exit(mysql_error());
($qDataQ = mysql_query($sDataQ)) || true;

while ($rDataQ = mysql_fetch_assoc($qDataQ)) {
	$biUmum[$rDataQ['kodevhc']] = $rDataQ['gaji'];
}

$sDataL = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110201\' and tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataL = mysql_query($sDataL)) || true;

while ($rDataL = mysql_fetch_assoc($qDataL)) {
	$sBiGaji[$rDataL['kodevhc']] = $rDataL['gaji'];
}

$sDataM = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110202\'  and tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataM = mysql_query($sDataM)) || true;

while ($rDataM = mysql_fetch_assoc($qDataM)) {
	$sBiLembur[$rDataM['kodevhc']] = $rDataM['gaji'];
}

$sDataN = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110203\' and  tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataN = mysql_query($sDataN)) || true;

while ($rDataN = mysql_fetch_assoc($qDataN)) {
	$sBiBbm[$rDataN['kodevhc']] = $rDataN['gaji'];
}

$sDataO = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110204\'  and tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataO = mysql_query($sDataO)) || true;

while ($rDataO = mysql_fetch_assoc($qDataO)) {
	$sBiSukuCdng[$rDataO['kodevhc']] = $rDataO['gaji'];
}

$sDataP = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110205\' and tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataP = mysql_query($sDataP)) || true;

while ($rDataP = mysql_fetch_assoc($qDataP)) {
	$sBiReparasi[$rDataP['kodevhc']] = $rDataP['gaji'];
}

$sDataQ = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110206\'  and tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataQ = mysql_query($sDataQ)) || true;

while ($rDataQ = mysql_fetch_assoc($qDataQ)) {
	$sBiAsuransi[$rDataQ['kodevhc']] = $rDataQ['gaji'];
}

$sDataQ = 'select a.kodevhc,sum(debet)/1000 as gaji from ' . $dbname . '.keu_jurnaldt_vw a left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc ' . "\r\n" . '         where b.kodetraksi like \'' . $kdUnit . '%\' and a.noakun=\'4110207\'  and tanggal between  \'' . $thn[0] . '-01-01\' and LAST_DAY(\'' . $periode . '-15\') group by kodevhc';

#exit(mysql_error());
($qDataQ = mysql_query($sDataQ)) || true;

while ($rDataQ = mysql_fetch_assoc($qDataQ)) {
	$sBiUmum[$rDataQ['kodevhc']] = $rDataQ['gaji'];
}

$varCek = count($dtKend);

if ($varCek < 1) {
	exit('Error:No data found');
}

$brdr = 0;
$bgcoloraja = '';

if ($_SESSION['language'] == 'EN') {
	$jud = 'HEAVY EQUIPMENT TRANSIT';
}
else {
	$jud = 'TRANSIT ALAT BERAT';
}

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>25.4 ' . $jud . '</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>';
$tab .= '<tr ' . $bgcoloraja . '>';
$tab .= '<td rowspan=3>No.</td>';
$tab .= '<td rowspan=3>' . $_SESSION['lang']['jenis'] . '</td>';
$tab .= '<td rowspan=3>' . $_SESSION['lang']['nama'] . '</td>';
$tab .= '<td rowspan=3>' . $_SESSION['lang']['kodevhc'] . '</td>';
$tab .= '<td rowspan=3>' . $_SESSION['lang']['tahunperolehan'] . '</td>';
$tab .= '<td rowspan=3>' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['setahun'] . ' (KM)</td>';
$tab .= '<td colspan=4>' . $_SESSION['lang']['realisasi'] . '  ' . $_SESSION['lang']['penggunaan'] . '  FISIK</td>';
$tab .= '<td rowspan=3>RATIO S/D ' . $_SESSION['lang']['bi'] . ' (LTR/KM)</td>';
$tab .= '<td rowspan=3>' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['setahun'] . ' (Rp.000,-)</td>';
$tab .= '<td colspan=16>' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['biaya'] . '  (000)</td>';
$tab .= '<td colspan=3>COST / UNIT<br />Rp. / KM</td></tr>';
$tab .= '<tr ' . $bgcoloraja . '><td colspan=2>KM</td>';
$tab .= '<td colspan=2>LTR</td>';
$tab .= '<td colspan=8> ' . $_SESSION['lang']['bi'] . '</td>';
$tab .= '<td colspan=8> ' . $_SESSION['lang']['sbi'] . '</td>';
$tab .= '<td rowspan=2>' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['setahun'] . '</td>';
$tab .= '<td colspan=2>' . $_SESSION['lang']['realisasi'] . '</td></tr>';
$tab .= '<tr ' . $bgcoloraja . '><td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['sbi'] . '</td><td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['sbi'] . '</td>';
$tab .= '<td>Gaji</td><td>Pre/Lembur</td><td>BBM/Plumas</td><td>S.Cadang</td><td>Reprasi</td><td>Asuransi</td><td>By Umum</td><td>Total</td>';
$tab .= '<td>Gaji</td><td>Pre/Lembur</td><td>BBM/Plumas</td><td>S.Cadang</td><td>Reprasi</td><td>Asuransi</td><td>By Umum</td><td>Total</td>';
$tab .= '<td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['sbi'] . '</td>';
$tab .= '</tr></thead><tbody>';

foreach ($dtKend as $lstKend) {
	$no += 1;
	@$rasioDt[$lstKend] = $jmlhBbmSbi[$lstKend] / $dtSbi[$lstKend];
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $no . '</td>';
	$tab .= '<td>' . $lsJenis[$lstKend] . '</td>';
	$tab .= '<td>' . $lsNama[$lstKend] . '</td>';
	$tab .= '<td>' . $lstKend . '</td>';
	$tab .= '<td>' . $lsThnPerolehan[$lstKend] . '</td>';
	$tab .= '<td align=right>' . number_format($lsKm[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($dtBi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($dtSbi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($jmlhBbm[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($jmlhBbmSbi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($rasioDt[$lstKend], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtAnggrn[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biGaji[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biLembur[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biBbm[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biSukuCdng[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biReparasi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biAsuransi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($biUmum[$lstKend], 0) . '</td>';
	$rTotal[$lstKend] = $biGaji[$lstKend] + $biLembur[$lstKend] + $biBbm[$lstKend] + $biSukuCdng[$lstKend] + $biReparasi[$lstKend] + $biAsuransi[$lstKend] + $biUmum[$lstKend];
	$tab .= '<td align=right>' . number_format($rTotal[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiGaji[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiLembur[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiBbm[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiSukuCdng[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiReparasi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiAsuransi[$lstKend], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sBiUmum[$lstKend], 0) . '</td>';
	$rTotalSbi[$lstKend] = $sBiGaji[$lstKend] + $sBiLembur[$lstKend] + $sBiBbm[$lstKend] + $sBiSukuCdng[$lstKend] + $sBiReparasi[$lstKend] + $sBiAsuransi[$lstKend] + $sBiUmum[$lstKend];
	$tab .= '<td align=right>' . number_format($rTotalSbi[$lstKend], 0) . '</td>';
	@$zData[$lstKend] = $dtAnggrn[$lstKend] / $lsKm[$lstKend];
	@$aaDta[$lstKend] = $rTotal[$lstKend] / $dtBi[$lstKend];
	@$abDta[$lstKend] = $rTotalSbi[$lstKend] / $dtSbi[$lstKend];
	$tab .= '<td align=right>' . number_format($zData[$lstKend], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($aaDta[$lstKend], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($abDta[$lstKend], 2) . '</td>';
	$tab .= '</tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'transitAlatBerat' . $dte;
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
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $tot;
			global $jud;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('25.4 ' . $jud), 0, 1, 'L');
			$this->Cell($width, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$height = 10;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 5);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(15, $height, 'No.', TLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['jenis'], TLR, 0, 'C', 1);
			$this->Cell(80, $height, $_SESSION['lang']['nama'], TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['kodevhc'], TLR, 0, 'C', 1);
			$this->Cell(25, $height, '', TLR, 0, 'C', 1);
			$this->Cell(45, $height, $_SESSION['lang']['anggaran'], TLR, 0, 'C', 1);
			$this->Cell(80, $height, $_SESSION['lang']['realisasi'] . '  ' . $_SESSION['lang']['pengunaan'], TLR, 0, 'C', 1);
			$this->Cell(50, $height, 'RATIO ', TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['anggaran'], TLR, 0, 'C', 1);
			$this->Cell(280, $height, $_SESSION['lang']['realisasi'] . '  ' . $_SESSION['lang']['biaya'] . '(000) ', TLR, 0, 'C', 1);
			$this->Cell(80, $height, 'COST / UNIT', TLR, 1, 'C', 1);
			$this->Cell(15, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(35, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(80, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(50, $height, '', LR, 0, 'C', 1);
			$this->Cell(25, $height, '', LR, 0, 'C', 1);
			$this->Cell(45, $height, $_SESSION['lang']['setahun'], LR, 0, 'C', 1);
			$this->Cell(40, $height, 'KM', TLR, 0, 'C', 1);
			$this->Cell(40, $height, 'LTR', TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['sbi'], LR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['setahun'], LR, 0, 'C', 1);
			$this->Cell(140, $height, $_SESSION['lang']['bi'], TLR, 0, 'C', 1);
			$this->Cell(140, $height, $_SESSION['lang']['sbi'], TLR, 0, 'C', 1);
			$this->Cell(40, $height, $_SESSION['lang']['anggaran'], TLR, 0, 'C', 1);
			$this->Cell(40, $height, $_SESSION['lang']['realisasi'], TLR, 1, 'C', 1);
			$this->Cell(15, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(35, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(80, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, '', BLR, 0, 'C', 1);
			$this->Cell(25, $height, $_SESSION['lang']['tahunperolehan'], BLR, 0, 'C', 1);
			$this->Cell(45, $height, '(KM)', BLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, '(LTR/KM)', BLR, 0, 'C', 1);
			$this->Cell(50, $height, ' (Rp.000,-) ', BLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Gaji', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 3.5);
			$this->Cell(20, $height, 'Pre/Lembur', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'BBM/Plumas', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'S.Cadang', TBLR, 0, 'L', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Reprasi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Asuransi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Total', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Gaji', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 3.5);
			$this->Cell(20, $height, 'Pre/Lembur', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'BBM/Plumas', TBLR, 0, 'L', 1);
			$this->Cell(20, $height, 'S.Cadang', TBLR, 0, 'L', 1);
			$this->SetFont('Arial', 'B', 4);
			$this->Cell(20, $height, 'Reprasi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Asuransi', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, 'Total', TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 5);
			$this->Cell(40, $height, $_SESSION['lang']['setahun'], BLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 1, 'C', 1);
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
	$tnggi = $jmlHari * $height;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 5);
	$i = 0;

	foreach ($dtKend as $lstKend) {
		$i += 1;
		$pdf->Cell(15, $height, $i, TBLR, 0, 'C', 1);
		$pdf->Cell(35, $height, $lsJenis[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(80, $height, $lsNama[$lstKend], TBLR, 0, 'L', 1);
		$pdf->Cell(50, $height, $lstKend, TBLR, 0, 'L', 1);
		$pdf->Cell(25, $height, $lsThnPerolehan[$lstKend], TBLR, 0, 'C', 1);
		$pdf->Cell(45, $height, number_format($lsKm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtBi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtSbi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($jmlhBbm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($jmlhBbmSbi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($rasioDt[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($dtAnggrn[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biGaji[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biLembur[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biBbm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biSukuCdng[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biReparasi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($biAsuransi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($rTotal[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiGaji[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiLembur[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiBbm[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiSukuCdng[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiReparasi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($sBiAsuransi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($rTotalSbi[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(40, $height, number_format($zData[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($aaDta[$lstKend], 0), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($abDta[$lstKend], 0), TBLR, 1, 'R', 1);
	}

	$pdf->Output();
	break;
}

?>
