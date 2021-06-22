<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$jabatan = $_POST['jabatan'];
$jabatan2 = $_POST['jabatan2'];
$kategori = $_POST['kategori'];
$topik = $_POST['topik'];
$remark = $_POST['remark'];
$matrixid = $_POST['matrixid'];
$method = $_POST['method'];
$optJabat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan order by kodejabatan asc';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    if ($rJabat['kodejabatan'] == $jabatan2) {
        $pilih = ' selected';
    } else {
        $pilih = '';
    }

    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
    $optJabat .= "<option value='".$rJabat['kodejabatan']."'".$pilih.'>'.$rJabat['namajabatan'].'</option>';
}
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5matriktraining set topik='".$topik."', catatan='".$remark."'\r\n        where matrixid='".$matrixid."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5matriktraining (kodejabatan,kategori,topik,catatan)\r\n        values('".$jabatan."','".$kategori."','".$topik."','".$remark."')";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5matriktraining\r\n    where matrixid='".$matrixid."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    default:
        break;
}
echo "<table><tr>\r\n        <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n        <td><select id=jabatan2 onchange=pilihjabatan()>".$optJabat."</select></td>\r\n    </tr></table>";
$str1 = 'select * from '.$dbname.".sdm_5matriktraining where kodejabatan like '%".$jabatan2."%' order by kodejabatan, kategori, topik";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['kategori']."</td>\r\n        <td>".$_SESSION['lang']['topik']."</td>\r\n        <td>".$_SESSION['lang']['catatan']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n        <td>".$kamusJabat[$bar1->kodejabatan]."</td>\r\n        <td>".$bar1->kategori."</td>\r\n        <td>".$bar1->topik."</td>\r\n        <td>".$bar1->catatan."</td>\r\n        <td align=center>\r\n            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->kategori."','".$bar1->topik."','".$bar1->catatan."','".$bar1->matrixid."');\">\r\n            <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"hapus('".$bar1->matrixid."');\">\r\n        </td>\r\n    </tr>";
}
echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";

?>