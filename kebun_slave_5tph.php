<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_POST['kodeorg'];
$notph = $_POST['notph'];
$keterangan = $_POST['keterangan'];
switch ($_POST['aksi']) {
    case 'save':
        $str = 'insert into '.$dbname.".kebun_5tph(kode,kodeorg,keterangan) values('".$notph."','".$kodeorg."','".$keterangan."')";
        if (mysql_query($str)) {
        } else {
            echo ' Error:'.addslashes(mysql_error($conn));
        }

        break;
    case 'edit':
        $str = 'update '.$dbname.".kebun_5tph set keterangan='".$keterangan."' where kodeorg='".$kodeorg."'\r\n               and kode='".$notph."'";
        if (mysql_query($str)) {
        } else {
            echo ' Error:'.addslashes(mysql_error($conn));
        }

        break;
    case 'del':
        $str = 'delete from '.$dbname.".kebun_5tph  where kodeorg='".$kodeorg."'\r\n               and kode='".$notph."'";
        if (mysql_query($str)) {
        } else {
            echo ' Error:'.addslashes(mysql_error($conn));
        }

        break;
    case 'list':
        break;
    default:
        break;
}
$str = 'select * from '.$dbname.".kebun_5tph where kodeorg='".$kodeorg."' order by kode";
$res = mysql_query($str);
echo "<table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n       <tr class=rowheader>\r\n           <td>".$_SESSION['lang']['no']."</td>\r\n           <td>".$_SESSION['lang']['kodeorg']."</td>\r\n           <td>".$_SESSION['lang']['notph']."</td>    \r\n           <td>".$_SESSION['lang']['keterangan']."</td>\r\n           <td>".$_SESSION['lang']['action']."</td>    \r\n       </tr>\r\n     </thead>\r\n     <tbody>";
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n           <td>".$no."</td>\r\n           <td>".$bar->kodeorg."</td>\r\n           <td>".$bar->kode."</td>    \r\n           <td>".$bar->keterangan."</td>\r\n           <td>\r\n               <img id='detail_edit' title='dedit data' class=zImgBtn onclick=\"editData('".$bar->kodeorg."','".$bar->kode."','".$bar->keterangan."')\" src='images/application/application_edit.png'/>    \r\n               <img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteData('".$bar->kodeorg."','".$bar->kode."')\" src='images/application/application_delete.png'/>\r\n           </td>    \r\n           \r\n       </tr> ";
}
echo "</tbody>\r\n    <tfoot></tfoot></table>";

?>