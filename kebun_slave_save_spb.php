<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$blok = $_POST['blok'];
$noSpb = $_POST['noSpb'];
$tanggalx = tanggalsystem($_POST['tgl_ganti']);
$tanggal = tanggalsystem($_POST['tgl']);
$bjrHsl = $_POST['bjr'];
$jjngHsl = (int) ($_POST['jjng']);
$brondolanHsl = (int) ($_POST['brondolan']);
$user_online = $_SESSION['standard']['userid'];
$kdOrg = $_POST['kdOrg'];
$idDiv = $_POST['idDiv'];
$matang = $_POST['matang'];
$mentah = $_POST['mentah'];
$busuk = $_POST['busuk'];
$lwtmatang = $_POST['lwtmatang'];
$kdOrg = $_POST['kdOrg'];
$oldBlok = $_POST['oldBlok'];
$kgwb = $_POST['kgwb'];
if ('' === $kgwb) {
    $kgwb = 0;
}

$sReg = 'select distinct regional from '.$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."'";
$qReg = mysql_query($sReg) ;
$rReg = mysql_fetch_assoc($qReg);
switch ($proses) {
    case 'generateNo':
        $tgl = date('Ymd');
        $bln = substr($tgl, 4, 2);
        $thn = substr($tgl, 0, 4);
        $lokasi = $_SESSION['empl']['lokasitugas'];
        $lokasi = substr($lokasi, 0, 4);
        $scOrg = 'select distinct tipe from '.$dbname.".organisasi where kodeorganisasi='".$lokasi."'";
        $qcOrg = mysql_query($scOrg) ;
        $rcOrg = mysql_fetch_assoc($qcOrg);
        $rcOrg['tipe'];
        if ('KEBUN' === $rcOrg['tipe'] || 'KANWIL' === $rcOrg['tipe']) {
            $nospb = $lokasi.'/'.date('Y').'/'.date('m').'/';
            $ql = 'select `nospb` from '.$dbname.".`kebun_spbht` where nospb like '%".$nospb."%' order by `nospb` desc limit 0,1";
            $qr = mysql_query($ql) ;
            $rp = mysql_fetch_object($qr);
            $awal = substr($rp->nospb, -4, 4);
            $awal = (int) $awal;
            $cekbln = substr($rp->nospb, -7, 2);
            $cekthn = substr($rp->nospb, -12, 4);
            if ($thn !== $cekthn) {
                $awal = 1;
            } else {
                ++$awal;
            }

            $counter = addZero($awal, 4);
            $nospb = $lokasi.'/'.$thn.'/'.$bln.'/'.$counter;
            echo $nospb;

            break;
        }

    case 'amblBjr':
        $sStpBlok = 'SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM '.$dbname.'.`kebun_spbdt` a left join '.$dbname.".kebun_spbht b on a.nospb=b.nospb where blok = '".$blok."' and tanggal < '".$tanggalx."' group by tanggal order by tanggal desc limit 1";
        $qStpBlok = mysql_query($sStpBlok) ;
        $rStpBlok = mysql_fetch_assoc($qStpBlok);
        $rBjrCek = mysql_num_rows($qStpBlok);
        if (0 === (int) ($rStpBlok['bjr']) || 0 === $rBjrCek) {
            $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$blok."' and tahunproduksi = '".substr($_POST['periode'], 0, 4)."'");
            $res = fetchData($query);
            if (!empty($res)) {
                $rStpBlok['bjr'] = $res[0]['bjr'];
            } else {
                exit('error: BJR is not exist');
            }
        }

        echo number_format($rStpBlok['bjr'], 2);

        break;
    case 'cekData':
        if ('' !== $kgwb && 'KALTIM' !== $rReg['regional']) {
            $kgwb = 0;
        }

        $sCek = 'select nospb from '.$dbname.".kebun_spbht where nospb='".$noSpb."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_fetch_row($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".kebun_spbht (`nospb`, `kodeorg`, `tanggal`,`updateby`) values ('".$noSpb."','".$kdOrg."','".$tanggal."','".$user_online."')";
            if (mysql_query($sIns)) {
                $kgBjr = $jjngHsl * $bjrHsl;
                $sDetIns = 'insert into '.$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr) values ('".$noSpb."','".$blok."','".$jjngHsl."','".$bjrHsl."','".$brondolanHsl."','".$mentah."','".$busuk."','".$matang."','".$lwtmatang."','".$kgBjr."')";
                if (mysql_query($sDetIns)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            $cekPost = 'select distinct posting from '.$dbname.".kebun_spbht where nospb='".$noSpb."'";
            $qcekPost = mysql_query($cekPost) ;
            $rCek = mysql_fetch_assoc($qcekPost);
            if (0 != $rCek['posting']) {
                exit('Error:Nospb Sudah Posting');
            }

            $kgBjr = $jjngHsl * $bjrHsl;
            $sDetIns = 'insert into '.$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr,kgwb)\r\n                                values ('".$noSpb."','".$blok."','".$jjngHsl."','".$bjrHsl."','".$brondolanHsl."','".$mentah."','".$busuk."','".$matang."','".$lwtmatang."','".$kgBjr."','".$kgwb."')";
            if (mysql_query($sDetIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        break;
    case 'loadNewData':
        $lokasi = $_SESSION['empl']['lokasitugas'];
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_spbht where `kodeorg`='".$_SESSION['empl']['lokasitugas']."' order by tanggal asc";
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.".kebun_spbht where `kodeorg`='".$_SESSION['empl']['lokasitugas']."' order by tanggal asc limit ".$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc) ;
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            ++$no;
            $tgl = explode('-', tanggalnormal($rlvhc['tanggal']));
            list(, $tglBln, $tglThn) = $tgl;
            $periode = $tglThn.'-'.$tglBln;
            $scek = 'select distinct * from '.$dbname.".kebun_spbdt where nospb='".$rlvhc['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
            $qcek = mysql_query($scek) ;
            $rcek = mysql_num_rows($qcek);
            echo "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$rlvhc['nospb']."</td>\r\n\t\t\t<td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n\t\t\t<td>".$rlvhc['kodeorg'].'</td>';
            if ($rlvhc['updateby'] === $user_online) {
                echo "\r\n\t\t\t<td>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',\r\n\t\t\t'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >\t\r\n\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
            } else {
                if ('98' === $_SESSION['empl']['kodejabatan']) {
                    echo "<td>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',\r\n\t\t\t'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >\t\r\n\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
                } else {
                    echo "\r\n\t\t\t<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
                }
            }
        }
        echo "</tr>\r\n\t\t<tr class=rowheader><td colspan=5 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";

        break;
    case 'delData':
        $sCek = 'select posting from '.$dbname.".kebun_spbht where nospb='".$noSpb."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_fetch_assoc($qCek);
        if ('1' === $rCek['posting']) {
            echo 'warning:Already Post This No. SPB';
            exit();
        }

        $sql = 'delete from '.$dbname.".kebun_spbht where nospb='".$noSpb."' ";
        if (mysql_query($sql)) {
            $sqlDet = 'delete from '.$dbname.".kebun_spbdt where nospb='".$noSpb."'";
            if (mysql_query($sqlDet)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cariNospb':
        $lokasi = $_SESSION['empl']['lokasitugas'];
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
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
            $where = "and nospb LIKE  '%".$txt_search."%'";
        } else {
            if ('' !== $txt_tgl) {
                $where .= "and tanggal LIKE '".$txt_tgl."'";
            } else {
                if ('' !== $txt_tgl && '' !== $txt_search) {
                    $where .= "and nospb LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                }
            }
        }

        if ('' === $txt_search && '' === $txt_tgl) {
            $slvhc = 'select * from '.$dbname.".kebun_spbht where kodeorg='".$lokasi."' ".$where.' order by tanggal desc limit '.$offset.','.$limit.'';
            $ql2 = 'select count(*) jmlhrow from '.$dbname.".kebun_spbht \twhere  kodeorg='".$lokasi."' ".$where.' order by tanggal,nospb desc';
        } else {
            $slvhc = 'select * from '.$dbname.".kebun_spbht where  kodeorg='".$lokasi."' ".$where.' order by tanggal desc limit '.$offset.','.$limit.'';
            $ql2 = 'select count(*) jmlhrow from '.$dbname.".kebun_spbht \twhere   kodeorg='".$lokasi."' ".$where.' order by tanggal,nospb desc';
        }

        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $qlvhc = mysql_query($slvhc) ;
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            ++$no;
            $tgl = explode('-', tanggalnormal($rlvhc['tanggal']));
            list(, $tglBln, $tglThn) = $tgl;
            $periode = $tglThn.'-'.$tglBln;
            $scek = 'select distinct * from '.$dbname.".kebun_spbdt where nospb='".$rlvhc['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
            $qcek = mysql_query($scek) ;
            $rcek = mysql_num_rows($qcek);
            echo "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$rlvhc['nospb']."</td>\r\n\t\t\t<td>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n\t\t\t<td>".$rlvhc['kodeorg'].'</td>';
            if ($rlvhc['updateby'] === $user_online) {
                echo "\r\n\t\t\t<td>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',\r\n\t\t\t'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >\t\r\n\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
            } else {
                if ('98' === $_SESSION['empl']['kodejabatan']) {
                    echo "<td>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',\r\n\t\t\t'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >\t\r\n\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
                } else {
                    echo "\r\n\t\t\t<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
                }
            }
        }
        echo "</tr>\r\n\t\t<tr class=rowheader><td colspan=5 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";

        break;
    case 'updateData':
        if ('' !== $kgwb && 'KALTIM' !== $rReg['regional']) {
            $kgwb = 0;
        }

        $data = $_POST;
        $sCek = 'select distinct nospb from '.$dbname.".kebun_spbht where nospb='".$data['noSpb']."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            if ('' === $data['jjng'] || '' === $data['brondolan'] || '' === $data['bjr']) {
                echo 'Error : Tolong lengkap data detail, data tidak boleh kosong';
                exit();
            }

            $cekPost = 'select distinct posting from '.$dbname.".kebun_spbht where nospb='".$data['noSpb']."' and posting=1";
            $qcekPost = mysql_query($cekPost) ;
            $rCek = mysql_fetch_assoc($qcekPost);
            if (0 !== $rCek['posting']) {
                exit('Error:Nospb Sudah Posting');
            }

            $kgBjr = $jjngHsl * $bjrHsl;
            $sUpHead = 'update '.$dbname.".kebun_spbht set tanggal='".$tanggal."' where nospb='".$data['noSpb']."'";
            if (mysql_query($sUpHead)) {
                $sUpDetail = 'update '.$dbname.".kebun_spbdt set\r\n                                                                   blok='".$blok."',jjg='".$jjngHsl."',bjr='".$bjrHsl."',brondolan='".$brondolanHsl."',mentah='".$mentah."',\r\n                                                                   busuk='".$busuk."',matang='".$matang."',lewatmatang='".$lwtmatang."',kgbjr='".$kgBjr."',kgwb='".$kgwb."'\r\n                                                                   where nospb='".$noSpb."' and blok='".$oldBlok."'";
                if (mysql_query($sUpDetail)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            $kgBjr = $jjngHsl * $bjrHsl;
            $sDetIns = 'insert into '.$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr) values ('".$noSpb."','".$blok."','".$jjngHsl."','".$bjrHsl."','".$brondolanHsl."','".$mentah."','".$busuk."','".$matang."','".$lwtmatang."','".$kgBjr."')";
            if (mysql_query($sDetIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        break;
    case 'getDivData':
        if ('' === $idDiv) {
            $optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
            $sORg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi LIKE '%".$kdOrg."%'";
            $qOrg = mysql_query($sORg) ;
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
            }
            echo $optOrg;
        } else {
            $sORg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi LIKE '%".$kdOrg."%'";
            $qOrg = mysql_query($sORg) ;
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                $optOrg .= '<option value='.$rOrg['kodeorganisasi'].' '.(($rOrg['kodeorganisasi'] === $idDiv ? 'selected' : '')).'>'.$rOrg['namaorganisasi'].'</option>';
            }
            echo $optOrg;
        }

        break;
    case 'addSession':
        $_SESSION['temp']['nSpb'] = $noSpb;
        echo 'warning:'.$_SESSION['temp']['nSpb'];
        exit();
    case 'delDetail':
        $sqlDet = 'delete from '.$dbname.".kebun_spbdt where nospb='".$noSpb."' and blok='".$blok."'";
        if (mysql_query($sqlDet)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    default:
        break;
}

?>
