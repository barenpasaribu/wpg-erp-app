<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$method = $_POST['method'];
$arrstatus = ['' => '', 'Disetujui', 'Ditolak'];
$arrstatusList = [1 => $_SESSION['lang']['disetujui'], 2 => $_SESSION['lang']['ditolak'], 3 => $_SESSION['lang']['wait_approval']];
$optstatus = "<option value='1'>Disetujui</option>";
$optstatus .= "<option value='2'>Ditolak</option>";
$setujuKe = $_POST['setujuKe'];
$noKode = $_POST['noKode'];
$apv = $_POST['apv'];
$tgl = tanggalsystem($_POST['tgl']);
$txt = $_POST['txt'];
$status = $_POST['status'];
$hrini = date('Ymd');
$kode = $_POST['kode'];
switch ($method) {
    case 'getApvForm':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['project'].'</td>';
        $tab .= "<td>:</td><td><input type='text' id=noKode class=myinputtext value='".$kode."' style=width:150px; disabled />  </td></tr>";
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['status'].'</td>';
        $tab .= '<td>:</td><td><select id=apv style=width:150px;>'.$optstatus.'</select></td></tr>';
        $tab .= '<tr class=rowcontent><td colspan=3 align=center>';
        $tab .= '<button class=mybutton onclick=saveApvForm('.$setujuKe.')>'.$_SESSION['lang']['save'].'</button></td></tr>';
        $tab .= '</table>';
        echo $tab;

        break;
    case 'saveApvForm':
        $setujuLanjut = $setujuKe + 1;
        if ('7' === $setujuKe) {
            if ('1' === $apv) {
                $str = 'update '.$dbname.'.project set stpersetujuan'.$setujuKe."='".$apv."',tglpersetujuan".$setujuKe."='".$hrini."' where kode='".$noKode."' ";
                if (mysql_query($str)) {
                } else {
                    echo ' Gagal,'.addslashes(mysql_error($conn));
                }
            } else {
                $str = 'update '.$dbname.'.project set stpersetujuan'.$setujuKe."='".$apv."',tglpersetujuan".$setujuKe."='".$hrini."' where kode='".$noKode."' ";
                if (mysql_query($str)) {
                    $i = 'select persetujuan'.$setujuKe.',updateby from '.$dbname.".project where kode='".$noKode."'";
                    $n = mysql_query($i);
                    $d = mysql_fetch_assoc($n);
                    $to = getUserEmail($d['updateby']);
                    $namakaryawan = getNamaKaryawan($d['persetujuan'.$setujuKe]);
                    $nmpnlk = getNamaKaryawan($d['updateby']);
                    $subject = '[Notifikasi]'.$_SESSION['lang']['persetujuan'].' '.$_SESSION['lang']['project'].' dari '.$namakaryawan;
                    $body = "<html>\r\n\t\t\t\t\t\t\t\t <head>\r\n\t\t\t\t\t\t\t\t <body>\r\n\t\t\t\t\t\t\t\t   <dd>Dengan Hormat, Bapak./Ibu. ".$nmpnlk."</dd><br>\r\n\t\t\t\t\t\t\t\t   Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' melakukan <b>Penolakan</b> atas '.$_SESSION['lang']['project'].' : '.$kode."\r\n\t\t\t\t\t\t\t\t   Regards,<br>\r\n\t\t\t\t\t\t\t\t   eAgro Plantation Management Software.\r\n\t\t\t\t\t\t\t\t </body>\r\n\t\t\t\t\t\t\t\t </head>\r\n\t\t\t\t\t\t\t   </html>";
                    $kirim = kirimEmailWindows($to, $subject, $body);
                } else {
                    echo ' Gagal,'.addslashes(mysql_error($conn));
                }
            }
        }

        if ('1' === $apv) {
            $str = 'update '.$dbname.'.project set stpersetujuan'.$setujuKe."='".$apv."',tglpersetujuan".$setujuKe."='".$hrini."',stpersetujuan".$setujuLanjut."='3' where kode='".$noKode."' ";
            if (mysql_query($str)) {
                $i = 'select persetujuan'.$setujuKe.',persetujuan'.$setujuLanjut.',updateby from '.$dbname.".project where kode='".$noKode."'";
                $n = mysql_query($i);
                $d = mysql_fetch_assoc($n);
                if ('0000000000' !== $d['stpersetujuan'.$setujuLanjut]) {
                    $to = getUserEmail($d['persetujuan'.$setujuLanjut]);
                    $namakaryawan = getNamaKaryawan($d['persetujuan'.$setujuKe]);
                    $nmpnlk = getNamaKaryawan($d['persetujuan'.$setujuLanjut]);
                    $subject = '[Notifikasi]'.$_SESSION['lang']['persetujuan'].' '.$_SESSION['lang']['project'].' dari '.$namakaryawan;
                    $body = "<html>\r\n\t\t\t\t\t\t\t\t <head>\r\n\t\t\t\t\t\t\t\t <body>\r\n\t\t\t\t\t\t\t\t   <dd>Dengan Hormat, Bapak./Ibu. ".$nmpnlk."</dd><br>\r\n\t\t\t\t\t\t\t\t   Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Persertujuan atas '.$_SESSION['lang']['project'].' : '.$kode."\r\n\t\t\t\t\t\t\t\t   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n\t\t\t\t\t\t\t\t   <br>\r\n\t\t\t\t\t\t\t\t   Regards,<br>\r\n\t\t\t\t\t\t\t\t   eAgro Plantation Management Software.\r\n\t\t\t\t\t\t\t\t </body>\r\n\t\t\t\t\t\t\t\t </head>\r\n\t\t\t\t\t\t\t   </html>";
                    $kirim = kirimEmailWindows($to, $subject, $body);
                }
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        } else {
            $str = 'update '.$dbname.'.project set stpersetujuan'.$setujuKe."='".$apv."',tglpersetujuan".$setujuKe."='".$hrini."',stpersetujuan".$setujuLanjut."='0' where kode='".$noKode."' ";
            if (mysql_query($str)) {
                $i = 'select persetujuan'.$setujuKe.',persetujuan'.$setujuLanjut.',updateby from '.$dbname.".project where kode='".$noKode."'";
                $n = mysql_query($i);
                $d = mysql_fetch_assoc($n);
                $to = getUserEmail($d['updateby']);
                $namakaryawan = getNamaKaryawan($d['persetujuan'.$setujuKe]);
                $nmpnlk = getNamaKaryawan($d['updateby']);
                $subject = '[Notifikasi]'.$_SESSION['lang']['persetujuan'].' '.$_SESSION['lang']['project'].' dari '.$namakaryawan;
                $body = "<html>\r\n\t\t\t\t\t\t\t <head>\r\n\t\t\t\t\t\t\t <body>\r\n\t\t\t\t\t\t\t   <dd>Dengan Hormat, Bapak./Ibu. ".$nmpnlk."</dd><br>\r\n\t\t\t\t\t\t\t   Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' melakukan <b>Penolakan</b> atas '.$_SESSION['lang']['project'].' : '.$kode."\r\n\t\t\t\t\t\t\t   Regards,<br>\r\n\t\t\t\t\t\t\t   eAgro Plantation Management Software.\r\n\t\t\t\t\t\t\t </body>\r\n\t\t\t\t\t\t\t </head>\r\n\t\t\t\t\t\t   </html>";
                $kirim = kirimEmailWindows($to, $subject, $body);
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        }

        break;
    case 'loadData':
        echo "\r\n\t\t\r\n\t\t<table cellspacing='1' border='0' class='sortable'>\r\n\t\t\r\n\t\t\t<thead>\r\n\t\t\t\t<tr class=rowheader>\r\n\t\t\t\t\t<td align=center rowspan=3>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\t<td align=center rowspan=3>".$_SESSION['lang']['project']."</td>\r\n\t\t\t\t\t<td align=center rowspan=3>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['project']."</td>\r\n\t\t\t\t\t<td colspan=14 align=center>".$_SESSION['lang']['persetujuan']."</td>\r\n   \t\t\t\t\t<td rowspan=3 align=center>".$_SESSION['lang']['pdf']."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>";
        for ($i = 1; $i <= 7; ++$i) {
            echo "\r\n\t\t\t\t\t\t<td align=center colspan=2>".$i.'</td>';
        }
        echo "\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>";
        for ($i = 1; $i <= 7; ++$i) {
            echo "\r\n\t\t\t\t\t\t<td align=center>".$_SESSION['lang']['nama']."</td>\r\n\t\t\t\t\t\t<td align=center>".$_SESSION['lang']['status'].'</td>';
        }
        echo "\r\n\t\t\t\t</tr>\r\n\t\t\t</thead>\r\n\t\t<tbody>";
        $limit = 30;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        if ('' !== $txt) {
            $txt = "where kode like '%".$txt."%'";
        } else {
            $txt = '';
        }

        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.project  '.$txt.' ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $ha = 'SELECT * FROM '.$dbname.'.project '.$txt.' order by kode desc  limit '.$offset.','.$limit.'';
        $hi = mysql_query($ha);
        $no = $maxdisplay;
        while ($hu = mysql_fetch_assoc($hi)) {
            ++$no;
            echo "\r\n\t\t\t<tr class=rowcontent id=tr_".$no.">\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$hu['kode']."</td>\r\n\t\t\t\t<td>".$hu['nama'].'</td>';
            for ($i = 1; $i <= 7; ++$i) {
                echo '<td>'.getNamaKaryawan($hu['persetujuan'.$i]).'</td>';
                if ($hu['persetujuan'.$i] === $_SESSION['standard']['userid'] && 3 === $hu['stpersetujuan'.$i]) {
                    echo "<td><img src=images/icons/arrow_right.png class=resicon height='30' title='Aprove Project: ".$hu['kode']."' onclick=\"getApvForm('".$hu['kode']."','".$i."');\"></td>";
                } else {
                    if ('0000-00-00' === $hu['tglpersetujuan'.$i]) {
                        $tgl = '';
                    } else {
                        $tgl = tanggalnormal($hu['tglpersetujuan'.$i]);
                    }

                    echo '<td><b>'.$arrstatusList[$hu['stpersetujuan'.$i]].'</b> '.$tgl.'</td>';
                }
            }
            echo "<td align=center><img onclick=\"masterPDF('project','".$hu['kode'].','.$hu['updateby']."','','vhc_slave_project_pdf',event);\" title=\"Print\" class=\"resicon\" src=\"images/pdf.jpg\"></td>";
            echo "</tr>\r\n\t\t\t\r\n\t\t\t";
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=18 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
}

?>