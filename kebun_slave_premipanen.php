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

$optCek = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,premirajin');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
switch ($param['proses']) {
    case 'preview':
        $periodeAKtif = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
        if ($param['periode'] != $periodeAKtif) {
            exit('error: Periode diffrent with active periode');
        }

        $blnthn = explode('-', $param['periode']);
        $jumHari = cal_days_in_month(CAL_GREGORIAN, $blnthn[1], $blnthn[0]);
        $tgl1 = $param['periode'].'-01';
        $tgl2 = $param['periode'].'-'.$jumHari;
        $date2 = tanggalnormal($tgl2);
        $totHari = dates_inbetween($tgl1, $tgl2);
        $pecahTgl1 = explode('-', $tgl1);
        list($thn1, $bln1, $tgl1) = $pecahTgl1;
        $i = 0;
        $sum = 0;
        do {
            $tanggal = date('d-m-Y', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1));
            if (0 == date('w', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1))) {
                ++$sum;
            }

            $sLbr = 'select distinct * from '.$dbname.".sdm_5harilibur where \r\n                  tanggal='".tanggalsystem($tanggal)."' and regional='".$_SESSION['empl']['regional']."'";
            $qLbr = mysql_query($sLbr) ;
            if (1 == mysql_num_rows($qLbr)) {
                ++$sum;
            }

            ++$i;
        } while ($tanggal != $date2);
        $is = 1;
        $sum = 0;
        $sPremi = 'select distinct * from '.$dbname.".kebun_5premipanen where kodeorg='".$param['kdpremi']."' order by hasilkg desc";
        $qPremi = mysql_query($sPremi) ;
        while ($rPremi = mysql_fetch_assoc($qPremi)) {
            if ('KALTENG' == $_SESSION['empl']['regional']) {
                $basisKg[$is] = $rPremi['hasilkg'];
                $premiRajin[$is] = $rPremi['premirajin'];
            } else {
                $basisKg[$is] = $rPremi['lebihbasiskg'];
            }

            $rupiah[$is] = $rPremi['rupiah'];
            ++$is;
        }
        $JmlhRow = $is - 1;
        $kdOrg = " and left(a.kodeorg,4)='".$param['kodeorg']."'";
        if ($param['kodeorg'] == substr($param['kdpremi'], 0, 4)) {
            $kdOrg = " and left(a.kodeorg,6)='".$param['kdpremi']."'";
        }

        $sData = 'select hasilkerjakg,a.nik,tanggal,namakaryawan,hasilkerja,upahkerja from '.$dbname.".kebun_prestasi a \r\n                left join  ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi\r\n                left join  ".$dbname.".datakaryawan c on a.nik=c.karyawanid where left(tanggal,7)='".$param['periode']."' and \r\n                tipetransaksi='PNN' ".$kdOrg.' order by tanggal asc,namakaryawan asc';
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            $whrGpk = "idkomponen=1 and tahun='".substr($param['periode'], 0, 4)."' and karyawanid='".$rData['nik']."'";
            $optCek = makeOption($dbname, 'sdm_5gajipokok', 'karyawanid,jumlah', $whrGpk);
            $gapok[$rData['nik']] = $optCek[$rData['nik']] / 30; // /25;
            if ($gapok[$rData['nik']] != $rData['upahkerja']) {
                $dtKaryId[$rData['nik']] = $rData['nik'];
                $dtKaryNm[$rData['nik']] = $rData['namakaryawan'];
                $dtHslKrj[$rData['nik'].$rData['tanggal']] = $rData['hasilkerjakg'];
                $dtHslJjg[$rData['nik']] += $rData['hasilkerja'];
            } else {
                continue;
            }
        }
        $jmlhRowKary = count($dtKaryId);
        $tab .= "<button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save']."</button>\r\n              <table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>\r\n              <tr align=center>";
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['periode'].'</td>';
        foreach ($totHari as $ar => $isi) {
            $qwe = date('D', strtotime($isi));
            $dhr = "regional='".$_SESSION['empl']['regional']."' and tanggal='".$isi."'";
            $optHariLbr = makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal', $dhr);
            $tab .= '<td width=5px  rowspan=2>';
            if ('Sun' == $qwe || '' != $optHariLbr[$_SESSION['empl']['regional']]) {
                $tab .= '<font color=red>'.substr($isi, 8, 2).'</font>';
            } else {
                $tab .= substr($isi, 8, 2);
            }

            $tab .= '</td>';
        }
        $tab .= '<td colspan=2>'.$_SESSION['lang']['total'].'</td>';
        if ('KALIMANTAN' == $param['kdpremi']) {
            $tab .= '<td  rowspan=2>'.$_SESSION['lang']['pengurang'].'</td>';
        }

        $tab .= '<td rowspan=2>'.$_SESSION['lang']['hk'].'</td>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['basis'].'</td>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['pengali'].'</td>';
        $tab .= '<td colspan=3>'.$_SESSION['lang']['totalpremi']."</td></tr>\r\n               <tr align=center>\r\n                    <td >".$_SESSION['lang']['jjg']."</td>\r\n                    <td>".$_SESSION['lang']['kg']."</td>\r\n                    <td>".$_SESSION['lang']['premipanen']."</td>\r\n                    <td>".$_SESSION['lang']['premirajin']."</td>\r\n                    <td>".$_SESSION['lang']['rp']."</td>\r\n               </thead><tbody>";
        foreach ($dtKaryId as $karyId) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td><input type=hidden id=karyId_'.$no." value='".$karyId."' />".$dtKaryNm[$karyId].'</td>';
            $tab .= '<td>'.$param['periode'].'</td>';
            foreach ($totHari as $ar => $isi) {
                $tab .= '<td align=right>'.number_format($dtHslKrj[$karyId.$isi], 0).'</td>';
                $totKary[$karyId] += $dtHslKrj[$karyId.$isi];
                if (0 != $dtHslKrj[$karyId.$isi]) {
                    ++$hariAktif[$karyId];
                }
            }
            $basisData = 0;
            $rup = 0;
            $rupy = 0;
            $totRup = 0;
            if ('KALTENG' == $_SESSION['empl']['regional']) {
                $basisData = 0;
                $totRup = 0;
                $pengaliDt = 0;
                if (22 == $hariAktif[$karyId] || 22 < $hariAktif[$karyId]) {
                    if ('KALTENG' == $param['kdpremi']) {
                        for ($awl = 1; $awl <= $JmlhRow; ++$awl) {
                            if ($basisKg[$awl] < $totKary[$karyId] || $totKary[$karyId] == $basisKg[$awl]) {
                                $rup = $rupiah[$awl];
                                $prmRajin = $premiRajin[$awl];

                                break;
                            }

                            if ($totKary[$karyId] < $basisKg[$itungDsr] || $totKary[$karyId] == $basisKg[$itungDsr]) {
                                $rup = $rupiah[$itungDsr];
                                $prmRajin = $premiRajin[$itungDsr];

                                break;
                            }
                        }
                        $bandingJjg[$karyId] = $dtHslJjg[$karyId] / 70;
                        if ($hariAktif[$karyId] < $bandingJjg[$karyId]) {
                            $rupy = $hariAktif[$karyId] * $prmRajin;
                        } else {
                            $rupy = $bandingJjg[$karyId] * $prmRajin;
                        }

                        $totRup = $rupy + $rup;
                    } else {
                        $totRup = $totKary[$karyId] * $rupiah[$awl];
                        $pengaliDt = $rupiah[$awl];
                    }
                }
            } else {
                if ('SUMUT' == $_SESSION['empl']['regional']) {
                    $bjrAktual[$karyId] = $totKary[$karyId] / $dtHslJjg[$karyId];
                    $lstert = 0;
                    $sTarif = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n                             kodeorg='".$param['kdpremi']."' and jenis='satuan' order by bjr desc";
                    $qTarif = mysql_query($sTarif) ;
                    while ($rTarif = mysql_fetch_assoc($qTarif)) {
                        $rpLbh[$rTarif['bjr']] = $rTarif['rplebih'];
                        $basisPanen[$rTarif['bjr']] = $rTarif['basisjjg'];
                        $lstBjr[] = $rTarif['bjr'];
                        $lstBjr2[$lstert] = $rTarif['bjr'];
                        ++$lstert;
                    }
                    $MaxRow = count($lstBjr);
                    foreach ($lstBjr as $lstRow => $dtIsiBjr) {
                        if (0 == $lstRow) {
                            if ($dtIsiBjr < $bjrAktual[$karyId]) {
                                $dtbjr = $dtIsiBjr;

                                break;
                            }
                        } else {
                            if ($lstRow != $MaxRow) {
                                $leapdt = $lstRow + 1;
                                if ($bjrAktual[$karyId] == $dtIsiBjr || $lstBjr2[$leapdt] < $bjrAktual[$karyId]) {
                                    $dtbjr = $dtIsiBjr;

                                    break;
                                }
                            } else {
                                $dmin = $dtIsiBjr - 1;
                                if ($dmin <= $bjrAktual[$karyId]) {
                                    $dtbjr = $dtIsiBjr;

                                    break;
                                }

                                $dtbjr = 0;
                            }
                        }
                    }
                    $bas[$karyId] = $basisPanen[$dtbjr] * $bjrAktual[$karyId] * ($totHk[$karyId] - $sum);
                    $pre[$karyId] = $totKary[$karyId] - $bas[$karyId];
                    for ($awl = 1; $awl <= $JmlhRow; ++$awl) {
                        if (1 == $awl && ($basisKg[$awl] < $pre[$karyId] || $pre[$karyId] == $basisKg[$awl])) {
                            $totRup = $rupiah[$awl];
                            $basisData = $basisKg[$awl];

                            break;
                        }

                        if ($awl != $JmlhRow) {
                            $fwd = $awl + 1;
                            if ($basisKg[$fwd] < $pre[$karyId] || $pre[$karyId] == $basisKg[$awl]) {
                                $totRup = $rupiah[$awl];
                                $basisData = $basisKg[$awl];

                                break;
                            }
                        } else {
                            if ($basisKg[$awl] < $pre[$karyId] || $pre[$karyId] == $basisKg[$awl]) {
                                $totRup = $rupiah[$awl];
                                $basisData = $basisKg[$awl];

                                break;
                            }
                        }
                    }
                }
            }

            $tab .= '<td align=right><input type=hidden id=totKg_'.$no." value='".$totKary[$karyId]."' />".number_format($totKary[$karyId], 0)."</td>\r\n                   <td align=right><input type=hidden   value='".$dtHslJjg[$karyId]."' />".number_format($dtHslJjg[$karyId], 0).'</td>';
            if ('KALIMANTAN' == $param['kdpremi']) {
                $tab .= '<td align=right>'.number_format($bas[$karyId], 0).'</td>';
            }

            $tab .= '<td align=right>'.($hariAktif[$karyId] - $sum).'</td>';
            $tab .= '<td align=right>'.$basisData.'</td>';
            $tab .= '<td align=right>'.$pengaliDt.'</td>';
            $tab .= '<td align=right>'.number_format($rup, 0)."</td>\r\n                   <td align=right>".number_format($rupy, 0)."</td>\r\n                   <td align=right><input type=hidden id=rpPremi_".$no." value='".$totRup."' />".number_format($totRup, 0)."</td>\r\n                   ";
            $totRup = 0;
            $rup = 0;
            $rupy = 0;
        }
        $tab .= "</tbody></table><button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save'].'</button>';
        echo $tab;

        break;
    case 'saveAll':
        $awal = 1;
        while ($awal <= $param['jmlhRow']) {
            $sdel = 'delete from '.$dbname.".`kebun_premipanen`  where \r\n                   kodeorg='".$param['kodeorg']."' and `karyawanid`='".$param['KaryId'][$awal]."'\r\n                   and periode='".$param['periode']."'";
            if (mysql_query($sdel)) {
                $sinsert = 'insert into '.$dbname.'.`kebun_premipanen` (`kodeorg`,`kodepremi`,`karyawanid`,`periode`,`totalkg`,`rupiahpremi`,`updateby`) values';
                $sinsert .= "('".$param['kodeorg']."','".$param['kdpremi']."','".$param['KaryId'][$awal]."','".$param['periode']."','".$param['hasilKg'][$awal]."','".$param['rpPremi'][$awal]."','".$_SESSION['standard']['userid']."')";
                if (!mysql_query($sinsert)) {
                    exit('error: db error '.mysql_error($conn).'___'.$sinsert);
                }

                ++$awal;
            } else {
                exit('error: db error '.mysql_error($conn).'___'.$sdel);
            }
        }

        break;
    case 'loadData':
        $periodeAktif = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
        $sData = 'select distinct kodeorg,periode,kodepremi from '.$dbname.".kebun_premipanen where \r\n                kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periode order by periode desc";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.$rData['periode'].'</td>';
            if ($rData['periode'] == $periodeAktif) {
                $tab .= "<td>\r\n                       <img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'kebun_slave_premipanen.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['kodepremi']."') >\r\n                       &nbsp;\r\n                       <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rData['kodeorg']."','".$rData['periode']."');\" >\r\n                       &nbsp;\r\n                       <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_premipanen','".$rData['kodeorg'].','.$rData['periode'].','.$rData['kodepremi']."','','kebun_slave_premipanen',event);\">\r\n                      </td>";
            } else {
                $tab .= "<td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'kebun_slave_premipanen.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['kodepremi']."') ></td>";
            }

            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'delData':
        $sdel = 'delete from '.$dbname.".`kebun_premipanen`  where \r\n               kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
        if (!mysql_query($sdel)) {
            exit('error: db error '.mysql_error($conn).'___'.$sdel);
        }

        break;
    case 'excel':
        $tab .= '<table>';
        $tab .= '<tr><td colspan=5>'.$_SESSION['lang']['kodeorg'].' : '.$optNmOrg[$param['kodeorg']].'</td></tr>';
        $tab .= '<tr><td colspan=5>'.$_SESSION['lang']['periode'].' : '.$param['periode'].'</td></tr>';
        $tab .= '<tr><td colspan=5>'.$_SESSION['lang']['kodepremi'].' : '.$param['kodePremi'].'</td></tr>';
        $tab .= '</table>';
        $tab .= '<table class=sortable border=1 cellpadding=1 cellspacing=1>';
        $tab .= '<thead>';
        $tab .= '<tr bgcolor=#DEDEDE align=center> ';
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hasilkerjakg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['premi'].'</td></tr><tbody>';
        $sData = 'select a.* from '.$dbname.".kebun_premipanen a \r\n                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                where kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'\r\n                order by namakaryawan asc";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$optNmKary[$rData['karyawanid']].'</td>';
            $tab .= '<td align=right>'.number_format($rData['totalkg'], 0).'</td>';
            $tab .= '<td align=right>'.number_format($rData['rupiahpremi'], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'premiPanen__'.$param['kodeorg'].'__'.$param['periode'];
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
                echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        $table = $_GET['table'];
        $column = explode(',', $_GET['column']);
        $where = $_GET['cond'];

class masterpdf extends FPDF
{
    public function Header()
    {
        global $table;
        global $header;
        global $column;
        global $dbname;
        global $optNmKary;
        global $optNmOrg;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['unit'], '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, ':', '', 0, 'L');
        $this->Cell(1 / 100 * $width, $height, $optNmOrg[$column[0]].' '.$column[0], '', 1, 'L');
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, ':', '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, $column[1], '', 1, 'L');
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['kodepremi'], '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, ':', '', 0, 'L');
        $this->Cell(1 / 100 * $width, $height, $column[2], '', 0, 'L');
        $this->Ln();
    }
}

        $pdf = new masterpdf('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->AddPage();
        $pdf->Cell(20, 1.5 * $height, 'No.', 'TBLR', 0, 'C');
        $pdf->Cell(160, 1.5 * $height, $_SESSION['lang']['namakaryawan'], 'TBLR', 0, 'L');
        $pdf->Cell(65, 1.5 * $height, $_SESSION['lang']['hasilkerjakg'], 'TBLR', 0, 'C');
        $pdf->Cell(65, 1.5 * $height, $_SESSION['lang']['premi'], 'TBLR', 1, 'L');
        $no = 0;
        $pdf->SetFillColor(255, 255, 255);
        $ql = 'select a.* from '.$dbname.'.'.$table." a \r\n                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                where kodeorg='".$column[0]."' and periode='".$column[1]."'\r\n                order by namakaryawan asc";
        $qData = mysql_query($ql) ;
        while ($data = mysql_fetch_assoc($qData)) {
            $pdf->SetFont('Arial', '', 7);
            ++$no;
            $pdf->Cell(20, $height, $no, 'TBLR', 0, 'L');
            $pdf->Cell(160, $height, $optNmKary[$data['karyawanid']], 'TBLR', 0, 'L');
            $pdf->Cell(65, $height, number_format($data['totalkg'], 0), 'TBLR', 0, 'R');
            $pdf->Cell(65, $height, number_format($data['rupiahpremi'], 0), 'TBLR', 1, 'R');
        }
        $pdf->Cell(15, $height, 'Page '.$pdf->PageNo(), '', 1, 'L');
        $pdf->Output();

        break;
}
function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);
    if ($date1 == $date2) {
        $dates_array = [];
        $dates_array[] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

?>