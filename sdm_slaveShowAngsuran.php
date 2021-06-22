<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$str = 'select * from '.$dbname.".sdm_ho_component\r\n      where name like '%Angs%'";
$res = mysql_query($str, $conn);
$arr = [];
$opt = '';
while ($bar = mysql_fetch_object($res)) {
    $arr[$bar->id] = $bar->name;
}
$val = trim($_POST['string']);
switch ($val) {
    case 'lunas':
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                      and `end`< '".date('Y-m')."'\r\n                                          order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid\r\n                                          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                      and `end`< '".date('Y-m')."'\r\n                                          order by namakaryawan";
        }

        break;
    case 'blmlunas':
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                      and `end`> '".date('Y-m')."'\r\n                                          order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid\r\n                                          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                      and `end`> '".date('Y-m')."'\r\n                                          order by namakaryawan";
        }

        break;
    case 'active':
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                      and `active`=1\r\n                                          order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid\r\n                                          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                      and `active`=1\r\n                                          order by namakaryawan";
        }

        break;
    case 'notactive':
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                      and `active`=0\r\n                                          order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid\r\n                                          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                      and `active`=0\r\n                                          order by namakaryawan";
        }

        break;
    case '':
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                      order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid\r\n                                          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                      order by namakaryawan";
        }

        break;
    default:
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                          and (`start`<='".$val."' AND `end`>='".$val."')\r\n                                          order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid \r\n                                          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                          and (`start`<='".$val."' AND `end`>='".$val."')\r\n                                          order by namakaryawan";
        }
}
if ($res = mysql_query($str, $conn)) {
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n                                <td class=firsttd>".$no."</td>\r\n                                <td>".$bar->karyawanid."</td>\r\n                                    <td>".$bar->namakaryawan."</td>\r\n                                    <td>".$arr[$bar->jenis]."</td>\r\n                                    <td align=right>".number_format($bar->total, 2, '.', ',')."</td>\r\n                                    <td align=center>".$bar->start."</td>\r\n                                    <td align=center>".$bar->end."</td>\r\n                                    <td align=right>".$bar->jlhbln."</td>\r\n                                    <td align=right>".number_format($bar->bulanan, 2, '.', ',')."</td>\t\t\t\t\r\n                                    <td align=center>".((1 == $bar->active ? 'Active' : 'Not Active'))."</td>\r\n                              </tr>";
        $ttl += $bar->bulanan;
    }
} else {
    echo 'Error:'.mysql_error($conn);
}

?>