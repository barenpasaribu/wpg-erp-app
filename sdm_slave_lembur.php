<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$txtFind = (isset($_POST['txtfind']) ? $_POST['txtfind'] : '');
$absnId = (isset($_POST['absnId']) ? explode('###', $_POST['absnId']) : ['', '']);
$tgl = tanggalsystem($absnId[1]);
$kdOrg = $absnId[0];
$krywnId = (isset($_POST['krywnId']) ? $_POST['krywnId'] : '');
$tpLmbr = (isset($_POST['tpLmbr']) ? $_POST['tpLmbr'] : '');
$ungTrans = (isset($_POST['ungTrans']) ? $_POST['ungTrans'] : '');
$ungMkn = (isset($_POST['ungMkn']) ? $_POST['ungMkn'] : '');
$Jam = (isset($_POST['Jam']) ? $_POST['Jam'] : '');
$ungLbhjm = (isset($_POST['ungLbhjm']) ? $_POST['ungLbhjm'] : '');
$optKry = '';
$optTipelembur = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$arrsstk = [$_SESSION['lang']['haribiasa'], $_SESSION['lang']['hariminggu'], $_SESSION['lang']['harilibur'], $_SESSION['lang']['hariraya']];
$kodeOrg = (isset($_POST['kodeOrg']) ? $_POST['kodeOrg'] : '');
$basisJam = (isset($_POST['basisJam']) ? $_POST['basisJam'] : '');
$periodeAkutansi = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
foreach ($arrsstk as $kei => $fal) {
    $optTipelembur .= "<option value='".$kei."'>".ucfirst($fal).'</option>';
}
$tpLembur = (isset($_POST['tpLembur']) ? $_POST['tpLembur'] : '');
$basisJam = (isset($_POST['basisJam']) ? $_POST['basisJam'] : '');
switch ($proses) {
    case 'cekData':
        $scek = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        $rRowcek = mysql_num_rows($qcek);
        if (0 < $rRowcek && 1 == $rcek['tutupbuku']) {
            exit('error:  This period '.$rcek['periode'].' already closed');
        }

        $_SESSION['temp']['OrgKd2'] = $kdOrg;
        $sCek = 'select kodeorg,tanggal from '.$dbname.".sdm_lemburht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_row($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".sdm_lemburht (`kodeorg`,`tanggal`,`updateby`,`updatetime`) \r\n                               values ('".$kdOrg."','".$tgl."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."')";
            if (mysql_query($sIns)) {
                if ('' != $tpLmbr && '' != $Jam) {
                    $sDetIns = 'insert into '.$dbname.".sdm_lemburdt \r\n                                        (`kodeorg`,`tanggal`,`karyawanid`,`tipelembur`,`jamaktual`,`uangmakan`,`uangtransport`,`uangkelebihanjam`) values ('".$kdOrg."','".$tgl."','".$krywnId."','".$tpLmbr."','".$Jam."','".$ungMkn."','".$ungTrans."','".$ungLbhjm."')";
                    if (mysql_query($sDetIns)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                    }
                } else {
                    if ('ID' == $_SESSION['language']) {
                        echo 'warning: Masukkan tipe lembur dan basis jam';
                    } else {
                        echo 'warning: Please choose overtime type and actual hours';
                    }

                    exit();
                }
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            if ('' != $tpLmbr && '' != $Jam) {
                $sDetIns = 'insert into '.$dbname.".sdm_lemburdt \r\n                                (`kodeorg`,`tanggal`,`karyawanid`,`tipelembur`,`jamaktual`,`uangmakan`,`uangtransport`,`uangkelebihanjam`) values ('".$kdOrg."','".$tgl."','".$krywnId."','".$tpLmbr."','".$Jam."','".$ungMkn."','".$ungTrans."','".$ungLbhjm."')";
                if (mysql_query($sDetIns)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                if ('ID' == $_SESSION['language']) {
                    echo 'warning: Masukkan tipe lembur dan basis jam';
                } else {
                    echo 'warning: Please choose overtime type and actual hours';
                }

                exit();
            }
        }

        break;
    case 'loadNewData':
        echo "<table cellspacing='1' border='0' class='sortable'>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                <td>".$_SESSION['lang']['namaorganisasi']."</td>\r\n                <td>".$_SESSION['lang']['tanggal']."</td>\r\n                <td>Action</td>\r\n                </tr>\r\n                </thead><tbody>";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_lemburht where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc";
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $scek = 'select distinct jabatan from '.$dbname.".setup_posting where kodeaplikasi='absensi'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        $slvhc = 'select * from '.$dbname.".sdm_lemburht where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc limit ".$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        $no = 0;
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            $thnPeriod = substr($rlvhc['tanggal'], 0, 7);
            $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
            $qOrg = mysql_query($sOrg);
            $rOrg = mysql_fetch_assoc($qOrg);
            $sGp = 'select DISTINCT sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$thnPeriod."' and tanggalmulai<='".$rlvhc['tanggal']."' and tanggalsampai>='".$rlvhc['tanggal']."'";
            $qGp = mysql_query($sGp);
            $rGp = mysql_fetch_assoc($qGp);
            ++$no;
            echo "\r\n                        <tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$rlvhc['kodeorg']."</td>\r\n                        <td>".$rOrg['namaorganisasi']."</td>\r\n                        <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                        <td>";
            if ($thnPeriod == $periodeAkutansi || 0 == $rGp['sudahproses']) {
                $sLok = 'select distinct * from '.$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
                $qLok = mysql_query($sLok);
                $rLok = mysql_fetch_assoc($qLok);
                $rowLok = mysql_num_rows($qLok);
                if (0 < $rowLok) {
                    if ($rLok['kodeorg'] == substr($rlvhc['kodeorg'], 0, 4) && ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby'])) {
                        echo "\r\n                                                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">\r\n                                                <!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
                    }
                } else {
                    if ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby']) {
                        echo "\r\n                                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">\r\n                                    <!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
                    }
                }
            }

            echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_lemburht','".$rlvhc['kodeorg'].','.tanggalnormal($rlvhc['tanggal'])."','','sdm_slave_lemburPdf',event)\">";
            echo "</td>\r\n                        </tr>\r\n                        ";
        }
        echo "\r\n                <tr class=rowheader><td colspan=5 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        echo '</tbody></table>';

        break;
    case 'delData':
        $sCek = 'select posting from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_assoc($qCek);
        if ('1' == $rCek['posting']) {
            echo 'warning: This data has been confirmed, can not continue';
            exit();
        }

        $sDel = 'delete from '.$dbname.".sdm_lemburht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        if (mysql_query($sDel)) {
            $sDelDetail = 'delete from '.$dbname.".sdm_lemburdt where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
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
        $thn = substr($tgl, 0, 4);
        $bln = substr($tgl, 4, 2);
        $periode = $thn.'-'.$bln;
        $sCek = 'select kodeorg,tanggal from '.$dbname.".sdm_lemburht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_row($qCek);
        if (0 < $rCek) {
            echo 'warning: Data already exist';
            exit();
        }

        $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$periode."' and\r\n                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            $aktif = true;
        } else {
            $aktif = false;
        }

        if (true == $aktif) {
            exit('Error: Accounting period has been closed to this date');
        }

        break;
    case 'cariAbsn':
        echo "\r\n                <div style='overflow:auto;height:400px'>\r\n                <table cellspacing='1' border='0' class='sortable'>\r\n<thead>\r\n<tr class=rowheader>\r\n<td>No.</td>\r\n<td>".$_SESSION['lang']['kodeorg']."</td>\r\n<td>".$_SESSION['lang']['namaorganisasi']."</td>\r\n<td>".$_SESSION['lang']['tanggal']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead><tbody>";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        if ('' != $tgl && '' != $kdOrg) {
            $where = " kodeorg = '".$kdOrg."' and tanggal='".$tgl."'";
        } else {
            if ('' != $kdOrg) {
                $where = " kodeorg ='".$kdOrg."'";
            } else {
                if ('' != $tgl) {
                    $where = "kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tanggal='".$tgl."'";
                } else {
                    if ('' == $tgl && '' == $kdOrg) {
                        echo 'warning: Please insert data';
                        exit();
                    }
                }
            }
        }

        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.sdm_lemburht where '.$where.' order by `tanggal`';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $scek = 'select distinct jabatan from '.$dbname.".setup_posting where kodeaplikasi='absensi'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        $slvhc = 'select * from '.$dbname.'.sdm_lemburht where '.$where.' order by `tanggal` limit '.$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            $thnPeriod = substr($rlvhc['tanggal'], 0, 7);
            $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
            $qOrg = mysql_query($sOrg);
            $rOrg = mysql_fetch_assoc($qOrg);
            $sGp = 'select DISTINCT sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$thnPeriod."' and tanggalmulai<='".$rlvhc['tanggal']."' and tanggalsampai>='".$rlvhc['tanggal']."'";
            $qGp = mysql_query($sGp);
            $rGp = mysql_fetch_assoc($qGp);
            ++$no;
            echo "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rlvhc['kodeorg']."</td>\r\n                <td>".$rOrg['namaorganisasi']."</td>\r\n                <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td>";
            if ($thnPeriod == $periodeAkutansi || 0 == $rGp['sudahproses']) {
                $sLok = 'select distinct * from '.$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
                $qLok = mysql_query($sLok);
                $rLok = mysql_fetch_assoc($qLok);
                $rowLok = mysql_num_rows($qLok);
                if (0 < $rowLok) {
                    if ($rLok['kodeorg'] == substr($rlvhc['kodeorg'], 0, 4) && ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby'])) {
                        echo "\r\n                                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">\r\n                                        <!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
                    }
                } else {
                    if ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby']) {
                        echo "\r\n                                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">\r\n                                        <!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
                    }
                }
            }

            echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_lemburht','".$rlvhc['kodeorg'].','.tanggalnormal($rlvhc['tanggal'])."','','sdm_slave_lemburPdf',event)\">";
            echo "</td>\r\n                </tr>\r\n                ";
        }
        echo "\r\n                <tr class=rowheader><td colspan=5 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        echo '</tbody></table></div>';

        break;
    case 'updateDetail':
        $scek = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        $rRowcek = mysql_num_rows($qcek);
        if (0 < $rRowcek && 1 == $rcek['tutupbuku']) {
            exit('error:  This period '.$rcek['periode'].' already closed');
        }

        if ('' != $tpLmbr && '' != $Jam) {
            $sUp = 'update '.$dbname.".sdm_lemburdt set tipelembur='".$tpLmbr."',jamaktual='".$Jam."',uangmakan='".$ungMkn."',uangtransport='".$ungTrans."',uangkelebihanjam='".$ungLbhjm."' where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
            if (mysql_query($sUp)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }

        if ('ID' == $_SESSION['language']) {
            echo 'warning: Masukkan tipe lembur dan basis jam';
        } else {
            echo 'warning: Please choose overtime type and actual hours';
        }

        exit();
    case 'delDetail':
        $sDel = 'delete from '.$dbname.".sdm_lemburdt where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
       // $sDel2 = 'delete from '.$dbname.".sdm_gaji where kodeorg='".$kdOrg."' and karyawanid='".$krywnId."' and idkomponen='17'";
       // $result = mysql_query($sDel2);
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'createTable':
        /*
        if (4 < strlen($kdOrg)) {
            $where = " subbagian='".$kdOrg."'  and (tanggalkeluar>".$tgl.' or tanggalkeluar is NULL)';
            $sKry = 'select namakaryawan,nik,karyawanid from '.$dbname.'.datakaryawan where '.$where.'';
        } else {
        */    
            //$where = " lokasitugas='".$kdOrg."' and (subbagian IS NULL or subbagian='0' or subbagian='') and (tanggalkeluar>".$tgl.' or tanggalkeluar is NULL)';
            $where = " lokasitugas='".$kdOrg."' and (tanggalkeluar>".$tgl.' or tanggalkeluar is NULL)';
            $sKry = 'select namakaryawan,nik,karyawanid from '.$dbname.".datakaryawan \r\n                               where ".$where.'';
        //}

        // print_r($sKry);
        // die();

        $optKry .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $qKry = mysql_query($sKry);
        while ($rKry = mysql_fetch_assoc($qKry)) {
            if (strlen($rKry['karyawanid']) < 10) {
                $rKry['karyawanid'] = addZero($rKry['karyawanid'], 10);
            }

            $scek = 'select * from '.$dbname.".setup_temp_lokasitugas where karyawanid='".$rKry['karyawanid']."'";
            $qcek = mysql_query($scek);
            $rcek = mysql_num_rows($qcek);
            if (0 < $rcek) {
                $rcekd = mysql_fetch_assoc($qcek);
                if ($rcekd['kodeorg'] == substr($kdOrg, 0, 4)) {
                    $optKry .= "<option value='".$rKry['karyawanid']."'>".$rKry['nik'].' - '.$rKry['namakaryawan'].'</option>';
                }
            } else {
                $optKry .= "<option value='".$rKry['karyawanid']."'>".$rKry['nik'].' - '.$rKry['namakaryawan'].'</option>';
            }
        }
        $table = "<table id='ppDetailTable' cellspacing='1' border='0' class='sortable'>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                <td>".$_SESSION['lang']['tipelembur']."</td>\r\n                <td>".$_SESSION['lang']['jamaktual']."</td>\r\n                <td style='display:none'>".$_SESSION['lang']['uangkelebihanjam']."</td>\r\n                <!-- hide permintaan analisa\r\n                <td>".$_SESSION['lang']['penggantiantransport']."</td>\r\n                <td>".$_SESSION['lang']['uangmakan']."</td>-->\r\n                <td>Action</td>\r\n                </tr></thead>\r\n                <tbody id='detailBody'>";
        $table .= "<tr class=rowcontent><td><select id=krywnId name=krywnId style='width:200px'>".$optKry."</select>\r\n                         <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namakaryawan'].'/'.$_SESSION['lang']['nik']."','1',event);\"  /></td>\r\n                <td><select id=tpLmbr name=tpLmbr style='width:100px' onchange='getLembur(0,0)'>".$optTipelembur."</select></td>\r\n                <td><select id=jam name=jam style='width:100px' onchange='getUangLem()'><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td>\r\n                <td style='display:none'><input type='text' class='myinputtextnumber' id='uang_lbhjm' name='uang_lbhjm' style='width:100px' onkeypress='return angka_doang(event)' value=0 />\r\n                <input type='hidden' class='myinputtextnumber' id='uang_trnsprt' name='uang_trnsprt' style='width:100px' onkeypress='return angka_doang(event)' value=0  />\r\n                <input type='hidden' class='myinputtextnumber' id='uang_mkn' name='uang_mkn' style='width:100px' onkeypress='return angka_doang(event)' value=0 />\r\n                </td>\r\n                <!-- hide sesuai dengan analisa, jika ingin mengaktifkan buang comment html dan hapus object yang tipenya hidden\r\n                <td><input type='text' class='myinputtextnumber' id='uang_trnsprt' name='uang_trnsprt' style='width:100px' onkeypress='return angka_doang(event)' value=0  /></td>\r\n                <td><input type='text' class='myinputtextnumber' id='uang_mkn' name='uang_mkn' style='width:100px' onkeypress='return angka_doang(event)' value=0 /></td>-->\r\n                <td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/></td>\r\n                </tr>\r\n                ";
        $table .= '</tbody></table>';
        echo $table;

        break;
    case 'getBasis':
        $dtOrg = $_SESSION['empl']['lokasitugas'];
        $optBasis = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sBasis = 'select jamaktual from '.$dbname.".sdm_5lembur where kodeorg='".$dtOrg."' and tipelembur='".$tpLembur."'";
        $qBasis = mysql_query($sBasis);
        while ($rBasis = mysql_fetch_assoc($qBasis)) {
            $optBasis .= '<option value='.$rBasis['jamaktual'].' '.(($rBasis['jamaktual'] == $basisJam ? 'selected' : '')).'>'.$rBasis['jamaktual'].'</option>';
        }
        echo $optBasis;

        break;
    case 'getUang':
        $optTipe = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
        $uangLembur = '';
        $kodeOrg = substr($kodeOrg, 0, 4);
        $sPengali = 'select jamlembur from '.$dbname.".sdm_5lembur  where kodeorg='".$kodeOrg."' and tipelembur='".$tpLmbr."' and jamaktual='".$basisJam."' ";
        $qPengali = mysql_query($sPengali);
        $rPengali = mysql_fetch_assoc($qPengali);
        $sGt = 'select sum(jumlah) as gapTun from '.$dbname.".sdm_5gajipokok where karyawanid='".$krywnId."' and idkomponen in (1,4,3,35,36,37,38,39,40,41,42,43,44,46,47,48,49,50,51) and tahun=".$_POST['tahun'];
        $qGt = mysql_query($sGt);
        $rGt = mysql_fetch_assoc($qGt);
        if ('KALTENG' == $_SESSION['empl']['regional']) {
            if (1 < $optTipe[$krywnId]) {
                $uangLembur = ($rGt['gapTun'] * $rPengali['jamlembur']) / 173;
            } else {
                $uangLembur = ($rGt['gapTun'] * $rPengali['jamlembur']) / 173;
            }
        } else {
            $uangLembur = ($rGt['gapTun'] * $rPengali['jamlembur']) / 173;
        }

        echo (int) $uangLembur;

        break;
    default:
        break;
}

?>