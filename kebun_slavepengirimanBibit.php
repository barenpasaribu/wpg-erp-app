<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$codeOrg = $_POST['codeOrg'];
$tgl = tanggalsystem($_POST['tgl']);
$orgTujuan = $_POST['orgTujuan'];
$jmlh = $_POST['jmlh'];
$jnsBibit = $_POST['jnsBibit'];
$custId = $_POST['custId'];
$notrans = $_POST['notrans'];
$kdKeg = $_POST['kdKeg'];
switch ($proses) {
    case 'loadData':
        $thnBln = date('Y-m');
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['list'].'</legend>';
        echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_pengirimanbbt','".$thnBln."','','kebun_slavepengirimanBibitPdf',event);\">&nbsp;<img onclick=dataKeExcel(event,'kebun_slave_pengirimanBibitExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>\r\n\t\t\t<table cellspacing=1 border=0 class=sortable>\r\n\t\t<thead>\r\n<tr class=rowheader>\r\n<td>".$_SESSION['lang']['notransaksi']."</td>\r\n<td>".$_SESSION['lang']['kodeorg']."</td>\r\n<td>".$_SESSION['lang']['tanggal']."</td>\r\n<td>".$_SESSION['lang']['jenisbibit']."</td>\r\n<td>".$_SESSION['lang']['jumlah']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_pengirimanbbt order by `tanggal` desc';
        $query2 = mysql_query($sql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.'.kebun_pengirimanbbt order by `tanggal` desc limit '.$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc) ;
        while ($res = mysql_fetch_assoc($qlvhc)) {
            echo "\r\n\t\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$res['notransaksi']."</td>\r\n\t\t\t\t\t<td>".$res['kodeorg']."</td>\r\n\t\t\t\t\t<td>".tanggalnormal($res['tanggal'])."</td>\r\n\t\t\t\t\t<td>".$res['jenisbibit']."</td>\r\n\t\t\t\t\t<td align='right'>".$res['jumlah'].'</td>';
            echo "\r\n\t\t\t\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">\r\n\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['notransaksi']."');\" >\r\n\t\t\t\t\t</td>\r\n\t\t\t\t\t</tr>";
        }
        echo "\r\n\t\t\t\t\t<tr><td colspan=5 align=center>\r\n\t\t\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t\t</tr>";
        echo '</table></fieldset>';
        CLOSE_BOX();

        break;
    case 'generateNo':
        $tgl = date('Ymd');
        $bln = substr($tgl, 4, 2);
        $thn = substr($tgl, 0, 4);
        $notransaksi = $codeOrg.'/'.date('Y').'/'.date('m').'/';
        $ql = 'select `notransaksi` from '.$dbname.".`kebun_pengirimanbbt` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
        $qr = mysql_query($ql) ;
        $rp = mysql_fetch_object($qr);
        $awal = substr($rp->notransaksi, -4, 4);
        $awal = (int) $awal;
        $cekbln = substr($rp->notransaksi, -7, 2);
        $cekthn = substr($rp->notransaksi, -12, 4);
        if ($bln !== $cekbln && $thn !== $cekthn) {
            $awal = 1;
        } else {
            ++$awal;
        }

        $counter = addZero($awal, 4);
        $notransaksi = $codeOrg.'/'.$thn.'/'.$bln.'/'.$counter;
        echo $notransaksi;

        break;
    case 'insert':
        if ('' === $notrans || '' === $tgl || '' === $jnsBibit || '' === $jmlh) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sCek = 'select notransaksi from '.$dbname.".kebun_pengirimanbbt where notransaksi='".$notrans."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".kebun_pengirimanbbt (notransaksi, kodeorg, tanggal, jenisbibit, jumlah, orgtujuan, pembeliluar, kodekegiatan) values \r\n\t\t\t('".$notrans."','".$codeOrg."','".$tgl."','".$jnsBibit."','".$jmlh."','".$orgTujuan."','".$custId."','".$kdKeg."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }

        echo 'warning:This Notransaction already input';
        exit();
    case 'getData':
        $sGet = 'select * from '.$dbname.".kebun_pengirimanbbt where notransaksi='".$notrans."'";
        $qGet = mysql_query($sGet) ;
        $rGet = mysql_fetch_assoc($qGet);
        if ('' === $rGet['orgtujuan']) {
            $rGet['orgtujuan'] = 1;
        }

        if ('' === $rGet['pembeliluar']) {
            $rGet['pembeliluar'] = 1;
        }

        echo $rGet['kodeorg'].'###'.tanggalnormal($rGet['tanggal']).'###'.$rGet['jenisbibit'].'###'.$rGet['jumlah'].'###'.$rGet['orgtujuan'].'###'.$rGet['pembeliluar'].'###'.$rGet['kodekegiatan'];

        break;
    case 'update':
        if ('' === $notrans || '' === $tgl || '' === $jnsBibit || '' === $jmlh) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sUp = 'update '.$dbname.".kebun_pengirimanbbt set tanggal='".$tgl."', jenisbibit='".$jnsBibit."', jumlah='".$jmlh."', orgtujuan='".$orgTujuan."', pembeliluar='".$custId."', kodekegiatan='".$kdKeg."' where notransaksi='".$notrans."'";
        if (mysql_query($sUp)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".kebun_pengirimanbbt where notransaksi='".$notrans."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cari_transaksi':
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['result'].'</legend>';
        echo "<div style=\"width:600px; height:450px; overflow:auto;\">\r\n\t\t\t<table cellspacing=1 border=0 class='sortable'>\r\n\t\t<thead>\r\n<tr class=rowheader>\r\n<td>".$_SESSION['lang']['notransaksi']."</td>\r\n<td>".$_SESSION['lang']['kodeorg']."</td>\r\n<td>".$_SESSION['lang']['tanggal']."</td>\r\n<td>".$_SESSION['lang']['jenisbibit']."</td>\r\n<td>".$_SESSION['lang']['jumlah']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
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

        if ('' !== $txt_search) {
            $where = " notransaksi LIKE  '%".$txt_search."%'";
        } else {
            if ('' !== $txt_tgl) {
                $where .= " tanggal LIKE '".$txt_tgl."'";
            } else {
                if ('' !== $txt_tgl && '' !== $txt_search) {
                    $where .= " notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                }
            }
        }

        $strx = 'select * from '.$dbname.'.kebun_pengirimanbbt where   '.$where.' order by tanggal desc';
        if ($qry = mysql_query($strx)) {
            $numrows = mysql_num_rows($qry);
            if ($numrows < 1) {
                echo '<tr class=rowcontent><td colspan=6>Not Found</td></tr>';
            } else {
                while ($res = mysql_fetch_assoc($qry)) {
                    echo "\r\n\t\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$res['notransaksi']."</td>\r\n\t\t\t\t\t<td>".$res['kodeorg']."</td>\r\n\t\t\t\t\t<td>".tanggalnormal($res['tanggal'])."</td>\r\n\t\t\t\t\t<td>".$res['jenisbibit']."</td>\r\n\t\t\t\t\t<td>".$res['jumlah'].'</td>';
                    echo "\r\n\t\t\t\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">\r\n\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['notransaksi']."');\" >\r\n\t\t\t\t</td>\r\n\t\t\t\t\t</tr>";
                }
                echo '</tbody></table></div></fieldset>';
            }
        } else {
            echo 'Gagal,'.mysql_error($conn);
        }

        CLOSE_BOX();

        break;
    default:
        break;
}

?>