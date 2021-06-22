<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showDetail':
        $where = "pabrik_pengolahan_id='".$param['nopengolahan']."'";
        $cols = 'shift, station, engine, start_time_stagnasi, stop_time_stagnasi, total_stagnasi, down_status, description';
        $query = selectQuery($dbname, 'pabrik_pengolahan_mesin', $cols, $where);
        $data = fetchData($query);
        $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "induk='".$param['kodeorg']."'");
        $optShift =
        [
            1 => "1",
            2 => "2"

        ];
        $optMesin = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='STENGINE' and induk='".end(array_reverse(array_keys($optOrg)))."'");
        $optMesinAll = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='STENGINE'", '0', true);
        $optDwnStat = ['EDT' => 'EDT : Emergency Downtime', 'SDT' => 'SDT : Sequential Downtime', 'CDT' => 'CDT : Commercial Downtime'];
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['station'] = $optOrg[$row['station']];
            $dataShow[$key]['tahuntanam'] = $optMesinAll[$row['tahuntanam']];
        }
        $theForm1 = new uForm('mesinForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['mesin'], 2);
        $theForm1->addEls('shift', "Shift", '', 'select', 'L', 25, $optShift);
        $theForm1->addEls('station', $_SESSION['lang']['station'], '', 'select', 'L', 25, $optOrg);
        $theForm1->_elements[1]->_attr['onchange'] = 'updMesin()';
        $theForm1->addEls('engine', $_SESSION['lang']['mesin'], '0', 'select', 'L', 25, $optMesin);
        $theForm1->addEls('start_time_stagnasi', $_SESSION['lang']['jammulaistagnasi'], '0', 'jammenit', 'R', 10);
        $theForm1->addEls('stop_time_stagnasi', $_SESSION['lang']['jamselesaistagnasi'], '0', 'jammenit', 'R', 10);
        $theForm1->addEls('total_stagnasi', $_SESSION['lang']['jamstagnasi'], '0', 'textnum', 'R', 10);
        $theForm1->addEls('down_status', $_SESSION['lang']['downstatus'], '0', 'select', 'L', 25, $optDwnStat);
        $theForm1->addEls('description', $_SESSION['lang']['keterangan'], '', 'text', 'L', 50);
        
        $theTable1 = new uTable('mesinTable', $_SESSION['lang']['tabel'].' '.$_SESSION['lang']['mesin'], $cols, $data, $dataShow);
        $formTab1 = new uFormTable('ftMesin', $theForm1, $theTable1, null, ['nopengolahan']);
        $formTab1->_target = 'pabrik_slave_pengolahan_mesin';
        $formTab1->_addActions = ['material' => ['img' => 'detail1.png', 'onclick' => 'showMaterial']];
        echo '<fieldset><legend><b>Detail</b></legend>';
        $formTab1->render();
        echo '</fieldset>';

        break;
    case 'updMesin':
        $opt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='STENGINE' and induk='".$param['station']."'");
        echo json_encode($opt);

        break;
    default:
        break;
}

?>