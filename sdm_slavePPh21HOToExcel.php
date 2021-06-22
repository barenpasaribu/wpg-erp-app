<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$jenis = $_GET['jenis'];
$periode = $_GET['periode'];
$regular = $_GET['regular'];
$thr = $_GET['thr'];
$jaspro = $_GET['jaspro'];
$jmsperusahaan = $_GET['jmsperusahaan'];
$arrComp = [];
$str = 'select id from '.$dbname.".sdm_ho_component\r\n      where `pph21`=1 and `lock`=1 order by id";
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
    $maxBJab = $baru->max;
}
if ('bulanan' == $jenis) {
    $str1 = "select e.karyawanid,e.npwp,e.taxstatus,e.name,sum(d.jumlah) as `jumlah`\r\n      from\r\n     ".$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji d\r\n\t where e.karyawanid=d.karyawanid ".$listComp."\r\n\t and periodegaji='".$periode."' and e.karyawanid not in (0999999999,0888888888) group by karyawanid";
    if ($res = mysql_query($str1, $conn)) {
        $stream = 'PPh21 Periode :'.$periode."\r\n\t     <table border=1>\r\n\t\t <thead>\r\n\t\t   <tr bgcolor=#DFDFDF>\r\n\t\t    <td>No.</td>\r\n\t\t\t<td>No.Karyawan</td>\r\n\t\t\t<td>Nama.Karyawan</td>\r\n\t\t\t<td>Status</td>\r\n\t\t\t<td>N.P.W.P</td>\r\n\t\t\t<td>Periode</td>\r\n\t\t\t<td>Sumber</td>\r\n\t\t\t<td>PPh21</td>\r\n\t\t   </tr>\r\n\t\t </thead><tbody id=tbody>";
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
                $str = 'select sum(jumlah)*-1 as jms from '.$dbname.".sdm_gaji\r\n\t\t\t\t      where karyawanid=".$bar->karyawanid." and idkomponen in(5,6,7,9)\r\n\t\t\t\t\t  and periodegaji='".$periode."'";
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
            $totalPendapatan = $totalPendapatan * 12;
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
            $pphbulanan = $ttlpph21 / 12;
            $stream .= "<tr>\r\n\t\t    <td>".$no."</td>\r\n\t\t\t<td align=center>".$bar->userid."</td>\r\n\t\t\t<td>".$bar->name."</td>\r\n\t\t\t<td align=center>".$bar->taxstatus."</td>\r\n\t\t\t<td>".$bar->npwp."</td>\r\n\t\t\t<td align=center>".$periode."</td>\r\n\t\t\t<td align=right>".number_format($pendapatanBulanan, 2, '.', '')."</td>\r\n\t\t\t<td align=right>".number_format($pphbulanan, 2, '.', '')."</td>\r\n\t\t   </tr>";
        }
        $stream .= "</tbody>\r\n          <tfoot>\r\n\t\t  <tr><td colspan=8>Jika Status pajak tidak sesuai atau kosong maka akan dikenakan status K/3.\r\n\t\t  </tr>\r\n\t\t  </tfoot>\r\n\t\t  </table>";
    } else {
        echo ' Error: '.addslashes(mysql_error($conn));
    }

    $nop_ = 'PPh21'.$jenis.'-'.$periode;
} else {
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
    $str = 'select id from '.$dbname.".sdm_ho_component\r\n      where `pph21`=1 and `lock`=1 order by id";
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
    $str1 = "select e.karyawanid,e.npwp,e.taxstatus,e.name,sum(d.jumlah) as `jumlah` from\r\n     ".$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji d\r\n\t where e.karyawanid=d.karyawanid ".$listComp."\r\n\t and periodegaji like'".$periode."%'  and (".$strType.") and karyawanid not in (0999999999,0888888888)\r\n\t group by karyawanid";
    if ($res = mysql_query($str1, $conn)) {
        $stream = 'PPh21 Periode :'.$periode."\r\n\t     <table border=1>\r\n\t\t <thead>\r\n\t\t   <tr bgcolor=#DFDFDF>\r\n\t\t    <td>No.</td>\r\n\t\t\t<td>No.Karyawan</td>\r\n\t\t\t<td>Nama.Karyawan</td>\r\n\t\t\t<td>Status</td>\r\n\t\t\t<td>N.P.W.P</td>\r\n\t\t\t<td>Periode</td>\r\n\t\t\t<td>Sumber</td>\r\n\t\t\t<td>PPh21</td>\r\n\t\t   </tr>\r\n\t\t </thead><tbody id=tbody>";
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
                $str = 'select sum(jumlah*-1) as jms,karyawanid from '.$dbname.".sdm_gaji\r\n\t\t\t\t      where karyawanid=".$bar->karyawanid." and idkomponen in(5,6,7,9)\r\n\t\t\t\t\t  and periode like '".$periode."%'\r\n\t\t\t\t\t  group by karyawanid";
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
            $stream .= "<tr>\r\n\t\t    <td>".$no."</td>\r\n\t\t\t<td align=center>".$bar->karyawanid."</td>\r\n\t\t\t<td>".$bar->name."</td>\r\n\t\t\t<td align=center>".$bar->taxstatus."</td>\r\n\t\t\t<td>".$bar->npwp."</td>\r\n\t\t\t<td align=center>".$periode."</td>\r\n\t\t\t<td align=right>".$pendapatanBulanan."</td>\r\n\t\t\t<td align=right>".$pphbulanan."</td>\r\n\t\t   </tr>";
        }
        $stream .= "</tbody>\r\n          <tfoot>\r\n\t\t  <tr><td colspan=8>Jika Status pajak tidak sesuai atau kosong maka akan dikenakan status K/3.\r\n\t\t  </tr>\r\n\t\t  </tfoot>\r\n\t\t  </table>";
    } else {
        echo ' Error: '.addslashes(mysql_error($conn));
    }

    $nop_ = 'PPh21'.$jenis.'-'.$periode;
}

if (0 < strlen($stream)) {
    if ($handle = opendir('tempExcel')) {
        while (false != ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
                @unlink('tempExcel/'.$file);
            }
        }
        closedir($handle);
    }

    $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
    if (!fwrite($handle, $stream)) {
        echo "<script language=javascript1.2>\r\n\t        parent.window.alert('Can't convert to excel format');\r\n\t        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n\t        window.location='tempExcel/".$nop_.".xls';\r\n\t        </script>";
    closedir($handle);
}

?>