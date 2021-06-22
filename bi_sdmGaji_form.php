<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $awal = $param['awal'];
        $sampai = $param['sampai'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select periodegaji as periode,sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                          plus=1 and periodegaji>='".$awal."' and periodegaji<='".$sampai."' group by periodegaji";
            $str2 = 'select periodegaji as periode,sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                          idkomponen=37 and periodegaji>='".$awal."' and periodegaji<='".$sampai."' group by periodegaji";
        } else {
            $str = 'select periodegaji as periode,sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                         plus=1 and periodegaji>='".$awal."' and periodegaji<='".$sampai."' and  kodeorg='".$kodeorg."' group by periodegaji";
            $str2 = 'select periodegaji as periode,sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                          idkomponen=37 and periodegaji>='".$awal."' and periodegaji<='".$sampai."' and  kodeorg='".$kodeorg."' group by periodegaji";
        }

        $res2 = mysql_query($str2);
        while ($bar2 = mysql_fetch_object($res2)) {
            $pengurang[$bar2->periode] = $bar2->total;
        }
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $periode[] = $bar->periode;
            $total[] = ($bar->total - $pengurang[$bar->periode]) / 1000000;
            $targ[] = '?periode='.$bar->periode.'&pks='.$kodeorg.'&jenis=level2';
            $alts[] = number_format($bar->total / 1000000, 2).', Click to Drill';
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(60, 20, 30, 50);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('silver');
        $graph->SetShadow();
        $graph->title->Set('MONITORING GAJI '.$param['pks']);
        $graph->title->SetColor('darkred');
        $graph->xaxis->SetColor('black', 'red');
        $graph->yscale->ticks->SupressZeroLabel(true);
        $graph->yaxis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($periode);
        $graph->xaxis->SetLabelAngle(60);
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $txt = new Text('Rp(Juta)');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $bplot = new BarPlot($total);
        $bplot->SetWidth(0.6);
        $bplot->SetCSIMTargets($targ, $alts);
        $bplot->SetLegend('Biaya Gaji');
        $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
        $bplot->SetColor('navy');
        $graph->Add($bplot);
        $graph->StrokeCSIM();
        echo '<br> Belum Termasuk Pajak dan Jamsostek Porsi Perusahaan';

        break;
    case 'level2':
        $periode = $param['periode'];
        $kodeorg = $param['pks'];
        if ('' === $kodeorg) {
            $str = 'select  sum(jumlah) as total,idkomponen,name from '.$dbname.".sdm_gajidetail_vw where\r\n                          plus=1 and periodegaji='".$periode."'  group by idkomponen";
            $str2 = 'select  sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                          idkomponen=37 and periodegaji ='".$periode."'";
        } else {
            $str = 'select sum(jumlah) as total,idkomponen,name from '.$dbname.".sdm_gajidetail_vw where\r\n                         plus=1 and periodegaji='".$periode."'  and  kodeorg='".$kodeorg."' group by idkomponen";
            $str2 = 'select sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                          idkomponen=37 and periodegaji ='".$periode."' and kodeorg='".$kodeorg."'";
        }

        $res2 = mysql_query($str2);
        while ($bar2 = mysql_fetch_object($res2)) {
            $pengurang = $bar2->total;
        }
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $komponen1[$bar->idkomponen] = $bar->name;
            if ('1' === $bar->idkomponen) {
                $total1[$bar->idkomponen] = ($bar->total - $pengurang) / 1000000;
            } else {
                $total1[$bar->idkomponen] = $bar->total / 1000000;
            }

            if ('' === $kodeorg) {
                $targ1[$bar->idkomponen] = '?periode='.$periode.'&pks='.$kodeorg.'&jenis=level3&idkomponen='.$bar->idkomponen;
                $alts1[$bar->idkomponen] = number_format($bar->total / 1000000, 2).', Click to Drill';
            }
        }
        foreach ($total1 as $key => $val) {
            $total[] = $total1[$key];
            $targ[] = $targ1[$key];
            $alts[] = $alts[$key];
            $komponen[] = $komponen1[$key];
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(60, 20, 30, 50);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('silver');
        $graph->SetShadow();
        $graph->title->Set('MONITORING GAJI '.$param['pks'].' Periode:'.$param['periode']);
        $graph->title->SetColor('darkred');
        $graph->xaxis->SetColor('black', 'red');
        $graph->yscale->ticks->SupressZeroLabel(true);
        $graph->yaxis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($komponen);
        $graph->xaxis->SetLabelAngle(90);
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $txt = new Text('Rp(Juta)');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $bplot = new BarPlot($total);
        $bplot->SetWidth(0.6);
        $bplot->SetCSIMTargets($targ, $alts);
        $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
        $bplot->SetColor('navy');
        $graph->Add($bplot);
        $graph->StrokeCSIM();
        echo '<br> Belum Termasuk Pajak dan Jamsostek Porsi Perusahaan<br><a href=javascript:history.back(-1)>Back</a>';

        break;
    case 'level3':
        $periode = $param['periode'];
        $kodeorg = $param['pks'];
        $idkomponen = $param['idkomponen'];
        if ('' === $kodeorg) {
            $str = 'select  kodeorg,sum(jumlah) as total,idkomponen,name from '.$dbname.".sdm_gajidetail_vw where\r\n                          idkomponen=".$idkomponen." and periodegaji='".$periode."'  group by kodeorg";
            if ('1' === $idkomponen) {
                $str2 = 'select  kodeorg,sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                                  idkomponen=37 and periodegaji ='".$periode."' group by kodeorg";
            }
        } else {
            $str = 'select kodeorg,sum(jumlah) as total,idkomponen,name from '.$dbname.".sdm_gajidetail_vw where\r\n                         idkomponen=".$idkomponen." and periodegaji='".$periode."'  and  kodeorg='".$kodeorg."' group by kodeorg";
            if ('1' === $idkomponen) {
                $str2 = 'select kodeorg,sum(jumlah) as total from '.$dbname.".sdm_gajidetail_vw where\r\n                              idkomponen=37 and periodegaji ='".$periode."' and kodeorg='".$kodeorg."' group by kodeorg";
            }
        }

        if ('1' === $idkomponen) {
            $res2 = mysql_query($str2);
            while ($bar2 = mysql_fetch_object($res2)) {
                $pengurang[$bar2->kodeorg] = $bar2->total;
            }
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $komponen1[$bar->kodeorg] = $bar->kodeorg;
            if ('1' === $bar->idkomponen) {
                $total1[$bar->kodeorg] = ($bar->total - $pengurang[$bar->kodeorg]) / 1000000;
            } else {
                $total1[$bar->kodeorg] = $bar->total / 1000000;
            }

            $nama = $bar->name;
        }
        foreach ($total1 as $key => $val) {
            $total[] = $total1[$key];
            $targ[] = $targ1[$key];
            $alts[] = $alts[$key];
            $komponen[] = $komponen1[$key];
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(60, 20, 30, 50);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('silver');
        $graph->SetShadow();
        $graph->title->Set('MONITORING GAJI '.$param['pks'].' Periode:'.$param['periode'].' Komponen:'.$nama);
        $graph->title->SetColor('darkred');
        $graph->xaxis->SetColor('black', 'red');
        $graph->yscale->ticks->SupressZeroLabel(true);
        $graph->yaxis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($komponen);
        $graph->xaxis->SetLabelAngle(90);
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $txt = new Text('Rp(Juta)');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $bplot = new BarPlot($total);
        $bplot->SetWidth(0.6);
        $bplot->SetCSIMTargets($targ, $alts);
        $bplot->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
        $bplot->SetColor('navy');
        $graph->Add($bplot);
        $graph->StrokeCSIM();
        echo '<br> Belum Termasuk Pajak dan Jamsostek Porsi Perusahaan<br><a href=javascript:history.back(-1)>Back</a>';

        break;
}

?>