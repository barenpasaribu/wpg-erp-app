<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$proses = $_POST['proses'];
$txtFind = $_POST['txtfind'];
$absnId = explode('###', $_POST['absnId']);
$tgl = tanggalsystem($absnId[1]);
$kdOrg = $absnId[0];
$krywnId = $_POST['krywnId'];
$shifTid = $_POST['shifTid'];
$asbensiId = $_POST['asbensiId'];
$Jam = $_POST['Jam'];
$Jam2 = $_POST['Jam2'];
$ket = $_POST['ket'];
$periode = $_POST['period'];
$idOrg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$catu = $_POST['catu'];
$penaltykehadiran = $_POST['dendakehadiran'];
$periodeAkutansi = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
$kdJbtn = makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan', $where);
$tipeKary = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $where);
switch ($proses) {
    case 'cariOrg':
        $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where namaorganisasi like '%".$txtFind."%' or kodeorganisasi like '%".$txtFind."%' ";
        if ($res = mysql_query($str)) {
            echo "\r\n          <fieldset>\r\n        <legend>Result</legend>\r\n        <div style=\"overflow:auto; height:300px;\" >\r\n        <table class=data cellspacing=1 cellpadding=2  border=0>\r\n                                 <thead>\r\n                                 <tr class=rowheader>\r\n                                 <td class=firsttd>\r\n                                 No.\r\n                                 </td>\r\n                                 <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                                 <td>".$_SESSION['lang']['namaorganisasi']."</td>\r\n                                 </tr>\r\n                                 </thead>\r\n                                 <tbody>";
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >\r\n                                          <td class=firsttd>".$no."</td>\r\n                                          <td>".$bar->kodeorganisasi."</td>\r\n                                          <td>".$bar->namaorganisasi."</td>\r\n                                         </tr>";
            }
            echo "</tbody>\r\n                                  <tfoot>\r\n                                  </tfoot>\r\n                                  </table></div></fieldset>";
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'cariOrg2':
        $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where namaorganisasi like '%".$txtFind."%' or kodeorganisasi like '%".$txtFind."%' ";
        if ($res = mysql_query($str)) {
            echo "\r\n          <fieldset>\r\n        <legend>Result</legend>\r\n        <div style=\"overflow:auto; height:300px;\" >\r\n        <table class=data cellspacing=1 cellpadding=2  border=0>\r\n                                 <thead>\r\n                                 <tr class=rowheader>\r\n                                 <td class=firsttd>\r\n                                 No.\r\n                                 </td>\r\n                                 <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                                 <td>".$_SESSION['lang']['namaorganisasi']."</td>\r\n                                 </tr>\r\n                                 </thead>\r\n                                 <tbody>";
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg2('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >\r\n                                          <td class=firsttd>".$no."</td>\r\n                                          <td>".$bar->kodeorganisasi."</td>\r\n                                          <td>".$bar->namaorganisasi."</td>\r\n                                         </tr>";
            }
            echo "</tbody>\r\n                                  <tfoot>\r\n                                  </tfoot>\r\n                                  </table></div></fieldset>";
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'cekData':
        if ('' == $kdOrg) {
            exit('error: Unit code must filled');
        }

        if ('' == $asbensiId) {
            exit('error: Absen tipe empty');
        }

        $scek = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        $rRowcek = mysql_num_rows($qcek);
        if (0 < $rRowcek && 1 == $rcek['tutupbuku']) {
            exit('error:  This periode '.$rcek['periode'].' already closed');
        }

        $sCek = 'select DISTINCT tanggalmulai,tanggalsampai,periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            $sCek = 'select kodeorg,tanggal from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_fetch_row($qCek);
            if ($rCek < 1) {
                $sIns = 'insert into '.$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`,`updateby`,`updatetime`)\r\n                                       values ('".$kdOrg."','".$tgl."','".$periode."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."')";
                if (mysql_query($sIns)) {
                    if ('' == $_POST['premidt']) {
                        $_POST['premidt'] = 0;
                    }

                    if ('' == $_POST['insentif']) {
                        $_POST['insentif'] = 0;
                    }

                    if (('H' == $asbensiId || 'AS' == $asbensiId) && $_POST['premidt'] != $_POST['premi']) {
                        $_POST['premi'] = $_POST['premidt'];
                    }

                    $sdtCek = 'select distinct * from '.$dbname.".kebun_kehadiran_vw\r\n                                                 where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                    $qDtCek = mysql_query($sdtCek);
                    $rSource = mysql_fetch_assoc($qDtCek);
                    $rDtCek = mysql_num_rows($qDtCek);
                    if (0 < $rDtCek) {
                        exit('error: Employee registered on transaction : '.$rSource['notransaksi']);
                    }

                    $sDetIns = 'insert into '.$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`)\r\n                                                  values ('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".$ket."',".$penaltykehadiran.','.$_POST['premidt'].','.$_POST['insentif'].')';
                    if (mysql_query($sDetIns)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.mysql_error($conn).$sDetIns;
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn).$sIns;
                }
            } else {
                if ('' == $_POST['premidt']) {
                    $_POST['premidt'] = 0;
                }

                if ('' == $_POST['insentif']) {
                    $_POST['insentif'] = 0;
                }

                $sdtCek = 'select distinct * from '.$dbname.".kebun_kehadiran_vw\r\n                                                 where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                $qDtCek = mysql_query($sdtCek);
                $rSource = mysql_fetch_assoc($qDtCek);
                $rDtCek = mysql_num_rows($qDtCek);
                if (0 < $rDtCek) {
                    exit('error: Employee registered on transaction : '.$rSource['notransaksi']);
                }

                $sDetIns = 'insert into '.$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`)\r\n                                                  values ('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".$ket."',".$penaltykehadiran.','.$_POST['premidt'].','.$_POST['insentif'].')';
                if (mysql_query($sDetIns)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn).$sDetIns;
                }
            }

            break;
        }

        echo 'warning:Date out of payment period';
        exit();
    case 'loadNewData':
        echo "\r\n                <table cellspacing=1 border=0 class=sortable>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                <td>".$_SESSION['lang']['tanggal']."</td>\r\n                <td>".$_SESSION['lang']['periode']."</td>\r\n                <td>Action</td>\r\n                </tr>\r\n                </thead>\r\n                <tbody>\r\n                ";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }
		$where = "";
		if ('' != $kdOrg) {
            $where .= " and a.kodeorg='".$kdOrg."'";
        }

        if ('' != $tgl) {
            $bln = explode('-', $absnId[1]);
            $where .= " and b.tanggal='".$bln[2].'-'.$bln[1].'-'.$bln[0]."'";
        }
		
        $offset = $page * $limit;
        //$ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."' order by `tanggal` desc";
        $ql2 = "select count(*) as jmlhrow from (select distinct kodeorg,periode,posting,updateby 
	from ".$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."') as a inner join (select distinct tanggal,kodeorg, cast(DATE_FORMAT(tanggal, '%Y-%m') as char(10)) AS periode from sdm_absensidt where substring(kodeorg,1,4)='".$idOrg."' ) as b on (a.kodeorg=b.kodeorg and a.periode=b.periode) where 1=1 ".$where;
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        //$slvhc = 'select * from '.$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."' order by `tanggal` desc limit ".$offset.','.$limit.'';
        $slvhc = "select b.tanggal,a.kodeorg,a.periode,a.posting,a.updateby from (select distinct kodeorg,periode,posting,updateby 
	from ".$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."') as a inner join (select distinct tanggal,kodeorg, cast(DATE_FORMAT(tanggal, '%Y-%m') as char(10)) AS periode from sdm_absensidt where substring(kodeorg,1,4)='".$idOrg."' ) as b on (a.kodeorg=b.kodeorg and a.periode=b.periode) where 1=1 ".$where." order by b.tanggal desc limit ".$offset.','.$limit.'';	
	
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
            $qOrg = mysql_query($sOrg);
            $rOrg = mysql_fetch_assoc($qOrg);
            $sGp = 'select DISTINCT sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$rlvhc['kodeorg']."' and `periode`='".$rlvhc['periode']."'";
            $qGp = mysql_query($sGp);
            $rGp = mysql_fetch_assoc($qGp);
            ++$no;
            echo "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rlvhc['kodeorg']."</td>\r\n                <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td>".substr(tanggalnormal($rlvhc['periode']), 1, 7)."</td>\r\n                <td>";
            $scek = 'select distinct jabatan from '.$dbname.".setup_posting where kodeaplikasi='absensi'";
            $qcek = mysql_query($scek);
            $rcek = mysql_fetch_assoc($qcek);
            if ($rlvhc['periode'] == $periodeAkutansi || 0 == $rGp['sudahproses']) {
                // $sLok = 'select distinct * from '.$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
                // $qLok = mysql_query($sLok);
                // $rLok = mysql_fetch_assoc($qLok);
                // $rowLok = mysql_num_rows($qLok);
                // if (0 < $rowLok) {
                //     if ($rLok['kodeorg'] == substr($rlvhc['kodeorg'], 0, 4) && ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby'])) {
                //         echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">\r\n                                     ";
                //     }
                // } else {
                    if ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby']) {
                        echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">\r\n                                     ";
                    }
                // }
            }
            echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].','.tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
            echo "</td>\r\n                </tr>\r\n                ";
        }
        echo "\r\n                <tr class=rowheader><td colspan=5 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        echo '</tbody></table>';

        break;
    case 'delData':
        $sCek = 'select posting from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_assoc($qCek);
        if ('1' == $rCek['posting']) {
            echo 'warning:Already Post This Data';
            exit();
        }

        $sDel = 'delete from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        if (mysql_query($sDel)) {
            $sDelDetail = 'delete from '.$dbname.".sdm_absensidt where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
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
        if ('' == $abs[0]) {
            exit('error: Unit code must filled');
        }

        $sCek = 'select DISTINCT tanggalmulai,tanggalsampai,periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            echo 'warning:Date out of range';
            exit();
        }

        $sCek = 'select kodeorg,tanggal from '.$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
        $qCek = mysql_query($sCek);
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

        if (true == $aktif) {
            exit('Error: This period '.$periode.' already closed');
        }

        break;
    case 'cariAbsn':
        echo "\r\n                <div style=overflow:auto; height:350px;>\r\n                <table cellspacing=1 border=0>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                <td>".$_SESSION['lang']['tanggal']."</td>\r\n                <td>".$_SESSION['lang']['periode']."</td>\r\n                <td>Action</td>\r\n                </tr>\r\n                </thead>\r\n                <tbody>\r\n                ";
        if ('' != $kdOrg) {
            $where .= " and kodeorg='".$kdOrg."'";
        }

        if ('' != $tgl) {
            $bln = explode('-', $absnId[1]);
            $where .= " and tanggal='".$bln[2].'-'.$bln[1].'-'.$bln[0]."'";
        }

        $sCek = 'select * from '.$dbname.".sdm_absensiht where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' ".$where.'';
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            $slvhc = 'select * from '.$dbname.".sdm_absensiht where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' ".$where.'  order by `tanggal` desc ';
            $qlvhc = mysql_query($slvhc);
            $user_online = $_SESSION['standard']['userid'];
            while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
                $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
                $qOrg = mysql_query($sOrg);
                $rOrg = mysql_fetch_assoc($qOrg);
                $sGp = 'select DISTINCT sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$rlvhc['kodeorg']."' and `periode`='".$rlvhc['periode']."'";
                $qGp = mysql_query($sGp);
                $rGp = mysql_fetch_assoc($qGp);
                ++$no;
                echo "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rlvhc['kodeorg']."</td>\r\n                <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td>".substr(tanggalnormal($rlvhc['periode']), 1, 7)."</td>\r\n                <td>";
                $scek = 'select distinct jabatan from '.$dbname.".setup_posting where kodeaplikasi='absensi'";
                $qcek = mysql_query($scek);
                $rcek = mysql_fetch_assoc($qcek);
                if ($rlvhc['periode'] == $periodeAkutansi || 0 == $rGp['sudahproses']) {
                    $sLok = 'select distinct * from '.$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
                    $qLok = mysql_query($sLok);
                    $rLok = mysql_fetch_assoc($qLok);
                    $rowLok = mysql_num_rows($qLok);
                    if (0 < $rowLok) {
                        if ($rLok['kodeorg'] == substr($rlvhc['kodeorg'], 0, 4) && ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby'])) {
                            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">\r\n                                     ";
                        }
                    } else {
                        if ($_SESSION['empl']['kodejabatan'] == $rcek['jabatan'] || $_SESSION['standard']['userid'] == $rlvhc['updateby']) {
                            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">\r\n                                 ";
                        }
                    }
                }

                echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].','.tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
                echo "</td>\r\n                </tr>\r\n                ";
            }
            echo '</tbody></table></div>';
        } else {
            echo '<tr class=rowcontent><td colspan=5 align=center>Not Found</td></tr></tbody></table></div>';
        }

        break;
    case 'updateData':
        if ('' == $_POST['premidt']) {
            $_POST['premidt'] = 0;
        }

        if ('' == $kdOrg) {
            exit('error:Unit code must filled');
        }

        $scek = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        $rRowcek = mysql_num_rows($qcek);
        if (0 < $rRowcek && 1 == $rcek['tutupbuku']) {
            exit('error:  This period '.$rcek['periode'].' already closed');
        }

        $sdtCek = 'select distinct * from '.$dbname.".kebun_kehadiran_vw\r\n                                 where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
        $qDtCek = mysql_query($sdtCek);
        $rSource = mysql_fetch_assoc($qDtCek);
        $rDtCek = mysql_num_rows($qDtCek);
        if (0 < $rDtCek) {
            exit('error: Employee registered on transaction : '.$rSource['notransaksi']);
        }

        $sUpd = 'update '.$dbname.".sdm_absensidt set shift='".$shifTid."',absensi='".$asbensiId."',jam='".$Jam."',jamPlg='".$Jam2."',penjelasan='".$ket."',\r\n                       penaltykehadiran=".$penaltykehadiran." ,`premi` ='".$_POST['premidt']."',`insentif` ='".$_POST['insentif']."'\r\n                       where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
        if (mysql_query($sUpd)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delDetail':
        $sDelDetail = 'delete from '.$dbname.".sdm_absensidt where tanggal='".$tgl."' and kodeorg='".$kdOrg."' and karyawanid='".$krywnId."'";
        if (mysql_query($sDelDetail)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'getPremi':
        $upahHarian = 0;
        $where = "karyawanid='".$_POST['karyId']."'";
        $tpKary = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $where);
        $tgl = explode('-', $_POST['tglDt']);
        $periode = $tgl[2].'-'.$tgl[1];
        $isi = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            exit();
        }

        if (4 == $tpKary[$_POST['karyId']]) {
            $sUmr = 'select sum(jumlah) as jumlah from '.$dbname.".sdm_5gajipokok\r\n                        where karyawanid='".$_POST['karyId']."' and tahun=".$tgl[2]."  and idkomponen='1'";
            $qUmr = mysql_query($sUmr);
            $rUmr = mysql_fetch_assoc($qUmr);
            $umr = $rUmr['jumlah'] / 25;
            if (0 == $umr) {
                exit('error: Please input basic salary');
            }

            $sAbsnGkByr = 'select distinct kodeabsen from '.$dbname.'.sdm_5absensi where kelompok=0';
            $qAbsnGkByr = mysql_query($sAbsnGkByr);
            while ($rAbnGkByr = mysql_fetch_assoc($qAbsnGkByr)) {
                $arrGkDbyr[$rAbnGkByr['kodeabsen']] = $rAbnGkByr['kodeabsen'];
            }
            $arrExc = ['C', 'L', 'MG'];
            if ('' != $arrExc[$_POST['absnId']]) {
                $upahHarian = $umr;
            } else {
                if ($arrGkDbyr[$_POST['absnId']]) {
                    $upahHarian = 0;
                } else {
                    if ('00:00' == $_POST['jamPlg']) {
                        $_POST['jmMulai'] = '00:00';
                    }

                    $jm1 = explode(':', $_POST['jmMulai']);
                    $jm2 = explode(':', $_POST['jamPlg']);
                    $dtTmbh = 0;
                    if ($jm2 < $jm1) {
                        $dtTmbh = 1;
                    }

                    $qwe = date('D', strtotime($isi));
                    $wktmsk = mktime((int) ($jm1[0]), (int) ($jm1[1]), (int) ($jm1[2]), (int) (substr($_POST['tglDt'], 3, 2)), (int) (substr($_POST['tglDt'], 0, 2)), substr($_POST['tglDt'], 6, 4));
                    $wktplg = mktime((int) ($jm2[0]), (int) ($jm2[1]), (int) ($jm2[2]), (int) (substr($_POST['tglDt'], 3, 2)), (int) (substr($_POST['tglDt'], 0, 2) + $dtTmbh), substr($_POST['tglDt'], 6, 4));
                    $slsihwaktu = $wktplg - $wktmsk;
                    $sisa = $slsihwaktu % 86400;
                    $jumlah_jam = floor($sisa / 3600);
                    if (7 <= $jumlah_jam) {
                        $upahHarian = $umr;
                    } else {
                        $upahHarian = $jumlah_jam / 7 * $umr;
                    }
                }
            }
        }

        echo $upahHarian;

        break;
}

?>