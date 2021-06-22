<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$proses = $_GET['proses'];
$kdOrg1 = $_POST['kdOrg1'];
$kdAfd1 = $_POST['kdAfd1'];
$kdBlok = $_POST['blok1'];
$tahun1 = $_POST['tahun1'];
$kegiatan1 = $_POST['kegiatan1'];
if ('excel' === $proses || 'pdf' === $proses) {
    $kdOrg1 = $_GET['kdOrg1'];
    $kdAfd1 = $_GET['kdAfd1'];
    $tahun1 = $_GET['tahun1'];
    $kegiatan1 = $_GET['kegiatan1'];
    $kdBlok = $_GET['blok1'];
}



if ('' === $kdAfd1) {
    $kdAfd1 = $kdOrg1;
}

if ('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) {
    if ('' === $kdOrg1) {
        echo 'Error: Estate code and afdeling code required.';
        exit();
    }

    if ('' === $tahun1) {
        echo 'Error: year is reqired.';
        exit();
    }
}

if ('excel' === $proses || 'preview' === $proses) {
    if ('EN' === $_SESSION['language']) {
        $zz = 'namakegiatan1 as namakegiatan';
    } else {
        $zz = 'namakegiatan';
    }

    $str = "select kodekegiatan, ".$zz.", satuan from $dbname.setup_kegiatan ";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $kamusKeg[$bar->kodekegiatan]['nama'] = $bar->namakegiatan;
        $kamusKeg[$bar->kodekegiatan]['satu'] = $bar->satuan;
    }
    $str = "select kodeorg, luasareaproduktif, tahuntanam from $dbname.setup_blok ";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $kamusOrg[$bar->kodeorg]['luas'] = $bar->luasareaproduktif;
        $kamusOrg[$bar->kodeorg]['tata'] = $bar->tahuntanam;
    }

    $str = "select kodekegiatan, kodeorg, hasilkerja, jumlahhk, tanggal ".
    "from $dbname.kebun_perawatan_vw ".
    "where kodeorg like '".($kdBlok==''? $kdAfd1 : $kdBlok)."%' and tanggal like '".$tahun1."%' ";//and kodekegiatan like '%".$kegiatan1."%' ";
    if ($kegiatan1!=''){
        $str.= " and kodekegiatan like '%".$kegiatan1."%' ";
    }
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $dzKeg[$bar->kodekegiatan] = $bar->kodekegiatan;
        $dzOrg[$bar->kodeorg] = $bar->kodeorg;
        $bulan = substr($bar->tanggal, 5, 2);
        $dzArr[$bar->kodekegiatan][$bar->kodeorg][$bulan]['hasilkerja'] += $bar->hasilkerja;
        $dzArr[$bar->kodekegiatan][$bar->kodeorg][$bulan]['jumlahhk'] += $bar->jumlahhk;
        if (!isset($barisKeg[$bar->kodekegiatan][$bar->kodeorg])) {
            $barisKeg[$bar->kodekegiatan][$bar->kodeorg] = $bar->kodekegiatan.$bar->kodeorg;
            ++$barizKeg[$bar->kodekegiatan];
        }
    }
    if (!empty($dzKeg)) {
        asort($dzKeg);
    }

    if (!empty($dzOrg)) {
        asort($dzOrg);
    }

    $jumlahKeg = count($dzKeg);
    $jumlahOrg = count($dzOrg);
    $border = 0;
    if ('excel' === $proses) {
        $border = 1;
    }

    $stream .= "<table cellspacing='1' border='".$border."' class='sortable'>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['namakegiatan']."</td>\r\n        <td colspan=3 align=center>".$_SESSION['lang']['blok'].'</td>';
    for ($i = 1; $i <= 12; ++$i) {
        $stream .= '<td colspan=3 align=center>'.numToMonth($i).'</td>';
    }
    $stream .= '<td colspan=3 align=center>'.$_SESSION['lang']['semester']." I</td>\r\n        <td colspan=3 align=center>".$_SESSION['lang']['semester']." II</td>\r\n        <td colspan=3 align=center>".$_SESSION['lang']['total']."</td>\r\n    </tr>\r\n    <tr class=rowheader>\r\n        <td align=center>".$_SESSION['lang']['kode']."</td>    \r\n        <td align=center>".$_SESSION['lang']['luas']."</td>\r\n        <td align=center>".$_SESSION['lang']['tahuntanam'].'</td>';
    for ($i = 1; $i <= 12; ++$i) {
        $stream .= '<td align=center>'.$_SESSION['lang']['jhk']."</td>\r\n        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>\r\n        <td align=center>Output (Hasil/JHK)</td>";
    }
    $stream .= '<td align=center>'.$_SESSION['lang']['jhk']."</td>\r\n        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>\r\n        <td align=center>Output (Hasil/JHK)</td>\r\n        <td align=center>".$_SESSION['lang']['jhk']."</td>\r\n        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>\r\n        <td align=center>Output (Hasil/JHK)</td>\r\n        <td align=center>".$_SESSION['lang']['jhk']."</td>\r\n        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>\r\n        <td align=center>Output (Hasil/JHK)</td>\r\n    </tr></thead>\r\n    <tbody>";
    if (!empty($dzKeg)) {
        foreach ($dzKeg as $rKeg) {
            $bariskegiatan = true;
            $stream .= "<tr class=rowcontent>\r\n            <td rowspan=".$barizKeg[$rKeg].'>'.$kamusKeg[$rKeg]['nama'].' ('.$kamusKeg[$rKeg]['satu'].')</td>';
            if (!empty($dzOrg)) {
                foreach ($dzOrg as $rOrg) {
                    $adadata = false;
                    for ($i = 1; $i <= 12; ++$i) {
                        if (1 === strlen($i)) {
                            $ii = '0'.$i;
                        } else {
                            $ii = $i;
                        }

                        if ('' !== $dzArr[$rKeg][$rOrg][$ii]['hasilkerja']) {
                            $adadata = true;
                        }

                        if ('' !== $dzArr[$rKeg][$rOrg][$ii]['jumlahhk']) {
                            $adadata = true;
                        }
                    }
                    if ($adadata) {
                        if (!$bariskegiatan) {
                            $stream .= '<tr class=rowcontent>';
                        }

                        $stream .= '<td>'.$rOrg.'</td>';
                        $stream .= '<td align=right>'.$kamusOrg[$rOrg]['luas'].'</td>';
                        $stream .= '<td align=right>'.$kamusOrg[$rOrg]['tata'].'</td>';
                        $jumlahhk1 = 0;
                        $jumlahhk2 = 0;
                        $hasilkerja1 = 0;
                        $hasilkerja2 = 0;
                        for ($i = 1; $i <= 12; ++$i) {
                            if (1 === strlen($i)) {
                                $ii = '0'.$i;
                            } else {
                                $ii = $i;
                            }

                            $haka = $dzArr[$rKeg][$rOrg][$ii]['jumlahhk'];
                            $hasi = $dzArr[$rKeg][$rOrg][$ii]['hasilkerja'];
                            $oput = 0;
                            $oput = $hasi / $haka;
                            if (0 === $haka && 0 === $hasi) {
                                $haka = '';
                                $hasi = '';
                                $oput = '';
                                $bisadiklik = '';
                            } else {
                                $haka = number_format($haka, 2);
                                $hasi = number_format($hasi, 2);
                                $oput = number_format($oput, 2);
                                $bisadiklik = " style='cursor:pointer;' onclick=\"viewDetail1('".$rKeg."','".$rOrg."','".$tahun1.'-'.$ii."',event);\" title=\"Click untuk melihat detail\" ";
                            }

                            $stream .= '<td align=right '.$bisadiklik.'>'.$haka."</td>\r\n                <td align=right ".$bisadiklik.'>'.$hasi."</td>\r\n                <td align=right ".$bisadiklik.'>'.$oput.'</td>';
                            if ($i < 8) {
                                $jumlahhk1 += $dzArr[$rKeg][$rOrg][$ii]['jumlahhk'];
                                $hasilkerja1 += $dzArr[$rKeg][$rOrg][$ii]['hasilkerja'];
                            } else {
                                $jumlahhk2 += $dzArr[$rKeg][$rOrg][$ii]['jumlahhk'];
                                $hasilkerja2 += $dzArr[$rKeg][$rOrg][$ii]['hasilkerja'];
                            }
                        }
                        $oput = 0;
                        $haka = 0;
                        $hasi = 0;
                        $haka = $jumlahhk1;
                        $hasi = $hasilkerja1;
                        $oput = $hasi / $haka;
                        if (0 === $haka && 0 === $hasi) {
                            $haka = '';
                            $hasi = '';
                            $oput = '';
                        } else {
                            $haka = number_format($haka, 2);
                            $hasi = number_format($hasi, 2);
                            $oput = number_format($oput, 2);
                        }

                        $stream .= '<td align=right>'.$haka."</td>\r\n            <td align=right>".$hasi."</td>\r\n            <td align=right>".$oput.'</td>';
                        $oput = 0;
                        $haka = 0;
                        $hasi = 0;
                        $haka = $jumlahhk2;
                        $hasi = $hasilkerja2;
                        $oput = $hasi / $haka;
                        if (0 === $haka && 0 === $hasi) {
                            $haka = '';
                            $hasi = '';
                            $oput = '';
                        } else {
                            $haka = number_format($haka, 2);
                            $hasi = number_format($hasi, 2);
                            $oput = number_format($oput, 2);
                        }

                        $stream .= '<td align=right>'.$haka."</td>\r\n            <td align=right>".$hasi."</td>\r\n            <td align=right>".$oput.'</td>';
                        $oput = 0;
                        $haka = 0;
                        $hasi = 0;
                        $haka = $jumlahhk1 + $jumlahhk2;
                        $hasi = $hasilkerja1 + $hasilkerja2;
                        $oput = $hasi / $haka;
                        if (0 === $haka && 0 === $hasi) {
                            $haka = '';
                            $hasi = '';
                            $oput = '';
                        } else {
                            $haka = number_format($haka, 2);
                            $hasi = number_format($hasi, 2);
                            $oput = number_format($oput, 2);
                        }

                        $stream .= '<td align=right>'.$haka."</td>\r\n            <td align=right>".$hasi."</td>\r\n            <td align=right>".$oput.'</td>';
                        $stream .= '</tr>';
                        $bariskegiatan = false;
                    }
                }
            }
        }
    }

    $stream .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHms');
        $nop_ = 'Pusingan_Perawatan_'.$kdAfd1.'_'.$tahun1.'_'.$kegiatan1.'_'.date('YmdHis');
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls.gz';\r\n            </script>";

        break;
}

?>
