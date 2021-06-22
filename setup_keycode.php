<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
include 'lib/jAddition.php';
OPEN_BOX();
echo '<script type="text/javascript" src="js/setup_keycode.js" /></script>' . "\r\n\r\n" . '<fieldset>' . "\r\n\t" . '<legend>';
echo $_SESSION['lang']['setupKeycode'];
echo '</legend>' . "\r\n\t" . '<table cellspacing="1" border="0">' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['keycode'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t\t" . '<td><input type="text" id="keyCode" name="keyCode" onKeyPress="return tanpa_kutip(event);" class="myinputtext"  style="width:150px;"/>' . "\r\n" . '            <input type="hidden" id="oldCode" name="oldCode"  />' . "\r\n\t\t\t\t" . '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['keterangan'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t\t" . '<td><input type="text" id="ket"  name="ket" onKeyPress="return tanpa_kutip(event);" class="myinputtext"  style="width:150px;"/></td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t" . '<input type="hidden" id="method" value="insert" />' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td colspan="3">' . "\r\n\t\t\t" . '<button class="mybutton" onclick="smpnKeycode()">';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n\t\t\t" . '<button class="mybutton" onclick="cancelKeycode()">';
echo $_SESSION['lang']['cancel'];
echo '</button></td>' . "\r\n\t\t" . '</tr>' . "\r\n\t" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n\t" . ' <table class="sortable" cellspacing="1" border="0">' . "\r\n\t" . ' <thead>' . "\r\n\t" . ' <tr class=rowheader>' . "\r\n\t" . ' <td>No.</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['keycode'];
echo '</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['keterangan'];
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
$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_keycode  order by code desc';

#exit(mysql_error());
($query2 = mysql_query($ql2)) || true;

while ($jsl = mysql_fetch_object($query2)) {
	$jlhbrs = $jsl->jmlhrow;
}

$str = 'select * from ' . $dbname . '.setup_keycode order by code desc limit ' . $offset . ',' . $limit . '';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t" . '<td>' . $no . '</td>' . "\r\n\t" . '<td id=\'nmorg_' . $no . '\'>' . $bar->code . '</td>' . "\r\n\t" . '<td id=\'kpsits_' . $no . '\'>' . $bar->keterangan . '</td>' . "\r\n\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->code . '\',\'' . $bar->keterangan . '\');"></td>' . "\r\n\t" . '</tr>';
	}

	echo ' ' . "\r\n\t" . '</tr><tr class=rowheader><td colspan=3 align=center>' . "\r\n\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n\t" . '<br />' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '</td></tr>';
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

echo "\t" . '  </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>' . "\r\n" . '</fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
