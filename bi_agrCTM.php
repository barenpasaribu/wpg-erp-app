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
            $str = 'select sum(rupiah) as rpbudget,kodeorg from '.$dbname.".bgt_budget_kebun_perakun_vw\r\n                              where tahunbudget<".substr($tahun, 0, 4)." and  noakun like '".$mayorTBM."%'   \r\n                               group by kodeorg";
            $z = (int) (substr($tahun, 5, 2));
            for ($x = 1; $x <= $z; ++$x) {
                if ($x < 10) {
                    if (1 === $x) {
                        $cont = 'rp0'.$x;
                    } else {
                        $cont .= '+rp0'.$x;
                    }
                } else {
                    $cont .= '+rp'.$x;
                }
            }
            $str2 = 'select sum('.$cont.') as rpbudget, left(kodeorg,4) as kodeorg from '.$dbname.".bgt_budget_detail\r\n                                   where tahunbudget=".substr($tahun, 0, 4)." and  noakun like '".$mayorTBM."%'   \r\n                               group by left(kodeorg,4)";
        } else {
            $level = 'level2';
            $str = "select sum(rp01) +sum(rp02)+sum(rp03)+sum(rp04)+\r\n                             sum(rp05)+sum(rp06)+sum(rp07)+sum(rp08)+\r\n                             sum(rp09)+sum(rp10)+sum(rp11)+sum(rp12) as rpbudget,thntnm  from ".$dbname.".bgt_budget_kebun_perblok_vw\r\n                              where tahunbudget<".substr($tahun, 0, 4)." and kodeorg like '".$kodeorg."%'  and noakun \r\n                              like '".$mayorTBM."%' group by thntnm";
            $z = (int) (substr($tahun, 5, 2));
            for ($x = 1; $x <= $z; ++$x) {
                if ($x < 10) {
                    if (1 === $x) {
                        $cont = 'rp0'.$x;
                    } else {
                        $cont .= '+rp0'.$x;
                    }
                } else {
                    $cont .= '+rp'.$x;
                }
            }
            $str2 = 'select sum('.$cont.') as rpbudget, thntnm from '.$dbname.".bgt_budget_kebun_perblok_vw\r\n                                   where tahunbudget=".substr($tahun, 0, 4)." and  noakun like '".$mayorTBM."%' \r\n                                    and kodeorg like '".$kodeorg."%'\r\n                                   group by thntnm";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_array($res)) {
            $budget[$bar[1]] = $bar[0] / 1000000;
            $cap[$bar[1]] = $bar[1];
        }
        $res = mysql_query($str2);
        while ($bar = mysql_fetch_array($res)) {
            $budget[$bar[1]] += $bar[0] / 1000000;
        }
        if ('' === $kodeorg) {
            $str = 'select sum(debet-kredit) as jumlah,kodeorg from '.$dbname.".keu_jurnalsum_vw where periode <='".$tahun."%'\r\n                    and noakun like '".$mayorTBM."%' and kodeorg like '%E' group by kodeorg order by kodeorg";
        } else {
            $str = 'select sum(debet-kredit) as jumlah,tahuntanam from '.$dbname.".keu_jurnal_blok_vw where \r\n                    tanggal <= '".$tahun."-31'\r\n                   and kodeorg='".$kodeorg."'  and kodeorg like '%E' and noakun like '".$mayorTBM."%' group by tahuntanam order by tahuntanam";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_array($res)) {
            $realisasi[$bar[1]] = $bar[0] / 1000000;
        }
        if ('' === $kodeorg) {
            $str = 'select sum(luasareaproduktif) as luas,left(kodeorg,4) as kodeorg from '.$dbname.".setup_blok where statusblok in ('TBM','TB')\r\n                           group by left(kodeorg,4)";
        } else {
            $str = 'select sum(luasareaproduktif) as luas,tahuntanam from '.$dbname.".setup_blok where statusblok in ('TBM','TB')\r\n                             and kodeorg like '".$kodeorg."%' group by tahuntanam";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_array($res)) {
            $luas[$bar[1]] = $bar[0];
        }
        if (0 < count($budget)) {
            foreach ($budget as $key => $val) {
                $budget1[] = $budget[$key];
                $realisasi1[] = $realisasi[$key];
                $rpperhabudget[] = ($budget[$key] * 1000000) / $luas[$key];
                $rprealisasi[] = ($realisasi[$key] * 1000000) / $luas[$key];
                $cap[] = $key;
            }
        } else {
            if (0 < count($realisasi)) {
                foreach ($realisasi as $key => $val) {
                    $budget1[] = $budget[$key];
                    $realisasi1[] = $realisasi[$key];
                    $rpperhabudget[] = ($budget[$key] * 1000000) / $luas[$key];
                    $rprealisasi[] = ($realisasi[$key] * 1000000) / $luas[$key];
                    $cap[] = $key;
                }
            } else {
                exit('Data Tidak Ada');
            }
        }

        if (count($luas) < 1) {
            exit('Data Tidak Ada');
        }

        $totalProduksi = (@array_sum($realisasi1) * 1000000) / @array_sum($luas);
        $totalBudget = (@array_sum($budget1) * 1000000) / @array_sum($luas);
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('COST TO MATURITY '.$param['pks'].' s/d '.$tahun);
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
        $plot1->SetLegend('Actual CTM');
        $plot2->SetLegend('Budget CTM');
        $graph->legend->SetPos(0.65, 0.17, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->StrokeCSIM();
        echo '<br>Total Biaya: '.number_format(array_sum($realisasi1) * 1000000, 2);
        echo '<br>Total Luas TBM :'.number_format(array_sum($luas), 2).' Ha';

        break;
}

?>