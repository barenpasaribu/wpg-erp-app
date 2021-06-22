<?php



require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$mess = "<html>\r\n               <head>\r\n               </head>\r\n               <body>\r\n               Hardaya Plantations Group Preventive Maintenance, remind you for the folowing task:<br>";
$str = 'select a.kodebarang,b.namabarang,a.jumlah,a.id,b.satuan from '.$dbname.".schedulerdt a left join \r\n          ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\n            left join ".$dbname.'.schedulerht c on a.id=c.id';
$res = mysql_query($str);
$detail = [];
while ($bar = mysql_fetch_object($res)) {
    $detail[$bar->id]['namabarang'][] = $bar->namabarang;
    $detail[$bar->id]['jumlah'][] = $bar->jumlah;
    $detail[$bar->id]['kodebarang'][] = $bar->kodebarang;
    $detail[$bar->id]['satuan'][] = $bar->satuan;
}
$str = 'SELECT max(tanggal) as tanggal, id, nilai FROM '.$dbname.'.scheduler_aksi group by id';
$res = mysql_query($str);
$lastReminder = [];
while ($bar = mysql_fetch_object($res)) {
    $lastReminder[$bar->id] = $bar->nilai;
}
$kmAhir = [];
$str = 'select * from '.$dbname.'.vhc_kmhmakhir_vw order by kodevhc';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kmAhir[$bar->kodevhc] = $bar->kmhmakhir;
}
$str = 'select sum(hmmesin) as hm, mesin as kodevhc from '.$dbname.'.pabrik_hmmesin_vw group by mesin order by mesin';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kmAhir[$bar->kodevhc] = $bar->hm;
}
$str = 'select * from '.$dbname.'.schedulerht  order by batasreminder asc';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $resetdong[$bar->kodemesin] = $bar->resethmkm;
    if (0 == $bar->batasreminder || '' == $bar->batasreminder) {
        if (date('Y-m-d') == $bar->setiaptanggal && 2 == $bar->sekali) {
            $subject = 'Hardaya Plantations Group Preventive Maintenance '.$bar->kodemesin;
            $mess .= "<table>\r\n                                <thead>\r\n                                </thead>\r\n                                <tbody>\r\n                                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>\r\n                                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>\r\n                                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    \r\n                                     <tr><td>Warning On</td><td>:".tanggalnormal($bar->setiaptanggal)."</td></tr>    \r\n                                </tbody>\r\n                                 </table>";
            if (0 < count($detail[$bar->id]['namabarang'])) {
                $mess .= 'Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>';
                foreach ($detail[$bar->id]['namabarang'] as $detil => $val) {
                    $mess .= '<tr><td>'.$detail[$bar->id]['kodebarang'][$detil].'</td><td>'.$detail[$bar->id]['namabarang'][$detil].'</td><td>'.$detail[$bar->id]['jumlah'][$detil].' '.$detail[$bar->id]['satuan'][$detil].'</td></tr>';
                }
                $mess .= '</table>';
            }

            $mess .= '<br><br>Regards,<br>eAgro Plantation Management Software</body></html>';
            $to = $bar->email;
            if ('' != $to) {
                $kirim = kirimEmailWindows($to, $subject, $mess);
            }

            $stru = 'update '.$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
            mysql_query($stru);
            $stri = 'insert into '.$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)\r\n                            values(".$bar->id.",\r\n                                       '".date('Y-m-d')."',\r\n                                       '".$bar->kodeorg."',\r\n                                       '".$bar->ketrangan."',\r\n                                       '".$bar->email."',0,0,'')";
            mysql_query($stri);
        } else {
            if (date('m-d') == substr($bar->setiaptanggal, 5, 5) && 1 == $bar->sekali) {
                $subject = 'Hardaya Plantations Group Preventive Maintenance '.$bar->kodemesin;
                $mess .= "<table>\r\n                                <thead>\r\n                                </thead>\r\n                                <tbody>\r\n                                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>\r\n                                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>\r\n                                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    \r\n                                     <tr><td>Warning On</td><td>:".tanggalnormal($bar->setiaptanggal)."</td></tr>    \r\n                                </tbody>\r\n                                 </table>";
                if (0 < count($detail[$bar->id]['namabarang'])) {
                    $mess .= 'Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>';
                    foreach ($detail[$bar->id]['namabarang'] as $detil => $val) {
                        $mess .= '<tr><td>'.$detail[$bar->id]['kodebarang'][$detil].'</td><td>'.$detail[$bar->id]['namabarang'][$detil].'</td><td>'.$detail[$bar->id]['jumlah'][$detil].' '.$detail[$bar->id]['satuan'][$detil].'</td></tr>';
                    }
                    $mess .= '</table>';
                }

                $mess .= '<br><br>Regards,<br>eAgro Plantation Management Software</body></html>';
                $to = $bar->email;
                if ('' != $to) {
                    $kirim = kirimEmailWindows($to, $subject, $mess);
                }

                $stru = 'update '.$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri = 'insert into '.$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)\r\n                            values(".$bar->id.",\r\n                                       '".date('Y-m-d')."',\r\n                                       '".$bar->kodeorg."',\r\n                                       '".$bar->ketrangan."',\r\n                                       '".$bar->email."',0,0,'')";
                mysql_query($stri);
            }
        }
    } else {
        if ('0000-00-00' != $bar->tastreminder && 2 == $bar->sekali) {
        } else {
            $batasAtas = $bar->batasatas;
            $peringatan = $bar->batasreminder;
            $saatIni = $kmAhir[$bar->kodemesin] - $resetdong[$bar->kodemesin];
            if ('' == $saatIni) {
                $saatIni = 0;
            }

            $peringatanTerakhir = $lastReminder[$bar->id];
            if ('' == $peringatanTerakhir) {
                $peringatanTerakhir = 0;
            }

            $z = $saatIni % $batasAtas;
            if ('' == $z) {
                $z = 0;
            }

            $akumulasi = $saatIni - $z;
            $sisa = $z;
            if ($peringatan <= $sisa && $peringatanTerakhir < $akumulasi) {
                $subject = 'Hardaya Plantations Group Preventive Maintenance '.$bar->kodemesin;
                $mess .= "<table>\r\n                <thead>\r\n                </thead>\r\n                <tbody>\r\n                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>\r\n                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>\r\n                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    \r\n                     <tr><td>Warning On</td><td>:".$saatIni.' '.$bar->satuan."</td></tr>    \r\n                </tbody>\r\n                 </table>";
                if (0 < count($detail[$bar->id]['namabarang'])) {
                    $mess .= 'Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>';
                    foreach ($detail[$bar->id]['namabarang'] as $detil => $val) {
                        $mess .= '<tr><td>'.$detail[$bar->id]['kodebarang'][$detil].'</td><td>'.$detail[$bar->id]['namabarang'][$detil].'</td><td>'.$detail[$bar->id]['jumlah'][$detil].' '.$detail[$bar->id]['satuan'][$detil].'</td></tr>';
                    }
                    $mess .= '</table>';
                }

                $mess .= '<br><br>Regards,<br>eAgro Plantation Management Software</body></html>';
                $to = $bar->email;
                if ('' != $to) {
                    $kirim = kirimEmailWindows($to, $subject, $mess);
                }

                $stru = 'update '.$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri = 'insert into '.$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)\r\n                            values(".$bar->id.",\r\n                                       '".date('Y-m-d')."',\r\n                                       '".$bar->kodeorg."',\r\n                                       '".$bar->ketrangan."',\r\n                                       '".$bar->email."',0,0,'".$saatIni."')";
                mysql_query($stri);
            }
        }
    }
}
$str = 'select kodebarang,namabarang,satuan,minstok from '.$dbname.'.log_5masterbarang where minstok>0 order by kodebarang';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $barang[$bar->kodebarang] = $bar->kodebarang;
    $namabarang[$bar->kodebarang] = $bar->namabarang;
    $satuan[$bar->kodebarang] = $bar->satuan;
    $minstok[$bar->kodebarang] = $bar->minstok;
}
$str = 'select sum(saldoqty) as saldo, a.kodebarang, kodeorg,b.minstok from '.$dbname.".log_5masterbarangdt a\r\n          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang where b.minstok>0\r\n          group by kodeorg,a.kodebarang\r\n          having (saldo < minstok or saldo=minstok)";
$res = mysql_query($str);
$mess1 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
if (0 < mysql_num_rows($res)) {
    $subject = ' Hardaya Plantations minimum stock reminder (On: '.date('d-m-Y H:i:s').')';
    $mess1 .= "Dear All,<br>Berikut ini adalah Material yang sudah mencapai batas minimun. Segera lakukan pengadaan untuk barang:\r\n                        <table border=1 cellspacing=0>\r\n                        <thead>\r\n                         <tr><td>No.</td>\r\n                         <td>PT</td>\r\n                         <td>Kodebarang</td>\r\n                         <td>Nama Barang</td>\r\n                         <td>Satuan</td>\r\n                         <td>Saldo Saat Ini</td>\r\n                         <td>Min.Saldo</td>\r\n                         </tr>   \r\n                        </thead>  \r\n                    <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $mess1 .= '<tr><td>'.$no.'</td><td>'.$bar->kodeorg.'</td><td>'.$bar->kodebarang."</td>\r\n                                  <td>".$namabarang[$bar->kodebarang].'</td><td>'.$satuan[$bar->kodebarang]."</td>\r\n                                   <td align=right>".number_format($bar->saldo, 0).'</td><td align=right>'.number_format($bar->minstok, 0)."</td>\r\n                                    </tr>";
    }
    $mess1 .= '</tbody><tfoot></tfoot></table><br>Regards, <br>eAgro Plantation Management Software<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>';
    $str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter='LOGRM'";
    $res1 = mysql_query($str);
    while ($bar = mysql_fetch_object($res1)) {
        $to1 = $bar->nilai;
    }
    if ('' != $to1) {
        $kirim = kirimEmailWindows($to1, $subject, $mess1);
    }
}

