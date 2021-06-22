<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$levelpendidikan = $_POST['levelpendidikan'];
$tahunlulus = $_POST['tahunlulus'];
$spesialisasi = $_POST['spesialisasi'];
$gelar = $_POST['gelar'];
$namasekolah = $_POST['namasekolah'];
$nilai = $_POST['nilai'];
$pendidikankota = $_POST['pendidikankota'];
$keterangan = $_POST['pendidikanketerangan'];
$karyawanid = $_POST['karyawanid'];
$kode = $_POST['kode'];
if ('' == $nilai) {
    $nilai = 0;
}

if (isset($_POST['del']) && 'true' == $_POST['del']) {
    $str = 'delete from '.$dbname.'.sdm_karyawanpendidikan where kode='.$kode;
} else {
    if (isset($_POST['queryonly'])) {
        $str = 'select 1=1';
    } else {
        $str = 'insert into '.$dbname.".sdm_karyawanpendidikan\r\n\t     (    `karyawanid`,\r\n\t\t      `levelpendidikan`,\r\n\t\t\t  `spesialisasi`,\r\n\t\t\t  `gelar`,\r\n\t\t\t  `tahunlulus`,\r\n\t\t\t  `namasekolah`,\r\n\t\t\t  `nilai`,\r\n\t\t\t  `kota`,\r\n\t\t\t  `keterangan`\r\n\t\t  )\r\n\t\t  values(".$karyawanid.",\r\n\t\t  ".$levelpendidikan.",\r\n\t\t  '".$spesialisasi."',\r\n\t\t  '".$gelar."',\r\n\t\t  '".$tahunlulus."',\r\n\t\t  '".$namasekolah."',\r\n\t\t  ".$nilai.",\r\n\t\t  '".$pendidikankota."',\r\n\t\t  '".$keterangan."'\r\n\t\t  )";
    }
}

if (mysql_query($str)) {
    $str = 'select a.*,b.kelompok from '.$dbname.'.sdm_karyawanpendidikan a,'.$dbname.".sdm_5pendidikan b\r\n\t \t\twhere a.karyawanid=".$karyawanid." \r\n\t \t\tand a.levelpendidikan=b.levelpendidikan\r\n\t\t\torder by a.levelpendidikan desc";
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo "\t  <tr class=rowcontent>\r\n\t\t\t  <td class=firsttd>".$no."</td>\r\n\t\t\t  <td>".$bar->kelompok."</td>\t\t\t  \r\n\t\t\t  <td>".$bar->namasekolah."</td>\r\n\t\t\t  <td>".$bar->kota."</td>\t\t\t  \r\n\t\t\t  <td>".$bar->spesialisasi."</td>\t\t\t  \r\n\t\t\t  <td>".$bar->tahunlulus."</td>\r\n\t\t\t  <td>".$bar->gelar."</td>\r\n\t\t\t  <td>".$bar->nilai."</td>\r\n\t\t\t  <td>".$bar->keterangan."</td>\r\n\t\t\t  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan('".$karyawanid."','".$bar->kode."');\"></td>\r\n\t\t\t</tr>";
    }
} else {
    echo ' Gagal:'.addslashes(mysql_error($conn)).$str;
}

?>