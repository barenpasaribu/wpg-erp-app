<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$namalokasi = $_POST['namalokasi'];
$status = $_POST['status'];
$method = $_POST['method'];

if ($method == 'update') {
	$str = 'update ' . $dbname . '.rencana_lahan set statuspengakuan=\'' . $status . '\'' . "\r\n\t" . '       where nama=\'' . $namalokasi . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

$str1 = 'select *, case statuspengakuan when 0 then \'' . $_SESSION['lang']['proses'] . '\'' . "\r\n\t" . '    when 1 then \'' . $_SESSION['lang']['redyforoperation'] . '\'' . "\r\n\t\t" . ' when 2 then \'' . $_SESSION['lang']['fail'] . '\' ' . "\t\t" . ' ' . "\r\n\t\t" . ' end as stats' . "\r\n" . '       from ' . $dbname . '.rencana_lahan        ' . "\r\n\t" . '   order by tanggalmulai desc';

if ($res1 = mysql_query($str1)) {
	$no = 0;

	while ($bar1 = mysql_fetch_object($res1)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '   <td>' . $no . '</td>' . "\r\n\t\t" . '    <td>' . $bar1->nama . '</td>' . "\r\n\t\t\t" . '<td>' . tanggalnormal($bar1->tanggalmulai) . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->peruntukanlahan . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->desa . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->kecamatan . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->kabupaten . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->provinsi . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->negara . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->kontak . '</td>' . "\r\n\t\t\t" . '<td>' . $bar1->stats . '</td>' . "\r\n\t\t\t";
	}
}

?>
