<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$param = $_POST;
switch ($_POST['method']) {
    case 'getKary':
        $kodeorganisasi = $_POST['kodeorganisasi'];
        if ($kodeorganisasi == '') {
            $kodeorganisasi = $_SESSION['empl']['lokasitugas'];
        }

        echo $str = 'select karyawanid,namakaryawan,subbagian,tanggalkeluar,b.tipe,nik from '.$dbname.".datakaryawan a\r\n              left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id\r\n              where lokasitugas='".$kodeorganisasi."' and (tanggalkeluar is NULL or tanggalkeluar = '0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') order by namakaryawan";
        $res = mysql_query($str);
        $opt = "<option value=''></option>";
        while ($bar = mysql_fetch_object($res)) {
            if ($bar->tanggalkeluar == '' && $bar->tanggalkeluar != '') {
                $add = ' Keluar: '.$bar->tanggalkeluar;
            } else {
                $add = '';
            }

            if ($bar->karyawanid == $_POST['karyawanid']) {
                $opt .= "<option value='".$bar->karyawanid."' selected>".$bar->nik.'-'.$bar->namakaryawan.' ['.$bar->subbagian.']-'.$bar->tipe.'-'.$add.'</option>';
            } else {
                $opt .= "<option value='".$bar->karyawanid."'>".$bar->nik.'-'.$bar->namakaryawan.' ['.$bar->subbagian.']-'.$bar->tipe.'-'.$add.'</option>';
            }
        }
        $opt .= "<option value='MASYARAKAT'>".$_SESSION['lang']['masyarakat'].'</option>';
        echo $opt;

        break;
    case 'getForm':
        $dform .= '<table><tr><td>'.$_SESSION['lang']['medicalId'].'</td>';
        $dform .= "<td><input type=text class=myinputtext id=txtCr style=width:150px onkeypress='return tanpa_kutip(event)' /></td></tr>";
        $dform .= '<tr><td colspan=2><button class=mybutton onclick=cariData()>'.$_SESSION['lang']['find'].'</button></td></tr>';
        $dform .= '</table>';
        $dform .= '<fieldset><legend>'.$_SESSION['lang']['result']."</legend><div id=hslCari style='overflow:auto; width:420px; height:330px;'></div>";
        $dform .= "</fieldset><input type=hidden id=karyId value='".$param['karyawanid']."' />\r\n                <input type=hidden id=ygBerobat value='".$param['ygberobat']."' />";
        echo $dform;

        break;
    case 'cari':
        $param['ygBerobat'] = 0;
        if (0 != $param['ygBerobat']) {
            $hwr .= "and nomor='".$param['ygBerobat']."'";
            if ('' != $param['karyId']) {
                $hwr .= "and karyawanid='".$param['karyId']."'";
            }
        }

        $tabledt .= '<table class=sortable cellpadding=1 cellspacing=1><thead>';
        $tabledt .= '<tr class=rowheader><td>'.$_SESSION['lang']['medicalId']."</td>\r\n                   <td>".$_SESSION['lang']['nik']."</td>\r\n                   <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                   <td>".$_SESSION['lang']['lokasitugas']."</td>\r\n                   </tr></thead><tbody>";
        if (0 != $param['ygBerobat']) {
            $sid = 'select distinct idmedicalklrg as idmedical from '.$dbname.".sdm_karyawankeluarga where idmedicalklrg like '%".$param['txtCr']."%' ".$hwr.'';
        } else {
            $sid = 'select distinct  idmedical,karyawanid,namakaryawan,lokasitugas,nik from '.$dbname.".datakaryawan where idmedical like '%".$param['txtCr']."%' ".$hwr.'';
        }

        $qdata = mysql_query($sid);
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $edr = "onclick=setDt('".$rdata['idmedical']."','".$rdata['karyawanid']."','".$rdata['lokasitugas']."') style=cursor:pointer";
            $tabledt .= '<tr class=rowcontent '.$edr.'><td>'.$rdata['idmedical'].'</td>';
            $tabledt .= '<td>'.$rdata['nik'].'</td>';
            $tabledt .= '<td>'.$rdata['namakaryawan'].'</td>';
            $tabledt .= '<td>'.$rdata['lokasitugas'].'</td>';
            $tabledt .= '</tr>';
        }
        $tabledt .= '</tbody></table>';
        echo $tabledt;

        break;
    case 'getMedId':
        $sid = 'select distinct idmedicalklrg as idmedical from '.$dbname.".sdm_karyawankeluarga where \r\n               nomor='".$param['nomor']."'";
        $qid = mysql_query($sid);
        $rid = mysql_fetch_assoc($qid);
        echo $rid['idmedical'];

        break;
}

?>