<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
require_once 'lib/devLibrary.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$sKlmpk = "select kode,kelompok from ' . $dbname . '.log_5klbarang order by kode";

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
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
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

if (strlen($thn[1]) < 2) {
	$field = 'kg0' . $thn[1];
}
else {
	$field = 'kg' . $thn[1];
}

$asr5 = 1;

while ($asr5 <= $thn[1]) {
	if (strlen($asr5) < 2) {
		if ($asr5 == 1) {
			$field5 = 'kg0' . $asr5;
		}
		else {
			$field5 .= '+kg0' . $asr5;
		}
	}
	else {
		$field5 .= '+kg' . $asr5;
	}

	++$asr5;
}

$sThnTnm = "select distinct thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
		"where kodeunit='" . $kdUnit . "' and tahunbudget='" . $thn[0] . "'  ".
		"order by thntnm asc";

if ($afdId != '') {
	$sThnTnm = "select distinct thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
		"where kodeblok like '" . $afdId . "%\' and tahunbudget='" . $thn[0] . "'  ".
		"order by thntnm asc";
}
/*
 * execute query $sThnTnm, data kosong
 */

#exit(mysql_error());
($qThnTnm = mysql_query($sThnTnm)) || true;

while ($rThnTnm = mysql_fetch_assoc($qThnTnm)) {
	if (strlen($rThnTnm['thntnm']) == '4') {
		$dtThnTnm[] = $rThnTnm['thntnm'];
	}
}

$sLuas = "select distinct sum(hathnini) as luas,thntnm from  $dbname.bgt_blok ".
	"where substr(kodeblok,1,4)='" . $kdUnit . "' and tahunbudget='" . $thn[0] . "' group by thntnm";

if ($afdId != '') {
	$sLuas = "select distinct sum(hathnini) as luas,thntnm from $dbname.bgt_blok ".
		"where substr(kodeblok,1,6)='" . $afdId . "' and tahunbudget='" . $thn[0] . "' group by thntnm";
}
/*
 * execute query $sLuas, data kosong
 */

#exit(mysql_error());
($qLuas = mysql_query($sLuas)) || true;
$lsAnggran=[];
while ($rLuas = mysql_fetch_assoc($qLuas)) {
	$lsAnggran[$rLuas['thntnm']] += $rLuas['luas'];
}

$sLuasRealisasi = "select distinct sum(luasareaproduktif) as luas,tahuntanam from $dbname.setup_blok ".
"where substr(kodeorg,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sLuasRealisasi = "select distinct sum(luasareaproduktif) as luas,tahuntanam from  $dbname.setup_blok ".
		"where substr(kodeorg,1,6)='" . $afdId . "' group by tahuntanam";
}

#exit(mysql_error());
($qLuasRealisasi = mysql_query($sLuasRealisasi)) || true;
$lsRealisasi=[];
while ($rLuasRealisasi = mysql_fetch_assoc($qLuasRealisasi)) {
//	$lsRealisasi += $rLuasRealisasi['tahuntanam'];
	$lsRealisasi[$rLuasRealisasi['tahuntanam']] += $rLuasRealisasi['luas'];
}

$sKgTaon = "select distinct sum(kgsetahun) as kgstaun,thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
	"where tahunbudget='" . $thn[0] . "' and kodeunit='" . $kdUnit . "' group by thntnm";

if ($afdId != '') {
	$sKgTaon = "select distinct sum(kgsetahun) as kgstaun,thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
	"where tahunbudget='" . $thn[0] . "' and kodeblok like '" . $afdId . "%' group by thntnm";
}
/*
 * execute query $sKgTaon, data kosong
 */


#exit(mysql_error());
($qKgTaon = mysql_query($sKgTaon)) || true;
$kgSthn=[];
while ($rKgTaon = mysql_fetch_assoc($qKgTaon)) {
	$kgSthn[$rKgTaon['thntnm']] += $rKgTaon['kgstaun'];
}

