<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji \r\n    where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n    and periode='".$param['periode']."'";
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

$str = 'select sum(jumlah) as jumlah,idkomponen,karyawanid from '.$dbname.".sdm_gajidetail_vw \r\n       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' \r\n       and idkomponen in(20) and periodegaji='".$param['periode']."' group by idkomponen,karyawanid";
$resx = mysql_query($str);
$potx = [];
while ($barx = mysql_fetch_object($resx)) {
    $potx[$barx->karyawanid] = $barx->jumlah;
}
$str = 'select sum(jumlah) as jumlah,karyawanid from '.$dbname.".sdm_gajidetail_vw \r\n       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' \r\n       and plus=1 and periodegaji='".$param['periode']."' group by karyawanid";
$res = mysql_query($str);
$gaji = [];
while ($bar = mysql_fetch_object($res)) {
    $gaji[$bar->karyawanid] = $bar->jumlah - $potx[$bar->karyawanid];
}
$str = 'select subbagian,karyawanid,namakaryawan from '.$dbname.".datakaryawan \r\n       where lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
$res = mysql_query($str);
$subunit = [];
while ($bar = mysql_fetch_object($res)) {
    $subunit[$bar->karyawanid] = $bar->subbagian;
    $namakaryawan[$bar->karyawanid] = $bar->namakaryawan;
}
$str = 'select distinct kodeorganisasi,tipe from '.$dbname.".organisasi \r\n       where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
$res = mysql_query($str);
$tipe = [];
while ($bar = mysql_fetch_object($res)) {
    $tipe[$bar->kodeorganisasi] = $bar->tipe;
}
$str = 'select karyawanid,(sum(umr)+sum(insentif)) as upah from '.$dbname.".kebun_kehadiran_vw\r\n        where unit='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$tgmulai."' \r\n        and '".$tgsampai."' group by karyawanid";
$res = mysql_query($str);
$gjPerawatan = [];
while ($bar = mysql_fetch_object($res)) {
    $gjPerawatan[$bar->karyawanid] = $bar->upah;
}
$str = 'select karyawanid,(sum(upahkerja)+sum(upahpremi)-sum(rupiahpenalty)) as upah from '.$dbname.".kebun_prestasi_vw\r\n        where unit='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$tgmulai."' \r\n        and '".$tgsampai."' group by karyawanid";
$res = mysql_query($str);
$gjPerawatan = [];
while ($bar = mysql_fetch_object($res)) {
    $gjPanen[$bar->karyawanid] = $bar->upah;
}
$masukkotak = [];
$gaji1 = $gaji;
foreach ($gaji as $karid => $gaji) {
    $gajiyangsudahdialokasi[$karid] = $gjPanen[$karid] + $gjPerawatan[$karid];
    if (0 !== $gajiyangsudahdialokasi[$karid]) {
        $masukkotak[$karid] = $gaji - $gajiyangsudahdialokasi[$karid];
    }
}
$zzz = $masukkotak;
if (empty($masukkotak)) {
    exit('Error: Salaries has been allocated correctly');
}

echo "Un Allocated Salaries:<br>\r\n                  <button class=mybutton onclick=prosesGajiLangsung(1) id=btnproses>Process/Allocate</button>\r\n                  <table class=sortable cellspacing=1 border=0>\r\n                  <thead>\r\n                    <tr class=rowheader>\r\n                    <td>No</td>\r\n                    <td>".$_SESSION['lang']['dari']."</td>\r\n                    <td>".$_SESSION['lang']['sampau']."</td>\r\n                    <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                    <td>".$_SESSION['lang']['karyawanid']."</td>\r\n                    <td>".$_SESSION['lang']['subbagian']."</td>\r\n                    <td>".$_SESSION['lang']['tipe']."</td>\r\n                    <td>".$_SESSION['lang']['blmAlokasi']."</td>\r\n                    <td>".$_SESSION['lang']['gaji']."</td>\r\n                    <td>Allocated</td>\r\n                    </tr>\r\n                  </thead>\r\n                  <tbody>";
$no = 0;
foreach ($masukkotak as $key => $baris) {
    ++$no;
    echo "<tr class=rowcontent>\r\n                    <td>".$no."</td>\r\n                    <td>".$tgmulai."</td>\r\n                    <td>".$tgsampai."</td>    \r\n                    <td>".$namakaryawan[$key]."</td>\r\n                    <td>".$key."</td>    \r\n                    <td>".$subunit[$key]."</td>\r\n                    <td>".$tipe[$subunit[$key]]."</td>                        \r\n                    <td align=right>".number_format($baris)."</td>\r\n                    <td align=right>".number_format($gaji1[$key])."</td>\r\n                    <td align=right>".number_format($gajiyangsudahdialokasi[$key])."</td>       \r\n                    </tr>";
    $ttl += $baris;
}
echo "<tr class=rowcontent id='row".$no."'>\r\n                    <td colspan=7>Total</td>\r\n                    <td align=right>".number_format($ttl)."</td>\r\n                    <td></td>\r\n                    <td></td>\r\n                    </tr>";
echo '</tbody><tfoot></tfoot></table>';
$s = 0;
foreach ($zzz as $karid => $val) {
    if (0 === $s) {
        $arrkarid = '#'.$karid.'#';
    } else {
        $arrkarid .= ',#'.$karid.'#';
    }

    ++$s;
}
echo '<input type=hidden id=karyawanid value="'.$arrkarid.'">';
echo "<input type=hidden id=jumlah value='".$ttl."'>";
echo "<input type=hidden id=dari value='".$tgmulai."'>";
echo "<input type=hidden id=sampai value='".$tgsampai."'>";

?>