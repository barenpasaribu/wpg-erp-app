<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
('' == $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' == $_POST['tanggal'] ? ($tanggal = $_GET['tanggal']) : ($tanggal = $_POST['tanggal']));
('' == $_POST['region'] ? ($region = $_GET['region']) : ($region = $_POST['region']));
if ('preview' == $proses || 'excel' == $proses || '' != $region) {
    if ('' == $tanggal) {
        exit('Error: All field required');
    }

    $str = 'select * from '.$dbname.".sdm_5tipekaryawan\n        where 1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $tipekar[$bar->id] = $bar->id;
        $artitkr[$bar->id] = $bar->tipe;
    }
    if ('' != $region) {
        $str = 'select * from '.$dbname.".bgt_regional_assignment\n            where regional = '".$region."'";
    } else {
        $str = 'select * from '.$dbname.".bgt_regional_assignment\n            where 1";
    }

    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        if ('' != $region) {
            $regional[$bar->kodeunit] = $bar->kodeunit;
        } else {
            $unitreg[$bar->kodeunit] = $bar->regional;
            $regional[$bar->regional] = $bar->regional;
        }
    }
    $str = 'select * from '.$dbname.".datakaryawan\n        where tanggalmasuk <= ".tanggalsystem($tanggal)." and (tanggalkeluar > '".substr(tanggalsystem($tanggal), 0, 6)."01' or tanggalkeluar is NULL) ";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        if ('' != $region) {
            $qwe = $bar->lokasitugas;
        } else {
            $qwe = $unitreg[$bar->lokasitugas];
        }

        ++$jumlahkar[$qwe][$bar->tipekaryawan];
    }
    if ('excel' != $proses) {
        $brd = 0;
        $bgcolor = '';
    } else {
        $tab .= $_SESSION['lang']['summary'].' '.$_SESSION['lang']['karyawan'].'<br>Tanggal: '.$tanggal.' ';
        $brd = 1;
        $bgcolor = 'bgcolor=#DEDEDE';
    }

    if ('' == $region) {
        $region = $_SESSION['lang']['regional'];
    } else {
        if ('excel' != $proses) {
            $tab .= "<img onclick=level1excel(event,'sdm_slave_2summarykaryawan.php','".$tanggal."','".$region."') src=images/excel.jpg class=resicon title='MS.Excel'>";
        }
    }

    $tab .= "\n    <table width=100% cellspacing=1 border=".$brd.">\n    <thead>\n    <tr>\n        <td ".$bgcolor.'>'.$region.'</td>';
    if (!empty($regional)) {
        foreach ($regional as $reg) {
            if ('' != $region) {
                $tab .= '<td '.$bgcolor." align=center title='Click to details...' onclick=getlevel1('".$tanggal."','".$reg."')>".$reg.'</td>';
            }
        }
    }

    $tab .= "\n        <td ".$bgcolor.' align=center>'.$_SESSION['lang']['total']."</td>\n    </tr>        \n    </thead>\n    <tbody>";
    if (!empty($tipekar)) {
        foreach ($tipekar as $tkr) {
            $tab .= "<tr class=rowcontent>\n        <td>".$artitkr[$tkr].'</td>';
            $total[$tkr] = 0;
            if (!empty($regional)) {
                foreach ($regional as $reg) {
                    $tab .= '<td align=right>'.number_format($jumlahkar[$reg][$tkr]).'</td>';
                    $total[$tkr] += $jumlahkar[$reg][$tkr];
                    $totalgrand[$reg] += $jumlahkar[$reg][$tkr];
                }
            }

            $tab .= "\n        <td align=right>".number_format($total[$tkr])."</td>\n        </tr>";
        }
    }

    $tab .= "<tr class=rowcontent>\n    <td>".$_SESSION['lang']['total'].'</td>';
    $totalnya = 0;
    if (!empty($regional)) {
        foreach ($regional as $reg) {
            $tab .= '<td align=right>'.number_format($totalgrand[$reg]).'</td>';
            $totalnya += $totalgrand[$reg];
        }
    }

    $tab .= "\n    <td align=right>".number_format($totalnya)."</td>\n    </tr>";
    $tab .= '</tbody></table>';
} else {
    if ('level1' == $proses) {
    }
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'level1':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'summary_karyawan_'.$tanggal.'_'.$region;
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
                echo "<script language=javascript1.2>\n                parent.window.alert('Can't convert to excel format');\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\n                window.location='tempExcel/".$nop_.".xls';\n                </script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>