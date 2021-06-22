<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=\'js/menusetting.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

if ($_SESSION['standard']['access_level'] == 1) {
	$str = 'select a.namauser,b.namakaryawan,b.lokasitugas,c.namajabatan,d.nama from ' . $dbname . '.user a' . "\r\n" . '          left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid' . "\r\n" . '          left join ' . $dbname . '.sdm_5jabatan c on b.kodejabatan=c.kodejabatan' . "\r\n" . '          left join ' . $dbname . '.sdm_5departemen d on b.bagian=d.kode';
	$res = mysql_query($str);
	$optPengguna = '';

	while ($bar = mysql_fetch_object($res)) {
		$optPengguna .= '<option value=\'' . $bar->namauser . '\'>' . $bar->namauser . '-' . $bar->lokasitugas . '</option>';
	}
}
else {
	$str = 'select a.namauser,b.namakaryawan,b.lokasitugas,c.namajabatan,d.nama from ' . $dbname . '.user a' . "\r\n" . '          left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid' . "\r\n" . '          left join ' . $dbname . '.sdm_5jabatan c on b.kodejabatan=c.kodejabatan' . "\r\n" . '          left join ' . $dbname . '.sdm_5departemen d on b.bagian=d.kode where a.hak != 1 and b.karyawanid not in (0999999999,0888888888)';
	$res = mysql_query($str);
	$optPengguna = '';

	while ($bar = mysql_fetch_object($res)) {
		$optPengguna .= '<option value=\'' . $bar->namauser . '\'>' . $bar->namauser . '-' . $bar->lokasitugas . '</option>';
	}
}

OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['privconf'] . ':');
echo '<fieldset>' . "\r\n" . '     <legend><img src=\'images/vista_icons_03.png\' height=60px style=\'vertical-align:middle;\'>' . $_SESSION['lang']['newuser'] . '</legend> ' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . ' <td>' . $_SESSION['lang']['newuser'] . '</td><td>:<select id=pengguna>' . $optPengguna . '</select></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['copyfrom'] . '</td><td>:<select id=dari>' . $optPengguna . '</select></td></tr>' . "\r\n\t" . ' <tr><td colspan=2 align=right><button class=mybutton onclick=copyPrivileges()>' . $_SESSION['lang']['proses'] . '</button></td></tr>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' </fieldset>' . "\r\n\t" . ' ';
echo CLOSE_THEME();
echo '<div id=container></div>';
CLOSE_BOX();
echo close_body();

?>
