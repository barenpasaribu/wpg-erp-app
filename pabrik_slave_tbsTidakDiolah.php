<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$proses = $_POST['proses'];
$kdORg = $_POST['kdOrg'];
$jmLh = $_POST['jmLh'];
$daTtgl = tanggalsystem($_POST['daTtgl']);
$lokasi = $_SESSION['empl']['lokasitugas'];
switch ($proses) {
    case 'LoadData':
        echo "<table cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=rowheader>\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['namaorganisasi']."</td> \r\n\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td>".$_SESSION['lang']['jumlah']."</td>\t \r\n\t\t<td>Action</td>\r\n\t\t</tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_sisatbsolah where `kodeorg` = '".$lokasi."'  order by `tanggal` desc";
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $str = 'select * from '.$dbname.".pabrik_sisatbsolah where `kodeorg` = '".$lokasi."'  order by `tanggal` desc  limit ".$offset.','.$limit.'';
        if ($res = mysql_query($str)) {
            while ($bar = mysql_fetch_object($res)) {
                $spr = 'select namaorganisasi from  '.$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
                $rep = mysql_query($spr);
                $bas = mysql_fetch_object($rep);
                ++$no;
                echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t<td>".$no."</td>\r\n\t\t<td>".$bar->kodeorg."</td>\r\n\t\t<td>".$bas->namaorganisasi."</td>\r\n\t\t<td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t<td align=right>".number_format($bar->jumlah, 2)."</td>\r\n\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"></td>\r\n\t\t</tr>";
            }
            echo "\r\n\t\t<tr><td colspan=7 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            echo ' Gagal,'.mysql_error($conn);
        }

        echo '</tbody></table>';

        break;
    case 'insert':
        if ('' === $kdORg || '' === $jmLh || '' === $daTtgl) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $tglCek = explode('-', $_POST['daTtgl']);
        $thnSkrng = date('Y');
        $blnSkrng = date('m');
        if ($tglCek[2] !== $thnSkrng) {
            echo 'warning: Please use this year, '.$thnSkrng.'';
            exit();
        }

        $sCek = 'select kodeorg,tanggal from '.$dbname.".pabrik_sisatbsolah where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".pabrik_sisatbsolah (kodeorg, tanggal, jumlah) values ('".$kdORg."','".$daTtgl."','".$jmLh."')";
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
        $sql = 'select catatan,pagi,sore from '.$dbname.".kebun_curahhujan where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        $query = mysql_query($sql);
        $res = mysql_fetch_assoc($query);
        echo $res['catatan'].'###'.$res['pagi'].'###'.$res['sore'];

        break;
    case 'update':
        if ('' === $kdORg || '' === $daTtgl || '' === $jmLh) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sUpd = 'update '.$dbname.".pabrik_sisatbsolah set  jumlah='".$jmLh."' where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        if (mysql_query($sUpd)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".pabrik_sisatbsolah where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cariData':
        echo "<div style='overflow:auto; width:450px; height:450px;'><table cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=rowheader>\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['namaorganisasi']."</td> \r\n\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td>".$_SESSION['lang']['jumlah']."</td>\t \r\n\t\t<td>Action</td>\r\n\t\t</tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        if ('' !== $kdORg && '' !== $daTtgl) {
            $where = " kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
        } else {
            if ('' !== $kdORg) {
                $where = " kodeorg='".$kdORg."'";
            } else {
                if ('' !== $daTtgl) {
                    $where = " tanggal='".$daTtgl."' and kodeorg = '".$lokasi."'";
                } else {
                    if ('' === $kdORg && '' === $daTtgl) {
                        echo 'warning:Please Insert Data';
                        exit();
                    }
                }
            }
        }

        $sCek = 'select * from '.$dbname.'.pabrik_sisatbsolah where '.$where.'';
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            $str = 'select * from '.$dbname.'.pabrik_sisatbsolah where '.$where.' order by tanggal desc';
            if ($res = mysql_query($str)) {
                while ($bar = mysql_fetch_object($res)) {
                    $spr = 'select * from  '.$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
                    $rep = mysql_query($spr);
                    $bas = mysql_fetch_object($rep);
                    ++$no;
                    echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t<td>".$bar->kodeorg."</td>\r\n\t\t\t\t\t<td>".$bas->namaorganisasi."</td>\r\n\t\t\t\t\t<td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t\t\t\t<td align=right>".number_format($bar->jumlah, 2)."</td>\r\n\t\t\t\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"></td>\r\n\t\t\t\t\t</tr>";
                }
            } else {
                echo ' Gagal,'.mysql_error($conn);
            }
        } else {
            echo '<tr class=rowcontent><td colspan=6 align=center>Not Found</td></tr>';
        }

        echo '</tbody></table></div>';

        break;
    default:
        break;
}

?>