<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$proses = $_GET['proses'];
$tglTerakhir = [];
$tgl = explode('-', $param['tanggal']);
$tglAbis = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
switch ($proses) {
    case 'list':
        $dis = '';
        if ('26' == $param['jenis']) {
            $dis = "'disabled'=>'disabled'";
        }

        $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and\r\n                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            $aktif = true;
        } else {
            $aktif = false;
        }

        if (true == $aktif) {
            exit('Error: Accounting period has been closed');
        }

        $qPeriod = selectQuery($dbname, 'sdm_5periodegaji', 'tanggalmulai,tanggalsampai,jenisgaji', "periode='".$param['periodegaji']."' and jenisgaji='".substr($param['jnsGaji'], 0, 1)."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'");
        $resPeriod = fetchData($qPeriod);
        $tglH1 = '';
        $tglH2 = '';
        $tglB1 = '';
        $tglB2 = '';
        foreach ($resPeriod as $row) {
            if ('Harian' == $param['jnsGaji']) {
                isset($row['tanggalmulai']);
                (isset($row['tanggalmulai']) ? ($tglH1 = $row['tanggalmulai']) : ($tglH1 = ''));
                isset($row['tanggalsampai']);
                (isset($row['tanggalsampai']) ? ($tglH2 = $row['tanggalsampai']) : ($tglH2 = ''));
            } else {
                isset($row['tanggalmulai']);
                (isset($row['tanggalmulai']) ? ($tglB1 = $row['tanggalmulai']) : ($tglB1 = ''));
                isset($row['tanggalsampai']);
                (isset($row['tanggalsampai']) ? ($tglB2 = $row['tanggalsampai']) : ($tglB2 = ''));
            }
        }
        if ('' != $tglB2) {
            $dtTgl = $tglB2;
        } else {
            $dtTgl = $tglH2;
        }

        $tglTerakhir = findfirstandlastday($dtTgl);
        if ('Bulanan' == $param['jnsGaji']) {
            $cols1 = 'karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,'."COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/365.25,3),0) as masakerja";
            $where1 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and sistemgaji='".$param['jnsGaji']."' and \r\n                (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar is NULL)";
            $query1 = selectQuery($dbname, 'datakaryawan', $cols1, $where1);
            $resBln = fetchData($query1);
        } else {
            if ('Harian' == $param['jnsGaji']) {
                $cols2 = 'karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,'."COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/365.25,3),0) as masakerja";
                $where2 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and sistemgaji='".$param['jnsGaji']."' and\r\n            (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar is NULL)";
                $query2 = selectQuery($dbname, 'datakaryawan', $cols2, $where2);
                $resHrn = fetchData($query2);
            }
        }

        $whereGj = 'karyawanid in (';
        $first = true;
        $tmpGj = [];
        if ('Bulanan' == $param['jnsGaji']) {
            foreach ($resBln as $key => $row) {
                $sGaji = 'select karyawanid,idkomponen,pengali,jumlah from '.$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (".$param['jenis'].") and kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
                $qGaji = mysql_query($sGaji);
                $rGaji = mysql_fetch_assoc($qGaji);
                if ('' != $rGaji['jumlah'] && 0 != $rGaji['jumlah']) {
                    $tmpGj[] = $rGaji;
                } else {
                    $sGaji2 = 'select karyawanid,idkomponen,sum(jumlah) as jumlah from '.$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (select distinct id from ".$dbname.".sdm_ho_component where `plus`=1 and `type`='basic' and `lock` =1) and tahun=".$param['tahun'];
                    $qGaji2 = mysql_query($sGaji2);
                    $rGaji2 = mysql_fetch_assoc($qGaji2);
                    $tmpGj[] = $rGaji2;
                }
            }
            echo $sGaji;
        } else {
            if ('Harian' == $param['jnsGaji']) {
                foreach ($resHrn as $row) {
                    $sGaji = 'select karyawanid,idkomponen,pengali,jumlah from '.$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (".$param['jenis'].") and kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
                    $qGaji = mysql_query($sGaji);
                    $rGaji = mysql_fetch_assoc($qGaji);
                    if ('' != $rGaji['jumlah'] || 0 != $rGaji['jumlah']) {
                        $tmpGj[] = $rGaji;
                    } else {
                        $sGaji2 = 'select karyawanid,idkomponen,sum(jumlah) as jumlah from '.$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (select distinct id from ".$dbname.".sdm_ho_component where `plus`=1 and `type`='basic' and `lock` =1) and tahun=".$param['tahun'];
                        $qGaji2 = mysql_query($sGaji2);
                        $rGaji2 = mysql_fetch_assoc($qGaji2);
                        $tmpGj[] = $rGaji2;
                    }
                }
            }
        }

        $resGj = [];
        foreach ($tmpGj as $row) {
            $resGj[$row['karyawanid']][1]['jumlah'] = $row['jumlah'];
            $resGj[$row['karyawanid']][$row['idkomponen']]['pengali'] = $row['pengali'];
        }
        $optKary = [];
        if ('Bulanan' == $param['jnsGaji']) {
            foreach ($resBln as $row) {
                $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
                if ('' == $row['subbagian'] || null == $row['subbagian'] || '0' == $row['subbagian']) {
                    $optKary[$row['karyawanid']]['kodeorganisasi'] = $_SESSION['empl']['lokasitugas'];
                } else {
                    $optKary[$row['karyawanid']]['kodeorganisasi'] = $row['subbagian'];
                }

                $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
            }
        } else {
            if ('Harian' == $param['jnsGaji']) {
                foreach ($resHrn as $row) {
                    $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                    $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
                    if ('' == $row['subbagian'] || null == $row['subbagian'] || '0' == $row['subbagian']) {
                        $optKary[$row['karyawanid']]['kodeorganisasi'] = $_SESSION['empl']['lokasitugas'];
                    } else {
                        $optKary[$row['karyawanid']]['kodeorganisasi'] = $row['subbagian'];
                    }

                    $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                    $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                    $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                    $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
                }
            }
        }

        $optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
        $optNmorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
        $optBagian = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
        $optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
        $data = [];
        $i = 0;
        $dataExist = [];
        foreach ($resGj as $kary => $row) {
            $data[$i] = ['id' => $kary, 'tipe' => $optKary[$kary]['tipe'], 'lokasitugas' => $optKary[$kary]['kodeorganisasi'], 'bagian' => $optKary[$kary]['bagian'], 'kodejabatan' => $optKary[$kary]['kodejabatan'], 'tmk' => $optKary[$kary]['tmk'], 'masakerja' => $optKary[$kary]['masakerja'], 'gajipokok' => $row[1]['jumlah']];
            $msKerja = $optKary[$kary]['masakerja'] * 12;
            if (isset($row[$param['jenis']])) {
                $dataExist[$i] = 1;
                if (0 != $row[$param['jenis']]['pengali']) {
                    $data[$i]['gajipokok'] = $row[1]['jumlah'] / $row[$param['jenis']]['pengali'];
                }

                $data[$i]['pengali'] = $row[$param['jenis']]['pengali'];
            } else {
                $dataExist[$i] = 0;
                if ($msKerja < 3) {
                    $row[1]['pengali'] = 0;
                } else {
                    if ($msKerja < 12 && 3 <= $msKerja) {
                        $data[$i]['gajipokok'] = $row[1]['jumlah'];
                        $z = floor($msKerja);
                        $z = $msKerja - $z;
                        if (0.5 < $z) {
                            $row[1]['pengali'] = ceil($msKerja) / 12;
                        } else {
                            $row[1]['pengali'] = floor($msKerja) / 12;
                        }
                    } else {
                        if (12 <= $msKerja) {
                            $row[1]['pengali'] = 1;
                        }
                    }
                }

                $data[$i]['pengali'] = $row[1]['pengali'];
            }

            $data[$i]['jumlah'] = $data[$i]['gajipokok'] * $data[$i]['pengali'];
            ++$i;
        }
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['id'] = $optKary[$row['id']]['nama'];
            $dataShow[$key]['tipe'] = $optTipe[$row['tipe']];
            $dataShow[$key]['lokasitugas'] = $optNmorg[$row['lokasitugas']];
            $dataShow[$key]['bagian'] = $optBagian[$row['bagian']];
            $dataShow[$key]['kodejabatan'] = $optJabatan[$row['kodejabatan']];
            $dataShow[$key]['tmk'] = tanggalnormal($row['tmk']);
        }
        $tmpColTab = 'namakaryawan,tipekaryawan,lokasitugas,bagian,kodejabatan,tmk,masakerja,thr,pengali,jumlah,action';
        $colTab = explode(',', $tmpColTab);
        $table = '';
        $table .= "<div style=overflow:auto;height:800px;><table class='sortable'>";
        $table .= "<thead><tr class='rowheader'>";
        foreach ($colTab as $row) {
            $table .= "<td align='center'>".$_SESSION['lang'][$row].'</td>';
        }
        $table .= '</tr></thead>';
        $table .= '<tbody>';
        foreach ($dataShow as $key => $row) {
            if ('' != $row['id']) {
                ++$listdt;
                if (1 == $dataExist[$key]) {
                    $table .= "<tr id='tr_".$listdt."' class='rowcontent' style='background:green'>";
                } else {
                    $table .= "<tr id='tr_".$listdt."' class='rowcontent'>";
                }

                foreach ($row as $head => $cont) {
                    $table .= "<td id='".$head.'_'.$listdt."' value='".$data[$key][$head]."'>";
                    if ('pengali' == $head) {
                        if ('26' != $param['jenis']) {
							$flag_fleksible_modify_pengali = 'Y'; //kalo mao di disable set jadi 'N'
							if( $flag_fleksible_modify_pengali == "Y"){
								$table .= makeElement($head.'_'.$listdt.'_text', 'textnum', $cont, ['style' => 'width:70px;text-align:center', 'onkeyup' => 'updJumlah('.$listdt.')']);
							}else{
								$table .= makeElement($head.'_'.$listdt.'_text', 'textnum', $cont, ['style' => 'width:70px;text-align:center', 'onkeyup' => 'updJumlah('.$listdt.')', 'disabled' => 'disabled']);
							}
                            
                            
                        } else {
                            $table .= makeElement($head.'_'.$listdt.'_text', 'textnum', $cont, ['style' => 'width:70px;text-align:center', 'onkeyup' => 'updJumlah('.$listdt.')']);
                        }
                    } else {
                        if ('jumlah' == $head) {
                            $table .= makeElement($head.'_'.$listdt.'_text', 'textnum', $cont, ['style' => 'width:70px']);
                        } else {
                            $table .= $cont;
                        }
                    }

                    $table .= '</td>';
                }
                $table .= '<td>';
                $table .= makeElement('save_'.$listdt, 'btn', $_SESSION['lang']['save'], ['onclick' => 'saveItDt('.$listdt.')']);
                $table .= '</td>';
                $table .= '</tr>';
            }
        }
        $table .= '<tr><td colspan=12 align=center><button class=mybutton onclick=saveAll('.$listdt.',1)>'.$_SESSION['lang']['save'].' '.$_SESSION['lang']['all'].'</button></td></tr>';
        $table .= '</tbody>';
        $table .= '</table></div>';
        echo $table;

        break;
    case 'post':
        $data = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $param['id'], 'idkomponen' => $param['jenis'], 'jumlah' => $param['jumlah'], 'pengali' => $param['pengali']];
        $dataUpd = ['jumlah' => $param['jumlah'], 'pengali' => $param['pengali']];
        $sCek = 'select distinct tutupbuku from '.$dbname.".setup_periodeakuntansi where kodeorg='".$data['kodeorg']."' and periode='".$data['periodegaji']."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_assoc($qCek);
        if (0 != $rCek['tutupbuku']) {
            echo 'warning: Data can not be saved, accunting period has closed';
            exit();
        }

        $query1 = insertQuery($dbname, 'sdm_gaji', $data);
        if (!mysql_query($query1)) {
            $tmpErr = mysql_error();
            $where = "kodeorg='".$data['kodeorg']."' and periodegaji='".$data['periodegaji']."' and karyawanid='".$data['karyawanid']."' and idkomponen=".$data['idkomponen'];
            $query2 = updateQuery($dbname, 'sdm_gaji', $dataUpd, $where);
            if (!mysql_query($query2)) {
                echo 'Insert DB Error : '.$tmpErr."\n";
                echo 'Update DB Error : '.mysql_error()."\n";
            }
        }

        break;
    case 'excel':
        $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and\r\n                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            $aktif = true;
        } else {
            $aktif = false;
        }

        if (true == $aktif) {
            exit('Error: Accounting period has been closed');
        }

        $param = $_GET;
        $tgl = explode('-', $param['tanggal']);
        $tglAbis = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $periode = $param['periodegaji'];
        $qPeriod = selectQuery($dbname, 'sdm_5periodegaji', 'tanggalmulai,tanggalsampai,jenisgaji', "periode='".$param['periodegaji']."' and jenisgaji='".substr($param['jnsGaji'], 0, 1)."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'");
        $resPeriod = fetchData($qPeriod);
        $tglH1 = '';
        $tglH2 = '';
        $tglB1 = '';
        $tglB2 = '';
        foreach ($resPeriod as $row) {
            if ('Harian' == $param['jnsGaji']) {
                isset($row['tanggalmulai']);
                (isset($row['tanggalmulai']) ? ($tglH1 = $row['tanggalmulai']) : ($tglH1 = ''));
                isset($row['tanggalsampai']);
                (isset($row['tanggalsampai']) ? ($tglH2 = $row['tanggalsampai']) : ($tglH2 = ''));
            } else {
                isset($row['tanggalmulai']);
                (isset($row['tanggalmulai']) ? ($tglB1 = $row['tanggalmulai']) : ($tglB1 = ''));
                isset($row['tanggalsampai']);
                (isset($row['tanggalsampai']) ? ($tglB2 = $row['tanggalsampai']) : ($tglB2 = ''));
            }
        }
        if ('' != $tglB2) {
            $dtTgl = $tglB2;
        } else {
            $dtTgl = $tglH2;
        }

        $tglTerakhir = findfirstandlastday($dtTgl);
        if ('Bulanan' == $param['jnsGaji']) {
            $cols1 = 'karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,'."COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/365.25,3),0) as masakerja";
            $where1 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and sistemgaji='".$param['jnsGaji']."' and \r\n                (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar is NULL)";
            $query1 = selectQuery($dbname, 'datakaryawan', $cols1, $where1);
            $resBln = fetchData($query1);
        } else {
            if ('Harian' == $param['jnsGaji']) {
                $cols2 = 'karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,'."COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/365.25,3),0) as masakerja";
                $where2 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and sistemgaji='".$param['jnsGaji']."' and\r\n            (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar is NULL)";
                $query2 = selectQuery($dbname, 'datakaryawan', $cols2, $where2);
                $resHrn = fetchData($query2);
            }
        }

        $whereGj = 'karyawanid in (';
        $first = true;
        $tmpGj = [];
        if ('Bulanan' == $param['jnsGaji']) {
            foreach ($resBln as $key => $row) {
                $sGaji = 'select karyawanid,idkomponen,pengali,jumlah from '.$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (".$param['jenis'].") and kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
                $qGaji = mysql_query($sGaji);
                $rGaji = mysql_fetch_assoc($qGaji);
                if ('' != $rGaji['jumlah'] && 0 != $rGaji['jumlah']) {
                    $tmpGj[] = $rGaji;
                } else {
                    $sGaji2 = 'select karyawanid,idkomponen,sum(jumlah) as jumlah from '.$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (select distinct id from ".$dbname.".sdm_ho_component where `plus`=1 and `type`='basic' and `lock` =1)  and tahun=".$param['tahun'];
                    $qGaji2 = mysql_query($sGaji2);
                    $rGaji2 = mysql_fetch_assoc($qGaji2);
                    $tmpGj[] = $rGaji2;
                }
            }
        } else {
            if ('Harian' == $param['jnsGaji']) {
                foreach ($resHrn as $row) {
                    $sGaji = 'select karyawanid,idkomponen,pengali,jumlah from '.$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (".$param['jenis'].") and kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
                    $qGaji = mysql_query($sGaji);
                    $rGaji = mysql_fetch_assoc($qGaji);
                    if ('' != $rGaji['jumlah']) {
                        $tmpGj[] = $rGaji;
                    } else {
                        $sGaji2 = 'select karyawanid,idkomponen,sum(jumlah) as jumlah from '.$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'\r\n                    and idkomponen in (select distinct id from ".$dbname.".sdm_ho_component where `plus`=1 and `type`='basic' and `lock` =1)  and tahun=".$param['tahun'];
                        $qGaji2 = mysql_query($sGaji2);
                        $rGaji2 = mysql_fetch_assoc($qGaji2);
                        $tmpGj[] = $rGaji2;
                    }
                }
            }
        }

        $resGj = [];
        foreach ($tmpGj as $row) {
            $resGj[$row['karyawanid']][1]['jumlah'] = $row['jumlah'];
            $resGj[$row['karyawanid']][$row['idkomponen']]['pengali'] = $row['pengali'];
        }
        $optKary = [];
        if ('Bulanan' == $param['jnsGaji']) {
            foreach ($resBln as $row) {
                $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
                if ('' == $row['subbagian'] || null == $row['subbagian'] || '0' == $row['subbagian']) {
                    $optKary[$row['karyawanid']]['kodeorganisasi'] = $_SESSION['empl']['lokasitugas'];
                } else {
                    $optKary[$row['karyawanid']]['kodeorganisasi'] = $row['subbagian'];
                }

                $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
            }
        } else {
            if ('Harian' == $param['jnsGaji']) {
                foreach ($resHrn as $row) {
                    $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                    $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
                    $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
                    if ('' == $row['subbagian'] || null == $row['subbagian'] || '0' == $row['subbagian']) {
                        $optKary[$row['karyawanid']]['kodeorganisasi'] = $_SESSION['empl']['lokasitugas'];
                    } else {
                        $optKary[$row['karyawanid']]['kodeorganisasi'] = $row['subbagian'];
                    }

                    $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                    $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                    $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                    $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
                }
            }
        }

        $optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
        $optNmorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
        $optBagian = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
        $optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
        $data = [];
        $i = 0;
        $dataExist = [];
        foreach ($resGj as $kary => $row) {
            $data[$i] = ['id' => $kary, 'tipe' => $optKary[$kary]['tipe'], 'lokasitugas' => $optKary[$kary]['kodeorganisasi'], 'kodejabatan' => $optKary[$kary]['kodejabatan'], 'bagian' => $optKary[$kary]['bagian'], 'tmk' => $optKary[$kary]['tmk'], 'masakerja' => $optKary[$kary]['masakerja'], 'gajipokok' => $row[1]['jumlah']];
            $msKerja = $optKary[$kary]['masakerja'] * 12;
            if (isset($row[$param['jenis']])) {
                $dataExist[$i] = 1;
                if (0 != $row[$param['jenis']]['pengali']) {
                    $data[$i]['gajipokok'] = $row[1]['jumlah'] / $row[$param['jenis']]['pengali'];
                }

                $data[$i]['pengali'] = $row[$param['jenis']]['pengali'];
            } else {
                $dataExist[$i] = 0;
                if ($msKerja < 3) {
                    $row[1]['pengali'] = 0;
                } else {
                    if ($msKerja < 12 && 3 <= $msKerja) {
                        $data[$i]['gajipokok'] = $row[1]['jumlah'];
                        $z = floor($msKerja);
                        $z = $msKerja - $z;
                        if (0.5 < $z) {
                            $row[1]['pengali'] = ceil($msKerja) / 12;
                        } else {
                            $row[1]['pengali'] = floor($msKerja) / 12;
                        }
                    } else {
                        if (12 <= $msKerja) {
                            $row[1]['pengali'] = 1;
                        }
                    }
                }

                $data[$i]['pengali'] = $row[1]['pengali'];
            }

            $data[$i]['jumlah'] = $data[$i]['gajipokok'] * $data[$i]['pengali'];
            ++$i;
        }
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['id'] = $optKary[$row['id']]['nama'];
            $dataShow[$key]['tipe'] = $optTipe[$row['tipe']];
            $dataShow[$key]['lokasitugas'] = $optNmorg[$row['lokasitugas']];
            $dataShow[$key]['kodejabatan'] = $optJabatan[$row['kodejabatan']];
            $dataShow[$key]['bagian'] = $optBagian[$row['bagian']];
            $dataShow[$key]['tmk'] = $row['tmk'];
        }
        $tmpColTab = 'namakaryawan,tipekaryawan,lokasitugas,kodejabatan,bagian,tmk,masakerja,gajipokok,pengali,jumlah';
        $colTab = explode(',', $tmpColTab);
        $table = "<table cellspacing=1 cellpading=0 border=0><tr><td colspan=7 align=center>\r\n            ".$_SESSION['lang']['rkpThr'].','.$_SESSSION['lang']['periode'].' :'.$periode.'<br /> '.$_SESSION['lang']['unit'].' :'.$_SESSION['empl']['lokasitugas'].'</td></tr></table>';
        $table .= "<table class='sortable' border=1>";
        $table .= "<thead><tr class='rowheader'>";
        foreach ($colTab as $row) {
            $table .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang'][$row].'</td>';
        }
        $table .= '</tr></thead>';
        $table .= '<tbody>';
        foreach ($dataShow as $key => $row) {
            if ('' != $row['id']) {
                $table .= "<tr id='tr_".$key."' class='rowcontent'>";
                foreach ($row as $head => $cont) {
                    $table .= "<td id='".$head.'_'.$key."' value='".$data[$key][$head]."'>";
                    $table .= $cont;
                    $table .= '</td>';
                }
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';
        $table .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'RekapThr_bonus_'.$periode.'__'.$_SESSION['empl']['lokasitugas'];
        if (0 < strlen($table)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $table)) {
                echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can't convert to excel format');\r\n                    </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls';\r\n                    </script>";
            closedir($handle);
        }

        break;
}
function findFirstAndLastDay($anyDate)
{
    list($yr, $mn, $dt) = preg_split('/-/D', $anyDate);
    $timeStamp = mktime(0, 0, 0, $mn, 1, $yr);
    $firstDay = date('D', $timeStamp);
    list($y, $m, $t) = preg_split('/-/D', date('Y-m-t', $timeStamp));
    $lastDayTimeStamp = mktime(0, 0, 0, $m, $t, $y);
    $lastDay = date('Y-m-d', $lastDayTimeStamp);

    return [(string) $firstDay, (string) $lastDay];
}

?>