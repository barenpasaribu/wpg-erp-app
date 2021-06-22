<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body('txtsearch');
echo "\r\n<script language=javascript1.2 src='js/datakaryawan.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '');
$sReg = 'select distinct regional from ' . $dbname . ".bgt_regional_assignment where kodeunit='" . $_SESSION['empl']['lokasitugas'] . "'";
$qReg = mysql_query($sReg);
$rReg = mysql_fetch_assoc($qReg);
$optlokasitugas = "<option value='0'></option>";
$optsubbagian = "<option value='0'></option>";
$saveable = '';
$str = 'select 1=1';
if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    $str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where tipe not in('BLOK','PT','STENGINE','STATION')\r\n              and length(kodeorganisasi)=4 order by namaorganisasi desc";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optlokasitugas .= "<option value='" . $bar->kodeorganisasi . "'>" . $bar->namaorganisasi . '</option>';
    }
    $optsubbagian = "<option value='0'></option>";
    $stdy = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where tipe not in('PT','BLOK','GUDANG','WORKSHOP','STENGINE')";
    $redy = mysql_query($stdy);
    while ($bardy = mysql_fetch_object($redy)) {
        $optsubbagian .= "<option value='" . $bardy->kodeorganisasi . "'>" . $bardy->namaorganisasi . '</option>';
    }
} else {
    if ('KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
        $str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where tipe not in('BLOK','PT','STENGINE','STATION')\r\n                   and length(kodeorganisasi)=4\r\n          and (kodeorganisasi in (select distinct kodeunit from " . $dbname . ".bgt_regional_assignment where regional='" . $rReg['regional'] . "')\r\n          or induk in (select distinct kodeunit from " . $dbname . ".bgt_regional_assignment where regional='" . $rReg['regional'] . "'))\r\n          order by kodeorganisasi asc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $optlokasitugas .= "<option value='" . $bar->kodeorganisasi . "'>" . $bar->namaorganisasi . '</option>';
        }
        $optsubbagian = "<option value='0'></option>";
        $stdy = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where tipe not in('PT','BLOK','WORKSHOP','STENGINE')\r\n                   and (kodeorganisasi in (select distinct kodeunit from " . $dbname . ".bgt_regional_assignment where regional='" . $rReg['regional'] . "')\r\n                   or induk in (select distinct kodeunit from " . $dbname . ".bgt_regional_assignment where regional='" . $rReg['regional'] . "'))\r\n                   order by kodeorganisasi asc";
        $redy = mysql_query($stdy);
        while ($bardy = mysql_fetch_object($redy)) {
            $optsubbagian .= "<option value='" . $bardy->kodeorganisasi . "'>" . $bardy->namaorganisasi . '</option>';
        }
    } else {
        if (trim('' != $_SESSION['org']['induk'])) {
            $str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where LENGTH(kodeorganisasi)=4\r\n        and kodeorganisasi  like '" . $_SESSION['empl']['lokasitugas'] . "%' order by namaorganisasi desc";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $optlokasitugas .= "<option value='" . $bar->kodeorganisasi . "'>" . $bar->namaorganisasi . '</option>';
            }
            $optsubbagian = "<option value='0'></option>";
            $stdy = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where tipe in('AFDELING','TRAKSI','GUDANG','WORKSHOP','BIBITAN','STATION','SIPIL') and kodeorganisasi like '" . $_SESSION['empl']['lokasitugas'] . "%'";
            $redy = mysql_query($stdy);
            while ($bardy = mysql_fetch_object($redy)) {
                $optsubbagian .= "<option value='" . $bardy->kodeorganisasi . "'>" . $bardy->namaorganisasi . '</option>';
            }
        } else {
            $saveable = 'disabled';
            echo "<script>\r\n        alert('You are not authorized');\r\n       </script>";
        }
    }
}