$str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter LIKE 'RCUTI%'";
$res1 = mysql_query($str);
$count = mysql_num_rows($res1);
$to1 = '';
$ax = 1;
while ($bar = mysql_fetch_object($res1)) {
    $to1 .= $bar->nilai;
    if ($ax != $count) {
        $to1 .= ',';
        ++$ax;
    }
}
$str1 = 'select karyawanid,namakaryawan,lokasitugas,email from '.$dbname.".datakaryawan\r\n\t       where (tanggalkeluar is NULL or tanggalkeluar > ".date('Ymd').")  and tanggallahir like '%".date('m-d')."'\r\n                          and tipekaryawan =0";
$res1 = mysql_query($str1);
$mess2 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
while ($bar1 = mysql_fetch_object($res1)) {
    $subject2 = 'Selamat Ulang Tahun !';
    $mess2 .= 'Dear '.$bar1->namakaryawan.",<br><br>\r\n                        Waktu berjalan tiada henti<br> \r\n                        mengiringi rembulan dan mentari yang terbit nan tenggelam setiap hari<br>\r\n                        mengiringi usiamu yang terus bertambah dari hari ke hari<br>\r\n                        hingga saat ini..<br><br>\r\n\r\n                        Selamat ulang tahun ".$bar1->namakaryawan."\r\n                        Sungguh masa depan itu memang ada<br> \r\n                        karena kau telah berhasil melewati satu 1 tahun lagi masa usiamu.<br><br>\r\n\r\n                        Semoga dengan bertambahnya usia menjadikan ".$bar1->namakaryawan." insan yang mulia,<br>\r\n                        semakin bijak dan menjadi berkah bagi lingkungan kehidupan saudara dan \r\n                        semakin berprestasi dan berkarya di Hardaya Plantations Group.<br><br>\r\n\r\n                        Kami segenap Direksi dan Karyawan Hardaya Plantations Group mengucapkan SELAMAT ULANG TAHUN<br>\r\n                        Panjang Umur dan Bahagia selalu dalam hidupmu.<br><br><br><br>\r\n                        \r\n                        Hardaya Plantations Group</body></html>";
    if ('' != $bar1->email) {
        $to2 .= $to1.','.$bar1->email;
    } else {
        $to2 = $to1;
    }

    if ('' != $to2) {
        $kirim = kirimEmailWindows($to2, $subject2, $mess2);
    }

    $mess1 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
}
$t = mktime(0, 0, 0, date('m'), date('d') - 76, date('Y'));
$tanggalmasuk = date('Y-m-d', $t);
$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,subbagian from '.$dbname.".datakaryawan\r\n\t       where  tanggalmasuk='".$tanggalmasuk."'  and tipekaryawan =0";
$res = mysql_query($str1);
$mess3 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
if (0 < mysql_num_rows($res)) {
    $subject3 = ' Reminder Akhir Masa Percobaan Karyawan Baru (On: '.date('d-m-Y H:i:s').')';
    $mess3 .= "Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa percobannya:\r\n                        <table border=1 cellspacing=0>\r\n                        <thead>\r\n                         <tr><td>No.</td>\r\n                         <td>Nama Naryawan</td>\r\n                         <td>TMK</td>\r\n                         <td>Lokasi Tugas</td>\r\n                         <td>Sub.Bagian</td>\r\n                         </tr>   \r\n                        </thead>  \r\n                    <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $mess3 .= '<tr><td>'.$no."</td>\r\n                                  <td>".$bar->namakaryawan."</td>\r\n                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n                                  <td>".$bar->lokasitugas.'</td><td>'.$bar->subbagian."</td>\r\n                                    </tr>";
    }
    $mess3 .= "</tbody><tfoot></tfoot></table><br>\r\n                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>\r\n                             <br>Regards, <br>eAgro Plantation Management Software<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
    if ('' != $to1) {
        $kirim = kirimEmailWindows($to1, $subject3, $mess3);
    }
}

