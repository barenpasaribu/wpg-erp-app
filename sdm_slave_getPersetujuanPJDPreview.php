<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_GET['notransaksi'];
$str = 'select * from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jabatan = '';
    $namakaryawan = '';
    $bagian = '';
    $karyawanid = '';
    $strc = "select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan \r\n\t\t    from ".$dbname.'.datakaryawan a left join  '.$dbname.".sdm_5jabatan b\r\n\t\t\ton a.kodejabatan=b.kodejabatan\r\n\t\t\twhere a.karyawanid=".$bar->karyawanid;
    $resc = mysql_query($strc);
    while ($barc = mysql_fetch_object($resc)) {
        $jabatan = $barc->namajabatan;
        $namakaryawan = $barc->namakaryawan;
        $bagian = $barc->bagian;
        $karyawanid = $barc->karyawanid;
    }
    $kodeorg = $bar->kodeorg;
    $persetujuan = $bar->persetujuan;
    $hrd = $bar->hrd;
    $tujuan3 = $bar->tujuan3;
    $tujuan2 = $bar->tujuan2;
    $tujuan1 = $bar->tujuan1;
    $tanggalperjalanan = tanggalnormal($bar->tanggalperjalanan);
    $tanggalkembali = tanggalnormal($bar->tanggalkembali);
    $uangmuka = $bar->uangmuka;
    $tugas1 = $bar->tugas1;
    $tugas2 = $bar->tugas2;
    $tugas3 = $bar->tugas3;
    $tujuanlain = $bar->tujuanlain;
    $tugaslain = $bar->tugaslain;
    $pesawat = $bar->pesawat;
    $darat = $bar->darat;
    $laut = $bar->laut;
    $mess = $bar->mess;
    $hotel = $bar->hotel;
    $statushrd = $bar->statushrd;
    $xhrd = $bar->statushrd;
    $xper = $bar->statuspersetujuan;
    if (0 == $statushrd) {
        $statushrd = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statushrd) {
            $statushrd = $_SESSION['lang']['disetujui'];
        } else {
            $statushrd = $_SESSION['lang']['ditolak'];
        }
    }

    $statuspersetujuan = $bar->statuspersetujuan;
    if (0 == $statuspersetujuan) {
        $perstatus = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statuspersetujuan) {
            $perstatus = $_SESSION['lang']['disetujui'];
        } else {
            $perstatus = $_SESSION['lang']['ditolak'];
        }
    }

    $perjabatan = '';
    $perbagian = '';
    $pernama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n\t       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n\t\t   where karyawanid=".$persetujuan;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $perjabatan = $barf->namajabatan;
        $perbagian = $barf->bagian;
        $pernama = $barf->namakaryawan;
    }
    $hjabatan = '';
    $hbagian = '';
    $hnama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n\t       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n\t\t   where karyawanid=".$hrd;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $hjabatan = $barf->namajabatan;
        $hbagian = $barf->bagian;
        $hnama = $barf->namakaryawan;
    }
}
echo $_SESSION['lang']['perjalanandinas'].":\r\n      <table class=standard cellspacing=1>\r\n\t <tr class=rowcontent>\r\n\t    <td>".$_SESSION['lang']['nama']."</td>\r\n\t\t<td>".$namakaryawan."</td>\r\n\t </tr>\r\n\t <tr class=rowcontent>\r\n\t    <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$kodeorg."</td>\r\n\t </tr>\t \r\n\t <tr class=rowcontent>\r\n\t    <td>".$_SESSION['lang']['tanggaldinas']."</td>\r\n\t\t<td>".$tanggalperjalanan.". &nbsp \r\n\t\t    ".$_SESSION['lang']['tanggalkembali']." \r\n\t\t\t".$tanggalkembali."\r\n\t\t</td>\r\n\t </tr>\t\r\n\t <tr class=rowcontent>\r\n\t    <td>".$_SESSION['lang']['transportasi'].'/'.$_SESSION['lang']['akomodasi']."</td>\r\n\t\t<td>\r\n\t\t     <input type=checkbox id=pesawat disabled ".((1 == $pesawat ? 'checked' : '')).'> '.$_SESSION['lang']['pesawatudara']."\r\n\t\t\t <input type=checkbox id=darat disabled ".((1 == $darat ? 'checked' : '')).'> '.$_SESSION['lang']['transportasidarat']."\r\n\t\t\t <input type=checkbox id=laut disabled ".((1 == $laut ? 'checked' : '')).'> '.$_SESSION['lang']['transportasiair']."\r\n\t\t\t <input type=checkbox id=mess disabled ".((1 == $mess ? 'checked' : '')).'> '.$_SESSION['lang']['mess']."\r\n\t\t\t <input type=checkbox id=hotel disabled ".((1 == $hotel ? 'checked' : '')).'> '.$_SESSION['lang']['hotel']."\r\n        </td>\r\n\t </tr>\t\r\n\t <tr class=rowcontent>\r\n\t   <td>\r\n\t      ".$_SESSION['lang']['uangmuka']."\r\n\t   </td>\r\n\t   <td>\r\n\t    <input type=hidden id=nitransaksipjd value='".$notransaksi."'>\r\n\t     <span id=oldval>".number_format($uangmuka, 2, '.', ',').'</span>';
if (0 == $xhrd || 0 == $xper) {
    echo $_SESSION['lang']['ganti'].":\r\n\t\t <input type=text class=myinputtextnumber id=newvalpjd onkeypress=\"return tanpa_kutip(event);\" size=15 maxlength=17>\r\n\t     <button class=mybutton onclick=saveUpdateValPJD()>".$_SESSION['lang']['save'].'</button>';
}

echo "   \r\n\t   </td>\r\n\t </tr> \t \t \r\n\t </table>\r\n\t <table class=standard  cellspacing=1>\r\n\t   <tr class=rowcontent>\r\n\t     <td>\r\n\t\t     ".$_SESSION['lang']['tujuan']."1\r\n\t\t </td>\r\n\t     <td>\r\n\t\t   ".$tujuan1.":\r\n\t\t   ".$tugas1."\r\n\t\t  </td> \r\n\t\t</tr>\r\n\t\t<tr class=rowcontent> \r\n\t     <td>\r\n\t\t    ".$_SESSION['lang']['tujuan']."2\r\n\t\t </td>\r\n\t     <td>\r\n\t\t   ".$tujuan2.":\r\n\t\t   ".$tugas2."\t\t \r\n\t\t  </td>\t\t \t\t \t\t \r\n\t   </tr>\r\n\t   \r\n\t   <tr class=rowcontent>\r\n\t     <td>\r\n\t\t     ".$_SESSION['lang']['tujuan']."3\r\n\t\t </td>\r\n\t     <td>\r\n\t\t   ".$tujuan3.":\r\n\t\t   ".$tugas3."\t\t \r\n\t\t </td>\r\n\t\t</tr>\r\n\t\t<tr class=rowcontent>\t\t \r\n\t     <td>\r\n\t\t    ".$_SESSION['lang']['tujuan']."4\r\n\t\t </td>\r\n\t     <td>\r\n\t\t   ".$tujuanlain.":\r\n\t\t   ".$tugaslain."\t\t </td>\t\t \t\t \t\t \r\n\t   </tr>\r\n\t </table>";

?>