$str = 'select kode,keterangan from ' . $dbname . '.sdm_5catuporsi order by kode';
$res = mysql_query($str);
$optCatu = '<option value=0>Tidak dapat catu</option>';
while ($bar = mysql_fetch_object($res)) {
    $optCatu .= "<option value='" . $bar->kode . "'>" . $bar->kode . '-' . $bar->keterangan . '</option>';
}
$opttipekaryawan = '';
if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas']) || 'KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
    $str = 'select * from ' . $dbname . '.sdm_5tipekaryawan order by tipe asc';
} else {
    $str = 'select * from ' . $dbname . '.sdm_5tipekaryawan where id<>0 order by tipe asc';
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttipekaryawan .= "<option value='" . $bar->id . "'>" . $bar->tipe . '</option>';
}
echo "<table>\r\n     <tr valign=middle>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>\r\n           <img class=delliconBig src=images/user_add.png title='" . $_SESSION['lang']['new'] . "'><br>" . $_SESSION['lang']['new'] . "</td>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n           <img class=delliconBig src=images/orgicon.png title='" . $_SESSION['lang']['list'] . "'><br>" . $_SESSION['lang']['list'] . "</td>\r\n         <td><fieldset><legend>" . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['caripadanama'] . ':<input type=text id=txtsearch size=20 maxlength=30 onkeypress="return validat(event);" class=myinputtext>';
echo $_SESSION['lang']['lokasitugas'] . ":<select id=schorg style='width:100px' onchange=changeCaption(this.options[this.selectedIndex].text);><option value=''>" . $_SESSION['lang']['all'] . '</option>' . $optsubbagian . '</select>';
echo $_SESSION['lang']['tipekaryawan'] . ":<select id=schtipe  style='width:80px' onchange=changeCaption1(this.options[this.selectedIndex].text);><option value=''>" . $_SESSION['lang']['all'] . '</option>' . $opttipekaryawan . '</select>';
echo $_SESSION['lang']['status'] . ":<select id=schstatus  style='width:80px' onchange=changeCaption1(this.options[this.selectedIndex].text);><option value=''>" . $_SESSION['lang']['all'] . "</option><option value='0000-00-00'>" . $_SESSION['lang']['aktif'] . "</option><option value='*'>" . $_SESSION['lang']['tidakaktif'] . '</select>';
echo $_SESSION['lang']['nik'] . ':<input type=text id=niksch size=10 maxlength=10 onkeypress="return validat(event);" class=myinputtext>';
echo '<button class=mybutton onclick=cariKaryawan(1)>' . $_SESSION['lang']['find'] . '</button>';
echo "</fieldset></td>\r\n     </tr>\r\n         </table> ";
CLOSE_BOX();
OPEN_BOX('', '');
echo "<div id='frminput'>\r\n    <b>" . $_SESSION['lang']['input'] . ' ' . $_SESSION['lang']['data'] . '</b>';
$optagama = '';
$arragama = getEnum($dbname, 'datakaryawan', 'agama');
foreach ($arragama as $kei => $fal) {
    $optagama .= "<option value='" . $kei . "'>" . $fal . '</option>';
}
$optbagian = '';
$str = 'select * from ' . $dbname . '.sdm_5departemen order by kode';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optbagian .= "<option value='" . $bar->kode . "'>" . $bar->nama . '</option>';
}
$optjabatan = '';
$str = 'select * from ' . $dbname . ".sdm_5jabatan where namajabatan not like '%available' order by namajabatan";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optjabatan .= "<option value='" . $bar->kodejabatan . "'>" . $bar->namajabatan . '</option>';
}
$optgolongan = '';
$str = 'select * from ' . $dbname . '.sdm_5golongan order by kodegolongan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optgolongan .= "<option value='" . $bar->kodegolongan . "'>" . $bar->namagolongan . '</option>';
}
$country = readCountry('./config/country.lst');
$optCountry = '';
for ($x = 0; $x < count($country); ++$x) {
    $optCountry .= "<option value='" . $country[$x][2] . "' >" . $country[$x][0] . '</option>';
}
$country = readCountry('./config/provinsi.lst');
$optProvinsi = '';
for ($x = 0; $x < count($country); ++$x) {
    $optProvinsi .= "<option value='" . $country[$x][1] . "' >" . $country[$x][0] . '</option>';
}
$optstatuspajak = '';
$str = 'select * from ' . $dbname . '.sdm_5statuspajak kode';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('EN' == $_SESSION['language']) {
        switch ($bar->kode) {
            case 'K0':
                $bar->nama = 'Married without children';

                break;
            case 'K1':
                $bar->nama = 'Married 1 children';

                break;
            case 'K2':
                $bar->nama = 'Married 2 children';

                break;
            case 'K3':
                $bar->nama = 'Married 3 children';

                break;
            default:
                $bar->nama = 'Single';

                break;
        }
    }

    $optstatuspajak .= "<option value='" . $bar->kode . "'>" . $bar->nama . '</option>';
}
$optGoldar = '';
$arrenum = getEnum($dbname, 'datakaryawan', 'golongandarah');
foreach ($arrenum as $key => $val) {
    $optGoldar .= "<option value='" . $key . "'>" . $val . '</option>';
}
$optorganisasi = '';
$str = 'select 1=1';
if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    $str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . ".organisasi where tipe='PT' order by namaorganisasi desc";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optorganisasi .= "<option value='" . $bar->kodeorganisasi . "'>" . $bar->namaorganisasi . '</option>';
    }
} else {
    if ('KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
        $str = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi in (select distinct kodeunit from ' . $dbname . ".bgt_regional_assignment where regional='" . $rReg['regional'] . "')\r\n                          order by namaorganisasi desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $sNama = 'select distinct namaorganisasi from ' . $dbname . ".organisasi where kodeorganisasi='" . $bar->induk . "'";
            $qNama = mysql_query($sNama);
            $rNama = mysql_fetch_object($qNama);
            $optorganisasi .= "<option value='" . $bar->induk . "'>" . $rNama->namaorganisasi . '</option>';
        }
    } else {
        if ('' != trim($_SESSION['org']['induk'])) {
            $optorganisasi = "<option value='" . trim($_SESSION['org']['kodeorganisasi']) . "'>" . $_SESSION['org']['namaorganisasi'] . '</option>';
        }
    }
}

