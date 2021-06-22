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
        $tipeKodeorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
        if ('KANWIL' == $tipeKodeorg[$param['kodeorg']]) {
            $whereKar = 'lokasitugas in (select kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
        } else {
            $whereKar = "lokasitugas='".$param['kodeorg']."'";
        }

        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,nik,lokasitugas,namakaryawan', $whereKar, '6');
        if (empty($optKary)) {
            $optKary = ['' => ''];
        }

        $where = "kodeorg='".$param['kodeorg']."' and periodegaji='".$param['periodegaji']."'";
        $cols = 'tanggal,nik,jumlahpotongan,keterangan';
        $query = selectQuery($dbname, 'sdm_potongandt', $cols, $where);
        $data = fetchData($query);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            $dataShow[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            $dataShow[$key]['nik'] = $optKary[$row['nik']];
            $total += $dataShow[$key]['jumlahpotongan'];
        }
        $data[] = ['tanggal' => '', 'nik' => 'Total', 'jumlahpotongan' => number_format($total), 'keterangan' => ''];
        $dataShow[] = ['tanggal' => '', 'nik' => 'Total', 'jumlahpotongan' => number_format($total), 'keterangan' => ''];
        $theForm1 = new uForm('detailForm', 'Form Detail', 2);
        $theForm1->addEls('tanggal', $_SESSION['lang']['tanggal'], '', 'text', 'L', 15);
        $theForm1->_elements[0]->_attr['readonly'] = 'readonly';
        $theForm1->_elements[0]->_attr['onmousemove'] = 'setCalendar(this.id)';
        $theForm1->addEls('nik', $_SESSION['lang']['nik'], '', 'select', 'L', 25, $optKary);
        $theForm1->addEls('jumlahpotongan', $_SESSION['lang']['potongan'], '0', 'textnum', 'R', 10);
        $theForm1->addEls('keterangan', $_SESSION['lang']['keterangan'], '', 'text', 'L', 50);
        $theTable1 = new uTable('detailTable', 'Tabel Detail', $cols, $data, $dataShow);
        $formTab1 = new uFormTable('ftDetail', $theForm1, $theTable1, null, ['kodeorg', 'periodegaji']);
        $formTab1->_target = 'sdm_slave_potongan_detail';
        $formTab1->setFreezeEls('##tanggal##nik');
        echo '<fieldset><legend><b>Detail</b></legend>';
        $formTab1->render();
        echo '</fieldset>';

        break;
    case 'add':
        $cols = ['tanggal', 'nik', 'jumlahpotongan', 'keterangan', 'kodeorg', 'periodegaji'];
        $data = $param;
        unset($data['numRow']);
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $periode = $data['periodegaji'];
        $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$periode."' and\r\n        kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            $aktif = true;
        } else {
            $aktif = false;
        }

        if (true == $aktif) {
            exit('Error: Accounting period has been closed to this date');
        }

        $query = insertQuery($dbname, 'sdm_potongandt', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['kodeorg'], $data['periodegaji']);

        $data['tanggal'] = tanggalnormal($data['tanggal']);
        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['kodeorg'], $data['tanggal'], $data['nik'], $data['periodegaji']);

        foreach ($data as $key => $cont) {
            if ('cond_' == substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "kodeorg='".$param['kodeorg']."' and periodegaji='".$param['periodegaji']."' and tanggal='".tanggalsystem($param['cond_tanggal'])."' and nik='".$param['cond_nik']."'";
        $query = updateQuery($dbname, 'sdm_potongandt', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "kodeorg='".$param['kodeorg']."' and periodegaji='".$param['periodegaji']."' and tanggal='".tanggalsystem($param['tanggal'])."' and nik='".$param['nik']."'";
        $query = 'delete from `'.$dbname.'`.`sdm_potongandt` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>