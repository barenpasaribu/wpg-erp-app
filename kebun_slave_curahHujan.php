<?php
session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$proses = $_POST['proses'];
$kdORg = $_POST['kdOrg'];
$daTpagi = $_POST['daTpagi'];
$daTsore = $_POST['daTsore'];
$note = $_POST['note'];
$jmlbayar = $_POST['jmlbayar'];
$daTtgl = tanggalsystem($_POST['daTtgl']);
$lokasi = $_SESSION['empl']['lokasitugas'];
$jam1 = $_POST['jm1'].':'.$_POST['mn1'].':00';
$jam2 = $_POST['jm2'].':'.$_POST['mn2'].':00';
$mulaipagi = $_POST['jmp'].':'.$_POST['mmp'].':00';
$selesaipagi = $_POST['jsp'].':'.$_POST['msp'].':00';
$mulaisore = $_POST['jms'].':'.$_POST['mms'].':00';
$selesaisore = $_POST['jss'].':'.$_POST['mss'].':00';
$periodeAkutansi = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
switch ($proses) {
    case 'LoadData':
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_curahhujan where `kodeorg` like  '".$lokasi."%' order by `tanggal` desc";
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $str = 'select * from '.$dbname.".kebun_curahhujan where `kodeorg` like '".$lokasi."%' order by tanggal desc limit ".$offset.','.$limit.'';
        if (mysql_query($str)) {
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $spr = 'select namaorganisasi from  '.$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
                $rep = mysql_query($spr) ;
                $bas = mysql_fetch_object($rep);
                ++$no;
                $sGp = 'select DISTINCT sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$bar->kodeorg."' and `periode`='".substr($bar->tanggal, 0, 7)."'";
                $qGp = mysql_query($sGp) ;
                $rGp = mysql_fetch_assoc($qGp);
                echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>\r\n\t\t\t<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t\t<td id='strt_".$no."'>".$bar->pagi."</td>\r\n\t\t\t<td id='end_".$no."'>".$bar->sore."</td>\r\n\t\t\t<td id='tglex_".$no."'>".$bar->catatan.'</td><td>';
                if (substr($bar->tanggal, 7) == $periodeAkutansi || 0 == $rGp['sudahproses']) {
                    echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\">";
                }

                echo '</td></tr>';
            }
            echo "\r\n\t\t\t<tr><td colspan=7 align=center>\r\n\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t</td>\r\n\t\t\t</tr>";
        } else {
            echo ' Gagal,'.mysql_error($conn);
        }

        break;
    case 'insert':
        if ($kdORg=='' || $daTtgl=='') {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $tglCek = explode('-', $_POST['daTtgl']);
        $thnSkrng = date('Y');
        $blnSkrng = date('m');
        $sCek = 'select kodeorg,tanggal from '.$dbname.".kebun_curahhujan where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".kebun_curahhujan (kodeorg, tanggal, pagi, sore, catatan,mulaipagi,selesaipagi,mulaisore,selesaisore,jmlbayar) \r\n\t\t\tvalues ('".$kdORg."','".$daTtgl."','".$daTpagi."','".$daTsore."','".$note."','".$mulaipagi."','".$selesaipagi."','".$mulaisore."','".$selesaisore."','".$jmlbayar."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }

        echo 'warning:Data Already Entry';
        exit();
    case 'showData':
        $sql = 'select * from '.$dbname.".kebun_curahhujan where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        $query = mysql_query($sql) ;
        $res = mysql_fetch_assoc($query);
        echo $res['catatan'].'###'.$res['pagi'].'###'.$res['sore'].'###'.substr($res['mulaipagi'], 0, 2).'###'.substr($res['mulaipagi'], 3, 2).'###'.substr($res['selesaipagi'], 0, 2).'###'.substr($res['selesaipagi'], 3, 2).'###'.substr($res['mulaisore'], 0, 2).'###'.substr($res['mulaisore'], 3, 2).'###'.substr($res['selesaisore'], 0, 2).'###'.substr($res['selesaisore'], 3, 2);

        break;
    case 'update':
        if ($kdORg=='' || $daTtgl=='') {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sUpd = "UPDATE ".$dbname.". kebun_curahhujan SET pagi='".$daTpagi."', sore='".$daTsore."', catatan='".$note."',mulaipagi='".$mulaipagi."',selesaipagi='".$selesaipagi."',mulaisore='".$mulaisore."',selesaisore='".$selesaisore."' where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";   
		
		if (mysql_query($sUpd)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".kebun_curahhujan where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'CekData':
        if (preg_match('/e$/Di', $lokasi)) {
            echo '';

            break;
        }

        echo 'warning:You Not In Estate';
        exit();
    case 'cariData':
        if (preg_match('/e$/Di', $lokasi)) {
            $limit = 10;
            $page = 0;
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                if ($page < 0) {
                    $page = 0;
                }
            }

            $offset = $page * $limit;
            if ($kdORg!='' && $daTtgl!='') {
                $where = " kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
            } else {
                if ($kdORg!='') {
                    $where = " kodeorg='".$kdORg."'";
                } else {
                    if ($daTtgl!='') {
                        $where = " tanggal='".$daTtgl."' and kodeorg = '".$lokasi."'";
                    } else {
                        if ($kdORg=='' && $daTtgl=='') {
                            echo 'warning:Please Insert Data';
                            exit();
                        }
                    }
                }
            }

            $sCek = 'select * from '.$dbname.'.kebun_curahhujan where '.$where.'';
            $qCek = mysql_query($sCek) ;
            $rCek = mysql_num_rows($qCek);
            if (0 < $rCek) {
                $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_curahhujan where '.$where.' order by `tanggal` desc';
                $query2 = mysql_query($ql2) ;
                while ($jsl = mysql_fetch_object($query2)) {
                    $jlhbrs = $jsl->jmlhrow;
                }
                $str = 'select * from '.$dbname.'.kebun_curahhujan where '.$where.' order by tanggal desc limit '.$offset.','.$limit.'';
                if ($res = mysql_query($str)) {
                    while ($bar = mysql_fetch_object($res)) {
                        $spr = 'select * from  '.$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
                        $rep = mysql_query($spr) ;
                        $bas = mysql_fetch_object($rep);
                        ++$no;
                        echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>\r\n\t\t\t\t<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t\t\t<td id='strt_".$no."'>".$bar->pagi."</td>\r\n\t\t\t\t<td id='end_".$no."'>".$bar->sore."</td>\r\n\t\t\t\t<td id='tglex_".$no."'>".$bar->catatan.'</td>><td>';
                        if (substr($bar->tanggal, 7) === $periodeAkutansi) {
                            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\">";
                        }

                        echo '</td></tr>';
                    }
                    echo "\r\n\t\t\t\t<tr class=rowheader><td colspan=7 align=center>\r\n\t\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t\t</td>\r\n\t\t\t\t</tr>";
                } else {
                    echo ' Gagal,'.mysql_error($conn);
                }
            } else {
                echo '<tr class=rowcontent><td colspan=7 align=center>Not Found</td></tr>';
            }

            break;
        }

        echo 'warning:You Not In Estate';
        exit();
    default:
        break;
}

?>