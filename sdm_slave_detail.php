<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
if ('createTable' == $_POST['proses']) {
    if (isset($_SESSION['temp']['OrgKd'])) {
        unset($_SESSION['temp']['OrgKd']);
    }

    $idAbn = explode('###', $_POST['absnId']);
    $_SESSION['temp']['OrgKd'] = $idAbn[0];
    $query = selectQuery($dbname, 'sdm_absensidt', '*', "`tanggal`='".tanggalsystem($idAbn[1])."' and `kodeorg`='".$idAbn[0]."'");
    $data = fetchData($query);
    createTabDetail($_POST['absnId'], $data);
} else {
    $data = $_POST;
    unset($data['proses']);
    switch ($_POST['proses']) {
        case 'detail_add':
            if (isset($_SESSION['temp']['OrgKd'])) {
                unset($_SESSION['temp']['OrgKd']);
            }

            $tmp = explode('###', $_POST['absnId']);
            $kdOrg = $tmp[0];
            $_SESSION['temp']['OrgKd'] = $kdOrg;
            $tgl = tanggalsystem($tmp[1]);
            $sql = 'select kodeorg,tanggal from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
            $query = mysql_query($sql);
            $res = mysql_fetch_row($query);
            if ($res < 1) {
                $sins = 'insert into '.$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`) values ('".$kdOrg."','".$tgl."','".$_POST['period']."')";
                if (mysql_query($sins)) {
                    $dins = 'insert into '.$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `penjelasan`) values ('".$kdOrg."','".$tgl."','".$_POST['krywnId']."','".$_POST['shifTid']."','".$_POST['asbensiId']."','".$_POST['Jam']."','".$_POST['ket']."')";
                    if (mysql_query($dins)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                $dins = 'insert into '.$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `penjelasan`) values \r\n\t\t\t\t('".$kdOrg."','".$tgl."','".$_POST['krywnId']."','".$_POST['shifTid']."','".$_POST['asbensiId']."','".$_POST['Jam']."','".$_POST['ket']."')";
                if (mysql_query($dins)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            }

            break;
        case 'detail_edit':
            $tmp = explode('###', $data['absnId']);
            $kdOrg = $tmp[0];
            $_SESSION['temp']['OrgKd'] = $kdOrg;
            $tgl = tanggalsystem($tmp[1]);
            $sCek = 'select posting from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_fetch_assoc($qCek);
            if ('1' == $rCek['posting']) {
                echo 'warning:Already Post This Data';
                exit();
            }

            $where = "`tanggal`='".$tgl."'";
            $where .= " and `kodeorg`='".$kdOrg."'";
            $where .= " and karyawanid='".$data['krywnId']."'";
            $query = 'update '.$dbname.".`sdm_absensidt` set shift='".$data['shifTid']."',absensi='".$data['asbensiId']."',jam='".$data['Jam']."', penjelasan='".$data['ket']."' where ".$where.'';
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        case 'detail_delete':
            $tmp = explode('###', $data['absnId']);
            $kdOrg = $tmp[0];
            $tgl = tanggalsystem($tmp[1]);
            $sCek = 'select posting from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_fetch_assoc($qCek);
            if ('1' == $rCek['posting']) {
                echo 'warning:Already Post This Data';
                exit();
            }

            $data = $_POST;
            $where = "`tanggal`='".$tgl."'";
            $where .= " and `kodeorg`='".$kdOrg."'";
            $where .= " and karyawanid='".$data['krywnId']."'";
            $query = 'delete from `'.$dbname.'`.`sdm_absensidt` where '.$where;
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
    $table .= "<table id='ppDetailTable'>";
    $table .= '<thead>';
    $table .= '<tr>';
    $table .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['shift'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['absensi'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['jam'].'</td>';
    $table .= '<td>'.$_SESSION['lang']['keterangan'].'</td>';
    $table .= '<td>Action</td>';
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= "<tbody id='detailBody'>";
    $i = 0;
    if ($data != []) {
        foreach ($data as $key => $row) {
            if (4 < strlen($row['kodeorg'])) {
                $where = " subbagian='".$row['kodeorg']."'";
            } else {
                $where = "lokasitugas='".$row['kodeorg']."' and subbagian is NULL";
            }

            $whre = " kodeorg='".$row['kodeorg']."'";
            $optShift = makeOption('e-Agro', 'pabrik_5shift', 'shift', $whre);
            $optAbsen = makeOption('e-Agro', 'sdm_5absensi', 'kodeabsen,keterangan');
            $optKry = makeOption('e-Agro', 'datakaryawan', 'karyawanid,namakaryawan', $where);
            $jmr = explode(':', $row['jam']);
            for ($t = 0; $t < 24; ++$t) {
                if (strlen($t) < 2) {
                    $t = '0'.$t;
                }

                $jm .= '<option value='.$t.' '.(($t == $jmr[0] ? 'selected' : '')).'>'.$t.'</option>';
            }
            for ($y = 0; $y < 60; ++$y) {
                if (strlen($y) < 2) {
                    $y = '0'.$y;
                }

                $mnt .= '<option value='.$y.' '.(($y == $jmr[1] ? 'selected' : '')).'>'.$y.'</option>';
            }
            $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= '<td>'.makeElement('krywnId_'.$key.'', 'select', $row['karyawanid'], ['style' => 'width:200px', 'disabled' => 'true'], $optKry).'</td>';
            $table .= '<td>'.makeElement('shiftId_'.$key.'', 'text', $row['shift'], ['style' => 'width:120px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
            $table .= '<td>'.makeElement('absniId_'.$key.'', 'select', $row['absensi'], ['style' => 'width:100px'], $optAbsen).'</td>';
            $table .= '<td><select id=jmId_'.$key.' name=jmId_'.$key.' >'.$jm.'</select>:<select id=mntId_'.$key.' name=mntId_'.$key.' >'.$mnt.'</select></td>';
            $table .= '<td>'.makeElement('ktrng_'.$key.'', 'text', $row['penjelasan'], ['style' => 'width:200px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
            $table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/001_45.png'/>";
            $table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
            $table .= '</tr>';
            $i = $key;
        }
        ++$i;
    }

    $idAbn = explode('###', $id);
    if (4 < strlen($idAbn[0])) {
        $where = " subbagian='".$idAbn[0]."'";
    } else {
        $where = "lokasitugas='".$idAbn[0]."' and subbagian is NULL";
    }

    $optKry = makeOption('e-Agro', 'datakaryawan', 'karyawanid,namakaryawan', $where);
    $whre = " kodeorg='".$idAbn[0]."'";
    $optShift = makeOption('e-Agro', 'pabrik_5shift', 'shift', $whre);
    $optAbsen = makeOption('e-Agro', 'sdm_5absensi', 'kodeabsen,keterangan');
    for ($t = 0; $t < 24; ++$t) {
        if (strlen($t) < 2) {
            $t = '0'.$t;
        }

        $jm .= '<option value='.$t.' '.((0 == $t ? 'selected' : '')).'>'.$t.'</option>';
    }
    for ($y = 0; $y < 60; ++$y) {
        if (strlen($y) < 2) {
            $y = '0'.$y;
        }

        $mnt .= '<option value='.$y.' '.((0 == $y ? 'selected' : '')).'>'.$y.'</option>';
    }
    $table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
    $table .= '<td>'.makeElement('krywnId_'.$i.'', 'select', '', ['style' => 'width:300px'], $optKry).'</td>';
    $table .= '<td>'.makeElement('shiftId_'.$i.'', 'text', '', ['style' => 'width:120px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
    $table .= '<td>'.makeElement('absniId_'.$i.'', 'select', '', ['style' => 'width:100px'], $optAbsen).'</td>';
    $table .= '<td><select id=jmId_'.$i.' name=jmId_'.$i.' >'.$jm.'</select>:<select id=mntId_'.$i.' name=mntId_'.$i.'>'.$mnt.'</select></td>';
    $table .= '<td>'.makeElement('ktrng_'.$i.'', 'text', '', ['style' => 'width:200px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
    $table .= "<td><img id='detail_add_".$i."' title='Simpan' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/save.png'/>";
    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
    $table .= '</tr>';
    $table .= '</tbody>';
    $table .= '</table>';
    echo $table;
}

?>