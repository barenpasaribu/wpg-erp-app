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
        $pks = $param['pks'];
        $str = 'select sum(tbsdiolah)/1000 as diolah, sum(oer)/1000 as cpo, sum(oerpk)/1000 as pk, left(tanggal,7) as periode from '.$dbname.".pabrik_produksi where\r\n                          kodeorg='".$pks."' and tanggal like '".$tahun."%' group by left(tanggal,7)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $cap[] = $bar->periode;
            $diolah[] = $bar->diolah;
            $cpo[] = $bar->cpo;
            $pk[] = $bar->pk;
            $oer[] = $bar->cpo / $bar->diolah * 100;
        }
        $str = "select avg(oerbunch) as oercpo,\r\n                                      avg(oerkernel) as oerpk,\r\n                                      sum(olah01)/1000 as olah1,\r\n                                      sum(olah02)/1000 as olah2,\r\n                                      sum(olah03)/1000 as olah3,\r\n                                      sum(olah04)/1000 as olah4,\r\n                                      sum(olah05)/1000 as olah5,\r\n                                      sum(olah06)/1000 as olah6,\r\n                                      sum(olah07)/1000 as olah7,\r\n                                      sum(olah08)/1000 as olah8,\r\n                                      sum(olah09)/1000 as olah9,\r\n                                      sum(olah10)/1000 as olah10,\r\n                                      sum(olah11)/1000 as olah11,\r\n                                      sum(olah12)/1000 as olah12\r\n                           from ".$dbname.'.bgt_produksi_pks where tahunbudget='.$tahun." and millcode='".$pks."'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $Boercpo = $bar->oercpo;
            $Boerpk = $bar->oerpk;
            $Bolah[0] = $bar->olah1;
            $Bolah[1] = $bar->olah2;
            $Bolah[2] = $bar->olah3;
            $Bolah[3] = $bar->olah4;
            $Bolah[4] = $bar->olah5;
            $Bolah[5] = $bar->olah6;
            $Bolah[6] = $bar->olah7;
            $Bolah[7] = $bar->olah8;
            $Bolah[8] = $bar->olah9;
            $Bolah[9] = $bar->olah10;
            $Bolah[10] = $bar->olah11;
            $Bolah[11] = $bar->olah12;
        }
        foreach ($Bolah as $key => $val) {
            $bcpo[$key] = ($val * $Boercpo) / 100;
            $bpk[$key] = ($val * $Boerpk) / 100;
            $B_OER[$key] = $Boercpo;
            $OER_NOL[$key] = 0;
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
        $graph->title->Set('PRODUKSI VS BUDGET PRODUKSI '.$param['pks']);
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
        $plot1 = new BarPlot($Bolah);
        $plot2 = new BarPlot($diolah);
        $plot3 = new BarPlot($bcpo);
        $plot4 = new BarPlot($cpo);
        $plot5 = new BarPlot($bpk);
        $plot6 = new BarPlot($pk);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot3->SetColor('blue');
        $plot4->SetColor('brown');
        $plot5->SetColor('yellow');
        $plot6->SetColor('purple');
        $plot7 = new LinePlot($B_OER);
        $plot8 = new LinePlot($oer);
        $plot9 = new LinePlot($OER_NOL);
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
        $graph->Stroke();

        break;
}

?>