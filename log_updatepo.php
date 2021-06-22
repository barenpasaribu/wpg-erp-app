<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['updatepo'] . '</b>');
$arr = '##nopo';
echo '<script>' . "\r\n" . 'dert="';
echo $optLokal2;
echo '";' . "\r\n" . 'dert2="';
echo $optLokal;
echo '";' . "\r\n" . '</script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/log_updatepo.js\'></script>' . "\r\n\r\n" . '<link rel=stylesheet type=\'text/css\' href=\'style/zTable.css\'>' . "\r\n";
echo '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>' . $_SESSION['lang']['updatepo'] . '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n\r\n" . '<tr><td><label>' . $_SESSION['lang']['nopo'] . '</label></td><td>' . "\r\n" . '<input type=text id=nopo class=myinputtext  style=width:150px; /></td></tr>' . "\r\n\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_updatepo\',\'' . $arr . '\',\'printContainer\')" class="mybutton">Proses</button>' . "\r\n" . '        </td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>';
echo '<fieldset style=\'clear:both;width:1050px;display:block;\'><legend><b>' . $_SESSION['lang']['list'] . '</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;width:1050px\'>' . "\r\n\r\n" . '</div></fieldset>';
CLOSE_BOX();
echo close_body();

?>
