<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body('pw1');
echo "\r\n" . '<script language=javascript1.2 src=js/menusetting.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['resetuserpassword'] . ':');
echo '<fieldset>' . "\r\n" . '     <legend><img src=\'images/vista_icons_03.png\' height=60px style=\'vertical-align:middle;\'>' . $_SESSION['lang']['changepasswordfor'] . ' <b>' . $_SESSION['standard']['username'] . ':</b></legend> ' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . ' <td>' . $_SESSION['lang']['oldpassword'] . '</td><td>:<input type=password id=pw1 class=myinputtext onkeypress="return tanpa_kutip(event);" size=25><img src="images/obl.png" title="Obligatory" height=17px></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['newpassword'] . '</td><td>:<input type=password id=pw2  class=myinputtext onkeypress="return tanpa_kutip(event);" size=25><img src="images/obl.png" title="Obligatory" height=17px></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['retypepassword'] . '</td><td>:<input type=password  id=pw3 class=myinputtext onkeypress="return validat(\'' . $_SESSION['standard']['username'] . '\',event);" size=25><img src="images/obl.png" title="Obligatory" height=17px></td></tr>' . "\r\n\t" . ' <tr><td colspan=2 align=right><button class=mybutton onclick=changeMyPassword(\'' . $_SESSION['standard']['username'] . '\')>Change!</button></td></tr>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' </fieldset>' . "\r\n\t" . ' ';
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>
