<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$proses = $_POST['proses'];
$kodeTrans = $_POST['kodeTrans'];
$batchVar = $_POST['batchVar'];
$kdOrg = $_POST['kdOrg'];
$jmlhBibitan = $_POST['jmlhBibitan'];
$ket = $_POST['ket'];
$jnsBibitan = $_POST['jnsBibitan'];
$supplierid = $_POST['supplierid'];
$tglProduksi = tanggalsystem($_POST['tglProduksi']);
$tglTnm = tanggalsystem($_POST['tglTnm']);
$optnmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optnmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmkaryawan = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$oldJenisBibit = $_POST['oldJenisBibit'];
$kdOrgTjn = $_POST['kdOrgTjn'];
$intexDt = $_POST['intexDt'];
$kdvhc = $_POST['kdvhc'];
$nmSupir = $_POST['nmSupir'];
$intexDt = $_POST['intexDt'];
$detPeng = $_POST['detPeng'];
$assistenPnb = $_POST['assistenPnb'];
$custId = $_POST['custId'];
$kodeAfd = $_POST['kodeAfd'];
$KegiatanId = $_POST['KegiatanId'];
$jmlhTrima = $_POST['jmlhTrima'];
$nodo = $_POST['nodo'];
$afkirKcmbh = $_POST['afkirKcmbh'];
$jmlhdDo = $_POST['jmlhdDo'];
$jmlRit = $_POST['jmlRit'];
switch ($proses) {
    case 'saveTab1':
        if ('' === $kdOrg || '' === $jmlhBibitan || '' === $jnsBibitan || '' === $supplierid || '' === $tglProduksi || '' === $tglTnm) {
            exit(' Error: '.$_SESSION['lang']['isifield'].'');
        }

        $scek = 'select  post from '.$dbname.".bibitan_mutasi where batch='".$tglTnm."' and kodeorg='".$kdOrg."'";
        $qcek = mysql_query($scek);//;// || exit('Error '.mysql_error($conn));
        $rcek = mysql_num_rows($qcek);
        if (0 == $rcek) {
//            if ('0' === $rcek) {
            $sInsert = 'insert into '.$dbname.".bibitan_batch (batch, tanggal, tanggaltanam, jenisbibit, supplerid, tanggalproduksi,jumlahdo,jumlahterima,jumlahafkir,nodo) \r\n                   values('".$tglTnm."','".$tglTnm."','".$tglTnm."','".$jnsBibitan."','".$supplierid."','".$tglProduksi."','".$jmlhdDo."','".$jmlhTrima."','".$afkirKcmbh."','".$nodo."')";
            if (mysql_query($sInsert)) {
                $sInsert2 = 'insert into '.$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby) \r\n                   values('".$tglTnm."','".$kdOrg."','".$tglTnm."','".$kodeTrans."','".$jmlhBibitan."','".$ket."','".$_SESSION['standard']['userid']."')";
                if (!mysql_query($sInsert2)) {
                    echo 'DB Error : '.$sInsert2."\n".mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
            }

            break;
        }
        break;
        exit(' Error:'.$_SESSION['lang']['post'].'');
    case 'loadDataStock':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $tglSkrng = date('Y-m-d');
        $thnSkrng = date('Y');
        $sql2 = 'select * from '.$dbname.".bibitan_mutasi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' \r\n                       group by batch,kodeorg order by tanggal desc";
        $query2 = mysql_query($sql2) ;//|| exit('Error '.mysql_error($conn));
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = "select  batch,kodeorg,sum(jumlah) as jumlah from $dbname.bibitan_mutasi ".
                "where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and post=1 ".
                "group by batch,kodeorg order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
            while ($rData = mysql_fetch_assoc($qData)) {
                $data = '';
                $sDatabatch = 'select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from '.$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                $qDataBatch = mysql_query($sDatabatch);//;//;// || exit('Error '.mysql_error($conn));
                $rDataBatch = mysql_fetch_assoc($qDataBatch);
                $thnData = substr($rDataBatch['tanggaltanam'], 0, 4);
                $starttime = strtotime($rDataBatch['tanggaltanam']);
                $endtime = strtotime($tglSkrng);
                $jmlHari = ($endtime - $starttime) / (60 * 60 * 24 * 30);
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$rData['batch'].'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.number_format($rData['jumlah'], 0).'</td>';
                $tab .= '<td>'.$optnmSup[$rDataBatch['supplerid']].'</td>';
                $tab .= '<td align=right>'.number_format($jmlHari, 2).'</td>';
                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'loadData1':
        $tanggal = substr(tanggalsystem($_POST['tglCari2']), 0, 4).'-'.substr(tanggalsystem($_POST['tglCari2']), 4, 2).'-'.substr(tanggalsystem($_POST['tglCari2']), 6, 2);
        if ('' !== $_POST['tglCari2']) {
            $wher .= " and tanggal like '%".$tanggal."%'";
        }

        if ('' !== $_POST['batchCari2']) {
            $wher .= " and batch like '%".$_POST['batchCari2']."%'";
        }

        if ('' !== $_POST['statCari2']) {
            $wher .= " and post='".$_POST['statCari2']."'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = "select * from $dbname.bibitan_mutasi ".
            "where kodetransaksi='TMB' and  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher.
            "order by tanggal desc ";
        $query2 = mysql_query($sql2);//;//;// || exit('Error '.mysql_error($conn));
        $jlhbrs = mysql_num_rows($query2);
        if (0 != $jlhbrs) {
            $sData = 'select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag from '.$dbname.".bibitan_mutasi  \r\n                        where kodetransaksi='TMB' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher."\r\n                        order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
            while ($rData = mysql_fetch_assoc($qData)) {
                $data = '';
                $sDatabatch = 'select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi,jumlahdo,jumlahterima,jumlahafkir,nodo from '.$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                $qDataBatch = mysql_query($sDatabatch);//;//;// || exit('Error '.mysql_error($conn));
                $rDataBatch = mysql_fetch_assoc($qDataBatch);
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$rData['kodetransaksi'].'</td>';
                $tab .= '<td>'.$rData['batch'].'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.$rData['jumlah'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$rDataBatch['jenisbibit'].'</td>';
                $tab .= '<td>'.$optnmSup[$rDataBatch['supplerid']].'</td>';
                $tab .= '<td>'.tanggalnormal($rDataBatch['tanggalproduksi']).'</td>';
                if (1 === $rData['post'] && 'manual' === $rData['flag']) {
                    $data = 1;
                } else {
                    if ('AUTO' === $rData['flag'] && 1 === $rData['post']) {
                        $data = 1;
                    } else {
                        if (0 === $rData['post'] && 'manual' === $rData['flag']) {
                            $data = 0;
                        } else {
                            $data = 3;
                        }
                    }
                }

                if (0 === $data) {
                    $tab .= "<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filFieldHead('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['jumlah']."','".tanggalnormal($rDataBatch['tanggaltanam'])."','".$rDataBatch['jenisbibit']."','".$rDataBatch['supplerid']."','".tanggalnormal($rDataBatch['tanggalproduksi'])."','".$rDataBatch['nodo']."'\r\n                              ,'".$rDataBatch['jumlahdo']."','".$rDataBatch['jumlahterima']."','".$rDataBatch['jumlahafkir']."')\" src='images/application/application_edit.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delFieldHead('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".$rDataBatch['jenisbibit']."')\" src='images/application/application_delete.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."')\" src='images/skyblue/posting.png'/></td>";
                } else {
                    if (3 === $data) {
                        $tab .= '<td>References</td>';
                    } else {
                        $tab .= '<td>'.$_SESSION['lang']['posting'].'</td>';
                    }
                }

                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'loadData2':
        if ('' !== $_POST['statCari']) {
            $wher .= " and post='".$_POST['statCari']."'";
        }

        if ('' !== $_POST['batchCari']) {
            $wher .= " and batch like '%".$_POST['batchCari']."%'";
        }

        $tanggal = substr(tanggalsystem($_POST['tglCari']), 0, 4).'-'.substr(tanggalsystem($_POST['tglCari']), 4, 2).'-'.substr(tanggalsystem($_POST['tglCari']), 6, 2);
        if ('' !== $_POST['tglCari']) {
            $wher .= " and tanggal like '%".$tanggal."%'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select * from '.$dbname.".bibitan_mutasi where kodetransaksi='TPB' and  \r\n                       kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher.' order by tanggal desc ';
        $query2 = mysql_query($sql2) ;//|| exit('Error '.mysql_error($conn));
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = 'select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag,tujuan,keterangan from '.$dbname.".bibitan_mutasi  where \r\n                        kodetransaksi='TPB' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'  ".$wher." \r\n                        order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
            while ($rData = mysql_fetch_assoc($qData)) {
                $data = '';
                $sDatabatch = 'select distinct jumlah from '.$dbname.".bibitan_mutasi where batch='".$rData['batch']."' and kodeorg='".$rData['tujuan']."' ";
                $qDataBatch = mysql_query($sDatabatch);//('Error '.mysql_error($conn));
                $rDataBatch = mysql_fetch_assoc($qDataBatch);
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$rData['kodetransaksi'].'</td>';
                $tab .= '<td>'.$rData['batch'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td>'.$optNm[$rData['tujuan']].'</td>';
                $tab .= '<td align=right>'.$rData['jumlah'].'</td>';
                $tab .= '<td>'.$rData['keterangan'].'</td>';
                if (1 === $rData['post'] && 'manual' === $rData['flag']) {
                    $data = 1;
                } else {
                    if ('AUTO' === $rData['flag'] && 0 === $rData['post']) {
                        $data = 1;
                    } else {
                        if (0 === $rData['post'] && 'manual' === $rData['flag']) {
                            $data = 0;
                        }
                    }
                }

                if (0 === $data) {
                    $tab .= "<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField2('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".tanggalnormal($rData['tanggal'])."','".substr($rData['jumlah'], 1)."')\" src='images/application/application_edit.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField2('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."')\" src='images/application/application_delete.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData2('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/></td>";
                } else {
                    $tab .= '<td>'.$_SESSION['lang']['posting'].'</td>';
                }

                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast2(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast2(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'loadData3':
        if ('' !== $_POST['statCari']) {
            $wher .= " and post='".$_POST['statCari']."'";
        }

        if ('' !== $_POST['batchCari']) {
            $wher .= " and batch like '%".$_POST['batchCari']."%'";
        }

        $tanggal = substr(tanggalsystem($_POST['tglCari']), 0, 4).'-'.substr(tanggalsystem($_POST['tglCari']), 4, 2).'-'.substr(tanggalsystem($_POST['tglCari']), 6, 2);
        if ('' !== $_POST['tglCari']) {
            $wher .= " and tanggal like '%".$tanggal."%'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = "select * from $dbname.bibitan_mutasi ".
            "where kodetransaksi='AFB' and  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".
            $wher.'  order by tanggal desc ';
        $query2 = mysql_query($sql2) ;//|| exit('Error '.mysql_error($conn));
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = "select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag,tujuan,keterangan ".
                "from $dbname.bibitan_mutasi  where kodetransaksi='AFB' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   ".
                $wher." order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData) ;//|| exit('Error '.mysql_error($conn));
            while ($rData = mysql_fetch_assoc($qData)) {
                $data = '';
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$rData['kodetransaksi'].'</td>';
                $tab .= '<td>'.$rData['batch'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.$rData['jumlah'].'</td>';
                $tab .= '<td>'.$rData['keterangan'].'</td>';
                if (1 === $rData['post'] && 'manual' === $rData['flag']) {
                    $data = 1;
                } else {
                    if ('AUTO' === $rData['flag'] && 0 === $rData['post']) {
                        $data = 1;
                    } else {
                        if (0 === $rData['post'] && 'manual' === $rData['flag']) {
                            $data = 0;
                        }
                    }
                }

                if (0 === $data) {
                    $tab .= "<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField3('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".substr($rData['jumlah'], 1)."')\" src='images/application/application_edit.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField3('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."')\" src='images/application/application_delete.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData3('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/></td>";
                } else {
                    $tab .= '<td>'.$_SESSION['lang']['posting'].'</td>';
                }

                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast2(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast2(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'loadData5':
        $tanggal = substr(tanggalsystem($_POST['tglCari']), 0, 4).'-'.substr(tanggalsystem($_POST['tglCari']), 4, 2).'-'.substr(tanggalsystem($_POST['tglCari']), 6, 2);
        if ('' !== $_POST['tglCari']) {
            $wher .= " and tanggal like '%".$tanggal."%'";
        }

        if ('' !== $_POST['batchCari']) {
            $wher .= " and batch like '%".$_POST['batchCari']."%'";
        }

        if ('' !== $_POST['statCari']) {
            $wher .= " and post='".$_POST['statCari']."'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select * from '.$dbname.".bibitan_mutasi where kodetransaksi='DBT' and  \r\n                       kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher.' order by tanggal desc ';
        $query2 = mysql_query($sql2) ;//|| exit('Error '.mysql_error($conn));
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = 'select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag,keterangan from '.$dbname.".bibitan_mutasi  where \r\n                        kodetransaksi='DBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'  ".$wher." \r\n                        order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
            while ($rData = mysql_fetch_assoc($qData)) {
                $data = '';
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$rData['kodetransaksi'].'</td>';
                $tab .= '<td>'.$rData['batch'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.$rData['jumlah'].'</td>';
                $tab .= '<td>'.$rData['keterangan'].'</td>';
                if (1 === $rData['post'] && 'manual' === $rData['flag']) {
                    $data = 1;
                } else {
                    if ('AUTO' === $rData['flag'] && 0 === $rData['post']) {
                        $data = 1;
                    } else {
                        if (0 === $rData['post'] && 'manual' === $rData['flag']) {
                            $data = 0;
                        }
                    }
                }

                if (0 === $data) {
                    $tab .= "<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField5('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".$rData['jumlah']."')\" src='images/application/application_edit.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField5('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."')\" src='images/application/application_delete.png'/>";
                    $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData5('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/></td>";
                } else {
                    $tab .= '<td>'.$_SESSION['lang']['posting'].'</td>';
                }

                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast2(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast2(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'loadData7':
        $tanggal = substr(tanggalsystem($_POST['tglCari']), 0, 4).'-'.substr(tanggalsystem($_POST['tglCari']), 4, 2).'-'.substr(tanggalsystem($_POST['tglCari']), 6, 2);
        if ('' !== $_POST['statCari']) {
            $wher .= " and post='".$_POST['statCari']."'";
        }

        if ('' !== $_POST['batchCari']) {
            $wher .= " and batch like '%".$_POST['batchCari']."%'";
        }

        if ('' !== $_POST['tglCari']) {
            $wher .= " and tanggal like '%".$tanggal."%'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select * from '.$dbname.".bibitan_mutasi where kodetransaksi='PNB' and  \r\n                       kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   ".$wher.'  order by tanggal desc ';
        $query2 = mysql_query($sql2);//;//;// || exit('Error '.mysql_error($conn));
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = 'select distinct * from '.$dbname.".bibitan_mutasi  where kodetransaksi='PNB' and \r\n                        kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   ".$wher."  \r\n                        order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData) ;//|| exit('Error '.mysql_error($conn));
            while ($rData = mysql_fetch_assoc($qData)) {
                $data = '';
                ++$no;
                if ('4' === strlen($rData['pelanggan'])) {
                    $pelanggan = $optNm[$rData['pelanggan']];
                } else {
                    $pelanggan = $optnmSup[$rData['pelanggan']];
                }

                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$rData['kodetransaksi'].'</td>';
                $tab .= '<td>'.$rData['batch'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.$rData['jumlah'].'</td>';
                $tab .= '<td>'.$rData['jenistanam'].'</td>';
                $tab .= '<td>'.$rData['keterangan'].'</td>';
                $tab .= '<td>'.$rData['kodevhc'].'</td>';
                $tab .= '<td>'.$pelanggan.'</td>';
                $tab .= '<td>'.$optNm[$rData['afdeling']].'</td>';
                $tab .= '<td>'.$optNmkaryawan[$rData['penanggungjawab']].'</td>';
                if (1 === $rData['post'] && 'manual' === $rData['flag']) {
                    $data = 1;
                } else {
                    if ('AUTO' === $rData['flag'] && 0 === $rData['post']) {
                        $data = 1;
                    } else {
                        if (0 === $rData['post'] && 'manual' === $rData['flag']) {
                            $data = 0;
                        }
                    }
                }

                if (0 === $data) {
                    $tab .= '<td  align=center colspan=2>';
                    $tab .= "&nbsp;<img  style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField7('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['rit']."','".trim($rData['kodevhc'])."')\" src='images/application/application_delete.png'/>";
                    $tab .= "&nbsp;<img  style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData7('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['rit']."','".trim($rData['kodevhc'])."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/>";
                    $tab .= "&nbsp;<img  style='cursor:pointer;' title='PDF ".$rData['batch']."' class=resicon  src='images/pdf.jpg' onclick=\"masterPDF('bibitan_mutasi','".$rData['tanggal'].','.$rData['kodetransaksi'].','.$rData['batch'].','.$rData['kodeorg'].','.$rData['rit'].','.trim($rData['kodevhc'])."','','kebun_slavepengirimanBibitPdf',event)\" /></td>";
                } else {
                    $tab .= "<td><img  style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=resicon  src='images/pdf.jpg' onclick=\"masterPDF('bibitan_mutasi','".$rData['tanggal'].','.$rData['kodetransaksi'].','.$rData['batch'].','.$rData['kodeorg'].','.$rData['rit'].','.$rData['kodevhc']."','','kebun_slavepengirimanBibitPdf',event)\" /></td>";
                }

                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=13 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast7(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast7(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=13>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'getKet':
        $sData = 'select distinct keterangan from '.$dbname.".bibitan_mutasi  where kodeorg='".$kdOrg."' and batch='".$batchVar."' and kodetransaksi='".$kodeTrans."' ";
        $qData = mysql_query($sData) ;//|| exit('Error '.mysql_error($conn));
        $rData = mysql_fetch_assoc($qData);
        echo $rData['keterangan'];

        break;
    case 'update1':
        if ('' === $kdOrg || '' === $jmlhBibitan || '' === $jnsBibitan || '' === $supplierid || '' === $tglProduksi || '' === $tglTnm) {
            exit(' Error: '.$_SESSION['lang']['isifield'].'');
        }

        $scek = 'select distinct post from '.$dbname.".bibitan_mutasi where batch='".$batchVar."' and  kodeorg='".$kdOrg."' and kodetransaksi='TMB' ";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_fetch_assoc($qcek);
        if ('0' === $rcek['post']) {
            $supdate = 'update '.$dbname.".bibitan_batch  set jenisbibit='".$jnsBibitan."',supplerid='".$supplierid."', tanggalproduksi='".$tglProduksi."',jumlahdo='".$jmlhdDo."',jumlahterima='".$jmlhTrima."',jumlahafkir='".$afkirKcmbh."',nodo='".$nodo."'\r\n                         where batch='".$batchVar."' and jenisbibit='".$oldJenisBibit."'";
            if (mysql_query($supdate)) {
                $supdate2 = 'update '.$dbname.".bibitan_mutasi set jumlah='".$jmlhBibitan."',keterangan='".$ket."',updateby='".$_SESSION['standard']['userid']."' where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='TMB' and tanggal='".$tglTnm."'";
                if (!mysql_query($supdate2)) {
                    echo 'DB Error : '.$supdate2."\n".mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.$supdate."\n".mysql_error($conn);
            }

            break;
        }

        exit(' Error: '.$_SESSION['lang']['post'].'');
    case 'delData':
        $scek = 'select distinct post from '.$dbname.".bibitan_mutasi where batch='".$tglTnm."' and kodeorg='".$kdOrg."' and tanggal='".$_POST['tanggal']."'";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_fetch_assoc($qcek);
        if ('0' === $rcek['post']) {
            $sDel = 'delete from '.$dbname.".bibitan_mutasi where kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and kodeorg='".$kdOrg."' and tanggal='".$tglTnm."' and tanggal='".$_POST['tanggal']."'";
            if (mysql_query($sDel)) {
                $sDel2 = 'delete from '.$dbname.".bibitan_batch where batch='".$batchVar."' and jenisbibit='".$oldJenisBibit."' and tanggal='".$_POST['tanggal']."'";
                if (!mysql_query($sDel2)) {
                }
            } else {
                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            }
        }

        break;
    case 'delData2':
        $scek = 'select distinct post from '.$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='TPB' and tujuan='".$kdOrgTjn."' and tanggal='".$_POST['tanggal']."'";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_fetch_assoc($qcek);
        if ('0' === $rcek['post']) {
            $sDelet = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrgTjn."' and kodetransaksi='TMB' and batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
            if (mysql_query($sDelet)) {
                $sDelete2 = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='TPB' and tujuan='".$kdOrgTjn."' and batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
                if (mysql_query($sDelete2)) {
                    echo '';
                } else {
                    echo 'DB Error : '.$sDelete2."\n".mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.$sDelet."\n".mysql_error($conn);
            }

            break;
        }

        exit(' Error:'.$_SESSION['lang']['post'].'');
    case 'delData3':
        $sDelete2 = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' \r\n            and kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
        if (!mysql_query($sDelete2)) {
            echo 'DB Error : '.$sDelete2."\n".mysql_error($conn);
        }

        break;
    case 'delData5':
        $sDelete2 = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='DBT' and  batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
        if (!mysql_query($sDelete2)) {
            echo 'DB Error : '.$sDelete2."\n".mysql_error($conn);
        }

        break;
    case 'delData7':
        $sDeleteX = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and rit='".$_POST['rit']."' and kodevhc='".$_POST['kodevhc']."' and batch='".$batchVar."'  and tanggal='".$_POST['tanggal']."'";
        if (!mysql_query($sDeleteX)) {
            echo 'DB Error : '.$sDeleteX."\n".mysql_error($conn);
        }

        break;
    case 'postData':
        $scek = 'select distinct post from '.$dbname.".bibitan_mutasi where batch='".$tglTnm."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."'";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_fetch_assoc($qcek);
        if ('0' === $rcek['post']) {
            $sDel = 'update '.$dbname.".bibitan_mutasi set post=1 where kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and kodeorg='".$kdOrg."' and tanggal='".$tglTnm."' and post='0'";
            if (!mysql_query($sDel)) {
                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            }

            break;
        }

        exit(' Error:'.$_SESSION['lang']['nodata'].'');
    case 'postData2':
        $scek2 = "select sum(jumlah) as totalBibitan from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2) ;//|| exit('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan * -1) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total']." \r\n                   ".$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $scek = 'select post from '.$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tujuan='".$kdOrgTjn."' and tanggal='".$_POST['tanggal']."'";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_fetch_assoc($qcek);
        if ('0' === $rcek['post']) {
            $sDel = 'update '.$dbname.".bibitan_mutasi set post=1 where kodetransaksi='TMB' and batch='".$batchVar."' and kodeorg='".$kdOrgTjn."' and post='0' and tanggal='".$_POST['tanggal']."' and flag='AUTO';";
            if (!mysql_query($sDel)) {
                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            } else {
                $su = 'update '.$dbname.".bibitan_mutasi set post=1 where kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and kodeorg='".$kdOrg."' and tujuan='".$kdOrgTjn."' and post='0' and tanggal='".$_POST['tanggal']."';";
                mysql_query($su);
            }

            break;
        }

        exit(' Error:'.$_SESSION['lang']['post'].'');
    case 'postData3':
        $scek2 = "select sum(jumlah) as totalBibitan from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2);//('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan * -1) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total'].' '.$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $scek = "select post from $dbname.bibitan_mutasi ".
            "where batch='".$batchVar."' and kodeorg='".$kdOrg."' and ".
            "kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."' and post=1";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_num_rows($qcek);
        if ('0' === $rcek) {
            $sDel = 'update '.$dbname.".bibitan_mutasi set post=1 where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."'";
            if (!mysql_query($sDel)) {
                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            }

            break;
        }

        exit(' Error: '.$_SESSION['lang']['post'].'');
    case 'postData5':
        $scek = "select post from $dbname.bibitan_mutasi ".
            "where batch='".$batchVar."' and kodeorg='".$kdOrg."' and ".
            "kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."' and post=1";
        $qcek = mysql_query($scek) ;//|| exit('Error '.mysql_error($conn));
        $rcek = mysql_num_rows($qcek);
        if ('0' === $rcek) {
            $sDel = 'update '.$dbname.".bibitan_mutasi set post=1 where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."'";
            if (!mysql_query($sDel)) {
                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            }

            break;
        }

        exit(' Error:'.$_SESSION['lang']['post'].'');
    case 'postData7':
        $scek2 = "select sum(jumlah) as totalBibitan from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2);//;//;// || exit('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan * -1) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total']." \r\n                   ".$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $scek = 'select distinct post from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and rit='".$jmlRit."' and kodevhc like '".$kdvhc."%' and batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
        $qcek = mysql_query($scek);//;//;// || exit('Error '.mysql_error($conn));
        $rcek = mysql_fetch_assoc($qcek);
        if ('0' === $rcek['post']) {
            $sDel = 'update '.$dbname.".bibitan_mutasi set post=1 where kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and rit='".$jmlRit."' and kodevhc like '".$kdvhc."%' and batch='".$batchVar."' and post='0' and tanggal='".$_POST['tanggal']."'";
            if (!mysql_query($sDel)) {
                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            }

            break;
        }

        exit(' Error:'.$_SESSION['lang']['post'].'');
    case 'getKodeorg':
        $optKdorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if ('' !== $batchVar) {
            $sData = 'select distinct kodeorg from '.$dbname.".bibitan_mutasi where batch='".$batchVar."'";
            $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
            while ($rOrg2 = mysql_fetch_assoc($qData)) {
                $optKdorg .= '<option value='.$rOrg2['kodeorg'].' >'.$optNm[$rOrg2['kodeorg']].'</option>';
            }
            echo $optKdorg;
        }

        break;
    case 'getKodeorgN':
        $optKdorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if ('' !== $batchVar) {
            $sData = 'select distinct kodeorg from '.$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg not like '%PN%'";
            $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
            while ($rOrg2 = mysql_fetch_assoc($qData)) {
                $optKdorg .= '<option value='.$rOrg2['kodeorg'].' >'.$optNm[$rOrg2['kodeorg']].'</option>';
            }
            echo $optKdorg;
        }

        break;
    case 'cekSmGak':
        $sData = 'select distinct kodeorg from '.$dbname.".bibitan_mutasi where batch='".$batchVar."'";
        $qData = mysql_query($sData);//;//;// || exit('Error '.mysql_error($conn));
        $rData = mysql_fetch_assoc($qData);
        if ($rData['kodeorg'] === $kdOrg) {
            echo '1';
        }

        break;
    case 'saveTab2':
        if ('' === $kdOrgTjn || '' === $batchVar || '' === $jmlhBibitan || '' === $tglTnm) {
            exit(' Error: '.$_SESSION['lang']['isifield'].'');
        }

        $str = "select * from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrgTjn."' and kodetransaksi='TMB' and ".
            "batch='".$batchVar."' and tanggal='".$tglTnm."' and post=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            exit(' Error: '.$_SESSION['lang']['exist'].'');
        }

        $scek2 = "select sum(jumlah) as totalBibitan from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2);//;//;// || exit('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total'].' '.$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $sDelet = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrgTjn."' and kodetransaksi='TMB' and batch='".$batchVar."' and tanggal='".$tglTnm."' and flag='AUTO'";
        if (mysql_query($sDelet)) {
            $sDelete2 = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='TPB' and tanggal='".$tglTnm."' and tujuan='".$kdOrgTjn."' and batch='".$batchVar."'";
            if (mysql_query($sDelete2)) {
                $jmlh = $jmlhBibitan * -1;
                $sInsert = 'insert into '.$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby,tujuan) \r\n                            values('".$batchVar."','".$kdOrg."','".$tglTnm."','TPB','".$jmlh."','".$ket."','".$_SESSION['standard']['userid']."','".$kdOrgTjn."')";
                if (mysql_query($sInsert)) {
                    $sInsert2 = 'insert into '.$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby,flag) \r\n                                values('".$batchVar."','".$kdOrgTjn."','".$tglTnm."','TMB','".$jmlhBibitan."','".$ket."','".$_SESSION['standard']['userid']."','AUTO')";
                    if (!mysql_query($sInsert2)) {
                        echo 'DB Error : '.$sInsert2."\n".mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                }
            }
        }

        break;
    case 'saveTab3':
        if ('' === $kdOrg || '' === $batchVar || '' === $jmlhBibitan || '' === $tglTnm) {
            exit(' Error: '.$_SESSION['lang']['isifield'].'');
        }

        $str = " select * from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and kodetransaksi='AFB' and batch='".$batchVar."' and tanggal='".$tglTnm."' ".
        "and post=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            exit(' Error: '.$_SESSION['lang']['exist'].'');
        }

        $scek2 = 'select sum(jumlah) as totalBibitan from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2);//;//;// || exit('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total'].' '.$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $sDelete2 = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='AFB' and batch='".$batchVar."' and tanggal='".$tglTnm."'";
        if (mysql_query($sDelete2)) {
            $jmlh = $jmlhBibitan * -1;
            $sInsert2 = 'insert into '.$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby) \r\n                        values('".$batchVar."','".$kdOrg."','".$tglTnm."','AFB','".$jmlh."','".$ket."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sInsert2)) {
                echo 'DB Error : '.$sInsert2."\n".mysql_error($conn);
            }
        } else {
            echo 'DB Error : '.$sDelete2."\n".mysql_error($conn);
        }

        break;
    case 'saveTab5':
        if ('' === $kdOrg || '' === $batchVar || '' === $jmlhBibitan || '' === $tglTnm) {
            exit(' Error: '.$_SESSION['lang']['isifield'].'');
        }

        $str = "select * from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and kodetransaksi='DBT'  and ".
            "batch='".$batchVar."' and tanggal='".$tglTnm."' and post=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            exit(' Error: '.$_SESSION['lang']['exist'].'');
        }

        $scek2 = "select  sum(jumlah) as totalBibitan from $dbname.bibitan_mutasi ".
            "where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2);//;//;//;// || exit('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total'].' '.$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $sDelete2 = 'delete from '.$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='DBT'  and batch='".$batchVar."' and tanggal='".$tglTnm."'";
        if (mysql_query($sDelete2)) {
            $sInsert2 = 'insert into '.$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby) \r\n                        values('".$batchVar."','".$kdOrg."','".$tglTnm."','DBT','".$jmlhBibitan."','".$ket."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sInsert2)) {
                echo 'DB Error : '.$sInsert2."\n".mysql_error($conn);
            }
        }

        break;
    case 'saveTab7':
        if ('' === $kdOrg || '' === $batchVar || '' === $jmlhBibitan || '' === $tglTnm || '' === $intexDt || '' === $kdvhc || '' === $nmSupir || '' === $assistenPnb || '' === $custId || '' === $jmlRit) {
            exit(' Error: '.$_SESSION['lang']['isifield'].'');
        }

        $scek2 = "select sum(jumlah) as totalBibitan from $dbname.bibitan_mutasi ".
        "where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
        $qcek2 = mysql_query($scek2);//;//;//;// || exit('Error '.mysql_error($conn));
        $rcek2 = mysql_fetch_assoc($qcek2);
        if ($rcek2['totalBibitan'] < $jmlhBibitan) {
            exit(' Error:'.$_SESSION['lang']['jumlah'].' '.$jmlhBibitan.' '.$_SESSION['lang']['greater'].' '.$_SESSION['lang']['total'].' '.$_SESSION['lang']['stock'].' '.$rcek2['totalBibitan'].' '.$_SESSION['lang']['on'].' '.$_SESSION['lang']['batch'].' '.$batchVar.' '.$_SESSION['lang']['lokasi'].' '.$kdOrg);
        }

        $jmlh = $jmlhBibitan * -1;
        $sInsert2 = 'insert into '.$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby, kodevhc, sopir, intex, pelanggan, lokasipengiriman, penanggungjawab,jenistanam,afdeling,rit) \r\n                values('".$batchVar."','".$kdOrg."','".$tglTnm."','PNB','".$jmlh."','".$ket."' ,'".$_SESSION['standard']['userid']."','".$kdvhc."','".$nmSupir."','".$intexDt."','".$custId."','".$detPeng."','".$assistenPnb."','".$KegiatanId."','".$kodeAfd."','".$jmlRit."')";
        if (!mysql_query($sInsert2)) {
            echo 'DB Error : '.$sInsert2."\n".mysql_error($conn);
        }

        break;
    case 'getCust':
        $optKode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if ('' !== $intexDt) {
            if ('1' === $intexDt) {
                $sOpt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."' and tipe='KEBUN'";
            } else {
                if ('2' === $intexDt) {
                    $sOpt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk<>'".$_SESSION['org']['kodeorganisasi']."'  and tipe='KEBUN'";
                } else {
                    if ('0' === $intexDt) {
                        $sOpt = 'select kodecustomer as kodeorganisasi,namacustomer as namaorganisasi from '.$dbname.'.pmn_4customer  order by namacustomer asc';
                    }
                }
            }

            $qOpt = mysql_query($sOpt);//;//;// || exit('Error '.mysql_error($conn));
            while ($rOpt = mysql_fetch_assoc($qOpt)) {
                if ('' !== $kdOrg) {
                    $optKode .= "<option value='".$rOpt['kodeorganisasi']."' ".(($rOpt['kodeorganisasi'] === $kdOrg ? 'selected' : '')).'>'.$rOpt['namaorganisasi'].'</option>';
                } else {
                    $optKode .= "<option value='".$rOpt['kodeorganisasi']."'>".$rOpt['namaorganisasi'].'</option>';
                }
            }
        }

        echo $optKode;

        break;
    case 'getAfd':
        $optKode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sOpt = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$kdOrg."'  order by namaorganisasi asc";
        $qOpt = mysql_query($sOpt);//;//;// || exit('Error '.mysql_error($conn));
        while ($rOpt = mysql_fetch_assoc($qOpt)) {
            if ('' !== $kdOrg) {
                $optKode .= "<option value='".$rOpt['kodeorganisasi']."' ".(($rOpt['kodeorganisasi'] === $kodeAfd ? 'selected' : '')).'>'.$rOpt['namaorganisasi'].'</option>';
            } else {
                $optKode .= "<option value='".$rOpt['kodeorganisasi']."'>".$rOpt['namaorganisasi'].'</option>';
            }
        }
        echo $optKode;

        break;
    case 'getBlok':
        $optKode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sOpt = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk like '".$kdOrg."%' and tipe = 'blok' order by namaorganisasi asc";
        $qOpt = mysql_query($sOpt) ;//|| exit('Error '.mysql_error($conn));
        while ($rOpt = mysql_fetch_assoc($qOpt)) {
            if ('' !== $kdOrg) {
                $optKode .= "<option value='".$rOpt['kodeorganisasi']."' ".(($rOpt['kodeorganisasi'] === $kodeAfd ? 'selected' : '')).'>'.$rOpt['namaorganisasi'].'</option>';
            } else {
                $optKode .= "<option value='".$rOpt['kodeorganisasi']."'>".$rOpt['namaorganisasi'].'</option>';
            }
        }
        echo $optKode;

        break;
    case 'getBatch':
        $optBatch = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sBatch = 'select distinct batch from '.$dbname.".bibitan_mutasi where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
        $qBatch = mysql_query($sBatch) ;//|| exit('Error '.mysql_error($conn));
        while ($rBatch = mysql_fetch_assoc($qBatch)) {
            $optBatch .= "<option value='".$rBatch['batch']."'>".$rBatch['batch'].'</option>';
        }
        echo $optBatch;

        break;
    default:
        break;
}

?>