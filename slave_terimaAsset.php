<?php

    // require_once('config/connection.php');

    $tipe = $_POST['tipe'];

    session_start();
	if(isset($_POST)){
        if ($tipe == 'LIST_TERIMA_ASSET') {
            if (isset($_POST['value'])) {
                $value = $_POST['value'];
                $_SESSION['list_terima_asset'] = $value;
                echo $_SESSION['list_terima_asset'];
            }
	    }
    }
    
?>
