<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
$karyawanid = $_POST['karyawanid'];
$str = 'select a.namaorganisasi from '.$dbname.'.datakaryawan b left join '.$dbname.".organisasi a \r\n          on b.kodeorganisasi=a.kodeorganisasi where b.karyawanid=".$karyawanid;
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = $bar->namaorganisasi;
}
$str = "select *,\r\n      case jeniskelamin when 'L' then 'Laki-Laki'\r\n          else  'Wanita'\r\n          end as jk\r\n          from ".$dbname.'.datakaryawan where karyawanid='.$karyawanid.' limit 1';
$res = mysql_query($str);
$defaulsrc = 'images/user.png';
echo "<div style='width:100%;height:100%;overflow:scroll;'>\r\n     <fieldset><legend>".$_SESSION['lang']['datapribadi']."</legend>\r\n     <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>";
while ($bar = mysql_fetch_object($res)) {
    $pendidikan = '';
    $str1 = 'select kelompok from '.$dbname.'.sdm_5pendidikan where levelpendidikan='.$bar->levelpendidikan;
    $res1 = mysql_query($str1);
    while ($bar1 = mysql_fetch_object($res1)) {
        $pendidikan = $bar1->kelompok;
    }
    $tipekaryawan = '';
    $str2 = 'select * from '.$dbname.'.sdm_5tipekaryawan where id='.$bar->tipekaryawan;
    $res2 = mysql_query($str2);
    while ($bar2 = mysql_fetch_object($res2)) {
        $tipekaryawan = $bar2->tipe;
    }
    $jabatan = '';
    $str3 = 'select * from '.$dbname.'.sdm_5jabatan where kodejabatan='.$bar->kodejabatan." and namajabatan not like '%available' order by kodejabatan";
    $res3 = mysql_query($str3);
    while ($bar3 = mysql_fetch_object($res3)) {
        $jabatan = $bar->kodejabatan;
    }
    $jabatanku = $bar->kodejabatan;
    echo "<tr>\r\n                 <td colspan=4 align=center>\r\n                           <img src='".(('' == $bar->photo ? $defaulsrc : $bar->photo))."' style='height:120px;'>\r\n                         </td>\r\n                 </tr>\r\n             <tr class=rowcontent>\r\n                <td align=right width=80px>".$_SESSION['lang']['uniqueid'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->karyawanid."</b></td>\r\n                        <td align=right width=80px>".$_SESSION['lang']['nik'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->nik."</b></td>\r\n                 </tr>\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['nama'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->namakaryawan."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['tempatlahir'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->tempatlahir."</b></td>\r\n                 </tr>\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['tanggallahir'].'</td><td align=left bgcolor=#EDEDED><b>'.tanggalnormal($bar->tanggallahir)."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['warganegara'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->warganegara."</b></td>\r\n                 </tr>\t\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['jeniskelamin'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->jk."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['statusperkawinan'].'</td align=left><td bgcolor=#EDEDED><b>'.$bar->statusperkawinan."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['tanggalmenikah'].'</td><td align=left bgcolor=#EDEDED><b>'.tanggalnormal($bar->tanggalmenikah)."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['agama'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->agama."</b></td>\r\n                 </tr>\t\t \t\t  \r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['golongandarah'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->golongandarah."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['pendidikan'].'</td><td align=left bgcolor=#EDEDED><b>'.$pendidikan."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right valign=top>".$_SESSION['lang']['alamataktif'].'</td><td align=left bgcolor=#EDEDED valign=top>'.$bar->alamataktif."</td>\r\n                        <td align=right valign=top>".$_SESSION['lang']['kota'].'</td><td align=left bgcolor=#EDEDED valign=top><b>'.$bar->kota."</b></td>\r\n                 </tr>\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['province'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->provinsi."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['kecamatan'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->kecamatan."</b></td>\r\n                 </tr>\r\n\t\t\t\t <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['desa'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->desa."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['kodepos'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->kodepos."</b></td>\r\n                 </tr>\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['telp'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->noteleponrumah."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['nohp'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->nohp."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['norekeningbank'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->norekeningbank."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['namabank'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->namabank."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['sistemgaji'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->sistemgaji."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['nopaspor'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->nopaspor."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['noktp'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->noktp."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['notelepondarurat'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->notelepondarurat."</b></td>\r\n                 </tr>\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['tanggalmasuk'].'</td><td align=left bgcolor=#EDEDED><b>'.tanggalnormal($bar->tanggalmasuk)."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['tanggalkeluar'].'</td><td align=left bgcolor=#EDEDED><b>'.tanggalnormal($bar->tanggalkeluar)."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['tipekaryawan'].'</td><td align=left bgcolor=#EDEDED><b>'.$tipekaryawan."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['jumlahanak'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->jumlahanak."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['tanggungan'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->jumlahtanggungan."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['statuspajak'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->statuspajak."</b></td>\r\n                 </tr>\t\t \r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['npwp'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->npwp."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['lokasipenerimaan'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->lokasipenerimaan."</b></td>\r\n                 </tr>\t\r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['kodeorganisasi'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->kodeorganisasi."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['bagian'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->bagian."</b></td>\r\n                 </tr>\t\t\t \t\t \t\t \t \t\t\t \t \t\t \t \r\n                 <tr class=rowcontent>\r\n                <td align=right>".$_SESSION['lang']['functionname'].'</td><td align=left bgcolor=#EDEDED><b>'.$jabatan."</b></td>\r\n                        <td align=right>".$_SESSION['lang']['kodegolongan'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->kodegolongan."</b></td>\r\n                 </tr>\r\n\t\t\t\t <tr class=rowcontent>\r\n\t\t\t\t\t<td align=right>".$_SESSION['lang']['pangkat'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->pangkat."</b></td>\r\n\t\t\t\t\t<td align=right>".$_SESSION['lang']['lokasitugas'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->lokasitugas."</b></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td align=right>".$_SESSION['lang']['email'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->email."</b></td>\r\n                <td align=right>".$_SESSION['lang']['subbagian'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->subbagian."</b></td>\r\n\t\t\t\t</tr>\r\n                 <tr class=rowcontent>\r\n                        <td align=right>".$_SESSION['lang']['jms'].'</td><td align=left bgcolor=#EDEDED><b>'.$bar->jms."</b></td>\r\n\t\t\t\t\t\t<td></td><td align=left bgcolor=#EDEDED><b></td>\r\n                 </tr>\r\n";
}
echo "</table>\r\n     </fieldset>\r\n         <fieldset><legend>".$_SESSION['lang']['pengalamankerja']."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n                        <tr class=rowheader>\r\n                          <td>No.</td>\r\n                          <td>".$_SESSION['lang']['orgname']."</td>\r\n                          <td>".$_SESSION['lang']['bidangusaha']."</td>\r\n                          <td>".$_SESSION['lang']['bulanmasuk']."</td>\r\n                          <td>".$_SESSION['lang']['bulankeluar']."</td>\r\n                          <td>".$_SESSION['lang']['jabatanterakhir']."</td>\r\n                          <td>".$_SESSION['lang']['bagian']."</td>\r\n                          <td>".$_SESSION['lang']['masakerja']."</td>\r\n                          <td>".$_SESSION['lang']['alamat']."</td>\t\r\n                        </tr>\t \r\n         ";
$str = 'select *,right(bulanmasuk,4) as masup,left(bulanmasuk,2) as busup from '.$dbname.'.sdm_karyawancv where karyawanid='.$karyawanid.' order by masup,busup';
$res = mysql_query($str);
$no = 0;
$mskerja = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $msk = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulanmasuk), 0, 2), 1, substr($bar->bulanmasuk, 3, 4));
    $klr = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulankeluar), 0, 2), 1, substr($bar->bulankeluar, 3, 4));
    $dateDiff = $klr - $msk;
    $mskerja = floor($dateDiff / (60 * 60 * 24)) / 365;
    echo "\t  <tr class=rowcontent>\r\n                          <td>".$no."</td>\r\n                          <td>".$bar->namaperusahaan."</td>\r\n                          <td>".$bar->bidangusaha."</td>\r\n                          <td>".$bar->bulanmasuk."</td>\r\n                          <td>".$bar->bulankeluar."</td>\r\n                          <td>".$bar->jabatan."</td>\r\n                          <td>".$bar->bagian."</td>\r\n                          <td>".number_format($mskerja, 2, ',', '.')." Yrs.</td>\r\n                          <td>".$bar->alamatperusahaan."</td>\t\r\n                        </tr>";
}
echo "</table>\r\n     </fieldset>\r\n         <fieldset><legend>".$_SESSION['lang']['pendidikan']."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['edulevel']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['namasekolah']."</td>\r\n          <td>".$_SESSION['lang']['kota']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['jurusan']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['tahunlulus']."</td>\r\n          <td>".$_SESSION['lang']['gelar']."</td>\r\n          <td>".$_SESSION['lang']['nilai']."</td>\r\n          <td>".$_SESSION['lang']['keterangan']."</td>\t\r\n         </tr>\r\n         ";
$str = 'select a.*,b.kelompok from '.$dbname.'.sdm_karyawanpendidikan a,'.$dbname.".sdm_5pendidikan b\r\n                        where a.karyawanid=".$karyawanid." \r\n                        and a.levelpendidikan=b.levelpendidikan\r\n                        order by a.levelpendidikan desc";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                          <td>".$no."</td>\r\n                          <td>".$bar->kelompok."</td>\t\t\t  \r\n                          <td>".$bar->namasekolah."</td>\r\n                          <td>".$bar->kota."</td>\t\t\t  \r\n                          <td>".$bar->spesialisasi."</td>\t\t\t  \r\n                          <td>".$bar->tahunlulus."</td>\r\n                          <td>".$bar->gelar."</td>\r\n                          <td>".$bar->nilai."</td>\r\n                          <td>".$bar->keterangan."</td>\r\n                        </tr>";
}
echo "</table>\r\n         </fieldset>\r\n         <fieldset><legend>EXTERNAL ".$_SESSION['lang']['kursus']."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['jeniskursus']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['legend']."</td>\r\n          <td>".$_SESSION['lang']['penyelenggara']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['bulanmasuk']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['bulankeluar']."</td>\r\n          <td>".$_SESSION['lang']['sertifikat']."</td>\r\n         </tr>\r\n     ";
$str = "select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat \r\n               from ".$dbname.".sdm_karyawantraining\r\n                        where karyawanid=".$karyawanid." \r\n                        order by bulanmulai desc";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                          <td class=firsttd>".$no."</td>\r\n                          <td>".$bar->jenistraining."</td>\t\t\t  \r\n                          <td>".$bar->judultraining."</td>\r\n                          <td>".$bar->penyelenggara."</td>\t\t\t  \r\n                          <td>".$bar->bulanmulai."</td>\t\t\t  \r\n                          <td>".$bar->bulanselesai."</td>\r\n                          <td>".$bar->bersertifikat."</td>\r\n                        </tr>";
}
echo "</table>\r\n     </fieldset>\r\n         <fieldset><legend>".$_SESSION['lang']['keluarga']."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['nama']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['jeniskelamin']."</td>\r\n          <td>".$_SESSION['lang']['hubungan']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['tanggallahir']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['statusperkawinan']."</td>\r\n                  <td>".$_SESSION['lang']['umur']."</td>\r\n          <td>".$_SESSION['lang']['edulevel']."</td>\r\n          <td>".$_SESSION['lang']['pekerjaan']."</td>\r\n          <td>".$_SESSION['lang']['telp']."</td>\r\n          <td>".$_SESSION['lang']['email']."</td>\r\n          <td>".$_SESSION['lang']['tanggungan']."</td>\r\n         </tr>\r\n         ";
$str = "select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, \r\n                       b.kelompok,COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',a.tanggallahir)/365.25,1),0) as umur\r\n                           from ".$dbname.'.sdm_karyawankeluarga a,'.$dbname.".sdm_5pendidikan b\r\n                                where a.karyawanid=".$karyawanid." \r\n                                and a.levelpendidikan=b.levelpendidikan\r\n                                order by hubungankeluarga";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    if ('EN' == $_SESSION['language']) {
        switch ($bar->hubungankeluarga) {
            case 'Pasangan':
                $val = 'Couple';

                break;
            case 'Anak':
                $val = 'Child';

                break;
            case 'Ibu':
                $val = 'Mother';

                break;
            case 'Bapak':
                $val = 'Father';

                break;
            case 'Adik':
                $val = 'Younger brother/sister';

                break;
            case 'Kakak':
                $val = 'Older brother/sister';

                break;
            case 'Ibu Mertua':
                $val = 'Monther-in-law';

                break;
            case 'Bapak Mertua':
                $val = 'Father-in-law';

                break;
            case 'Sepupu':
                $val = 'Cousin';

                break;
            case 'Ponakan':
                $val = 'Nephew';

                break;
            default:
                $val = 'Foster child';

                break;
        }
    }

    if ('EN' == $_SESSION['language'] && 'Kawin' == $bar->status) {
        $gal = 'Married';
    }

    if ('EN' == $_SESSION['language'] && ('Bujang' == $bar->status || 'Lajang' == $bar->status)) {
        $gal = 'Single';
    }

    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td>".$no."</td>\r\n                                  <td>".$bar->nama."</td>\t\t\t  \r\n                                  <td>".$bar->jeniskelamin."</td>\r\n                                  <td>".$val."</td>\t\t\t  \r\n                                  <td>".$bar->tempatlahir.','.tanggalnormal($bar->tanggallahir)."</td>\t\t\t  \r\n                                  <td>".$gal."</td>\r\n                                  <td>".$bar->umur." Yrs</td>\r\n                                  <td>".$bar->kelompok."</td>\r\n                                  <td>".$bar->pekerjaan."</td>\r\n                                  <td>".$bar->telp."</td>\r\n                                  <td>".$bar->email."</td>\r\n                                  <td>".$bar->tanggungan1."</td>\r\n                                </tr>";
}
echo "</table>\r\n     </fieldset>\r\n         <fieldset><legend>".$_SESSION['lang']['alamat']."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['alamat']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['kota']."</td>\r\n          <td>".$_SESSION['lang']['province']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['kodepos']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['emplasmen']."</td>\r\n          <td>".$_SESSION['lang']['status']."</td>\r\n         </tr>\r\n         ";
$str = "select *,case aktif when 1 then 'Yes' when 0 then 'No' end as status from ".$dbname.'.sdm_karyawanalamat where karyawanid='.$karyawanid.' order by nomor desc';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$no."</td>\r\n                                  <td>".$bar->alamat."</td>\t\t\t  \r\n                                  <td>".$bar->kota."</td>\r\n                                  <td>".$bar->provinsi."</td>\t\t\t  \r\n                                  <td>".$bar->kodepos."</td>\t\t\t  \r\n                                  <td>".$bar->emplasemen."</td>\r\n                                  <td>".$bar->status."</td>\r\n                                </tr>";
}
if ('EN' == $_SESSION['language']) {
    $gg = 'History of reprimands';
} else {
    $gg = 'Riwayat Teguran dan SP';
}

echo "</table>\r\n         </fieldset>\r\n         <fieldset><legend>".$gg."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['jenissp']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n          <td>".$_SESSION['lang']['masaberlaku'].' ('.$_SESSION['lang']['bulan'].")</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['pelanggaran']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['penandatangan']."</td>\r\n          <td>".$_SESSION['lang']['functionname']."</td>\r\n         </tr>\r\n         ";
$str = 'select * from '.$dbname.'.sdm_suratperingatan where karyawanid='.$karyawanid.' order by tanggal desc';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$bar->nomor."</td>\r\n                                  <td>".$bar->jenissp."</td>\t\t\t  \r\n                                  <td>".tanggalnormal($bar->tanggal)."</td>\r\n                                  <td align=right>".$bar->masaberlaku."</td>\t\t\t  \r\n                                  <td>".$bar->pelanggaran."</td>\t\t\t  \r\n                                  <td>".$bar->penandatangan."</td>\r\n                                  <td>".$bar->jabatan."</td>\r\n                                </tr>";
}
$str = 'select * from '.$dbname.'.sdm_5jabatan';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    $kamusjabatan[$bar->kodejabatan] = $bar->namajabatan;
}
if ('EN' == $_SESSION['language']) {
    $gg = 'Promotion history';
} else {
    $gg = 'Riwayat Mutasi/Promosi/Demosi';
}

echo "</table>\r\n         </fieldset>\r\n         <fieldset><legend>".$gg."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['tipetransaksi']."</td>\r\n          <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n          <td>".$_SESSION['lang']['tanggalberlaku']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['dari']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['ke']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['penandatangan']."</td>\r\n         </tr>\r\n         ";
$str = 'select * from '.$dbname.'.sdm_riwayatjabatan where karyawanid='.$karyawanid.' order by tanggalsk desc';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$bar->nomorsk."</td>\r\n                                  <td>".$bar->tipesk."</td>\t\t\t  \r\n                                  <td>".tanggalnormal($bar->tanggalsk)."</td>\r\n                                  <td>".tanggalnormal($bar->mulaiberlaku).'</td>';
    if ('Mutasi' == $bar->tipesk) {
        echo '<td>'.$bar->darikodeorg."</td>\t\t\t  \r\n                                      <td>".$bar->kekodeorg.'</td>';
    } else {
        if ('Promosi' == $bar->tipesk) {
            echo '<td>'.$kamusjabatan[$bar->darikodejabatan].' ('.$bar->darikodegolongan.")</td>\t\t\t  \r\n                                      <td>".$kamusjabatan[$bar->kekodejabatan].' ('.$bar->kekodegolongan.') </td>';
        } else {
            if ('Demosi' == $bar->tipesk) {
                echo '<td>'.$kamusjabatan[$bar->darikodejabatan].' ('.$bar->darikodegolongan.")</td>\t\t\t  \r\n                                      <td>".$kamusjabatan[$bar->kekodejabatan].' ('.$bar->kekodegolongan.') </td>';
            }
        }
    }

    echo '<td>'.$bar->namadireksi."</td>\r\n                                </tr>";
}
echo "</table>\r\n         </fieldset>";
$sJabat = 'select * from '.$dbname.'.sdm_5matriktraining where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusKategori[$rJabat['matrixid']] = $rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']] = $rJabat['topik'];
}
if ('EN' == $_SESSION['language']) {
    $gg = 'Training provided By '.$namapt;
} else {
    $gg = 'Training di '.$namapt;
}

echo '<fieldset><legend>'.$gg."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>".$_SESSION['lang']['kategori']."</td>\r\n          <td>".$_SESSION['lang']['topik']."</td>\r\n          <td>".$_SESSION['lang']['tanggalmulai']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['tanggalsampai']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['catatan']."</td>\t\t\t  \r\n         </tr>\r\n         ";
$str = 'select * from '.$dbname.".sdm_matriktraining\r\n        where karyawanid = '".$karyawanid."'\r\n        ";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$kamusKategori[$bar->matrikxid]."</td>\r\n                                  <td>".$kamusTopik[$bar->matrikxid]."</td>\t\t\t  \r\n                                  <td>".tanggalnormal($bar->tanggaltraining)."</td>\r\n                                  <td>".tanggalnormal($bar->sampaitanggal)."</td>\r\n                                  <td>".$bar->catatan."</td>\r\n                                </tr>";
}
echo "</table>\r\n         </fieldset>";
if ('EN' == $_SESSION['language']) {
    $gg = 'Standard Training';
} else {
    $gg = 'Standard training yang harus diikuti';
}

echo '<fieldset><legend>'.$gg."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>".$_SESSION['lang']['jabatan']."</td>\r\n          <td>".$_SESSION['lang']['kategori']."</td>\r\n          <td>".$_SESSION['lang']['topik']."</td>\t\t\t  \r\n         </tr>\r\n         ";
$str = 'select * from '.$dbname.".sdm_5matriktraining\r\n        where kodejabatan = '".$jabatanku."'\r\n        ";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$kamusjabatan[$bar->kodejabatan]."</td>\r\n                                  <td>".$bar->kategori."</td>\t\t\t  \r\n                                  <td>".$bar->topik."</td>\r\n                                </tr>";
}
echo "</table>\r\n         </fieldset>";
$sJabat = 'select * from '.$dbname.'.log_5supplier where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusSup[$rJabat['supplierid']] = $rJabat['namasupplier'];
}
if ('EN' == $_SESSION['language']) {
    $gg = 'Additional Training';
} else {
    $gg = 'Additional training yang sudah diikuti';
}

echo '<fieldset><legend>'.$gg."</legend>\r\n         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>\r\n         <tr class=rowheader>\r\n          <td>".$_SESSION['lang']['namatraining']."</td>\r\n          <td>".$_SESSION['lang']['penyelenggara']."</td>\r\n          <td>".$_SESSION['lang']['tanggalmulai']."</td>\t\t\t  \r\n          <td>".$_SESSION['lang']['tanggalsampai']."</td>\t\t\t  \r\n         </tr>\r\n         ";
$str = 'select * from '.$dbname.".sdm_5training\r\n        where karyawanid = '".$karyawanid."' and sthrd = '1'\r\n        ";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "\t  <tr class=rowcontent>\r\n                                  <td class=firsttd>".$bar->namatraining."</td>\r\n                                  <td>".$kamusSup[$bar->penyelenggara]."</td>\t\t\t  \r\n                                  <td>".tanggalnormal($bar->tglmulai)."</td>\r\n                                  <td>".tanggalnormal($bar->tglselesai)."</td>\r\n                                </tr>";
}
echo "</table>\r\n         </fieldset></div>";

?>