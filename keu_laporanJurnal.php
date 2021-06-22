<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
$periode1 = $_POST['periode1'];
$revisi = $_POST['revisi'];
if (4 < (int) (str_replace('-', '', $periode1)) - (int) (str_replace('-', '', $periode))) {
    exit('error: periode terlalu panjang');
}

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
$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
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

        echo "<tr class=rowcontent>\r\n            <td align=center  style='width:50px;'>".$no."</td>\r\n            <td style='width:250px;'>".$bar->nojurnal."</td>\r\n\t\t\t<td style='width:250px;'>".$bar->nodok."</td>\r\n            <td style='width:80px;'>".tanggalnormal($bar->tanggal)."</td>\r\n            <td align=center style='width:60px;'>".$bar->kodeorg."</td>\r\n            <td style='width:60px;'>".$bar->noakun."</td>\r\n            <td style='width:200px;'>".$bar->namaakun."</td>\r\n            <td style='width:240px;'>".$bar->keterangan."</td>\r\n            <td align=right style='width:100px;'>".number_format($debet, 2)."</td>\r\n            <td align=right style='width:100px;'>".number_format($kredit, 2)."</td>\r\n            <td align=center style='width:200px;'>".$bar->noreferensi."</td>    \r\n            <td align=center style='width:80px;'>".$bar->kodeblok."</td>\r\n            <td align=center style='width:60px;'>".$tahuntanam[$bar->kodeblok]."</td>\r\n            <td align=center style='width:30px;'>".$bar->revisi."</td>\r\n            </tr>";
        $tdebet += $debet;
        $tkredit += $kredit;
    }
    echo "<tr class=rowtitle>\r\n        <td align=center colspan=8>Total</td>\r\n        <td align=right width=100>".number_format($tdebet, 2)."</td>\r\n        <td align=right width=100>".number_format($tkredit, 2)."</td>\r\n        <td align=center colspan=4></td>\r\n        </tr>";
}

?>