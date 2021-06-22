<?php

//session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

$ganti = $_POST['ganti'];
$proses = $_POST['proses'];
$tglijin = tanggalsystem($_POST['tglijin']);
$jnsIjin = $_POST['jnsIjin'];
$jamDr = $_POST['jamDr'];
$jamSmp = $_POST['jamSmp'];
$keperluan = $_POST['keperluan'];
$ket = $_POST['ket'];
$atasan = $_POST['atasan'];
$atasan2 = $_POST['atasan2'];
$tglAwal = explode('-', $_POST['tglAwal']);
$tgl1 = $tglAwal[2].'-'.$tglAwal[1].'-'.$tglAwal[0];
$tglEnd = explode('-', $_POST['tglEnd']);
$tgl2 = $tglEnd[2].'-'.$tglEnd[1].'-'.$tglEnd[0];
$jamDr1 = $tgl1.' '.$jamDr;
$jamSmp1 = $tgl2.' '.$jamSmp;
$arrNmkary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan = [$_SESSION['lang']['diajukan'], $_SESSION['lang']['disetujui'], $_SESSION['lang']['ditolak']];
$where = " tanggal='".$tglijin."' and karyawanid='".$_SESSION['standard']['userid']."'";
$atsSblm = $_POST['atsSblm'];
$hk = $_POST['jumlahhk'];
$hrd = $_POST['hrd'];
$periodec = $_POST['periodec'];
if (($proses=='update' || $proses=='insert') && $jnsIjin=='CUTI') {
    $strf = 'select sisa from '.$dbname.'.sdm_cutiht where karyawanid='.$_SESSION['standard']['userid']." \r\n                    and periodecuti=".$periodec;
    $res = mysql_query($strf);
    $sisa = '';
    while ($barf = mysql_fetch_object($res)) {
        $sisa = $barf->sisa;
    }
    if ($sisa == '') {
        $sisa = 0;
    }

    $strfx = 'select max(periodecuti) as periodecuti from '.$dbname.'.sdm_cutiht where karyawanid='.$_SESSION['standard']['userid'];
    $resx = mysql_query($strfx);
    while ($barx = mysql_fetch_object($resx)) {
        $lastp = $barx->periodecuti;
    }
    $zz = substr($tgl1, 0, 4);
    if ($lastp < $zz && $lastp != '') {
        $str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas from '.$dbname.".datakaryawan\r\n\t       where  karyawanid='".$_SESSION['standard']['userid']."'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $x = readTextFile('config/jumlahcuti.lst');
            if ((int)$x > 0) {
                $hakcuti = $x;
            } else {
                $hakcuti = 12;
            }

            if ($bar1->tipekaryawan == 5 && 'HO' != substr($bar1->lokasitugas, 2, 2)) {
                $hakcuti = 12;
            } else {
                if ($bar1->tipekaryawan != 5 && 'HO' != substr($bar1->lokasitugas, 2, 2)) {
                    $hakcuti = 12;
                }
            }

            $sisa = $hakcuti;
            if ($zz <= substr($bar1->tanggalmasuk, 0, 4)) {
                continue;
            }

            $tgl = substr(str_replace('-', '', $bar1->tanggalmasuk), 4, 4);
            $dari = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), $zz);
            $dari = date('Ymd', $dari);
            $sampai = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), $zz + 1);
            $sampai = date('Ymd', $sampai);
            $d = substr(str_replace('-', '', $bar1->tanggalmasuk), 0, 4);
            $str = 'select sisa from '.$dbname.'.sdm_cutiht where karyawanid='.$bar1->karyawanid." \r\n                                               and periodecuti>".($periodec - 2).' order by periodecuti desc limit 1';
            $resx = mysql_query($str);
            $sisalalu = 0;
            while ($barx = mysql_fetch_object($resx)) {
                $sisalalu = $barx->sisa;
            }
            $str = 'select * from '.$dbname.'.sdm_cutiht where karyawanid='.$bar1->karyawanid." \r\n                                               and periodecuti=".$periodec.' order by periodecuti desc limit 1';
            $resy = mysql_query($str);
            if (mysql_num_rows($resy) > 0) {
            } else {
                $saldo = $hakcuti;
                $strx = 'select sum(jumlahcuti) as diambil from '.$dbname.".sdm_cutidt\r\n                                                            where karyawanid=".$bar1->karyawanid."\r\n                                                             and  daritanggal >=".$dari.' and daritanggal<='.$sampai;
                $diambil = 0;
                $resx = mysql_query($strx);
                while ($barx = mysql_fetch_object($resx)) {
                    $diambil = $barx->diambil;
                    if ('' == $diambil) {
                        $diambil = 0;
                    }
                }
                $saldo = $saldo - $diambil;
                $sisa = $saldo;
                $str = 'insert into '.$dbname.".sdm_cutiht(kodeorg, karyawanid, periodecuti, keterangan, dari, sampai, hakcuti, diambil, sisa)\r\n                                                   values('".$bar1->lokasitugas."',".$bar1->karyawanid.','.$periodec.",'',".$dari.','.$sampai.','.$hakcuti.',0,'.$saldo.')';
                mysql_query($str);
            }
        }
    }
}

