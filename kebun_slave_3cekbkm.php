<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kdorg'];
$per = $_POST['per'];
if ('excel' === $proses) {
    $kdorg = $_GET['kdorg'];
    $per = $_GET['per'];
    $border = 'border=1';
}

$stream .= "<table cellspacing='1' ".$border." class='sortable'>\r\n\t\t\t<thead class=rowheader>\r\n\t\t\t\t<tr class=rowheader>\r\n\t\t\t\t\t<td align=center rowspan=2>No</td>\r\n\t\t\t\t\t<td align=center colspan=4>Prestasi</td>\r\n\t\t\t\t\t<td align=center colspan=3>Absensi</td>\r\n\t\t\t\t\t\r\n\t\t\t\t  </tr>\r\n\t\t\t\t  <tr>\r\n\t\t\t\t\t<td align=center>Kegiatan</td><td align=center>Notransaksi</td>\r\n\t\t\t\t\t<td align=center>HK</td>\r\n\t\t\t\t\t<td align=center>Hasil Kerja</td>\r\n\t\t\t\t\t<td align=center>Notransaksi</td>\r\n\t\t\t\t\t<td align=center>HK</td>\r\n\t\t\t\t\t<td align=center>Hasil Kerja</td>\r\n\t\t\t\t\t\r\n\t\t\t\t</tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody>";
$iPres = "SELECT a.notransaksi notransaksi ,sum(a.jumlahhk) as jumlahhk,sum(a.hasilkerja) as hasilkerja\r\n\t\t\t\t, c.namakegiatan FROM ".$dbname.".`kebun_prestasi` a join `setup_kegiatan` `c` on(`a`.`kodekegiatan` = `c`.`kodekegiatan`) where a.notransaksi in \r\n\t\t\t\t(select notransaksi from ".$dbname.".kebun_aktifitas where kodeorg='".$kdorg."' and tanggal like '%".$per."%' and tipetransaksi!='PNN' and jurnal=0) group by notransaksi";

$nPres = mysql_query($iPres) ;
while ($dPres = mysql_fetch_assoc($nPres)) {
    $listTran[$dPres['notransaksi']] = $dPres['notransaksi'];
    $listHkPres[$dPres['notransaksi']] = round($dPres['jumlahhk'], 2);
    $listHslPres[$dPres['notransaksi']] = round($dPres['hasilkerja'],2);
    $listNmPres[$dPres['notransaksi']] = $dPres['namakegiatan'];
}
$iAbs = "SELECT notransaksi,sum(jhk) as jumlahhk,sum(hasilkerja) as hasilkerja\r\n\t\t\t\tFROM ".$dbname.".`kebun_kehadiran` where notransaksi in \r\n\t\t\t\t(select notransaksi from ".$dbname.".kebun_aktifitas where kodeorg='".$kdorg."' and tanggal like '%".$per."%' and tipetransaksi!='PNN'  and jurnal=0) group by notransaksi";
$nAbs = mysql_query($iAbs) ;
while ($dAbs = mysql_fetch_assoc($nAbs)) {
    $listTran[$dAbs['notransaksi']] = $dAbs['notransaksi'];
    $listHkAbs[$dAbs['notransaksi']] = round($dAbs['jumlahhk'], 2);
    $listHslAbs[$dAbs['notransaksi']] = round($dAbs['hasilkerja'], 2);
}
foreach ($listTran as $notran) {
    if ($listHkPres[$notran] === $listHkAbs[$notran]) {
        $cekHk = '';
    } else {
        $cekHk = 'SALAH';
    }

    if ($listHslPres[$notran] === $listHslAbs[$notran]) {
        $cekHs = '';
    } else {
        $cekHs = 'SALAH';
    }

    if ($listHkPres[$notran] !== $listHkAbs[$notran] || $listHslPres[$notran] !== $listHslAbs[$notran]) {
        $bg = 'bgcolor=#FF0000';
    } else {
        $bg = '';
    }

    ++$no;
    $stream .= "\r\n\t\t\t<tr class=rowcontent id=row".$no.">\r\n\t\t\t\t<td  ".$bg.' align=center>'.$no."</td>\r\n\t\t\t\t<td  ".$bg.' align=left id=not'.$no.'>'.$notran."</td><td  ".$bg.' align=left id=not'.$no.'>'.$listNmPres[$notran]."</td>\r\n\t\t\t\t<td ".$bg.' align=right >'.$listHkPres[$notran]."</td>\r\n\t\t\t\t<td  ".$bg.' align=right >'.$listHslPres[$notran]."</td>\r\n\t\t\t\t<td ".$bg.' align=left>'.$notran."</td>\r\n\t\t\t\t<td ".$bg.' align=right id=hk'.$no.'>'.$listHkAbs[$notran]."</td>\r\n\t\t\t\t<td ".$bg.' align=right id=hs'.$no.'>'.$listHslAbs[$notran]."</td>\r\n\t\t\t\t\r\n\t\t\t</tr>";
}
$stream .= '</table>';
$stream .= '<button class=mybutton onclick=saveAll('.$no.');>'.$_SESSION['lang']['proses'].'</button>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $tglSkrg = date('Ymd');
        $nop_ = 'laporan_cek_prestasi_kehadiran'.$tglSkrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n\t\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t\t</script>";
            closedir($handle);
        }

        break;
}

?>