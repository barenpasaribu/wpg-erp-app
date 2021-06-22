<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
include_once 'lib/devLibrary.php';
$param = $_POST;

        $sdata = "select * from ".$dbname.".keu_5parameterjurnal where jurnalid='HPP2'";
        $qdata = mysql_query($sdata);
        $rdata = mysql_fetch_assoc($qdata);
//        saveLog($sdata);

        $str = "select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
        $qry = mysql_query($str);
        $data = mysql_fetch_assoc($qry);
//        saveLog($str);

        $kodeJurnal = 'HPP1';
        $tgl=date_create($data['tanggalsampai']);
        $tgmulaid = date_format($tgl,'Ymd');
        $pengguna = substr($_SESSION['empl']['lokasitugas'], 0, 3);

        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter','kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
        $nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

        $sinsert = "insert into $dbname.keu_jurnalht (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) values ('".$nojurnal."','HPP','".$data['tanggalsampai']."','".date('Y-m-d')."','1','".$param['hargapemakaiantbs']."','-".$param['hargapemakaiantbs']."','0','','1','IDR','1','0')";
//        saveLog($sinsert);

            if (mysql_query($sinsert)) {
                //DEBET
                $no=1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                    ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','". $rdata['noakundebet']."','PEMAKAIAN TBS PERIODE ".$param['periode']."','".$param['hargapemakaiantbs']."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

                //KREDIT
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','".$rdata['noakunkredit']."','PEMAKAIAN TBS PERIODE ".$param['periode']."','-".$param['hargapemakaiantbs']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

            $upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";

            $upcount=mysql_query($upcounter);

        }


//JURNAL PRODUKSI (PERSEDIAAN CPO/PK)
        $sdata1 = "select * from ".$dbname.".keu_5parameterjurnal where jurnalid='HPP3'";
        $qdata1 = mysql_query($sdata1);
        $rdata1 = mysql_fetch_assoc($qdata1);
//        saveLog($sdata);

        $kodeJurnal = 'HPP1';
        $tgl=date_create($data['tanggalsampai']);
        $tgmulaid = date_format($tgl,'Ymd');
        $pengguna = substr($_SESSION['empl']['lokasitugas'], 0, 3);

        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter','kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
        $nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

        $sinsert = "insert into $dbname.keu_jurnalht (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) values ('".$nojurnal."','HPP','".$data['tanggalsampai']."','".date('Y-m-d')."','1','".($param['produksicpo']+$param['produksipk'])."','-".($param['produksicpo']+$param['produksipk'])."','0','','1','IDR','1','0')";
//        saveLog($sinsert);

            if (mysql_query($sinsert)) {
                //DEBET
                $no=1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                    ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','". $rdata1['noakundebet']."','PRODUKSI (PERSEDIAAN CPO/PK) PERIODE ".$param['periode']."','".$param['produksicpo']."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//          saveLog($sins);
                
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                    ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','". $rdata1['sampaidebet']."','PRODUKSI (PERSEDIAAN CPO/PK) PERIODE ".$param['periode']."','".$param['produksipk']."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);

                //KREDIT
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','".$rdata1['noakunkredit']."','PRODUKSI (PERSEDIAAN CPO/PK) PERIODE ".$param['periode']."','-".($param['produksicpo']+$param['produksipk'])."','IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

            $upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";

            $upcount=mysql_query($upcounter);

        }


//JURNAL JURNAL HARGA POKOK PENJUALAN CPO/PK

        $sdata2 = "select * from ".$dbname.".keu_5parameterjurnal where jurnalid='HPP4'";
        $qdata2 = mysql_query($sdata2);
        $rdata2 = mysql_fetch_assoc($qdata2);
//        saveLog($sdata);


        $kodeJurnal = 'HPP1';
        $tgl=date_create($data['tanggalsampai']);
        $tgmulaid = date_format($tgl,'Ymd');
        $pengguna = substr($_SESSION['empl']['lokasitugas'], 0, 3);

        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter','kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
        $nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

        $sinsert = "insert into $dbname.keu_jurnalht (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) values ('".$nojurnal."','HPP','".$data['tanggalsampai']."','".date('Y-m-d')."','1','".($param['penjualancpo']+$param['penjualanpk'])."','-".($param['penjualancpo']+$param['penjualanpk'])."','0','','1','IDR','1','0')";
//        saveLog($sinsert);

            if (mysql_query($sinsert)) {
                //DEBET
                $no=1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                    ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','". $rdata2['noakundebet']."','PENJUALAN CPO PERIODE ".$param['periode']."','".$param['penjualancpo']."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                    ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','". $rdata2['sampaidebet']."','PENJUALAN PK PERIODE ".$param['periode']."','".$param['penjualanpk']."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

                //KREDIT
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','".$rdata2['noakunkredit']."','PENJUALAN CPO PERIODE ".$param['periode']."','-".$param['penjualancpo']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg, kodecustomer, noreferensi, nodok, revisi, nik, kodesupplier) values 
                ('".$nojurnal."','".$data['tanggalsampai']."','".$no."','".$rdata2['sampaikredit']."','PENJUALAN PK PERIODE ".$param['periode']."','-".$param['penjualanpk']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','','','','0','','')";
                mysql_query($sins);
//                saveLog($sins);

            $upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";

            $upcount=mysql_query($upcounter);

        }

        $str="insert into ".$dbname.".flag_alokasi(kodeorg,periode,tipe) values('".$param['kodeorg']."','".$param['periode']."','HPP')";
        mysql_query($str);

?>