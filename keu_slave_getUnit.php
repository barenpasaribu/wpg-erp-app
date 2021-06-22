<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$pt = $_POST['pt'];
$tipe = $_POST['tipe'];
$hasil = '';
if ('bb' == $tipe) {
    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL' or tipe = 'TRAKSI'\r\n                        or tipe='HOLDING')  and induk!='' and induk = '".$pt."'\r\n                        ";
        $hasil .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
    } else {
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                        where induk='".$pt."' and length(kodeorganisasi)=4 and kodeorganisasi not like '%HO'";
        } else {
            $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                        where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'  and induk!=''";
        }
    }

    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $hasil .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
} else {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n      where induk='".$pt."'";
    $res = mysql_query($str);
    $hasil = '<option value="">'.$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $hasil .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    if ('' == $pt) {
        $hasil = '<option value="">'.$_SESSION['lang']['pilihdata'].'</option>';
    }
}

echo $hasil;

?>