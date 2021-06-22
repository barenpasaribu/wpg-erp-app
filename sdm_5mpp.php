<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "<script language=javascript1.2 src='js/sdm_5mpp.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['mpp'].':</b>');
$optkodeorg = "<option value=''></option>";
$optkodeorg .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas'].'</option>';
$optbagian = "<option value=''></option>";
$optbagian .= "<option value='".$_SESSION['empl']['bagian']."'>".$_SESSION['empl']['bagian'].'</option>';
$str = 'select * from '.$dbname.'.sdm_5golongan order by kodegolongan';
$res = mysql_query($str);
$optgol = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $gol[$bar->kodegolongan] = $bar->namagolongan;
    $optgol .= "<option value='".$bar->kodegolongan."'>".$bar->namagolongan.'</option>';
}
$str = 'select * from '.$dbname.'.sdm_5jabatan order by kodejabatan';
$res = mysql_query($str);
$optjabatan = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $jab[$bar->kodejabatan] = $bar->namajabatan;
    $optjabatan .= "<option value='".$bar->kodejabatan."'>".$bar->namajabatan.'</option>';
}
$optjeniskelamin = "<option value=''></option>";
$arrenum = getEnum($dbname, 'sdm_5mpp', 'jkelamin');
foreach ($arrenum as $key => $val) {
    $optjeniskelamin .= "<option value='".$key."'>".$val.'</option>';
}
$str = 'select * from '.$dbname.'.sdm_5pendidikan order by idpendidikan';
$res = mysql_query($str);
$optpendidikan = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optpendidikan .= "<option value='".$bar->kelompok."'>".$bar->kelompok.' '.$bar->pendidikan.'</option>';
}
$str = 'select distinct tahunbudget from '.$dbname.'.sdm_5mpp order by tahunbudget desc';
$res = mysql_query($str);
$opttahun = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $opttahun .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
}
echo "<fieldset style='width:700px;'>\r\n    <legend>".$_SESSION['lang']['form'].":</legend>\r\n    <table><tr><td valign=top>\r\n        <table cellspacing=1 border=0 width=700px>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td><input type=text class=myinputtextnumber id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\"></td>\t\r\n            <td>".$_SESSION['lang']['min'].' '.$_SESSION['lang']['umur']."</td>\r\n            <td><input type=text class=myinputtextnumber id=minumur name=minumur onkeypress=\"return angka_doang(event);\" maxlength=3 style=\"width:150px;\"></td>\t\r\n        </tr>\r\n        <tr> \r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td><select id=kodeorg name=kodeorg style=\"width:150px;\">".$optkodeorg."</select></td>\t\r\n            <td>".$_SESSION['lang']['max'].' '.$_SESSION['lang']['umur']."</td>\r\n            <td><input type=text class=myinputtextnumber id=maxumur name=maxumur onkeypress=\"return angka_doang(event);\" maxlength=3 style=\"width:150px;\"></td>\t\r\n        </tr>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['bagian']."</td>\r\n            <td><select id=bagian name=bagian style=\"width:150px;\">".$optbagian."</select></td>\r\n            <td>".$_SESSION['lang']['jeniskelamin']."</td>\r\n            <td><select id=jeniskelamin name=jeniskelamin style=\"width:150px;\">".$optjeniskelamin."</select></td>\t\r\n        </tr>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n            <td><select id=golongan name=golongan style=\"width:150px;\">".$optgol."</select></td>\r\n            <td>".$_SESSION['lang']['pendidikan']."</td>\r\n            <td><select id=pendidikan name=pendidikan style=\"width:150px;\">".$optpendidikan."</select></td>\t\r\n        </tr>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['jabatan']."</td>\r\n            <td><select id=jabatan name=jabatan style=\"width:150px;\">".$optjabatan."</select></td>\r\n            <td>".$_SESSION['lang']['pengalamankerja']."</td>\r\n            <td><input type=text class=myinputtextnumber id=pengalaman name=pengalaman onkeypress=\"return angka_doang(event);\" maxlength=3 style=\"width:150px;\"> ".$_SESSION['lang']['tahun']."</td>\t\r\n        </tr>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['min'].' '.$_SESSION['lang']['gaji']."</td>\r\n            <td><input type=text class=myinputtextnumber id=mingaji name=mingaji onkeypress=\"return angka_doang(event);\" maxlength=12 style=\"width:150px;\"></td>\r\n            <td>".$_SESSION['lang']['poh']."</td>\r\n            <td><input type=text class=myinputtext id=poh name=poh onkeypress=\"return tanpa_kutip();\" maxlength=30 style=\"width:150px;\" /></td>\t\r\n        </tr>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['max'].' '.$_SESSION['lang']['gaji']."</td>\r\n            <td><input type=text class=myinputtextnumber id=maxgaji name=maxgaji onkeypress=\"return angka_doang(event);\" maxlength=12 style=\"width:150px;\"></td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td><input type=text class=myinputtextnumber id=jumlah name=jumlah onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\"> ".$_SESSION['lang']['orang']."</td>\t\r\n        </tr>\r\n        <tr>\r\n            <td>".$_SESSION['lang']['tanggalmasuk']."</td>\r\n            <td><input type=text class=myinputtext id=tanggalmasuk onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" style=\"width:150px;\"></td>\r\n            <td><input type=hidden id=kunci name=kunci value='' /></td>\r\n            <td></td>\r\n        </tr>\r\n        </table>\t  \r\n    </td>\r\n    </tr>\t  \r\n    </table>\t\r\n    <center><button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=batal()>".$_SESSION['lang']['cancel']."</button></center>\r\n    </fieldset>\r\n\t ";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n     ".$_SESSION['lang']['budgetyear'].' : <select onchange=displayList() id=listtahun name=listtahun>'.$opttahun."</select>\r\n     <input type=hidden id=pilihantahun name=pilihantahun value='' />\r\n    <table class=sortable cellspacing=1 border=0 width=100%>\r\n        <thead>\r\n            <tr class=rowheader>\r\n            <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['bagian']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodegolongan']."</td>\r\n            <td align=center>".$_SESSION['lang']['jabatan']."</td>\r\n            <td align=center>".$_SESSION['lang']['min'].' '.$_SESSION['lang']['gaji']."</td>\r\n            <td align=center>".$_SESSION['lang']['max'].' '.$_SESSION['lang']['gaji']."</td>\r\n            <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>\r\n            <td align=center>".$_SESSION['lang']['min'].' '.$_SESSION['lang']['umur']."</td>\r\n            <td align=center>".$_SESSION['lang']['max'].' '.$_SESSION['lang']['umur']."</td>\r\n            <td align=center>".$_SESSION['lang']['jeniskelamin']."</td>\r\n            <td align=center>".$_SESSION['lang']['pendidikan']."</td>\r\n            <td align=center>".$_SESSION['lang']['pengalamankerja']."</td>\r\n            <td align=center>".$_SESSION['lang']['poh']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td colspan=2 align=center>".$_SESSION['lang']['action']."</td>\r\n            </tr>\r\n        </thead>\r\n\t<tbody id=container>";
$str = 'select * from '.$dbname.".sdm_5mpp\r\n      ";
$res = mysql_query($str);
$no = 1;
while ($bar = mysql_fetch_object($res)) {
    echo "<tr class=rowcontent>\r\n    <td>".$no."</td>\r\n    <td>".$bar->tahunbudget."</td>\r\n    <td>".$bar->kodeorg."</td>\r\n    <td>".$bar->departement."</td>\r\n    <td>".$gol[$bar->golongan]."</td>\r\n    <td>".$jab[$bar->jabatan]."</td>\r\n    <td align=right>".number_format($bar->startgaji, 2, '.', ',')."</td>\r\n    <td align=right>".number_format($bar->endgaji, 2, '.', ',')."</td>\r\n    <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n    <td align=right>".$bar->startumur."</td>\r\n    <td align=right>".$bar->endumur."</td>\r\n    <td>".$bar->jkelamin."</td>\r\n    <td>".$bar->pendidikan."</td>\r\n    <td align=right>".$bar->pengalaman."</td>\r\n    <td>".$bar->poh."</td>\r\n    <td align=right>".$bar->jumlah."</td>\r\n\r\n    <td>\r\n        <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edit('".$bar->tahunbudget."','".$bar->kodeorg."','".$bar->departement."','".$bar->golongan."','".$bar->jabatan."','".$bar->startgaji."','".$bar->endgaji."','".tanggalnormal($bar->tanggalmasuk)."',\r\n        '".$bar->startumur."','".$bar->endumur."','".$bar->jkelamin."','".$bar->pendidikan."','".$bar->pengalaman."','".$bar->poh."','".$bar->jumlah."','".$bar->kunci."');\">\r\n    </td>\r\n    <td>\r\n        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"del('".$bar->kunci."');\">\r\n    </td>\r\n    </tr>";
    ++$no;
}
echo "\t\r\n        </tbody>\r\n        <tfoot>\r\n        </tfoot>\r\n    </table>\r\n    </fieldset>";
CLOSE_BOX();
close_body();

?>