<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$proses = $_GET['proses'];
switch ($proses) {
    case 'preview':
        $param = $_POST;

        break;
    case 'excel':
        $param = $_GET;

        break;
}
$bulanini = $param['periode'];
$qwe = explode('-', $bulanini);
list($tahunlalu, $bulanlalu) = $qwe;
if ('01' === $bulanlalu) {
    --$tahunlalu;
    $bulanlalu = '12';
} else {
    --$bulanlalu;
}

$bulanlalu = str_pad($bulanlalu, 2, '0', STR_PAD_LEFT);
$bulankemarin = $tahunlalu.'-'.$bulanlalu;
$sbjrlalu = 'select blok, sum(jjg) as jjg, sum(kgwb) as kgwb from '.$dbname.".kebun_spb_vw\r\n        where notiket IS NOT NULL and tanggal like '".$bulankemarin."%'\r\n        group by blok";
$qbjrlalu = mysql_query($sbjrlalu) ;
while ($rbjrlalu = mysql_fetch_assoc($qbjrlalu)) {
    $beje = $rbjrlalu['kgwb'] / $rbjrlalu['jjg'];
    $bjrlalu[$rbjrlalu['blok']] = $beje;
}
$str = 'select kodeorg,tahuntanam,kodeorg from '.$dbname.".setup_blok where kodeorg like '".$param['idKebun']."%'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tt[$bar->kodeorg] = $bar->tahuntanam;
    $blok[] = $bar->kodeorg;
}
$str = 'select sum(hasilkerja) as jjgpanen,kodeorg,tanggal from '.$dbname.".kebun_prestasi_vw where tanggal like '".$param['periode']."%'\r\n                  and kodeorg like '".$param['idKebun']."%' group by tanggal,kodeorg";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jjgpanen[$bar->tanggal][$bar->kodeorg] = $bar->jjgpanen;
}
$str = 'select sum(jjg) as jjgangkut,blok,sum(totalkg) as kgwb, tanggal,sum(brondolan) as brd from '.$dbname.".kebun_spb_vw where tanggal like '".$param['periode']."%'\r\n                  and kodeorg = '".$param['idKebun']."' group by tanggal,blok";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jjgangkut[$bar->tanggal][$bar->blok] = $bar->jjgangkut;
    $brdkbn[$bar->tanggal][$bar->blok] = $bar->brd;
    $berat[$bar->tanggal][$bar->blok] = $bar->kgwb;
}
$str = 'select blok,jjg,tanggal,notiket from '.$dbname.".kebun_spb_vw where tanggal like '".$param['periode']."%'\r\n                  and kodeorg = '".$param['idKebun']."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $spbk[$bar->notiket][$bar->tanggal][$bar->blok] = $bar->jjg;
    $spbktg[$bar->notiket] = $bar->tanggal;
}
$str = 'select notransaksi,brondolan as bb from '.$dbname.".pabrik_timbangan\r\n                  where notransaksi in(select notiket from ".$dbname.".kebun_spb_vw where tanggal like '".$param['periode']."%'\r\n                  and kodeorg = '".$param['idKebun']."')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tiket[$bar->notransaksi] = $bar->bb;
}
foreach ($tiket as $tik => $nx) {
    foreach ($spbk[$tik] as $tg) {
        $tjg = array_sum($tg);
        foreach ($tg as $bl => $jg) {
            $brd[$spbktg[$tik]][$bl] += $jg / $tjg * $tiket[$tik];
        }
    }
}
$stream .= 'Produksi_Per_Blok '.$param['idKebun'].' Periode:'.$param['periode']."\r\n         <table class=sortable border=0 cellspacing=1>\r\n          <thead>\r\n          <tr class=rowheader>\r\n             <td>No</td>\r\n             <td>".$_SESSION['lang']['tanggal']."</td>\r\n             <td>".$_SESSION['lang']['blok']."</td>\r\n             <td>".$_SESSION['lang']['thntnm']."</td>\r\n             <td>".$_SESSION['lang']['tbs'].' '.$_SESSION['lang']['panen']."(JJG)</td>\r\n             <td>".$_SESSION['lang']['pengiriman']."(JJG)</td>\r\n             <td>Netto(Kg)</td>           \r\n             <td>".$_SESSION['lang']['bjr']." Actual</td>           \r\n             <td>".$_SESSION['lang']['bjr'].' '.$_SESSION['lang']['blnlalu']."</td>           \r\n          </tr></thead><tbody>\r\n          ";
$mk = mktime(0, 0, 0, substr($param['periode'], 5, 2), 15, substr($param['periode'], 0, 4));
$jhari = date('j', $mk);
$a = 0;
for ($x = 1; $x <= $jhari; ++$x) {
    foreach ($blok as $ki => $bl) {
        $tttt = str_pad($x, 2, '0', STR_PAD_LEFT);
        if (0 < $jjgpanen[$param['periode'].'-'.$tttt][$bl] || 0 < $jjgangkut[$param['periode'].'-'.$tttt][$bl] || 0 < $brdkbn[$param['periode'].'-'.$tttt][$bl] || 0 < $brd[$param['periode'].'-'.$tttt][$bl]) {
            ++$a;
            $bjraktual = $berat[$param['periode'].'-'.$tttt][$bl] / $jjgangkut[$param['periode'].'-'.$tttt][$bl];
            if ($bjraktual < $bjrlalu[$bl]) {
                $merah = ' bgcolor=red';
            } else {
                $merah = '';
            }

            $stream .= "<tr class=rowcontent>\r\n                           <td>".$a."</td>\r\n                           <td>".$param['periode'].'-'.$tttt."</td>\r\n                           <td>".$bl."</td>\r\n                           <td>".$tt[$bl]."</td>\r\n                            <td align=right>".number_format($jjgpanen[$param['periode'].'-'.$tttt][$bl])."</td>\r\n                            <td align=right>".number_format($jjgangkut[$param['periode'].'-'.$tttt][$bl])."</td>    \r\n                           <td align=right>".number_format($berat[$param['periode'].'-'.$tttt][$bl], 2)."</td> \r\n                           <td align=right>".number_format($bjraktual, 2)."</td> \r\n                           <td align=right ".$merah.'>'.number_format($bjrlalu[$bl], 2)."</td> \r\n                     </tr>";
            $tjp += $jjgpanen[$param['periode'].'-'.$tttt][$bl];
            $tja += $jjgangkut[$param['periode'].'-'.$tttt][$bl];
            $tbk += $brdkbn[$param['periode'].'-'.$tttt][$bl];
            $tb += $brd[$param['periode'].'-'.$tttt][$bl];
            $tberat += $berat[$param['periode'].'-'.$tttt][$bl];
        }
    }
}
$stream .= "</tbody><tfoot>\r\n                    <tr class=rowcontent>\r\n                       <td colspan=4>TOTAL</td>\r\n                       <td align=right>".number_format($tjp, 2)."</td>\r\n                       <td align=right>".number_format($tja, 2)."</td>\r\n                       <td align=right>".number_format($tberat, 2)."</td>\r\n                       <td></td><td></td>    \r\n                       </tr align=right>\r\n                 </tfoot></table>Pastikan SPB sudah diinput dengan Benar/Make sure all FFB Transport document has been confirmed";
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $nop_ = 'produksiperblok_'.$param['unit'].'_'.$param['periode'];
        if (0 < strlen($stream)) {
            $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
            gzwrite($gztralala, $stream);
            gzclose($gztralala);
            echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls.gz';\r\n                    </script>";
        }

        break;
}

?>