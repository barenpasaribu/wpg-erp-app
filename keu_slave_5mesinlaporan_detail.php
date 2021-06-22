<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showDetail':
        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,noakun,namaakun', 'detail=1', '9', true);
        $optTipe = getEnum($dbname, 'keu_5mesinlaporandt', 'tipe');
        $where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."'";
        $cols = "nourut,tipe,noakundari,noakunsampai,noakundisplay,keterangandisplay,\n\t    inputbit,rubahoperatr,variableoutput,operator,variablejadi,resetvariableoutput";
        $query = selectQuery($dbname, 'keu_5mesinlaporandt', $cols, $where);
        $data = fetchData($query);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['tipe'] = $optTipe[$row['tipe']];
        }
        $maxNo = 1;
        foreach ($data as $row) {
            if ($maxNo < $row['nourut']) {
                $maxNo = $row['nourut'];
            }
        }
        ++$maxNo;
        $theForm1 = new uForm('mesinForm', 'Form Detail', 2);
        $theForm1->addEls('nourut', $_SESSION['lang']['nourut'], $maxNo, 'textnum', 'R', 10);
        $theForm1->addEls('tipe', $_SESSION['lang']['tipe'], '', 'select', 'L', 25, $optTipe);
        $theForm1->addEls('noakundari', $_SESSION['lang']['noakundari'], ' ', 'select', 'L', 25, $optAkun);
        $theForm1->addEls('noakunsampai', $_SESSION['lang']['noakunsampai'], ' ', 'select', 'L', 25, $optAkun);
        $theForm1->addEls('noakundisplay', $_SESSION['lang']['noakundisplay'], ' ', 'text', 'L', 25);
        $theForm1->addEls('keterangandisplay', $_SESSION['lang']['keterangandisplay'], '', 'text', 'L', 45);
        $theForm1->addEls('inputbit', $_SESSION['lang']['inputbit'], '', 'text', 'C', 2);
        $theForm1->addEls('rubahoperatr', $_SESSION['lang']['ubahoperator'], '0', 'textnum', 'C', 2);
        $theForm1->addEls('variableoutput', $_SESSION['lang']['variableoutput'], '0', 'textnum', 'C', 10);
        $theForm1->addEls('operator', $_SESSION['lang']['operator'], '+', 'text', 'C', 2);
        $theForm1->addEls('variablejadi', $_SESSION['lang']['variablejadi'], '0', 'textnum', 'C', 10);
        $theForm1->addEls('resetvariableoutput', $_SESSION['lang']['resetvariableoutput'], '0', 'textnum', 'C', 2);
        $theTable1 = new uTable('mesinTable', 'Tabel Detail', $cols, $data, $dataShow);
        $formTab1 = new uFormTable('ftMesin', $theForm1, $theTable1, null, ['kodeorg', 'namalaporan']);
        $formTab1->_target = 'keu_slave_5mesinlaporan_detail';
        $formTab1->_nourutJs = true;
        echo '<fieldset><legend><b>Detail</b></legend>';
        $formTab1->render();
        echo '</fieldset>';

        break;
    case 'add':
        $tmpCol = 'nourut,tipe,noakundari,noakunsampai,noakundisplay,keterangandisplay,'.'inputbit,rubahoperatr,variableoutput,operator,variablejadi,resetvariableoutput,kodeorg,namalaporan';
        $cols = explode(',', $tmpCol);
        $data = $param;
        unset($data['numRow']);
        $query = insertQuery($dbname, 'keu_5mesinlaporandt', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['kodeorg'], $data['namalaporan']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['kodeorg'], $data['namalaporan']);

        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."' and nourut='".$param['cond_nourut']."'";
        $query = updateQuery($dbname, 'keu_5mesinlaporandt', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."' and nourut='".$param['nourut']."'";
        $query = 'delete from `'.$dbname.'`.`keu_5mesinlaporandt` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>