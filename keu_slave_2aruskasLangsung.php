<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
if ('' === $pt) {
    $str = 'select * from '.$dbname.".keu_5mesinlaporandt\r\n\t\twhere namalaporan='CASH FLOW DIRECT'\r\n\t\torder by nourut \r\n\t\t";
    $str1 = 'select * from '.$dbname.".keu_jurnaldt \r\n\t\twhere substr(tanggal,1,7)<='".$periode."' \r\n\t\t";
    $str2 = 'select * from '.$dbname.".keu_jurnaldt \r\n\t\twhere noakun<='1110299' and \r\n\t\tsubstr(tanggal,1,4)<'".substr($periode, 0, 4)."'  \r\n\t\t";
} else {
    if ('' === $gudang) {
        $str = 'select a.*,c.induk from '.$dbname.".keu_5mesinlaporandt a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere a.namalaporan='CASH FLOW DIRECT'\r\n\t\torder by a.nourut \r\n\t\t";
        if ('' !== $pt) {
            $str1 = 'select a.*, b.induk from '.$dbname.".keu_jurnaldt a \r\n\t\t\tleft join ".$dbname.".organisasi b\r\n\t\t\ton a.kodeorg=b.kodeorganisasi\r\n\t\twhere substr(a.tanggal,1,7)<='".$periode."' \r\n\t\tand b.induk = '".$pt."'  \r\n\t\t\t";
        } else {
            $str1 = 'select a.*, b.induk from '.$dbname.".keu_jurnaldt a \r\n\t\t\tleft join ".$dbname.".organisasi b\r\n\t\t\ton a.kodeorg=b.kodeorganisasi\r\n\t\twhere substr(a.tanggal,1,7)<='".$periode."' \r\n\t\t\t";
        }

        $str2 = 'select * from '.$dbname.".keu_jurnaldt where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(tanggal,1,4)<'".substr($periode, 0, 4)."'  \r\n\t\t";
    } else {
        $str = 'select a.*,c.induk from '.$dbname.".keu_5mesinlaporandt a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere a.namalaporan='CASH FLOW DIRECT'\r\n\t\torder by a.nourut \r\n\t\t";
        $str1 = 'select * from '.$dbname.".keu_jurnaldt \r\n\t\twhere substr(kodeorg,1,4) = '".$gudang."' and substr(tanggal,1,7)<='".$periode."' and substr(kodeorg,4,1)!=' '  \r\n\t\t";
        $str2 = 'select * from '.$dbname.".keu_jurnaldt where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(tanggal,1,4)<'".substr($periode, 0, 4)."'  \r\n\t\t";
    }
}

$begbal = 0;
$salakqty = 0;
$masukqty = 0;
$keluarqty = 0;
$sawalQTY = 0;
$t1balance = $t2balance = $t3balance = $t4balance = $t5balance = $t6balance = $t7balance = $t8balance = 0;
$t1ebalance = $t2ebalance = $t3ebalance = $t4ebalance = $t5ebalance = $t6ebalance = $t7ebalance = $t8ebalance = $t9ebalance = 0;
$res = mysql_query($str);
$res1 = mysql_query($str1);
$res2 = mysql_query($str2);
$begbal = 0;
while ($bar = mysql_fetch_object($res2)) {
    $begbal += $bar->debet;
    $begbal -= $bar->kredit;
}
$no = $counter = 0;
$stawal = $stdebet = $stkredit = $stakhir = $sawal = 0;
$tawal = $tdebet = $tkredit = $takhir = 0;
$noakun1 = $namaakun1 = ' ';
if (mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $tanggal = $bar->tanggal;
        $noakun = $bar->noakun;
        $nourut = $bar->nourut;
        $nojurnal = $bar->nojurnal;
        $namaakun = $bar->namaakun;
        $noakundari = $bar->noakundari;
        $noakunsampai = $bar->noakunsampai;
        $tipe = $bar->tipe;
        $keterangandisplay = $bar->keterangandisplay;
        $variableoutput = $bar->variableoutput;
        if ($periode === $bar->periode) {
            $stdebet += $bar->debet;
            $stkredit += $bar->kredit;
        } else {
            $stawal += $bar->debet - $bar->kredit;
        }

        $stakhir = ($stawal + $stdebet) - $stkredit;
        if ('Total' === $tipe) {
            echo "<tr>\r\n\t\t\t  <td>&nbsp;</td>\r\n\t\t\t  <td>".$keterangandisplay."</td>\r\n\t\t\t";
            if ('1' === $variableoutput) {
                echo "\r\n\t\t\t  <td align=right>".number_format($t1balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($t1ebalance, 2, '.', ',')."</td>\r\n\t\t\t";
                $t1balance = $t1ebalance = 0;
            }

            if ('2' === $variableoutput) {
                echo "\r\n\t\t\t  <td align=right>".number_format($t2balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($t2ebalance, 2, '.', ',')."</td>\r\n\t\t\t";
                $t1balance = $t1ebalance = 0;
            }

            if ('9' === $variableoutput) {
                echo "\r\n\t\t\t  <td align=right>".number_format($t9balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($t9ebalance, 2, '.', ',')."</td>\r\n\t\t\t";
                $t1balance = $t1ebalance = $t2balance = $t2ebalance = $t3balance = $t3ebalance = 0;
                $t4balance = $t4ebalance = $t5balance = $t5ebalance = $t6balance = $t6ebalance = 0;
                $t7balance = $t7ebalance = $t8balance = $t8ebalance = $t9balance = $t9ebalance = 0;
            }

            echo '</tr>';
        }

        if ('Header' === $tipe) {
            echo "<tr>\r\n\t\t\t  <td colspan=4>".$keterangandisplay."</td>\r\n\t\t\t";
            echo '</tr>';
        }

        if ('Detail' === $tipe) {
            $res1 = mysql_query($str1);
            $balance = $endbalance = 0;
            while ($bar1 = mysql_fetch_object($res1)) {
                $noakun1 = $bar1->noaruskas;
                $jumlah1 = $bar1->jumlah;
                if ($noakun1 === $nourut) {
                    $balance += $jumlah1;
                    $endbalance += $jumlah1;
                }
            }
            if (51000 === $nourut) {
                $balance = $begbal;
                $endbalance = $begbal;
            }

            if (52000 === $nourut) {
                $balance = $xbalance + $begbal;
                $endbalance = $xbalance + $begbal;
            }

            echo "<tr class=rowcontent style='cursor:pointer;'>\r\n\t\t\t  <td>".$nourut."</td>\r\n\t\t\t  <td>".$keterangandisplay."</td>\r\n\t\t\t  <td align=right>".number_format($balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($endbalance, 2, '.', ',')."</td>\r\n\t\t\t</tr>";
            $xbalance += $balance;
            $t1balance += $balance;
            $t2balance += $balance;
            $t3balance += $balance;
            $t4balance += $balance;
            $t5balance += $balance;
            $t6balance += $balance;
            $t7balance += $balance;
            $t8balance += $balance;
            $t9balance += $balance;
            $t1ebalance += $endbalance;
            $t2ebalance += $endbalance;
            $t3ebalance += $endbalance;
            $t4ebalance += $endbalance;
            $t5ebalance += $endbalance;
            $t6ebalance += $endbalance;
            $t7ebalance += $endbalance;
            $t8ebalance += $endbalance;
            $t9ebalance += $endbalance;
            $balance = $endbalance = 0;
        }
    }
}

?>