<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
if (isset($_GET['txtsearch'])) {
    $txtsearch = $_GET['txtsearch'];
    $orgsearch = $_GET['orgsearch'];
    $tipesearch = $_GET['tipesearch'];
    $statussearch = $_GET['statussearch'];
    $thnmsk = $_GET['thnmsk'];
    $blnmsk = $_GET['blnmsk'];
    $thnkel = $_GET['thnkel'];
    $blnkel = $_GET['blnkel'];
    $schjk = $_GET['schjk'];
    $nik = $_GET['nik'];
} else {
    $txtsearch = '';
    $orgsearch = '';
    $tipesearch = '';
    $statussearch = '';
    $thnmsk = '';
    $blnmsk = '';
    $thnkel = '';
    $blnkel = '';
    $schjk = '';
    $nik = '';
}

$where = '';
if ('' != $txtsearch) {
    $where = " and a.namakaryawan like '%".$txtsearch."%'";
}

if ('' != $orgsearch) {
    $where .= " and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') ";
}

if ('' != $nik) {
    $where .= " and nik like '%".$nik."%'";
}

if ('' != $tipesearch) {
    if (100 == $tipesearch) {
        $where .= ' and a.tipekaryawan!=4 ';
    } else {
        $where .= " and a.tipekaryawan='".$tipesearch."'";
    }
}

if ('' != $thnmsk) {
    $where .= "and left(a.tanggalmasuk,4)='".$thnmsk."'   ";
}

if ('' != $blnmsk) {
    $where .= "and mid(a.tanggalmasuk,6,2)='".$blnmsk."'  ";
}

if ('' != $thnkel) {
    $where .= "and left(a.tanggalkeluar,4)='".$thnkel."'  ";
}

if ('' != $blnkel) {
    $where .= "and mid(a.tanggalkeluar,6,2)='".$blnkel."' ";
}

$hariini = date('Y-m-d');
if ('*' == $statussearch) {
    $where .= " and (a.tanggalkeluar is NULL or a.tanggalkeluar<='".$hariini."')";
} else {
    if ('0000-00-00' == $statussearch) {
        $where .= " and (a.tanggalkeluar is NULL or a.tanggalkeluar>'".$hariini."')";
    }
}

if ('' != $schjk) {
    $where .= " and a.jeniskelamin='".$schjk."'";
}

