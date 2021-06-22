<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showDetail':
        $headFrame = [$_SESSION['lang']['prestasi'], $_SESSION['lang']['absensi'], $_SESSION['lang']['material']];
        $contentFrame = [];
        $whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
        $whereKeg .= "kelompok='PNN'";
        $optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', $whereKeg);
        $whereOrg = "kodeorg like '%".$param['afdeling']."%'";
        $optOrg = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama', $whereOrg, 2, true);
        $firstOrg = end(array_reverse(array_keys($optOrg)));
        $optThTanam = makeOption($dbname, 'setup_blok', 'kodeorg,tahuntanam', "kodeorg='".end(array_reverse(array_keys($optOrg)))."'");
        $optBin = [1 => 'Ya', 0 => 'Tidak'];
        $thTanam = $optThTanam[end(array_reverse(array_keys($optOrg)))];
        $tgld = explode('-', $param['tanggal']);
        $sBjr = "SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal \r\n\t\t   FROM ".$dbname.'.`kebun_spbdt` a left join '.$dbname.".kebun_spbht b on \r\n\t\t   a.nospb=b.nospb where blok like '".substr($firstOrg, 0, 6)."%'\r\n\t\t   and tanggal <= '".tanggalsystem($param['tanggal'])."' group by tanggal order by tanggal desc limit 1";
        $qBjr = mysql_query($sBjr) ;
        $rBjr = mysql_fetch_assoc($qBjr);
        $rBjrCek = mysql_num_rows($qBjr);
        if (0 === (int) ($rBjr['bjr']) || 0 === $rBjrCek) {
            $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$firstOrg."' and tahunproduksi = '".$tgld[2]."'");
            $res = fetchData($query);
            if (!empty($res)) {
                $rBjr['bjr'] = $res[0]['bjr'];
            } else {
                $rBjr['bjr'] = 0;
            }
        }

        $where = "notransaksi='".$param['notransaksi']."'";
        $cols = 'nik,kodeorg,bjraktual,tahuntanam,tarif,norma,hasilkerja,hasilkerjakg,upahkerja,upahpremi,'.'penalti1,penalti2,penalti3,penalti4,penalti5,penalti6,penalti7,rupiahpenalty,luaspanen';
        $query = selectQuery($dbname, 'kebun_prestasi', $cols, $where);
        $data = fetchData($query);
        $nikList = '';
        foreach ($data as $row) {
            if ('' !== $nikList) {
                $nikList .= ',';
            }

            $nikList .= $row['nik'];
        }
        $whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and "."tipekaryawan in (3,4) and kodejabatan='120')";
        if (!empty($nikList)) {
            $whereKary .= ' or karyawanid in ('.$nikList.')';
        }

        $qKary = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan,nik,subbagian', $whereKary);
        $resKary = fetchData($qKary);
        $optKary = [];
        $optKary[] = '';
        foreach ($resKary as $kary) {
            $optKary[$kary['karyawanid']] = $kary['nik'].'-'.$kary['namakaryawan'].'('.$kary['subbagian'].')';
        }
        $firstKary = getFirstKey($optKary);
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$firstKary.' and tahun='.date('Y').' and idkomponen in (1)');
        $Umr = fetchData($qUMR);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['nik'] = $optKary[$row['nik']];
            $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
        }
        $arrData = ['satuan' => 'satuan', 'harian' => 'harian'];
        $theForm2 = new uForm('prestasiForm', 'Form Prestasi', 3);
        $theForm2->addEls('nik', $_SESSION['lang']['nik'], '', 'selectsearch', 'L', 25, $optKary);
        $theForm2->_elements[0]->_attr['onchange'] = 'updUpah()';
        $theForm2->addEls('kodeorg', $_SESSION['lang']['kodeorg'], '', 'selectsearch', 'L', 25, $optOrg, null, null, null, 'ftPrestasi_kodeorg');
        $theForm2->_elements[1]->_attr['onchange'] = 'updTahunTanam();';
        $theForm2->addEls('bjraktual', $_SESSION['lang']['bjraktual'], number_format($rBjr['bjr'], 2), 'textnum', 'R', 6);
        $theForm2->_elements[2]->_attr['disabled'] = 'disabled';
        $theForm2->addEls('tahuntanam', $_SESSION['lang']['tahuntanam'], $thTanam, 'textnum', 'R', 6);
        $theForm2->_elements[3]->_attr['disabled'] = 'disabled';
        $theForm2->addEls('tarif', $_SESSION['lang']['tarif'], '', 'select', 'L', 25, $arrData);
        $theForm2->_elements[4]->_attr['onchange'] = 'updUpah();';
        $theForm2->addEls('norma', $_SESSION['lang']['basiskg'], '0', 'textnum', 'R', 10);
        $theForm2->_elements[5]->_attr['disabled'] = 'disabled';
        $theForm2->addEls('hasilkerja', $_SESSION['lang']['hasilkerja'], '0', 'textnum', 'R', 10);
        $theForm2->_elements[6]->_attr['onblur'] = 'updUpah();';
        $theForm2->addEls('hasilkerjakg', $_SESSION['lang']['hasilkerjakg'], '0', 'textnum', 'R', 10);
        $theForm2->_elements[7]->_attr['disabled'] = 'disabled';
        $theForm2->_elements[7]->_attr['title'] = 'Hasil Kerja (JJG) * BJR [Kebun - Setup - Tabel BJR]';
        $theForm2->addEls('upahkerja', $_SESSION['lang']['upahkerja'], $Umr[0]['nilai'] / 25, 'textnum', 'R', 10);
        $theForm2->_elements[8]->_attr['disabled'] = 'disabled';
        $theForm2->addEls('upahpremi', $_SESSION['lang']['upahpremi'], '0', 'textnum', 'R', 10);
        $theForm2->_elements[9]->_attr['disabled'] = 'disabled';
        $theForm2->addEls('penalti1', $_SESSION['lang']['penalti1'], '0', 'textnum', 'R', 10);
        $theForm2->addEls('penalti2', $_SESSION['lang']['penalti2'], '0', 'textnum', 'R', 10);
        $theForm2->addEls('penalti3', $_SESSION['lang']['penalti3'], '0', 'textnum', 'R', 10);
        $theForm2->addEls('penalti4', $_SESSION['lang']['penalti4'], '0', 'textnum', 'R', 10);
        $theForm2->addEls('penalti5', $_SESSION['lang']['penalti5'], '0', 'textnum', 'R', 10);
        $theForm2->addEls('penalti6', $_SESSION['lang']['penalti6'], '0', 'textnum', 'R', 10);
        $theForm2->addEls('penalti7', $_SESSION['lang']['penalti7'], '0', 'textnum', 'R', 10);
        $theForm2->_elements[10]->_attr['onblur'] = "updDenda('BM');";
        $theForm2->_elements[11]->_attr['onblur'] = "updDenda('TP');";
        $theForm2->_elements[12]->_attr['onblur'] = "updDenda('TD');";
        $theForm2->_elements[14]->_attr['onblur'] = "updDenda('BT');";
        $theForm2->_elements[15]->_attr['onblur'] = "updDenda('PT');";
        $theForm2->_elements[16]->_attr['onblur'] = "updDenda('TM');";
        $theForm2->addEls('rupiahpenalty', $_SESSION['lang']['rupiahpenalty'], '0', 'textnum', 'R', 10);
        $theForm2->_elements[17]->_attr['disabled'] = 'disabled';
        $theForm2->addEls('luaspanen', $_SESSION['lang']['luaspanen'], '0', 'textnum', 'R', 10);
        $theTable2 = new uTable('prestasiTable', 'Tabel Prestasi', $cols, $data, $dataShow);
        $formTab2 = new uFormTable('ftPrestasi', $theForm2, $theTable2, null, ['notransaksi']);
        $formTab2->_target = 'kebun_slave_panen_detail';
        $formTab2->_noClearField = '##kodeorg##tahuntanam##bjraktual##norma##luaspanen';
        $formTab2->_noEnable = '##tahuntanam##bjraktual##upahkerja##upahpremi##rupiahpenalty##hasilkerjakg##norma';
        $formTab2->_defValue = '##upahkerja='.$Umr[0]['nilai'] / 25;
        echo "<fieldset><legend><b>Detail</b></legend><input type=checkbox id=allptnik onclick=allPtKaryawan('nik',this) title='Show All Employee in Company'>All Employee in Company</checkbox>";
        $formTab2->render();
        echo '</fieldset>';

        break;
    case 'add':
        $cols = ['nik', 'kodeorg', 'bjraktual', 'tahuntanam', 'tarif', 'norma', 'hasilkerja', 'hasilkerjakg', 'upahkerja', 'upahpremi', 'penalti1', 'penalti2', 'penalti3', 'penalti4', 'penalti5', 'penalti6', 'penalti7', 'rupiahpenalty', 'luaspanen', 'notransaksi', 'kodekegiatan', 'statusblok', 'pekerjaanpremi'];
        $data = $param;
        unset($data['numRow']);
        $data['kodekegiatan'] = '0';
        $data['statusblok'] = 0;
        $data['pekerjaanpremi'] = 0;
        $dmn = "notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek = makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik', $dmn);
        if (isset($optCek[$data['notransaksi']]) && '' !== $optCek[$data['notransaksi']]) {
            $warning = 'Data sudah ada';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if (0 === $data['upahkerja']) {
            $warning = 'Upah tidak boleh kosong';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if (0 === $data['luaspanen']) {
            $warning = 'Luas Panen(Ha)';
            echo 'error: Silakan mengisi '.$warning.'.';
            exit();
        }

        $query = insertQuery($dbname, 'kebun_prestasi', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['notransaksi'], $data['kodekegiatan'], $data['statusblok'], $data['pekerjaanpremi']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        unset($data['notransaksi']);
        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $dmn = "notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek = makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik', $dmn);
        if ('' !== $optCek[$data['notransaksi']]) {
            $warning = 'Data sudah ada';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if (0 === $data['upahkerja']) {
            $warning = 'Upah tidak boleh kosong';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if (0 === $data['luaspanen']) {
            $warning = 'Luas Panen(Ha)';
            echo 'error: Silakan mengisi '.$warning.'.';
            exit();
        }

        $where = "notransaksi='".$param['notransaksi']."' and nik='".$param['cond_nik']."' and kodeorg='".$param['cond_kodeorg']."'";
        $query = updateQuery($dbname, 'kebun_prestasi', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."' and nik='".$param['nik']."' and kodeorg='".$param['kodeorg']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_prestasi` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    case 'updTahunTanam':
        $query = selectQuery($dbname, 'setup_blok', 'kodeorg,tahuntanam', "kodeorg='".$param['kodeorg']."'");
        $res = fetchData($query);
        if (!empty($res)) {
            $thntnm = $res[0]['tahuntanam'];
        } else {
            $thntnm = 0;
        }

        $tgld = explode('-', $param['tanggal']);
        $sBjr = "SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal \r\n               FROM ".$dbname.'.`kebun_spbdt` a left join '.$dbname.".kebun_spbht b on \r\n               a.nospb=b.nospb where blok like '".substr($param['kodeorg'], 0, 6)."%'\r\n               and tanggal <= '".tanggalsystem($param['tanggal'])."' group by tanggal order by tanggal desc limit 1";
        $qBjr = mysql_query($sBjr) ;
        $rBjr = mysql_fetch_assoc($qBjr);
        $rBjrCek = mysql_num_rows($qBjr);
        if (0 === (int) ($rBjr['bjr']) || 0 === $rBjrCek) {
            $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$param['kodeorg']."' and tahunproduksi = '".$tgld[2]."'");
            $res = fetchData($query);
            if (!empty($res)) {
                $rBjr['bjr'] = $res[0]['bjr'];
            } else {
                exit('error: BJR is not exist');
            }
        }

        echo $thntnm.'####'.number_format($rBjr['bjr'], 2);

        break;
    case 'updBjr':
        $tahuntahuntahun = substr($param['notransaksi'], 0, 4);
        $hasilhasilhasil = $param['hasilkerja'];
        $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$param['kodeorg']."' and tahunproduksi = '".$tahuntahuntahun."'");
        $res = fetchData($query);
        if (!empty($res)) {
            $hasilhasil = $hasilhasilhasil * $res[0]['bjr'];
            echo $hasilhasil;
        } else {
            echo '0';
        }

        break;
    case 'updUpah':
        $dtr = "kodeorg='".$param['blok']."'";
        $optTopo = makeOption($dbname, 'setup_blok', 'kodeorg,topografi', $dtr);
        $hasilKg = $param['bjraktual'] * $param['jmlhJjg'];
        $firstKary = $param['nik'];
        $tgl = explode('-', $param['tanggal']);
        $tnggl = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$firstKary.' and tahun='.$tgl[2].' and idkomponen in (1)');
        $Umr = fetchData($qUMR);
        $uphHarian = $Umr[0]['nilai'] / 25;
        if (0 === $uphHarian) {
            exit("error: Don't have basic salary !!");
        }

        $qwe = date('D', strtotime($tnggl));
        $dhr = "regional='".$_SESSION['empl']['regional']."' and tanggal='".$tnggl."'";
        $optHariLbr = makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal', $dhr);
        $regData = $_SESSION['empl']['regional'];
        $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."'";
        $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
        $optBasis = makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis', $dmn);
        if ('B1' === $optTopo[$param['blok']]) {
            $optIns = makeOption($dbname, 'kebun_5basispanen', 'jenis,rptopografi', $dmn);
        }

        $lstert = 0;
        $sTarif = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n                         kodeorg='".$_SESSION['empl']['regional']."' and jenis='satuan' order by bjr desc";
        $qTarif = mysql_query($sTarif) ;
        while ($rTarif = mysql_fetch_assoc($qTarif)) {
            $rpLbh[$rTarif['bjr']] = $rTarif['rplebih'];
            $basisPanen[$rTarif['bjr']] = $rTarif['basisjjg'];
            $lstBjr[] = $rTarif['bjr'];
            $lstBjr2[$lstert] = $rTarif['bjr'];
            ++$lstert;
        }
        if ('Sun' === $qwe || isset($optHariLbr[$_SESSION['empl']['regional']]) && '' !== $optHariLbr[$_SESSION['empl']['regional']]) {
            $basis = 0;
            if ('KALTENG' === $_SESSION['empl']['regional']) {
                switch ($param['tarif']) {
                    case 'harian':
                        $basis = $optBasis[$param['tarif']];
                        if (0 === $basis) {
                            $upah = $uphHarian;
                            $insentif = $optIns[$param['tarif']];
                        }

                        if (0 !== $basis) {
                            if (1 === $optDenda[$param['tarif']]) {
                                if ($hasilKg < $basis) {
                                    $upah = $hasilKg / $basis * $uphHarian;
                                } else {
                                    if ($basis < $hasilKg) {
                                        if (0 !== $optRp[$param['tarif']]) {
                                            $upah = $uphHarian + $optRp[$param['tarif']] * ($hasilKg - $basis);
                                        } else {
                                            $upah = $uphHarian;
                                        }
                                    } else {
                                        $upah = $uphHarian;
                                    }
                                }
                            } else {
                                $upah = $optRp[$param['tarif']] * $hasilKg;
                            }

                            $insentif = $optIns[$param['tarif']];
                        }

                        break;
                    case 'satuan':
                        $upah = $optRp[$param['tarif']] * $hasilKg;
                        $insentif = $optIns[$param['tarif']];

                        break;
                }
            } else {
                if ('SUMUT' === $_SESSION['empl']['regional']) {
                    switch ($param['tarif']) {
                        case 'harian':
                            $upah = $uphHarian * $optRp[$param['tarif']];
                            $insentif = 0;
                            $basis = $optBasis[$param['tarif']];

                            break;
                        case 'satuan':
                            $MaxRow = count($lstBjr);
                            foreach ($lstBjr as $lstRow => $dtIsiBjr) {
                                if (0 === $lstRow) {
                                    if ($dtIsiBjr < $param['bjraktual']) {
                                        $hsl = $rpLbh[$dtIsiBjr] * $hasilKg;
                                        $dtbjr = $dtIsiBjr;

                                        break;
                                    }
                                } else {
                                    if ($lstRow !== $MaxRow) {
                                        $leapdt = $lstRow + 1;
                                        if ($param['bjraktual'] === $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                                            $hsl = $rpLbh[$dtIsiBjr] * $hasilKg;
                                            $dtbjr = $dtIsiBjr;

                                            break;
                                        }
                                    } else {
                                        $dmin = $dtIsiBjr - 1;
                                        $dtbjr = $dtIsiBjr;
                                        if ($dmin <= $param['bjraktual']) {
                                            $hsl = $rpLbh[$dtIsiBjr] * $hasilKg;

                                            break;
                                        }

                                        $hsl = $param['jmlhJjg'] / $basisPanen[$dtIsiBjr] * $uphHarian;
                                    }
                                }
                            }
                            $dmn = "kodeorg='".$regional."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
                            $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
                            $optBasis = makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg', $dmn);
                            $optDenda = makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis', $dmn);
                            $upah = $hsl;
                            $insentif = 0;
                            $basis = $optBasis[$param['tarif']];

                            break;
                    }
                }
            }
        } else {
            switch ($_SESSION['empl']['regional']) {
                case 'KALTENG':
                    switch ($param['tarif']) {
                        case 'harian':
                            $basis = $optBasis[$param['tarif']];
                            if (0 === $basis) {
                                $upah = $uphHarian;
                                $insentif = $optIns[$param['tarif']];
                            }

                            if (0 !== $basis) {
                                if (1 === $optDenda[$param['tarif']]) {
                                    if ($hasilKg < $basis) {
                                        $upah = $hasilKg / $basis * $uphHarian;
                                    } else {
                                        if ($basis < $hasilKg) {
                                            if (0 !== $optRp[$param['tarif']]) {
                                                $upah = $uphHarian + $optRp[$param['tarif']] * ($hasilKg - $basis);
                                            } else {
                                                $upah = $uphHarian;
                                            }
                                        } else {
                                            $upah = $uphHarian;
                                        }
                                    }
                                } else {
                                    $upah = $optRp[$param['tarif']] * $hasilKg;
                                }

                                $insentif = $optIns[$param['tarif']];
                            }

                            break;
                        case 'satuan':
                            $basis = $optBasis[$param['tarif']];
                            $upah = $hasilKg * $optRp[$param['tarif']];
                            $insentif = $optIns[$param['tarif']];

                            break;
                    }

                    break;
                case 'KALIMANTAN':
                    switch ($param['tarif']) {
                        case 'harian':
                            $basis = $optBasis[$param['tarif']];
                            if ('1' === $optDenda[$param['tarif']]) {
                                if ($param['jmlhJjg'] < $basis) {
                                    $upah = $param['jmlhJjg'] / $basis * $uphHarian;
                                } else {
                                    $upah = $uphHarian;
                                }
                            } else {
                                $upah = $uphHarian;
                            }

                            $insentif = 0;

                            break;
                        case 'satuan':
                            $insentif = 0;
                            $MaxRow = count($lstBjr);
                            foreach ($lstBjr as $lstRow => $dtIsiBjr) {
                                if (0 === $lstRow) {
                                    if ($dtIsiBjr < $param['bjraktual']) {
                                        $upah = $rpLbh[$dtIsiBjr] * $hasilKg;
                                        $dtbjr = $dtIsiBjr;

                                        break;
                                    }
                                } else {
                                    if ($lstRow !== $MaxRow) {
                                        $leapdt = $lstRow + 1;
                                        if ($param['bjraktual'] === $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                                            $upah = $rpLbh[$dtIsiBjr] * $hasilKg;
                                            $dtbjr = $dtIsiBjr;

                                            break;
                                        }
                                    } else {
                                        $dmin = $dtIsiBjr - 1;
                                        $dtbjr = $dtIsiBjr;
                                        if ($dmin <= $param['bjraktual']) {
                                            $upah = $rpLbh[$dtIsiBjr] * $hasilKg;

                                            break;
                                        }

                                        $upah = $param['jmlhJjg'] / $basisPanen[$dtIsiBjr] * $uphHarian;
                                    }
                                }
                            }
                            $dmn = "kodeorg='".$regional."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
                            $optBasis = makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg', $dmn);
                            $basis = $optBasis[$param['tarif']];
                            $insentif = 0;
                            if ('1' === $optDenda[$param['tarif']] && $param['jmlhJjg'] < $basis) {
                                $upah = $param['jmlhJjg'] / $basis * $uphHarian;
                            }

                            break;
                    }

                    break;
            }
        }

        echo $upah.'####'.number_format($basis, 0).'####'.$insentif.'####'.$hasilKg;

        break;
    case 'updDenda':
        if ('KALTENG' === $_SESSION['empl']['regional']) {
            $dtbjr = 0;
        } else {
            $lstert = 0;
            $sTarif = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n                         kodeorg='".$_SESSION['empl']['regional']."' and jenis='".$param['tarif']."' order by bjr desc";
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
                if (0 === $lstRow) {
                    if ($dtIsiBjr < $param['bjraktual']) {
                        $dtbjr = $dtIsiBjr;

                        break;
                    }
                } else {
                    if ($lstRow !== $MaxRow) {
                        $leapdt = $lstRow + 1;
                        if ($param['bjraktual'] === $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                            $dtbjr = $dtIsiBjr;

                            break;
                        }
                    } else {
                        $dmin = $dtIsiBjr - 1;
                        if ($dmin <= $param['bjraktual']) {
                            $dtbjr = $dtIsiBjr;

                            break;
                        }

                        $dtbjr = 0;
                    }
                }
            }
        }

        $regData = $_SESSION['empl']['regional'];
        if ('KALTENG' === $_SESSION['empl']['regional']) {
            $afd = substr($param['blok'], 0, 6);
            $dmn = "kodeorg='".$afd."'";
            $optCek = makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis', $dmn);
            if ('' !== $optCek[$afd]) {
                $regData = $afd;
            }
        }

        $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
        if ('KALTENG' === $_SESSION['empl']['regional']) {
            $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."'";
        }

        if ('H12E02' === $regData) {
            $dmn = "kodeorg='".$_SESSION['empl']['regional']."' and jenis='satuan'";
        }

        $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5denda', 'kode,jumlah');
        for ($der = 1; $der < 8; ++$der) {
            if (1 === $der) {
                $det = 'BM';
                $dend = $_POST['isiDt'][$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
            } else {
                if (3 === $der) {
                    $det = 'TD';
                    $dend = $_POST['isiDt'][$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                } else {
                    if (5 === $der) {
                        $det = 'BT';
                        $dend = $_POST['isiDt'][$der] / $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                    } else {
                        $det = 'TP';
                        $dend = $_POST['isiDt'][$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                    }
                }
            }

            $denda += $dend;
        }
        echo $denda;

        break;
}

?>