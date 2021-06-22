<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$str = 'select a.*, b.*,c.namakaryawan,c.kodegolongan,c.bagian,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a left join\r\n  ".$dbname.".sdm_5rs b on a.rs=b.id \r\n  left join ".$dbname.".datakaryawan c\r\n  on a.karyawanid=c.karyawanid\r\n  left join ".$dbname.".sdm_5diagnosa d\r\n  on a.diagnosa=d.id\r\n  where a.notransaksi='".$notransaksi."'\r\n  order by a.updatetime desc, a.tanggal desc";
$stream = '';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    $periode = substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4);
    $tanggal = tanggalnormal($bar->tanggal);
    $karyawanid = $bar->karyawanid;
    $namakaryawan = $bar->namakaryawan;
    $doagnosa = $bar->ketdiag;
    $namars = $bar->namars.'['.$bar->kota.']';
    $jenisbiaya = $bar->kodebiaya;
    $keterangan = $bar->keterangan;
    $totalbayar = $bar->jlhbayar;
    $totalklaim = $bar->totalklaim;
    $tahunplafon = $bar->tahunplafon;
    $bagian = $bar->bagian;
    $tanggalbayar = tanggalnormal($bar->tanggalbayar);
    $golongan = $bar->kodegolongan;
    $jasars = $bar->jasars;
    $jasadr = $bar->jasadr;
    $jasalab = $bar->jasalab;
    $byobat = $bar->byobat;
    $bypendaftaran = $bar->bypendaftaran;
    $bytransport = $bar->bytransport;
    if (0 == $bar->ygsakit) {
        $ygsakit['namaygsakit'] = $namakaryawan;
    } else {
        $str1 = " select nama,jeniskelamin,hubungankeluarga, \r\n\t\t           ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as umur\r\n\t\t           from ".$dbname.".sdm_karyawankeluarga\r\n\t           where nomor=".$bar->ygsakit;
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $ygsakit['namaygsakit'] = $bar1->nama;
            $ygsakit['jk'] = $bar1->jeniskelamin;
            $ygsakit['hubungankeluarga'] = $bar1->hubungankeluarga;
            $ygsakit['umur'] = $bar1->umur;
        }
    }
}
echo '<fieldset><legend>'.$_SESSION['lang']['karyawan']."<legend>\r\n       <table class=sortable cellspacing=1 borde=0>\r\n\t   <thead></thead>\r\n\t   <tbody>\r\n       <tr class=rowcontent><td>".$_SESSION['lang']['notransaksi'].'</td><td>'.$notransaksi."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['tanggal'].'</td><td>'.$tanggal."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['thnplafon'].'</td><td>'.$tahunplafon."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['periode'].'</td><td>'.$periode."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['namakaryawan'].'</td><td>'.$namakaryawan."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['bagian'].'</td><td>'.$bagian."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['keterangan'].'</td><td>'.$keterangan."</td></tr>\r\n       </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t   </fieldset>\r\n\t   <fieldset><legend>".$_SESSION['lang']['pasien']."<legend>\r\n       <table class=sortable cellspacing=1 borde=0>\r\n\t   <thead></thead>\r\n\t   <tbody>\t   \r\n       <tr class=rowcontent><td>".$_SESSION['lang']['jenisbiayapengobatan'].'</td><td>'.$jenisbiaya."</td></tr>\t   \r\n       <tr class=rowcontent><td>".$_SESSION['lang']['namapasien'].'</td><td>'.$ygsakit['namaygsakit']."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['jeniskelamin'].'</td><td>'.$ygsakit['jk']."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['umur'].'</td><td>'.$ygsakit['umur'].' '.$_SESSION['lang']['tahun']."</td></tr>\t   \r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['hubungan'].'</td><td>'.$ygsakit['hubungankeluarga']."</td></tr>\r\n       </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n       </table>\r\n\t   </fieldset>\r\n\t   \r\n\t   <fieldset><legend>".$namars."<legend>\r\n       <table class=sortable cellspacing=1 borde=0>\t\r\n\t   <thead></thead>\r\n\t   <tbody>\t      \r\n       <tr class=rowcontent><td>".$_SESSION['lang']['biayaadministrasi'].'</td><td align=right>'.number_format($bypendaftaran, 2, '.', ',')."</td></tr>\r\n           <tr class=rowcontent><td>".$_SESSION['lang']['biayatransport'].'</td><td align=right>'.number_format($bytransport, 2, '.', ',')."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['jasars'].'</td><td align=right>'.number_format($jasars, 2, '.', ',')."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['biayadr'].'</td><td align=right>'.number_format($jasadr, 2, '.', ',')."</td></tr>\t   \r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['biayalab'].'</td><td align=right>'.number_format($jasalab, 2, '.', ',')."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['biayaobat'].'</td><td align=right>'.number_format($byobat, 2, '.', ',')."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['nilaiklaim'].'</td><td align=right>'.number_format($totalklaim, 2, '.', ',')."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['dibayar'].'</td><td align=right>'.number_format($totalbayar, 2, '.', ',')."</td></tr>\r\n\t   <tr class=rowcontent><td>".$_SESSION['lang']['tanggal'].'</td><td>'.$tanggalbayar."</td></tr>\r\n       </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n       </table>\r\n\t   </fieldset>\r\n\t   ";
$str = 'select value from '.$dbname.'.sdm_ho_basicsalary where component=1 and  karyawanid='.$karyawanid;
$res = mysql_query($str);
$gp = 0;
while ($bar = mysql_fetch_object($res)) {
    $gp = $bar->value;
}
$str = 'select a.kode,b.persen from '.$dbname.".sdm_5jenisbiayapengobatan a\r\n\t      left join ".$dbname.".sdm_pengobatanplafond b\r\n\t\t  on a.kode=b.kodejenisbiaya\r\n\t      where b.kodegolongan='".$golongan."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $plaf[$bar->kode] = ($bar->persen * $gp) / 100;
}
$str = 'select sum(jlhbayar) as jlhbayar,kodebiaya from '.$dbname.".sdm_pengobatanht\r\n\t      where karyawanid=".$karyawanid.' and tahunplafon='.$tahunplafon."\r\n\t\t  group by kodebiaya";
$res = mysql_query($str);
echo mysql_error($conn);
echo '<fieldset><legend>'.$_SESSION['lang']['plafon']."</legend>\r\n\t      <table class=sortable cellspacing=1 borde=0>\t\r\n\t\t   <thead>\r\n\t\t   <tr clas=rowheader>\r\n\t\t    <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['plafon']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['sudahdipakai']."</td>\r\n\t\t   </tr>\r\n\t\t   </thead>\r\n\t\t   <tbody>";
while ($bar = mysql_fetch_object($res)) {
    echo "<tr class=rowcontent>\r\n\t\t    <td>".$golongan."</td>\r\n\t\t\t<td>".$bar->kodebiaya."</td>\r\n\t\t\t<td align=right>".number_format($plaf[$bar->kodebiaya], 2, ',', '.')."</td>\r\n\t\t\t<td align=right>".number_format($bar->jlhbayar, 2, ',', '.')."</td>\r\n\t\t   </tr>";
}
echo "</tbody>\r\n\t\t   <tfoot>\r\n\t\t   </tfoot>\r\n\t       </table>\r\n\t\t  </fieldset>";

?>