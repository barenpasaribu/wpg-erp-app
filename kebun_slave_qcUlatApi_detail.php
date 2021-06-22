<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
$optNmblk = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$param = $_POST;
switch ($param['proses']) {
    case 'createTable':
        $table .= "<table id='ppDetailTable'>";
        $table .= '<thead>';
        $table .= '<tr class=rowheader>';
        $table .= '<td>'.$_SESSION['lang']['pokokdiamati'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['luaspengamatan'].'</td>';
        $table .= '<td>Darna Trima</td>';
        $table .= '<td>Setothosea Asigna</td>';
        $table .= '<td>Setora Nitens</td>';
        $table .= '<td>Ulat Kantong</td>';
        $table .= '<td>Keterangan</td>';
        $table .= '<td>Action</td>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= "<tr id='detail_tr' class='rowcontent'>";
        $table .= '<td>'.makeElement('pkkId', 'textnum', '0', ['style' => 'width:300px']).'</td>';
        $table .= '<td>'.makeElement('luasPengamatan', 'textnum', '0', ['style' => 'width:300px']).'</td>';
        $table .= '<td>'.makeElement('darnaTrima', 'textnum', '0', ['style' => 'width:300px']).'</td>';
        $table .= '<td>'.makeElement('Asigna', 'textnum', '0', ['style' => 'width:300px']).'</td>';
        $table .= '<td>'.makeElement('Nitens', 'textnum', '0', ['style' => 'width:300px']).'</td>';
        $table .= '<td>'.makeElement('Kantong', 'textnum', '0', ['style' => 'width:300px']).'</td>';
        $table .= '<td>'.makeElement('ktrangan', 'text', '', ['style' => 'width:300px']).'</td>';
        $table .= "<td><input type=hidden id=nourut />\r\n                           <img id='detail_add' title='".$_SESSION['lang']['save']."' class=zImgBtn onclick=\"svDetail()\" src='images/save.png'/>\r\n                           <img title='".$_SESSION['lang']['clear']."' class=resicon onclick=\"clearData()\" src='images/clear.png'/>";
        $table .= "&nbsp;<img id='detail_delete' /></td>";
        $table .= '</tr>';
        $table .= '</tbody>';
        $table .= '</table>';
        if ('updateForm' === $param['status']) {
            $sdata = 'select distinct * from '.$dbname.".kebun_qc_ulatapiht \r\n                                where kodeblok='".$param['kodeblok']."' and tanggal='".tanggaldgnbar($param['tanggal'])."'";
            $qdata = mysql_query($sdata) ;
            $rdata = mysql_fetch_assoc($qdata);
            $optKary3 = $optKary2 = $optKary .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
            $sData = 'select distinct nik,karyawanid,namakaryawan from '.$dbname.".datakaryawan where lokasitugas='".substr($rdata['kodeblok'], 0, 4)."'\r\n                                and tipekaryawan=3 and tanggalkeluar is NULL order by namakaryawan asc";
            $qData = mysql_query($sData) ;
            while ($rData = mysql_fetch_assoc($qData)) {
                if ('' !== $rdata['pengawas']) {
                    $optKary .= "<option value='".$rData['karyawanid']."' ".(($rdata['pengawas'] === $rData['karyawanid'] ? 'selected' : '')).'>'.$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
                } else {
                    $optKary .= "<option value='".$rData['karyawanid']."'>".$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
                }

                if ('' !== $rdata['pendamping']) {
                    $optKary2 .= "<option value='".$rData['karyawanid']."' ".(($rdata['pendamping'] === $rData['karyawanid'] ? 'selected' : '')).'>'.$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
                } else {
                    $optKary2 .= "<option value='".$rData['karyawanid']."'>".$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
                }

                if ('' !== $rdata['mengetahui']) {
                    $optKary3 .= "<option value='".$rData['karyawanid']."'  ".(($rdata['mengetahui'] === $rData['karyawanid'] ? 'selected' : '')).'>'.$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
                } else {
                    $optKary3 .= "<option value='".$rData['karyawanid']."'>".$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
                }
            }
            $dert = substr($rdata['kodeblok'], 0, 4).'####'.$rdata['kodeblok'].'####'.$optNmblk[$rdata['kodeblok']].'####'.tanggalnormal($rdata['tanggal']).'####';
            $dert .= tanggalnormal($rdata['tanggalpengendalian']).'####'.$rdata['jenissensus'].'####'.$rdata['catatan'].'####';
            $dert .= $optKary.'####'.$optKary2.'####'.$optKary3;
        } else {
            $dert = '';
        }

        if ('updateForm' === $param['status']) {
            echo $table.'####'.$dert;
        } else {
            echo $table;
        }

        break;
    case 'loadDetail':
        $sDt = 'select * from '.$dbname.".kebun_qc_ulatapidt\r\n                      where kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tanggal'])."'";
        $qDt = mysql_query($sDt) ;
        while ($rDet = mysql_fetch_assoc($qDt)) {
            ++$no;
            echo "\r\n                        <tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td align=right>".$rDet['pokokdiamati']."</td>\r\n                        <td align=right>".$rDet['luasdiamati']."</td>\r\n                        <td align=right>".$rDet['jlhdarnatrima']."</td>\r\n                        <td align=right>".$rDet['jlhsetothosea']."</td>\r\n                        <td align=right>".$rDet['jlhsetoranitens']."</td>\r\n\t\t\t\t\t\t<td align=right>".$rDet['jlhulatkantong']."</td>\r\n                        <td>".$rDet['keterangan']."</td>\r\n                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' \r\n                        onclick=\"editDetail('".$rDet['pokokdiamati']."','".$rDet['luasdiamati']."','".$rDet['jlhdarnatrima']."','".$rDet['jlhsetothosea']."','".$rDet['jlhsetoranitens']."','".$rDet['jlhulatkantong']."','".$rDet['keterangan']."','".$rDet['nourut']."');\">\r\n                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeblok']."','".tanggalnormal($rDet['tanggal'])."','".$rDet['nourut']."');\" ></td>\r\n                        </tr>\r\n                        ";
        }

        break;
    case 'insertDetail':
        if ('' === $param['nourut']) {
            $sck = 'select distinct * from '.$dbname.".kebun_qc_ulatapidt where \r\n                              kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tanggal'])."'\r\n                              order by nourut desc limit 0,1";
            $qck = mysql_query($sck) ;
            $rck = mysql_fetch_assoc($qck);
            if ('' === $rck['nourut']) {
                $param['nourut'] = 1;
            } else {
                $param['nourut'] = $rck['nourut'] + 1;
            }

            $sinsert = 'insert into '.$dbname.'.kebun_qc_ulatapidt values ';
            $sinsert .= "('".$param['kodeBlok']."','".tanggaldgnbar($param['tanggal'])."','".$param['nourut']."'\r\n                        ,'".$param['pkkId']."','".$param['luasPengamatan']."','".$param['darnaTrima']."','".$param['Asigna']."'\r\n                        ,'".$param['Nitens']."','".$param['Kantong']."','".$param['ktrangan']."')";
            if (!mysql_query($sinsert)) {
                exit('error: dberror'.mysql_error($conn).'___'.$sinsert);
            }
        } else {
            $sdel = 'delete from '.$dbname.".kebun_qc_ulatapidt where \r\n                               kodeblok='".$param['kodeBlok']."'  and tanggal='".tanggaldgnbar($param['tanggal'])."'\r\n                               and nourut='".$param['nourut']."'";
            if (mysql_query($sdel)) {
                $sinsert = 'insert into '.$dbname.'.kebun_qc_ulatapidt values ';
                $sinsert .= "('".$param['kodeBlok']."','".tanggaldgnbar($param['tanggal'])."','".$param['nourut']."'\r\n                                        ,'".$param['pkkId']."','".$param['luasPengamatan']."','".$param['darnaTrima']."','".$param['Asigna']."'\r\n                                        ,'".$param['Nitens']."','".$param['Kantong']."','".$param['ktrangan']."')";
                if (!mysql_query($sinsert)) {
                    exit('error: dberror'.mysql_error($conn).'___'.$sinsert);
                }
            } else {
                exit('error: dberror'.mysql_error($conn).'___'.$sdel);
            }
        }

        break;
    case 'delDetail':
        $sdel = 'delete from '.$dbname.".kebun_qc_ulatapidt where kodeblok='".$param['kodeBlok']."'  and tanggal='".tanggaldgnbar($param['tanggal'])."'\r\n                               and nourut='".$param['nourut']."'";
        if (!mysql_query($sdel)) {
            exit('error: dberror'.mysql_error($conn).'___'.$sdel);
        }

        break;
}

?>