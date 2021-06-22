<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include 'lib/zMysql.php';

include_once 'lib/zLib.php';

include_once 'lib/devLibrary.php';

echo open_body();

echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script   language=javascript1.2 src='js/vhc_wo.js'></script>\r\n";

include 'master_mainMenu.php';

OPEN_BOX();

$jam = $mnt = 0;

for ($i = 0; $i < 24; ++$i) {

    if (strlen($i) < 2) {

        $i = '0'.$i;

    }



    $jam .= '<option value='.$i.'>'.$i.'</option>';

}

for ($i = 0; $i < 60; ++$i) {

    if (strlen($i) < 2) {

        $i = '0'.$i;

    }



    $mnt .= '<option value='.$i.'>'.$i.'</option>';

}

$optSebabRusak = "<option value='UMUM'>UMUM</option>";

$optSebabRusak .= "<option value='KECELAKAAN'>KECELAKAAN</option>";

$optTraksi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sGet = selectQuery($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='TRAKSI'");

$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $optTraksi .= '<option value='.$rGet['kodeorganisasi'].'>'.$rGet['namaorganisasi'].'</option>';

}

$hedept = '';

$sGet = sdmJabatanQuery("lower(alias) like '%manager%' or lower(alias) like '%asisten%'  "); //selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "right(lokasitugas,2)='RO' and kodegolongan>='3' and karyawanid<>'0999999999'");

$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $hedept .= '<option value='.$rGet['karyawanid'].'>'.$rGet['karyawanid'].' - ' .$rGet['namakaryawan'].' ('.$rGet['namajabatan'].')</option>';

}

$divmanager = '';

$sGet = sdmJabatanQuery("lower(alias) like '%manager%'   "); //selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and left(kodegolongan,1)>=3 and karyawanid<>'0999999999'");

$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $divmanager .= '<option value='.$rGet['karyawanid'].'>'.$rGet['karyawanid'].' - ' .$rGet['namakaryawan'].' ('.$rGet['namajabatan'].')</option>';

}



$workshop = '';

//$sGet = sdmJabatanQuery("lower(alias) like '%kepala bengkel%'  "); 

$sGet = sdmJabatanQuery("lower(alias) like '%mekanik%' "); 



//selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and left(kodegolongan,1)>=3 and karyawanid<>'0999999999'");



$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $workshop .= '<option value='.$rGet['karyawanid'].'>'.$rGet['karyawanid'].' - ' .$rGet['namakaryawan'].' ('.$rGet['namajabatan'].')</option>';

}



$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$i = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.vhc_wo order by periode desc limit 10';

$j = mysql_query($i);

while ($k = mysql_fetch_assoc($j)) {

    $optPer .= "<option value='".$k['periode']."'>".$k['periode'].'</option>';

}

$optAlat = $optOperator = '';

echo "<fieldset style='width:500px;'>\r\n    <legend>Work Order</legend>\r\n    <table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td><input type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['jam']."</td>\r\n        <td><select id=jam>".$jam.'</select>:<select id=mnt>'.$mnt."</select></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['kodetraksi']."</td>\r\n        <td><select id=kodetraksi style='width:150px;' onchange=getAlat()>".$optTraksi."</select></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['kodealat']."</td>\r\n        <td><select id=kodealat style='width:150px;' onchange=getOperator()>".$optAlat."</select></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['operator']."</td>\r\n        <td><select id=operator style='width:150px;'>".$optOperator."</select></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['posisihm']."</td>\r\n        <td><input type=text id=posisihm value='0' onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['namapelapor']."</td>\r\n        <td><input type=text id=namapelapor onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n    </tr>\r\n    <tr><td valign=top>".$_SESSION['lang']['indikasikerusakan']."</td>\r\n        <td><textarea cols=35 rows=5 id=indikasikerusakan onkeypress=\"return tanpa_kutip(event);\"></textarea></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['penyebabrusak']."</td>\r\n        <td><select id=penyebabrusak style='width:150px;' onchange='cekBA()'>".$optSebabRusak."</select></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['noberitaacara']."</td>\r\n        <td><select id=noberitaacara style='width:150px;'><option value=''></option></select></td>\r\n    </tr>\r\n    

<tr><td>"."Asisten"."</td><td><select id=hedept style='width:150px;'>".$hedept."</select></td>\r\n    </tr>\r\n    

<tr><td>"."Manager"."</td><td><select id=divmanager style='width:150px;'>".$divmanager."</select></td>\r\n    </tr>\r\n    

<tr><td>".$_SESSION['lang']['workshop']."</td><td><select id=workshop style='width:150px;'>".$workshop."</select></td>\r\n    </tr>\r\n    </table>\r\n    <input type=hidden value=insert id=method>\r\n    <input type=hidden value='' id=notransaksi>\r\n    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=batal()>".$_SESSION['lang']['new']."</button>\t \r\n    </table></fieldset>";

CLOSE_BOX();

OPEN_BOX();
echo "<fieldset>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n                ".$_SESSION['lang']['periode'].' : <select id=perSch style="width:100px;" onchange=loadData()>'.$optPer."</select>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";

CLOSE_BOX();

echo close_body();



?>