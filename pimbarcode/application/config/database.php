<?php
 if (!defined('BASEPATH')) {
     exit('No direct script access allowed');
 }
/*
| /////////////////////////////////// TECHNOILAHI CORPORATION \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
| 
| Nah, Bagi Teman - teman yang baru mendapatkan script ini, maka teman-teman harus terlebih dahulu
| menginstall xampp or lampp. setelah itu di phpmyadmin buat sebuah database, and ane saranin pakai
| php versi 5.6, soalnya kalau pakai versi 7 ke atas scriptnya ga bakal optimal hehe. 
|
| Ok, Setelah itu, teman - teman isi bagian //username\\ //password\\ //database\\ | | tiga itu, 
| tapi, biasanya kalau di lampp atau di xampp si password di kosongkan, dan username defaulthnya 
| bernama root
|
|
|
|
|
|
|
| //////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/

// The following values will probably need to be changed.
$db['default']['username'] = 'admin';  // isi yang ini gan pakai username database antum
$db['default']['password'] = 'WPG123!@#';   // isi password darabase antum
$db['default']['database'] = 'fastenvi_pimdbfr'; // isi nama database antum

// The following values can probably stay the same.
$db['default']['hostname'] = '202.157.185.209';
$db['default']['dbdriver'] = 'mysqli'; //Updated to latest driver.
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = false;
$db['default']['db_debug'] = true;
$db['default']['cache_on'] = false;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';

$active_group = 'default';
$active_record = true;

/* End of file database.php */
/* Location: ./application/config/database.php */
