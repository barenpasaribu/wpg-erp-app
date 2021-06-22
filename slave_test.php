<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zMysql.php';
$process = $_POST['process'];

switch ($process) {
case 'insRandom':
	$i = 1;

	while ($i <= 5) {
		$query = insertQuery($dbname, 'test', array('\'' . rand() . '\'', '\\'' . rand() . '\''));

		try {
			mysql_query($query);
			echo 'Query ' . $i . ' inserted' . "\n";
		}
		catch (Exception $e) {
			echo 'Query ' . $i . ' failed';
		}
	}

	break;

case 'deleteAll':
	mysql_query('delete from ' . $dbname . '.test');
	echo 'Table ' . $dbname . '.test flushed' . "\n";
	break;
}

?>
