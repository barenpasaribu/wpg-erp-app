<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zConfig.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src='js/zMaster.js?v=".mt_rand()."'></script>".
	"<script language=javascript src='js/zTools.js?v=".mt_rand()."'></script>".
	"<script language=javascript src='js/setup_blok.js?v=".mt_rand()."'></script>".
	"<link rel=stylesheet type=text/css href=style/zTable.css>";
$abc="select distinct klasifikasitanah from setup_blok where not (klasifikasitanah is null) order by klasifikasitanah";
$optKlsTanah=array();
$res = mysql_query($abc);
while ($row = mysql_fetch_assoc($res)) {
	$optKlsTanah[$row['klasifikasitanah']]=$row['klasifikasitanah'];
}
$optJenisTanah=array();
$abc="select distinct kodetanah from setup_blok where not (kodetanah is null)  order by kodetanah";
$res = mysql_query($abc);
while ($row = mysql_fetch_assoc($res)) {
	$optJenisTanah[$row['kodetanah']]=$row['kodetanah'];
}

$optTopografi = makeOption($dbname, 'setup_topografi', 'topografi,keterangan');
$optOrg = array();
$optMonth = optionMonth('I', 'long');
$optBlokStat = getEnum($dbname, 'setup_blok', 'statusblok');
$optIP = getEnum($dbname, 'setup_blok', 'intiplasma');
$isIT = $_SESSION['empl']['bagian'];

if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
	$tmpOpt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'tipe=\'KEBUN\'');
}
else if ($_SESSION['empl']['tipelokasitugas'] == 'KEBUN') {
	$tmpOpt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'');
}
else {
	$tmpOpt = getOrgBelow($dbname, $_SESSION['empl']['lokasitugas'], false, 'kebunonly');
}

$sKebun = array('' => '');

foreach ($tmpOpt as $key => $row) {
	$sKebun[$key] = $row;
}

$optBibit = makeOption($dbname, 'setup_jenisbibit', 'jenisbibit,jenisbibit');
$searchEls = $_SESSION['lang']['kebun'] . ' ';
$searchEls .= makeElement('sKebun', 'select', '', array('onchange' => 'getAfdeling(this,\'sAfdeling\')', 'style' => 'width:300px'), $sKebun) . ' ';
$searchEls .= $_SESSION['lang']['afdeling'] . ' ';
$searchEls .= makeElement('sAfdeling', 'select', '', array('style' => 'width:300px'), array()) . ' ';
$searchEls .= makeElement('searchIt', 'button', $_SESSION['lang']['find'], array('onclick' => 'showData()')) . ' ';
echo '<fieldset id=\'search\' style=\'margin-bottom:10px;float:left;clear:both\'>';
echo '<legend><b>' . $_SESSION['lang']['searchdata'] . '</b></legend>';
echo $searchEls;
echo '</fieldset>';

if ($isIT == 'HO_ITGS') {
	$disNotIt = '';
}
else {
	$disNotIt = 'disabled';
}

