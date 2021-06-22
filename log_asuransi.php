<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n" . '<script language=javascript1.2 src=\'js/log_asuransi.js\'></script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n\r\n\r\n";
OPEN_BOX('', '<b>Insurance</b>');
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=loadData()>' . "\r\n\t" . '   <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo ' ' . $_SESSION['lang']['nokonosemen'] . ' :<input type=text id=txt size=25 maxlength=30 class=myinputtext>';
echo '<button class=mybutton onclick=cari()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n\t" . ' </table> ';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n\t\t" . '<legend>' . $_SESSION['lang']['list'] . '</legend>' . "\r\n\t\t" . '<div id=container> ' . "\r\n\t\t\t" . '<script>loadData()</script>' . "\r\n\t\t" . '</div>' . "\r\n\t" . '</fieldset>';
CLOSE_BOX();
echo close_body();

?>
