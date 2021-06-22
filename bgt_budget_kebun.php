<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo '<script>' . "\r\n" . 'pilh=" ';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . '</script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/bgt_budget_kebun.js"></script>' . "\r\n" . '<script>' . "\r\n" . 'dataKdvhc="';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . '</script>' . "\r\n";
$optBlok = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optKeg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optAfdeling = $optBlok;
$optKdbdgt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg2 = 'select kodebudget,nama from ' . $dbname . '.bgt_kode where kodebudget like \'%SDM%\' order by nama asc';

#exit(mysql_error());
($qOrg2 = mysql_query($sOrg2)) || true;

while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
	$optKdbdgt .= '<option value=' . $rOrg2['kodebudget'] . '>' . $rOrg2['nama'] . '</option>';
}

$optKeg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sKeg = 'select distinct kodekegiatan,namakegiatan,kelompok from ' . $dbname . '.setup_kegiatan where  kelompok in (\'PNN\',\'TBM\',\'TM\',\'BBT\',\'TB\')  order by kodekegiatan asc';

#exit(mysql_error());
($qKeg = mysql_query($sKeg)) || true;

while ($rKeg = mysql_fetch_assoc($qKeg)) {
	if ($kegId != '') {
		$optKeg .= '<option value=' . $rKeg['kodekegiatan'] . ' ' . ($rKeg['kodekegiatan'] == $kegId ? 'selected' : '') . '>' . $rKeg['kodekegiatan'] . ' [' . $rKeg['namakegiatan'] . '][' . $rKeg['kelompok'] . ']</option>';
	}
	else {
		$optKeg .= '<option value=' . $rKeg['kodekegiatan'] . '>' . $rKeg['kodekegiatan'] . ' [' . $rKeg['namakegiatan'] . '][' . $rKeg['kelompok'] . ']</option>';
	}
}

