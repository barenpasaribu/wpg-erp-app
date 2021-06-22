<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
$param = $_GET;
$pks = $param['pks'];
$tahun = $param['tahun'];
switch ($param['jenis']) {
    case 'global':
        $str = 'select sum(tbsdiolah) as diolah,sum(oer) as cpo,sum(oerpk) as pk,left(tanggal,7) as periode from '.$dbname.".pabrik_produksi where kodeorg='".$pks."'\r\n                  and tanggal like '".$tahun."%' group by periode";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $diolah[str_replace('-', '', $bar->periode)] = $bar->diolah;
            $cpo[str_replace('-', '', $bar->periode)] = $bar->cpo;
            $pk[str_replace('-', '', $bar->periode)] = $bar->pk;
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

        $str = 'select sum(debet)-sum(kredit) as biaya,periode from '.$dbname.".keu_jurnaldt_vw \r\n                 where kodeorg='".$pks."' and periode like '".$tahun."%'\r\n                 and noakun >= '".$akunatas."' and noakun not like '64%' group by periode order by periode";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $biaya[str_replace('-', '', $bar->periode)] = $bar->biaya;
        }
        for ($x = 1; $x <= 12; ++$x) {
            $y = $tahun.str_pad($x, 2, '0', STR_PAD_LEFT);
            $totalbiaya[$y] = $biaya[$y];
            $rpcpo[$y] = $totalbiaya[$y] / $cpo[$y];
            $rpdiolah[$y] = $totalbiaya[$y] / $diolah[$y];
            $rppk[$y] = $totalbiaya[$y] / $pk[$y];
            if ('' === $totalbiaya[$y]) {
                $totalbiaya[$y] = 0;
            }

            if ('' === $rpcpo[$y]) {
                $rpcpo[$y] = 0;
            }

            if ('' === $rpdiolah[$y]) {
                $rpdiolah[$y] = 0;
            }

            if ('' === $rppk[$y]) {
                $rppk[$y] = 0;
            }

            $cap[] = $tahun.'-'.str_pad($x, 2, '0', STR_PAD_LEFT);
        }
        foreach ($totalbiaya as $key => $val) {
            $data[] = $val / 1000000;
            $ceka[] = $rpcpo[$key];
            $ceker[] = $rppk[$key];
        }
        $str = "select sum(kgcpo01) as cpo1,\r\n                                      sum(kgcpo02) as cpo2,\r\n                                      sum(kgcpo03) as cpo3,\r\n                                      sum(kgcpo04) as cpo4,\r\n                                      sum(kgcpo05) as cpo5,\r\n                                      sum(kgcpo06) as cpo6,\r\n                                      sum(kgcpo07) as cpo7,\r\n                                      sum(kgcpo08) as cpo8,\r\n                                      sum(kgcpo09) as cpo9,\r\n                                      sum(kgcpo10) as cpo10,\r\n                                      sum(kgcpo11) as cpo11,\r\n                                      sum(kgcpo12) as cpo12,\r\n                                     sum(kgker01) as ker1,\r\n                                      sum(kgker02) as ker2,\r\n                                      sum(kgker03) as ker3,\r\n                                      sum(kgker04) as ker4,\r\n                                      sum(kgker05) as ker5,\r\n                                      sum(kgker06) as ker6,\r\n                                      sum(kgker07) as ker7,\r\n                                      sum(kgker08) as ker8,\r\n                                      sum(kgker09) as ker9,\r\n                                      sum(kgker10) as ker10,\r\n                                      sum(kgker11) as ker11,\r\n                                      sum(kgker12) as ker12                                      \r\n                           from ".$dbname.'.bgt_produksi_pks_vw where tahunbudget='.$tahun." and millcode='".$pks."'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $CPOh[0] = $bar->cpo1;
            $CPOh[1] = $bar->cpo2;
            $CPOh[2] = $bar->cpo3;
            $CPOh[3] = $bar->cpo4;
            $CPOh[4] = $bar->cpo5;
            $CPOh[5] = $bar->cpo6;
            $CPOh[6] = $bar->cpo7;
            $CPOh[7] = $bar->cpo8;
            $CPOh[8] = $bar->cpo9;
            $CPOh[9] = $bar->cpo10;
            $CPOh[10] = $bar->cpo11;
            $CPOh[11] = $bar->cpo12;
            $KERh[0] = $bar->ker1;
            $KERh[1] = $bar->ker2;
            $KERh[2] = $bar->ker3;
            $KERh[3] = $bar->ker4;
            $KERh[4] = $bar->ker5;
            $KERh[5] = $bar->ker6;
            $KERh[6] = $bar->ker7;
            $KERh[7] = $bar->ker8;
            $KERh[8] = $bar->ker9;
            $KERh[9] = $bar->ker10;
            $KERh[10] = $bar->ker11;
            $KERh[11] = $bar->ker12;
        }
        $str = "select \r\n                                      sum(rp01) as olah1,\r\n                                      sum(rp02) as olah2,\r\n                                      sum(rp03) as olah3,\r\n                                      sum(rp04) as olah4,\r\n                                      sum(rp05) as olah5,\r\n                                      sum(rp06) as olah6,\r\n                                      sum(rp07) as olah7,\r\n                                      sum(rp08) as olah8,\r\n                                      sum(rp09) as olah9,\r\n                                      sum(rp10) as olah10,\r\n                                      sum(rp11) as olah11,\r\n                                      sum(rp12) as olah12\r\n                           from ".$dbname.'.bgt_budget_detail where tahunbudget='.$tahun." \r\n                           and noakun >= '".$akunatas."' and noakun not like '64%'\r\n                           and kodeorg like '".$pks."%'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $Rbgt[0] = $bar->olah1 / 1000000;
            $Rbgt[1] = $bar->olah2 / 1000000;
            $Rbgt[2] = $bar->olah3 / 1000000;
            $Rbgt[3] = $bar->olah4 / 1000000;
            $Rbgt[4] = $bar->olah5 / 1000000;
            $Rbgt[5] = $bar->olah6 / 1000000;
            $Rbgt[6] = $bar->olah7 / 1000000;
            $Rbgt[7] = $bar->olah8 / 1000000;
            $Rbgt[8] = $bar->olah9 / 1000000;
            $Rbgt[9] = $bar->olah10 / 1000000;
            $Rbgt[10] = $bar->olah11 / 1000000;
            $Rbgt[11] = $bar->olah12 / 1000000;
            $bRcpo[0] = $bar->olah1 / $CPOh[0];
            $bRker[0] = $bar->olah1 / $KERh[0];
            $bRcpo[1] = $bar->olah2 / $CPOh[1];
            $bRker[1] = $bar->olah2 / $KERh[1];
            $bRcpo[2] = $bar->olah3 / $CPOh[2];
            $bRker[2] = $bar->olah3 / $KERh[2];
            $bRcpo[3] = $bar->olah4 / $CPOh[3];
            $bRker[3] = $bar->olah4 / $KERh[3];
            $bRcpo[4] = $bar->olah5 / $CPOh[4];
            $bRker[4] = $bar->olah5 / $KERh[4];
            $bRcpo[5] = $bar->olah6 / $CPOh[5];
            $bRker[5] = $bar->olah6 / $KERh[5];
            $bRcpo[6] = $bar->olah7 / $CPOh[6];
            $bRker[6] = $bar->olah7 / $KERh[6];
            $bRcpo[7] = $bar->olah8 / $CPOh[7];
            $bRker[7] = $bar->olah8 / $KERh[7];
            $bRcpo[8] = $bar->olah9 / $CPOh[8];
            $bRker[8] = $bar->olah9 / $KERh[8];
            $bRcpo[9] = $bar->olah10 / $CPOh[9];
            $bRker[9] = $bar->olah10 / $KERh[9];
            $bRcpo[10] = $bar->olah11 / $CPOh[10];
            $bRker[10] = $bar->olah11 / $KERh[10];
            $bRcpo[11] = $bar->olah12 / $CPOh[11];
            $bRker[11] = $bar->olah12 / $KERh[11];
        }
        if (0 === $biaya) {
            exit('No current data');
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
        $graph->title->Set('BIAYA PRODUKSI VS BUDGET  '.$param['pks']);
        $txt = new Text('RP(Juta)');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $txt = new Text('Rp/Kg');
        $txt->SetPos(0.95, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $plot1 = new BarPlot($data);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot1->SetLegend('Realisasi (Juta)');
        $plot2 = new BarPlot($Rbgt);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot2->SetLegend('Budget (Juta)');
        $plot5 = new LinePlot($ceka);
        $plot5->SetLegend('Aktual Rp/Kg CPO');
        $plot7 = new LinePlot($bRcpo);
        $plot7->SetLegend('Budget Rp/Kg CPO');
        $plot5->mark->SetType(MARK_FILLEDCIRCLE);
        $plot5->mark->SetFillColor('magenta');
        $plot7->mark->SetType(MARK_FILLEDCIRCLE);
        $plot7->mark->SetFillColor('red');
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->AddY2($plot5);
        $graph->AddY2($plot7);
        $graph->StrokeCSIM();
        echo '<br>Biaya Tidak termasuk pembelian TBS<br>';
        $str = 'select noakun,namaakun from '.$dbname.'.keu_5akun where length(noakun)=3';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $akun[$bar->noakun] = $bar->namaakun;
        }
        $str = 'select sum(debet)-sum(kredit) as biaya,left(noakun,3) as major,periode from '.$dbname.".keu_jurnaldt_vw \r\n                 where kodeorg='".$pks."' and periode like '".$tahun."%'\r\n                 and noakun >= '".$akunatas."' and noakun not like '64%' group by left(noakun,3),periode order by left(noakun,3), periode";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $biaya[$bar->major][str_replace('-', '', $bar->periode)] = $bar->biaya;
            $major[$bar->major] = $bar->major;
        }
        $str = "select left(noakun,3) as major,\r\n                        sum(rp01) as olah1,\r\n                        sum(rp02) as olah2,\r\n                        sum(rp03) as olah3,\r\n                        sum(rp04) as olah4,\r\n                        sum(rp05) as olah5,\r\n                        sum(rp06) as olah6,\r\n                        sum(rp07) as olah7,\r\n                        sum(rp08) as olah8,\r\n                        sum(rp09) as olah9,\r\n                        sum(rp10) as olah10,\r\n                        sum(rp11) as olah11,\r\n                        sum(rp12) as olah12\r\n             from ".$dbname.'.bgt_budget_detail where tahunbudget='.$tahun." \r\n             and noakun >= '".$akunatas."' and noakun not like '64%'\r\n             and kodeorg like '".$pks."%' group by left(noakun,3) order by noakun";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $budget[$bar->major]['01'] = $bar->olah1;
            $budget[$bar->major]['02'] = $bar->olah2;
            $budget[$bar->major]['03'] = $bar->olah3;
            $budget[$bar->major]['04'] = $bar->olah4;
            $budget[$bar->major]['05'] = $bar->olah5;
            $budget[$bar->major]['06'] = $bar->olah6;
            $budget[$bar->major]['07'] = $bar->olah7;
            $budget[$bar->major]['08'] = $bar->olah8;
            $budget[$bar->major]['09'] = $bar->olah9;
            $budget[$bar->major][10] = $bar->olah10;
            $budget[$bar->major][11] = $bar->olah11;
            $budget[$bar->major][12] = $bar->olah12;
            $budget[$bar->major]['total'] = $bar->olah1 + $bar->olah2 + $bar->olah3 + $bar->olah4 + $bar->olah5 + $bar->olah6 + $bar->olah7 + $bar->olah8 + $bar->olah9 + $bar->olah10 + $bar->olah11 + $bar->olah12;
        }
        echo "<table border=1 cellspacing=0><tr>\r\n            <td rowspan=2>No.Akun</td>\r\n            <td rowspan=2>Nama.Akun</td>\r\n            <td colspan=2 align=center>Jan ".$tahun."</td>\r\n            <td colspan=2 align=center>Feb  ".$tahun."</td>\r\n            <td colspan=2 align=center>Mar  ".$tahun."</td>\r\n            <td colspan=2 align=center>Apr ".$tahun."</td>\r\n            <td colspan=2 align=center>Mei ".$tahun."</td>\r\n            <td colspan=2 align=center>Jun ".$tahun."</td>\r\n            <td colspan=2 align=center>Jul ".$tahun."</td>\r\n            <td colspan=2 align=center>Aug ".$tahun."</td>\r\n            <td colspan=2 align=center>Sep ".$tahun."</td>\r\n            <td colspan=2 align=center>Oct ".$tahun."</td>\r\n            <td colspan=2 align=center>Nov ".$tahun."</td>\r\n            <td colspan=2 align=center>Dec ".$tahun."</td>\r\n            <td colspan=2 align=center>Total</td>\r\n            </tr>\r\n            <tr><td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td>\r\n            <td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td>\r\n            <td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td>\r\n            <td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td><td>Realisasi</td><td>Budget</td>\r\n            <td>Realisasi</td><td>Budget</td></tr>\r\n             ";
        foreach ($major as $kay => $val) {
            echo '<tr><td>'.$kay.'</td><td>'.$akun[$kay].'</td>';
            for ($x = 1; $x <= 12; ++$x) {
                $y = str_pad($x, 2, '0', STR_PAD_LEFT);
                echo ' <td align=right>'.number_format($biaya[$val][$tahun.$y])."</td>\r\n                        <td align=right>".number_format($budget[$val][$y]).'</td>';
                $total[$val] += $biaya[$val][$tahun.$y];
                $totalReal[$y] += $biaya[$val][$tahun.$y];
                $totalbudget[$y] += $budget[$val][$y];
            }
            echo ' <td align=right>'.number_format($total[$val])."</td>\r\n                        <td align=right>".number_format($budget[$val]['total']).'</td>';
            echo '</tr>';
        }
        echo '<tr><td colspan=2>Total</td>';
        for ($x = 1; $x <= 12; ++$x) {
            $y = str_pad($x, 2, '0', STR_PAD_LEFT);
            echo ' <td align=right>'.number_format($totalReal[$y])."</td>\r\n                        <td align=right>".number_format($totalbudget[$y]).'</td>';
            $totalR += $totalReal[$y];
            $totalB += $totalbudget[$y];
        }
        echo ' <td align=right>'.number_format($totalR)."</td>\r\n                        <td align=right>".number_format($totalB).'</td>';
        echo '</tr></table>';

        break;
}

?>