switch ($proses) {
    case 'insert':
        if ($tglijin == '' || $jnsIjin == '' || $jamDr1 == '' || $jamSmp1 == '' || $keperluan == '' || $atasan == '' || $ganti == '' || $hrd == '' ) {
            echo 'warning:Please Complete The Form';
            exit();
        }
		$karyawanId = $_POST['karyawanid'];
        $wktu = '0000-00-00 00:00:00';
        $sCek = 'select tanggal from '.$dbname.'.sdm_ijin where  '.$where.'';
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_row($qCek);
        if ($rCek < 1) {
            if ($atasan != '') {
                $wktu = date('Y-m-d H:i:s');
            }

            //$sIns = 'insert into '.$dbname.".sdm_ijin (karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, tipeijin,hrd,periodecuti,jumlahhari,persetujuan2,ganti) \r\n                        values ('".$_SESSION['standard']['userid']."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr1."','".$jamSmp1."','".$jnsIjin."',".$hrd.','.$periodec.','.$hk.",'".$atasan2."','".$ganti."')";
            $sIns = 'insert into '.$dbname.".sdm_ijin (karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, tipeijin,hrd,periodecuti,jumlahhari,persetujuan2,ganti) \r\n                        values ('".$karyawanId."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr1."','".$jamSmp1."','".$jnsIjin."',".$hrd.','.$periodec.','.$hk.",'".$atasan2."','".$ganti."')";
            if (mysql_query($sIns)) {
                if ($atasan != '') {
                    $to = getUserEmail($atasan.','.$atasan2.','.$hrd);
                    //$namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                    $namakaryawan = getNamaKaryawan($karyawanId);
                    $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor/Cuti a/n '.$namakaryawan;
                    $body = "<html>\r\n                                                     <head>\r\n                                                     <body>\r\n                                                       <dd>Dengan Hormat,</dd><br>\r\n                                                       <br>\r\n                                                       Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Ijin/'.$jnsIjin.' ('.$keperluan.")\r\n                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Note: Sisa cuti ybs periode ".$periodec.':'.$sisa." Hari\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Regards,<br>\r\n                                                       eAgro Plantation Management Software.\r\n                                                     </body>\r\n                                                     </head>\r\n                                                   </html>\r\n                                                   ";
                    //$kirim = kirimEmailWindows($to, $subject, $body);
                }

                if ($ganti != '') {
                    $to = getUserEmail($ganti.','.$atasan2.','.$hrd);
                    //$namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                    $namakaryawan = getNamaKaryawan($karyawanId);
                    $subject = '[Notifikasi]Pengalihan tugas a/n '.$namakaryawan;
                    $body = "<html>\r\n                                                     <head>\r\n                                                     <body>\r\n                                                       <dd>Dengan Hormat,</dd><br>\r\n                                                       <br>\r\n                                                       Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' melakukan '.$jnsIjin.' ('.$keperluan.")\r\n                                                       dan mengalihkan sementara pekerjaan kepada bapak/ibu untuk sementara.\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Regards,<br>\r\n                                                       eAgro Plantation Management Software.\r\n                                                     </body>\r\n                                                     </head>\r\n                                                   </html>\r\n                                                   ";
                    //$kirim = kirimEmailWindows($to, $subject, $body);
                }
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }

        exit('Error:Data Pada Tanggal '.$_POST['tglijin'].' Sudah ada');
    case 'loadData':
    $userlogin = $_SESSION['standard']['userid'];
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
        //$slvhc = 'select * from '.$dbname.".sdm_ijin where karyawanid='".$_SESSION['standard']['userid']."'   order by `tanggal` desc limit ".$offset.','.$limit.' ';
        $slvhc = 'select t1.karyawanid,t3.namakaryawan,t1.tanggal,t1.tipeijin,t1.keperluan,t1.persetujuan1,t1.persetujuan2,t1.stpersetujuan1,t1.darijam,t1.sampaijam,t1.ganti,t1.stpersetujuanhrd,t1.jenisijin,t1.hrd,t1.jumlahhari,t1.periodecuti,t2.keterangan from '.$dbname.".sdm_ijin as t1 left join ".$dbname.".sdm_5absensi as t2 on (t1.tipeijin=t2.kodeabsen) left join ".$dbname.".datakaryawan as t3 on (t1.karyawanid = t3.karyawanid)  order by t1.tanggal desc limit ".$offset.','.$limit.' ';
        $qlvhc = mysql_query($slvhc);
        $userlogin = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            $no++;
              $karyawanid = $rlvhc['karyawanid'];
    $strnew = "SELECT s.*,
        (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.userlogin) AS namauserlogin,
        (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.karyawanid) AS namakaryawan
        FROM setup_pengaturanadmin s where s.userlogin='$userlogin' and s.karyawanid='$karyawanid'";
        $res = mysql_query($strnew);
        while ($bar = mysql_fetch_object($res)) {
            $id_karyawan = $bar->karyawanid;
    
        }
        if($karyawanid == $id_karyawan || $karyawanid == $userlogin ){
			$jenis_ijin = "";
			$slvhc1 = 'select * from '.$dbname.".sdm_5absensi where kodeabsen='".$rlvhc['tipeijin']."'";
			$qlvhc1 = mysql_query($slvhc1);			
			while ($rlvhc1 = mysql_fetch_assoc($qlvhc1)) {
				$jenis_ijin = $rlvhc1['keterangan'];
			}
            echo "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rlvhc['namakaryawan']."</td>\r\n<td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td>".$rlvhc['keperluan']."</td>\r\n                <td>".$jenis_ijin."</td>\r\n                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>\r\n\t\t\t\t<td>".$arrNmkary[$rlvhc['persetujuan2']]."</td>\r\n                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>\r\n                <td>".tanggalnormald($rlvhc['darijam'])."</td>\r\n                <td>".tanggalnormald($rlvhc['sampaijam'])."</td>\r\n\t\t\t\t<td>".$arrNmkary[$rlvhc['ganti']].'</td>';
            if ($rlvhc['stpersetujuan1'] == 0 && $rlvhc['stpersetujuanrd']==0) {
                echo "<td>
				<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['keperluan']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['jenisijin']."','".$rlvhc['persetujuan1']."','".$rlvhc['stpersetujuan1']."','".$rlvhc['darijam']."','".$rlvhc['sampaijam']."','".$rlvhc['hrd']."','".$rlvhc['jumlahhari']."','".$rlvhc['periodecuti']."');\">\r\n                    
				<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['karyawanid']."','".$rlvhc['tanggal']."');\" ></td>";
            } else {
                echo '<td>'.$arrKeputusan[$rlvhc['stpersetujuan1']].'</td>';
            }
        }
    }
        echo "\r\n                </tr><tr class=rowheader><td colspan=9 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";

        break;
    case 'getKet':
        $sket = 'select distinct keterangan from '.$dbname.'.sdm_ijin where '.$where.'';
        $qKet = mysql_query($sket);
        $rKet = mysql_fetch_assoc($qKet);
        echo $rKet['keterangan'];

        break;
    case 'deleteData':
        $krid = $_POST['karyawanid'];
        $tanggaldel = $_POST['tanggaldel'];
        $tgldel = str_replace("_","-",$tanggaldel);
        $sket = "select distinct stpersetujuan1,stpersetujuan2 from sdm_ijin where karyawanid='".$krid."' and tanggal='".$tgldel."' ";
        $qKet = mysql_query($sket);
        $rKet = mysql_fetch_assoc($qKet);
        if ($rKet['stpersetujuan1'] == 0 || $rKet['stpersetujuan2'] == 0) {
            $sDel = "delete from sdm_ijin where karyawanid='".$krid."' and tanggal='".$tgldel."'";
            if (mysql_query($sDel)) {
               
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }else{
          echo 'Error : Sudah ada persetujuan, tidak bisa di hapus! ';  
        }
        
        exit('Error:Sudah ada keputusan');
    case 'update':
        if ($jnsIjin == '' || $jamDr == '' || $jamSmp == '' || $keperluan == '' || $atasan == '' || $ganti == '' ) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sket = 'select distinct stpersetujuan1,persetujuan2,ganti from '.$dbname.'.sdm_ijin where '.$where.'';
        $qKet = mysql_query($sket);
        $rKet = mysql_fetch_assoc($qKet);
        if ($rKet['stpersetujuan1'] == 0) {
            $sUp = 'update  '.$dbname.".sdm_ijin set keperluan='".$keperluan."', keterangan='".$ket."', darijam='".$jamDr1."', \r\n                          sampaijam='".$jamSmp1."',jenisijin='".$jnsIjin."',persetujuan2='".$atasan2."',\r\n                          hrd=".$hrd.',periodecuti='.$periodec.',jumlahhari='.$hk.' ';
            if ($atsSblm != $atasan) {
                $wktu = date('Y-m-d H:i:s');
                $sUp .= ",persetujuan1='".$atasan."',waktupengajuan='".$wktu."'";
            }

            if ($rKet['persetujuan2'] != $atasan2) {
                $sUp .= ",persetujuan2='".$atasan2."'";
            }

            if ($rKet['ganti'] != $ganti) {
                $sUp .= ",ganti='".$ganti."'";
            }

            $sUp .= ' where '.$where.'';
            if (mysql_query($sUp)) {
                if ($atsSblm != $atasan) {
                    $to = getUserEmail($atasan);
                    $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                    $subject = '[Notifikasi]Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
                    $body = "<html>\r\n                                                     <head>\r\n                                                     <body>\r\n                                                       <dd>Dengan Hormat,</dd><br>\r\n                                                       <br>\r\n                                                       Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Ijin/'.$jnsIjin.' ('.$keperluan.")\r\n                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Note: Sisa cuti ybs periode ".$periodec.':'.$sisa." Hari\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Regards,<br>\r\n                                                       eAgro Plantation Management Software.\r\n                                                     </body>\r\n                                                     </head>\r\n                                                   </html>\r\n                                                   ";
                    //$kirim = kirimEmailWindows($to, $subject, $body);
                }

                if ($rKet['ganti'] != $ganti) {
                    $to = getUserEmail($ganti);
                    $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                    $subject = '[Notifikasi]Pengalihan tugas a/n '.$namakaryawan;
                    $body = "<html>\r\n                                                     <head>\r\n                                                     <body>\r\n                                                       <dd>Dengan Hormat,</dd><br>\r\n                                                       <br>\r\n                                                       Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' melakukan '.$jnsIjin.' ('.$keperluan.")\r\n                                                       dan mengalihkan pekerjaan kepada bapak/ibu untuk sementar.\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Regards,<br>\r\n                                                       eAgro Plantation Management Software.\r\n                                                     </body>\r\n                                                     </head>\r\n                                                   </html>\r\n                                                   ";
                    //$kirim = kirimEmailWindows($to, $subject, $body);
                }
            }

            if ($atsSblm != $atasan) {
                $to = getUserEmail($atsSblm);
                $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                $subject = '[Notifikasi]Pembatalan Persetujuan Ijin Keluar Kantor a/n '.$namakaryawan;
                $body = "<html>\r\n                                                     <head>\r\n                                                     <body>\r\n                                                       <dd>Dengan Hormat,</dd><br>\r\n                                                       <br>\r\n                                                       Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Ijin/'.$jnsIjin.' ('.$keperluan.")\r\n                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Note: Sisa cuti ybs periode ".$periodec.':'.$sisa." Hari\r\n                                                       <br>\r\n                                                       <br>\r\n                                                       Regards,<br>\r\n                                                       eAgro Plantation Management Software.\r\n                                                     </body>\r\n                                                     </head>\r\n                                                   </html>\r\n                                                   ";
                //$kirim = kirimEmailWindows($to, $subject, $body);
            }

            break;
        }

        exit('Error:Sudah ada keputusan');
    default:
        break;
}

?>