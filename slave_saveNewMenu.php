<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$parent = $_POST['id_parent'];
$caption = $_POST['caption'];
$caption2 = $_POST['caption2'];
$caption3 = $_POST['caption3'];
$action = $_POST['action'];
$class = $_POST['class'];
$createFile = $_POST['create'];
$nex_parent = $parent;
$deep = 0;
$x = 0;

while ($x < 8) {
	$st = 'select parent from ' . $dbname . '.menu where id=' . $nex_parent;
	$re = mysql_query($st);

	if (0 < mysql_num_rows($re)) {
		$deep += 1;

		while ($ba = mysql_fetch_array($re)) {
			$nex_parent = $ba[0];
		}
	}
	else {
		break;
	}

	++$x;
}

if (6 < $deep) {
	echo ' Warning: Menu to deep(max 6 child)';
}
else {
	if ($parent == 0) {
		$type = 'master';
	}
	else {
		$type = 'list';
	}

	if ($class == 'devider') {
		$caption = '';
		$action = '';
	}

	if ($class == 'title') {
		$action = '';
	}

	$str = 'select max(urut) from ' . $dbname . '.menu where parent=' . $parent;
	$res = mysql_query($str);

	while ($bar = mysql_fetch_array($res)) {
		$urut = $bar[0];
	}

	if (!isset($urut)) {
		$urut = 0;
	}

	$nex_urut = $urut + 1;
	$str = 'insert into ' . $dbname . '.menu (' . "\r\n\t\t" . '  type,' . "\r\n\t\t" . '  class,' . "\r\n\t\t" . '  caption,' . "\r\n" . '                                          caption2,' . "\r\n" . '                                          caption3,' . "\r\n\t\t" . '  action,' . "\r\n\t\t" . '  parent,' . "\r\n\t\t" . '  urut,' . "\r\n\t\t" . '  hide,' . "\r\n\t\t" . '  lastuser)' . "\r\n\t\t\t" . '  values(' . "\r\n\t\t" . '      \'' . $type . '\',' . "\r\n\t\t\t" . '  \'' . $class . '\',' . "\r\n\t\t\t" . '  \'' . $caption . '\',' . "\r\n" . '                                                              \'' . $caption2 . '\',' . "\r\n" . '                                                              \'' . $caption3 . '\',     ' . "\r\n\t\t\t" . '  \'' . $action . '\',' . "\r\n\t\t\t" . '   ' . $parent . ',' . "\r\n\t\t\t" . '   ' . $nex_urut . ',' . "\r\n\t\t\t" . '  1,' . "\r\n\t\t\t" . '  \'' . $_SESSION['standard']['username'] . '\'' . "\r\n\t\t\t" . '  )';

	if (mysql_query($str)) {
		if ($parent != 0) {
			$str1 = 'update ' . $dbname . '.menu set type=\'parent\'' . "\r\n\t\t\t" . '        where id=' . $parent . ' and type=\'list\'';
			mysql_query($str1);
		}

		if ($createFile == 'yes') {
			$filename = $action . '.php';

			if (file_exists($filename)) {
			}
			else {
				$defaulContent = '<?//@antoniuslouis2016' . "\r\n" . '?>';
				$handle = fopen($filename, 'w');

				if (!fwrite($handle, $defaulContent)) {
				}

				fclose($handle);
			}
		}

		$str2 = 'select max(id) from ' . $dbname . '.menu';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_array($res2)) {
			$max = $bar2[0];
		}

		if (5 < $deep) {
			echo $max . ',stop';
		}
		else {
			echo $max . ',available';
		}
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

?>