$str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter='KONTRAK'";
$res2 = mysql_query($str);
while ($bar = mysql_fetch_object($res2)) {
    $to2 = $bar->nilai;
}
$t = mktime(0, 0, 0, date('m'), date('d') + 14, date('Y'));
$tanggalkeluar = date('Y-m-d', $t);
$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from '.$dbname.".datakaryawan\r\n\t       where  tanggalkeluar='".$tanggalkeluar."'  and tipekaryawan in(6,2)";
$res = mysql_query($str1);
$mess4 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
if (0 < mysql_num_rows($res)) {
    $subject4 = ' Reminder Akhir Masa Kontrak Karyawan (On: '.date('d-m-Y H:i:s').')';
    $mess4 .= "Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa kontraknya:\r\n                        <table border=1 cellspacing=0>\r\n                        <thead>\r\n                         <tr><td>No.</td>\r\n                         <td>Nama Naryawan</td>\r\n                         <td>TMK</td>\r\n                         <td>Lokasi Tugas</td>\r\n                         <td>Sub.Bagian</td>\r\n                         <td>Akhir.Kontrak</td>\r\n                         </tr>   \r\n                        </thead>  \r\n                    <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $mess4 .= '<tr><td>'.$no."</td>\r\n                                  <td>".$bar->namakaryawan."</td>\r\n                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n                                  <td>".$bar->lokasitugas."</td>\r\n                                  <td>".$bar->subbagian."</td>\r\n                                  <td>".tanggalnormal($bar->tanggalkeluar)."</td>    \r\n                                    </tr>";
    }
    $mess4 .= "</tbody><tfoot></tfoot></table><br>\r\n                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>\r\n                             <br>Regards, <br>eAgro Plantation Management Software<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
    if ('' != $to2) {
        $kirim = kirimEmailWindows($to2, $subject4, $mess4);
    }
}

