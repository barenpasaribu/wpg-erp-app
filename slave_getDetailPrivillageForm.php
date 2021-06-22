<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
echo '<div>' . "\r\n" . '     <fieldset style=\'width:300px;color:#333399;\'>' . "\r\n\t" . ' <legend>[Info] ' . $_SESSION['lang']['userdetailprivsetup'] . ':</legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['thisusesdetailpriv'] . ' ' . "\r\n\t" . ' </fieldset>' . "\r\n\t" . ' <input type=button value=\'' . $_SESSION['lang']['apply'] . '\' class=mybutton onclick=window.location.reload()>' . "\r\n" . '     <input type=button value=\'' . $_SESSION['lang']['close'] . '\' class=mybutton onclick="hideDetailForm(\'ctr\',\'ctrmenu\');hideThis(\'lab3\');">' . "\r\n\t" . ' <hr>' . "\r\n\t" . ' ' . "\t" . ' <font color=#F8800A>' . $_SESSION['lang']['clickuser'] . '..!</font>' . "\r\n\t" . ' ';
$opt = '<option>0</option>';
$d = 1;

while ($d < 25) {
	$opt .= '<option>' . $d . '</option>';
	++$d;
}

if ($_SESSION['standard']['access_level'] == 1) {
	$str = 'select a.*,b.namakaryawan,b.lokasitugas from ' . $dbname . '.user a left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid order by a.namauser';
	$res = mysql_query($str);
	echo '<table width=100% cellspacing=1 border=0 class=data>' . "\r\n" . '      <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '  <td>No.</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['username'] . '</td>' . "\r\n\t" . '      <td>UID</td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['employeename'] . '</td>' . "\r\n" . '                         <td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n" . '                         <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t" . '  </tr>' . "\t" . '  ' . "\r\n\t" . '  </thead>' . "\r\n\t" . '  <tbody>' . "\r\n\t" . '  ';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr bgcolor=#DEDEDE class=standardrow onclick="setMapUserMenu(event,this,\'' . $bar->namauser . '\')" title=\'Click to Append menu to user ' . $bar->uname . '\'>' . "\r\n\t" . '         <td align=right class=firsttd>' . $no . '</td>' . "\r\n" . '                        <td>' . $bar->namauser . '</td>' . "\r\n" . '                        <td>' . $bar->karyawanid . '</td>' . "\r\n" . '                        <td>' . $bar->namakaryawan . '</td>' . "\r\n" . '                        <td>' . $bar->lokasitugas . '</td>';

		if ($bar->status == 1) {
			echo '<td><font color=#00AA00><b>' . $_SESSION['lang']['active'] . '</b></td>';
		}
		else {
			echo '<td>' . $_SESSION['lang']['inactive'] . '</td>';
		}

		echo '</tr>';
	}

	echo '</tbody></table><br>';
}
else {
	$str = 'select a.*,b.namakaryawan,b.lokasitugas from ' . $dbname . '.user a left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where a.hak != 1 and b.karyawanid not in (0999999999,0888888888)  order by a.namauser';
	$res = mysql_query($str);
	echo '<table width=100% cellspacing=1 border=0 class=data>' . "\r\n" . '      <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '  <td>No.</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['username'] . '</td>' . "\r\n\t" . '      <td>UID</td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['employeename'] . '</td>' . "\r\n" . '                         <td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n" . '                         <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t" . '  </tr>' . "\t" . '  ' . "\r\n\t" . '  </thead>' . "\r\n\t" . '  <tbody>' . "\r\n\t" . '  ';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr bgcolor=#DEDEDE class=standardrow onclick="setMapUserMenu(event,this,\'' . $bar->namauser . '\')" title=\'Click to Append menu to user ' . $bar->uname . '\'>' . "\r\n\t" . '         <td align=right class=firsttd>' . $no . '</td>' . "\r\n" . '                        <td>' . $bar->namauser . '</td>' . "\r\n" . '                        <td>' . $bar->karyawanid . '</td>' . "\r\n" . '                        <td>' . $bar->namakaryawan . '</td>' . "\r\n" . '                        <td>' . $bar->lokasitugas . '</td>';

		if ($bar->status == 1) {
			echo '<td><font color=#00AA00><b>' . $_SESSION['lang']['active'] . '</b></td>';
		}
		else {
			echo '<td>' . $_SESSION['lang']['inactive'] . '</td>';
		}

		echo '</tr>';
	}

	echo '</tbody></table><br>';
}

echo "\r\n" . '<input type=button value=\'' . $_SESSION['lang']['apply'] . '\' class=mybutton onclick=window.location.reload()>' . "\r\n" . '<input type=button value=\'' . $_SESSION['lang']['close'] . '\' class=mybutton onclick="hideDetailForm(\'ctr\',\'ctrmenu\');hideThis(\'lab3\');">' . "\r\n" . '<br><br>';

?>
