<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetransaksi = 7;
if (isTransactionPeriod()) {
    $nodok = $_POST['nodok'];
    $tanggal = tanggalsystem($_POST['tanggal']);
    $kodebarang = $_POST['kodebarang'];
    $kegudang = $_POST['kegudang'];
    $satuan = $_POST['satuan'];
    $qty = $_POST['qty'];
    $gudang = $_POST['gudang'];
    $catatan = $_POST['catatan'];
    $pemilikbarang = $_POST['pemilikbarang'];
    $user = $_SESSION['standard']['userid'];
    $post = 0;
    $status = 0;
    $user1 = $_SESSION['standard']['userid'];
    if (0 == $_POST['statusInput']) {
        $antri = 0;
        if(0 == $antri) {
            $str = 'select user from '.$dbname.".log_transaksiht where notransaksi='".$nodok."'";

            $res = mysql_query($str);
            if (1 == mysql_num_rows($res)) {

                $antri = 0;
                $num = 1;
                $str = 'select max(notransaksi) notransaksi from '.$dbname.'.log_transaksiht where tipetransaksi>4 and tanggal>='.$_SESSION['gudang'][$gudang]['start'].' and tanggal<='.$_SESSION['gudang'][$gudang]['end']."\r\n                                and kodegudang='".$gudang."' order by notransaksi desc limit 1";

                if ('KEBUN' == $_SESSION['empl']['tipelokasitugas']) {
                    $str = '';
                    $str = 'select max(notransaksi) notransaksi from '.$dbname.'.log_transaksiht where tipetransaksi>4 and tanggal>='.$_SESSION['gudang'][$gudang]['start'].' and tanggal<='.$_SESSION['gudang'][$gudang]['end']."\r\n                                    and kodegudang='".$gudang."' and substr( `notransaksi` , 7, 1 ) not like '%M%' order by notransaksi desc limit 1";
                }

                if ($res = mysql_query($str)) {

                    while ($bar = mysql_fetch_object($res)) {
                        $num = $bar->notransaksi;
                        if ('' != $num) {
                            $num = (int) (substr($num, 6, 5)) + 1;
                        } else {
                            $num = 1;
                        }
                    }

                    if ($num < 10) {
                        $num = '0000'.$num;
                    } else {
                        if ($num < 100) {
                            $num = '000'.$num;
                        } else {
                            if ($num < 1000) {
                                $num = '00'.$num;
                            } else {
                                if ($num < 10000) {
                                    $num = '0'.$num;
                                } else {
                                    $num = $num;
                                }
                            }
                        }
                    }


                    $nodok = $_SESSION['gudang'][$gudang]['tahun'].$_SESSION['gudang'][$gudang]['bulan'].$num.'-GI-'.$gudang;

                }

            } else {
                $antri = 1;
            }

        }

    } else {
        $status = 1;
    }

    if ('update' == $method) {
        $status = 2;
    }

    if (isset($_POST['delete'])) {
        $status = 5;
    }

    $str = 'select * from '.$dbname.".log_transaksiht where notransaksi='".$nodok."'\r\n               and post=1";

    if (0 < mysql_num_rows(mysql_query($str))) {
        $status = 3;
    }

    if ('' == $pemilikbarang) {
        $status = 4;
    }

    if (isset($_POST['displayonly'])) {
        $status = 6;
    }

    $jumlahlalu = 0;
    $str = "select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi \r\n            from ".$dbname.".log_transaksidt a,\r\n                 ".$dbname.".log_transaksiht b\r\n                   where a.notransaksi=b.notransaksi \r\n               and a.kodebarang='".$kodebarang."'\r\n                   and a.notransaksi<='".$nodok."'\r\n                   and tipetransaksi>4\r\n                   and b.kodegudang='".$gudang."'\r\n                   order by notransaksi desc, waktutransaksi desc limit 1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $jumlahlalu = $bar->jumlah;
    }
    $qtynotpostedin = 0;
    $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt\r\n               b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' \r\n                           and a.tipetransaksi<5\r\n                           and a.kodegudang='".$gudang."'\r\n                           and a.post=0\t\t\t   \r\n                           group by kodebarang";
    $res2 = mysql_query($str2);
    while ($bar2 = mysql_fetch_object($res2)) {
        $qtynotpostedin = $bar2->jumlah;
    }
    if ('' == $qtynotpostedin) {
        $qtynotpostedin = 0;
    }

    $qtynotposted = 0;
    $str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM '.$dbname.'.log_transaksiht a left join '.$dbname.".log_transaksidt\r\n           b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' \r\n                   and a.tipetransaksi=7\r\n                   and a.kodegudang='".$gudang."'\r\n                   and a.post=0\t\t   \r\n                   group by kodebarang";
    $res2 = mysql_query($str2);
    while ($bar2 = mysql_fetch_object($res2)) {
        $qtynotposted = $bar2->jumlah;
    }
   
    $saldoqty = 0;
    //$strs = 'select saldoqty from '.$dbname.".log_5masterbarangdt where kodebarang='".$kodebarang."'\r\n          and kodeorg='".$pemilikbarang."'\r\n                  and kodegudang='".$gudang."'";
    $strs = 'select saldoakhirqty from '.$dbname.".log_5saldobulanan where kodebarang='".$kodebarang."'\r\n and kodegudang='".$gudang."'";
    $ress = mysql_query($strs);
    while ($bars = mysql_fetch_object($ress)) {
        $saldoqty = $bars->saldoakhirqty;
    }
    if (0 == $status || 1 == $status) {
        if ($saldoqty + $qtynotpostedin < $qty + $qtynotposted) {
            echo ' Error: ('.$strs.')'.$_SESSION['lang']['saldo'].' '.$_SESSION['lang']['tidakcukup'].' '.$saldoqty.'+'.$qtynotpostedin.'-'.$qtynotposted.'='.$qty;
            $status = 6;
            exit(0);
        }
    } else {
        if (2 == $status) {
            $jlhlama = 0;
            $strt = 'select jumlah from '.$dbname.".log_transaksidt where notransaksi='".$nodok."'\r\n               and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
            $rest = mysql_query($strt);
            while ($bart = mysql_fetch_object($rest)) {
                $jlhlama = $bart->jumlah;
            }
            if ($saldoqty + $jlhlama + $qtynotpostedin < $qty + $qtynotposted) {
                echo ' Error: '.$_SESSION['lang']['saldo'].' '.$_SESSION['lang']['tidakcukup'];
                $status = 6;
                exit(0);
            }
        }
    }

    if (0 == $status) {
        $sKdPt = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".substr($kegudang, 0, 4)."'";
        $qKdPt = mysql_query($sKdPt); // || exit(mysql_error($sKdPt));
        $rKdpt = mysql_fetch_assoc($qKdPt);
        if ('' == $rKdpt['induk']) {
			echo "warning: ".$sKdPt. ' / rKdpt='.$rKdpt. '  /  ';
            exit('Kode PT Penerima Kosong');
        }

        $str = 'insert into '.$dbname.".log_transaksiht (\r\n                          `tipetransaksi`,`notransaksi`,\r\n                          `tanggal`,`kodept`,`untukpt`,\r\n                          `gudangx`,`keterangan`,\r\n                          `kodegudang`,`user`,\r\n                          `post`)\r\n                values(".$tipetransaksi.",'".$nodok."',\r\n                       ".$tanggal.",'".$pemilikbarang."','".$rKdpt['induk']."',\r\n                          '".$kegudang."','".$catatan."',\r\n                          '".$gudang."',".$user.",\r\n                           ".$post."\r\n                )";
        if (mysql_query($str)) {
            $str = 'insert into '.$dbname.".log_transaksidt (\r\n                          `notransaksi`,`kodebarang`,\r\n                          `satuan`,`jumlah`,`jumlahlalu`,\r\n                          `updateby`)\r\n                          values('".$nodok."','".$kodebarang."',\r\n                          '".$satuan."',".$qty.','.$jumlahlalu.",\r\n                          '".$user."')";
            if (mysql_query($str)) {
            } else {
                echo ' Gagal, (insert detail on status 0)'.addslashes(mysql_error($conn)).$str;
                exit(0);
            }
        } else {
            echo ' Gagal,  (insert header on status 0)'.addslashes(mysql_error($conn)).$str;
            exit(0);
        }
    }

    if (1 == $status) {
        $scek = 'select distinct * from '.$dbname.".log_transaksiht where notransaksi='".$nodok."' and tipetransaksi=7";
        $qcek = mysql_query($scek);
        $rcek = mysql_num_rows($qcek);
        if (0 == $rcek) {
			$sKdPt = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".substr($kegudang, 0, 4)."'";
			$qKdPt = mysql_query($sKdPt) || exit(mysql_error($sKdPt));
			$rKdpt = mysql_fetch_assoc($qKdPt);
			if ('' == $rKdpt['induk']) {
				exit('Kode PT Penerima Kosong');
			}

			$str = 'insert into '.$dbname.".log_transaksiht (\r\n                          `tipetransaksi`,`notransaksi`,\r\n                          `tanggal`,`kodept`,`untukpt`,\r\n                          `gudangx`,`keterangan`,\r\n                          `kodegudang`,`user`,\r\n                          `post`)\r\n                values(".$tipetransaksi.",'".$nodok."',\r\n                       ".$tanggal.",'".$pemilikbarang."','".$rKdpt['induk']."',\r\n                          '".$kegudang."','".$catatan."',\r\n                          '".$gudang."',".$user.",\r\n                           ".$post."\r\n                )";
			if (mysql_query($str)) {
				$str = 'insert into '.$dbname.".log_transaksidt (\r\n                          `notransaksi`,`kodebarang`,\r\n                          `satuan`,`jumlah`,`jumlahlalu`,\r\n                          `updateby`)\r\n                          values('".$nodok."','".$kodebarang."',\r\n                          '".$satuan."',".$qty.','.$jumlahlalu.",\r\n                          '".$user."')";
				if (mysql_query($str)) {
				} else {
					echo ' Gagal, (insert detail on status 1)'.addslashes(mysql_error($conn));
					exit(0);
				}
			} else {
				echo ' Gagal,  (insert header on status 0)'.addslashes(mysql_error($conn)).$str;
				exit(0);
			}
            #exit('Error: This transaction belongs to other user, please reload and start over');
        } else {
			$str = 'insert into '.$dbname.".log_transaksidt (\r\n                          `notransaksi`,`kodebarang`,\r\n                          `satuan`,`jumlah`,`jumlahlalu`,\r\n                          `updateby`)\r\n                          values('".$nodok."','".$kodebarang."',\r\n                          '".$satuan."',".$qty.','.$jumlahlalu.",\r\n                          '".$user."')";
				if (mysql_query($str)) {
				} else {
					echo ' Gagal, (insert detail on status 1)'.addslashes(mysql_error($conn));
					exit(0);
				}
		}

        
    }

    if (2 == $status) {
        $str = 'update '.$dbname.".log_transaksidt set\r\n                              `jumlah`=".$qty.",\r\n                                  `updateby`=".$user.",\r\n                                  where `notransaksi`='".$nodok."'\r\n                                  and `kodebarang`='".$kodebarang."'";
        mysql_query($str);
        if (mysql_affected_rows($conn) < 1) {
            echo ' Gagal, (update detail on status 2)'.addslashes(mysql_error($conn));
            exit(0);
        }
    }

    if (3 == $status) {
        echo ' Gagal: Data has been posted';
        exit(0);
    }

    if (4 == $status) {
        echo ' Gagal: Company code of the Recipient is not defined';
        exit(0);
    }

    if (5 == $status) {
        $str = 'delete from '.$dbname.".log_transaksidt where kodebarang='".$kodebarang."'\r\n                 and notransaksi='".$nodok."'";
        mysql_query($str);
        if (0 < mysql_affected_rows($conn)) {
        }
    }

    $strj = 'select a.* from '.$dbname.".log_transaksidt a \r\n        where a.notransaksi='".$nodok."'";
    $resj = mysql_query($strj);
    $no = 0;
    while ($barj = mysql_fetch_object($resj)) {
        ++$no;
        $namabarangk = '';
        $strk = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$barj->kodebarang."'";
        $resk = mysql_query($strk);
        while ($bark = mysql_fetch_object($resk)) {
            $namabarangk = $bark->namabarang;
        }
        $stream .= "<tr class=rowcontent>\r\n                    <td>".$no."</td>\r\n                        <td>".$barj->kodebarang."</td>\r\n                        <td>".$namabarangk."</td>\r\n                        <td>".$barj->satuan."</td>\r\n                        <td align=right>".number_format($barj->jumlah, 2, '.', ',')."</td>\r\n                        <td>\r\n                        &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delMutasi('".$nodok."','".$barj->kodebarang."');\">\r\n                        </td>\r\n                   </tr>";
    }
    if (6 == $status) {
        echo $stream;
    } else {
        echo $stream.'####'.$nodok;
    }
} else {
    echo ' Error: Transaction Period missing';
}


?>