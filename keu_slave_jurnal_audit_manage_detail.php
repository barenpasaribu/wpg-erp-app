<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/tanaman.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'add':
        $selQuery = selectQuery($dbname, 'keu_jurnaldt', 'nourut', "nojurnal='".$param['nojurnal']."'");
        $nourut = fetchData($selQuery);
        $maxNoUrut = 1;
        if (!empty($nourut)) {
            foreach ($nourut as $row) {
                ($maxNoUrut <= $row['nourut'] ? ($maxNoUrut = $row['nourut']) : false);
            }
            ++$maxNoUrut;
        }

        $cols = ['nourut', 'noakun', 'keterangan', 'jumlah', 'matauang', 'kurs', 'noaruskas', 'kodekegiatan', 'kodeasset', 'kodebarang', 'nik', 'kodecustomer', 'kodesupplier', 'kodevhc', 'nodok', 'kodeblok', 'revisi', 'nojurnal', 'tanggal', 'kodeorg'];
        $data = $param;
        $data['nourut'] = $maxNoUrut;
        $data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $data['jumlah'] = str_replace(',', '', $data['jumlah']);
        unset($data['numRow'], $data['kodejurnal']);

        $blk = str_replace(' ', '', $param['kodeblok']);
        if (cekAkun($param['noakun']) && '' === $blk) {
            exit('[ Error ]: Organization code is obligatory to this account.');
        }

        $query = insertQuery($dbname, 'keu_jurnaldt', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['nojurnal'], $data['kodejurnal'], $data['tanggal'], $data['kodeorg']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['nojurnal'], $data['kodejurnal'], $data['nourut']);

        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $data['jumlah'] = str_replace(',', '', $data['jumlah']);
        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "nojurnal='".$param['nojurnal']."' and nourut='".$param['nourut']."'";
        $query = updateQuery($dbname, 'keu_jurnaldt', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "nojurnal='".$param['nojurnal']."' and nourut='".$param['nourut']."'";
        $query = 'delete from `'.$dbname.'`.`keu_jurnaldt` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>