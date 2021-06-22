<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['form'] . '</b>');
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/pmn_5kodePengenaanPajak.js"></script>' . "\r\n" . '<input type="hidden" id="proses" name="proses" value="insert"  />' . "\r\n\r\n" . '<div id="tambah">' . "\r\n" . '<fieldset style=\'float:left;\'>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['form'];
echo '</legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '    <tr>' . "\r\n" . '        <td>';
echo $_SESSION['lang']['kodeabs'];
echo '</td><td>:</td>' . "\r\n" . '        <td><input type=\'text\' class=\'myinputtext\' id=\'kode\'  size=\'10\' maxlength=\'35\' style="width:200px;" /></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '        <td>';
echo $_SESSION['lang']['keterangan'];
echo '</td><td>:</td>' . "\r\n" . '        <td><input type=\'text\' class=\'myinputtext\' id=\'nama\' onkeypress="return tanpa_kutip();"  size=\'10\' style="width:200px;" /></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td colspan="3" id="tmblHeader">' . "\r\n" . '        <button class=mybutton id=saveForm onclick=saveForm()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '        <button class=mybutton id=cancelForm onclick=cancelForm()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '    </td>' . "\r\n" . '    </tr>' . "\r\n" . '</table><input type="hidden" id="hiddenz" name="hiddenz" />' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset style=\'float:left;\'>' . "\r\n" . '    <legend>';
echo $_SESSION['lang']['list'];
echo '</legend> ' . "\r\n" . '    <table cellspacing="1" border="0" class="sortable">' . "\r\n" . '        <thead>' . "\r\n" . '            <tr class="rowheader">' . "\r\n" . '            <td align="center">No.</td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['kodeabs'];
echo '</td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['keterangan'];
echo '</td>' . "\r\n" . '            <td colspan="3" align="center">';
echo $_SESSION['lang']['action'];
echo '</td>' . "\r\n" . '            </tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody id="contain">' . "\r\n" . '        ';
$limit = 10;
$page = 0;

if (isset($_POST['page'])) {
	$page = $_POST['page'];

	if ($page < 0) {
		$page = 0;
	}
}

$offset = $page * $limit;
$sCount = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_5fakturkode order by kode asc';

#exit(mysql_error());
($qCount = mysql_query($sCount)) || true;

while ($rCount = mysql_fetch_object($qCount)) {
	$jmlbrs = $rCount->jmlhrow;
}

$sShow = 'select * from ' . $dbname . '.pmn_5fakturkode order by kode asc limit ' . $offset . ',' . $limit . ' ';

#exit(mysql_error());
($qShow = mysql_query($sShow)) || true;

while ($row = mysql_fetch_assoc($qShow)) {
	$no += 1;
	echo '<script>loadNData()</script>';
	echo '<td><img src=images/edit.png class=resicon  title=\'Edit\' onclick="editRow(\'' . $row['kode'] . '\',\'' . $row['nama'] . '\');" ></td>';
	echo '<td><img src=images/delete1.jpg class=resicon  title=\'Delete\' onclick="delData(\'' . $row['kode'] . '\',\'' . $row['nama'] . '\')></td></tr>';
}

echo '<tr class=rowheader><td colspan=5 align=center>' . "\r\n" . '                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jmlbrs . '<br />' . "\r\n" . '                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                </td>' . "\r\n" . '                </tr>';
echo "\r\n" . '        </tbody>' . "\r\n" . '    </table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();

?>
