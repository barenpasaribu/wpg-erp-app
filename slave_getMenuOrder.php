<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$parent = $_POST['parent'];
$sub = $_POST['sub'];

if ($sub == 'true') {
	$str = 'select * from ' . $dbname . '.menu ' . "\r\n\t" . '      where parent=' . $parent . ' order by urut';
}
else {
	$str = 'select * from ' . $dbname . '.menu ' . "\r\n\t" . '      where type=\'master\' order by urut';
}

$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
	echo ' Gagal, Menu ini tidak memiliki submenu';
}
else {
	echo '<br> ' . "\r\n\t\t" . '     ' . $_SESSION['lang']['updownmenuitem'] . "\r\n\t\t" . '     <table width=100% cellspacing=1 border=0 class=data>' . "\r\n" . '             <thead>' . "\r\n\t\t" . '     <tr>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['menuid'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['type'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['caption'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['action'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['order'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['move'] . '</td>' . "\r\n\t\t\t" . ' </tr>' . "\r\n\t\t\t" . ' </thead><tbody>';
	$max = mysql_num_rows($res);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;

		if ($bar->class == 'devider') {
			$bar->caption = '----------';
		}

		echo '<tr class=rowcontent>' . "\r\n\t\t" . '        <td class=firsttd id=orderid' . $no . '>' . $bar->id . '</td>' . "\r\n\t\t\t\t" . '<td id=ordertype' . $no . '>' . $bar->class . '</td>' . "\r\n\t\t" . '        <td id=ordercaption' . $no . '>' . $bar->caption . '</td>' . "\r\n\t\t\t\t" . '<td id=orderaction' . $no . '>' . $bar->action . '</td>' . "\r\n\t\t\t\t" . '<td id=orderurut' . $no . '>' . $bar->urut . '</td>' . "\r\n\t\t\t\t" . '<td>';

		if (1 < $max) {
			if ($no != $max) {
				echo '<img class=dellicon src=images/menu/arrow_57.gif title=\'Move down\' onclick=change(\'down\',\'' . $no . '\',\'' . $max . '\')>&nbsp &nbsp';
			}

			if (1 < $no) {
				echo '<img class=dellicon src=images/menu/arrow_58.gif title=\'Move up\' onclick=change(\'up\',\'' . $no . '\',\'' . $max . '\')>';
			}
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>' . "\r\n\t" . '        <br>';

	if (1 < $max) {
		echo '<input type=button class=mybutton value=\'' . $_SESSION['lang']['done'] . '\' onclick=closeOrderEditor()> ';
	}

	echo ' <input type=button class=mybutton value=\'' . $_SESSION['lang']['close'] . '\' onclick=closeOrderEditor()>';
}

?>
