<?php
require_once 'master_validation.php' ;
include 'config/connection.php' ;
include 'lib/devLibrary.php' ;

$kelompok=(empty($_POST) ? $_GET['kelompok']:$_POST['kelompok']);
$str="select * from ".$dbname.".vhc_5jenisvhc where kelompokvhc='".$kelompok."' order  by namajenisvhc";
$optjnsvhc = makeOption2($str,
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'jenisvhc',"captionfield"=> 'v' ),null,true
); 
echoMessage('options ', $optjnsvhc); 
?>
