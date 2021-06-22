<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$keluarganama = $_POST['keluarganama'];
$keluargajk = $_POST['keluargajk'];
$keluargatmplahir = $_POST['keluargatmplahir'];
if ('' == $_POST['keluargatgllahir']) {
    $_POST['keluargatgllahir'] = '00-00-0000';
}

$keluargatgllahir = tanggalsystem($_POST['keluargatgllahir']);
$keluargapekerjaan = $_POST['keluargapekerjaan'];
$keluargatelp = $_POST['keluargatelp'];
$keluargaemail = $_POST['keluargaemail'];
$karyawanid = $_POST['karyawanid'];
$hubungankeluarga = $_POST['hubungankeluarga'];
$keluargastatus = $_POST['keluargastatus'];
$keluargapendidikan = $_POST['keluargapendidikan'];
$keluargatanggungan = $_POST['keluargatanggungan'];
$method = $_POST['method'];
$karyawanid = $_POST['karyawanid'];
$nomor = $_POST['nomor'];
if ('' == $nilai) {
    $nilai = 0;
}

if (isset($_POST['del']) || '' != $keluarganama || isset($_POST['queryonly'])) {
    if (isset($_POST['del']) && 'true' == $_POST['del']) {
        $str = 'delete from '.$dbname.'.sdm_karyawankeluarga where nomor='.$nomor;
    } else {
        if (isset($_POST['queryonly'])) {
            $str = 'select 1=1';
        } else {
            if ('insert' == $method) {
                $str = 'insert into '.$dbname.".sdm_karyawankeluarga\r\n                     (\t`karyawanid`,\r\n                                `nama`,\r\n                                `jeniskelamin`,\r\n                                `tempatlahir`,\r\n                                `tanggallahir`,\r\n                                `hubungankeluarga`,\r\n                                `status`,\r\n                                `levelpendidikan`,\r\n                                `pekerjaan`,\r\n                                `telp`,\r\n                                `email`,\r\n                                `tanggungan`\r\n                          )\r\n                          values(".$karyawanid.",\r\n                          '".$keluarganama."',\r\n                          '".$keluargajk."',\r\n                          '".$keluargatmplahir."',\r\n                          ".$keluargatgllahir.",\r\n                          '".$hubungankeluarga."',\r\n                          '".$keluargastatus."',\r\n                          '".$keluargapendidikan."',\r\n                          '".$keluargapekerjaan."',\r\n                          '".$keluargatelp."',\r\n                          '".$keluargaemail."',\r\n                          '".$keluargatanggungan."'\r\n                          )";
            } else {
                $str = 'update '.$dbname.".sdm_karyawankeluarga set\r\n                     `karyawanid`=".$karyawanid.",\r\n                                `nama`='".$keluarganama."',\r\n                                `jeniskelamin`='".$keluargajk."',\r\n                                `tempatlahir`='".$keluargatmplahir."',\r\n                                `tanggallahir`=".$keluargatgllahir.",\r\n                                `hubungankeluarga`='".$hubungankeluarga."',\r\n                                `status`='".$keluargastatus."',\r\n                                `levelpendidikan`=".$keluargapendidikan.",\r\n                                `pekerjaan`='".$keluargapekerjaan."',\r\n                                `telp`='".$keluargatelp."',\r\n                                `email`='".$keluargaemail."',\r\n                                `tanggungan`=".$keluargatanggungan."\r\n                                where nomor=".$nomor;
            }
        }
    }

    if (mysql_query($str)) {
        $str = "select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, \r\n                       b.kelompok,COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',a.tanggallahir)/365.25,1),0) as umur\r\n                           from ".$dbname.'.sdm_karyawankeluarga a,'.$dbname.".sdm_5pendidikan b\r\n                                where a.karyawanid=".$karyawanid." \r\n                                and a.levelpendidikan=b.levelpendidikan\r\n                                order by hubungankeluarga";
        $res = mysql_query($str);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            if ('EN' == $_SESSION['language']) {
                switch ($bar->hubungankeluarga) {
                    case 'Pasangan':
                        $val = 'Couple';

                        break;
                    case 'Anak':
                        $val = 'Child';

                        break;
                    case 'Ibu':
                        $val = 'Mother';

                        break;
                    case 'Bapak':
                        $val = 'Father';

                        break;
                    case 'Adik':
                        $val = 'Younger brother/sister';

                        break;
                    case 'Kakak':
                        $val = 'Older brother/sister';

                        break;
                    case 'Ibu Mertua':
                        $val = 'Monther-in-law';

                        break;
                    case 'Bapak Mertua':
                        $val = 'Father-in-law';

                        break;
                    case 'Sepupu':
                        $val = 'Cousin';

                        break;
                    case 'Ponakan':
                        $val = 'Nephew';

                        break;
                    default:
                        $val = 'Foster child';

                        break;
                }
            } else {
                $val = $bar->hubungankeluarga;
            }

            if ('EN' == $_SESSION['language'] && 'Kawin' == $bar->status) {
                $gal = 'Married';
            }

            if ('EN' == $_SESSION['language'] && ('Bujang' == $bar->status || 'Lajang' == $bar->status)) {
                $gal = 'Single';
            } else {
                $gal = $bar->status;
            }

            echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$no."</td>\r\n                                  <td>".$bar->nama."</td>\t\t\t  \r\n                                  <td>".$bar->jeniskelamin."</td>\r\n                                  <td>".$val."</td>\t\t\t  \r\n                                  <td>".$bar->tempatlahir.','.tanggalnormal($bar->tanggallahir)."</td>\t\t\t  \r\n                                  <td>".$gal."</td>\r\n                                                                  <td>".$bar->umur."Yrs</td>\r\n                                  <td>".$bar->kelompok."</td>\r\n                                  <td>".$bar->pekerjaan."</td>\r\n                                  <td>".$bar->telp."</td>\r\n                                  <td>".$bar->email."</td>\r\n                                  <td>".$bar->tanggungan1."</td>\r\n                                  <td>\r\n                                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->nama."','".$bar->jeniskelamin."','".$bar->tempatlahir."','".tanggalnormal($bar->tanggallahir)."','".$bar->hubungankeluarga."','".$bar->status."','".$bar->levelpendidikan."','".$bar->pekerjaan."','".$bar->telp."','".$bar->email."','".$bar->tanggungan."','".$bar->nomor."');\"> \r\n                                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKeluarga('".$karyawanid."','".$bar->nomor."');\">\r\n                                  </td>\r\n                                </tr>";
        }
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn)).$str;
    }
} else {
    echo ' Error; Data incomplete';
}

?>