<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\r\n";
$kodeorg = $_POST['kodeorg'];
$regional = $_POST['regional'];
$kodeblok = $_POST['kodeblok'];
$jarak = $_POST['jarak'];
$method = $_POST['method'];
switch ($method) {
    case 'save':
        $i = 'insert into '.$dbname.".vhc_5jarakblok(regional,kodeorg,kodeblok,jarak,updateby)\r\n\t\tvalues ('".$regional."','".$kodeorg."','".$kodeblok."','".$jarak."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getBlok':
        $optblok = "<option value=''>Pilih data</option>";
        $x = 'select * from '.$dbname.".setup_blok where kodeorg like '%".$kodeorg."%'";
        $y = mysql_query($x);
        while ($z = mysql_fetch_assoc($y)) {
            $optblok .= "<option value='".$z['kodeorg']."'>".$z['kodeorg'].'</option>';
        }
        echo $optblok;

        break;
    case 'delete':
        $i = 'DELETE FROM '.$dbname.".pabrik_5kelengkapanloses WHERE id='".$id."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>