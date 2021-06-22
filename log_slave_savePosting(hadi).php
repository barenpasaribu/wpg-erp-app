<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
if (isTransactionPeriod()) {
    $tipetransaksi = $_POST['tipetransaksi'];
    $tanggal = $_POST['tanggal'];
    $kodebarang = $_POST['kodebarang'];
    $satuan = $_POST['satuan'];
    $jumlah = $_POST['jumlah'];
    $kodept = $_POST['kodept'];
    $gudangx = $_POST['gudangx'];
    $untukpt = $_POST['untukpt'];
    $gudang = $_POST['gudang'];
    $blok = $_POST['kodeblok'];
    $notransaksi = $_POST['notransaksi'];
    $user = $_SESSION['standard']['userid'];
    $hargasatuan = $_POST['hargasatuan'];
    $nopo = $_POST['nopo'];
    $supplier = $_POST['supplier'];
    $kodekegiatan = $_POST['kodekegiatan'];
    $kodemesin = $_POST['kodemesin'];
    $statussaldo = 0;
    $str = 'select  statussaldo from '.$dbname.".log_transaksidt \r\n            where notransaksi='".$notransaksi."'\r\n                and kodebarang='".$kodebarang."'\r\n                and kodeblok='".$blok."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $statussaldo = $res->statussaldo;
    }
    if ($statussaldo < 0 && $statussaldo!="") {
        exit(0);
    }

    $periode = $_SESSION['gudang'][$gudang]['tahun'].'-'.$_SESSION['gudang'][$gudang]['bulan'];
    $str = 'select tutupbuku from '.$dbname.".setup_periodeakuntansi where periode='".$periode."' and kodeorg='".substr($gudang, 0, 4)."'";
    $res = mysql_query($str);
    $close = 0;
    while ($bar = mysql_fetch_object($res)) {
        $close = $bar->tutupbuku;
    }
    if ($close=='1') {
        exit(' Error: Accounting Period has been closed.');
    }

    if ($gudangx!='' && substr($gudang, 0, 4) != substr($gudangx, 0, 4)) {
        $str = 'select tutupbuku from '.$dbname.".setup_periodeakuntansi where periode='".$periode."' and kodeorg='".substr($gudangx, 0, 4)."'";
        $res = mysql_query($str);
        $close = 0;
        while ($bar = mysql_fetch_object($res)) {
            $close = $bar->tutupbuku;
        }
        if ($close=='1' && $tipetransaksi!='3') {
            exit(' Error: Receiver Accounting Period has been closed.');
        }
    }

    $str = 'select * from '.$dbname.".log_transaksi_vw where kodebarang='".$kodebarang."' and kodegudang='".$gudang."' \r\n              and tanggal<'".tanggalsystem($tanggal)."' and statussaldo=0";
    $res = mysql_query($str);
    if (mysql_num_rows($res) < 0) {
        exit(' Error: There is material has not been posted on previous date.');
    }

    $str = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$kodebarang."'";
    $res = mysql_query($str);
    $namabarang = '';
    while ($bar = mysql_fetch_object($res)) {
        $namabarang = $bar->namabarang;
    }
    if ($namabarang=='') {
        $namabarang = $kodebarang;
    }
	$headErr = '';
	
    if ($tipetransaksi=='1') {
        if ((int) $hargasatuan==0 || $nopo=='' || $supplier=='') {
            exit(' Error: price/PO/supplier not found.');
        }

        $nilaitotal = $jumlah * $hargasatuan;
        $cursaldo = 0;
        $nilaisaldo = 0;
        $qtymasuk = 0;
        $qtymasukxharga = 0;
        $saldoakhirqty = 0;
        $nilaisaldoakhir = 0;
        $hargarata = 0;
        $str = 'select saldoakhirqty,hargarata,nilaisaldoakhir,qtymasuk,qtymasukxharga from '.$dbname.".log_5saldobulanan where periode='".$periode."'\r\n                       and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $res = mysql_query($str);
        if (mysql_numrows($res) < 1) {
            $newhargarata = $hargasatuan;
            $newqtymasuk = $jumlah;
            $newqtymasukxharga = $nilaitotal;
            $newsaldoakhirqty = $jumlah;
            $newnilaisaldoakhir = $nilaitotal;
            $strupdate = 'insert into '.$dbname.".log_5saldobulanan (\r\n                                   kodeorg, kodebarang, saldoakhirqty, hargarata, lastuser,\r\n                                   periode, nilaisaldoakhir, kodegudang, qtymasuk, qtykeluar, qtymasukxharga, \r\n                                   qtykeluarxharga, saldoawalqty, hargaratasaldoawal, nilaisaldoawal)\r\n                                   values('".$kodept."','".$kodebarang."',".$newqtymasuk.','.$newhargarata.','.$user.",\r\n                                   '".$periode."',".$newqtymasukxharga.",'".$gudang."',".$newsaldoakhirqty.',0,'.$newnilaisaldoakhir.',0,0,0,0)';
        } else {
            while ($bar = mysql_fetch_object($res)) {
                $cursaldo = $bar->saldoakhirqty;
                $nilaisaldo = $bar->nilaisaldoakhir;
                $qtymasuk = $bar->qtymasuk;
                $qtymasukxharga = $bar->qtymasukxharga;
                $hargarata = $bar->hargarata;
            }
            $newhargarata = ($nilaitotal + $nilaisaldo) / ($jumlah + $cursaldo);
            $newqtymasuk = $qtymasuk + $jumlah;
            $newqtymasukxharga = $qtymasukxharga + $nilaitotal;
            $newsaldoakhirqty = $jumlah + $cursaldo;
            $newnilaisaldoakhir = $newhargarata * $newsaldoakhirqty;
            if ($newhargarata==0) {
                exit(' Error: Average price cannot be formed for '.$notransaksi.' material code :'.$kodebarang);
            }

            $strupdate = 'update '.$dbname.".log_5saldobulanan set \r\n                                       saldoakhirqty=".$newsaldoakhirqty.', hargarata='.$newhargarata.',nilaisaldoakhir='.$newnilaisaldoakhir.",\r\n                                       lastuser=".$user.',qtymasuk='.$newqtymasuk.',qtymasukxharga='.$newqtymasukxharga."\r\n                                       where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        }

        $strrollback = 'update '.$dbname.".log_5saldobulanan set \r\n                    saldoakhirqty=".$cursaldo.', hargarata='.$hargarata.',nilaisaldoakhir='.$nilaisaldo.",\r\n                    lastuser=".$user.',qtymasuk='.$qtymasuk.',qtymasukxharga='.$qtymasukxharga."\r\n                    where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $instmaster = ' insert into '.$dbname.".log_5masterbarangdt(\r\n                                kodeorg, kodebarang, saldoqty, hargalastin, hargalastout, \r\n                                stockbataspesan, stockminimum, lastuser,kodegudang) values(\r\n                                '".$kodept."','".$kodebarang."',".$newsaldoakhirqty.','.$newhargarata.",\r\n                                0,0,0,".$user.",'".$gudang."'\r\n                                )";
        $updmaster = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.$newsaldoakhirqty.",\r\n                                hargalastin=".$newhargarata." where kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $kodekl = substr($supplier, 0, 4);
        $str = 'select noakun from '.$dbname.".log_5klsupplier where kode='".$kodekl."'";
        $res = mysql_query($str);
        $akunspl = '';
        while ($bar = mysql_fetch_object($res)) {
            $akunspl = $bar->noakun;
        }
        $klbarang = substr($kodebarang, 0, 3);
        $str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
        $res = mysql_query($str);
        $akunbarang = '';
        while ($bar = mysql_fetch_object($res)) {
            $akunbarang = $bar->noakun;
        }
        if (($akunbarang=='' || $akunspl=='') && ($klbarang < '400' || substr($kodebarang, 0, 1)== '9')) {
//            exit('Error: Account no. for material or supplier not available yet for '.$notransaksi);
            exit('Error: '.$notransaksi.'/akunbrg: '.$akunbarang.'/akunspl: '.$akunspl.'/klbrg: '.$klbarang.'/kdbrg: '.$kodebarang);
        }

        $kodeJurnal = 'INVM1';
        $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$kodept."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
        $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $nilaitotal, 'totalkredit' => -1 * $nilaitotal, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
        $noUrut = 1;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => 'Pembelian barang '.$namabarang.' '.$jumlah.' '.$satuan, 'jumlah' => $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $supplier, 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $nopo, 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => 'Pembelian barang '.$namabarang.' '.$jumlah.' '.$satuan, 'jumlah' => -1 * $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $supplier, 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $nopo, 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        $updflagststussaldo = 'update '.$dbname.'. log_transaksidt set statussaldo=1,hargarata='.$newhargarata.',jumlahlalu='.$cursaldo."\r\n   where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
        if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && trim($akunbarang)!='') {
            $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
            if (!mysql_query($insHead)) {
//                $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                $headErr .= 'Insert Header Error 1: '.addslashes(mysql_error($conn))."\n";
            }

            if ($headErr=='') {
                $detailErr = '';
                foreach ($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                    if (!mysql_query($insDet)) {
                        $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                        break;
                    }
                }
                if ($detailErr=='') {
                    $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$kodept."' and kodekelompok='".$kodeJurnal."'");
                    if (!mysql_query($updJurnal)) {
                        echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                        if (!mysql_query($RBDet)) {
                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                            exit();
                        }

                        exit();
                    }

                    $errGudang = '';
                    if (!mysql_query($strupdate)) {
                        echo ' Gagal update saldobulanan';
                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                        if (!mysql_query($RBDet)) {
                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                            exit();
                        }
                    } else {
                        if (!mysql_query($updmaster)) {
                            $errGudang = ' Error update masterbarangdt';
                        }

                        if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                        }

                        if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                            $errGudang = ' Error update statussaldo on masterbarangdt';
                        }

                        if ($errGudang!='') {
                            echo $errGudang;
                            if (!mysql_query($strrollback)) {
                                echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                            }

                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                exit();
                            }

                            exit();
                        }
                    }
                } else {
                    echo $detailErr;
                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                    if (!mysql_query($RBDet)) {
                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                        exit();
                    }
                }
            } else {
                echo $headErr;
                exit();
            }
        } else {
            $errGudang = '';
            if (!mysql_query($strupdate)) {
                echo ' Error update saldobulanan';
            } else {
                if (!mysql_query($updmaster)) {
                    $errGudang = ' Error update masterbarangdt';
                }

                if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                }

                if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                    $errGudang = ' Error update statussaldo on masterbarangdt';
                }

                if ($errGudang!='') {
                    echo $errGudang;
                    if (!mysql_query($strrollback)) {
                        echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                    }

                    exit();
                }
            }
        }
    }

    if ($tipetransaksi=='6') {
        if ((int) $hargasatuan==0 || $nopo=='' || $supplier=='') {
            exit(' Error: price/PO/supplier not found');
        }

        $nilaitotal = $jumlah * $hargasatuan;
        $cursaldo = 0;
        $nilaisaldo = 0;
        $qtymasuk = 0;
        $qtymasukxharga = 0;
        $saldoakhirqty = 0;
        $nilaisaldoakhir = 0;
        $hargarata = 0;
        $str = 'select saldoakhirqty,hargarata,nilaisaldoakhir,qtykeluar,qtykeluarxharga from '.$dbname.".log_5saldobulanan where periode='".$periode."'\r\n                       and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $res = mysql_query($str);
        if (mysql_numrows($res) < 1) {
            $newhargarata = $hargasatuan;
            $newqtykeluar = $jumlah;
            $newqtykeluarxharga = $nilaitotal;
            $newsaldoakhirqty = $jumlah;
            $newnilaisaldoakhir = $nilaitotal;
        }

        while ($bar = mysql_fetch_object($res)) {
            $cursaldo = $bar->saldoakhirqty;
            $nilaisaldo = $bar->nilaisaldoakhir;
            $qtykeluar = $bar->qtykeluar;
            $qtykeluarxharga = $bar->qtykeluarxharga;
            $hargarata = $bar->hargarata;
        }
        if ($cursaldo - $jumlah <= 0) {
            $newhargarata = $hargasatuan;
        } else {
            $newhargarata = ($nilaisaldo - $nilaitotal) / ($cursaldo - $jumlah);
        }

        $newqtykeluar = $qtykeluar + $jumlah;
        $newqtykeluarxharga = $qtykeluarxharga + $nilaitotal;
        $newsaldoakhirqty = $cursaldo - $jumlah;
        $newnilaisaldoakhir = $newhargarata * $newsaldoakhirqty;
        if ($newsaldoakhirqty < 0) {
            exit(' Error: Amount not sufficient (retur:'.$jumlah.' volume:'.$cursaldo);
        }

        if ($newhargarata==0) {
            exit(' Error: Average price can not be formed on '.$notransaksi.' material code :'.$kodebarang);
        }

        $strupdate = 'update '.$dbname.".log_5saldobulanan set \r\n                                       saldoakhirqty=".$newsaldoakhirqty.', hargarata='.$newhargarata.',nilaisaldoakhir='.$newnilaisaldoakhir.",\r\n                                       lastuser=".$user.',qtykeluar='.$newqtykeluar.',qtykeluarxharga='.$newqtykeluarxharga."\r\n                                       where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $strrollback = 'update '.$dbname.".log_5saldobulanan set \r\n                    saldoakhirqty=".$cursaldo.', hargarata='.$hargarata.',nilaisaldoakhir='.$nilaisaldo.",\r\n                    lastuser=".$user.',qtykeluar='.$qtykeluar.',qtykeluarxharga='.$qtykeluarxharga."\r\n                    where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $instmaster = ' insert into '.$dbname.".log_5masterbarangdt(\r\n                                kodeorg, kodebarang, saldoqty, hargalastin, hargalastout, \r\n                                stockbataspesan, stockminimum, lastuser,kodegudang) values(\r\n                                '".$kodept."','".$kodebarang."',".$newsaldoakhirqty.','.$newhargarata.",\r\n                                0,0,0,".$user.",'".$gudang."'\r\n                                )";
        $updmaster = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.$newsaldoakhirqty.",\r\n                                hargalastout=".$newhargarata." where kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
        $kodekl = substr($supplier, 0, 4);
        $str = 'select noakun from '.$dbname.".log_5klsupplier where kode='".$kodekl."'";
        $res = mysql_query($str);
        $akunspl = '';
        while ($bar = mysql_fetch_object($res)) {
            $akunspl = $bar->noakun;
        }
        $klbarang = substr($kodebarang, 0, 3);
        $str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
        $res = mysql_query($str);
        $akunbarang = '';
        while ($bar = mysql_fetch_object($res)) {
            $akunbarang = $bar->noakun;
        }
        if (($akunbarang=='' || $akunspl=='') && ($klbarang < '400' || substr($kodebarang, 0, 1)=='9')) {
            exit('Error: Account number for material or supplier not available yet on '.$notransaksi);
        }

        $kodeJurnal = 'INVK1';
        $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$kodept."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
        $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $nilaitotal, 'totalkredit' => -1 * $nilaitotal, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
        $noUrut = 1;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => 'ReturSupplier '.$namabarang.' '.$jumlah.' '.$satuan, 'jumlah' => $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $supplier, 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $nopo, 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => 'ReturSupplier '.$namabarang.' '.$jumlah.' '.$satuan, 'jumlah' => -1 * $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $supplier, 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $nopo, 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        $updflagststussaldo = 'update '.$dbname.'. log_transaksidt set statussaldo=1,hargarata='.$newhargarata.',jumlahlalu='.$cursaldo."\r\n   where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
        if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && trim($akunbarang)!='') {
            $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
            if (!mysql_query($insHead)) {
//                $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                $headErr .= 'Insert Header Error 2: '.addslashes(mysql_error($conn))."\n";
            }

            if ($headErr=='') {
                $detailErr = '';
                foreach ($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                    if (!mysql_query($insDet)) {
                        $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                        break;
                    }
                }
                if ($detailErr=='') {
                    $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$kodept."' and kodekelompok='".$kodeJurnal."'");
                    if (!mysql_query($updJurnal)) {
                        echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                        if (!mysql_query($RBDet)) {
                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                            exit();
                        }

                        exit();
                    }

                    $errGudang = '';
                    if (!mysql_query($strupdate)) {
                        echo ' Gagal update saldobulanan';
                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                        if (!mysql_query($RBDet)) {
                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                            exit();
                        }
                    } else {
                        if (!mysql_query($updmaster)) {
                            $errGudang = ' Error update masterbarangdt';
                        }

                        if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                        }

                        if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                            $errGudang = ' Error update statussaldo on masterbarangdt';
                        }

                        if ($errGudang!='') {
                            echo $errGudang;
                            if (!mysql_query($strrollback)) {
                                echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                            }

                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                exit();
                            }

                            exit();
                        }
                    }
                } else {
                    echo $detailErr;
                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                    if (!mysql_query($RBDet)) {
                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                        exit();
                    }
                }
            } else {
                echo $headErr;
                exit();
            }
        } else {
            $errGudang = '';
            if (!mysql_query($strupdate)) {
                echo ' Error update saldobulanan';
            } else {
                if (!mysql_query($updmaster)) {
                    $errGudang = ' Error update masterbarangdt';
                }

                if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                }

                if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                    $errGudang = ' Error update statussaldo on masterbarangdt';
                }

                if ($errGudang!='') {
                    echo $errGudang;
                    if (!mysql_query($strrollback)) {
                        echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                    }

                    exit();
                }
            }
        }
    } else {
        if ($tipetransaksi=='2') {
            $hargarata = 0;
            $saldoakhirqty = 0;
            $nilaisaldoakhir = 0;
            $qtymasukxharga = 0;
            $qtymasuk = 0;
            $str = 'select saldoakhirqty,hargarata,nilaisaldoakhir,qtymasuk,qtymasukxharga from '.$dbname.".log_5saldobulanan where periode='".$periode."'\r\n                       and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $hargarata = $bar->hargarata;
                $saldoakhirqty = $bar->saldoakhirqty;
                $nilaisaldoakhir = $bar->nilaisaldoakhir;
                $qtymasukxharga = $bar->qtymasukxharga;
                $qtymasuk = $bar->qtymasuk;
            }
            if ($hargarata==0) {
                exit(' Error: Average price not available. '.$str);
            }

            $newsaldoakhirqty = $saldoakhirqty + $jumlah;
            $newhargarata = $hargarata;
            $newnilaisaldoakhir = $newhargarata * $newsaldoakhirqty;
            $newqtymasuk = $qtymasuk + $jumlah;
            $newqtymasukxharga = $newqtymasuk * $hargarata;
            $strupdate = 'update '.$dbname.".log_5saldobulanan set \r\n                    saldoakhirqty=".$newsaldoakhirqty.',nilaisaldoakhir='.$newnilaisaldoakhir.",\r\n                    lastuser=".$user.',qtymasuk='.$newqtymasuk.',qtymasukxharga='.$newqtymasukxharga."\r\n                    where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
            $strrollback = 'update '.$dbname.".log_5saldobulanan set \r\n            saldoakhirqty=".$saldoakhirqty.',nilaisaldoakhir='.$nilaisaldoakhir.",\r\n            lastuser=".$user.',qtymasuk='.$qtymasuk.',qtymasukxharga='.$qtymasukxharga."\r\n            where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
            $instmaster = ' insert into '.$dbname.".log_5masterbarangdt(\r\n                            kodeorg, kodebarang, saldoqty, hargalastin, hargalastout, \r\n                            stockbataspesan, stockminimum, lastuser,kodegudang) values(\r\n                            '".$kodept."','".$kodebarang."',".$newsaldoakhirqty.",0,\r\n                            ".$newhargarata.',0,0,'.$user.",'".$gudang."'\r\n                            )";
            $updmaster = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.$newsaldoakhirqty.",\r\n                            hargalastout=".$newhargarata." where kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
            if ($newhargarata==0) {
                exit(' Error: Price not found on the beginning of the month.');
            }

            $pengguna = substr($_POST['untukunit'], 0, 4);
            $ptpengguna = '';
            $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $ptpengguna = $bar->induk;
            }
            $str = 'select akunhutang,jenis from '.$dbname.".keu_5caco where \r\n           kodeorg='".$pengguna."'";
            $res = mysql_query($str);
            $intraco = '';
            $interco = '';
            while ($bar = mysql_fetch_object($res)) {
                if ($bar->jenis=='intra') {
                    $intraco = $bar->akunhutang;
                } else {
                    $interco = $bar->akunhutang;
                }
            }
            $ptGudang = '';
            $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".substr($gudang, 0, 4)."'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $ptGudang = $bar->induk;
            }
            $akunspl = '';
            if ($ptGudang != $ptpengguna) {
                $str = 'select akunpiutang from '.$dbname.".keu_5caco where kodeorg='".substr($gudang, 0, 4)."' and jenis='inter'";
                $res = mysql_query($str);
                $akunspl = '';
                while ($bar = mysql_fetch_object($res)) {
                    $akunspl = $bar->akunpiutang;
                }
                $inter = $interco;
                if ($akunspl=='') {
                    exit('Error: Account for intraco or interco not available yet for '.$pengguna);
                }
            } else {
                if ($pengguna != substr($gudang, 0, 4)) {
                    $str = 'select akunpiutang from '.$dbname.".keu_5caco where kodeorg='".substr($gudang, 0, 4)."' and jenis='intra'";
                    $res = mysql_query($str);
                    $akunspl = '';
                    while ($bar = mysql_fetch_object($res)) {
                        $akunspl = $bar->akunpiutang;
                    }
                    $inter = $intraco;
                    if ($akunspl=='') {
                        exit('Error: Account for intraco or interco not available yet for '.$pengguna);
                    }
                }
            }

            $statustm = '';
            $str = 'select statusblok from '.$dbname.".setup_blok where kodeorg='".$blok."'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $statustm = $bar->statusblok;
            }
            $str = 'select noakun from '.$dbname.".setup_kegiatan where \r\n                kodekegiatan='".$kodekegiatan."'";
            $akunpekerjaan = '';
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $akunpekerjaan = $bar->noakun;
            }
            if ($akunpekerjaan=='') {
                exit('Error: Account not available yet for activity '.$kodekegiatan);
            }

            $klbarang = substr($kodebarang, 0, 3);
            $str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
            $res = mysql_query($str);
            $akunbarang = '';
            while ($bar = mysql_fetch_object($res)) {
                $akunbarang = $bar->noakun;
            }
            if ($akunbarang=='') {
                exit('Error: Material account not available yet on '.$notransaksi);
            }

            $updflagststussaldo = 'update '.$dbname.'. log_transaksidt set statussaldo=1,jumlahlalu='.$saldoakhirqty.', hargarata='.$newhargarata."\r\n                                        where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
            if (substr($gudang, 0, 4)==$pengguna) {
                $kodeJurnal = 'INVM1';
                $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                $tmpKonter = fetchData($queryJ);
                $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
                $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                $noUrut = 1;
                $keterangan = 'ReturGudang barang '.$namabarang.' '.$jumlah.' '.$satuan;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                ++$noUrut;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => $kodekegiatan, 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                ++$noUrut;
                if ((substr($kodebarang, 0, 3) < '400' || '9' == substr($kodebarang, 0, 1)) && '' != trim($akunbarang)) {
                    $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                    if (!mysql_query($insHead)) {
//                        $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                        $headErr .= 'Insert Header Error 3: '.addslashes(mysql_error($conn))."\n";
                    }

                    if ($headErr=='') {
                        $detailErr = '';
                        foreach ($dataRes['detail'] as $row) {
                            $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                            if (!mysql_query($insDet)) {
                                $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                break;
                            }
                        }
                        if ($detailErr=='') {
                            $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."'");
                            if (!mysql_query($updJurnal)) {
                                echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    exit();
                                }

                                exit();
                            }

                            $errGudang = '';
                            if (!mysql_query($strupdate)) {
                                echo ' Error update saldobulanan';
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    exit();
                                }
                            } else {
                                if (!mysql_query($updmaster)) {
                                    $errGudang = ' Gagal update masterbarangdt';
                                }

                                if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                }

                                if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                    $errGudang = ' Error update statussaldo on masterbarangdt';
                                }

                                if ($errGudang!='') {
                                    echo $errGudang;
                                    if (!mysql_query($strrollback)) {
                                        echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                    }

                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                    if (!mysql_query($RBDet)) {
                                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                        exit();
                                    }

                                    exit();
                                }
                            }
                        } else {
                            echo $detailErr;
                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                exit();
                            }
                        }
                    } else {
                        echo $headErr;
                        exit();
                    }
                } else {
                    $errGudang = '';
                    if (!mysql_query($strupdate)) {
                        echo ' Error update saldobulanan';
                    } else {
                        if (!mysql_query($updmaster)) {
                            $errGudang = ' Error update masterbarangdt ';
                        }

                        if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                        }

                        if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                            $errGudang = ' Error update statussaldo on masterbarangdt';
                        }

                        if ($errGudang!='') {
                            echo $errGudang;
                            if (!mysql_query($strrollback)) {
                                echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                            }

                            exit();
                        }
                    }
                }
            } else {
                $kodeJurnal = 'INVM1';
                $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."' ");
                $tmpKonter = fetchData($queryJ);
                $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
                $header1pemilik = $nojurnal;
                $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                $noUrut = 1;
                $keterangan = 'ReturGudang barang '.$namabarang.' '.$jumlah.' '.$satuan;
                $keterangan = substr($keterangan, 0, 150);
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $inter, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && '' != trim($akunbarang)) {
                    $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                    if (!mysql_query($insHead)) {
//                        $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                        $headErr .= 'Insert Header Error 4: '.addslashes(mysql_error($conn))."\n";
                    }

                    if ($headErr=='') {
                        $detailErr = '';
                        foreach ($dataRes['detail'] as $row) {
                            $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                            if (!mysql_query($insDet)) {
                                $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                break;
                            }
                        }
                        if ($detailErr=='') {
                            $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."'");
                            if (!mysql_query($updJurnal)) {
                                echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    exit();
                                }

                                exit();
                            }
                        } else {
                            echo $detailErr;
                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                exit();
                            }
                        }
                    } else {
                        echo $headErr;
                        exit();
                    }
                }

                $kodeJurnal = 'INVM1';
                $stri = 'select tanggalmulai from '.$dbname.".setup_periodeakuntansi\r\n                           where kodeorg='".$pengguna."' and tutupbuku=0";
                $tanggalsana = '';
                $resi = mysql_query($stri);
                while ($bari = mysql_fetch_object($resi)) {
                    $tanggalsana = $bari->tanggalmulai;
                }
                if ($tanggalsana=='' || substr($tanggalsana, 0, 7) == substr(tanggalsystem($tanggal), 0, 4).'-'.substr(tanggalsystem($tanggal), 4, 2)) {
                    $tanggalsana = tanggalsystem($tanggal);
                    $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                    $nojurnal = str_replace('-', '', $tanggalsana).'/'.$pengguna.'/'.$kodeJurnal.'/'.$konter;
                    unset($dataRes['header']);
                    $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggalsana, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                    $keterangan = 'ReturGudang barang '.$namabarang.' '.$jumlah.' '.$satuan.' '.substr($_POST['tanggal'], 0, 7);
                    $keterangan = substr($keterangan, 0, 150);
                    $noUrut = 1;
                    unset($dataRes['detail']);
                    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggalsana, 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                    ++$noUrut;
                    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggalsana, 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => $kodekegiatan, 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                    ++$noUrut;
                    if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && '' != trim($akunbarang)) {
                        $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                        if (!mysql_query($insHead)) {
//                            $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                            $headErr .= 'Insert Header Error 5: '.addslashes(mysql_error($conn))."\n";
                        }

                        if ($headErr=='') {
                            $detailErr = '';
                            foreach ($dataRes['detail'] as $row) {
                                $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                                if (!mysql_query($insDet)) {
                                    $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                    break;
                                }
                            }
                            if ($detailErr=='') {
                                $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."'");
                                if (!mysql_query($updJurnal)) {
                                    echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                    if (!mysql_query($RBDet)) {
                                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                        exit();
                                    }

                                    exit();
                                }

                                $errGudang = '';
                                if (!mysql_query($strupdate)) {
                                    echo ' Gagal update saldobulanan';
                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                    if (!mysql_query($RBDet)) {
                                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                        exit();
                                    }
                                } else {
                                    if (!mysql_query($updmaster)) {
                                        $errGudang = ' Gagal update masterbarangdt';
                                    }

                                    if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                    }

                                    if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                        $errGudang = ' Error update statussaldo on masterbarangdt';
                                    }

                                    if ($errGudang!='') {
                                        echo $errGudang;
                                        if (!mysql_query($strrollback)) {
                                            echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                        }

                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                        }

                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                            exit();
                                        }

                                        exit();
                                    }
                                }
                            } else {
                                echo $detailErr;
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                }

                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                    exit();
                                }
                            }
                        } else {
                            echo $headErr;
                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                exit();
                            }
                        }
                    } else {
                        $errGudang = '';
                        if (!mysql_query($strupdate)) {
                            echo ' Error update saldobulanan';
                        } else {
                            if (!mysql_query($updmaster)) {
                                $errGudang = ' Error update masterbarangdt';
                            }

                            if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                            }

                            if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                $errGudang = ' Error update statussaldo on masterbarangdt';
                            }

                            if ($errGudang!='') {
                                echo $errGudang;
                                if (!mysql_query($strrollback)) {
                                    echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                }

                                exit();
                            }
                        }
                    }
                } else {
                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                    if (!mysql_query($RBDet)) {
                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                        exit(' Error: Receivers accounting period not the same as warehouse.');
                    }

                    exit(' Error: Receivers accounting period not the same as warehouse.');
                }
            }
        } else {
            if ($tipetransaksi=='3') {
                $hargarata = 0;
                $saldoakhirqty = 0;
                $nilaisaldoakhir = 0;
                $qtymasukxharga = 0;
                $qtymasuk = 0;
                $nilaitotal = $jumlah * $hargasatuan;
                $str = 'select saldoakhirqty,hargarata,nilaisaldoakhir,qtymasuk,qtymasukxharga from '.$dbname.".log_5saldobulanan where periode='".$periode."'\r\n                       and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                $res = mysql_query($str);
                if (mysql_numrows($res) < 1) {
                    $newhargarata = $hargasatuan;
                    $newqtymasuk = $jumlah;
                    $newqtymasukxharga = $nilaitotal;
                    $newsaldoakhirqty = $jumlah;
                    $newnilaisaldoakhir = $nilaitotal;
                    $strupdate = 'insert into '.$dbname.".log_5saldobulanan (\r\n                                kodeorg, kodebarang, saldoakhirqty, hargarata, lastuser,\r\n                                periode, nilaisaldoakhir, kodegudang, qtymasuk, qtykeluar, qtymasukxharga, \r\n                                qtykeluarxharga, saldoawalqty, hargaratasaldoawal, nilaisaldoawal)\r\n                                values('".$kodept."','".$kodebarang."',".$newqtymasuk.','.$newhargarata.','.$user.",\r\n                                '".$periode."',".$newqtymasukxharga.",'".$gudang."',".$newsaldoakhirqty.',0,'.$newnilaisaldoakhir.',0,0,0,0)';
                } else {
                    while ($bar = mysql_fetch_object($res)) {
                        $hargarata = $bar->hargarata;
                        $saldoakhirqty = $bar->saldoakhirqty;
                        $nilaisaldoakhir = $bar->nilaisaldoakhir;
                        $qtymasukxharga = $bar->qtymasukxharga;
                        $qtymasuk = $bar->qtymasuk;
                    }
                    $newsaldoakhirqty = $saldoakhirqty + $jumlah;
                    $newhargarata = ($nilaitotal + $nilaisaldoakhir) / $newsaldoakhirqty;
                    $newnilaisaldoakhir = $newhargarata * $newsaldoakhirqty;
                    $newqtymasuk = $qtymasuk + $jumlah;
                    $newqtymasukxharga = $newqtymasuk * $hargarata;
                    if ($newhargarata==0 || $newhargarata=='') {
                        exit(' Error: Average price cannot be formed on '.$notransaksi.' material code :'.$kodebarang);
                    }

                    $strupdate = 'update '.$dbname.".log_5saldobulanan set \r\n                                       saldoakhirqty=".$newsaldoakhirqty.', hargarata='.$newhargarata.',nilaisaldoakhir='.$newnilaisaldoakhir.",\r\n                                       lastuser=".$user.',qtymasuk='.$newqtymasuk.',qtymasukxharga='.$newqtymasukxharga."\r\n                                       where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                }

                $strrollback = 'update '.$dbname.".log_5saldobulanan set \r\n            saldoakhirqty=".$saldoakhirqty.',nilaisaldoakhir='.$nilaisaldoakhir.",\r\n            lastuser=".$user.',qtymasuk='.$qtymasuk.',qtymasukxharga='.$qtymasukxharga."\r\n            where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                $instmaster = ' insert into '.$dbname.".log_5masterbarangdt(\r\n                            kodeorg, kodebarang, saldoqty, hargalastin, hargalastout, \r\n                            stockbataspesan, stockminimum, lastuser,kodegudang) values(\r\n                            '".$kodept."','".$kodebarang."',".$newsaldoakhirqty.",0,\r\n                            ".$newhargarata.',0,0,'.$user.",'".$gudang."'\r\n                            )";
                $updmaster = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.$newsaldoakhirqty.",\r\n                            hargalastin=".$newhargarata." where kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                $pengguna = substr($gudang, 0, 4);
                $ptpengguna = '';
                $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    $ptpengguna = $bar->induk;
                }
                $ptGudang = '';
                $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".substr($gudangx, 0, 4)."'";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    $ptGudang = $bar->induk;
                }
                $akunspl = '';
                if ($ptGudang != $ptpengguna) {
                    $str = 'select akunpiutang from '.$dbname.".keu_5caco where left(kodeorg,4)='".substr($gudangx, 0, 4)."' and jenis='inter'";
                    $res = mysql_query($str);
                    $akunspl = '';
                    while ($bar = mysql_fetch_object($res)) {
                        $akunspl = $bar->akunpiutang;
                    }
                    if ('' == $akunspl) {
                        exit(' Error1: Account intraco or interco not available for '.substr($gudangx, 0, 4).' / '.$str);
                    }
                } else {
                    if ($pengguna != substr($gudangx, 0, 4)) {
                        $str = 'select akunpiutang from '.$dbname.".keu_5caco where left(kodeorg,4)='".substr($gudangx, 0, 4)."' and jenis='intra'";
                        $res = mysql_query($str);
                        $akunspl = '';
                        while ($bar = mysql_fetch_object($res)) {
                            $akunspl = $bar->akunpiutang;
                        }
                        if ($akunspl=='') {
                            exit(' Error: Account intraco / interco not available for '.substr($gudangx, 0, 4));
                        }
                    }
                }

                $klbarang = substr($kodebarang, 0, 3);
                $str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
                $res = mysql_query($str);
                $akunbarang = '';
                while ($bar = mysql_fetch_object($res)) {
                    $akunbarang = $bar->noakun;
                }
                if ($akunbarang=='') {
                    exit(' Error: Account for material not available for '.$notransaksi);
                }

                $updflagststussaldo = 'update '.$dbname.'. log_transaksidt set statussaldo=1,jumlahlalu='.$saldoakhirqty.', hargarata='.$newhargarata."\r\n                                        where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."'";
                $kodeJurnal = 'INVM1';
                $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                $tmpKonter = fetchData($queryJ);
                $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                $nojurnal = tanggalsystem($tanggal).'/'.$pengguna.'/'.$kodeJurnal.'/'.$konter;
                unset($dataRes['header']);
                $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $nilaitotal, 'totalkredit' => -1 * $nilaitotal, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                $keterangan = 'Terima Mutasi barang '.$namabarang.' '.$jumlah.' '.$satuan.' '.substr($_POST['tanggal'], 0, 7);
                $keterangan = substr($keterangan, 0, 150);
                $noakunmutasi = '1210299';
                $noUrut = 1;
                unset($dataRes['detail']);
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => $keterangan, 'jumlah' => $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => $keterangan, 'jumlah' => -1 * $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $noakunmutasi, 'keterangan' => $keterangan, 'jumlah' => -1 * $nilaitotal, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9')  && trim($akunbarang)!='' && substr($pengguna, 0, 4) != substr($gudangx, 0, 4)) {
                    $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                    if (!mysql_query($insHead)) {
//                        $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                        $headErr .= 'Insert Header Error 6: '.addslashes(mysql_error($conn))."\n";
                    }

                    if ($headErr=='') {
                        $detailErr = '';
                        foreach ($dataRes['detail'] as $row) {
                            $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                            if (!mysql_query($insDet)) {
                                $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn)).$insDet."\n";

                                break;
                            }
                        }
                        if ($detailErr=='') {
                            $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."'");
                            if (!mysql_query($updJurnal)) {
                                echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    exit();
                                }

                                exit();
                            }

                            $errGudang = '';
                            if (!mysql_query($strupdate)) {
                                echo ' Error update saldobulanan';
                            } else {
                                if (!mysql_query($updmaster)) {
                                    $errGudang = ' Error update masterbarangdt';
                                }

                                if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                }

                                if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                    $errGudang = ' Error update statussaldo on masterbarangdt';
                                }

                                if ($errGudang!='') {
                                    echo $errGudang;
                                    if (!mysql_query($strrollback)) {
                                        echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                    }

                                    exit();
                                }
                            }
                        } else {
                            echo $detailErr;
                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                            }

                            if (!mysql_query($RBDet)) {
                                echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                exit();
                            }
                        }
                    } else {
                        echo $headErr;
                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                        if (!mysql_query($RBDet)) {
                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                            exit();
                        }
                    }
                } else {
                    $errGudang = '';
                    if (!mysql_query($strupdate)) {
                        echo ' Error update saldobulanan';
                    } else {
                        if (!mysql_query($updmaster)) {
                            $errGudang = ' Error update masterbarangdt';
                        }

                        if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                        }

                        if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                            $errGudang = ' Error update statussaldo pada masterbarangdt';
                        }

                        if ($errGudang !='') {
                            echo $errGudang;
                            if (!mysql_query($strrollback)) {
                                echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                            }

                            exit();
                        }
                    }
                }
            } else {
                if ($tipetransaksi=='7') {
                    $hargarata = 0;
                    $saldoakhirqty = 0;
                    $nilaisaldoakhir = 0;
                    $qtykeluarxharga = 0;
                    $qtykeluar = 0;
                    $str = 'select saldoakhirqty,hargarata,nilaisaldoakhir,qtykeluar,qtykeluarxharga from '.$dbname.".log_5saldobulanan where periode='".$periode."'\r\n                       and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".substr($kodept,0,3)."'";
                    $res = mysql_query($str);
                    while ($bar = mysql_fetch_object($res)) {
                        $hargarata = $bar->hargarata;
                        $saldoakhirqty = $bar->saldoakhirqty;
                        $nilaisaldoakhir = $bar->nilaisaldoakhir;
                        $qtykeluarxharga = $bar->qtykeluarxharga;
                        $qtykeluar = $bar->qtykeluar;
                    }
                    if ($hargarata==0) {
                        exit(' Error: Average price not available.'.$str);
                    }

                    $newsaldoakhirqty = $saldoakhirqty - $jumlah;
                    $newhargarata = $hargarata;
                    $newnilaisaldoakhir = $newhargarata * $newsaldoakhirqty;
                    $newqtykeluar = $qtykeluar + $jumlah;
                    $newqtykeluarxharga = $newqtykeluar * $newhargarata;
                    if ($newsaldoakhirqty < 0) {
                        exit(' Error: Amount not sufficient');
                    }

                    $strupdate = 'update '.$dbname.".log_5saldobulanan set \r\n                    saldoakhirqty=".$newsaldoakhirqty.',nilaisaldoakhir='.$newnilaisaldoakhir.",\r\n                    lastuser=".$user.',qtykeluar='.$newqtykeluar.',qtykeluarxharga='.$newqtykeluarxharga."\r\n                    where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                    $strrollback = 'update '.$dbname.".log_5saldobulanan set \r\n            saldoakhirqty=".$saldoakhirqty.',nilaisaldoakhir='.$nilaisaldoakhir.",\r\n            lastuser=".$user.',qtykeluar='.$qtykeluar.',qtykeluarxharga='.$qtykeluarxharga."\r\n            where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                    $instmaster = ' insert into '.$dbname.".log_5masterbarangdt(\r\n                            kodeorg, kodebarang, saldoqty, hargalastin, hargalastout, \r\n                            stockbataspesan, stockminimum, lastuser,kodegudang) values(\r\n                            '".$kodept."','".$kodebarang."',".$newsaldoakhirqty.",0,\r\n                            ".$newhargarata.',0,0,'.$user.",'".$gudang."'\r\n                            )";
                    $updmaster = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.$newsaldoakhirqty.",\r\n                            hargalastout=".$newhargarata." where kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                    $pengguna = substr($gudangx, 0, 4);
                    $ptpengguna = '';
                    $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
                    $res = mysql_query($str);
                    while ($bar = mysql_fetch_object($res)) {
                        $ptpengguna = $bar->induk;
                    }
                    $str = 'select akunpiutang,jenis from '.$dbname.".keu_5caco where \r\n           kodeorg='".$pengguna."'";
                    $res = mysql_query($str);
                    $intraco = '';
                    $interco = '';
                    while ($bar = mysql_fetch_object($res)) {
                        if ($bar->jenis=='intra') {
                            $intraco = $bar->akunpiutang;
                        } else {
                            $interco = $bar->akunpiutang;
                        }
                    }
                    $ptGudang = '';
                    $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".substr($gudang, 0, 4)."'";
                    $res = mysql_query($str);
                    while ($bar = mysql_fetch_object($res)) {
                        $ptGudang = $bar->induk;
                    }
                    $akunspl = '';
                    if ($ptGudang != $ptpengguna) {
                        $str = 'select akunhutang from '.$dbname.".keu_5caco where left(kodeorg,4)='".substr($gudang, 0, 4)."' and jenis='inter'";
                        $res = mysql_query($str);
                        $akunspl = '';
                        while ($bar = mysql_fetch_object($res)) {
                            $akunspl = $bar->akunhutang;
                        }
                        $inter = $interco;
                        if ($akunspl=='') {
                            exit('Error2: Account intraco or interco not available for '.$pengguna.' / '.$str);
                        }
                    } else {
                        if ($pengguna != substr($gudang, 0, 4)) {
                            $str = 'select akunhutang from '.$dbname.".keu_5caco where left(kodeorg,4)='".substr($gudang, 0, 4)."' and jenis='intra'";
                            $res = mysql_query($str);
                            $akunspl = '';
                            while ($bar = mysql_fetch_object($res)) {
                                $akunspl = $bar->akunhutang;
                            }
                            $inter = $intraco;
                            if ($akunspl=='') {
                                exit('Error3: Account intraco or interco not available for '.$pengguna.' / '.$str);
                            }
                        }
                    }

                    $klbarang = substr($kodebarang, 0, 3);
                    $str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
                    $res = mysql_query($str);
                    $akunbarang = '';
                    while ($bar = mysql_fetch_object($res)) {
                        $akunbarang = $bar->noakun;
                    }
                    if ($akunbarang=='') {
                        exit('Error: Account for material not available for  '.$notransaksi);
                    }

                    $updflagststussaldo = 'update '.$dbname.'. log_transaksidt set statussaldo=1,jumlahlalu='.$saldoakhirqty.',hargarata='.$newhargarata."\r\n                                        where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."'";
                    if ($pengguna == substr($gudang, 0, 4)) {
                        $errGudang = '';
                        if (!mysql_query($strupdate)) {
                            echo ' Error update saldobulanan';
                        } else {
                            if (!mysql_query($updmaster)) {
                                $errGudang = ' Error update masterbarangdt';
                            }

                            if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                            }

                            if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                $errGudang = ' Error update statussaldo on masterbarangdt';
                            }

                            if ($errGudang!='') {
                                echo $errGudang;
                                if (!mysql_query($strrollback)) {
                                    echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                }

                                exit();
                            }
                        }
                    } else {
                        $kodeJurnal = 'INVK1';
                        $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."' ");
                        $tmpKonter = fetchData($queryJ);
                        $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                        $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
                        $header1pemilik = $nojurnal;
                        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                        $noUrut = 1;
                        $keterangan = 'Mutasi barang '.$namabarang.' '.$jumlah.' '.$satuan;
                        $keterangan = substr($keterangan, 0, 150);
                        $noakunmutasi = '1210299';
                        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $inter, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                        ++$noUrut;
                        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $noakunmutasi, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                        ++$noUrut;
                        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                        ++$noUrut;
                        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $inter, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                        if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && '' != trim($akunbarang)) {
                            $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                            if (!mysql_query($insHead)) {
//                                $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                                $headErr .= 'Insert Header Error 7: '.addslashes(mysql_error($conn))."\n";
                            }

                            if ($headErr=='') {
                                $detailErr = '';
                                foreach ($dataRes['detail'] as $row) {
                                    $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                                    if (!mysql_query($insDet)) {
                                        $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                        break;
                                    }
                                }
                                if ($detailErr=='') {
                                    $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."'");
                                    if (!mysql_query($updJurnal)) {
                                        echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                            exit();
                                        }

                                        exit();
                                    }

                                    $sNokono = 'select distinct nopo from '.$dbname.".log_transaksidt \r\n                                  where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."'";
                                    $qKono = mysql_query($sNokono);
                                    $rKono = mysql_fetch_assoc($qKono);
                                    $bKono = 'select distinct nokonosemen from '.$dbname.".log_rinciankono \r\n                                where kodebarang='".$kodebarang."' and nopo='".$rKono['nopo']."'";
                                    $qbKono = mysql_query($bKono);
                                    $rbKono = mysql_fetch_assoc($qbKono);
                                    $scek = 'select distinct * from '.$dbname.".log_konosemenht where \r\n                               nokonosemen='".$rbKono['nokonosemen']."' and posting=0";
                                    $qcek = mysql_query($scek);
                                    $rcek = mysql_num_rows($qcek);
                                    if ($rcek==0) {
                                        $supdatedt = 'update '.$dbname.".log_konosemenht set statusmutasi=1 where nokonosemen='".$rbKono['nokonosemen']."'";
                                        if (!mysql_query($supdatedt)) {
                                            exit('error: db bermasalah :'.mysql_error($conn).'___'.$supdatedt);
                                        }
                                    }

                                    $errGudang = '';
                                    if (!mysql_query($strupdate)) {
                                        echo ' Gagal update saldobulanan';
                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                            exit();
                                        }
                                    } else {
                                        if (!mysql_query($updmaster)) {
                                            $errGudang = ' Error update masterbarangdt';
                                        }

                                        if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                        }

                                        if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                            $errGudang = ' Error update statussaldo on masterbarangdt';
                                        }

                                        if ($errGudang!='') {
                                            echo $errGudang;
                                            if (!mysql_query($strrollback)) {
                                                echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                            }

                                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                            }

                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                                exit();
                                            }

                                            exit();
                                        }
                                    }
                                } else {
                                    echo $detailErr;
                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                    if (!mysql_query($RBDet)) {
                                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                        exit();
                                    }
                                }
                            } else {
                                echo $headErr;
                                exit();
                            }
                        } else {
                            if (!mysql_query($updmaster)) {
                                $errGudang = ' Error update masterbarangdt';
                            }

                            if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                            }

                            if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                $errGudang = ' Error update statussaldo on masterbarangdt';
                            }

                            if ($errGudang!='') {
                                echo $errGudang;
                                if (!mysql_query($strrollback)) {
                                    echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                }

                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                }

                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                    exit();
                                }

                                exit();
                            }
                        }
                    }
                } else {
                    if ($tipetransaksi=='5') {
                        $hargarata = 0;
                        $saldoakhirqty = 0;
                        $nilaisaldoakhir = 0;
                        $qtykeluarxharga = 0;
                        $qtykeluar = 0;
						$str="select distinct a.saldoakhirqty,a.hargarata,a.nilaisaldoakhir,a.qtykeluar,a.qtykeluarxharga,a.periode 
						from ".$dbname.".log_5saldobulanan a
						where a.periode<='".$periode."'";
						if (trim($gudang)!="") {
							$str.="and a.kodegudang='".$gudang."'";
						} 
						$str.="and a.kodebarang='".$kodebarang."'
						and left(a.kodeorg,3)='".substr($kodept,0,3)."' ORDER BY a.periode DESC LIMIT 1"; 
						
						//echo "warning: ".$str;
						//exit();
						
                        $res = mysql_query($str);						
                        while ($bar = mysql_fetch_object($res)) {
                            $hargarata = $bar->hargarata;
                            $saldoakhirqty = $bar->saldoakhirqty;
                            $nilaisaldoakhir = $bar->nilaisaldoakhir;
                            $qtykeluarxharga = $bar->qtykeluarxharga;
                            $qtykeluar = $bar->qtykeluar;
                        }
						
						if($hargarata==0){
							$sqlUpRata="UPDATE log_5masterbarangdt SET hargalastin=hargalastout  WHERE hargalastin>0 AND kodeorg='".$kodept."' AND kodebarang='".$kodebarang."' and kodegudang='".$gudang."'";
							mysql_query($sqlUpRata);
							$sqlSelectRata="SELECT hargalastin FROM log_5masterbarangdt WHERE left(kodeorg,3)='".substr($kodept,0,3)."' AND kodebarang='".$kodebarang."' and kodegudang='".$gudang."' LIMIT 1 ";

							//echo "warning: ".$sqlSelectRata;
							//exit();

							$resSelectRata=mysql_query($sqlSelectRata);
							while($r = mysql_fetch_object($resSelectRata)) {
								$hargarata= $r->hargalastin;
							}
						}
																		
                        if ($hargarata==0) {
                            exit(' Error: harga rata-rata belum ada');
                        }

						$newsaldoakhirqty=$saldoakhirqty-$jumlah;
						$newhargarata=$hargarata;
						$newnilaisaldoakhir=$newhargarata*$newsaldoakhirqty;
						$newqtykeluar=$qtykeluar+$jumlah;
						$newqtykeluarxharga=$newqtykeluar*$newhargarata;
						//echo 'warning: newsaldoakhirqty='.$newsaldoakhirqty.',saldoakhirqty='.$saldoakhirqty.',jumlah='.$jumlah;
						//exit();
						if($newsaldoakhirqty<0){
							exit("Error: Saldo tidak cukup");
						}

                        $strupdate = 'update '.$dbname.".log_5saldobulanan set \r\n                    saldoakhirqty=".$newsaldoakhirqty.',nilaisaldoakhir='.$newnilaisaldoakhir.",\r\n                    lastuser=".$user.',qtykeluar='.$newqtykeluar.',qtykeluarxharga='.$newqtykeluarxharga."\r\n                    where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                        $strrollback = 'update '.$dbname.".log_5saldobulanan set \r\n            saldoakhirqty=".$saldoakhirqty.',nilaisaldoakhir='.$nilaisaldoakhir.",\r\n            lastuser=".$user.',qtykeluar='.$qtykeluar.',qtykeluarxharga='.$qtykeluarxharga."\r\n            where periode='".$periode."' and kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                        $instmaster = ' insert into '.$dbname.".log_5masterbarangdt(\r\n                            kodeorg, kodebarang, saldoqty, hargalastin, hargalastout, \r\n                            stockbataspesan, stockminimum, lastuser,kodegudang) values(\r\n                            '".$kodept."','".$kodebarang."',".$newsaldoakhirqty.",0,\r\n                            ".$newhargarata.',0,0,'.$user.",'".$gudang."'\r\n                            )";
                        $updmaster = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.$newsaldoakhirqty.",\r\n                            hargalastout=".$newhargarata." where kodegudang='".$gudang."' and kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
                        $pengguna = substr($_POST['untukunit'], 0, 4);
                        $ptpengguna = '';
                        $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
                        $res = mysql_query($str);
                        while ($bar = mysql_fetch_object($res)) {
                            $ptpengguna = $bar->induk;
                        }
                        $str = 'select akunpiutang,jenis from '.$dbname.".keu_5caco where \r\n           kodeorg='".$pengguna."'";
                        $res = mysql_query($str);
                        $intraco = '';
                        $interco = '';
                        while ($bar = mysql_fetch_object($res)) {
                            if ($bar->jenis=='intra') {
                                $intraco = $bar->akunpiutang;
                            } else {
                                $interco = $bar->akunpiutang;
                            }
                        }
                        $ptGudang = '';
                        $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".substr($gudang, 0, 4)."'";
                        $res = mysql_query($str);
                        while ($bar = mysql_fetch_object($res)) {
                            $ptGudang = $bar->induk;
                        }
                        $akunspl = '';
                        if ($ptGudang != $ptpengguna) {
                            $str = 'select akunhutang from '.$dbname.".keu_5caco where kodeorg='".substr($gudang, 0, 4)."' and jenis='inter'";
                            $res = mysql_query($str);
                            $akunspl = '';
                            while ($bar = mysql_fetch_object($res)) {
                                $akunspl = $bar->akunhutang;
                            }
                            $inter = $interco;
                            if ($akunspl=='') {
                                exit('Error4: Account intraco or interco not available for '.$pengguna.' / '.$str);
                            }
                        } else {
                            if ($pengguna != substr($gudang, 0, 4)) {
                                $str = 'select akunhutang from '.$dbname.".keu_5caco where left(kodeorg,4)='".substr($gudang, 0, 4)."' and jenis='intra'";
                                $res = mysql_query($str);
                                $akunspl = '';
                                while ($bar = mysql_fetch_object($res)) {
                                    $akunspl = $bar->akunhutang;
                                }
                                $inter = $intraco;
                                if ($akunspl=='') {
                                    exit('Error5: Account intraco or interco not available for '.$pengguna.' / '.$str);
                                }
                            }
                        }

                        $statustm = '';
                        $str = 'select statusblok from '.$dbname.".setup_blok where left(kodeorg,4)='".$blok."'";
                        $res = mysql_query($str);
                        while ($bar = mysql_fetch_object($res)) {
                            $statustm = $bar->statusblok;
                        }
                        $str = 'select noakun from '.$dbname.".setup_kegiatan where \r\n                kodekegiatan='".$kodekegiatan."'";
                        $akunpekerjaan = '';
                        $res = mysql_query($str);
                        while ($bar = mysql_fetch_object($res)) {
                            $akunpekerjaan = $bar->noakun;
                        }
                        $kodeasset = '';
                        if (substr($blok, 0, 2)=='AK' || substr($blok, 0, 2)=='PB') {
                            $akunpekerjaan = substr($kodekegiatan, 0, 7);
                            $kodeasset = $blok;
                            $blok = '';
                        }

                        if ($akunpekerjaan=='') {
                            exit('Error: Account not available for activity '.$kodekegiatan);
                        }

                        $klbarang = substr($kodebarang, 0, 3);
                        $str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
                        $res = mysql_query($str);
                        $akunbarang = '';
                        while ($bar = mysql_fetch_object($res)) {
                            $akunbarang = $bar->noakun;
                        }
                        if (substr($kodeasset, 0, 2)=='AK' || substr($kodeasset, 0, 2)=='PB') {
                            $updflagststussaldo = 'update '.$dbname.'.log_transaksidt set statussaldo=1,jumlahlalu='.$saldoakhirqty.',hargarata='.$newhargarata."\r\n                                        where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$kodeasset."'";
                        } else {
                            $updflagststussaldo = 'update '.$dbname.'.log_transaksidt set statussaldo=1,jumlahlalu='.$saldoakhirqty.',hargarata='.$newhargarata."\r\n                                        where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
                        }

                        if (substr($gudang, 0, 4)==$pengguna) {
                            $kodeJurnal = 'INVK1';
                            $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                            $tmpKonter = fetchData($queryJ);
                            $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                            $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
                            $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                            $noUrut = 1;
                            $keterangan = 'Pemakaian barang '.$namabarang.' '.$jumlah.' '.$satuan;
                            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => $kodekegiatan, 'kodeasset' => $kodeasset, 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                            ++$noUrut;
                            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                            ++$noUrut;
                            if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && trim($akunbarang)!='') {
                                $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                                if (!mysql_query($insHead)) {
//                                    $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                                    $headErr .= 'Insert Header Error 8: '.addslashes(mysql_error($conn))."\n";
                                }

                                if ($headErr=='') {
                                    $detailErr = '';
                                    foreach ($dataRes['detail'] as $row) {
                                        $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                                        if (!mysql_query($insDet)) {
                                            $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                            break;
                                        }
                                    }
                                    if ($detailErr=='') {
                                        $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."'");
                                        if (!mysql_query($updJurnal)) {
                                            echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                exit();
                                            }

                                            exit();
                                        }

                                        $errGudang = '';
                                        if (!mysql_query($strupdate)) {
                                            echo ' Error update saldobulanan';
                                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                exit();
                                            }
                                        } else {
                                            if (!mysql_query($updmaster)) {
                                                $errGudang = ' Gagal update masterbarangdt';
                                            }

                                            if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                            }

                                            if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                                $errGudang = ' Error update statussaldo on log_transaksidt';
                                            }

                                            if ($errGudang!='') {
                                                echo $errGudang;
                                                if (!mysql_query($strrollback)) {
                                                    echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                                }

                                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                                if (!mysql_query($RBDet)) {
                                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                    exit();
                                                }

                                                exit();
                                            }
                                        }
                                    } else {
                                        echo $detailErr;
                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                            exit();
                                        }
                                    }
                                } else {
                                    echo $headErr;
                                    exit();
                                }
                            } else {
                                $errGudang = '';
                                if (!mysql_query($strupdate)) {
                                    echo ' Error update saldobulanan';
                                } else {
                                    if (!mysql_query($updmaster)) {
                                        $errGudang = ' Error update masterbarangdt';
                                    }

                                    if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                    }

                                    if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                        $errGudang = ' Error update statussaldo on masterbarangdt';
                                    }

                                    if ($errGudang!='') {
                                        echo $errGudang;
                                        if (!mysql_query($strrollback)) {
                                            echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                        }

                                        exit();
                                    }
                                }
                            }
                        } else {
                            $kodeJurnal = 'INVK1';
                            $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."' ");
                            $tmpKonter = fetchData($queryJ);
                            $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                            $nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
                            $header1pemilik = $nojurnal;
                            $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                            $noUrut = 1;
                            $keterangan = 'Pemakaian barang '.$namabarang.' '.$jumlah.' '.$satuan;
                            $keterangan = substr($keterangan, 0, 150);
                            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $inter, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                            ++$noUrut;
                            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                            if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && trim($akunbarang)!='') {
                                $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                                if (!mysql_query($insHead)) {
//                                    $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                                    $headErr .= 'Insert Header Error 9: '.addslashes(mysql_error($conn))."\n";
                                }

                                if ($headErr=='') {
                                    $detailErr = '';
                                    foreach ($dataRes['detail'] as $row) {
                                        $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                                        if (!mysql_query($insDet)) {
                                            $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                            break;
                                        }
                                    }
                                    if ($detailErr=='') {
                                        $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."'");
                                        if (!mysql_query($updJurnal)) {
                                            echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                exit();
                                            }

                                            exit();
                                        }
                                    } else {
                                        echo $detailErr;
                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                            exit();
                                        }
                                    }
                                } else {
                                    echo $headErr;
                                    exit();
                                }
                            }

                            $kodeJurnal = 'INVK1';
                            $stri = 'select tanggalmulai from '.$dbname.".setup_periodeakuntansi\r\n                           where kodeorg='".$pengguna."' and tutupbuku=0";
                            $tanggalsana = '';
                            $resi = mysql_query($stri);
                            while ($bari = mysql_fetch_object($resi)) {
                                $tanggalsana = $bari->tanggalmulai;
                            }
                            if ($tanggalsana=='' || substr(tanggalsystem($tanggal), 0, 4).'-'.substr(tanggalsystem($tanggal), 4, 2) == substr($tanggalsana, 0, 7)) {
                                $tanggalsana = tanggalsystem($tanggal);
                                $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                                $tmpKonter = fetchData($queryJ);
                                $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
                                $nojurnal = str_replace('-', '', $tanggalsana).'/'.$pengguna.'/'.$kodeJurnal.'/'.$konter;
                                unset($dataRes['header']);
                                $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggalsana, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $jumlah * $hargarata, 'totalkredit' => -1 * $jumlah * $hargarata, 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                                $keterangan = 'Pemakaian barang '.$namabarang.' '.$jumlah.' '.$satuan.' '.substr($_POST['tanggal'], 0, 7);
                                $keterangan = substr($keterangan, 0, 150);
                                $noUrut = 1;
                                unset($dataRes['detail']);
                                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggalsana, 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $keterangan, 'jumlah' => $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => $kodekegiatan, 'kodeasset' => $kodeasset, 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                                ++$noUrut;
                                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggalsana, 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => $keterangan, 'jumlah' => -1 * $jumlah * $hargarata, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => $kodemesin, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
                                ++$noUrut;
                                if ((substr($kodebarang, 0, 3) < '400' || substr($kodebarang, 0, 1)=='9') && trim($akunbarang)!='') {
                                    $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
                                    if (!mysql_query($insHead)) {
//                                        $headErr .= 'Insert Header Error : '.addslashes(mysql_error($conn))."\n";
                                        $headErr .= 'Insert Header Error 10: '.addslashes(mysql_error($conn))."\n";
                                    }

                                    if ($headErr=='') {
                                        $detailErr = '';
                                        foreach ($dataRes['detail'] as $row) {
                                            $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                                            if (!mysql_query($insDet)) {
                                                $detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

                                                break;
                                            }
                                        }
                                        if ($detailErr=='') {
                                            $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."'");
                                            if (!mysql_query($updJurnal)) {
                                                echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
                                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                                if (!mysql_query($RBDet)) {
                                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                    exit();
                                                }

                                                exit();
                                            }

                                            $errGudang = '';
                                            if (!mysql_query($strupdate)) {
                                                echo ' Error update saldobulanan';
                                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                                if (!mysql_query($RBDet)) {
                                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                    exit();
                                                }
                                            } else {
                                                if (!mysql_query($updmaster)) {
                                                    $errGudang = ' Error update masterbarangdt';
                                                }

                                                if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                                }

                                                if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                                    $errGudang = ' Error update statussaldo on masterbarangdt';
                                                }

                                                if ($errGudang!='') {
                                                    echo $errGudang;
                                                    if (!mysql_query($strrollback)) {
                                                        echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                                    }

                                                    $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                                    if (!mysql_query($RBDet)) {
                                                        echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                                    }

                                                    if (!mysql_query($RBDet)) {
                                                        echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                                        exit();
                                                    }

                                                    exit();
                                                }
                                            }
                                        } else {
                                            echo $detailErr;
                                            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                            }

                                            if (!mysql_query($RBDet)) {
                                                echo 'Rollback Delete Header pemilik Error : '.addslashes(mysql_error($conn))."\n";
                                                exit();
                                            }
                                        }
                                    } else {
                                        echo $headErr;
                                        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                        if (!mysql_query($RBDet)) {
                                            echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                            exit();
                                        }
                                    }
                                } else {
                                    $errGudang = '';
                                    if (!mysql_query($strupdate)) {
                                        echo ' Error update saldobulanan';
                                    } else {
                                        if (!mysql_query($updmaster)) {
                                            $errGudang = ' Error update masterbarangdt';
                                        }

                                        if (mysql_affected_rows()!=0 || !@mysql_query($instmaster)) {
                                        }

                                        if ($errGudang=='' && !mysql_query($updflagststussaldo)) {
                                            $errGudang = ' Error update statussaldo on masterbarangdt';
                                        }

                                        if ($errGudang!='') {
                                            echo $errGudang;
                                            if (!mysql_query($strrollback)) {
                                                echo 'Rollback saldobulanan Error : '.addslashes(mysql_error($conn))."\n";
                                            }

                                            exit();
                                        }
                                    }
                                }
                            } else {
                                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$header1pemilik."'");
                                if (!mysql_query($RBDet)) {
                                    echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
                                    exit(' Error: Receivers accounting period not the same as warehouse');
                                }

                                exit(' Error: Receivers accounting period not the same as warehouse');
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    echo ' Error: Transaction Period missing';
}

?>