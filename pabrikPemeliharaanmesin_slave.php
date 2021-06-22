<?php
session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$namaKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$proses = $_POST['proses'];
$kdPabrik = $_POST['kdOrg'];
$kdStatsiun = $_POST['statId'];
$noTrans = $_POST['noTrans'];
$pbrkId = $_POST['pbrkId'];
$shft = $_POST['shft'];
$statid = $_POST['statid'];
$mesinId = $_POST['mesinId'];
$tgl = tanggalsystem($_POST['tgl']);
$jmAwal = substr(tanggalsystemd($_POST['jmAwal']), 0, 10);
$jmAkhir = substr(tanggalsystemd($_POST['jmAkhir']), 0, 10);
$kdbrg = $_POST['kdbrg'];
$satuan = $_POST['satuan'];
$jmlhMinta = $_POST['jmlhMinta'];
$ketrngn = $_POST['ketrngn'];
$userOnline = $_SESSION['standard']['userid'];
$kegiatan = $_POST['kgtn'];
$keterangan = $_POST['keterangan'];
$pbrikId = $_POST['kdrg'];
$jamMulai = $_POST['jamMulai'];
$mntMulai = $_POST['mntMulai'];
$jamSlsi = $_POST['jamSlsi'];
$mntSlsi = $_POST['mntSlsi'];
$jmAwal = $jmAwal.' '.$jamMulai.':'.$mntMulai;
$jmAkhir = $jmAkhir.' '.$jamSlsi.':'.$mntSlsi;
switch ($proses) {
    case 'GetStat':
        if ('' !== $kdPabrik) {
            $sOrg = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi where induk='".$kdPabrik."'";
            $qOrg = mysql_query($sOrg);
            $optStat .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                if ('' !== $statid) {
                    $optStat .= '<option value='.$rOrg['kodeorganisasi'].' '.(($rOrg['kodeorganisasi'] === $statid ? 'selected' : '')).'>'.$rOrg['namaorganisasi'].'</option>';
                } else {
                    $optStat .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
                }
            }
            $sShft = 'select shift from '.$dbname.".pabrik_5shift where kodeorg='".$kdPabrik."' and shift!='0'";
            $qShft = mysql_query($sShft);
            while ($rShft = mysql_fetch_assoc($qShft)) {
                if ('' !== $shft) {
                    $optShift .= '<option value='.$rShft['shift'].' '.(($rShft['shift'] === $shft ? 'selected' : '')).'>'.$rShft['shift'].'</option>';
                } else {
                    $optShift .= '<option value='.$rShft['shift'].'>'.$rShft['shift'].'</option>';
                }
            }
            echo $optStat.'###'.$optShift;
        } else {
            echo 'warning : Organization code is obligatory';
        }

        break;
    case 'GetMsn':
        $sOrg = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi where induk='".$kdStatsiun."'";
        $qOrg = mysql_query($sOrg);
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            if ('' !== $mesinId) {
                $optMsn .= '<option value='.$rOrg['kodeorganisasi'].' '.(($rOrg['kodeorganisasi'] === $mesinId ? 'selected' : '')).'>'.$rOrg['namaorganisasi'].'</option>';
            } else {
                $optMsn .= '<option value='.$rOrg['kodeorganisasi'].' >'.$rOrg['namaorganisasi'].'</option>';
            }
        }
        echo $optMsn;

        break;
    case 'CreateNo':
        $jmAwal = explode(' ', $jmAwal);
        $jmAkhir = explode(' ', $jmAkhir);
        if ($jmAkhir[0] < $jmAwal[0]) {
            echo 'warning: Start time must lower then end time';
            exit();
        }

        $tgl = date('Ymd');
        $bln = substr($tgl, 4, 2);
        $thn = substr($tgl, 0, 4);
        $notransaksi = '/'.$kdStatsiun.'/'.date('m').'/'.date('Y');
        $ql = 'select `notransaksi` from '.$dbname.".`pabrik_rawatmesinht` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
        $qr = mysql_query($ql);
        $rp = mysql_fetch_object($qr);
        $awal = substr($rp->notransaksi, 0, 4);
        $awal = (int) $awal;
        $cekbln = substr($rp->notransaksi, -7, 2);
        $cekthn = substr($rp->notransaksi, -12, 4);
        if ($bln !== $cekbln && $thn !== $cekthn) {
            $awal = 1;
        } else {
            ++$awal;
        }

        $counter = addZero($awal, 4);
        $notransaksi = $counter.'/'.$kdStatsiun.'/'.$bln.'/'.$thn;
        echo $notransaksi;

        break;
    case 'cekData':
        if ('' === $shft || '' === $statid || '' === $mesinId || '' === $tgl || '' === $kdbrg) {
            echo 'warning: Please complete the form';
            exit();
        }

        $sCek = 'select notransaksi from '.$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_row($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".pabrik_rawatmesinht (notransaksi, pabrik, tanggal, shift, statasiun, mesin, kegiatan, jammulai, jamselesai, updateby, keterangan) \r\n                        values ('".$noTrans."','".$pbrkId."','".$tgl."','".$shft."','".$statid."','".$mesinId."','".$kegiatan."','".$jmAwal."','".$jmAkhir."','".$userOnline."','".$keterangan."')";
            if (mysql_query($sIns)) {
                $sInd = 'insert into '.$dbname.".pabrik_rawatmesindt (notransaksi, kodebarang, satuan, jumlah, keterangan) values ('".$noTrans."','".$kdbrg."','".$satuan."','".$jmlhMinta."','".$ketrngn."')";
                if (mysql_query($sInd)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        $test = count($_POST['kdbrg']);
        echo $test;

        break;
    case 'saveHeader':
        if ('' === $shft || '' === $statid || '' === $mesinId || '' === $tgl) {
            echo 'warning: Please complete the form';
            exit();
        }

        $sCek = 'select notransaksi from '.$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_row($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".pabrik_rawatmesinht (notransaksi, pabrik, tanggal, shift, statasiun, mesin, kegiatan, jammulai, jamselesai, updateby, keterangan) \r\n                        values ('".$noTrans."','".$pbrkId."','".$tgl."','".$shft."','".$statid."','".$mesinId."','".$kegiatan."','".$jmAwal."','".$jmAkhir."','".$userOnline."','".$keterangan."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        break;
    case 'cari_barang':
        $txtcari = $_POST['txtcari'];
        $gudang = $_SESSION['empl']['kdgudang'];
        $str = 'select a.kodebarang,a.namabarang,a.satuan from '.$dbname.".log_5masterbarang a where a.namabarang like '%".$txtcari."%' or a.kodebarang like '%".$txtcari."'";
        $res = mysql_query($str);
        if (mysql_num_rows($res) < 1) {
            echo 'Error: '.$_SESSION['lang']['tidakditemukan'];
        } else {
            echo "\r\n        <fieldset>\r\n        <legend>".$_SESSION['lang']['result']."</legend>\r\n        <div style=\"width:450px; height:300px; overflow:auto;\">\r\n                <table class=sortable cellspacing=1 border=0>\r\n                    <thead>\r\n                            <tr class=rowheader>\r\n                                    <td>No</td>\r\n                                    <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                                    <td>".$_SESSION['lang']['namabarang']."</td>\r\n                                    <td>".$_SESSION['lang']['satuan']."</td>\r\n                                    <td>".$_SESSION['lang']['saldo']."</td>\r\n                            </tr>\r\n                    </thead>\r\n                    <tbody>";
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                $saldoqty = 0;
                $str1 = 'select saldoqty from '.$dbname.".log_5masterbarangdt where kodebarang='".$bar->kodebarang."'\r\n\t\t\t\t                and kodegudang='".$gudang."'";
                $res1 = mysql_query($str1);
                while ($bar1 = mysql_fetch_object($res1)) {
                    $saldoqty = $bar1->saldoqty;
                }
                $qtynotpostedin = 0;
                $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt\r\n                                b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$bar->kodebarang."' \r\n\t\t\t\t\t            and a.tipetransaksi<5\r\n\t\t\t\t\t            and a.kodegudang='".$gudang."'\r\n\t\t\t\t\t            and a.post=0\r\n\t\t\t\t\t            group by kodebarang";
                $res2 = mysql_query($str2);
                while ($bar2 = mysql_fetch_object($res2)) {
                    $qtynotpostedin = $bar2->jumlah;
                }
                if ('' === $qtynotpostedin) {
                    $qtynotpostedin = 0;
                }

                $qtynotposted = 0;
                $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt\r\n        b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$bar->kodebarang."' \r\n\t\tand a.tipetransaksi>4\r\n\t\tand a.kodegudang='".$gudang."'\r\n\t\tand a.post=0\r\n\t\tgroup by kodebarang";
                $res2 = mysql_query($str2);
                while ($bar2 = mysql_fetch_object($res2)) {
                    $qtynotposted = $bar2->jumlah;
                }
                if ('' === $qtynotposted) {
                    $qtynotposted = 0;
                }

                $saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;
                if (0 === $saldoqty) {
                    echo "<tr class=rowcontent>\r\n\t<td>".$no."</td>\r\n\t<td>".$bar->kodebarang."</td>\r\n\t<td>".$bar->namabarang."</td>\r\n\t<td>".$bar->satuan."</td>\r\n\t<td align=right>".number_format($saldoqty, 2, ',', '.')."</td>\r\n</tr>";
                } else {
                    echo "<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"throwThisRow('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."');\">\r\n    <td>".$no."</td>\r\n    <td>".$bar->kodebarang."</td>\r\n    <td>".$bar->namabarang."</td>\r\n    <td>".$bar->satuan."</td>\r\n    <td align=right>".number_format($saldoqty, 2, ',', '.')."</td>\r\n    </tr>";
                }
            }
            echo "\r\n                            </tbody>\r\n                            <tfoot></tfoot>\r\n                            </table></div></fieldset>";
        }

        break;
    case 'loadData':
        $limit = 25;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.pabrik_rawatmesinht  order by tanggal desc';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.'.pabrik_rawatmesinht  order by tanggal desc limit '.$offset.','.$limit.' ';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            ++$no;
            $dtJamMulai = explode(' ', $rlvhc['jammulai']);
            $jamMulai = explode(':', $dtJamMulai[1]);
            $dtJamSlsi = explode(' ', $rlvhc['jamselesai']);
            $jamSlsi = explode(':', $dtJamSlsi[1]);
            echo "\r\n\t\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t<td>".$rlvhc['notransaksi']."</td>\r\n\t\t\t\t\t<td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n\t\t\t\t\t<td>".$rlvhc['shift']."</td>\r\n\t\t\t\t\t<td>".$rlvhc['statasiun']."</td>\r\n\t\t\t\t\t<td>".$rlvhc['mesin']."</td>\r\n\t\t\t\t\t<td>".tanggalnormald($rlvhc['jammulai'])."</td>\r\n\t\t\t\t\t<td>".tanggalnormald($rlvhc['jamselesai'])."</td>\r\n\t\t\t\t\t<td>".$namaKar[$rlvhc['updateby']]."</td>\r\n\t\t\t\t\t<td>";
            if ($rlvhc['updateby'] === $userOnline) {
                echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['pabrik']."','".$rlvhc['shift']."','".$rlvhc['statasiun']."','".$rlvhc['mesin']."','".$rlvhc['kegiatan']."','".tanggalnormal($dtJamMulai[0])."','".tanggalnormal($dtJamSlsi[0])."','".$jamMulai[0]."','".$jamMulai[1]."','".$jamSlsi[0]."','".$jamSlsi[1]."','".$rlvhc['keterangan']."');\">\r\n\t\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."');\" >\r\n\t\t\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
            } else {
                echo "\r\n\t\t\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
            }
        }
        echo '</td></tr>';
        echo "\r\n                </tr><tr class=rowheader><td colspan=9 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";

        break;
    case 'cariTransaksi':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        if (isset($_POST['txtSearch'])) {
            $txt_search = $_POST['txtSearch'];
            $txt_tgl = tanggalsystem($_POST['txtTgl']);
            $txt_tgl_a = substr($txt_tgl, 0, 4);
            $txt_tgl_b = substr($txt_tgl, 4, 2);
            $txt_tgl_c = substr($txt_tgl, 6, 2);
            $txt_tgl = $txt_tgl_a.'-'.$txt_tgl_b.'-'.$txt_tgl_c;
        } else {
            $txt_search = '';
            $txt_tgl = '';
        }

        if ('' !== $txt_search) {
            $where = " notransaksi LIKE  '%".$txt_search."%'";
        } else {
            if ('' !== $txt_tgl) {
                $where .= " tanggal LIKE '".$txt_tgl."'";
            } else {
                if ('' !== $txt_tgl && '' !== $txt_search) {
                    $where .= " notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                }
            }
        }

        if ('' === $txt_search && '' === $txt_tgl) {
            $strx = 'select * from '.$dbname.".pabrik_rawatmesinht where kodeorg='".$lokasi."' ".$where.' order by notransaksi desc limit '.$offset.','.$limit.'';
            $sql = 'select count(*) jmlhrow from '.$dbname.".pabrik_rawatmesinht \twhere  kodeorg='".$lokasi."' ".$where.' order by notransaksi desc';
        } else {
            $strx = 'select * from '.$dbname.'.pabrik_rawatmesinht where '.$where." order by notransaksi desc \r\n                                limit ".$offset.','.$limit.'';
            $sql = 'select count(*) jmlhrow from '.$dbname.'.pabrik_rawatmesinht where  '.$where.' order by notransaksi desc';
        }

        $query = mysql_query($sql);
        while ($jsl = mysql_fetch_object($query)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        if ($res = mysql_query($strx)) {
            $numrows = mysql_num_rows($res);
            if ($numrows < 1) {
                echo '<tr class=rowcontent><td colspan=9>Not Found</td></tr>';
            } else {
                while ($rlvhc = mysql_fetch_assoc($res)) {
                    $dtJamMulai = explode(' ', $rlvhc['jammulai']);
                    $jamMulai = explode(':', $dtJamMulai[1]);
                    $dtJamSlsi = explode(' ', $rlvhc['jamselesai']);
                    $jamSlsi = explode(':', $dtJamSlsi[1]);
                    ++$no;
                    echo "\r\n                                                <tr class=rowcontent>\r\n                                                <td>".$no."</td>\r\n                                                <td>".$rlvhc['notransaksi']."</td>\r\n                                                <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                                                <td>".$rlvhc['shift']."</td>\r\n                                                <td>".$rlvhc['statasiun']."</td>\r\n                                                <td>".$rlvhc['mesin']."</td>\r\n                                                <td>".tanggalnormald($rlvhc['jammulai'])."</td>\r\n                                                <td>".tanggalnormald($rlvhc['jamselesai'])."</td>\r\n\t\t\t\t\t\t\t\t\t\t\t\t <td>".$namaKar[$rlvhc['updateby']].'</td>';
                    echo '<td>';
                    if ($rlvhc['updateby'] === $userOnline) {
                        echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['pabrik']."','".$rlvhc['shift']."','".$rlvhc['statasiun']."','".$rlvhc['mesin']."','".$rlvhc['kegiatan']."','".tanggalnormal($dtJamMulai[0])."','".tanggalnormal($dtJamSlsi[0])."','".$jamMulai[0]."','".$jamMulai[1]."','".$jamSlsi[0]."','".$jamSlsi[1]."','".$rlvhc['keterangan']."');\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."');\" >\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
                    } else {
                        echo "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
                    }
                }
                echo '</td></tr>';
                echo "\r\n                                                </tr><tr class=rowheader><td colspan=9 align=center>\r\n                                                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                                                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                                                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                                                </td>\r\n                                                </tr>";
            }
        } else {
            echo 'Gagal,'.mysql_error($conn);
        }

        break;
    case 'posting':
        $flag = 0;
        $sekarang = date('Y-m-d');
        $x = 'select kodejabatan from '.$dbname.".sdm_5jabatan\r\n\t\t\t\t\t\t\t\t  where alias like '%ka.%' or alias like '%kepala%' ";
        $y = mysql_query($x);
        while ($z = mysql_fetch_assoc($y)) {
            $pos = $z['kodejabatan'];
            echo $pos;
            if ($pos === $_SESSION['empl']['kodejabatan']) {
                $flag = 1;
            }
        }
        if (1 === $flag) {
            $i = 'update  '.$dbname.".pabrik_rawatmesinht set statPost=1,postingdate='".$sekarang."',postingby='".$_SESSION['standard']['userid']."' where notransaksi='".$noTrans."'";
            if (mysql_query($i)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }

            break;
        }

        exit("Error:Sory you can't posting this transaction");
    case 'deletData':
        $sDel = 'delete from '.$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'";
        if (mysql_query($sDel)) {
            $sdelDet = 'delete from '.$dbname.".pabrik_rawatmesindt where notransaksi='".$noTrans."'";
            mysql_query($sdelDet);
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'upDate':
        if ('' === $jmAkhir || '' === $jmAwal || '' === $tgl) {
            echo 'warning: Please complete the form';
            exit();
        }

        $sUp = 'update  '.$dbname.".pabrik_rawatmesinht set kegiatan='".$kegiatan."', jammulai='".$jmAwal."', jamselesai='".$jmAkhir."', tanggal='".$tgl."', keterangan='".$keterangan."' where notransaksi='".$noTrans."'";
        mysql_query($sUp);

        break;
    default:
        break;
}

?>