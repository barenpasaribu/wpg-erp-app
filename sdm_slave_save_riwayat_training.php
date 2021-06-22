<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$jenistraining = $_POST['jenistraining'];
$judultraining = $_POST['judultraining'];
$penyelenggara = $_POST['penyelenggara'];
$trainingblnmulai = $_POST['trainingblnmulai'];
$trainingthnmulai = $_POST['trainingthnmulai'];
$trainingblnselesai = $_POST['trainingblnselesai'];
$trainingthnselesai = $_POST['trainingthnselesai'];
$sertifikat = $_POST['sertifikat'];
$biaya = $_POST['biaya'];
$karyawanid = $_POST['karyawanid'];
$nomor = $_POST['nomor'];
if ('' == $nilai) {
    $nilai = 0;
}

if (isset($_POST['del']) || '' != $trainingblnmulai && '' != $trainingthnmulai && '' != $trainingblnselesai && '' != $trainingthnselesai || isset($_POST['queryonly'])) {
    if (isset($_POST['del']) && 'true' == $_POST['del']) {
        $str = 'delete from '.$dbname.'.sdm_karyawantraining where nomor='.$nomor;
    } else {
        if (isset($_POST['queryonly'])) {
            $str = 'select 1=1';
        } else {
            $trainingblnmulai = $trainingblnmulai.'-'.$trainingthnmulai;
            $trainingblnselesai = $trainingblnselesai.'-'.$trainingthnselesai;
            $str = 'insert into '.$dbname.".sdm_karyawantraining\r\n\t     (\t`karyawanid`,\r\n\t\t\t`jenistraining`,\r\n\t\t\t`bulanmulai`,\r\n\t\t\t`bulanselesai`,\r\n\t\t\t`judultraining`,\r\n\t\t\t`penyelenggara`,\r\n\t\t\t`sertifikat`,\r\n                        `biaya`\r\n\t\t  )\r\n\t\t  values(".$karyawanid.",\r\n\t\t  '".$jenistraining."',\r\n\t\t  '".$trainingblnmulai."',\r\n\t\t  '".$trainingblnselesai."',\r\n\t\t  '".$judultraining."',\r\n\t\t  '".$penyelenggara."',\r\n\t\t  ".$sertifikat.",\r\n                  ".$biaya."\r\n\t\t  )";
        }
    }

    if (mysql_query($str)) {
        $str = "select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat \r\n\t       from ".$dbname.".sdm_karyawantraining\r\n\t \t\twhere karyawanid=".$karyawanid." \r\n\t\t\torder by bulanmulai desc";
        $res = mysql_query($str);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "\t  <tr class=rowcontent>\r\n\t\t\t  <td class=firsttd>".$no."</td>\r\n\t\t\t  <td>".$bar->jenistraining."</td>\t\t\t  \r\n\t\t\t  <td>".$bar->judultraining."</td>\r\n\t\t\t  <td>".$bar->penyelenggara."</td>\t\t\t  \r\n\t\t\t  <td>".$bar->bulanmulai."</td>\t\t\t  \r\n\t\t\t  <td>".$bar->bulanselesai."</td>\r\n\t\t\t  <td>".$bar->bersertifikat."</td>\r\n                          <td align=right>".$bar->biaya."</td>\r\n\t\t\t  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delTraining('".$karyawanid."','".$bar->nomor."');\"></td>\r\n\t\t\t</tr>";
        }
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn)).$str;
    }
} else {
    echo ' Error; Data incomplete';
}

?>