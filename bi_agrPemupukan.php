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
        $optnmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
        $optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
        if ('' !== $kodeorg) {
            $whr = " and a.kodeorg like '".$kodeorg."%'";
            $whrb = " and kodeorg like '".$kodeorg."%'";
        }

        $sData = 'select distinct sum(kwantitas) as jmlh,sum(kwantitasha) as luas,b.tahuntanam,a.kodebarang from  '.$dbname.".kebun_pakai_material_vw a\n                left join ".$dbname.".setup_blok b on a.kodeorg=b.kodeorg where left(tanggal,7)<='".$periode."' and left(tanggal,4)='".$tahun."'\n                and kodekegiatan='621030201' ".$whr.' group by a.kodebarang,b.tahuntanam order by b.tahuntanam asc';
        $qData = mysql_query($sData) || exit(mysql_error($conns));
        while ($rData = mysql_fetch_assoc($qData)) {
            $dtThnTnm[$rData['tahuntanam']] = $rData['tahuntanam'];
            $dtJmlh[$rData['tahuntanam'].$rData['kodebarang']] = $rData['jmlh'];
            $dtLuas[$rData['tahuntanam']] = $rData['luas'];
            $dtKdbrg[$rData['kodebarang']] = $rData['kodebarang'];
        }
        $addstr3 = '(';
        for ($W = 1; $W <= (int) $bulan; ++$W) {
            if ($W < 10) {
                $jack = 'fis0'.$W;
            } else {
                $jack = 'fis'.$W;
            }

            if ($W < (int) $bulan) {
                $addstr3 .= $jack.'+';
            } else {
                $addstr3 .= $jack;
            }
        }
        $addstr3 .= ')';
        $sBgt = 'select distinct sum(jumlah) as jmlh,thntnm as tahuntanam,kodebarang from '.$dbname.".bgt_budget_kebun_perblok_vw\n               where tahunbudget='".$tahun."' and kegiatan='621030201' and left(kodebarang,3)='311'\n               ".$whrb.'   group by kodebarang,thntnm order by thntnm';
        $qBgt = mysql_query($sBgt) || exit(mysql_error($conns));
        while ($rBgt = mysql_fetch_assoc($qBgt)) {
            $dtJmlhBgt[$rBgt['tahuntanam'].$rBgt['kodebarang']] = $rBgt['jmlh'];
        }
        $cekDt = count($dtKdbrg);
        if (0 === $cekDt) {
            exit('Error: Realisasi Masih Kosong di Tahun :'.$tahun);
        }

        $tab .= "<link rel=stylesheet tyle=text href='style/generic.css'>\n                              <script language=javascript src='js/generic.js'></script>";
        $tab .= '<br />PEMUPUKAN TM Per :'.$periode."\n                  <table cellpadding=1 cellspacing=1 border=0 class=sortable>\n                <thead>\n                <tr class=rowheader>\n                <td rowspan=2 align=center>".$_SESSION['lang']['tahuntanam']."</td>\n                <td rowspan=2 align=center>".$_SESSION['lang']['luas'].'</td>';
        array_multisort($dtKdbrg);
        foreach ($dtKdbrg as $dtKdbarang) {
            $tab .= '<td colspan=4 align=center>'.$optnmBrg[$dtKdbarang].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '<tr>';
        foreach ($dtKdbrg as $dtKdbarang) {
            $tab .= '<td align=center>Program setahun</td>';
            $tab .= '<td align=center>Realisasi s/d Bi</td>';
            $tab .= '<td align=center>%</td>';
            $tab .= '<td align=center>Sisa</td>';
        }
        $tab .= '</tr></thead><tbody>';
        foreach ($dtThnTnm as $lstThnTnm) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$lstThnTnm.'</td>';
            $tab .= '<td align=right>'.number_format($dtLuas[$lstThnTnm], 2).'</td>';
            foreach ($dtKdbrg as $dtKdbarang) {
                $tab .= '<td align=right>'.number_format($dtJmlhBgt[$lstThnTnm.$dtKdbarang], 2).'</td>';
                $tab .= '<td align=right>'.number_format($dtJmlh[$lstThnTnm.$dtKdbarang], 2).'</td>';
                $prsen[$lstThnTnm.$dtKdbarang] = $dtJmlh[$lstThnTnm.$dtKdbarang] / $dtJmlhBgt[$lstThnTnm.$dtKdbarang] * 100;
                $sisa[$lstThnTnm.$dtKdbarang] = $dtJmlhBgt[$lstThnTnm.$dtKdbarang] - $dtJmlh[$lstThnTnm.$dtKdbarang];
                $tab .= '<td align=right>'.number_format($prsen[$lstThnTnm.$dtKdbarang], 2).'</td>';
                $tab .= '<td align=right>'.number_format($sisa[$lstThnTnm.$dtKdbarang], 2).'</td>';
                $totaBgt[$dtKdbarang] += $dtJmlhBgt[$lstThnTnm.$dtKdbarang];
                $totaReal[$dtKdbarang] += $dtJmlh[$lstThnTnm.$dtKdbarang];
            }
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['total'].'</td>';
        foreach ($dtKdbrg as $dtKdbarang) {
            $ader = 1;
            $tab .= '<td align=right>'.number_format($totaBgt[$dtKdbarang], 2).'</td>';
            $tab .= '<td align=right>'.number_format($totaReal[$dtKdbarang], 2).'</td>';
            $prsenTo[$dtKdbarang] = $totaReal[$dtKdbarang] / $totaBgt[$dtKdbarang] * 100;
            $sisaTot[$dtKdbarang] = $totaBgt[$dtKdbarang] - $totaReal[$dtKdbarang];
            $tab .= '<td align=right>'.number_format($prsenTo[$dtKdbarang], 2).'</td>';
            $tab .= '<td align=right>'.number_format($sisaTot[$dtKdbarang], 2).'</td>';
            $dtNmbarang[] = $optnmBrg[$dtKdbarang];
            $bgtBgt[] = $totaBgt[$dtKdbarang];
            $bgtReal[] = $totaReal[$dtKdbarang];
        }
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $drrowd = count($dtNmbarang);
        foreach ($dtNmbarang as $lstbarang) {
            ++$arer;
            if (1 === $arer) {
                $dtLstBrg[] = '';
                $dtLstBrg[] = $lstbarang;
            } else {
                $dtLstBrg[] = $lstbarang;
            }
        }
        $arer = 0;
        foreach ($bgtBgt as $lstbarang2) {
            ++$arer;
            if (1 === $arer) {
                $dtLstDtBgt[] = 'Budget';
                $dtLstDtBgt[] = $lstbarang2;
            } else {
                $dtLstDtBgt[] = $lstbarang2;
            }
        }
        $arer = 0;
        foreach ($bgtReal as $lstbarang3) {
            ++$arer;
            if (1 === $arer) {
                $dtLstDtReal[] = 'Realisasi';
                $dtLstDtReal[] = $lstbarang3;
            } else {
                $dtLstDtReal[] = $lstbarang3;
            }
        }
        $datay[] = $dtLstBrg;
        $datay[] = $dtLstDtBgt;
        $datay[] = $dtLstDtReal;
        $nbrbar = $drrowd;
        $cellwidth = 70;
        $tableypos = 290;
        $tablexpos = 60;
        $tablewidth = ($nbrbar + 1) * $cellwidth;
        $rightmargin = 30;
        $topmargin = 30;
        $width = $tablexpos + $tablewidth + $rightmargin;
        $table = new GTextTable();
        $table->Set($datay);
        $table->SetPos($tablexpos, $tableypos + 85);
        $table->SetFont(FF_ARIAL, FS_NORMAL, 8);
        $table->SetAlign('right');
        $table->SetMinColWidth($cellwidth);
        $table->SetNumberFormat('%0.1f');
        $table->SetRowPadding(0, 5);
        $table->SetRowFillColor(0, 'teal@0.7');
        $table->SetRowFont(0, FF_ARIAL, FS_BOLD, 8);
        $table->SetRowAlign(0, 'center');
        $datay1 = $bgtBgt;
        $datay2 = $bgtReal;
        $graph = new Graph(750, 455, 'auto');
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $height = 550;
        $graph->img->SetMargin($tablexpos, $rightmargin, $topmargin, $height - $tableypos);
        $graph->xaxis->SetTickLabels($dtNmbarang);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $graph->title->Set('PROGRESS PEMUPUKAN TANAMAN MENGHASILKAN (KG)');
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 6);
        $bplot1 = new BarPlot($datay1);
        $bplot2 = new BarPlot($datay2);
        $bplot1->SetFillColor('orange');
        $bplot2->SetFillColor('brown');
        $bplot1->SetLegend('Budget');
        $bplot2->SetLegend('Realisasi');
        $graph->legend->Pos(0.02, 0.03);
        $bplot1->SetShadow();
        $bplot2->SetShadow();
        $bplot1->SetShadow();
        $bplot2->SetShadow();
        $gbarplot = new GroupBarPlot([$bplot1, $bplot2]);
        $gbarplot->SetWidth(0.3);
        $graph->Add($gbarplot);
        $graph->Add($table);
        $graph->StrokeCSIM();
        echo $tab;

        break;
}

?>