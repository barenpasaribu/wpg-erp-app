<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showHeadList':
        $where = "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc";
        // if (isset($param['where'])) {
        //     $tmpW = str_replace('\\', '', $param['where']);
        //     $arrWhere = json_decode($tmpW, true);
        //     if (!empty($arrWhere)) {
        //         foreach ($arrWhere as $key => $r1) {
        //             if (0 === $key) {
        //                 $where .= 'and '.$r1[0]." like '%".$r1[1]."%'";
        //             } else {
        //                 $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
        //             }
        //         }
        //     } else {
        //         $where .= null;
        //     }
        // } else {
        //     $where .= null;
        // }

        $header = [$_SESSION['lang']['afdeling'], $_SESSION['lang']['blok'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['bulan'], $_SESSION['lang']['tahun'], $_SESSION['lang']['jumlah'], $_SESSION['lang']['jumlahbungabetina'], $_SESSION['lang']['jumlahpokok']];
        $optNamaOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
        $cols = 'kodeorg,kodeblok,tanggal,bulan,tahun,jumlah,jumlahbungabetina,jumlahpokok';
        $query = selectQuery($dbname, 'kebun_rencanapanen', $cols, $where, '', false, $param['shows'], $param['page']);
        $data = fetchData($query);
        $totalRow = getTotalRow($dbname, 'kebun_rencanapanen', $where);
        foreach ($data as $key => $row) {
            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            $data[$key]['kodeorg'] = $optNamaOrg[$row['kodeorg']];
            $data[$key]['kodeblok'] = $row['kodeblok'];
        }
        $tHeader = new rTable('headTable', 'headTableBody', $header, $data);
        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
        if (isset($arrWhere)) {
            $tHeader->setWhere($arrWhere);
        }

        $tHeader->renderTable();

        break;
    case 'showAdd':
        echo formHeader('add', []);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'showEdit':
        $where = "kodeorg='".substr($param['kodeblok'], 0, 6)."' and bulan=".$param['bulan'].' and tahun='.$param['tahun'];
        $query = selectQuery($dbname, 'kebun_rencanapanen', '*', $where);
        $tmpData = fetchData($query);
        $data = $tmpData[0];
        $data['tanggal'] = tanggalnormal($data['tanggal']);
        echo formHeader('edit', $data);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'add':
        $data = $_POST;
        $data['tipetransaksi'] = $_GET['tipe'];
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $cols = ['notransaksi', 'kodeorg', 'tanggal', 'nikmandor', 'nikmandor1', 'nikasisten', 'keranimuat', 'tipetransaksi'];
        $query = insertQuery($dbname, 'kebun_rencanapanen', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'edit':
        $data = $_POST;
        $where = "notransaksi='".$data['notransaksi']."'";
        unset($data['notransaksi']);
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $query = updateQuery($dbname, 'kebun_rencanapanen', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'delete':
        $where = "kodeorg='".$param['kodeorg']."' and tahun=".$param['tahun'].' and bulan='.$param['bulan']." and kodeblok='".$param['kodeblok']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_rencanapanen` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}
function formHeader($mode, $data)
{
    global $dbname;
    if (empty($data)) {
        $data['kodeorg'] = '';
        $data['bulan'] = date('m');
        $data['tahun'] = date('Y');
    }

    if ('edit' === $mode) {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    $optAfd = getOrgBelow($dbname, $_SESSION['empl']['lokasitugas'], false, 'afdeling');
    $optBulan = optionMonth(substr($_SESSION['language'], 0, 1), 'long');
    $els = [];
    $els[] = [makeElement('period', 'label', $_SESSION['lang']['periode']), makeElement('bulan', 'select', $data['bulan'], [], $optBulan).'&nbsp;/&nbsp;'.makeElement('tahun', 'text', $data['tahun'], ['style' => 'width:50px'])];
    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:200px', $disabled => $disabled], $optAfd)];
    $els['btn'] = [makeElement('showDetBtn', 'btn', $_SESSION['lang']['detail'], ['onclick' => 'showDetail()'])];

    return genElementMultiDim($_SESSION['lang']['control'], $els);
}

?>
