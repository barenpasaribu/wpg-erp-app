<?php
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    require_once 'lib/fpdf.php';
    require_once 'lib/terbilang.php';
    
    $proses = $_GET['proses'];
    $lksiTgs = $_SESSION['empl']['lokasitugas'];
    $kdeOrg = $_POST['kdeOrg'];
    
    if ('' == $kdeOrg) {
        $kdeOrg = $_GET['kdeOrg'];
    }

    $kdOrg = $_POST['kdOrg'];
    if ('' == $kdOrg) {
        $kdOrg = $_GET['kdOrg'];
    }
    // echo "hai";
    // echo "<br>";
    // print_r($_POST['tgl1']);
    // echo "<br>";
    // print_r($_POST['tgl2']);
    // die();
    $tgl1 = tanggalsystem($_POST['tgl1']);
    $tgl2 = tanggalsystem($_POST['tgl2']);
    
    if ('' == $tgl1) {
        $tgl1 = tanggalsystem($_GET['tgl1']);
    }

    if ('' == $tgl2) {
        $tgl2 = tanggalsystem($_GET['tgl2']);
    }

    $tgl_1 = tanggalsystem($_POST['tgl_1']);
    $tgl_2 = tanggalsystem($_POST['tgl_2']);

    if ('' == $tgl_1) {
        $tgl_1 = tanggalsystem($_GET['tgl_1']);
    }

    if ('' == $tgl_2) {
        $tgl_2 = tanggalsystem($_GET['tgl_2']);
    }

    $periode = $_POST['periode'];
    if ('' == $periode) {
        $periode = $_GET['periode'];
    }

    $periodeGaji = $periode;
    $periode = explode('-', $periode);
    $kdUnit = $_POST['kdUnit'];
    $pilihan = $_POST['pilihan'];
    if ($pilihan == '') {
        $pilihan = $_GET['pilihan'];
    }

    $pilihan2 = $_POST['pilihan2'];
    if ($pilihan2 == '') {
        $pilihan2 = $_GET['pilihan2'];
    }

    $pilihan3 = $_POST['pilihan3'];
    if ('' == $pilihan3) {
        $pilihan3 = $_GET['pilihan3'];
    }

    if (!$kdOrg) {
        $kdOrg = $_SESSION['empl']['lokasitugas'];
    }

    if ('' != $tgl_1 && '' != $tgl_2) {
        $tgl1 = $tgl_1;
        $tgl2 = $tgl_2;
    }

    $test = dates_inbetween($tgl1, $tgl2);
    // print_r($test);
    // die();
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";
    // print_r($sOrg);
    // die();
    $qOrg = mysql_query($sOrg);
    while ($rOrg = mysql_fetch_assoc($qOrg)) {
        $nmOrg = $rOrg['namaorganisasi'];
    }
    if (!$nmOrg) {
        $nmOrg = $kdOrg;
    }

    if ('' != $kdOrg) {
        $kodeOrg = $kdOrg;
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $where = " and lokasitugas = '".$kodeOrg."'";
            $where2 = " and substr(kodeorg,1,4)='".$kdOrg."'";
        } else {
            if (4 < strlen($kdOrg)) {
                $where = " and subbagian='".$kdOrg."'";
                $where2 = " and kodeorg='".$kdOrg."'";
            } else {
                $where = " and lokasitugas='".$kdOrg."'";
                $where2 = " and substr(kodeorg,1,4)='".$kdOrg."'";
            }
        }
    } else {
        $kodeOrg = $_SESSION['empl']['lokasitugas'];
        $where = " and lokasitugas='".$kodeOrg."'";
    }

    if ('semua' == $pilihan2) {
        $where3 = '';
    } else {
        if ('bulanan' == $pilihan2) {
            $where3 = " and a.sistemgaji = 'Bulanan' ";
        } else {
            if ('harian' == $pilihan2) {
                $where3 = " and a.sistemgaji = 'Harian' ";
            }
        }
    }

    if ('semua' == $pilihan3) {
        $where4 = '';
    } else {
        $where4 = " and a.bagian = '".$pilihan3."' ";
    }

    $strJ = 'select * from '.$dbname.'.sdm_5jabatan';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $jab[$barJ->kodejabatan] = $barJ->namajabatan;
    }
    $strJ = 'select * from '.$dbname.'.sdm_5departemen';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $bag[$barJ->kode] = $barJ->nama;
    }
    $dzArr = [];
    $tot = [];
    $total = 0;
    $resData = [];
    $sGetLembur = 'select jamaktual, jamlembur,tipelembur from '.$dbname.".sdm_5lembur where kodeorg = '".$kodeOrg."'";
    $rGetLembur = fetchData($sGetLembur);
    foreach ($rGetLembur as $row => $kar) {
        $GetLembur[$kar['jamaktual']][$kar['tipelembur']] = $kar['jamlembur'];
    }
    // if ('rupiah' == $pilihan) {
    //     $sPeople = "
    //     SELECT 

    //     a.subbagian,b.karyawanid as karyawanid, b.tanggal as tanggal, 
    //     b.uangkelebihanjam as uangkelebihanjam, a.namakaryawan, a.bagian, a.kodejabatan
        
    //     FROM 

    //     ".$dbname.".sdm_lemburdt b
    //     LEFT JOIN ".$dbname.".datakaryawan a on a.karyawanid = b.karyawanid
        
    //     WHERE 

    //     b.uangkelebihanjam > 0 ".$where2.' '.$where3.' '.$where4.' 
    //     ';
    // } else {
    //     $sPeople = "
    //     SELECT 
    //     a.subbagian,b.karyawanid as karyawanid, b.tanggal as tanggal, 
    //     b.jamaktual as uangkelebihanjam , b.tipelembur, a.namakaryawan, 
    //     a.bagian, a.kodejabatan
        
    //     FROM 
    //     ".$dbname.".sdm_lemburdt b 
    //     LEFT JOIN ".$dbname.".datakaryawan a on a.karyawanid = b.karyawanid
    //     WHERE 
    //     b.tanggal between  '".$tgl1."' and '".$tgl2."' ".$where2.' '.$where3.' '.$where4.' 
    //     and b.jamaktual > 0
    //     and tanggal BETWEEN "2019-12-01" AND "2019-12-31"
    //     ';
    // }

    if ('rupiah' == $pilihan) {
        $sPeople = "SELECT a.subbagian,b.karyawanid as karyawanid, b.tanggal as tanggal, b.uangkelebihanjam as uangkelebihanjam, a.namakaryawan, a.bagian, a.kodejabatan\r\n                          FROM ".$dbname.".sdm_lemburdt b\r\n                          LEFT JOIN ".$dbname.".datakaryawan a on a.karyawanid = b.karyawanid\r\n                          WHERE b.tanggal between  '".$tgl1."' and '".$tgl2."' ".$where2.' '.$where3.' '.$where4.' and b.uangkelebihanjam>0';
    } else {
        $sPeople = "SELECT a.subbagian,b.karyawanid as karyawanid, b.tanggal as tanggal, b.jamaktual as uangkelebihanjam , b.tipelembur, a.namakaryawan, a.bagian, a.kodejabatan\r\n                          FROM ".$dbname.".sdm_lemburdt b \r\n                          LEFT JOIN ".$dbname.".datakaryawan a on a.karyawanid = b.karyawanid\r\n                          WHERE b.tanggal between  '".$tgl1."' and '".$tgl2."' ".$where2.' '.$where3.' '.$where4.' and b.jamaktual>0';
    }

    // print_r($sPeople);
    // die();

    $query = mysql_query($sPeople);
    while ($res = mysql_fetch_assoc($query)) {
        $dzArr[$res['karyawanid']]['id'] = $res['karyawanid'];
        $dzArr[$res['karyawanid']]['sb'] = $res['subbagian'];
        $dzArr[$res['karyawanid']]['nm'] = $res['namakaryawan'];
        $dzArr[$res['karyawanid']]['bg'] = $bag[$res['bagian']];
        $dzArr[$res['karyawanid']]['jb'] = $jab[$res['kodejabatan']];
        if ('jam_lembur' != $pilihan) {
            $dzArr[$res['karyawanid']][$res['tanggal']] = $res['uangkelebihanjam'];
        } else {
            $dzArr[$res['karyawanid']][$res['tanggal']] = $GetLembur[$res['uangkelebihanjam']][$res['tipelembur']];
        }
    }
    switch ($proses) {
        case 'preview':
            // echo "hai";
            // echo "<br>";
            // print_r($sPeople);
            // echo "<br>";
            // print_r($tgl2);
            // die();
            if ('' == $periodeGaji) {
                echo 'warning: Period required';
                exit();
            }

            echo "<table cellspacing='1' border='0' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td>No</td>\r\n        <td>".$_SESSION['lang']['nama']."</td>\r\n        <td>".$_SESSION['lang']['subbagian']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['bagian'].'</td>';
            foreach ($test as $ar => $isi) {
                $qwe = date('D', strtotime($isi));
                echo '<td width=5px align=center>';
                if ('Sun' == $qwe) {
                    echo '<font color=red>'.substr($isi, 8, 2).'</font>';
                } else {
                    echo substr($isi, 8, 2);
                }
                echo '</td>';
            }
            echo "<td>Jumlah</td></tr></thead>\r\n        <tbody>";
            foreach ($dzArr as $qwe) {
                ++$no;
                echo '<tr class=rowcontent><td>'.$no."</td>\r\n                <td>".$qwe['nm']."</td>\r\n                <td>".$qwe['sb']."</td>\r\n                <td>".$qwe['jb']."</td>\r\n                <td>".$qwe['bg'].'</td>';
                $zxc = 0;
                foreach ($test as $ar => $isi) {
                    if (0 != $qwe[$isi]) {
                        if ('rupiah' == $pilihan) {
                            echo '<td align=right>'.number_format($qwe[$isi]).'</td>';
                        } else {
                            echo '<td align=right>'.number_format($qwe[$isi], 1).'</td>';
                        }
                    } else {
                        echo '<td align=right></td>';
                    }


                    $zxc += $qwe[$isi];
                    $asd[$isi] += $qwe[$isi];
                }
                if ('rupiah' == $pilihan) {
                    echo '<td align=right>'.number_format($zxc).'</td>';
                } else {
                    echo '<td align=right>'.number_format($zxc, 1).'</td>';
                }

                echo '</tr>';
            }
            echo "<thead class=rowheader>\r\n        <tr>\r\n        <td colspan=5>Total</td>";
            foreach ($test as $ar => $isi) {
                if ('rupiah' == $pilihan) {
                    echo '<td align=right>'.number_format($asd[$isi]).'</td>';
                } else {
                    echo '<td align=right>'.number_format($asd[$isi], 1).'</td>';
                }

                $total += $asd[$isi];
            }


            if ('rupiah' == $pilihan) {
                echo '<td align=right>'.number_format($total).'</td>';
            } else {
                echo '<td align=right>'.number_format($total, 1).'</td>';
            }

            echo '</tbody></table>';
            break;
        case 'pdf':
        if ('' == $periodeGaji) {
            echo 'warning: period required';
            exit();
        }

        class PDF extends FPDF
        {
            public function Header()
            {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                global $period;
                global $periode;
                global $kdOrg;
                global $kdeOrg;
                global $tgl1;
                global $tgl2;
                global $where;
                global $jmlHari;
                global $test;
                global $nmOrg;
                global $pilihan;
                global $pilihan2;
                $jmlHari = $jmlHari * 1.5;
                $cols = 247.5;
                $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/SSP_logo.jpg';
                } else {
                    if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/MI_logo.jpg';
                    } else {
                        if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                            $path = 'images/HS_logo.jpg';
                        } else {
                            if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                                $path = 'images/BM_logo.jpg';
                            }
                        }
                    }
                }

                $this->Image($path, $this->lMargin, $this->tMargin, 70);
                $this->SetFont('Arial', 'B', 9);
                $this->SetFillColor(255, 255, 255);
                $this->SetX(100);
                $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
                $this->SetX(100);
                $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
                $this->SetX(100);
                $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
                $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
                $this->Ln();
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['laporanLembur'].' (dalam '.$pilihan.') '.$pilihan2, '', 0, 'L');
                $this->Ln();
                $this->Cell($width, $height, strtoupper('Rekapitulasi Lembur Karyawan').' : '.$nmOrg, '', 0, 'C');
                $this->Ln();
                $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2), '', 0, 'C');
                $this->Ln();
                $this->SetFont('Arial', 'B', 7);
                $this->SetFillColor(220, 220, 220);
                $this->Cell(2 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
                $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['nama'], 1, 0, 'C', 1);
                $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['jabatan'], 1, 0, 'C', 1);
                $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['bagian'], 1, 0, 'C', 1);
                foreach ($test as $ar => $isi) {
                    $this->Cell(2.6 / 100 * $width, $height, substr($isi, 8, 2), 1, 0, 'C', 1);
                    $akhirX = $this->GetX();
                }
                $this->SetY($this->GetY());
                $this->SetX($akhirX);
                $this->Cell(4 / 100 * $width, $height, $_SESSION['lang']['jumlah'], 1, 1, 'C', 1);
            }

            public function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
            }
        }

        $pdf = new PDF('L', 'pt', 'Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        if ('rupiah' == $pilihan) {
            $pdf->SetFont('Arial', '', 5);
        } else {
            $pdf->SetFont('Arial', '', 7);
        }

        foreach ($dzArr as $qwe) {
            ++$no;
            $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $qwe['nm'], 1, 0, 'L', 1);
            $pdf->Cell(5 / 100 * $width, $height, $qwe['jb'], 1, 0, 'L', 1);
            $pdf->Cell(5 / 100 * $width, $height, $qwe['bg'], 1, 0, 'L', 1);
            $zxc = 0;
            foreach ($test as $ar => $isi) {
                if (0 != $qwe[$isi]) {
                    if ('rupiah' == $pilihan) {
                        $pdf->Cell(2.6 / 100 * $width, $height, number_format($qwe[$isi]), 1, 0, 'R', 1);
                    } else {
                        $pdf->Cell(2.6 / 100 * $width, $height, number_format($qwe[$isi], 1), 1, 0, 'R', 1);
                    }
                } else {
                    $pdf->Cell(2.6 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                $zxc += $qwe[$isi];
                $asd[$isi] += $qwe[$isi];
                $akhirX = $pdf->GetX();
            }
            if ('rupiah' == $pilihan) {
                $pdf->Cell(4 / 100 * $width, $height, number_format($zxc), 1, 1, 'R', 1);
            } else {
                $pdf->Cell(4 / 100 * $width, $height, number_format($zxc, 1), 1, 1, 'R', 1);
            }
        }
        $pdf->Cell(17 / 100 * $width, $height, 'Total', 1, 0, 'C', 1);
        foreach ($test as $ar => $isi) {
            if ('rupiah' == $pilihan) {
                $pdf->Cell(2.6 / 100 * $width, $height, number_format($asd[$isi]), 1, 0, 'R', 1);
            } else {
                $pdf->Cell(2.6 / 100 * $width, $height, number_format($asd[$isi], 1), 1, 0, 'R', 1);
            }

            $total += $asd[$isi];
            $akhirX = $pdf->GetX();
        }
        if ('rupiah' == $pilihan) {
            $pdf->Cell(4 / 100 * $width, $height, number_format($total), 1, 1, 'R', 1);
        } else {
            $pdf->Cell(4 / 100 * $width, $height, number_format($total, 1), 1, 1, 'R', 1);
        }

        $pdf->Output();

        break;
        case 'excel':
        if ('' == $periodeGaji) {
            echo 'warning: Periode tidak boleh kosong';
            exit();
        }

        $colatas = count($test) + 4;
        $stream .= "<table border='0'><tr><td colspan='".$colatas."' align=center>".strtoupper('Overtime Recapitulation').' : '.$nmOrg.' (dalam '.$pilihan.') '.$pilihan2."</td></tr>\r\n        <tr><td colspan='".$colatas."' align=center>".strtoupper($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2)."</td></tr><tr><td colspan='".$colatas."'>&nbsp;</td></tr></table>";
        $stream .= "<table cellspacing='1' border='1' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td bgcolor=#DEDEDE>No</td>\r\n        <td bgcolor=#DEDEDE>".$_SESSION['lang']['nama']."</td>\r\n        <td bgcolor=#DEDEDE>".$_SESSION['lang']['subbagian']."</td>\r\n        <td bgcolor=#DEDEDE>".$_SESSION['lang']['jabatan']."</td>\r\n        <td bgcolor=#DEDEDE>".$_SESSION['lang']['bagian'].'</td>';
        foreach ($test as $ar => $isi) {
            $qwe = date('D', strtotime($isi));
            $stream .= '<td bgcolor=#DEDEDE width=5px align=center>';
            if ('Sun' == $qwe) {
                $stream .= '<font color=red>'.substr($isi, 8, 2).'</font>';
            } else {
                $stream .= substr($isi, 8, 2);
            }

            $stream .= '</td>';
        }
        $stream .= "<td bgcolor=#DEDEDE>Jumlah</td></tr></thead>\r\n\r\n        <tbody>";
        foreach ($dzArr as $qwe) {
            ++$no;
            $stream .= '<tr class=rowcontent><td>'.$no."</td>\r\n                <td>".$qwe['nm']."</td>\r\n                <td>".$qwe['sb']."</td>\r\n                <td>".$qwe['jb']."</td>\r\n                <td>".$qwe['bg'].'</td>';
            $zxc = 0;
            foreach ($test as $ar => $isi) {
                if (0 != $qwe[$isi]) {
                    if ('rupiah' == $pilihan) {
                        $stream .= '<td align=right>'.number_format($qwe[$isi]).'</td>';
                    } else {
                        $stream .= '<td align=right>'.number_format($qwe[$isi], 1).'</td>';
                    }
                } else {
                    $stream .= '<td align=right></td>';
                }

                $zxc += $qwe[$isi];
                $asd[$isi] += $qwe[$isi];
            }
            if ('rupiah' == $pilihan) {
                $stream .= '<td align=right>'.number_format($zxc).'</td>';
            } else {
                $stream .= '<td align=right>'.number_format($zxc, 1).'</td>';
            }

            $stream .= '</tr>';
        }
        $stream .= "<thead class=rowheader>\r\n        <tr>\r\n        <td colspan=5>Total</td>";
        foreach ($test as $ar => $isi) {
            if ('rupiah' == $pilihan) {
                $stream .= '<td align=right>'.number_format($asd[$isi]).'</td>';
            } else {
                $stream .= '<td align=right>'.number_format($asd[$isi], 1).'</td>';
            }

            $total += $asd[$isi];
        }
        if ('rupiah' == $pilihan) {
            $stream .= '<td align=right>'.number_format($total).'</td>';
        } else {
            $stream .= '<td align=right>'.number_format($total, 1).'</td>';
        }

        $stream .= '</tbody></table>';
        $stream .= '</tbody></table>';
        $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        if ('' != $period) {
            $art = $period;
            $art = $art[1].$art[0];
        }

        if ('' != $periode) {
            $art = $periode;
            $art = $art[1].$art[0];
        }

        if ('' != $kdeOrg) {
            $kodeOrg = $kdeOrg;
        }

        if ('' != $kdOrg) {
            $kodeOrg = $kdOrg;
        }

        $nop_ = 'RekapLembur'.$art.'__'.$kodeOrg;
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
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
        case 'getTgl':
        $add = '';
        if ('' != $periode) {
            $tgl = $periode;
            $tanggal = $tgl[0].'-'.$tgl[1];
        } else {
            if ('' != $period) {
                $tgl = $period;
                $tanggal = $tgl[0].'-'.$tgl[1];
            }
        }

        if ('bulanan' == $pilihan2) {
            $add = " and jenisgaji='B'";
        }

        if ('harian' == $pilihan2) {
            $add = " and jenisgaji='H'";
        }

        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where \r\n            kodeorg='".substr($kdUnit, 0, 4)."' and periode='".$tanggal."' ".$add.'';
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai']).'###'.tanggalnormal($rTgl['tanggalsampai']);

        break;
        case 'getPeriode':
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji  where kodeorg='".$kdOrg."'";
        $optPeriode = "<option value''>".$_SESSION['lang']['pilihdata'].'</option>';
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
        }
        echo $optPeriode;

        break;
        default:
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

        return $dates_array;
    }

?>