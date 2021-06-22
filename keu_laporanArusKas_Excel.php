<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$stream = '';
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'ALL';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
if ('' === $pt) {
    $str = 'select a.*,c.induk from '.$dbname.".keu_5mesinlaporandt a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere a.namalaporan='CASH FLOW'\r\n\t\torder by a.nourut \r\n\t\t";
    $str1 = 'select a.* from '.$dbname.".keu_jurnalsum_vw a\r\n\t\t\twhere a.noakun !='' and a.periode = '".$periode."'\r\n\t\t\torder by a.noakun, a.periode \r\n\t\t\t";
    $str2 = 'select * from '.$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode, 0, 4)."'  \r\n\t\t";
} else {
    if ('' === $gudang) {
        $str = 'select a.*,c.induk from '.$dbname.".keu_5mesinlaporandt a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere a.namalaporan='CASH FLOW'\r\n\t\torder by a.nourut \r\n\t\t";
        if ('' !== $pt) {
            $str1 = 'select a.*,b.induk from '.$dbname.".keu_jurnalsum_vw a\r\n\t\t\tleft join ".$dbname.".organisasi b\r\n\t\t\ton a.kodeorg=b.kodeorganisasi\r\n\t\t\twhere b.induk = '".$pt."' and a.noakun !='' and a.periode = '".$periode."'\r\n\t\t\torder by a.noakun, a.periode \r\n\t\t\t";
        } else {
            $str1 = 'select a.*,b.induk from '.$dbname.".keu_jurnalsum_vw a\r\n\t\t\tleft join ".$dbname.".organisasi b\r\n\t\t\ton a.kodeorg=b.kodeorganisasi\r\n\t\t\twhere a.noakun !='' and a.periode = '".$periode."'\r\n\t\t\torder by a.noakun, a.periode \r\n\t\t\t";
        }

        $str2 = 'select * from '.$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode, 0, 4)."'  \r\n\t\t";
    } else {
        $str = 'select a.*,c.induk from '.$dbname.".keu_5mesinlaporandt a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere a.namalaporan='CASH FLOW'\r\n\t\torder by a.nourut \r\n\t\t";
        $str1 = 'select *,b.namaakun from '.$dbname.".keu_jurnalsum_vw a\r\n\t\tleft join ".$dbname.".keu_5akun b\r\n\t\ton a.noakun=b.noakun\r\n\t\twhere substr(a.kodeorg,1,4) = '".$gudang."' and a.noakun !=''  and a.periode = '".$periode."'\r\n\t\torder by a.noakun, a.periode \r\n\t\t";
        $str2 = 'select * from '.$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode, 0, 4)."'  \r\n\t\t";
    }
}

$begbal = 0;
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
    $stream .= $_SESSION['lang']['aruskas'].': '.$namapt."<br>\r\n\t\t<table border=1>\r\n\t\t\t\t    <tr>\r\n\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang'][' ']."</td>\r\n\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>\r\n\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoakhir']."</td>\r\n\t\t\t\t\t</tr>";
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
        if ('Header' === $tipe) {
            $stream .= "<tr>\r\n\t\t\t  <td colspan=4>".$keterangandisplay."</td>\r\n\t\t\t</tr>";
        }

        if ('Detail' === $tipe) {
            $res1 = mysql_query($str1);
            $balance = 0;
            $endbalance = 0;
            $debet1 = 0;
            $kredit1 = 0;
            while ($bar1 = mysql_fetch_object($res1)) {
                $noakun1 = $bar1->noakun;
                $debet1 = $bar1->debet;
                $kredit1 = $bar1->kredit;
                $kodeorg1 = $bar1->kodeorg;
                if ($noakundari <= $noakun1 && $noakun1 <= $noakunsampai) {
                    $balance += $debet1;
                    $balance -= $kredit1;
                    $endbalance += $debet1;
                    $endbalance -= $kredit1;
                }
            }
            if (10510 === $nourut) {
                $balance = $begbal;
                $endbalance = $begbal;
            }

            if (10520 === $nourut) {
                $balance = $t2balance + $begbal;
                $endbalance = $t2ebalance + $begbal;
            }

            $stream .= "<tr class=rowcontent style='cursor:pointer;'>\r\n\t\t\t  <td>".$nourut."</td>\r\n\t\t\t  <td>".$keterangandisplay."</td>\r\n\t\t\t  <td align=right>".number_format($balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($endbalance, 2, '.', ',')."</td>\r\n\t\t\t</tr>";
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
        }

        if ('Total' === $tipe) {
            $stream .= "<tr>\r\n\t\t\t  <td>&nbsp;</td>\r\n\t\t\t  <td>".$keterangandisplay."</td>\r\n\t\t\t";
            if ('1' === $variableoutput) {
                $stream .= "\r\n\t\t\t  <td align=right>".number_format($t1balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($t1ebalance, 2, '.', ',')."</td>\r\n\t\t\t";
                $t1balance = $t1ebalance = 0;
            }

            if ('2' === $variableoutput) {
                $stream .= "\r\n\t\t\t  <td align=right>".number_format($t2balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($t2ebalance, 2, '.', ',')."</td>\r\n\t\t\t";
                $t1balance = $t1ebalance = 0;
            }

            if ('9' === $variableoutput) {
                $stream .= "\r\n\t\t\t  <td align=right>".number_format($t9balance, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".number_format($t9ebalance, 2, '.', ',')."</td>\r\n\t\t\t";
                $t1balance = $t1ebalance = $t2balance = $t2ebalance = $t3balance = $t3ebalance = 0;
                $t4balance = $t4ebalance = $t5balance = $t5ebalance = $t6balance = $t6ebalance = 0;
                $t7balance = $t7ebalance = $t8balance = $t8ebalance = $t9balance = $t9ebalance = 0;
            }

            $stream .= '</tr>';
        }
    }
    $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
}

$nop_ = 'ArusKas';
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