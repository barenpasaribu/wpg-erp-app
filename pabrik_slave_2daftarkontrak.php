<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
extract($_POST);
extract($_GET);

if ('' === $kdKomoditi) {
    exit('Error: '.$_SESSION['lang']['komoditi']." can't empty");
}

if ('' === $thnKontrak) {
    exit('Error: '.$_SESSION['lang']['tahunkontrak']." can't empty");
}

if ('' === strlen($thnKontrak)) {
    exit('Error: pls check  '.$_SESSION['lang']['tahunkontrak'].'');
}

$nmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$brdr = 0;
if ('excel' === $proses) {
    $bgclr = ' bgcolor=#DEDEDE';
    $brdr = 1;
}

if ('preview' === $proses || 'excel' === $proses) {
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brdr.' class=sortable><thead>';
    $tab .= '<tr '.$bgclr.'>';
    $tab .= '<td >No.</td>';
    $tab .= '<td >'.$_SESSION['lang']['NoKontrak'].'</td>';
    $tab .= '<td >'.$_SESSION['lang']['namacust'].'</td>';
    $tab .= '<td >'.$_SESSION['lang']['volume'].'</td>';
    $tab .= '</tr>';
    $tab .= '</thead><tbody>';
    $sData = 'select nokontrak,koderekanan,kuantitaskontrak from '.$dbname.".pmn_kontrakjual where \r\n        tanggalkontrak like '".$thnKontrak."%' and kodebarang='".$kdKomoditi."' and  substr(nokontrak,5,3)='".$pt."'";
    $qData = mysql_query($sData);
    while ($rData = mysql_fetch_assoc($qData)) {
        ++$no;
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$no.'</td>';
        $tab .= '<td>'.$rData['nokontrak'].'</td>';
        $tab .= '<td>'.$nmCust[$rData['koderekanan']].'</td>';
        $tab .= '<td align=right>'.number_format($rData['kuantitaskontrak'], 0).'</td>';
        $tab .= '</tr>';
    }
    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'daftarkontrak';
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
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getKodeorg':
        $optorg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        if (1 === $tipeIntex) {
            $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk ='PMO' order by namaorganisasi asc";
        } else {
            if (0 === $tipeIntex) {
                $sOrg = 'SELECT namasupplier,`kodetimbangan` FROM '.$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan!='NULL' order by namasupplier asc";
            } else {
                if (2 === $tipeIntex) {
                    $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk <>'PMO' order by namaorganisasi asc";
                }
            }
        }

        if (3 !== $tipeIntex) {
            $qOrg = mysql_query($sOrg);
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                if (0 !== $tipeIntex) {
                    $optorg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
                } else {
                    $optorg .= '<option value='.$rOrg['kodetimbangan'].'>'.$rOrg['namasupplier'].'</option>';
                }
            }
        }

        echo $optorg;

        break;
    default:
        break;
}

?>
