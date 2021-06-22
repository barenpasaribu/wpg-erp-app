<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji\r\n    where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n    and periode='".$param['periode']."'";
$tgmulai = '';
$tgsampai = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tgsampai = $bar->tanggalsampai;
    $tgmulai = $bar->tanggalmulai;
}
if ('' === $tgmulai || '' === $tgsampai) {
    exit('Error: Accounting period is not registered');
}

$str = 'select sum(jumlah) as jumlah,idkomponen,karyawanid from '.$dbname.".sdm_gajidetail_vw\r\n       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'\r\n       and idkomponen in(20) and periodegaji='".$param['periode']."' group by idkomponen,karyawanid";
$resx = mysql_query($str);
$potx = [];
while ($barx = mysql_fetch_object($resx)) {
    $potx[$barx->karyawanid] = $barx->jumlah;
}
$str = 'select jumlah,idkomponen,karyawanid from '.$dbname.".sdm_gajidetail_vw\r\n       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'\r\n       and plus=1 and periodegaji='".$param['periode']."'";
$res = mysql_query($str);
$gaji = [];
while ($bar = mysql_fetch_object($res)) {
    if (1 === $bar->idkomponen) {
        $gaji[$bar->karyawanid][$bar->idkomponen] = $bar->jumlah - $potx[$bar->karyawanid];
    } else {
        $gaji[$bar->karyawanid][$bar->idkomponen] = $bar->jumlah;
    }
}
$str = 'select subbagian,karyawanid,namakaryawan from '.$dbname.".datakaryawan\r\n       where lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
$res = mysql_query($str);
$subunit = [];
while ($bar = mysql_fetch_object($res)) {
    $subunit[$bar->karyawanid] = $bar->subbagian;
    $namakaryawan[$bar->karyawanid] = $bar->namakaryawan;
}
$str = 'select distinct kodeorganisasi,tipe from '.$dbname.".organisasi\r\n       where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
$res = mysql_query($str);
$tipe = [];
while ($bar = mysql_fetch_object($res)) {
    $tipe[$bar->kodeorganisasi] = $bar->tipe;
}
$GJ = $gaji;
$str = 'select karyawanid from '.$dbname.".kebun_kehadiran_vw\r\n          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."'\r\n          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    unset($gaji[$bar->karyawanid]);
}
$str1 = 'select karyawanid from '.$dbname.".datakaryawan where\r\n           lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n           and tanggalmasuk>'".$tgsampai."'";
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
    unset($gaji[$bar1->karyawanid]);
}
$str = 'select karyawanid from '.$dbname.".kebun_prestasi_vw\r\n          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."'\r\n          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    unset($gaji[$bar->karyawanid]);
}
$str = 'select vhc,karyawanid from '.$dbname.'.vhc_5operator';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $ken[$bar->karyawanid] = $bar->vhc;
}
$str = 'select id,name from '.$dbname.'.sdm_ho_component';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $komponen[$bar->id] = $bar->id;
    $namakomponen[$bar->id] = $bar->name;
}
$str = 'select sum(umr) as umr, sum(insentif) as insentif,karyawanid from '.$dbname.".kebun_kehadiran_vw\r\n          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."'\r\n          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1 group by karyawanid";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $potongan[$bar->karyawanid][1] += $bar->umr;
    $potongan[$bar->karyawanid][32] += $bar->insentif;
}
$str = "select sum(upahkerja) as umr, sum(upahpremi) as insentif,sum(rupiahpenalty) as penalty,\r\n          karyawanid from ".$dbname.".kebun_prestasi_vw\r\n          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."'\r\n          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1 group by karyawanid";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $potongan[$bar->karyawanid][1] += $bar->umr - $bar->penalty;
    $potongan[$bar->karyawanid][32] += $bar->insentif;
}
$gajiblmalokasi = $GJ;
foreach ($GJ as $key => $row) {
    $gajiblmalokasi[$key][1] -= $potongan[$key][1];
    $gajiblmalokasi[$key][32] -= $potongan[$key][32];
}
$kekurangan = 0;
foreach ($gajiblmalokasi as $key) {
    foreach ($key as $row => $cell) {
        if ($cell < 0) {
            $kekurangan += $cell;
        }
    }
}
if (empty($gaji)) {
    exit('Error: No Salary data found');
}

echo "<button class=mybutton onclick=prosesGaji(1) id=btnproses>Process</button>\r\n                  <table class=sortable cellspacing=1 border=0>\r\n                  <thead>\r\n                    <tr class=rowheader>\r\n                    <td>No</td>\r\n                    <td>".$_SESSION['lang']['periode']."</td>\r\n                    <td>".$_SESSION['lang']['employeename']."</td>\r\n                    <td>".$_SESSION['lang']['karyawanid']."</td>\r\n                    <td>".$_SESSION['lang']['idkomponen']."</td>\r\n                    <td>".$_SESSION['lang']['nama']."</td>\r\n                    <td>".$_SESSION['lang']['subbagian']."</td>\r\n                    <td>".$_SESSION['lang']['tipe']."</td>\r\n                    <td>".$_SESSION['lang']['kendaraan']."</td>\r\n                    <td>".$_SESSION['lang']['jumlah']."</td>\r\n                    </tr>\r\n                  </thead>\r\n                  <tbody>";
$no = 0;
foreach ($gaji as $key => $baris) {
    foreach ($baris as $val => $jlh) {
        ++$no;
        echo "<tr class=rowcontent id='row".$no."'>\r\n                    <td>".$no."</td>\r\n                    <td id='periode".$no."'>".$_POST['periode']."</td>\r\n                    <td id='namakaryawan".$no."'>".$namakaryawan[$key]."</td>\r\n                    <td id='karyawanid".$no."'>".$key."</td>\r\n                    <td id='komponen".$no."'>".$val."</td>\r\n                    <td id='namakomponen".$no."'>".$namakomponen[$val]."</td>\r\n                    <td id='subbagian".$no."'>".$subunit[$key]."</td>\r\n                    <td id='tipeorganisasi".$no."'>".$tipe[$subunit[$key]]."</td>\r\n                    <td id='mesin".$no."'>".$ken[$key]."</td>\r\n                    <td align=right id='jumlah".$no."'>".$jlh."</td>\r\n                    </tr>";
        $ttl += $jlh;
    }
}
echo "<tr class=rowcontent id='row".$no."'>\r\n                    <td colspan=9>Total</td>\r\n                    <td align=right>".number_format($ttl)."</td>\r\n                    </tr>";
echo '</tbody><tfoot></tfoot></table>';

?>