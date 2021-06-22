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
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['list'].'</legend>';
        echo "<div style=\"width:600px; height:450px; overflow:auto;\">\r\n\t\t\t<table cellspacing=1 border=0 id=rkmndsiPupuk class='sortable'>\r\n\t\t<thead>\r\n<tr class=rowheader>\r\n<td>No</td>\r\n<td>".$_SESSION['lang']['kodeorg']."</td>\r\n<td>".$_SESSION['lang']['tglNospb']."</td>\r\n<td>".$_SESSION['lang']['nospb']."</td>\r\n<td>".$_SESSION['lang']['status']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_timbangan where kodeorg='".$kdOrg."' and tanggal='".$tgl."' order by `tanggal` desc";
        $query2 = mysql_query($sql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select nospb,notransaksi from '.$dbname.".pabrik_timbangan where kodeorg='".$kdOrg."' and tanggal='".$tgl."' order by `tanggal` desc limit ".$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc) ;
        $row = mysql_num_rows($qlvhc);
        if (0 < $row) {
            while ($res = mysql_fetch_assoc($qlvhc)) {
                $sNospb = 'select a.tanggal,a.kodeorg,a.posting,b.* from '.$dbname.'.kebun_spbht a inner join '.$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.nospb='".$res['nospb']."'";
                $qNospb = mysql_query($sNospb) ;
                $rNospb = mysql_fetch_assoc($qNospb);
                if ($rNospb['posting'] < 1) {
                    $stat = 'Belum';
                } else {
                    if (0 < $rNospb['posting']) {
                        $stat = 'Sudah';
                    }
                }

                ++$no;
                echo "\r\n\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$rNospb['kodeorg']."</td>\r\n\t\t\t\t<td>".tanggalnormal($rNospb['tanggal'])."</td>\r\n\t\t\t\t<td>".$rNospb['nospb']."</td>\r\n\t\t\t\t<td>".$stat.'</td><td>';
                if ($rNospb['posting'] < 1) {
                    echo "<a href=# onclick=prosesData('".$rNospb['nospb']."','".$res['notransaksi']."')>".$_SESSION['lang']['belumposting'].'</a>';
                } else {
                    echo $_SESSION['lang']['posting'];
                }

                echo '</td></tr>';
            }
            echo '</tbody></table></div></fieldset>';
        } else {
            echo "<tr class=rowcontent><td colspan='6' align='center'>Not Found</td></tr></tbody></table></div></fieldset>";
        }

        CLOSE_BOX();

        break;
    case 'PostingData':
        $sNospb = 'select * from '.$dbname.".kebun_spbdt where nospb='".$noSpb."'";
        $qNospb = mysql_query($sNospb) ;
        while ($rNospb = mysql_fetch_assoc($qNospb)) {
            $sTimbngn = 'select * from '.$dbname.".pabrik_timbangan where notransaksi='".$noTrans."' and nospb='".$noSpb."' ";
            $qTimbngn = mysql_query($sTimbngn) ;
            $rTimbngn = mysql_fetch_assoc($qTimbngn);
            $rTimbngn['beratbersih'] = $rTimbngn['beratbersih'] - $rTimbngn['kgpotsortasi'];
            $x = (int) ($rTimbngn['beratbersih']);
            $y = (int) ($rTimbngn['beratbersih'] + $rTimbngn['brondolan']);
            $sBagi = 'SELECT sum(jjg+bjr+mentah+busuk+matang+lewatmatang)as pembagi,jjg,bjr  FROM '.$dbname.".kebun_spbdt where blok='".$rNospb['blok']."'";
            $qBagi = mysql_query($sBagi) ;
            $rBagi = mysql_fetch_assoc($qBagi);
            $berat = (int) ($rBagi['jjg']) * (int) ($rBagi['bjr']);
            $persen = $berat / (int) ($rBagi['pembagi']);
            $kgWb = $persen * $x;
            $totKg = $persen * $y;
            $kgBjr = $berat;
            $sUpd = 'update '.$dbname.".kebun_spbdt set kgwb='".$kgWb."',totalkg='".$totKg."',kgbjr='".$kgBjr."' where nospb='".$rNospb['nospb']."' and blok='".$rNospb['blok']."' ";
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
    case 'insert':
        if ('' === $jnsPpk || '' === $dosis) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sCek = 'select kodeorg,tahuntanam,periodepemupukan from '.$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".kebun_rekomendasipupuk (kodeorg, tahuntanam, kodebarang, dosis, satuan, periodepemupukan, jenisbibit) values \r\n\t\t\t('".$idKbn."','".$thnTnm."','".$jnsPpk."','".$dosis."','".$satuan."','".$periode."','".$jnsBibit."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }

        echo 'warning:This Data Already Input';
        exit();
    case 'getData':
        $sGet = 'select * from '.$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
        $qGet = mysql_query($sGet) ;
        $rGet = mysql_fetch_assoc($qGet);
        echo $rGet['kodeorg'].'###'.$rGet['tahuntanam'].'###'.$rGet['kodebarang'].'###'.$rGet['dosis'].'###'.$rGet['satuan'].'###'.$rGet['periodepemupukan'].'###'.$rGet['jenisbibit'];

        break;
    case 'update':
        if ('' === $jnsPpk || '' === $dosis) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sUp = 'update '.$dbname.".kebun_rekomendasipupuk set kodebarang='".$jnsPpk."', dosis='".$dosis."', satuan='".$satuan."', jenisbibit='".$jnsBibit."' where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
        if (mysql_query($sUp)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cariData':
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['result'].'</legend>';
        echo "<div style=\"width:600px; height:450px; overflow:auto;\">\r\n\t\t\t<table cellspacing=1 border=0 class='sortable'>\r\n\t\t<thead>\r\n<tr class=rowheader>\r\n<td>".$_SESSION['lang']['tahunpupuk']."</td>\r\n<td>".$_SESSION['lang']['kebun']."</td>\r\n<td>".$_SESSION['lang']['tahuntanam']."</td>\r\n<td>".$_SESSION['lang']['jenisPupuk']."</td>\r\n<td>".$_SESSION['lang']['dosis']."</td>\r\n<td>".$_SESSION['lang']['satuan']."</td>\r\n<td>".$_SESSION['lang']['jenisbibit']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        if ('' !== $periode) {
            $where = " periodepemupukan LIKE  '%".$periode."%'";
        } else {
            if ('' !== $idKbn) {
                $where .= " kodeorg LIKE '".$idKbn."'";
            } else {
                if ('' !== $periode && '' !== $idKbn) {
                    $where .= " periodepemupukan LIKE '%".$periode."%' and kodeorg LIKE '%".$idKbn."%'";
                }
            }
        }

        $strx = 'select * from '.$dbname.'.kebun_rekomendasipupuk where '.$where.' order by periodepemupukan desc';
        if ($qry = mysql_query($strx)) {
            $numrows = mysql_num_rows($qry);
            if ($numrows < 1) {
                echo '<tr class=rowcontent><td colspan=9>Not Found</td></tr>';
            } else {
                while ($res = mysql_fetch_assoc($qry)) {
                    $skdBrg = 'select  namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
                    $qkdBrg = mysql_query($skdBrg) ;
                    $rBrg = mysql_fetch_assoc($qkdBrg);
                    $sBibit = 'select jenisbibit  from '.$dbname.".setup_jenisbibit where jenisbibit='".$res['jenisbibit']."'";
                    $qBibit = mysql_query($sBibit) ;
                    $rBibit = mysql_fetch_assoc($qBibit);
                    ++$no;
                    echo "\r\n\t\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t<td>".$res['periodepemupukan']."</td>\r\n\t\t\t\t\t<td>".$res['kodeorg']."</td>\r\n\t\t\t\t\t<td>".$res['tahuntanam']."</td>\r\n\t\t\t\t\t<td>".$rBrg['namabarang']."</td>\r\n\t\t\t\t\t<td>".$res['dosis']."</td>\r\n\t\t\t\t\t<td>".$rBrg['satuan']."</td>\r\n\t\t\t\t\t<td>".$rBibit['jenisbibit'].'</td>';
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