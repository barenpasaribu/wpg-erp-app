<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/usersetting.js></script>' . "\n";
include 'master_mainMenu.php';

if ($_SESSION['standard']['access_level'] == 1) {
	$str = 'select karyawanid,namakaryawan, lokasitugas from ' . $dbname . '.datakaryawan where (tanggalkeluar>\'' . date('Y-m-d') . '\' or tanggalkeluar is NULL) order by namakaryawan';
	$res = mysql_query($str);
	$opt = '';

	while ($bar = mysql_fetch_object($res)) {
		$opt .= '<option value=\'' . $bar->karyawanid . '\'>' . $bar->namakaryawan . '-' . $bar->lokasitugas . '</option>';
	}
}
else {
	//$str = 'select karyawanid,namakaryawan, lokasitugas from ' . $dbname . '.datakaryawan where (tanggalkeluar>\'' . date('Y-m-d') . '\' or tanggalkeluar is NULL and karyawanid not in (0000000000,0888888888,0999999999)) order by namakaryawan';
	$str = 'select karyawanid,namakaryawan, lokasitugas from ' . $dbname . '.datakaryawan where ((tanggalkeluar is NULL or tanggalkeluar =\'0000-00-00\')and karyawanid not in (0000000000,0888888888,0999999999) and lokasitugas like \''.$_SESSION['empl']['induklokasitugas'].'%\') order by namakaryawan';
	$res = mysql_query($str);
	$opt = '';
	while ($bar = mysql_fetch_object($res)) {
		$opt .= '<option value=\'' . $bar->karyawanid . '\'>' . $bar->namakaryawan . '-' . $bar->lokasitugas . '</option>';
	}
}
//echo "test : ".$str;
//echo " ssss ".$_SESSION['empl']['induklokasitugas'];
//print_r($_SESSION);
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['newuser'] . ':');
echo '<fieldset>' . "\n" . '     <legend><img src=\'images/user.png\' height=60px style=\'vertical-align:middle;\'><b>' . $_SESSION['lang']['addnewuser'] . ':</b></legend> ' . "\n" . '      <table cellspacing=1 border=0\'>' . "\n\t" . '  <tbody>' . "\n" . '        <tr><td>' . $_SESSION['lang']['employeename'] . '</td><td>' . "\n\t\t" . '        <select id=userid onchange=enablecheck(this.options[this.selectedIndex].value)>' . "\n\t\t\t" . '      <option value=0>*Additional User ... </option>' . $opt . "\n\t\t\t" . '     </select>' . "\n\t\t\t" . ' </td>' . "\n\t\t" . ' </tr>' . "\n\t" . '     <tr><td>' . $_SESSION['lang']['username'] . '</td><td><input  class=myinputtext type=text size=20 maxlength=40 id=uname onkeypress="return tanpa_kutip_dan_sepasi(event);"><img src=\'images/obligatory.gif\' style=\'height:15px;vertical-align:middle;\' title=\'Required Element\'></td></tr>' . "\n\t\t" . ' <tr><td>' . $_SESSION['lang']['password'] . '</td><td><input  class=myinputtext type=password id=pwd1 size=20 maxlength=20 onkeypress="return tanpa_kutip(event);"><img src=\'images/obligatory.gif\' style=\'height:15px;vertical-align:middle;\' title=\'Required Element\'></td></tr>' . "\n\t\t" . ' <tr><td>Re-Type ' . $_SESSION['lang']['password'] . '</td><td><input  class=myinputtext type=password id=pwd2 size=20 maxlength=20 onkeypress="return tanpa_kutip(event);"><img src=\'images/obligatory.gif\' style=\'height:15px;vertical-align:middle;\' title=\'Required Element\'></td></tr>' . "\n" . '          <tr><td>Status</td><td><input type=radio name=radio id=radio value=1 class=myradio checked>Active <input type=radio name=radio id=radio1 value=0 class=myradio>Not Active<br>' . "\n\t\t" . '  <input type=checkbox id=sendmail style=\'vertical-align:middle;\' disabled>' . $_SESSION['lang']['sendmailtouser'] . "\n\t\t" . '  </td></tr>' . "\n\t\t" . '  <tr><td colspan=2 align=right>' . "\n\t\t\t" . '  <input type=button class=mybutton value=\'' . $_SESSION['lang']['cancel'] . '\' onclick=resetf()>' . "\n\t\t\t" . '  <input type=button class=mybutton value=\'' . $_SESSION['lang']['save'] . '\' onclick=savef()> &nbsp ' . "\n\t\t" . '  </td></tr>' . "\t\t" . ' ' . "\n\t" . '  </tbody>' . "\n\t" . '  </table>  ' . "\n\t" . ' </fieldset><br><hr>' . "\n\t" . ' <div id=temp></div>' . "\n\t" . ' ';
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>
