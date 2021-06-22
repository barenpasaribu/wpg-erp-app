<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$lokasitugas = $_POST['lokasitugas'];
$periode = $_POST['periode'];
$mitmk = $periode.'1231';
//FA 2019-11-06 - CDS/LSP
if ('HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
    //$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from '.$dbname.".datakaryawan\r\n\t       where lokasitugas='".$lokasitugas."' and alokasi=0\r\n\t\t   and tanggalmasuk<>'0000-00-00' and \r\n\t\t   tanggalmasuk<".$mitmk.' and tipekaryawan in(0,1,2,3)';
    $str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from '.$dbname.".datakaryawan\r\n\t       where lokasitugas='".$lokasitugas."' and alokasi=0\r\n\t\t   and tanggalmasuk<>'0000-00-00' and \r\n\t\t   tanggalmasuk<".$mitmk.' ';
} else {
    //$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from '.$dbname.".datakaryawan\r\n\t       where alokasi=1\r\n\t\t   and tanggalmasuk<>'0000-00-00' and \r\n\t\t   tanggalmasuk<".$mitmk.' and tipekaryawan in(0,1,2,3)';
    $str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from '.$dbname.".datakaryawan\r\n\t       where lokasitugas like '".substr($lokasitugas,0,3)."%' and tanggalmasuk<>'0000-00-00' and \r\n\t\t   tanggalmasuk<".$mitmk.' ';
	//echo "warning: ".$str1;
	//exit();
}

$res1 = mysql_query($str1);
$max = mysql_num_rows($res1);
echo '<button class=mybutton onclick=simpanAwal('.$max.')>'.$_SESSION['lang']['save']."</button>\r\n\t     <table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t    <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalmasuk']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['dari']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['hakcuti']."</td>\r\n\t\t\t<td>".'Lokasi Tugas'."</td>\r\n\t\t\t</tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
$no = -1;
while ($bar1 = mysql_fetch_object($res1)) {
    $x = readTextFile('config/jumlahcuti.lst');
    if (0 < (int) $x) {
        $hakcuti = $x;
    } else {
        $hakcuti = 12;
    }

    ++$no;
    $tgl = substr(str_replace('-', '', $bar1->tanggalmasuk), 4, 4);
    $dari = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), $periode);
    $dari = date('Ymd', $dari);
    $sampai = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), $periode + 1);
    $sampai = date('Ymd', $sampai);
    $d = str_replace('-', '', $bar1->tanggalmasuk);
    if ($d == $dari) {
        $hakcuti = 0;
    }

    echo '<tr class=rowcontent id=baris'.$no.">\r\n\t\t           <td id=karyawanid".$no.'>'.$bar1->karyawanid."</td>\r\n\t\t\t\t   <td id=nama".$no.'>'.$bar1->namakaryawan."</td>\r\n\t\t\t\t   <td>".$bar1->tanggalmasuk."</td>\r\n\t\t\t\t   <td id=dari".$no.'>'.$dari."</td>\r\n\t\t\t\t   <td id=sampai".$no.'>'.$sampai."</td>\r\n\t\t\t\t   <td id=periode".$no.'>'.$periode."</td>\r\n\t\t\t\t   <td id=hak".$no.'>'.$hakcuti."</td>\r\n\t\t\t\t   <td id=kodeorg".$no.'>'.substr($bar1->lokasitugas, 0, 4)."</td>\r\n\t\t\t\t   ";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";

?>