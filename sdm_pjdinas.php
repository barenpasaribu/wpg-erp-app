<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "<script language=javascript src='js/sdm_pjdinas.js'></script>\r\n";

    OPEN_BOX('', $_SESSION['lang']['perjalanandinas']);
    $str = "SELECT kodeabsen, keterangan 
            FROM sdm_5absensi 
            WHERE 
            kodeabsen = 'D1' 
            OR 
            kodeabsen = 'D2'
            ";
    $res = mysql_query($str);
    $optTipe = "<option value=''>Pilih Tipe Dinas</option>";
    while ($bar = mysql_fetch_object($res)) {
        $optTipe .= "<option value='".$bar->kodeabsen."'>".$bar->keterangan.'</option>';
    }

    $str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
    $res = mysql_query($str);
    $optKar = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
    }
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and kodegolongan in ('1A','1B','1C','1D','1E','1F','1G','1H') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid <>".$_SESSION['standard']['userid'].' and karyawanid in (select karyawanid from setup_approval where lower(applikasi)=\'atasan dari atasan\' ) order by namakaryawan';
    $str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan dari atasan') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
    $res = mysql_query($str);
    $optKar2 = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKar2 .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
    }
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and bagian in ('HO_HRGA','RO_HRGA') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    //$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid <>".$_SESSION['standard']['userid'].' and karyawanid in (select karyawanid from setup_approval where lower(applikasi)=\'persetujuan hrd\' ) order by namakaryawan';
    $str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='hrd') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
    $res = mysql_query($str);
    $optKarHrd = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKarHrd .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
    }
    $str = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi\r\n      where length(kodeorganisasi)=4 order by namaorganisasi desc";
    $res = mysql_query($str);
    echo mysql_error($conn);
    $optOrg = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optOrg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $lokasitugas = $_SESSION['empl']['lokasitugas'];
    $str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan
            where karyawanid=".$_SESSION['standard']['userid']." ";
    $str .= "UNION select t2.namakaryawan,t1.karyawanid from setup_pengaturanadmin as t1 
            left join datakaryawan as t2 on (t1.karyawanid=t2.karyawanid)
            where t1.userlogin='".$_SESSION['standard']['userid']."' and t1.perjalanandinas='1' order by namakaryawan";
            
    $namakaryawan = '';
    $karyawanid = '';
    $optKarData = '';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $namakaryawan = $bar->namakaryawan;
        $karyawanid = $bar->karyawanid;
        if( $karyawanid == $_SESSION['standard']['userid']){
            $optKarData .= '<option value="'.$karyawanid.'" selected>'.$namakaryawan.'</option>';
        }else{
            $optKarData .= '<option value="'.$karyawanid.'">'.$namakaryawan.'</option>';
        }
    }

    $notif = NULL;

    $tanggalHariIni = date("Y-m-d");
    $tanggal1BulanLalu = date('Y-m-d', strtotime('-1 month', strtotime( $tanggalHariIni )));

    $queryCekPertanggungJawaban1 = ' select * from '.$dbname.".sdm_pjdinasht 
                                    where
                                    karyawanid=".$_SESSION['standard']['userid']."
                                    AND lunas=0
                                    AND statuspertanggungjawaban=0 
                                    AND tglpertanggungjawaban = '0000-00-00'
                                    AND tanggalperjalanan <= (NOW() - INTERVAL 1 MONTH)
                                    AND
                                    statuspersetujuan = 1
                                    AND
                                    statuspersetujuan2 = 1
                                    AND 
                                    statushrd = 1
                                    AND
                                    isBatal = 0
                                    ";
                
    $queryAct1 = mysql_query($queryCekPertanggungJawaban1);
    $queryHasil1 = mysql_num_rows($queryAct1);
    
    $queryCekPertanggungJawaban = ' select * from '.$dbname.".sdm_pjdinasht 
                                    where
                                    karyawanid=".$_SESSION['standard']['userid']."
                                    AND lunas=0
                                    AND statuspertanggungjawaban=0 
                                    AND tglpertanggungjawaban = '0000-00-00'
                                    AND
                                    statuspersetujuan = 1
                                    AND
                                    statuspersetujuan2 = 1
                                    AND 
                                    statushrd = 1
                                    AND
                                    isBatal = 0
                                    ";
                                    // print_r($queryCekPertanggungJawaban);
                                    // die();
    $queryAct = mysql_query($queryCekPertanggungJawaban);
    $queryHasil = mysql_num_rows($queryAct);
    
    $disableBtn = "";

    if($queryHasil >= 3 ){
        $disableBtn = "disabled";
        $notif = "<br><h3><b style='padding-left: 30px; color:red;'>Silahkan membuat pertanggung jawaban perjalanan dinas.</b></h3><br><br>";
    }

    if($queryHasil1 >= 3 ){
        $disableBtn = "disabled";
        $notif = "<br><h3><b style='padding-left: 30px; color:red;'>Silahkan membuat pertanggung jawaban perjalanan dinas.</b></h3><br><br>";
    }

    $frm[0] .= "<fieldset>".$notif."
                    <legend>".$_SESSION['lang']['form']."</legend>
                        <table>
                            <tr>
                                <input type=hidden value='insert' id=method>
                                <input type=hidden value='' id=notransaksi>
                                <td>".$_SESSION['lang']['nama']."</td>
                                <td><select ".$disableBtn." id='karyawanid'>".$optKarData."</select></td>
                            </tr>
                            <tr>
                                <td>Tipe Perjalanan Dinas</td>
                                <td><select ".$disableBtn." id='tipe_perjalanan_dinas'>".$optTipe."</select></td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['kodeorg']."</td>
                                <td>
                                    <select id='kodeorg'>
                                        <option ".$disableBtn." value='".$lokasitugas."'>".$lokasitugas."</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['tanggaldinas']."</td>
                                <td><input ".$disableBtn." type=text id=tanggalperjalanan class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10>".$_SESSION['lang']['tanggalkembali']."
                                    <input ".$disableBtn." type=text id=tanggalkembali class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10>
                                </td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['transportasi'].'/'.$_SESSION['lang']['akomodasi']."</td>
                                <td>
                                    <input ".$disableBtn." type=checkbox id=pesawat> ".$_SESSION['lang']['pesawatudara']."
                                    <input ".$disableBtn." type=checkbox id=darat> ".$_SESSION['lang']['transportasidarat']."
                                    <input ".$disableBtn." type=checkbox id=laut> ".$_SESSION['lang']['transportasiair']."
                                    <input ".$disableBtn." type=checkbox id=mess> ".$_SESSION['lang']['mess']."
                                    <input ".$disableBtn." type=checkbox id=hotel> ".$_SESSION['lang']['hotel']."
                                    <input ".$disableBtn." type=checkbox id=mobilsewa>Mobil Sewa
                                    <input ".$disableBtn." type=checkbox id=mobildinas>Mobil Dinas      
                                </td>
                                </tr>
                                <tr>
                                    <td>
                                        ".$_SESSION['lang']['uangmuka']."
                                    </td>
                                    <td>
                                        <input ".$disableBtn." type=text class=myinputtextnumber onblur=change_number(this) id=uangmuka onkeypress=\"return angka_doang(event);\" size=15 maxlength=15>
                                    </td>
                                </tr> 
                                    <tr>
                                    <td>
                                        ".$_SESSION['lang']['keterangan']."
                                    </td>
                                    <td>
                                        <textarea ".$disableBtn." id=ket onkeypress=\"return tanpa_kutip(event);\"></textarea>
                                    </td>
                                </tr> 
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        ".$_SESSION['lang']['tujuan']."1
                                    </td>
                                    <td>
                                        <select ".$disableBtn." id='tujuan1' style='width:150px'>".$optOrg."</select>
                                        ".$_SESSION['lang']['tugas']."
                                        <input ".$disableBtn." type=text id=tugas1 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        ".$_SESSION['lang']['tujuan']."2
                                    </td>
                                    <td>
                                        <select ".$disableBtn." id='tujuan2' style='width:150px'>".$optOrg."</select>
                                        ".$_SESSION['lang']['tugas']."
                                        <input ".$disableBtn." type=text id=tugas2 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
                                    </td>
                                </tr>
                                </tr>
                                <tr><tr>
                                    <td>
                                        ".$_SESSION['lang']['tujuan']."3
                                    </td>
                                    <td>
                                        <select ".$disableBtn." id='tujuan3' style='width:150px'>".$optOrg."</select>
                                        ".$_SESSION['lang']['tugas']."
                                        <input ".$disableBtn." type=text id=tugas3 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        ".$_SESSION['lang']['tujuan']."4
                                    </td>
                                    <td>
                                        <input ".$disableBtn." type=text style='width:150px' id=tujuanlain class=myinputtext onkeypress=\"return tanpa_kutip(event)\" maxlength=45>
                                        ".$_SESSION['lang']['tugas']."
                                        <input ".$disableBtn." type=text id=tugaslain class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
                                    </td>
                                </tr>
                            </table>
                        <fieldset>
                        <legend>
                            ".$_SESSION['lang']['approve']."
                        </legend>
                        <table>
                            <tr>
                                <td>".$_SESSION['lang']['atasan']."</td>
                                <td>
                                    <select ".$disableBtn." id=persetujuan>".$optKar."</select>
                                </td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['atasan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['atasan']."</td>
                                <td>
                                    <select ".$disableBtn." id=persetujuan2>".$optKar2."</select>
                                </td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['hrd']."</td>
                                <td>
                                    <select ".$disableBtn." id=hrd>".$optKarHrd."</select>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <center>
                        <button ".$disableBtn." class=mybutton onclick=simpanPJD()>".$_SESSION['lang']['save']."</button>
                        <button class=mybutton onclick=clearForm()>".$_SESSION['lang']['new']."</button>
                    </center>
                </fieldset>";
    $frm[1] = "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t  <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['approval_status']."</td>\r\n\t  <td>".$_SESSION['lang']['approval_status']." 2</td>\r\n\t  <td>".$_SESSION['lang']['hrd']."</td>\r\n\t  <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= $_POST['tex'];
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht \r\n        where notransaksi like '%".$notransaksi."%'\r\n\t\tand karyawanid=".$_SESSION['standard']['userid']."\r\n\t\torder by jlhbrs desc";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $jlhbrs = $bar->jlhbrs;
    }
    if (isset($_POST['page'])) {
        $page = $_POST['page'];
        if ($page < 0) {
            $page = 0;
        }
    }

    $offset = $page * $limit;
    $str = 'select * from '.$dbname.".sdm_pjdinasht 
            where 
                notransaksi like '%".$notransaksi."%'
            and 
            (
                karyawanid=".$_SESSION['standard']['userid']."
            or 
                created_by=".$_SESSION['standard']['userid']."
            )
            order by 
                tanggalbuat desc limit ".$offset.',20';
    $res = mysql_query($str);
    $no = $page * $limit;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $namakaryawan = '';
        $strx = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->karyawanid;
        $resx = mysql_query($strx);
        while ($barx = mysql_fetch_object($resx)) {
            $namakaryawan = $barx->namakaryawan;
        }
        $add = '';
        if (0 == $bar->statuspersetujuan && 0 == $bar->statushrd) {
            $add .= "&nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">\r\n\t\t &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">\r\n         ";
        }

        if (2 == $bar->statuspersetujuan) {
            $stpersetujuan = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspersetujuan) {
                $stpersetujuan = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan = $_SESSION['lang']['wait_approve'];
                $stpersetujuan .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan','".$bar->notransaksi."')>".$optKar.'</select>';
            }
        }

        if (2 == $bar->statuspersetujuan2) {
            $stpersetujuan2 = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspersetujuan2) {
                $stpersetujuan2 = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan2 = $_SESSION['lang']['wait_approve'];
                $stpersetujuan2 .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan2','".$bar->notransaksi."')>".$optKar2.'</select>';
            }
        }

        if (2 == $bar->statushrd) {
            $sthrd = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statushrd) {
                $sthrd = $_SESSION['lang']['disetujui'];
            } else {
                $sthrd = $_SESSION['lang']['wait_approve'];
                $sthrd .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select   style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'hrd','".$bar->notransaksi."')>".$optKarHrd.'</select>';
            }
        }

        if (2 == $bar->statuspersetujuan) {
            $stpersetujuan2 = '';
            $sthrd = '';
        }

        if ($bar->isBatal == 1) {
            $kolomstatus = "<td colspan=3 align=center>Perjalanan dinas dibatalkan</td>";
        } else {
            $kolomstatus = "<td>".$stpersetujuan."</td>
            <td>".$stpersetujuan2."</td>
            <td>".$sthrd."</td>";
        }
        

        $frm[1] .= "    <tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$bar->notransaksi."</td>
                            <td>".$namakaryawan."</td>
                            <td>".tanggalnormal($bar->tanggalbuat)."</td>
                            <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain." "."</td>
                            ".$kolomstatus."
                            <td align=center>
                                 
        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\">
        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD2('".$bar->notransaksi."',event);\">
        
        ".$add."\r\n\t  </td>\r\n\t  </tr>";
    }
    $frm[1] .= "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";
    $frm[1] .= "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
    $hfrm[0] = $_SESSION['lang']['form'];
    $hfrm[1] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body('');

?>