<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$proses = $_GET['proses'];
$lokasi = $_SESSION['empl']['lokasitugas'];
$kebun = $_POST['kebun'];
$mandor = $_POST['mandor'];
$tanggal = $_POST['tanggal'];
if ('excel' === $proses || 'pdf' === $proses) {
    $kebun = $_GET['kebun'];
    $mandor = $_GET['mandor'];
    $tanggal = $_GET['tanggal'];
}

if ('getmandor' === $proses) {
    $optMandor = '<option value="all">'.$_SESSION['lang']['all'].'</option>';
    $sMan = 'select a.nikmandor, b.namakaryawan, b.lokasitugas from '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid\r\n    where a.kodeorg like '%".$lokasi."%'\r\n    group by a.nikmandor\r\n    order by b.namakaryawan";
    $qMan = mysql_query($sMan) ;
    while ($rMan = mysql_fetch_assoc($qMan)) {
        $optMandor .= '<option value='.$rMan['nikmandor'].'>'.$rMan['namakaryawan'].' ['.$rMan['lokasitugas'].']</option>';
    }
}

$lokasi = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$tanggal = tanggalsystem($tanggal);
$tanggal = substr($tanggal, 0, 4).'-'.substr($tanggal, 4, 2).'-'.substr($tanggal, 6, 2);
if ('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) {
    if ('' === $kebun) {
        echo 'Error: Kebun tidak boleh kosong.';
        exit();
    }

    if ('' === $tanggal) {
        echo 'Error: Tanggal tidak boleh kosong.';
        exit();
    }
}


if ('excel' === $proses || 'preview' === $proses) {
    $border = 0;
    if ('excel' === $proses) {
        $border = 1;
    }

    $stream .= "<table cellspacing='1' border='".$border."' class='sortable'>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n        <td>".$_SESSION['lang']['nomor']."</td>\r\n        <td>".$_SESSION['lang']['notransaksi']."</td>    \r\n\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t<td>".$_SESSION['lang']['jhk']."</td>\r\n\t<td>".$_SESSION['lang']['umr']."</td>\r\n\t<td>".$_SESSION['lang']['insentif']."</td>            \r\n        </tr></thead>\r\n\t<tbody>";

    $selectmandor = "b.nikmandor = '".$rMan['nikmandor']."'";
    if ('all' === $mandor) {
        $selectmandor = "b.nikmandor like '%%'";
    }


    $sMan = 'select DISTINCT(a.nikmandor), b.namakaryawan as mandor from '.$dbname.".kebun_aktifitas a left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid where a.kodeorg = '".$lokasi."' group by a.nikmandor order by b.namakaryawan";
    $qMan = mysql_query($sMan) ;
    
    while ($rMan = mysql_fetch_array($qMan)) {
        
    $stream .= "<tr style=cursor:pointer; onclick=tampilhilang('".$rMan['mandor']."')> 
    <td colspan=4>".$rMan['nikmandor']." | ".$rMan['mandor']."</td> 
    <td align=right></td><td align=right></td><td align=right></td></tr>";



    $str1 = 'select a.*, b.*, c.namakaryawan from '.$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid where  b.kodeorg like '".$kebun."%' and nikmandor = '".$rMan['nikmandor']."' and b.tanggal = '".$tanggal."' AND a.notransaksi like '%PNN%' ";
    
    $res1 = mysql_query($str1);

    while ($bar1 = mysql_fetch_object($res1)) {
        
        ++$no;
        $stream .= "<tr class=rowcontent><td>".$no."</td><td>".$bar1->notransaksi."</td><td>".tanggalnormal($bar1->tanggal)."</td><td>".$bar1->namakaryawan."</td><td align=right>".number_format($bar1->jumlahhk, 2)."</td><td align=right>".number_format($bar1->upahkerja)."</td><td align=right>".number_format($bar1->upahpremi)."</td></tr>";
        }




    $str = 'select * from '.$dbname.".kebun_kehadiran a left join ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid where  b.kodeorg like '".$kebun."%' and nikmandor = '".$rMan['nikmandor']."' and b.tanggal = '".$tanggal."' ";
//    saveLog($str);
    $res = mysql_query($str);

    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        
        ++$no;
       $stream .= "<tr class=rowcontent><td>".$no."</td><td>".$bar->notransaksi."</td><td>".tanggalnormal($bar->tanggal)."</td><td>".$bar->namakaryawan."</td><td align=right>".number_format($bar->jhk, 2)."</td><td align=right>".number_format($bar->umr['umr'])."</td><td align=right>".number_format($bar->insentif)."</td>     </tr>";
        }
    

    }

//    $stream .= "<tr class=rowcontent>\r\n\t<td colspan=4>Total</td>\r\n\t<td align=right>".number_format($jhk, 2)."</td>\r\n\t<td align=right>".number_format($umr)."</td>\r\n\t<td align=right>".number_format($insentif)."</td>\r\n        </tbody></table>";
}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHms');
        $nop_ = 'KehadiranperMandor'.$kebun.$mandor.'-'.$tanggal.'_'.date('YmdHis');
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls.gz';\r\n            </script>";

        break;
}

?>