OPEN_BOX('', '<b>' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['kebun'] . '</b>');
echo '<br /><br /><fieldset style=\'float:left;\'><legend>' . $_SESSION['lang']['entryForm'] . '</legend> <table border=0 cellpadding=1 cellspacing=1>';
echo '<tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'thnBudget\' style=\'width:150px;\' maxlength=\'4\' onkeypress=\'return angka_doang(event)\' onblur=\'getKodeblok(0,0,0)\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['tipe'] . '</td><td><input type=\'text\' class=\'myinputtext\' disabled value=\'ESTATE\' id=\'tipeBudget\' style=width:150px; /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['kodeblok'] . '</td><td><select style=\'width:150px;\' id=\'kdBlok\' onchange=isiLuas(this)>' . $optBlok . '</select></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['kegiatan'] . '</td><td><select style=\'width:150px;\' id=\'kegId\' onchange=\'getSatuan()\'>' . $optKeg . '</select></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['noakun'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'noAkun\' disabled style=\'width:150px;\' onkeypress=\'return angka_doang(event)\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['fisik'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'volKeg\' style=\'width:150px;\' onkeypress=\'return angka_doang(event)\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['satuan'] . '</td><td><input type=\'text\' class=\'myinputtext\' id=\'satKeg\' style=\'width:150px;\' onkeypress=\'return tanpa_kutip(event)\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['rotasi'] . '/' . $_SESSION['lang']['tahun'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'rotThn\' style=\'width:150px;\' onkeypress=\'return tanpa_kutip(event)\' value=\'1\' /></td></tr>';
echo '<tr><td colspan=\'2\'><button class="mybutton"  id="saveData" onclick=\'saveData()\'>' . $_SESSION['lang']['save'] . '</button><button  class="mybutton"  id="newData" onclick=\'newData()\'>' . $_SESSION['lang']['baru'] . '</button></td></tr>';
echo '</table></fieldset>';
$optThnTtp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
echo '<fieldset  style=\'float:left\'><legend>' . $_SESSION['lang']['tutup'] . '</legend>' . "\r\n" . '    <div><table><tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td><select id=\'thnBudgetTutup\' style=\'width:150px\'>' . $optThnTtp . '</select></td></tr>';
echo '<tr><td colspan=2 align=center><button class="mybutton"  id="saveData" onclick=\'closeBudget()\'>' . $_SESSION['lang']['tutup'] . '</button></td></tr></table>';
echo '</div></fieldset>';
$frm .= 0;
$frm .= 0;
$frm .= 0;
CLOSE_BOX();
echo '<div id=\'listDatHeader\' style=\'display:block\'>';
OPEN_BOX();
$optTahunBudgetHeader = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sThn = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget where substring(kodeorg,1,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' and tipebudget=\'ESTATE\' and kodebudget!=\'UMUM\' order by tahunbudget desc';

#exit(mysql_error($conn));
($qThn = mysql_query($sThn)) || true;

while ($rThn = mysql_fetch_assoc($qThn)) {
	$optTahunBudgetHeader .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
}

$optBlok = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sBlok = 'select distinct kodeblok from ' . $dbname . '.bgt_blok where kodeblok like \'' . $_SESSION['empl']['lokasitugas'] . '%\'order by kodeblok asc';

#exit(mysql_error());
($qBlok = mysql_query($sBlok)) || true;

while ($rBlok = mysql_fetch_assoc($qBlok)) {
	$optBlok .= '<option value=\'' . $rBlok['kodeblok'] . '\'>' . $rBlok['kodeblok'] . '</option>';
}

$optAkun = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sAkun = 'select distinct a.noakun,b.namaakun from ' . $dbname . '.bgt_budget a' . "\r\n" . '        left join ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '        where tipebudget=\'ESTATE\' and kodebudget!=\'UMUM\' order by noakun asc';

exit(mysql_error($sAkun));
($qAkun = mysql_query($sAkun)) || true;

while ($rAkun = mysql_fetch_assoc($qAkun)) {
	$optAkun .= '<option value=\'' . $rAkun['noakun'] . '\'>' . $rAkun['noakun'] . '-' . $rAkun['namaakun'] . '</option>';
}

echo '<div><table><tr><td>' . $_SESSION['lang']['budgetyear'] . ': <select id=\'thnbudgetHeader\' style=\'width:150px;\' onchange=\'ubah_list()\'>' . $optTahunBudgetHeader . '</select></td>' . "\r\n" . '    <td>' . $_SESSION['lang']['blok'] . ':<select id=kdBlokCari style=\'width:150px;\' onchange=\'ubah_list()\'>' . $optBlok . '</select></td><td>' . $_SESSION['lang']['noakun'] . ':<select id=noakunCari style=\'width:150px;\' onchange=\'ubah_list()\'>' . $optAkun . '</select></td></tr></table></div>';
echo '<div id=\'listDatHeader2\'>';
echo '<script>dataHeader()</script></div>';
CLOSE_BOX();
echo '</div>';
echo '<div id=\'formIsian\' style=\'display:none;\'>';
OPEN_BOX();
$frm .= 0;
$frm .= 0;
$optKdbdgtM = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrgm = 'select kodebudget,nama from ' . $dbname . '.bgt_kode where substr(kodebudget,1,1)=\'M\' order by kodebudget asc';

#exit(mysql_error());
($qOrgm = mysql_query($sOrgm)) || true;

while ($rOrgm = mysql_fetch_assoc($qOrgm)) {
	$optKdbdgtM .= '<option value=\'' . $rOrgm['kodebudget'] . '\'>' . $rOrgm['kodebudget'] . ' [' . $rOrgm['nama'] . ']</option>';
}

$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$sOrgm = 'select kodebudget,nama from ' . $dbname . '.bgt_kode where kodebudget=\'TOOL\' order by kodebudget asc';

#exit(mysql_error());
($qOrgm = mysql_query($sOrgm)) || true;

while ($rOrgm = mysql_fetch_assoc($qOrgm)) {
	$optKdbdgtL .= '<option value=\'' . $rOrgm['kodebudget'] . '\'>' . $rOrgm['kodebudget'] . ' [' . $rOrgm['nama'] . ']</option>';
}

$frm .= 2;
$frm .= 2;
$frm .= 2;
$frm .= 2;
$frm .= 2;
$sOrgB = 'select kodebudget,nama from ' . $dbname . '.bgt_kode where kodebudget like \'%KONTRAK%\' order by nama asc';

#exit(mysql_error());
($qOrgB = mysql_query($sOrgB)) || true;

while ($rOrgB = mysql_fetch_assoc($qOrgB)) {
	$optKdbdgt_B .= '<option value=\'' . $rOrgB['kodebudget'] . '\'>' . $rOrgB['nama'] . '</option>';
}

$optAkun = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sJns = 'select noakun,namaakun from ' . $dbname . '.keu_5akun where detail=1 and tipeakun=\'BIAYA\' order by noakun asc';

#exit(mysql_error($conn));
($qJns = mysql_query($sJns)) || true;

while ($rJns = mysql_fetch_assoc($qJns)) {
	$optAkun .= '<option value=\'' . $rJns['noakun'] . '\'>' . $rJns['noakun'] . ' - [' . $rJns['namaakun'] . ']</option>';
}

$frm .= 3;
$frm .= 3;
$frm .= 3;
$frm .= 3;
$frm .= 3;
$sOrgv = 'select kodebudget,nama from ' . $dbname . '.bgt_kode where kodebudget like \'%VHC%\' order by nama asc';

#exit(mysql_error());
($qOrgv = mysql_query($sOrgv)) || true;

while ($rOrgv = mysql_fetch_assoc($qOrgv)) {
	$optKdbdgt_V .= '<option value=\'' . $rOrgv['kodebudget'] . '\'>' . $rOrgv['nama'] . '</option>';
}

$optAkun = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sJns = 'select noakun,namaakun from ' . $dbname . '.keu_5akun where detail=1 and tipeakun=\'BIAYA\' order by noakun asc';

#exit(mysql_error($conn));
($qJns = mysql_query($sJns)) || true;

while ($rJns = mysql_fetch_assoc($qJns)) {
	$optAkun .= '<option value=\'' . $rJns['noakun'] . '\'>' . $rJns['noakun'] . ' - [' . $rJns['namaakun'] . ']</option>';
}

$optVhc = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$frm .= 4;
$frm .= 4;
$frm .= 4;
$frm .= 4;
$frm .= 4;
$arrBln = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sept', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');
$frm .= 5;

