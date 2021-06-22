<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

echo open_body();

echo "<script language=javascript1.2 src='js/keu_laporan.js'></script>\r\n";

include 'master_mainMenu.php';

OPEN_BOX('', '<b>'.$_SESSION['lang']['daftarHutang'].'/'.$_SESSION['lang']['usiapiutang'].'</b>');

// untuk WPG GROUP

$str = 'select b.noakun, b.namaakun from  '.$dbname.".keu_5akun b \r\n      where detail=1 and (noakun like '113%' or noakun like '114%' or noakun like '2%' or noakun like '118%') order by b.noakun";


// untuk MPS
/*
$str = 'select b.noakun, b.namaakun from  '.$dbname.".keu_5akun b \r\n      where detail=1 and (noakun like '131%' or noakun like '132%' or noakun like '2%' or noakun like '141%' or noakun like '142%' or noakun like '139%') order by b.noakun";
*/
$res = mysql_query($str);

$optnoakun = "<option value=''></option>";

while ($bar = mysql_fetch_object($res)) {

    $optnoakun .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';

}

$str = 'select kodeorganisasi, namaorganisasi from  '.$dbname.".organisasi \r\n where length(kodeorganisasi)=3 AND kodeorganisasi like '".substr($_SESSION['empl']['lokasitugas'], 0, 3)."' order by kodeorganisasi\r\n";

//$optorg .= "<option value=''>".$_SESSION['lang']['all'].'</option>';

$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {

    $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.' - '.$bar->namaorganisasi.'</option>';

}

$str = 'select a.nik, b.namakaryawan from '.$dbname.".keu_jurnaldt_vw a\r\n      left join ".$dbname.".datakaryawan b on a.nik = b.karyawanid\r\n      where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' and a.nik!='0'\r\n      and a.nik != '' and a.noakun != '' group by a.nik order by b.namakaryawan\r\n";

$res = mysql_query($str);

$optnamakaryawan = "<option value=''></option>";

while ($bar = mysql_fetch_object($res)) {

    $optnamakaryawan .= "<option value='".$bar->nik."'>".$bar->namakaryawan.'</option>';

}

echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['laporanjurnal']."</legend>\r\n         ".$_SESSION['lang']['tanggalmulai']." : <input class=\"myinputtext\" id=\"tanggalmulai\" size=\"12\" onmousemove=\"setCalendar(this.id)\" maxlength=\"10\" onkeypress=\"return false;\" type=\"text\" autocomplete='OFF'>\r\n         s/d <input class=\"myinputtext\" id=\"tanggalsampai\" size=\"12\" onmousemove=\"setCalendar(this.id)\" maxlength=\"10\" onkeypress=\"return false;\" type=\"text\" autocomplete='OFF'>\r\n         ".$_SESSION['lang']['noakun'].' <select id=noakun >'.$optnoakun."</select>\r\n         ".$_SESSION['lang']['kodeorg'].' <select id=kodeorg >'.$optorg."</select>    \r\n         <button class=mybutton onclick=getLaporanJurnalPiutangKaryawan()>".$_SESSION['lang']['proses']."</button>\r\n         </fieldset>";

CLOSE_BOX();

OPEN_BOX('', 'Result:');

echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=piutangKaryawanKeExcel(event,'keu_laporanJurnalPiutangKaryawan_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'>\r\n         </span>    \r\n         <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n             <thead>\r\n                    <tr>\r\n                          <td align=center width=50>".$_SESSION['lang']['nourut']."</td>\r\n                          <td align=center>".$_SESSION['lang']['organisasi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['karyawan'].'/'.$_SESSION['lang']['supplier']."</td>\r\n                          <td align=center>".$_SESSION['lang']['saldoawal']."</td>                             \r\n                          <td align=center>".$_SESSION['lang']['debet']."</td>\r\n                          <td align=center>".$_SESSION['lang']['kredit']."</td>\r\n                          <td align=center>".$_SESSION['lang']['saldoakhir']."</td>                               \r\n                        </tr>  \r\n                 </thead>\r\n                 <tbody id=container>\r\n                 </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\t\t \r\n           </table>\r\n     </div>";

CLOSE_BOX();

close_body();



?>