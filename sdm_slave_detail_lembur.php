<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
$proses = $_POST['proses'];
$id = (isset($_POST['absnId']) ? $_POST['absnId'] : '');
$kdOrg = $_POST['kdOrg'];
$tgl = tanggalsystem($_POST['tgl']);
$arrTipeLembur = [$_SESSION['lang']['haribiasa'], $_SESSION['lang']['hariminggu'], $_SESSION['lang']['harilibur'], $_SESSION['lang']['hariraya']];
switch ($proses) {
    case 'createTable':
        $table .= "<table id='ppDetailTable'>";
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['tipelembur'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['jamaktual'].'</td>';
        $table .= "<td style='display:none'>".$_SESSION['lang']['uangmakan'].'</td>';
        $table .= "<td style='display:none'>".$_SESSION['lang']['penggantiantransport'].'</td>';
        $table .= "<td style='display:none'>".$_SESSION['lang']['uangkelebihanjam'].'</td>';
        $table .= '<td>Action</td>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= "<tbody id='detailBody'>";
        $idAbn = explode('###', $id);
        $sTpLmbr2 = 'select tipelembur from '.$dbname.".sdm_5lembur where kodeorg='".substr($idAbn[0], 0, 4)."'";
        $qTpLmbr2 = mysql_query($sTpLmbr2);
        while ($rTpLmbr2 = mysql_fetch_assoc($qTpLmbr2)) {
            $optLmbr2 .= '<option value='.$rTpLmbr2['tipelembur'].' >'.$arrTipeLembur[$rTpLmbr2['tipelembur']].'</option>';
        }
        if (4 < strlen($idAbn[0])) {
            $where = " subbagian='".$idAbn[0]."'";
        } else {
            $where = " lokasitugas='".$idAbn[0]."'";
        }

        $optKry = makeOption('e-Agro', 'datakaryawan', 'karyawanid,namakaryawan', $where, 0);
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
        $table .= "<tr id='detail_tr' class='rowcontent'>";
        $table .= '<td>'.makeElement('krywnId', 'select', '', ['style' => 'width:300px'], $optKry).'</td>';
        $table .= '<td><select id=tpLmbr>'.$optLmbr2.'</select></td>';
        $table .= '<td><select id=jmId name=jmId >'.$jm.'</select>:<select id=mntId name=mntId >'.$mnt.'</select></td>';
        $table .= '<td>'.makeElement('uang_mkn', 'textnum', 0, ['style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '10', 'onblur' => 'chngeFormat()', 'onfocus' => 'normal_number_1()']).'</td>';
        $table .= '<td>'.makeElement('uang_trnsprt', 'textnum', 0, ['style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '10', 'onblur' => 'chngeFormat()', 'onfocus' => 'normal_number_2()']).'</td>';
        $table .= '<td>'.makeElement('uang_lbhjm', 'textnum', 0, ['style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '10', 'onblur' => 'chngeFormat()', 'onfocus' => 'normal_number_3()']).'</td>';
        $table .= "<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/>";
        $table .= "&nbsp;<img id='detail_delete' /></td>";
        $table .= '</tr>';
        $table .= '</tbody>';
        $table .= '</table>';
        echo $table;

        break;
    case 'loadDetail':
        $sDt = 'select * from '.$dbname.".sdm_lemburdt where kodeorg='".$kdOrg."' and tanggal='".$tgl."'";
        // echo "<pre>";
        // print_r($sDt);
        // die();
        $qDt = mysql_query($sDt);
        $no = 0;
        $totum = $totut = $totle = 0;
        while ($rDet = mysql_fetch_assoc($qDt)) {
            $sNm = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$rDet['karyawanid']."'";
            $qNm = mysql_query($sNm);
            $rNm = mysql_fetch_assoc($qNm);
            ++$no;
            echo "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$rNm['namakaryawan']."</td>\r\n\t\t\t<td>".$arrTipeLembur[$rDet['tipelembur']]."</td>\r\n\t\t\t<td>".$rDet['jamaktual']."</td>\r\n\t\t\t<td style='display:none' align=right>".number_format($rDet['uangmakan'], 2)."</td>\r\n\t\t\t<td style='display:none' align=right>".number_format($rDet['uangtransport'], 2)."</td>\r\n\t\t\t<td style='display:none' align=right>".number_format($rDet['uangkelebihanjam'], 2)."</td>\r\n\t\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDetail('".$rDet['karyawanid']."','".$rDet['tipelembur']."','".$rDet['jamaktual']."','".$rDet['uangmakan']."','".$rDet['uangtransport']."','".$rDet['uangkelebihanjam']."');\">\r\n\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeorg']."','".tanggalnormal($rDet['tanggal'])."','".$rDet['karyawanid']."');\" ></td>\r\n\t\t\t</tr>\r\n\t\t\t";
            $totum += $rDet['uangmakan'];
            $totut += $rDet['uangtransport'];
            $totle += $rDet['uangkelebihanjam'];
        }

        break;
    default:
        break;
}

?>