$str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter LIKE 'RCUTI%'";
$res2 = mysql_query($str);
$count = mysql_num_rows($res2);
$toCuti = '';
$ax = 1;
while ($bar = mysql_fetch_object($res2)) {
    $toCuti .= $bar->nilai;
    if ($ax != $count) {
        $toCuti .= ',';
        ++$ax;
    }
}
$mess4 = '';
$t = mktime(0, 0, 0, date('m'), date('d') + 60, date('Y'));
$tanggalakhir = date('Y-m-d', $t);
$tglll = date('m-d', $t);
$tmasuk = date('Y', $t);
$tmasuk = $tmasuk - 1;
$tnorm = date('d-m-Y', $t);
$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from '.$dbname.".datakaryawan\r\n\t       where  tanggalmasuk like '%".$tglll."'  and tipekaryawan in(1,2,3) and left(tanggalmasuk,4)<=".$tmasuk." and (tanggalkeluar is NULL or tanggalkeluar>'".$tanggalakhir."')";
$res = mysql_query($str1);
$mess4 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
if (0 < mysql_num_rows($res)) {
    $subject4 = ' Reminder akhir masa cuti tahunan karyawan ['.date('d-m-Y H:i:s').']';
    $mess4 .= "Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa cuti tahunan:\r\n                        <table border=1 cellspacing=0>\r\n                        <thead>\r\n                         <tr><td>No.</td>\r\n                         <td>Nama Naryawan</td>\r\n                         <td>TMK</td>\r\n                         <td>Lokasi Tugas</td>\r\n                         <td>Sub.Bagian</td>\r\n                         <td>Akhir.Cuti</td>\r\n                         </tr>   \r\n                        </thead>  \r\n                    <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $mess4 .= '<tr><td>'.$no."</td>\r\n                                  <td>".$bar->namakaryawan."</td>\r\n                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n                                  <td>".$bar->lokasitugas."</td>\r\n                                  <td>".$bar->subbagian."</td>\r\n                                  <td>".$tnorm."</td>    \r\n                                    </tr>";
    }
    $mess4 .= '</tbody><tfoot></tfoot></table>';
    $mess4 .= "<br>\r\n                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>\r\n                             <br>Regards, <br>eAgro Plantation Management Software<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
    if ('' != $toCuti) {
        $kirim = kirimEmailWindows($toCuti, $subject4, $mess4);
    }
}

