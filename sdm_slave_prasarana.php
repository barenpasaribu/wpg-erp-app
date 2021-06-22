<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$kdOrg = $_POST['kdOrg'];
$idKlmpk = $_POST['idKlmpk'];
$idJenis = $_POST['idJenis'];
$idLokasi = $_POST['idLokasi'];
$jmlhSarana = $_POST['jmlhSarana'];
$thnPerolehan = $_POST['thnPerolehan'];
$blnPerolehan = $_POST['blnPerolehan'];
$statFr = $_POST['statFr'];
$idData = $_POST['idData'];
$idData = $_POST['idData'];
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$sKlmpk = 'select distinct * from '.$dbname.'.sdm_5kl_prasarana order by kode asc';
$qKlmpk = mysql_query($sKlmpk);
while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
    $orgNmKlmpk[$rKlmpk['kode']] = $rKlmpk['nama'];
}
$optKlmpk2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sKlmpk2 = 'select distinct jenis,nama from '.$dbname.'.sdm_5jenis_prasarana order by nama asc';
$qKlmpk2 = mysql_query($sKlmpk2);
while ($rKlmpk2 = mysql_fetch_assoc($qKlmpk2)) {
    $orgNmKlmpk2[$rKlmpk2['jenis']] = $rKlmpk2['nama'];
}
switch ($method) {
    case 'insert':
        if ('' == $thnPerolehan || '' == $blnPerolehan || '' == $idLokasi || '' == $idJenis || '' == $idKlmpk || '' == $kdOrg) {
            echo 'warning:Semua Field tidak boleh kosong';
            exit();
        }

        if (12 < $blnPerolehan) {
            exit('Error:Bulan di luar standard');
        }

        if ('' == $jmlhSarana || '0' == $jmlhSarana) {
            exit('Error:Jumlah tidak boleh kosong');
        }

        $sCek = 'select * from '.$dbname.".sdm_prasarana where tahunperolehan='".$thnPerolehan."' and bulanperolehan='".$blnPerolehan."' and \r\n                       lokasi='".$idLokasi."' and kelompokprasarana='".$idKlmpk."' and jenisprasarana='".$idJenis."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            echo 'warning:Data Sudah ada';
            exit();
        }

        $sIns = 'insert into '.$dbname.".sdm_prasarana (kodeorg,  tahunperolehan, bulanperolehan, jumlah, kelompokprasarana, status, lokasi, jenisprasarana) \r\n                           values ('".$kdOrg."','".$thnPerolehan."','".$blnPerolehan."','".$jmlhSarana."','".$idKlmpk."','".$statFr."','".$idLokasi."','".$idJenis."')";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'loadData':
        $no = 0;
        $arr = ['Tidak Aktif', 'Aktif'];
        $str = 'select * from '.$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunperolehan,bulanperolehan desc";
        $res = mysql_query($str);
        $row = mysql_num_rows($res);
        if (0 < $row) {
            $limit = 20;
            $page = 0;
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                if ($page < 0) {
                    $page = 0;
                }
            }

            $offset = $page * $limit;
            $sql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunperolehan,bulanperolehan desc";
            $query2 = mysql_query($sql2);
            while ($jsl = mysql_fetch_object($query2)) {
                $jlhbrs = $jsl->jmlhrow;
            }
            $str = 'select * from '.$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunperolehan,bulanperolehan desc limit ".$offset.','.$limit.' ';
            $res = mysql_query($str);
            while ($bar = mysql_fetch_assoc($res)) {
                ++$no;
                echo "<tr class=rowcontent>\r\n                    <td>".$no."</td>\r\n                    <td>".$optNmOrg[$bar['kodeorg']]."</td>\r\n                    <td>".$orgNmKlmpk[$bar['kelompokprasarana']]."</td>\r\n                    <td>".$orgNmKlmpk2[$bar['jenisprasarana']]."</td>\r\n                    <td>".$optNmOrg[$bar['lokasi']]."</td>\r\n                    <td align=right>".number_format($bar['jumlah'], 0)."</td>\r\n                    <td align=right>".$bar['tahunperolehan']."</td>\r\n                    <td align=right>".$bar['bulanperolehan']."</td>\r\n                    <td>".$arr[$bar['status']]."</td>\r\n                    <td>\r\n                      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['kodeprasarana']."');\"> \r\n                      <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['kodeprasarana']."');\">\r\n                      </td>\r\n                    </tr>";
            }
            echo " <tr><td colspan=10 align=center>\r\n                        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                        <button class=mybutton onclick=cariBast2(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                        <button class=mybutton onclick=cariBast2(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                        </td>\r\n                        </tr>";
        } else {
            echo '<tr class=rowcontent><td colspan=10>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
    case 'update':
        if ('' == $thnPerolehan || '' == $blnPerolehan || '' == $idLokasi || '' == $idJenis || '' == $idKlmpk || '' == $kdOrg) {
            echo 'warning:Semua Field tidak boleh kosong';
            exit();
        }

        if (12 < $blnPerolehan) {
            exit('Error:Bulan di luar standard');
        }

        if ('' == $jmlhSarana || '0' == $jmlhSarana) {
            exit('Error:Jumlah tidak boleh kosong');
        }

        $sUpd = 'update '.$dbname.".sdm_prasarana set `tahunperolehan`='".$thnPerolehan."',`bulanperolehan`='".$blnPerolehan."',`jumlah`='".$jmlhSarana."',`kelompokprasarana`='".$idKlmpk."',\r\n                               status='".$statFr."',lokasi='".$idLokasi."',jenisprasarana='".$idJenis."'\r\n                               where kodeprasarana='".$idData."'";
        if (!mysql_query($sUpd)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".sdm_prasarana where kodeprasarana='".$idData."'";
        if (!mysql_query($sDel)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'getData':
        $sDt = 'select * from '.$dbname.".sdm_prasarana where kodeprasarana='".$idData."'";
        $qDt = mysql_query($sDt);
        $rDet = mysql_fetch_assoc($qDt);
        echo $rDet['tahunperolehan'].'###'.$rDet['bulanperolehan'].'###'.$rDet['jumlah'].'###'.$rDet['kelompokprasarana'].'###'.$rDet['status'].'###'.$rDet['lokasi'].'###'.$rDet['jenisprasarana'].'###'.$rDet['kodeprasarana'];

        break;
    case 'getSatuan':
        $sSatuan = 'select distinct satuan from '.$dbname.".sdm_5jenis_prasarana where jenis='".$idJenis."'";
        $qSatuan = mysql_query($sSatuan);
        $rSatuan = mysql_fetch_assoc($qSatuan);
        echo $rSatuan['satuan'];

        break;
    case 'getJenis':
        $optKlmpk2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sKlmpk2 = 'select distinct jenis,nama from '.$dbname.".sdm_5jenis_prasarana where kelompok='".$idKlmpk."' order by nama asc";
        $qKlmpk2 = mysql_query($sKlmpk2);
        while ($rKlmpk2 = mysql_fetch_assoc($qKlmpk2)) {
            if ('' != $idJenis) {
                $optKlmpk2 .= "<option value='".$rKlmpk2['jenis']."'  ".(($rKlmpk2['jenis'] == $idJenis ? 'selected' : '')).'>'.$rKlmpk2['nama'].'</option>';
            } else {
                $optKlmpk2 .= "<option value='".$rKlmpk2['jenis']."'>".$rKlmpk2['nama'].'</option>';
            }
        }
        echo $optKlmpk2;

        break;
    default:
        break;
}

?>