<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'lib/eagrolib.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $tahun = $param['tahun'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select periode,sum(jlhbayar) as total from '.$dbname.".sdm_pengobatanht where\n                          periode like'".$tahun."%' group by periode";
        } else {
            $str = 'select a.periode,sum(a.jlhbayar) as total from '.$dbname.".sdm_pengobatanht a \n                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\n                           where a.periode like'".$tahun."%' and b.lokasitugas='".$kodeorg."' group by a.periode";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $periode[] = $bar->periode;
            $total[] = $bar->total / 1000000;
            $targ[] = '?periode='.$bar->periode.'&pks='.$kodeorg.'&jenis=rinci';
            $alts[] = number_format($bar->total / 1000000, 2).', Click to Drill';
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(60, 20, 30, 50);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('silver');
        $graph->SetShadow();
        $graph->title->Set('MONITORING BIAYA PENGOBATAN '.$kodeorg);
        $graph->title->SetColor('darkred');
        $graph->xaxis->SetColor('black', 'red');
        $graph->yscale->ticks->SupressZeroLabel(true);
        $graph->yaxis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($periode);
        $graph->xaxis->SetLabelAngle(60);
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $txt = new Text('Rp(Juta)');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $bplot = new BarPlot($total);
        $bplot->SetWidth(0.6);
        $bplot->SetCSIMTargets($targ, $alts);
        $bplot->SetLegend('Biaya Pengobatan');
        $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
        $bplot->SetColor('navy');
        $graph->Add($bplot);
        $graph->StrokeCSIM();
        echo '<br>';
        if ('' === $kodeorg) {
            $str = 'select lokasitugas,sum(jlhbayar) as total from '.$dbname.".sdm_pengobatanht a\n                         left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\n                         where\n                          periode like'".$tahun."%' group by lokasitugas";
        } else {
            $str = 'select b.lokasitugas,sum(a.jlhbayar) as total from '.$dbname.".sdm_pengobatanht a \n                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\n                           where a.periode like'".$tahun."%' and b.lokasitugas='".$kodeorg."' group by b.lokasitugas";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $lokasitugas[] = $bar->lokasitugas;
            $total1[] = $bar->total;
        }
        echo 'Total Pengobatan By Unit '.$tahun."<br><table border=1px cellspacing=0 style='font-size:12px;'><tr>";
        foreach ($lokasitugas as $key => $val) {
            echo '<td align=center>'.$val.'</td>';
        }
        echo '<td>Total</td></tr><tr>';
        foreach ($total1 as $key => $val) {
            echo '<td align=right>'.number_format($val, 2, ',', '.').'</td>';
            $tt += $val;
        }
        echo '<td align=right>'.number_format($tt, 2, ',', '.').'</td></tr>';
        echo '</table>';
        if ('' === $kodeorg) {
            $str = 'select b.id,b.namars,sum(jlhbayar) as total from '.$dbname.".sdm_pengobatanht a\n                         left join ".$dbname.".sdm_5rs b on a.rs=b.id\n                         where\n                          periode like'".$tahun."%' group by b.id order by total desc";
        } else {
            $str = 'select b.id,b.namars,sum(a.jlhbayar) as total from '.$dbname.".sdm_pengobatanht a \n                          left join ".$dbname.".sdm_5rs b on a.rs=b.id\n                          left join     ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid\n                           where a.periode like'".$tahun."%' and c.lokasitugas='".$kodeorg."' group by b.id order by total desc";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $lokasitugas2[] = $bar->namars;
            $total2[] = $bar->total;
        }
        echo '<br>Total Pengobatan By Rs'.$tahun."<br><table border=1px cellspacing=0 style='font-size:12px;'>";
        $no = 1;
        foreach ($lokasitugas2 as $key => $val) {
            echo '<tr><td>'.$no.'<td>'.$val.'</td><td align=right>'.number_format($total2[$key], 2, ',', '.').'</td></tr>';
            $tt2 += $total2[$key];
            ++$no;
        }
        echo '<tr><td colspan=2>Total</td><td align=right>'.number_format($tt2, 2, ',', '.').'</td></tr>';
        echo '</table>';

        break;
    case 'rinci':
        $waktu = $param['periode'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select b.lokasitugas as kodeorg,sum(a.jlhbayar) as total from '.$dbname.".sdm_pengobatanht a \n                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\n                          where  a.periode like '".$waktu."%' group by b.lokasitugas";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $periode[] = $bar->kodeorg;
                $total[] = $bar->total / 1000000;
                $targ[] = '?periode='.$waktu.'&pks='.$bar->kodeorg.'&jenis=rinci';
                $alts[] = number_format($bar->total / 1000000, 2).', Click to Drill';
            }
            $graph = new Graph(800, 400);
            $graph->img->SetMargin(60, 20, 30, 50);
            $graph->SetScale('textlin');
            $graph->SetMarginColor('silver');
            $graph->SetShadow();
            $graph->title->Set('MONITORING BIAYA PENGOBATAN '.$kodeorg.' Periode:'.$waktu);
            $graph->title->SetColor('darkred');
            $graph->xaxis->SetColor('black', 'red');
            $graph->yscale->ticks->SupressZeroLabel(true);
            $graph->yaxis->scale->SetGrace(30);
            $graph->xaxis->SetTickLabels($periode);
            $graph->xaxis->SetLabelAngle(60);
            $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
            $txt = new Text('Rp(Juta)');
            $txt->SetPos(0.02, 0.1, 'left', 'bottom');
            $txt->SetBox('white', 'black');
            $graph->AddText($txt);
            $bplot = new BarPlot($total);
            $bplot->SetWidth(0.6);
            $bplot->SetCSIMTargets($targ, $alts);
            $bplot->SetLegend('Biaya Pengobatan');
            $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
            $bplot->SetColor('navy');
            $graph->Add($bplot);
            $graph->StrokeCSIM();
            echo '<a href=javascript:history.back(-1)>Back</a>';
        } else {
            echo "<link rel=stylesheet tyle=text href='style/generic.css'>\n                              <script language=javascript src='js/generic.js'></script>";
            $str = 'select b.namakaryawan as nama,a.karyawanid,b.subbagian,c.tipe,sum(a.jlhbayar) as total,a.periode from '.$dbname.".sdm_pengobatanht a \n                               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\n                               left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan=c.id    \n                               where\n                               a.periode like '".substr($waktu, 0, 4)."%' and b.lokasitugas='".$kodeorg."' \n                               group by a.karyawanid,a.periode order by total  desc,periode";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $karid[$bar->karyawanid] = $bar->karyawanid;
                $nama[$bar->karyawanid] = $bar->nama;
                $klaim[$bar->karyawanid][$bar->periode] = $bar->total;
                $total[$bar->karyawanid] += $bar->total;
                $tipe[$bar->karyawanid] = $bar->tipe;
                $subbagian[$bar->karyawanid] = $bar->subbagian;
            }
            arsort($total);
            echo 'MONITORING BIAYA PENGOBATAN '.$kodeorg.' Periode:'.substr($waktu, 0, 4)."                    \n                            <table class=sortable cellspacing=1 border=0>\n                           <thead><tr class=rowheader>\n                           <td>".$_SESSION['lang']['urut']."</td>\n                           <td>".$_SESSION['lang']['namakaryawan']."</td>\n                           <td>".$_SESSION['lang']['tipekaryawan']."</td>\n                            <td>".$_SESSION['lang']['subbagian']."</td>   \n                           <td>Jan ".substr($waktu, 0, 4)."</td>\n                           <td>Feb ".substr($waktu, 0, 4)."</td>\n                           <td>Mar ".substr($waktu, 0, 4)."</td>\n                           <td>Apr ".substr($waktu, 0, 4)."</td>\n                           <td>Mei ".substr($waktu, 0, 4)."</td>\n                           <td>Jun ".substr($waktu, 0, 4)."</td>\n                           <td>Jul ".substr($waktu, 0, 4)."</td>    \n                           <td>Aug ".substr($waktu, 0, 4)."</td>\n                           <td>Sep ".substr($waktu, 0, 4)."</td>\n                           <td>Okt ".substr($waktu, 0, 4)."</td>\n                           <td>Nop ".substr($waktu, 0, 4)."</td>\n                           <td>Des ".substr($waktu, 0, 4)."</td>    \n                           <td>Total ".substr($waktu, 0, 4)."</td>\n                           </tr></thead>\n                           <tbody>";
            $no = 0;
            $GT = 0;
            foreach ($total as $val => $ttt) {
                ++$no;
                echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"window.location='?tahun=".$waktu.'&karyawanid='.$karid[$val].'&jenis=diagnosa&nama='.$nama[$val]."';\">\n                           <td>".$no."</td>\n                           <td>".$nama[$val]."</td>\n                           <td>".$tipe[$val]."</td>\n                           <td>".$subbagian[$val]."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-01'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-02'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-03'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-04'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-05'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-06'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-07'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-08'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-09'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-10'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-11'])."</td>\n                            <td align=right>".number_format($klaim[$val][substr($waktu, 0, 4).'-12'])."</td>  \n                           <td align=right>".number_format($total[$val])."</td>\n                           </tr>";
                $tt[substr($waktu, 0, 4).'-01'] += $klaim[$val][substr($waktu, 0, 4).'-01'];
                $tt[substr($waktu, 0, 4).'-02'] += $klaim[$val][substr($waktu, 0, 4).'-02'];
                $tt[substr($waktu, 0, 4).'-03'] += $klaim[$val][substr($waktu, 0, 4).'-03'];
                $tt[substr($waktu, 0, 4).'-04'] += $klaim[$val][substr($waktu, 0, 4).'-04'];
                $tt[substr($waktu, 0, 4).'-05'] += $klaim[$val][substr($waktu, 0, 4).'-05'];
                $tt[substr($waktu, 0, 4).'-06'] += $klaim[$val][substr($waktu, 0, 4).'-06'];
                $tt[substr($waktu, 0, 4).'-07'] += $klaim[$val][substr($waktu, 0, 4).'-07'];
                $tt[substr($waktu, 0, 4).'-08'] += $klaim[$val][substr($waktu, 0, 4).'-08'];
                $tt[substr($waktu, 0, 4).'-09'] += $klaim[$val][substr($waktu, 0, 4).'-09'];
                $tt[substr($waktu, 0, 4).'-10'] += $klaim[$val][substr($waktu, 0, 4).'-10'];
                $tt[substr($waktu, 0, 4).'-11'] += $klaim[$val][substr($waktu, 0, 4).'-11'];
                $tt[substr($waktu, 0, 4).'-12'] += $klaim[$val][substr($waktu, 0, 4).'-12'];
                $GT += $total[$val];
            }
            echo "</tbody><tfoot>\n                             <tr class=rowcontent>\n                                 <td colspan=4>".$_SESSION['lang']['total']."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-01'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-02'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-03'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-04'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-05'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-06'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-07'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-08'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-09'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-10'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-11'])."</td>\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-12'])."</td>    \n                                 <td align=right>".number_format($GT)."</td>\n                              </tr>\n                            </tfoot></table>";
            echo '<a href=javascript:history.back(-1)>Back</a>';
        }

        break;
    case 'diagnosa':
        $str = 'select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, e.lokasitugas as loktug,nama from '.$dbname.".sdm_pengobatanht a left join\n      ".$dbname.".sdm_5rs b on a.rs=b.id \n\t  left join ".$dbname.".datakaryawan c\n\t  on a.karyawanid=c.karyawanid\n\t  left join ".$dbname.".sdm_5diagnosa d\n\t  on a.diagnosa=d.id\n          left join ".$dbname.".datakaryawan e\n\t  on a.karyawanid=e.karyawanid\n        left join ".$dbname.".sdm_karyawankeluarga f\n        on a.ygsakit=f.nomor\n\t  where a.periode like '".substr($_GET['tahun'], 0, 4)."%'\n\t  and a.karyawanid = ".$_GET['karyawanid']."\n          order by a.updatetime desc, a.tanggal desc";
        $res = mysql_query($str);
        $tab .= "<link rel=stylesheet tyle=text href='style/generic.css'>\n          <script language=javascript src='js/generic.js'></script>";
        $tab .= 'Pengobatan Atas Nama :'.$_GET['nama'].' Periode:'.substr($_GET['tahun'], 0, 4)."\n     <table class=sortable cellspacing=1 border=0>\n    <thead>\n    <tr class=rowheader>\n        <td>No</td>\n        <td>".$_SESSION['lang']['tanggal']."</td>\n        <td >".$_SESSION['lang']['jenis']."</td>            \n        <td >".$_SESSION['lang']['namakaryawan']."</td>\n        <td>".$_SESSION['lang']['pasien']."</td>\n        <td >".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\n        <td >".$_SESSION['lang']['rumahsakit']."</td>\n        <td >".$_SESSION['lang']['nilaiklaim']."</td>\n        <td>".$_SESSION['lang']['diagnosa']."</td>\n    </tr>\n    </thead>\n    \n    <tbody id='container'>";
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $pasien = '';
            $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \n            where nomor=".$bar->ygsakit;
            $resu = mysql_query($stru);
            while ($baru = mysql_fetch_object($resu)) {
                $pasien = $baru->hubungankeluarga;
            }
            if ('' === $pasien) {
                $pasien = 'AsIs';
            }

            $tab .= "<tr class=rowcontent>\n            <td>".$no."</td>\n            <td>".tanggalnormal($bar->tanggal)."</td>\n            <td>".$bar->kodebiaya."</td>\n            <td>".$bar->namakaryawan."</td>\n            <td>".$pasien."</td>\n            <td>".$bar->nama."</td>\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\n            <td align=right>".number_format($bar->jlhbayar, 2, '.', ',')."</td>\n            <td>".$bar->ketdiag."</td>\n        </tr>";
        }
        $tab .= "</tbody>\n    <tfoot>\n    </tfoot>\n    </table>";
        echo $tab;
        echo '<a href=javascript:history.back(-1)>Back</a>';
}

?>