$str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter LIKE 'RCUTI%'";
$res2 = mysql_query($str);
$count = mysql_num_rows($res2);
$toCuti = '';
$ax = 1;
while ($bar = mysql_fetch_object($res2)) {
    $toCuti .= $bar->nilai;
    if ($ax != $count) {
        $toCuti .= ',';
        ++$ax;
    }
}
$mess4 = '';
$t = mktime(0, 0, 0, date('m'), date('d') + 60, date('Y'));
$tanggalakhir = date('Y-m-d', $t);
$tnorm = date('d-m-Y', $t);
$t = mktime(0, 0, 0, date('m'), date('d') - 2130, date('Y'));
$masuknya1 = date('Y-m-d', $t);
$t = mktime(0, 0, 0, date('m'), date('d') - 4320, date('Y'));
$masuknya2 = date('Y-m-d', $t);
$t = mktime(0, 0, 0, date('m'), date('d') - 6510, date('Y'));
$masuknya3 = date('Y-m-d', $t);
$t = mktime(0, 0, 0, date('m'), date('d') - 8700, date('Y'));
$masuknya4 = date('Y-m-d', $t);
$t = mktime(0, 0, 0, date('m'), date('d') - 10890, date('Y'));
$masuknya5 = date('Y-m-d', $t);
$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from '.$dbname.".datakaryawan\r\n             where  tanggalmasuk in('".$masuknya1."','".$masuknya2."','".$masuknya3."','".$masuknya4."','".$masuknya5."')  \r\n              and tipekaryawan in(1,2,3)  and (tanggalkeluar is NULL or tanggalkeluar>'".$tanggalakhir."')";
$res = mysql_query($str1);
$mess4 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
if (0 < mysql_num_rows($res)) {
    $subject4 = ' Reminder akhir masa cuti panjang karyawan ['.date('d-m-Y H:i:s').']';
    $mess4 .= "Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa cuti panjang:\r\n                        <table border=1 cellspacing=0>\r\n                        <thead>\r\n                         <tr><td>No.</td>\r\n                         <td>Nama Naryawan</td>\r\n                         <td>TMK</td>\r\n                         <td>Lokasi Tugas</td>\r\n                         <td>Sub.Bagian</td>\r\n                         <td>Akhir.Cuti</td>\r\n                         </tr>   \r\n                        </thead>  \r\n                    <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $mess4 .= '<tr><td>'.$no."</td>\r\n                                  <td>".$bar->namakaryawan."</td>\r\n                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n                                  <td>".$bar->lokasitugas."</td>\r\n                                  <td>".$bar->subbagian."</td>\r\n                                  <td>".$tnorm."</td>    \r\n                                    </tr>";
    }
    $mess4 .= '</tbody><tfoot></tfoot></table>';
    $mess4 .= "<br>\r\n                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>\r\n                             <br>Regards, <br>eAgro Plantation Management Software<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
    if ('' != $toCuti) {
        $kirim = kirimEmailWindows($toCuti, $subject4, $mess4);
    }
}