foreach ($arrBln as $brsBulan => $listBln) {
	$frm .= 5;
}

$sNamaAkun58 = 'select distinct noakun,namaakun  from ' . $dbname . '.keu_5akun order by namaakun asc';

#exit(mysql_error());
($qNamaAkun58 = mysql_query($sNamaAkun58)) || true;

while ($rNamaAkun58 = mysql_fetch_assoc($qNamaAkun58)) {
	$namaAkun58[$rNamaAkun58['noakun']] = $rNamaAkun58['namaakun'];
}

$optNoakunData58 = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOptNoakun58 = 'select distinct noakun from ' . $dbname . '.bgt_budget where tipebudget=\'ESTATE\' and kodebudget!=\'UMUM\' and kodeorg like \'' . $_SESSION['empl']['lokasitugas'] . '%\' order by noakun asc';

exit(mysql_error($sOptNoakun58));
($qOptNoakun58 = mysql_query($sOptNoakun58)) || true;

while ($rOptNoakun58 = mysql_fetch_assoc($qOptNoakun58)) {
	$optNoakunData58 .= '<option value=\'' . $rOptNoakun58['noakun'] . '\'>' . $rOptNoakun58['noakun'] . '-' . $namaAkun58[$rOptNoakun58['noakun']] . '</option>';
}

$sAfd = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'AFDELING\' and kodeorganisasi like \'' . $_SESSION['empl']['lokasitugas'] . '%\'';

#exit(mysql_error($conn));
($qAfd = mysql_query($sAfd)) || true;

while ($rAfd = mysql_fetch_assoc($qAfd)) {
	$optAfdeling .= '<option value=\'' . $rAfd['kodeorganisasi'] . '\'>' . $rAfd['kodeorganisasi'] . '</option>';
}

$frm .= 5;
$frm .= 5;
$frm .= 5;

foreach ($arrBln as $brsBulan => $listBln) {
	$frm .= 5;
}

$frm .= 5;
$frm .= 5;
$hfrm[0] = $_SESSION['lang']['sdm'];
$hfrm[1] = $_SESSION['lang']['material'];
$hfrm[2] = $_SESSION['lang']['peralatan'];
$hfrm[3] = $_SESSION['lang']['kontrak'];
$hfrm[4] = $_SESSION['lang']['kndran'];
$hfrm[5] = 'Sebaran';
drawTab('FRM', $hfrm, $frm, 100, 1100);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
