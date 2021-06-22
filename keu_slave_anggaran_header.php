<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
switch ($proses) {
    case 'addHeader':
        if (!isset($_SESSION['org']['below'])) {
            $_SESSION['org']['below'] = getOrgBelow($dbname, $_SESSION['empl']['lokasitugas']);
        }

        $optOrg = $_SESSION['org']['below'];
        $optCurr = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $optType = getEnum($dbname, 'keu_anggaran', 'tipeanggaran');
        $els = [];
        $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
        $els[] = [makeElement('kodeanggaran', 'label', $_SESSION['lang']['kodeanggaran']), makeElement('kodeanggaran', 'text', '', ['style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
        $els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:250px', 'maxlength' => '50', 'onkeypress' => 'return tanpa_kutip(event)'])];
        $els[] = [makeElement('tipeanggaran', 'label', $_SESSION['lang']['tipeanggaran']), makeElement('tipeanggaran', 'select', '', ['style' => 'width:70px'], $optType)];
        $els[] = [makeElement('tahun', 'label', $_SESSION['lang']['tahun']), makeElement('tahun', 'text', date('Y'), ['style' => 'width:70px', 'maxlength' => '4', 'onkeypress' => 'return angka_doang(event)'])];
        $els[] = [makeElement('matauang', 'label', $_SESSION['lang']['matauang']), makeElement('matauang', 'select', '', ['style' => 'width:70px'], $optCurr)];
        $els[] = [makeElement('revisi', 'label', $_SESSION['lang']['revisi']), makeElement('revisi', 'textnum', '0', ['style' => 'width:70px', 'maxlength' => '2', 'onkeypress' => 'return angka_doang(event)'])];
        $els[] = [makeElement('tutup', 'label', $_SESSION['lang']['tutup']), makeElement('tutup', 'check')];
        $fieldStr = '##kodeorg##kodeanggaran##keterangan##tipeanggaran##tahun##matauang'.'##tutup##revisi';
        $els['button'] = [makeElement('addDataHead', 'button', $_SESSION['lang']['save'], ['onclick' => "addDataHeader('".$fieldStr."')"])];
        echo genElementMultiDim('Tambah Header Anggaran', $els, 1);

        break;
    case 'showList':
        $lokTugas = getOrgBelow($dbname, $_SESSION['empl']['lokasitugas']);
        $whereLok = '';
        $i = 0;
        foreach ($lokTugas as $key => $row) {
            if (0 === $i) {
                $whereLok .= "kodeorg='".$key."'";
            } else {
                $whereLok .= " or kodeorg='".$key."'";
            }

            ++$i;
        }
        $query = selectQuery($dbname, 'keu_anggaran', 'kodeanggaran,kodeorg,tahun,keterangan,tipeanggaran,tutup,revisi', $whereLok, 'kodeanggaran');
        $tmpData = fetchData($query);
        $data = [];
        foreach ($tmpData as $key => $row) {
            $data[$key] = $row;
            $fieldStr = '';
            $fieldVal = '';
            foreach ($row as $h => $r) {
                $fieldStr .= '##'.$h;
                $fieldVal .= '##'.$r;
            }
            $data[$key]['manage'] = "<img id='showDetail_".$key."' title='Lihat Detail' onclick=\"showingDetail('".$fieldStr."','".$fieldVal."','".$row['tutup']."')\"\r\n\t\t    class='zImgBtn' src='images/edit.png' />&nbsp;"."<img id='editHeader_".$key."' title='Edit Header' onclick=\"editHeader(event,'".$fieldStr."','".$fieldVal."')\"\r\n\t\t    class='zImgBtn' src='images/001_45.png' />&nbsp;"."<img id='deleteHeader_".$key."' title='Hapus Header' onclick=\"deleteHeader(".$key.",'".$fieldStr."','".$fieldVal."')\"\r\n\t\t    class='zImgBtn' src='images/delete_32.png' />";
        }
        $header = ['kodeanggaran' => $_SESSION['lang']['kodeanggaran'], 'kodeorg' => $_SESSION['lang']['kodeorg'], 'tahun' => $_SESSION['lang']['tahun'], 'keterangan' => $_SESSION['lang']['keterangan'], 'tipeanggaran' => $_SESSION['lang']['tipeanggaran'], 'tutup' => $_SESSION['lang']['tutup'], 'revisi' => $_SESSION['lang']['revisi'], 'manip' => 'Z'];
        $tables = makeCompleteTable('listHeader', 'bodyList', $header, $data, [], true, 'edit_tr');
        echo $tables;

        break;
    case 'editHeader':
        $query = selectQuery($dbname, 'keu_anggaran', '*', "kodeanggaran='".$_POST['kodeanggaran']."'");
        $tmpData = fetchData($query);
        $data = $tmpData[0];
        $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$data['kodeorg']."'");
        $optCurr = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $optType = getEnum($dbname, 'keu_anggaran', 'tipeanggaran');
        $els = [];
        $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:200px', 'disabled' => 'disabled'], $optOrg)];
        $els[] = [makeElement('kodeanggaran', 'label', $_SESSION['lang']['kodeanggaran']), makeElement('kodeanggaran', 'text', $data['kodeanggaran'], ['style' => 'width:70px', 'maxlength' => '10', 'disabled' => 'disabled'])];
        $els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', $data['keterangan'], ['style' => 'width:250px', 'maxlength' => '50', 'onkeypress' => 'return tanpa_kutip(event)'])];
        $els[] = [makeElement('tipeanggaran', 'label', $_SESSION['lang']['tipeanggaran']), makeElement('tipeanggaran', 'select', $data['tipeanggaran'], ['style' => 'width:70px'], $optType)];
        $els[] = [makeElement('tahun', 'label', $_SESSION['lang']['tahun']), makeElement('tahun', 'text', $data['tahun'], ['style' => 'width:70px', 'maxlength' => '4', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled'])];
        $els[] = [makeElement('matauang', 'label', $_SESSION['lang']['matauang']), makeElement('matauang', 'select', $data['matauang'], ['style' => 'width:70px'], $optCurr)];
        $els[] = [makeElement('revisi', 'label', $_SESSION['lang']['revisi']), makeElement('revisi', 'textnum', $data['revisi'], ['style' => 'width:70px', 'disabled' => 'disabled'])];
        if (1 === $data['tutup']) {
            $els[] = [makeElement('tutup', 'label', $_SESSION['lang']['tutup']), makeElement('tutup', 'check', '', ['checked' => 'checked'])];
        } else {
            $els[] = [makeElement('tutup', 'label', $_SESSION['lang']['tutup']), makeElement('tutup', 'check')];
        }

        $fieldStr = '##kodeorg##kodeanggaran##keterangan##tipeanggaran##tahun##matauang'.'##tutup##revisi';
        $els['button'] = [makeElement('editDataHead', 'button', $_SESSION['lang']['save'], ['onclick' => "editDataHeader('".$fieldStr."')"]).makeElement('cancelEdit', 'button', $_SESSION['lang']['cancel'], ['onclick' => 'showHeadList(event)'])];
        echo genElementMultiDim('Edit Header Anggaran', $els, 1);

        break;
    case 'showHead':
        $data = $_POST;
        $nameOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$data['kodeorg']."'");
        $where = "kodeanggaran='".$data['kodeanggaran']."'";
        $query = selectQuery($dbname, 'keu_anggaran', '*', $where);
        $dataX = fetchData($query);
        $dataX[0]['nameorg'] = $nameOrg[$data['kodeorg']];
        echo showMainHead($dbname, $dataX[0]);

        break;
    case 'deleteHeader':
        break;
    case 'add':
        $data = $_POST;
        unset($data['nameOrg']);
        $data['jumlahkoreksi'] = 0;
        $data['jumlah'] = 0;
        $column = [];
        foreach ($data as $key => $row) {
            $column[] = $key;
        }
        $query = insertQuery($dbname, 'keu_anggaran', $data, $column);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        $data['nameorg'] = $_POST['nameOrg'];
        echo showMainHead($dbname, $data);

        break;
    case 'edit':
        $data = $_POST;
        unset($data['nameOrg']);
        $column = [];
        foreach ($data as $key => $row) {
            $column[] = $key;
        }
        $where = "kodeanggaran='".$data['kodeanggaran']."' AND "."kodeorg='".$data['kodeorg']."' AND "."tahun='".$data['tahun']."'";
        $query = updateQuery($dbname, 'keu_anggaran', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        $query2 = selectQuery($dbname, 'keu_anggaran', 'jumlah', $where);
        $jml = fetchData($query2);
        $data['nameorg'] = $_POST['nameOrg'];
        $data['jumlah'] = $jml[0]['jumlah'];
        showMainHead($dbname, $data);

        break;
    case 'delete':
        $data = $_POST;
        $where = "kodeorg='".$data['kodeorg']."' AND "."kodeanggaran='".$data['kodeanggaran']."' AND "."tahun='".$data['tahun']."'";
        $query = 'delete from `'.$dbname.'`.`keu_anggarandt` where '.$where;
        $query2 = 'delete from `'.$dbname.'`.`keu_anggaran` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        if (!mysql_query($query2)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}
function showMainHead($dbname, $data)
{
    foreach ($data as $key => $row) {
        echo 'var '.$key." = document.getElementById('main_".$key."');";
        echo 'if('.$key.') {';
        echo 'if('.$key.".getAttribute('type')=='checkbox') {";
        if ('1' === $row) {
            echo $key.'.checked=true;';
        } else {
            echo $key.'.checked=false;';
        }

        echo '} else {'.$key.".value='".$row."';}";
        echo '}';
    }
}

?>