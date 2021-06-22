<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'getAllPt':
        if ('all' == $param['tipe']) {
            $pt = $_SESSION['empl']['kodeorganisasi'];
            $str = 'select karyawanid,namakaryawan,subbagian from '.$dbname.".datakaryawan where kodeorganisasi='".$pt."'  and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') order by namakaryawan";
        } else {
            $subbagian = substr($param['kodeorg'], 0, 4);
            $str = 'select karyawanid,namakaryawan,subbagian from '.$dbname.".datakaryawan where lokasitugas='".$subbagian."'  and (tanggalkeluar='0000-00-00' or tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') order by namakaryawan";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            echo "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' - '.$bar->subbagian.'</option>';
        }

        break;
    default:
        break;
}

?>