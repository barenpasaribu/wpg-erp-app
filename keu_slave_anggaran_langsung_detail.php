<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
switch ($proses) {
    case 'showDetail':
        $closed = $_POST['closed'];
        $where = "kodeorg='".$_POST['kodeorg']."' and kodeanggaran='".$_POST['kodeanggaran']."' and tahun=".$_POST['tahun'];
        $query = selectQuery($dbname, 'keu_anggarandt', 'kodebagian,kelompok,kodekegiatan,revisi,kodebarang', $where, 'kodebagian');
        $tmpData = fetchData($query);
        $data = [];
        foreach ($tmpData as $key => $row) {
            $fieldStr = '';
            $fieldVal = '';
            foreach ($row as $h => $r) {
                $fieldStr .= '##'.$h;
                $fieldVal .= '##'.$r;
            }
            unset($row['kelompok'], $row['kodekegiatan']);

            $data[$key] = $row;
            if ('0' === $closed) {
                $data[$key]['manage'] = "<img id='editDetail_".$key."' title='Edit' onclick=\"editDetail(".$key.",event,'".$fieldStr."','".$fieldVal."')\"\r\n\t\t\tclass='zImgBtn' src='images/001_45.png' />&nbsp;"."<img id='deleteDetail_".$key."' title='Hapus' onclick=\"deleteDetail(".$key.",'".$fieldStr."','".$fieldVal."')\"\r\n\t\t\tclass='zImgBtn' src='images/delete_32.png' />";
            } else {
                $data[$key]['manage'] = '';
            }
        }
        $header = [$_SESSION['lang']['kodebagian'], $_SESSION['lang']['revisi'], $_SESSION['lang']['kodebarang'], 'Z'];
        $tables = makeTable('listDetail', 'bodyDetail', $header, $data, [], true, 'detail_tr');
        if ('0' === $closed) {
            echo "<img id='addDetailId' title='Tambah Detail' src='images/plus.png'"."style='width:20px;height:20px;cursor:pointer' onclick='addDetail(event)' />&nbsp;";
        }

        echo $tables;

        break;
    case 'addDetail':
        $data = ['kodeorg' => $_POST['kodeorg'], 'kodebagian' => '', 'kodekegiatan' => '', 'kelompok' => '', 'noaruskas' => '', 'kodebarang' => '', 'revisi' => 0, 'hargasatuan' => 0, 'jumlah' => 0, 'kodevhc' => '', 'jan' => 0, 'peb' => 0, 'mar' => 0, 'apr' => 0, 'mei' => 0, 'jun' => 0, 'jul' => 0, 'agt' => 0, 'sep' => 0, 'okt' => 0, 'nov' => 0, 'dec' => 0];
        if ('VR' === $_POST['tipeanggaran']) {
            $optVhc = makeOption($dbname, 'vhc_5master', 'kodevhc,kodebarang');
            $harga = makeOption($dbname, 'log_5masterbaranganggaran', 'kodebarang,hargasatuan', "kodebarang='".end(array_reverse($optVhc))."'");
            $data['kodebarang'] = end(array_reverse($optVhc));
            $data['kodevhc'] = end(array_reverse(array_keys($optVhc)));
            if ('' === end($harga)) {
                $data['hargasatuan'] = 0;
            } else {
                $data['hargasatuan'] = end($harga);
            }
        }

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
        $column = ['kodeorg', 'kodeanggaran', 'tahun', 'kodebagian', 'kodekegiatan', 'kelompok', 'kodebarang', 'revisi', 'hargasatuan', 'jumlah', 'jan', 'peb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agt', 'sep', 'okt', 'nov', 'dec'];
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
        $total = $data['jan'] + $data['peb'] + $data['mar'] + $data['apr'] + $data['mei'] + $data['jun'] + $data['jul'] + $data['agt'] + $data['sep'] + $data['okt'] + $data['nov'] + $data['dec'];
        if ($data['jumlah'] < $total) {
            alert('Error : Alokasi Jumlah lebih besar dari batas maksimum');
            exit();
        }

        $where = "kodeanggaran='".$_POST['kodeanggaran']."' AND ";
        $where .= "kodebagian='".$_POST['kodebagian']."' AND ";
        $where .= "kodeorg='".$_POST['kodeorg']."' AND ";
        $where .= "tahun='".$_POST['tahun']."' AND ";
        $where .= "kodekegiatan='".$_POST['kodekegiatan']."' AND ";
        $where .= "kelompok='".$_POST['kelompok']."' AND ";
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
    case 'autoFill':
        $where = "kodekegiatan='".$_POST['kodekegiatan']."'";
        $cols = 'kodebarang,kuantitas1,kuantitas2,rotasi,pusingan';
        $query = selectQuery($dbname, 'setup_kegiatannorma', $cols, $where, '', true);
        $resKeg = fetchData($query);
        $param = $_POST;
        if (!empty($resKeg)) {
            $whereBar = '(';
            $i = 0;
            foreach ($resKeg as $row) {
                if (0 === $i) {
                    $whereBar .= "kodebarang='".$row['kodebarang']."'";
                } else {
                    $whereBar .= " or kodebarang='".$row['kodebarang']."'";
                }

                ++$i;
            }
            $whereBar .= ") and kodeorg='".$param['kodeorg']."' and matauang='".$param['matauang']."'";
            $resHarga = makeOption($dbname, 'log_5masterbaranganggaran', 'kodebarang,hargasatuan', $whereBar);
            $data = [];
            $tahun = $param['tahun'];
            $numRow = $param['numRow'];
            unset($param['matauang'], $param['tahun'], $param['numRow']);

            foreach ($resKeg as $key => $row) {
                $barang = $row['kodebarang'];
                if (isset($resHarga[$barang])) {
                    $harga = $resHarga[$barang];
                } else {
                    $harga = 0;
                }

                if (0 === $row['rotasi']) {
                    $norma = 0;
                } else {
                    $norma = $row['kuantitas1'] / $row['kuantitas2'] * $row['rotasi'];
                }

                $data[$key] = $param;
                $data[$key]['revisi'] = (int) $data[$key]['revisi'];
                $data[$key]['kodebarang'] = $barang;
                $data[$key]['hargasatuan'] = $harga;
                $data[$key]['jumlah'] = $norma;
                $data[$key]['jan'] = 0;
                $data[$key]['peb'] = 0;
                $data[$key]['mar'] = 0;
                $data[$key]['apr'] = 0;
                $data[$key]['mei'] = 0;
                $data[$key]['jun'] = 0;
                $data[$key]['jul'] = 0;
                $data[$key]['agt'] = 0;
                $data[$key]['sep'] = 0;
                $data[$key]['okt'] = 0;
                $data[$key]['nov'] = 0;
                $data[$key]['dec'] = 0;
                $data[$key]['tahun'] = $tahun;
                $data[$key]['kodevhc'] = '';
            }
            $colName = '##kodebagian##kodekegiatan##kelompok##revisi##kodebarang';
            $resp = '';
            $dbErr = '';
            foreach ($data as $row) {
                $tmpQuery = insertQuery($dbname, 'keu_anggarandt', $row);
                if (!mysql_query($tmpQuery)) {
                    $dbErr .= 'DB Error : '.mysql_error().'<br>';
                } else {
                    $fieldVal = '##'.$row['kodebagian'].'##'.$row['kodekegiatan'].'##'.$row['kelompok'].'##'.$row['revisi'].'##'.$row['kodebarang'];
                    $resp .= "<tr id='detail_tr_".$numRow."' class='rowcontent'>";
                    $resp .= '<td>'.$row['kodebagian'].'</td>';
                    $resp .= '<td>'.$row['kodekegiatan'].'</td>';
                    $resp .= '<td>'.$row['kelompok'].'</td>';
                    $resp .= '<td>'.$row['revisi'].'</td>';
                    $resp .= '<td>'.$row['kodebarang'].'</td>';
                    $resp .= '<td>';
                    $resp .= "<img id='editDetail_".$numRow."' title='Edit' onclick=\"editDetail(".$numRow.",event,'".$colName."','".$fieldVal."')\"\r\n\t\t\tclass='zImgBtn' src='images/001_45.png' />&nbsp;"."<img id='deleteDetail_".$numRow."' title='Hapus' onclick=\"deleteDetail(".$numRow.",'".$colName."','".$fieldVal."')\"\r\n\t\t\tclass='zImgBtn' src='images/delete_32.png' />";
                    $resp .= '</td></tr>';
                }
            }
            if ('' === $resp) {
                echo $dbErr;
            } else {
                echo $resp;
            }
        }

        break;
    default:
        break;
}
function renderFormDetail($data, $mode = 'add', $num = 0)
{
    global $dbname;
    $holding = getHolding($dbname, $data['kodeorg']);
    $optCashFlow = makeOption($dbname, 'keu_5mesinlaporandt', 'nourut,keterangandisplay', "tipe='Detail' and namalaporan='CASH FLOW DIRECT'");
    if (false !== $holding) {
        $kelompok = ['' => ''];
        $tmpKel = makeOption($dbname, 'setup_klpkegiatan', 'kodeklp,namakelompok', "kodeorg='".$holding['kode']."'");
        foreach ($tmpKel as $key => $row) {
            $kelompok[$key] = $row;
        }
    } else {
        $kelompok = [];
    }

    if ('add' === $mode) {
        $kegiatan = [];
    } else {
        $kegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', "kodekegiatan='".$data['kodekegiatan']."'");
    }

    if ('VR' === $_POST['tipeanggaran']) {
        $optVhc = makeOption($dbname, 'vhc_5master', 'kodevhc,kodevhc');
    }

    $orgBelow = getOrgBelow($dbname, $data['kodeorg'], true, 'blok');
    if ('add' === $mode) {
        $disabled = '';
    } else {
        $disabled = 'disabled';
    }

    $els = [];
    $els[] = [makeElement('kodebagian', 'label', $_SESSION['lang']['kodebagian']), makeElement('kodebagian', 'select', $data['kodebagian'], ['style' => 'width:250px', $disabled => $disabled], $orgBelow)];
    $els[] = [makeElement('kelompok', 'label', $_SESSION['lang']['kelompok']), makeElement('kelompok', 'select', $data['kelompok'], ['style' => 'width:250px', 'onchange' => "getKegiatan(this,'kodekegiatan')", $disabled => $disabled], $kelompok)];
    $els[] = [makeElement('kodekegiatan', 'label', $_SESSION['lang']['kodekegiatan']), makeElement('kodekegiatan', 'select', $data['kodekegiatan'], ['style' => 'width:250px', $disabled => $disabled], $kegiatan)];
    $els[] = [makeElement('noaruskas', 'label', $_SESSION['lang']['noaruskas']), makeElement('noaruskas', 'select', $data['noaruskas'], ['style' => 'width:250px'], $optCashFlow)];
    if ('VR' === $_POST['tipeanggaran']) {
        $els[] = [makeElement('kodevhc', 'label', $_SESSION['lang']['kodevhc']), makeElement('kodevhc', 'select', $data['kodevhc'], ['style' => 'width:250px', 'onchange' => 'updBarang()', $disabled => $disabled], $optVhc)];
        $els[] = [makeElement('kodebarang', 'hid', $data['kodebarang'])];
    } else {
        $els[] = [makeElement('kodevhc', 'hid', $data['kodevhc'])];
        $els[] = [makeElement('kodebarang', 'label', $_SESSION['lang']['kodebarang']), makeElement('kodebarang', 'text', $data['kodebarang'], ['style' => 'width:70px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)', 'readonly' => 'readonly', $disabled => $disabled]).makeElement('getInvBtn', 'btn', 'Cari', ['onclick' => "getInv(event,'kodebarang')", $disabled => $disabled])];
    }

    $els[] = [makeElement('hargasatuan', 'label', $_SESSION['lang']['hargasatuan']), makeElement('hargasatuan', 'textnum', $data['hargasatuan'], ['style' => 'width:70px', 'readonly' => 'readonly'])];
    $els[] = [makeElement('jumlah', 'label', $_SESSION['lang']['jumlah']), makeElement('jumlah', 'textnum', $data['jumlah'], ['style' => 'width:70px', 'readonly' => 'readonly'])];
    $els2 = [];
    $els2[] = [makeElement('jan', 'label', $_SESSION['lang']['jan']), makeElement('jan', 'textnum', $data['jan'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('peb', 'label', $_SESSION['lang']['peb']), makeElement('peb', 'textnum', $data['peb'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('mar', 'label', $_SESSION['lang']['mar']), makeElement('mar', 'textnum', $data['mar'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('apr', 'label', $_SESSION['lang']['apr']), makeElement('apr', 'textnum', $data['apr'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('mei', 'label', $_SESSION['lang']['mei']), makeElement('mei', 'textnum', $data['mei'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('jun', 'label', $_SESSION['lang']['jun']), makeElement('jun', 'textnum', $data['jun'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('jul', 'label', $_SESSION['lang']['jul']), makeElement('jul', 'textnum', $data['jul'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('agt', 'label', $_SESSION['lang']['agt']), makeElement('agt', 'textnum', $data['agt'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('sep', 'label', $_SESSION['lang']['sep']), makeElement('sep', 'textnum', $data['sep'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('okt', 'label', $_SESSION['lang']['okt']), makeElement('okt', 'textnum', $data['okt'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('nov', 'label', $_SESSION['lang']['nov']), makeElement('nov', 'textnum', $data['nov'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
    $els2[] = [makeElement('dec', 'label', $_SESSION['lang']['dec']), makeElement('dec', 'textnum', $data['dec'], ['style' => 'width:90px', 'maxlength' => '13', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'updateQty(this)'])];
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