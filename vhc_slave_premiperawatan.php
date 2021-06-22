<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/fpdf.php';
if ('excel' == $_GET['proses'] || 'pdf' == $_GET['proses']) {
    $param = $_GET;
} else {
    $param = $_POST;
}

$optThn = makeOption($dbname, 'vhc_5master', 'kodevhc,tahunperolehan');
$optKlmpk = makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,kelompokvhc');
$optNmKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
switch ($param['proses']) {
    case 'preview':
        $periodeAKtif = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
        if ($param['periode'] != $periodeAKtif) {
            exit('error: Periode diffrent with active periode');
        }

        $sKend = 'select distinct sum(jumlah) as jmlh,kodevhc,jenisvhc,tanggal from '.$dbname.".vhc_rundt a \n                left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi \n                where tanggal like '".$param['periode']."%' group by kodevhc,tanggal \n                order by kodevhc asc";
        $qKend = mysql_query($sKend);
        while ($rKend = mysql_fetch_assoc($qKend)) {
            $dtVhc[$rKend['kodevhc']] = $rKend['kodevhc'];
            $dtPrest[$rKend['kodevhc']] += $rKend['jmlh'];
            ++$jmlHar[$rKend['kodevhc']];
            $jnsVhc[$rKend['kodevhc']] = $rKend['jenisvhc'];
        }
        $jmlhRowKary = count($dtVhc);
        if (0 == $jmlhRowKary) {
            exit('error: Data Empty');
        }

        $sKary = "SELECT distinct kodevhc,premicuci,a.tanggal\n                FROM ".$dbname.".vhc_runhk a\n                LEFT JOIN ".$dbname.".vhc_runht b ON a.notransaksi = b.notransaksi\n                WHERE a.tanggal LIKE '".$param['periode']."%'\n                AND premicuci !=0\n                ORDER BY kodevhc ASC ";
       $qKary = mysql_query($sKary);
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $jmlCuci[$rKary['kodevhc']] += $rKary['premicuci'];
        }
        $sOpt = "select distinct karyawanid,vhc \n               from ".$dbname.'.vhc_5operator order by vhc asc';
        $qOpt = mysql_query($sOpt);
        while ($rOpt = mysql_fetch_assoc($qOpt)) {
            $operator[$rOpt['vhc']] = $rOpt['karyawanid'];
        }
        $tab .= "<button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save']."</button>\n              <table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr align=center>";
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodevhc'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hasilkerjad'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlahhari'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlahcuci'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['premi'].'</td>';
        $tab .= '</tr></thead><tbody>';
        foreach ($dtVhc as $lsVhc) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td >'.$lsVhc.'</td>';
            $tab .= '<td align=right>'.number_format($dtPrest[$lsVhc], 0).'</td>';
            $tab .= '<td align=right>'.$jmlHar[$lsVhc].'</td>';
            $tab .= '<td align=right>'.$jmlCuci[$lsVhc].'</td>';
            if ('0' == (int) ($operator[$lsVhc])) {
                $operator[$lsVhc] = 0;
            }
			
            $tab .= '<td>'.$optNmKary[$operator[$lsVhc]].'<input type=hidden id=karyId_'.$no." value='".$operator[$lsVhc]."' /></td>";
            $premi[$lsVhc] = 0;
            $thn[$lsVhc] = (int) (date('Y')) - $optThn[$lsVhc];
