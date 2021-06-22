<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=\"js/keu_laporan.js\"></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['neracasaldo']).'</b>');
$str = 'select distinct periode as periode from '.$dbname.".setup_periodeakuntansi\r\n      order by periode desc";
$res = mysql_query($str);
$optper = '';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
$optrev .= "<option value='0'>0</option>";
$optrev .= "<option value='1'>1</option>";
$optrev .= "<option value='2'>2</option>";
$optrev .= "<option value='3'>3</option>";
$optrev .= "<option value='4'>4</option>";
$optrev .= "<option value='5'>5</option>";
$karyawanid = $_SESSION['empl']['karyawanid'];
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select organisasi.kodeorganisasi,organisasi.namaorganisasi from '.$dbname.".organisasi\r\n join datakaryawan on datakaryawan.kodeorganisasi = organisasi.kodeorganisasi        where tipe='PT'\r\n    and datakaryawan.karyawanid = '$karyawanid'    order by organisasi.namaorganisasi desc";
    $res = mysql_query($str);
    $optpt = '';
    $optpt .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $str = 'select organisasi.kodeorganisasi,organisasi.namaorganisasi from '.$dbname.".organisasi join datakaryawan on datakaryawan.kodeorganisasi = organisasi.kodeorganisasi \r\n        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n        or tipe='HOLDING') and datakaryawan.karyawanid = '$karyawanid' and induk!=''\r\n        ";
    $res = mysql_query($str);
    $optgudang = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
    }
} else {
    if ('KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
        $str = 'select organisasi.kodeorganisasi,organisasi.namaorganisasi from '.$dbname.".organisasi\r\n join datakaryawan on datakaryawan.kodeorganisasi = organisasi.kodeorganisasi        where tipe='PT'\r\n    and datakaryawan.karyawanid = '$karyawanid'    order by organisasi.namaorganisasi desc";
        $res = mysql_query($str);
        $optpt = '';
        $optpt .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
        }
        $str = 'select organisasi.kodeorganisasi,organisasi.namaorganisasi from '.$dbname.".organisasi\r\n join datakaryawan on datakaryawan.kodeorganisasi = organisasi.kodeorganisasi        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL') and datakaryawan.karyawanid = '$karyawanid' and induk!=''\r\n        ";
        $res = mysql_query($str);
        $optgudang = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
        }
    } else {
        $optpt = '';
        $optpt .= "<option value='".$_SESSION['empl']['kodeorganisasi']."'>".$_SESSION['empl']['kodeorganisasi'].'</option>';
        $optgudang .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas'].'</option>';
    }
}

echo "<fieldset>\r\n    <legend>".$_SESSION['lang']['neracasaldo']."</legend>\r\n    ".$_SESSION['lang']['pt'].' : '."<select id=pt style='width:200px;'  onchange=ambilAnakBB(this.options[this.selectedIndex].value)>".$optpt."</select>\r\n    ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>\r\n    ".$_SESSION['lang']['periode'].' : '."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n    ".$_SESSION['lang']['tglcutisampai']."\r\n    ".$_SESSION['lang']['periode'].' : '."<select id=periode1 onchange=hideById('printPanel')>".$optper."</select>\r\n    ".$_SESSION['lang']['revisi'].' : '."<select id=revisi onchange=hideById('printPanel')>".$optrev."</select>\r\n    <button class=mybutton onclick=getLaporanBukuBesar()>".$_SESSION['lang']['proses']."</button>\r\n</fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n        <img onclick=fisikKeExcel(event,'keu_laporanBukuBesar_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n        <img onclick=fisikKePDF(event,'keu_laporanBukuBesar_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n    </span>  \r\n    <div style='width:99%;display:fixed'>\r\n    <table class=sortable cellspacing=1 border=0 >\r\n    <thead>\r\n    <tr>\r\n        <td align=center style='width:50px;' rowspan='2'>".$_SESSION['lang']['nomor']."</td>\r\n        <td align=center style='width:80px;' rowspan='2'>".$_SESSION['lang']['noakun']."</td>\r\n        <td align=center style='width:430px;' rowspan='2'>".$_SESSION['lang']['namaakun']."</td>\r\n        <td align=center style='width:130px;' colspan='2'>".$_SESSION['lang']['saldoawal']."</td>\r\n        <td align=center style='width:130px;' rowspan='2'>".$_SESSION['lang']['debet']."</td>\r\n        <td align=center style='width:130px;' rowspan='2'>".$_SESSION['lang']['kredit']."</td>\r\n        <td align=center style='width:130px;' colspan='2'>".$_SESSION['lang']['saldoakhir']."</td>\r\n    </tr>  \r\n  <tr> <td align=center style='width:130px;'>".$_SESSION['lang']['debet']."</td>\r\n        <td align=center style='width:130px;'>".$_SESSION['lang']['kredit']."</td>\r\n        <td align=center style='width:130px;'>".$_SESSION['lang']['debet']."</td>\r\n        <td align=center style='width:130px;'>".$_SESSION['lang']['kredit']."</td>\r\n    </tr>  \r\n    </thead>\r\n    <tbody>\r\n    </tbody>\r\n    <tfoot>\r\n    </tfoot>\t\t \r\n    </table>\r\n    </div>         \r\n    <div style='width:100%;height:475px;overflow:auto;'>\r\n    <table class=sortable cellspacing=1 border=0 style='display:fixed'>\r\n    <thead>\r\n    </thead>\r\n    <tbody id=container>\r\n    </tbody>\r\n    <tfoot>\r\n    </tfoot>\t\t \r\n    </table>\r\n    </div>";
CLOSE_BOX();
close_body();

?>