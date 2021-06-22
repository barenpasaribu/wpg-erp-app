<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include 'lib/zMysql.php';

include 'lib/zFunction.php';

echo open_body();

echo "\r\n<script language=javascript1.2 src='js/vhc.js'></script>\r\n";

include 'master_mainMenu.php';

OPEN_BOX('', '<b>'.$_SESSION['lang']['datamesinkendaraan'].'</b>');

$optklvhc = "<option value=''></option>";

$arrklvhc = getEnum($dbname, 'vhc_5master', 'kelompokvhc');

foreach ($arrklvhc as $kei => $fal) {

    switch ($kei) {

        case 'AB':

            ('EN' !== $_SESSION['language'] ? ($fal = 'Alat Berat') : ($fal = 'Heavy Equipment'));



            break;

        case 'KD':

            ('EN' !== $_SESSION['language'] ? ($fal = 'Kendaraan') : ($fal = 'Vehicle'));



            break;

        case 'MS':

            ('EN' !== $_SESSION['language'] ? ($fal = 'Mesin') : ($fal = 'Machinery'));



            break;

    }

    $optklvhc .= "<option value='".$kei."'>".$fal.'</option>';

}

$str = 'select * from '.$dbname.'.vhc_5jenisvhc order  by namajenisvhc';

$res = mysql_query($str);

$optjnsvhc = "<option value=''></option>";

while ($bar = mysql_fetch_object($res)) {

    $optjnsvhc .= "<option value='".$bar->jenisvhc."'>".$bar->namajenisvhc.'</option>';

}

//$str = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kelompokbarang in ('905','906','907','909','910') order by namabarang";

$str = "select kodebarang,namabarang from $dbname.log_5masterbarang m inner join $dbname.log_5klbarang k on k.kode=m.kelompokbarang where lower(k.alias)='vhc' order by namabarang";

$res = mysql_query($str);

$optbarang = '';

while ($bar = mysql_fetch_object($res)) {

    $optbarang .= "<option value='".$bar->kodebarang."'>".$bar->namabarang.' - ('.$bar->kodebarang.')</option>';

}

$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='TRAKSI' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi desc";

$res = mysql_query($str);

$opttraksi = '';

while ($bar = mysql_fetch_object($res)) {

    $opttraksi .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';

}

$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe  in('KANWIL','HOLDING','KEBUN','PABRIK','TRAKSI') and (length(kodeorganisasi)=4||length(kodeorganisasi)=6) order  by namaorganisasi";

$res = mysql_query($str);

$optorg = "<option value=''></option>";

while ($bar = mysql_fetch_object($res)) {

    $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';

}

$optkepemilikan = ' <option value=1>'.$_SESSION['lang']['miliksendiri']."</option>\r\n                  <option value=0>".$_SESSION['lang']['sewa'].'</option>';

echo "<fieldset><table>\r\n    <tr><td>".$_SESSION['lang']['kodekelompok'].'</td><td><select id=kelompokvhc onchange=loadJenis(this.options[this.selectedIndex].value)>'.$optklvhc."</select></td>\r\n        <td>".$_SESSION['lang']['jenkendabmes'].'</td><td><select id=jenisvhc onchange=getList()>'.$optjnsvhc."</select></td>\r\n        <td>".$_SESSION['lang']['tglakhirstnk']."</td><td><input type=text class=myinputtext id=tglakhirstnk name=tglakhirstnk onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td>\r\n</tr>\r\n    <tr><td>".$_SESSION['lang']['kodeorganisasi'].'(Owner)</td><td><select id=kodeorg onchange=getList()>'.$optorg."</select></td>\r\n        <td>".$_SESSION['lang']['kodenopol']."</td><td><input type=text id=kodevhc size=12 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\" class=myinputtext maxlength=20></td>\r\n        <td>".$_SESSION['lang']['tglakhirkir']."</td><td><input type=text class=myinputtext id=tglakhirkir name=tglakhirkir onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td>\r\n</tr>\r\n    <tr><td>".$_SESSION['lang']['namabarang']."</td><td><select id=kodebarang onchange style='width:200px'>".$optbarang."</select></td>\r\n        <td>".$_SESSION['lang']['tahunperolehan']."</td><td><input type=text id=tahunperolehan size=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4></td>\r\n        <td>".$_SESSION['lang']['tglakhirijinbongkar']."</td><td><input type=text class=myinputtext id=tglakhirijinbm name=tglakhirijinbm onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td>\r\n</tr>\r\n    <tr><td></td><td></td>\r\n        <td>".$_SESSION['lang']['beratkosong']."</td><td><input type=text id=beratkosong size=5 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=5>Kg.</td>\r\n        <td>".$_SESSION['lang']['tglakhirijinangkut']."</td><td><input type=text class=myinputtext id=tglakhirijinang name=tglakhirijinang onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td>\r\n </tr>\r\n    <tr><td>".$_SESSION['lang']['nomorrangka']."</td><td><input type=text id=nomorrangka size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtextnumber maxlength=45></td>\r\n        <td>".$_SESSION['lang']['nomormesin']."</td><td><input type=text id=nomormesin size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtextnumber maxlength=45></td>\r\n        <td>".$_SESSION['lang']['kodeorganisasi'].'(lokasi)</td><td><select id=kodelokasi>'.$optorg."</select></td>            \r\n\r\n</tr>\r\n    <tr><td rowspan=2>".$_SESSION['lang']['tmbhDetail']."</td><td rowspan=2><textarea id=detailvhc cols=25 rows=2 onkeypress=\"return tanpa_kutip(event);\" maxlength=255></textarea></td>\r\n        <td valign=top>".$_SESSION['lang']['kepemilikan'].'</td><td valign=top><select id=kepemilikan>'.$optkepemilikan."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kodetraksi'].'</td><td><select id=kodetraksi>'.$opttraksi."</select></td>\r\n    </tr>\r\n    </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanMasterVhc()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelMasterVhc()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";

echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n    <img onclick=dataKeExcel(event,'vhc_slave_save_vhc_excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n    <div style='width:95%;height:225px;overflow:scroll;'>";

$str1 = 'select * from '.$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' \r\n               order by status desc,kodeorg,kodevhc asc";

$res1 = mysql_query($str1);

echo "<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t  <td>No</td>\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['kodeorganisasi'])."(owner)</td>\t\t \r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['kodekelompok'])."</td>\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['jenkendabmes'])."</td>\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['kodenopol'])."</td>\t\t\r\n                   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['namabarang'])."</td>\t\t\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['tahunperolehan'])."</td>\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['nomormesin'])."</td>\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['detail'])."</td>\t   \r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['kepemilikan'])."</td>\r\n\t\t   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['kodetraksi'])."</td>\r\n                   <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['kodeorganisasi'])."(lokasi)</td>\t\t \r\n                  <td>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";

$no = 0;

while ($bar1 = mysql_fetch_object($res1)) {

    ++$no;

    $str = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$bar1->kodebarang."'";

    $res = mysql_query($str);

    $namabarang = '';

    while ($bar = mysql_fetch_object($res)) {

        $namabarang = $bar->namabarang;

    }

    if (1 == $bar1->kepemilikan) {

        $dptk = $_SESSION['lang']['miliksendiri'];

    } else {

        $dptk = $_SESSION['lang']['sewa'];

    }



    $sttd = '';

    $sttd = 'Deactivate';

    $bgcrcolor = "class='rowcontent'";

    if ('0' == $bar1->status) {

        $bgcrcolor = 'bgcolor=orange';

        $sttd = '';

        $sttd = 'Actived';

    }



    $clidt = " style='cursor:pointer' title='".$sttd.' '.$bar1->kodevhc."' onclick=deAktif('".$bar1->kodevhc."','".$bar1->status."')";

    echo '<tr '.$bgcrcolor.">\r\n\t\t     <td  ".$clidt.'  >'.$no."</td>\r\n\t\t     <td  ".$clidt.'  >'.$bar1->kodeorg."</td>\r\n\t\t\t <td  ".$clidt.'  >'.$bar1->kelompokvhc."</td>\t\t\t\t \r\n\t\t\t <td  ".$clidt.'  >'.$bar1->jenisvhc."</td>\t\t\t \t\t\r\n\t\t\t <td  ".$clidt.'  >'.$bar1->kodevhc."</td>\r\n\t\t\t <td  ".$clidt.'  >'.$namabarang."</td>\r\n\t\t\t <td  ".$clidt.'  >'.$bar1->tahunperolehan."</td>\r\n\t\t\t <input type=hidden value=".$bar1->beratkosong.">\t\t\r\n\t\t\t <input type=hidden value=".$bar1->nomorrangka.">\r\n\t\t\t <td  ".$clidt.'  >'.$bar1->nomormesin."</td> \r\n\t\t\t <td>".$bar1->detailvhc."</td> \t\r\n\t\t\t <td  ".$clidt.'  >'.$dptk."</td>\r\n                         <td  ".$clidt.'  >'.$bar1->kodetraksi."</td>\r\n                         <td  ".$clidt.'  >'.$bar1->kodelokasi."</td>\r\n\t\t\t <td>\r\n\t\t\t     <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillMasterField('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."','".$bar1->beratkosong."','".$bar1->nomorrangka."','".$bar1->nomormesin."','".$bar1->tahunperolehan."','".$bar1->kodebarang."','".$bar1->kepemilikan."','".$bar1->kodetraksi."','".tanggalnormal($bar1->tglakhirstnk)."','".tanggalnormal($bar1->tglakhirkir)."','".tanggalnormal($bar1->tglakhirijinbm)."','".tanggalnormal($bar1->tglakhirijinang)."','".$bar1->kodelokasi."','".$bar1->detailvhc."');\">\r\n\t\t\t     <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"deleteMasterVhc('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."');\">\r\n\t\t\t</td></tr>";

}

echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div></fieldset>";

CLOSE_BOX();

echo close_body();



?>