//            if ('' != $jmlCuci[$lsVhc]) {
                if ('MJ' == substr($lsVhc, 0, 2) && 'KD' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                    $premi[$lsVhc] = 4500 * $jmlCuci[$lsVhc];
                } else {
                    if ('DT' == substr($lsVhc, 0, 2) && 'KD' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                        $premi[$lsVhc] = 20000 * $jmlHar[$lsVhc];
                    } else {
                        if ('DT' != substr($lsVhc, 0, 2) && 'KD' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                            if ($optThn[$lsVhc] < 6 && 24 < $jmlHar[$lsVhc]) {
                                $premi[$lsVhc] = 11000 * $jmlHar[$lsVhc];
                            } else {
                                if ($thn[$lsVhc] > 5 && $jmlHar[$lsVhc] > 22) {
                                    $premi[$lsVhc] = 11000 * $jmlHar[$lsVhc];
                                } else {
									$premi[$lsVhc] = ($thn[$lsVhc] * 1000) + $jmlHar[$lsVhc];
								}
                            }
                        } else {
                            if ('AB' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                                if ($thn[$lsVhc] < 6 && 175 <= $dtPrest[$lsVhc]) {
                                    $premi[$lsVhc] = 5000 * $dtPrest[$lsVhc];
                                } else {
                                    if (5 < $thn[$lsVhc] && 150 <= $dtPrest[$lsVhc]) {
                                        $premi[$lsVhc] = 5000 * $dtPrest[$lsVhc];
                                    } else {
                                        if (10 < $thn[$lsVhc] && 125 <= $dtPrest[$lsVhc]) {
                                            $premi[$lsVhc] = 5000 * $dtPrest[$lsVhc];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            //}

            $tab .= '<td align=right>'.number_format($premi[$lsVhc], 2).'<input type=hidden id=premiDt_'.$no.' value='.$premi[$lsVhc].' /></td>';
            $tab .= '</tr>';
        }
        $tab .= "</tbody></table><button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save'].'</button>';
        echo $tab;

        break;
    case 'saveAll':
        for ($awal = 1; $awal <= $param['jmlhRow']; ++$awal) {
            if (0 != (int) ($param['karyId'][$awal])) {
                $sdel = 'delete from '.$dbname.".`kebun_premikemandoran`  where \n                       kodeorg='".$param['kodeorg']."' and `karyawanid`='".$param['karyId'][$awal]."'\n                       and periode='".$param['periode']."'";
                if (mysql_query($sdel)) {
                    $sinsert = 'insert into '.$dbname.'.`kebun_premikemandoran` (`kodeorg`,`karyawanid`,`periode`,`jabatan`,`premi`,`updateby`) values';
                    $sinsert .= "('".$param['kodeorg']."','".$param['karyId'][$awal]."','".$param['periode']."','RAWATKD','".$param['premiDt'][$awal]."','".$_SESSION['standard']['userid']."')";
                    if (!mysql_query($sinsert)) {
                        exit('error: db error '.mysql_error($conn).'___'.$sinsert);
                    }
                } else {
                    exit('error: db error '.mysql_error($conn).'___'.$sdel);
                }
            }
        }

        break;
    case 'loadData':
        $periodeAktif = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
        $sData = 'select distinct kodeorg,periode,premi,karyawanid from '.$dbname.".kebun_premikemandoran where \n                kodeorg='".$_SESSION['empl']['lokasitugas']."' and jabatan='RAWATKD'  kodeorg,periode order by periode desc";
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.$rData['karyawanid'].'</td>';
            $tab .= '<td>'.$rData['premi'].'</td>';
            if ($rData['periode'] == $periodeAktif) {
                $tab .= "<td>\n                       <img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'vhc_slave_premiperawatan.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['RAWATKD']."') >\n                       &nbsp;\n                       <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rData['kodeorg']."','".$rData['periode']."','".$rData['RAWATKD']."');\" >\n                       &nbsp;\n                      </td>";
            } else {
                $tab .= "<td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'kebun_slave_premipanen.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['kodepremi']."') ></td>";
            }

            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'delData':
        $sdel = 'delete from '.$dbname.".`kebun_premipanen`  where \n               kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
        if (!mysql_query($sdel)) {
            exit('error: db error '.mysql_error($conn).'___'.$sdel);
        }

        break;
    case 'excel':
        $tab .= '<table>';
        $tab .= '<tr><td colspan=5>'.$_SESSION['lang']['kodeorg'].' : '.$optNmOrg[$param['kodeorg']].'</td></tr>';
        $tab .= '<tr><td colspan=5>'.$_SESSION['lang']['periode'].' : '.$param['periode'].'</td></tr>';
        $tab .= '</table>';
        $periodeAKtif = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
        if ($param['periode'] != $periodeAKtif) {
            exit('error: Periode diffrent with active periode');
        }

        $sKend = 'select distinct sum(jumlah) as jmlh,kodevhc,jenisvhc,tanggal from '.$dbname.".vhc_rundt a \n                left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi \n                where tanggal like '".$param['periode']."%' group by kodevhc,tanggal \n                order by kodevhc asc";
        $qKend = mysql_query($sKend);
        while ($rKend = mysql_fetch_assoc($qKend)) {
            $dtVhc[$rKend['kodevhc']] = $rKend['kodevhc'];
            $dtPrest[$rKend['kodevhc']] += $rKend['jmlh'];
            ++$jmlHar[$rKend['kodevhc']];
            $jnsVhc[$rKend['kodevhc']] = $rKend['jenisvhc'];
        }
        $jmlhRowKary = count($dtVhc);
        if (0 == $jmlhRowKary) {
            exit('error: Data Empty');
        }

        $sKary = "SELECT distinct kodevhc,premicuci,a.tanggal\n                FROM ".$dbname.".vhc_runhk a\n                LEFT JOIN ".$dbname.".vhc_runht b ON a.notransaksi = b.notransaksi\n                WHERE a.tanggal LIKE '".$param['periode']."%'\n                AND premicuci !=0\n                ORDER BY kodevhc ASC ";
        $qKary = mysql_query($sKary);
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $jmlCuci[$rKary['kodevhc']] += $rKary['premicuci'];
        }
        $sOpt = "select distinct karyawanid,vhc \n               from ".$dbname.'.vhc_5operator order by vhc asc';
        $qOpt = mysql_query($sOpt);
        while ($rOpt = mysql_fetch_assoc($qOpt)) {
            $operator[$rOpt['vhc']] = $rOpt['karyawanid'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead><tr bgcolor=#DEDEDE align=center>';
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodevhc'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hasilkerjad'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlahhari'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlahcuci'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['premi'].'</td>';
        $tab .= '</tr></thead><tbody>';
        foreach ($dtVhc as $lsVhc) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td >'.$lsVhc.'</td>';
            $tab .= '<td align=right>'.number_format($dtPrest[$lsVhc], 0).'</td>';
            $tab .= '<td align=right>'.$jmlHar[$lsVhc].'</td>';
            $tab .= '<td align=right>'.$jmlCuci[$lsVhc].'</td>';
            if ('0' == (int) ($operator[$lsVhc])) {
                $operator[$lsVhc] = 0;
            }

            $tab .= '<td>'.$optNmKary[$operator[$lsVhc]].'<input type=hidden id=karyId_'.$no." value='".$operator[$lsVhc]."' /></td>";
            $premi[$lsVhc] = 0;
            $thn[$lsVhc] = (int) (date('Y')) - $optThn[$lsVhc];
            if ('' != $jmlCuci[$lsVhc]) {
                if ('MJ' == substr($lsVhc, 0, 2) && 'KD' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                    $premi[$lsVhc] = 4500 * $jmlCuci[$lsVhc];
                } else {
                    if ('DT' == substr($lsVhc, 0, 2) && 'KD' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                        $premi[$lsVhc] = 20000 * $jmlHar[$lsVhc];
                    } else {
                        if ('DT' != substr($lsVhc, 0, 2) && 'KD' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                            if ($optThn[$lsVhc] < 6 && 24 < $jmlHar[$lsVhc]) {
                                $premi[$lsVhc] = 11000 * $jmlHar[$lsVhc];
                            } else {
                                if (5 < $thn[$lsVhc] && 22 < $jmlHar[$lsVhc]) {
                                    $premi[$lsVhc] = 11000 * $jmlHar[$lsVhc];
                                }
                            }
                        } else {
                            if ('AB' == $optKlmpk[$jnsVhc[$lsVhc]]) {
                                if ($thn[$lsVhc] < 6 && 175 <= $dtPrest[$lsVhc]) {
                                    $premi[$lsVhc] = 5000 * $dtPrest[$lsVhc];
                                } else {
                                    if (5 < $thn[$lsVhc] && 150 <= $dtPrest[$lsVhc]) {
                                        $premi[$lsVhc] = 5000 * $dtPrest[$lsVhc];
                                    } else {
                                        if (10 < $thn[$lsVhc] && 125 <= $dtPrest[$lsVhc]) {
                                            $premi[$lsVhc] = 5000 * $dtPrest[$lsVhc];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $tab .= '<td align=right>'.number_format($premi[$lsVhc], 2).'<input type=hidden id=premiDt_'.$no.' value='.$premi[$lsVhc].' /></td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'premiPerawatan_'.$param['kodeorg'].'__'.$param['periode'];
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
                echo "<script language=javascript1.2>\n        parent.window.alert('Can't convert to excel format');\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\n        window.location='tempExcel/".$nop_.".xls';\n        </script>";
            closedir($handle);
        }

        break;
}

?>