<?php



$smtp = '192.168.1.100';
$sslsmtp = '';
$port = '25';
$username = 'system@eagro.id';
$password = 'system';
$timeout = null;
$auth = true;
$arrsmtp = ['host' => $smtp, 'port' => $port, 'auth' => $auth, 'username' => $username, 'password' => $password, 'timeout' => $timeout];
$arrsmtpSSL = ['host' => 'ssl://'.$smtp, 'port' => $port, 'auth' => $auth, 'username' => $username, 'password' => $password, 'timeout' => $timeout];

?>