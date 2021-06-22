<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tablename = $_POST['tablename'];
$texttofind = $_POST['txttofind'];
$field = $_POST['field'];
$order = $_POST['order'];
$periode = $_POST['periode'];
if ((trim($tablename) == '') || (trim($field) == '')) {
	echo 'No table/field';
}
else if (isset($_POST['page'])) {
	printSearchOnTable($tablename, $field, $texttofind, $order, $periode, $_POST['page']);
}
else {
	printSearchOnTable($tablename, $field, $texttofind, $order, $periode);
}

?>
