<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/pabrik_2pengolahan.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$nopengolahan = $_GET['nopengolahan'];
$tanggal = $_GET['tanggal'];
$kodeorg = $_GET['kodeorg'];
$periode_tahun = $_GET['periode_tahun'];
$periode_bulan = $_GET['peruide_bulan'];
$periode = $periode_tahun.'-'.addZero($periode_bulan, 2);
echo "<fieldset><legend>Print Excel</legend>\r\n     <img onclick=\"detailExcel(event,'pabrik_slave_2pengolahanmesin.php?type=excel&nopengolahan=".$nopengolahan.'&kodeorg='.$kodeorg.'&periode_tahun='.$periode_tahun.'&periode_bulan='.$periode_bulan."')\" src=images/excel.jpg class=resicon title='MS.Excel'>\r\n     </fieldset>";
if ('excel' !== $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
} else {
    $stream = '<table class=sortable border=1 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td>No</td>\r\n          <td>Station</td>\r\n          <td>".$_SESSION['lang']['mesin']."</td>\r\n          <td>".$_SESSION['lang']['jammulai']."</td>\r\n          <td>".$_SESSION['lang']['jamselesai']."</td>\r\n          <td>".$_SESSION['lang']['jamstagnasi']."</td>\r\n          <td>".$_SESSION['lang']['keterangan']."</td>\r\n          <td>".$_SESSION['lang']['prestasi']."</td>\r\n        </tr>\r\n      </thead>\r\n      <tbody>";
$str = 'select * from '.$dbname.".pabrik_pengolahanmesin\r\n              where nopengolahan = '".$nopengolahan."%'";
$strJ = 'select * from '.$dbname.'.organisasi';
$resJ = mysql_query($strJ, $conn);
while ($barJ = mysql_fetch_object($resJ)) {
    $org[$barJ->kodeorganisasi] = $barJ->namaorganisasi;
}
$res = mysql_query($str);
$no = 0;
$tdebet = 0;
$tkredit = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $debet = 0;
    $kredit = 0;
    if (0 < $bar->jumlah) {
        $debet = $bar->jumlah;
    } else {
        $kredit = $bar->jumlah * -1;
    }

    $stream .= "<tr class=rowcontent>\r\n           <td align=right>".$no."</td>\r\n           <td>".$org[$bar->kodeorg]."</td>               \r\n           <td>".$org[$bar->tahuntanam]."</td>               \r\n           <td align=right>".substr($bar->jammulai, 0, 5)."</td>               \r\n           <td align=right>".substr($bar->jamselesai, 0, 5)."</td>               \r\n           <td align=right>".$bar->jamstagnasi."</td>               \r\n           <td>".$bar->keterangan."</td>               \r\n           <td>".$bar->prestasi."</td>               \r\n         </tr>";
}
$stream .= '</tbody></table>';
if ('excel' === $_GET['type']) {
    $nop_ = 'Detail_pengolahan_(Mesin)_'.$kodeorg.'_'.$nopengolahan;
    if (0 < strlen($stream)) {
        if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
                if ('.' !== $file && '..' !== $file) {
                    @unlink('tempExcel/'.$file);
                }
            }
            closedir($handle);
        }

        $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
        if (!fwrite($handle, $stream)) {
            echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
            exit();
        }

        echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
        closedir($handle);
    }
} else {
    echo $stream;
}

?>