echo '<div id=\'formBlok\' style=\'display:none;margin-bottom:10px;clear:both\'>';
$els = array();
$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', array('style' => 'width:300px'), $optOrg));
$els[] = array(makeElement('bloklama', 'label', $_SESSION['lang']['bloklama']), makeElement('bloklama', 'text', '', array('style' => 'width:300px')));
$els[] = array(makeElement('tahuntanam', 'label', $_SESSION['lang']['tahuntanam']), makeElement('tahuntanam', 'textnumber', '', array('style' => 'width:70px', 'maxlength' => '6', $disNotIt => $disNotIt)) . makeElement('tahuntanamCurr', 'hidden', ''));
$els[] = array(makeElement('luasareaproduktif', 'label', $_SESSION['lang']['luasareaproduktif']), makeElement('luasareaproduktif', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'this.value=_formatted(this)', $disNotIt => $disNotIt)) . ' Ha');
$els[] = array(makeElement('luasareanonproduktif', 'label', $_SESSION['lang']['luasareanonproduktif']), makeElement('luasareanonproduktif', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'readonly' => 'readonly', $disNotIt => $disNotIt)) . ' Ha');
$els[] = array(makeElement('jumlahpokok', 'label', $_SESSION['lang']['jumlahpokok']), makeElement('jumlahpokok', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt)));
$els[] = array(
	makeElement('jumlahpokoksisipan1', 'label', "Jumlah Pokok (Sisipan) 1"),
	makeElement('jumlahpokoksisipan1', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt)). '&nbsp;&nbsp;'.
	makeElement('tahunjumlahpokoksisipan1', 'label', "Tahun Jumlah Pokok (Sisipan) 1") . '&nbsp;&nbsp;'.
	makeElement('tahunjumlahpokoksisipan1', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt))
);
$els[] = array(
	makeElement('jumlahpokoksisipan2', 'label', "Jumlah Pokok (Sisipan) 2"),
	makeElement('jumlahpokoksisipan2', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt)). '&nbsp;&nbsp;'.
	makeElement('tahunjumlahpokoksisipan2', 'label', "Tahun Jumlah Pokok (Sisipan) 2") . '&nbsp;&nbsp;'.
	makeElement('tahunjumlahpokoksisipan2', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt))
);
$els[] = array(
	makeElement('jumlahpokoksisipan3', 'label', "Jumlah Pokok (Sisipan) 3"),
	makeElement('jumlahpokoksisipan3', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt)). '&nbsp;&nbsp;'.
	makeElement('tahunjumlahpokoksisipan3', 'label', "Tahun Jumlah Pokok (Sisipan) 3") . '&nbsp;&nbsp;'.
	makeElement('tahunjumlahpokoksisipan3', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt))
);
$els[] = array(makeElement('jumlahpokokabnormal', 'label', "Jumlah Pokok Abnormal"), makeElement('jumlahpokokabnormal', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt)));
$els[] = array(makeElement('jumlahpokokmati', 'label', "Jumlah Pokok Mati"), makeElement('jumlahpokokmati', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)')));
//$els[] = array(makeElement('jumlahpokoksisipan', 'label', "Jumlah Pokok (Sisipan)"), makeElement('jumlahpokoksisipan', 'textnumber', '0', array('style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', $disNotIt => $disNotIt)));
$els[] = array(makeElement('statusblok', 'label', $_SESSION['lang']['statusblok']), makeElement('statusblok', 'select', '', array('style' => 'width:100px'), $optBlokStat));
$els[] = array(
	makeElement('tahunmulaipanen', 'label', $_SESSION['lang']['mulaipanen']),
	makeElement('bulanmulaipanen', 'select', '', array('style' => 'width:300px'), $optMonth) . ' / ' .
	makeElement('tahunmulaipanen', 'textnumber', '', array('style' => 'width:90px', 'maxlength' => '4', 'onkeypress' => 'return angka_doang(event)')));
$els[] = array(makeElement('kodetanah', 'label', $_SESSION['lang']['kodetanah']), makeElement('kodetanah', 'select', '', array('style' => 'width:300px'), $optJenisTanah));
$els[] = array(makeElement('klasifikasitanah', 'label', $_SESSION['lang']['klasifikasitanah']), makeElement('klasifikasitanah', 'select', '', array('style' => 'width:300px'), $optKlsTanah));
$els[] = array(makeElement('topografi', 'label', $_SESSION['lang']['topografi']), makeElement('topografi', 'select', '', array('style' => 'width:300px'), $optTopografi));
$els[] = array(makeElement('intiplasma', 'label', $_SESSION['lang']['intiplasma']), makeElement('intiplasma', 'select', '', array('style' => 'width:300px'), $optIP));
$els[] = array(makeElement('jenisbibit', 'label', $_SESSION['lang']['jenisbibit']), makeElement('jenisbibit', 'select', '', array('style' => 'width:300px'), $optBibit));
$els[] = array(makeElement('tanggalpengakuan', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggalpengakuan', 'text', '', array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
$els[] = array(makeElement('cadangan', 'label', $_SESSION['lang']['cadangan']), makeElement('cadangan', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('okupasi', 'label', $_SESSION['lang']['okupasi']), makeElement('okupasi', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('rendahan', 'label', $_SESSION['lang']['rendahan']), makeElement('rendahan', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('sungai', 'label', $_SESSION['lang']['sungai']), makeElement('sungai', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('rumah', 'label', $_SESSION['lang']['rumah']), makeElement('rumah', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('kantor', 'label', $_SESSION['lang']['kantor']), makeElement('kantor', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('pabrik', 'label', $_SESSION['lang']['pabrik']), makeElement('pabrik', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('jalan', 'label', $_SESSION['lang']['jalan']), makeElement('jalan', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('kolam', 'label', $_SESSION['lang']['kolam']), makeElement('kolam', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$els[] = array(makeElement('umum', 'label', $_SESSION['lang']['umum']), makeElement('umum', 'textnumber', '0', array('style' => 'width:80px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'itungUnplan()')) . ' Ha');
$fieldStr = '##kodeorg##bloklama##tahuntanam##tahuntanamCurr##luasareaproduktif##luasareanonproduktif';
$fieldStr .= '##jumlahpokok##statusblok##bulanmulaipanen##tahunmulaipanen';
$fieldStr .= '##kodetanah##klasifikasitanah##topografi##intiplasma##jenisbibit##tanggalpengakuan';
$fieldStr .= '##cadangan##okupasi##rendahan##sungai##rumah##kantor##pabrik##jalan##kolam##umum';
$fieldStr .= '##jumlahpokoksisipan1##tahunjumlahpokoksisipan1##jumlahpokoksisipan2##tahunjumlahpokoksisipan2##jumlahpokoksisipan3##tahunjumlahpokoksisipan3##jumlahpokokabnormal';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));

if ($isIT == 'HO_ITGS') {
	$disabled = '';
}
else {
	$disabled = '##tahuntanam##tahuntanamCurr##luasareaproduktif##luasareanonproduktif##jumlahpokok';
} 

$els['btn'] = array(
	makeElement('add', 'btn', $_SESSION['lang']['save'], ['onclick' => "simpanDataBlok()"]).
	makeElement('cancel', 'btn', $_SESSION['lang']['cancel'], ['onclick' => "resetDataBlok()"]));

// $els['btn'] = array(genFormBtn($fieldStr, 'setup_blok', '##kodeorg##tahuntanam', 'setup_slave_blok_add', NULL, NULL, 'setup_slave_blok_edit', '##tahuntanamCurr' . $disabled, $disabled));
echo genElementMultiDim('Blok', $els, 2);
echo '</div>';
echo '<div id=\'blokTable\' style=\'float:left;clear:both;\'>';
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
