<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$uname = trim($_POST['uname']);
$password = $_POST['password'];
$sendmail = $_POST['sendmail'];
$userid = $_POST['userid'];

if ($sendmail == 1) {
	$email = getUserEmail($userid, $conn);
}
else {
	$email = '';
}

$str = 'update ' . $dbname . '.user' . "\r\n" . '              set password=MD5(\'' . $password . '\'),' . "\r\n" . '                  lastuser=\'' . $_SESSION['standard']['username'] . '\' ' . "\r\n" . '                  where namauser=\'' . $uname . '\'';

if (mysql_query($str)) {
	if ($email != '') {
		$subject = 'Your Password Has been reset by Administrator';
		$body = '<html><head></head><body>' . "\r\n" . '                            Dear ' . $uname . ',<br><br>' . "\r\n" . '                                  Here is your new password:' . "\r\n" . '                                          <table>' . "\r\n" . '                                          <tr><td>UserName</td><td>:' . $uname . '</td></tr>' . "\r\n" . '                                          <tr><td>NewPassword</td><td>:<b>' . $password . '</b></td></tr>' . "\r\n" . '                                          </table><br>' . "\r\n" . '                                          Please maintain your password periodically.' . "\r\n" . '                                          <br>' . "\r\n" . '                                          Regards,' . "\r\n" . '                                          System, at ' . date('d-m-YYY H:i:s') . "\r\n" . '                               </body></html>';
		$to = $email;

		if ($to != '') {
			$kirim = kirimEmailWindows($to, $subject, $body);
		}

		if ($kirim == 1) {
			echo "\n" . 'An announcement email has been sent to user.';
		}
		else {
			echo "\n" . 'But, An announcement email was failed to send:' . $kirim;
		}
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
