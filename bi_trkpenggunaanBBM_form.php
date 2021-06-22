<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
$waktu = $param['tahun'];
$kodeorg = $param['pks'];
$str = 'select jenisvhc,namajenisvhc from '.$dbname.'.vhc_5jenisvhc';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $nama[$bar->jenisvhc] = $bar->namajenisvhc;
}
switch ($param['jenis']) {
    case 'global':
        $str = 'select sum(jlhbbm) as jlh,kodevhc,left(tanggal,7) as periode from '.$dbname.".vhc_runht where\r\n               tanggal like '".$waktu."%'\r\n              group by kodevhc,left(tanggal,7)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $real[$bar->periode][$bar->kodevhc] = $bar->jlh;
        }
        $str = 'select sum(a.jumlah) as km, b.kodevhc,left(b.tanggal,7) as periode from '.$dbname.".vhc_rundt a\r\n                    left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi\r\n                    and tanggal like '".$waktu."%' group by kodevhc,left(b.tanggal,7)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $realHM[$bar->periode][$bar->kodevhc] = $bar->km;
        }
        $str = "SELECT a.kodevhc,\r\n           sum(fis01) as fis01,\r\n           sum(fis02) as fis02,\r\n           sum(fis03) as fis03,\r\n           sum(fis04) as fis04,\r\n           sum(fis05) as fis05,\r\n           sum(fis06) as fis06,\r\n           sum(fis07) as fis07,\r\n           sum(fis08) as fis08,\r\n           sum(fis09) as fis09,\r\n           sum(fis10) as fis10,\r\n           sum(fis11) as fis11,\r\n           sum(fis12) as fis12\r\n            FROM ".$dbname.".bgt_budget_detail a\r\n           where a.kodevhc is not null and tipebudget='TRK' and tahunbudget='".$waktu."'\r\n           and kodebarang like '351%'    \r\n           group by kodevhc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $bgtfis[$waktu.'-01'][$bar->kodevhc] = $bar->fis01;
            $bgtfis[$waktu.'-02'][$bar->kodevhc] = $bar->fis02;
            $bgtfis[$waktu.'-03'][$bar->kodevhc] = $bar->fis03;
            $bgtfis[$waktu.'-04'][$bar->kodevhc] = $bar->fis04;
            $bgtfis[$waktu.'-05'][$bar->kodevhc] = $bar->fis05;
            $bgtfis[$waktu.'-06'][$bar->kodevhc] = $bar->fis06;
            $bgtfis[$waktu.'-07'][$bar->kodevhc] = $bar->fis07;
            $bgtfis[$waktu.'-08'][$bar->kodevhc] = $bar->fis08;
            $bgtfis[$waktu.'-09'][$bar->kodevhc] = $bar->fis09;
            $bgtfis[$waktu.'-10'][$bar->kodevhc] = $bar->fis10;
            $bgtfis[$waktu.'-11'][$bar->kodevhc] = $bar->fis11;
            $bgtfis[$waktu.'-12'][$bar->kodevhc] = $bar->fis12;
        }
        $str = "SELECT a.kodevhc,\r\n           sum(jam01) as jam01,\r\n           sum(jam02) as jam02,\r\n           sum(jam03) as jam03,\r\n           sum(jam04) as jam04,\r\n           sum(jam05) as jam05,\r\n           sum(jam06) as jam06,\r\n           sum(jam07) as jam07,\r\n           sum(jam08) as jam08,\r\n           sum(jam09) as jam09,\r\n           sum(jam10) as jam10,\r\n           sum(jam11) as jam11,\r\n           sum(jam12) as jam12\r\n            FROM ".$dbname.".bgt_vhc_jam a  where tahunbudget='".$waktu."'\r\n           group by a.kodevhc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $bgtjam[$waktu.'-01'][$bar->kodevhc] = $bar->jam01;
            $bgtjam[$waktu.'-02'][$bar->kodevhc] = $bar->jam02;
            $bgtjam[$waktu.'-03'][$bar->kodevhc] = $bar->jam03;
            $bgtjam[$waktu.'-04'][$bar->kodevhc] = $bar->jam04;
            $bgtjam[$waktu.'-05'][$bar->kodevhc] = $bar->jam05;
            $bgtjam[$waktu.'-06'][$bar->kodevhc] = $bar->jam06;
            $bgtjam[$waktu.'-07'][$bar->kodevhc] = $bar->jam07;
            $bgtjam[$waktu.'-08'][$bar->kodevhc] = $bar->jam08;
            $bgtjam[$waktu.'-09'][$bar->kodevhc] = $bar->jam09;
            $bgtjam[$waktu.'-10'][$bar->kodevhc] = $bar->jam10;
            $bgtjam[$waktu.'-11'][$bar->kodevhc] = $bar->jam11;
            $bgtjam[$waktu.'-12'][$bar->kodevhc] = $bar->jam12;
        }
        if ('' === $kodeorg) {
            $str = 'select kodevhc,jenisvhc from '.$dbname.'.vhc_5master';
        } else {
            $str = 'select kodevhc,jenisvhc from '.$dbname.".vhc_5master where kodetraksi like '".$kodeorg."%'";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $kodevhc[] = $bar->kodevhc;
            $jenisvhc[$bar->kodevhc] = $bar->jenisvhc;
        }
        foreach ($jenisvhc as $kd => $val) {
            if ($val === $jenisvhc[$kd]) {
                for ($kk = 1; $kk <= 12; ++$kk) {
                    if ($kk < 10) {
                        $zz = $waktu.'-0'.$kk;
                    } else {
                        $zz = $waktu.'-'.$kk;
                    }

                    $cap[$zz] = $zz;
                    $RealJ[$val][$zz] += $real[$zz][$kd];
                    $RealH[$val][$zz] += $realHM[$zz][$kd];
                    $BgtJ[$val][$zz] += $bgtfis[$zz][$kd];
                    $BgtlH[$val][$zz] += $bgtjam[$zz][$kd];
                }
            }
        }
        foreach ($RealJ as $a => $dg) {
            $jn[] = $nama[$a];
            foreach ($dg as $d => $u) {
                $RealJ1[$a] += $RealJ[$a][$d];
                $RealH1[$a] += $RealH[$a][$d];
                $BgtJ1[$a] += $BgtJ[$a][$d];
                $BgtlH1[$a] += $BgtlH[$a][$d];
            }
            $targ[] = '?tahun='.$waktu.'&pks='.$kodeorg.'&jenis=detail&tipe='.$a;
            $alts[] = ' Click to Drill';
        }
        require_once 'jpgraph/jpgraph_bar.php';
        require_once 'jpgraph/jpgraph_line.php';
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(50, 50, 50, 80);
        $graph->SetScale('textlin');
        $graph->SetShadow();
        $graph->xaxis->SetTickLabels($jn);
        $graph->xaxis->SetLabelAngle(90);
        $graph->title->Set('PENGGUNAAN BBM '.$param['pks'].' PERIODE: '.$waktu);
        $graph->yaxis->scale->SetGrace(30);
        $txt = new Text('Ltr.');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $txt->SetShadow();
        $graph->AddText($txt);
        $x = 0;
        foreach ($RealJ1 as $key => $val) {
            $leg[] = $key;
            $arrBBM[] = $val;
            $arrBGT[] = $BgtJ1[$key];
            $arrHM[] = $RealH1[$key];
            $arrBGTHM[] = $BgtlH1[$key];
        }
        $plot1 = new BarPlot($arrBBM);
        $plot1->SetCSIMTargets($targ, $alts);
        $plot2 = new BarPlot($arrBGT);
        $plot2->SetCSIMTargets($targ, $alts);
        $plot1->SetLegend('Aktual BBM');
        $plot2->SetLegend('Budget BBM');
        $graph->legend->SetPos(0.7, 0.15, 'center', 'bottom');
        $gbar = new GroupbarPlot([$plot1, $plot2]);
        $graph->Add($gbar);
        $graph->StrokeCSIM();

        break;
    case 'detail':
        echo "<link rel=stylesheet tyle=text href='style/generic.css'>\r\n          <script language=javascript src='js/generic.js'></script>";
        $str = 'select sum(jlhbbm) as jlh,kodevhc,left(tanggal,7) as periode from '.$dbname.".vhc_runht where\r\n       tanggal like '".$waktu."%'\r\n      group by kodevhc,left(tanggal,7)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $real[$bar->periode][$bar->kodevhc] = $bar->jlh;
        }
        $str = 'select sum(a.jumlah) as km, b.kodevhc,left(b.tanggal,7) as periode from '.$dbname.".vhc_rundt a\r\n            left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi\r\n            and tanggal like '".$waktu."%' group by kodevhc,left(b.tanggal,7)";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $realHM[$bar->periode][$bar->kodevhc] = $bar->km;
        }
        $str = "SELECT a.kodevhc,\r\n   sum(fis01) as fis01,\r\n   sum(fis02) as fis02,\r\n   sum(fis03) as fis03,\r\n   sum(fis04) as fis04,\r\n   sum(fis05) as fis05,\r\n   sum(fis06) as fis06,\r\n   sum(fis07) as fis07,\r\n   sum(fis08) as fis08,\r\n   sum(fis09) as fis09,\r\n   sum(fis10) as fis10,\r\n   sum(fis11) as fis11,\r\n   sum(fis12) as fis12\r\n    FROM ".$dbname.".bgt_budget_detail a\r\n   where a.kodevhc is not null and tipebudget='TRK' and tahunbudget='".$waktu."'\r\n   and kodebarang like '351%'    \r\n   group by kodevhc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $bgtfis[$waktu.'-01'][$bar->kodevhc] = $bar->fis01;
            $bgtfis[$waktu.'-02'][$bar->kodevhc] = $bar->fis02;
            $bgtfis[$waktu.'-03'][$bar->kodevhc] = $bar->fis03;
            $bgtfis[$waktu.'-04'][$bar->kodevhc] = $bar->fis04;
            $bgtfis[$waktu.'-05'][$bar->kodevhc] = $bar->fis05;
            $bgtfis[$waktu.'-06'][$bar->kodevhc] = $bar->fis06;
            $bgtfis[$waktu.'-07'][$bar->kodevhc] = $bar->fis07;
            $bgtfis[$waktu.'-08'][$bar->kodevhc] = $bar->fis08;
            $bgtfis[$waktu.'-09'][$bar->kodevhc] = $bar->fis09;
            $bgtfis[$waktu.'-10'][$bar->kodevhc] = $bar->fis10;
            $bgtfis[$waktu.'-11'][$bar->kodevhc] = $bar->fis11;
            $bgtfis[$waktu.'-12'][$bar->kodevhc] = $bar->fis12;
        }
        $str = "SELECT a.kodevhc,\r\n   sum(jam01) as jam01,\r\n   sum(jam02) as jam02,\r\n   sum(jam03) as jam03,\r\n   sum(jam04) as jam04,\r\n   sum(jam05) as jam05,\r\n   sum(jam06) as jam06,\r\n   sum(jam07) as jam07,\r\n   sum(jam08) as jam08,\r\n   sum(jam09) as jam09,\r\n   sum(jam10) as jam10,\r\n   sum(jam11) as jam11,\r\n   sum(jam12) as jam12\r\n    FROM ".$dbname.".bgt_vhc_jam a  where tahunbudget='".$waktu."'\r\n   group by a.kodevhc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $bgtjam[$waktu.'-01'][$bar->kodevhc] = $bar->jam01;
            $bgtjam[$waktu.'-02'][$bar->kodevhc] = $bar->jam02;
            $bgtjam[$waktu.'-03'][$bar->kodevhc] = $bar->jam03;
            $bgtjam[$waktu.'-04'][$bar->kodevhc] = $bar->jam04;
            $bgtjam[$waktu.'-05'][$bar->kodevhc] = $bar->jam05;
            $bgtjam[$waktu.'-06'][$bar->kodevhc] = $bar->jam06;
            $bgtjam[$waktu.'-07'][$bar->kodevhc] = $bar->jam07;
            $bgtjam[$waktu.'-08'][$bar->kodevhc] = $bar->jam08;
            $bgtjam[$waktu.'-09'][$bar->kodevhc] = $bar->jam09;
            $bgtjam[$waktu.'-10'][$bar->kodevhc] = $bar->jam10;
            $bgtjam[$waktu.'-11'][$bar->kodevhc] = $bar->jam11;
            $bgtjam[$waktu.'-12'][$bar->kodevhc] = $bar->jam12;
        }
        if ('' === $kodeorg) {
            $str = 'select kodevhc,jenisvhc from '.$dbname.".vhc_5master where  jenisvhc='".$param['tipe']."'";
        } else {
            $str = 'select kodevhc,jenisvhc from '.$dbname.".vhc_5master where kodetraksi like '".$kodeorg."%' and jenisvhc='".$param['tipe']."'";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $kodevhc[] = $bar->kodevhc;
            $jenisvhc[$bar->kodevhc] = $bar->jenisvhc;
        }
        echo 'Penggunaan BBM  Sat./Litre  Kendaraan-Alat Berat-Mesin '.$kodeorg.' Periode:'.substr($waktu, 0, 4)."                   \r\n                <table class=sortable cellspacing=1 border=0>\r\n               <thead><tr class=rowheader>\r\n               <td rowspan=3>".$_SESSION['lang']['urut']."</td>\r\n               <td rowspan=3>".$_SESSION['lang']['kodevhc']."</td>\r\n                <td rowspan=3>".$_SESSION['lang']['jenis']."</td>\r\n               <td colspan=6 align=center>Jan ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Feb ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Mar ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Apr ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Mei ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Jun ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Jul ".substr($waktu, 0, 4)."</td>    \r\n               <td colspan=6 align=center>Aug ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Sep ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Okt ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Nop ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Des ".substr($waktu, 0, 4)."</td>    \r\n               <td colspan=6 align=center>Total ".substr($waktu, 0, 4)."</td>\r\n               </tr>\r\n               <tr class=rowheader>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n               </tr>\r\n               <tr class=rowheader>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>\r\n               </tr>\r\n               </thead>\r\n               <tbody>";
        $no = 0;
        foreach ($kodevhc as $key => $val) {
            ++$no;
            $treal = 0;
            $trealHM = 0;
            $tbgtfis = 0;
            $tbgtjam = 0;
            echo "<tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$val."</td>\r\n                <td>".$jenisvhc[$val].'</td>';
            for ($kk = 1; $kk <= 12; ++$kk) {
                if ($kk < 10) {
                    $zz = $waktu.'-0'.$kk;
                } else {
                    $zz = $waktu.'-'.$kk;
                }

                $color = 'bgcolor=green';
                if ($realHM[$zz][$val] / $real[$zz][$val] < $bgtjam[$zz][$val] / $bgtfis[$zz][$val]) {
                    $color = 'bgcolor=red';
                }

                echo '<td align=right>'.number_format($real[$zz][$val], 2).'</td><td align=right>'.number_format($realHM[$zz][$val], 2).'</td><td '.$color.'  align=right>'.@number_format($realHM[$zz][$val] / $real[$zz][$val], 2).'</td><td align=right>'.number_format($bgtfis[$zz][$val], 2).'</td><td align=right>'.number_format($bgtjam[$zz][$val], 2).'</td><td align=right bgcolor=#dedede>'.@number_format($bgtjam[$zz][$val] / $bgtfis[$zz][$val], 2).'</td>';
                $treal += $real[$zz][$val];
                $trealHM += $realHM[$zz][$val];
                $tbgtfis += $bgtfis[$zz][$val];
                $tbgtjam += $bgtjam[$zz][$val];
            }
            $color = 'bgcolor=green';
            if ($trealHM / $treal < $tbgtjam / $tbgtfis) {
                $color = 'bgcolor=red';
            }

            echo '<td align=right>'.number_format($treal, 2).'</td><td align=right>'.number_format($trealHM, 2).'</td><td '.$color.'  align=right>'.@number_format($trealHM / $treal, 2).'</td><td align=right>'.number_format($tbgtfis, 2).'</td><td align=right>'.number_format($tbgtjam, 2).'</td><td align=right bgcolor=#dedede>'.@number_format($tbgtjam / $tbgtfis, 2).'</td>';
            echo '</tr>';
        }
        echo "</tbody><tfoot>\r\n                </tfoot></table><a href=javascript:history.back(-1)>Back</a>";

        break;
}

?>