<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$optTipePot = makeOption($dbname, 'sdm_ho_component', 'id,name');
('' == $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
('' == $_POST['kodeorg'] ? ($kodeorg = $_GET['kodeorg']) : ($kodeorg = $_POST['kodeorg']));
('' == $_POST['periodegaji'] ? ($periodegaji = $_GET['periodegaji']) : ($periodegaji = $_POST['periodegaji']));
('' == $_POST['tipepotongan'] ? ($tipepotongan = $_GET['tipepotongan']) : ($tipepotongan = $_POST['tipepotongan']));
$arrNmtp = ['0', 'Staff', 3 => 'KBL', 4 => 'KHT'];
switch ($method) {
    case 'excel':
        $iHead = 'select * from '.$dbname.".sdm_potonganht \r\n\t\twhere kodeorg='".$kodeorg."' and periodegaji='".$periodegaji."' and tipepotongan='".$tipepotongan."'";
        $nHead = mysql_query($iHead);
        $dHead = mysql_fetch_assoc($nHead);
        $stream = 'Kode Organisasi : '.$kodeorg.'<br>';
        $stream .= 'Periode : '.$periodegaji.'<br>';
        $stream .= 'Tipe Potongan : '.$optTipePot[$tipepotongan].'<br>';
        $stream .= "<br /><table class=sortable border=1 cellspacing=1>\r\n\t\t\t <thead>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nourut']."</td> \r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nik']."</td> \r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['namakaryawan']."</td> \r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['tipekaryawan']."</td> \r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['lokasitugas']."</td> \r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['potongan']."</td> \r\n\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t\t</tr>";
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
            $iDet = 'select * from '.$dbname.".sdm_potongandt where periodegaji='".$periodegaji."' ".'and kodeorg in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n                      and tipepotongan='".$tipepotongan."'  order by nik asc";
        } else {
            $iDet = 'select * from '.$dbname.".sdm_potongandt where periodegaji='".$periodegaji."' "."and kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n                      and tipepotongan='".$tipepotongan."'  order by nik asc";
        }

        $nDet = mysql_query($iDet);
        while ($dDet = mysql_fetch_assoc($nDet)) {
            $wh = "karyawanid='".$dDet['nik']."'";
            $optNik = makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $wh);
            $optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $wh);
            $optTp = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $wh);
            ++$no;
            $stream .= "<tr>\r\n\t\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t\t<td>'".$optNik[$dDet['nik']]."</td>\r\n\t\t\t\t\t\t<td>".$optNm[$dDet['nik']]."</td>\r\n\t\t\t\t\t\t<td>".$arrNmtp[$optTp[$dDet['nik']]]."</td>\r\n\t\t\t\t\t\t<td>".$dDet['kodeorg']."</td>\r\n\t\t\t\t\t\t<td>".number_format($dDet['jumlahpotongan'])."</td>\r\n\t\t\t\t\t\t<td>".$dDet['keterangan']."</td>\r\n\t\t\t\t\t</tr>";
            $tot += $dDet['jumlahpotongan'];
        }
        $stream .= "<tr>\r\n\t\t\t\t\t\t<td colspan=5>Total</td>\r\n\t\t\t\t\t\t<td colspan=1>".number_format($tot)."</td>\r\n\t\t\t\t\t</tr></table>";
        $stream .= '</tbody></table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'Laporan_Potongan_'.$dHead['kode'];
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n\twindow.location='tempExcel/".$nop_.".xls.gz';\r\n\t</script>";

        break;
}

?>