$optJK = '';
$arrenum = getEnum($dbname, 'datakaryawan', 'jeniskelamin');
foreach ($arrenum as $key => $val) {
    $optJK .= "<option value='" . $key . "'>" . $val . '</option>';
}
$optsisgaji = '';
$arrsgaj = getEnum($dbname, 'datakaryawan', 'sistemgaji');
foreach ($arrsgaj as $kei => $fal) {
    if ('EN' == $_SESSION['language'] && 'Harian' == $fal) {
        $fal = 'Daily';
    }

    if ('EN' == $_SESSION['language'] && 'Bulanan' == $fal) {
        $fal = 'Monthly';
    }

    $optsisgaji .= "<option value='" . $kei . "'>" . $fal . '</option>';
}
$optstkawin = '';
$arrsstk = getEnum($dbname, 'datakaryawan', 'statusperkawinan');
foreach ($arrsstk as $kei => $fal) {
    if ('EN' == $_SESSION['language'] && 'Menikah' == $fal) {
        $fal = 'Married';
    }

    if ('EN' == $_SESSION['language'] && 'Janda' == $fal) {
        $fal = 'Widow';
    }

    if ('EN' == $_SESSION['language'] && 'Duda' == $fal) {
        $fal = 'Widower';
    }

    if ('EN' == $_SESSION['language'] && 'Lajang' == $fal) {
        $fal = 'Single';
    }

    $optstkawin .= "<option value='" . $kei . "'>" . $fal . '</option>';
}
$optlvlpendidikan = '';
$str = 'select * from ' . $dbname . '.sdm_5pendidikan order by levelpendidikan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optlvlpendidikan .= "<option value='" . $bar->levelpendidikan . "'>" . $bar->kelompok . '</option>';
}
$frm[0] = '<fieldset><legend>' . $_SESSION['lang']['inputdatakaryawan'] . "</legend>\r\n         <table border=0 cellspacing=1>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['nik'] . "</td><td><input type=text class=myinputtext id=nik size=26 maxlength=10 onblur=cekNik() onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['employeename'] . "</td><td><input type=text class=myinputtext id=namakaryawan size=26 maxlength=40 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                        <td align=right>" . $_SESSION['lang']['tempatlahir'] . "</td><td><input type=text class=myinputtext id=tempatlahir size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['tanggallahir'] . "</td><td><input type=text class=myinputtext id=tanggallahir size=26 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['jeniskelamin'] . "</td><td><select id=jeniskelamin  style='width:150px;'>" . $optJK . "</select></td>\r\n                        <td align=right>" . $_SESSION['lang']['agama'] . "</td><td><select id=agama style='width:150px;'>" . $optagama . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['departemen'] . "</td><td><select id=bagian style='width:150px;'>" . $optbagian . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['kodejabatan'] . "</td><td><select id=kodejabatan style='width:150px;'>" . $optjabatan . "</select></td>\r\n                        <td align=right>" . $_SESSION['lang']['levelname'] . "</td><td><select id=kodegolongan style='width:150px;'>" . $optgolongan . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['lokasitugas'] . "</td><td><select onchange=getSub() id=lokasitugas style='width:150px;'>" . $optlokasitugas . "</select><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['pt'] . "</td><td><select id=kodeorganisasi style='width:150px;'>" . $optorganisasi . "</select></td>\r\n                        <td align=right>" . $_SESSION['lang']['tipekaryawan'] . "</td><td><select id=tipekaryawan style='width:150px;'>" . $opttipekaryawan . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['noktp'] . "</td><td><input type=text class=myinputtext id=noktp size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>No." . $_SESSION['lang']['passport'] . "</td><td><input type=text class=myinputtext id=nopassport size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['warganegara'] . "</td><td><select id=warganegara style='width:150px;'>" . $optCountry . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n\t\t\t\t  <td align=right>" . $_SESSION['lang']['lokasipenerimaan'] . "</td><td><input type=text class=myinputtext id=lokasipenerimaan size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n\r\n\t\t\t\t    <td align=right>" . $_SESSION['lang']['statuspajak'] . "</td><td><select id=statuspajak style='width:150px;'>" . $optstatuspajak . "</select></td>\r\n                        <td align=right>" . $_SESSION['lang']['npwp'] . "</td><td><input type=text id=npwp size=26 maxlength=30 class=myinputtext onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right rowspan=2>" . $_SESSION['lang']['alamataktif'] . "</td><td rowspan=2><textarea id=alamataktif cols=16 rows=2></textarea><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['kota'] . "</td><td><input type=text class=myinputtext id=kota size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                        <td align=right>" . $_SESSION['lang']['province'] . "</td><td><select id=provinsi style='width:150px;'>" . $optProvinsi . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['kodepos'] . "</td><td><input type=text class=myinputtext id=kodepos size=26 maxlength=5 onkeypress=\"return angka_doang(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['telp'] . "</td><td><input type=text class=myinputtext id=noteleponrumah size=26 maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['nohp'] . "</td><td><input type=text class=myinputtext id=nohp size=26 maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['norekeningbank'] . "</td><td><input type=text class=myinputtext id=norekeningbank size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['namabank'] . "</td><td><input type=text class=myinputtext id=namabank size=26 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['sistemgaji'] . "</td><td><select id=sistemgaji style='width:150px;'>" . $optsisgaji . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['golongandarah'] . "</td><td><select id=golongandarah style='width:150px;'>" . $optGoldar . "</select></td>\r\n                        <td align=right>" . $_SESSION['lang']['tanggalmasuk'] . "</td><td><input type=text class=myinputtext id=tanggalmasuk size=26 maxlength=10 onkeypress=\"return false;\" onmousemove=setCalendar(this)><img src=images/obl.png title='Obligatory'></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['tanggalkeluar'] . "</td><td><input type=text class=myinputtext id=tanggalkeluar size=26 maxlength=10 onkeypress=\"return false;\" onmousemove=setCalendar(this) onblur=cekKeluar()></td>\r\n                    <td align=right>" . $_SESSION['lang']['statusperkawinan'] . "</td><td><select id=statusperkawinan style='width:150px;'>" . $optstkawin . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['tanggalmenikah'] . "</td><td><input type=text class=myinputtext id=tanggalmenikah size=26 maxlength=10 onkeypress=\"return false;\" onmousemove=setCalendar(this)></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['jumlahanak'] . "</td><td><input type=text class=myinputtext id=jumlahanak size=26 maxlength=2 onkeypress=\"return angka_doang(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['jumlahtanggungan'] . "</td><td><input type=text class=myinputtext id=jumlahtanggungan size=26 maxlength=2  onkeypress=\"return angka_doang(event);\"></td>\r\n         <td align=right>Natura</td><td><select id='natura'>".$optCatu."</select></td>            \r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['levelpendidikan'] . "</td><td><select id=levelpendidikan style='width:150px;'>" . $optlvlpendidikan . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['notelepondarurat'] . "</td><td><input type=text class=myinputtext id=notelepondarurat size=26 maxlength=15  onkeypress=\"return tanpa_kutip(event);\"></td>\r\n              <td align=right>" . $_SESSION['lang']['alokasibiaya'] . '</td><td><select id=alokasi><option value=0>Unit</option><option value=1>' . $_SESSION['lang']['ho'] . "</option></select></td>      \r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>Sub Lokasi Tugas</td><td><select id=subbagian style='width:150px;'>" . $optsubbagian . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['jms'] . "</td><td><input type=text class=myinputtext id=jms size=26 maxlength=30  onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n  <td align=right>" . $_SESSION['lang']['email'] . "</td><td><input type=text class=myinputtext id=email onblur=emailCheck(this.value) size=26 maxlength=45  onkeypress=\"return tanpa_kutip(event);\"></td>                  \r\n                 </tr>\r\n\t\t\t\t <tr>\r\n                    <td align=right>" . $_SESSION['lang']['desa'] . "</td><td><input type=text class=myinputtext id=desa size=26 maxlength=45  onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['bpjskes'] . "</td><td><input type=text class=myinputtext id=bpjskes size=26 maxlength=45  onkeypress=\"return tanpa_kutip(event);\"><input type=hidden class=myinputtext id=pangkat size=26 maxlength=45  onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['kecamatan'] . "</td><td><input type=text class=myinputtext id=kecamatan size=26 maxlength=45  onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n\t\t\t\t\t<td align=right></td><td><input type=hidden id=catu style='width:150px;' /></td>\r\n                    <td align=right></td><td><input type=hidden id=dptPremi /></td>\r\n                    <td align=right>&nbsp;</td><td>&nbsp;</td>\r\n                 </tr>\r\n                 <input type=hidden id=karyawanid value=''>\r\n                 <input type=hidden id=method value='insert'>\r\n   <td align=right>Data duplikat</td><td><input type=checkbox id=isduplicate /></td>              </table>\r\n                 </fieldset>\r\n                 <button " . $saveable . ' class=mybutton onclick=simpanKaryawan()>' . $_SESSION['lang']['save'] . "</button>\r\n                 <button " . $saveable . ' class=mybutton onclick=cancelDataKaryawan()>' . $_SESSION['lang']['new'] . "</button>\r\n                ";
$optbln = "<option value=''>" . $_SESSION['lang']['bulan'] . '</option>';
for ($x = 1; $x < 13; ++$x) {
    if ($x < 10) {
        $bln = '0' . $x;
    } else {
        $bln = $x;
    }

    $optbln .= "<option value='" . $bln . "'>" . $bln . '</option>';
}
$optthn = "<option value=''>" . $_SESSION['lang']['tahun'] . '</option>';
for ($x = 0; $x < 60; ++$x) {
    $thn = date('Y') - $x;
    $optthn .= "<option value='" . $thn . "'>" . $thn . '</option>';
}
$frm[1] = '<fieldset><legend>' . $_SESSION['lang']['pengalamankerja'] . "</legend>\r\n         <table border=0 cellspacing=1>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['orgname'] . "</td><td><input type=text class=myinputtext id=namaperusahaan size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['bidangusaha'] . "</td><td><input type=text class=myinputtext id=bidangusaha size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['bulanmasuk'] . "</td><td><select id=blnmasuk style='width:85px;'>" . $optbln . "</select>-<select id=thnmasuk style='width:85px;'>" . $optthn . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['bulankeluar'] . "</td><td><select id=blnkeluar style='width:85px;'>" . $optbln . "</select>-<select id=thnkeluar style='width:85px;'>" . $optthn . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['jabatanterakhir'] . "</td><td><input type=text class=myinputtext id=pengalamanjabatan size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['bagian'] . "</td><td><input type=text class=myinputtext id=pengalamanbagian size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['alamat'] . "</td><td colspan=3><input type=text class=myinputtext id=pengalamanalamat size=89 maxlength=100 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 </table>\r\n                 </fieldset>\r\n                 <button id=btncv disabled class=mybutton onclick=simpanPengalaman()>" . $_SESSION['lang']['save'] . "</button>\r\n                <br>\r\n                <div style='width:100%;height:250px;overflow:scroll;'>\r\n                <table class=sortable border=0 cellspacing=1 width=100%>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                          <td class=firsttd>No.</td>\r\n                          <td>" . $_SESSION['lang']['orgname'] . "</td>\r\n                          <td>" . $_SESSION['lang']['bidangusaha'] . "</td>\r\n                          <td>" . $_SESSION['lang']['bulanmasuk'] . "</td>\r\n                          <td>" . $_SESSION['lang']['bulankeluar'] . "</td>\r\n                          <td>" . $_SESSION['lang']['jabatanterakhir'] . "</td>\r\n                          <td>" . $_SESSION['lang']['bagian'] . "</td>\r\n                          <td>" . $_SESSION['lang']['masakerja'] . "</td>\r\n                          <td>" . $_SESSION['lang']['alamat'] . "</td>\r\n                          <td></td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody id=container>\r\n                        </tbody>\r\n                        <tfoot>\r\n                        </tfoot>\r\n                </table>\r\n                </div>\r\n                ";
$str = 'select kelompok,levelpendidikan from ' . $dbname . '.sdm_5pendidikan order by levelpendidikan';
$res = mysql_query($str);
$optpendidikan = '';
while ($bar = mysql_fetch_object($res)) {
    $optpendidikan .= "<option value='" . $bar->levelpendidikan . "'>" . $bar->kelompok . '</option>';
}
$frm[2] = '<fieldset><legend>' . $_SESSION['lang']['educationentry'] . "</legend>\r\n         <table border=0 cellspacing=1>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['edulevel'] . "</td><td><select id=levelpendidikan2 style='width:170px;'>" . $optpendidikan . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['jurusan'] . "</td><td><input type=text class=myinputtext id=spesialisasi size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['gelar'] . "</td><td><input type=text class=myinputtext id=gelar size=30 maxlength=20 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['tahunlulus'] . "</td><td><select id=tahunlulus style='width:170px;'>" . $optthn . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['namasekolah'] . "</td><td><input type=text class=myinputtext id=namasekolah size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                        <td align=right>" . $_SESSION['lang']['nilai'] . "</td><td><input type=text class=myinputtextnumber id=nilai size=30 maxlength=4 onkeypress=\"return angka_doang(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['kota'] . "</td><td><input type=text class=myinputtext id=pendidikankota size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['keterangan'] . "</td><td><input type=text class=myinputtextnumber id=pendidikanketerangan size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 </table>\r\n                 </fieldset>\r\n                 <button id=btnpendidikan disabled class=mybutton onclick=simpanPendidikan()>" . $_SESSION['lang']['save'] . "</button>\r\n                <br>\r\n                <div style='width:100%;height:250px;overflow:scroll;'>\r\n                <table class=sortable border=0 cellspacing=1 width=100%>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                          <td>No.</td>\r\n                          <td>" . $_SESSION['lang']['edulevel'] . "</td>\r\n                          <td>" . $_SESSION['lang']['namasekolah'] . "</td>\r\n                          <td>" . $_SESSION['lang']['kota'] . "</td>\r\n                          <td>" . $_SESSION['lang']['jurusan'] . "</td>\r\n                          <td>" . $_SESSION['lang']['tahunlulus'] . "</td>\r\n                          <td>" . $_SESSION['lang']['gelar'] . "</td>\r\n                          <td>" . $_SESSION['lang']['nilai'] . "</td>\r\n                          <td>" . $_SESSION['lang']['keterangan'] . "</td>\r\n                          <td></td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody id=containerpendidikan>\r\n                        </tbody>\r\n                        <tfoot>\r\n                        </tfoot>\r\n                </table>\r\n                </div>\r\n                ";
$frm[3] = '<fieldset><legend>' . $_SESSION['lang']['kursus'] . "</legend>\r\n         <table border=0 cellspacing=1>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['jeniskursus'] . "</td><td><input type=text class=myinputtext id=jenistraining size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['legend'] . "</td><td><input type=text class=myinputtext id=judultraining size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['biaya'] . "</td><td>Rp.<input type=text class=myinputtextnumber id=biaya value=0 size=12 maxlength=15 onkeypress=\"return angka_doang(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['bulanmasuk'] . "</td><td><select id=trainingblnmulai style='width:85px;'>" . $optbln . "</select>-<select id=trainingthnmulai style='width:85px;'>" . $optthn . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['bulankeluar'] . "</td><td><select id=trainingblnselesai style='width:85px;'>" . $optbln . "</select>-<select id=trainingthnselesai style='width:85px;'>" . $optthn . "</select></td>\r\n                    <td></td><td></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['penyelenggara'] . "</td><td><input type=text class=myinputtext id=penyelenggara size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                        <td align=right>" . $_SESSION['lang']['sertifikat'] . "</td><td><select id=sertifikat style='width:170px;'><option value=0>" . $_SESSION['lang']['no'] . '</option><option value=1>' . $_SESSION['lang']['yes'] . "</option></select></td>\r\n                        <td></td><td></td>\r\n                 </tr>\r\n                 </table>\r\n                 </fieldset>\r\n                 <button id=btntraining disabled class=mybutton onclick=simpanTraining()>" . $_SESSION['lang']['save'] . "</button>\r\n                <br>\r\n                <br>\r\n                <div style='width:100%;height:250px;overflow:scroll;'>\r\n                <table class=sortable border=0 cellspacing=1 width=100%>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                          <td>No.</td>\r\n                          <td>" . $_SESSION['lang']['jeniskursus'] . "</td>\r\n                          <td>" . $_SESSION['lang']['legend'] . "</td>\r\n                          <td>" . $_SESSION['lang']['penyelenggara'] . "</td>\r\n                          <td>" . $_SESSION['lang']['bulanmasuk'] . "</td>\r\n                          <td>" . $_SESSION['lang']['bulankeluar'] . "</td>\r\n                          <td>" . $_SESSION['lang']['sertifikat'] . "</td>\r\n                          <td>" . $_SESSION['lang']['biaya'] . "</td>\r\n                          <td></td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody id=containertraining>\r\n                        </tbody>\r\n                        <tfoot>\r\n                        </tfoot>\r\n                </table>\r\n                </div>\r\n                ";
$opthubk = '';
$arrenum = getEnum($dbname, 'sdm_karyawankeluarga', 'hubungankeluarga');
foreach ($arrenum as $key => $val) {
    if ('EN' == $_SESSION['language']) {
        switch ($key) {
            case 'Pasangan':
                $val = 'Couple';

                break;
            case 'Anak':
                $val = 'Child';

                break;
            case 'Ibu':
                $val = 'Mother';

                break;
            case 'Bapak':
                $val = 'Father';

                break;
            case 'Adik':
                $val = 'Younger brother/sister';

                break;
            case 'Kakak':
                $val = 'Older brother/sister';

                break;
            case 'Ibu Mertua':
                $val = 'Monther-in-law';

                break;
            case 'Bapak Mertua':
                $val = 'Father-in-law';

                break;
            case 'Sepupu':
                $val = 'Cousin';

                break;
            case 'Ponakan':
                $val = 'Nephew';

                break;
            default:
                $val = 'Foster child';

                break;
        }
    }

    $opthubk .= "<option value='" . $key . "'>" . $val . '</option>';
}
$optstk = '';
$arrenum = getEnum($dbname, 'sdm_karyawankeluarga', 'status');
foreach ($arrenum as $key => $val) {
    if ('EN' == $_SESSION['language'] && 'Kawin' == $val) {
        $val = 'Married';
    }

    if ('EN' == $_SESSION['language'] && ('Bujang' == $val || 'Lajang' == $val)) {
        $val = 'Single';
    }

    $optstk .= "<option value='" . $key . "'>" . $val . '</option>';
}
$frm[4] = '<fieldset><legend>' . $_SESSION['lang']['keluarga'] . "</legend>\r\n         <table border=0 cellspacing=1>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['nama'] . "</td><td><input type=text class=myinputtext id=keluarganama size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['jeniskelamin'] . "</td><td><select id=keluargajk  style='width:170px;'>" . $optJK . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['tempatlahir'] . "</td><td><input type=text class=myinputtext id=keluargatmplahir size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['tanggallahir'] . "</td><td><input type=text class=myinputtext id=keluargatgllahir size=30 onmousemove=setCalendar(this.id) size=10 maxlength=10 onkeypress=\"return false;\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['hubungan'] . "</td><td><select id=hubungankeluarga  style='width:170px;'>" . $opthubk . "</select></td>\r\n                        <td align=right>" . $_SESSION['lang']['statusperkawinan'] . "</td><td><select id=keluargastatus style='width:170px;'>" . $optstk . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['edulevel'] . "</td><td><select id=keluargapendidikan  style='width:170px;'>" . $optpendidikan . "</select></td>\r\n                    <td align=right>" . $_SESSION['lang']['pekerjaan'] . "</td><td><input type=text class=myinputtext id=keluargapekerjaan size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['telp'] . "</td><td><input type=text class=myinputtext id=keluargatelp size=30 maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                    <td align=right>" . $_SESSION['lang']['email'] . "</td><td><input type=text class=myinputtext id=keluargaemail size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" onblur=emailCheck(this.value)></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['tanggungan'] . "</td><td colspan=3><select id=keluargatanggungan style='width:170px;'><option value=0>" . $_SESSION['lang']['no'] . '</option><option value=1>' . $_SESSION['lang']['yes'] . "</option></select></td>\r\n                 </tr>\r\n                 </table>\r\n                 <input type=hidden value=insert id=keluargamethod>\r\n                 <input type=hidden value='' id=keluarganomor>\r\n                 </fieldset>\r\n                 <button id=btnkeluarga disabled class=mybutton onclick=simpanKeluarga()>" . $_SESSION['lang']['save'] . "</button>\r\n                 <button  class=mybutton onclick=clearKeluarga()>" . $_SESSION['lang']['new'] . "</button>\r\n                <br>\r\n                <br>\r\n                <div style='width:100%;height:250px;overflow:scroll;'>\r\n                <table class=sortable border=0 cellspacing=1 width=100%>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                          <td>No.</td>\r\n                          <td>" . $_SESSION['lang']['nama'] . "</td>\r\n                          <td>" . $_SESSION['lang']['jeniskelamin'] . "</td>\r\n                          <td>" . $_SESSION['lang']['hubungan'] . "</td>\r\n                          <td>" . $_SESSION['lang']['tanggallahir'] . "</td>\r\n                          <td>" . $_SESSION['lang']['statusperkawinan'] . "</td>\r\n                                                  <td>" . $_SESSION['lang']['umur'] . "</td>\r\n                          <td>" . $_SESSION['lang']['edulevel'] . "</td>\r\n                          <td>" . $_SESSION['lang']['pekerjaan'] . "</td>\r\n                          <td>" . $_SESSION['lang']['telp'] . "</td>\r\n                          <td>" . $_SESSION['lang']['email'] . "</td>\r\n                          <td>" . $_SESSION['lang']['tanggungan'] . "</td>\r\n                          <td></td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody id=containerkeluarga>\r\n                        </tbody>\r\n                        <tfoot>\r\n                        </tfoot>\r\n                </table>\r\n                </div>\r\n                ";
$frm[5] = "<fieldset style='width:155px;height:180px;'>\r\n         <legend>Photo</legend>\r\n         <img src='' id=displayphoto style='width:150;height:175px;'>\r\n                 </fieldset>\r\n                 <fieldset><legend>Upload.Photo (Max.50Kb)</legend>\r\n                 <iframe frameborder=0 width=350px height=70px name=winForm id=winForm src=sdm_form_upload_photo.php>\r\n                 </iframe>\r\n                 </fieldset>\r\n                 <iframe name=frame id=frame  frameborder=0 width=0px height=0px></iframe>\r\n                 <button id=btnphoto disabled class=mybutton onclick=simpanPhoto()>" . $_SESSION['lang']['save'] . "</button>\r\n                 <button  class=mybutton onclick=cancelPhoto()>" . $_SESSION['lang']['cancel'] . "</button>\r\n                ";
$frm[6] = '<fieldset><legend>' . $_SESSION['lang']['alamat'] . "</legend>\r\n         <table border=0 cellspacing=1>\r\n                 <tr>\r\n                    <td align=right rowspan=2>" . $_SESSION['lang']['alamat'] . "</td><td rowspan=2><textarea id=alamatalamat cols=19 rows=2 onkeypress=\"return tanpa_kutip(event);\"></textarea><img src=images/obl.png title='Obligatory'></td>\r\n                    <td align=right>" . $_SESSION['lang']['kota'] . "</td><td><input type=text class=myinputtext id=alamatkota size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                        <td align=right>" . $_SESSION['lang']['province'] . "</td><td><select id=alamatprovinsi style='width:170px;'>" . $optProvinsi . "</select></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['kodepos'] . "</td><td><input type=text class=myinputtext id=alamatkodepos size=30  maxlength=5 onkeypress=\"return angka_doang(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['telp'] . "</td><td><input type=text class=myinputtext id=alamattelepon size=30  maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                 </tr>\r\n                 <tr>\r\n                    <td align=right>" . $_SESSION['lang']['emplasmen'] . "</td><td><input type=text class=myinputtext id=alamatemplasement size=30  maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n                        <td align=right>" . $_SESSION['lang']['alamataktif'] . "</td><td colspan=3><select id=alamatstatus  style='width:170px;'><option value='0'>" . $_SESSION['lang']['no'] . "</option><option value='1'>" . $_SESSION['lang']['yes'] . "</option></select></td>\r\n                 </tr>\r\n                 </table>\r\n                 </fieldset>\r\n                 <button id=btnalamat disabled class=mybutton onclick=simpanAlamat()>" . $_SESSION['lang']['save'] . "</button>\r\n                <br>\r\n                <br>\r\n                <div style='width:100%;height:250px;overflow:scroll;'>\r\n                <table class=sortable border=0 cellspacing=1 width=100%>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                          <td>No.</td>\r\n                          <td>" . $_SESSION['lang']['alamat'] . "</td>\r\n                          <td>" . $_SESSION['lang']['kota'] . "</td>\r\n                          <td>" . $_SESSION['lang']['province'] . "</td>\r\n                          <td>" . $_SESSION['lang']['kodepos'] . "</td>\r\n                          <td>" . $_SESSION['lang']['emplasmen'] . "</td>\r\n                          <td>" . $_SESSION['lang']['status'] . "</td>\r\n                          <td></td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody id=containeralamat>\r\n                        </tbody>\r\n                        <tfoot>\r\n                        </tfoot>\r\n                </table>\r\n                </div>\r\n                ";
$hfrm[0] = $_SESSION['lang']['karyawanbaru'];
$hfrm[1] = $_SESSION['lang']['pengalamankerja'];
$hfrm[2] = $_SESSION['lang']['pendidikan'];
$hfrm[3] = $_SESSION['lang']['kursus'];
$hfrm[4] = $_SESSION['lang']['keluarga'];
$hfrm[5] = $_SESSION['lang']['photo'];
$hfrm[6] = $_SESSION['lang']['alamat'];
drawTab('FRM', $hfrm, $frm, 100);
echo '</div>';
echo "<div id='searchplace' style='display:none;'>" . $_SESSION['lang']['daftarkaryawan'] . ' ' . $_SESSION['empl']['lokasitugas'] . ":<span id=cap1></span>-<span id=cap2></span>\r\n     <br>\r\n         <button class=mybutton value=0 onclick=prefDatakaryawan(this,this.value) id=prefbtn>< " . $_SESSION['lang']['pref'] . " </button>\r\n         &nbsp\r\n         <button class=mybutton value=2 onclick=nextDatakaryawan(this,this.value) id=nextbtn> " . $_SESSION['lang']['lanjut'] . " ></button>\r\n         <table class=sortable border=0 cellspacing=1>\r\n         <thead>\r\n           <tr class=rowheader>\r\n             <td align=center>No.</td>\r\n                 <td align=center>" . $_SESSION['lang']['nik'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['nama'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['functionname'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['kodegolongan'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['lokasitugas'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['pt'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['noktp'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['jms'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['bpjskes'] . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['pendidikan'] . "</td>\r\n                 <td align=center>" . str_replace(' ', '<br>', $_SESSION['lang']['statuspajak']) . "</td>\r\n                 <td align=center>" . str_replace(' ', '<br>', $_SESSION['lang']['statusperkawinan']) . "</td>\r\n                 <td align=center>" . str_replace(' ', '<br>', $_SESSION['lang']['jumlahanak']) . "</td>\r\n                 <td align=center>" . $_SESSION['lang']['tanggalmasuk'] . "</td>\r\n                 <td align=center>" . str_replace(' ', '<br>', $_SESSION['lang']['tipekaryawan']) . "</td>\r\n                 <td> </td>\r\n           </tr>\r\n         </thead>\r\n         <tbody id=searchplaceresult>\r\n         </tbody>\r\n         <tfoot>\r\n         </tfoot>\r\n         </table>\r\n     </div>";
CLOSE_BOX();
close_body('');
