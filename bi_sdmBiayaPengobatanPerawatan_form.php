<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $tahun = $param['tahun'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select a.periode,sum(a.jlhbayar) as total,a.kodebiaya,b.nama from '.$dbname.".sdm_pengobatanht a\r\n                         left join ".$dbname.".sdm_5jenisbiayapengobatan b on a.kodebiaya=b.kode \r\n                         where\r\n                          periode like'".$tahun."%' group by periode,kodebiaya order by periode";
        } else {
            $str = 'select a.periode,sum(a.jlhbayar) as total,a.kodebiaya,c.nama from '.$dbname.".sdm_pengobatanht a \r\n                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          left join ".$dbname.".sdm_5jenisbiayapengobatan c on a.kodebiaya=c.kode\r\n                           where a.periode like'".$tahun."%' and b.lokasitugas='".$kodeorg."' group by a.periode,kodebiaya order by periode";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $kode[$bar->kodebiaya] = $bar->nama;
            $total[$bar->periode][$bar->kodebiaya] = $bar->total / 1000000;
        }
        foreach ($kode as $gg => $val) {
            $nilai[$gg][0] = $total[$tahun.'-01'][$gg];
            $nilai[$gg][1] = $total[$tahun.'-02'][$gg];
            $nilai[$gg][2] = $total[$tahun.'-03'][$gg];
            $nilai[$gg][3] = $total[$tahun.'-04'][$gg];
            $nilai[$gg][4] = $total[$tahun.'-05'][$gg];
            $nilai[$gg][5] = $total[$tahun.'-06'][$gg];
            $nilai[$gg][6] = $total[$tahun.'-07'][$gg];
            $nilai[$gg][7] = $total[$tahun.'-08'][$gg];
            $nilai[$gg][8] = $total[$tahun.'-09'][$gg];
            $nilai[$gg][9] = $total[$tahun.'-10'][$gg];
            $nilai[$gg][10] = $total[$tahun.'-11'][$gg];
            $nilai[$gg][11] = $total[$tahun.'-12'][$gg];
            $periode[] = $tahun.'-01';
            $periode[] = $tahun.'-02';
            $periode[] = $tahun.'-03';
            $periode[] = $tahun.'-04';
            $periode[] = $tahun.'-05';
            $periode[] = $tahun.'-06';
            $periode[] = $tahun.'-07';
            $periode[] = $tahun.'-08';
            $periode[] = $tahun.'-09';
            $periode[] = $tahun.'-10';
            $periode[] = $tahun.'-11';
            $periode[] = $tahun.'-12';
        }
        foreach ($periode as $kj => $jk) {
            $targ[] = '?periode='.$jk.'&pks='.$kodeorg.'&jenis=rinci';
            $alts[] = ' Click to Drill';
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(60, 20, 30, 50);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('silver');
        $graph->SetShadow();
        $graph->title->Set('BIAYA PENGOBATAN BERDASARKAN PERAWATAN  '.$namatipe.' Periode '.$param['tahun']);
        $graph->title->SetColor('darkred');
        $graph->xaxis->SetColor('black', 'red');
        $graph->yscale->ticks->SupressZeroLabel(true);
        $graph->yaxis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($periode);
        $graph->xaxis->SetLabelAngle(90);
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $txt = new Text('Rp(Juta)');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $x = 0;
        foreach ($kode as $key => $val) {
            $Gar[$x] = new BarPlot($nilai[$key]);
            $Gar[$x]->SetWidth(1);
            $Gar[$x]->SetCSIMTargets($targ, $alts);
            $Gar[$x]->SetLegend($val);
            ++$x;
        }
        $gbar = new GroupbarPlot($Gar);
        $graph->Add($gbar);
        $graph->StrokeCSIM();

        break;
    case 'rinci':
        $waktu = $param['periode'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select b.lokasitugas as kodeorg,sum(a.jlhbayar) as total,a.kodebiaya,c.nama from '.$dbname.".sdm_pengobatanht a \r\n                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          left join ".$dbname.".sdm_5jenisbiayapengobatan c on a.kodebiaya=c.kode    \r\n                          where  a.periode like '".$waktu."%' group by b.lokasitugas,a.kodebiaya";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $kodeorg[$bar->kodeorg] = $bar->kodeorg;
                $kode[$bar->kodebiaya] = $bar->kodebiaya;
                $total[$bar->kodeorg][$bar->kodebiaya] = $bar->total / 1000000;
            }
            foreach ($kode as $hh => $ii) {
                foreach ($kodeorg as $key => $val) {
                    $bax[$hh][] = $total[$key][$hh];
                }
            }
            foreach ($kodeorg as $ww => $zz) {
                $targ[] = '?periode='.$waktu.'&pks='.$ww.'&jenis=rinci';
                $alts[] = ' Click to Drill';
                $label[] = $ww;
            }
            $graph = new Graph(800, 400);
            $graph->img->SetMargin(60, 20, 30, 50);
            $graph->SetScale('textlin');
            $graph->SetMarginColor('silver');
            $graph->SetShadow();
            $graph->title->Set('BIAYA PENGOBATAN BERDASARKAN PERAWATAN   Periode '.$param['periode']);
            $graph->title->SetColor('darkred');
            $graph->xaxis->SetColor('black', 'red');
            $graph->yscale->ticks->SupressZeroLabel(true);
            $graph->yaxis->scale->SetGrace(30);
            $graph->xaxis->SetTickLabels($label);
            $graph->xaxis->SetLabelAngle(90);
            $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
            $txt = new Text('Rp(Juta)');
            $txt->SetPos(0.02, 0.1, 'left', 'bottom');
            $txt->SetBox('white', 'black');
            $graph->AddText($txt);
            $x = 0;
            foreach ($kode as $key => $val) {
                $Gar[$x] = new BarPlot($bax[$key]);
                $Gar[$x]->SetWidth(1);
                $Gar[$x]->SetCSIMTargets($targ, $alts);
                $Gar[$x]->SetLegend($val);
                ++$x;
            }
            $gbar = new GroupbarPlot($Gar);
            $graph->Add($gbar);
            $graph->StrokeCSIM();
            echo '<a href=javascript:history.back(-1)>Back</a>';
        } else {
            echo "<link rel=stylesheet tyle=text href='style/generic.css'>\r\n                              <script language=javascript src='js/generic.js'></script>";
            $str = 'select a.periode,sum(a.jlhbayar) as total,a.kodebiaya,c.nama from '.$dbname.".sdm_pengobatanht a \r\n                              left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                              left join ".$dbname.".sdm_5jenisbiayapengobatan c on a.kodebiaya=c.kode\r\n                               where a.periode like'".substr($waktu, 0, 4)."%' and b.lokasitugas='".$kodeorg."' group by a.periode,kodebiaya \r\n                               order by periode";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $kode[$bar->kodebiaya] = $bar->nama;
                $total[$bar->periode][$bar->kodebiaya] = $bar->total;
            }
            foreach ($kode as $gg => $val) {
                $nilai[$gg][0] = $total[substr($waktu, 0, 4).'-01'][$gg];
                $nilai[$gg][1] = $total[substr($waktu, 0, 4).'-02'][$gg];
                $nilai[$gg][2] = $total[substr($waktu, 0, 4).'-03'][$gg];
                $nilai[$gg][3] = $total[substr($waktu, 0, 4).'-04'][$gg];
                $nilai[$gg][4] = $total[substr($waktu, 0, 4).'-05'][$gg];
                $nilai[$gg][5] = $total[substr($waktu, 0, 4).'-06'][$gg];
                $nilai[$gg][6] = $total[substr($waktu, 0, 4).'-07'][$gg];
                $nilai[$gg][7] = $total[substr($waktu, 0, 4).'-08'][$gg];
                $nilai[$gg][8] = $total[substr($waktu, 0, 4).'-09'][$gg];
                $nilai[$gg][9] = $total[substr($waktu, 0, 4).'-10'][$gg];
                $nilai[$gg][10] = $total[substr($waktu, 0, 4).'-11'][$gg];
                $nilai[$gg][11] = $total[substr($waktu, 0, 4).'-12'][$gg];
            }
            echo 'MONITORING BIAYA PENGOBATAN '.$kodeorg.' Periode:'.substr($waktu, 0, 4)."                    \r\n                            <table class=sortable cellspacing=1 border=0>\r\n                           <thead><tr class=rowheader>\r\n                           <td>".$_SESSION['lang']['urut']."</td>\r\n                           <td>".$_SESSION['lang']['kode']."</td>  \r\n                           <td>Jan ".substr($waktu, 0, 4)."</td>\r\n                           <td>Feb ".substr($waktu, 0, 4)."</td>\r\n                           <td>Mar ".substr($waktu, 0, 4)."</td>\r\n                           <td>Apr ".substr($waktu, 0, 4)."</td>\r\n                           <td>Mei ".substr($waktu, 0, 4)."</td>\r\n                           <td>Jun ".substr($waktu, 0, 4)."</td>\r\n                           <td>Jul ".substr($waktu, 0, 4)."</td>    \r\n                           <td>Aug ".substr($waktu, 0, 4)."</td>\r\n                           <td>Sep ".substr($waktu, 0, 4)."</td>\r\n                           <td>Okt ".substr($waktu, 0, 4)."</td>\r\n                           <td>Nop ".substr($waktu, 0, 4)."</td>\r\n                           <td>Des ".substr($waktu, 0, 4)."</td>    \r\n                           <td>Total ".substr($waktu, 0, 4)."</td>\r\n                           </tr></thead>\r\n                           <tbody>";
            $no = 0;
            $GT = 0;
            foreach ($kode as $val => $ttt) {
                ++$no;
                $tbaris = $nilai[$val][0] + $nilai[$val][1] + $nilai[$val][2] + $nilai[$val][3] + $nilai[$val][4] + $nilai[$val][5] + $nilai[$val][6] + $nilai[$val][7] + $nilai[$val][8] + $nilai[$val][9] + $nilai[$val][10] + $nilai[$val][11] + $nilai[$val][12];
                echo "<tr class=rowcontent>\r\n                           <td>".$no."</td>\r\n                           <td>".$kode[$val]."</td>\r\n                            <td align=right>".number_format($nilai[$val][0])."</td>\r\n                            <td align=right>".number_format($nilai[$val][1])."</td>\r\n                            <td align=right>".number_format($nilai[$val][2])."</td> \r\n                            <td align=right>".number_format($nilai[$val][3])."</td> \r\n                            <td align=right>".number_format($nilai[$val][4])."</td> \r\n                            <td align=right>".number_format($nilai[$val][5])."</td> \r\n                            <td align=right>".number_format($nilai[$val][6])."</td> \r\n                            <td align=right>".number_format($nilai[$val][7])."</td> \r\n                            <td align=right>".number_format($nilai[$val][8])."</td> \r\n                            <td align=right>".number_format($nilai[$val][9])."</td> \r\n                            <td align=right>".number_format($nilai[$val][10])."</td>\r\n                            <td align=right>".number_format($nilai[$val][11])."</td>  \r\n                           <td align=right>".number_format($tbaris)."</td>\r\n                           </tr>";
                $tt[substr($waktu, 0, 4).'-01'] += $nilai[$val][0];
                $tt[substr($waktu, 0, 4).'-02'] += $nilai[$val][1];
                $tt[substr($waktu, 0, 4).'-03'] += $nilai[$val][2];
                $tt[substr($waktu, 0, 4).'-04'] += $nilai[$val][3];
                $tt[substr($waktu, 0, 4).'-05'] += $nilai[$val][4];
                $tt[substr($waktu, 0, 4).'-06'] += $nilai[$val][5];
                $tt[substr($waktu, 0, 4).'-07'] += $nilai[$val][6];
                $tt[substr($waktu, 0, 4).'-08'] += $nilai[$val][7];
                $tt[substr($waktu, 0, 4).'-09'] += $nilai[$val][8];
                $tt[substr($waktu, 0, 4).'-10'] += $nilai[$val][9];
                $tt[substr($waktu, 0, 4).'-11'] += $nilai[$val][10];
                $tt[substr($waktu, 0, 4).'-12'] += $nilai[$val][11];
                $GT += $tbaris;
            }
            echo "</tbody><tfoot>\r\n                             <tr class=rowcontent>\r\n                                 <td colspan=2>".$_SESSION['lang']['total']."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-01'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-02'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-03'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-04'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-05'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-06'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-07'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-08'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-09'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-10'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-11'])."</td>\r\n                                 <td align=right>".number_format($tt[substr($waktu, 0, 4).'-12'])."</td>    \r\n                                 <td align=right>".number_format($GT)."</td>\r\n                              </tr>\r\n                            </tfoot></table>";
            echo '<a href=javascript:history.back(-1)>Back</a>';
        }

        break;
}

?>