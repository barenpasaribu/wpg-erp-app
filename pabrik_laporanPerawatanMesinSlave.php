<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$pbrkId = $_POST['pbrkId'];
$statId = $_POST['statId'];
$msnId = $_POST['msnId'];
$periode = $_POST['periode'];
$kdBrg = $_POST['kdBrg'];
$optNmMsn = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
switch ($proses) {
    case 'getData':
        if ('0' === $periode) {
            $sql = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a left join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n                where a.pabrik='".$pbrkId."' and a.statasiun='".$statId."'  order by a.tanggal asc";
        } else {
            if ('0' !== $periode) {
                $sql = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a left join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n                where a.pabrik='".$pbrkId."' and a.statasiun='".$statId."' and tanggal like '%".$periode."%' order by a.tanggal asc";
            }
        }

        $query = mysql_query($sql);
        echo '<div style="width:100%; height:300px; overflow:scroll;">';
        echo "<table cellspacing=1 border=0 width=1500px>\r\n                <thead><tr class=rowheader>\r\n                        <td>No</td>\r\n                        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n                        <td>".$_SESSION['lang']['tanggal']."</td>\r\n                        <td>".$_SESSION['lang']['kegiatan']."</td>\r\n                        <td>".$_SESSION['lang']['jammulai']."</td>\r\n                        <td>".$_SESSION['lang']['jamselesai']."</td>\r\n                        <td>".$_SESSION['lang']['mesin']."</td>\r\n                        <td>".$_SESSION['lang']['nmmesin']."</td>\r\n                        <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                        <td>".$_SESSION['lang']['namabarang']."</td>\r\n                        <td>".$_SESSION['lang']['satuan']."</td>\r\n                        <td>".$_SESSION['lang']['jumlah']."</td>\r\n                        <td>".$_SESSION['lang']['keterangan'].'</td></tr></thead><tbody>';
        while ($res = mysql_fetch_assoc($query)) {
            $sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
            $qBrg = mysql_query($sBrg);
            $rBrg = mysql_fetch_assoc($qBrg);
            ++$no;
            echo "<tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$res['notransaksi']."</td>\r\n                        <td>".tanggalnormal($res['tanggal'])."</td>\r\n                        <td>".$res['kegiatan']."</td>\r\n                        <td>".tanggalnormald($res['jammulai'])."</td>\r\n                        <td>".tanggalnormald($res['jamselesai'])."</td>\r\n                        <td>".$res['mesin']."</td>\r\n                        <td>".$optNmMsn[$res['mesin']]."</td>\r\n                        <td>".$res['kodebarang']."</td>\r\n                        <td>".$rBrg['namabarang']."</td>\r\n                        <td>".$res['satuan']."</td>\r\n                        <td>".$res['jumlah']."</td>\r\n                        <td>".$res['keterangan']."</td>\r\n                        </tr>";
        }
        echo '</tbody></table></div>';

        break;
    case 'get_result_cari':
        $sql = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a inner join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n                where a.pabrik='".$pbrkId."' and tanggal like '%".$periode."%' and b.kodebarang='".$kdBrg."' order by a.tanggal asc";
        $query = mysql_query($sql);
        echo '<div style="width:850px; height:300px; overflow:auto;">';
        echo "<table cellspacing=1 border=0>\r\n                <thead><tr class=rowheader>\r\n                        <td>No</td>\r\n                        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n                        <td>".$_SESSION['lang']['tanggal']."</td>\r\n                        <td>".$_SESSION['lang']['mesin']."</td>\r\n                        </thead><tbody>";
        while ($res = mysql_fetch_assoc($query)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$res['notransaksi']."</td>\r\n                        <td>".tanggalnormal($res['tanggal'])."</td>\r\n                        <td>".$res['mesin']."</td>\r\n                        </tr>";
        }
        echo '</tbody></table></div>';

        break;
    case 'GetDataMsn':
        $sql = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a inner join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n                where a.pabrik='".$pbrkId."' and tanggal like '%".$periode."%' and a.mesin='".$msnId."' order by a.tanggal asc";
        $query = mysql_query($sql);
        echo '<div style="width:850px; height:300px; overflow:auto;">';
        echo "<table cellspacing=1 border=0>\r\n                <thead><tr class=rowheader>\r\n                        <td>No</td>\r\n                        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n                        <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                        <td>".$_SESSION['lang']['namabarang']."</td>\r\n                        <td>".$_SESSION['lang']['satuan']."</td>\r\n                        <td>".$_SESSION['lang']['jumlah']."</td>\r\n                        <td>".$_SESSION['lang']['keterangan'].'</td></tr></thead><tbody>';
        while ($res = mysql_fetch_assoc($query)) {
            $sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
            $qBrg = mysql_query($sBrg);
            $rBrg = mysql_fetch_assoc($qBrg);
            ++$no;
            echo "<tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$res['notransaksi']."</td>\r\n                        <td>".$res['kodebarang']."</td>\r\n                        <td>".$rBrg['namabarang']."</td>\r\n                        <td>".$res['satuan']."</td>\r\n                        <td>".$res['jumlah']."</td>\r\n                        <td>".$res['keterangan']."</td>\r\n                        </tr>";
        }
        echo '</tbody></table></div>';

        break;
    default:
        break;
}

?>