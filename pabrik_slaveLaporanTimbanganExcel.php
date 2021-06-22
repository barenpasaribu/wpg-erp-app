<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kdBrg = $_GET['kdBrg'];
$kdPbrk = $_GET['kdPbrk'];
$tgl = $_GET['tgl'];
$txt_tgl_a = substr($tgl, 0, 2);
$txt_tgl_b = substr($tgl, 3, 2);
$txt_tgl_c = substr($tgl, 6, 4);
$tgl = $txt_tgl_c.'-'.$txt_tgl_b.'-'.$txt_tgl_a;
$sOrg = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$kdPbrk."' ";
$qOrg = mysql_query($sOrg);
$rOrg = mysql_fetch_assoc($qOrg);
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rOrg['induk']."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
if ('0' === $kdBrg) {
    $strx = 'select * from '.$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."' order by tanggal asc";
    $stream .= "\r\n                        <table>\r\n                        <tr><td colspan=12 align=center>".$_SESSION['lang']['laporanPabrikTimbangan']."</td></tr>\r\n                        <tr><td colspan=3>".$_SESSION['lang']['pt'].' : '.$namapt.'</td></tr>';
    $stream .= '<tr><td colspan=3>'.$_SESSION['lang']['tanggal'].' : '.$tgl."</td></tr>\r\n                        <tr><td colspan=3>".$_SESSION['lang']['kdpabrik'].' : '.$kdPbrk."</td></tr>\r\n                        <tr><td colspan=3>&nbsp;</td></tr>\r\n                        </table>\r\n                        <table border=1>\r\n                                                <tr>\r\n                                                  <td bgcolor=#DEDEDE align=center>No.</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>\r\n                                                   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noTiket']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodenopol']."</td>                                             <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratMasuk']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratKeluar']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratnormal']."</td>\t\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammasuk']."</td>\t\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamkeluar']."</td>                                                   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>\r\n                                                </tr>";
    $resx = mysql_query($strx);
    $no = 0;
    while ($barx = mysql_fetch_assoc($resx)) {
        ++$no;
        $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$barx['kodebarang']."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        if ('' !== $barx['kodecustomer']) {
            if ('40000001' === $barx['kodebarang'] || '40000005' === $barx['kodebarang'] || '40000002' === $barx['kodebarang'] || '40000004' === $barx['kodebarang']) {
                $sKontrak = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$barx['nokontrak']."'";
                $qKontrak = mysql_query($sKontrak);
                $rKontrak = mysql_fetch_assoc($qKontrak);
                $sSupp = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'";
                $qSupp = mysql_query($sSupp);
                $rSupp = mysql_fetch_assoc($qSupp);
                $hsl = $rSupp['namacustomer'];
            } else {
                if ('40000003' === $barx['kodebarang']) {
                    $sSupp = 'select namasupplier  from '.$dbname.".log_5supplier where supplierid='".$barx['kodecustomer']."'";
                    $qSupp = mysql_query($sSupp);
                    $rSupp = mysql_fetch_assoc($qSupp);
                    $hsl = $rSupp['namasupplier'];
                }
            }
        }

        $rTRP = '';
        $sTRP = 'select TRPNAME  from '.$dbname.".pabrik_transporter where TRPCODE='".$barx['trpcode']."'";
        $qTRP = mysql_query($sTRP);
        $rTRP = mysql_fetch_assoc($qTRP);
        $stream .= "\t<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$rBrg['namabarang']."</td>\r\n                                <td>".$barx['notransaksi']."</td>\r\n                                <td>".$barx['nokendaraan']."</td>\r\n                                <td>".number_format($barx['beratmasuk'], 2)."</td>\r\n                                <td>".number_format($barx['beratkeluar'], 2)."</td>\r\n                                <td>".number_format($barx['beratbersih'], 2)."</td>\r\n                                <td>".$barx['jammasuk']."</td>\r\n                                <td>".$barx['jamkeluar']."</td>                                <td>".$hsl."</td>\r\n                                <td>".$barx['supir']."</td>\r\n                                <td>".$barx['brondolan']."</td>\t\r\n                                </tr>";
        $totBeratMsk += $barx['beratmasuk'];
        $totBeratKlr += $barx['beratkeluar'];
        $totBeratBrs += $barx['beratbersih'];
        $totBrondolan += $barx['brondolan'];
    }
    $stream .= '<tr class=rowcontent><td colspan=4>Total</td><td>'.$totBeratMsk.'</td><td>'.$totBeratKlr.'</td><td>'.$totBeratBrs.'</td><td colspan=4></td><td>'.$totBrondolan.'</td></tr>';
} else {
    if ('0' !== $kdBrg) {
        $strx = 'select * from '.$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."' and kodebarang='".$kdBrg."' order by tanggal asc";
        $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        $stream .= "\r\n                        <table>\r\n                        <tr><td colspan=12 align=center>".$_SESSION['lang']['laporanPabrikTimbangan']."</td></tr>\r\n                        <tr><td colspan=3>".$_SESSION['lang']['pt'].' : '.$namapt.'</td></tr>';
        $stream .= '<tr><td colspan=3>'.$_SESSION['lang']['tanggal'].' : '.$tgl."</td></tr>\r\n                        <tr><td colspan=3>".$_SESSION['lang']['kdpabrik'].' : '.$kdPbrk."</td></tr>\r\n                        <tr><td colspan=3>".$_SESSION['lang']['namabarang'].' : '.$rBrg['namabarang']."</td></tr>\r\n                        <tr><td colspan=3>&nbsp;</td></tr>\r\n                        </table>\r\n                        <table border=1>\r\n                                                <tr>\r\n                                                  <td bgcolor=#DEDEDE align=center>No.</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noTiket']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodenopol']."</td>                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratMasuk']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratKeluar']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratnormal']."</td>\t\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammasuk']."</td>\t\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamkeluar']."</td><td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>\r\n                                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>\r\n                                                </tr>";
        $resx = mysql_query($strx);
        $no = 0;
        while ($barx = mysql_fetch_assoc($resx)) {
            ++$no;
            if ('' !== $barx['kodecustomer']) {
                if ('40000001' === $barx['kodebarang'] || '40000005' === $barx['kodebarang'] || '40000002' === $barx['kodebarang'] || '40000004' === $barx['kodebarang']) {
                    $sKontrak = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$barx['nokontrak']."'";
                    $qKontrak = mysql_query($sKontrak);
                    $rKontrak = mysql_fetch_assoc($qKontrak);
                    $sSupp = 'select namacustomer as namasupplier from '.$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'";
                    $qSupp = mysql_query($sSupp);
                    $rSupp = mysql_fetch_assoc($qSupp);
                } else {
                    if ('40000003' === $barx['kodebarang']) {
                        $sSupp = 'select namasupplier  from '.$dbname.".log_5supplier where supplierid='".$barx['kodecustomer']."'";
                        $qSupp = mysql_query($sSupp);
                        $rSupp = mysql_fetch_assoc($qSupp);
                    }
                }
            }

            $rTRP = '';
            $sTRP = 'select TRPNAME  from '.$dbname.".pabrik_transporter where TRPCODE='".$barx['trpcode']."'";
            $qTRP = mysql_query($sTRP);
            $rTRP = mysql_fetch_assoc($qTRP);
            $stream .= "\t<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$barx['notransaksi']."</td>\r\n                                <td>".$barx['nokendaraan']."</td> <td>".number_format($barx['beratmasuk'], 2)."</td>\r\n                                <td>".number_format($barx['beratkeluar'], 2)."</td>\r\n                                <td>".number_format($barx['beratbersih'], 2)."</td>\t\t\t\t\r\n                                <td>".$barx['jammasuk']."</td>\r\n                                <td>".$barx['jamkeluar']."</td><td>".$rSupp['namasupplier']."</td>\r\n                                <td>".$barx['supir']."</td>\r\n                                <td>".$barx['brondolan']."</td>\t\r\n                                </tr>";
            $totBeratMsk += $barx['beratmasuk'];
            $totBeratKlr += $barx['beratkeluar'];
            $totBeratBrs += $barx['beratbersih'];
            $totBrondolan += $barx['brondolan'];
        }
        $stream .= '<tr class=rowcontent><td colspan=3>Total</td><td>'.$totBeratMsk.'</td><td>'.$totBeratKlr.'</td><td>'.$totBeratBrs.'</td><td colspan=5></td><td>'.$totBrondolan.'</td></tr>';
    }
}

$stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
$nop_ = 'ReportWB';
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>