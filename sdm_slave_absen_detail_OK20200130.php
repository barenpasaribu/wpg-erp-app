<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
$proses = $_POST['proses'];
$absnId = $_POST['absnId'];
$kdOrg = $_POST['kdOrg'];
$tgAbsn = tanggalsystem($_POST['tgAbsn']);
switch ($proses) {
    case 'createTable':
        $table .= "<table id='ppDetailTable'>";
        $table .= '<thead>';
        $table .= '<tr class=rowheader>';
        $table .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['shift'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['absensi'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['jamMsk'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['jamPlg'].'</td>';
        $table .= "<td title='kehadiran kurang dari 7 jam/Presence under 7 hours'>".$_SESSION['lang']['penaltykehadiran'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['premi'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['keterangan'].'</td>';
        $table .= '<td>Action</td>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= "<tbody id='detailBody'>";
        $idAbn = explode('###', $absnId);
        $tgl = tanggalsystem($idAbn[1]);
        //$where = ' tipekaryawan!=7';
		
        $where = ' 21=21'; //FA 20191023 - FA 20200122
		$where .= " and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar>".$tgl." or tanggalkeluar is NULL or tanggalkeluar = '0000-00-00')";
		$ha = 'select karyawanid,nik,namakaryawan,subbagian from '.$dbname.'.datakaryawan where '.$where.' and karyawanid is not null ';
/*
        if (strlen($idAbn[0])>4) {
            $where .= " and subbagian='".$idAbn[0]."'  and (tanggalkeluar>".$tgl." or tanggalkeluar is NULL or tanggalkeluar = '0000-00-00')";
            $ha = 'select karyawanid,nik,namakaryawan,subbagian from '.$dbname.'.datakaryawan where '.$where.' and karyawanid is not null ';
        } else {
            $where .= " and lokasitugas='".$idAbn[0]."' and (subbagian IS NULL or subbagian='0' or subbagian='')and (tanggalkeluar>".$tgl." or tanggalkeluar is NULL or tanggalkeluar = '0000-00-00')";
            $ha = 'select karyawanid,nik,namakaryawan,subbagian from '.$dbname.'.datakaryawan where '.$where.' and karyawanid is not null ';
            $ha .= 'UNION select a.karyawanid as karyawanid,nik,namakaryawan,b.lokasitugas from '.$dbname.'.setup_temp_lokasitugas a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where kodeorg='".$idAbn[0]."' and a.karyawanid is not null";
        }
*/		
//echo "warning createtable: ".$ha;
//exit();

        $hi = mysql_query($ha);
        while ($rKry = mysql_fetch_assoc($hi)) {
            if (4 == strlen($idAbn[0])) {
                if (strlen($rKry['karyawanid']) < 10) {
                    $rKry['karyawanid'] = addZero($rKry['karyawanid'], 10);
                }

                $scek = 'select * from '.$dbname.".setup_temp_lokasitugas where karyawanid='".$rKry['karyawanid']."'";
                $qcek = mysql_query($scek);
                $rcek = mysql_num_rows($qcek);
                if (0 < $rcek) {
                    $rcekd = mysql_fetch_assoc($qcek);
                    if ($rcekd['kodeorg'] == $idAbn[0]) {
                        $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['nik'].' - '.$rKry['namakaryawan'].'</option>';
                    }
                } else {
                    $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['nik'].' - '.$rKry['namakaryawan'].'</option>';
                }
            } else {
                $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['nik'].' - '.$rKry['namakaryawan'].'</option>';
            }
        }
        $whre = " kodeorg='".$idAbn[0]."'";
        $optShift = makeOption($dbname, 'pabrik_5shift', 'shift', $whre);
        $optAbsen = makeOption($dbname, 'sdm_5absensi', 'kodeabsen,keterangan');
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
        $table .= '<td><select id=krywnId onchange=getPremiTetap() style="width:200px;">'.$optKry."</select> <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namakaryawan'].'/'.$_SESSION['lang']['nik']."','1',event);\"  /></td>";
        $table .= '<td>'.makeElement('shiftId', 'text', '', ['style' => 'width:120px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
        $table .= '<td>'.makeElement('absniId', 'select', '', ['style' => 'width:100px', 'onchange' => 'getPremiTetap()'], $optAbsen).'</td>';
        $table .= '<td><select id=jmId name=jmId onchange=getPremiTetap()>'.$jm.'</select>:<select id=mntId name=mntId onchange=getPremiTetap()>'.$mnt.'</select></td>';
        $table .= '<td><select id=jmId2 name=jmId2 onchange=getPremiTetap()>'.$jm.'</select>:<select id=mntId2 name=mntId2 onchange=getPremiTetap()>'.$mnt.'</select></td>';
        $table .= '<td><input type=text id=dendakehadiran class=myinputtextnumber size=12 onkeypress="return angka_doang(event)" value=0></td>';
        $table .= '<td><input type=text id=premiInsentif class=myinputtextnumber size=12 onkeypress="return angka_doang(event)" /></td>';
        $table .= '<td>'.makeElement('ktrng', 'text', '', ['style' => 'width:200px', 'onkeypress' => 'return tanpa_kutip(event)']).'</td>';
        $table .= "<td><input type=hidden id=insentif value='' /><input type=hidden id=premi /><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/>";
        $table .= "&nbsp;<img id='detail_delete' /></td>";
        $table .= '</tr>';
        $table .= '</tbody>';
        $table .= '</table>';
        echo $table;

        break;
    case 'loadDetail':
        $sDt = 'select * from '.$dbname.".sdm_absensidt where kodeorg='".$kdOrg."' and tanggal='".$tgAbsn."'";
//echo "warning load: ".$sDt;
//exit();
        $qDt = mysql_query($sDt);
        while ($rDet = mysql_fetch_assoc($qDt)) {
            $sNm = 'select nik,namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$rDet['karyawanid']."'";
            $qNm = mysql_query($sNm);
            $rNm = mysql_fetch_assoc($qNm);
            $sAbsn = 'select keterangan from '.$dbname.".sdm_5absensi where kodeabsen='".$rDet['absensi']."'";
            $qAbsn = mysql_query($sAbsn);
            $rAbsn = mysql_fetch_assoc($qAbsn);
            ++$no;
            $strot = 0;
            $drpermi = $rDet['premi'];
            if (0 != $drpermi) {
                $strot = 1;
            }

            echo "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$rNm['nik']."</td>\r\n                        <td>".$rNm['namakaryawan']."</td>\r\n\t\t\t<td>".$rDet['shift']."</td>\r\n\t\t\t<td>".$rAbsn['keterangan']."</td>\r\n\t\t\t<td>".$rDet['jam']."</td>\r\n\t\t\t<td>".$rDet['jamPlg']."</td>\r\n\t\t\t<td align=right>".number_format($drpermi)."</td>\r\n\t\t\t<td align=right>".number_format($rDet['penaltykehadiran'])."</td>\r\n\t\t\t<td>".$rDet['penjelasan']."</td>\r\n\t\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' \r\n\t\t\tonclick=\"editDetail('".$rDet['karyawanid']."','".$rDet['shift']."','".$rDet['absensi']."','".$rDet['jam']."','".$rDet['jamPlg']."','".$rDet['penjelasan']."','".$rDet['penaltykehadiran']."','".$rDet['premi']."','".$rDet['insentif']."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeorg']."','".tanggalnormal($rDet['tanggal'])."','".$rDet['karyawanid']."');\" ></td>\r\n\t\t\t</tr>\r\n\t\t\t";
        }

        break;
    case 'getKary':
        if ('' == $_POST['unit']) {
            exit('error:'.$_SESSION['lang']['kodeorg']." can't empty");
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['nik'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '</tr></thead><tbody>';
		
		//FA 20200122
		$wher = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar>".tanggaldgnbar($_POST['tanggalcr'])." or tanggalkeluar is NULL or tanggalkeluar = '0000-00-00')";
		$sDt = 'select karyawanid,nik,namakaryawan,subbagian from '.$dbname.".datakaryawan where ".$wher." and karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
/*		
        if (strlen($_POST['unit']) > 4) {
            $wher = "subbagian='".$_POST['unit']."'  and (tanggalkeluar>".tanggaldgnbar($_POST['tanggalcr'])." or tanggalkeluar is NULL or tanggalkeluar = '0000-00-00')";
            $sDt = 'select karyawanid,nik,namakaryawan,subbagian from '.$dbname.".datakaryawan where ".$wher." and karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
        } else {
            $wher = "lokasitugas='".$_POST['unit']."' and (subbagian is null or subbagian='') and (tanggalkeluar>".tanggaldgnbar($_POST['tanggalcr'])." or tanggalkeluar is NULL or tanggalkeluar = '0000-00-00')";
            $sDt = 'select karyawanid,nik,namakaryawan,subbagian from '.$dbname.".datakaryawan\r\n                             where ".$wher." and karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
            $sDt .= "UNION \r\n                             select a.karyawanid as karyawanid,nik,namakaryawan,b.lokasitugas from ".$dbname.".setup_temp_lokasitugas a \r\n                             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid \r\n                             where kodeorg='".$_POST['unit']."'   and a.karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
        }
*/		
//echo "warning getkary: ".$sDt;
//exit();
        $qDt = mysql_query($sDt);
        while ($rDt = mysql_fetch_assoc($qDt)) {
            if (strlen($rDt['karyawanid']) < 10) {
                $rDt['karyawanid'] = addZero($rDt['karyawanid'], 10);
            }

            $clid = "onclick=setKary('".$rDt['karyawanid']."') style=cursor:pointer;";
            $tab .= '<tr '.$clid.' class=rowcontent><td>'.$rDt['nik'].'</td>';
            $tab .= '<td>'.$rDt['namakaryawan'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    default:
        break;
}

?>