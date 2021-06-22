<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$periode = $_POST['periode'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdOrg = $_POST['kdOrg'];
if ('' == $periode) {
    $periode = $_GET['periode'];
}

if ('' == $kdOrg) {
    $kdOrg = $_GET['kdOrg'];
}

if ('' == $kdOrg) {
    $kdOrg = $_SESSION['empl']['lokasitugas'];
}

$lok = substr($kdOrg, 0, 4);
$sDatez = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where periode = '".$periode."' and kodeorg= '".$lok."'";
$qDatez = mysql_query($sDatez);
while ($rDatez = mysql_fetch_assoc($qDatez)) {
    $tanggalMulai = $rDatez['tanggalmulai'];
    $tanggalSampai = $rDatez['tanggalsampai'];
}
$str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan where lokasitugas='".$lok."'";
$res = mysql_query($str);
$nama = [];
while ($bar = mysql_fetch_object($res)) {
    $nama[$bar->karyawanid] = $bar->namakaryawan;
}
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    $str = "select a.notransaksi,b.tanggal,sum(a.upahpremi) as premi, sum(a.hasilkerja) as jjg, sum(a.rupiahpenalty)as penalty,\r\n    sum(hasilkerjakg) as kg, b.nikmandor as mandor,b.nikmandor1 as mandor1, b.nikasisten as kraniproduksi, b.keranimuat\r\n    FROM ".$dbname.".kebun_prestasi a\r\n    left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi\r\n    where a.notransaksi like '%PNN%' and b.tanggal between '".$tanggalMulai."' and '".$tanggalSampai."' \r\n    and a.notransaksi like '%".$lok."%'\r\n    group by a.notransaksi";
} else {
    $str = "select a.notransaksi,b.tanggal,sum(a.upahpremi) as premi, sum(a.hasilkerja) as jjg, sum(a.rupiahpenalty)as penalty,\r\n    sum(hasilkerjakg) as kg,b.nikmandor as mandor,b.nikmandor1 as mandor1, b.nikasisten as kraniproduksi, b.keranimuat\r\n    FROM ".$dbname.".kebun_prestasi a\r\n    left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi\r\n    where a.notransaksi like '%PNN%' and b.tanggal between '".$tanggalMulai."' and '".$tanggalSampai."' \r\n    and a.notransaksi like '%".$lok."%' and a.nik in(\r\n        select karyawanid from ".$dbname.".datakaryawan where subbagian='".$kdOrg."' and lokasitugas='".$lok."'\r\n        )\r\n    group by a.notransaksi";
}

$res = mysql_query($str);
$brd = 0;
$bg = '';
if ('exce' == $proses) {
    $brd = 1;
    $bg = ' bgcolor=#DEDEDE';
}

$stream = 'Premi per No.Transaksi:';
$stream .= '<table class=sortable cellspacing=1 border='.$brd.">\r\n          <thead>\r\n          <tr class=rowheader>\r\n            <td ".$bg.'>'.$_SESSION['lang']['nomor']."</td>\r\n            <td ".$bg.'>'.$_SESSION['lang']['notransaksi']."</td>    \r\n            <td ".$bg.'>'.$_SESSION['lang']['tanggal']."</td>\r\n            <td ".$bg.'>'.$_SESSION['lang']['mandor']."</td>  \r\n            <td ".$bg.'>'.$_SESSION['lang']['nikmandor1']."</td> \r\n            <td ".$bg.'>'.$_SESSION['lang']['keraniafdeling']."</td>\r\n            <td ".$bg.'>'.$_SESSION['lang']['keranimuat']."</td>   \r\n            <td ".$bg.'>'.$_SESSION['lang']['jmlhTandan']."</td>\r\n            <td ".$bg.'>'.$_SESSION['lang']['upahpremi']."</td>\r\n            <td ".$bg.'>'.$_SESSION['lang']['rupiahpenalty']."</td>   \r\n            <td ".$bg.'>'.$_SESSION['lang']['hasilkerjakg']."</td>    \r\n          </tr>\r\n          </thead>\r\n          <tbody>\r\n          ";
$no = 0;
$ttandan = 0;
$tpremi = 0;
$tpenalty = 0;
$tkg = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "  <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$bar->notransaksi."</td>    \r\n                <td>".tanggalnormal($bar->tanggal)."</td>\r\n                <td>".$nama[$bar->mandor]."</td>  \r\n                <td>".$nama[$bar->mandor1]."</td> \r\n                <td>".$nama[$bar->kraniproduksi]."</td>\r\n                <td>".$nama[$bar->keranimuat]."</td>   \r\n                <td align=right>".$bar->jjg."</td> \r\n                <td align=right>".number_format($bar->premi)."</td>\r\n                <td align=right>".number_format($bar->penalty)."</td>   \r\n                <td align=right>".number_format($bar->kg)."</td>    \r\n              </tr>";
    $ttandan += $bar->jjg;
    $tpremi += $bar->premi;
    $tpenalty += $bar->penalty;
    $tkg += $bar->kg;
}
$stream .= "</tbody>\r\n          <tfoot>\r\n          <tr class=rowcontent>\r\n             <td colspan=7>Total</td>\r\n             <td align=right>".$ttandan."</td>\r\n             <td align=right>".number_format($tpremi)."</td>\r\n             <td align=right>".number_format($tpenalty)."</td>   \r\n             <td align=right>".number_format($tkg)."</td>     \r\n          </tr>\r\n          </tfoot>\r\n          </table>";
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $nop_ = 'Laporan_premi_per_kemandoran_'.$kdOrg.'_'.$periode;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can't convert to excel format');\r\n                    </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls';\r\n                    </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        echo 'belum tersedia';

        break;
}

?>