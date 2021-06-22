<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
echo "<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$noakundari = $_GET['noakundari'];
$noakunsampai = $_GET['noakunsampai'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$pt = $_GET['pt'];
$unit = $_GET['unit'];
$periodesaldo = str_replace('-', '', $periode);
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

if ('excel' !== $proses) {
    echo "<fieldset><legend>Print Excel</legend>\r\n     <img onclick=\"parent.detailNeracaKeExcel(event,'keu_slave_getNeracaDetail.php?proses=excel&noakundari=".$noakundari.'&noakunsampai='.$noakunsampai.'&periode='.$periode.'&periode1='.$periode1.'&pt='.$pt.'&unit='.$unit."')\" src=images/excel.jpg class=resicon title='MS.Excel'>\r\n     </fieldset>";
}

if ('excel' === $proses) {
    $bg = ' bgcolor=#DEDEDE';
    $brdr = 1;
} else {
    $bg = '';
    $brdr = 0;
}

$qwe = '<table class=sortable border='.$brdr." cellspacing=1>\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td align=center>No</td>\r\n          <td align=center>No.Akun</td>\r\n          <td align=center>Nama Akun</td>\r\n          <td align=center>Saldo Awal</td>\r\n          <td align=center>Debet</td>\r\n          <td align=center>Kredit</td>\r\n        </tr>\r\n      </thead>\r\n      <tbody>";
if ('' === $unit) {
    $where = ' a.kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " a.kodeorg='".$unit."'";
}

$s_detail = 'select a.noakun,b.namaakun as namaakun,sum(a.awal'.substr($periodesaldo, 4, 2).') as awal, sum(a.debet'.substr($periodesaldo, 4, 2).') as debet,sum(a.kredit'.substr($periodesaldo, 4, 2).') as kredit from '.$dbname.'.keu_saldobulanan a join '.$dbname.".keu_5akun b where a.noakun=b.noakun and a.noakun between '".$noakundari."'and '".$noakunsampai."' and a.periode='".$periodesaldo."' and ".$where.' group by a.noakun';
$q_detail = mysql_query($s_detail);
while ($r_detail = mysql_fetch_assoc($q_detail)) {
    $akun[$r_detail['noakun']] = $r_detail['noakun'];
    $nmakun[$r_detail['noakun']] = $r_detail['namaakun'];
    $awal[$r_detail['noakun']] = $r_detail['awal'];
    $debet[$r_detail['noakun']] = $r_detail['debet'];
    $kredit[$r_detail['noakun']] = $r_detail['kredit'];
}
$no = 0;
if (!empty($akun)) {
    foreach ($akun as $lst_akun) {
        ++$no;
        $qwe .= "<tr class=rowcontent>\r\n                   <td align=center>".$no."</td>\r\n                   <td align=center>".$lst_akun."</td>\r\n                   <td align=left>".$nmakun[$lst_akun]."</td>               \r\n                   <td align=right>".number_format($awal[$lst_akun], 2)."</td>    \r\n                   <td align=right>".number_format($debet[$lst_akun], 2)."</td>    \r\n                   <td align=right>".number_format($kredit[$lst_akun], 2).'</td>';
        $qwe .= '</tr>';
        $tawal += $awal[$lst_akun];
        $tdebet += $debet[$lst_akun];
        $tkredit += $kredit[$lst_akun];
    }
}

$qwe .= "<tr class=rowcontent>\r\n           <td colspan=3 align=center><b>TOTAL</b></td>\r\n           <td align=right>".number_format($tawal,2)."</td>\r\n           <td align=right>".number_format($tdebet,2)."</td>\r\n           <td align=right>".number_format($tkredit,2)."</td>\r\n      </tr>";
$qwe .= '</tbody><tfoot></tfoot></table>';
switch ($proses) {
    case 'preview':
        echo $qwe;

        break;
    case 'excel':
        $qwe .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'DetailNeraca_'.$dte;
        if (0 < strlen($qwe)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $qwe)) {
                echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can't convert to excel format');\r\n                    </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>