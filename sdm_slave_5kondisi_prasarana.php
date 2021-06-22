<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$kdSarana = $_POST['kdSarana'];
$tglKonSarana = tanggalsystem($_POST['tglKonSarana']);
$kondId = $_POST['kondId'];
$idProgress = $_POST['idProgress'];
$jmlhSarana = $_POST['jmlhSarana'];
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKlmpk2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sKlmpk2 = 'select distinct jenis,nama,satuan from '.$dbname.'.sdm_5jenis_prasarana order by nama asc';
$qKlmpk2 = mysql_query($sKlmpk2);
while ($rKlmpk2 = mysql_fetch_assoc($qKlmpk2)) {
    $orgNmKlmpk2[$rKlmpk2['jenis']] = $rKlmpk2['nama'];
    $arrSat[$rKlmpk2['jenis']] = $rKlmpk2['satuan'];
}
switch ($method) {
    case 'insert':
        if ('' == $kdSarana || '' == $tglKonSarana || '' == $kondId || '' == $idProgress) {
            echo 'warning:Semua Field tidak boleh kosong';
            exit();
        }

        if ('' == $jmlhSarana || '0' == $jmlhSarana) {
            exit('Error:Jumlah tidak boleh kosong');
        }

        $sCek2 = 'select distinct jumlah,jenisprasarana from '.$dbname.".sdm_prasarana where kodeprasarana='".$kdSarana."'";
        $qCek2 = mysql_query($sCek2);
        $rCek2 = mysql_fetch_assoc($qCek2);
        if ($rCek2['jumlah'] < $jmlhSarana) {
            exit('Error:Jumlah tidak boleh lebih dari '.$arrSat[$rCek2['jenisprasarana']].' yang tersedia');
        }

        $sCek = 'select * from '.$dbname.".sdm_kondisi_prasarana where kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            echo 'warning:Data Sudah ada';
            exit();
        }

        $sIns = 'insert into '.$dbname.".sdm_kondisi_prasarana (kodeprasarana, jumlah, kondisi, tanggal, progress, karyawanid) \r\n                           values ('".$kdSarana."','".$jmlhSarana."','".$kondId."','".$tglKonSarana."','".$idProgress."','".$_SESSION['standard']['userid']."')";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'loadData':
        $no = 0;
        $arrProgrs = [1 => $_SESSION['lang']['slsiPerbaikan'], 2 => $_SESSION['lang']['dlmPerbaikan']];
        $str = 'select a.* from '.$dbname.'.sdm_kondisi_prasarana a  left join '.$dbname.".sdm_prasarana b on a.kodeprasarana=b.kodeprasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n                           order by tahunperolehan,bulanperolehan desc";
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
            $sql2 = 'select count(*) as jmlhrow from '.$dbname.'.sdm_kondisi_prasarana a  left join '.$dbname.".sdm_prasarana b on a.kodeprasarana=b.kodeprasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n                           order by tahunperolehan,bulanperolehan desc";
            $query2 = mysql_query($sql2);
            while ($jsl = mysql_fetch_object($query2)) {
                $jlhbrs = $jsl->jmlhrow;
            }
            $str = 'select a.*,b.jenisprasarana,b.lokasi  from '.$dbname.'.sdm_kondisi_prasarana a  left join '.$dbname.".sdm_prasarana b on a.kodeprasarana=b.kodeprasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n                           order by tahunperolehan,bulanperolehan desc limit ".$offset.','.$limit.' ';
            $res = mysql_query($str);
            while ($bar = mysql_fetch_assoc($res)) {
                ++$no;
                echo "<tr class=rowcontent>\r\n                    <td>".$no."</td>\r\n                    <td>".$bar['kodeprasarana']."</td>\r\n                    <td>".$orgNmKlmpk2[$bar['jenisprasarana']]."</td>\r\n                    <td>".$bar['lokasi']."</td>\r\n                    <td>".tanggalnormal($bar['tanggal'])."</td>\r\n                    <td>".$bar['kondisi']."</td>\r\n                    <td>".$arrProgrs[$bar['progress']]."</td>\r\n                    <td align=right>".number_format($bar['jumlah'], 0)."</td>\r\n                    <td>".$arrSat[$bar['jenisprasarana']]."</td>\r\n                    <td>\r\n                      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['kodeprasarana']."','".tanggalnormal($bar['tanggal'])."');\"> \r\n                      <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['kodeprasarana']."','".tanggalnormal($bar['tanggal'])."');\">\r\n                      </td>\r\n                    </tr>";
            }
            echo " <tr><td colspan=10 align=center>\r\n                        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                        <button class=mybutton onclick=cariBast2(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                        <button class=mybutton onclick=cariBast2(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                        </td>\r\n                        </tr>";
        } else {
            echo '<tr class=rowcontent><td colspan=10>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
    case 'update':
        if ('' == $kdSarana || '' == $tglKonSarana || '' == $kondId || '' == $idProgress) {
            echo 'warning:Semua Field tidak boleh kosong';
            exit();
        }

        if ('' == $jmlhSarana || '0' == $jmlhSarana) {
            exit('Error:Jumlah tidak boleh kosong');
        }

        $sCek2 = 'select distinct jumlah,jenisprasarana from '.$dbname.".sdm_prasarana where kodeprasarana='".$kdSarana."'";
        $qCek2 = mysql_query($sCek2);
        $rCek2 = mysql_fetch_assoc($qCek2);
        if ($rCek2['jumlah'] < $jmlhSarana) {
            exit('Error:Jumlah tidak boleh lebih dari '.$arrSat[$rCek2['jenisprasarana']].' yang tersedia');
        }

        $sUpd = 'update '.$dbname.".sdm_kondisi_prasarana set `jumlah`='".$jmlhSarana."',`kondisi`='".$kondId."',`progress`='".$idProgress."',`karyawanid`='".$_SESSION['standard']['userid']."'\r\n                       where kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
        if (!mysql_query($sUpd)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".sdm_kondisi_prasarana where  kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
        if (!mysql_query($sDel)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'getData':
        $sDt = 'select * from '.$dbname.".sdm_kondisi_prasarana where kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
        $qDt = mysql_query($sDt);
        $rDet = mysql_fetch_assoc($qDt);
        echo $rDet['jumlah'].'###'.$rDet['kondisi'].'###'.$rDet['progress'];

        break;
    case 'getSatuan':
        $sSatuan2 = 'select distinct jenisprasarana from '.$dbname.".sdm_prasarana where kodeprasarana='".$kdSarana."'";
        $qSatuan2 = mysql_query($sSatuan2);
        $rSatuan2 = mysql_fetch_assoc($qSatuan2);
        $sSatuan = 'select distinct satuan from '.$dbname.".sdm_5jenis_prasarana where jenis='".$rSatuan2['jenisprasarana']."'";
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