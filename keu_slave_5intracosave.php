<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$jenis = $_POST['jenis'];
$akunpiutang = $_POST['akunpiutang'];
$akunhutang = $_POST['akunhutang'];
$kodeorgbef = $_POST['kodeorgbef'];
$jenisbef = $_POST['jenisbef'];
$noakunbef = $_POST['noakunbef'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".keu_5caco set kodeorg='".$kodeorg."', jenis='".$jenis."', akunpiutang='".$akunpiutang."',akunhutang='".$akunhutang."' \r\n\t       where kodeorg='".$kodeorgbef."' and jenis='".$jenisbef."' and akunpiutang='".$noakunbef."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".keu_5caco (kodeorg,jenis,akunpiutang,akunhutang)\r\n\t      values('".$kodeorg."','".$jenis."','".$akunpiutang."','".$akunhutang."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".keu_5caco \r\n\twhere kodeorg='".$kodeorg."' and jenis='".$jenis."' and akunpiutang='".$akunpiutang."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun where (noakun like '221%' or noakun like '122%' or noakun like '121%') and char_length(noakun)=7 order by noakun";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
$str1 = 'select * from '.$dbname.'.keu_5caco order by kodeorg';
if ($res1 = mysql_query($str1)) {
    echo "<table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t <tr class=rowheader><td style='width:100px;'>".$_SESSION['lang']['kodeorg']."</td><td style='width:40px;'>".$_SESSION['lang']['jenis']."</td><td>Akun Piutang</td><td>Akun Hutang</td><td  style='width:30px;'>*</td></tr>\r\n\t </thead>\r\n\t <tbody>";
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kodeorg.'</td>';
        if ('inter' === $bar1->jenis) {
            echo '<td>Inter</td>';
        } else {
            echo '<td align=right>Intra</td>';
        }

        echo '<td>'.$bar1->akunpiutang.' - '.$namaakun[$bar1->akunpiutang]."</td>\r\n                             <td>".$bar1->akunhutang.' - '.$namaakun[$bar1->akunhutang]."</td>    \r\n\t\t\r\n\t\t<td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenis."','".$bar1->akunpiutang."','".$bar1->akunhutang."');\"></td></tr>";
    }
    echo "\t \r\n\t </tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>";
}

?>