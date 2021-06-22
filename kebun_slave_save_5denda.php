<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$method = $_POST['method'];
$sreg = 'select distinct regional from '.$dbname.".bgt_regional_assignment \n                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg = mysql_query($sreg) ;
$rreg = mysql_fetch_assoc($qreg);
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".kebun_5denda set nama='".$_POST['tanggal']."',jumlah='".$_POST['ket']."'\n               ,updateby='".$_SESSION['standard']['userid']."'\n\t       where kode='".$_POST['regId']."' ";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $scek = 'select distinct * from '.$dbname.".kebun_5denda where kode='".$_POST['regId']."'";
        $qcek = mysql_query($scek) ;
        if (0 === mysql_num_rows($qcek)) {
            $sIns = 'insert into '.$dbname.".kebun_5denda (`kode`, `nama`, `jumlah`, `updateby`) values \n                   ('".$_POST['regId']."','".$_POST['tanggal']."','".$_POST['ket']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sIns)) {
                echo ' error,'.addslashes(mysql_error($conn));
            }

            break;
        }

        exit('error: Data already exist');
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5harilibur\n\t where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\n\t     <thead>\n\t\t <tr class=rowheader>\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['kode']."</td>\n\t\t\t<td>".$_SESSION['lang']['nama']."</td>\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\n\t\t\t<td style='width:30px;'>*</td></tr>\n\t\t </thead><tbody >";
        $sdata = 'select distinct * from '.$dbname.'.kebun_5denda';
        $res1 = mysql_query($sdata) ;
        if (0 < mysql_num_rows($res1)) {
            while ($bar1 = mysql_fetch_object($res1)) {
                echo "<tr class=rowcontent>\n\t\t      <td align=center>".$bar1->kode."</td>\n\t\t      <td>".$bar1->nama."</td>\n\t\t      <td align=right>".$bar1->jumlah."</td>\n\t\t      <td><img src=images/application/application_edit.png class=resicon  caption='Edit' \n                                       onclick=\"fillField('".$bar1->kode."','".$bar1->nama."','".$bar1->jumlah."');\"></td></tr>";
            }
            echo "\t \n\t\t </tbody>\n\t\t <tfoot>\n         \n\t\t </tfoot>\n\t\t </table>";
        } else {
            echo '<tr class=rowcontent><td colspan=4>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
}

?>