<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
include 'lib/jAddition.php';
OPEN_BOX();
echo '<script type="text/javascript" src="js/setup_mappremi.js" /></script>' . "\r\n";
$optKeycode = '';
$sKey = 'select code from ' . $dbname . '.setup_keycode order by code asc';

#exit(mysql_error());
($qKey = mysql_query($sKey)) || true;

while ($rKey = mysql_fetch_assoc($qKey)) {
	$optKeycode .= '<option value=' . $rKey['code'] . ' title=' . $rKey['keterangan'] . '>' . $rKey['code'] . '</option>';
}

$soptOrg = '';
$sorg = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';

#exit(mysql_error());
($qorg = mysql_query($sorg)) || true;
global $kd_org;

while ($rorg = mysql_fetch_assoc($qorg)) {
	$kd_org = $rorg['kodeorganisasi'];
	$optOrg .= '<option \'' . ($rorg['kodeorganisasi'] == $rest['kodeorganisasi'] ? 'selected=selected' : '') . '\' value=' . $rorg['kodeorganisasi'] . ' >' . $rorg['namaorganisasi'] . '</option>';
}

$arrTipe = getEnum($dbname, 'setup_mappremi', 'tipepremi');
$optTipe = '';

foreach ($arrTipe as $isi) {
	$optTipe .= '<option value=' . $isi . '>' . $isi . '</option>';
}

echo '<fieldset>' . "\r\n\t" . '<legend>';
echo $_SESSION['lang']['setupKeycode'];
echo '</legend>' . "\r\n\t" . '<table cellspacing="1" border="0">' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['kodeorg'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t\t" . '<td><select id="optOrg" name="optOrg"  style="width:150px;">';
echo $optOrg;
echo '</select></td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['tipepremi'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t\t" . '<td><select id="tipePremi" style="width:150px;">';
echo $optTipe;
echo '</select><input type="hidden" id="oldtipePremi" name="oldtipePremi" /></td>' . "\r\n\t\t" . '</tr>' . "\r\n" . '        <tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['keycode'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t\t" . '<td><select id="keyCode" name="keyCode">';
echo $optKeycode;
echo '</select><input type="hidden" id="oldKey" name="oldKey" /></td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t" . '<input type="hidden" id="method" name="method" value="insert" />' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td colspan="3">' . "\r\n\t\t\t" . '<button class="mybutton" onclick="smpnKeycode()">';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n\t\t\t" . '<button class="mybutton" onclick="cancelKeycode()">';
echo $_SESSION['lang']['cancel'];
echo '</button></td>' . "\r\n\t\t" . '</tr>' . "\r\n\t" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n\t" . ' <table class="sortable" cellspacing="1" border="0">' . "\r\n\t" . ' <thead>' . "\r\n\t" . ' <tr class=rowheader>' . "\r\n\t" . ' <td>No.</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['kodeorg'];
echo '</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['tipepremi'];
echo '</td> ' . "\r\n" . '     <td>';
echo $_SESSION['lang']['keycode'];
echo '</td> ' . "\r\n\t" . ' <td>Action</td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id="container">' . "\r\n\t" . ' ';
$limit = 10;
$page = 0;

if (isset($_POST['page'])) {
	$page = $_POST['page'];

	if ($page < 0) {
		$page = 0;
	}
}

$offset = $page * $limit;
$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_mappremi  order by kodeorg,keycode desc limit ' . $offset . ',' . $limit . '';

#exit(mysql_error());
($query2 = mysql_query($ql2)) || true;

while ($jsl = mysql_fetch_object($query2)) {
	$jlhbrs = $jsl->jmlhrow;
}

$str = 'select * from ' . $dbname . '.setup_mappremi order by kodeorg,keycode desc';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$sPt = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeorg . '\'';

		#exit(mysql_error());
		($qPt = mysql_query($sPt)) || true;
		$rOrg = mysql_fetch_assoc($qPt);
		$no += 1;
		echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t" . '<td>' . $no . '</td>' . "\r\n\t" . '<td id=\'nmorg_' . $no . '\'>' . $rOrg['namaorganisasi'] . '</td>' . "\r\n\t" . '<td id=\'kpsits_' . $no . '\'>' . $bar->tipepremi . '</td>' . "\r\n\t" . '<td id=\'kpsits_' . $no . '\'>' . $bar->keycode . '</td>' . "\r\n\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kodeorg . '\',\'' . $bar->tipepremi . '\',\'' . $bar->keycode . '\');"><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delCode(\'' . $bar->kodeorg . '\',\'' . $bar->tipepremi . '\',\'' . $bar->keycode . '\');"></td>' . "\r\n\t" . '</tr>';
	}

	echo ' ' . "\r\n\t" . '</tr><tr class=rowheader><td colspan=5 align=center>' . "\r\n\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n\t" . '<br />' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '</td></tr>';
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

echo "\t" . '  </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>' . "\r\n" . '</fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
