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
        $count = 0;
        while ($awal < $sampai) {
            $df = mktime(0, 0, 0, (int) (substr($param['awal'], 4, 2) + $count), 15, substr($param['awal'], 0, 4));
            $cap[] = date('M-y', $df);
            $current = date('Y-m', $df);
            if ('' !== $param['pks']) {
                $str = 'select sisatbskemarin from '.$dbname.".pabrik_produksi where tanggal like '".$current."%' and \r\n                                kodeorg='".$param['pks']."' order by tanggal asc limit 1";
            } else {
                $str = 'select sisatbskemarin from '.$dbname.".pabrik_produksi where tanggal = '".$current."-01'";
            }

            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $sisaKemarin += $bar->sisatbskemarin;
            }
            if ('' !== $param['pks']) {
                $str = 'select sum(tbsmasuk) as masuk, sum(oer) as cpo, sum(oerpk) as pk, sum(tbsdiolah) as diolah from '.$dbname.".pabrik_produksi\r\n                                where kodeorg='".$param['pks']."' and tanggal like '".$current."%'";
            } else {
                $str = 'select sum(tbsmasuk) as masuk, sum(oer) as cpo, sum(oerpk) as pk, sum(tbsdiolah) as diolah from '.$dbname.".pabrik_produksi\r\n                                where  tanggal like '".$current."%'";
            }

            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $masuk = $bar->masuk;
                $cpo[] = $bar->cpo / 1000;
                $pk[] = $bar->pk / 1000;
                $diolah[] = $bar->diolah / 1000;
            }
            $tersedia[] = ($masuk + $sisaKemarin) / 1000;
            if ('' !== $param['pks']) {
                $sPengolahan = 'select jamdinasbruto as jampengolahan, jamstagnasi as jamstagnasi from '.$dbname.".pabrik_pengolahan \r\n                                where kodeorg='".$param['pks']."' and tanggal like '".$current."%'";
                $shari = 'select count(distinct tanggal)   as jtgl  from '.$dbname.".pabrik_pengolahan \r\n                                where kodeorg='".$param['pks']."' and tanggal like '".$current."%'";
            } else {
                $sPengolahan = 'select jamdinasbruto as jampengolahan, jamstagnasi as jamstagnasi from '.$dbname.".pabrik_pengolahan \r\n                                where  tanggal like '".$current."%'";
                $shari = 'select count(distinct tanggal)  as jtgl from '.$dbname.".pabrik_pengolahan \r\n                                where  tanggal like '".$current."%'";
            }

            $qPengolahan = mysql_query($sPengolahan) || exit(mysql_error($conns));
            unset($jamP, $menitP, $jamS, $menitS);

            while ($res2 = mysql_fetch_object($qPengolahan)) {
                $dd = preg_split('/\\./D', $res2->jampengolahan);
                $ee = preg_split('/\\./D', $res2->jamstagnasi);
                list($jamP[], $menitP[]) = $dd;
                list($jamS[], $menitS[]) = $ee;
            }
            $jamolah[] = @array_sum($jamP) + @array_sum($menitP) / 60;
            $jamstag[] = @array_sum($jamS) + @array_sum($menitS) / 60;
            $awal = date('Ym', $df);
            ++$count;
            $resh = mysql_query($shari);
            while ($bar = mysql_fetch_object($resh)) {
                $jlhhari += $bar->jtgl;
            }
        }
        $tdiolah = array_sum($diolah);
        $tjamolah = array_sum($jamolah);
        $kapasitas = $tdiolah / $tjamolah;
        $avgperhari = $tjamolah / $jlhhari;
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 50);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->SetY2Scale('lin');
        $graph->yaxis->scale->SetGrace(30);
        $graph->y2axis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($cap);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('KAPASITAS OLAH PKS '.$param['pks']);
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
        $txt = new Text('AVG: '.number_format($kapasitas, 2).' Ton TBS/Jam = '.number_format($avgperhari, 2).'jam/Hari');
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
        $plot5 = new LinePlot($jamolah);
        $plot6 = new LinePlot($jamstag);
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

?>