<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$param = $_POST;
if ('' != isset($_GET['proses'])) {
    if ('excel' == $_GET['proses']) {
        $param = $_GET;
    } else {
        $param['proses'] = $_GET['proses'];
    }
}

$str = 'select * from '.$dbname.".sdm_5tipekaryawan\r\n        where 1";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tipekar[$bar->id] = $bar->id;
    $artitkr[$bar->id] = $bar->tipe;
}
if ('' != $param['region']) {
    $str = 'select * from '.$dbname.".bgt_regional_assignment\r\n            where regional = '".$param['region']."'";
} else {
    $str = 'select * from '.$dbname.".bgt_regional_assignment\r\n            where 1";
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('' != $param['region']) {
        $regional[$bar->kodeunit] = $bar->kodeunit;
    } else {
        $unitreg[$bar->kodeunit] = $bar->regional;
        $regional[$bar->regional] = $bar->regional;
    }
}
if ('' == $param['ptId2']) {
    $whr .= 'and a.kodeorganisasi in (select distinct kodeorganisasi from '.$dbname.".organisasi where tipe='PT')";
    $whrd = 'and kodeorganisasi in (select distinct kodeorganisasi from '.$dbname.".organisasi where tipe='PT')";
} else {
    $whr .= "and a.kodeorganisasi='".$param['ptId2']."'";
    $whrd = "and kodeorganisasi='".$param['ptId2']."'";
}

if ('' != $param['unitId2']) {
    $whr .= "and lokasitugas='".$param['unitId2']."'";
}

if ('' != $param['region']) {
    $whr = '';
    $whrd = '';
    $whr = 'and lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$param['region']."')";
    $whrd = 'and lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$param['region']."')";
}

$sholding = 'select distinct count(karyawanid) as jmlhkary,tipekaryawan,lokasitugas from '.$dbname.".datakaryawan \r\n                   where (left(tanggalkeluar,7)>='".$param['prdIdDr2']."' or left(tanggalkeluar,7)='0000-00') and alokasi=1 ".$whrd."\r\n                   group by tipekaryawan,lokasitugas";
$qholding = mysql_query($sholding);
while ($rholding = mysql_fetch_assoc($qholding)) {
    if ('' != $param['region']) {
        $qwe = $rholding['lokasitugas'];
    } else {
        $qwe = $unitreg[$rholding['lokasitugas']];
    }

    $jumlahkar[$qwe][$rholding['tipekaryawan']] += $rholding['jmlhkary'];
}
$sdmGaji = 'select distinct count(b.karyawanid) as jmlhkary,tipekaryawan,a.kodeorganisasi,a.lokasitugas from '.$dbname.".datakaryawan a \r\n          left join ".$dbname.".sdm_gaji b on a.karyawanid=b.karyawanid where \r\n          (left(tanggalkeluar,7)>='".$param['prdIdDr2']."' or left(tanggalkeluar,7)='0000-00')\r\n          and periodegaji='".$param['prdIdDr2']."'   ".$whr."\r\n          and idkomponen=1\r\n          group by tipekaryawan,lokasitugas";
$qsdmGaji = mysql_query($sdmGaji);
while ($rsdmGaji = mysql_fetch_assoc($qsdmGaji)) {
    if ('' != $param['region']) {
        $qwe = $rsdmGaji['lokasitugas'];
    } else {
        $qwe = $unitreg[$rsdmGaji['lokasitugas']];
    }

    $jumlahkar[$qwe][$rsdmGaji['tipekaryawan']] += $rsdmGaji['jmlhkary'];
}
if ('excel' != $param['proses']) {
    $brd = 0;
    $bgcolor = '';
} else {
    $tab .= $_SESSION['lang']['summary'].' '.$_SESSION['lang']['karyawan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['gaji'].'<br>Periode: '.$param['prdIdDr2'].' ';
    $brd = 1;
    $bgcolor = 'bgcolor=#DEDEDE';
}

if ('' == $param['region']) {
    $param['region'] = $_SESSION['lang']['regional'];
} else {
    if ('excel' != $param['proses']) {
        $tab .= "<img onclick=level2excel(event,'sdm_slave_2summarykaryawan2.php','".$param['prdIdDr2']."','".$param['region']."') src=images/excel.jpg class=resicon title='MS.Excel'>";
    }
}

$tab .= "\r\n    <table width=100% cellspacing=1 border=".$brd.">\r\n    <thead>\r\n    <tr  ".$bgcolor.">\r\n        <td>".$_SESSION['lang']['tipekaryawan'].'</td>';
if (!empty($regional)) {
    foreach ($regional as $reg) {
        if ('' != $reg) {
            $tab .= '<td '.$bgcolor." align=center title='Click to details...' onclick=getlevel2('".$param['prdIdDr2']."','".$reg."')>".$reg.'</td>';
        }
    }
}

$tab .= "\r\n        <td align=center>".$_SESSION['lang']['total']."</td>\r\n    </tr>        \r\n    </thead>\r\n    <tbody>";
if (!empty($tipekar)) {
    foreach ($tipekar as $tkr) {
        $tab .= "<tr class=rowcontent>\r\n        <td>".$artitkr[$tkr].'</td>';
        $total[$tkr] = 0;
        if (!empty($regional)) {
            foreach ($regional as $reg) {
                $tab .= '<td align=right>'.number_format($jumlahkar[$reg][$tkr]).'</td>';
                $total[$tkr] += $jumlahkar[$reg][$tkr];
                $totalgrand[$reg] += $jumlahkar[$reg][$tkr];
            }
        }

        $tab .= "\r\n        <td align=right>".number_format($total[$tkr])."</td>\r\n        </tr>";
    }
}

$tab .= "<tr class=rowcontent>\r\n    <td>".$_SESSION['lang']['total'].'</td>';
$totalnya = 0;
if (!empty($regional)) {
    foreach ($regional as $reg) {
        $tab .= '<td align=right>'.number_format($totalgrand[$reg]).'</td>';
        $totalnya += $totalgrand[$reg];
    }
}

$tab .= "\r\n    <td align=right>".number_format($totalnya)."</td>\r\n    </tr>";
$tab .= '</tbody></table>';
switch ($param['proses']) {
    case 'preview':
        echo $tab;

        break;
    case 'level1':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'summary_karyawan_'.$param['prdIdDr2'];
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
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
}

?>