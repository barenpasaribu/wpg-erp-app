<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $awal = $param['awal'];
        $sampai = $param['sampai'];
        $tipe = $param['pks'];
        $str = 'select tipe from '.$dbname.'.sdm_5tipekaryawan where id='.$tipe;
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $namatipe = $bar->tipe;
        }
        $x = mktime(0, 0, 0, substr($sampai, 4, 2), 15, substr($sampai, 0, 4));
        $akhir = date('Ym', $x);
        $d = (int) (substr($awal, 4, 2));
        $z = 0;
        for ($awal1 = 0; $now <= (int) $akhir; ++$z) {
            $u = mktime(0, 0, 0, $d + $z, 15, substr($awal, 0, 4));
            $now = date('Ym', $u);
            $listPeriode[$z] = date('Y-m', $u);
            $masuk[$z] = 0;
            $targ[$z] = '?periode='.date('Y-m', $u).'&tipe='.$tipe.'&jenis=rinci';
            $alts[$z] = ' Click to Drill';
            $str = 'select count(*) as jumlah from  '.$dbname.".datakaryawan where\n                                  tanggalmasuk like '".date('Y-m', $u)."%' and tipekaryawan=".$tipe;
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $masuk[$z] = $bar->jumlah;
            }
            $keluar[$z] = 0;
            $str1 = 'select count(*) as jumlah from  '.$dbname.".datakaryawan where\n                                  tanggalkeluar like '".date('Y-m', $u)."%' and tipekaryawan=".$tipe;
            $res1 = mysql_query($str1);
            while ($bar1 = mysql_fetch_object($res1)) {
                $keluar[$z] = $bar1->jumlah;
            }
        }
        $graph = new Graph(800, 400);
        $graph->img->SetMargin(60, 20, 30, 50);
        $graph->SetScale('textlin');
        $graph->SetMarginColor('silver');
        $graph->SetShadow();
        $graph->title->Set('TURN OVER KARYWAN  '.$namatipe.' Periode '.$param['awal'].'-'.$param['sampai']);
        $graph->title->SetColor('darkred');
        $graph->xaxis->SetColor('black', 'red');
        $graph->yscale->ticks->SupressZeroLabel(true);
        $graph->yaxis->scale->SetGrace(30);
        $graph->xaxis->SetTickLabels($listPeriode);
        $graph->xaxis->SetLabelAngle(90);
        $graph->legend->SetPos(0.7, 0.22, 'center', 'bottom');
        $txt = new Text('Jumlah');
        $txt->SetPos(0.02, 0.1, 'left', 'bottom');
        $txt->SetBox('white', 'black');
        $graph->AddText($txt);
        $masukBar = new BarPlot($masuk);
        $masukBar->SetWidth(0.6);
        $masukBar->SetCSIMTargets($targ, $alts);
        $masukBar->SetLegend('Masuk');
        $masukBar->SetFillGradient('navy', 'steelblue', GRAD_MIDVER);
        $keluarBar = new BarPlot($keluar);
        $keluarBar->SetWidth(0.6);
        $keluarBar->SetCSIMTargets($targ, $alts);
        $keluarBar->SetLegend('Keluar');
        $keluarBar->SetFillGradient('orange', 'magenta', GRAD_MIDVER);
        $gbar = new GroupbarPlot([$masukBar, $keluarBar]);
        $graph->Add($gbar);
        $graph->StrokeCSIM();

        break;
    case 'rinci':
        echo "<link rel=stylesheet tyle=text href='style/generic.css'>\n                              <script language=javascript src='js/generic.js'></script>";
        $tipe = $param['tipe'];
        $periode = $param['periode'];
        $str = 'select a.namakaryawan,a.tanggalmasuk,a.lokasitugas,b.namajabatan,c.tipe from '.$dbname.".datakaryawan a\n                              left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\n                              left join ".$dbname.".sdm_5tipekaryawan c on a.tipekaryawan=c.id\n                              where tanggalmasuk like '".$periode."%' and tipekaryawan=".$tipe;
        $res = mysql_query($str);
        echo mysql_error($conn);
        echo 'Karyawan Masuk Periode '.$periode."                    \n                            <table class=sortable cellspacing=1 border=0>\n                           <thead><tr class=rowheader>\n                           <td>".$_SESSION['lang']['urut']."</td>\n                           <td>".$_SESSION['lang']['namakaryawan']."</td>\n                           <td>".$_SESSION['lang']['tipekaryawan']."</td>\n                            <td>".$_SESSION['lang']['jabatan']."</td>\n                            <td>".$_SESSION['lang']['lokasitugas']."</td>   \n                           <td>".$_SESSION['lang']['tanggalmasuk']."</td>\n                           </tr></thead>\n                           <tbody>";
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "<tr class=rowcontent>\n                           <td>".$no."</td>\n                           <td>".$bar->namakaryawan."</td>\n                           <td>".$bar->tipe."</td>\n                           <td>".$bar->namajabatan."</td>     \n                           <td>".$bar->lokasitugas."</td>    \n                           <td>".tanggalnormal($bar->tanggalmasuk)."</td>\n                           </tr>";
        }
        echo "</tbody><tfoot>\n                            </tfoot></table>";
        $str = 'select a.namakaryawan,a.tanggalkeluar,a.tanggalmasuk,a.lokasitugas,b.namajabatan,c.tipe from '.$dbname.".datakaryawan a\n                              left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\n                              left join ".$dbname.".sdm_5tipekaryawan c on a.tipekaryawan=c.id\n                              where tanggalkeluar like '".$periode."%' and tipekaryawan=".$tipe;
        $res = mysql_query($str);
        echo '<br>Karyawan Keluar Periode '.$periode."                    \n                            <table class=sortable cellspacing=1 border=0>\n                           <thead><tr class=rowheader>\n                           <td>".$_SESSION['lang']['urut']."</td>\n                           <td>".$_SESSION['lang']['namakaryawan']."</td>\n                           <td>".$_SESSION['lang']['tipekaryawan']."</td>\n                            <td>".$_SESSION['lang']['jabatan']."</td>\n                            <td>".$_SESSION['lang']['lokasitugas']."</td>   \n                           <td>".$_SESSION['lang']['tanggalmasuk']."</td>\n                           <td>".$_SESSION['lang']['tanggalkeluar']."</td>                               \n                           </tr> \n                           <tbody>";
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "<tr class=rowcontent>\n                           <td>".$no."</td>\n                           <td>".$bar->namakaryawan."</td>\n                           <td>".$bar->tipe."</td>\n                           <td>".$bar->namajabatan."</td>     \n                           <td>".$bar->lokasitugas."</td>\n                           <td>".tanggalnormal($bar->tanggalmasuk)."</td>                               \n                           <td>".tanggalnormal($bar->tanggalkeluar)."</td>\n                           </tr></thead>";
        }
        echo "</tbody><tfoot>\n                            </tfoot></table><a href=javascript:history.back(-1)>Back</a>";

        break;
}

?>