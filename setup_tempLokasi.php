<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n" . '<script language=javascript1.2 src=\'js/kebun_5tempLokasi.js\'></script>' . "\r\n\r\n\r\n";
$optKar = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$i = 'select distinct(a.karyawanid) as karyawanid,b.namakaryawan,b.nik from ' . $dbname . '.user a' . "\r\n\t" . 'left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where b.kodeorganisasi=\'' . $_SESSION['empl']['kodeorganisasi'] . '\'  ';

#exit(mysql_error($conn));
($n = mysql_query($i)) || true;

while ($d = mysql_fetch_assoc($n)) {
	$optKar .= '<option value=\'' . $d['karyawanid'] . '\'>' . $d['namakaryawan'] . ' [' . $d['nik'] . ']</option>';
}

$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$i = 'select * from ' . $dbname . '.organisasi where length(kodeorganisasi)=4 order by namaorganisasi asc';

#exit(mysql_error($conn));
($n = mysql_query($i)) || true;

while ($d = mysql_fetch_assoc($n)) {
	$optOrg .= '<option value=\'' . $d['kodeorganisasi'] . '\'>' . $d['namaorganisasi'] . '</option>';
}

$optTipe = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optTipe .= '<option value=\'D\'>Dump Truck</option>';
$optTipe .= '<option value=\'F\'>Fuso</option>';
echo "\r\n\r\n";
OPEN_BOX();
echo '<fieldset style=\'float:left;\'>';
echo '<legend>Temp Organization</legend>';
echo '<table border=0 cellpadding=1 cellspacing=1>' . "\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t" . '<td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t" . '<td><select id=kar style="width:150px;">' . $optKar . '</select></td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\r\n\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t" . '<td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t" . '<td><select id=kdorg style="width:150px;">' . $optOrg . '</select></td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t" . '<tr><td colspan=2></td>' . "\r\n\t\t\t\t\t" . '<td colspan=3>' . "\r\n\t\t\t\t\t\t" . '<button class=mybutton onclick=simpan()>Simpan</button>' . "\r\n\t\t\t\t\t\t" . '<button class=mybutton onclick=cancel()>Hapus</button>' . "\r\n\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t\r\n\t\t\t" . '</table></fieldset>' . "\r\n\t\t\t\t\t" . '<input type=hidden id=method value=\'insert\'>';
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo '<fieldset>' . "\r\n\t\t" . '<legend>' . $_SESSION['lang']['list'] . '</legend>' . "\r\n\t\t" . '<div id=container> ' . "\r\n\t\t\t" . '<script>loadData()</script>' . "\r\n\t\t" . '</div>' . "\r\n\t" . '</fieldset>';
CLOSE_BOX();
echo close_body();

?>
