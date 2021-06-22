<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$param = $_POST;

if (($param['kodeorg'] != '') && ($param['periode'] != '')) {
	$str1 = 'select a.*,b.namakaryawan from ' . $dbname . '.rencana_gis_file a' . "\r\n" . '            left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid ' . "\r\n" . '            where unit=\'' . $param['kodeorg'] . '\' and tanggal like \'' . $param['periode'] . '%\' ' . "\r\n" . '            and kode=\'' . $param['kode'] . '\' and a.karyawanid=\'' . $_SESSION['standard']['userid'] . '\'     ' . "\r\n" . '            order by a.lastupdate  desc';
}
else if (($param['kodeorg'] != '') && ($param['periode'] == '')) {
	$str1 = 'select a.*,b.namakaryawan from ' . $dbname . '.rencana_gis_file a' . "\r\n" . '            left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid ' . "\r\n" . '            where unit=\'' . $param['kodeorg'] . '\' and kode=\'' . $param['kode'] . '\' and a.karyawanid=\'' . $_SESSION['standard']['userid'] . '\'' . "\r\n" . '            order by a.lastupdate  desc';
}
else if (($param['kodeorg'] == '') && ($param['periode'] != '')) {
	$str1 = 'select a.*,b.namakaryawan from ' . $dbname . '.rencana_gis_file a' . "\r\n" . '            left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid ' . "\r\n" . '            where  tanggal like \'' . $param['periode'] . '%\' and ' . "\r\n" . '            kode=\'' . $param['kode'] . '\' and a.karyawanid=\'' . $_SESSION['standard']['userid'] . '\'' . "\r\n" . '            order by a.lastupdate  desc';
}
else if (($param['kodeorg'] == '') && ($param['periode'] == '') && ($param['kode'] == '')) {
	$str1 = 'select a.*,b.namakaryawan from ' . $dbname . '.rencana_gis_file a' . "\r\n" . '            left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid=\'' . $_SESSION['standard']['userid'] . '\' ' . "\r\n" . '            order by a.lastupdate  desc limit 100';
}
else {
	$str1 = 'select a.*,b.namakaryawan from ' . $dbname . '.rencana_gis_file a' . "\r\n" . '            left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid ' . "\r\n" . '            where   kode=\'' . $param['kode'] . '\' and a.karyawanid=\'' . $_SESSION['standard']['userid'] . '\'   ' . "\r\n" . '            order by a.lastupdate  desc';
}

$res1 = mysql_query($str1);
$no = 0;

while ($bar1 = mysql_fetch_object($res1)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n" . '               <td>' . $no . '</td>' . "\r\n" . '                <td>' . $bar1->unit . '</td>' . "\r\n" . '                    <td>' . $bar1->kode . '</td>' . "\r\n" . '                    <td>' . tanggalnormal($bar1->tanggal) . '</td>' . "\r\n" . '                    <td>' . $bar1->namakaryawan . '</td>' . "\r\n" . '                    <td>' . $bar1->lastupdate . '</td>' . "\r\n" . '                    <td>' . $bar1->keterangan . '</td>' . "\r\n" . '                    <td>' . $bar1->namafile . '</td>' . "\r\n" . '                    <td align=right>' . $bar1->ukuran . '</td>' . "\r\n" . '                    <td>' . $bar1->namakaryawan . '</td>' . "\r\n" . '                    <td>';

	if ($bar1->karyawanid == $_SESSION['standard']['userid']) {
		echo '<img class=zImgBtn src=images/skyblue/delete.png   title=\'Edit\' onclick="delFile(\'' . $bar1->unit . '\',\'' . $bar1->kode . '\',\'' . $bar1->namafile . '\');"> &nbsp  &nbsp  &nbsp';
	}

	echo '<img class=zImgBtn src=images/skyblue/save.png   title=\'Save\' onclick="download(\'' . $bar1->namafile . '\');"></td></tr>';
}

?>
