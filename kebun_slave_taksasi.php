<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo "\r\n";
$param = $_POST;
$proses = $_POST['proses'];
switch ($proses) {
    case 'loadData':
        $where = 'afdeling in (select distinct kodeorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%')";
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        $tab .= '<td>'.$_SESSION['lang']['afdeling'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['blok'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['section'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hasisa'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['haesok'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jmlhpokok'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' !== $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.' order by tanggal desc limit '.$offset.','.$limit.' ';
        $qdata = mysql_query($sdata) ;
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $tab .= '<tr class=rowcontent align=center>';
            $tab .= '<td>'.$rdata['afdeling'].'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            $tab .= '<td>'.$rdata['blok'].'</td>';
            $tab .= '<td>'.$rdata['seksi'].'</td>';
            $tab .= '<td align=right>'.$rdata['hasisa'].'</td>';
            $tab .= '<td align=right>'.$rdata['haesok'].'</td>';
            $tab .= '<td align=right>'.$rdata['jmlhpokok'].'</td>';
            $tab .= "<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['blok']."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab .= "<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['blok']."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
            $tab .= '</tr>';
        }
        $tab .= '</tbody><tfoot>';
        $tab .= '<tr>';
        $tab .= '<td colspan=10 align=center>';
        $tab .= "<img src=\"images/skyblue/first.png\" onclick='loadData(0)' style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/prev.png\" onclick='loadData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.'';
        $qpage = mysql_query($spage) ;
        $rpage = mysql_num_rows($qpage);
        $tab .= "<select id='pages' style='width:50px' onchange='loadData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' === $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $tab .= "<option value='".$starAwal."' ".(($starAwal === $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $tab .= '</select>';
        $tab .= "<img src=\"images/skyblue/next.png\" onclick='loadData(".($page + 1).")'  style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/last.png\" onclick='loadData(".(int) $totalPage.")'  style='cursor:pointer'>";
        $tab .= '</td></tr></tfoot></table>';
        echo $tab;

        break;
    case 'cariData':
        $where = 'afdeling in (select distinct kodeorganisasi from '.$dbname.".organisasi where tipe='AFDELING')";
        if ('' !== $param['sNoTrans']) {
            $tgl = explode('-', $param['sNoTrans']);
            $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
            $where .= " and tanggal like '%".$param['tanggal']."%'";
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        $tab .= '<td>'.$_SESSION['lang']['afdeling'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['blok'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['section'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hasisa'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['haesok'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jmlhpokok'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' !== $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.' order by tanggal desc limit '.$offset.','.$limit.' ';
        $qdata = mysql_query($sdata) ;
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $tab .= '<tr class=rowcontent align=center>';
            $tab .= '<td>'.$rdata['afdeling'].'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            $tab .= '<td>'.$rdata['blok'].'</td>';
            $tab .= '<td>'.$rdata['seksi'].'</td>';
            $tab .= '<td align=right>'.$rdata['hasisa'].'</td>';
            $tab .= '<td align=right>'.$rdata['haesok'].'</td>';
            $tab .= '<td align=right>'.$rdata['jmlhpokok'].'</td>';
            $tab .= "<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['karyawanid']."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab .= "<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['karyawanid']."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
            $tab .= '</tr>';
        }
        $tab .= '</tbody><tfoot>';
        $tab .= '<tr>';
        $tab .= '<td colspan=10 align=center>';
        $tab .= "<img src=\"images/skyblue/first.png\" onclick='cariData(0)' style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/prev.png\" onclick='cariData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.'';
        $qpage = mysql_query($spage) ;
        $rpage = mysql_num_rows($qpage);
        $tab .= "<select id='pages' style='width:50px' onchange='cariData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' === $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $tab .= "<option value='".$starAwal."' ".(($starAwal === $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $tab .= '</select>';
        $tab .= "<img src=\"images/skyblue/next.png\" onclick='cariData(".($page + 1).")'  style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/last.png\" onclick='cariData(".(int) $totalPage.")'  style='cursor:pointer'>";
        $tab .= '</td></tr></tfoot></table>';
        $cols = 'notransaksi,tanggal,kodeorg,kodetangki,kuantitas,suhu';
        echo $tab;

        break;
    case 'insert':
        ('' === $param['hasisa'] ? $param['hasisa'] : $param['hasisa']);
        ('' === $param['haesok'] ? $param['haesok'] : $param['haesok']);
        ('' === $param['jmlhpokok'] ? $param['jmlhpokok'] : $param['jmlhpokok']);
        ('' === $param['persenbuahmatang'] ? $param['persenbuahmatang'] : $param['persenbuahmatang']);
        ('' === $param['jjgmasak'] ? $param['jjgmasak'] : $param['jjgmasak']);
        ('' === $param['jjgoutput'] ? $param['jjgoutput'] : $param['jjgoutput']);
        ('' === $param['hkdigunakan'] ? $param['hkdigunakan'] : $param['hkdigunakan']);
        ('' === $param['bjr'] ? $param['bjr'] : $param['bjr']);
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $scek2 = 'select distinct * from '.$dbname.".kebun_taksasi where tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."' and blok='".$param['blok']."'";
        $qcek2 = mysql_query($scek2) ;
        $rcek2 = mysql_num_rows($qcek2);
        if (0 !== $rcek2) {
            $sins = 'update '.$dbname.".kebun_taksasi  set `seksi`='".$param['seksi']."',\r\n            `hasisa`='".$param['hasisa']."', `haesok`='".$param['haesok']."', `jmlhpokok`='".$param['jmlhpokok']."', \r\n            `persenbuahmatang`='".$param['persenbuahmatang']."',`jjgmasak`='".$param['jjgmasak']."', `jjgoutput`='".$param['jjgoutput']."', \r\n            `hkdigunakan`='".$param['hkdigunakan']."', `bjr`='".$param['bjr']."'   \r\n             where tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."' and blok='".$param['blok']."'";
            if (!mysql_query($sins)) {
                exit('error:'.mysql_error($conn).'__'.$sins);
            }
        } else {
            $scek = 'select distinct * from '.$dbname.".kebun_taksasi \r\n              where tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."' and blok='".$param['blok']."'";
            $qcek = mysql_query($scek) ;
            $rcek = mysql_num_rows($qcek);
            if (0 !== $rcek) {
                exit('error:Data Sudah Ada');
            }

            $sins = 'insert into '.$dbname.".kebun_taksasi  \r\n            (`afdeling`,`tanggal`, `blok`, `seksi`, `hasisa`, `haesok`, `jmlhpokok`, `persenbuahmatang`, `jjgmasak`, `jjgoutput`, `hkdigunakan`, `bjr`)\r\n            values ('".$param['afdeling']."','".$param['tanggal']."','".$param['blok']."','".$param['seksi']."','".$param['hasisa']."','".$param['haesok']."','".$param['jmlhpokok']."','".$param['persenbuahmatang']."','".$param['jjgmasak']."','".$param['jjgoutput']."','".$param['hkdigunakan']."','".$param['bjr']."')";
            if (!mysql_query($sins)) {
                exit('error:'.mysql_error($conn).'__'.$sins);
            }
        }

        break;
    case 'getData':
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $str = 'select distinct * from '.$dbname.".kebun_taksasi \r\n          where tanggal='".$param['tanggal']."' and \r\n          afdeling='".$param['afdeling']."' and blok ='".$param['blok']."'";
        $qstr = mysql_query($str) ;
        $rts = mysql_fetch_assoc($qstr);
        echo $rts['afdeling'].'###'.tanggalnormal($rts['tanggal']).'###'.$rts['blok'].'###'.$rts['seksi'].'###'.$rts['hasisa'].'###'.$rts['haesok'].'###'.$rts['jmlhpokok'].'###'.$rts['persenbuahmatang'].'###'.$rts['jjgmasak'].'###'.$rts['jjgoutput'].'###'.$rts['hkdigunakan'].'###'.$rts['bjr'].'###'.$rts['karyawanid'].'###'.substr($rts['afdeling'], 0, 4).'###'.$rts['blok'];

        break;
    case 'delete':
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $where = "tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."'  and blok='".$param['blok']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_taksasi` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    case 'getAfd':
        $bloklama = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama');
        $optafd = '';
        $sorg = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and induk='".$param['kebun']."'";
        $qorg = mysql_query($sorg) ;
        while ($rorg = mysql_fetch_assoc($qorg)) {
            if (!empty($param['afdeling'])) {
                $optafd .= "<option value='".$rorg['kodeorganisasi']."' ".(($param['afdeling'] === $rorg['kodeorganisasi'] ? 'selected' : '')).'>'.$rorg['namaorganisasi'].'</option>';
            } else {
                $optafd .= "<option value='".$rorg['kodeorganisasi']."'>".$rorg['namaorganisasi'].'</option>';
            }
        }
        echo $optafd;

        break;
    case 'getBlok':
        $bloklama = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama');
        $sorg2 = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n\t\t\t\twhere tipe='BLOK' and kodeorganisasi like '".$param['kebun']."%' \r\n\t\t\t\tand kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,4)='".$param['kebun']."' and luasareaproduktif!=0)";
        $qorg2 = mysql_query($sorg2) ;
        $optafd2 = '';
        for ($i = 0; $rorg2 = mysql_fetch_assoc($qorg2); ++$i) {
            if (!empty($param['blok'])) {
                $optafd2 .= "<option value='".$rorg2['kodeorganisasi']."' ".(($param['blok'] === $rorg2['kodeorganisasi'] ? 'selected' : '')).'>'.$rorg2['namaorganisasi'].' ['.$bloklama[$rorg2['kodeorganisasi']].']</option>';
            } else {
                $optafd2 .= "<option value='".$rorg2['kodeorganisasi']."'>".$rorg2['namaorganisasi'].' ['.$bloklama[$rorg2['kodeorganisasi']].']</option>';
            }

            if (0 === $i) {
                $firstBlok = $rorg2['kodeorganisasi'];
            }
        }
        if (!empty($param['blok'])) {
            $qBlok = selectQuery($dbname, 'setup_blok', 'jumlahpokok,luasareaproduktif', "kodeorg='".$param['blok']."'");
        } else {
            $qBlok = selectQuery($dbname, 'setup_blok', 'jumlahpokok,luasareaproduktif', "kodeorg='".$firstBlok."'");
        }

        $resBlok = fetchData($qBlok);
        if (empty($resBlok)) {
            $sph = '0.00';
        } else {
            $sph = number_format($resBlok[0]['jumlahpokok'] / $resBlok[0]['luasareaproduktif'], 2);
        }

        echo $optafd2.'####'.$sph;

        break;
    case 'getSph':
        $qBlok = selectQuery($dbname, 'setup_blok', 'jumlahpokok,luasareaproduktif', "kodeorg='".$param['blok']."'");
        $resBlok = fetchData($qBlok);
        if (empty($resBlok)) {
            $sph = '0.00';
        } else {
            $sph = number_format($resBlok[0]['jumlahpokok'] / $resBlok[0]['luasareaproduktif'], 2);
        }

        if ('' !== $param['tanggal']) {
            $tgl = explode('-', $param['tanggal']);
            $param['tanggal'] = $tgl[2];
        }

        $qBjr = selectQuery($dbname, 'kebun_5bjr', 'bjr', "kodeorg='".$param['blok']."' and tahunproduksi='".$param['tanggal']."'");
        $resBjr = fetchData($qBjr);
        if (empty($resBjr)) {
            $bjr = '0.00';
        } else {
            $bjr = number_format($resBjr[0]['bjr'], 2);
        }

        echo $sph.'####'.$bjr;

        break;
}

?>