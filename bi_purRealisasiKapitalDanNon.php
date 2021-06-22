<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_line.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_pie.php';
require_once 'jpgraph/jpgraph_pie3d.php';
require_once 'jpgraph/jpgraph_table.php';
require_once 'jpgraph/jpgraph_canvas.php';
include 'jpgraph/jpgraph_mgraph.php';
$param = $_GET;
$tipe = $param['tipe'];
$kdPt = $param['kdPt'];
$periode = $param['periode'];
$stat = $param['stat'];
$klmpkBrg = $param['klmpkBrg'];
$indatais = $param['indatais'];
$regDt = $param['regDt'];
$qwe = explode('-', $periode);
list($tahun, $bulan) = $qwe;
$optKlmpk = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optNmbrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (('excel' === $proses || 'preview' === $proses) && '' === $periode) {
    exit('Error:Field Tidak Boleh Kosong');
}

$whrtd = " regional in ('SUMSEL','LAMPUNG')";
$sUnit = 'select distinct kodeunit from '.$dbname.'.bgt_regional_assignment where '.$whrtd.'';
$sumselReg = '';
$qUnit = mysql_query($sUnit) || exit(mysql_error($conns));
while ($rUnit = mysql_fetch_assoc($qUnit)) {
    ++$ader;
    if (1 === $ader) {
        $sumselReg .= "'".$rUnit['kodeunit']."'";
    } else {
        $sumselReg .= ",'".$rUnit['kodeunit']."'";
    }
}
$whrbgtSumsel = ' and substr(kodeorg,1,4) in ('.$sumselReg.')';
$whrKaptSumsel = ' and substr(kodeunit,1,4) in ('.$sumselReg.')';
$sPt = 'select distinct induk from '.$dbname.'.organisasi where kodeorganisasi in ('.$sumselReg.')';
$qPt = mysql_query($sPt) || exit(mysql_error($conns));
while ($rPt = mysql_fetch_assoc($qPt)) {
    ++$ert;
    $optPt[$rPt['induk']] = 'SUMSEL';
    if (1 === $ert) {
        $dtPtSumsel .= "'".$rPt['induk']."'";
    } else {
        $dtPtSumsel .= ",'".$rPt['induk']."'";
    }
}
$ader = 0;
$sUnit = 'select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='KALTIM'";
$kaltimReg = '';
$qUnit = mysql_query($sUnit) || exit(mysql_error($conns));
while ($rUnit = mysql_fetch_assoc($qUnit)) {
    ++$ader;
    if (1 === $ader) {
        $kaltimReg .= "'".$rUnit['kodeunit']."'";
    } else {
        $kaltimReg .= ",'".$rUnit['kodeunit']."'";
    }
}
$whrbgtKaltim = ' and substr(kodeorg,1,4) in ('.$kaltimReg.')';
$whrKaptKaltim = ' and substr(kodeunit,1,4) in ('.$kaltimReg.')';
$dtPtKaltim = '';
$ert = 0;
$sPt = 'select distinct induk from '.$dbname.'.organisasi where kodeorganisasi in ('.$kaltimReg.')';
$qPt = mysql_query($sPt) || exit(mysql_error($conns));
while ($rPt = mysql_fetch_assoc($qPt)) {
    ++$ert;
    $optPt[$rPt['induk']] = 'KALTIM';
    if (1 === $ert) {
        $dtPtKaltim .= "'".$rPt['induk']."'";
    } else {
        $dtPtKaltim .= ",'".$rPt['induk']."'";
    }
}
$sKurs = 'select distinct kode from '.$dbname.".setup_matauang where kode!='IDR' and kode!='' order by kode desc";
$qKurs = mysql_query($sKurs) || exit(mysql_error($conns));
while ($rKurs = mysql_fetch_assoc($qKurs)) {
    ++$ard;
    $arr .= '##mtUang_'.$ard.'';
    $arr .= '##kurs_'.$ard.'';
    if ($param['mtUang_'.$ard] === $rKurs['kode']) {
        $krsDt[$rKurs['kode']] = $param['kurs_'.$ard];
    }
}
$addstr = '(';
for ($W = 1; $W <= (int) $bulan; ++$W) {
    if ($W < 10) {
        $jack = 'rp0'.$W;
    } else {
        $jack = 'rp'.$W;
    }

    if ($W < (int) $bulan) {
        $addstr .= $jack.'+';
    } else {
        $addstr .= $jack;
    }
}
$addstr .= ')';
$addkap = '(';
for ($W = 1; $W <= (int) $bulan; ++$W) {
    if ($W < 10) {
        $jack = 'k0'.$W;
    } else {
        $jack = 'k'.$W;
    }

    if ($W < (int) $bulan) {
        $addkap .= $jack.'+';
    } else {
        $addkap .= $jack;
    }
}
$addkap .= ')';
$tab = "<link rel=stylesheet tyle=text href='style/generic.css'>\n            <script language=javascript src='js/generic.js'></script>";
switch ($param['jenis']) {
    case 'global':
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtSumsel.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgt = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptSumsel.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgt = $res['total'];
        $totAllBgtSumsel = $totKapBgt + $totNonBgt;
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totAllRealSumsel += $rTot['total'];
        }
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtKaltim.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgtKaltim = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptKaltim.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgtKaltim = $res['total'];
        $totAllBgtKaltim = $totKapBgtKaltim + $totNonBgtKaltim;
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtKaltim.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totAllKaltim += $rTot['total'];
        }
        $totRealSma = $totAllRealSumsel + $totAllKaltim;
        $totBgtSma = $totAllBgtSumsel + $totAllBgtKaltim;
        $arrhead = [$_SESSION['lang']['regional'], 'SUMSEL', 'KALTIM', strtoupper($_SESSION['lang']['total'])];
        $arrReal = [$_SESSION['lang']['realisasi'], $totAllRealSumsel, $totAllKaltim, $totRealSma];
        $arrBgt = [$_SESSION['lang']['budget'], $totAllBgtSumsel, $totAllBgtKaltim, $totBgtSma];
        $arrhead2 = ['SUMSEL', 'KALTIM', $_SESSION['lang']['total']];
        $totAllRSmsel = $totAllRealSumsel / 1000000;
        $totAllRKtlm = $totAllKaltim / 1000000;
        $totAllRsmaa = $totRealSma / 1000000;
        $totAllBSmsel = $totAllBgtSumsel / 1000000;
        $totAllBKltm = $totAllBgtKaltim / 1000000;
        $totAllBsmaa = $totBgtSma / 1000000;
        $arrReal2 = [$totAllRSmsel, $totAllRKtlm, $totAllRsmaa];
        $arrBgt2 = [$totAllBSmsel, $totAllBKltm, $totAllBsmaa];
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td align=center>'.$_SESSION['lang']['regional'].'</td>';
        $tab .= '<td align=center>SUMSEL</td><td align=center>KALTIM</td><td align=center>'.$_SESSION['lang']['total'].'</td></tr><thead><tbody>';
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['realisasi'].'</td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Sumsel&stat=1'  class=linkBi>".number_format($totAllRealSumsel, 0).'</a></td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Kaltim&stat=1' class=linkBi>".number_format($totAllKaltim, 0).'</a></td>';
        $tab .= '<td align=right>'.number_format($totRealSma, 0).'</td></tr>';
        $tab .= '<tr class=rowcontent >';
        $tab .= '<td>'.$_SESSION['lang']['budget'].'</td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Sumsel&stat=1' class=linkBi>".number_format($totAllBgtSumsel, 0).'</a></td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Kaltim&stat=1' class=linkBi>".number_format($totAllBgtKaltim, 0).'</a></td>';
        $tab .= '<td align=right>'.number_format($totBgtSma, 0).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaAnggaran' title='tampilan dalam persen dari anggaran'  class=linkBi>% Dari Anggaran</a> | \n               <a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaTotGrop' title='tampilan dalam persen dari Total Group' class=linkBi>% Dari Total Group</a></p>";
        $datay[] = $arrhead;
        $datay[] = $arrReal;
        $datay[] = $arrBgt;
        $drr = count($arrhead);
        $nbrbar = $drr;
        $cellwidth = 125;
        $tableypos = 200;
        $tablexpos = 120;
        $tablewidth = $nbrbar * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $table = new GTextTable();
        $table->Set($datay);
        $table->SetPos($tablexpos, $tableypos + 85);
        $table->SetFont(FF_VERDANA, FS_NORMAL, 8);
        $table->SetAlign('right');
        $table->SetMinColWidth($cellwidth);
        $table->SetNumberFormat('%0.2f');
        $table->SetRowPadding(0, 5);
        $table->SetRowFillColor(0, 'teal@0.5');
        $table->SetRowFont(0, FF_ARIAL, FS_BOLD, 9);
        $table->SetRowAlign(0, 'center');
        $datay1 = $arrReal2;
        $datay2 = $arrBgt2;
        $graph = new Graph($width, 455, 'auto');
        $graph->SetScale('textlin');
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($arrhead2);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('NILAI REALISASI PEMBELIAN vs ANGGARAN TAHUN '.$tahun);
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $bplot1 = new BarPlot($datay1);
        $bplot2 = new BarPlot($datay2);
        $bplot1->SetFillColor('darkgreen');
        $bplot2->SetFillColor('yellow');
        $bplot1->SetLegend('Realisasi');
        $bplot2->SetLegend('Budget');
        $graph->legend->Pos(0.06, 0.04);
        $gbarplot = new GroupBarPlot([$bplot1, $bplot2]);
        $gbarplot->SetWidth(0.3);
        $graph->Add($gbarplot);
        $graph->StrokeCSIM();
        echo $tab;

        break;
    case 'Sumsel':
        $aresta = 'SELECT sum('.$addstr.') as total,left(kodeorg,4) as kdkbn FROM '.$dbname.".bgt_budget_detail\n            WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtSumsel.' group by left(kodeorg,4)';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $sInduk = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$res['kdkbn']."'";
            $qInduk = mysql_query($sInduk) || exit(mysql_error($conns));
            $rInduk = mysql_fetch_assoc($qInduk);
            $totBgt[$rInduk['induk']] += $res['total'];
        }
        $aresta = 'SELECT sum(harga) as total,left(kodeunit,4) as kdkbn FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptSumsel.' group by left(kodeunit,4)';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $sInduk = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$res['kdkbn']."'";
            $qInduk = mysql_query($sInduk) || exit(mysql_error($conns));
            $rInduk = mysql_fetch_assoc($qInduk);
            $totBgt[$rInduk['induk']] += $res['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'\n                 group by kodeorg order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRel[$rTot['kodeorg']] += $rTot['total'];
            $dInduk[$rTot['kodeorg']] = $rTot['kodeorg'];
            $dataReal[] = $rTot['total'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td align=center></td>';
        foreach ($dInduk as $lstPt) {
            $tab .= '<td align=center>'.$lstPt.'</td>';
        }
        $tab .= '<td align=center>'.$_SESSION['lang']['total'].'</td></tr></thead><tbody>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['realisasi'].'</td>';
        foreach ($dInduk as $lstPt) {
            $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt&kdPt='.$lstPt.'&stat='.$stat."' title='Pembelian PT. ".$lstPt."' class=linkBi>".number_format($totRel[$lstPt], 0).'</a></td>';
            $totDt += $totRel[$lstPt];
            $totRel[$lstPt] = $totRel[$lstPt] / 1000000;
            $dIndukDt[] = $totRel[$lstPt];
            $lstPtDt[] = $lstPt;
        }
        $tab .= '<td align=right>'.number_format($totDt, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['anggaran'].'</td>';
        foreach ($dInduk as $lstPt) {
            $tab .= '<td align=right>'.number_format($totBgt[$lstPt], 0).'</td>';
            $totBgtDt += $totBgt[$lstPt];
            $totBgt[$lstPt] = $totBgt[$lstPt] / 1000000;
            $dIndukBgtdt[] = $totBgt[$lstPt];
        }
        $tab .= '<td align=right>'.number_format($totBgtDt, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        if (1 === $stat) {
            $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=global' title='kembali ke halaman awal'>back</a> </p>";
        } else {
            if (2 === $stat) {
                $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaAnggaran' title='kembali ke halaman awal'>back</a> </p>";
            } else {
                if (3 === $stat) {
                    $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaTotGrop' title='kembali ke halaman awal'>back</a> </p>";
                }
            }
        }

        $drr = count($lstPtDt);
        $nbrbar = $drr;
        $cellwidth = 115;
        $tableypos = 250;
        $tablexpos = 120;
        $tablewidth = $nbrbar * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $height = 295;
        $datay1 = $dIndukDt;
        $datay2 = $dIndukBgtdt;
        $graph = new Graph($width, $height, 'auto');
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($lstPtDt);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('NILAI REALISASI PEMBELIAN vs ANGGARAN TAHUN '.$tahun);
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $bplot1 = new BarPlot($datay1);
        $bplot2 = new BarPlot($datay2);
        $bplot1->SetFillColor('darkgreen');
        $bplot2->SetFillColor('yellow');
        $bplot1->SetLegend('Realisasi');
        $bplot2->SetLegend('Budget');
        $graph->legend->Pos(0.05, 0.06);
        $gbarplot = new GroupBarPlot([$bplot1, $bplot2]);
        $gbarplot->SetWidth(0.5);
        $graph->Add($gbarplot);
        $graph->StrokeCSIM();
        echo $tab;

        break;
    case 'Kaltim':
        $aresta = 'SELECT sum('.$addstr.') as total,left(kodeorg,4) as kdkbn FROM '.$dbname.".bgt_budget_detail\n            WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtKaltim.' group by left(kodeorg,4)';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $sInduk = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$res['kdkbn']."'";
            $qInduk = mysql_query($sInduk) || exit(mysql_error($conns));
            $rInduk = mysql_fetch_assoc($qInduk);
            $totBgt[$rInduk['induk']] += $res['total'];
        }
        $aresta = 'SELECT sum(harga) as total,left(kodeunit,4) as kdkbn FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptKaltim.' group by left(kodeunit,4)';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $sInduk = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$res['kdkbn']."'";
            $qInduk = mysql_query($sInduk) || exit(mysql_error($conns));
            $rInduk = mysql_fetch_assoc($qInduk);
            $totBgt[$rInduk['induk']] += $res['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtKaltim.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'\n                 group by kodeorg order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRel[$rTot['kodeorg']] += $rTot['total'];
            $dInduk[$rTot['kodeorg']] = $rTot['kodeorg'];
            $dataReal[] = $rTot['total'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td align=center></td>';
        foreach ($dInduk as $lstPt) {
            $tab .= '<td align=center>'.$lstPt.'</td>';
        }
        $tab .= '<td align=center>'.$_SESSION['lang']['total'].'</td></tr></thead><tbody>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['realisasi'].'</td>';
        foreach ($dInduk as $lstPt) {
            $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperptKal&kdPt='.$lstPt.'&stat='.$stat."' title='Pembelian PT. ".$lstPt."' class=linkBi>".number_format($totRel[$lstPt], 0).'</a></td>';
            $totDt += $totRel[$lstPt];
            $totRel[$lstPt] = $totRel[$lstPt] / 1000000;
            $dIndukDt[] = $totRel[$lstPt];
            $lstPtDt[] = $lstPt;
        }
        $tab .= '<td align=right>'.number_format($totDt, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['anggaran'].'</td>';
        foreach ($dInduk as $lstPt) {
            $tab .= '<td align=right>'.number_format($totBgt[$lstPt], 0).'</td>';
            $totBgtDt += $totBgt[$lstPt];
            $totBgt[$lstPt] = $totBgt[$lstPt] / 1000000;
            $dIndukBgtdt[] = $totBgt[$lstPt];
        }
        $tab .= '<td align=right>'.number_format($totBgtDt, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        if (1 === $stat) {
            $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=global' title='kembali ke halaman awal'>back</a> </p>";
        } else {
            if (2 === $stat) {
                $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaAnggaran' title='kembali ke halaman awal'>back</a> </p>";
            } else {
                if (3 === $stat) {
                    $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaTotGrop' title='kembali ke halaman awal'>back</a> </p>";
                }
            }
        }

        $drr = count($lstPtDt);
        $nbrbar = $drr;
        $cellwidth = 125;
        $tableypos = 250;
        $tablexpos = 120;
        $tablewidth = $nbrbar * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $height = 295;
        $datay1 = $dIndukDt;
        $datay2 = $dIndukBgtdt;
        $graph = new Graph($width, $height, 'auto');
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($lstPtDt);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('NILAI REALISASI PEMBELIAN vs ANGGARAN TAHUN '.$tahun);
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $bplot1 = new BarPlot($datay1);
        $bplot2 = new BarPlot($datay2);
        $bplot1->SetFillColor('darkgreen');
        $bplot2->SetFillColor('yellow');
        $bplot1->SetLegend('Realisasi');
        $bplot2->SetLegend('Budget');
        $graph->legend->Pos(0.05, 0.06);
        $gbarplot = new GroupBarPlot([$bplot1, $bplot2]);
        $gbarplot->SetWidth(0.3);
        $graph->Add($gbarplot);
        $graph->StrokeCSIM();
        echo $tab;

        break;
    case 'prsnaAnggaran':
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtSumsel.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgt = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptSumsel.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgt = $res['total'];
        $totAllBgtSumsel = $totKapBgt + $totNonBgt;
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totAllRealSumsel += $rTot['total'];
        }
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtKaltim.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgtKaltim = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptKaltim.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgtKaltim = $res['total'];
        $totAllBgtKaltim = $totKapBgtKaltim + $totNonBgtKaltim;
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtKaltim.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totAllKaltim += $rTot['total'];
        }
        $totRealSma = $totAllRealSumsel + $totAllKaltim;
        $totBgtSma = $totAllBgtSumsel + $totAllBgtKaltim;
        $prsnSumsel = $totAllRealSumsel / $totAllBgtSumsel * 100;
        $prsnKaltim = $totAllKaltim / $totAllBgtKaltim * 100;
        $prsnSma = $totRealSma / $totBgtSma * 100;
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<tr class=rowcontent><td rowspan=2>%  dari Anggaran</td>';
        $tab .= '<td>SUMSEL</td><td>KALTIM</td><td>TOTAL</td></tr>';
        $tab .= '<tr class=rowcontent><td align=right>'.number_format($prsnSumsel, 0).'</td>';
        $tab .= '<td align=right>'.number_format($prsnKaltim, 0).'</td>';
        $tab .= '<td align=right>'.number_format($prsnSma, 0).'</td></tr></table>';
        $data = [$prsnSumsel, $prsnKaltim, $prsnSma];
        $arrhead = ["SUMSEL\n".number_format($prsnSumsel, 0).'%', " KALTIM\n".number_format($prsnKaltim, 0).'%', strtoupper($_SESSION['lang']['total'])."\n".number_format($prsnSma, 0).'%'];
        $graph = new PieGraph(700, 500);
        $graph->title->Set('%  dari Anggaran Tahun '.$tahun.'');
        $graph->title->SetMargin(1);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->SetCenter(0.5, 0.45);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('white');
        $p1->SetLabels($arrhead);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $targ = [$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Sumsel&stat=2', $_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Kaltim&stat=2'];
        $alts = ['Click to drill', 'Click to drill'];
        $p1->SetCSIMTargets($targ, $alts);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=global' title='kembali ke halaman sebelumnya'>back</a> </p>";
        echo $tab;

        break;
    case 'beliperpt':
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) not in ('8','9')\n                 order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRelN += $rTot['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) in ('9') and left(kodebarang,1) !='8'\n                 order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRelK += $rTot['total'];
        }
        $data = [$totRelN, $totRelK];
        $graph = new PieGraph(700, 500);
        $graph->title->Set('Realisasi Pembelian PT.'.$kdPt.',Tahun '.$tahun.'');
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('white');
        $p1->SetLabelType(PIE_VALUE_PER);
        $lbl = ["Non Kapital\n%.1f%%", "Kapital\n%.1f%%"];
        $p1->SetLabels($lbl);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $targ = [$_SERVER['PHP_SELF'].'?periode='.$periode.'&kdPt='.$kdPt.'&jenis=nonkapital&stat='.$stat.'', $_SERVER['PHP_SELF'].'?periode='.$periode.'&kdPt='.$kdPt.'&jenis=kapital&stat='.$stat.''];
        $alts = ['Click to drill', 'Click to drill'];
        $p1->SetCSIMTargets($targ, $alts);
        $txt = new Text('Non Kapital :Rp. '.number_format($totRelN, 0)." \nKapital :Rp. ".number_format($totRelK, 0).'');
        $txt->SetPos(0.5, 0.2, 'center', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td colspan=3>Realisasi Pembelian PT.'.$kdPt.',Tahun '.$tahun.'</td></tr><tr>';
        $tab .= '<td>Non Kapital</td>';
        $tab .= '<td>'.$_SESSION['lang']['kapital'].'</td><td>'.$_SESSION['lang']['total'].'</td></tr></thead><tbody>';
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td align=right>'.number_format($totRelN, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totRelK, 0).'</td>';
        $totDtl = $totRelN + $totRelK;
        $tab .= '<td align=right>'.number_format($totDtl, 0).'</td>';
        $tab .= '</tr></tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Sumsel&stat='.$stat."' title='kembali ke halaman sebelumnya'>back</a> </p>";
        echo $tab;

        break;
    case 'beliperptKal':
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtKaltim.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) not in ('8','9')\n                 order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRelN += $rTot['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtKaltim.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) in ('9') and left(kodebarang,1) !='8'\n                 order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRelK += $rTot['total'];
        }
        $data = [$totRelN, $totRelK];
        $graph = new PieGraph(700, 500);
        $graph->title->Set('Realisasi Pembelian PT.'.$kdPt.',Tahun '.$tahun.'');
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('white');
        $p1->SetLabelType(PIE_VALUE_PER);
        $lbl = ["Non Kapital\n%.1f%%", "Kapital\n%.1f%%"];
        $p1->SetLabels($lbl);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $targ = [$_SERVER['PHP_SELF'].'?periode='.$periode.'&kdPt='.$kdPt.'&jenis=nonkapital&stat='.$stat.'', $_SERVER['PHP_SELF'].'?periode='.$periode.'&kdPt='.$kdPt.'&jenis=kapital&stat='.$stat.''];
        $alts = ['Click to drill', 'Click to drill'];
        $p1->SetCSIMTargets($targ, $alts);
        $txt = new Text('Non Kapital :Rp. '.number_format($totRelN, 0)." \nKapital :Rp. ".number_format($totRelK, 0).'');
        $txt->SetPos(0.5, 0.2, 'center', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td colspan=3>Realisasi Pembelian PT.'.$kdPt.',Tahun '.$tahun.'</td></tr><tr>';
        $tab .= '<td>Non Kapital</td>';
        $tab .= '<td>'.$_SESSION['lang']['kapital'].'</td><td>'.$_SESSION['lang']['total'].'</td></tr></thead><tbody>';
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td align=right>'.number_format($totRelN, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totRelK, 0).'</td>';
        $totDtl = $totRelN + $totRelK;
        $tab .= '<td align=right>'.number_format($totDtl, 0).'</td>';
        $tab .= '</tr></tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Kaltim&stat='.$stat."' title='kembali ke halaman sebelumnya'>back</a> </p>";
        echo $tab;

        break;
    case 'prsnaTotGrop':
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtSumsel.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgt = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptSumsel.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgt = $res['total'];
        $totAllBgtSumsel = $totKapBgt + $totNonBgt;
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totAllRealSumsel += $rTot['total'];
        }
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgtKaltim.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgtKaltim = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ".$whrKaptKaltim.'';
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgtKaltim = $res['total'];
        $totAllBgtKaltim = $totKapBgtKaltim + $totNonBgtKaltim;
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from \n                 ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtKaltim.")\n                 and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)!='8'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totAllKaltim += $rTot['total'];
        }
        $totRealSma = $totAllRealSumsel + $totAllKaltim;
        $totBgtSma = $totAllBgtSumsel + $totAllBgtKaltim;
        $prsnSumsel = $totAllRealSumsel / $totRealSma * 100;
        $prsnKaltim = $totAllKaltim / $totRealSma * 100;
        $prsnSma = $totRealSma / $totRealSma * 100;
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<tr class=rowcontent><td rowspan=2>% dari Total Group</td>';
        $tab .= '<td>SUMSEL</td><td>KALTIM</td><td>TOTAL</td></tr>';
        $tab .= '<tr class=rowcontent><td align=right>'.number_format($prsnSumsel, 0).'</td>';
        $tab .= '<td align=right>'.number_format($prsnKaltim, 0).'</td>';
        $tab .= '<td align=right>'.number_format($prsnSma, 0).'</td></tr></table>';
        $data = [$prsnSumsel, $prsnKaltim, $prsnSma];
        $arrhead = ["SUMSEL\n".number_format($prsnSumsel, 0).'%', " KALTIM\n".number_format($prsnKaltim, 0).'%', strtoupper($_SESSION['lang']['total'])."\n".number_format($prsnSma, 0).'%'];
        $graph = new PieGraph(700, 500);
        $graph->title->Set('%  dari Anggaran Tahun '.$tahun.'');
        $graph->title->SetMargin(1);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->SetCenter(0.5, 0.45);
        $p1->value->SetFont(FF_FONT2, FS_BOLD, 12);
        $p1->value->SetColor('white');
        $p1->SetLabels($arrhead);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $targ = [$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Sumsel&stat=3', $_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Kaltim&stat=3'];
        $alts = ['Click to drill', 'Click to drill'];
        $p1->SetCSIMTargets($targ, $alts);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=global' title='kembali ke halaman sebelumnya'>back</a> </p>";
        echo $tab;

        break;
    case 'nonkapital':
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg,left(kodebarang,3) as klmpkBrg from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) not in ('8','9')\n                 group by left(kodebarang,3) \n                 order by sum((hargasatuan*kurs)*jumlahpesan) desc limit 0,10";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        $aer = 0;
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totHrg[$rTot['klmpkBrg']] = $rTot['total'];
            $lbl[] = $optKlmpk[$rTot['klmpkBrg']];
            $lbl2[$rTot['klmpkBrg']] = $rTot['klmpkBrg'];
            $data[] = $rTot['total'];
            $targ[] = $_SERVER['PHP_SELF'].'?klmpkBrg='.$rTot['klmpkBrg'].'&kdPt='.$kdPt.'&jenis=rinci2&periode='.$periode.'&stat='.$stat.'';
            $alts[] = 'Click to drill';
            ++$aer;
            if (1 === $aer) {
                $inklmp = "'".$rTot['klmpkBrg']."'";
            } else {
                $inklmp .= ",'".$rTot['klmpkBrg']."'";
            }
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg,left(kodebarang,3) as klmpkBrg from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) not in ('8','9') and left(kodebarang,3) not in (".$inklmp.')';
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        $rDet = mysql_fetch_assoc($qTot);
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr>';
        $tab .= '<td>'.$_SESSION['lang']['kelompokbarang'].'</td><td>'.$_SESSION['lang']['rp'].'</td></tr></thead>';
        foreach ($lbl2 as $dtklmprbrg) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td><a href='.$_SERVER['PHP_SELF'].'?klmpkBrg='.$dtklmprbrg.'&kdPt='.$kdPt.'&jenis=rinci2&periode='.$periode.'&stat='.$stat.'>'.$optKlmpk[$dtklmprbrg].' ['.$dtklmprbrg.']</a></td>';
            $tab .= '<td align=right>'.number_format($totHrg[$dtklmprbrg], 0).'</td></tr>';
            $totData += $totHrg[$dtklmprbrg];
        }
        $tab .= '<tr class=rowcontent><td><a href='.$_SERVER['PHP_SELF'].'?klmpkBrg='.$dtklmprbrg.'&kdPt='.$kdPt.'&jenis=rincio&periode='.$periode.'&indatais='.$inklmp.'&stat='.$stat.'> Others</a></td><td align=right>'.number_format($rDet['total']).'</td><tr>';
        $totData += $rDet['total'];
        $tab .= '<tr class=rowcontent><td></td><td align=right>'.number_format($totData, 0).'</td></tr></table>';
        $graph = new PieGraph(700, 500);
        $graph->title->Set('Realisasi Pembelian NON KAPITAL PT.'.$kdPt.',Tahun '.$tahun.'');
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(0.7);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $p1->SetCSIMTargets($targ, $alts);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        if ('SUMSEL' === $optPt[$kdPt]) {
            $jns = 'beliperpt';
        } else {
            $jns = 'beliperptKal';
        }

        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis='.$jns.'&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'rinci2':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodebarang from \n                     ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and kodeorg='".$kdPt."'\n                     and left(kodebarang,3)='".$klmpkBrg."' group by kodebarang";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $sDt = 'select sum(jumlahpesan) as jumlahpesan from '.$dbname.".log_po_vw where kodebarang='".$rTot['kodebarang']."'\n                          and kodeorg='".$kdPt."' and left(tanggal,7) between '".$tahun."-01' and '".$periode."'";
            $qDt = mysql_query($sDt) || exit(mysql_error($conns));
            $rDt = mysql_fetch_assoc($qDt);
            $tab .= '<tr class=rowcontent><td>'.$rTot['kodebarang'].'</td>';
            $tab .= '<td>'.$optNmbrg[$rTot['kodebarang']].'</td>';
            $tab .= '<td align=right>'.number_format($rDt['jumlahpesan'], 0).'</td>';
            $tab .= '<td align=right>'.number_format($rTot['total'], 0).'</td></tr>';
            $totalDt += $rTot['total'];
        }
        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['grandtotal'].'</td>';
        $tab .= '<td align=right>'.number_format($totalDt, 0).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=nonkapital&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'rincio':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';
        $strd = strlen($indatais);
        $dtisi = substr($indatais, 2, $strd - 2);
        $dtisi2 = substr($dtisi, -2, $strd - 4);
        $der = explode("\\',\\'", $dtisi);
        $pnjng = count($der);
        $aer = 0;
        foreach ($der as $lstere) {
            ++$aer;
            if (1 === $aer) {
                $inklmp = "'".$lstere."'";
            } else {
                if ($aer === $pnjng) {
                    $inklmp .= ",'".substr($lstere, 0, 3)."'";
                } else {
                    $inklmp .= ",'".$lstere."'";
                }
            }
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodebarang from \n                     ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and kodeorg='".$kdPt."'\n                     and left(kodebarang,1) not in ('8','9') and left(kodebarang,3) not in (".$inklmp.') group by kodebarang';
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $sDt = 'select sum(jumlahpesan) as jumlahpesan from '.$dbname.".log_po_vw where kodebarang='".$rTot['kodebarang']."'\n                          and kodeorg='".$kdPt."' and left(tanggal,7) between '".$tahun."-01' and '".$periode."'";
            $qDt = mysql_query($sDt) || exit(mysql_error($conns));
            $rDt = mysql_fetch_assoc($qDt);
            $tab .= '<tr class=rowcontent><td>'.$rTot['kodebarang'].'</td>';
            $tab .= '<td>'.$optNmbrg[$rTot['kodebarang']].'</td>';
            $tab .= '<td align=right>'.number_format($rDt['jumlahpesan'], 0).'</td>';
            $tab .= '<td align=right>'.number_format($rTot['total'], 0).'</td></tr>';
            $totalDt += $rTot['total'];
        }
        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['grandtotal'].'</td>';
        $tab .= '<td align=right>'.number_format($totalDt, 0).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=nonkapital&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'kapital':
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg,left(kodebarang,3) as klmpkBrg from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) in ('9')\n                 group by left(kodebarang,3) \n                 order by sum((hargasatuan*kurs)*jumlahpesan) desc limit 0,10";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totHrg[$rTot['klmpkBrg']] = $rTot['total'];
            $lbl[] = $optKlmpk[$rTot['klmpkBrg']];
            $lbl2[$rTot['klmpkBrg']] = $rTot['klmpkBrg'];
            $data[] = $rTot['total'];
            $targ[] = $_SERVER['PHP_SELF'].'?klmpkBrg='.$rTot['klmpkBrg'].'&kdPt='.$kdPt.'&jenis=rinci&periode='.$periode.'';
            $alts[] = 'Click to drill';
            ++$aer;
            if (1 === $aer) {
                $inklmp = "'".$rTot['klmpkBrg']."'";
            } else {
                $inklmp .= ",'".$rTot['klmpkBrg']."'";
            }
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg,left(kodebarang,3) as klmpkBrg from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'\n                 and kodeorg='".$kdPt."' and left(kodebarang,1) in ('9') and left(kodebarang,3) not in (".$inklmp.')';
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        $rDet = mysql_fetch_assoc($qTot);
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr>';
        $tab .= '<td>'.$_SESSION['lang']['kelompokbarang'].'</td><td>'.$_SESSION['lang']['rp'].'</td></tr></thead>';
        foreach ($lbl2 as $dtklmprbrg) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td><a href='.$_SERVER['PHP_SELF'].'?klmpkBrg='.$dtklmprbrg.'&kdPt='.$kdPt.'&jenis=rinci&periode='.$periode.'&stat='.$stat.'>'.$optKlmpk[$dtklmprbrg].' ['.$dtklmprbrg.']</a></td>';
            $tab .= '<td align=right>'.number_format($totHrg[$dtklmprbrg], 0).'</td></tr>';
            $totData += $totHrg[$dtklmprbrg];
        }
        $tab .= '<tr class=rowcontent><td><a href='.$_SERVER['PHP_SELF'].'?klmpkBrg='.$dtklmprbrg.'&kdPt='.$kdPt.'&jenis=rincio2&periode='.$periode.'&indatais='.$inklmp.'&stat='.$stat.'> Others</a></td><td align=right>'.number_format($rDet['total']).'</td><tr>';
        $totData += $rDet['total'];
        $tab .= '<tr class=rowcontent><td></td><td align=right>'.number_format($totData, 0).'</td></tr></table>';
        $graph = new PieGraph(700, 500);
        $graph->title->Set('Realisasi Pembelian KAPITAL PT.'.$kdPt.',Tahun '.$tahun.'');
        $graph->title->SetMargin(2);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($lbl);
        $p1->SetLabelPos(0.5);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $p1->SetCSIMTargets($targ, $alts);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        if ('SUMSEL' === $optPt[$kdPt]) {
            $jns = 'beliperpt';
        } else {
            $jns = 'beliperptKal';
        }

        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis='.$jns.'&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'rinci':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodebarang from \n                     ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and kodeorg='".$kdPt."'\n                     and left(kodebarang,3)='".$klmpkBrg."' group by kodebarang";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $sDt = 'select sum(jumlahpesan) as jumlahpesan from '.$dbname.".log_po_vw where kodebarang='".$rTot['kodebarang']."'\n                          and kodeorg='".$kdPt."' and left(tanggal,7) between '".$tahun."-01' and '".$periode."'";
            $qDt = mysql_query($sDt) || exit(mysql_error($conns));
            $rDt = mysql_fetch_assoc($qDt);
            $tab .= '<tr class=rowcontent><td>'.$rTot['kodebarang'].'</td>';
            $tab .= '<td>'.$optNmbrg[$rTot['kodebarang']].'</td>';
            $tab .= '<td align=right>'.number_format($rDt['jumlahpesan'], 0).'</td>';
            $tab .= '<td align=right>'.number_format($rTot['total'], 0).'</td></tr>';
            $totalDt += $rTot['total'];
        }
        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['grandtotal'].'</td>';
        $tab .= '<td align=right>'.number_format($totalDt, 0).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=kapital&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'rincio2':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';
        $strd = strlen($indatais);
        $dtisi = substr($indatais, 2, $strd - 2);
        $dtisi2 = substr($dtisi, -2, $strd - 4);
        $der = explode("\\',\\'", $dtisi);
        $pnjng = count($der);
        $aer = 0;
        foreach ($der as $lstere) {
            ++$aer;
            if (1 === $aer) {
                $inklmp = "'".$lstere."'";
            } else {
                if ($aer === $pnjng) {
                    $inklmp .= ",'".substr($lstere, 0, 3)."'";
                } else {
                    $inklmp .= ",'".$lstere."'";
                }
            }
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodebarang from \n                     ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and kodeorg='".$kdPt."'\n                     and left(kodebarang,1) in ('9') and left(kodebarang,3) not in (".$inklmp.') group by kodebarang';
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $sDt = 'select sum(jumlahpesan) as jumlahpesan from '.$dbname.".log_po_vw where kodebarang='".$rTot['kodebarang']."'\n                          and kodeorg='".$kdPt."' and left(tanggal,7) between '".$tahun."-01' and '".$periode."'";
            $qDt = mysql_query($sDt) || exit(mysql_error($conns));
            $rDt = mysql_fetch_assoc($qDt);
            $tab .= '<tr class=rowcontent><td>'.$rTot['kodebarang'].'</td>';
            $tab .= '<td>'.$optNmbrg[$rTot['kodebarang']].'</td>';
            $tab .= '<td align=right>'.number_format($rDt['jumlahpesan'], 0).'</td>';
            $tab .= '<td align=right>'.number_format($rTot['total'], 0).'</td></tr>';
            $totalDt += $rTot['total'];
        }
        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['grandtotal'].'</td>';
        $tab .= '<td align=right>'.number_format($totalDt, 0).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=kapital&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
}

?>