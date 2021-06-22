<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$userid = $_POST['userid'];
$bank = $_POST['bank'];
$bankac = $_POST['bankac'];
$jms = $_POST['jms'];
$firstperiod = $_POST['firstperiod'];
$firstvol = $_POST['firstvol'];
$lastperiod = $_POST['lastperiod'];
$lastvol = $_POST['lastvol'];
$jmsperiod = $_POST['jmsperiod'];
$stra = 'update ' . $dbname . '.sdm_ho_employee set' . "\r\n\t" . '        bank=\'' . $bank . '\',' . "\r\n\t\t\t" . 'bankaccount=\'' . $bankac . '\',' . "\r\n\t\t\t" . 'nojms=\'' . $jms . '\',' . "\r\n\t\t\t" . 'firstpayment=\'' . $firstperiod . '\',' . "\r\n\t\t\t" . 'firstvol=' . $firstvol . ',' . "\r\n\t\t\t" . 'lastpayment=\'' . $lastperiod . '\',' . "\r\n\t\t\t" . 'lastvol=' . $lastvol . ',' . "\r\n\t\t\t" . 'jmsstart=\'' . $jmsperiod . '\'' . "\t\t\t\r\n\t\t\t" . 'where karyawanid=' . $userid;

if (mysql_query($stra)) {
}
else {
	echo ' Error: ' . addslashes(mysql_error($conn));
}

?>
