<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showMaterial':
        $where = "nopengolahan='".$param['nopengolahan']."' and kodeorg='".$param['kodeorg']."' and tahuntanam='".$param['tahuntanam']."'";
        $cols = 'kodebarang,jumlah';
        $query = selectQuery($dbname, 'pabrik_pengolahan_barang', $cols, $where);
        $data = fetchData($query);
        if (!empty($data)) {
            $whereBarang = 'kodebarang in (';
            foreach ($data as $key => $row) {
                if (0 === $key) {
                    $whereBarang .= "'".$row['kodebarang']."'";
                } else {
                    $whereBarang .= ",'".$row['kodebarang']."'";
                }
            }
            $whereBarang .= ')';
            $optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang', $whereBarang);
        } else {
            $optBarang = [];
        }

        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
        }
        $theForm1 = new uForm('materialForm', 'Form Material');
        $theForm1->addEls('kodebarang', $_SESSION['lang']['kodebarang'], '', 'searchBarang', 'L', 20, null, null, 'jumlah_satuan');
        $theForm1->addEls('jumlah', $_SESSION['lang']['jumlah'], '0', 'textnumwsatuan', 'L', 10);
        $theTable1 = new uTable('materialTable', 'Tabel Material', $cols, $data, $dataShow);
        $formTab1 = new uFormTable('ftMaterial', $theForm1, $theTable1, null, ['nopengolahan', 'ftMesin_station_'.$param['numRow'], 'ftMesin_tahuntanam_'.$param['numRow']]);
        $formTab1->_target = 'pabrik_slave_pengolahan_material';
        $formTab1->render();

        break;
    case 'add':
        $cols = ['nopengolahan', 'kodeorg', 'tahuntanam', 'kodebarang', 'jumlah'];
        $data = [];
        $data['nopengolahan'] = $param['nopengolahan'];
        foreach ($param as $key => $row) {
            if ('station' === substr($key, 8, 7)) {
                $data['kodeorg'] = $row;
            }
        }
        foreach ($param as $key => $row) {
            if ('tahuntanam' === substr($key, 8, 10)) {
                $data['tahuntanam'] = $row;
            }
        }
        $data['kodebarang'] = $param['kodebarang'];
        $data['jumlah'] = $param['jumlah'];
        $query = insertQuery($dbname, 'pabrik_pengolahan_barang', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['nopengolahan'], $data['kodeorg'], $data['tahuntanam']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        foreach ($param as $key => $row) {
            if ('station' === substr($key, 8, 7)) {
                $param['kodeorg'] = $row;
            }
        }
        foreach ($param as $key => $row) {
            if ('tahuntanam' === substr($key, 8, 10)) {
                $param['tahuntanam'] = $row;
            }
        }
        $data = [];
        $data['kodebarang'] = $param['kodebarang'];
        $data['jumlah'] = $param['jumlah'];
        unset($data['nopengolahan']);
        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "nopengolahan='".$param['nopengolahan']."' and kodeorg='".$param['kodeorg']."' and tahuntanam='".$param['tahuntanam']."' and kodebarang='".$param['cond_kodebarang']."'";
        $query = updateQuery($dbname, 'pabrik_pengolahan_barang', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($data);

        break;
    case 'delete':
        foreach ($param as $key => $row) {
            if ('station' === substr($key, 8, 7)) {
                $param['kodeorg'] = $row;
            }
        }
        foreach ($param as $key => $row) {
            if ('tahuntanam' === substr($key, 8, 10)) {
                $param['tahuntanam'] = $row;
            }
        }
        $where = "nopengolahan='".$param['nopengolahan']."' and kodeorg='".$param['kodeorg']."' and tahuntanam='".$param['tahuntanam']."' and kodebarang='".$param['kodebarang']."'";
        $query = 'delete from `'.$dbname.'`.`pabrik_pengolahan_barang` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>