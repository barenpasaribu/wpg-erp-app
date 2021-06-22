<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$unit = $_POST['unit'];
$unitbawah = $_POST['unitbawah'];
$desa = $_POST['desa'];
$kecamatan = $_POST['kecamatan'];
$kabupaten = $_POST['kabupaten'];
$method = $_POST['method'];

if ($method == '') {
	$method = $_GET['method'];
	$unitbawah = $_GET['unitbawah'];
}

switch ($method) {
case 'excel':
	$stream = '';
	$str1 = 'select * from ' . $dbname . '.pad_5desa where unit like \'' . $unitbawah . '%\' order by namadesa';

	if ($res1 = mysql_query($str1)) {
		$stream .= '<table class=sortable cellspacing=1 border=1 style=\'width:500px;\'>' . "\r\n" . '    <thead><tr bgcolor=\'#dedede\'>' . "\r\n" . '        <td style=\'width:150px;\'>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '        <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['kecamatan'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kabupaten'] . '</td>    ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>';

		while ($bar1 = mysql_fetch_object($res1)) {
			$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td align=center>' . $bar1->unit . '</td>' . "\r\n" . '        <td>' . $bar1->namadesa . '</td>' . "\r\n" . '        <td>' . $bar1->kecamatan . '</td>' . "\r\n" . '        <td>' . $bar1->kabupaten . '</td>    ' . "\r\n" . '        </tr>';
		}

		$stream .= "\t" . ' ' . "\r\n" . '    </tbody>' . "\r\n" . '    <tfoot>' . "\r\n" . '    </tfoot>' . "\r\n" . '    </table><br>';
	}

	$stream .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$qwe = date('YmdHms');
	$nop_ = 'Daftar_Desa_' . $unitbawah . ' ' . $qwe;

	if (0 < strlen($stream)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $stream);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
	}

	exit();
	break;

case 'update':
	$str = 'update ' . $dbname . '.pad_5desa set unit=\'' . $unit . '\',' . "\r\n" . '                           kecamatan=\'' . $kecamatan . '\',' . "\r\n" . '                           kabupaten=\'' . $kabupaten . '\'' . "\r\n" . '               where namadesa=\'' . $desa . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'insert':
	$str = 'insert into ' . $dbname . '.pad_5desa (namadesa,unit,kecamatan,kabupaten)' . "\r\n" . '              values(\'' . $desa . '\',\'' . $unit . '\',\'' . $kecamatan . '\',\'' . $kabupaten . '\')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.pad_5desa' . "\r\n" . '        where namadesa=\'' . $desa . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;
}

$str1 = ($unit = $_POST['unit']) . '.pad_5desa where unit like \'' . $unitbawah . '%\' order by namadesa';

if ($res1 = mysql_query($str1)) {
	echo '<table class=sortable cellspacing=1 border=0 style=\'width:500px;\'>' . "\r\n" . '     <thead><tr class=rowheader>' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['kecamatan'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kabupaten'] . '</td>    ' . "\r\n" . '                <td style=\'width:30px;\'>*</td></tr>    ' . "\r\n" . '      </thead>' . "\r\n" . '      <tbody>';

	while ($bar1 = mysql_fetch_object($res1)) {
		echo '<tr class=rowcontent>' . "\r\n" . '                          <td align=center>' . $bar1->unit . '</td>' . "\r\n" . '                           <td>' . $bar1->namadesa . '</td>' . "\r\n" . '                           <td>' . $bar1->kecamatan . '</td>' . "\r\n" . '                           <td>' . $bar1->kabupaten . '</td>    ' . "\r\n" . '                           <td><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->unit . '\',\'' . $bar1->namadesa . '\',\'' . $bar1->kecamatan . '\',\'' . $bar1->kabupaten . '\');">' . "\r\n" . '                            </td></tr>';
	}

	echo "\t" . ' ' . "\r\n" . '         </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\r\n" . '         </table>';
}

?>
