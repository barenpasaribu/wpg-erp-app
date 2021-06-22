<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<link rel=stylesheet type=\'text/css\' href=style/efs.css>' . "\r\n" . '<script language=javascript1.2 src=js/usersetting.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo OPEN_THEME('Active/Deactive/Delete Users:');
echo '<br><fieldset>' . "\r\n" . '     <legend><img src=\'images/useraccounts.png\' height=60px style=\'vertical-align:middle;\'><b>Activate/Deactivate/Delete Users Account:</b></legend> ' . "\r\n\t" . '  Find User:<input type=text id=uname class=myinputtext onkeypress="return validat(event);" size=20 maxlength=30 title=\'Enter part of username then click Find\'>' . "\r\n\t" . ' <input type=button class=mybutton value=Find title=\'Click to process\' onclick=getUserForActivation()>' . "\r\n\t" . ' <br>' . "\r\n\t" . ' </fieldset><br><hr>' . "\r\n\t" . ' <fieldset>' . "\r\n\t" . ' <legend>Result</legend>' . "\r\n\t" . ' <div id=result></div>' . "\r\n\t" . ' </fieldset>' . "\r\n\t" . ' <div id=temp></div>' . "\r\n\t" . ' ';
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>
