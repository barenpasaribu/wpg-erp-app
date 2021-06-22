<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$kdOrg = $_POST['kdOrg'];
$method = $_POST['method'];
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
switch ($method) {
    case 'getKar':
        if ('' == $kdOrg || '0' == $kdOrg) {
            $str1 = 'select * from '.$dbname.".datakaryawan\r\n\t\t\t\t where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') \r\n\t\t\t\t  and tipekaryawan!=5 \r\n\t\t\t\t  and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t\t\t\t  order by namakaryawan";
        } else {
            $str1 = 'select * from '.$dbname.".datakaryawan\r\n\t\t\t\t where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') \r\n\t\t\t\t  and tipekaryawan!=5 \r\n\t\t\t\t  and lokasitugas='".$kdOrg."'\r\n\t\t\t\t  order by namakaryawan";
        }

        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $opt1 .= '<option value='.$bar1->karyawanid.'>'.$bar1->namakaryawan.' -- '.$bar1->nik.' -- '.$bar1->lokasitugas.'['.$nmOrg[$bar1->lokasitugas].']</option>';
        }
        echo $opt1;

        break;
    case 'loadData':
        $str = 'select * from '.$dbname.".sdm_ho_component\r\n\t\twhere name like '%Angs%'";
        $res = mysql_query($str, $conn);
        $arr = [];
        $opt = '';
        while ($bar = mysql_fetch_object($res)) {
            $opt .= '<option value='.$bar->id.'>'.$bar->name.'</option>';
            $arr[$bar->id] = $bar->name;
        }
        if ('' == $kdOrg || '0' == $kdOrg) {
            $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t  where u.tipekaryawan!=5 and \r\n\t\t\t  u.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t\t\t  order by namakaryawan";
        } else {
            $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t  where u.tipekaryawan!=5 and \r\n\t\t\t  u.lokasitugas='".$kdOrg."'\r\n\t\t\t  order by namakaryawan";
        }

        $res = mysql_query($str, $conn);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n\t\t\t\t\t<td class=firsttd>".$no."</td>\r\n\t\t\t\t\t<td>".$bar->nik."</td>\r\n\t\t\t\t\t\t<td>".$bar->namakaryawan."</td>\r\n\t\t\t\t\t\t<td>".$bar->lokasitugas.' -- '.$nmOrg[$bar->lokasitugas]." </td>\r\n\t\t\t\t\t\t<td>".$arr[$bar->jenis]."</td>\r\n\t\t\t\t\t\t<td align=right>".number_format($bar->total, 2, '.', ',')."</td>\r\n\t\t\t\t\t\t<td align=center>".$bar->start."</td>\r\n\t\t\t\t\t\t<td align=center>".$bar->end."</td>\r\n\t\t\t\t\t\t<td align=right>".$bar->jlhbln."</td>\r\n\t\t\t\t\t\t<td align=right>".number_format($bar->bulanan, 2, '.', ',')."</td>\t\t\t\t\r\n\t\t\t\t\t\t<td align=center>".((1 == $bar->active ? 'Active' : 'Not Active'))."</td>\r\n\t\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">\r\n\t\t\t\t\t &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">\t\t\r\n\t\t\t\t\t\t\t\t</td>\t\t\t\t\r\n\t\t\t\t  </tr>";
        }

        break;
    default:
        break;
}

?>