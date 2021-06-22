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
            $internal[$count] = 0;
            $str = 'select sum(beratbersih) as netto from '.$dbname.".pabrik_timbangan where millcode='".$param['pks']."'\r\n                                 and kodebarang='40000003' and kodeorg is not null and kodeorg!='' and tanggal like '".$current."%'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $internal[$count] = $bar->netto / 1000;
            }
            $str = 'select sum(beratbersih) as tbsluar from '.$dbname.".pabrik_timbangan where millcode='".$param['pks']."'\r\n                                 and kodebarang='40000003' and (kodeorg is null or kodeorg='') and tanggal like '".$current."%'";
            $res = mysql_query($str);
            $external[$count] = 0;
            while ($bar = mysql_fetch_object($res)) {
                $external[$count] = $bar->tbsluar / 1000;
            }
            $str = "select \r\n\t\t  sum(tbsdiolah) as tbsdiolah,\r\n\t\t  sum(oer)  as oer from ".$dbname.".pabrik_produksi\r\n\t\t  where kodeorg='".$param['pks']."' and tanggal like '".$current."%'";
            $res = mysql_query($str);
            $oer[$count] = 0;
            $diolah[$count] = 0;
            while ($bar = mysql_fetch_object($res)) {
                $oer[$count] = $bar->oer / $bar->tbsdiolah * 100;
                $diolah[$count] = $bar->tbsdiolah / 1000;
                $noll[$count] = 0;
            }
            $awal = date('Ym', $df);
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
        $graph->title->Set('DISTRIBUSI PENERIMAAN TBS Vs OER PKS '.$param['pks']);
        $txt = new Text('Ton TBS');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('OER');
        $txt->SetPos(0.95, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($internal);
        $plot2 = new BarPlot($external);
        $plot3 = new BarPlot($diolah);
        $plot4 = new LinePlot($oer);
        $plot4->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5 = new LinePlot($noll);
        $plot1->SetLegend('Internal');
        $plot2->SetLegend('External');
        $plot3->SetLegend('Diolah');
        $plot4->SetLegend('OER');
        $graph->legend->SetPos(0.7, 0.2, 'center', 'bottom');
        $plot2->SetFillColor('red');
        $plot3->SetFillColor('orange');
        $gbar = new GroupbarPlot([$plot1, $plot2, $plot3]);
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
            echo '<td>Internal</td><td>External</td><td>Olah</td><td>OER(%)</td>';
        }
        echo '</tr><tr>';
        foreach ($internal as $key => $vx) {
            echo '<td>'.number_format($vx, 2).'</td><td>'.number_format($external[$key], 2).'</td><td>'.number_format($diolah[$key], 2).'</td><td>'.number_format($oer[$key], 2).'</td>';
        }
        echo '</tr></table>';

        break;
}

?>