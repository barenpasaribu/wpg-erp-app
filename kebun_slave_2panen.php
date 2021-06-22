<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$pt = $_POST['pt'];
if (isset($_GET['proses'])) {
    $proses = $_GET['proses'];
} else {
    $proses = $_POST['proses'];
}

switch ($proses) {
    case 'getKbn':
        $optKebun = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sKbn = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$pt."' and tipe='KEBUN'";
        $qKbn = mysql_query($sKbn) ;
        while ($rKbn = mysql_fetch_assoc($qKbn)) {
            $optKebun .= '<option value='.$rKbn['kodeorganisasi'].'>'.$rKbn['namaorganisasi'].'</option>';
        }
        echo $optKebun;

        break;
    case 'getDetail':
        $kodeorg = $_GET['kodeorg'];
        $tgl = $_GET['tanggal'];
        $sKary = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan where lokasitugas='".substr($kodeorg, 0, 4)."'";
        $qKary = mysql_query($sKary) ;
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $rArrKary[$rKary['karyawanid']] = $rKary['namakaryawan'];
        }
        echo "<link rel=stylesheet type=text/css href=style/generic.css>\r\n        <script language=javascript1.2 src='js/generic.js'></script>\r\n        <script language=javascript1.2 src='js/kebun_panen.js'></script>";
        echo '<fieldset><legend>'.$_SESSION['lang']['detail'].'</legend>';
        echo $_SESSION['lang']['unit'].':'.$kodeorg.'<br />';
        echo $_SESSION['lang']['tanggal'].':'.tanggalnormal($tgl).'<br />';
        echo "<br /><img onclick=fisikKeExcel2(event,'kebun_slave_2panen.php') src=images/excel.jpg class=resicon title='MS.Excel'> ";
        echo "<input type='hidden' id='tanggal' value='".$tgl."' /><input type='hidden' id='kdOrg' value='".$kodeorg."' />\r\n        <table class=sortable cellpadding=1 border=0>\r\n        <thead>\r\n        <tr class=rowheader>\r\n        <td>No.</td>\r\n        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td>".$_SESSION['lang']['blok']."</td>\r\n        <td>".$_SESSION['lang']['nikmandor']."</td>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['hasilkerja']."</td>\r\n        <td>".$_SESSION['lang']['hasilkerjakg']."</td>\r\n        <td>".$_SESSION['lang']['upahkerja']."</td>\r\n        <td>".$_SESSION['lang']['upahpremi']."</td>\r\n        <td>".$_SESSION['lang']['rupiahpenalty']."</td>\r\n        </tr></thead><tbody>\r\n            ";
        $sPrestasi = 'select a.*,b.tanggal,b.nikmandor from '.$dbname.".kebun_prestasi a \r\n        left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n            where a.kodeorg='".$kodeorg."' and b.tanggal='".$tgl."' and b.tipetransaksi='PNN'";
        $qPrestasi = mysql_query($sPrestasi) || exit(mysql_erro($conn));
        while ($rPrestasi = mysql_fetch_assoc($qPrestasi)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$rPrestasi['notransaksi']."</td>\r\n            <td>".$rPrestasi['kodeorg'].'</td>';
            if ($tempNik !== $rPrestasi['nikmandor']) {
                $brs = 1;
            }

            if (1 === $brs) {
                $tempNik = $rPrestasi['nikmandor'];
                echo '<td>'.$rArrKary[$rPrestasi['nikmandor']].'</td>';
                $brs = 0;
            } else {
                echo '<td>&nbsp;</td>';
            }

            echo '<td>'.$rArrKary[$rPrestasi['nik']]."</td>\r\n            <td align=right>".number_format($rPrestasi['hasilkerja'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['hasilkerjakg'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['upahkerja'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['upahpremi'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['rupiahpenalty'], 2)."</td>\r\n            </tr>";
            $totKerja += $rPrestasi['hasilkerja'];
            $totKerjakg += $rPrestasi['hasilkerjakg'];
            $totUpahKerja += $rPrestasi['upahkerja'];
            $totPenalty += $rPrestasi['rupiahpenalty'];
            $totPremi += $rPrestasi['upahpremi'];
        }
        echo '<tr class=rowcontent><td colspan=5>Total</td><td align=right>'.number_format($totKerja, 2)."</td>\r\n        <td align=right>".number_format($totKerjakg, 2).'</td><td align=right>'.number_format($totUpahKerja, 2)."</td>\r\n        <td align=right>".number_format($totPremi, 2).'</td><td align=right>'.number_format($totPenalty, 2).'</td></tr>';
        echo '</tbody></table></fieldset>';

        break;
    case 'excelDetail':
        $kodeorg = $_GET['kdOrg'];
        $tgl = $_GET['tgl'];
        $sKary = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan where lokasitugas='".substr($kodeorg, 0, 4)."'";
        $qKary = mysql_query($sKary) ;
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $rArrKary[$rKary['karyawanid']] = $rKary['namakaryawan'];
        }
        $tab .= "\r\n        <table class=sortable cellpadding=1 border=1>\r\n        <thead>\r\n        <tr class=rowheader>\r\n        <td bgcolor=#DEDEDE align=center>No.</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['blok']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nikmandor']."</td>    \r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hasilkerja']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hasilkerjakg']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahkerja']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahpremi']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['rupiahpenalty']."</td>\r\n        </tr></thead><tbody>\r\n            ";
        $sPrestasi = 'select a.*,b.tanggal,b.nikmandor from '.$dbname.".kebun_prestasi a \r\n        left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n            where a.kodeorg='".$kodeorg."' and b.tanggal='".$tgl."' and b.tipetransaksi='PNN'";
        $qPrestasi = mysql_query($sPrestasi) || exit(mysql_erro($conn));
        while ($rPrestasi = mysql_fetch_assoc($qPrestasi)) {
            ++$no;
            $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$rPrestasi['notransaksi']."</td>\r\n            <td>".$rPrestasi['kodeorg'].'</td>';
            if ($tempNik !== $rPrestasi['nikmandor']) {
                $brs = 1;
            }

            if (1 === $brs) {
                $tempNik = $rPrestasi['nikmandor'];
                $tab .= '<td>'.$rArrKary[$rPrestasi['nikmandor']].'</td>';
                $brs = 0;
            } else {
                $tab .= '<td>&nbsp;</td>';
            }

            $tab .= "\r\n            <td>".$rArrKary[$rPrestasi['nik']]."</td>\r\n            <td align=right>".number_format($rPrestasi['hasilkerja'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['hasilkerjakg'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['upahkerja'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['upahpremi'], 2)."</td>\r\n            <td align=right>".number_format($rPrestasi['rupiahpenalty'], 2)."</td>\r\n            </tr>";
            $totKerja += $rPrestasi['hasilkerja'];
            $totKerjakg += $rPrestasi['hasilkerjakg'];
            $totUpahKerja += $rPrestasi['upahkerja'];
            $totPenalty += $rPrestasi['rupiahpenalty'];
            $totPremi += $rPrestasi['upahpremi'];
        }
        $tab .= '<tr class=rowcontent><td colspan=5>Total</td><td align=right>'.number_format($totKerja, 2)."</td>\r\n        <td align=right>".number_format($totKerjakg, 2).'</td><td align=right>'.number_format($totUpahKerja, 2)."</td>\r\n        <td align=right>".number_format($totPremi, 2).'</td><td align=right>'.number_format($totPenalty, 2).'</td></tr>';
        $tab .= '</tbody>';
        $tab .= '</table>Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $nop_ = 'laporanPanenDetail_'.$kodeorg.'_'.$tgl;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>