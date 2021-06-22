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
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (('excel' === $proses || 'preview' === $proses) && '' === $periode) {
    exit('Error:Field Tidak Boleh Kosong');
}

$optKlmpk = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optNmbrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
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
$whrKaptKaltim = ' and substr(kodeunit,1,4) in ('.$sumselReg.')';
$dtPtKaltim = '';
$ert = 0;
$sPt = 'select distinct induk from '.$dbname.'.organisasi where kodeorganisasi in ('.$kaltimReg.')';
$qPt = mysql_query($sPt) || exit(mysql_error($conns));
while ($rPt = mysql_fetch_assoc($qPt)) {
    ++$ert;
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
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ";
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgt = $res['total'];
        $aresta = 'SELECT sum('.$addkap.') as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."'";
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgt = $res['total'];
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                  ".$dbname.".log_po_vw where  substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' \n                  and left(kodebarang,1) not in ('8','9')";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totNKap += $rTot['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                  ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' \n                  and left(kodebarang,1) in ('9')";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totKap += $rTot['total'];
        }
        $totReal = $totKap + $totNKap;
        $totBudget = $totKapBgt + $totNonBgt;
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td align=center>'.$_SESSION['lang']['kelompok'].'</td>';
        $tab .= '<td align=center>KAPITAL</td><td align=center>NON KAPITAL</td><td align=center>'.$_SESSION['lang']['total'].'</td></tr><thead><tbody>';
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['realisasi'].'</td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Kapital&stat=1'  class=linkBi>".number_format($totKap, 0).'</a></td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Realisasi&stat=1' class=linkBi>".number_format($totNKap, 0).'</a></td>';
        $tab .= '<td align=right>'.number_format($totReal, 0).'</td></tr>';
        $tab .= '<tr class=rowcontent >';
        $tab .= '<td>'.$_SESSION['lang']['budget'].'</td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Kapital&stat=1' class=linkBi>".number_format($totKapBgt, 0).'</a></td>';
        $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=Realisasi&stat=1' class=linkBi>".number_format($totNonBgt, 0).'</a></td>';
        $tab .= '<td align=right>'.number_format($totBudget, 0).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=prsnaAnggaran' title='tampilan dalam persen dari anggaran'  class=linkBi>PORSENTASE REALISASI</a>";
        $totKap = $totKap / 1000000;
        $totNKap = $totNKap / 1000000;
        $totKapBgt = $totKapBgt / 1000000;
        $totNonBgt = $totNonBgt / 1000000;
        $totReal = $totKap + $totNKap;
        $totBudget = $totKapBgt + $totNonBgt;
        $arrhead = [$_SESSION['lang']['regional'], 'KAPITAL', 'NON KAPITAL', strtoupper($_SESSION['lang']['total'])];
        $arrReal = [$_SESSION['lang']['realisasi'], $totKap, $totNKap, $totReal];
        $arrBgt = [$_SESSION['lang']['budget'], $totKapBgt, $totNonBgt, $totBudget];
        $arrhead2 = ['KAPITAL', 'NON KAPITAL', $_SESSION['lang']['total']];
        $arrReal2 = [$totKap, $totNKap, $totReal];
        $arrBgt2 = [$totKapBgt, $totNonBgt, $totBudget];
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
        $height = 420;
        $datay1 = $arrReal2;
        $datay2 = $arrBgt2;
        $graph = new Graph($width, $height, 'auto');
        $graph->SetScale('textlin');
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($arrhead2);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('NILAI REALISASI PEMBELIAN vs ANGGARAN TAHUN '.$tahun);
        $graph->title->SetMargin(1);
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $txt = new Text('RP(JUTA)');
        $txt->SetPos(0.01, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
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
    case 'Kapital':
        $aresta = 'SELECT sum('.$addkap.') as total,left(kodeunit,4) as kdkbn FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' group by left(kodeunit,4)";
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $sInduk = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$res['kdkbn']."'";
            $qInduk = mysql_query($sInduk) || exit(mysql_error($conns));
            $rInduk = mysql_fetch_assoc($qInduk);
            $totBgtkap[$rInduk['induk']] += $res['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1) in ('9')\n                 group by kodeorg order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRelkap[$rTot['kodeorg']] += $rTot['total'];
            $dInduk[$rTot['kodeorg']] = $rTot['kodeorg'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr><td align=center></td>';
        foreach ($dInduk as $lstPt) {
            $tab .= '<td align=center>'.$lstPt.'</td>';
        }
        $tab .= '<td align=center colspan=2>'.$_SESSION['lang']['total'].'</td></tr>';
        $tab .= '</thead><tbody>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['realisasi'].'</td>';
        foreach ($dInduk as $lstPt) {
            $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt&kdPt='.$lstPt.'&stat='.$stat."' title='Pembelian PT. ".$lstPt."' class=linkBi>\n                      ".number_format($totRelkap[$lstPt], 0).'</a></td>';
            $totDtKap += $totRelkap[$lstPt];
            $totRelkap[$lstPt] = $totRelkap[$lstPt] / 1000000;
            $dataReal[] = $totRelkap[$lstPt];
            $lstPtDt[] = $lstPt;
        }
        $tab .= '<td align=right>'.number_format($totDtKap, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['anggaran'].'</td>';
        foreach ($dInduk as $lstPt) {
            $tab .= '<td align=right>'.number_format($totBgtkap[$lstPt], 0).'</td>';
            $totBgtDt += $totBgtkap[$lstPt];
            $totBgtkap[$lstPt] = $totBgtkap[$lstPt] / 1000000;
            $dIndukBgtdt[] = $totBgtkap[$lstPt];
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
        $cellwidth = 95;
        $tableypos = 250;
        $tablexpos = 70;
        $tablewidth = $nbrbar * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $height = 295;
        $datay1 = $dataReal;
        $datay2 = $dIndukBgtdt;
        $graph = new Graph($width, $height, 'auto');
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($lstPtDt);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('NILAI REALISASI PEMBELIAN KAPITAL vs ANGGARAN TAHUN '.$tahun);
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
    case 'Realisasi':
        $aresta = 'SELECT sum('.$addstr.') as total,left(kodeorg,4) as kdkbn FROM '.$dbname.".bgt_budget_detail\n            WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' group by left(kodeorg,4)";
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $sInduk = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$res['kdkbn']."'";
            $qInduk = mysql_query($sInduk) || exit(mysql_error($conns));
            $rInduk = mysql_fetch_assoc($qInduk);
            $totBgt[$rInduk['induk']] += $res['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodeorg from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)not in ('8','9')\n                 group by kodeorg order by kodeorg asc";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totRel[$rTot['kodeorg']] += $rTot['total'];
            $dInduk[$rTot['kodeorg']] = $rTot['kodeorg'];
            $rTot['total'] = $rTot['total'] / 1000000;
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
            $tab .= "<td align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt2&kdPt='.$lstPt.'&stat='.$stat."' title='Pembelian PT. ".$lstPt."' class=linkBi>".number_format($totRel[$lstPt], 0).'</a></td>';
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
        $cellwidth = 95;
        $tableypos = 250;
        $tablexpos = 70;
        $tablewidth = $nbrbar * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $height = 295;
        $datay1 = $dataReal;
        $datay2 = $dIndukBgtdt;
        $graph = new Graph($width, $height, 'auto');
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($lstPtDt);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('NILAI REALISASI PEMBELIAN NON KAPITAL vs ANGGARAN TAHUN '.$tahun);
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $bplot1 = new BarPlot($datay1);
        $bplot2 = new BarPlot($datay2);
        $bplot1->SetFillColor('darkgreen');
        $bplot2->SetFillColor('yellow');
        $bplot1->SetLegend('Realisasi');
        $bplot2->SetLegend('Budget');
        $graph->legend->Pos(0.06, 0.06);
        $gbarplot = new GroupBarPlot([$bplot1, $bplot2]);
        $gbarplot->SetWidth(0.3);
        $graph->Add($gbarplot);
        $graph->StrokeCSIM();
        echo $tab;

        break;
    case 'prsnaAnggaran':
        $aresta = 'SELECT sum('.$addstr.') as total FROM '.$dbname.".bgt_budget_detail\n                 WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."'";
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totNonBgt = $res['total'];
        $aresta = 'SELECT sum(harga) as total FROM '.$dbname.".bgt_kapital_vw\n            WHERE tahunbudget = '".$tahun."' ";
        $query = mysql_query($aresta) || exit(mysql_error($conns));
        $res = mysql_fetch_assoc($query);
        $totKapBgt = $res['total'];
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                 ".$dbname.".log_po_vw where  substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' \n                  and left(kodebarang,1) not in ('8','9')";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totNKap += $rTot['total'];
        }
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total from \n                 ".$dbname.".log_po_vw where substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and left(kodebarang,1)='9'";
        $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
        while ($rTot = mysql_fetch_assoc($qTot)) {
            $totKap += $rTot['total'];
        }
        $totRealSma = $totNKap + $totKap;
        $totBgtSma = $totKapBgt + $totNonBgt;
        $prsnKap = $totKap / $totKapBgt * 100;
        $prsnNKap = $totNKap / $totNonBgt * 100;
        $prsnSma = $totRealSma / $totBgtSma * 100;
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td></td>';
        $tab .= '<td>'.$_SESSION['lang']['kapital'].'</td><td>'.$_SESSION['lang']['nonkapital'].'</td><td>'.$_SESSION['lang']['total'].'</td></tr></thead>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['realisasi'].'</td><td align=right>'.number_format($totKap, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totNKap, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totRealSma, 0).'</td></tr>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['anggaran'].'</td><td align=right>'.number_format($totKapBgt, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totNonBgt, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBgtSma, 0).'</td></tr>';
        $tab .= '<tr class=rowcontent><td>%</td><td align=right>'.number_format($prsnKap, 0).'</td>';
        $tab .= '<td align=right>'.number_format($prsnNKap, 0).'</td>';
        $tab .= '<td align=right>'.number_format($prsnSma, 0).'</td></tr></table>';
        $data = [$prsnKap, $prsnNKap, $prsnSma];
        $arrhead = ['KAPITAL '.number_format($prsnKap, 0).'%', ' NON KAPITAL '.number_format($prsnNKap, 0).'%', strtoupper($_SESSION['lang']['total'])."\n".number_format($prsnSma, 0).'%'];
        $graph = new PieGraph(700, 500);
        $graph->title->Set('PORSENTASE REALISASI PEMBELIAN TERHADAP BUDGET '.$tahun.'');
        $graph->title->SetMargin(1);
        $p1 = new PiePlot3d($data);
        $p1->SetSize(0.5);
        $p1->SetCenter(0.5);
        $p1->SetAngle(50);
        $p1->value->SetColor('black');
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($arrhead);
        $p1->SetLabelPos(0.6);
        $p1->SetShadow();
        $p1->ExplodeAll(10);
        $targ = [$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Kapital&stat=2', $_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Realisasi&stat=2'];
        $alts = ['Click to drill', 'Click to drill'];
        $p1->SetCSIMTargets($targ, $alts);
        $graph->Add($p1);
        $graph->StrokeCSIM();
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode."&jenis=global' title='kembali ke halaman sebelumnya'>back</a> </p>";
        echo $tab;

        break;
    case 'beliperpt':
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
            $tab .= '<td><a href='.$_SERVER['PHP_SELF'].'?klmpkBrg='.$dtklmprbrg.'&kdPt='.$kdPt.'&jenis=rinci&periode='.$periode.'>'.$optKlmpk[$dtklmprbrg].' ['.$dtklmprbrg.']</a></td>';
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
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Kapital&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'beliperpt2':
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
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=Realisasi&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'rinci':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodebarang from \n                     ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                     and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and kodeorg='".$kdPt."'\n                     and left(kodebarang,3)='".$klmpkBrg."' group by kodebarang";
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
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
    case 'rinci2':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';
        $sTot = "select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,kodebarang from \n                     ".$dbname.'.log_po_vw where kodeorg in ('.$dtPtSumsel.")\n                     and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' and kodeorg='".$kdPt."'\n                     and left(kodebarang,3)='".$klmpkBrg."' group by kodebarang";
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
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt2&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
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
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt2&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
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
        $tab .= "<p align=right><a href='".$_SERVER['PHP_SELF'].'?periode='.$periode.'&jenis=beliperpt&kdPt='.$kdPt.'&stat='.$stat."' title='kembali ke halaman awal'>back</a> </p>";
        echo $tab;

        break;
}

?>