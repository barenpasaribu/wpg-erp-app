<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
require_once 'jpgraph/jpgraph_table.php';
require_once 'jpgraph/jpgraph_canvas.php';
include 'jpgraph/jpgraph_iconplot.php';
include 'jpgraph/jpgraph_flags.php';
$param = $_GET;
$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
switch ($param['jenis']) {
    case 'global':
        $kodeorg = $param['pks'];
        $periode = $param['tahun'];
        $qwe = explode('-', $periode);
        list($tahun, $bulan) = $qwe;
        $optnmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
        $optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
        if ('' !== $kodeorg) {
            $whr = " and kodeorg like '".$kodeorg."%'";
        } else {
            $kodeorg = 'MHO';
        }

        $sData = 'select batch,substr(kodeorg,1,4) as kbn,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi  where tanggal<='".$periode."-31'".$whr.' group by batch,kodeorg order by kodeorg asc';
        $qData = mysql_query($sData) || exit(mysql_error($conns));
        while ($rData = mysql_fetch_assoc($qData)) {
            $sDatabatch = "select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi \n             from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
            $qDataBatch = mysql_query($sDatabatch) || exit(mysql_error($sDatabatch));
            $rDataBatch = mysql_fetch_assoc($qDataBatch);
            $thnData = substr($rDataBatch['tanggaltanam'], 0, 4);
            $starttime = strtotime($rDataBatch['tanggaltanam']);
            if (date('Ymd') < str_replace('-', '', $periode).'31') {
                $endtime = time();
            } else {
                $endtime = mktime(0, 0, 0, substr($periode, 5, 2), 31, substr($periode, 0, 4));
            }

            $jmlHari = ($endtime - $starttime) / (60 * 60 * 24 * 30);
            if (1 === floor($jmlHari)) {
                $umr = '01';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (2 === floor($jmlHari)) {
                $umr = '02';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (3 === floor($jmlHari)) {
                $umr = '03';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (4 === floor($jmlHari)) {
                $umr = '04';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (5 === floor($jmlHari)) {
                $umr = '05';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (6 === floor($jmlHari)) {
                $umr = '06';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (7 === floor($jmlHari)) {
                $umr = '07';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (8 === floor($jmlHari)) {
                $umr = '08';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (9 === floor($jmlHari)) {
                $umr = '09';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (10 < $jmlHari || 10 === $jmlHari) {
                $umr = '10';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (11 === floor($jmlHari)) {
                $umr = '11';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (12 === floor($jmlHari)) {
                $umr = '12';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if (12 < floor($jmlHari)) {
                $umr = '13';
                $lstBatch[$umr][$rData['batch']] = $rData['batch'];
            }

            if ($kbnDt !== $rData['kbn']) {
                $kbnDt = $rData['kbn'];
                $ard = 1;
            } else {
                if ($jns !== $rDataBatch['jenisbibit']) {
                    $jns = $rDataBatch['jenisbibit'];
                    ++$ard;
                }
            }

            $dtRow[$rData['kbn']] = $ard;
            $dtJnsBibi[$rDataBatch['jenisbibit']] = $rDataBatch['jenisbibit'];
            $dtJmlh[$rDataBatch['jenisbibit']] += $rData['jumlah'];
            $dtKbn[$rData['kbn']] = $rData['kbn'];
            ++$ard;
        }
        $rowdata = count($dtJnsBibi);
        if (0 === $rowdata) {
            exit('Error:Data Kosong');
        }

        foreach ($lstBatch as $lstDtBtch => $btchdt) {
            foreach ($btchdt as $isiBatch) {
                $prd = $tahun.'-'.$lstDtBtch;
                $sData = "select batch,substr(kodeorg,1,4) as kbn,sum(jumlah) as jumlah \n                            from ".$dbname.".bibitan_mutasi  where tanggal<='".$prd."-31'".$whr." and batch='".$isiBatch."' group by batch order by batch desc";
                $qData = mysql_query($sData) || exit(mysql_error($conns));
                $rData = mysql_fetch_assoc($qData);
                $sDatabatch = 'select distinct jenisbibit from '.$dbname.".bibitan_batch \n                                 where batch='".$rData['batch']."' ";
                $qDataBatch = mysql_query($sDatabatch) || exit(mysql_error($sDatabatch));
                $rDataBatch = mysql_fetch_assoc($qDataBatch);
                if (0 !== $rData['jumlah']) {
                    $dtJmlh2[$rDataBatch['jenisbibit']][$lstDtBtch] += $rData['jumlah'];
                }
            }
        }
        $tab .= "<link rel=stylesheet tyle=text href='style/generic.css'>\n                              <script language=javascript src='js/generic.js'></script>";
        $tab .= '<br />Stok Bibit Per :'.$periode."\n                  <table cellpadding=1 cellspacing=1 border=0 class=sortable>\n                <thead>\n                <tr class=rowheader>\n                <td rowspan=2 align=center>".$_SESSION['lang']['kebun']."</td>\n                <td rowspan=2 align=center>".$_SESSION['lang']['jenisbibit']."</td>\n                <td rowspan=2 align=center>".$_SESSION['lang']['total']."</td>\n                <td colspan=13 align=center>Umur (bulan)</td></tr>\n                <tr>";
        $ader = 1;
        $arrBulanDt = [];
        foreach ($optBulan as $blnno => $lstBln) {
            $tab .= '<td align=center>'.(int) $blnno.'</td>';
            $arrBulanDt[] = (int) $blnno;
        }
        $tab .= '<td align=center>>12</td>';
        $tab .= "</tr>\n                </thead><tbody id=containDataStock>";
        foreach ($dtJnsBibi as $lsJnsBibit) {
            if (0 !== $dtJmlh[$lsJnsBibit]) {
                $tab .= '<tr class=rowcontent>';
                if ($dert2 !== $kodeorg) {
                    $dert2 = $kodeorg;
                    $dtKodeorg = $optNm[$kodeorg];
                    $ert = true;
                    $tab .= '<td>'.$dtKodeorg.'</td>';
                } else {
                    if (true === $ert) {
                        $ert = false;
                        $tab .= '<td rowspan='.($rowdata - 1).'>&nbsp;</td>';
                    }
                }

                $tab .= '<td>'.$lsJnsBibit.'</td>';
                $tab .= '<td align=right>'.number_format($dtJmlh[$lsJnsBibit], 0).'</td>';
                foreach ($optBulan as $blnno => $lstBln) {
                    $tab .= '<td align=right>'.number_format($dtJmlh2[$lsJnsBibit][$blnno], 2).'</td>';
                    if ('1' === (int) $blnno) {
                        $dert = 0;
                        $arrIsi[$ader][$dert] = $lsJnsBibit;
                        ++$dert;
                        $arrIsi[$ader][$dert] .= $dtJmlh2[$lsJnsBibit][$blnno];
                    } else {
                        ++$dert;
                        $arrIsi[$ader][$dert] .= $dtJmlh2[$lsJnsBibit][$blnno];
                    }

                    if (12 === (int) $blnno) {
                        ++$ader;
                    }
                }
                $ardt = 13;
                $tab .= '<td align=right>'.number_format($dtJmlh2[$lsJnsBibit][$ardt], 2).'</td>';
                $tab .= '</tr>';
            }
        }
        $tab .= '</tbody></table>';
        $arrd = [' ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
        for ($awl = 1; $awl < $ader; ++$awl) {
            if (1 === $awl) {
                $datay[] = $arrd;
            }

            $datay[] = $arrIsi[$awl];
        }
        $nbrbar = 12;
        $cellwidth = 70;
        $tableypos = 170;
        $tablexpos = 60;
        $tablewidth = ($nbrbar + 1) * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $height = 320;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $graph = new Graph($width, $height);
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('white');
        if (4 === $ader) {
            $bplot = new BarPlot($datay[3]);
            $bplot->SetFillColor('orange');
            $bplot2 = new BarPlot($datay[2]);
            $bplot2->SetFillColor('red');
            $bplot3 = new BarPlot($datay[1]);
            $bplot3->SetFillColor('darkgreen');
            $accbplot = new AccBarPlot([$bplot, $bplot2, $bplot3]);
        } else {
            if (3 === $ader) {
                $bplot2 = new BarPlot($datay[2]);
                $bplot2->SetFillColor('red');
                $bplot3 = new BarPlot($datay[1]);
                $bplot3->SetFillColor('darkgreen');
                $accbplot = new AccBarPlot([$bplot2, $bplot3]);
            } else {
                if (2 === $ader) {
                    $bplot3 = new BarPlot($datay[1]);
                    $bplot3->SetFillColor('darkgreen');
                    $accbplot = new AccBarPlot([$bplot3]);
                }
            }
        }

        $accbplot->value->Show();
        $graph->Add($accbplot);
        $table = new GTextTable();
        $table->Set($datay);
        $table->SetPos($tablexpos, $tableypos + 1);
        $table->SetFont(FF_ARIAL, FS_NORMAL, 7);
        $table->SetAlign('right');
        $table->SetMinColWidth($cellwidth);
        $table->SetRowPadding(0, 5);
        $table->SetRowFillColor(0, 'teal@0.7');
        $table->SetRowFont(0, FF_ARIAL, FS_BOLD, 8);
        $table->SetRowAlign(0, 'center');
        $graph->Add($table);
        $graph->StrokeCSIM();
        echo $tab;

        break;
}

?>