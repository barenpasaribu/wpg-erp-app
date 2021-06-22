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
        $tahun1 = $param['tahun1'];
        $pks = $param['pks'];
        $str = 'select sum(tbsdiolah)/1000 as diolah, sum(oer)/1000 as cpo, sum(oerpk)/1000 as pk, left(tanggal,4) as periode from '.$dbname.".pabrik_produksi where\r\n                          kodeorg='".$pks."' and left(tanggal,4)>='".$tahun."' and left(tanggal,4)<='".$tahun1."' group by left(tanggal,4) order by tanggal";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $diolah[$bar->periode] = $bar->diolah;
            $cpo[$bar->periode] = $bar->cpo;
            $pk[$bar->periode] = $bar->pk;
            $oer[$bar->periode] = $bar->cpo / $bar->diolah * 100;
        }
        $str = "select avg(oerbunch) as oercpo,\r\n                                      avg(oerkernel) as oerpk,\r\n                                     sum(kgolah) as olah,\r\n                                     tahunbudget\r\n                           from ".$dbname.'.bgt_produksi_pks where tahunbudget>='.$tahun.'  and tahunbudget<='.$tahun1." \r\n                           and millcode='".$pks."' group by tahunbudget order by tahunbudget";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $Boercpo[$bar->tahunbudget] = $bar->oercpo;
            $Boerpk[$bar->tahunbudget] = $bar->oerpk;
            $Bolah[$bar->tahunbudget] = $bar->olah / 1000;
        }
        for ($x = $tahun; $x <= $tahun1; ++$x) {
            $bcpo[$x] = ($Bolah[$x] * $Boercpo[$x]) / 100;
            $bpk[$x] = ($Bolah[$x] * $Boerpk[$x]) / 100;
            $B_OER[$x] = $Boercpo[$x];
            $OER_NOL[$x] = 0;
            if (!isset($diolah[$x])) {
                $diolah[$x] = 0;
            }

            if (!isset($cpo[$x])) {
                $cpo[$x] = 0;
            }

            if (!isset($pk[$x])) {
                $pk[$x] = 0;
            }

            if (!isset($oer[$x])) {
                $oer[$x] = 0;
            }

            if (!isset($Boercpo[$x])) {
                $Boercpo[$x] = 0;
            }

            if (!isset($Boerpk[$x])) {
                $Boerpk[$x] = 0;
            }

            if (!isset($Bolah[$x])) {
                $Bolah[$x] = 0;
            }
        }
        $y = 0;
        for ($x = $tahun; $x <= $tahun1; ++$x) {
            $cap[] = $x;
            $B_OERx[$y] = $B_OER[$x];
            $oerx[$y] = $oer[$x];
            $OER_NOLx[$y] = $OER_NOL[$x];
            $Bolahx[$y] = $Bolah[$x];
            $diolahx[$y] = $diolah[$x];
            $bcpox[$y] = $bcpo[$x];
            $cpox[$y] = $cpo[$x];
            $bpkx[$y] = $bpk[$x];
            $pkx[$y] = $pk[$x];
            $targ[] = 'bi_produksiVsBudhetPKS_form.php?tahun='.$x.'&pks='.$pks.'&jenis=global';
            $alts[] = 'Click to drill';
            ++$y;
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PRODUKSI VS BUDGET PRODUKSI TAHUNAN '.$param['pks']);
        $txt = new Text('TON');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('%');
        $txt->SetPos(0.95, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($Bolahx);
        $plot2 = new BarPlot($diolahx);
        $plot3 = new BarPlot($bcpox);
        $plot4 = new BarPlot($cpox);
        $plot5 = new BarPlot($bpkx);
        $plot6 = new BarPlot($pkx);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot3->SetCSIMTargets($targ, $alts);
        $plot4->SetCSIMTargets($targ, $alts);
        $plot5->SetCSIMTargets($targ, $alts);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot3->SetColor('blue');
        $plot4->SetColor('brown');
        $plot5->SetColor('yellow');
        $plot6->SetColor('purple');
        $plot7 = new LinePlot($B_OERx);
        $plot8 = new LinePlot($oerx);
        $plot9 = new LinePlot($OER_NOLx);
        $plot7->mark->SetType(MARK_FILLEDCIRCLE);
        $plot7->mark->SetFillColor('red');
        $plot8->mark->SetType(MARK_FILLEDCIRCLE);
        $plot8->mark->SetFillColor('blue');
        $plot1->SetLegend('Budget Olah');
        $plot2->SetLegend('Aktual Olah');
        $plot3->SetLegend('Budget CPO');
        $plot4->SetLegend('Aktual CPO');
        $plot5->SetLegend('Budget PK');
        $plot6->SetLegend('Aktual PK');
        $plot7->SetLegend('Budget OER');
        $plot8->SetLegend('Aktual OER');
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2, $plot3, $plot4, $plot5, $plot6]);
        $graph->Add($gbar);
        $graph->AddY2($plot7);
        $graph->AddY2($plot8);
        $graph->AddY2($plot9);
        $graph->StrokeCSIM();

        break;
}

?>