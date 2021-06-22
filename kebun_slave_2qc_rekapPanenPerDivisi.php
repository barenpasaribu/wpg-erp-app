<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_line.php';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$per = $_GET['per'];
$reg = $_GET['reg'];
$bln = substr($per, 5, 2);
$thn = substr($per, 0, 4);
$nmBulan = numToMonth($bln, 'I', 'long');
$xdata = [];
$xdata[0] = '';
$i = 'select distinct substr(kodeblok,1,4) as divisi from '.$dbname.'.kebun_qc_panendt where substr(kodeblok,1,4) in (select kodeunit from '.$dbname.".bgt_regional_assignment \r\n\twhere regional='".$reg."') and tanggalcek like '%".$per."%' group by substr(kodeblok,1,4) order by substr(kodeblok,1,4) asc";
$n = mysql_query($i) ;
while ($d = mysql_fetch_assoc($n)) {
    ++$x;
    $xdata[$x] = $d['divisi'];
}
$yPanen = [];
$yTdkPanen = [];
$yRasio = [];
$str = "\tselect sum(jjgpanen) as jjgpanen,sum(jjgtdkpanen) as jjgtdkpanen,sum(brdtdkdikutip)/sum(jjgpanen) as rasio\r\n\t\tfrom ".$dbname.'.kebun_qc_panendt where substr(kodeblok,1,4) in (select kodeunit from '.$dbname.".bgt_regional_assignment \r\n\t\twhere regional='".$reg."') and tanggalcek like '%".$per."%' group by substr(kodeblok,1,4) order by substr(kodeblok,1,4) asc";
$yPanen[$y] = 0;
$yTdkPanen[$y] = 0;
$yRasio[$y] = 0;
$res = mysql_query($str) ;
while ($bar = mysql_fetch_assoc($res)) {
    ++$y;
    $yPanen[$y] = $bar['jjgpanen'];
    $yTdkPanen[$y] = $bar['jjgtdkpanen'];
    $yRasio[$y] = $bar['rasio'];
}
if ('' === $xdata[$x]) {
    exit('Error:Data Kosong');
}

$graph = new Graph(500, 300);
$graph->SetScale('textlin');
$graph->img->SetMargin(40, 30, 40, 40);
$graph->xaxis->SetTickLabels($xdata);
$graph->xaxis->title->Set($_SESSION['lang']['divisi']);
$graph->yaxis->title->Set();
$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->title->SetShadow('gray@0.4', 5);
$graph->title->Set(strtoupper($_SESSION['lang']['regional'].' '.$reg.' '.$nmBulan.' '.$thn));
$lineplot = new LinePlot($yPanen);
$lineplot2 = new LinePlot($yTdkPanen);
$lineplot3 = new LinePlot($yRasio);
$lineplot->SetColor('blue');
$lineplot2->SetColor('red');
$lineplot3->SetColor('');
$graph->legend->SetPos(0.1, 0.99, 'left', 'bottom');
$graph->legend->SetShadow('gray@0.4', -10);
$lineplot->SetLegend(strtoupper($_SESSION['lang']['tbs']).'  '.$_SESSION['lang']['panen']);
$graph->legend->SetPos(0.1, 0.99, 'left', 'bottom');
$graph->legend->SetShadow('gray@0.4', -10);
$lineplot2->SetLegend(strtoupper($_SESSION['lang']['tbs']).' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['panen']);
$graph->legend->SetPos(0.1, 0.99, 'left', 'bottom');
$graph->legend->SetShadow('gray@0.4', -10);
$lineplot3->SetLegend($_SESSION['lang']['rasio'].' '.$_SESSION['lang']['brondolan']);
$graph->Add($lineplot);
$graph->Add($lineplot2);
$graph->Add($lineplot3);
$graph->StrokeCSIM();
$tab = "<link rel=stylesheet tyle=text href='style/generic.css'>\r\n            <script language=javascript src='js/generic.js'></script>";
$tab .= "<br /><br />\r\n\r\n\t<table class=sortable cellspacing=1 cellpadding=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['kode']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['divisi']."</td>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['jjg'].'<br />'.$_SESSION['lang']['panen']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['no'].'<br />'.$_SESSION['lang']['panen']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['jjg'].'<br />'.$_SESSION['lang']['tidakdikumpul']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['brondolan'].'<br />'.$_SESSION['lang']['tdkdikutip']."</td> \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['rasio'].'<br />'.$_SESSION['lang']['brondolan']."</td> \t\r\n\t\t\t</tr></thead>";
$str1 = "select sum(jjgtdkpanen) as jjgtdkpanen,sum(jjgpanen) as jjgpanen, sum(jjgtdkkumpul) as jjgtdkkumpul,\r\n\t\tsum(brdtdkdikutip) as brdtdkdikutip,substr(kodeblok,1,4) as divisi \r\n\t\tfrom ".$dbname.'.kebun_qc_panendt where substr(kodeblok,1,4) in (select kodeunit from '.$dbname.".bgt_regional_assignment \r\n\t\twhere regional='".$reg."') and tanggalcek like '%".$per."%' group by substr(kodeblok,1,4) order by substr(kodeblok,1,4) asc";
$res1 = mysql_query($str1) ;
while ($bar1 = mysql_fetch_assoc($res1)) {
    $tab .= "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t\t<td align=left>".$bar1['divisi']."</td>\r\n\t\t\t\t<td align=left>".$optNmOrg[$bar1['divisi']]."</td>\r\n\t\t\t\t<td align=right>".$bar1['jjgpanen']."</td>\r\n\t\t\t\t<td align=right>".$bar1['jjgtdkpanen']."</td>\r\n\t\t\t\t<td align=right>".$bar1['jjgtdkkumpul']."</td>\r\n\t\t\t\t<td align=right>".$bar1['brdtdkdikutip']."</td>\r\n\t\t\t\t<td align=right>".number_format($bar1['brdtdkdikutip'] / $bar1['jjgpanen'], 2)."</td>\r\n\t\t\t</tr>";
}
$tab .= '</table>';
echo $tab;
echo " \r\n\r\n\r\n\r\n";

?>