<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    echo open_body();
    include 'master_mainMenu.php';
    OPEN_BOX('', '<b>'.$_SESSION['lang']['izinkntor'].'/'.$_SESSION['lang']['cuti'].'</b>');
    echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script>\r\n jdl_ats_0='";
    echo $_SESSION['lang']['find'];
    echo "';\r\n// alert(jdl_ats_0);\r\n jdl_ats_1='";
    echo $_SESSION['lang']['findBrg'];
    echo "';\r\n content_0='<fieldset><legend>";
    echo $_SESSION['lang']['findnoBrg'];
    echo "</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';\r\n\r\nnmSaveHeader='';\r\nnmCancelHeader='';\r\nnmDetialDone='";
    echo $_SESSION['lang']['done'];
    echo "';\r\nnmDetailCancel='";
    echo $_SESSION['lang']['cancel'];
    echo "';\r\n\r\n</script>\r\n<script type=\"application/javascript\" src=\"js/sdm_ijin_meninggalkan_kantor.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"headher\">\r\n";
    for ($i = 0; $i < 24; ++$i) {
        if (strlen($i) < 2) {
            $i = '0'.$i;
        }

        $jm .= '<option value='.$i.'>'.$i.'</option>';
    }
    for ($i = 0; $i < 60; ++$i) {
        if (strlen($i) < 2) {
            $i = '0'.$i;
        }

        $mnt .= '<option value='.$i.'>'.$i.'</option>';
    }
    $whrKry = "karyawanid='".$_SESSION['standard']['userid']."'";
    $keKdGol = makeOption($dbname, 'datakaryawan', 'karyawanid,kodegolongan', $whrKry);
    $kdGol = $keKdGol[$_SESSION['standard']['userid']];
    $whrKdgol = "and kodegolongan<='".$kdGol."'";
    $optGanti = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    //$x = 'select karyawanid,lokasitugas,namakaryawan,nik from '.$dbname.'.datakaryawan where lokasitugas in (select kodeunit from '.$dbname.".bgt_regional_assignment\r\nwhere regional='".$_SESSION['empl']['regional']."') ".$whrKdgol." and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
    $x = 'select karyawanid,lokasitugas,namakaryawan,nik from '.$dbname.'.datakaryawan where lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' '." and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
    //echo $x;
    $y = mysql_query($x);
    while ($z = mysql_fetch_assoc($y)) {
        $optGanti .= '<option value='.$z['karyawanid'].'>'.$z['namakaryawan'].' ['.$z['nik'].'] ['.$z['lokasitugas'].']</option>';
    }
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and bagian in ('HO_HRGA','RO_HRGA') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    $str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='hrd') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
    //echo $str;
    $res = mysql_query($str);
    $optKarHrd = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKarHrd .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
    }
    $optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $sOrg = 'select karyawanid, namakaryawan,nik from '.$dbname.".datakaryawan where tipekaryawan='5' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
    $qOrg = mysql_query($sOrg);
    while ($rOrg = mysql_fetch_assoc($qOrg)) {
        $optKary .= '<option value='.$rOrg['karyawanid'].'>'.$rOrg['namakaryawan'].' ['.$rOrg['nik'].']</option>';
    }
    $optKarat = "<option value=''></option>";
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where tipekaryawan=5 and kodegolongan < '3' and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    $str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan dari atasan') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
    $res = mysql_query($str);
    $optKarat = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKarat .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
    }
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where tipekaryawan=5 and kodegolongan<'4' and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    $str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
    //echo $str;

    $res = mysql_query($str);
    $optKar2 = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKar2 .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
    }
    $optJenis = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $str = 'select kodeabsen,keterangan from '.$dbname.".sdm_5absensi where LEFT(kodeabsen,1) in ('C','I') order by keterangan";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optJenis .= "<option value='".$bar->kodeabsen ."'>".$bar->keterangan.' </option>';
    }
    $userlogin = $_SESSION['standard']['userid'];
    $stry = 'select namakaryawan,cast(karyawanid as char) as karyawanid from '.$dbname.".datakaryawan\r\n      where karyawanid=".$_SESSION['standard']['userid']." ";
    // $str .= "UNION select t2.namakaryawan,cast(t1.karyawanid as char) as karyawanid from setup_pengaturanadmin as t1 left join datakaryawan as t2 on (t1.karyawanid=t2.karyawanid) where t1.cuti='1' order by namakaryawan";


    $resss = mysql_query($stry);
    while ($bari = mysql_fetch_object($resss)) {
        $namakar =  $bari->namakaryawan;
        $idkar =  $bari->karyawanid;
    }
    
    $strnew = " SELECT s.*,
                (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.userlogin) AS namauserlogin,
                (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.karyawanid) AS namakaryawan
                FROM setup_pengaturanadmin s where s.cuti = 1 and s.userlogin='$userlogin'";
    $optKarData = '<option value="'.$idkar.'" selected>'.$namakar.'</option>';
    $res = mysql_query($strnew);
    
    while ($bar = mysql_fetch_object($res)) {
        $queryCekIsDuplicate = "SELECT isduplicate FROM datakaryawan WHERE karyawanid='".$bar->karyawanid."'";
        $queryAct = mysql_query($queryCekIsDuplicate);
        $hasilQueryCID = mysql_fetch_object($queryAct);
        if($hasilQueryCID->isduplicate == 0){
            $optKarData .= '<option value="'.$bar->karyawanid.'">'.$bar->namakaryawan.'</option>';   
        }
    }
    /*
    $arragama = getEnum($dbname, 'sdm_ijin', 'jenisijin');
    foreach ($arragama as $kei => $fal) {
        if ('ID' == $_SESSION['language']) {
            $optJenis .= "<option value='".$kei."'>".$fal.'</option>';
        } else {
            switch ($fal) {
                case 'TERLAMBAT':
                    $fal = 'Late for work';

                    break;
                case 'KELUAR':
                    $fal = 'Out of Office';

                    break;
                case 'PULANGAWAL':
                    $fal = 'Home early';

                    break;
                case 'IJINLAIN':
                    $fal = 'Other purposes';

                    break;
                case 'CUTI':
                    $fal = 'Leave';

                    break;
                case 'MELAHIRKAN':
                    $fal = 'Maternity';

                    break;
                default:
                    $fal = 'Wedding, Circumcision or Graduation';

                    break;
            }
            $optJenis .= "<option value='".$kei."'>".$fal.'</option>';
        }
    }
    */
    $stc = 'select right(tanggalmasuk,5) as tanggalmasuk from '.$dbname.'.datakaryawan where karyawanid='.$_SESSION['standard']['userid'];
    $rec = mysql_query($stc);
    $tglmasup = '';
    $hrini = date('md');
    while ($bac = mysql_fetch_object($rec)) {
        $tglmasup = str_replace('-', '', $bac->tanggalmasuk);
    }
    if ($hrini < $tglmasup) {
        $tahunplafon = date('Y') - 1;
    } else {
        $tahunplafon = date('Y');
    }
    $optPeriodec = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    // $optPeriodec .= '<option value='.$tahunplafon.'>'.$tahunplafon.'</option>';
    // $optPeriodec .= '<option value='.($tahunplafon - 1).'>'.($tahunplafon - 1).'</option>';

    $queryGetTahun = "  SELECT periodecuti FROM sdm_cutiht
                        WHERE
                        karyawanid = '".$_SESSION['standard']['userid']."'";
    $dataTahun = fetchData($queryGetTahun);
    // print_r($dataTahun);
    // die();
    foreach ($dataTahun as $key => $value) {
        $optPeriodec .= '<option value="'.$value['periodecuti'].'">'.$value['periodecuti'].'</option>';
    }

    $tanggalSekarang = date("d-m-Y");
    echo "<fieldset style='float:left;'>\r\n<legend>";
    echo $_SESSION['lang']['form'];
    echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['tanggal'];
    echo "</td>\r\n<td>:</td>\r\n<td>
    <input type='text' value=".$tanggalSekarang." class='myinputtext' id='tglIzin' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" />
    </td>\r\n</tr>\r\n";
    echo "<tr>\r\n<td>Karyawan ";
    echo "</td>
            <td>:</td>
            <td>
                <select id=\"karyawanId\"  name=\"karyawanId\" style=\"width:150px\" onchange=\"getTahun()\">";
    echo $optKarData;
    echo "      </select>
            </td>
        </tr>
        ";
    echo "<tr>\r\n<td>".$_SESSION['lang']['jenisijin'];
    echo "</td>\r\n<td>:</td>\r\n<td><select id=\"jnsIjin\" onchange=\"loadSisaCuti()\" name=\"jnsIjin\" style=\"width:150px\">";
    echo $optJenis;
    echo "</select></td>\r\n</tr>\r\n\r\n";
    echo "<tr>\r\n<td>".$_SESSION['lang']['pengabdian'].' '.$_SESSION['lang']['tahun'];
    echo "      </td>
                <td>:</td>
                <td>
                    <select id=\"periodec\"  style=\"width:150px\" onchange=\"loadSisaCuti()\">";
    echo            $optPeriodec;
    echo "          </select>
                </td>
            </tr>
            <tr>
                <td>";
    echo $_SESSION['lang']['dari'].'  '.$_SESSION['lang']['tanggal'].' & '.$_SESSION['lang']['jam'];
    echo "</td>\r\n<td>:</td>\r\n<td><input type='text' class='myinputtext' id='tglAwal' onchange=\"loadSisaCuti()\" onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /><select id=\"jam1\">";
    echo $jm;
    echo '</select>:<select id="mnt1">';
    echo $mnt;
    echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['sampai'].'  '.$_SESSION['lang']['tanggal'].' & '.$_SESSION['lang']['jam'];
    echo "</td>\r\n<td>:</td>\r\n<td><input type='text' class='myinputtext' id='tglEnd' onchange=\"loadSisaCuti()\" onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /><select id=\"jam2\">";
    echo $jm;
    echo '</select>:<select id="mnt2">';
    echo $mnt;
    echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['jumlahhk'].' '.$_SESSION['lang']['diambil'];
    echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" onchange=\"loadSisaCuti()\" id=\"jumlahhk\" name=\"keperluan\" onkeypress=\"return angka_doang(event);\" maxlength=\"5\" value=\"0\"/>";
    echo $_SESSION['lang']['hari'];
    echo " -\r\n(";
    echo $_SESSION['lang']['sisa'];
    echo ': <span id="sis"> 0 </span> '.$_SESSION['lang']['hari'] . ")";
    echo "<br> <span id='notifHK'></span>";
    echo "</td>\r\n</tr>\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['keperluan'];
    echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"keperluan\" name=\"keperluan\" onkeypress=\"return tanpa_kutip(event);\" maxlength=\"30\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['keterangan'];
    echo "</td>\r\n<td>:</td>\r\n<td>\r\n<textarea id='ket'  onkeypress=\"return tanpa_kutip(event);\"></textarea>\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>Pengganti Tugas</td>\r\n<td>:</td>\r\n<td>\r\n    <select id=\"ganti\" style=\"width:150px\">";
    echo $optGanti;
    echo "</select>\r\n</td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['atasan'];
    echo "</td>\r\n<td>:</td>\r\n<td>\r\n    <select id=\"atasan\" style=\"width:150px\">";
    echo $optKar2;
    echo "</select>\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['atasan'];
    echo ' ';
    echo $_SESSION['lang']['dari'];
    echo ' ';
    echo $_SESSION['lang']['atasan'];
    echo "</td>\r\n<td>:</td>\r\n<td>\r\n    <select id=\"atasan2\" style=\"width:150px\">";
    echo $optKarat;
    echo "</select>\r\n</td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n<td>";
    echo $_SESSION['lang']['hrd'];
    echo "</td>\r\n<td>:</td>\r\n<td>\r\n    <select id=\"hrd\" style=\"width:150px\">";
    echo $optKarHrd;
    echo "  </select>\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td colspan=\"3\" id=\"tmblHeader\">\r\n    
            <button disabled class=mybutton id=dtlForm onclick=saveForm()>";
    echo $_SESSION['lang']['save'];
    echo "</button>\r\n    <button class=mybutton id=cancelForm onclick=cancelForm()>";
    echo $_SESSION['lang']['cancel'];
    echo "</button>\r\n</td>\r\n</tr>\r\n</table><div id=messageHK></div><input type=\"hidden\" id=\"atsSblm\" name=\"atsSblm\" />\r\n</fieldset>\r\n\r\n";
    CLOSE_BOX();
    echo "</div>\r\n<div id=\"list_ganti\">\r\n";
    OPEN_BOX();
    echo "    <div id=\"action_list\">\r\n\r\n</div>\r\n<fieldset style='float:left;'>\r\n<legend>";
    echo $_SESSION['lang']['list'];
    echo "</legend>\r\n\r\n<table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n<thead>\r\n<tr class=\"rowheader\"><td>";
    echo $_SESSION['lang']['tanggal'];
    echo "</td>\r\n<td>";
    echo "Nama Karyawan";
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['keperluan'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['jenisijin'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['persetujuan'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['atasan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['atasan'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['approval_status'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['dari'].'  '.$_SESSION['lang']['jam'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['tglcutisampai'].'  '.$_SESSION['lang']['jam'];
    echo "</td>\r\n<td>";
    echo $_SESSION['lang']['ganti'];
    echo "</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contain\">\r\n";
    $arrNmkary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
    $arrKeputusan = [$_SESSION['lang']['diajukan'], $_SESSION['lang']['disetujui'], $_SESSION['lang']['ditolak']];
    $userOnline = $_SESSION['standard']['userid'];
    $limit = 10;
    $page = 0;
    if (isset($_POST['page'])) {
        $page = $_POST['page'];
        if ($page < 0) {
            $page = 0;
        }
    }

    $offset = $page * $limit;
    $ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_ijin where karyawanid='".$_SESSION['standard']['userid']."'  order by `tanggal` desc";
    $query2 = mysql_query($ql2);
    while ($jsl = mysql_fetch_object($query2)) {
        $jlhbrs = $jsl->jmlhrow;
    }
    //$slvhc = 'select t1.tanggal,t1.tipeijin,t1.keperluan,t1.persetujuan1,t1.persetujuan2,t1.stpersetujuan1,t1.darijam,t1.sampaijam,t1.ganti,t1.stpersetujuanrd,t1.jenisijin,t1.hrd,t1.jumlahhari,t1.periodecuti,t2.keterangan from '.$dbname.".sdm_ijin as t1 left join ".$dbname.".sdm_5absensi as t2 on (t1.tipeijin=t2.kodeabsen) where t1.karyawanid='".$_SESSION['standard']['userid']."'   order by t1.tanggal desc limit ".$offset.','.$limit.' ';
    $slvhc = '  select 
                t1.karyawanid,t3.namakaryawan,t1.tanggal,t1.tipeijin,
                t1.keperluan,t1.persetujuan1,t1.persetujuan2,t1.stpersetujuan1,
                t1.darijam,t1.sampaijam,t1.ganti,t1.stpersetujuanhrd,
                t1.jenisijin,t1.hrd,t1.jumlahhari,t1.periodecuti,t1.isBatal
                from '.$dbname.".sdm_ijin as t1 
                
                left join ".$dbname.".datakaryawan as t3 
                on (t1.karyawanid = t3.karyawanid)
                WHERE
                t1.karyawanid = '".$userlogin."'
                OR
                t1.created_by = '".$userlogin."'
                order by t1.tanggal desc limit ".$offset.','.$limit.' ';

    $qlvhc = mysql_query($slvhc);
    $user_online = $_SESSION['standard']['userid'];
    while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
        ++$no;
        $karyawanid = $rlvhc['karyawanid'];
        // $strnew = "SELECT s.*,
        //     (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.userlogin) AS namauserlogin,
        //     (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.karyawanid) AS namakaryawan
        //     FROM setup_pengaturanadmin s where s.userlogin='$userlogin' and s.karyawanid='$karyawanid'";
        //     $res = mysql_query($strnew);
        //     while ($bar = mysql_fetch_object($res)) {
        //         $id_karyawan = $bar->karyawanid;
        
        //     }
        // if($karyawanid == $id_karyawan || $karyawanid == $userlogin ){
            $jenis_ijin = $rlvhc['tipeijin'];
            
            
            echo "<tr class=\"rowcontent\">\r\n";
            echo "\r\n<td>";
            echo tanggalnormal($rlvhc['tanggal']);
            echo "</td>\r\n<td>";
            echo $rlvhc['namakaryawan'];
            echo "</td>\r\n<td>";
            echo $rlvhc['keperluan'];
            echo "</td>\r\n<td>";
            echo $jenis_ijin;
            echo "</td>\r\n<td>";
            echo $arrNmkary[$rlvhc['persetujuan1']];
            echo "</td>\r\n<td>";
            echo $arrNmkary[$rlvhc['persetujuan2']];
            echo "</td>\r\n<td>";
            echo $arrKeputusan[$rlvhc['stpersetujuan1']];
            echo "</td>";

            if ($rlvhc['isBatal'] == 1) {
                echo "<td align=center colspan=2>Cuti dibatalkan</td>";
            } else {
                echo "<td>".tanggalnormald($rlvhc['darijam'])."</td>";
                echo "<td>".tanggalnormald($rlvhc['sampaijam'])."</td>";
            }
            
            
            
            echo "<td>";
            echo $arrNmkary[$rlvhc['ganti']];
            echo "</td>\r\n";
            if (0 == $rlvhc['stpersetujuan1'] && 0 == $rlvhc['stpersetujuanhrd']) {
                echo "<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['keperluan']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['jenisijin']."','".$rlvhc['persetujuan1']."','".$rlvhc['stpersetujuan1']."','".$rlvhc['darijam']."','".$rlvhc['sampaijam']."','".$rlvhc['hrd']."','".$rlvhc['jumlahhari']."','".$rlvhc['periodecuti']."','".$rlvhc['persetujuan2']."');\">\r\n    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['karyawanid']."','".$rlvhc['tanggal']."');\" ></td>";
            } else {
                ?>
                <td><img src="images/pdf.jpg" class="resicon" title="Print" onclick="previewPdf('<?= tanggalnormal($rlvhc['tanggal']); ?>','<?= $karyawanid; ?>',event)"></td>
                <?php
            }

            echo "</tr>\r\n\r\n";
        // }
    }
    echo "\r\n        <tr class=rowheader><td colspan=9 align=center>\r\n        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n        <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n        <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n        </td>\r\n        </tr>";
    echo "\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n";
    CLOSE_BOX();
    echo "</div>\r\n\r\n";
    echo close_body();

?>