<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$div = $_POST['div'];
$per = $_POST['per'];
$ulatKir = $_POST['ulat'];
if ('excel' === $proses) {
    $div = $_GET['div'];
    $per = $_GET['per'];
    $ulatKir = $_GET['ulat'];
}

$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) && ('' === $div || '' === $per)) {
    echo 'Error: Field Was Empty';
    exit();
}

$arrNm = ['jlhdarnatrima' => 'Darna Trima', 'jlhsetothosea' => 'Setothosea Asigna', 'jlhsetothosea' => 'Setora Nitens', 'jlhulatkantong' => 'Ulat Kantong'];
$blnthn = explode('-', $per);
$jumHari = cal_days_in_month(CAL_GREGORIAN, $blnthn[1], $blnthn[0]);
$tgl1 = $per.'-01';
$tgl2 = $per.'-'.$jumHari;
$arrTgl = rangeTanggal($tgl1, $tgl2);
$nmBulan = strtoupper(numToMonth($blnthn[1], 'I', 'long'));
if ('excel' === $proses) {
    $border = 'border=1';
    $bgCol = 'bgcolor=#CCCCCC';
} else {
    $border = 'border=0';
    $bgCol = '';
}

$stream .= ' '.$_SESSION['org']['namaorganisasi'].'<br />Quality Control<br /><br /><b>MONITORING SENSUS ULAT API</b><br /><br />'.$optNmOrg[$div]."\r\n";
$stream .= "\r\n\t\t\t<table class=sortable ".$border." cellspacing=1 cellpadding=0>\r\n<thead>\r\n\t\t\t\t\t <tr>\r\n\t\t\t\t\t\t<td rowspan=2 align=center ".$bgCol.' >'.$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t\t<td colspan=".$jumHari.' align=center '.$bgCol.' >'.$nmBulan.' '.$blnthn[0]."</td>\r\n\t\t\t\t\t </tr>\r\n\t\t\t\t\t <tr>";
foreach ($arrTgl as $lstTgl => $tgl) {
    $stream .= '<td align=center '.$bgCol.'>'.substr($tgl, 8, 2).'</td>';
}
$stream .= '</tr></thead></tbody>';
if ('jlhdarnatrima' === $ulatKir) {
    $sql = "select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,\r\n\t\tsum(jlhdarnatrima) as jlhdarnatrima\r\n\t\tfrom ".$dbname.'.kebun_qc_ulatapiht a left join '.$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok\r\n\t\twhere a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'\r\n\t\t group by a.kodeblok,a.tanggal order by a.kodeblok asc";
} else {
    if ('jlhsetothosea' === $ulatKir) {
        $sql = "select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,\r\n\t\tsum(jlhsetothosea) as jlhsetothosea\r\n\t\tfrom ".$dbname.'.kebun_qc_ulatapiht a left join '.$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok\r\n\t\twhere a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'\r\n\t\t group by a.kodeblok,a.tanggal order by a.kodeblok asc";
    } else {
        if ('jlhsetoranitens' === $ulatKir) {
            $sql = "select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,\r\n\t\tsum(jlhsetoranitens) as jlhsetoranitens\r\n\t\tfrom ".$dbname.'.kebun_qc_ulatapiht a left join '.$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok\r\n\t\twhere a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'\r\n\t\t group by a.kodeblok,a.tanggal order by a.kodeblok asc";
        } else {
            $sql = "select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,sum(jlhdarnatrima) as jlhdarnatrima,\r\n\t\tsum(jlhsetothosea) as jlhsetothosea,sum(jlhsetoranitens) as jlhsetoranitens \r\n\t\tfrom ".$dbname.'.kebun_qc_ulatapiht a left join '.$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok\r\n\t\twhere a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'\r\n\t\t group by a.kodeblok,a.tanggal order by a.kodeblok asc";
        }
    }
}

$res = mysql_query($sql) ;
while ($bar = mysql_fetch_assoc($res)) {
    $blok[$bar['kodeblok']] = $bar['kodeblok'];
    $jenis[$bar['kodeblok']] = $bar['jenissensus'];
    $pokokdiamati[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']] .= $bar['pokokdiamati'];
    $jlhdarnatrima[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']] = $bar['jlhdarnatrima'];
    $jlhsetothosea[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']] = $bar['jlhsetothosea'];
    $jlhsetoranitens[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']] = $bar['jlhsetoranitens'];
}
$bgcolor = ['sebelum' => 'bgcolor=#FF0000', 'pengendalian' => 'bgcolor=#FFCC00', 'sesudah' => 'bgcolor=00FF00'];
foreach ($blok as $lstblok) {
    $stream .= "<tr class=rowcontent>\r\n\t\t\t\t<td>".$lstblok.'</td>';
    foreach ($arrTgl as $lstTgl => $i) {
        $jumPkk = $pokokdiamati[$lstblok.$jenis[$lstblok].$i];
        $ulat = $jlhdarnatrima[$lstblok.$jenis[$lstblok].$i] + $jlhsetothosea[$lstblok.$jenis[$lstblok].$i] + $jlhsetoranitens[$lstblok.$jenis[$lstblok].$i];
        $jenisPembagi = $jenis[$lstblok];
        if ('sebelum' === $jenisPembagi) {
            $hasil = $ulat;
        } else {
            if ('pengendalian' === $jenisPembagi) {
                $hasil = $ulat;
            } else {
                if ('sesudah' === $jenisPembagi) {
                    $hasil = $ulat;
                }
            }
        }

        if (0 === $hasil) {
            $hasil = '';
            $stream .= '<td>'.number_format($hasil, 2).'</td>';
        } else {
            $stream .= '<td align=right '.$bgcolor[$jenis[$lstblok]].'>'.number_format($hasil, 2).'</td>';
        }
    }
    $stream .= '</tr>';
}
$stream .= '</tbody></table><br /><br />Keterangan :<br />';
$stream .= '<table class=sortable '.$border." cellspacing=1 cellpadding=0>\r\n<thead>";
$stream .= '<tr '.$bgCol." class=rowcontent>\r\n\t\t\t<td align=center>Ulat</td>\r\n\t\t\t<td >Kreteria</td>\r\n\t\t\t<td align=center>Max</td>\r\n\t\t\t<td align=center>Min</td>\r\n\t\t\t\r\n</tr></thead>";
$x = 'select * from '.$dbname.".kebun_qc_5ulatapi where ulat='".$ulatKir."'";
$y = mysql_query($x) ;
while ($z = mysql_fetch_assoc($y)) {
    $stream .= '<tr class=rowcontent>';
    $stream .= '<td>'.$arrNm[$z['ulat']].'</td>';
    $stream .= '<td>'.ucfirst($z['kret']).'</td>';
    $stream .= '<td>'.$z['minu'].'</td>';
    $stream .= '<td>'.$z['maxu'].'</td>';
    $stream .= '</tr>';
}
$stream .= '</table>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_QC_Ulat_Api'.$per;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n\t\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t\t</script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>