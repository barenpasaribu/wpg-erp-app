<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_pie.php';
require_once 'jpgraph/jpgraph_pie3d.php';
$param = $_GET;
$pks = $param['pks'];
$tahun = $param['tahun'];
switch ($param['jenis']) {
    case 'global':
        $str = 'select sum(tbsdiolah) as diolah,sum(oer) as cpo,sum(oerpk) as pk from '.$dbname.".pabrik_produksi where kodeorg='".$pks."'\r\n                  and tanggal like '".$tahun."%'";
        $res = mysql_query($str);
        $diolah = 0;
        $cpo = 0;
        $pk = 0;
        while ($bar = mysql_fetch_object($res)) {
            $diolah = $bar->diolah;
            $cpo = $bar->cpo;
            $pk = $bar->pk;
        }
        $str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where jurnalid='PKS01'";
        $res = mysql_query($str);
        $akunatas = '';
        while ($bar = mysql_fetch_object($res)) {
            $akunatas = $bar->noakundebet;
        }
        if ('' === $akunatas) {
            exit('Error: Parameter jurnal untuk PKS01(akun teratas pks) belum ditentukan');
        }

        $str = 'select sum(debet)-sum(kredit) as biaya, left(noakun,3) as major from '.$dbname.".keu_jurnalsum_vw \r\n                 where kodeorg='".$pks."' and periode like '".$tahun."%'\r\n                 and noakun >= '".$akunatas."' and noakun not like '64%'  group by left(noakun,3) order by biaya desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $biaya[$bar->major] = $bar->biaya;
        }
        $totalbiaya = array_sum($biaya);
        $rpcpo = $totalbiaya / $cpo;
        $rpdiolah = $totalbiaya / $diolah;
        $rppk = $totalbiaya / $pk;
        $str = 'select noakun,namaakun from '.$dbname.'.keu_5akun where length(noakun)=3';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $akun[$bar->noakun] = $bar->namaakun;
        }
        $stbyl = '';
        foreach ($biaya as $key => $val) {
            if (isset($akun[$key])) {
                $lbl[] = $akun[$key]."\n%.1f%%";
                $data[] = $val;
                $targ[] = $_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks.'&jenis=rinci&noakun='.$key.'&judul='.$akun[$key];
                $alts[] = 'Click to drill';
            } else {
                $bylain += $val;
                if ('' === $stbyl) {
                    $stbyl = '`'.$key.'`';
                } else {
                    $stbyl .= ',`'.$key.'`';
                }
            }
        }
        $lbl[] = " Biaya lainnya \n%.1f%%";
        $data[] = $bylain;
        $targ[] = $_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks.'&jenis=rincilain&noakun='.$stbyl.'&judul=BIAYA LAIN';
        $alts[] = 'Click to drill';
        if (0 === $biaya) {
            exit('No current data');
        }

        $graph = new PieGraph(700, 500);
        $graph->title->Set('A. BIAYA PRODUKSI CPO UNIT  '.$pks.' TAHUN '.$tahun);
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(0.8);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $p1->SetCSIMTargets($targ, $alts);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        echo "<br>Biaya Tidak termasuk pembelian TBS:<table>\r\n                   <tr><td>Total Biaya :</td><td align=right>".number_format($totalbiaya / 1000000, 0)."(Juta)</td></tr>\r\n                    <tr><td>Rp/Kg Tbs :</td><td align=right>".number_format($totalbiaya / $diolah, 0)."</td></tr>\r\n                    <tr><td>Rp/Kg CPO:</td><td align=right>".number_format($totalbiaya / $cpo, 0)."</td></tr>\r\n                    <tr><td>TBS Diolah:</td><td align=right>".number_format($diolah, 0)." Kg</td></tr>\r\n                    <tr><td>Produksi CPO:</td><td align=right>".number_format($cpo, 0)." Kg</td></tr>\r\n                  </table>";
        echo '<br><table cellspacing=1 border=1><tr><td>No.Akun</td><td>Nama.Akun</td><td>Biaya(Rp.)</td></tr>';
        foreach ($biaya as $key => $val) {
            echo '<tr><td>'.$key.'</td><td>'.$akun[$key].'</td><td align=right>'.number_format($val).'</td></tr>';
        }
        echo '<tr><td colspan=2>Total</td><td align=right>'.number_format(array_sum($biaya)).'</td></tr>';
        echo '</table>';

        break;
    case 'rinci':
        $str = 'select sum(tbsdiolah) as diolah,sum(oer) as cpo,sum(oerpk) as pk from '.$dbname.".pabrik_produksi where kodeorg='".$pks."'\r\n                  and tanggal like '".$tahun."%'";
        $res = mysql_query($str);
        $diolah = 0;
        $cpo = 0;
        $pk = 0;
        while ($bar = mysql_fetch_object($res)) {
            $diolah = $bar->diolah;
            $cpo = $bar->cpo;
            $pk = $bar->pk;
        }
        $str = 'select sum(debet)-sum(kredit) as biaya,noakun from '.$dbname.".keu_jurnalsum_vw \r\n                 where kodeorg='".$pks."' and periode like '".$tahun."%'\r\n                 and noakun like '".$_GET['noakun']."%' group by noakun order by biaya desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            if (0 < $bar->biaya) {
                $biaya[$bar->noakun] = $bar->biaya;
            }
        }
        $totalbiaya = array_sum($biaya);
        $rpcpo = $totalbiaya / $cpo;
        $rpdiolah = $totalbiaya / $diolah;
        $rppk = $totalbiaya / $pk;
        $str = 'select noakun,namaakun from '.$dbname.".keu_5akun where noakun like '".$_GET['noakun']."%' ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $akun[$bar->noakun] = $bar->namaakun;
        }
        $stbyl = '';
        foreach ($biaya as $key => $val) {
            $lbl[] = $akun[$key]."\n%.1f%%";
            $data[] = $val;
        }
        if (0 === $biaya) {
            exit('No current data');
        }

        $graph = new PieGraph(700, 500);
        $graph->title->Set('B. BIAYA PRODUKSI ('.$_GET['judul'].')  UNIT  '.$pks.' TAHUN '.$tahun);
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(0.6);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        echo "<a href='".$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks."&jenis=global'>[ Back ]</a>";
        echo "<table>\r\n                   <tr><td>Total Biaya :</td><td align=right>".number_format($totalbiaya / 1000000, 0)."(Juta)</td></tr>\r\n                    <tr><td>Rp/Kg Tbs :</td><td align=right>".number_format($totalbiaya / $diolah, 0)."</td></tr>\r\n                    <tr><td>Rp/Kg CPO:</td><td align=right>".number_format($totalbiaya / $cpo, 0)."</td></tr>\r\n                    <tr><td>TBS Diolah:</td><td align=right>".number_format($diolah, 0)." Kg</td></tr>\r\n                    <tr><td>Produksi CPO:</td><td align=right>".number_format($cpo, 0)." Kg</td></tr>\r\n                  </table>";
        echo '<br><table cellspacing=1 border=1><tr><td>No.Akun</td><td>Nama.Akun</td><td>Biaya(Rp.)</td></tr>';
        foreach ($biaya as $key => $val) {
            echo '<tr><td>'.$key.'</td><td>'.$akun[$key].'</td><td align=right>'.number_format($val).'</td></tr>';
        }
        echo '<tr><td colspan=2>Total</td><td align=right>'.number_format(array_sum($biaya)).'</td></tr>';
        echo '</table>';

        break;
    case 'rincilain':
        $str = 'select sum(tbsdiolah) as diolah,sum(oer) as cpo,sum(oerpk) as pk from '.$dbname.".pabrik_produksi where kodeorg='".$pks."'\r\n                  and tanggal like '".$tahun."%'";
        $res = mysql_query($str);
        $diolah = 0;
        $cpo = 0;
        $pk = 0;
        while ($bar = mysql_fetch_object($res)) {
            $diolah = $bar->diolah;
            $cpo = $bar->cpo;
            $pk = $bar->pk;
        }
        $str = 'select sum(debet)-sum(kredit) as biaya,noakun from '.$dbname.".keu_jurnalsum_vw \r\n                 where kodeorg='".$pks."' and periode like '".$tahun."%'\r\n                 and left(noakun,3) in(".str_replace('`', "'", $_GET['noakun']).') group by noakun order by biaya desc';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            if (0 < $bar->biaya) {
                $biaya[$bar->noakun] = $bar->biaya;
            }
        }
        $totalbiaya = array_sum($biaya);
        $rpcpo = $totalbiaya / $cpo;
        $rpdiolah = $totalbiaya / $diolah;
        $rppk = $totalbiaya / $pk;
        $str = 'select noakun,namaakun from '.$dbname.'.keu_5akun where  left(noakun,3) in('.str_replace('`', "'", $_GET['noakun']).')';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $akun[$bar->noakun] = $bar->namaakun;
        }
        $stbyl = '';
        $count = 0;
        foreach ($biaya as $key => $val) {
            ++$count;
            $lbl[] = $akun[$key]."\n%.1f%%";
            if ($count <= 10) {
                $data[] = $val;
            }
        }
        if (0 === $biaya) {
            exit('No current data');
        }

        $graph = new PieGraph(700, 500);
        $graph->title->Set('B. BIAYA PRODUKSI ('.$_GET['judul'].')  UNIT  '.$pks.' TAHUN '.$tahun."\n(10 Besar)");
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(0.9);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        echo "<a href='".$_SERVER['PHP_SELF'].'?tahun='.$tahun.'&pks='.$pks."&jenis=global'>[ Back ]</a>";
        echo "<table>\r\n                   <tr><td>Total Biaya :</td><td align=right>".number_format($totalbiaya / 1000000, 0)."(Juta)</td></tr>\r\n                    <tr><td>Rp/Kg Tbs :</td><td align=right>".number_format($totalbiaya / $diolah, 0)."</td></tr>\r\n                    <tr><td>Rp/Kg CPO:</td><td align=right>".number_format($totalbiaya / $cpo, 0)."</td></tr>\r\n                    <tr><td>TBS Diolah:</td><td align=right>".number_format($diolah, 0)." Kg</td></tr>\r\n                    <tr><td>Produksi CPO:</td><td align=right>".number_format($cpo, 0)." Kg</td></tr>\r\n                  </table>";

        break;
}

?>