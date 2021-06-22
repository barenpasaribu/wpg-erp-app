<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo '<link rel="stylesheet" type="text/css" href="style/generic.css">' . "\r\n" . '<script language=javascript src=\'js/generic.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/keu_vp.js\'></script>' . "\r\n";
$tipe = $_GET['tipe'];
$param = $_GET;
$optTipe = array('po' => $_SESSION['lang']['po'],
    'k' => $_SESSION['lang']['kontrak'],
    'sj' => $_SESSION['lang']['suratjalan'],
    'ns' => $_SESSION['lang']['konosemen'],
    'ot' => $_SESSION['lang']['lain']);
echo "\r\n" . '<div style=\'margin:10px 0 15px 5px\'>' . "\r\n" . '    <label for=\'po\'>';
echo $_SESSION['lang']['find'];
echo '</label>' . "\r\n\t";
echo makeElement('tipe', 'select', 'po', array(), $optTipe);
echo makeElement('po', 'text', '', array('onkeypress' => 'key=getKey(event);if(key==13){findPO()}'));
echo '    <button class=mybutton onclick=\'findPO()\'>';
echo $_SESSION['lang']['find'];
echo '</button>' . "\r\n" . '</div>' . "\r\n" . '<fieldset><legend>';
echo $_SESSION['lang']['hasil'];
echo '</legend>' . "\r\n" . '    <div id=\'hasilPO\'></div>' . "\r\n" . '    <div id=\'hasilInvoice\' style=\'display:none\'></div>' . "\r\n" . '</fieldset>' . "\r\n" . '<div id=\'progress\'></div>';

?>
