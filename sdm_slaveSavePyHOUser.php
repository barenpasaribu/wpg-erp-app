<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$user = $_POST['user'];
$type = $_POST['type'];
$stra = 'insert into ' . $dbname . '.sdm_ho_payroll_user' . "\r\n\t\t\t" . '(uname,type) values(\'' . $user . '\',\'' . $type . '\')';

if (mysql_query($stra)) {
	$str = 'select * from ' . $dbname . '.sdm_ho_payroll_user order by uname';
	$res = mysql_query($str, $conn);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent><td class=fisttd>' . $no . '</td>' . "\r\n\t\t\t\t" . '      <td id=\'uname' . $no . '\'>' . $bar->uname . '</td>' . "\r\n\t\t\t\t" . '      <td>' . $bar->type . '</td>' . "\t\r\n\t\t\t\t\t" . '  <td align=center><img src=images/close.png  height=11px class=dellicon title=Delete  onclick="delPyUser(\'' . $bar->uname . '\')"></td>  ' . "\r\n\t\t\t\t\t" . '  </tr>';
	}
}
else {
	echo ' Error: ' . addslashes(mysql_error($conn));
}

?>
