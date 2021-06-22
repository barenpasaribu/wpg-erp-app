<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
echo '<div>' . "\r\n" . '     <fieldset style=\'width:200px;color:#333399;\'>' . "\r\n" . '         <legend>[Info] ' . $_SESSION['lang']['menulevel'] . ':</legend>' . "\r\n" . '         ' . $_SESSION['lang']['menuleveldesc'] . "\r\n" . '         </fieldset><br>' . "\r\n" . '         <input type=button value=\'' . $_SESSION['lang']['apply'] . '\' class=mybutton onclick=window.location.reload()>' . "\r\n" . '     <input type=button value=\'' . $_SESSION['lang']['close'] . '\' class=mybutton onclick="hideDetailForm(\'ctr\',\'ctrmenu\');hideThis(\'lab2\');">' . "\r\n" . '         <hr>';
$opt = '<option>0</option>';
$d = 1;

while ($d < 25) {
	$opt .= '<option>' . $d . '</option>';
	++$d;
}

if ($_SESSION['standard']['access_level'] == 1) {
	$str = 'select a.*,b.namakaryawan,b.lokasitugas from ' . $dbname . '.user a' . "\r\n" . '          left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid order by namauser';
	$res = mysql_query($str);
	echo '<table width=100% cellspacing=1 border=0 class=data>' . "\r\n" . '      <thead>' . "\r\n" . '          <tr><td>Uname</td>' . "\r\n" . '              <td>KaryawanId</td>' . "\r\n" . '              <td>Nama</td>' . "\r\n" . '              <td>Lokasi.Tugas</td>' . "\r\n" . '                  <td>UserStatus</td>' . "\r\n" . '                  <td>Access Level</td>' . "\r\n" . '          </tr>' . "\t" . '  ' . "\r\n" . '          </thead>' . "\r\n" . '          <tbody>' . "\r\n" . '          ';

	while ($bar = mysql_fetch_object($res)) {
		echo '<tr class=rowcontent>' . "\r\n" . '                 <td class=firsttd>' . $bar->namauser . '</td>' . "\r\n" . '                  <td>' . $bar->karyawanid . '</td>' . "\r\n" . '                  <td>' . $bar->namakaryawan . '</td>' . "\r\n" . '                  <td>' . $bar->lokasitugas . '</td>';

		if ($bar->status == 1) {
			echo '<td><font color=#00AA00><b>Active</b></td>';
		}
		else {
			echo '<td>Inactive</td>';
		}

		echo "\t" . ' <td align=right>' . "\r\n" . '                           <select id="select' . $bar->namauser . '" onchange="setAccessLevel(this,\'' . $bar->namauser . '\',this.options[this.selectedIndex].text)">' . "\r\n" . '                             <option>' . $bar->hak . '</option' . $opt . "\r\n" . '                           </select>' . "\r\n" . '             </td>' . "\t" . ' ' . "\r\n" . '                 </tr>';
	}

	echo '</tbody></table><br>';
}
else {
	$str = 'select a.*,b.namakaryawan,b.lokasitugas from ' . $dbname . '.user a left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where a.hak != 1 and b.karyawanid not in (0999999999,0888888888)  order by a.namauser';
	$res = mysql_query($str);
	echo '<table width=100% cellspacing=1 border=0 class=data>' . "\r\n" . '      <thead>' . "\r\n" . '          <tr><td>Uname</td>' . "\r\n" . '              <td>KaryawanId</td>' . "\r\n" . '              <td>Nama</td>' . "\r\n" . '              <td>Lokasi.Tugas</td>' . "\r\n" . '                  <td>UserStatus</td>' . "\r\n" . '                  <td>Access Level</td>' . "\r\n" . '          </tr>' . "\t" . '  ' . "\r\n" . '          </thead>' . "\r\n" . '          <tbody>' . "\r\n" . '          ';

	while ($bar = mysql_fetch_object($res)) {
		echo '<tr class=rowcontent>' . "\r\n" . '                 <td class=firsttd>' . $bar->namauser . '</td>' . "\r\n" . '                  <td>' . $bar->karyawanid . '</td>' . "\r\n" . '                  <td>' . $bar->namakaryawan . '</td>' . "\r\n" . '                  <td>' . $bar->lokasitugas . '</td>';

		if ($bar->status == 1) {
			echo '<td><font color=#00AA00><b>Active</b></td>';
		}
		else {
			echo '<td>Inactive</td>';
		}

		echo "\t" . ' <td align=right>' . "\r\n" . '                           <select id="select' . $bar->namauser . '" onchange="setAccessLevel(this,\'' . $bar->namauser . '\',this.options[this.selectedIndex].text)">' . "\r\n" . '                             <option>' . $bar->hak . '</option' . $opt . "\r\n" . '                           </select>' . "\r\n" . '             </td>' . "\t" . ' ' . "\r\n" . '                 </tr>';
	}

	echo '</tbody></table><br>';
}

echo "\r\n" . '<input type=button value=\'' . $_SESSION['lang']['apply'] . '\' class=mybutton onclick=window.location.reload()>' . "\r\n" . '<input type=button value=\'' . $_SESSION['lang']['close'] . '\' class=mybutton onclick="hideDetailForm(\'ctr\',\'ctrmenu\');hideThis(\'lab2\');">' . "\r\n" . '<br><br>';

?>
