<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_pie.php';
require_once 'jpgraph/jpgraph_pie3d.php';
$param = $_GET;
$tahun = $param['tahun'];
$pks = $param['pks'];
switch ($param['jenis']) {
    case 'global':
        $str = 'select sum(beratbersih-kgpotsortasi) as totalnetto from '.$dbname.".pabrik_timbangan where millcode='".$pks."'\r\n                  and kodebarang='40000003' and tanggal like '".$tahun."%'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $total = $bar->totalnetto;
        }
        $str = 'select sum(beratbersih-kgpotsortasi) as tbsluar from '.$dbname.".pabrik_timbangan where millcode='".$pks."'\r\n                  and kodebarang='40000003' and (kodeorg is null or kodeorg='') and tanggal like '".$tahun."%'";
        $res = mysql_query($str);
        $tbsluar = 0;
        while ($bar = mysql_fetch_object($res)) {
            $tbsluar = $bar->tbsluar;
        }
        $tbsinternal = $total - $tbsluar;
        if (0 === $tbsinternal && 0 === $tbsluar) {
            exit('No current data');
        }

        $data = [$tbsinternal, $tbsluar];
        $graph = new PieGraph(700, 500);
        $graph->title->Set('A. PROPORSI SUMBER TBS PKS '.$pks.' TAHUN '.$tahun."\n Total TBS diterima :".number_format($total / 1000, 0).' Ton');
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 14);
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('white');
        $p1->SetLabelType(PIE_VALUE_PER);
        $lbl = ["TBS Internal\n%.1f%%", "TBS External\n%.1f%%"];
        $p1->SetLabels($lbl);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $targ = [$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks.'&jenis=internal', $_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks.'&jenis=external'];
        $alts = ['Click to drill', 'Click to drill'];
        $p1->SetCSIMTargets($targ, $alts);
        $txt = new Text('Internal :'.number_format($tbsinternal / 1000, 0)." Ton\nExternal :".number_format($tbsluar / 1000, 0).' Ton');
        $txt->SetFont(FF_FONT2, FS_BOLD, 14);
        $txt->SetPos(0.5, 0.2, 'center', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $graph->Add($p1);
        $graph->StrokeCSIM();

        break;
    case 'internal':
        $str = 'select sum(beratbersih-kgpotsortasi) as tbsinternal, kodeorg from '.$dbname.".pabrik_timbangan where millcode='".$pks."'\r\n                  and kodebarang='40000003' and kodeorg is not null and kodeorg!='' and tanggal like '".$tahun."%'\r\n                  group by kodeorg order by tbsinternal desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $lbl[] = $bar->kodeorg." \n%.1f%%";
            $data[] = $bar->tbsinternal;
            $text .= $bar->kodeorg.' : '.number_format($bar->tbsinternal / 1000, 0)." Ton\n";
            $targ[] = $_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks.'&jenis=kebun&kebun='.$bar->kodeorg;
            $alts[] = 'Click to drill';
        }
        if (!isset($data)) {
            exit('No current data');
        }

        $graph = new PieGraph(700, 500);
        $graph->title->Set('B. PROPORSI TBS INTERNAL PKS '.$pks.' TAHUN '.$tahun."\n Total TBS Internal :".number_format(array_sum($data) / 1000, 0).' Ton');
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 14);
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(1);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $p1->SetCSIMTargets($targ, $alts);
        $txt = new Text($text);
        $txt->SetFont(FF_FONT2, FS_BOLD, 14);
        $txt->SetPos(0.4, 0.6, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        echo "<a href='".$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks."&jenis=global'>[ Back ]</a>";

        break;
    case 'kebun':
        $str = 'select sum(beratbersih-kgpotsortasi) as tbsafd, substr(nospb,9,6) as afdeling from '.$dbname.".pabrik_timbangan where millcode='".$pks."'\r\n                  and kodebarang='40000003' and kodeorg ='".$param['kebun']."' and tanggal like '".$tahun."%'\r\n                  group by substr(nospb,9,6)  order by tbsafd desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $lbl[] = $bar->afdeling." \n%.1f%%";
            $data[] = $bar->tbsafd;
            $text .= $bar->afdeling.' : '.number_format($bar->tbsafd / 1000, 0)." Ton\n";
        }
        if (!isset($data)) {
            exit('No current data');
        }

        $graph = new PieGraph(700, 500);
        $graph->title->Set('C. PENERIMAAN TBS dari KEBUN '.$param['kebun'].' TAHUN '.$tahun."\n Total TBS ".$param['kebun'].' :'.number_format(array_sum($data) / 1000, 0).' Ton');
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 14);
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(1);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $txt = new Text($text);
        $txt->SetFont(FF_FONT2, FS_BOLD, 14);
        $txt->SetPos(0.4, 0.6, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        echo "<a href='".$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks."&jenis=global'>[ Back-2 ]</a>\r\n                    <a href='".$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks."&jenis=internal'>[ Back ]</a>";

        break;
    case 'external':
        $str = 'select sum(beratbersih-kgpotsortasi) as tbsexternal, kodecustomer,namasupplier from '.$dbname.".pabrik_timbangan a\r\n                  left join ".$dbname.".log_5supplier b on a.kodecustomer=b.kodetimbangan\r\n                   where millcode='".$pks."'\r\n                  and kodebarang='40000003' and intex=0 and tanggal like '".$tahun."%'\r\n                   group by kodecustomer order by tbsexternal desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $lbl[] = $bar->namasupplier." \n%.1f%%";
            $data[] = $bar->tbsexternal;
            $text .= '<tr><td>'.$bar->namasupplier.'</td><td align=right> :'.number_format($bar->tbsexternal / 1000, 0).' Ton</td></tr>';
        }
        if (!isset($data)) {
            exit('No current data');
        }

        for ($x = 0; $x <= 9; ++$x) {
            $data1[] = $data[$x];
            $lbl1[] = $lbl[$x];
        }
        $graph = new PieGraph(700, 500);
        $graph->title->Set('D. PROPORSI TBS EXTERNAL PKS '.$pks.' TAHUN '.$tahun."\n Total TBS EXTERNAL :".number_format(array_sum($data) / 1000, 0).' Ton');
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 14);
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data1);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(75);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl1);
        $p1->SetLabelPos(0.9);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        echo "<a href='".$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks."&jenis=global'>[ Back ]</a>";
        echo '<table>'.$text.'</table>';

        break;
}

?>