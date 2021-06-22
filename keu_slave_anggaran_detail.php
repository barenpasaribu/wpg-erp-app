<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
switch ($proses) {
    case 'showDetail':
        $closed = $_POST['closed'];
        $where = "kodeorg='".$_POST['kodeorg']."' and kodeanggaran='".$_POST['kodeanggaran']."' and tahun=".$_POST['tahun'];
        $query = selectQuery($dbname, 'keu_anggarandt', 'kodebagian,kodekegiatan,kelompok,revisi,kodebarang', $where, 'kodebagian');
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
            if ('0' === $closed) {
                $data[$key]['manage'] = "<img id='editDetail_".$key."' title='Edit' onclick=\"editDetail(".$key.",event,'".$fieldStr."','".$fieldVal."')\"\r\n\t\t\tclass='zImgBtn' src='images/001_45.png' />&nbsp;"."<img id='deleteDetail_".$key."' title='Hapus' onclick=\"deleteDetail(".$key.",'".$fieldStr."','".$fieldVal."')\"\r\n\t\t\tclass='zImgBtn' src='images/delete_32.png' />";
            } else {
                $data[$key]['manage'] = '';
            }
        }
        $header = [$_SESSION['lang']['kodebagian'], $_SESSION['lang']['kodekegiatan'], $_SESSION['lang']['kelompok'], $_SESSION['lang']['revisi'], $_SESSION['lang']['kodebarang'], 'Z'];
        $tables = makeTable('listDetail', 'bodyDetail', $header, $data, [], true, 'detail_tr');
        if ('0' === $closed) {
            echo "<img id='addDetailId' title='Tambah Detail' src='images/plus.png'"."style='width:20px;height:20px;cursor:pointer' onclick='addDetail(event)' />&nbsp;";
        }

        echo $tables;

        break;
    case 'addDetail':
        $data = ['kodeorg' => $_POST['kodeorg'], 'kodebagian' => '', 'kodekegiatan' => '', 'noaruskas' => '', 'kelompok' => '', 'kodebarang' => '', 'revisi' => 0, 'hargasatuan' => 0, 'jumlah' => 0, 'jan' => 0, 'peb' => 0, 'mar' => 0, 'apr' => 0, 'mei' => 0, 'jun' => 0, 'jul' => 0, 'agt' => 0, 'sep' => 0, 'okt' => 0, 'nov' => 0, 'dec' => 0];
        $form = renderFormDetail($data);
        echo $form;

        break;
    case 'editDetail':
        $where = "kodeanggaran='".$_POST['kodeanggaran']."' AND "."kodebagian='".$_POST['kodebagian']."' AND "."tahun='".$_POST['tahun']."' AND "."kodeorg='".$_POST['kodeorg']."' AND "."kodekegiatan='".$_POST['kodekegiatan']."' AND "."kelompok='".$_POST['kelompok']."' AND ".'revisi='.$_POST['revisi'].' AND '."kodebarang='".$_POST['kodebarang']."'";
        $query = selectQuery($dbname, 'keu_anggarandt', '*', $where);
        $res = fetchData($query);
        $data = $res[0];
        $form = renderFormDetail($data, 'edit', $_POST['numRow']);
        echo $form;

        break;
    case 'add':
        $data = $_POST;
        $numRow = $_POST['numRow'];
        unset($data['numRow']);
        $data['kelompok'] = '-';
        $column = ['kodeorg', 'kodeanggaran', 'tahun', 'kodebagian', 'kodekegiatan', 'noaruskas', 'kodebarang', 'revisi', 'hargasatuan', 'jumlah', 'jan', 'peb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agt', 'sep', 'okt', 'nov', 'dec', 'kelompok'];
        $query = insertQuery($dbname, 'keu_anggarandt', $data, $column);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        } else {
            $field = $data;
            unset($field['kodeorg'], $field['kodeanggaran']);

            $fieldStr = '';
            $fieldVal = '';
            foreach ($field as $key => $row) {
                $fieldStr .= '##'.$key;
                $fieldVal .= '##'.$row;
            }
            $tRow = "<tr id='detail_tr_".$numRow."'>";
            $tRow .= "<td class='rowcontent'>".$data['kodebagian'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['kodekegiatan'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['kelompok'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['revisi'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['kodebarang'].'</td>';
            $tRow .= "<td class='rowcontent'><img id='editDetail_".$numRow."' title='Edit' onclick=\"editDetail(".$numRow.",event,'".$fieldStr."','".$fieldVal."')\"\r\n\t\tclass='zImgBtn' src='images/001_45.png' />&nbsp;";
            $tRow .= "<img id='deleteDetail_".$numRow."' title='Hapus' onclick=\"deleteDetail(".$numRow.",'".$fieldStr."','".$fieldVal."')\"\r\n\t\tclass='zImgBtn' src='images/delete_32.png' /></td>";
            $tRow .= '</tr>';
            echo $tRow;
        }

        break;
    case 'edit':
        $data = $_POST;
        $numRow = $_POST['numRow'];
        unset($data['numRow']);
        if ('' === $data['kelompok']) {
            $data['kelompok'] = '-';
        }

        $where = "kodeanggaran='".$_POST['kodeanggaran']."' AND ";
        $where .= "kodebagian='".$_POST['kodebagian']."' AND ";
        $where .= "kodeorg='".$_POST['kodeorg']."' AND ";
        $where .= "tahun='".$_POST['tahun']."' AND ";
        $where .= "kodekegiatan='".$_POST['kodekegiatan']."' AND ";
        $where .= "kelompok='".$data['kelompok']."' AND ";
        $where .= 'revisi='.$_POST['revisi'].' AND ';
        $where .= "kodebarang='".$_POST['kodebarang']."'";
        $query = updateQuery($dbname, 'keu_anggarandt', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        } else {
            $field = $data;
            unset($field['kodeorg'], $field['kodeanggaran'], $field['tahun']);

            $fieldStr = '';
            $fieldVal = '';
            foreach ($field as $key => $row) {
                $fieldStr .= '##'.$key;
                $fieldVal .= '##'.$row;
            }
            $tRow = '';
            $tRow .= "<td class='rowcontent'>".$data['kodebagian'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['kodekegiatan'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['kelompok'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['revisi'].'</td>';
            $tRow .= "<td class='rowcontent'>".$data['kodebarang'].'</td>';
            $tRow .= "<td class='rowcontent'><img id='editDetail_".$numRow."' title='Edit' onclick=\"editDetail(".$numRow.",event,'".$fieldStr."','".$fieldVal."')\"\r\n\t\tclass='zImgBtn' src='images/001_45.png' />&nbsp;";
            $tRow .= "<img id='deleteDetail_".$numRow."' title='Hapus' onclick=\"deleteDetail(".$numRow.",'".$fieldStr."','".$fieldVal."')\"\r\n\t\tclass='zImgBtn' src='images/delete_32.png' /></td>";
            echo $tRow;
        }

        break;
    case 'delete':
        $where = "kodeanggaran='".$_POST['kodeanggaran']."' AND ";
        $where .= "kodebagian='".$_POST['kodebagian']."' AND ";
        $where .= "kodeorg='".$_POST['kodeorg']."' AND ";
        $where .= "tahun='".$_POST['tahun']."' AND ";
        $where .= "kodekegiatan='".$_POST['kodekegiatan']."' AND ";
        $where .= "kelompok='".$_POST['kelompok']."' AND ";
        $where .= 'revisi='.$_POST['revisi'].' AND ';
        $where .= "kodebarang='".$_POST['kodebarang']."'";
        $query = 'delete from `'.$dbname.'`.`keu_anggarandt` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    default:
        break;
}
function renderFormDetail($data, $mode = 'add', $num = 0)
{
    global $dbname;
    $holding = getHolding($dbname, $data['kodeorg']);
    if (false !== $holding) {
        $kelompok = ['' => ''];
        $tmpKel = makeOption($dbname, 'setup_klpkegiatan', 'kodeklp,namakelompok', "kodeorg='".$holding['kode']."'");
        foreach ($tmpKel as $key => $row) {
            $kelompok[$key] = $row;
        }
    } else {
        $kelompok = [];
    }

    $optCashFlow = makeOption($dbname, 'keu_5mesinlaporandt', 'nourut,keterangandisplay', "tipe='Detail' and namalaporan='CASH FLOW DIRECT'");
    $whereKeg = "(substr(noakun,1,2)='52' or substr(noakun,1,2)='64') and detail=1";
    $kegiatan = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereKeg, 1);
    if (!isset($_SESSION['org']['below'])) {
        $_SESSION['org']['below'] = getOrgBelow($dbname, $data['kodeorg']);
    }

    $orgBelow = $_SESSION['org']['below'];
    if ('add' === $mode) {
        $disabled = '';
    } else {
        $disabled = 'disabled';
    }

    $els = [];
    $els[] = [makeElement('kodebagian', 'label', $_SESSION['lang']['kodebagian']), makeElement('kodebagian', 'select', $data['kodebagian'], ['style' => 'width:250px', $disabled => $disabled], $orgBelow)];
    $els[] = [makeElement('kelompok', 'label', $_SESSION['lang']['kelompok']), makeElement('kelompok', 'select', $data['kelompok'], ['style' => 'width:250px', 'onchange' => "getKegiatan(this,'kodekegiatan')", 'disabled' => 'disabled'], $kelompok)];
    $els[] = [makeElement('kodekegiatan', 'label', $_SESSION['lang']['posbiaya']), makeElement('kodekegiatan', 'select', $data['kodekegiatan'], ['style' => 'width:250px', $disabled => $disabled], $kegiatan)];
    $els[] = [makeElement('noaruskas', 'label', $_SESSION['lang']['noaruskas']), makeElement('noaruskas', 'select', $data['noaruskas'], ['style' => 'width:250px'], $optCashFlow)];
    $els[] = [makeElement('kodebarang', 'label', $_SESSION['lang']['kodebarang']), makeElement('kodebarang', 'searchBarang', $data['kodebarang'], ['style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)', 'readonly' => 'readonly', $disabled => $disabled])];
    $els[] = [makeElement('hargasatuan', 'label', $_SESSION['lang']['hargasatuan']), makeElement('hargasatuan', 'textnum', $data['hargasatuan'], ['style' => 'width:70px', 'readonly' => 'readonly'])];
    $els[] = [makeElement('jumlah', 'label', $_SESSION['lang']['jumlah']), makeElement('jumlah', 'textnum', $data['jumlah'], ['style' => 'width:70px', 'readonly' => 'readonly'])];
    $els2 = [];
    $els2[] = [makeElement('jan', 'label', $_SESSION['lang']['jan']), makeElement('jan', 'textnum', $data['jan'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('peb', 'label', $_SESSION['lang']['peb']), makeElement('peb', 'textnum', $data['peb'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('mar', 'label', $_SESSION['lang']['mar']), makeElement('mar', 'textnum', $data['mar'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('apr', 'label', $_SESSION['lang']['apr']), makeElement('apr', 'textnum', $data['apr'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('mei', 'label', $_SESSION['lang']['mei']), makeElement('mei', 'textnum', $data['mei'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('jun', 'label', $_SESSION['lang']['jun']), makeElement('jun', 'textnum', $data['jun'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('jul', 'label', $_SESSION['lang']['jul']), makeElement('jul', 'textnum', $data['jul'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('agt', 'label', $_SESSION['lang']['agt']), makeElement('agt', 'textnum', $data['agt'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('sep', 'label', $_SESSION['lang']['sep']), makeElement('sep', 'textnum', $data['sep'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('okt', 'label', $_SESSION['lang']['okt']), makeElement('okt', 'textnum', $data['okt'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('nov', 'label', $_SESSION['lang']['nov']), makeElement('nov', 'textnum', $data['nov'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $els2[] = [makeElement('dec', 'label', $_SESSION['lang']['dec']), makeElement('dec', 'textnum', $data['dec'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty()'])];
    $fieldStr = '##kodebagian##kodekegiatan##kelompok##revisi##kodebarang##hargasatuan'.'##jumlah##jan##peb##mar##apr##mei##jun##jul##agt##sep##okt##nov##dec';
    if ('add' === $mode) {
        $btn = makeElement('addDataDetailB', 'button', $_SESSION['lang']['save'], ['onclick' => 'addDataDetail()', 'style' => 'float:left;clear:both;']);
    } else {
        $btn = makeElement('editDataDetailB', 'button', $_SESSION['lang']['save'], ['onclick' => 'editDataDetail('.$num.')', 'style' => 'float:left;clear:both;']);
    }

    $form = genElTitle('Form Detail', $els);
    $form .= genElementMultiDim('Rincian Sebaran', $els2, 3);
    $form .= $btn;

    return $form;
}

?>