$listOrg = ambilLokasiTugasDanTurunannya('list', $_SESSION['empl']['lokasitugas']);
$list = str_replace('|', "','", $listOrg);
$list = "'".$list."'";
if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a\r\nleft join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\nleft join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan\r\nleft join ".$dbname.'.sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 '.$where;
    $strd = "select b.*,a.namakaryawan,c.kelompok, case b.status when 1 then 'Y' when 0 then 'T' end as statusx\r\n       from ".$dbname.".sdm_karyawankeluarga b\r\n       left join ".$dbname.".datakaryawan a\r\n\t   on b.karyawanid=a.karyawanid\r\n\t   left join ".$dbname.".sdm_5pendidikan c on b.levelpendidikan=c.levelpendidikan\r\n\t   where 1=1 ".$where;
} else {
    if ('KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
        $str = 'select a.*,b.namajabatan,d.tipe,c.namagolongan from '.$dbname.".datakaryawan a \r\n    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n    left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan\r\n    left join ".$dbname.'.sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 '.$where." \r\n    and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n    order by a.nik asc";
        $strd = "select b.*,a.namakaryawan,c.kelompok, case b.status when 1 then 'Y' when 0 then 'T' end as statusx\r\n       from ".$dbname.".sdm_karyawankeluarga b\r\n       left join ".$dbname.".datakaryawan a\r\n\t   on b.karyawanid=a.karyawanid\r\n\t   left join ".$dbname.".sdm_5pendidikan c on b.levelpendidikan=c.levelpendidikan\r\n\t   where a.lokasitugas in(".$list.') '.$where;
    } else {
        $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a \r\n      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n      left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan\r\n      left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan and a.tipekaryawan!=5 where \r\n      lokasitugas in(".$list.')  '.$where;
        $strd = "select b.*,a.namakaryawan,c.kelompok, case b.status when 1 then 'Y' when 0 then 'T' end as statusx\r\n       from ".$dbname.".sdm_karyawankeluarga b\r\n       left join ".$dbname.".datakaryawan a\r\n\t   on b.karyawanid=a.karyawanid\r\n\t   left join ".$dbname.".sdm_5pendidikan c on b.levelpendidikan=c.levelpendidikan\r\n\t   where a.lokasitugas in(".$list.') '.$where;
    }
}

$stream = '';
$stream .= "\r\n       Daftar karyawan:\r\n\t   <table border=1>\r\n\t   <tr>\r\n\t     <td align=center>No.</td>\r\n \t\t <td align=center>".$_SESSION['lang']['nokaryawan']."</td>\t\t \r\n\t\t <td align=center>".$_SESSION['lang']['nik']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['nama']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['functionname']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['kodegolongan']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['pangkat']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['lokasitugas']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['pt']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['noktp']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['pendidikan']."</td>\r\n\t\t <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['statuspajak'])."</td>\r\n\t\t <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['statusperkawinan'])."</td>\r\n\t\t <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['jumlahanak'])."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>\r\n\t\t <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['tipekaryawan'])."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['tempatlahir']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['tanggallahir']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['warganegara']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['jeniskelamin']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['tanggalmenikah']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['agama']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['golongandarah']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['alamataktif']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['provinsi']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['kota']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['kecamatan']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['desa']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['kodepos']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['noteleponrumah']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['nohp']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['norekeningbank']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['namabank']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['sistemgaji']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['nopaspor']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['notelepondarurat']."</td>\r\n   \t\t <td align=center>".$_SESSION['lang']['tanggalkeluar']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['jumlahtanggungan']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['npwp']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['lokasipenerimaan']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['bagian']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['subbagian']."</td>\r\n                 <td align=center>".$_SESSION['lang']['jms']."</td>    \r\n\t\t <td align=center>".$_SESSION['lang']['email']."</td>\r\n\t     </tr>";
$res = mysql_query($str);
$numrows = mysql_numrows($res);
if ($numrows < 1) {
    $stream .= '<tr><td>NO DATA FOUND</td></tr>';
} else {
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= "<tr>\r\n\t\t     <td>".$no."</td>\r\n\t\t\t <td>'".$bar->karyawanid."</td>\r\n\t\t\t <td>".$bar->nik."</td>\r\n\t\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t\t <td>".$bar->namajabatan."</td>\r\n\t\t\t <td>".$bar->namagolongan."</td>\r\n\t\t\t <td>".$bar->pangkat."</td>\r\n\t\t\t <td>".$bar->lokasitugas."</td>\r\n\t\t\t <td>".$bar->kodeorganisasi."</td>\r\n\t\t\t <td>".$bar->noktp."</td>\r\n\t\t\t <td>".$bar->kelompok."</td>\r\n\t\t\t <td>".$bar->statuspajak."</td>\r\n\t\t\t <td>".$bar->statusperkawinan."</td>\r\n\t\t\t <td align=right >".$bar->jumlahanak."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n\t\t\t <td>".$bar->tipe."</td>\r\n\t\t\t <td>".$bar->tempatlahir."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggallahir)."</td>\r\n\t\t\t <td>".$bar->warganegara."</td>\r\n\t\t\t <td>".$bar->jeniskelamin."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggalmenikah)."</td>\r\n\t\t\t <td>".$bar->agama."</td>\r\n\t\t\t <td>".$bar->golongandarah."</td>\r\n\t\t\t <td>".$bar->alamataktif."</td>\r\n\t\t\t <td>".$bar->provinsi."</td>\r\n\t\t\t <td>".$bar->kota."</td>\r\n\t\t\t <td>".$bar->kecamatan."</td>\r\n\t\t\t <td>".$bar->desa."</td>\r\n\t\t\t <td>".$bar->kodepos."</td>\r\n\t\t\t <td>".$bar->noteleponrumah."</td>\r\n\t\t\t <td>".$bar->nohp."</td>\r\n\t\t\t <td>".$bar->norekeningbank."</td>\r\n\t\t\t <td>".$bar->namabank."</td>\r\n\t\t\t <td>".$bar->sistemgaji."</td>\r\n\t\t\t <td>".$bar->nopaspor."</td>\r\n\t\t\t <td>".$bar->notelepondarurat."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggalkeluar)."</td>\r\n\t\t\t <td>".$bar->jumlahtanggungan."</td>\r\n\t\t\t <td>".$bar->npwp."</td>\r\n\t\t\t <td>".$bar->lokasipenerimaan."</td>\r\n\t\t\t <td>".$bar->bagian."</td>\r\n\t\t\t <td>".$bar->subbagian."</td>\r\n                         <td>".$bar->jms."</td>    \r\n\t\t\t <td>".$bar->email."</td>\t \r\n\t\t  </tr>";
    }
    $stream .= '</table>';
    $stream .= 'KELUARGA';
    $stream .= "<table border=1>\r\n\t   <tr>\r\n\t     <td align=center>No.</td>\r\n \t\t <td align=center>".$_SESSION['lang']['nokaryawan']."</td>\t\t \r\n\t\t <td align=center>".$_SESSION['lang']['nama']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['anggotakeluarga']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['jeniskelamin']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['hubungan']."</td>\r\n\t \t <td align=center>".$_SESSION['lang']['tempatlahir']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['tanggallahir']."</td>\t\t \t\t \r\n\t\t <td align=center>".$_SESSION['lang']['pekerjaan']."</td> \r\n\t\t <td align=center>".$_SESSION['lang']['statusperkawinan']."</td>\t \r\n\t\t <td align=center>".$_SESSION['lang']['pendidikan']."</td>\t\t \r\n\t\t <td align=center>".$_SESSION['lang']['email']."</td>\r\n\t\t <td align=center>".$_SESSION['lang']['telp']."</td>\t \r\n\t\t <td align=center>".$_SESSION['lang']['tanggungan']."</td>\r\n\t     </tr>";
    $res = mysql_query($strd);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= "<table border=1>\r\n\t   <tr>\r\n\t     <td>".$no."</td>\r\n \t\t <td>'".$bar->karyawanid."</td>\t\t \r\n\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t <td>".$bar->nama."</td>\r\n\t\t <td>".$bar->jeniskelamin."</td>\r\n\t\t <td>".$bar->hubungankeluarga."</td>\r\n\t \t <td>".$bar->tempatlahir."</td>\r\n\t\t <td>".tanggalnormal($bar->tanggallahir)."</td>\t\t \t\t \r\n\t\t <td>".$bar->pekerjaan."</td> \r\n\t\t <td>".$bar->status."</td>\t \r\n\t\t <td>".$bar->kelompok."</td>\t\t \r\n\t\t <td>".$bar->email."</td>\r\n\t\t <td>".$bar->telp."</td>\t \r\n\t\t <td>".$bar->statusx."</td>\r\n\t     </tr>";
    }
    $stream .= '</table>';
}

$wktu = date('Hms');
$nop_ = 'DT_Employee_'.$wktu.'__'.date('Y');
if (0 < strlen($stream)) {
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>