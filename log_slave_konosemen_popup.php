<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo '<link rel="stylesheet" type="text/css" href="style/generic.css">' . "\r\n" . '<script language=javascript src=\'js/generic.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/log_konosemen.js\'></script>' . "\r\n";
$tipe = $_GET['tipe'];
$param = $_GET;

switch ($tipe) {
case 'PO':
	echo '<label for=po>' . $_SESSION['lang']['nopo'] . '</label>';
	echo '<input id=\'po\' onkeypress=\'key=getKey(event);if(key==13){findPO()}\'>';
	echo '<button class=mybutton onclick=\'findPO()\'>' . $_SESSION['lang']['find'] . '</button>';
	break;

case 'SJ':
	echo '<label for=sj>No. Delivery Order</label>';
	echo '<input id=\'sj\' onkeypress=\'key=getKey(event);if(key==13){findPL()}\'>';
	echo '<button class=mybutton onclick=\'findSJ()\'>' . $_SESSION['lang']['find'] . '</button>';
	break;

case 'M':
	echo '<label for=mat>Find Material</label>';
	echo '<input id=\'mat\' onkeypress=\'key=getKey(event);if(key==13){findMat()}\'>';
	echo '<button class=mybutton onclick=\'findMat()\'>' . $_SESSION['lang']['find'] . '</button>';
	break;
}

echo $param['kodept'];
echo '\'>' . "\r\n" . '<div id=\'hasilCari\'></div>' . "\r\n" . '<div id=\'progress\'></div>';

?>
