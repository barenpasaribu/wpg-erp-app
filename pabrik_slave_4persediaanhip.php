<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
if (isset($_POST['kodeorg2'])) {
    $param = $_POST;
} else {
    $param = $_GET;
}

$tgl = explode('-', $param['tanggal2']);
$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
$tab .= '<tr>';
$tab .= '<td>No.</td>';
$tab .= '<td>'.$_SESSION['lang']['produk'].'</td>';
$tab .= '<td>'.$_SESSION['lang']['kodetangki'].'</td>';
$tab .= '<td>'.$_SESSION['lang']['tanggalsounding'].'</td>';
$tab .= '<td>'.$_SESSION['lang']['stock'].' KG</td></tr></thead><tbody>';
$sCpo = 'select  * from '.$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"."and kodetangki='ST01'".'order by tanggal desc';
$qCpo = mysql_query($sCpo);
$rCpo = mysql_fetch_assoc($qCpo);
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td>CPO</td>';
$tab .= '<td>ST01</td>';
$tab .= '<td>'.$rCpo['tanggal'].'</td>';
$tab .= '<td align=right>'.number_format($rCpo['kuantitas'], 0).'</td></tr>';
$sCpo2 = 'select  * from '.$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"."and kodetangki='ST02'".'order by tanggal desc';
$qCpo2 = mysql_query($sCpo2);
$rCpo2 = mysql_fetch_assoc($qCpo2);
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td>CPO</td>';
$tab .= '<td>ST02</td>';
$tab .= '<td>'.$rCpo2['tanggal'].'</td>';
$tab .= '<td align=right>'.number_format($rCpo2['kuantitas'], 0).'</td></tr>';
$sbTotCpo += $rCpo2['kuantitas'] + $rCpo['kuantitas'];
$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=4 align=center>'.$_SESSION['lang']['total'].'</td>';
$tab .= '<td align=right>'.number_format($sbTotCpo, 0).'</td></tr>';
$tglMax = 'select max(tanggal) as tanggal from '.$dbname.'.pabrik_masukkeluartangki '."where kodetangki in ('ST01','ST02') and kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'";
$qtglMax = mysql_query($tglMax);
$rTglMax = mysql_fetch_assoc($qtglMax);
$sCpoKoma = 'select sum(beratbersih) as kuantitas from '.$dbname.'.pabrik_timbangan '."where kodebarang='40000001' and tanggal>='".$rTglMax['tanggal']."' and millcode='".$param['kodeorg2']."'";
$qCpoKoma = mysql_query($sCpoKoma);
$rCpoKoma = mysql_fetch_assoc($qCpoKoma);
$sCpoKoma2 = 'select sum(beratbersih) as kuantitas from '.$dbname.'.pabrik_timbangan '."where kodebarang='40000001' and tanggal>='".$rTglMax['tanggal']."' and millcode='".$param['kodeorg2']."'";
$qCpoKoma2 = mysql_query($sCpoKoma2);
$rCpoKoma2 = mysql_fetch_assoc($qCpoKoma2);
$pengiriman = $rCpoKoma['kuantitas'] + $rCpoKoma2['kuantitas'];
$grndCpo = $sbTotCpo + $pengiriman;
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td colspan=3>Pengiriman CPO (Kg)</td>';
$tab .= '<td align=right>'.number_format($pengiriman, 0).'</td></tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=4 align=center>'.$_SESSION['lang']['grnd_total'].' CPO '.$_SESSION['lang']['tanggal'].' (Kg) '.tanggaldgnbar($param['tanggal2']).'</td>';
$tab .= '<td align=right>'.number_format($grndCpo, 0).'</td></tr>';
$sKer = 'select  * from '.$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"."and kodetangki='BLK01'".'order by tanggal desc';
$qKer = mysql_query($sKer);
$rKer = mysql_fetch_assoc($qKer);
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td>KERNEL</td>';
$tab .= '<td>BLK01</td>';
$tab .= '<td>'.$rKer['tanggal'].'</td>';
$tab .= '<td align=right>'.number_format($rKer['kernelquantity'], 0).'</td></tr>';
$sbKer += $rKer['kernelquantity'];
$sKer2 = 'select  * from '.$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"."and kodetangki='BLK02'".'order by tanggal desc';
$qKer2 = mysql_query($sKer2);
$rKer2 = mysql_fetch_assoc($qKer2);
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td>KERNEL</td>';
$tab .= '<td>BLK02</td>';
$tab .= '<td>'.$rKer2['tanggal'].'</td>';
$tab .= '<td align=right>'.number_format($rKer2['kernelquantity'], 0).'</td></tr>';
$sbKer += $rKer2['kernelquantity'];
$sKer3 = 'select  * from '.$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"."and kodetangki='BLK03'".'order by tanggal desc';
$qKer3 = mysql_query($sKer3);
$rKer3 = mysql_fetch_assoc($qKer3);
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td>KERNEL</td>';
$tab .= '<td>BLK03</td>';
$tab .= '<td>'.$rKer3['tanggal'].'</td>';
$tab .= '<td align=right>'.number_format($rKer3['kernelquantity'], 0).'</td></tr>';
$sbKer += $rKer3['kernelquantity'];
$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=4 align=center>'.$_SESSION['lang']['total'].'</td>';
$tab .= '<td align=right>'.number_format($sbKer, 0).'</td></tr>';
$tglMax2 = 'select   max(tanggal) as tanggal from '.$dbname.'.pabrik_masukkeluartangki '."where kodetangki in ('BLK02') and kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'";
$qtglMax2 = mysql_query($tglMax2);
$rTglMax2 = mysql_fetch_assoc($qtglMax2);
$sKerKrm = 'select sum(beratbersih) as kuantitas from '.$dbname.'.pabrik_timbangan '."where kodebarang='40000002' and tanggal>='".$rTglMax2['tanggal']."' and millcode='".$param['kodeorg2']."'";
$qKerKrm = mysql_query($sKerKrm);
$rKerKrm = mysql_fetch_assoc($qKerKrm);
$grndKer = $rKerKrm['kuantitas'] + $sbKer;
++$no;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>'.$no.'</td>';
$tab .= '<td colspan=3>Pengiriman KERNEL(Kg)</td>';
$tab .= '<td align=right>'.number_format($rKerKrm['kuantitas'], 0).'</td></tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=4 align=center>'.$_SESSION['lang']['grnd_total'].' KERNEL '.$_SESSION['lang']['tanggal'].' (Kg) '.tanggaldgnbar($param['tanggal2']).'</td>';
$tab .= '<td align=right>'.number_format($grndKer, 0).'</td></tr>';
$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $periode = $_GET['periode'];
        $stream .= "\r\n\t\t\t<table>\r\n\t\t\t<tr><td>".$_SESSION['lang']['laporanstok'].' '.$kdPbrik.' '.$kdTangki."</td></tr>\r\n\t\t\t<tr><td>".$_SESSION['lang']['periode'].'</td><td>'.$tampilperiode."</td></tr>\r\n\t\t\t<tr></tr>\r\n\t\t\t</table>\r\n\t\t\t<table border=1>\r\n\t\t\t<tr bgcolor=#DEDEDE>\r\n\t\t\t\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td>".$_SESSION['lang']['kodetangki']."</td>\r\n\t\t<td>".$_SESSION['lang']['max']." Kg</td>\r\n\t\t<td align=right>".$_SESSION['lang']['cpokuantitas']." (KG)</td>\r\n\t\t\r\n\t\t<td align=right>".$_SESSION['lang']['cpoffa']." (%)</td>\r\n\t\t<td align=right>".$_SESSION['lang']['cpokdair']." (%)</td>\r\n\t\t<td align=right>".$_SESSION['lang']['cpokdkot']." (%)</td>\r\n\t\t<td align=right>".$_SESSION['lang']['kernelquantity']." (KG)</td>\r\n\t\t\r\n\t\t<td align=right>".$_SESSION['lang']['kernelffa']." (%)</td>\r\n\t\t<td align=right>".$_SESSION['lang']['kernelkdair']." (%)</td>\r\n\t\t<td align=right>".$_SESSION['lang']['kernelkdkot']." (%)</td>\r\n\t\t\t\r\n\t\t\t\r\n\t\t\t\r\n\t\t\t</tr>";
        if (!empty($tanger)) {
            foreach ($tanger as $tgl) {
                $stream .= "<tr class=rowcontent>\r\n\t\t<td>".$tanker[$tgl]['kodorg']."</td>\r\n\t\t<td>".$tgl."</td>\r\n\t\t<td>".$tanker[$tgl]['kotang']."</td>\r\n\t\t\r\n\t\t<td>".number_format($cMax['volume'])."</td>\r\n\t\t\r\n\t\t<td align=right>".number_format($tanker[$tgl]['cpokua'], 0)."</td>\r\n\t\t\r\n\t\t<td align=right>".number_format($tanker[$tgl]['cpoffa'], 2)."</td>\r\n\t\t<td align=right>".number_format($tanker[$tgl]['cpokai'], 2)."</td>\r\n\t\t<td align=right>".number_format($tanker[$tgl]['cpokko'], 2)."</td>\r\n\t\t<td align=right>".number_format($tanker[$tgl]['kerkua'], 0)."</td>\r\n\t\t\r\n\t\t<td align=right>".number_format($tanker[$tgl]['kerffa'], 2)."</td>\r\n\t\t<td align=right>".number_format($tanker[$tgl]['kerkai'], 2)."</td>\r\n\t\t<td align=right>".number_format($tanker[$tgl]['kerkko'], 2)."</td>\r\n\t\t</tr>\r\n\t\t";
            }
        }

        $stream .= '</table>';
        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'Laporan Stok-'.$kdPbrik.$periode.$kdTangki;
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
                echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
            closedir($handle);
        }

        break;
    case 'getTangki':
        $sGet = 'select kodetangki,keterangan from '.$dbname.".pabrik_5tangki where kodeorg='".$kdPbrik."'";
        $qGet = mysql_query($sGet);
        $optTangki .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optTangki .= '<option value='.$rGet['kodetangki'].'>'.$rGet['keterangan'].'</option>';
        }
        echo $optTangki;

        break;
    default:
        break;
}

?>