<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $tahun = $param['tahun'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select sum(beratbersih) as netto,left(tanggal,7) as periode from '.$dbname.".pabrik_timbangan where \r\n                           tanggal like '".$tahun."%' and kodeorg is not null and kodeorg!=''\r\n                           group by left(tanggal,7)";
            $level = 'level1';
        } else {
            $str = 'select sum(beratbersih) as netto,left(tanggal,7) as periode from '.$dbname.".pabrik_timbangan where \r\n                           tanggal like '".$tahun."%' and kodeorg='".$kodeorg."'\r\n                           group by left(tanggal,7)";
            $level = 'level2';
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $netto[$bar->periode] = $bar->netto / 1000;
        }
        if ('' === $kodeorg) {
            $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12 from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".$tahun.' group by tahunbudget';
        } else {
            $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12 from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".$tahun." and kodeunit='".$kodeorg."' group by tahunbudget";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $cap[] = $tahun.'-01';
            $cap[] = $tahun.'-02';
            $cap[] = $tahun.'-03';
            $cap[] = $tahun.'-04';
            $cap[] = $tahun.'-05';
            $cap[] = $tahun.'-06';
            $cap[] = $tahun.'-07';
            $cap[] = $tahun.'-08';
            $cap[] = $tahun.'-09';
            $cap[] = $tahun.'-10';
            $cap[] = $tahun.'-11';
            $cap[] = $tahun.'-12';
            $budget[$tahun.'-01'] = $bar->kg01 / 1000;
            $budget[$tahun.'-02'] = $bar->kg02 / 1000;
            $budget[$tahun.'-03'] = $bar->kg03 / 1000;
            $budget[$tahun.'-04'] = $bar->kg04 / 1000;
            $budget[$tahun.'-05'] = $bar->kg05 / 1000;
            $budget[$tahun.'-06'] = $bar->kg06 / 1000;
            $budget[$tahun.'-07'] = $bar->kg07 / 1000;
            $budget[$tahun.'-08'] = $bar->kg08 / 1000;
            $budget[$tahun.'-09'] = $bar->kg09 / 1000;
            $budget[$tahun.'-10'] = $bar->kg10 / 1000;
            $budget[$tahun.'-11'] = $bar->kg11 / 1000;
            $budget[$tahun.'-12'] = $bar->kg12 / 1000;
            $targ[] = '?periode='.$tahun.'-01'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-02'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-03'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-04'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-05'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-06'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-07'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-08'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-09'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-10'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-11'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-12'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
        }
        if ('' === $kodeorg) {
            $str = 'select sum(luasareaproduktif) as luas from '.$dbname.".setup_blok where statusblok='TM'";
        } else {
            $str = 'select sum(luasareaproduktif) as luas from '.$dbname.".setup_blok where statusblok='TM'\r\n                             and kodeorg like '".$kodeorg."%'";
        }

        $res = mysql_query($str);
        $luas = 0;
        while ($bar = mysql_fetch_object($res)) {
            $luas = $bar->luas;
        }
        if (0 < count($budget)) {
            foreach ($budget as $key => $val) {
                $netto1[] = $netto[$key];
                $budget1[] = $budget[$key];
                $kghaaktual[] = $netto[$key] / $luas;
                $kghabudget[] = $budget[$key] / $luas;
            }
        } else {
            $netto1[] = $netto[$tahun.'-01'];
            $netto1[] = $netto[$tahun.'-02'];
            $netto1[] = $netto[$tahun.'-03'];
            $netto1[] = $netto[$tahun.'-04'];
            $netto1[] = $netto[$tahun.'-05'];
            $netto1[] = $netto[$tahun.'-06'];
            $netto1[] = $netto[$tahun.'-07'];
            $netto1[] = $netto[$tahun.'-08'];
            $netto1[] = $netto[$tahun.'-09'];
            $netto1[] = $netto[$tahun.'-10'];
            $netto1[] = $netto[$tahun.'-11'];
            $netto1[] = $netto[$tahun.'-12'];
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $budget1[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghabudget[] = 0;
            $kghaaktual[] = $netto[$tahun.'-01'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-02'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-03'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-04'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-05'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-06'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-07'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-08'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-09'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-10'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-11'] / $luas;
            $kghaaktual[] = $netto[$tahun.'-12'] / $luas;
            $targ[] = '?periode='.$tahun.'-01'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-02'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-03'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-04'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-05'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-06'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-07'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-08'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-09'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-10'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-11'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
            $targ[] = '?periode='.$tahun.'-12'.'&pks='.$kodeorg.'&jenis='.$level;
            $alts[] = ' Click to Drill';
        }

        $totalProduksi = @array_sum($netto1) / $luas;
        $totalBudget = @array_sum($budget1) / $luas;
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PRODUKSI TBS VS BUDGET PRODUKSI TBS '.$param['pks']);
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $txt = new Text('Ton.');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton/Ha');
        $txt->SetPos(0.93, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton.');
        $txt = new Text('Aktual TBS/Ha/Tahun:'.number_format($totalProduksi, 2).' Ton');
        $txt->SetPos(0.1, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget TBS/Ha/Tahun:'.number_format($totalBudget, 2).' Ton');
        $txt->SetPos(0.1, 0.15, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($netto1);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($budget1);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot3 = new LinePlot($kghaaktual);
        $plot4 = new LinePlot($kghabudget);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Produksi');
        $plot2->SetLegend('Budget');
        $plot3->SetLegend('Ton/Ha Aktual');
        $plot4->SetLegend('Ton/Ha Budget');
        $plot3->mark->SetType(MARK_FILLEDCIRCLE);
        $plot3->mark->SetFillColor('red');
        $plot4->mark->SetType(MARK_FILLEDCIRCLE);
        $plot4->mark->SetFillColor('blue');
        $graph->legend->SetPos(0.7, 0.15, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->AddY2($plot3);
        $graph->AddY2($plot4);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Luas TM :'.number_format($luas, 2).' Ha';
        echo '<br>Total Produksi :'.number_format(array_sum($netto1), 2).' Ton';
        echo '<br>Total Budget :'.number_format(array_sum($budget1), 2).' Ton';

        break;
    case 'level1':
        $tahun = $param['periode'];
        $kodeorg = $param['pks'];
        $str = 'select sum(beratbersih) as netto,kodeorg from '.$dbname.".pabrik_timbangan where \r\n                           tanggal like '".$tahun."%' and kodeorg is not null and kodeorg!=''\r\n                           group by kodeorg";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $netto[$bar->kodeorg] = $bar->netto / 1000;
            $cap[$bar->kodeorg] = $bar->kodeorg;
        }
        $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12,left(kodeblok,4) as kodeorg from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".substr($tahun, 0, 4).' group by left(kodeblok,4)';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $cap[$bar['kodeorg']] = $bar['kodeorg'];
            $index = 'kg'.substr($tahun, 5, 2);
            $budget[$bar['kodeorg']] = $bar[$index] / 1000;
            if (!isset($netto[$bar['kodeorg']])) {
                $netto[$bar['kodeorg']] = 0;
            }
        }
        $str = 'select sum(luasareaproduktif) as luas,left(kodeorg,4) as kodeorg from '.$dbname.".setup_blok where statusblok='TM'\r\n                           group by left(kodeorg,4)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $luas[$bar->kodeorg] = $bar->luas;
        }
        if (0 < count($netto)) {
            foreach ($netto as $key => $val) {
                $netto1[] = $netto[$key];
                $budget1[] = $budget[$key];
                $kghaaktual[] = ($netto[$key] / $luas[$key] === '' ? 0 : $netto[$key] / $luas[$key]);
                $kghabudget[] = ($budget[$key] / $luas[$key] === '' ? 0 : $budget[$key] / $luas[$key]);
                $cap1[] = $cap[$key];
                $targ[] = '?periode='.$tahun.'&pks='.$key.'&jenis=level2';
                $alts[] = ' Click to Drill';
            }
        }

        $totalProduksi = @array_sum($netto1) / @array_sum($luas);
        $totalBudget = @array_sum($budget1) / @array_sum($luas);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->xaxis->SetTickLabels($cap1);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PRODUKSI TBS VS BUDGET PRODUKSI TBS periode :'.$tahun);
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $txt = new Text('Ton.');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton/Ha');
        $txt->SetPos(0.93, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton.');
        $txt = new Text('Aktual TBS/Ha :'.number_format($totalProduksi, 2).' Ton');
        $txt->SetPos(0.1, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget TBS/Ha :'.number_format($totalBudget, 2).' Ton');
        $txt->SetPos(0.1, 0.15, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($netto1);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($budget1);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot3 = new LinePlot($kghaaktual);
        $plot4 = new LinePlot($kghabudget);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Produksi');
        $plot2->SetLegend('Budget');
        $plot3->SetLegend('Ton/Ha Aktual');
        $plot4->SetLegend('Ton/Ha Budget');
        $plot3->mark->SetType(MARK_FILLEDCIRCLE);
        $plot3->mark->SetFillColor('red');
        $plot4->mark->SetType(MARK_FILLEDCIRCLE);
        $plot4->mark->SetFillColor('blue');
        $graph->legend->SetPos(0.7, 0.15, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->AddY2($plot3);
        $graph->AddY2($plot4);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Luas TM :'.number_format(array_sum($luas), 2).' Ha';
        echo '<br>Total Produksi :'.number_format(array_sum($netto1), 2).' Ton';
        echo '<br>Total Budget :'.number_format(array_sum($budget1), 2).' Ton';
        echo '<br><a href=javascript:history.back(-1)>Back</a>';

        break;
    case 'level2':
        $tahun = $param['periode'];
        $kodeorg = $param['pks'];
        $str = 'select sum(beratbersih) as netto,substr(nospb,9,6) as kodeorg from '.$dbname.".pabrik_timbangan where \r\n                           tanggal like '".$tahun."%' and kodeorg='".$kodeorg."'\r\n                           group by substr(nospb,9,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $netto[$bar->kodeorg] = $bar->netto / 1000;
            $cap[$bar->kodeorg] = $bar->kodeorg;
        }
        $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12,left(kodeblok,6) as kodeorg from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".substr($tahun, 0, 4)." and kodeblok like '".$kodeorg."%' group by left(kodeblok,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $cap[$bar['kodeorg']] = $bar['kodeorg'];
            $index = 'kg'.substr($tahun, 5, 2);
            $budget[$bar['kodeorg']] = $bar[$index] / 1000;
            if (!isset($netto[$bar['kodeorg']])) {
                $netto[$bar['kodeorg']] = 0;
            }
        }
        $str = 'select sum(luasareaproduktif) as luas,left(kodeorg,6) as kodeorg from '.$dbname.".setup_blok where statusblok='TM'\r\n                           and kodeorg like '".$kodeorg."%' group by left(kodeorg,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $luas[$bar->kodeorg] = $bar->luas;
        }
        if (0 < count($netto)) {
            foreach ($netto as $key => $val) {
                $netto1[] = $netto[$key];
                $budget1[] = $budget[$key];
                $kghaaktual[] = ($netto[$key] / $luas[$key] === '' ? 0 : $netto[$key] / $luas[$key]);
                $kghabudget[] = ($budget[$key] / $luas[$key] === '' ? 0 : $budget[$key] / $luas[$key]);
                $cap1[] = $cap[$key];
            }
        }

        $totalProduksi = @array_sum($netto1) / @array_sum($luas);
        $totalBudget = @array_sum($budget1) / @array_sum($luas);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->xaxis->SetTickLabels($cap1);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PRODUKSI TBS VS BUDGET PRODUKSI TBS '.$kodeorg.' periode :'.$tahun);
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $txt = new Text('Ton.');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton/Ha');
        $txt->SetPos(0.93, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton.');
        $txt = new Text('Aktual TBS/Ha :'.number_format($totalProduksi, 2).' Ton');
        $txt->SetPos(0.1, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget TBS/Ha :'.number_format($totalBudget, 2).' Ton');
        $txt->SetPos(0.1, 0.15, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($netto1);
        $plot2 = new BarPlot($budget1);
        $plot3 = new LinePlot($kghaaktual);
        $plot4 = new LinePlot($kghabudget);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Produksi');
        $plot2->SetLegend('Budget');
        $plot3->SetLegend('Ton/Ha Aktual');
        $plot4->SetLegend('Ton/Ha Budget');
        $plot3->mark->SetType(MARK_FILLEDCIRCLE);
        $plot3->mark->SetFillColor('red');
        $plot4->mark->SetType(MARK_FILLEDCIRCLE);
        $plot4->mark->SetFillColor('blue');
        $graph->legend->SetPos(0.7, 0.15, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->AddY2($plot3);
        $graph->AddY2($plot4);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Luas TM :'.number_format(array_sum($luas), 2).' Ha';
        echo '<br>Total Produksi :'.number_format(array_sum($netto1), 2).' Ton';
        echo '<br>Total Budget :'.number_format(array_sum($budget1), 2).' Ton';
        echo '<br><a href=javascript:history.back(-1)>Back</a>';

        break;
}

?>