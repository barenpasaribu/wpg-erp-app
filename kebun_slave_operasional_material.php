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

        $cols = ['kodeorg', 'kwantitasha', 'kodegudang', 'kodebarang', 'kwantitas', 'notransaksi', 'hargasatuan'];
        $data = $param;
        unset($data['numRow']);
        $data['hargasatuan'] = 0;
        if ('' === $data['kodebarang'] || '0' === $data['kodebarang']) {
            echo 'Warning : Barang harus diisi';
            exit();
        }

        cekHa($dbname, $param, $resKeg[0]['kodekegiatan']);
        $query = insertQuery($dbname, 'kebun_pakaimaterial', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['notransaksi'], $data['hargasatuan']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['notransaksi']);
        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $qKeg = selectQuery($dbname, 'kebun_prestasi', '*', "notransaksi='".$param['notransaksi']."'");
        $resKeg = fetchData($qKeg);
        cekHa($dbname, $param, $resKeg[0]['kodekegiatan']);
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['cond_kodeorg']."' and kodebarang='".$param['cond_kodebarang']."'";
        $query = updateQuery($dbname, 'kebun_pakaimaterial', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and kodebarang='".$param['kodebarang']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_pakaimaterial` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}
function cekHa($dbname, $param, $kegiatan)
{
    $qKeg = selectQuery($dbname, 'setup_kegiatan', 'kodekegiatan,satuan', "kodekegiatan='".$kegiatan."'");
    $resKeg = fetchData($qKeg);
    if ('HA' === trim($resKeg[0]['satuan'])) {
        $tipe = 'BLOK';
        $str = 'select tipe from '.$dbname.".organisasi where kodeorganisasi='".$param['kodeorg']."'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $tipe = $bar->tipe;
        }
        if ('BLOK' !== $tipe) {
        } else {
            $theHa = makeOption($dbname, 'setup_blok', 'kodeorg,luasareaproduktif', "kodeorg='".$param['kodeorg']."'");
            if (6 === strlen(trim($param['kodeorg']))) {
            } else {
                if ($theHa[$param['kodeorg']] < $param['kwantitasha']) {
                    echo 'Validation Error : Ha harus lebih kecil dari Luas produktif Blok:'.$param['kodeorg'];
                    exit();
                }
            }
        }
    }
}

?>