<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
echo open_body();
require_once 'master_mainMenu.php';
OPEN_BOX('', '<b>Material Group List & Material List</b><br /><br />');
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n\r\n\r\n";
$optB = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$i = 'select * from ' . $dbname . '.log_5klbarang order by kelompok ';

#exit(mysql_error($conn));
($n = mysql_query($i)) || true;

while ($d = mysql_fetch_assoc($n)) {
	$optB .= '<option value=\'' . $d['kode'] . '\'>' . $d['kelompok'] . '</option>';
}

$arr2 = '##kel';
echo '<fieldset style=\'float:left;\'>' . "\r\n\t\t" . '<legend>Kelompok Barang</legend>' . "\r\n\t\t\t" . '<table border=0 cellpadding=1 cellspacing=1>' . "\r\n\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t" . '<td colspan=4>' . "\r\n\t\t\t\t\t" . '<button onclick=zPreview(\'log_slave_2klbarang\',\'' . $arr . '\',\'printContainer\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['preview'] . '</button>' . "\r\n\t\t\t\t\t" . '<button onclick=zExcel(event,\'log_slave_2klbarang.php\',\'' . $arr . '\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['excel'] . '</button>' . "\r\n\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t" . '</table>' . "\r\n\t" . '  </fieldset>';
echo '<fieldset style=\'float:left;\'>' . "\r\n\t\t" . '<legend>Kelompok Barang</legend>' . "\r\n\t\t\t" . '<table border=0 cellpadding=1 cellspacing=1>' . "\r\n\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t" . '<td>' . $_SESSION['lang']['kelompokbarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t" . '<td><select id=kel style="width:150px;">' . $optB . '</select></td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t" . '<td colspan=4>' . "\r\n\t\t\t\t\t" . '<button onclick=zPreview(\'log_slave_2barang\',\'' . $arr2 . '\',\'printContainer\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['preview'] . '</button>' . "\r\n\t\t\t\t\t" . '<button onclick=zExcel(event,\'log_slave_2barang.php\',\'' . $arr2 . '\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['excel'] . '</button>' . "\r\n\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t" . '</table>' . "\r\n\t\t" . '</fieldset>';
CLOSE_BOX();
echo "\r\n\r\n";
OPEN_BOX();
echo "\r\n" . '<fieldset style=\'clear:both\'><legend><b>' . $_SESSION['lang']['printArea'] . '</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:400px;max-width:1220px\'; >' . "\r\n" . '</div></fieldset>';
CLOSE_BOX();
echo close_body();

?>
