<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $awal = $param['awal'];
        $sampai = $param['sampai'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select periode,sum(total) as total from '.$dbname.".sdm_lembur_vw where\r\n                          periode>='".$awal."' and periode<='".$sampai."' group by periode";
        } else {
            $str = 'select periode,sum(total) as total from '.$dbname.".sdm_lembur_vw where\r\n                          periode>='".$awal."' and periode<='".$sampai."' and kodeorg='".$kodeorg."' group by periode";
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
        $graph->title->Set('MONITORING BIAYA LEMBUR '.$param['pks']);
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
        $bplot->SetLegend('Biaya Lembur');
        $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
        $bplot->SetColor('navy');
        $graph->Add($bplot);
        $graph->StrokeCSIM();

        break;
    case 'rinci':
        $waktu = $param['periode'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select kodeorg,sum(total) as total from '.$dbname.".sdm_lembur_vw where\r\n                           periode='".$waktu."' group by kodeorg";
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
            $graph->title->Set('MONITORING BIAYA LEMBUR '.$kodeorg.' Periode:'.$waktu);
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
            $bplot->SetLegend('Biaya Lembur');
            $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
            $bplot->SetColor('navy');
            $graph->Add($bplot);
            $graph->StrokeCSIM();
            echo '<a href=javascript:history.back(-1)>Back</a>';
        } else {
            echo "<link rel=stylesheet tyle=text href='style/generic.css'>\r\n                              <script language=javascript src='js/generic.js'></script>";
            $str = 'select b.namakaryawan as nama,a.karyawanid,b.subbagian,c.tipe,sum(a.uangmakan+a.uangtransport+a.uangkelebihanjam) as total,left(tanggal,7) as periode from '.$dbname.".sdm_lemburdt a \r\n                               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                               left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan=c.id    \r\n                               where\r\n                               a.tanggal like '".substr($waktu, 0, 4)."%' and left(a.kodeorg,4)='".$kodeorg."' \r\n                               group by a.karyawanid,left(tanggal,7) order by total  desc,periode";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $karid[$bar->karyawanid] = $bar->karyawanid;
                $nama[$bar->karyawanid] = $bar->nama;
                $lembur[$bar->karyawanid][$bar->periode] = $bar->total;
                $total[$bar->karyawanid] += $bar->total;
                $tipe[$bar->karyawanid] = $bar->tipe;
                $subbagian[$bar->karyawanid] = $bar->subbagian;
            }
            arsort($total);
            echo 'MONITORING BIAYA LEMBUR '.$kodeorg.' Periode:'.substr($waktu, 0, 4)."                    \r\n                            <table class=sortable cellspacing=1 border=0>\r\n                           <thead><tr class=rowheader>\r\n                           <td>".$_SESSION['lang']['urut']."</td>\r\n                           <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                           <td>".$_SESSION['lang']['tipekaryawan']."</td>\r\n                            <td>".$_SESSION['lang']['subbagian']."</td>   \r\n                           <td>Jan ".substr($waktu, 0, 4)."</td>\r\n                           <td>Feb ".substr($waktu, 0, 4)."</td>\r\n                           <td>Mar ".substr($waktu, 0, 4)."</td>\r\n                           <td>Apr ".substr($waktu, 0, 4)."</td>\r\n                           <td>Mei ".substr($waktu, 0, 4)."</td>\r\n                           <td>Jun ".substr($waktu, 0, 4)."</td>\r\n                           <td>Jul ".substr($waktu, 0, 4)."</td>    \r\n                           <td>Aug ".substr($waktu, 0, 4)."</td>\r\n                           <td>Sep ".substr($waktu, 0, 4)."</td>\r\n                           <td>Okt ".substr($waktu, 0, 4)."</td>\r\n                           <td>Nop ".substr($waktu, 0, 4)."</td>\r\n                           <td>Des ".substr($waktu, 0, 4)."</td>    \r\n                           <td>Total ".substr($waktu, 0, 4)."</td>\r\n                           </tr></thead>\r\n                           <tbody>";
            $no = 0;
            $GT = 0;
            foreach ($total as $val => $ttt) {
                ++$no;
                echo "<tr class=rowcontent>\r\n                           <td>".$no."</td>\r\n                           <td>".$nama[$val]."</td>\r\n                           <td>".$tipe[$val]."</td>\r\n                           <td>".$subbagian[$val]."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-01'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-02'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-03'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-04'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-05'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-06'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-07'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-08'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-09'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-10'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-11'])."</td>\r\n                            <td align=right>".number_format($lembur[$val][substr($waktu, 0, 4).'-12'])."</td>  \r\n                           <td align=right>".number_format($total[$val])."</td>\r\n                           </tr>";
                $tt[substr($waktu, 0, 4).'-01'] += $lembur[$val][substr($waktu, 0, 4).'-01'];
                $tt[substr($waktu, 0, 4).'-02'] += $lembur[$val][substr($waktu, 0, 4).'-02'];
                $tt[substr($waktu, 0, 4).'-03'] += $lembur[$val][substr($waktu, 0, 4).'-03'];
                $tt[substr($waktu, 0, 4).'-04'] += $lembur[$val][substr($waktu, 0, 4).'-04'];
                $tt[substr($waktu, 0, 4).'-05'] += $lembur[$val][substr($waktu, 0, 4).'-05'];
                $tt[substr($waktu, 0, 4).'-06'] += $lembur[$val][substr($waktu, 0, 4).'-06'];
                $tt[substr($waktu, 0, 4).'-07'] += $lembur[$val][substr($waktu, 0, 4).'-07'];
                $tt[substr($waktu, 0, 4).'-08'] += $lembur[$val][substr($waktu, 0, 4).'-08'];
                $tt[substr($waktu, 0, 4).'-09'] += $lembur[$val][substr($waktu, 0, 4).'-09'];
                $tt[substr($waktu, 0, 4).'-10'] += $lembur[$val][substr($waktu, 0, 4).'-10'];
                $tt[substr($waktu, 0, 4).'-11'] += $lembur[$val][substr($waktu, 0, 4).'-11'];
                $tt[substr($waktu, 0, 4).'-12'] += $lembur[$val][substr($waktu, 0, 4).'-12'];
                $GT += $total[$val];
            }
            echo "</tbody><tfoot>\r\n                             <tr class=rowcontent>\r\n                                 <td colspan=4>".$_SESSION['lang']['total']."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-01'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-02'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-03'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-04'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-05'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-06'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-07'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-08'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-09'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-10'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-11'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-12'])."</td>    \r\n                                 <td align=right>".number_format($GT)."</td>\r\n                              </tr>\r\n                            </tfoot></table>";
            echo '<a href=javascript:history.back(-1)>Back</a>';
        }

        break;
}

?>