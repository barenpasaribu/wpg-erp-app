<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$kdBrg = $_POST['kdBrg'];
$kdPbrk = $_POST['kdPbrk'];
$tgl = $_POST['tgl'];
$txt_tgl_a = substr($tgl, 0, 2);
$txt_tgl_b = substr($tgl, 3, 2);
$txt_tgl_c = substr($tgl, 6, 4);
$tgl = $txt_tgl_c.'-'.$txt_tgl_b.'-'.$txt_tgl_a;
switch ($proses) {
    case 'getData':
        if ('0' === $kdBrg) {
            echo "<table cellspacing=1 border=0 class=sortable>\r\n\t\t\t\t<thead class=rowheader>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>No.</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['noTiket']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodenopol']."</td>\r\n                    <!--td>".$_SESSION['lang']['transportasi']."</td-->    \r\n\t\t\t\t\t<td>".$_SESSION['lang']['beratMasuk']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['beratKeluar']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['beratnormal']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jammasuk']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jamkeluar']."</td><td>".$_SESSION['lang']['supplier']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['sopir']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['brondolan']."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t</thead>\r\n\t\t\t\t<tbody>\r\n\t\t\t";
            $sTrans = 'select * from '.$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."'";
            $qTrans = mysql_query($sTrans);
            while ($rTrans = mysql_fetch_assoc($qTrans)) {
                ++$no;
                $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$rTrans['kodebarang']."'";
                $qBrg = mysql_query($sBrg);
                $rBrg = mysql_fetch_assoc($qBrg);
                if ('40000001' === $rTrans['kodebarang'] || '40000002' === $rTrans['kodebarang'] || '40000004' === $rTrans['kodebarang']) {
                    $sKontrak = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rTrans['nokontrak']."'";
                    $qKontrak = mysql_query($sKontrak);
                    $rKontrak = mysql_fetch_assoc($qKontrak);
                    $sSupp = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'";
                    $qSupp = mysql_query($sSupp);
                    $rSupp = mysql_fetch_assoc($qSupp);
                    $hsl = $rSupp['namacustomer'];
                } else {
                    if ('40000003' === $rTrans['kodebarang']) {
                        $sSupp = 'select namasupplier  from '.$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'";
                        $qSupp = mysql_query($sSupp);
                        $rSupp = mysql_fetch_assoc($qSupp);
                        $hsl = $rSupp['namasupplier'];
                    }
                }

                echo "<tr class=rowcontent align=right>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$rBrg['namabarang']."</td>\r\n\t\t\t\t<td>".$rTrans['notransaksi']."</td>\r\n\t\t\t\t<td>".$rTrans['nokendaraan']."</td>\r\n                                <!--td>".$rTRP['TRPNAME']."</td-->\r\n\t\t\t\t<td align=\"right\">".number_format($rTrans['beratmasuk'], 2)."</td>\r\n\t\t\t\t<td align=\"right\">".number_format($rTrans['beratkeluar'], 2)."</td>\r\n\t\t\t\t<td align=\"right\">".number_format($rTrans['beratbersih'], 2)."</td>\r\n\t\t\t\t<td>".$rTrans['jammasuk']."</td>\r\n\t\t\t\t<td>".$rTrans['jamkeluar']."</td><td>".$hsl."</td>\r\n\t\t\t\t<td>".$rTrans['supir']."</td>\r\n\t\t\t\t<td align=\"right\">".number_format($rTrans['brondolan'], 2)."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t";
                $totBeratMsk += $rTrans['beratmasuk'];
                $totBeratKlr += $rTrans['beratkeluar'];
                $totBeratBrs += $rTrans['beratbersih'];
                $totBrondolan += $rTrans['brondolan'];
            }
            echo '<tr class=rowcontent><td colspan=4>Total</td><td align="right">'.number_format($totBeratMsk, 2).'</td><td align="right">'.number_format($totBeratKlr, 2).'</td><td align="right">'.number_format($totBeratBrs, 2).'</td><td colspan=4></td><td align="right">'.number_format($totBrondolan, 2).'</td></tr>';
            echo '</tbody></table>';
        } else {
            if ('0' !== $kdBrg) {
                echo "<table cellspacing=1 border=0 class=sortable>\r\n\t\t\t\t<thead class=rowheader>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>No.</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['noTiket']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodenopol']."</td>\r\n                    <td>".$_SESSION['lang']['beratMasuk']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['beratKeluar']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['beratnormal']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jammasuk']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jamkeluar']."</td><td>".$_SESSION['lang']['supplier']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['sopir']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['brondolan']."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t</thead>\r\n\t\t\t\t<tbody>\r\n\t\t\t";
                $sTrans = 'select * from '.$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."' and kodebarang='".$kdBrg."'";
                $qTrans = mysql_query($sTrans);
                while ($rTrans = mysql_fetch_assoc($qTrans)) {
                    ++$no;
                    $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$rTrans['kodebarang']."'";
                    $qBrg = mysql_query($sBrg);
                    $rBrg = mysql_fetch_assoc($qBrg);
                    if ('40000001' === $rTrans['kodebarang'] || '40000002' === $rTrans['kodebarang'] || '40000004' === $rTrans['kodebarang']) {
                        $sKontrak = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rTrans['nokontrak']."'";
                        $qKontrak = mysql_query($sKontrak);
                        $rKontrak = mysql_fetch_assoc($qKontrak);
                        $sSupp = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'";
                        $qSupp = mysql_query($sSupp);
                        $rSupp = mysql_fetch_assoc($qSupp);
                        $hsl = $rSupp['namacustomer'];
                    } else {
                        if ('40000003' === $rTrans['kodebarang']) {
                            $sSupp = 'select namasupplier  from '.$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'";
                            $qSupp = mysql_query($sSupp);
                            $rSupp = mysql_fetch_assoc($qSupp);
                            $hsl = $rSupp['namasupplier'];
                        }
                    }

                    $rTRP = '';
                    $sTRP = 'select TRPNAME  from '.$dbname.".pabrik_transporter where TRPCODE='".$rTrans['trpcode']."'";
                    $qTRP = mysql_query($sTRP);
                    $rTRP = mysql_fetch_assoc($qTRP);
                    echo "<tr class=rowcontent>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$rTrans['notransaksi']."</td>\r\n\t\t\t\t<td>".$rTrans['nokendaraan']."</td>\r\n                <td align=right>".number_format($rTrans['beratmasuk'], 2)."</td>\r\n\t\t\t\t<td align=right>".number_format($rTrans['beratkeluar'], 2)."</td>\r\n\t\t\t\t<td align=right>".number_format($rTrans['beratbersih'], 2)."</td>\r\n\t\t\t\t<td align=center>".$rTrans['jammasuk']."</td>\r\n\t\t\t\t<td align=center>".$rTrans['jamkeluar']."</td><td>".$hsl."</td>\r\n\t\t\t\t<td>".$rTrans['supir']."</td>\r\n\t\t\t\t<td align=right>".number_format($rTrans['brondolan'], 2)."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t";
                    $totBeratMsk += $rTrans['beratmasuk'];
                    $totBeratKlr += $rTrans['beratkeluar'];
                    $totBeratBrs += $rTrans['beratbersih'];
                    $totBrondolan += $rTrans['brondolan'];
                }
                echo '<tr class=rowcontent><td colspan=3>Total</td><td  align="right">'.number_format($totBeratMsk, 2).'</td><td  align="right">'.number_format($totBeratKlr, 2).'</td><td  align="right">'.number_format($totBeratBrs, 2).'</td><td colspan=4></td><td align=right>'.number_format($totBrondolan, 2).'</td></tr>';
                echo '</tbody></table>';
            }
        }

        break;
    default:
        break;
}

?>