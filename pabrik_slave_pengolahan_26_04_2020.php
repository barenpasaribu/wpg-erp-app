<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'posting':
        $data = $_POST;
        $where = "nopengolahan='".$data['nopengolahan']."'";
        $query = updateQuery($dbname, 'pabrik_pengolahan', ['posting' => '1'], $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'showHeadList':
        $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
        if (isset($param['where'])) {
            $tmpW = str_replace('\\', '', $param['where']);
            $arrWhere = json_decode($tmpW, true);
            if (!empty($arrWhere)) {
                foreach ($arrWhere as $key => $r1) {
                    if (0 == $key) {
                        $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
                    }
                }
            } else {
                $where .= null;
            }
        } else {
            $where .= null;
        }

        $header = [$_SESSION['lang']['nopengolahan'], $_SESSION['lang']['pabrik'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['shift']];
        $cols = 'nopengolahan,kodeorg,tanggal,shift,posting';
        $query = selectQuery($dbname, 'pabrik_pengolahan', $cols, $where. ' order by nopengolahan DESC, tanggal ASC', '', false, $param['shows'], $param['page']);		
        $data = fetchData($query);
        $totalRow = getTotalRow($dbname, 'pabrik_pengolahan', $where);
        foreach ($data as $key => $row) {
            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            if (1 == $row['posting']) {
                $data[$key]['switched'] = true;
            }

            unset($data[$key]['posting']);
        }
        $x = 'select kodejabatan from '.$dbname.".sdm_5jabatan where alias like '%ka.%' or alias like '%kepala%' or alias like '%Mill'";
        $y = mysql_query($x);
        while ($z = mysql_fetch_assoc($y)) {
            $pos = $z['kodejabatan'];
            if ($pos == $_SESSION['empl']['kodejabatan']) {
                $flag = 1;
            }
        }
        $tHeader = new rTable('headTable', 'headTableBody', $header, $data);
        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
        $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png', 'onclick="postingData()"');
        $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
        if (1 != $flag) {
            $tHeader->_actions[2]->_name = '';
        }

        $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
        $tHeader->_actions[3]->addAttr('event');
        $tHeader->_switchException = ['detailPDF'];
        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
        if (isset($param['where'])) {
            $tHeader->setWhere($arrWhere);
        }

        $tHeader->renderTable();

        break;
    case 'showAdd':
        echo formHeader('add', []);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'showEdit':
        $query = selectQuery($dbname, 'pabrik_pengolahan', '*', "nopengolahan='".$param['nopengolahan']."'");
        $tmpData = fetchData($query);
        $data = $tmpData[0];
        $data['tanggal'] = tanggalnormal($data['tanggal']);
        echo formHeader('edit', $data);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'add':
        $data = $_POST;
        $warning = '';
        if ('' == $data['tanggal']) {
            $warning .= "Tanggal harus diisi\n";
        }

        if ('' != $warning) {
            echo "Warning :\n".$warning;
            exit();
        }

        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        unset($data['nopengolahan']);
        $cols = ['kodeorg', 'tanggal', 'shift', 'jammulai', 'jamselesai', 'mandor', 'asisten', 'jamdinasbruto', 'jamstagnasi', 'jumlahlori', 'tbsdiolah'];
        $query = insertQuery($dbname, 'pabrik_pengolahan', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        } else {
            $w = "kodeorg='".$data['kodeorg']."' and tanggal='".$data['tanggal']."' and shift=".$data['shift']." and jammulai='".$data['jammulai']."' and jamselesai='".$data['jamselesai']."' and mandor='".$data['mandor']."' and asisten='".$data['asisten']."' and jamdinasbruto=".$data['jamdinasbruto'].' and jamstagnasi='.$data['jamstagnasi'].' and jumlahlori='.$data['jumlahlori'].' and tbsdiolah='.$data['tbsdiolah'];
            $q = selectQuery($dbname, 'pabrik_pengolahan', 'nopengolahan', $w);
            $res = fetchData($q);
            echo $res[0]['nopengolahan'];
        }

        break;
    case 'edit':
        $data = $_POST;
        $where = "nopengolahan='".$data['nopengolahan']."'";
        unset($data['nopengolahan']);
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $query = updateQuery($dbname, 'pabrik_pengolahan', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'delete':
        $where = 'nopengolahan='.$param['nopengolahan'];
        $query = 'delete from `'.$dbname.'`.`pabrik_pengolahan` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    case 'updMandorAst':
        $mode = $param['mode'];
        $shift = $param['shift'];
        if ('tanggal' == $mode) {
            $optShift = makeOption($dbname, 'pabrik_5shift', 'shift,shift', "kodeorg='".$_SESSION['empl']['lokasitugas']."'");
            if (empty($optShift)) {
                echo 'Warning : Tidak ada shift yang berlaku pada tanggal tersebut';
                exit();
            }

            $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and shift in (";
            $i = 0;
            foreach ($optShift as $row) {
                if (0 == $i) {
                    $where .= $row;
                } else {
                    $where .= ','.$row;
                }

                ++$i;
            }
            $where .= ')';
            $cols = 'shift,mandor,asisten';
        } else {
            $cols = 'mandor,asisten';
            $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and shift=".$param['shift'];
        }

        $query = selectQuery($dbname, 'pabrik_5shift', $cols, $where);
        $res = fetchData($query);
        $whereKary = 'karyawanid in ('.$res[0]['mandor'].','.$res[0]['asisten'].')';
        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKary);
        $resShift = [];
        $resMandor = [$res[0]['mandor'] => $optKary[$res[0]['mandor']]];
        $resAst = [$res[0]['asisten'] => $optKary[$res[0]['asisten']]];
        if ('tanggal' == $mode) {
            foreach ($res as $row) {
                $resShift[$row['shift']] = $row['shift'];
            }
        } else {
            $resShift = 'empty';
        }

        $result = ['shift' => $resShift, 'mandor' => $resMandor, 'asisten' => $resAst];
        echo json_encode($result);

        break;
    default:
        break;
}
function formHeader($mode, $data)
{
    global $dbname;
    if (empty($data)) {
        $new = true;
        $data['kodeorg'] = '';
        $data['nopengolahan'] = '0';
        $data['tanggal'] = '';
        $data['shift'] = '1';
        $data['jammulai'] = '00:00:00';
        $data['jamselesai'] = '00:00:00';
        $data['mandor'] = '';
        $data['asisten'] = '';
        $data['jamdinasbruto'] = '0';
        $data['jamstagnasi'] = '0';
        $data['jumlahlori'] = '0';
        $data['tbsdiolah'] = '0';
    } else {
        $new = false;
    }

    if ('edit' == $mode) {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $qShift = selectQuery($dbname, 'pabrik_5shift', 'shift,mandor,asisten', "kodeorg='".$_SESSION['empl']['lokasitugas']."'");
    $tmpShift = fetchData($qShift);
    $optShift = [];
    $whereKary = '';
    $whereKaryNew = '';
    foreach ($tmpShift as $key => $row) {
        $optShift[$row['shift']] = $row['shift'];
        if (0 == $key) {
            $whereKaryNew .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
            $whereKary .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
        } else {
            $whereKaryNew .= " or karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
        }
    }
    $optKaryMandor=[];
    $sql="select karyawanid, namakaryawan from pabrik_5shift a inner join datakaryawan b on a.mandor=b.karyawanid where
            lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
    $data=mysql_query($sql);
    while ($row=mysql_fetch_array($data)) {
        $optKaryMandor[$row['karyawanid']] = $row['namakaryawan'];
    }

    $optKaryAsst=[];
    $sql="select karyawanid, namakaryawan from pabrik_5shift a inner join datakaryawan b on a.asisten=b.karyawanid where
            lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
    $data=mysql_query($sql);
    while ($row=mysql_fetch_array($data)) {
        $optKaryAsst[$row['karyawanid']] = $row['namakaryawan'];
    }

                                 
//   $whereKaryMandor = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan \r\n\t\t\t\t\t\twhere  alias like '%KEPALA%' or alias like '%Mandor%' or alias like '%foreman%' or alias like '%foreman%'\r\n\t\t\t\t\t\tor alias like '%pjs. ka%')";

//    $optKaryMandor = makeOption($dbname, 'pabrik_5shift', 'mandor,asisten', $whereKaryMandor);

//    $whereKaryAsst = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Asisten%')";
//    $optKaryAsst = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKaryAsst);
    $els = [];
    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['pabrik']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:300px'], $optOrg)];
    $els[] = [makeElement('nopengolahan', 'label', $_SESSION['lang']['nopengolahan']), makeElement('nopengolahan', 'text', $data['nopengolahan'], ['style' => 'width:200px', 'maxlength' => '15', 'disabled' => 'disabled'])];
    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
    $els[] = [makeElement('shift', 'label', $_SESSION['lang']['shift']), makeElement('shift', 'select', $data['shift'], ['style' => 'width:300px'], $optShift)];
    $els[] = [makeElement('jammulai', 'label', $_SESSION['lang']['jammulai']), makeElement('jammulai', 'jammenit', $data['jammulai'])];
    $els[] = [makeElement('jamselesai', 'label', $_SESSION['lang']['jamselesai']), makeElement('jamselesai', 'jammenit', $data['jamselesai'])];
    $els[] = [makeElement('mandor', 'label', $_SESSION['lang']['mandor']), makeElement('mandor', 'select', $data['mandor'], ['style' => 'width:300px'], $optKaryMandor)];
    $els[] = [makeElement('asisten', 'label', $_SESSION['lang']['asisten']), makeElement('asisten', 'select', $data['asisten'], ['style' => 'width:300px'], $optKaryAsst)];
    $els[] = [makeElement('jamdinasbruto', 'label', $_SESSION['lang']['jampengolahan']), makeElement('jamdinasbruto', 'textnum', $data['jamdinasbruto'], ['style' => 'width:300px'])];
    $els[] = [makeElement('jamstagnasi', 'label', $_SESSION['lang']['jamstagnasi']), makeElement('jamstagnasi', 'textnum', $data['jamstagnasi'], ['style' => 'width:300px'])];
    $els[] = [makeElement('jumlahlori', 'label', $_SESSION['lang']['jumlahlori']), makeElement('jumlahlori', 'textnum', $data['jumlahlori'], ['style' => 'width:300px'])];
    $els[] = [makeElement('tbsdiolah', 'label', $_SESSION['lang']['tbsdiolah']), makeElement('tbsdiolah', 'textnum', $data['tbsdiolah'], ['style' => 'width:300px']).' kg'];
    if ('add' == $mode) {
        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'addDataTable()'])];
    } else {
        if ('edit' == $mode) {
            $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'editDataTable()'])];
        }
    }

    if ('add' == $mode) {
        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
    }

    if ('edit' == $mode) {
        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
    }
}

?>