$sKgTaonbi = "select distinct sum(' . $field . ') as kgstaun,thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
			"where tahunbudget='" . $thn[0] . "' and kodeunit='" . $kdUnit . "' group by thntnm";

if ($afdId != '') {
	$sKgTaonbi = "select distinct sum(' . $field . ') as kgstaun,thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
				" where tahunbudget='" . $thn[0] . "' and kodeblok like '" . $afdId . "%' group by thntnm";
}
/*
 * execute query $sKgTaonbi, data kosong
 */

#exit(mysql_error());
($qKgTaonbi = mysql_query($sKgTaonbi)) || true;
$kgSthnBi=[];
while ($rKgTaonbi = mysql_fetch_assoc($qKgTaonbi)) {
	$kgSthnBi[$rKgTaonbi['thntnm']] += $rKgTaonbi['kgstaun'];
}

$sKgTaonsbi = "select distinct sum(" . $field5 . ") as kgstaun,thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
			"where tahunbudget='" . $thn[0] . "' and kodeunit='" . $kdUnit . "' group by thntnm";

if ($afdId != '') {
	$sKgTaonsbi = "select distinct sum(" . $field5 . ") as kgstaun,thntnm from $dbname.bgt_produksi_kbn_kg_vw ".
				"where tahunbudget='" . $thn[0] . "' and kodeblok like '" . $afdId . "%' group by thntnm";
}
/*
 * execute query $sKgTaonsbi, data kosong
 */

#exit(mysql_error());
($qKgTaonsbi = mysql_query($sKgTaonsbi)) || true;
$kgSthnsBi=[];
while ($rKgTaonsbi = mysql_fetch_assoc($qKgTaonsbi)) {
	$kgSthnsBi[$rKgTaonsbi['thntnm']] += $rKgTaonsbi['kgstaun'];
}

$sSensus = "select distinct sum(kgsensus) as kgsensus, tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw  ".//kebun_spb_vs_rencana_blok_vw
			"where periode='" . $periode . "' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sSensus = "select distinct sum(kgsensus) as kgsensus, tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where periode='" . $periode . "' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
}
/*
 * table or view or function or stored procedure kebun_spb_vs_rencana_blok_vw not exist
 * added view kebun_spb_vs_rencana_blok_vw with query :
 * SELECT * from kebun_spb_vs_rencana_vw k  INNER JOIN setup_blok s ON s.kodeorg=k.blok
 */

/*
 * execute query $sSensus, kgsensus field null
 */
#exit(mysql_error());
($qSensus = mysql_query($sSensus)) || true;
$biSensus=[];
while ($rSensus = mysql_fetch_assoc($qSensus)) {
	$biSensus[$rSensus['tahuntanam']] += $rSensus['kgsensus'];
}

$sSensus = "select distinct sum(kgsensus) as kgsensus,tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
			"where periode<='" . $periode . "' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sSensus = "select distinct sum(kgsensus) as kgsensus,tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where periode<='" . $periode . "' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
}
/*
 * execute query $sSensus, kgsensus field null
 */
#exit(mysql_error());
($qSensus = mysql_query($sSensus)) || true;
$sbiSensus=[];
while ($rSensus = mysql_fetch_assoc($qSensus)) {
	$sbiSensus[$rSensus['tahuntanam']] += $rSensus['kgsensus'];
}
$senSmstr=[];
if ($thn[1] < 7) {
	$sSensus = "select distinct sum(kgsensus) as kgsensus,tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where periode<'" . $thn[0] . "-07' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

	if ($afdId != '') {
		$sSensus = "select distinct sum(kgsensus) as kgsensus,tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
		 			"where periode<'" . $thn[0] . "-07' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
	}

	/*
     * execute query $sSensus, kgsensus field null
     */

	#exit(mysql_error());
	($qSensus = mysql_query($sSensus)) || true;

	while ($rSensus = mysql_fetch_assoc($qSensus)) {
		$senSmstr[$rSensus['tahuntanam']] += $rSensus['kgsensus'];
	}
}
else if (($thn[1] < 13) && (6 < $thn[1])) {
	$sSensus = "select distinct sum(kgsensus) as kgsensus,tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where periode>'" . $thn[0] . "-06' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

	if ($afdId != '') {
		$sSensus = "select distinct sum(kgsensus) as kgsensus,tahuntanam from $dbname.kebun_spb_vs_rencana_blok_vw ".
					"where periode>'" . $thn[0] . "-06' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
	}
	/*
     * execute query $sSensus, kgsensus field null
     */

	#exit(mysql_error());
	($qSensus = mysql_query($sSensus)) || true;

	while ($rSensus = mysql_fetch_assoc($qSensus)) {
		$senSmstr[$rSensus['tahuntanam']] += $rSensus['kgsensus'];
	}
}

