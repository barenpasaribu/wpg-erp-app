<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$kdOrg = $_POST['kdOrg'];
$tgl = tanggalsystem($_POST['tgl']);
$noSpb = $_POST['noSpb'];
$noTrans = $_POST['noTrans'];
switch ($proses) {
    case 'getData':
        unset($_SESSION['temp']['tempNospb']);
        echo "<fieldset>\r\n                <legend>".$_SESSION['lang']['list'].'</legend>';
        echo "<table cellspacing=1 border=0>\r\n                <thead>\r\n                <tr><td align=center>".$_SESSION['lang']['kebun']."</td>\r\n                <td align=center>".$_SESSION['lang']['pabrik']."</td></tr>\r\n                </thead>\r\n                <tbody><tr class=rowcontent><td>";
        echo "\r\n                        <table cellspacing=1 border=0 id=rkmndsiPupuk class='sortable'>\r\n                <thead>\r\n<tr class=rowheader>\r\n<td>No</td>\r\n<td>".$_SESSION['lang']['kodeorg']."</td>\r\n<td>".$_SESSION['lang']['nospb']."</td>\r\n<td>".$_SESSION['lang']['tglNospb']."</td>\r\n<td>".$_SESSION['lang']['status']."</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        $limit = 50;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_spbht  where kodeorg='".$kdOrg."' and tanggal='".$tgl."' order by `tanggal` desc";
        $query2 = mysql_query($sql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select kodeorg,nospb,tanggal,posting from '.$dbname.".kebun_spbht  where kodeorg='".$kdOrg."' and tanggal='".$tgl."' order by `tanggal` desc limit ".$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc) ;
        $row = mysql_num_rows($qlvhc);
        if (0 < $row) {
            while ($res = mysql_fetch_assoc($qlvhc)) {
                $sNospb = 'select nospb from '.$dbname.".kebun_spbht where kodeorg='".$kdOrg."' and tanggal='".$tgl."'";
                $qNospb = mysql_query($sNospb) ;
                $rNospb = mysql_fetch_assoc($qNospb);
                $arrStat = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
                $stat = $arrStat[$res['posting']];
                $arrNospb[] = $res;
                $_SESSION['temp']['tempNospb'] = $arrNospb;
                ++$no;
                echo "\r\n                                <tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$res['kodeorg']."</td>\r\n                                <td>".$res['nospb']."</td>\r\n                                <td>".tanggalnormal($res['tanggal'])."</td>\r\n                                <td>".$stat.'</td>';
            }
            echo '</tbody></table>';
        } else {
            echo "<tr class=rowcontent><td colspan='5' align='center'>Not Found</td></tr></tbody></table>";
        }

        echo '</td><td>';
        echo "\r\n                                                <table cellspacing=1 border=0  class='sortable'>\r\n                                        <thead>\r\n                        <tr class=rowheader>\r\n                        <td>No</td>\r\n                        <td>".$_SESSION['lang']['tanggal']."</td>\r\n                        <td>".$_SESSION['lang']['nospb']."</td>\r\n                        <td>".$_SESSION['lang']['berat']."</td>\r\n                        <td>Action</td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody>\r\n                        ";
        if (isset($_SESSION['temp']['tempNospb'])) {
            foreach ($_SESSION['temp']['tempNospb'] as $rw => $dt) {
                $hslNosbp = $dt['nospb'];
                $sPabrik = 'select * from '.$dbname.".pabrik_timbangan where nospb='".$hslNosbp."' and kodeorg='".$kdOrg."' ";
                $qPabrik = mysql_query($sPabrik) ;
                $rowPabrik = mysql_num_rows($qPabrik);
                if (0 < $rowPabrik) {
                    $res = mysql_fetch_assoc($qPabrik);
                    $sNospb = 'select totalkg,nospb from '.$dbname.".kebun_spbdt  where nospb='".$res['nospb']."'";
                    $qNospb = mysql_query($sNospb) ;
                    $rNospb = mysql_fetch_assoc($qNospb);
                    $res['beratbersih'] = $res['beratbersih'] - $res['kgpotsortasi'];
                    $y = (int) ($res['beratbersih']);
                    ++$tr;
                    echo "\r\n                                                    <tr class=rowcontent>\r\n                                                                        <td>".$tr."</td>\r\n                                                                        <td>".tanggalnormal($res['tanggal'])."</td>\r\n                                                                        <td>".$res['nospb']."</td>\r\n                                                                        <td>".$y.'</td><td>';
					if (0 == $y) {
                        echo 'Data incomplete';
                    } else {
                        if (0 == $rNospb['totalkg']) {
                            echo "[&nbsp;<a href=# onclick=prosesData('".$rNospb['nospb']."','".$res['notransaksi']."')>".$_SESSION['lang']['belumposting'].'</a>&nbsp;]&nbsp; [&nbsp;';
                            echo "<a href=# onclick=\"viewData('".$rNospb['nospb'].'###'.$res['notransaksi']."','".$_SESSION['lang']['detail']."','<fieldset><legend>".$_SESSION['lang']['AmbilKgTimbangan'].'</legend><div id=container></div><input type=hidden id=detNospb name=detNospb value='.$key."></fieldset>',event)\";>".$_SESSION['lang']['detail'].'</a>&nbsp;]';
                        } else {
                            echo "<a href=# onclick=\"viewData('".$rNospb['nospb'].'###'.$res['notransaksi']."','".$_SESSION['lang']['detail']."','<fieldset><legend>".$_SESSION['lang']['AmbilKgTimbangan'].'</legend><div id=container></div><input type=hidden id=detNospb name=detNospb value='.$key."></fieldset>',event)\";>".$_SESSION['lang']['detail'].'</a>';
                        }
                    }

                    echo '</td></tr>';
                } else {
                    echo "<tr class=rowcontent><td colspan='5' align='center'>Not Found</td></tr>";
                }
            }
        } else {
            echo "<tr class=rowcontent><td colspan='5' align='center'>Not Found</td></tr>";
        }

        echo "</tbody></table></td></tr></tbody>\r\n                                        <table></fieldset>";

        break;
    case 'PostingData':
        $sCek = 'select bjr from '.$dbname.".kebun_spbdt where nospb='".$noSpb."'";
        $qCek = mysql_query($sCek) ;
        $b = 0;
        while ($rCek = mysql_fetch_assoc($qCek)) {
            if (0 != $rCek['bjr']) {
                ++$b;
            }
        }
        $sCek2 = 'select bjr from '.$dbname.".kebun_spbdt where nospb='".$noSpb."'";
        $qCek = mysql_query($sCek2) ;
        $rCek2 = mysql_num_rows($qCek);
        if ($b == $rCek2) {
            $sNospb = 'select nospb,blok,kgbjr from '.$dbname.".kebun_spbdt where nospb='".$noSpb."'";
            $qNospb = mysql_query($sNospb) ;
            while ($rNospb = mysql_fetch_assoc($qNospb)) {
                $sTotal = 'select sum(kgbjr) as total from '.$dbname.".kebun_spbdt where nospb='".$rNospb['nospb']."'";
                $qTotal = mysql_query($sTotal) ;
                $rTotal = mysql_fetch_assoc($qTotal);
                $sTimbngn = 'select (beratbersih-kgpotsortasi) as beratbersih,brondolan from '.$dbname.".pabrik_timbangan where notransaksi='".$noTrans."' and nospb='".$rNospb['nospb']."' ";
                $qTimbngn = mysql_query($sTimbngn) ;
                $rTimbngn = mysql_fetch_assoc($qTimbngn);
                $x = (int) ($rTimbngn['beratbersih']);
                $persen = @(int) ($rNospb['kgbjr']) / @(int) ($rTotal['total']);
                if (0 == $persen) {
                    $sTotal = 'select count(*) as total from '.$dbname.".kebun_spbdt where nospb='".$rNospb['nospb']."'";
                    $qTotal = mysql_query($sTotal) ;
                    $rTotal = mysql_fetch_assoc($qTotal);
                    $kgWb = $x / $rTotal['total'];
                    $totKg = $x / $rTotal['total'];
                } else {
                    $kgWb = $persen * $x;
                    $totKg = $persen * $x;
                }

                $sUpd = 'update '.$dbname.".kebun_spbdt set kgwb='".$kgWb."',totalkg='".$totKg."' where nospb='".$rNospb['nospb']."' and blok='".$rNospb['blok']."' ";
                if (mysql_query($sUpd)) {
                    $sUpdate = 'update '.$dbname.".kebun_spbht set posting='1',postingby='".$_SESSION['standard']['userid']."' where nospb='".$rNospb['nospb']."'";
                    if (mysql_query($sUpdate)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            }

            break;
        }

        if ('EN' == $_SESSION['language']) {
            echo 'warning: There is no AVG weight in this transaction, confirmation can not be done';
        } else {
            echo 'warning: Dalam No.SPB ini ada data belum terdapat BJR, posting tidak dapat dilanjutkan';
        }

        exit();
    case 'ShowData':
        $sShwData2 = 'select * from '.$dbname.".kebun_spbht where nospb='".$noSpb."' ";
        $qShwData2 = mysql_query($sShwData2) ;
        $rShwData2 = mysql_fetch_assoc($qShwData2);
        $arrStat = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
        $stat = $arrStat[$rShwData2['posting']];
        echo "\r\n                <fieldset><legend>".$_SESSION['lang']['header']."</legend>\r\n                <table cellspacing=1 border=0>\r\n                <tr><td>".$_SESSION['lang']['nospb'].'</td><td>:</td><td>'.$rShwData2['nospb']."</td></tr>\r\n                <tr><td>".$_SESSION['lang']['tglNospb'].'</td><td>:</td><td>'.tanggalnormal($rShwData2['tanggal'])."</td></tr>\r\n                <tr><td>".$_SESSION['lang']['kodeorg'].'</td><td>:</td><td>'.$rShwData2['kodeorg']."</td></tr>\r\n                <tr><td>".$_SESSION['lang']['status'].'</td><td>:</td><td>'.$stat."</td></tr>\r\n                </table></fieldset><br />\r\n                ";
        echo '<fieldset><legend>'.$_SESSION['lang']['detail']."</legend>\r\n                <table cellspacing=1 border=0>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No</td>\r\n                <td>".$_SESSION['lang']['blok']."</td>\r\n                <td>".$_SESSION['lang']['janjang']."</td>\r\n                <td>".$_SESSION['lang']['bjr']."</td>\r\n                </tr></thead>\r\n                <tbody>\r\n                ";
        $sShwData = 'select a.*,b.* from '.$dbname.'.kebun_spbht a inner join '.$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.nospb='".$noSpb."' ";
        $qShwData = mysql_query($sShwData) ;
        while ($rShwData = mysql_fetch_assoc($qShwData)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rShwData['blok']."</td>\r\n                <td>".$rShwData['jjg']."</td>\r\n                <td>".$rShwData['bjr']."</td>\r\n                </tr>";
        }
        echo '</tbody></table>*nb.Satuan yang digunakan KG/Unit of Measurement is KG.</legend>';

        break;
    default:
        break;
}

?>