<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$type = 'regular';
$userid = $_POST['userid'];
$component = $_POST['component'];
$plus = $_POST['plus'];
$val = $_POST['val'];
$periode = $_SESSION['pyperiode'];
$terbilang = $_POST['terbilang'];
if (0 == $plus || '0' == $plus) {
    $val = $val * -1;
}

if (0 == $val) {
} else {
    if (isset($_POST['replace'])) {
        $str = 'delete from '.$dbname.".sdm_ho_detailmonthly\r\n\t      where karyawanid=".$userid."\r\n\t\t  and periode='".$periode."'";
        mysql_query($str, $conn);
        $str = 'insert into '.$dbname.".sdm_ho_detailmonthly \r\n\t\t(karyawanid,component,value,periode,plus,updatedby) \r\n\t\tvalues(".$userid.','.$component.','.$val.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";
        if (mysql_query($str, $conn)) {
        } else {
            echo ' Error: '.addslashes(mysql_error($conn));
        }
    } else {
        $str = 'select * from '.$dbname.".sdm_ho_detailmonthly\r\n\t      where karyawanid=".$userid.' and component='.$component."\r\n\t\t  and periode='".$periode."'";
        $res = mysql_query($str, $conn);
        if (0 < mysql_num_rows($res)) {
            echo ' Double';
        } else {
            $str = 'insert into '.$dbname.".sdm_ho_detailmonthly \r\n\t\t(karyawanid,component,value,periode,plus,updatedby) \r\n\t\tvalues(".$userid.','.$component.','.$val.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";
            if (mysql_query($str, $conn)) {
            } else {
                echo ' Error: '.addslashes(mysql_error($conn));
            }
        }

        if (1 == $component) {
            $stu = 'select * from '.$dbname.".sdm_ho_payrollterbilang\r\n\t\t\t\twhere userid=".$userid." and periode='".$periode."'\r\n\t\t\t\tand `type`='".$type."'";
            $resu = mysql_query($stu);
            if (0 < mysql_num_rows($resu)) {
                $stre = 'update '.$dbname.".sdm_ho_payrollterbilang\r\n\t\t\t\t\tset terbilang='".$terbilang."'\r\n\t\t\t\t\twhere userid=".$userid." and periode='".$periode."'\r\n\t\t\t\t\tand `type`='".$type."'";
            } else {
                $stre = 'insert into '.$dbname.".sdm_ho_payrollterbilang\r\n\t\t\t\t\t(userid,periode,`type`,terbilang)\r\n\t\t\t\t\tvalues(".$userid.",'".$periode."','".$type."','".$terbilang."')";
            }

            if (mysql_query($stre, $conn)) {
            } else {
                echo ' Error: gagal insert TERBILANG '.addslashes(mysql_error($conn));
            }
        }
    }
}

?>