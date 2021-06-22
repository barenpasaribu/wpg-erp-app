<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$kodeorg = $_GET['kodeorg'];
$optJbtn = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$str = "select a.karyawanid,a.namakaryawan,a.kodecatu,a.subbagian,b.tipe,c.keterangan,a.kodecatu,a.tipekaryawan,a.kodejabatan,d.namajabatan\r\n                  from ".$dbname.'.datakaryawan a left join '.$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id                  \r\n                  left join ".$dbname.".sdm_5catuporsi c on a.kodecatu=c.kode\r\n                  left join ".$dbname.".sdm_5jabatan d on a.kodejabatan=d.kodejabatan    \r\n                  where a.lokasitugas='".$kodeorg."' and tipekaryawan!=5";
$res = mysql_query($str);
$kamusKar = [];
while ($bar = mysql_fetch_object($res)) {
    $kamusKar[$bar->karyawanid]['id'] = $bar->karyawanid;
    $kamusKar[$bar->karyawanid]['nama'] = $bar->namakaryawan;
    $kamusKar[$bar->karyawanid]['kodecatu'] = $bar->kodecatu;
    $kamusKar[$bar->karyawanid]['tipekaryawan'] = $bar->tipekaryawan;
    $kamusKar[$bar->karyawanid]['namatipe'] = $bar->tipe;
    $kamusKar[$bar->karyawanid]['kelompok'] = $bar->keterangan;
    $kamusKar[$bar->karyawanid]['kode'] = $bar->kodecatu;
    $kamusKar[$bar->karyawanid]['jabatan'] = $bar->namajabatan;
}
switch ($_GET['aksi']) {
    case 'excel':
        $stream .= 'Daftar Catu beras Periode '.$_GET['periode']."<br>\r\n                    Unit: ".$kodeorg."\r\n                    <table class=sortable border=1 cellspacing=1>\r\n                    <thead>\r\n                    <tr class=rowheader>\r\n                    <td bgcolor=#DEDEDE align=center>No.</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['subbagian']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['periode']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipe']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['status']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>Ltr/Hk</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']." HK</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n                    <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."(Rp)</td>\r\n                    </tr>\r\n                    </thead>\r\n                    <tbody>";
        $sData = 'select distinct * from '.$dbname.".sdm_catu where \r\n                    kodeorg='".$kodeorg."' and periodegaji='".$_GET['periode']."'";
        $qData = mysql_query($sData) || exit(msyql_error($conn));
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $stream .= "<tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$kodeorg."</td> \t\r\n                        <td>".$rData['subbagian']."</td>\r\n                        <td>".$rData['periodegaji']."</td>\r\n                        <td>".$kamusKar[$rData['karyawanid']]['nama']."</td>\r\n                        <td>".$kamusKar[$rData['karyawanid']]['namatipe']."</td>\r\n                        <td>".$kamusKar[$rData['karyawanid']]['jabatan']."</td>\r\n                        <td>".$kamusKar[$rData['karyawanid']]['kode']."</td>                          \r\n                        <td>".number_format($rData['catuperhk'], 2, '.', ',')."</td>\r\n                        <td align=right>".number_format($rData['jumlahhk'], 0, '.', ',')."</td>\r\n                        <td align=right>".number_format($rData['hargacatu'], 0, '.', ',')."</td>     \r\n                        <td align=right>".number_format($rData['jumlahrupiah'], 0, '.', ',')."</td>     \r\n                        ";
            $ttl += $rData['jumlahrupiah'];
        }
        $stream .= "<tr class=rowheader>\r\n                        <td colspan=11>TOTAL</td>     \r\n                        <td align=right>".number_format($ttl, 0, '.', ',')."</td>     \r\n                        ";
        $stream .= '</tbody></table>';
        $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'listDataCatuBeras__'.$_GET['kodeorg'].'__'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls.gz';\r\n            </script>";

        break;
    default:
        break;
}

?>