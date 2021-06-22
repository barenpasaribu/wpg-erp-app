<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$str = 'select * from '.$dbname.".sdm_ho_component\r\n      where name like '%Angs%'";
$res = mysql_query($str, $conn);
$arr = [];
$opt = '';
while ($bar = mysql_fetch_object($res)) {
    $opt .= '<option value='.$bar->id.'>'.$bar->name.'</option>';
    $arr[$bar->id] = $bar->name;
}
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') \r\n          and alokasi=1\r\n          order by namakaryawan";
} else {
    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
        $str1 = 'select * from '.$dbname.".datakaryawan\r\n \t where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') \r\n\t  and tipekaryawan!=5 \r\n\t  and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t  order by namakaryawan";
    } else {
        $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') \r\n          and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n          order by namakaryawan";
    }
}

$res1 = mysql_query($str1, $conn);
$opt1 = '';
while ($bar1 = mysql_fetch_object($res1)) {
    $opt1 .= '<option value='.$bar1->karyawanid.'>'.$bar1->namakaryawan.' -- '.$bar1->nik.' -- '.$bar1->lokasitugas.'['.$nmOrg[$bar1->lokasitugas].']</option>';
}
$str2 = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi in \r\n   \t\t (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$optOrg = '<option value=0>'.$_SESSION['lang']['all'].'</option>';
$res2 = mysql_query($str2, $conn);
while ($bar2 = mysql_fetch_assoc($res2)) {
    $optOrg .= '<option value='.$bar2['kodeorganisasi'].'>'.$bar2['namaorganisasi'].'</option>';
}
if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    $sortOrg = "\r\n\t\t\t<tr>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<table>\r\n\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t<td>Sortir Organisasi</td>\r\n\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t<td><select id=kdOrg style=\"width:150px;\" onchange=getKar()>".$optOrg."</select></td>\r\n\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t</table>\t\t\t\t\t\t\r\n\t\t\t\t</td>\r\n\t\t\t</tr>";
}

$opt3 = '';
for ($z = -12; $z <= 64; ++$z) {
    $da = mktime(0, 0, 0, date('m') - $z, '1', date('Y'));
    $opt3 .= "<option value='".date('Y-m', $da)."'>".date('m-Y', $da).'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['angsuran'].'</b>');
echo '<div id=EList>';
echo OPEN_THEME($_SESSION['lang']['angsuran'].':');
echo '<table>';
echo $sortOrg;
echo "<tr>\r\n\t\t\t\t\t\t\t<td>";
echo "<table class=data>\r\n\t\t \r\n\t\t\r\n\t\t \r\n                      <thead>\r\n                          <tr>\r\n                            <td align=center><b>".$_SESSION['lang']['namakaryawan']."</b></td>\r\n                                <td align=center><b>".$_SESSION['lang']['jennisangsuran']."</b></td>\r\n                                <td align=center><b>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['nilaihutang']."<br>(Rp.)</b></td>\r\n                                <td align=center>".$_SESSION['lang']['bulanawal'].'<br>'.$_SESSION['lang']['potongan']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jumlah'].'<br>('.$_SESSION['lang']['bulan'].")</td>\r\n                                <td align=center>".$_SESSION['lang']['status']."</td>\r\n                          </tr> \r\n                          </thead>\r\n                          <tbody>\r\n                          <tr class=rowcontent>\r\n                          <td><select id=userid>".$opt1."</select></td>\r\n                          <td><select id=idx>".$opt."</select></td>\r\n                          <td><input type=text id=total class=myinputtextnumber size=13 maxlength=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this)></td>\r\n                          <td><select id=start>".$opt3."</select></td>\r\n                          <td><input type=text id=lama class=myinputtextnumber size=4 maxlength=3 onkeypress=\"return angka_doang(event);\" value=0></td>\r\n                          <td><select id=active><option value=1>Active</option>\r\n                          <option value=0>Not Active</option></select>\r\n                          <input type=hidden value='insert' id=method>\r\n                          </td>\r\n                          </tr>\r\n                          </body>\r\n                          <tfoot></tfoot>\r\n                      </table>\r\n                          <center>\r\n                            <button class=mybutton onclick=saveAngsuran()>".$_SESSION['lang']['save']."</button>\r\n                            <button class=mybutton onclick=cancelAngsuran()>".$_SESSION['lang']['cancel']."</button>\r\n                          </center>\r\n                          ";
if ('ID' == $_SESSION['language']) {
    echo "</td><td>\r\n                             <fieldset style='text-align:left;width:300px;'>\r\n                                   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>\r\n                                   <p>Satu karyawan hanya dapat diinput satu jenis angsuran.\r\n                                      Jika angsuran sudah ada dan diinput dengan tipe yang  sama maka angsuran lama akan ditimpah. Untuk menambah komponen angsuran\r\n                                          gunakan menu <b>Payroll Component</b> dengan syarat, awal nama komponen harus '<b>Angs.</b>'. \r\n                                   </p>\r\n                                   </fieldset>\t\t      \r\n                      </td></tr>\r\n                          </table>";
} else {
    echo "</td><td>\r\n                             <fieldset style='text-align:left;width:300px;'>\r\n                                   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>\r\n                                   <p>Each employee can only has one type of loan.\r\n                                        If the installments already exist and in the same type of input with the old installment will be overwritten. \r\n                                        If there is a new component, please register on the setup menu <b>Payroll Component</b> with condition:  component name must be preceded by the word '<b>Angsuran</b>'. \r\n                                   </p>\r\n                                   </fieldset>\t\t      \r\n                      </td></tr>\r\n                          </table>";
}

