<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=\'js/zMaster.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zSearch.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/setup_kegiatan.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '  ' . "\r\n";
$where = '`detail`=1';
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $where, '2', true);
$whereOrg = 'tipe=\'HOLDING\' and induk is null or induk = \'\'';
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '2');

if ($_SESSION['language'] == 'EN') {
	$optKlpKeg = makeOption($dbname, 'setup_klpkegiatan', 'kodeklp,namakelompok1');
}
else {
	$optKlpKeg = makeOption($dbname, 'setup_klpkegiatan', 'kodeklp,namakelompok');
}

echo '<div style=\'margin-bottom:30px\'>';
$els = array();
$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', array('style' => 'width:250px'), $optOrg));
$els[] = array(makeElement('noakun', 'label', $_SESSION['lang']['noakun']), makeElement('noakun', 'select', '', array('style' => 'width:250px', 'onchange' => 'ambilkegiatan()'), $optAkun));
$els[] = array(makeElement('kodekegiatan', 'label', $_SESSION['lang']['kodekegiatan']), makeElement('kodekegiatan', 'text', '', array('style' => 'width:60px', 'maxlength' => '9')));
$els[] = array(makeElement('namakegiatan', 'label', $_SESSION['lang']['namakegiatan']), makeElement('namakegiatan', 'text', '', array('style' => 'width:250px', 'maxlength' => '80')));
$els[] = array(makeElement('namakegiatan1', 'label', $_SESSION['lang']['namakegiatan'] . '(EN)'), makeElement('namakegiatan1', 'text', '', array('style' => 'width:250px', 'maxlength' => '80')));
$els[] = array(makeElement('kelompok', 'label', $_SESSION['lang']['kelompok']), makeElement('kelompok', 'select', '', array('style' => 'width:250px'), $optKlpKeg));
$els[] = array(makeElement('satuan', 'label', $_SESSION['lang']['satuan']), makeElement('satuan', 'text', '', array('style' => 'width:250px', 'maxlength' => '8')));
$fieldStr = '##kodeorg##kodekegiatan##namakegiatan##namakegiatan1##kelompok##satuan##noakun';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = array(genFormBtn($fieldStr, 'setup_kegiatan', '##kodekegiatan##kodeorg##kelompok'));
echo genElTitle($_SESSION['lang']['kegiatan'], $els);
echo '</div>';
$table = 'setup_kegiatan';
$query = 'select * from ' . $dbname . '.' . $table;
$res = mysql_query($query);
$j = mysql_num_fields($res);
$i = 0;
$field = array();
$fieldStr = '';
$primary = array();
$primaryStr = '';

while ($i < $j) {
	$meta = mysql_fetch_field($res, $i);
	$field[] = strtolower($meta->name);
	$fieldStr .= '##' . strtolower($meta->name);

	if ($meta->primary_key == '1') {
		$primary[] = strtolower($meta->name);
		$primaryStr .= '##' . strtolower($meta->name);
	}

	++$i;
}

$fForm = $field;
$result = array();

while ($bar = mysql_fetch_assoc($res)) {
	$result[] = $bar;
}

$tables = '<fieldset><legend><b>' . $_SESSION['lang']['list'] . ' : ' . $table . '</b></legend>';
$tables .= '<img src=\'images/pdf.jpg\' title=\'PDF Format\'' . "\r\n" . '  style=\'width:20px;height:20px;cursor:pointer\' onclick="masterPDF(\'' . $table . '\',\'*\',null,\'setup_slave_kegiatan_pdf\',event)">&nbsp;';
$tables .= '<img src=\'images/printer.png\' title=\'Print Page\'' . "\r\n" . '  style=\'width:20px;height:20px;cursor:pointer\' onclick=\'javascript:print()\'>';
$tables .= '<div style=\'height:170px;overflow:auto\'>';
$tables .= '<table id=\'masterTable\' class=\'sortable\' cellspacing=\'1\' border=\'0\'>';
$tables .= '<thead><tr class=\'rowheader\'>';

foreach ($field as $hName) {
	$tables .= '<td>' . $_SESSION['lang'][$hName] . '</td>';
}

$tables .= '<td colspan=\'3\'></td>';
$tables .= '</tr></thead>';
$tables .= '<tbody id=\'mTabBody\'>';
$i = 0;

foreach ($result as $row) {
	$tables .= '<tr id=\'tr_' . $i . '\' class=\'rowcontent\'>';
	$tmpVal = '';
	$tmpKey = '';
	$j = 0;

	foreach ($row as $b => $c) {
		$tmpC = explode('-', $c);

		if (count($tmpC) == 3) {
			$c = $tmpC[2] . '-' . $tmpC[1] . '-' . $tmpC[0];
		}

		$tables .= '<td id=\'' . $fForm[$j] . '_' . $i . '\' value=\'' . $c . '\'>' . $c . '</td>';
		$tmpVal .= '##' . $c;

		if (in_array($fForm[$j], $primary)) {
			$tmpKey .= '##' . $c;
		}

		++$j;
	}

	$tables .= '<td><img id=\'editRow' . $i . '\' title=\'Edit\' onclick="editRow(' . $i . ',\'' . $fieldStr . '\',\'' . $tmpVal . '\')"' . "\r\n" . '    class=\'zImgBtn\' src=\'images/001_45.png\' /></td>';
	$tables .= '<td><img id=\'delRow' . $i . '\' title=\'Hapus\' onclick="delRow(' . $i . ',\'' . $primaryStr . '\',\'' . $tmpKey . '\',null,\'' . $table . '\')"' . "\r\n" . '    class=\'zImgBtn\' src=\'images/delete_32.png\' /></td>';
	$tables .= '<td><img id=\'norma' . $i . '\' title=\'Edit Norma\' onclick="showNorma(' . $i . ',\'' . $primaryStr . '##namakegiatan##satuan\',event)"' . "\r\n" . '    class=\'zImgBtn\' src=\'images/application/application_view_xp.png\' /></td>';
	$tables .= '</tr>';
	++$i;
}

$tables .= '</tbody>';
$tables .= '<tfoot></tfoot>';
$tables .= '</table></div></fieldset>';
echo '<div style=\'clear:both;float:left\'>';
echo $tables;
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