$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where periode='" . $periode . "' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
					"where periode='" . $periode . "' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
}

#exit(mysql_error());
($qRealisasi = mysql_query($sRealaisasi)) || true;
$biRealisasi=[];
while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$biRealisasi[$rRealisasi['tahuntanam']] += $rRealisasi['realisasi'];
}
//echoMessage(' sql ',$sRealaisasi);
//echoMessage(' biRealisasi ',$biRealisasi,true);
$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where  periode<='" . $periode . "' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw  ".
					"where  periode<='" . $periode . "' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
}

#exit(mysql_error());
($qRealisasi = mysql_query($sRealaisasi)) || true;
$sbiRealisasi=[];
while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$sbiRealisasi[$rRealisasi['tahuntanam']] += $rRealisasi['realisasi'];
}

$thnLalu = $thn[0] - 1;
$period = $thnLalu . '-' . $thn[1];
$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where  periode<='" . $period . "' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
					"where  periode<='" . $period . "' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
}

#exit(mysql_error());
($qRealisasi = mysql_query($sRealaisasi)) || true;
$prodThnLalusbi=[];
while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$prodThnLalusbi[$rRealisasi['tahuntanam']] += $rRealisasi['realisasi'];
}

$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
				"where  substr(periode,1,4)='" . $thnLalu . "' and substr(blok,1,4)='" . $kdUnit . "' group by tahuntanam";

if ($afdId != '') {
	$sRealaisasi = "select distinct sum(nettotimbangan) as realisasi,tahuntanam from  $dbname.kebun_spb_vs_rencana_blok_vw ".
					"where  substr(periode,1,4)='" . $thnLalu . "' and substr(blok,1,6)='" . $afdId . "' group by tahuntanam";
}

#exit(mysql_error());
($qRealisasi = mysql_query($sRealaisasi)) || true;
$prodThnLalu=[];
while ($rRealisasi = mysql_fetch_assoc($qRealisasi)) {
	$prodThnLalu[$rRealisasi['tahuntanam']] += $rRealisasi['realisasi'];
}

$sPotensi = "select distinct tahuntanam,klasifikasitanah,jenisbibit from $dbname.kebun_spb_vs_rencana_blok_vw ".
			"where  periode='" . $periode . "' and substr(blok,1,4)='" . $kdUnit . "' ";

if ($afdId != '') {
	$sPotensi = "select distinct tahuntanam,klasifikasitanah,jenisbibit from $dbname.kebun_spb_vs_rencana_blok_vw ".
		"where  periode='" . $periode . "' and substr(blok,1,6)='" . $afdId . "' ";
}

#exit(mysql_error());
($qPotensi = mysql_query($sPotensi)) || true;
$potProd=[];
while ($rSensus = mysql_fetch_assoc($qPotensi)) {
	$umur = $thn[0] - $rSensus['tahuntanam'];
	$sPot = "select distinct kgproduksi from $dbname.kebun_5stproduksi ".
			"where jenisbibit='" . $rSensus['jenisbibit'] . "' ".
			"and klasifikasitanah='" . $rSensus['klasifikasitanah'] . "' ".
			"and umur='" . $umur . "'";

	#exit(mysql_error());
	($qPot = mysql_query($sPot)) || true;
	$rPot = mysql_fetch_assoc($qPot);
	$potProd[$rSensus['tahuntanam']] = ($lsRealisasi[$rSensus['tahuntanam']] * $rPot['kgproduksi']) / 1000;
}

