<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\n\n";
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
$per = $_POST['per'];
$kodeorg = $_POST['kodeorg'];
$jabatan = $_POST['jabatan'];
$kar = $_POST['kar'];
$pembagi = $_POST['pembagi'];
$bjr = $_POST['bjr'];
$totpanen = $_POST['totpanen'];
$premi = $_POST['premi'];
$m1 = $_POST['m1'];
$rpm1 = $_POST['rpm1'];
$m2 = $_POST['m2'];
$rpm2 = $_POST['rpm2'];
$m3 = $_POST['m3'];
$rpm3 = $_POST['rpm3'];
$m4 = $_POST['m4'];
$rpm4 = $_POST['rpm4'];
$m5 = $_POST['m5'];
$rpm5 = $_POST['rpm5'];
$m6 = $_POST['m6'];
$rpm6 = $_POST['rpm6'];
$r1 = $_POST['r1'];
$rpr1 = $_POST['rpr1'];
$r2 = $_POST['r2'];
$rpr2 = $_POST['rpr2'];
$r3 = $_POST['r3'];
$rpr3 = $_POST['rpr3'];
$r4 = $_POST['r4'];
$rpr4 = $_POST['rpr4'];
$k1 = $_POST['k1'];
$rpk1 = $_POST['rpk1'];
$k2 = $_POST['k2'];
$rpk2 = $_POST['rpk2'];
$k3 = $_POST['k3'];
$rpk3 = $_POST['rpk3'];
$k4 = $_POST['k4'];
$rpk4 = $_POST['rpk4'];
$k5 = $_POST['k5'];
$rpk5 = $_POST['rpk5'];
$pterima = $_POST['pterima'];
echo "\n";
switch ($method) {
    case 'getPremi':
        if ('Mandor Satu' === $jabatan) {
            $i = 'select avg(premiinput) as premi from '.$dbname.".kebun_premikemandoran where periode='".$per."' and karyawanid in\n\t\t\t\t\t(select nikmandor from ".$dbname.".kebun_aktifitas  \n\t\t\t\t\t where notransaksi  like '%".$kodeorg."/PNN%' and tanggal like '".$per."%' and nikmandor1='".$kar."')";
            $n = mysql_query($i) ;
            $d = mysql_fetch_assoc($n);
            $rataPremiPnn = $d['premi'];
        } else {
            $i = 'select basisjjg from '.$dbname.".kebun_5basispanen where bjr<='".$bjr."' and kodeorg='".$_SESSION['empl']['regional']."' order by bjr desc limit 1 ";
            $n = mysql_query($i) ;
            $d = mysql_fetch_assoc($n);
            $basisBor = $d['basisjjg'];
            $pnnBlnTon = $totpanen / 1000;
            $basisBorBln = ($basisBor * $bjr) / 1000 * 23;
            $lebihBasisBor = $pnnBlnTon - $basisBorBln;
            if ($lebihBasisBor <= 0) {
                $lebihBasisBor = 0;
            }

            $totPremiLebihBasisBor = $lebihBasisBor * 83000;
            if ($pembagi <= 10) {
                $pembagi = 10;
            }

            $rataPremiPnn = $totPremiLebihBasisBor / $pembagi;
        }

        if ('Mandor' === $jabatan) {
            $pengali = 1.5;
        } else {
            if ('Kerani' === $jabatan) {
                $pengali = 1.25;
            } else {
                if ('Mandor Satu' === $jabatan) {
                    $pengali = 1.5;
                }
            }
        }

        echo number_format($rataPremiPnn * $pengali, 2);

        break;
    case 'getPremiDitCon':
        break;
    case 'getKar':
        $optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if ('Mandor Satu' === $jabatan) {
            $disKar = 'distinct(nikmandor1)';
        } else {
            if ('Kerani' === $jabatan) {
                $disKar = 'distinct(keranimuat)';
            } else {
                if ('Mandor' === $jabatan) {
                    $disKar = 'distinct(nikmandor)';
                }
            }
        }

        $i = 'select '.$disKar.' from '.$dbname.".kebun_aktifitas where notransaksi like '%".$kodeorg."/PNN%' and tanggal like '".$per."%' ";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            if ('Mandor Satu' === $jabatan) {
                $optKar .= "<option value='".$d['nikmandor1']."'>".$optNikKar[$d['nikmandor1']].' - '.$optNmKar[$d['nikmandor1']].'</option>';
            } else {
                if ('Kerani' === $jabatan) {
                    $optKar .= "<option value='".$d['keranimuat']."'>".$optNikKar[$d['keranimuat']].' - '.$optNmKar[$d['keranimuat']].'</option>';
                } else {
                    if ('Mandor' === $jabatan) {
                        $optKar .= "<option value='".$d['nikmandor']."'>".$optNikKar[$d['nikmandor']].' - '.$optNmKar[$d['nikmandor']].'</option>';
                    }
                }
            }
        }
        echo $optKar;

        break;
    case 'getPembagi':
        if ('Kerani' === $jabatan) {
            $where = "and b.keranimuat='".$kar."'";
            $groupby = 'group by b.keranimuat';
        } else {
            if ('Mandor' === $jabatan) {
                $where = "and b.nikmandor='".$kar."'";
                $groupby = 'group by b.nikmandor';
            } else {
                if ('RECORDER' === $jabatan) {
                    $where = "and b.nikasisten='".$kar."'";
                    $groupby = 'group by b.nikasisten';
                }
            }
        }

        $i = 'select count(distinct a.nik) as jumlah  from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \n\t\t\twhere a.notransaksi like '%".$kodeorg."/PNN%' and tanggal like '".$per."%' ".$where.' ';
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $x = 'select sum(hasilkerjakg)/sum(hasilkerja) as bjr,sum(hasilkerjakg) as hasilkerjakg from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas \n\t\t\tb on a.notransaksi=b.notransaksi \n\t\t\twhere a.notransaksi like '%".$kodeorg."/PNN%' and tanggal like '".$per."%' ".$where.' '.$groupby.' ';
        $y = mysql_query($x) ;
        $z = mysql_fetch_assoc($y);
        echo $d['jumlah'].'###'.$jabatan.'###'.number_format($z['bjr'], 2).'###'.number_format($z['hasilkerjakg'], 2);

        break;
    case 'tKuning':
        $i = 'select * from '.$dbname.".kebun_5dendapengawas where jabatan='".$jabatan."' order by kode asc";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $nilai[$no] = $d['denda'];
        }
        $rpk1 = $k1 * $nilai[1];
        $rpk2 = $k2 * $nilai[2];
        $rpk3 = $k3 * $nilai[3];
        $rpk4 = $k4 * $nilai[4];
        $rpk5 = $k5 * $nilai[5];
        $pterima = $premi - ($rpk1 + $rpk2 + $rpk3 + $rpk4 + $rpk5);
        echo number_format($rpk1, 2).'###'.number_format($rpk2, 2).'###'.number_format($rpk3, 2).'###'.number_format($rpk4, 2).'###'.number_format($rpk5, 2).'###'.number_format($pterima, 2);

        break;
    case 'tMerah':
        $i = 'select * from '.$dbname.".kebun_5dendapengawas where jabatan='".$jabatan."' order by kode asc";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $nilai[$no] = $d['denda'];
        }
        $rpr1 = $r1 * $nilai[1];
        $rpr2 = $r2 * $nilai[2];
        $rpr3 = $r3 * $nilai[3];
        $rpr4 = $r4 * $nilai[4];
        $pterima = $premi - ($rpr1 + $rpr2 + $rpr3 + $rpr4);
        echo number_format($rpr1, 2).'###'.number_format($rpr2, 2).'###'.number_format($rpr3, 2).'###'.number_format($rpr4, 2).'###'.number_format($pterima, 2);

        break;
    case 'getHijau':
        $i = "select sum(penalti1) as m1, sum(penalti2) as m4, sum(penalti3) as m2, sum(penalti5) as m3, sum(penalti6) as m5, sum(penalti7) as m6,b.nikmandor\n\t\t\t\tfrom ".$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas \n\t\t\t\tb on a.notransaksi=b.notransaksi \n\t\t\t\twhere a.notransaksi like '%".$kodeorg."/PNN%' and tanggal like '".$per."%' and b.nikmandor='".$kar."' group by b.nikmandor ";
        $n = mysql_query($i) || mysql_error($conn);
        $d = mysql_fetch_assoc($n);
        echo number_format($d['m1']).'###'.number_format($d['m2']).'###'.number_format($d['m3']).'###'.number_format($d['m4']).'###'.number_format($d['m5']).'###'.number_format($d['m6']);

        break;
    case 'tHijau':
        $i = 'select * from '.$dbname.".kebun_5dendapengawas where jabatan='".$jabatan."' order by kode asc";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $nilai[$no] = $d['denda'];
        }
        $rpm1 = $m1 * $nilai[1];
        $rpm2 = $m2 * $nilai[2];
        $rpm3 = $m3 * $nilai[3];
        $rpm4 = $m4 * $nilai[4];
        $rpm5 = $m5 * $nilai[5];
        $rpm6 = $m6 * $nilai[6];
        $pterima = $premi - ($rpm1 + $rpm2 + $rpm3 + $rpm4 + $rpm5 + $rpm6);
        echo number_format($rpm1, 2).'###'.number_format($rpm2, 2).'###'.number_format($rpm3, 2).'###'.number_format($rpm4, 2).'###'.number_format($rpm5, 2).'###'.number_format($rpm6, 2).'###'.number_format($pterima, 2);

        break;
    case 'insert':
        $i = 'insert into '.$dbname.".kebun_premikemandoran \n\t\t\t(periode,kodeorg,jabatan,karyawanid,pembagi,bjr,totalpanen,premi,\n\t\t\tm1,rpm1,m2,rpm2,m3,rpm3,m4,rpm4,m5,rpm5,m6,rpm6,\n\t\t\tr1,rpr1,r2,rpr2,r3,rpr3,r4,rpr4,\n\t\t\tk1,rpk1,k2,rpk2,k3,rpk3,k4,rpk4,k5,rpk5,\n\t\t\tpremiinput,updateby)\n\t\t\tvalues ('".$per."','".$kodeorg."','".$jabatan."','".$kar."','".$pembagi."','".$bjr."','".$totpanen."','".$premi."',\n\t\t\t'".$m1."','".$rpm1."','".$m2."','".$rpm2."','".$m3."','".$rpm3."','".$m4."','".$rpm4."','".$m5."','".$rpm5."','".$m6."','".$rpm6."',\n\t\t\t'".$r1."','".$rpr1."','".$r2."','".$rpr2."','".$r3."','".$rpr3."','".$r4."','".$rpr4."',\n\t\t\t'".$k1."','".$rpk1."','".$k2."','".$rpk2."','".$k3."','".$rpk3."','".$k4."','".$rpk4."','".$k5."','".$rpk5."','".$pterima."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\n\t<div id=container>\n\t\t<img onclick=excel(event,'kebun_slave_premimandorbaru.php') src=images/excel.jpg class=resicon title='MS.Excel'> \n\t\t<table class=sortable cellspacing=1 border=0>\n\t     <thead>\n\t\t\t <tr class=rowheader>\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\n\t\t\t \t <td align=center>".$_SESSION['lang']['periode']."</td>\n\t\t\t \t <td align=center>".$_SESSION['lang']['kodeorg']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['jabatan']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['namakaryawan']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['pembagi']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['bjr']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['panen']."</td>\n\t\t\t\t\n\t\t\t\t \n\t\t\t\t <td align=center>".$_SESSION['lang']['m1']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['m2']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['m3']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['m4']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['m5']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['m6']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t \n\t\t\t\t <td align=center>".$_SESSION['lang']['r1']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['r2']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['r3']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['r4']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t \n\t\t\t\t <td align=center>".$_SESSION['lang']['k1']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['k2']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['k3']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['k4']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['k5']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t \n\t\t\t\t <td align=center>".$_SESSION['lang']['premi'].' '.$_SESSION['lang']['input']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['posting']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['tanggalupdate']."</td>\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\n\t\t\t </tr>\n\t\t</thead>\n\t\t<tbody>";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_premikemandoran  where kodeorg='".$kodeorg."' and periode='".$per."'";
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.".kebun_premikemandoran where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate desc limit ".$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['periode'].'</td>';
            echo '<td align=left>'.$d['kodeorg'].'</td>';
            echo '<td align=left>'.$d['jabatan'].'</td>';
            echo '<td align=left>'.$optNmKar[$d['karyawanid']].'</td>';
            echo '<td align=right>'.$d['pembagi'].'</td>';
            echo '<td align=right>'.$d['bjr'].'</td>';
            echo '<td align=right>'.$d['totalpanen'].'</td>';
            echo '<td align=right>'.$d['m1'].'</td>';
            echo '<td align=right>'.$d['rpm1'].'</td>';
            echo '<td align=right>'.$d['m2'].'</td>';
            echo '<td align=right>'.$d['rpm2'].'</td>';
            echo '<td align=right>'.$d['m3'].'</td>';
            echo '<td align=right>'.$d['rpm3'].'</td>';
            echo '<td align=right>'.$d['m4'].'</td>';
            echo '<td align=right>'.$d['rpm4'].'</td>';
            echo '<td align=right>'.$d['m5'].'</td>';
            echo '<td align=right>'.$d['rpm5'].'</td>';
            echo '<td align=right>'.$d['m6'].'</td>';
            echo '<td align=right>'.$d['rpm6'].'</td>';
            echo '<td align=right>'.$d['r1'].'</td>';
            echo '<td align=right>'.$d['rpr1'].'</td>';
            echo '<td align=right>'.$d['r2'].'</td>';
            echo '<td align=right>'.$d['rpr2'].'</td>';
            echo '<td align=right>'.$d['r3'].'</td>';
            echo '<td align=right>'.$d['rpr3'].'</td>';
            echo '<td align=right>'.$d['r4'].'</td>';
            echo '<td align=right>'.$d['rpr4'].'</td>';
            echo '<td align=right>'.$d['k1'].'</td>';
            echo '<td align=right>'.$d['rpk1'].'</td>';
            echo '<td align=right>'.$d['k2'].'</td>';
            echo '<td align=right>'.$d['rpk2'].'</td>';
            echo '<td align=right>'.$d['k3'].'</td>';
            echo '<td align=right>'.$d['rpk3'].'</td>';
            echo '<td align=right>'.$d['k4'].'</td>';
            echo '<td align=right>'.$d['rpk4'].'</td>';
            echo '<td align=right>'.$d['k5'].'</td>';
            echo '<td align=right>'.$d['rpk5'].'</td>';
            echo '<td align=right>'.$d['premiinput'].'</td>';
            echo '<td align=left>'.$d['posting'].'</td>';
            echo '<td align=left>'.$d['lastupdate'].'</td>';
            echo '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
            echo '</tr>';
        }
        echo "\n\t\t<tr class=rowheader><td colspan=43 align=center>\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\n\t\t</td>\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'excel':
        $per = $_GET['per'];
        $kodeorg = $_GET['kodeorg'];
        $stream = 'Laporan_Premi_Panen';
        $stream .= "<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>\n\t\t\t\t\t\t\t<thead class=rowheader>\n\t\t\t\t\t\t\t\t<tr class=rowheader>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nourut']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['periode']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['kodeorg']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['jabatan']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['namakaryawan']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['pembagi']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['bjr']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['panen']."</td>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['m1']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['m2']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['m3']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['m4']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['m5']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['m6']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['r1']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['r2']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['r3']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['r4']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['k1']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['k2']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['k3']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['k4']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['k5']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['denda']."</td>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['premi'].' '.$_SESSION['lang']['input']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['posting']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['tanggalupdate']."</td>\n\t\t\t\t\t\t\t\t\t<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['updateby']."</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t<tbody>";
        $i = 'select * from '.$dbname.".kebun_premikemandoran where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate desc";
        $n = mysql_query($i) || mysql_error($conn);
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $stream .= '<tr class=rowcontent bgcolor=#FFFFFF>';
            $stream .= '<td align=center>'.$no.'</td>';
            $stream .= '<td align=left>'.$d['periode'].'</td>';
            $stream .= '<td align=left>'.$d['kodeorg'].'</td>';
            $stream .= '<td align=left>'.$d['jabatan'].'</td>';
            $stream .= '<td align=left>'.$optNmKar[$d['karyawanid']].'</td>';
            $stream .= '<td align=right>'.$d['pembagi'].'</td>';
            $stream .= '<td align=right>'.$d['bjr'].'</td>';
            $stream .= '<td align=right>'.$d['totalpanen'].'</td>';
            $stream .= '<td align=right>'.$d['m1'].'</td>';
            $stream .= '<td align=right>'.$d['rpm1'].'</td>';
            $stream .= '<td align=right>'.$d['m2'].'</td>';
            $stream .= '<td align=right>'.$d['rpm2'].'</td>';
            $stream .= '<td align=right>'.$d['m3'].'</td>';
            $stream .= '<td align=right>'.$d['rpm3'].'</td>';
            $stream .= '<td align=right>'.$d['m4'].'</td>';
            $stream .= '<td align=right>'.$d['rpm4'].'</td>';
            $stream .= '<td align=right>'.$d['m5'].'</td>';
            $stream .= '<td align=right>'.$d['rpm5'].'</td>';
            $stream .= '<td align=right>'.$d['m6'].'</td>';
            $stream .= '<td align=right>'.$d['rpm6'].'</td>';
            $stream .= '<td align=right>'.$d['r1'].'</td>';
            $stream .= '<td align=right>'.$d['rpr1'].'</td>';
            $stream .= '<td align=right>'.$d['r2'].'</td>';
            $stream .= '<td align=right>'.$d['rpr2'].'</td>';
            $stream .= '<td align=right>'.$d['r3'].'</td>';
            $stream .= '<td align=right>'.$d['rpr3'].'</td>';
            $stream .= '<td align=right>'.$d['r4'].'</td>';
            $stream .= '<td align=right>'.$d['rpr4'].'</td>';
            $stream .= '<td align=right>'.$d['k1'].'</td>';
            $stream .= '<td align=right>'.$d['rpk1'].'</td>';
            $stream .= '<td align=right>'.$d['k2'].'</td>';
            $stream .= '<td align=right>'.$d['rpk2'].'</td>';
            $stream .= '<td align=right>'.$d['k3'].'</td>';
            $stream .= '<td align=right>'.$d['rpk3'].'</td>';
            $stream .= '<td align=right>'.$d['k4'].'</td>';
            $stream .= '<td align=right>'.$d['rpk4'].'</td>';
            $stream .= '<td align=right>'.$d['k5'].'</td>';
            $stream .= '<td align=right>'.$d['rpk5'].'</td>';
            $stream .= '<td align=right>'.$d['premiinput'].'</td>';
            $stream .= '<td align=left>'.$d['posting'].'</td>';
            $stream .= '<td align=left>'.$d['lastupdate'].'</td>';
            $stream .= '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
            $stream .= '</tr>';
        }
        $stream .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'Premi_Kemandoran'.$tglSkrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\n\t\t\t\tparent.window.alert('Can't convert to excel format');\n\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\n\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\n\t\t\t\t</script>";
            closedir($handle);
        }

        break;
}

?>