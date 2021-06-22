<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' === $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
('' === $_POST['kdBrg'] ? ($kdBrg = $_GET['kdBrg']) : ($kdBrg = $_POST['kdBrg']));
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmSup = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optdt = makeOption($dbname, 'log_5supplier', 'kodetimbangan,namasupplier');
$brd = 0;
$bgclr = '';
if ('excel' === $proses) {
    $bgclr = ' bgcolor=#DEDEDE align=center';
    $brd = 1;
}

if ('' !== $periode) {
    $where .= " and SIPBDATE like '".$periode."%'";
}

$tab .= '<table class=sortable cellspacing=1 border='.$brd."><thead>\r\n        <tr class=rowheader ".$bgclr.">\r\n        <td>".$_SESSION['lang']['NoKontrak']."</td>\r\n        <td>".$_SESSION['lang']['nosipb']."</td>\r\n        <td>".$_SESSION['lang']['tglKontrak']."</td>\r\n        <td>".$_SESSION['lang']['transporter']."</td>\r\n        <td>".$_SESSION['lang']['kodebarang']."</td>\r\n        <td>".$_SESSION['lang']['namabarang']."</td>\r\n        </tr></thead><tbody>\r\n        ";
if ('' !== $kdBrg) {
    $where = " and kodebarang='".$kdBrg."'";
}

$sql = 'select * from '.$dbname.'.pabrik_mssipb '."where SIPBDATE!='' ".$where.' order by SIPBDATE asc';
$query = mysql_query($sql);
while ($res = mysql_fetch_assoc($query)) {
    $sTimb = 'select  distinct kodecustomer from '.$dbname.".pabrik_timbangan where nokontrak='".$res['CTRNO']."'";
    $qTimb = mysql_query($sTimb);
    $rTimb = mysql_fetch_assoc($qTimb);
    $sCust = 'select namacustomer  from '.$dbname.".pmn_4customer where kodetimbangan='".$rTimb['kodecustomer']."'";
    $qCust = mysql_query($sCust);
    $rCust = mysql_fetch_assoc($qCust);
    if ($rowd = 0 === mysql_num_rows($qCust)) {
        $rCust['namacustomer'] = $optdt[$rTimb['kodecustomer']];
    }

    $tab .= "<tr class=rowcontent>\r\n                <td>".$res['CTRNO']."</td>\r\n                <td>".$res['SIPBNO']."</td>\r\n                <td>".tanggalnormal($res['SIPBDATE'])."</td>\r\n                <td>".$rCust['namacustomer']."</td>\r\n                <td>".$res['PRODUCTCODE']."</td>\r\n                <td>".$optNmBrg[$res['PRODUCTCODE']]."</td>\r\n                </tr>";
}
$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'daftarSibp';
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
}

?>