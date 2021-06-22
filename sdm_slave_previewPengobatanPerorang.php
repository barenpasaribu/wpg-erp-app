<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$str = 'select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, e.lokasitugas as loktug,nama from '.$dbname.".sdm_pengobatanht a left join\r\n      ".$dbname.".sdm_5rs b on a.rs=b.id \r\n\t  left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid\r\n\t  left join ".$dbname.".sdm_5diagnosa d\r\n\t  on a.diagnosa=d.id\r\n          left join ".$dbname.".datakaryawan e\r\n\t  on a.karyawanid=e.karyawanid\r\n        left join ".$dbname.".sdm_karyawankeluarga f\r\n        on a.ygsakit=f.nomor\r\n\t  where a.periode like '".$_POST['tahun']."%'\r\n\t  and a.karyawanid = ".$_POST['karyawanid']."\r\n          order by a.updatetime desc, a.tanggal desc";
$res = mysql_query($str);
$tab = "<table class=sortable cellspacing=1 border=0 width=1200px>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>No</td>\r\n        <td width=30>".$_SESSION['lang']['tanggal']."</td>\r\n        <td width=200>".$_SESSION['lang']['jenis']."</td>            \r\n        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>\r\n        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td>".$_SESSION['lang']['diagnosa']."</td>\r\n         <td>Obat/Drugs</td>           \r\n    </tr>\r\n    </thead>\r\n    \r\n    <tbody id='container'>";
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pasien = '';
    $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n            where nomor=".$bar->ygsakit;
    $resu = mysql_query($stru);
    while ($baru = mysql_fetch_object($resu)) {
        $pasien = $baru->hubungankeluarga;
    }
    $str2 = 'select namaobat,jenis from '.$dbname.".sdm_pengobatandt where notransaksi='".$bar->notransaksi."'";
    $resxx = mysql_query($str2);
    while ($barxx = mysql_fetch_object($resxx)) {
        $obat .= $barxx->namaobat.' ['.$barxx->jenis.']';
    }
    if ('' == $pasien) {
        $pasien = 'AsIs';
    }

    $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".tanggalnormal($bar->tanggal)."</td>\r\n            <td>".$bar->kodebiaya."</td>\r\n            <td>".$bar->namakaryawan."</td>\r\n            <td>".$pasien."</td>\r\n            <td>".$bar->nama."</td>\r\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n            <td align=right>".number_format($bar->totalklaim, 2, '.', ',')."</td>\r\n            <td>".$bar->ketdiag."</td>\r\n             <td>".$obat."</td>\r\n        </tr>";
}
$tab .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
echo $tab;

?>