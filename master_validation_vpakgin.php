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

if (2 < count($bb) && 0 !== $bb[2]) {
    echo ' [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!';
    session_destroy();
    exit();
}

unset($_POST['par'], $_GET['par']);

if ((int) ($_SESSION['DIE']) < time()) {
    echo ' [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!';
    session_destroy();
    exit();
}

$_SESSION['DIE'] = time() + $_SESSION['MAXLIFETIME'];
if (isset($_SESSION['standard']['username'], $_SESSION['access_type'])) {
    if (6 <= strlen($_SESSION['standard']['username']) && ('level' === $_SESSION['access_type'] || 'detail' === $_SESSION['access_type'])) {
    } else {
        exit('Sorry, You entering the system like cracker');
    }
} else {
    if ('on' === $_SESSION['security']) {
        exit('Not Authorized');
    }
}

if (!isset($_SESSION['org']['holding'])) {
    echo ' [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!';
    session_destroy();
    exit();
}

?>