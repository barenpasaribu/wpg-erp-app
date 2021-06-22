<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$namapasar = (isset($_POST['namapasar']) ? $_POST['namapasar'] : '');
$id = (isset($_POST['id']) ? $_POST['id'] : '');
$method = $_POST['method'];

switch ($method) {
case 'insert':
	$str = 'insert into ' . $dbname . '.pmn_5pasar (namapasar)' . "\n\t" . '      values(\'' . $namapasar . '\')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'update':
	$str = 'update ' . $dbname . '.pmn_5pasar set namapasar=\'' . $namapasar . '\'' . "\n\t" . 'where id=' . $id;

	if (!mysql_query($str)) {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

$str1 = $_POST . '.pmn_5pasar' . "\n" . '        order by namapasar';

if ($res1 = mysql_query($str1)) {
	while ($bar1 = mysql_fetch_object($res1)) {
		echo '<tr class=rowcontent>' . "\n\t\t\t" . '<td align=center>' . $bar1->namapasar . '</td>' . "\n\t\t\t" . '<td>' . "\n\t\t\t\t" . '<img src=images/skyblue/edit.png class=zImgBtn  caption=\'Edit\' onclick="editField(' . $bar1->id . ',\'' . $bar1->namapasar . '\');">' . "\n\t\t\t" . '</td></tr>';
	}
}

?>
