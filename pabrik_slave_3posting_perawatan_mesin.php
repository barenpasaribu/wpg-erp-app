<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$proses = $_POST['proses'];
$noTrans = $_POST['noTrans'];
$thisDate = date('Y-m-d');
$txtTgl = tanggalsystem($_POST['txtTgl']);
$statPost = $_POST['statPost'];
switch ($proses) {
    case 'loadData':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.pabrik_rawatmesinht   order by `notransaksi` desc';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.'.pabrik_rawatmesinht  order by `notransaksi` desc limit '.$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            ++$no;
            echo "\r\n\t\t<tr class=rowcontent>\r\n\t\t<td>".$no."</td>\r\n\t\t<td>".$rlvhc['notransaksi']."</td>\r\n\t\t<td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n\t\t<td>".$rlvhc['shift']."</td>\r\n\t\t<td>".$rlvhc['statasiun']."</td>\r\n\t\t<td>".$rlvhc['mesin']."</td>\r\n\t\t<td>".tanggalnormald($rlvhc['jammulai'])."</td>\r\n\t\t<td>".tanggalnormald($rlvhc['jamselesai']).'</td>';
            if ('0' === $rlvhc['statPost']) {
                if ($rlvhc['updateby'] !== $userOnline) {
                    echo "<td><img src=images/skyblue/posting.png class=resicon  title='Edit' onclick=\"postThis('".$rlvhc['notransaksi']."');\">\r\n\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
                } else {
                    echo "\r\n\t\t\t\t<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
                }
            } else {
                echo "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
            }
        }
        echo "\r\n\t\t\t\t <tr><td colspan=9 align=center>\r\n\t\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t\t</td>\r\n\t\t\t\t</tr>";

        break;
    case 'postThis':
        $sCek = 'select statPost from '.$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_assoc($qCek);
        if ($rCek['statPost'] < 1) {
            $sUpdate = 'update '.$dbname.".pabrik_rawatmesinht set statPost='1',postingby='".$_SESSION['standard']['userid']."',postingdate='".$thisDate."' where notransaksi='".$noTrans."' ";
            if (!mysql_query($sUpdate)) {
                echo 'DB Error : '.mysql_error();
                exit();
            }

            break;
        }

        echo 'warning:No Transaksi ini telah terposting';
        exit();
    case 'cariTransaksi':
        if ('' === $noTrans && '' === $txtTgl && '' === $statPost) {
            $where = "order by 'notransaksi' desc";
        } else {
            if ('' !== $noTrans && '' !== $txtTgl && '' !== $statPost) {
                $where = "where notransaksi='".$noTrans."' and tanggal='".$txtTgl."' and statPost='".$statPost."' ";
            } else {
                if ('' !== $noTrans && '' === $txtTgl && '' === $statPost) {
                    $where = "where notransaksi='".$noTrans."'";
                } else {
                    if ('' === $noTrans && '' !== $txtTgl && '' === $statPost) {
                        $where = "where tanggal='".$txtTgl."'";
                    } else {
                        if ('' === $noTrans && '' === $txtTgl && '' !== $statPost) {
                            $hwere = "where statPost='".$statPost."'";
                        } else {
                            if ('' !== $noTrans && '' === $txtTgl && '' !== $statPost) {
                                $where = "where notransaksi='".$noTrans."' and statPost='".$statPost."' ";
                            } else {
                                if ('' === $noTrans && '' !== $txtTgl && '' !== $statPost) {
                                    $where = "where tanggal='".$txtTgl."' and statPost='".$statPost."' ";
                                } else {
                                    if ('' !== $noTrans && '' !== $txtTgl && '' === $statPost) {
                                        $where = "where notransaksi='".$noTrans."' and tanggal='".$txtTgl."' ";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $sql = 'select * from '.$dbname.'.pabrik_rawatmesinht  '.$where.'';
        $query = mysql_query($sql);
        while ($rlvhc = mysql_fetch_assoc($query)) {
            ++$no;
            echo "\r\n\t\t<tr class=rowcontent>\r\n\t\t<td>".$no."</td>\r\n\t\t<td>".$rlvhc['notransaksi']."</td>\r\n\t\t<td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n\t\t<td>".$rlvhc['shift']."</td>\r\n\t\t<td>".$rlvhc['statasiun']."</td>\r\n\t\t<td>".$rlvhc['mesin']."</td>\r\n\t\t<td>".tanggalnormald($rlvhc['jammulai'])."</td>\r\n\t\t<td>".tanggalnormald($rlvhc['jamselesai']).'</td>';
            if ('0' === $rlvhc['statPost']) {
                if ($rlvhc['updateby'] !== $userOnline) {
                    echo "<td><img src=images/skyblue/posting.png class=resicon  title='Edit' onclick=\"postThis('".$rlvhc['notransaksi']."');\">\r\n\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
                } else {
                    echo "\r\n\t\t\t\t<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\"></td>";
                }
            } else {
                echo "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\"></td>";
            }
        }

        break;
    default:
        break;
}

?>