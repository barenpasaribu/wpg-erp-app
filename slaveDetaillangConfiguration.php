<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$langname = $_POST['langname'];
$search = '';
$limit = ' limit 100';

if (isset($_POST['findlang'])) {
	$limit = '';
	$search = ' where legend like \'%' . $_POST['findlang'] . '%\' or location like \'%' . $_POST['findlang'] . '%\' ';
}

$str = 'select idx,legend,location,' . $langname . ' from ' . $dbname . '.bahasa ' . $search . ' order by legend ' . $limit;

if ($res = mysql_query($str)) {
	echo '<table class=data border=0 cellspacing=1>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader><td>' . $_SESSION['lang']['legend'] . '</td><td>' . $_SESSION['lang']['location'] . '</td><td>' . $_SESSION['lang']['text'] . '</td><td></td></tr>' . "\r\n\t\t" . ' </thead>' . "\r\n" . '         <tbody>  ';

	while ($bar = mysql_fetch_assoc($res)) {
		echo '<tr class=rowcontent><td>' . "\r\n\t\t\t" . '            ' . $bar['legend'] . "\r\n" . '                     </td>' . "\r\n\t\t\t\t\t" . ' <td>' . "\r\n" . '                        <input type=text class=myinputtext id=\'' . $bar['idx'] . 'location\' value=\'' . $bar['location'] . '\' onkeypress="return tanpa_kutip(event);" size=35>' . "\r\n\t\t\t\t\t" . ' </td>' . "\r\n\t\t\t\t\t" . ' <td><input type=text class=myinputtext id=\'' . $bar['idx'] . 'caption\' value=\'' . $bar[$_POST['langname']] . '\'  onkeypress="return tanpa_kutip(event);" size=65></td>' . "\r\n\t\t\t\t" . '     <td><button class=mybutton onclick="updateCaption(\'' . $bar['idx'] . '\',\'' . $bar['idx'] . 'location\',\'' . $bar['idx'] . 'caption\',\'' . $_POST['langname'] . '\')">' . $_SESSION['lang']['save'] . '</button></td>' . "\r\n\t\t\t\t" . ' </tr>';
	}

	echo '</tbody>' . "\r\n\t" . '     <tfoot></tfoot>' . "\r\n\t\t" . ' </table>';
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
