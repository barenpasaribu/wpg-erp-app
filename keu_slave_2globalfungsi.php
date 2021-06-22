<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$param = $_POST;
switch ($param['proses']) {
    case 'getPeriodeRev':
        if ('' !== $param['pt']) {
            $wher = 'and kodeorg in (select distinct kodeorganisasi from '.$dbname.".organisasi where induk='".$param['divisi']."')";
        }

        if ('' !== $param['divisi']) {
            $wher = '';
            $wher = "and kodeorg='".$param['divisi']."'";
        }

        if ('' !== $param['revisi']) {
            $wher .= "and revisi='".$param['revisi']."'";
        }

        $sPeriodeRev = 'select distinct periode from '.$dbname.".keu_jurnaldt_vw where kodeorg!='' ".$wher.' order by periode desc';
        $qPeriodeRev = mysql_query($sPeriodeRev);
        $rowcek = mysql_num_rows($qPeriodeRev);
        if (0 === $rowcek || 0 === $param['revisi']) {
            $str = 'select distinct periode as periode from '.$dbname.".setup_periodeakuntansi\r\n                  order by periode desc";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $optPeriode .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
            }
        } else {
            while ($rPeriodeRev = mysql_fetch_assoc($result)) {
                $optPeriode .= "<option value='".$rPeriodeRev['periode']."'>".$rPeriodeRev['periode'].'</option>';
            }
        }

        echo $optPeriode;

        break;
    case 'getKary':
        if ('' === $param['unit']) {
            exit('error:'.$_SESSION['lang']['untukunit'].'/'.$_SESSION['lang']['subunit']." can't empty");
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['nik'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '</tr></thead><tbody>';
        if ('6' === strlen($param['subunit'])) {
            $wher = "subbagian='".$param['subunit']."'";
        } else {
            $wher = "lokasitugas='".$param['unit']."'";
        }

        $sDt = 'select distinct karyawanid,nik,namakaryawan from '.$dbname.'.datakaryawan where '.$wher." and namakaryawan like '".$param['nmkary']."%' \r\n              and tanggalkeluar is NULL order by namakaryawan asc";
        $qDt = mysql_query($sDt);
        while ($rDt = mysql_fetch_assoc($qDt)) {
            $clid = "onclick=setKary('".$rDt['karyawanid']."') style=cursor:pointer;";
            $tab .= '<tr '.$clid.' class=rowcontent><td>'.$rDt['nik'].'</td>';
            $tab .= '<td>'.$rDt['namakaryawan'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'getBlok':
        $tmplPro = 0;
        if ('6' === strlen($param['subunit'])) {
            $induk = $param['subunit'];
            $wher = "induk='".$param['subunit']."' and tipe='BLOK'";
        } else {
            $induk = $param['unit'];
            $wher = "induk='".$param['unit']."' and tipe='AFDELING'";
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodeorganisasi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['bloklama'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namaorganisasi'].'</td>';
        $tab .= '</tr></thead><tbody>';
        if ('AK' === substr($induk, 0, 2) || 'PB' === substr($induk, 0, 2)) {
            $str = 'select distinct kode as kodeorganisasi,nama as namaorganisasi from '.$dbname.".project where kode='".$induk."'";
        } else {
            $str = 'select distinct kodeorganisasi,namaorganisasi,tipe,bloklama from '.$dbname.".organisasi a \r\n                  left join ".$dbname.'.setup_blok b on a.kodeorganisasi=b.kodeorg where '.$wher."\r\n                  and tipe not like '%gudang%' and (bloklama like '%".$param['nmkary']."%' or kodeorganisasi like '%".$param['nmkary']."%'  or namaorganisasi like '%".$param['nmkary']."%')  order by kodeorganisasi";
            $tmplPro = 1;
        }

        $res = mysql_query($str);
        while ($rDt = mysql_fetch_assoc($res)) {
            $clid = "onclick=setBlok('".$rDt['kodeorganisasi']."','BLOK') style=cursor:pointer;";
            $tab .= '<tr '.$clid." class=rowcontent>\r\n                  <td>".$rDt['kodeorganisasi']."</td>\r\n                  <td>".$rDt['bloklama'].'</td>';
            $tab .= '<td>'.$rDt['namaorganisasi'].'</td>';
            $tab .= '</tr>';
        }
        if (1 === $tmplPro) {
            $str = 'select kode as kodeorganisasi,nama as namaorganisasi from '.$dbname.".project where kodeorg='".$induk."' and posting=0";
            $res = mysql_query($str);
            while ($rDt = mysql_fetch_assoc($res)) {
                $clid = "onclick=setBlok('".$rDt['kodeorganisasi']."','BLOK') style=cursor:pointer;";
                $tab .= '<tr '.$clid." class=rowcontent>\r\n                       <td>".$rDt['kodeorganisasi']."</td>\r\n                       <td></td>";
                $tab .= '<td>'.$rDt['namaorganisasi'].'</td>';
                $tab .= '</tr>';
            }
        }

        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'getMesin':
        $optJns = makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc');
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= "<tr>\r\n               <td>".$_SESSION['lang']['kodevhc']."</td>\r\n               <td>".$_SESSION['lang']['jenisvch']."</td>\r\n               <td>".$_SESSION['lang']['detail'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $whr = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n               and tipe='KANWIL'";
        $sOrg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$whr.'';
        $qOrg = mysql_query($sOrg);
        $rOrg = mysql_fetch_assoc($qOrg);
        if ('' !== $param['nmkary']) {
            $isi = " and kodevhc like '%".$param['nmkary']."%'";
        }

        $sVhc = 'select distinct kodevhc,jenisvhc,detailvhc from '.$dbname.".vhc_5master where kodetraksi like '%".$rOrg['kodeorganisasi']."%' ".$isi.'';
        $qVHc = mysql_query($sVhc);
        while ($rDt = mysql_fetch_assoc($qVHc)) {
            $clid = "onclick=setBlok('".$rDt['kodevhc']."','TRAKSI') style=cursor:pointer;";
            $tab .= '<tr '.$clid." class=rowcontent>\r\n                       <td>".$rDt['kodevhc']."</td>\r\n                       <td>".$optJns[$rDt['jenisvhc']]."</td>\r\n                       <td>".$rDt['detailvhc'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'getKeg':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= "<tr>\r\n               <td>".$_SESSION['lang']['kodekgiatan']."</td>\r\n               <td>".$_SESSION['lang']['kelompok']."</td>\r\n               <td>".$_SESSION['lang']['namakegiatan'].'</td>';
        $tab .= '</tr></thead><tbody>';
        if ('' === $param['kdDet']) {
            if (6 === strlen($param['subunit'])) {
                $param['kdDet'] = $param['subunit'];
            } else {
                $param['kdDet'] = $param['unit'];
            }
        }

        $optTipe = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
        if ('' !== $param['nmkary']) {
            $whrdt = "and (namakegiatan like '%".$param['nmkary']."%' or kodekegiatan like  '%".$param['nmkary']."%')";
        }

        echo $param['kdDet'];
        switch ($optTipe[$param['kdDet']]) {
            case 'STENGINE':
            case 'STATION':
                $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n\t\t\t       where kelompok='MIL' ".$whrdt.' order by kelompok,namakegiatan';

                break;
            case 'BLOK':
                $optTpBlok = makeOption($dbname, 'setup_blok', 'kodeorg,statusblok');
                if ('TM' === $optTpBlok[$param['kdDet']]) {
                    $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n                            where (kelompok='TM' or kelompok='PNN')  ".$whrdt.'  order by kelompok,namakegiatan';
                } else {
                    $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n                           where kelompok='".$optTpBlok[$param['kdDet']]."'  ".$whrdt.'  order by kelompok,namakegiatan';
                }

                break;
            case 'WORKSHOP':
                $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n\t\t\t       where kelompok='WSH'  ".$whrdt.'  order by kelompok,namakegiatan';

                break;
            case 'SIPIL':
                $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n\t\t\t       where kelompok='SPL'  ".$whrdt.'  order by kelompok,namakegiatan';

                break;
            case 'BIBITAN':
                $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n\t\t\t       where  kelompok in ('BBT','MN','PN')  ".$whrdt.'  order by kelompok,namakegiatan';

                break;
            default:
                $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan \r\n                                   where kelompok='KNT'  ".$whrdt.'  order by kelompok,namakegiatan';

                break;
        }
}
$resf = mysql_query($strf);
while ($rData = mysql_fetch_assoc($resf)) {
    $clid = "onclick=setDtKeg('".$rData['kodekegiatan']."') style=cursor:pointer;";
    $tab .= '<tr class=rowcontent '.$clid.">\r\n               <td>".$rData['kodekegiatan']."</td>\r\n               <td>".$rData['kelompok']."</td>\r\n               <td>".$rData['namakegiatan'].'</td>';
}
$tab .= '</tbody></table>';
echo $tab;

break;

?>