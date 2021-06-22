<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$periode = $_POST['periode'];
$regular = $_POST['regular'];
$thr = $_POST['thr'];
$jaspro = $_POST['jaspro'];
$jmsperusahaan = $_POST['jmsperusahaan'];
$strType = '';
if ('yes' == $regular) {
    $strType .= ' idkomponen = 1';
}

if ('yes' == $thr) {
    $strType .= ' or idkomponen = 14 ';
}

if ('yes' == $jaspro) {
    $strType .= ' or idkomponen = 13 ';
}

$arrComp = [];
$str = 'select id from '.$dbname.'.sdm_ho_component where `pph21`=1 order by id';
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_array($res)) {
    array_push($arrComp, $bar[0]);
}
for ($x = 0; $x < count($arrComp); ++$x) {
    if (0 == $x) {
        $listComp = $arrComp[$x];
    } else {
        $listComp .= ','.$arrComp[$x];
    }
}
$listComp = ' and d.idkomponen in('.$listComp.')';
$arrPtkp = [];
$str = 'select * from '.$dbname.'.sdm_ho_pph21_ptkp order by id';
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    $arrPtkp[$bar->id] = $bar->value;
}
$arrTarif = [];
$arrTarifVal = [];
$str = 'select * from '.$dbname.".sdm_ho_pph21_kontribusi\r\n      where percent!=0 or upto!=0  order by upto";
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    array_push($arrTarif, $bar->percent);
    array_push($arrTarifVal, $bar->upto);
}
$jmsporsi = 6.54;
$jmsporsikar = 3;
$str = 'select * from '.$dbname.'.sdm_ho_hr_jms_porsi';
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    if ('perusahaan' == $bar->id) {
        $jmsporsi = $bar->value;
    } else {
        $jmsporsikar = $bar->value;
    }
}
$stru = 'select `persen`,`max` from '.$dbname.'.sdm_ho_pph21jabatan';
$resu = mysql_query($stru);
$percenJab = 0;
$maxBJab = 0;
while ($baru = mysql_fetch_object($resu)) {
    $percenJab = $baru->persen;
    $maxBJab = $baru->max * 12;
}
$str1 = "select e.karyawanid,e.npwp,e.taxstatus,e.name,sum(d.jumlah) as `jumlah` from\r\n     ".$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji d\r\n\t where e.karyawanid=d.karyawanid ".$listComp."\r\n\t and periodegaji like'".$periode."%'  and (".$strType.") and e.karyawanid not in (0999999999,0888888888)\r\n\t group by karyawanid";
if ($res = mysql_query($str1, $conn)) {
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $jmsDariPrsh = 0;
        $totalPendapatan = 0;
        $taxstatus = str_replace('M', '', $bar->taxstatus);
        $taxstatus = str_replace('TK', 'T', $taxstatus);
        $taxstatus = str_replace('K', '', $taxstatus);
        $taxstatus = str_replace('/', '', $taxstatus);
        $taxstatus = str_replace('-', '', $taxstatus);
        $taxstatus = trim($taxstatus);
        if ('yes' == $jmsperusahaan) {
            $str = 'select sum(jumlah*-1) as jms,karyawanid from '.$dbname.".sdm_gaji\r\n\t\t\t\t      where karyawanid=".$bar->karyawanid." and idkomponen in(5,9)\r\n\t\t\t\t\t  and periode like '".$periode."%'\r\n\t\t\t\t\t  group by karyawanid";
            $jmsKar = 0;
            $rex = mysql_query($str);
            while ($bax = mysql_fetch_array($rex)) {
                $jmsKar = $bax[0];
            }
            if (0 < $jmsKar) {
                $jmsDariPrsh = $jmsKar / $jmsporsikar * 100 * $jmsporsi / 100;
            }
        }

        $totalPendapatan = $jmsDariPrsh + $bar->jumlah;
        $pendapatanBulanan = $totalPendapatan;
        if ($maxBJab < $totalPendapatan * $percenJab / 100) {
            $byJab = $maxBJab;
        } else {
            $byJab = $totalPendapatan * $percenJab / 100;
        }

        $totalPendapatan = $totalPendapatan - $byJab;
        if (isset($arrPtkp[$taxstatus])) {
            $ptkp = $arrPtkp[$taxstatus];
        } else {
            $ptkp = $arrPtkp[3];
        }

        $pkp = $totalPendapatan - $ptkp;
        $pph21 = [];
        $valVol = $pkp;
        if (0 < $pkp) {
            for ($z = 0; $z < count($arrTarif); ++$z) {
                if ($z < count($arrTarif) - 1) {
                    if (0 == $z) {
                        if ($arrTarifVal[$z] < $pkp) {
                            $pph21[$z] = $arrTarif[$z] / 100 * $arrTarifVal[$z];
                        } else {
                            $pph21[$z] = $pkp * $arrTarif[$z] / 100;
                        }
                    } else {
                        if ($arrTarifVal[$z] < $pkp) {
                            $pph21[$z] = $arrTarif[$z] / 100 * ($arrTarifVal[$z] - $arrTarifVal[$z - 1]);
                        } else {
                            if (0 < $pkp - $arrTarifVal[$z - 1]) {
                                $pph21[$z] = $arrTarif[$z] / 100 * ($pkp - $arrTarifVal[$z - 1]);
                            } else {
                                $pph21[$z] = 0;
                            }
                        }
                    }
                } else {
                    if ($pkp - $arrTarifVal[$z - 1] <= 0) {
                        $pph21[$z] = 0;
                    } else {
                        $pph21[$z] = $arrTarif[$z] / 100 * ($pkp - $arrTarifVal[$z - 1]);
                    }
                }
            }
        } else {
            $pphbulanan = 0;
        }

        $ttlpph21 = array_sum($pph21);
        $pphbulanan = $ttlpph21;
        echo "<tr class=rowcontent>\r\n\t\t    <td class=firsttd>".$no."</td>\r\n\t\t\t<td align=center>".$bar->karyawanid."</td>\r\n\t\t\t<td>".$bar->name."</td>\r\n\t\t\t<td align=center>".$bar->taxstatus."</td>\r\n\t\t\t<td>".$bar->npwp."</td>\r\n\t\t\t<td align=center>".$periode."</td>\r\n\t\t\t<td align=right>".number_format($pendapatanBulanan, 2, '.', ',')."</td>\r\n\t\t\t<td align=right>".number_format($pphbulanan, 2, '.', ',')."</td>\r\n\t\t   </tr>";
    }
} else {
    echo ' Error: '.addslashes(mysql_error($conn));
}

?>