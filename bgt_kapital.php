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
echo '";' . "\r\n" . '</script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script language="javascript" src="js/zTools.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/bgt_kapital.js"></script>' . "\r\n" . '<script>' . "\r\n" . 'dataKdvhc="';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . '</script>' . "\r\n";
$optBlok = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg2 = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\' order by namaorganisasi asc';

#exit(mysql_error());
($qOrg2 = mysql_query($sOrg2)) || true;

while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
	$optBlok .= '<option value=' . $rOrg2['kodeorganisasi'] . '>' . $rOrg2['namaorganisasi'] . '</option>';
}

$optJns = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

if ($_SESSION['language'] == 'EN') {
	$dd = 'namatipe1 as namatipe';
}
else {
	$dd = 'namatipe as namatipe';
}

$sJns = 'select kodetipe,' . $dd . ' from ' . $dbname . '.sdm_5tipeasset order by namatipe';

#exit(mysql_error($conn));
($qJns = mysql_query($sJns)) || true;

while ($rJns = mysql_fetch_assoc($qJns)) {
	$optJns .= '<option value=\'' . $rJns['kodetipe'] . '\'>' . $rJns['namatipe'] . '</option>';
}

$optlokasi = '<option value=\'\'></option>';
$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where kodeorganisasi like \'' . $_SESSION['empl']['lokasitugas'] . '%\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optlokasi .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

OPEN_BOX('', '<b>' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['kapital'] . '</b>');
echo '<br /><br /><fieldset style=\'float:left;\'><legend>' . $_SESSION['lang']['entryForm'] . '</legend> <table border=0 cellpadding=1 cellspacing=1>';
echo '<tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'tahunbudget\' style=\'width:150px;\' maxlength=\'4\' onkeypress=\'return angka_doang(event)\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['unit'] . '</td><td><select style=\'width:150px;\' id=\'kodeorg\' >' . $optBlok . '</select></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['jnsKapital'] . '</td><td><select style=\'width:150px;\' id=\'jeniskapital\'>' . $optJns . '</select></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['lokasi'] . '</td><td><select id=lokasi>' . $optlokasi . '</select></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['keterangan'] . '</td><td><input type=\'text\' maxlength=\'45\' onkeypress=\'return tanpa_kutip(event)\' id=\'keterangan\' class=\'myinputtext\'style=\'width:150px;\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['jumlah'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'jumlah\' style=\'width:150px;\'  onkeypress=\'return angka_doang(event)\' onblur=\'kaliKan()\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['hargasatuan'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'harga\' style=\'width:150px;\' onkeypress=\'return angka_doang(event)\' onblur=\'kaliKan()\' /></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['total'] . '</td><td><input type=\'text\' class=\'myinputtextnumber\' id=\'totalrp\' style=\'width:150px;\' readonly/></td></tr>';
echo '<tr><td colspan=\'2\'><button class="mybutton"  id="saveData" onclick=\'saveHeader()\'>' . $_SESSION['lang']['save'] . '</button></td></tr>';
echo '</table></fieldset>';
CLOSE_BOX();
echo '<div id=\'formIsian\' style=\'display:block;\'>';
OPEN_BOX();
$frm .= 0;
$str = 'select a.*,b.' . $dd . ',' . "\r\n" . '            (a.k01+a.k02+a.k03+a.k04+a.k05+a.k06+a.k07+a.k08+a.k09+a.k10+a.k11+a.k12) as sebaran' . "\r\n" . '            from ' . $dbname . '.bgt_kapital a left join' . "\r\n" . '           ' . $dbname . '.sdm_5tipeasset b on a.jeniskapital=b.kodetipe' . "\r\n" . '           where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '           order by tahunbudget desc limit 100';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$bar->tutup == 0 ? $rtp = ' title="Sebaran" onclick="sebaran(' . $bar->kunci . ',event)" style=\'cursor:pointer;\'' : $rtp = '';
	$no += 1;
	$frm .= 0;

	if (round($bar->sebaran) < round($bar->hargatotal)) {
		$frm .= 0;
	}
	else {
		$frm .= 0;
	}

	if ($bar->tutup == 1) {
		$frm .= 0;
	}
	else {
		$frm .= 0;
	}

	$frm .= 0;
}

$frm .= 0;
$frm .= 1;
$frm .= 1;
$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_kapital where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\' and tutup=0 order by tahunbudget desc';
$optThnTtp = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optThnTtp .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$frm .= 2;
$frm .= 2;
$frm .= 2;
$hfrm[0] = $_SESSION['lang']['list'];
$hfrm[1] = $_SESSION['lang']['sebaran'];
$hfrm[2] = $_SESSION['lang']['tutup'];
drawTab('FRM', $hfrm, $frm, 100, 1000);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
