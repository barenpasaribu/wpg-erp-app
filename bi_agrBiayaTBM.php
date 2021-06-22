<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
$param = $_GET;
$mayorTBM = '126';
switch ($param['jenis']) {
    case 'global':
        $tahun = $param['tahun'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $level = 'level1';
            $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12 from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".$tahun." and  noakun like '".$mayorTBM."%'   group by tahunbudget";
        } else {
            $level = 'level2';
            $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12  from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".$tahun." and kodeorg like '".$kodeorg."%'  and noakun like '".$mayorTBM."%' group by tahunbudget";
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
            $str = 'select periode,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_vw where periode like '".$tahun."%'\r\n                    and noakun like '".$mayorTBM."%' and kodeorg like '%E' group by periode order by periode";
        } else {
            $str = 'select periode,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_vw where periode like '".$tahun."%'\r\n                   and kodeorg='".$kodeorg."'  and kodeorg like '%E' and noakun like '".$mayorTBM."%' group by periode order by periode";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $realisasi[$bar->periode] = $bar->jumlah / 1000000;
        }
        if ('' === $kodeorg) {
            $str = 'select sum(luasareaproduktif) as luas from '.$dbname.".setup_blok where statusblok in ('TBM','TB')";
        } else {
            $str = 'select sum(luasareaproduktif) as luas from '.$dbname.".setup_blok where statusblok in ('TBM','TB')\r\n                             and kodeorg like '".$kodeorg."%'";
        }

        $res = mysql_query($str);
        $luas = 0;
        while ($bar = mysql_fetch_object($res)) {
            $luas = $bar->luas;
        }
        if (0 < count($budget)) {
            foreach ($budget as $key => $val) {
                $budget1[] = $budget[$key];
                $realisasi1[] = $realisasi[$key];
            }
        } else {
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

        $totalProduksi = (@array_sum($realisasi1) * 1000000) / $luas;
        $totalBudget = (@array_sum($budget1) * 1000000) / $luas;
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('BIAYA TBM VS BUDGET '.$param['pks']);
        $txt = new Text('Rp(juta)');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Aktual Rp/Ha:'.number_format($totalProduksi, 2));
        $txt->SetPos(0.17, 0.13, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget Rp/Ha:'.number_format($totalBudget, 2));
        $txt->SetPos(0.17, 0.18, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($realisasi1);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($budget1);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Biaya TBM');
        $plot2->SetLegend('Budget Biaya TBM');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->StrokeCSIM();
        echo '<br>';
        echo 'Total Biaya TBM:'.number_format(array_sum($realisasi1) * 1000000, 2);
        echo '<br>Total Luas TBM :'.number_format($luas, 2).' Ha';

        break;
    case 'level1':
        $tahun = $param['periode'];
        $kodeorg = $param['pks'];
        $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12,left(kodeorg,4) as kodeorg from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".substr($tahun, 0, 4)." and noakun like '".$mayorTBM."%'  group by left(kodeorg,4)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $index = 'rp'.substr($tahun, 5, 2);
            $budget[$bar['kodeorg']] = $bar[$index] / 1000000;
        }
        $str = 'select kodeorg,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_vw where periode like '".$tahun."%'\r\n                    and noakun like '".$mayorTBM."%' and kodeorg like '%E'  group by kodeorg having jumlah >0";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $cap[$bar->kodeorg] = $bar->kodeorg;
            $realisasi[$bar->kodeorg] = $bar->jumlah / 1000000;
        }
        $str = 'select sum(luasareaproduktif) as luas,left(kodeorg,4) as kodeorg from '.$dbname.".setup_blok where  statusblok in ('TBM','TB')\r\n                           group by left(kodeorg,4)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $luas[$bar->kodeorg] = $bar->luas;
        }
        if (0 < count($realisasi)) {
            foreach ($realisasi as $key => $val) {
                $budget1[] = $budget[$key];
                $realisasi1[] = $realisasi[$key];
                $cap1[] = $cap[$key];
                $targ[] = '?periode='.$tahun.'&pks='.$key.'&jenis=level2';
                $alts[] = ' Click to Drill';
            }
        }

        $totalProduksi = (@array_sum($realisasi1) * 1000000) / @array_sum($luas);
        $totalBudget = (@array_sum($budget1) * 1000000) / @array_sum($luas);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->xaxis->SetTickLabels($cap1);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('BIAYA TBM VS BUDGET '.$param['pks'].' Periode '.$tahun);
        $txt = new Text('Rp(juta)');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Aktual Rp/Ha:'.number_format($totalProduksi, 2));
        $txt->SetPos(0.17, 0.13, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Budget Rp/Ha:'.number_format($totalBudget, 2));
        $txt->SetPos(0.17, 0.18, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($realisasi1);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($budget1);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Biaya TBM');
        $plot2->SetLegend('Budget Biaya TBM');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->StrokeCSIM();
        echo '<br>Total Biaya TBM:'.number_format(array_sum($realisasi1) * 1000000, 2);
        echo '<br>Total Luas TBM :'.number_format(array_sum($luas), 2).' Ha';
        echo '<br><a href=javascript:history.back(-1)>Back</a>';

        break;
    case 'level2':
        $tahun = $param['periode'];
        $kodeorg = $param['pks'];
        $str = "select sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,\r\n                             sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07,sum(rp08) as rp08,\r\n                             sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12,left(kodeorg,6) as kodeorg from ".$dbname.".bgt_budget_detail\r\n                              where tahunbudget=".substr($tahun, 0, 4)." and noakun like '".$mayorTBM."%'\r\n                              and kodeorg like '".$kodeorg."%' group by left(kodeorg,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            $index = 'rp'.substr($tahun, 5, 2);
            $budget[$bar['kodeorg']] = $bar[$index] / 1000000;
        }
        $str = 'select left(kodeblok,6) as kodeorg,sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnalsum_blok_vw where periode like '".$tahun."%'\r\n                   and noakun like '".$mayorTBM."%' and kodeorg='".$kodeorg."'  and kodeorg like '%E' group by left(kodeblok,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $cap[$bar->kodeorg] = $bar->kodeorg;
            $realisasi[$bar->kodeorg] = $bar->jumlah / 1000000;
        }
        $str = 'select sum(luasareaproduktif) as luas,left(kodeorg,6) as kodeorg from '.$dbname.".setup_blok where statusblok  in ('TBM','TB')\r\n                           and  kodeorg like '".$kodeorg."%' group by left(kodeorg,6)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $luas[$bar->kodeorg] = $bar->luas;
        }
        if (0 < count($realisasi)) {
            foreach ($realisasi as $key => $val) {
                $budget1[] = $budget[$key];
                $kgbudget1[] = $kgbudget[$key];
                $realisasi1[] = $realisasi[$key];
                $cap1[] = $cap[$key];
            }
        }

        $totalProduksi = (@array_sum($realisasi1) * 1000000) / @array_sum($luas);
        $totalBudget = (@array_sum($budget1) * 1000000) / @array_sum($luas);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->xaxis->SetTickLabels($cap1);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('BIAYA TBM VS BUDGET '.$param['pks'].' Periode '.$tahun);
        $txt = new Text('Rp(juta)');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
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
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot1->SetLegend('Biaya TBM');
        $plot2->SetLegend('Budget Biaya TBM');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->StrokeCSIM();
        echo '<br>Total Biaya TBM:'.number_format(array_sum($realisasi1) * 1000000, 2);
        echo '<br>Total Luas TBM :'.number_format(array_sum($luas), 2).' Ha';
        echo '<br><a href=javascript:history.back(-1)>Back</a>';

        break;
}

?>