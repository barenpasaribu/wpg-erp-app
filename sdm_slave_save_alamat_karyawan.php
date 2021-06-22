<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$alamatalamat = $_POST['alamatalamat'];
$alamatkota = $_POST['alamatkota'];
$alamatkodepos = $_POST['alamatkodepos'];
$alamattelepon = $_POST['alamattelepon'];
$alamatemplasement = $_POST['alamatemplasement'];
$alamatstatus = $_POST['alamatstatus'];
$alamatprovinsi = $_POST['alamatprovinsi'];
if ('' == $alamatstatus) {
    $alamatstatus = 0;
}

$karyawanid = $_POST['karyawanid'];
$nourut = $_POST['nomor'];
if ('' != $alamatalamat || 'true' == $_POST['del'] || isset($_POST['queryonly'])) {
    if ('' == $nourut) {
        $nourut = 0;
    }

    if (isset($_POST['del']) && 'true' == $_POST['del']) {
        $str = 'delete from '.$dbname.'.sdm_karyawanalamat where nomor='.$nourut;
    } else {
        if (isset($_POST['queryonly'])) {
            $str = 'select 1=1';
        } else {
            $str = 'insert into '.$dbname.".sdm_karyawanalamat\r\n\t\t     (`karyawanid`,\r\n\t\t\t\t  `alamat`,\r\n\t\t\t\t  `kota`,\r\n\t\t\t\t  `kodepos`,\r\n\t\t\t\t  `telepon`,\r\n\t\t\t\t  `emplasemen`,\r\n\t\t\t\t  `aktif`,\r\n\t\t\t\t  `provinsi`\r\n\t\t\t  )\r\n\t\t\t  values(".$karyawanid.",\r\n\t\t\t  '".$alamatalamat."',\r\n\t\t\t  '".$alamatkota."',\r\n\t\t\t  '".$alamatkodepos."',\r\n\t\t\t  '".$alamattelepon."',\r\n\t\t\t  '".$alamatemplasement."',\r\n\t\t\t  ".$alamatstatus.",\r\n\t\t\t  '".$alamatprovinsi."'\r\n\t\t\t  )";
        }
    }

    if (mysql_query($str)) {
        if (1 == $alamatstatus) {
            $strx = 'update '.$dbname.".datakaryawan set alamataktif='".$alamatalamat."',\r\n\t\t\tkota='".$alamatkota."', provinsi='".$alamatprovinsi."'\r\n\t\t\twhere karyawanid=".$karyawanid;
            mysql_query($strx);
        }

        $str = "select *,case aktif when 1 then 'Yes' when 0 then 'No' end as status from ".$dbname.'.sdm_karyawanalamat where karyawanid='.$karyawanid.' order by nomor desc';
        $res = mysql_query($str);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "\t  <tr class=rowcontent>\r\n\t\t\t\t  <td class=firsttd>".$no."</td>\r\n\t\t\t\t  <td>".$bar->alamat."</td>\t\t\t  \r\n\t\t\t\t  <td>".$bar->kota."</td>\r\n\t\t\t\t  <td>".$bar->provinsi."</td>\t\t\t  \r\n\t\t\t\t  <td>".$bar->kodepos."</td>\t\t\t  \r\n\t\t\t\t  <td>".$bar->emplasemen."</td>\r\n\t\t\t\t  <td>".$bar->status."</td>\r\n\t\t\t\t  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delAlamat('".$karyawanid."','".$bar->nomor."');\"></td>\r\n\t\t\t\t</tr>";
        }
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn)).$str;
    }
} else {
    echo ' Error: Incorrect Period';
}

?>