$str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter LIKE 'RCUTI%'";
$res2 = mysql_query($str);
$count = mysql_num_rows($res2);
$toCuti = '';
$ax = 1;
while ($bar = mysql_fetch_object($res2)) {
    $toCuti .= $bar->nilai;
    if ($ax != $count) {
        $toCuti .= ',';
        ++$ax;
    }
}
$mess4 = '';
$str1 = 'select a.namakaryawan, a.lokasitugas,b.nama,b.hubungankeluarga from '.$dbname.".datakaryawan a left join\r\n           ".$dbname.".sdm_karyawankeluarga b on a.karyawanid=b.karyawanid where \r\n             COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',b.tanggallahir)/365.25,1),0)>20.9\r\n             and b.tanggungan=1 and b.hubungankeluarga='Anak'";
$res = mysql_query($str1);
$mess4 = "<html>\r\n               <head>\r\n               </head>\r\n               <body>";
if (0 < mysql_num_rows($res)) {
    $subject4 = ' Reminder karyawan 21th ['.date('d-m-Y H:i:s').']';
    $mess4 .= "Dear Hrd,<br>Berikut data karyawan yang harus diupdate berkaitan dengan umur tanggungan sudah 21 Th:\r\n                        <table border=1 cellspacing=0>\r\n                        <thead>\r\n                         <tr><td>No.</td>\r\n                         <td>Nama Naryawan</td>\r\n                         <td>Loaksi Tugas</td>\r\n                         <td>Nama Tanggungan</td>\r\n                         <td>Hubungan Keluarga</td>\r\n                         </tr>   \r\n                        </thead>  \r\n                    <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $mess4 .= '<tr><td>'.$no."</td>\r\n                                  <td>".$bar->namakaryawan."</td>\r\n                                  <td>".$bar->loaksitugas."</td>\r\n                                  <td>".$bar->nama."</td>\r\n                                  <td>".$bar->hubungankeluarga."</td>\r\n                                    </tr>";
    }
    $mess4 .= '</tbody><tfoot></tfoot></table>';
    $mess4 .= "<br>\r\n                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>\r\n                             <br>Regards, <br>eAgro Plantation Management Software<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
    if ('' != $toCuti) {
        $kirim = kirimEmailWindows($toCuti, $subject4, $mess4);
    }
}

?>