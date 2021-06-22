<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showDetail':
        $where = "noinvoice='".$param['noinvoice']."'";
        $cols = 'nodo,nilaitransaksi';
        $query = selectQuery($dbname, 'keu_penagihandt', $cols, $where);
        $data = fetchData($query);
        $dataShow = $data;
        $theForm2 = new uForm('transForm', 'Form Penagihan');
        $theForm2->addEls('nodo', $_SESSION['lang']['nodo'], '', 'text', 'L', 10);
        $theForm2->addEls('nilaitransaksi', $_SESSION['lang']['nilaitransaksi'], '0', 'textnum', 'L', 11);
        $theTable2 = new uTable('transTable', 'Tabel Penagihan', $cols, $data, $dataShow);
        $formTab2 = new uFormTable('transFT', $theForm2, $theTable2, null, ['noinvoice']);
        $formTab2->_target = 'keu_slave_penagihan_detail';
        echo '<fieldset><legend><b>Detail</b></legend>';
        $formTab2->render();
        echo '</fieldset>';

        break;
    case 'add':
        $cols = ['nodo', 'nilaitransaksi', 'noinvoice'];
        $data = $param;
        unset($data['numRow']);
        $query = insertQuery($dbname, 'keu_penagihandt', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['noinvoice']);
        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['noinvoice']);
        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "noinvoice='".$param['noinvoice']."' and nodo='".$param['cond_nodo']."'";
        $query = updateQuery($dbname, 'keu_penagihandt', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "noinvoice='".$param['noinvoice']."' and nodo='".$param['nodo']."'";
        $query = 'delete from `'.$dbname.'`.`keu_penagihandt` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>