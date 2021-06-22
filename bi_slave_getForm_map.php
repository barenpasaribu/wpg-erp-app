<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/qc_lib.php';
$id = $_POST['id'];
$viewbox = $_POST['viewbox'];
$kebun = $_POST['kebun'];
$kodeorg = $_POST['kodeorg'];
$viewbox = $_POST['viewbox'];
$kontrol = $_POST['kontrol'];
$option = $_POST['option'];
$tanggal0 = $_POST['tanggal0'];
$tanggal1 = $_POST['tanggal1'];
$suboption = $_POST['suboption'];
$periode = $_POST['periode'];
$cek = $_POST['cek'];
$tanggallama0 = $tanggal0;
$tanggallama1 = $tanggal1;
$qwe = explode('-', $tanggal0);
$tanggal0 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$qwe = explode('-', $tanggal1);
$tanggal1 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$optTahun = "<option value=''>Pilih Tahun</option>";
for ($x = 0; $x <= 5; ++$x) {
    $optTahun .= "<option value='".(date('Y') - $x)."'>".(date('Y') - $x).'</option>';
}
$str = 'SELECT * FROM '.$dbname.".qc_5final\n    order by max";
$query = mysql_query($str) || exit(mysql_error($conns));
while ($res = mysql_fetch_assoc($query)) {
    $nilaiqc[$res['max']] = $res['max'];
    $warnaqc[$res['max']] = $res['color'];
    $artiqc[$res['max']] = $res['name'];
}
switch ($id) {
    case 'cekoption':
        if ('qc' === $option) {
            $sOrg2 = 'select tipe from '.$dbname.".qc_5parameter\n                where tipe != 'XBLOK'\n                group by tipe\n                order by tipe\n                ";
            $qOrg2 = mysql_query($sOrg2) || exit(mysql_error($conns));
            while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
                $hasilcek .= '<option value='.$rOrg2['tipe'].'>'.$rOrg2['tipe'].'</option>';
            }
        }

        echo $hasilcek;

        break;
    case 'option':
        $hasil = '';
        $str = 'SELECT kodeorg FROM '.$dbname.".kebun_peta WHERE kodeorg like '".$kebun."%'";
        $query = mysql_query($str) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $arrblok[$res['kodeorg']] = $res['kodeorg'];
            $arrwarna[$res['kodeorg']] = '#dedede';
            $arrtitle[$res['kodeorg']] = $res['kodeorg'];
        }
        if ('--' !== $tanggal1) {
            $wheretanggal = "tanggal between '".$tanggal0."' and '".$tanggal1."'";
        } else {
            $wheretanggal = "tanggal like '".$tanggal0."%'";
        }

        if ('qc' === $option) {
            $str = 'SELECT * FROM '.$dbname.".setup_blok WHERE kodeorg like '".$kebun."%'";
            $query = mysql_query($str) || exit(mysql_error($conns));
            while ($res = mysql_fetch_assoc($query)) {
                $nilainya[$res['kodeorg']] = nilaiOrganisasi($res['kodeorg'], $suboption, $tanggallama0, $tanggallama1, 'nilai');
                $arrtitle[$res['kodeorg']] = $res['kodeorg'].': '.$nilainya[$res['kodeorg']];
                if ('nosample' === $nilainya[$res['kodeorg']]) {
                    $nilainya[$res['kodeorg']] = 0;
                }

                if (!empty($nilaiqc)) {
                    foreach ($nilaiqc as $nilai) {
                        if ($nilaiqc[$nilai] < $nilainya[$res['kodeorg']]) {
                            $arrwarna[$res['kodeorg']] = $warnaqc[$nilai];
                        }
                    }
                }
            }
        }

        if ('produksi' === $option) {
            $str = 'SELECT blok, totalkg FROM '.$dbname.".kebun_spb_vw \n                WHERE blok like '".$kebun."%' and tanggal like '".$periode."%'\n                    ";
            $query = mysql_query($str) || exit(mysql_error($conns));
            while ($res = mysql_fetch_assoc($query)) {
                $blok[$res['blok']] = $res['blok'];
                $prodact[$res['blok']] += $res['totalkg'];
            }
            $str = 'SELECT * FROM '.$dbname.".bgt_produksi_kbn_kg_vw\n                WHERE kodeblok like '".$kebun."%' and tahunbudget = '".substr($periode, 0, 4)."'\n                    ";
            $query = mysql_query($str) || exit(mysql_error($conns));
            while ($res = mysql_fetch_assoc($query)) {
                $blok[$res['kodeblok']] = $res['kodeblok'];
                $kg = 'kg'.substr($periode, 5, 2);
                $prodbgt[$res['kodeblok']] += $res[$kg];
            }
            if (!empty($blok)) {
                foreach ($blok as $blox) {
                    $nilainya[$blox] = $prodact[$blox] / $prodbgt[$blox] * 100;
                    $arrtitle[$blox] = $blox.': '.number_format($nilainya[$blox], 2).'% ('.number_format($prodact[$blox]).'/'.number_format($prodbgt[$blox]).')';
                    if (!empty($nilaiqc)) {
                        foreach ($nilaiqc as $nilai) {
                            if ($nilaiqc[$nilai] < $nilainya[$blox]) {
                                $arrwarna[$blox] = $warnaqc[$nilai];
                            }
                        }
                    }
                }
            }
        }

        if ('biayapanen' === $option || 'biayatm' === $option || 'biayatbm' === $option) {
            if ('biayapanen' === $option) {
                $mayorPanen = '611';
            }

            if ('biayatm' === $option) {
                $mayorPanen = '62';
            }

            if ('biayatbm' === $option) {
                $mayorPanen = '126';
            }

            $str = 'SELECT kodeblok, jumlah FROM '.$dbname.".keu_jurnaldt \n                WHERE kodeblok like '".$kebun."%' and tanggal like '".$periode."%'\n                    and noakun like '".$mayorPanen."%';\n                    ";
            $query = mysql_query($str) || exit(mysql_error($conns));
            while ($res = mysql_fetch_assoc($query)) {
                $blok[$res['kodeblok']] = $res['kodeblok'];
                $prodact[$res['kodeblok']] += $res['jumlah'];
            }
            $str = 'SELECT * FROM '.$dbname.".bgt_budget_detail\n                WHERE kodeorg like '".$kebun."%' and tahunbudget = '".substr($periode, 0, 4)."'\n                    and noakun like '".$mayorPanen."%';\n                    ";
            $query = mysql_query($str) || exit(mysql_error($conns));
            while ($res = mysql_fetch_assoc($query)) {
                $blok[$res['kodeorg']] = $res['kodeorg'];
                $rp = 'rp'.substr($periode, 5, 2);
                $prodbgt[$res['kodeorg']] += $res[$rp];
            }
            if (!empty($blok)) {
                foreach ($blok as $blox) {
                    $beda = abs($prodbgt[$blox] - $prodact[$blox]);
                    $nilainya[$blox] = 100 - $beda / $prodbgt[$blox] * 100;
                    if (0 === $prodbgt[$blox]) {
                        $nilainya[$blox] = 0;
                    }

                    $arrwarna[$blox] = 'red';
                    $arrtitle[$blox] = $blox.' Diff:'.@number_format($beda / $prodbgt[$blox] * 100, 2).'% (Actl:'.number_format($prodact[$blox]).'/Budg:'.number_format($prodbgt[$blox]).')';
                    if (!empty($nilaiqc)) {
                        foreach ($nilaiqc as $nilai) {
                            if ($nilaiqc[$nilai] < $nilainya[$blox]) {
                                $arrwarna[$blox] = $warnaqc[$nilai];
                            }
                        }
                    }

                    if (0 === $prodbgt[$blox]) {
                        $arrwarna[$blox] = 'gray';
                    }
                }
            }
        }

        if ('xblok' === $option) {
            $str = 'select kodeorg, keterangan from '.$dbname.".kebun_crossblock_ht \n                where kodeorg like '".$kebun."%' and ".$wheretanggal."\n                ";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $arrwarna[$bar->kodeorg] = 'green';
                if ('' === $arrtitle[$bar->kodeorg]) {
                    $arrtitle[$bar->kodeorg] = $bar->kodeorg;
                }

                if ('' !== $arrtitle[$bar->kodeorg]) {
                    $arrtitle[$bar->kodeorg] .= ', '.$bar->keterangan;
                }
            }
        }

        if ('rencanapanen' === $option) {
            $str = 'select kodeblok from '.$dbname.".kebun_rencanapanen\n                where kodeblok like '%".$kebun."%' and ".$wheretanggal."\n                group by kodeblok";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $arrwarna[$bar->kodeblok] = 'green';
                $arrtitle[$bar->kodeorg] = $bar->kodeorg;
            }
        }

        if ('panen' === $option) {
            $str = 'select kodeorg, tanggal from '.$dbname.".kebun_prestasi_vw \n                where notransaksi like '%".$kebun."%' and notransaksi like '%PNN%' and ".$wheretanggal."\n                group by kodeorg, tanggal";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $arrwarna[$bar->kodeorg] = 'green';
                ++$kalipanen[$bar->kodeorg];
                $arrtitle[$bar->kodeorg] = $bar->kodeorg.': '.$kalipanen[$bar->kodeorg].'x';
            }
        }

        if ('perawatan' === $option) {
            $str = 'select kodeorg from '.$dbname.".kebun_perawatan_dan_spk_vw\n                where unit like '%".$kebun."%' and ".$wheretanggal." and kodekegiatan like '".$suboption."%'\n                group by kodeorg";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $arrwarna[$bar->kodeorg] = 'green';
                $arrtitle[$bar->kodeorg] = $bar->kodeorg;
            }
        }

        if (!empty($arrblok)) {
            foreach ($arrblok as $blox) {
                $hasil .= $arrblok[$blox].'**'.$arrwarna[$blox].'**'.$arrtitle[$blox].'****';
            }
        }

        $hasil = substr($hasil, 0, -4);
        $artioption['panen'] = 'Panen';
        $artioption['rencanapanen'] = 'Rencana Panen';
        $artioption['xblok'] = 'Cross Block';
        $artioption['qc'] = 'QC';
        $artioption['perawatan'] = 'Perawatan';
        $artioption['produksi'] = 'Produksi';
        $artioption['biayapanen'] = 'Biaya Panen vs Budget';
        $artioption['biayatm'] = 'Biaya TM vs Budget';
        $artioption['biayatbm'] = 'Biaya TBM vs Budget';
        $hasil .= '******';
        $hasil .= '<table class=sortable cellspacing=1 border=0 width=100%>';
        if ('qc' === $option || 'produksi' === $option || 'biayapanen' === $option || 'biayatm' === $option || 'biayatbm' === $option) {
            if (!empty($nilaiqc)) {
                foreach ($nilaiqc as $nilai) {
                    $hasil .= '<tr class=rowcontent>';
                    $hasil .= '<td bgcolor="'.$warnaqc[$nilai].'">&nbsp</td><td>'.$artiqc[$nilai].' (>'.$nilai.')</td>';
                    $hasil .= '</tr>';
                }
            }

            if ('biayapanen' === $option || 'biayatm' === $option || 'biayatbm' === $option) {
                $hasil .= '<tr class=rowcontent>';
                $hasil .= '<td bgcolor="gray">&nbsp</td><td>No Budget</td>';
                $hasil .= '</tr>';
            }
        } else {
            $hasil .= '<tr class=rowcontent>';
            $hasil .= '<td bgcolor="green">&nbsp</td><td>'.$artioption[$option].' '.$suboption.'</td>';
            $hasil .= '</tr>';
        }

        $hasil .= '</table>';
        echo $hasil;

        break;
    case 'bi_map_general':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optKebun = '';
        while ($bar = mysql_fetch_object($res)) {
            $optKebun .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
        }
        echo "<table>\n            <tr>\n            <td>\n                Kebun\n            </td>\n            <td>\n                <select style=\"width:150px;\" id=kebun onchange=resetkebun()>".$optKebun."</select>   \n            </td></tr></table>";
        echo "<center><button onclick=showmap('') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'legend':
        $viewbox = '';
        $kegiatan = 'SELECT * FROM '.$dbname.".setup_blok WHERE kodeorg = '".$kodeorg."'";
        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $viewbox .= '<table class=sortable border=0 cellspacing=1>';
            $viewbox .= '<tr class=rowcontent><td>kodeorg</td><td>'.$res['kodeorg'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>tahuntanam</td><td>'.$res['tahuntanam'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>luas</td><td>'.number_format($res['luasareaproduktif'], 2).'+'.number_format($res['luasareanonproduktif'], 2).' ha</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>pokok</td><td>'.$res['jumlahpokok'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>status</td><td>'.$res['statusblok'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>tahuntanam</td><td>'.$res['tahuntanam'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>mulaipanen</td><td>'.$res['tahunmulaipanen'].'-'.$res['bulanmulaipanen'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>tanah</td><td>'.$res['kodetanah'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>klasifikasi tanah</td><td>'.$res['klasifikasitanah'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>topografi</td><td>'.$res['topografi'].'</td></tr>';
            $viewbox .= '<tr class=rowcontent><td>bibit</td><td>'.$res['jenisbibit'].'</td></tr>';
        }
        echo $viewbox;

        break;
    case 'show_map':
        $asd = ambilviewbox($dbname, $conn, $kebun);
        $viewbox = $asd[0].' '.$asd[1].' '.$asd[2].' '.$asd[3];
        list(, , , , $pengurangx, $pengurangy) = $asd;
        $warna = 'green';
        echo "<svg id=map onmousemove=\"geserklik(evt)\" onmousedown=\"mulaiklik(evt)\" onmouseup=\"selesaiklik(evt)\" version=\"1.1\" baseProfile=\"full\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" \n            xml:space=\"preserve\" preserveAspectRatio=\"xMinYMin meet\"  width=\"795px\" height=\"555px\" viewBox=\"".$viewbox.'">';
        echo '<g id=blok style="display:inline;fill-rule:evenodd">';
        echo '<desc>Layer '.$res['kodeorg'].'</desc>';
        $kegiatan = 'SELECT * FROM '.$dbname.".kebun_peta WHERE tipe in ('kebun', 'divisi', 'blok') and kodeorg like '".$kebun."%'";
        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $div = substr($res['kodeorg'], 4, 2);
            $splitel = explode('l', $res['path']);
            $splitem = explode('M', $splitel[0]);
            $splitkoma = explode(',', $splitem[1]);
            $jadinya0 = $splitkoma[0] - $pengurangx;
            $jadinya1 = $splitkoma[1] - $pengurangy;
            $trilili = 'M'.$jadinya0.','.$jadinya1.' l '.$splitel[1];
            if (10 === strlen(trim($res['kodeorg']))) {
                echo '<path id="'.$res['kodeorg'].'" d="'.$trilili."\" title='".$res['kodeorg']."'\n                onmouseover=\"evt.target.setAttribute('opacity', '0.25')\"\n                onmouseout=\"evt.target.setAttribute('opacity', '0.5')\"\n                onclick=\"gantul2('".$res['kodeorg']."')\" opacity=0.5\n                style=\"fill:".$warna.';stroke-linejoin:round;stroke:black;stroke-width:0.0001;cursor:pointer;"/>';
            }

            if (4 === strlen($res['kodeorg'])) {
                echo '<path id="'.$res['kodeorg'].'" d="'.$trilili."\" title='".$res['kodeorg']."'\n                style=\"fill:none;stroke-linejoin:round;stroke:black;stroke-width:20;cursor:pointer;\"/>";
            }

            $arrx[$res['kodeorg']] = tengahx($jadinya0, $splitel[1]);
            $arry[$res['kodeorg']] = tengahy($jadinya1, $splitel[1]);
            if (10 === strlen(trim($res['kodeorg']))) {
                $arrk[$res['kodeorg']] = $res['kodeorg'];
            }
        }
        if (!empty($arrk)) {
            foreach ($arrk as $kodeorg) {
                echo '<text id="t'.$kodeorg.'" x="'.$arrx[$kodeorg].'" y="'.$arry[$kodeorg].'" transform="rotate(-30 '.$arrx[$kodeorg].','.$arry[$kodeorg].")\"\n            font-family=\"Verdana\" style=\"display:block;\" font-size=\"75\" stroke-width=\"1.5\" stroke=\"white\" fill=\"black\" ><[".substr($arrk[$kodeorg], 4, 2).']'.substr($arrk[$kodeorg], 6, 4).'</text>';
            }
        }

        $kegiatan = 'SELECT * FROM '.$dbname.".kebun_peta WHERE tipe = 'jalan'";
        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $splitel = explode('l', $res['path']);
            $splitem = explode('M', $splitel[0]);
            $splitkoma = explode(',', $splitem[1]);
            $jadinya0 = $splitkoma[0] - $pengurangx;
            $jadinya1 = $splitkoma[1] - $pengurangy;
            $trilili = 'M'.$jadinya0.','.$jadinya1.' l '.$splitel[1];
            echo '<path id="'.$res['kodeorg'].'" d="'.$trilili."\" title='".$res['kodeorg']."'\n                style=\"fill:none;stroke-linejoin:round;stroke:black;stroke-width:30;cursor:pointer;\"/>";
        }
        $kegiatan = 'SELECT * FROM '.$dbname.".kebun_peta WHERE tipe like 'sungai%'";
        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $splitel = explode('l', $res['path']);
            $splitem = explode('M', $splitel[0]);
            $splitkoma = explode(',', $splitem[1]);
            $jadinya0 = $splitkoma[0] - $pengurangx;
            $jadinya1 = $splitkoma[1] - $pengurangy;
            $trilili = 'M'.$jadinya0.','.$jadinya1.' l '.$splitel[1];
            echo '<path id="'.$res['kodeorg'].'" d="'.$trilili."\" title='".$res['kodeorg']."'\n                style=\"fill:none;stroke-linejoin:round;stroke:blue;stroke-width:20;cursor:pointer;\"/>";
        }
        $kegiatan = 'SELECT * FROM '.$dbname.".kebun_peta WHERE tipe like 'kota%'";
        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $splitkoma = explode(',', $res['path']);
            $jadinya0 = $splitkoma[0] - $pengurangx;
            $jadinya1 = $splitkoma[1] - $pengurangy;
            $trilili = 'M'.$jadinya0.','.$jadinya1.' l '.$splitel[1];
            echo '<circle id="'.$res['kodeorg'].'" cx="'.$jadinya0.'" cy="'.$jadinya1.'" r="100" style="fill:rgb(255,0,0);stroke:black;stroke-width:50;"/>';
            $jadinya0 += 100;
            $jadinya1 -= 100;
            echo '<text id="t'.$res['kodeorg'].'" x="'.$jadinya0.'" y="'.$jadinya1.'" transform="rotate(-30 '.$jadinya0.','.$jadinya1.")\"\n            font-family=\"Verdana\" style=\"display:block;\" font-size=\"500\" stroke-width=\"1.5\" stroke=\"white\" fill=\"black\" ><[".$res['kodeorg'].']</text>';
        }
        echo '</g></svg>';

        break;
    case 'show_cek':
        $hasil = '';
        if ('textblock' === $cek) {
            $kegiatan = 'SELECT kodeorg FROM '.$dbname.".kebun_peta WHERE kodeorg like '".$kebun."%' and tipe like 'blok%'";
        }

        if ('textcity' === $cek) {
            $kegiatan = 'SELECT kodeorg FROM '.$dbname.".kebun_peta WHERE tipe like 'kota%'";
        }

        if ('pathroad' === $cek) {
            $kegiatan = 'SELECT kodeorg FROM '.$dbname.".kebun_peta WHERE tipe like 'jalan%'";
        }

        if ('pathriver' === $cek) {
            $kegiatan = 'SELECT kodeorg FROM '.$dbname.".kebun_peta WHERE tipe like 'sungai%'";
        }

        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $hasil .= $res['kodeorg'].'**';
        }
        echo $hasil;

        break;
    case 'show_kontrol':
        $asd = ambilviewbox($dbname, $conn, $kebun);
        list(, , , , $pengurangx, $pengurangy) = $asd;
        $warna[0] = '#0404B4';
        $warna[1] = '#0066FF';
        $warna[2] = '#00CCFF';
        $warna[3] = '#3300FF';
        $warna[4] = '#3366FF';
        $warna[5] = '#3399FF';
        $warna[6] = '#33FFFF';
        $warna[7] = '#6633FF';
        $warna[8] = '#6699FF';
        $warna[9] = '#66FFFF';
        $warna[10] = '#9933FF';
        $warna[11] = '#9999FF';
        $warna[12] = '#99FFFF';
        $warna[13] = '#CC33FF';
        $warna[14] = '#CC99FF';
        $warna[15] = '#CCFFFF';
        $warna[16] = '#FF6F00';
        $warna[17] = '#FF99FF';
        $warna[18] = '#0033CC';
        $warna[19] = '#0099CC';
        $warna[20] = '#00FFCC';
        $warna[21] = '#3333CC';
        $warna[22] = '#3399CC';
        $warna[23] = '#33FFCC';
        $warna[24] = '#6633CC';
        $warna[25] = '#6699CC';
        $warna[26] = '#66FFCC';
        $warna[27] = '#9933CC';
        $warna[28] = '#9999CC';
        $warna[29] = '#99FFCC';
        $kegiatan = 'SELECT * FROM '.$dbname.".setup_blok WHERE kodeorg = '".$kodeorg."'";
        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $tahuntanam[$res['tahuntanam']] = $res['tahuntanam'];
        }
        if (!empty($tahuntanam)) {
            foreach ($tahuntanam as $tnm) {
            }
        }

        echo "<svg id=map onmousemove=\"geserklik(evt)\" onmousedown=\"mulaiklik(evt)\" onmouseup=\"selesaiklik(evt)\" version=\"1.1\" baseProfile=\"full\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" \n            xml:space=\"preserve\" preserveAspectRatio=\"xMinYMin meet\"  width=\"795px\" height=\"555px\" viewBox=\"".$viewbox.'">';
        echo '<g id=blok style="display:inline;fill-rule:evenodd">';
        echo '<desc>Layer '.$res['kodeorg'].'</desc>';
        $id = 0;
        if ('divisi' === $kontrol) {
            $kegiatan = 'SELECT a.*, b.tahuntanam FROM '.$dbname.".kebun_peta a\n            left join ".$dbname.".setup_blok b on a.kodeorg=b.kodeorg\n            WHERE a.kodeorg like '".$kebun."%' order by a.kodeorg";
        }

        if ('tahuntanam' === $kontrol) {
            $kegiatan = 'SELECT a.*, b.tahuntanam FROM '.$dbname.".kebun_peta a\n            left join ".$dbname.".setup_blok b on a.kodeorg=b.kodeorg\n            WHERE a.kodeorg like '".$kebun."%' order by b.tahuntanam";
        }

        $query = mysql_query($kegiatan) || exit(mysql_error($conns));
        $i = 0;
        $qwe = '';
        while ($res = mysql_fetch_assoc($query)) {
            if ('divisi' === $kontrol) {
                $div = substr($res['kodeorg'], 4, 2);
                if ($qwe !== $div) {
                    ++$i;
                    $qwe = $div;
                    $divisi[$i] = $i;
                    $namadivisi[$i] = $div;
                }

                $warnanya = $warna[$i];
            }

            if ('tahuntanam' === $kontrol) {
                $div = $res['tahuntanam'];
                if ($qwe !== $div) {
                    ++$i;
                    $qwe = $div;
                    $divisi[$i] = $i;
                    $namadivisi[$i] = $div;
                }

                $warnanya = $warna[$i];
            }

            $splitel = explode('l', $res['path']);
            $splitem = explode('M', $splitel[0]);
            $splitkoma = explode(',', $splitem[1]);
            $jadinya0 = $splitkoma[0] - $pengurangx;
            $jadinya1 = $splitkoma[1] - $pengurangy;
            $trilili = 'M'.$jadinya0.','.$jadinya1.' l '.$splitel[1];
            echo '<path id="'.$res['kodeorg'].'" d="'.$trilili."\" title='".$res['kodeorg']."'\n                onmouseover=\"evt.target.setAttribute('opacity', '0.5')\"\n                onmouseout=\"evt.target.setAttribute('opacity', '1')\"\n                onclick=\"gantul2('".$res['kodeorg']."')\"\n                style=\"fill:".$warnanya.';stroke-linejoin:round;stroke:black;stroke-width:0.0001;cursor:pointer;"/>';
            echo '</g>';
        }
        echo '</svg>####';
        if ('divisi' === $kontrol) {
            $apaanke = 'Divisi ';
        }

        if ('tahuntanam' === $kontrol) {
            $apaanke = 'Tahun Tanam ';
        }

        echo '<table class=sortable cellspacing=1 border=0 width=100%>';
        if (!empty($divisi)) {
            foreach ($divisi as $div) {
                echo '<tr class=rowcontent>';
                echo '<td bgcolor="'.$warna[$div].'">&nbsp</td><td>'.$apaanke.' '.$namadivisi[$div].'</td>';
                echo '</tr>';
            }
        }

        echo '</table>';

        break;
    default:
        break;
}
function ambilviewbox($dbname, $conn, $kebun)
{
    $kegiatan = 'SELECT * FROM '.$dbname.".kebun_peta WHERE kodeorg = '".$kebun."'";
    $query = mysql_query($kegiatan) || exit(mysql_error($conns));
    while ($res = mysql_fetch_assoc($query)) {
        $viewbox = $res['viewbox'];
    }
    $pengurangx = $pengurangy = 0;
    $asd = explode(' ', $viewbox);
    if (1000 < $asd[0] || $asd[0] < -1000) {
        $qwe = floor($asd[0] / 1000);
        $qwex = explode('.', $qwe);
        $qwe = $qwex[0];
        $qwe *= 1000;
        $asd[0] -= $qwe;
        $pengurangx = $qwe;
    }

    if (1000 < $asd[1] || $asd[1] < -1000) {
        $qwe = $asd[1] / 1000;
        $qwex = explode('.', $qwe);
        $qwe = $qwex[0];
        $qwe *= 1000;
        $asd[1] -= $qwe;
        $pengurangy = $qwe;
    }

    $asd[4] = $pengurangx;
    $asd[5] = $pengurangy;

    return $asd;
}

function tengahx($ja0, $spel)
{
    $lastx = $ja0;
    $splitsp = explode(' ', trim($spel));
    if (!empty($splitsp)) {
        foreach ($splitsp as $londiko) {
            if ('Z' !== trim($londiko)) {
                $splitko = explode(',', $londiko);
                $lastx = $lastx + $splitko[0];
                $spko[] = $lastx;
            }
        }
    }

    $maxx = max($spko);
    $minn = min($spko);

    return $minn + ($maxx - $minn) / 2;
}

function tengahy($ja1, $spel)
{
    $lastx = $ja1;
    $splitsp = explode(' ', trim($spel));
    if (!empty($splitsp)) {
        foreach ($splitsp as $londiko) {
            if ('Z' !== trim($londiko)) {
                $splitko = explode(',', $londiko);
                $lastx = $lastx + $splitko[1];
                $spko[] = $lastx;
            }
        }
    }

    $maxx = max($spko);
    $minn = min($spko);

    return $minn + ($maxx - $minn) / 2;
}

?>