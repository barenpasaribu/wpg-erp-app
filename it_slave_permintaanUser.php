<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
('' !== $_POST['proses'] ? ($proses = $_POST['proses']) : ($proses = $_GET['proses']));
('' !== $_POST['jenislayanan'] ? ($jenislayanan = $_POST['jenislayanan']) : ($jenislayanan = $_GET['jenislayanan']));
('' !== $_POST['deskripsi'] ? ($deskripsi = $_POST['deskripsi']) : ($deskripsi = $_GET['deskripsi']));
('' !== $_POST['atasan'] ? ($atasan = $_POST['atasan']) : ($atasan = $_GET['atasan']));
('' !== $_POST['managerit'] ? ($managerit = $_POST['managerit']) : ($managerit = $_GET['managerit']));
$date = date('Y-m-d');
$d = substr($date, 8, 2);
$m = numToMonth(substr($date, 6, 2), $lang = 'I', $format = 'long');
$y = substr($date, 0, 4);
$tanggal = $d.' '.$m.' '.$y;
$lokasitugas = $_SESSION['empl']['lokasitugas'];
$karyawanid = $_SESSION['standard']['userid'];
('' === $_POST['kepuasanuser'] ? ($kepuasanuser = $_GET['kepuasanuser']) : ($kepuasanuser = $_POST['kepuasanuser']));
('' === $_POST['nilaikomunikasi'] ? ($nilaikomunikasi = $_GET['nilaikomunikasi']) : ($nilaikomunikasi = $_POST['nilaikomunikasi']));
('' !== $_POST['notransaksi'] ? ($notransaksi = $_POST['notransaksi']) : ($notransaksi = $_GET['notransaksi']));
('' !== $_POST['saranuser'] ? ($saranuser = $_POST['saranuser']) : ($saranuser = $_GET['saranuser']));
('' !== $_POST['tolak'] ? ($tolak = $_POST['tolak']) : ($tolak = $_GET['tolak']));
('' !== $_POST['transaksi'] ? ($transaksi = $_POST['transaksi']) : ($transaksi = $_GET['transaksi']));
switch ($proses) {
    case 'insert':
        $notransaksi = 0;
        $insert = 'insert into '.$dbname.".it_request\r\n        (notransaksi,kodekegiatan,deskripsi,tanggal,lokasitugas,karyawanid,atasan,managerit)\r\n         values('".$notransaksi."','".$jenislayanan."','".$deskripsi."','".$date."','".$lokasitugas."',\r\n        '".$karyawanid."','".$atasan."','".$managerit."')";
        if (mysql_query($insert)) {
            $s_ket = 'select a.keterangan as ket from '.$dbname.'.it_standard a left join '.$dbname.".it_request b\r\n            on a.kodekegiatan=b.kodekegiatan where a.kodekegiatan=".$jenislayanan.'';
            $q_ket = mysql_query($s_ket) || exit(mysql_error($conns));
            $r_ket = mysql_fetch_assoc($q_ket);
            $ket = $r_ket['ket'];
            if ('' !== $atasan) {
                $to = getUserEmail($atasan);
                $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                $subject = '[Notifikasi] Permintaan Layanan '.$ket.' ';
                $body = "<html>\r\n         <head>\r\n         <body>\r\n           Dengan Hormat,<br>\r\n           <br>\r\n           Karyawan a/n: ".$namakaryawan.' meminta layanan '.$ket.' pada tanggal '.$tanggal." \r\n           ke departemen IT<br>dengan deskripsi ".$deskripsi."<br>Mohon konfirmasi dari bapak/ibu melalui \r\n           menu IT->Permintaan Layanan\r\n           <br>\r\n           <br>\r\n           Regards,<br>\r\n           eAgro Plantation Management Software.\r\n         </body>\r\n         </head>\r\n        </html>\r\n        ";
                $kirim = kirimEmailWindows($to, $subject, $body);
            }

            if ('' !== $managerit) {
                $to = getUserEmail($managerit);
                $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
                $subject = '[Notifikasi] Permintaan Layanan '.$ket.'';
                $body = "<html>\r\n         <head>\r\n         <body>\r\n           Dengan Hormat,<br>\r\n           <br>\r\n           Karyawan a/n: ".$namakaryawan.' meminta layanan '.$ket.' pada tanggal '.$tanggal." \r\n           ke departemen IT<br>dengan deskripsi ".$deskripsi."<br>Mohon konfirmasi dari bapak/ibu melalui \r\n           menu IT->Permintaan Layanan\r\n           <br>\r\n           <br>\r\n           Regards,<br>\r\n           eAgro Plantation Management Software.\r\n         </body>\r\n         </head>\r\n        </html>\r\n        ";
                $kirim = kirimEmailWindows($to, $subject, $body);
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loaddata':
        $limit = 25;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $sCount = 'select count(*) as jmlhrow from '.$dbname.".it_request \r\n         where karyawanid='".$_SESSION['standard']['userid']."' or atasan='".$_SESSION['standard']['userid']."'\r\n         or managerit='".$_SESSION['standard']['userid']."' order by notransaksi asc";
        $qCount = mysql_query($sCount) || exit(mysql_error($conns));
        while ($rCount = mysql_fetch_object($qCount)) {
            $jmlbrs = $rCount->jmlhrow;
        }
        $offset = $page * $limit;
        if ($jmlbrs < $offset) {
            --$page;
        }

        $offset = $page * $limit;
        $no = $offset;
        $s_login = "select a.tanggal as tgl,b.keterangan as namakegiatan,c.namakaryawan as namakaryawan,\r\n          a.atasan as atasan,a.statusatasan as statusatasan, tanggalatasan as tglatasan,\r\n          a.statusmanagerit as statusmgr,a.pelaksana as pelaksana,a.waktupelaksanaan as wktpelaksanaan,\r\n          a.waktuselesai as wktselesai,a.nilaikomunikasi as nilaikom,a.saranuser as saran,\r\n          a.saranpelaksana as saranpelaksana,a.karyawanid as karyawanid,\r\n          a.nilaihasilkerja as nilaihasilkerja,a.saranuser as saran,a.notransaksi as notransaksi,\r\n          a.managerit as managerit\r\n          from ".$dbname.".it_request a\r\n          left join ".$dbname.".it_standard b on a.kodekegiatan=b.kodekegiatan\r\n          left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid\r\n          where a.karyawanid='".$_SESSION['standard']['userid']."' or a.atasan='".$_SESSION['standard']['userid']."'\r\n          or a.managerit='".$_SESSION['standard']['userid']."'\r\n          order by a.notransaksi asc limit ".$offset.','.$limit.' ';
        $q_login = mysql_query($s_login) || exit(mysql_error($conns));
        while ($r_login = mysql_fetch_assoc($q_login)) {
            $s_atasan = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$r_login['atasan']."'";
            $q_atasan = mysql_query($s_atasan) || exit(mysql_error($conns));
            $r_atasan = mysql_fetch_assoc($q_atasan);
            ++$no;
            echo "<tr class=rowcontent>\r\n        <td id='no' align='center'>".$no."</td>\r\n        <td id='tgl_".$no."' align='center'>".$r_login['tgl']."</td>\r\n        <td id='namakegiatan_".$no."' align='left'>".$r_login['namakegiatan']."</td>\r\n        <td id='namakaryawan_".$no."' align='left'>".$r_login['namakaryawan']."</td>\r\n        <td id='atasan_".$no."' align='left'>".$r_atasan['namakaryawan'].'</td>';
            $s_kepuasanuser = 'select keterangan from '.$dbname.".it_stkepuasan \r\n                         where kode='HASILKERJA' and nilai='".$r_login['nilaihasilkerja']."' order by nilai asc";
            $q_kepuasanuser = mysql_query($s_kepuasanuser) || exit(mysql_error($conns));
            $r_kepuasanuser = mysql_fetch_assoc($q_kepuasanuser);
            $s_nilaikom = 'select keterangan from '.$dbname.".it_stkepuasan \r\n                 where kode='KOMUNIKASI' and nilai='".$r_login['nilaikom']."' order by nilai asc";
            $q_nilaikom = mysql_query($s_nilaikom) || exit(mysql_error($conns));
            $r_nilaikom = mysql_fetch_assoc($q_nilaikom);
            if ($karyawanid === $r_login['atasan']) {
                if ('0' === $r_login['statusatasan']) {
                    echo "<td id='statusatasan_".$no."' align='center'>\r\n                  <button class=mybutton onclick=setuju('".$no."');>".$_SESSION['lang']['setuju']."</button>\r\n                  <button class=mybutton onclick=tolak('".$no."');>".$_SESSION['lang']['tolak'].'</button></td>';
                } else {
                    if ('1' === $r_login['statusatasan']) {
                        echo "<td id='statusatasan_".$no."' align='left'>Setuju</td>";
                    } else {
                        echo "<td id='statusatasan_".$no."' align='left'>".$r_login['statusatasan'].'</td>';
                    }
                }

                echo "<td id='tglatasan_".$no."' align='center'>".$r_login['tglatasan']."</td>\r\n        <td id='statusmgr_".$no."' align='center'>".$r_login['statusmgr']."</td>\r\n        <td id='pelaksana_".$no."' align='left'>".$r_login['pelaksana']."</td>\r\n        <td id='wktpelaksana_".$no."' align='center'>".$r_login['wktpelaksanaan']."</td>\r\n        <td id='wktselesai_".$no."' align='center'>".$r_login['wktselesai']."</td>\r\n        <input type=hidden id='notransaksi_".$no."' value='".$r_login['notransaksi']."'>\r\n        <td id='kepuasanuser_".$no."' align='center'>".$r_kepuasanuser['keterangan']."</td>\r\n        <td id='nilaikomunikasi_".$no."' align='center'>".$r_nilaikom['keterangan']."</td>\r\n        <td id='saran_".$no."' colspan=2 align='left'>".$r_login['saran']."</td>\r\n        <td id='saranpelaksana_".$no."' align='center'>".$r_login['saranpelaksana']."</td>\r\n        <td align=center><img onclick=view('".$no."') title=\"View\" class=\"resicon\" src=\"images/zoom.png\"></td>";
            } else {
                if ($karyawanid === $r_login['karyawanid']) {
                    if ('1' === $r_login['statusatasan']) {
                        echo "<td id='statusatasan_".$no."' align='left'>Setuju</td>";
                    } else {
                        echo "<td id='statusatasan_".$no."' align='left'>".$r_login['statusatasan'].'</td>';
                    }

                    echo "<td id='tglatasan_".$no."' align='center'>".$r_login['tglatasan']."</td>\r\n        <td id='statusmgr_".$no."' align='center'>".$r_login['statusmgr']."</td>\r\n        <td id='pelaksana_".$no."' align='left'>".$r_login['pelaksana']."</td>\r\n        <td id='wktpelaksana_".$no."' align='center'>".$r_login['wktpelaksanaan']."</td>\r\n        <td id='wktselesai_".$no."' align='center'>".$r_login['wktselesai']."</td>\r\n        <input type=hidden id='notransaksi_".$no."' value='".$r_login['notransaksi']."'>";
                    if (0 === $r_login['nilaihasilkerja']) {
                        $opt_kepuasanuser = "<option value=''></option>";
                        $s_kepuasanuser = 'select nilai,keterangan from '.$dbname.".it_stkepuasan \r\n                             where kode='HASILKERJA' order by nilai asc";
                        $q_kepuasanuser = mysql_query($s_kepuasanuser) || exit(mysql_error($conns));
                        while ($r_kepuasanuser = mysql_fetch_assoc($q_kepuasanuser)) {
                            $opt_kepuasanuser .= "<option value='".$r_kepuasanuser['nilai']."'>".$r_kepuasanuser['keterangan'].'</option>';
                        }
                        echo "<td><select id='kepuasanuser_".$no."' style='width:150px;' onchange=update_nilaihk('".$no."'); >".$opt_kepuasanuser.'</select></td>';
                    } else {
                        echo "<td id='kepuasanuser_".$no."' align='left'>".$r_kepuasanuser['keterangan'].'</td>';
                    }

                    if (0 === $r_login['nilaikom']) {
                        $opt_nilaikom = "<option value=''></option>";
                        $s_nilaikom = 'select nilai,keterangan from '.$dbname.".it_stkepuasan \r\n                         where kode='KOMUNIKASI' order by nilai asc";
                        $q_nilaikom = mysql_query($s_nilaikom) || exit(mysql_error($conns));
                        while ($r_nilaikom = mysql_fetch_assoc($q_nilaikom)) {
                            $opt_nilaikom .= "<option value='".$r_nilaikom['nilai']."'>".$r_nilaikom['keterangan'].'</option>';
                        }
                        echo "<td><select id='nilaikomunikasi_".$no."' style='width:150px;' onchange=update_nilaikom('".$no."');>".$opt_nilaikom."\r\n                </select></td>";
                    } else {
                        echo "<td id='nilaikomunikasi_".$no."' align='left'>".$r_nilaikom['keterangan'].'</td>';
                    }

                    if ('' === $r_login['saran']) {
                        echo "<td><textarea rows=2 cols=15 id='saranuser_".$no."' onkeypress=return tanpa_kutip(); /></textarea></td>\r\n                  <td><img onclick=simpan('".$no."'); class=\"resicon\" src=\"images/skyblue/save.png\"></td>";
                    } else {
                        $saran = substr($r_login['saran'], 0, 15);
                        echo "<td colspan=2 id='saran_".$no."' align='left'>".$saran.'...'.'</td>';
                    }

                    if ('' !== $r_login['saranpelaksana']) {
                        echo "<td id='saranpelaksana_".$no."' align='left'>".substr($r_login['saranpelaksana'], 0, 15).'...'.'</td>';
                    } else {
                        echo "<td id='saranpelaksana_".$no."' align='left'>".$r_login['saranpelaksana'].'</td>';
                    }

                    echo "<td align=center><img onclick=view('".$no."') title=\"View\" class=\"resicon\" src=\"images/zoom.png\"></td>";
                } else {
                    if ($karyawanid === $r_login['managerit']) {
                        if ('1' === $r_login['statusatasan']) {
                            echo "<td id='statusatasan_".$no."' align='left'>Setuju</td>";
                        } else {
                            echo "<td id='statusatasan_".$no."' align='left'>".$r_login['statusatasan'].'</td>';
                        }

                        echo "<td id='tglatasan_".$no."' align='center'>".$r_login['tglatasan']."</td>\r\n        <td id='statusmgr_".$no."' align='center'>".$r_login['statusmgr']."</td>\r\n        <td id='pelaksana_".$no."' align='left'>".$r_login['pelaksana']."</td>\r\n        <td id='wktpelaksana_".$no."' align='center'>".$r_login['wktpelaksanaan']."</td>\r\n        <td id='wktselesai_".$no."' align='center'>".$r_login['wktselesai']."</td>\r\n        <input type=hidden id='notransaksi_".$no."' value='".$r_login['notransaksi']."'>\r\n        <td id='kepuasanuser_".$no."' align='left'>".$r_kepuasanuser['keterangan']."</td>\r\n        <td id='nilaikomunikasi_".$no."' align='left'>".$r_nilaikom['keterangan']."</td>\r\n        <td id='saran_".$no."' colspan=2 align='left'>".$r_login['saran']."</td>\r\n        <td id='saranpelaksana_".$no."' align='left'>".$r_login['saranpelaksana']."</td>\r\n        <td align=center><img onclick=view('".$no."') title=\"View\" class=\"resicon\" src=\"images/zoom.png\"></td>";
                    }
                }
            }
        }
        echo "\r\n</tr><tr class=rowheader><td colspan=15 align=center>\r\n".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jmlbrs."<br />\r\n<button class=mybutton onclick=pages(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n<button class=mybutton onclick=pages(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n</td>\r\n</tr>";

        break;
    case 'update_nilaihk':
        $s_update = 'update '.$dbname.".it_request set nilaihasilkerja='".$kepuasanuser."' where notransaksi='".$notransaksi."' ";
        if (!mysql_query($s_update)) {
            echo 'DB Error : '.mysql_error($conn);
            exit();
        }

        break;
    case 'update_nilaikom':
        $s_update = 'update '.$dbname.".it_request set nilaikomunikasi='".$nilaikomunikasi."' where notransaksi='".$notransaksi."' ";
        if (!mysql_query($s_update)) {
            echo 'DB Error : '.mysql_error($conn);
            exit();
        }

        break;
    case 'update_saranuser':
        $s_update = 'update '.$dbname.".it_request set saranuser='".$saranuser."' where notransaksi='".$notransaksi."' ";
        if (!mysql_query($s_update)) {
            echo 'DB Error : '.mysql_error($conn);
            exit();
        }

        break;
    case 'setuju':
        $s_setuju = 'update '.$dbname.".it_request set statusatasan=1 where notransaksi='".$notransaksi."' ";
        if (!mysql_query($s_setuju)) {
            echo 'DB Error : '.mysql_error($conn);
            exit();
        }

        break;
    case 'formpenolakan':
        $s_form = 'select * from '.$dbname.".it_request where notransaksi='".$notransaksi."' ";
        $q_from = mysql_query($s_form) || exit(mysql_error($conns));
        $r_form = mysql_fetch_assoc($q_from);
        echo '<div id=form_tolak><fieldset><legend>No Transaksi: '.$notransaksi."</legend>\r\n          <table cellspacing=1 border=0>\r\n            <tr>\r\n               <td><textarea rows=5 cols=34 id='tolak'></textarea></td>\r\n               <td><button class=mybutton id=save onclick=save('".$notransaksi."')>";
        echo $_SESSION['lang']['save'];
        echo '</button></td></tr></table></filedset></div>';

        break;
    case 'update_statusatasan':
        $s_tolak = 'update '.$dbname.".it_request set statusatasan='".$tolak."',tanggalatasan='".date('Y-m-d')."' \r\n              where notransaksi='".$transaksi."' ";
        if (!mysql_query($s_tolak)) {
            echo 'DB Error : '.mysql_error($conn);
            exit();
        }

        break;
    case 'show':
        $s_form = 'select * from '.$dbname.".it_request where notransaksi='".$notransaksi."' ";
        $q_from = mysql_query($s_form) || exit(mysql_error($conns));
        $r_form = mysql_fetch_assoc($q_from);
        echo '<div id=view><fieldset><legend>No Transaksi: '.$notransaksi."</legend>\r\n          <table cellspacing=1 border=0>\r\n              <tr><td style=width:100px;>Deskripsi</td><td>:</td><td align=left>".$r_form['deskripsi']."</td></tr>\r\n              <tr><td style=width:100px;>Saran User</td><td>:</td><td align=left>".$r_form['saranuser']."</td></tr>\r\n              <tr><td style=width:100px;>Saran Pelaksana</td><td>:</td><td align=left>".$r_form['saranpelaksana']."</td></tr>\r\n          </table></filedset></div>";

        break;
    default:
        break;
}

?>