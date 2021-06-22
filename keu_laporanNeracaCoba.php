<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
$tahun1 = substr($periode, 0, 4);
$bulan1 = substr($periode, 5, 2);
$periode1 = $periode;
if ('' === $gudang) {
    $str = 'select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk from '.$dbname.".keu_jurnalsum_vw a\r\n                left join ".$dbname.".keu_5akun b\r\n                on a.noakun=b.noakun\r\n                left join ".$dbname.".organisasi c\r\n                on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n                where c.induk = '".$pt."' and a.periode='".$periode1."'\r\n                order by a.noakun, a.periode \r\n                ";
} else {
    $str = 'select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk from '.$dbname.".keu_jurnalsum_vw a\r\n                left join ".$dbname.".keu_5akun b\r\n                on a.noakun=b.noakun\r\n                left join ".$dbname.".organisasi c\r\n                on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n                where c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."' and a.periode='".$periode1."'\r\n                order by a.noakun, a.periode \r\n                ";
}

$tawal = 0;
$tdebet = 0;
$tkredit = 0;
$tsalak = 0;
if ('' === $periode) {
    $res = mysql_query($str);
    $no = 0;
    if (mysql_num_rows($res) < 1) {
        echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
    } else {
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $periode = date('Y-m-d H:i:s');
            $noakun = $bar->noakun;
            $namaakun = $bar->namaakun;
            $periode = $bar->periode;
            $tahun = substr($periode, 0, 4);
            $bulan = substr($periode, 5, 2);
            $kodeorg = $bar->kodeorg;
            $bussunitcode = $bar->bussunitcode;
            $induk = $bar->induk;
            $debet = $bar->debet;
            $kredit = $bar->kredit;
            $sawal = 0;
            $strx = 'select awal'.$bulan.' from '.$dbname.".keu_saldobulanan where \r\n                              noakun='".$noakun."' and kodeorg='".$bussunitcode."' \r\n                              and periode='".$tahun.$bulan."'";
            $resx = mysql_query($strx);
            while ($barx = mysql_fetch_array($resx)) {
                $sawal = $barx[0];
            }
            echo "<tr class=rowcontent  style='cursor:pointer;'>\r\n                                  <td>".$noakun."</td>\r\n                                  <td>".$namaakun."</td>\r\n                                  <td align=center>".$tahun."</td>\r\n                                  <td align=center>".$bulan."</td>\r\n                                  <td align=center>".$kodeorg."</td>\r\n                                  <td align=center>".$bussunitcode."</td>\r\n                                  <td align=center>".$induk."</td>\r\n                                  <td align=right>".number_format($sawal, 2)."</td>   \r\n                                  <td align=right>".number_format($debet, 2)."</td>\r\n                                  <td align=right>".number_format($kredit, 2)."</td>\r\n                                  <td align=right>".number_format(($sawal + $debet) - $kredit, 2)."</td>    \r\n                                </tr>";
            $tawal += $sawal;
            $tdebet += $debet;
            $tkredit += $kredit;
            $tsalak += ($sawal + $debet) - $kredit;
        }
    }
} else {
    $res = mysql_query($str);
    $res4 = mysql_query($str);
    $no = 0;
    if (mysql_num_rows($res) < 1) {
        echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
    } else {
        echo "<table class=sortable cellspacing=1 border=0 width=100%>\r\n             <thead>\r\n                    <tr>\r\n                          <td align=center width=100>".$_SESSION['lang']['noakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['tahun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['bulan']."</td>\r\n                          <td align=center width=100>".$_SESSION['lang']['organisasi']."</td>\r\n                          <td align=center width=80>".$_SESSION['lang']['unitkerja']."</td>\r\n                          <td align=center width=80>".$_SESSION['lang']['perusahaan']."</td>\r\n                          <td align=center width=80>".$_SESSION['lang']['saldoawal']."</td>    \r\n                          <td align=center width=150>".$_SESSION['lang']['debet']."</td>\r\n                          <td align=center width=150>".$_SESSION['lang']['kredit']."</td>\r\n                          <td align=center width=150>".$_SESSION['lang']['saldoakhir']."</td>                                  \r\n                        </tr>  \r\n                 </thead>\r\n                 <tbody>";
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            if ($bar->noakun < 4000000) {
                $periode = date('Y-m-d H:i:s');
                $noakun = $bar->noakun;
                $namaakun = $bar->namaakun;
                $periode = $bar->periode;
                $tahun = substr($periode, 0, 4);
                $bulan = substr($periode, 5, 2);
                $kodeorg = $bar->kodeorg;
                $induk = $bar->induk;
                $bussunitcode = $bar->bussunitcode;
                $debet = $bar->debet;
                $kredit = $bar->kredit;
                $sawal = 0;
                $strx = 'select awal'.$bulan.' from '.$dbname.".keu_saldobulanan where \r\n                                  noakun='".$noakun."' and kodeorg='".$bussunitcode."' \r\n                                  and periode='".$tahun.$bulan."'";
                $resx = mysql_query($strx);
                while ($barx = mysql_fetch_array($resx)) {
                    $sawal = $barx[0];
                }
                echo "<tr class=rowcontent style='cursor:pointer;'>\r\n                                      <td>".$noakun."</td>\r\n                                      <td>".$namaakun."</td>\r\n                                      <td align=center>".$tahun."</td>\r\n                                      <td align=center>".$bulan."</td>\r\n                                      <td align=center>".$kodeorg."</td>\r\n                                      <td align=center>".$bussunitcode."</td>\r\n                                      <td align=center>".$induk."</td>\r\n                                      <td align=right>".number_format($sawal, 2)."</td>     \r\n                                      <td align=right>".number_format($debet, 2)."</td>\r\n                                      <td align=right>".number_format($kredit, 2)."</td>\r\n                                      <td align=right>".number_format(($sawal + $debet) - $kredit, 2)."</td>    \r\n                            </tr>";
                $tawal += $sawal;
                $tdebet += $debet;
                $tkredit += $kredit;
                $tsalak += ($sawal + $debet) - $kredit;
            }
        }
        echo "<tr class=rowcontent>\r\n                              <td colspan=7 align=center>TOTAL</td>\r\n                              <td align=right>".number_format($tawal, 2)."</td>     \r\n                              <td align=right>".number_format($tdebet, 2)."</td>\r\n                              <td align=right>".number_format($tkredit, 2)."</td>\r\n                              <td align=right>".number_format($tsalak, 2)."</td>\r\n                            </tr></tbody><tfoot></tfoot></table>";
        echo '<br><hr><br>';
        echo "<table class=sortable cellspacing=1 border=0 width=100%>\r\n             <thead>\r\n                    <tr>\r\n                          <td align=center width=100>".$_SESSION['lang']['noakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['tahun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['bulan']."</td>\r\n                          <td align=center width=100>".$_SESSION['lang']['organisasi']."</td>\r\n                          <td align=center width=80>".$_SESSION['lang']['unitkerja']."</td>\r\n                          <td align=center width=80>".$_SESSION['lang']['perusahaan']."</td>\r\n                          <td align=center width=80>".$_SESSION['lang']['saldoawal']."</td>    \r\n                          <td align=center width=150>".$_SESSION['lang']['debet']."</td>\r\n                          <td align=center width=150>".$_SESSION['lang']['kredit']."</td>\r\n                          <td align=center width=150>".$_SESSION['lang']['saldoakhir']."</td>                                  \r\n                        </tr>  \r\n                 </thead>\r\n                 <tbody>";
        $tawal = 0;
        $tdebet = 0;
        $tkredit = 0;
        $tsalak = 0;
        while ($bar = mysql_fetch_object($res4)) {
            if (3999999 < $bar->noakun) {
                ++$no;
                $periode = date('Y-m-d H:i:s');
                $noakun = $bar->noakun;
                $namaakun = $bar->namaakun;
                $periode = $bar->periode;
                $tahun = substr($periode, 0, 4);
                $bulan = substr($periode, 5, 2);
                $kodeorg = $bar->kodeorg;
                $induk = $bar->induk;
                $bussunitcode = $bar->bussunitcode;
                $debet = $bar->debet;
                $kredit = $bar->kredit;
                $sawal = 0;
                $strx = 'select awal'.$bulan.' from '.$dbname.".keu_saldobulanan where \r\n                                  noakun='".$noakun."' and kodeorg='".$bussunitcode."' \r\n                                  and periode='".$tahun.$bulan."'";
                $resx = mysql_query($strx);
                while ($barx = mysql_fetch_array($resx)) {
                    $sawal = $barx[0];
                }
                echo "<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"detailJurnal(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">\r\n                                      <td>".$noakun."</td>\r\n                                      <td>".$namaakun."</td>\r\n                                      <td align=center>".$tahun."</td>\r\n                                      <td align=center>".$bulan."</td>\r\n                                      <td align=center>".$kodeorg."</td>\r\n                                      <td align=center>".$bussunitcode."</td>\r\n                                      <td align=center>".$induk."</td>\r\n                                      <td align=right>".number_format($sawal, 2)."</td>     \r\n                                      <td align=right>".number_format($debet, 2)."</td>\r\n                                      <td align=right>".number_format($kredit, 2)."</td>\r\n                                      <td align=right>".number_format(($sawal + $debet) - $kredit, 2)."</td>    \r\n                            </tr>";
                $tawal += $sawal;
                $tdebet += $debet;
                $tkredit += $kredit;
                $tsalak += ($sawal + $debet) - $kredit;
            }
        }
        echo "<tr class=rowcontent>\r\n                              <td colspan=7 align=center>TOTAL</td>\r\n                              <td align=right>".number_format($tawal, 2)."</td>     \r\n                              <td align=right>".number_format($tdebet, 2)."</td>\r\n                              <td align=right>".number_format($tkredit, 2)."</td>\r\n                              <td align=right>".number_format($tsalak, 2)."</td>\r\n                            </tr>";
        echo " </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\t\t \r\n                </table> ";
    }
}

?>