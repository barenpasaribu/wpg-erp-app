<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
if ('createTable' === $_POST['proses']) {
    $query = selectQuery($dbname, 'pabrik_rawatmesindt', '*', "`notransaksi`='".$_POST['noTrans']."'");
    $data = fetchData($query);
    createTabDetail($_POST['noTrans'], $data);
} else {
    $data = $_POST;
    unset($data['proses']);
    switch ($_POST['proses']) {
        case 'detail_add':
            $lokasi = $_SESSION['empl']['lokasitugas'];
            $entry_by = $_SESSION['standard']['userid'];
            if ('' === $data['jmlh'] || '' === $data['kd_brg']) {
                echo 'Error :Please Complete The Detail Form';
                exit();
            }

            $sCek = 'select notransaksi from '.$dbname.".pabrik_rawatmesinht where notransaksi='".$data['noTrans']."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_fetch_row($qCek);
            if ($rCek < 1) {
                $sIns = 'insert into '.$dbname.".pabrik_rawatmesinht (notransaksi, pabrik, tanggal, shift, statasiun, mesin,kegiatan, jammulai, jamselesai, updateby) \r\n\t\t\tvalues ('".$data['noTrans']."','".$data['pbrkId']."','".$data['tgl']."','".$data['shft']."','".$data['statid']."','".$data['mesinId']."','".$data['kegiatan']."',''".tanggalsystemd($data['jmAwal'])."','".tanggalsystemd($data['jmAkhir'])."','".$userOnline."')";
                echo 'warning:'.$sIns;
                if (mysql_query($sIns)) {
                    $sInd = 'insert into '.$dbname.".pabrik_rawatmesindt (notransaksi, kodebarang, satuan, jumlah, keterangan) values ('".$data['noTrans']."','".$data['kd_brg']."','".$data['satuan']."','".$data['jmlh']."','".$data['ket']."')";
                    if (mysql_query($sInd)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                $sInd = 'insert into '.$dbname.".pabrik_rawatmesindt (notransaksi, kodebarang, satuan, jumlah, keterangan) values ('".$data['noTrans']."','".$data['kd_brg']."','".$data['satuan']."','".$data['jmlh']."','".$data['ket']."')";
                if (mysql_query($sInd)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            }

            break;
        case 'detail_edit':
            if ('' === $data['noTrans'] || '' === $data['kd_brg'] || '' === $data['satuan'] || '' === $data['jmlh']) {
                echo 'Error : Data tidak boleh ada yang kosong';
                exit();
            }

            $where = "`notransaksi`='".$data['noTrans']."'";
            $where .= " and `kodebarang`='".$data['dkd_brg']."'";
            $query = 'update '.$dbname.".`pabrik_rawatmesindt` set kodebarang='".$data['kd_brg']."',satuan='".$data['satuan']."',jumlah='".$data['jmlh']."', keterangan='".$data['ket']."' where ".$where.'';
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        case 'detail_delete':
            $data = $_POST;
            $where = "`notransaksi`='".$data['noTrans']."'";
            $where .= " and `kodebarang`='".$data['kd_brg']."'";
            $query = 'delete from `'.$dbname.'`.`pabrik_rawatmesindt` where '.$where;
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        default:
            break;
    }
}

function createTabDetail($id, $data)
{
    global $dbname;
    global $conn;
    $table = '<b>'.$_SESSION['lang']['notransaksi'].'</b> : '.makeElement('detail_kode', 'text', $id, ['disabled' => 'disabled', 'style' => 'width:200px']);
    $table .= "<table id='ppDetailTable'>";
    $table .= '<thead>';
    $table .= '<tr>';
    $table .= '<td>'.$_SESSION['lang']['kodebarang'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['satuan'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['keterangan'].'</td>';
    $table .= '<td colspan=3>Action</td>';
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= "<tbody id='detailBody'>";
    $i = 0;
    if ($data !== []) {
        foreach ($data as $key => $row) {
            $sbrg = 'select * from '.$dbname.".log_5masterbarang where kodebarang='".$row['kodebarang']."'";
            $qbrg = mysql_query($sbrg);
            $res = mysql_fetch_assoc($qbrg);
            $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= '<td>'.makeElement('kd_brg_'.$key.'', 'txt', $row['kodebarang'], ['style' => 'width:120px', 'disabled' => 'disabled', 'class=myinputtext'])."<input type=hidden value='".$row['kodebarang']."' name=skd_brg_".$key.' id=skd_brg_'.$key.' /></td>';
            $table .= '<td>'.makeElement('nm_brg_'.$key.'', 'txt', $res['namabarang'], ['style' => 'width:120px', 'disabled' => 'disabled', 'class=myinputtext']).'</td>';
            $table .= '<td>'.makeElement('sat_'.$key.'', 'txt', $row['satuan'], ['style' => 'width:70px', 'disabled' => 'disabled', 'class=myinputtext']).'<img src=images/search.png class=dellicon title='.$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value='.$key.">',event)\";></td>";
            $table .= '<td>'.makeElement('jmlh_'.$key.'', 'textnum', $row['jumlah'], ['style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)']).'</td>';
            $table .= '<td>'.makeElement('ket_'.$key.'', 'text', $row['keterangan'], ['style' => 'width:130px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
            $table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/001_45.png'/>";
            $table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
            $table .= '</tr>';
            $i = $key;
        }
        ++$i;
    }

    $table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
    $table .= '<td>'.makeElement('kd_brg_'.$i.'', 'txt', '', ['style' => 'width:120px', 'disabled' => 'disabled', 'class=myinputtext']).'<input type=hidden id=skd_brg_'.$i.' name=skd_brg_'.$i.' /></td>';
    $table .= '<td>'.makeElement('nm_brg_'.$i.'', 'txt', '', ['style' => 'width:120px', 'disabled' => 'disabled', 'class=myinputtext']).'</td>';
    $table .= '<td>'.makeElement('sat_'.$i.'', 'txt', '', ['style' => 'width:70px', 'disabled' => 'disabled', 'class=myinputtext']).'<img src=images/search.png class=dellicon title='.$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><input type=hidden id=nomor name=nomor value='.$i."><div id=container></div>',event)\";></td>";
    $table .= '<td>'.makeElement('jmlh_'.$i.'', 'textnum', '', ['style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)']).'</td>';
    $table .= '<td>'.makeElement('ket_'.$i.'', 'text', '', ['style' => 'width:130px', 'onkeypress' => 'return tanpa_kutip(event)', 'maxlength' => '45']).'</td>';
    $table .= "<td><img id='detail_add_".$i."' title='Simpan' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/save.png'/>";
    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
    $table .= '</tr>';
    $table .= '</tbody>';
    $table .= '</table>';
    echo $table;
}

?>