echo CLOSE_THEME();
echo "<hr><div id=laporan style='width:100%; height:340px;overflow:scroll;'>\r\n                     List Angsuran:";
echo "<table class=sortable width=100% border=0 cellspacing=1>\r\n                      <thead>\r\n                          <tr class=rowheader>\r\n                            <td align=center>No.</td>\r\n                                <td align=center>".$_SESSION['lang']['nik']."</td>\r\n                           \t\t<td align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t\t\t\t\t\t<td align=center>".$_SESSION['lang']['lokasitugas']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jennisangsuran']."</td>\r\n                                <td align=center>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>\r\n                                <td align=center>".$_SESSION['lang']['bulanawal']."</td>\r\n                                <td align=center>".$_SESSION['lang']['sampai']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jumlah'].'<br>('.$_SESSION['lang']['bulan'].")</td>\r\n                                <td align=center>".$_SESSION['lang']['potongan'].'/'.$_SESSION['lang']['bulan'].".<br>(Rp.)</td>\t\t\t\t\r\n                                <td align=center>".$_SESSION['lang']['status']."</td>\r\n                                <td align=center></td>\r\n                          </tr> \r\n                          </thead>\r\n                          <tbody id=tbody>";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t\t\t  where u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n                      order by namakaryawan";
} else {
    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
        $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t\t\t  where u.tipekaryawan!=5 and \r\n\t\t\t\t\t  u.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t\t\t\t\t  order by namakaryawan";
    } else {
        $str = 'select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from '.$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\t\t\t  where u.tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                      order by namakaryawan";
    }
}

$res = mysql_query($str, $conn);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n                            <td class=firsttd>".$no."</td>\r\n                            <td>".$bar->nik."</td>\r\n                                <td>".$bar->namakaryawan."</td>\r\n\t\t\t\t\t\t\t\t<td>".$bar->lokasitugas.' -- '.$nmOrg[$bar->lokasitugas]." </td>\r\n                                <td>".$arr[$bar->jenis]."</td>\r\n                                <td align=right>".number_format($bar->total, 2, '.', ',')."</td>\r\n                                <td align=center>".$bar->start."</td>\r\n                                <td align=center>".$bar->end."</td>\r\n                                <td align=right>".$bar->jlhbln."</td>\r\n                                <td align=right>".number_format($bar->bulanan, 2, '.', ',')."</td>\t\t\t\t\r\n                                <td align=center>".((1 == $bar->active ? 'Active' : 'Not Active'))."</td>\r\n                                        <td>\r\n                             <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">\r\n                             &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">\t\t\r\n                                        </td>\t\t\t\t\r\n                          </tr>";
}
echo "</body>\r\n                          <tfoot></tfoot>\r\n                      </table></div></div>";
CLOSE_BOX();
echo close_body();

?>