<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$tahun1 = substr($periode, 0, 4);
$bulan1 = substr($periode, 5, 2);
$periode1 = $periode;
$stream = '';
if ('' === $gudang) {
    $str = 'select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk from '.$dbname.".keu_jurnalsum_vw a\r\n\t\tleft join ".$dbname.".keu_5akun b\r\n\t\ton a.noakun=b.noakun\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere c.induk = '".$pt."' and a.periode='".$periode1."'\r\n\t\torder by a.noakun, a.periode \r\n\t\t";
} else {
    $str = 'select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk from '.$dbname.".keu_jurnalsum_vw a\r\n\t\tleft join ".$dbname.".keu_5akun b\r\n\t\ton a.noakun=b.noakun\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."' and a.periode='".$periode1."'\r\n\t\torder by a.noakun, a.periode \r\n\t\t";
}

$res = mysql_query($str);
$res4 = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    $stream .= $_SESSION['lang']['laporanneracacoba'].': '.$pt.' '.$gudang.' '.$periode."<br>\r\n\t\t<table border=1>\r\n                    <tr>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoakhir']."</td>\r\n                        </tr>";
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        if ($bar->noakun < 4000000) {
            $periode = date('d-m-Y H:i:s');
            $kodebarang = $bar->kodebarang;
            $namabarang = $bar->namabarang;
            $kuantitas = $bar->kuan;
            $nojurnal = $bar->nojurnal;
            $tanggal = $bar->tanggal;
            $noakun = $bar->noakun;
            $namaakun = $bar->namaakun;
            $keterangan = $bar->keterangan;
            $bussunitcode = $bar->bussunitcode;
            $sawal = 0;
            $strx = 'select awal'.$bulan1.' from '.$dbname.".keu_saldobulanan where \r\n                              noakun='".$noakun."' and kodeorg='".$bussunitcode."' \r\n                              and periode='".$tahun1.$bulan1."'";
            $resx = mysql_query($strx);
            while ($barx = mysql_fetch_array($resx)) {
                $sawal = $barx[0];
            }
            $debet = $bar->debet;
            $kredit = $bar->kredit;
            $sakhir = ($sawal + $debet) - $kredit;
            $stream .= "<tr>\r\n                          <td>".$noakun."</td>\r\n                          <td>".$namaakun."</td>\r\n                           <td align=right class=firsttd>".number_format($sawal, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($debet, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($kredit, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($sakhir, 2, '.', '')."</td>\r\n                        </tr>";
            $tawal += $sawal;
            $tdebet += $debet;
            $tkredit += $kredit;
            $tsalak += ($sawal + $debet) - $kredit;
        }
    }
    $stream .= "<tr>\r\n                          <td></td>\r\n                          <td></td>\r\n                           <td align=right class=firsttd>".number_format($tawal, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($tdebet, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($tkredit, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($tsalak, 2, '.', '')."</td>\r\n                        </tr>";
    $stream .= '</table>';
    $tawal = 0;
    $tdebet = 0;
    $tkredit = 0;
    $tsalak = 0;
    $stream .= "<br>\r\n\t\t<table border=1>\r\n                    <tr>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>\r\n                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoakhir']."</td>\r\n                        </tr>";
    while ($bar = mysql_fetch_object($res4)) {
        ++$no;
        if (3999999 < $bar->noakun) {
            $periode = date('d-m-Y H:i:s');
            $kodebarang = $bar->kodebarang;
            $namabarang = $bar->namabarang;
            $kuantitas = $bar->kuan;
            $nojurnal = $bar->nojurnal;
            $tanggal = $bar->tanggal;
            $noakun = $bar->noakun;
            $namaakun = $bar->namaakun;
            $keterangan = $bar->keterangan;
            $bussunitcode = $bar->bussunitcode;
            $sawal = 0;
            $strx = 'select awal'.$bulan1.' from '.$dbname.".keu_saldobulanan where \r\n                              noakun='".$noakun."' and kodeorg='".$bussunitcode."' \r\n                              and periode='".$tahun1.$bulan1."'";
            $resx = mysql_query($strx);
            while ($barx = mysql_fetch_array($resx)) {
                $sawal = $barx[0];
            }
            $debet = $bar->debet;
            $kredit = $bar->kredit;
            $sakhir = ($sawal + $debet) - $kredit;
            $stream .= "<tr>\r\n                          <td>".$noakun."</td>\r\n                          <td>".$namaakun."</td>\r\n                           <td align=right class=firsttd>".number_format($sawal, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($debet, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($kredit, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($sakhir, 2, '.', '')."</td>\r\n                        </tr>";
            $tawal += $sawal;
            $tdebet += $debet;
            $tkredit += $kredit;
            $tsalak += ($sawal + $debet) - $kredit;
        }
    }
    $stream .= "<tr>\r\n                          <td></td>\r\n                          <td></td>\r\n                           <td align=right class=firsttd>".number_format($tawal, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($tdebet, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($tkredit, 2, '.', '')."</td>\r\n                           <td align=right class=firsttd>".number_format($tsalak, 2, '.', '')."</td>\r\n                        </tr>";
    $stream .= '</table>';
}

$nop_ = 'NeracaPercobaan'.$gudang.$tahun1.$bulan1;
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