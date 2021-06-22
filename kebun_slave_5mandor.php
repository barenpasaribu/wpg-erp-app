<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$mandor = $_POST['mandor'];
$karyawan = $_POST['karyawan'];
$urut = $_POST['urut'];
$aktif = $_POST['aktif'];
switch ($method) {
    case 'tampilmandor':
        $str = 'select distinct(a.mandorid), b.namakaryawan from '.$dbname.".kebun_5mandor a\r\n        left join ".$dbname.".datakaryawan b on a.mandorid = b.karyawanid\r\n        ";
        $res = mysql_query($str) ;
        while ($bar = mysql_fetch_assoc($res)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n        <td>".$no."</td>\r\n        <td align=left onclick=pilihmandor('".$bar['mandorid']."') style=\"cursor:pointer;\">".$bar['namakaryawan']."</td>\r\n        <td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapusmandor('".$bar['mandorid']."');\"></td>\r\n        </tr>";
        }

        break;
    case 'tampilkaryawan':
        $optkaryawan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $str = 'select karyawanid, namakaryawan from '.$dbname.".datakaryawan\r\n            where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and alokasi = 0\r\n                and karyawanid != '".$mandor."'\r\n            order by namakaryawan";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $optkaryawan .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->karyawanid.']</option>';
        }
        echo $optkaryawan;

        break;
    case 'pilihmandor':
        $no = 0;
        $str = 'select a.karyawanid, b.namakaryawan, a.statusaktif, a.mandorid from '.$dbname.".kebun_5mandor a\r\n        left join ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid\r\n        where a.mandorid='".$mandor."'\r\n        order by a.nourut";
        $res = mysql_query($str) ;
        echo '<table>';
        $statusaktif[0] = '';
        $statusaktif[1] = 'Aktif';
        while ($bar = mysql_fetch_assoc($res)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n        <td>".$no."</td>\r\n        <td align=left>".$bar['namakaryawan']."</td>\r\n        <td align=center title='Set Aktif' onclick=\"aktifkaryawan('".$bar['karyawanid']."','".$bar['mandorid']."','".$bar['statusaktif']."');\" style=\"cursor:pointer;\">[ ".$statusaktif[$bar['statusaktif']]." ]</td>\r\n        <td align=center><img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"hapuskaryawan('".$bar['karyawanid']."');\"></td>\r\n        </tr>";
        }
        echo '</table>';

        break;
    case 'tambahkaryawan':
        $sIns = 'insert into '.$dbname.".kebun_5mandor (`mandorid`,`karyawanid`,`statusaktif`,`nourut`,`updateby`) \r\n        values ('".$mandor."','".$karyawan."','1','".$urut."','".$_SESSION['standard']['userid']."')";
        if (!mysql_query($sIns)) {
            echo 'Gagal : '.mysql_error($conn);
        }

        break;
    case 'hapuskaryawan':
        $sIns = 'delete from '.$dbname.".kebun_5mandor where mandorid='".$mandor."' and karyawanid='".$karyawan."'";
        if (!mysql_query($sIns)) {
            echo 'Gagal : '.mysql_error($conn);
        }

        break;
    case 'hapusmandor':
        $sIns = 'delete from '.$dbname.".kebun_5mandor where mandorid='".$mandor."'";
        if (!mysql_query($sIns)) {
            echo 'Gagal : '.mysql_error($conn);
        }

        break;
    case 'aktifkaryawan':
        if ('1' === $aktif) {
            $aktif = '0';
        } else {
            $aktif = '1';
        }

        $sIns = 'update '.$dbname.".kebun_5mandor set statusaktif ='".$aktif."' where mandorid='".$mandor."' and karyawanid = '".$karyawan."'";
        if (!mysql_query($sIns)) {
            echo 'Gagal : '.mysql_error($conn);
        }

        break;
    case 'insert':
        $qwe = explode('-', $periode);
        $periode = $qwe[0].$qwe[1];
        if ('' === $hkefektif) {
            echo 'warning : Silakan memilih periode.';
            exit();
        }

        if ($hkefektif <= 0) {
            echo 'warning : HK Efektif <= 0.';
            exit();
        }

        $sIns = 'insert into '.$dbname.".sdm_hk_efektif (`periode`,`minggu`,`libur`,`hkefektif`,`catatan`) \r\n        values ('".$periode."','".$hariminggu."','".$harilibur."','".$hkefektif."','".$catatan."')";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'delete':
        $sIns = 'delete from '.$dbname.".sdm_hk_efektif where periode = '".$periode."'";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    default:
        break;
}

?>