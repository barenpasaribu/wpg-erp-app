<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'add':
        $qKeg = selectQuery($dbname, 'kebun_prestasi', '*', "notransaksi='".$param['notransaksi']."'");
        $resKeg = fetchData($qKeg);
        if (empty($resKeg)) {
            echo 'Warning : Kegiatan harus diisi lebih dahulu';
            exit();
        }

        $selQuery = selectQuery($dbname, 'kebun_kehadiran', 'nourut', "notransaksi='".$param['notransaksi']."'");
        $nourut = fetchData($selQuery);
        $maxNoUrut = 1;
        if (!empty($nourut)) {
            foreach ($nourut as $row) {
                ($maxNoUrut <= $row['nourut'] ? ($maxNoUrut = $row['nourut']) : false);
            }
            ++$maxNoUrut;
        }

        $tanggal = substr($param['notransaksi'], 0, 8);
        $str = 'select sum(jhk) as jum from '.$dbname.'.kebun_kehadiran_vw where tanggal='.$tanggal."\n              and karyawanid='".$param['nik']."' group by karyawanid";
        $res = mysql_query($str);
        $datr = mysql_fetch_assoc($res);
        $str1 = 'select * from '.$dbname.'.sdm_absensidt where tanggal='.$tanggal." \n              and karyawanid=".$param['nik'];
        $res1 = mysql_query($str1);
        if (1 < $datr['jum'] + $param['jhk']) {
            $not = '';
            $str = 'select * from '.$dbname.'.kebun_kehadiran_vw where tanggal='.$tanggal."\n                  and karyawanid='".$param['nik']."'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $not .= "\n".$bar->notransaksi;
            }
            exit('Error: Karyawan tersebut sudah memiliki absen lebih dari satu HK ('.$not.')__'.$datr['jum']);
        }

        if (0 < mysql_num_rows($res1)) {
            exit('Error: Karyawan tersebut sudah memiliki absen pada daftar absen untuk hari yang sama');
        }

        $cols = ['nourut', 'nik', 'absensi', 'jhk', 'umr', 'insentif', 'hasilkerja', 'notransaksi'];
        $data = $param;
        $data['nourut'] = $maxNoUrut;
        unset($data['numRow']);
        $query = insertQuery($dbname, 'kebun_kehadiran', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['notransaksi']);
        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['notransaksi'], $data['nourut']);

        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "notransaksi='".$param['notransaksi']."' and nourut='".$param['cond_nourut']."'";
        $query = updateQuery($dbname, 'kebun_kehadiran', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."' and nourut='".$param['nourut']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_kehadiran` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>