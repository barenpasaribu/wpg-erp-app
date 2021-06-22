<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $awal = $param['awal'];
        $sampai = $param['sampai'];
        for ($count = 0; $awal < $sampai; ++$count) {
            $df = mktime(0, 0, 0, (int) (substr($param['awal'], 4, 2) + $count), 15, substr($param['awal'], 0, 4));
            $cap[] = date('M-y', $df);
            $current = date('Y-m', $df);
            if ('' !== $param['pks']) {
                $str = "select \r\n\t\t  sum(tbsdiolah) as tbsdiolah,\r\n\t\t  sum(oer)  as oer, sum(oerpk)  as oerpk from ".$dbname.".pabrik_produksi\r\n\t\t  where kodeorg='".$param['pks']."' and tanggal like '".$current."%'";
            } else {
                $str = "select \r\n\t\t  sum(tbsdiolah) as tbsdiolah,\r\n\t\t  sum(oer)  as oer, sum(oerpk)  as oerpk from ".$dbname.".pabrik_produksi\r\n\t\t  where tanggal like '".$current."%'";
            }

            $res = mysql_query($str);
            $oer[$count] = 0;
            $diolah[$count] = 0;
            while ($bar = mysql_fetch_object($res)) {
                $oer[$count] = $bar->oer / $bar->tbsdiolah * 100;
                $diolah[$count] = $bar->tbsdiolah / 1000;
                $oerpk[$count] = $bar->oerpk / $bar->tbsdiolah * 100;
                $cpo[$count] = $bar->oer / 1000;
                $pk[$count] = $bar->oerpk / 1000;
            }
            $awal = date('Ym', $df);
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetY2Scale('lin');
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $graph->y2axis->SetColor('red');
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PRODUKSI PKS '.$param['pks']);
        $txt = new Text('Ton');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $txt = new Text('%');
        $txt->SetPos(0.95, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $plot2 = new BarPlot($cpo);
        $plot3 = new BarPlot($pk);
        $plot4 = new LinePlot($oer);
        $plot5 = new LinePlot($oerpk);
        $plot4->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5->mark->SetType(MARK_FILLEDCIRCLE);
        $plot4->mark->SetFillColor('green');
        $plot5->mark->SetFillColor('brown');
        $plot2->SetLegend('CPO (Ton)');
        $plot3->SetLegend('Inti (Ton)');
        $plot4->SetLegend('OER (%)');
        $plot5->SetLegend('Ker( %)');
        $graph->legend->SetPos(0.7, 0.2, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot2, $plot3]);
        $gbar->SetWidth(0.3);
        $graph->Add($gbar);
        $graph->AddY2($plot4);
        $graph->AddY2($plot5);
        $graph->StrokeCSIM();
        echo "<br>Dalam Ton:<table cellspacing=0 border=1 style='font-size:11px;'>\r\n                                <tr>";
        foreach ($cap as $v) {
            echo '<td colspan=4 align=center>'.$v.'</td>';
        }
        echo '</tr><tr>';
        foreach ($cap as $v) {
            echo '<td>CPO</td><td>PK</td><td>OER(%)</td><td>KER(%)</td>';
        }
        echo '</tr><tr>';
        foreach ($cpo as $key => $vx) {
            echo '<td>'.number_format($vx, 2).'</td><td>'.number_format($pk[$key], 2).'</td><td>'.number_format($oer[$key], 2).'</td><td>'.number_format($oerpk[$key], 2).'</td>';
        }
        echo '</tr></table>';

        break;
}

?>