<?php







session_cache_expire(25);

session_start();

if (isset($_POST['par'])) {

    $bb = preg_split('#/#D', $_POST['par']);

} else {

    if (isset($_GET['par'])) {

        $bb = preg_split('#/#D', $_GET['par']);

    }

}



/*if (2 < count($bb) && 0 != $bb[2]) {

    echo pre($bb).' [Gagal/Failed/Error],asd your session has expired, please press refresh button and login again..!';

    session_destroy();

    exit();

}*/



unset($_POST['par'], $_GET['par']);



$_SESSION['DIE'] = time() + $_SESSION['MAXLIFETIME'];



if ((int) ($_SESSION['DIE']) < time()) {

    echo ' [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!';

    session_destroy();

    exit();

}



if (isset($_SESSION['standard']['username'], $_SESSION['access_type'])) {

    if (5 <= strlen($_SESSION['standard']['username']) && ('level' == $_SESSION['access_type'] || 'detail' == $_SESSION['access_type'])) {

    } else {

        exit('Sorry, You entering the system like cracker');

    }

} else {

    if ($_SESSION['security'] == 'on') {

        exit('Not Authorized');

    }

}



/*if (!isset($_SESSION['org']['holding'])) {

    echo ' [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!';

    session_destroy();

    exit();

}*/

   function pre($array){

	echo '<pre>';print_r($array); echo '<pre>';

 }

 

 function showerror(){

	 error_reporting(E_ALL);

	ini_set('display_errors', TRUE);

	ini_set('display_startup_errors', TRUE);

 }


function saveLog($info)
{

	date_default_timezone_set('Asia/Jakarta');
	$user=$_SESSION['empl']['name'];
	$log = fopen("log.txt","a");
	fwrite($log,date('d-m-Y H:i:s')." # ".$user." # ".$info.PHP_EOL.PHP_EOL);
	fclose($log);
}


?>