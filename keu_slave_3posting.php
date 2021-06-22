<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select kodeorganisasi from '.$dbname.".organisasi where tipe='GUDANG' and \r\n       kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
$res = mysql_query($str);
$gudang = '';
while ($bar = mysql_fetch_array($res)) {
    $gudang = $bar[0];
}
if ('' === $gudang) {
    exit('Error: You have no inventory control');
}

$str = 'select tanggalmulai,tanggalsampai from '.$dbname.".setup_periodeakuntansi\r\n           where kodeorg='".$gudang."' and periode='".$param['periode']."'";
$res = mysql_query($str);
$periodeawal = '';
$periodeakhir = '';
while ($bar = mysql_fetch_object($res)) {
    $periodeawal = $bar->tanggalmulai;
    $periodeakhir = $bar->tanggalsampai;
}
if ('' === $periodeakhir || '' === $periodeawal) {
    exit('Error: Invalid inventory control period');
}

$str = 'select * from '.$dbname.".log_transaksi_vw \r\n               where kodegudang='".$gudang."' and tanggal >='".$periodeawal."' \r\n               and tanggal<='".$periodeakhir."' and post=0 limit 1";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    exit('Error: there is still a warehouse transactions that have not been posted');
}

$str = 'select * from '.$dbname.".log_transaksi_vw where kodegudang='".$gudang."' \r\n                   and tanggal >='".$periodeawal."' and tanggal<='".$periodeakhir."'\r\n                   and statusjurnal=0 and kodebarang<40000000 order by tanggal";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo "<button class=mybutton onclick=prosesGudang(1) id=btnproses>Process</button>\r\n                  <table class=sortable cellspacing=1 border=0>\r\n                  <thead>\r\n                    <tr class=rowheader>\r\n                    <td>No</td>\r\n                    <td>".$_SESSION['lang']['tipetransaksi']."</td>\r\n                    <td>".$_SESSION['lang']['tanggal']."</td>\r\n                    <td>".$_SESSION['lang']['notransaksi']."</td>\r\n                    <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                    <td>".$_SESSION['lang']['jumlah']."</td>\r\n                    <td>".$_SESSION['lang']['satuan']."</td>\r\n                    <td>".$_SESSION['lang']['supplier']."</td>\r\n                    <td>".$_SESSION['lang']['dari'].'/'.$_SESSION['lang']['ke']."</td>\r\n                    <td>".$_SESSION['lang']['untukunit']."</td>\r\n                    <td>".$_SESSION['lang']['blok']."</td>\r\n                    <td>".$_SESSION['lang']['kendaraan']."</td>\r\n                    <td>".$_SESSION['lang']['kegiatan']."</td>\r\n                    <td>".$_SESSION['lang']['harga']."</td>\r\n                    <td>".$_SESSION['lang']['nopo']."</td>\r\n                    <td>".$_SESSION['lang']['sloc']."</td>\r\n                    <td>".$_SESSION['lang']['keterangan']."</td>\r\n                    </tr>\r\n                  </thead>\r\n                  <tbody>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $nilaitotal = 0;
        if (1 === $bar->tipetransaksi) {
            $nilaitotal = $bar->hargasatuan * $bar->jumlah;
        }

        if (0 === $nilaitotal) {
            $nilaitotal = $bar->hartot;
        }

        echo "<tr class=rowcontent id='row".$no."'>\r\n                    <td>".$no."</td>\r\n                    <td id='tipetransaksi".$no."'>".$bar->tipetransaksi."</td>\r\n                    <td id='tanggal".$no."'>".$bar->tanggal."</td>    \r\n                    <td id='notransaksi".$no."'>".$bar->notransaksi."</td>\r\n                    <td id='kodebarang".$no."'>".$bar->kodebarang."</td>\r\n                    <td align=right id='jumlah".$no."'>".$bar->jumlah."</td>\r\n                    <td id='satuan".$no."'>".$bar->satuan."</td>\r\n                    <td id='idsupplier".$no."'>".$bar->idsupplier."</td>\r\n                    <td id='gudangx".$no."'>".$bar->gudangx."</td>    \r\n                    <td id='untukunit".$no."'>".$bar->untukunit."</td>\r\n                    <td id='kodeblok".$no."'>".$bar->kodeblok."</td>\r\n                    <td id='kodemesin".$no."'>".$bar->kodemesin."</td>\r\n                    <td id='kodekegiatan".$no."'>".$bar->kodekegiatan."</td>    \r\n                    <td align=right id='hartot".$no."'>".number_format($nilaitotal, 2, '.', '')."</td>\r\n                    <td id='nopo".$no."'>".$bar->nopo."</td>\r\n                    <td id='kodegudang".$no."'>".$bar->kodegudang."</td>\r\n                    <td id='keterangan".$no."'>".$bar->keterangan."</td>    \r\n                    </tr>";
    }
    echo '</tbody><tfoot></tfoot></table>';
} else {
    echo 'No. Data';
}

?>