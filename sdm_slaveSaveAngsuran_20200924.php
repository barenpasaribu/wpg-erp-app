<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$userid = $_POST['userid'];
$component = $_POST['idx'];
$total = $_POST['total'];
$start = $_POST['start'];
$lama = $_POST['lama'];
$active = $_POST['active'];
$method = $_POST['method'];
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$dt = mktime(0, 0, 0, (int) (substr($start, 5, 2)) + $lama - 1, 15, substr($start, 0, 4));
$end = date('Y-m', $dt);
if ('update' == $method) {
    $angsbln = $total / $lama;
    $str = 'update '.$dbname.".sdm_angsuran\r\n                       set total=".$total.",updateby='".$_SESSION['standard']['username']."',\r\n                           active=".$active.',jlhbln='.$lama.",\r\n                           start='".$start."',end='".$end."',bulanan=".$angsbln."\r\n                           where karyawanid=".$userid."\r\n                           and jenis=".$component;
} else {
    if ('insert' == $method) {
        $angsbln = $total / $lama;
        $str = 'insert into '.$dbname.".sdm_angsuran (karyawanid,jenis,total,updateby,jlhbln,bulanan,active,start,end)\r\n                       values(".$userid.','.$component.','.$total.",'".$_SESSION['standard']['username']."',".$lama.','.$angsbln.','.$active.",'".$start."','".$end."')";
    } else {
        if ('delete' == $method) {
            $str = 'delete from '.$dbname.".sdm_angsuran  \r\n                                   where karyawanid=".$userid."\r\n                               and jenis=".$component;
        }
    }
}

if (mysql_query($str, $conn)) {
    $str = 'select * from '.$dbname.".sdm_ho_component\r\n                                      where name like '%Angs%'";
    $res = mysql_query($str, $conn);
    $arr = [];
    $opt = '';
    while ($bar = mysql_fetch_object($res)) {
        $arr[$bar->id] = $bar->name;
    }
    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
        $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t\t\t\t\t\t\t  where u.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n\t\t\t\t\t\t\t\t\t  order by namakaryawan";
    } else {
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
            $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t\t\t\t\t\t\t  where u.tipekaryawan!=5 and \r\n\t\t\t\t\t\t\t\t\t  u.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t\t\t\t\t\t\t\t\t  order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t\t\t\t\t\t\t  where u.tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t\t\t\t\t\t\t\t  order by namakaryawan";
        }
    }

    $res = mysql_query($str, $conn);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n                                    <td class=firsttd>".$no."</td>\r\n                                    <td>".$bar->nik."</td>\r\n\t\t\t\t\t\t\t\t\t<td>".$bar->namakaryawan."</td>\r\n\t\t\t\t\t\t\t\t\t<td>".$bar->lokasitugas.' -- '.$nmOrg[$bar->lokasitugas]." </td>\r\n\t\t\t\t\t\t\t\t\t<td>".$arr[$bar->jenis]."</td>\r\n\t\t\t\t\t\t\t\t\t<td align=right>".number_format($bar->total, 2, '.', ',')."</td>\r\n\t\t\t\t\t\t\t\t\t<td align=center>".$bar->start."</td>\r\n\t\t\t\t\t\t\t\t\t<td align=center>".$bar->end."</td>\r\n\t\t\t\t\t\t\t\t\t<td align=right>".$bar->jlhbln."</td>\r\n\t\t\t\t\t\t\t\t\t<td align=right>".number_format($bar->bulanan, 2, '.', ',')."</td>\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t<td align=center>".((1 == $bar->active ? 'Active' : 'Not Active'))."</td>\r\n                                        <td>\r\n                             <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">\r\n                             &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">\t\t\t\r\n                                        </td>\r\n                                  </tr>";
    }
} else {
    echo ' Error: '.addslashes(mysql_error($conn));
}

?>