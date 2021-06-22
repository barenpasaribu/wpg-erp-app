<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
$param = $_GET;
$mayorPanen = '611';
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
            $netto[$bar->periode] = $bar->netto / 10000;
        }
        if ('' === $kodeorg) {
            $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12 from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".$tahun." and noakun like '".$mayorPanen."%' group by tahunbudget";
        } else {
            $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12  from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".$tahun." and kodeorg like '".$kodeorg."%'  and noakun like '".$mayorPanen."%' group by tahunbudget";
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
            $budget[$tahun.'-01'] = $bar->rp01 / 1000000;
            $budget[$tahun.'-02'] = $bar->rp02 / 1000000;
            $budget[$tahun.'-03'] = $bar->rp03 / 1000000;
            $budget[$tahun.'-04'] = $bar->rp04 / 1000000;
            $budget[$tahun.'-05'] = $bar->rp05 / 1000000;
            $budget[$tahun.'-06'] = $bar->rp06 / 1000000;
            $budget[$tahun.'-07'] = $bar->rp07 / 1000000;
            $budget[$tahun.'-08'] = $bar->rp08 / 1000000;
            $budget[$tahun.'-09'] = $bar->rp09 / 1000000;
            $budget[$tahun.'-10'] = $bar->rp10 / 1000000;
            $budget[$tahun.'-11'] = $bar->rp11 / 1000000;
            $budget[$tahun.'-12'] = $bar->rp12 / 1000000;
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
            $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12 from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".$tahun.'  group by tahunbudget';
        } else {
            $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12 from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".$tahun." and kodeunit='".$kodeorg."' group by tahunbudget";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $kgbudget[$tahun.'-01'] = $bar->kg01 / 10000;
            $kgbudget[$tahun.'-02'] = $bar->kg02 / 10000;
            $kgbudget[$tahun.'-03'] = $bar->kg03 / 10000;
            $kgbudget[$tahun.'-04'] = $bar->kg04 / 10000;
            $kgbudget[$tahun.'-05'] = $bar->kg05 / 10000;
            $kgbudget[$tahun.'-06'] = $bar->kg06 / 10000;
            $kgbudget[$tahun.'-07'] = $bar->kg07 / 10000;
            $kgbudget[$tahun.'-08'] = $bar->kg08 / 10000;
            $kgbudget[$tahun.'-09'] = $bar->kg09 / 10000;
            $kgbudget[$tahun.'-10'] = $bar->kg10 / 10000;
            $kgbudget[$tahun.'-11'] = $bar->kg11 / 10000;
            $kgbudget[$tahun.'-12'] = $bar->kg12 / 10000;
        }
        if ('' === $kodeorg) {
            $str = 'select periode,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_vw where periode like '".$tahun."%'\r\n                   and noakun like '".$mayorPanen."%' group by periode order by periode";
        } else {
            $str = 'select periode,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_vw where periode like '".$tahun."%'\r\n                   and kodeorg='".$kodeorg."' and noakun like '".$mayorPanen."%' group by periode order by periode";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $realisasi[$bar->periode] = $bar->jumlah / 1000000;
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
                $kgbudget1[] = $kgbudget[$key];
                $realisasi1[] = $realisasi[$key];
                $nolLine[] = 0;
                $rpkgaktual[] = ($realisasi[$key] * 100) / $netto[$key];
                $rpkgbudget[] = ($budget[$key] * 100) / $kgbudget[$key];
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
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $nolLine[] = 0;
            $realisasi1[] = $realisasi[$tahun.'-01'];
            $realisasi1[] = $realisasi[$tahun.'-02'];
            $realisasi1[] = $realisasi[$tahun.'-03'];
            $realisasi1[] = $realisasi[$tahun.'-04'];
            $realisasi1[] = $realisasi[$tahun.'-05'];
            $realisasi1[] = $realisasi[$tahun.'-06'];
            $realisasi1[] = $realisasi[$tahun.'-07'];
            $realisasi1[] = $realisasi[$tahun.'-08'];
            $realisasi1[] = $realisasi[$tahun.'-09'];
            $realisasi1[] = $realisasi[$tahun.'-10'];
            $realisasi1[] = $realisasi[$tahun.'-11'];
            $realisasi1[] = $realisasi[$tahun.'-12'];
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
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $kgbudget1[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgbudget[] = 0;
            $rpkgaktual[] = ($realisasi[$tahun.'-01'] * 100) / $netto[$tahun.'-01'];
            $rpkgaktual[] = ($realisasi[$tahun.'-02'] * 100) / $netto[$tahun.'-02'];
            $rpkgaktual[] = ($realisasi[$tahun.'-03'] * 100) / $netto[$tahun.'-03'];
            $rpkgaktual[] = ($realisasi[$tahun.'-04'] * 100) / $netto[$tahun.'-04'];
            $rpkgaktual[] = ($realisasi[$tahun.'-05'] * 100) / $netto[$tahun.'-05'];
            $rpkgaktual[] = ($realisasi[$tahun.'-06'] * 100) / $netto[$tahun.'-06'];
            $rpkgaktual[] = ($realisasi[$tahun.'-07'] * 100) / $netto[$tahun.'-07'];
            $rpkgaktual[] = ($realisasi[$tahun.'-08'] * 100) / $netto[$tahun.'-08'];
            $rpkgaktual[] = ($realisasi[$tahun.'-09'] * 100) / $netto[$tahun.'-09'];
            $rpkgaktual[] = ($realisasi[$tahun.'-10'] * 100) / $netto[$tahun.'-10'];
            $rpkgaktual[] = ($realisasi[$tahun.'-11'] * 100) / $netto[$tahun.'-11'];
            $rpkgaktual[] = ($realisasi[$tahun.'-12'] * 100) / $netto[$tahun.'-12'];
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

        $totalProduksi = (@array_sum($realisasi1) * 100) / @array_sum($netto1);
        $totalBudget = (@array_sum($budget1) * 100) / @array_sum($kgbudget1);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('BIAYA PANEN dan PENGANGKUTAN VS BUDGET '.$param['pks']);
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $txt = new Text('Rp(juta)-10 Ton');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Rp/Kg');
        $txt->SetPos(0.93, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton.');
        $txt = new Text('Aktual Rp/Kg:'.number_format($totalProduksi, 2));
        $txt->SetPos(0.17, 0.13, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget Rp/Kg:'.number_format($totalBudget, 2));
        $txt->SetPos(0.17, 0.18, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($realisasi1);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($budget1);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot3 = new BarPlot($netto1);
        $plot3->SetCSIMTargets($targ, $alts);
        $plot4 = new BarPlot($kgbudget1);
        $plot4->SetCSIMTargets($targ, $alts);
        $plot5 = new LinePlot($rpkgaktual);
        $plot6 = new LinePlot($rpkgbudget);
        $plot7 = new LinePlot($nolLine);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Biaya Panen');
        $plot2->SetLegend('Budget Biaya Panen');
        $plot3->SetLegend('Produksi/10Ton');
        $plot4->SetLegend('Budget Produksi/10Ton');
        $plot5->SetLegend('Rp/Kg Aktual');
        $plot6->SetLegend('Rp/Kg Budget');
        $plot5->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5->mark->SetFillColor('red');
        $plot6->mark->SetType(MARK_FILLEDCIRCLE);
        $plot6->mark->SetFillColor('blue');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2, $plot3, $plot4]);
        $graph->Add($gbar);
        $graph->AddY2($plot5);
        $graph->AddY2($plot6);
        $graph->AddY2($plot7);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Luas TM :'.number_format($luas, 2).' Ha';

        break;
    case 'level1':
        $tahun = $param['periode'];
        $kodeorg = $param['pks'];
        $str = 'select sum(beratbersih) as netto,kodeorg from '.$dbname.".pabrik_timbangan where \r\n                           tanggal like '".$tahun."%' and kodeorg is not null and kodeorg!=''\r\n                           group by kodeorg";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $netto[$bar->kodeorg] = $bar->netto / 10000;
            $cap[$bar->kodeorg] = $bar->kodeorg;
        }
        $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12,left(kodeorg,4) as kodeorg from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".substr($tahun, 0, 4)." and noakun like '".$mayorPanen."%' group by left(kodeorg,4)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $cap[$bar['kodeorg']] = $bar['kodeorg'];
            $index = 'rp'.substr($tahun, 5, 2);
            $budget[$bar['kodeorg']] = $bar[$index] / 1000000;
            if (!isset($netto[$bar['kodeorg']])) {
                $netto[$bar['kodeorg']] = 0;
            }
        }
        $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12,kodeunit  as kodeorg from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".substr($tahun, 0, 4).'  group by kodeunit';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $index = 'kg'.substr($tahun, 5, 2);
            $kgbudget[$bar['kodeorg']] = $bar[$index] / 10000;
        }
        $str = 'select kodeorg,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_vw where periode like '".$tahun."%'\r\n                   and  noakun like '".$mayorPanen."%' group by kodeorg";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $realisasi[$bar->kodeorg] = $bar->jumlah / 1000000;
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
                $kgbudget1[] = $kgbudget[$key];
                $realisasi1[] = $realisasi[$key];
                $rpkgaktual[] = ($realisasi[$key] * 100) / $netto[$key];
                $rpkgbudget[] = ($budget[$key] * 100) / $kgbudget[$key];
                $nolLine[] = 0;
                $cap1[] = $cap[$key];
                $targ[] = '?periode='.$tahun.'&pks='.$key.'&jenis=level2';
                $alts[] = ' Click to Drill';
            }
        }

        $totalProduksi = (@array_sum($realisasi1) * 100) / @array_sum($netto1);
        $totalBudget = (@array_sum($budget1) * 100) / @array_sum($kgbudget);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->xaxis->SetTickLabels($cap1);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('BIAYA PANEN dan PENGANGKUTAN VS BUDGET '.$param['pks'].' Periode '.$tahun);
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $txt = new Text('Rp(juta)-10 Ton');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Rp/Kg');
        $txt->SetPos(0.93, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton.');
        $txt = new Text('Aktual Rp/Kg:'.number_format($totalProduksi, 2));
        $txt->SetPos(0.17, 0.13, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget Rp/Kg:'.number_format($totalBudget, 2));
        $txt->SetPos(0.17, 0.18, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($realisasi1);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($budget1);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot3 = new BarPlot($netto1);
        $plot3->SetCSIMTargets($targ, $alts);
        $plot4 = new BarPlot($kgbudget1);
        $plot4->SetCSIMTargets($targ, $alts);
        $plot5 = new LinePlot($rpkgaktual);
        $plot6 = new LinePlot($rpkgbudget);
        $plot7 = new LinePlot($nolLine);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Biaya Panen');
        $plot2->SetLegend('Budget Biaya Panen');
        $plot3->SetLegend('Produksi/10Ton');
        $plot4->SetLegend('Budget Produksi/10Ton');
        $plot5->SetLegend('Rp/Kg Aktual');
        $plot6->SetLegend('Rp/Kg Budget');
        $plot5->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5->mark->SetFillColor('red');
        $plot6->mark->SetType(MARK_FILLEDCIRCLE);
        $plot6->mark->SetFillColor('blue');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2, $plot3, $plot4]);
        $graph->Add($gbar);
        $graph->AddY2($plot5);
        $graph->AddY2($plot6);
        $graph->AddY2($plot7);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Luas TM :'.number_format(array_sum($luas), 2).' Ha';
        echo '<br><a href=javascript:history.back(-1)>Back</a>';

        break;
    case 'level2':
        $tahun = $param['periode'];
        $kodeorg = $param['pks'];
        $str = 'select sum(beratbersih) as netto,substr(nospb,9,6) as kodeorg from '.$dbname.".pabrik_timbangan where \r\n                           tanggal like '".$tahun."%' and kodeorg is not null and kodeorg='".$kodeorg."'\r\n                           group by substr(nospb,9,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $netto[$bar->kodeorg] = $bar->netto / 10000;
            $cap[$bar->kodeorg] = $bar->kodeorg;
        }
        $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12,left(kodeorg,6) as kodeorg from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".substr($tahun, 0, 4)." and noakun like '".$mayorPanen."%' \r\n                              and kodeorg like '".$kodeorg."%' group by left(kodeorg,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $cap[$bar['kodeorg']] = $bar['kodeorg'];
            $index = 'rp'.substr($tahun, 5, 2);
            $budget[$bar['kodeorg']] = $bar[$index] / 1000000;
            if (!isset($netto[$bar['kodeorg']])) {
                $netto[$bar['kodeorg']] = 0;
            }
        }
        $str = "select sum(kg01) as kg01,sum(kg02) as kg02,sum(kg03) as kg03,sum(kg04) as kg04,\r\n                             sum(kg05) as kg05,sum(kg06) as kg06,sum(kg07) as kg07,sum(kg08) as kg08,\r\n                             sum(kg09) as kg09,sum(kg10) as kg10,sum(kg11) as kg11,sum(kg12) as kg12,left(kodeblok,6)  as kodeorg from ".$dbname.".bgt_produksi_kbn_kg_vw\r\n                              where tahunbudget=".substr($tahun, 0, 4)."  and kodeunit='".$kodeorg."' group by left(kodeblok,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $index = 'kg'.substr($tahun, 5, 2);
            $kgbudget[$bar['kodeorg']] = $bar[$index] / 10000;
        }
        $str = 'select left(kodeblok,6) as kodeorg,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_blok_vw where periode like '".$tahun."%'\r\n                   and  noakun like '".$mayorPanen."%' and kodeorg='".$kodeorg."' group by left(kodeblok,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $realisasi[$bar->kodeorg] = $bar->jumlah / 1000000;
        }
        $str = 'select sum(luasareaproduktif) as luas,left(kodeorg,6) as kodeorg from '.$dbname.".setup_blok where statusblok='TM'\r\n                           and  kodeorg like '".$kodeorg."%' group by left(kodeorg,6)";
        $res = mysql_query($str);
        echo mysql_error($conn);
        while ($bar = mysql_fetch_object($res)) {
            $luas[$bar->kodeorg] = $bar->luas;
        }
        if (0 < count($netto)) {
            foreach ($netto as $key => $val) {
                $netto1[] = $netto[$key];
                $budget1[] = $budget[$key];
                $kgbudget1[] = $kgbudget[$key];
                $realisasi1[] = $realisasi[$key];
                $rpkgaktual[] = ($realisasi[$key] * 100) / $netto[$key];
                $rpkgbudget[] = ($budget[$key] * 100) / $kgbudget[$key];
                $nolLine[] = 0;
                $cap1[] = $cap[$key];
            }
        }

        $totalProduksi = (@array_sum($realisasi1) * 100) / @array_sum($netto1);
        $totalBudget = (@array_sum($budget1) * 100) / @array_sum($kgbudget);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->xaxis->SetTickLabels($cap1);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('BIAYA PANEN dan PENGANGKUTAN VS BUDGET '.$param['pks'].' Periode '.$tahun);
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $txt = new Text('Rp(juta)-10 Ton');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Rp/Kg');
        $txt->SetPos(0.93, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Ton.');
        $txt = new Text('Aktual Rp/Kg:'.number_format($totalProduksi, 2));
        $txt->SetPos(0.17, 0.13, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget Rp/Kg:'.number_format($totalBudget, 2));
        $txt->SetPos(0.17, 0.18, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($realisasi1);
        $plot2 = new BarPlot($budget1);
        $plot3 = new BarPlot($netto1);
        $plot4 = new BarPlot($kgbudget1);
        $plot5 = new LinePlot($rpkgaktual);
        $plot6 = new LinePlot($rpkgbudget);
        $plot7 = new LinePlot($nolLine);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Biaya Panen');
        $plot2->SetLegend('Budget Biaya Panen');
        $plot3->SetLegend('Produksi/10Ton');
        $plot4->SetLegend('Budget Produksi/10Ton');
        $plot5->SetLegend('Rp/Kg Aktual');
        $plot6->SetLegend('Rp/Kg Budget');
        $plot5->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5->mark->SetFillColor('red');
        $plot6->mark->SetType(MARK_FILLEDCIRCLE);
        $plot6->mark->SetFillColor('blue');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2, $plot3, $plot4]);
        $graph->Add($gbar);
        $graph->AddY2($plot5);
        $graph->AddY2($plot6);
        $graph->AddY2($plot7);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Luas TM :'.number_format(array_sum($luas), 2).' Ha';
        echo '<br><a href=javascript:history.back(-1)>Back</a>';

        break;
}

?>