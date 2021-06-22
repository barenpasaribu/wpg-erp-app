<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$koderorg = $_POST['koderorg'];
$kapasitasolah = $_POST['kapasitasolah'];
$berlakusampai = tanggalsystemd($_POST['berlakusampai']);
$jammulai = tanggalsystemd($_POST['jam_mulai']);
$jamselesai = tanggalsystemd($_POST['jam_selesai']);
$kapasitaslori = $_POST['kapasitaslori'];
$method = $_POST['method'];
switch ($method) {
    case 'delete':
        $strx = 'delete from '.$dbname.".pabrik_5jampengolahan where koderorg='".$koderorg."' ";

        break;
    case 'update':
        $strx = 'update '.$dbname.".pabrik_5jampengolahan set kapasitasolah='".$kapasitasolah."',jammulai='".$jammulai."',jamselesai='".$jamselesai."',berlakusampai='".$berlakusampai."',kapasitaslori=".$kapasitaslori." where koderorg='".$koderorg."'";
        if (mysql_query($strx)) {
            echo '';
        } else {
            echo ' Gagal,'.mysql_error($conn);
        }

        break;
    case 'insert':
        $sql = 'select from '.$dbname.".pabrik_5jampengolahan where kodeorg='".$koderorg."'";
        $query = mysql_query($sql);
        $res = mysql_fetch_row($query);
        if ($res < 1) {
            $strx = 'insert into '.$dbname.".pabrik_5jampengolahan(\r\n\t\t\t\t\t\t   koderorg,kapasitasolah,jammulai,jamselesai,berlakusampai,kapasitaslori)\r\n\t\t\t\t\tvalues('".$koderorg."','".$kapasitasolah."','".$jammulai."','".$jamselesai."','".$berlakusampai."',".$kapasitaslori.')';
            if (mysql_query($strx)) {
                echo '';
            } else {
                echo ' Gagal,'.mysql_error($conn);
            }

            break;
        }

        echo 'warning:This Factory Already Input';
        exit();
    case 'load_data':
        $str = 'select * from '.$dbname.'.pabrik_5jampengolahan order by koderorg desc';
        if ($res = mysql_query($str)) {
            while ($bar = mysql_fetch_object($res)) {
                $noakun = $bar->noakun;
                $spr = 'select * from  '.$dbname.".organisasi where  kodeorganisasi='".$bar->koderorg."'";
                $rep = mysql_query($spr);
                $bas = mysql_fetch_object($rep);
                ++$no;
                echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t<td>".$no."</td>\r\n\t\t<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>\r\n\t\t<td id='kpsits_".$no."'>".$bar->kapasitasolah."</td>\r\n\t\t<td id='strt_".$no."'>".tanggalnormald($bar->jammulai)."</td>\r\n\t\t<td id='end_".$no."'>".tanggalnormald($bar->jamselesai)."</td>\r\n\t\t<td id='tglex_".$no."'>".tanggalnormald($bar->berlakusampai)."</td>\r\n\t\t<td id='kplori_".$no."'>".$bar->kapasitaslori."</td>\r\n\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->koderorg."','".$bar->kapasitasolah."','".tanggalnormald($bar->jammulai)."','".tanggalnormald($bar->jamselesai)."','".tanggalnormald($bar->berlakusampai)."','".$bar->kapasitaslori."');\"></td>\r\n\t\t<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delJampeng('".$bar->koderorg."');\"></td>\r\n\t\t</tr>";
            }
        } else {
            echo ' Gagal,'.mysql_error($conn);
        }

        break;
    default:
        break;
}

?>