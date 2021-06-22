<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$param = $_POST;
switch ($param['proses']) {
    case 'getDetailPP':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodeblok'].'</td>';
        $tab .= "<td>\r\n                       <input type=text style=width:150px class=myinputtext id=fnOrg  onkeypress='return tanpa_kutip(event)' value='".$param['kbnId']."' /></td></tr></table>";
        $tab .= '<button class=mybutton onclick=findOrg()>'.$_SESSION['lang']['find'].'</button>';
        $tab .= '<fieldset><legend>'.$_SESSION['lang']['hasil']."</legend>\r\n                       <table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab .= '<thead><tr class=rowheader>';
        $tab .= '<td>'.$_SESSION['lang']['kodeblok'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namaorganisasi']."</td></tr></thead>\r\n                        <tbody id=hasilpencarian style='overflow:auto; width:300px; height:300px;'>";
        $tab .= '</tbody></table></fieldset>';
        echo $tab;

        break;
    case 'cariOrg':
        $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where \r\n                      namaorganisasi like '%".$param['txtfind']."%' or kodeorganisasi like '%".$param['txtfind']."%' and tipe='BLOK' ";
        if ($res = mysql_query($str)) {
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >\r\n                                      <td>".$bar->kodeorganisasi."</td>\r\n                                      <td>".$bar->namaorganisasi."</td>\r\n                                     </tr>";
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getKary':
        $optKary .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sData = 'select distinct nik,karyawanid,namakaryawan from '.$dbname.".datakaryawan where lokasitugas='".$param['kbnId']."'\r\n                             and tanggalkeluar is NULL order by namakaryawan asc";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            $optKary .= "<option value='".$rData['karyawanid']."'>".$rData['nik'].'-'.$rData['namakaryawan'].'</option>';
        }
        echo $optKary;

        break;
    case 'insert':
        if ('' === $param['kodeBlok']) {
            exit('error: '.$_SESSION['lang']['kodeblok']." can't empty");
        }

        if ('' === $param['tglSensus']) {
            exit('error: '.$_SESSION['lang']['tglsensus']." can't empty");
        }

        if ('' === $param['tglPengendalian']) {
            exit('error: '.$_SESSION['lang']['tglPengendalian']." can't empty");
        }

        $hwre = "kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tglSensus'])."'";
        $optCar = makeOption($dbname, 'kebun_qc_ulatapiht', 'kodeblok,tanggal', $hwre);
        if ('' === $optCar[$param['kodeBlok']]) {
            $sInsert = 'insert into '.$dbname.".kebun_qc_ulatapiht \r\n                                  (kodeblok,tanggal,tanggalpengendalian,jenissensus,catatan,pengawas,pendamping,mengetahui,updateby) values ";
            $sInsert .= "('".$param['kodeBlok']."','".tanggaldgnbar($param['tglSensus'])."','".tanggaldgnbar($param['tglPengendalian'])."'\r\n                                    ,'".$param['jenisId']."','".$param['cattn']."','".$param['pengawasId']."','".$param['pendampingId']."','".$param['mengetahuiId']."'\r\n                                    ,'".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sInsert)) {
                exit('error: dberror'.mysql_error($conn).'___'.$sInsert);
            }

            break;
        }

        exit('error: Data already exist');
    case 'updateData':
        $hwre = "kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tglSensus'])."'";
        $supdate = 'update '.$dbname.".kebun_qc_ulatapiht set tanggalpengendalian='".tanggaldgnbar($param['tglPengendalian'])."',\r\n                              jenissensus='".$param['jenisId']."',catatan='".$param['cattn']."',pengawas='".$param['pengawasId']."',\r\n                              pendamping='".$param['pendampingId']."',mengetahui='".$param['mengetahuiId']."',updateby='".$_SESSION['standard']['userid']."'\r\n                              where ".$hwre.'';
        if (!mysql_query($supdate)) {
            exit('error: dberror'.mysql_error($conn).'___'.$supdate);
        }

        break;
    case 'loadNewData':
        echo "\r\n                <table cellspacing=1 border=0 class=sortable>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['kodeblok']."</td>\r\n                <td>".$_SESSION['lang']['tglsensus']."</td>\r\n                <td>".$_SESSION['lang']['jenis']."</td>\r\n                <td>".$_SESSION['lang']['pengawas']."</td>\r\n                <td>".$_SESSION['lang']['pendamping']."</td>\r\n                <td>".$_SESSION['lang']['mengetahui']."</td>\r\n                <td>Action</td>\r\n                </tr>\r\n                </thead>\r\n                <tbody>";
        if ('' !== $param['tanggal']) {
            $whr .= "and tanggal='".tanggaldgnbar($param['tanggal'])."'";
        }

        if ('' !== $param['divisiId']) {
            $whr .= "and kodeblok like '".$param['divisiId']."%'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_qc_ulatapiht where kodeblok!='' ".$whr.' order by `tanggal` desc';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.".kebun_qc_ulatapiht  where kodeblok!='' ".$whr.' order by `tanggal` desc limit '.$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc) ;
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            ++$no;
            echo "\r\n                    <tr class=rowcontent>\r\n                    <td>".$no."</td>\r\n                    <td>".$rlvhc['kodeblok']."</td>\r\n                    <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                    <td>".$rlvhc['jenissensus']."</td>\r\n                    <td>".$optNm[$rlvhc['pengawas']]."</td>\r\n                    <td>".$optNm[$rlvhc['pendamping']]."</td>\r\n                    <td>".$optNm[$rlvhc['mengetahui']]."</td>\r\n                    <td>";
            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeblok']."','".tanggalnormal($rlvhc['tanggal'])."','updateForm');\">\r\n                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeblok']."','".tanggalnormal($rlvhc['tanggal'])."');\" >\t\r\n                    <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_qc_ulatapiht','".$rlvhc['kodeblok'].','.$rlvhc['tanggal']."','','kebun_qc_ulatApi_pdf',event)\">";
            echo "</td>\r\n                </tr>\r\n                ";
        }
        echo '</tbody><tfoot>';
        echo "\r\n                <tr class=rowheader><td colspan=8 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        echo '</tfoot></table>';

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".kebun_qc_ulatapidt \r\n                       where tanggal='".tanggaldgnbar($param['tanggal'])."' and kodeblok='".$param['kodeBlok']."'";
        if (mysql_query($sDel)) {
            $sDelDetail = 'delete from '.$dbname.".kebun_qc_ulatapiht \r\n                                     where tanggal='".tanggaldgnbar($param['tanggal'])."' and kodeblok='".$param['kodeBlok']."'";
            if (mysql_query($sDelDetail)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cekHeader':
        $abs = explode('###', $_POST['absnId']);
        if ('' === $abs[0]) {
            exit('error: Unit code must filled');
        }

        $sCek = 'select DISTINCT tanggalmulai,tanggalsampai,periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            echo 'warning:Date out of range';
            exit();
        }

        $sCek = 'select kodeorg,tanggal from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_fetch_row($qCek);
        if (0 < $rCek) {
            echo 'warning:This date and Organization Name already exist';
            exit();
        }

        $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$periode."' and\r\n                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            $aktif = true;
        } else {
            $aktif = false;
        }

        if (true === $aktif) {
            exit('Error:Accounting period has been closed');
        }

        break;
}

?>