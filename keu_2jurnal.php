<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/keu_laporan.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['laporanjurnal']).'</b>');
$str = 'select distinct periode as periode from '.$dbname.".setup_periodeakuntansi\r\n      order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PT' OR (tipe='HOLDING' AND induk = '') order by namaorganisasi desc";
    $res = mysql_query($str);
    $optpt = '';
    while ($bar = mysql_fetch_object($res)) {
        $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n        or tipe='HOLDING')  and induk!=''\r\n        ";
    $res = mysql_query($str);
    $optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
} else {
    if ('KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n          where tipe='PT'\r\n          order by namaorganisasi desc";
        $res = mysql_query($str);
        $optpt = '';
        while ($bar = mysql_fetch_object($res)) {
            $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL')  and induk!=''\r\n        ";
        $res = mysql_query($str);
        $optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
        }
    } else {
        $optpt = '';
        $optpt .= "<option value='".$_SESSION['empl']['kodeorganisasi']."'>".$_SESSION['empl']['kodeorganisasi'].'</option>';
        $optgudang .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas'].'</option>';
    }
}

for ($mulaidrnol = 0; $mulaidrnol < 6; ++$mulaidrnol) {
    $optrev .= "<option value='".$mulaidrnol."'>".$mulaidrnol.'</option>';
}
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['laporanjurnal']."</legend>\r\n\t ".$_SESSION['lang']['pt'].' : '."<select id=pt style='width:200px;'  onchange=ambilAnakBB(this.options[this.selectedIndex].value)>".$optpt."</select>\r\n\t ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>\r\n\t ".$_SESSION['lang']['periode'].' : '."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n\t  - "."<select id=periode1 onchange=hideById('printPanel')>".$optper."</select>             \r\n\t ".$_SESSION['lang']['revisi'].' : '.'<select id=revisi onchange=getPeriodeRev()>'.$optrev."</select>\r\n\t <br><button class=mybutton onclick=getLaporanJurnal()>".$_SESSION['lang']['proses']."</button>\r\n\t <button class=mybutton onclick=fisikKeExcel(event,'keu_laporanJurnal_Excel.php')>".$_SESSION['lang']['excel']."</button>\r\n\t <button class=mybutton onclick=fisikKePDF(event,'keu_laporanJurnal_pdf.php')>".$_SESSION['lang']['pdf']."</button>\r\n\t </fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=fisikKeExcel(event,'keu_laporanJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=fisikKePDF(event,'keu_laporanJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span>\r\n\t <div style='width:1480px;display:fixed;'>\r\n       <table class=sortable cellspacing=1 border=0 width=1480px>\r\n\t     <thead>\r\n\t\t    <tr>\r\n                        <td align=center style='width:50px;'>".$_SESSION['lang']['nourut']."</td>\r\n                        <td align=center style='width:250px;'>".$_SESSION['lang']['nojurnal']."</td>\r\n\t\t\t\t\t\t<td align=center style='width:250px;'>".$_SESSION['lang']['nodok']."</td>\r\n                        <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal']."</td>\r\n                        <td align=center style='width:60px;'>".$_SESSION['lang']['organisasi']."</td>\r\n                        <td align=center style='width:60px;'>".$_SESSION['lang']['noakun']."</td>\r\n                        <td align=center style='width:200px;'>".$_SESSION['lang']['namaakun']."</td>\r\n                        <td align=center  style='width:240px;'>".$_SESSION['lang']['keterangan']."</td>\r\n                        <td align=center  style='width:100px;'>".$_SESSION['lang']['debet']."</td>\r\n                        <td align=center style='width:100px;'>".$_SESSION['lang']['kredit']."</td>\r\n                        <td align=center style='width:200px;'>".$_SESSION['lang']['noreferensi']."</td>    \r\n                        <td align=center style='width:80px;'>".$_SESSION['lang']['kodeblok']."</td>\r\n                        <td align=center style='width:60px;'>".$_SESSION['lang']['tahuntanam']."</td>\r\n                        <td align=center style='width:30px;'>".$_SESSION['lang']['revisi']."</td>\r\n\t\t   </tr>  \r\n\t\t </thead>\r\n\t\t <tbody>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>         \r\n\r\n\t <div style='width:1500px;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t  <tr>\r\n\t\t </tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
CLOSE_BOX();
close_body();

?>