<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
('' == $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' == $_POST['tglijin'] ? ($tglijin = tanggalsystem($_GET['tglijin'])) : ($tglijin = tanggalsystem($_POST['tglijin'])));
('' == $_POST['krywnId'] ? ($krywnId = $_GET['krywnId']) : ($krywnId = $_POST['krywnId']));
$stat = $_POST['stat'];
$ket = $_POST['ket'];
$arrNmkary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan = [$_SESSION['lang']['diajukan'], $_SESSION['lang']['disetujui'], $_SESSION['lang']['ditolak']];
$where = " tanggal='".$tglijin."' and karyawanid='".$krywnId."'";
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arragama = getEnum($dbname, 'sdm_ijin', 'jenisijin');
$jnsCuti = $_POST['jnsCuti'];
$karyidCari = $_POST['karyidCari'];
$atasan = $_POST['atasan'];
switch ($proses) {
    case 'loadData':
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.sdm_ijin,datakaryawan where sdm_ijin.karyawanid=datakaryawan.karyawanid where datakaryawan.kodeorganisasi='.$_SESSION['empl']['kodeorganisasi'].'  order by `tanggal` desc';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = "select sdm_ijin.*,sdm_5absensi.keterangan as ketabs from sdm_ijin,datakaryawan,sdm_5absensi where  sdm_ijin.karyawanid=datakaryawan.karyawanid and sdm_5absensi.kodeabsen=sdm_ijin.tipeijin and datakaryawan.kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'  order by `tanggal` desc limit ".$offset.",".$limit." ";
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            if ($_SESSION['language']=='ID') {
                $dd = $rlvhc['jenisijin'];
                ++$no;
                $sSisa = 'select sisa from '.$dbname.".sdm_cutiht where karyawanid='".$rlvhc['karyawanid']."' \r\n                        and periodecuti='".$rlvhc['periodecuti']."'";
                $qSisa = mysql_query($sSisa);
                $rSisa = mysql_fetch_assoc($qSisa);
                echo "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>\r\n                <td>".$rlvhc['keperluan']."</td>\r\n                <td>".$rlvhc['ketabs']."</td>\r\n\t\t\t\t\r\n\t\t\t\t<td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>\r\n                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>\r\n                <td>".$rlvhc['darijam']."</td>\r\n                <td>".$rlvhc['sampaijam']."</td>\r\n\t\t\t\t\r\n\t\t\t\t\r\n             \r\n                \r\n\r\n                <td align=center>".$rlvhc['jumlahhari']."</td>\r\n                <td align=center>".$rSisa['sisa'].'</td>';
                if ($rlvhc['persetujuan1'] == $_SESSION['standard']['userid']) {
                    if ($rlvhc['stpersetujuan1']==0) {
                        echo "<td align=center> <button class=mybutton id=dtlForm onclick=appSetuju('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>\r\n                         <button class=mybutton id=dtlForm onclick=showAppTolak('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>\r\n                         <button class=mybutton id=dtlForm onclick=showAppForw('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>Forward</button></td>";
                    } else {
                        if ($rlvhc['stpersetujuan1']==2) {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        } else {
                            if ($rlvhc['stpersetujuan1']==1) {
                                echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                            } else {
                                if ($rlvhc['stpersetujuan1']==0) {
                                    echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                                }
                            }
                        }
                    }
                } else {
                    if ($rlvhc['stpersetujuan1']==1) {
                        echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                    } else {
                        if ($rlvhc['stpersetujuan1']==0) {
                            echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        }
                    }
                }

                if ($rlvhc['persetujuan2'] == $_SESSION['standard']['userid']) {
                    if ($rlvhc['stpersetujuan2']==0) {
                        echo "<td align=center> <button class=mybutton id=dtlForm onclick=appSetuju2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>\r\n                         <button class=mybutton id=dtlForm onclick=showAppTolak2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>\r\n                         <button class=mybutton id=dtlForm onclick=showAppForw2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>Forward</button></td>";
                    } else {
                        if ($rlvhc['stpersetujuan2']==2) {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        } else {
                            if ($rlvhc['stpersetujuan2']==1) {
                                echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                            } else {
                                if ($rlvhc['stpersetujuan2']==0) {
                                    echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                                }
                            }
                        }
                    }
                } else {
                    if ($rlvhc['stpersetujuan2']==1) {
                        echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                    } else {
                        if (0 == $rlvhc['stpersetujuan2']) {
                            echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        }
                    }
                }

                if ($rlvhc['hrd'] == $_SESSION['standard']['userid']) {
                    if ($rlvhc['stpersetujuanhrd']==0) {
                        echo "<td align=center><button class=mybutton id=dtlForm onclick=appSetujuHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>\r\n                         <button class=mybutton id=dtlForm onclick=showAppTolakHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak'].'</button></td>';
                    } else {
                        if ($rlvhc['stpersetujuan1']==2) {
                            echo '<td align=center>(Tunggu atasan)</td>';
                        } else {
                            if ($rlvhc['stpersetujuanhrd']==2) {
                                echo '<td align=center>('.$_SESSION['lang']['ditolak'].'</td>';
                            } else {
                                if ($rlvhc['stpersetujuanhrd']==1) {
                                    echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                                } else {
                                    if ($rlvhc['stpersetujuanhrd']==0) {
                                        echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($rlvhc['stpersetujuanhrd']==0) {
                        echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                    } else {
                        if ($rlvhc['stpersetujuanhrd']==1) {
                            echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        }
                    }
                }

                echo '<td align=center>'.$arrNmkary[$rlvhc['ganti']].'</td>';
                echo "<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)\"></td>";

            } else {
                switch ($rlvhc['jenisijin']) {
                    case 'TERLAMBAT':
                        $dd = 'Late for work';

                        break;
                    case 'KELUAR':
                        $dd = 'Out of Office';

                        break;
                    case 'PULANGAWAL':
                        $dd = 'Home early';

                        break;
                    case 'IJINLAIN':
                        $dd = 'Other purposes';

                        break;
                    case 'CUTI':
                        $dd = 'Leave';

                        break;
                    case 'MELAHIRKAN':
                        $dd = 'Maternity';

                        break;
                    default:
                        $dd = 'Wedding, Circumcision or Graduation';

                        break;
                }
            }

        }
        echo "\r\n                </tr><tr class=rowheader><td colspan=13 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";


        break;
    case 'cariData':
        if ('' != $karyidCari) {
            $cari .= " and karyawanid='".$karyidCari."'";
        }

        if ('' != $jnsCuti) {
            $cari .= " and jenisijin='".$jnsCuti."'";
        }

        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_ijin,datakaryawan where sdm_ijin.karyawanid=datakaryawan.karyawanid andkaryawanid!='' ".$cari.'  order by `tanggal` desc';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.".sdm_ijin,datakaryawan where sdm_ijin.karyawanid=datakaryawan.karyawanid and sdm_ijin.karyawanid!='' ".$cari.'  order by `tanggal` desc limit '.$offset.','.$limit.' ';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            if ('ID' == $_SESSION['language']) {
                $dd = $rlvhc['jenisijin'];
                ++$no;
                $sSisa = 'select sisa from '.$dbname.".sdm_cutiht where karyawanid='".$rlvhc['karyawanid']."' \r\n                        order by periodecuti desc limit 1";
                $qSisa = mysql_query($sSisa);
                $rSisa = mysql_fetch_assoc($qSisa);
                echo "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>\r\n                <td>".$rlvhc['keperluan']."</td>\r\n                <td>".$dd."</td>\r\n                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>\r\n                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>\r\n                <td>".$rlvhc['darijam']."</td>\r\n                <td>".$rlvhc['sampaijam']."</td>\r\n                <td align=center>".$rlvhc['jumlahhari']."</td>\r\n                <td align=center>".$rSisa['sisa'].'</td>';
                if ($rlvhc['persetujuan1'] == $_SESSION['standard']['userid']) {
                    if (0 == $rlvhc['stpersetujuan1']) {
                        echo "<td align=center>\r\n                          <button class=mybutton id=dtlForm onclick=appSetuju('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>\r\n                          <button class=mybutton id=dtlForm onclick=showAppTolak('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>\r\n                          <button class=mybutton id=dtlForm onclick=showAppForw('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>Forward</button></td>";
                    } else {
                        if (2 == $rlvhc['stpersetujuan1']) {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                        }
                    }
                } else {
                    if (1 == $rlvhc['stpersetujuan1']) {
                        echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                    } else {
                        if (0 == $rlvhc['stpersetujuan1']) {
                            echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        }
                    }
                }

                if ($rlvhc['persetujuan2'] == $_SESSION['standard']['userid']) {
                    if (0 == $rlvhc['stpersetujuan2']) {
                        echo "<td align=center>\r\n                          <button class=mybutton id=dtlForm2 onclick=appSetuju2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>\r\n                          <button class=mybutton id=dtlForm2 onclick=showAppTolak2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>\r\n                          <button class=mybutton id=dtlForm2 onclick=showAppForw2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>Forward</button></td>";
                    } else {
                        if (2 == $rlvhc['stpersetujuan2']) {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                        }
                    }
                } else {
                    if (1 == $rlvhc['stpersetujuan2']) {
                        echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                    } else {
                        if (0 == $rlvhc['stpersetujuan2']) {
                            echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        }
                    }
                }

                if ($rlvhc['hrd'] == $_SESSION['standard']['userid']) {
                    if (0 == $rlvhc['stpersetujuanhrd']) {
                        echo "<td align=center><button class=mybutton id=dtlForm onclick=appSetujuHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>\r\n                         <button class=mybutton id=dtlForm onclick=showAppTolakHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak'].'</button></td>';
                    } else {
                        if (2 == $rlvhc['stpersetujuan1']) {
                            echo '<td align=center>(Tunggu atasan)</td>';
                        } else {
                            if (2 == $rlvhc['stpersetujuanhrd']) {
                                echo '<td align=center>('.$_SESSION['lang']['ditolak'].'</td>';
                            } else {
                                echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                            }
                        }
                    }
                } else {
                    if (1 == $rlvhc['stpersetujuanhrd']) {
                        echo '<td align=center>'.$_SESSION['lang']['disetujui'].'</td>';
                    } else {
                        if (0 == $rlvhc['stpersetujuanhrd']) {
                            echo '<td align=center>'.$_SESSION['lang']['wait_approval'].'</td>';
                        } else {
                            echo '<td align=center>'.$_SESSION['lang']['ditolak'].'</td>';
                        }
                    }
                }

                echo '<td align=center>'.$arrNmkary[$rlvhc['ganti']].'</td>';
                echo "<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)\"></td>";
            } else {
                switch ($rlvhc['jenisijin']) {
                    case 'TERLAMBAT':
                        $dd = 'Late for work';

                        break;
                    case 'KELUAR':
                        $dd = 'Out of Office';

                        break;
                    case 'PULANGAWAL':
                        $dd = 'Home early';

                        break;
                    case 'IJINLAIN':
                        $dd = 'Other purposes';

                        break;
                    case 'CUTI':
                        $dd = 'Leave';

                        break;
                    case 'MELAHIRKAN':
                        $dd = 'Maternity';

                        break;
                    default:
                        $dd = 'Wedding, Circumcision or Graduation';

                        break;
                }
            }
        }
        echo "\r\n                </tr><tr class=rowheader><td colspan=13 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";

        break;
    case 'appSetuju':
        $sket = 'select distinct jenisijin,stpersetujuan1,persetujuan1,hrd,tanggal,stpersetujuan2,persetujuan2 from '.$dbname.'.sdm_ijin where '.$where.'';
        $qKet = mysql_query($sket);
        $rKet = mysql_fetch_assoc($qKet);
        if (1 == $stat) {
            $ket = 'permintaaan '.$arrNmkary[$krywnId].' '.$arrKeputusan[$stat].'';
        }

        $sUpdate = 'update '.$dbname.".sdm_ijin  set stpersetujuan1='".$stat."',komenst1='".$ket."' where ".$where.'';
        if (mysql_query($sUpdate)) {
            if (1 == $stat) {
                $to = getUserEmail($rKet['persetujuan2']);
                $namakaryawan = $arrNmkary[$krywnId];
                $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
                $body = "<html>\r\n\t\t\t\t\t\t\t\t\t <head>\r\n\t\t\t\t\t\t\t\t\t <body>\r\n\t\t\t\t\t\t\t\t\t   <dd>Dengan Hormat,</dd><br>\r\n\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t   Permintaan persetujuan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal']).' karyawan a/n  '.$namakaryawan.' telah '.$arrKeputusan[$stat].". \r\n\t\t\t\t\t\t\t\t\t   Oleh atasan ybs. Selanjutnya, mohon untuk memberikan persetujuan lanjutan. Untuk melihat lebih detail, silahkan ikuti link dibawah.\r\n\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t   Regards,<br>\r\n\t\t\t\t\t\t\t\t\t   eAgro Plantation Management Software.\r\n\t\t\t\t\t\t\t\t\t </body>\r\n\t\t\t\t\t\t\t\t\t </head>\r\n\t\t\t\t\t\t\t\t   </html>\r\n\t\t\t\t\t\t\t\t   ";
                //$kirim = kirimEmailWindows($to, $subject, $body);
            } else {
                $x = 'update '.$dbname.".sdm_ijin set stpersetujuan2='2',stpersetujuanhrd='2' where ".$where.'';
                if (mysql_query($x)) {
                    $to = getUserEmail($krywnId);
                    $namakaryawan = $arrNmkary[$krywnId];
                    $subject = '[Notifikasi]Penolakan Ijin Keluar Kantor a/n '.$namakaryawan;
                    $body = "<html>\r\n\t\t\t\t\t\t\t\t\t\t\t <head>\r\n\t\t\t\t\t\t\t\t\t\t\t <body>\r\n\t\t\t\t\t\t\t\t\t\t\t   <dd>Dengan Hormat,</dd><br>\r\n\t\t\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t\t\t   Permintaan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal']).' karyawan a/n  '.$namakaryawan." telah ditolak Oleh atasan ybs. \r\n\t\t\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t\t\t\t   Regards,<br>\r\n\t\t\t\t\t\t\t\t\t\t\t   eAgro Plantation Management Software.\r\n\t\t\t\t\t\t\t\t\t\t\t </body>\r\n\t\t\t\t\t\t\t\t\t\t\t </head>\r\n\t\t\t\t\t\t\t\t\t\t   </html>\r\n\t\t\t\t\t\t\t\t\t\t   ";
                    //$kirim = kirimEmailWindows($to, $subject, $body);
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            }
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'appSetuju2':
        $sket = 'select distinct jenisijin,hrd,tanggal,stpersetujuan2,persetujuan2,stpersetujuan1,persetujuan1 from '.$dbname.'.sdm_ijin where '.$where.'';
        $qKet = mysql_query($sket);
        $rKet = mysql_fetch_assoc($qKet);
        if ('2' == $rKet['stpersetujuan1']) {
            exit("Error:Sorry you can't approve this document,  because the first approver has been rejected");
        }

        if (1 == $stat) {
            $ket = 'permintaaan '.$arrNmkary[$krywnId].' '.$arrKeputusan[$stat].'';
        }

        $sUpdate = 'update '.$dbname.".sdm_ijin  set stpersetujuan2='".$stat."',komenst2='".$ket."' where ".$where.'';
        if (mysql_query($sUpdate)) {
            $to = getUserEmail($rKet['hrd']);
            $namakaryawan = $arrNmkary[$krywnId];
            $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
            $body = "<html>\r\n                                             <head>\r\n                                             <body>\r\n                                               <dd>Dengan Hormat,</dd><br>\r\n                                               <br>\r\n                                               Permintaan persetujuan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal']).' karyawan a/n  '.$namakaryawan.' telah '.$arrKeputusan[$stat].". \r\n                                               Oleh atasan ybs. Selanjutnya, mohon persetujuan dari HRD. Untuk melihat lebih detail, silahkan ikuti link dibawah.\r\n                                               <br>\r\n                                               <br>\r\n                                               <br>\r\n                                               Regards,<br>\r\n                                               eAgro Plantation Management Software.\r\n                                             </body>\r\n                                             </head>\r\n                                           </html>\r\n                                           ";
            //$kirim = kirimEmailWindows($to, $subject, $body);
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'appSetujuHRD':
        $sket = 'select distinct darijam,sampaijam,jumlahhari,jenisijin,stpersetujuanhrd,hrd,tanggal,periodecuti,tipeijin from '.$dbname.'.sdm_ijin where '.$where.'';

        $qKet = mysql_query($sket);
        $rKet = mysql_fetch_assoc($qKet);
        $sabs = 'select keterangan from '.$dbname.'.sdm_5absensi where kodeabsen= '.$rKet['tipeijin'].'';
        $qAbs = mysql_query($sabs);
        $rAbs = mysql_fetch_assoc($qAbs);
        if (1 == $stat) {
            $ket = 'permintaaan '.$arrNmkary[$krywnId].' '.$arrKeputusan[$stat].'';
            $stru = 'select lokasitugas from '.$dbname.'.datakaryawan where karyawanid='.$krywnId;
            $resu = mysql_query($stru);
            $kodeorg = '';
            while ($baru = mysql_fetch_object($resu)) {
                $kodeorg = $baru->lokasitugas;
            }
            if ('' == $kodeorg) {
                exit('Error: Karywan tidak memiliki loaksi tugas');
            }

           // if ('CUTI' == $rKet['tipeijin'] || 'MELAHIRKAN' == $rKet['tipeijin'] || 'KAWIN/SUNATAN/WISUDA' == $rKet['tipeijin']) {
                $str = 'insert into '.$dbname.".sdm_cutidt \r\n                                (kodeorg,karyawanid,periodecuti,daritanggal,\r\n                                    sampaitanggal,jumlahcuti,keterangan\r\n                                    )\r\n                                values('".$kodeorg."',".$krywnId.",\r\n                                    '".$rKet['periodecuti']."','".substr($rKet['darijam'], 0, 10)."','".substr($rKet['sampaijam'], 0, 10)."',".$rKet['jumlahhari'].",'".$rAbs['keterangan']."'\r\n                                    )";
                if (mysql_query($str)) {
                    $strx = "select sum(jumlahcuti) as diambil from sdm_cutidt where kodeorg='".$kodeorg."' and keterangan = '".$rAbs['keterangan']."' and karyawanid='".$krywnId."'  and periodecuti='".$rKet['periodecuti']."'";
                    $diambil = 0;
                    $resx = mysql_query($strx);
                    while ($barx = mysql_fetch_object($resx)) {
                        $diambil = $barx->diambil;
                    }
                   // if ('CUTI' == $rKet['jenisijin'] && '' == $diambil) {
                     //   $diambil = 0;
                    //}

                    $strup = "update sdm_cutiht set diambil='".$diambil."',sisa=(hakcuti-".$rKet['jumlahhari'].")  where kodeorg='".$kodeorg."' and karyawanid='".$krywnId."' and periodecuti='".$rKet['periodecuti']."'";
                  //  if ('CUTI' == $rKet['jenisijin']) {
                        mysql_query($strup);
                        echo $strup;
                    //}
                } else {
                    echo mysql_error($conn);
                    exit('Error: Update table cuti');
                }
            //}

            $sUpdate = 'update '.$dbname.".sdm_ijin  set stpersetujuanhrd='".$stat."',komenst2='".$ket."' where ".$where.'';
            if (mysql_query($sUpdate)) {
                $to = getUserEmail($rKet['hrd']);
                $namakaryawan = getNamaKaryawan($krywnId);
                $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
                $body = "<html>\r\n                                             <head>\r\n                                             <body>\r\n                                               <dd>Dengan Hormat,</dd><br>\r\n                                               <br>\r\n                                               Permintaan persetujuan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal']).' karyawan a/n  '.$namakaryawan.' telah '.$arrKeputusan[$stat].". \r\n                                                   Untuk melihat lebih detail, silahkan ikuti link dibawah.\r\n                                               <br>\r\n                                               <br>\r\n                                               <br>\r\n                                               Regards,<br>\r\n                                               eAgro Plantation Management Software.\r\n                                             </body>\r\n                                             </head>\r\n                                           </html>\r\n                                           ";
                //$kirim = kirimEmailWindows($to, $subject, $body);
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        break;
    case 'prevPdf':
         class PDF extends FPDF
         {
             public function Header()
             {
                 if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
                     $path = 'images/SSP_logo.jpg';
                 } else {
                     if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                         $path = 'images/MI_logo.jpg';
                     } else {
                         if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                             $path = 'images/HS_logo.jpg';
                         } else {
                             if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                                 $path = 'images/BM_logo.jpg';
                             }
                         }
                     }
                 }

                 //$this->Image($path, 15, 2, 40);
                 $this->SetFont('Arial', 'B', 10);
                 $this->SetFillColor(255, 255, 255);
                 $this->SetY(22);
                 $this->Cell(60, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'C');
                 $this->SetFont('Arial', '', 15);
                 $this->Cell(190, 5, '', 0, 1, 'C');
                 $this->SetFont('Arial', '', 6);
                 $this->SetY(30);
                 $this->SetX(163);
                 $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
                 $this->Line(10, 32, 200, 32);
             }

             public function Footer()
             {
                 $this->SetY(-15);
                 $this->SetFont('Arial', 'I', 8);
                 $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
             }
         }

         $str = 'select sdm_ijin.*, sdm_5absensi.keterangan as ketabs from '.$dbname.'.sdm_ijin,sdm_5absensi where sdm_ijin.tipeijin=sdm_5absensi.kodeabsen and '.$where.'';
         $res = mysql_query($str);
         while ($bar = mysql_fetch_object($res)) {
             $ganti = $bar->ganti;
             $jabatan = '';
             $namakaryawan = '';
             $bagian = '';
             $karyawanid = '';
             $strc = "select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan from ".$dbname.'.datakaryawan a left join  '.$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where a.karyawanid=".$bar->karyawanid;
             $resc = mysql_query($strc);
             while ($barc = mysql_fetch_object($resc)) {
                 $jabatan = $barc->namajabatan;
                 $namakaryawan = $barc->namakaryawan;
                 $bagian = $barc->bagian;
                 $karyawanid = $barc->karyawanid;
             }
             $perstatus = $bar->stpersetujuan1;
             $tgl = tanggalnormal($bar->tanggal);
             $kperluan = $bar->keperluan;
             $persetujuan = $bar->persetujuan1;
             $persetujuan2 = $bar->persetujuan2;
             $jns = $bar->jenisijin;
             $jmDr = $bar->darijam;
             $jmSmp = $bar->sampaijam;
             $koments = $bar->komenst1;
             $ket = $bar->keterangan;
             $periode = $bar->periodecuti;
             $sthrd = $bar->stpersetujuanhrd;
             $hk = $bar->jumlahhari;
             $ketabs = $bar->ketabs;
             $hrd = $bar->hrd;
             $koments2 = $bar->komenst2;
             if ('ID' == $_SESSION['language']) {
                 $dd = $jns;
             } else {
                 switch ($jns) {
                     case 'TERLAMBAT':
                         $dd = 'Late for work';

                         break;
                     case 'KELUAR':
                         $dd = 'Out of Office';

                         break;
                     case 'PULANGAWAL':
                         $dd = 'Home early';

                         break;
                     case 'IJINLAIN':
                         $dd = 'Other purposes';

                         break;
                     case 'CUTI':
                         $dd = 'Leave';

                         break;
                     case 'MELAHIRKAN':
                         $dd = 'Maternity';

                         break;
                     default:
                         $dd = 'Wedding, Circumcision or Graduation';

                         break;
                 }
                 $perjabatan = '';
                 $perbagian = '';
                 $pernama = '';
                 $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where karyawanid=".$persetujuan;
                 $resf = mysql_query($strf);
                 while ($barf = mysql_fetch_object($resf)) {
                     $perjabatan = $barf->namajabatan;
                     $perbagian = $barf->bagian;
                     $pernama = $barf->namakaryawan;
                 }
                 $perjabatan2 = '';
                 $perbagian2 = '';
                 $pernama2 = '';
                 $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where karyawanid=".$persetujuan2;
                 $resf = mysql_query($strf);
                 while ($barf = mysql_fetch_object($resf)) {
                     $perjabatan2 = $barf->namajabatan;
                     $perbagian2 = $barf->bagian;
                     $pernama2 = $barf->namakaryawan;
                 }
                 $perjabatanhrd = '';
                 $perbagianhrd = '';
                 $pernamahrd = '';
                 $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where karyawanid=".$hrd;
                 $resf = mysql_query($strf);
                 while ($barf = mysql_fetch_object($resf)) {
                     $perjabatanhrd = $barf->namajabatan;
                     $perbagianhrd = $barf->bagian;
                     $pernamahrd = $barf->namakaryawan;
                 }
             }
         }
         $pdf = new PDF('P', 'mm', 'A4');
         $pdf->SetFont('Arial', 'B', 14);
         $pdf->AddPage();
         $pdf->SetY(40);
         $pdf->SetX(20);
         $pdf->SetFillColor(255, 255, 255);
         $pdf->Cell(175, 5, strtoupper($_SESSION['lang']['ijin'].'/'.$_SESSION['lang']['cuti']), 0, 1, 'C');
         $pdf->SetX(20);
         $pdf->SetFont('Arial', '', 8);
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['tanggal'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$tgl, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['nokaryawan'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$karyawanid, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$namakaryawan, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['bagian'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$bagian, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['functionname'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$jabatan, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['keperluan'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$kperluan, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['jenisijin'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$ketabs, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['keterangan'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$ket, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['pengabdian'].' '.$_SESSION['lang']['tahun'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$periode, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['dari'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$jmDr, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['tglcutisampai'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$jmSmp, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['jumlah'].' '.$_SESSION['lang']['hari'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$hk.' '.$_SESSION['lang']['hari'], 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, 'Karyawan Pengganti', 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$arrNmkary[$ganti], 0, 1, 'L');
         $pdf->Ln();
         $pdf->SetX(20);
         $pdf->SetFont('Arial', 'B', 8);
         $pdf->Cell(172, 5, strtoupper($_SESSION['lang']['approval_status']), 0, 1, 'L');
         $pdf->SetX(30);
		 /*
         $pdf->Cell(30, 5, strtoupper($_SESSION['lang']['bagian']), 1, 0, 'C');
         $pdf->Cell(50, 5, strtoupper($_SESSION['lang']['namakaryawan']), 1, 0, 'C');
         $pdf->Cell(40, 5, strtoupper($_SESSION['lang']['functionname']), 1, 0, 'C');
         $pdf->Cell(37, 5, strtoupper($_SESSION['lang']['keputusan']), 1, 1, 'C');
		 $pdf->SetFont('Arial', '', 8);
         $pdf->SetX(30);
         $pdf->Cell(30, 5, $perbagian, 1, 0, 'L');
         $pdf->Cell(50, 5, $pernama, 1, 0, 'L');
         $pdf->Cell(40, 5, $perjabatan, 1, 0, 'L');
         $pdf->Cell(37, 5, $arrKeputusan[$perstatus], 1, 1, 'L');
         $pdf->SetX(30);
         $pdf->Cell(30, 5, $perbagian2, 1, 0, 'L');
         $pdf->Cell(50, 5, $pernama2, 1, 0, 'L');
         $pdf->Cell(40, 5, $perjabatan2, 1, 0, 'L');
         $pdf->Cell(37, 5, $arrKeputusan[$perstatus2], 1, 1, 'L');
         $pdf->SetX(30);
         $pdf->Cell(30, 5, $perbagianhrd, 1, 0, 'L');
         $pdf->Cell(50, 5, $pernamahrd, 1, 0, 'L');
         $pdf->Cell(40, 5, $perjabatanhrd, 1, 0, 'L');
         $pdf->Cell(37, 5, $arrKeputusan[$sthrd], 1, 1, 'L');
         */

         $pdf->Ln();
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['keputusan'].' '.$_SESSION['lang']['atasan'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$koments, 0, 1, 'L');
         $pdf->SetX(20);
         $pdf->Cell(30, 5, $_SESSION['lang']['keputusan'].' '.$_SESSION['lang']['hrd'], 0, 0, 'L');
         $pdf->Cell(50, 5, ' : '.$koments2, 0, 1, 'L');
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Output();
         echo $strc;
         break;
    case 'getExcel':
        $tab .= " \r\n                <table class=sortable cellspacing=1 border=1 width=80%>\r\n                <thead>\r\n                <tr  >\r\n                <td align=center bgcolor='#DFDFDF'>No.</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tanggal']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['nama']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['keperluan']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['jenisijin']."</td>  \r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['persetujuan']."</td>    \r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['approval_status']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['dari'].'  '.$_SESSION['lang']['jam']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tglcutisampai'].'  '.$_SESSION['lang']['jam']."</td>\r\n                </tr>  \r\n                </thead><tbody>";
        $slvhc = 'select * from '.$dbname.'.sdm_ijin   order by `tanggal` desc ';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            if ('ID' == $_SESSION['language']) {
                $dd = $rlvhc['jenisijin'];
                ++$no;
                $tab .= "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rlvhc['tanggal']."</td>\r\n                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>\r\n                <td>".$rlvhc['keperluan']."</td>\r\n                <td>".$dd."</td>\r\n                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>\r\n                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>\r\n                <td>".$rlvhc['darijam']."</td>\r\n                <td>".$rlvhc['sampaijam'].'</td>';
            } else {
                switch ($rlvhc['jenisijin']) {
                    case 'TERLAMBAT':
                        $dd = 'Late for work';

                        break;
                    case 'KELUAR':
                        $dd = 'Out of Office';

                        break;
                    case 'PULANGAWAL':
                        $dd = 'Home early';

                        break;
                    case 'IJINLAIN':
                        $dd = 'Other purposes';

                        break;
                    case 'CUTI':
                        $dd = 'Leave';

                        break;
                    case 'MELAHIRKAN':
                        $dd = 'Maternity';

                        break;
                    default:
                        $dd = 'Wedding, Circumcision or Graduation';

                        break;
                }
            }
        }
        $tab .= '</tbody></table>';
        $nop_ = 'listizinkeluarkantor';
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
            closedir($handle);
        }

        break;
    case 'formForward':
        $optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sKary = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and kodegolongan>='4B' and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
        $qKary = mysql_query($sKary) || exit(mysql_error($sKary));
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $optKary .= "<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan'].'</option>';
        }
        $tab .= '<fieldset><legend>'.$arrNmkary[$krywnId].', '.$_SESSION['lang']['tanggal'].' : '.tanggalnormal($tglijin).'</legend><table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td>'.$_SESSION['lang']['namakaryawan'].'</td><td><select id=karywanId>'.$optKary.'</select></td></tr>';
        $tab .= '<tr><td colspan=2><button class=mybutton id=dtlForm onclick=AppForw()>Forward</button></td></tr></table>';
        $tab .= "</table></fieldset><input type='hidden' id=karyaid value=".$krywnId.' /><input type=hidden id=tglIjin value='.tanggalnormal($tglijin).'/>';
        echo $tab;

        break;
    case 'forwardData':
        $sup = 'update '.$dbname.".sdm_ijin set persetujuan1='".$atasan."' where ".$where;
        if (mysql_query($sup)) {
            $sKar = 'select distinct * from '.$dbname.'.sdm_ijin where '.$where;
            $qKar = mysql_query($sKar);
            $rKar = mysql_fetch_assoc($qKar);
            $strf = 'select sisa from '.$dbname.'.sdm_cutiht where karyawanid='.$krywnId." \r\n                        and periodecuti=".$rKar['periodecuti'];
            $res = mysql_query($strf);
            $sisa = '';
            while ($barf = mysql_fetch_object($res)) {
                $sisa = $barf->sisa;
            }
            if ('' == $sisa) {
                $sisa = 0;
            }

            $to = getUserEmail($atasan);
            $namakaryawan = getNamaKaryawan($krywnId);
            $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
            $body = "<html>\r\n                    <head>\r\n                    <body>\r\n                    <dd>Dengan Hormat,</dd><br>\r\n                    <br>\r\n                    Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Ijin/'.$rKar['jenisijin'].' ('.$rKar['keperluan'].")\r\n                    kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                    <br>\r\n                    <br>\r\n                    Note: Sisa cuti ybs periode ".$rKar['periodecuti'].':'.$sisa." Hari\r\n                    <br>\r\n                    <br>\r\n                    Regards,<br>\r\n                    eAgro Plantation Management Software.\r\n                    </body>\r\n                    </head>\r\n                    </html>\r\n                    ";
            //$kirim = kirimEmailWindows($to, $subject, $body);
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'formForward2':
        $optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sKary = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and kodegolongan>='4B' and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
        $qKary = mysql_query($sKary) || exit(mysql_error($sKary));
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $optKary .= "<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan'].'</option>';
        }
        $tab .= '<fieldset><legend>'.$arrNmkary[$krywnId].', '.$_SESSION['lang']['tanggal'].' : '.tanggalnormal($tglijin).'</legend><table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td>'.$_SESSION['lang']['namakaryawan'].'</td><td><select id=karywanId>'.$optKary.'</select></td></tr>';
        $tab .= '<tr><td colspan=2><button class=mybutton id=dtlForm onclick=AppForw2()>Forward</button></td></tr></table>';
        $tab .= "</table></fieldset><input type='hidden' id=karyaid value=".$krywnId.' /><input type=hidden id=tglIjin value='.tanggalnormal($tglijin).'/>';
        echo $tab;

        break;
    case 'forwardData2':
         $sup = 'update '.$dbname.".sdm_ijin set persetujuan2='".$atasan."' where ".$where;
         if (mysql_query($sup)) {
             $sKar = 'select distinct * from '.$dbname.'.sdm_ijin where '.$where;
             $qKar = mysql_query($sKar);
             $rKar = mysql_fetch_assoc($qKar);
             $strf = 'select sisa from '.$dbname.'.sdm_cutiht where karyawanid='.$krywnId." \r\n                        and periodecuti=".$rKar['periodecuti'];
             $res = mysql_query($strf);
             $sisa = '';
             while ($barf = mysql_fetch_object($res)) {
                 $sisa = $barf->sisa;
             }
             if ('' == $sisa) {
                 $sisa = 0;
             }

             $to = getUserEmail($atasan);
             $namakaryawan = getNamaKaryawan($krywnId);
             $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
             $body = "<html>\r\n                    <head>\r\n                    <body>\r\n                    <dd>Dengan Hormat,</dd><br>\r\n                    <br>\r\n                    Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Ijin/'.$rKar['jenisijin'].' ('.$rKar['keperluan'].")\r\n                    kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                    <br>\r\n                    <br>\r\n                    Note: Sisa cuti ybs periode ".$rKar['periodecuti'].':'.$sisa." Hari\r\n                    <br>\r\n                    <br>\r\n                    Regards,<br>\r\n                    eAgro Plantation Management Software.\r\n                    </body>\r\n                    </head>\r\n                    </html>\r\n                    ";
             //$kirim = kirimEmailWindows($to, $subject, $body);
         } else {
             echo 'DB Error : '.mysql_error($conn);
         }

         break;
    default:
        break;
}

?>