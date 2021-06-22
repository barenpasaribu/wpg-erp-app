<?php
if(file_exists('config/connection.php')){
require_once('config/connection.php');
echo "database: ".$dbname;
}

?>
