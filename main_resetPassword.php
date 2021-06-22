<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=js/usersetting.js></script>' . "\r\n";
include 'master_mainMenu.php';
$str = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan order by namakaryawan';
$res = mysql_query($str);
$opt = '';

while ($bar = mysql_fetch_object($res)) {
	$opt .= '<option value=\'' . $bar->karyawanid . '\'>' . $bar->namakaryawan . '</option>';
}

OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['resetuserpassword'] . ':');
echo '<fieldset>' . "\r\n" . '     <legend><img src=\'images/vista_icons_03.png\' height=60px style=\'vertical-align:middle;\'><b>' . $_SESSION['lang']['resetuserpassword'] . ':</b></legend> ' . "\r\n\t" . '  ' . $_SESSION['lang']['finduser'] . ':<input type=text id=uname class=myinputtext onkeypress="return validat1(event);" size=20 maxlength=30 title=\'Enter part of username then click Find\'>' . "\r\n\t" . ' <input type=button class=mybutton value=\'' . $_SESSION['lang']['find'] . '\' title=\'Click to process\' onclick=getUserForResetP()>' . "\r\n\t" . ' <br>' . "\r\n\t" . ' </fieldset><br><hr>' . "\r\n\t" . ' <fieldset>' . "\r\n\t" . ' <legend>Result</legend>' . "\r\n\t" . ' <div id=result></div>' . "\r\n\t" . ' </fieldset>' . "\r\n\t" . ' <div id=temp></div>' . "\r\n\t" . ' ';
echo CLOSE_THEME();
echo '<div id=resetter style=\'display:none;position:absolute;\'>';
echo OPEN_THEME($_SESSION['lang']['resetuserpassword'] . ':');
echo '<input type=hidden value=\'\' id=uid>' . "\r\n" . '       <center></center>' . "\r\n" . '       <div id=resetwin>' . "\r\n\t" . '   <table>' . "\r\n\t" . '   <tr><td><b>Account</b></td><td>:<b><a id=un></a></b></td></tr>' . "\r\n\t" . '   <tr>' . "\r\n\t" . '    <td>' . $_SESSION['lang']['newpassword'] . '</td><td>:<input class=myinputtext type=password id=newpwd1 size=15 onkeypress="return tanpa_kutip(event);"><img src=\'images/obligatory.gif\' style=\'height:15px;vertical-align:middle;\' title=\'Required Element\'></td></tr>' . "\r\n" . '        <tr><td>Re-Type ' . $_SESSION['lang']['newpassword'] . '</td><td>:<input class=myinputtext type=password id=newpwd2 size=15 onkeypress="return tanpa_kutip(event);"><img src=\'images/obligatory.gif\' style=\'height:15px;vertical-align:middle;\' title=\'Required Element\'></td></tr>' . "\r\n\t" . '    <tr><td colspan=2 align=right><input style=\'vertical-align:middle;\' type=checkbox id=sendmail>' . $_SESSION['lang']['sendmailtouser'] . '.</td></tr>' . "\r\n\t\t" . '<tr><td colspan=2 align=right>' . "\r\n\t\t" . '<input type=button class=mybutton value=\'' . $_SESSION['lang']['close'] . '\' onclick=hideSetter()>' . "\r\n\t\t" . '<input type=button class=mybutton value=\'' . $_SESSION['lang']['save'] . '\' onclick=saveNewPwd()></td></tr>' . "\r\n\t" . '   </table>' . "\r\n\t" . '   </div>';
echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