$varCek = count($dtThnTnm);

if ($varCek < 1) {
	$sThnTnm = "select distinct tahuntanam as thntnm from $dbname.setup_blok ".
				"where kodeorg like '" . $kdUnit . "%'  order by tahuntanam asc";

	if ($afdId != '') {
		$sThnTnm = "select distinct tahuntanam as thntnm from $dbname.setup_blok ".
					"where kodeorg like '" . $afdId . "%' order by tahuntanam asc";
	}

	#exit(mysql_error());
	($qThnTnm = mysql_query($sThnTnm)) || true;

	while ($rThnTnm = mysql_fetch_assoc($qThnTnm)) {
		if (strlen($rThnTnm['thntnm']) == '4') {
			$dtThnTnm[] = $rThnTnm['thntnm'];
		}
	}
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=8 align=left><b>' . $_GET['judul'] . '</b></td><td colspan=3 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=8 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table   cellpadding=q cellspacing=0 border=1 class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['umur'] . ' (' . $_SESSION['lang']['tahun'] . ')</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=2>' . $_SESSION['lang']['luas'] . ' (Ha)</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=3>' . $_SESSION['lang']['anggaran'] . ' (TON)</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=2>' . $_SESSION['lang']['sensus'] . ' (TON)</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=2>' . $_SESSION['lang']['realisasi'] . ' (TON)</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=2>% VARIAN REAL VS CENSUS</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=2>% VARIAN REAL VS BUDGET</td>';
$tab .= '<td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['sbi'] . ' (' . $_SESSION['lang']['tahunlalu'] . ')</td><td ' . $bgcoloraja . ' rowspan=2>CENSUS  SM-I/II</td>';
$tab .= '<td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tahunlalu'] . '</td><td ' . $bgcoloraja . ' rowspan=2>Potency ' . $_SESSION['lang']['produksi'] . '</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['anggaran'] . '</td><td ' . $bgcoloraja . ' >REAL</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['setahun'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['bi'] . '</td>' . "\r\n" . '               <td ' . $bgcoloraja . ' >' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['sbi'] . '</td>' . "\r\n" . '               <td ' . $bgcoloraja . ' >' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['sbi'] . '</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';

$totLAngr=0;
$totLReali=0;
$totKgStaon=0;
$totKgSthnBi=0;
$totkgSthnsBi=0;
$totbiSensus=0;
$totsbiSensus=0;
$totbiRealisasi=0;
$totsbiRealisasi=0;
$totsnVsRealibi=0;
$totsnVsRealisbi=0;
$totangVsRealibi=0;
$totprodThnLalusbi=0;
$totsenSmstr=0;
$totprodThnLalu=0;
$totpotProd=0;

foreach ($dtThnTnm as $lstThnTnm) {
	$tab .= '<tr class=rowcontent><td>' . $lstThnTnm . '</td>';
	$umur = $periode - $lstThnTnm;
	$tab .= '<td align=right>' . $umur . '</td>';
	$tab .= '<td align=right>' . number_format($lsAnggran[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($lsRealisasi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kgSthn[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kgSthnBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kgSthnsBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($biSensus[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($sbiSensus[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($biRealisasi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($sbiRealisasi[$lstThnTnm], 2) . '</td>';
	$snVsRealibi[$lstThnTnm] = ($biRealisasi[$lstThnTnm] / $biSensus[$lstThnTnm]) * 100;
	$snVsRealisbi[$lstThnTnm] = ($sbiRealisasi[$lstThnTnm] / $sbiSensus[$lstThnTnm]) * 100;
	$tab .= '<td align=right>' . number_format($snVsRealibi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($snVsRealisbi[$lstThnTnm], 2) . '</td>';
	$angVsRealibi[$lstThnTnm] = ($biRealisasi[$lstThnTnm] / $kgSthnBi[$lstThnTnm]) * 100;
	$angVsRealisbi[$lstThnTnm] = ($sbiRealisasi[$lstThnTnm] / $kgSthnsBi[$lstThnTnm]) * 100;
	$tab .= '<td align=right>' . number_format($angVsRealibi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($angVsRealisbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($prodThnLalusbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($senSmstr[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($prodThnLalu[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($potProd[$lstThnTnm], 2) . '</td>';
	$tab .= '</tr>';
	$totLAngr += $lsAnggran[$lstThnTnm];
	$totLReali += $lsRealisasi[$lstThnTnm];
	$totKgStaon += $kgSthn[$lstThnTnm];
	$totKgSthnBi += $kgSthnBi[$lstThnTnm];
	$totkgSthnsBi += $kgSthnsBi[$lstThnTnm];
	$totbiSensus += $biSensus[$lstThnTnm];
	$totsbiSensus += $sbiSensus[$lstThnTnm];
	$totbiRealisasi += $biRealisasi[$lstThnTnm];
	$totsbiRealisasi += $sbiRealisasi[$lstThnTnm];
	$totsnVsRealibi += $snVsRealibi[$lstThnTnm];
	$totsnVsRealisbi += $snVsRealisbi[$lstThnTnm];
	$totangVsRealibi += $angVsRealibi[$lstThnTnm];
	$totprodThnLalusbi += $prodThnLalusbi[$lstThnTnm];
	$totsenSmstr += $senSmstr[$lstThnTnm];
	$totprodThnLalu += $prodThnLalu[$lstThnTnm];
	$totpotProd += $potProd[$lstThnTnm];
}

$tab .= '<tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '<td align=right>' . number_format($totLAngr, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totLReali, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totKgStaon, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totKgSthnBi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totkgSthnsBi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totbiSensus, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totsbiSensus, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totbiRealisasi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totsbiRealisasi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totsnVsRealibi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totsnVsRealisbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totangVsRealibi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totangVsRealibi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totprodThnLalusbi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totsenSmstr, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totprodThnLalu, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totpotProd, 2) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = $judul . '_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'pdf':
	generateTablePDF($tab);
	exit();
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $judul;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper($judul), 0, 1, 'L');
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
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(25, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['umur'], TLR, 0, 'C', 1);
			$this->Cell(60, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(150, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(100, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(100, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(60, $height, '% VARIAN', TLR, 0, 'C', 1);
			$this->Cell(70, $height, '% VARIAN', TLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TLR, 0, 'C', 1);
			$this->Cell(55, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(55, $height, $_SESSION['lang']['tahunlalu'], TLR, 0, 'C', 1);
			$this->Cell(40, $height, ' ', TLR, 1, 'C', 1);
			$this->Cell(25, $height, $_SESSION['lang']['tahun'], LR, 0, 'C', 1);
			$this->Cell(50, $height, '', LR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['luas'] . ' (Ha)', LR, 0, 'C', 1);
			$this->Cell(150, $height, $_SESSION['lang']['anggaran'] . ' (TON)', LR, 0, 'C', 1);
			$this->Cell(100, $height, 'CENSUS (TON)', LR, 0, 'C', 1);
			$this->Cell(100, $height, $_SESSION['lang']['realisasi'] . ' (TON)', LR, 0, 'C', 1);
			$this->Cell(60, $height, 'REAL VS', LR, 0, 'C', 1);
			$this->Cell(70, $height, 'REAL VS', LR, 0, 'C', 1);
			$this->Cell(30, $height, '(' . $_SESSION['lang']['tahunlalu'] . ')', LR, 0, 'C', 1);
			$this->Cell(55, $height, 'CENSUS', LR, 0, 'C', 1);
			$this->Cell(55, $height, '', LR, 0, 'C', 1);
			$this->Cell(40, $height, 'POTENCY', LR, 1, 'C', 1);
			$this->Cell(25, $height, $_SESSION['lang']['tanam'], LR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['tahunlalu'], LR, 0, 'C', 1);
			$this->Cell(60, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(150, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(100, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(100, $height, ' ', LR, 0, 'C', 1);
			$this->Cell(60, $height, 'CNS', LR, 0, 'C', 1);
			$this->Cell(70, $height, 'BUDGET', LR, 0, 'C', 1);
			$this->Cell(30, $height, '', LR, 0, 'C', 1);
			$this->Cell(55, $height, 'SM-I/II ', LR, 0, 'C', 1);
			$this->Cell(55, $height, '', LR, 0, 'C', 1);
			$this->Cell(40, $height, $_SESSION['lang']['produksi'], LR, 1, 'C', 1);
			$this->Cell(25, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(50, $height, ' ', BLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(30, $height, 'BUDGET', TBLR, 0, 'C', 1);
			$this->Cell(30, $height, 'REAL', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['setahun'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(35, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(30, $height, '', BLR, 0, 'C', 1);
			$this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(40, $height, ' ', BLR, 1, 'C', 1);
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
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);

	foreach ($dtThnTnm as $lstThnTnm) {
		$umur = $periode - $lstThnTnm;
		$pdf->Cell(25, $height, $lstThnTnm, 1, 0, 'C', 1);
		$pdf->Cell(50, $height, $umur, 1, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($lsAnggran[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($lsRealisasi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($kgSthn[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($kgSthnBi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($kgSthnsBi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($biSensus[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($sbiSensus[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($biRealisasi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, $height, number_format($sbiRealisasi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($snVsRealibi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($snVsRealisbi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(35, $height, number_format($angVsRealibi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(35, $height, number_format($angVsRealisbi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($prodThnLalusbi[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(55, $height, number_format($senSmstr[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(55, $height, number_format($prodThnLalu[$lstThnTnm], 2), 1, 0, 'R', 1);
		$pdf->Cell(40, $height, number_format($potProd[$lstThnTnm], 2), 1, 1, 'R', 1);
	}

	$tab .= '<tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totLAngr, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totLReali, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totKgStaon, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totKgSthnBi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totkgSthnsBi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totbiSensus, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totsbiSensus, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totbiRealisasi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totsbiRealisasi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totsnVsRealibi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totsnVsRealisbi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totangVsRealibi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totangVsRealibi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totprodThnLalusbi, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totsenSmstr, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totprodThnLalu, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totpotProd, 2) . '</td>';
	$tab .= '</tr>';
	$pdf->Cell(75, $height, $_SESSION['lang']['total'], 1, 0, 'L', 1);
	$pdf->Cell(30, $height, number_format($totLAngr, 2), 1, 0, 'R', 1);
	$pdf->Cell(30, $height, number_format($totLReali, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totKgStaon, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totKgSthnBi, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totkgSthnsBi, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totbiSensus, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totsbiSensus, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totbiRealisasi, 2), 1, 0, 'R', 1);
	$pdf->Cell(50, $height, number_format($totsbiRealisasi, 2), 1, 0, 'R', 1);
	$pdf->Cell(30, $height, number_format($totsnVsRealibi, 2), 1, 0, 'R', 1);
	$pdf->Cell(30, $height, number_format($totsnVsRealisbi, 2), 1, 0, 'R', 1);
	$pdf->Cell(35, $height, number_format($totangVsRealibi, 2), 1, 0, 'R', 1);
	$pdf->Cell(35, $height, number_format($totangVsRealibi, 2), 1, 0, 'R', 1);
	$pdf->Cell(30, $height, number_format($prodThnLalusbi[$lstThnTnm], 2), 1, 0, 'R', 1);
	$pdf->Cell(55, $height, number_format($totsenSmstr, 2), 1, 0, 'R', 1);
	$pdf->Cell(55, $height, number_format($totprodThnLalu, 2), 1, 0, 'R', 1);
	$pdf->Cell(40, $height, number_format($totpotProd, 2), 1, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
