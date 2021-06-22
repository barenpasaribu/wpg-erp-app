<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';  
$kodeorg=$_POST['kodeorg'];
$tahuntanam=$_POST['tahuntanam']; 
try {
	$delete="delete from setup_blok where kodeorg='$kodeorg' and tahuntanam=$tahuntanam";
	if (!mysql_query($delete)) {
		echo 'DB Error : ' . mysql_error($conn);
		exit();
	} 
}
catch (Exception $e) {
	echo  mysql_error($conn);
}

?>
