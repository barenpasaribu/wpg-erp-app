<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/zLib.php';
$pt = $_POST['pt'];
$unit = $_POST['unit'];
$periode = $_POST['periode'];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$kodelaporan = 'BALANCE SHEET';
$periodesaldo = str_replace('-', '', $periode);
$tahun = substr($periodesaldo, 0, 4);
$tahunlalu = $tahun - 1;
$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
if ('' === $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$stream = "<table class=sortable border=0 cellspacing=1>\r\n          <thead>\r\n           <tr class=rowheader>\r\n            <td>Keterangan</td>\r\n            <td align=center>".numToMonth(12, 'E').' '.$tahunlalu."</td>    \r\n            <td align=center>".numToMonth(1, 'E')."</td>                \r\n            <td align=center>".numToMonth(2, 'E')."</td>\r\n            <td align=center>".numToMonth(3, 'E')."</td>\r\n            <td align=center>".numToMonth(4, 'E')."</td>\r\n            <td align=center>".numToMonth(5, 'E')."</td>\r\n            <td align=center>".numToMonth(6, 'E')."</td>\r\n            <td align=center>".numToMonth(7, 'E')."</td>\r\n            <td align=center>".numToMonth(8, 'E')."</td>\r\n            <td align=center>".numToMonth(9, 'E')."</td>\r\n            <td align=center>".numToMonth(10, 'E')."</td>\r\n            <td align=center>".numToMonth(11, 'E')."</td>\r\n            <td align=center>".numToMonth(12, 'E')."</td>\r\n            </tr>\r\n         </thead><tbody>";
while ($bar = mysql_fetch_object($res)) {
    $tampildari = $bar->variableoutput;
    if ('Header' === $bar->tipe) {
        if ('ID' === $_SESSION['language']) {
            $stream .= '<tr class=rowcontent><td colspan=14><b>'.$bar->keterangandisplay.'</b></td></tr>';
        } else {
            $stream .= '<tr class=rowcontent><td colspan=14><b>'.$bar->keterangandisplay1.'</b></td></tr>';
        }
    } else {
        $st12 = "select sum(awal01) as awal01, sum(awal02) as awal02, sum(awal03) as awal03, sum(awal04) as awal04, sum(awal05) as awal05, sum(awal06) as awal06, sum(awal07) as awal07, sum(awal08) as awal08, sum(awal09) as awal09, sum(awal10) as awal10, sum(awal11) as awal11, sum(awal12) as awal12,sum(debet12) as debet12,sum(kredit12) as kredit12 from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' \r\n               and '".$bar->noakunsampai."' and ".$where." and periode like '".$tahun."%'";
        $res12 = mysql_query($st12);
        $awal01 = 0;
        $awal02 = 0;
        $awal03 = 0;
        $awal04 = 0;
        $awal05 = 0;
        $awal06 = 0;
        $awal07 = 0;
        $awal08 = 0;
        $awal09 = 0;
        $awal10 = 0;
        $awal11 = 0;
        $awal12 = 0;
        $debet12 = 0;
        $kredit12 = 0;
        while ($ba12 = mysql_fetch_object($res12)) {
            $awal01 = $ba12->awal01;
            $awal02 = $ba12->awal02;
            $awal03 = $ba12->awal03;
            $awal04 = $ba12->awal04;
            $awal05 = $ba12->awal05;
            $awal06 = $ba12->awal06;
            $awal07 = $ba12->awal07;
            $awal08 = $ba12->awal08;
            $awal09 = $ba12->awal09;
            $awal10 = $ba12->awal10;
            $awal11 = $ba12->awal11;
            $awal12 = $ba12->awal12;
            $debet12 = $ba12->debet12;
            $kredit12 = $ba12->kredit12;
        }
        if ('Total' === $bar->tipe) {
            $stream .= '<tr class=rowcontent>';
            if ('ID' === $_SESSION['language']) {
                $stream .= '<td><b>'.$bar->keterangandisplay.'</b></td>';
            } else {
                $stream .= '<td><b>'.$bar->keterangandisplay1.'</b></td>';
            }

            $stream .= '<td align=right><b>'.number_format($awal01,2)."</b></td>\r\n                        <td align=right><b>".number_format($awal02,2)."</b></td>    \r\n                        <td align=right><b>".number_format($awal03,2)."</b></td>\r\n                        <td align=right><b>".number_format($awal04,2)."</b></td>    \r\n                        <td align=right><b>".number_format($awal05,2)."</b></td>\r\n                        <td align=right><b>".number_format($awal06,2)."</b></td>    \r\n                        <td align=right><b>".number_format($awal07,2)."</b></td>\r\n                        <td align=right><b>".number_format($awal08,2)."</b></td>    \r\n                        <td align=right><b>".number_format($awal09,2)."</b></td>\r\n                        <td align=right><b>".number_format($awal10,2)."</b></td>    \r\n                        <td align=right><b>".number_format($awal11,2)."</b></td>\r\n                        <td align=right><b>".number_format($awal12,2)."</b></td>    \r\n                        <td align=right><b>".number_format($awal12+$debet12-$kredit12,2)."</b></td>    \r\n                     </tr>\r\n                     ";
        } else {
            $stream .= '<tr class=rowcontent>';
            if ('ID' === $_SESSION['language']) {
                $stream .= '<td>'.$bar->keterangandisplay.'</td>';
            } else {
                $stream .= '<td>'.$bar->keterangandisplay1.'</td>';
            }

            $stream .= '<td align=right>'.number_format($awal01,2)."</td>\r\n                    <td align=right>".number_format($awal02,2)."</td>    \r\n                    <td align=right>".number_format($awal03,2)."</td>\r\n                    <td align=right>".number_format($awal04,2)."</td>    \r\n                    <td align=right>".number_format($awal05,2)."</td>\r\n                    <td align=right>".number_format($awal06,2)."</td>    \r\n                    <td align=right>".number_format($awal07,2)."</td>\r\n                    <td align=right>".number_format($awal08,2)."</td>    \r\n                    <td align=right>".number_format($awal09,2)."</td>\r\n                    <td align=right>".number_format($awal10,2)."</td>    \r\n                    <td align=right>".number_format($awal11,2)."</td>\r\n                    <td align=right>".number_format($awal12,2)."</td>    \r\n                    <td align=right>".number_format($awal12+$debet12-$kredit12,2)."</td>    \r\n                     </tr>";
        }
    }
}
$stream .= '</tbody></tfoot></tfoot></table>';
echo $stream;

?>