<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$revisi = $_GET['revisi'];
if ('' !== $gudang) {
    $str = 'select a.*,b.namaakun from '.$dbname.".keu_jurnaldt_vw a\r\n        left join ".$dbname.".keu_5akun b\r\n        on a.noakun=b.noakun\r\n        where a.tanggal between '".$periode."-01' and LAST_DAY('".$periode1."-15')\r\n        and a.kodeorg='".$gudang."'\r\n        and a.nojurnal NOT LIKE '%CLSM%'\r\n        and a.revisi<='".$revisi."'\r\n        order by a.nojurnal \r\n        ";
} else {
    $str = 'select a.*,b.namaakun from '.$dbname.".keu_jurnaldt_vw a\r\n        left join ".$dbname.".keu_5akun b\r\n        on a.noakun=b.noakun\r\n        where a.tanggal between '".$periode."-01' and LAST_DAY('".$periode1."-15')\r\n        and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' \r\n        and a.nojurnal NOT LIKE '%CLSM%'\r\n        and a.revisi<='".$revisi."'\r\n        and length(kodeorganisasi)=4)                    \r\n        order by a.nojurnal \r\n        ";
}

$aresta = 'SELECT kodeorg, tahuntanam FROM '.$dbname.".setup_blok\r\n    ";
$query = mysql_query($aresta);
while ($res = mysql_fetch_assoc($query)) {
    $tahuntanam[$res['kodeorg']] = $res['tahuntanam'];
}
$stream = "<table border=1>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['nojurnal']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['organisasi']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['debet']."</td>\r\n\t\t\t  <td bgcolor='#dedede'>".$_SESSION['lang']['kredit']."</td>\r\n                          <td bgcolor='#dedede'>".$_SESSION['lang']['noreferensi']."</td>\r\n                          <td bgcolor='#dedede'>".$_SESSION['lang']['kodevhc']."</td>\r\n                          <td bgcolor='#dedede'>".$_SESSION['lang']['kodeblok']."</td>\r\n                          <td bgcolor='#dedede'>".$_SESSION['lang']['tahuntanam']."</td>\r\n                          <td bgcolor='#dedede'>".$_SESSION['lang']['afdeling']."</td>    \r\n                          <td bgcolor='#dedede'>".$_SESSION['lang']['revisi']."</td>    \r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container>";
$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    $stream .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $debet = 0;
        $kredit = 0;
        if (0 < $bar->jumlah) {
            $debet = $bar->jumlah;
        } else {
            $kredit = $bar->jumlah * -1;
        }

        $stream .= "<tr class=rowcontent>\r\n              <td align=center width=20>".$no."</td>\r\n              <td>".$bar->nojurnal."</td>\r\n              <td>".tanggalnormal($bar->tanggal)."</td>\r\n              <td align=center>".$bar->kodeorg."</td>\r\n              <td>".$bar->noakun."</td>\r\n              <td>".$bar->namaakun."</td>\r\n              <td>".$bar->keterangan."</td>\r\n              <td align=right width=100>".number_format($debet, 2)."</td>\r\n              <td align=right width=100>".number_format($kredit, 2)."</td>\r\n              <td>".$bar->noreferensi." </td>\r\n              <td>".$bar->kodevhc." </td>\r\n              <td>".$bar->kodeblok." </td>\r\n              <td>".$tahuntanam[$bar->kodeblok]." </td>\r\n              <td>".substr($bar->kodeblok, 0, 6)."</td>    \r\n              <td>".$bar->revisi." </td>\r\n             </tr>";
        $tdebet += $debet;
        $tkredit += $kredit;
    }
    $stream .= "<tr bgcolor='#dedede'>\r\n        <td align=center colspan=7>Total</td>\r\n        <td align=right width=100>".number_format($tdebet, 2)."</td>\r\n        <td align=right width=100>".number_format($tkredit, 2)."</td>\r\n        <td align=center colspan=6></td>\r\n        </tr>";
}

$stream .= "</tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>";
$stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
$qwe = date('YmdHms');
$nop_ = 'LP_JRNL_'.$gudang.$periode.'rev'.$revisi.'___'.$qwe;
if (0 < strlen($stream)) {
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>