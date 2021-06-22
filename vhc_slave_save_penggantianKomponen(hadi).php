<?php
session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$lokasi = $_SESSION['empl']['lokasitugas'];
$tglGanti = tanggalsystem($_POST['tglGanti']);
$kdJenis = $_POST['kdjenis'];
$usr_id = $_SESSION['standard']['userid'];
$notransaksi = $_POST['notrans'];
$codeOrg = $_POST['codeOrg'];
$descDmg = $_POST['descDmg'];
$dwnTime = $_POST['dwnTime'];
$statInp = $_POST['statInp'];
$tglMasuk = tanggalsystem($_POST['tglMasuk']);
$tglSelesai = tanggalsystem($_POST['tglSelesai']);
$tglAmbil = tanggalsystem($_POST['tglAmbil']);
$kmhmMasuk = $_POST['kmhmMasuk'];
$namaMekanik1 = $_POST['namaMekanik1'];
$namaMekanik2 = $_POST['namaMekanik2'];
$namaMekanik3 = $_POST['namaMekanik3'];
$namaMekanik4 = $_POST['namaMekanik4'];
$namaMekanik5 = $_POST['namaMekanik5'];
$jammasuk = $_POST['jm1'].':'.$_POST['mn1'].':00';
$jamselesai = $_POST['jm2'].':'.$_POST['mn2'].':00';
$noGudang = $_POST['noGudang'];
$noTranGudang = $_POST['noTranGudang'];
$optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$jumkeluar= 0; // FA 20191106 CDS/LSP
switch ($proses) {
    case 'goCariGudang':
        echo "\r\n\t\t\t\t\t\t<table cellspacing=1 border=0 class=data>\r\n\t\t\t\t\t\t<thead>\r\n\t\t\t\t\t\t\t<tr class=rowheader>\r\n\t\t\t\t\t\t\t\t<td>No</td>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['notransaksi']."</td>\r\n                                <td>".$_SESSION['lang']['nomris']."</td>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['gudang']."</td>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['dibuat']."</td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t</thead>\r\n\t\t\t\t\t</tbody>";
        //$i = 'select * from '.$dbname.".log_transaksiht where tipetransaksi='5' and notransaksi like '%".$noGudang."%' and kodegudang like '%".$_SESSION['empl']['lokasitugas']."%' order by notransaksi desc";
		
		// Yang sudah di posting - FA 20191106 CDS/LSP
        $i = 'select * from '.$dbname.".log_transaksiht where tipetransaksi='5' and post=1 and notransaksi like '%".$noGudang."%' and kodegudang like '%".$_SESSION['empl']['lokasitugas']."%' order by notransaksi desc";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo "\r\n\t\t\t\t\t\t<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickGudang('".$d['notransaksi']."')>\r\n\t\t\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t\t\t<td>".$d['notransaksi']."</td>\r\n                            <td>".$d['nomris']."</td>\r\n\t\t\t\t\t\t\t<td>".$d['kodegudang']."</td>\r\n\t\t\t\t\t\t\t<td>".tanggalnormal($d['tanggal'])."</td>\r\n\t\t\t\t\t\t\t<td>".$optNm[$d['user']]."</td>\r\n\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t";
        }

        break;
    case 'generate_no':
        if ($notransaksi !='') {
            $svhc = 'select kodevhc,jenisvhc,tahunperolehan from '.$dbname.".vhc_5master where kodeorg='".$codeOrg."'";
            $qvhc = mysql_query($svhc);
            while ($rvhc = mysql_fetch_assoc($qvhc)) {
                $optVhc .= "<option value='".$rvhc['kodevhc']."' ".(($rvhc['kodevhc'] === $kdJenis ? 'selected' : '')).'>'.$rvhc['kodevhc'].'['.$rvhc['tahunperolehan'].']</option>';
            }
            echo $optVhc.'###'.$notransaksi;
        } else {
            $tgl = date('Ymd');
            $bln = substr($tgl, 4, 2);
            $thn = substr($tgl, 0, 4);
            $notransaksi = $codeOrg.'/'.date('Y').'/'.date('m').'/';
            $ql = 'select `notransaksi` from '.$dbname.".`vhc_penggantianht` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
            $qr = mysql_query($ql);
            $rp = mysql_fetch_object($qr);
            $awal = substr($rp->notransaksi, -4, 4);
            $awal = (int) $awal;
            $cekbln = substr($rp->notransaksi, -7, 2);
            $cekthn = substr($rp->notransaksi, -12, 4);
            if ($cekbln != $bln && $cekthn != $thn) {
                $awal = 1;
            } else {
                ++$awal;
            }

            $counter = addZero($awal, 4);
            $notransaksi = $codeOrg.'/'.$thn.'/'.$bln.'/'.$counter;
            $svhc = 'select kodevhc,jenisvhc,tahunperolehan from '.$dbname.".vhc_5master where kodeorg='".$codeOrg."'";
            $qvhc = mysql_query($svhc);
            while ($rvhc = mysql_fetch_assoc($qvhc)) {
                $optVhc .= "<option value='".$rvhc['kodevhc']."'>".$rvhc['kodevhc'].'['.$rvhc['tahunperolehan'].']</option>';
            }
            echo $optVhc.'###'.$notransaksi;
        }

        break;
    case 'load_data':
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['list'].'</legend>';
        echo "\r\n                        <table cellspacing=1 border=0 class=sortable>\r\n                <thead>\r\n<tr class=rowheader>\r\n<td>".$_SESSION['lang']['notransaksi']."</td>\r\n<td>".$_SESSION['lang']['tanggal']."</td>\r\n<td>".$_SESSION['lang']['kodevhc']."</td>\r\n<td>".$_SESSION['lang']['jenisvch']."</td>\r\n<td>".$_SESSION['lang']['downtime']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        if ($_SESSION['empl']['tipeinduk']=='KANWIL' || $_SESSION['empl']['tipeinduk']=='HOLDING') {
            $cond .= ' order by `tanggal` desc';
        } else {
            $cond .= " where updateby='".$_SESSION['standard']['userid']."' order by `tanggal` desc";
        }

        $sql2 = 'select count(*) as jmlhrow from '.$dbname.'.vhc_penggantianht '.$cond.'';
        $query2 = mysql_query($sql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.'.vhc_penggantianht '.$cond.' limit '.$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc);
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            $pvhc = 'select kodevhc,jenisvhc from '.$dbname.".vhc_5master where kodevhc='".$rlvhc['kodevhc']."'";
            $qpvhc = mysql_query($pvhc);
            $rpvhc = mysql_fetch_assoc($qpvhc);
            echo "\r\n                                        <tr class=rowcontent>\r\n                                        <td>".$rlvhc['notransaksi']."</td>\r\n                                        <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                                        <td>".$rlvhc['kodevhc']."</td>\r\n                                        <td>".$rpvhc['jenisvhc']."</td>\r\n                                        <td>".$rlvhc['downtime'].'</td>';
            if ($rlvhc['posting']==0) {
                echo "\r\n                                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['kodevhc']."','".$rlvhc['posting']."','".$rlvhc['downtime']."','".$rlvhc['kerusakan']."','".tanggalnormal($rlvhc['tanggalmasuk'])."','".tanggalnormal($rlvhc['tanggalselesai'])."','".tanggalnormal($rlvhc['tanggaldiambil'])."','".substr($rlvhc['jammasuk'], 0, 2)."','".substr($rlvhc['jammasuk'], 3, 2)."','".substr($rlvhc['jamselesai'], 0, 2)."','".substr($rlvhc['jamselesai'], 3, 2)."','".$rlvhc['kmhmmasuk']."','".$rlvhc['namamekanik1']."','".$rlvhc['namamekanik2']."','".$rlvhc['namamekanik3']."','".$rlvhc['namamekanik4']."','".$rlvhc['namamekanik5']."','".$rlvhc['notransaksigudang']."');\">\r\n                                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."','".$rlvhc['kodevhc']."');\" >\t\r\n                                        <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].','.$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>\r\n                                        </tr>";
            } else {
                echo "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].','.$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>";
            }
        }
        echo "\r\n                                        <tr><td colspan=5 align=center>\r\n                                        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                                        <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                                        <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                                        </td>\r\n                                        </tr>";
        echo '</table></fieldset>';
        CLOSE_BOX();

        break;
    case 'delete':
        $sql = 'delete from '.$dbname.".vhc_penggantianht where notransaksi='".$notransaksi."'";
        if (mysql_query($sql)) {
            $sql2 = 'delete from '.$dbname.".vhc_penggantiandt where notransaksi='".$notransaksi."'";
            mysql_query($sql2);
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cari_barang':
        $txtcari = $_POST['txtcari'];
        $noGudang = $_POST['noGudang'];
        //$str = 'select a.kodebarang,a.namabarang,a.satuan from '.$dbname.'.log_5masterbarang a inner join '.$dbname.".log_transaksidt b on a.kodebarang=b.kodebarang where a.kelompokbarang like '3%' and b.notransaksi='".$noGudang."'";
        $str = 'select a.kodebarang,a.namabarang,a.satuan from '.$dbname.'.log_5masterbarang a inner join '.$dbname.".log_transaksidt b on a.kodebarang=b.kodebarang where b.notransaksi='".$noGudang."'";
        $res = mysql_query($str);
        if (mysql_num_rows($res) < 1) {
            echo 'Error: '.$_SESSION['lang']['tidakditemukan'];
        } else {
            echo "\r\n                <fieldset>\r\n                <legend>".$_SESSION['lang']['result']."</legend>\r\n                No. Transaksi Gudang : <input type=text id=noGudang value=".$noGudang." class=myinputtext onkeypress=\"return tanpa_kutip(event);\" disabled maxlength=25>\r\n                <div style=\"width:450px; height:300px; overflow:auto;\">\r\n                        <table class=sortable cellspacing=1 border=0>\r\n                     <thead>\r\n                              <tr class=rowheader>\r\n                                      <td>No</td>\r\n                                          <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                                          <td>".$_SESSION['lang']['namabarang']."</td>\r\n                                          <td>".$_SESSION['lang']['satuan']."</td>\r\n                                          <td>".$_SESSION['lang']['saldo']."</td>\r\n                                          <td>".$_SESSION['lang']['gudang']."</td>\r\n                                  </tr>\r\n                     </thead>\r\n                         <tbody>";
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;

                $jumkeluar = 0;
                $str1 = "select * from log_transaksidt where notransaksi='".$noGudang."' and kodebarang='".$bar->kodebarang."'";
                $res1 = mysql_query($str1);
                while ($bar1 = mysql_fetch_object($res1)) {
                    $jumkeluar = $bar1->jumlah;
                }
				
                $saldoqty = 0;
                $str1 = 'select sum(a.saldoqty) as saldoqty,a.kodegudang,b.namaorganisasi as namaorganisasi from '.$dbname.'.log_5masterbarangdt a inner join '.$dbname.".organisasi b where a.kodegudang=b.kodeorganisasi and a.kodebarang='".$bar->kodebarang."' and a.kodeorg='".$_SESSION['empl']['kodeorganisasi']."'";
                $res1 = mysql_query($str1);
                while ($bar1 = mysql_fetch_object($res1)) {
                    $saldoqty = $bar1->saldoqty;
                    $kodegudang = $bar1->kodegudang;
                    $nmgudang = $bar1->namaorganisasi;
                }

                $qtynotpostedin = 0;
                $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt\r\n                b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$bar->kodebarang."' \r\n                and a.tipetransaksi<5\r\n                and a.post=0\r\n                group by kodebarang";
                $res2 = mysql_query($str2);
                while ($bar2 = mysql_fetch_object($res2)) {
                    $qtynotpostedin = $bar2->jumlah;
                }
                if ($qtynotpostedin=='') {
                    $qtynotpostedin = 0;
                }

                $qtynotposted = 0;
                $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt\r\n                b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$bar->kodebarang."' \r\n                and a.tipetransaksi>4\r\n                and a.post=0\r\n                group by kodebarang";
                $res2 = mysql_query($str2);
                while ($bar2 = mysql_fetch_object($res2)) {
                    $qtynotposted = $bar2->jumlah;
                }
                if ($qtynotposted=='') {
                    $qtynotposted = 0;
                }

                //$saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;
				$saldoqty = $jumkeluar; //FA 20191106
                if ($bar->inactive == 1) {
                    echo "<tr class=rowcontent style='cursor:pointer;'  title='Inactive' >";
                    $bar->namabarang = $bar->namabarang.' [Inactive]';
                } else {
                    if ($saldoqty <= 0) {
                        echo "<tr class=rowcontent style='cursor:pointer;'  title='Negative Stock' >";
                        $bar->namabarang = $bar->namabarang.' [Negative Stock]';
                    } else {
                        $clikData = "\"throwThisRow('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."',".$no.','.$saldoqty.')"';
                        if ($pil == 2) {
                            $clikData = "\"throwThisRow('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."',".$no.','.$saldoqty.')"';
                        }

                        echo "<tr class=rowcontent style='cursor:pointer;' onclick=".$clikData." title='Click' >";
                    }
                }

                echo '           <td>'.$no."</td>\r\n                                  <td>".$bar->kodebarang."</td>\r\n                                  <td>".$bar->namabarang."</td>\r\n                                  <td>".$bar->satuan."</td>\r\n                                  <td align=right>".number_format($saldoqty, 2, ',', '.')."</td>\r\n                                  <td>".$nmgudang.'('.$kodegudang.")</td>\r\n                              </tr>";
            }
            echo "\r\n                                 </tbody>\r\n                                 <tfoot></tfoot>\r\n                                 </table></div></fieldset>";
        }

        break;
    case 'cek_entry_jenis_vhc':
        $sql_cek = 'select * from '.$dbname.".vhc_penggantianht where tanggal ='".$tglGanti."' and kodevhc='".$kdJenis."'";
        $query_cek = mysql_query($sql_cek);
        $res = mysql_fetch_row($query_cek);
        if ($res > 0) {
            echo 'warning: duplicate entry';
            exit();
        }

        if ($codeOrg=='' || $tglGanti=='' || $dwnTime=='' || $descDmg=='') {
            echo 'warning: Please complete form';
            exit();
        }

        break;
    case 'cek_data_header':
        if ($notransaksi!='' || $tglGanti!='' || $dwnTime !='' || $descDmg !='') {
            $sql = 'select * from '.$dbname.".vhc_penggantianht where notransaksi='".$_POST['notrans']."'";
            $query = mysql_query($sql);
            $row = mysql_fetch_row($query);
            if ($row < 1) {
                foreach ($_POST['kdbrg'] as $brs => $isi) {
                    $saldoqty = 0;
                    $str1 = 'select sum(saldoqty) as saldoqty from '.$dbname.".log_5masterbarangdt where kodebarang='".$isi."' and kodeorg='".$_SESSION['empl']['kodeorganisasi']."'";
                    $res1 = mysql_query($str1);
                    while ($bar1 = mysql_fetch_object($res1)) {
                        $saldoqty = $bar1->saldoqty;
                    }
                    $qtynotpostedin = 0;
                    $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$isi."' and a.tipetransaksi<5 and a.post=0 group by kodebarang";
                    $res2 = mysql_query($str2);
                    while ($bar2 = mysql_fetch_object($res2)) {
                        $qtynotpostedin = $bar2->jumlah;
                    }
                    if ($qtynotpostedin=='') {
                        $qtynotpostedin = 0;
                    }

                    $qtynotposted = 0;
                    $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$isi."' and a.tipetransaksi>4 and a.post=0 group by kodebarang";
                    $res2 = mysql_query($str2);
                    while ($bar2 = mysql_fetch_object($res2)) {
                        $qtynotposted = $bar2->jumlah;
                    }
                    if ($qtynotposted=='') {
                        $qtynotposted = 0;
                    }

                    $saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;
                    $kodebarang = $isi;
                    $satuan = $_POST['satuan'][$brs];
                    $jumlah = $_POST['jmlhMinta'][$brs];
                    $keterangan = $_POST['ketrngn'][$brs];
                    if ($kodebarang=='' || $jumlah=='') {
                        echo 'warning: Please complete form';
                        exit();
                    }

                    if ($saldoqty < $jumlah) {
                        echo 'Error :Saldo Barang Di Gudang Tidak Cukup. Saldo Terakhir Di Gudang : '.$saldoqty;
                        exit();
                    }

                    $sins = 'INSERT INTO '.$dbname.".vhc_penggantianht  (`kodeorg`, `kodevhc`, `tanggal`, `updateby`,`notransaksi`, `downtime`, `kerusakan`, `tanggalmasuk`, `jammasuk`,`tanggalselesai`, `jamselesai`, `tanggaldiambil`,`kmhmmasuk`, `namamekanik1`, `namamekanik2`,`namamekanik3`,`namamekanik4`,`namamekanik5`,`notransaksigudang`) VALUES('".$codeOrg."','".$kdJenis."','".$tglGanti."','".$usr_id."','".$notransaksi."','".$dwnTime."','".$descDmg."','".$tglMasuk."','".$jammasuk."','".$tglSelesai."','".$jamselesai."','".$tglAmbil."','".$kmhmMasuk."','".$namaMekanik1."','".$namaMekanik2."','".$namaMekanik3."','".$namaMekanik4."','".$namaMekanik5."','".$noTranGudang."')";
                    if (mysql_query($sins)) {
                        $dins = 'insert into '.$dbname.".vhc_penggantiandt (`notransaksi`,`kodebarang`,`jumlah`,`satuan`,`keterangan`) values ('".$notransaksi."','".$kodebarang."','".$jumlah."','".$satuan."',\r\n                                                '".$keterangan."')";
                        if (mysql_query($dins)) {
                        } else {
                            echo 'DB Error : '.mysql_error($conn);
                        }
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                    }
                }
            }

            //$test = count($_POST['kdbrg']);
            //echo $test;

            break;
        }

            echo 'warning: Please complete form';
            exit();

    case 'insert':
        if ($notransaksi!='' || $tglGanti!='' || $dwnTime!='' || $descDmg!='') {
            $sql = 'select * from '.$dbname.".vhc_penggantianht where notransaksi='".$_POST['notrans']."'";
            $query = mysql_query($sql);
            $row = mysql_num_rows($query);
            if ($row < 1) {
                $sins = 'INSERT INTO '.$dbname.".vhc_penggantianht  (`kodeorg`, `kodevhc`, `tanggal`, `updateby`,`notransaksi`, `downtime`, `kerusakan`,`tanggalmasuk`, `jammasuk`,`tanggalselesai`, `jamselesai`, `tanggaldiambil`,`kmhmmasuk`, `namamekanik1`, `namamekanik2`,`namamekanik3`,`namamekanik4`,`namamekanik5`, `notransaksigudang`) VALUES ('".$codeOrg."','".$kdJenis."','".$tglGanti."','".$usr_id."','".$notransaksi."','".$dwnTime."','".$descDmg."' ,'".$tglMasuk."','".$jammasuk."','".$tglSelesai."','".$jamselesai."','".$tglAmbil."','".$kmhmMasuk."' ,'".$namaMekanik1."','".$namaMekanik2."','".$namaMekanik3."','".$namaMekanik4."','".$namaMekanik5."','".$noTranGudang."')";
                if (mysql_query($sins)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }

                break;
            }

            echo 'warning: Transaction Number already exist';
            exit();
        }

        echo 'warning: Please complete form';
        exit();
    case 'delete_all':
        $sql = 'delete from '.$dbname.".vhc_penggantianht where notransaksi='".$notransaksi."' and kodevhc='".$kdJenis."'";
        if (mysql_query($sql)) {
            $sqld = 'delete from '.$dbname.".vhc_penggantiandt where notransaksi='".$notransaksi."' ";
            if (mysql_query($sqld)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cari_transaksi':
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['result'].'</legend>';
        echo "<div style=\"width:600px; height:450px; overflow:auto;\">\r\n                        <table cellspacing=1 border=0>\r\n                <thead>\r\n<tr class=rowheader>\r\n<td>".$_SESSION['lang']['notransaksi']."</td>\r\n<td>".$_SESSION['lang']['tanggal']."</td>\r\n<td>".$_SESSION['lang']['kodevhc']."</td>\r\n<td>".$_SESSION['lang']['jenisvch']."</td>\r\n<td>".$_SESSION['lang']['downtime']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        if (isset($_POST['txtSearch'])) {
            $txt_search = $_POST['txtSearch'];
            $txt_tgl = tanggalsystem($_POST['txtTgl']);
            $txt_tgl_a = substr($txt_tgl, 0, 4);
            $txt_tgl_b = substr($txt_tgl, 4, 2);
            $txt_tgl_c = substr($txt_tgl, 6, 2);
            $txt_tgl = $txt_tgl_a.'-'.$txt_tgl_b.'-'.$txt_tgl_c;
        } else {
            $txt_search = '';
            $txt_tgl = '';
        }

        if ($txt_search!='') {
            $where = " notransaksi LIKE  '%".$txt_search."%'";
        } else {
            if ($txt_tgl!='') {
                $where .= " tanggal LIKE '".$txt_tgl."'";
            } else {
                if ($txt_tgl!='' && $txt_search!='') {
                    $where .= " notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                }
            }
        }

        if ($txt_search=='' && $txt_tgl=='') {
            $strx = 'select * from '.$dbname.'.vhc_penggantianht where  '.$where.' order by tanggal desc';
        } else {
            $strx = 'select * from '.$dbname.'.vhc_penggantianht where   '.$where.' order by tanggal desc';
        }

        if ($res = mysql_query($strx)) {
            $numrows = mysql_num_rows($res);
            if ($numrows < 1) {
                echo '<tr class=rowcontent><td colspan=5>Not Found</td></tr>';
            } else {
                while ($rlvhc = mysql_fetch_assoc($res)) {
                    $pvhc = 'select kodevhc,jenisvhc from '.$dbname.".vhc_5master where kodevhc='".$rlvhc['kodevhc']."'";
                    $qpvhc = mysql_query($pvhc);
                    $rpvhc = mysql_fetch_assoc($qpvhc);
                    echo "\r\n                                        <tr class=rowcontent>\r\n                                        <td>".$rlvhc['notransaksi']."</td>\r\n                                        <td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                                        <td>".$rlvhc['kodevhc']."</td>\r\n                                        <td>".$rpvhc['jenisvhc']."</td>\r\n                                        <td>".$rlvhc['downtime'].'</td>';
                    if ($rlvhc['updateby'] == $usr_id) {
                        echo "\r\n                                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['kodevhc']."');\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].','.$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\">\t\r\n                                        <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].','.$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>\r\n                                        ";
                    } else {
                        echo "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].','.$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>";
                    }

                    echo '</tr>';
                }
                echo '</tbody></table></div></fieldset>';
            }
        } else {
            echo 'Gagal,'.mysql_error($conn);
        }

        CLOSE_BOX();

        break;
    case 'update_header':
	/* FA 20191107
        $n = 'update '.$dbname.".vhc_penggantianht  set downtime='".$dwnTime."',kerusakan='".$descDmg."',tanggalmasuk='".$tglMasuk."' ,\r\n\t\t\t\t\tjammasuk='".$jammasuk."' ,tanggalselesai='".$tglSelesai."' ,jamselesai='".$jamselesai."',tanggaldiambil='".$tglAmbil."',kmhmmasuk='".$kmhmMasuk."',namamekanik1='".$namaMekanik1."',namamekanik2='".$namaMekanik2."',\r\n\t\t\t\t\tnamamekanik3='".$namaMekanik3."',namamekanik4='".$namaMekanik4.",namamekanik5='".$namaMekanik5."','notransaksigudang='".$noTranGudang."' where notransaksi='".$notransaksi."' ";
        if (mysql_query($n)) {
        } else {
//            echo ' Gagal,'.addslashes(mysql_error($conn));
            echo ' Gagal,'.$n;
        }
	*/
        // no break
    default:
        break;
}

?>