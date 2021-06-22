<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $tahun = substr($param['tahun'], 0, 4).'-'.substr($param['tahun'], 4, 2);
        $pks = $param['pks'];
        $str = 'select tanggal,tbsmasuk,tbsdiolah,sisatbskemarin,oer,oerpk from '.$dbname.".pabrik_produksi where kodeorg='".$pks."' and tanggal like '".$tahun."%' order by tanggal";
        $res = mysql_query($str);
        $res = mysql_query($str);
        for ($x = 0; $bar = mysql_fetch_object($res); ++$x) {
            $masuk[] = $bar->tbsmasuk / 1000;
            $cpo[] = $bar->oer / 1000;
            $pk[] = $bar->oerpk / 1000;
            $diolah[] = $bar->tbsdiolah / 1000;
            $cap[] = $bar->tanggal;
            $tersedia[] = ($bar->tbsmasuk + $bar->sisatbskemarin) / 1000;
        }
        if (!isset($cpo) && !isset($masuk)) {
            exit('Tidak ada data');
        }

        foreach ($cap as $key => $val) {
            $pengolahan = 0;
            $stag = 0;
            $sPengolahan = 'select jamdinasbruto as jampengolahan, jamstagnasi as jamstagnasi,tanggal from '.$dbname.".pabrik_pengolahan \r\n                            where kodeorg='".$param['pks']."' and tanggal = '".$val."' order by tanggal";
            $qPengolahan = mysql_query($sPengolahan) || exit(mysql_error($conns));
            while ($res2 = mysql_fetch_object($qPengolahan)) {
                $dd = preg_split('/\\./D', $res2->jampengolahan);
                $ee = preg_split('/\\./D', $res2->jamstagnasi);
                $pengolahan += $dd[0] + $dd[1] / 60;
                $stag += $ee[0] + $ee[1] / 60;
            }
            $tpengolahan[] = $pengolahan;
            $tstag[] = $stag;
        }
        $jamolah[] = @array_sum($tpengolahan);
        $jamstag[] = @array_sum($tstag);
        $tdiolah = array_sum($diolah);
        $tjamolah = array_sum($jamolah);
        $kapasitas = $tdiolah / $tjamolah;
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PENGOLAHAN HARIAN PKS '.$param['pks'].' Periode: '.$tahun);
        $txt = new Text('TON');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('JAM');
        $txt->SetPos(0.95, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('AVG: '.number_format($kapasitas, 2).' Ton TBS/Jam');
        $txt->SetFont(FF_FONT2, FS_NORMAL, 10);
        $txt->SetPos(0.1, 0.1, 'left', 'bottom');
        $graph->AddText($txt);
        $plot1 = new BarPlot($tersedia);
        $plot2 = new BarPlot($diolah);
        $plot3 = new BarPlot($cpo);
        $plot4 = new BarPlot($pk);
        $plot1->SetColor('red');
        $plot2->SetColor('orange');
        $plot3->SetColor('blue');
        $plot4->SetColor('brown');
        $plot5 = new LinePlot($tpengolahan);
        $plot6 = new LinePlot($tstag);
        $plot5->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5->mark->SetFillColor('red');
        $plot6->mark->SetType(MARK_FILLEDCIRCLE);
        $plot6->mark->SetFillColor('blue');
        $plot1->SetLegend('Tersedia');
        $plot2->SetLegend('Diolah');
        $plot3->SetLegend('CPO');
        $plot4->SetLegend('KER');
        $plot5->SetLegend('Jam Olah');
        $plot6->SetLegend('Jam Stag');
        $graph->legend->SetPos(0.7, 0.2, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2, $plot3, $plot4]);
        $graph->Add($gbar);
        $graph->AddY2($plot5);
        $graph->AddY2($plot6);
        $graph->Stroke();

        break;
}
echo "\r\n";

?>