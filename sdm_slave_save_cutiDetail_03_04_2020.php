<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_POST['kodeorgJ'];
$karyawanid = $_POST['karyawanidJ'];
$periode = $_POST['periodeJ'];
$dari = tanggalsystem($_POST['dariJ']);
$sampai = tanggalsystem($_POST['sampaiJ']);
$diambil = $_POST['diambilJ'];
$keterangan = $_POST['keteranganJ'];
$method = $_POST['method'];
if ('insert' == $method) {
    $strc = 'select * from '.$dbname.".sdm_cutidt\r\n       where karyawanid = '".$karyawanid."' and ((daritanggal>=".$dari.' and daritanggal<='.$sampai.")\r\n\t   or (sampaitanggal>=".$dari.' and sampaitanggal<='.$sampai.")\r\n\t   or (daritanggal<=".$dari.' and sampaitanggal>='.$sampai.'))';
    if (0 < mysql_num_rows(mysql_query($strc))) {
        echo ' Error '.$_SESSION['lang']['irisan'];
        exit(0);
    }

    if ($sampai < $dari) {
        echo ' Error < >';
        exit(0);
    }
}

if ('' == $diambil) {
    $diambil = 0;
}

switch ($method) {
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_cutidt\r\n\t\t       where kodeorg='".$kodeorg."'\r\n\t\t\t   and karyawanid=".$karyawanid."\r\n\t\t\t   and periodecuti='".$periode."'\r\n\t\t\t   and daritanggal='".$_POST['dariJ']."'";

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_cutidt \r\n\t\t      (kodeorg,karyawanid,periodecuti,daritanggal,\r\n\t\t\t  sampaitanggal,jumlahcuti,keterangan\r\n\t\t\t  )\r\n\t\t      values('".$kodeorg."',".$karyawanid.",\r\n\t\t\t  '".$periode."','".$dari."','".$sampai."',\r\n\t\t\t  ".$diambil.",'".$keterangan."'\r\n\t\t\t  )";

        break;
    default:
        break;
}
if (mysql_query($str)) {
    $strx = 'select sum(jumlahcuti) as diambil from '.$dbname.".sdm_cutidt\r\n\t\t       where kodeorg='".$kodeorg."'\r\n\t\t\t   and karyawanid=".$karyawanid."\r\n\t\t\t   and periodecuti='".$periode."'";
    $diambil = 0;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_object($resx)) {
        $diambil = $barx->diambil;
    }
    if ('' == $diambil) {
        $diambil = 0;
    }

    $strup = 'update '.$dbname.'.sdm_cutiht set diambil='.$diambil.',sisa=(hakcuti-'.$diambil.")\t\r\n\t\t       where kodeorg='".$kodeorg."'\r\n\t\t\t   and karyawanid=".$karyawanid."\r\n\t\t\t   and periodecuti='".$periode."'";
    mysql_query($strup);
} else {
    echo ' Gagal,'.addslashes(mysql_error($conn));
}

?>