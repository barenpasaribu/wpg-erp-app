<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/sdm_payrollHO.js'></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$str = 'select * from '.$dbname.".sdm_ho_component\r\n      where name like '%Angs%'";
$res = mysql_query($str, $conn);
$arr = [];
$opt = '';
while ($bar = mysql_fetch_object($res)) {
    $opt .= '<option value='.$bar->id.'>'.$bar->name.'</option>';
    $arr[$bar->id] = $bar->name;
}
$opt3 = '';
for ($z = -12; $z <= 64; ++$z) {
    $da = mktime(0, 0, 0, date('m') - $z, '1', date('Y'));
    $opt3 .= "<option value='".date('Y-m', $da)."'>".date('m-Y', $da).'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['angsuran'].'</b>');
echo '<div id=EList>';
echo OPEN_THEME($_SESSION['lang']['angsuran']);
if ('EN' == $_SESSION['language']) {
    echo "<br>(Display installment on:<select id=bln onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>)\r\n                     || (Display installment which:<select id=lunas  onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>\r\n                         <option value=lunas>Settled</option>\r\n                         <option value=blmlunas>Not yet settled</option>\r\n                         <option value=active>Active</option>\r\n                         <option value=notactive>Not Active</option></select>)";
    echo "<hr><br>Below installment that :<b><span id=caption>not yet settled</span></b>\r\n                     <image src=images/pdf.jpg class=resicon title='PDF' onclick=angsuranPDF(event)>\r\n                         <input type=hidden id=val value=''>\r\n                         ";
} else {
    echo "<br>(Tampilkan Angsuran Bulan:<select id=bln onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>)\r\n                     || (Tampilkan Angsuran Yang<select id=lunas  onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>\r\n                         <option value=lunas>Sudah Lunas</option>\r\n                         <option value=blmlunas>Belum Lunas</option>\r\n                         <option value=active>Active</option>\r\n                         <option value=notactive>Not Active</option></select>)\t \r\n                         ";
    echo "<hr><br>Berikut Angsuran Karyawan :<b><span id=caption>Belum Lunas</span></b>\r\n                     <image src=images/pdf.jpg class=resicon title='PDF' onclick=angsuranPDF(event)>\r\n                         <input type=hidden id=val value=''>\r\n                         ";
}

if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          (u.tipekaryawan=5 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')\r\n                                      order by namakaryawan";
} else {
    $str = 'select a.*,u.namakaryawan from '.$dbname.'.sdm_angsuran a, '.$dbname.".datakaryawan u\r\n                                      where a.karyawanid=u.karyawanid and\r\n                                          tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                                      order by namakaryawan";
}

echo "<table class=sortable width=900px border=0 cellspacing=1>\r\n                      <thead>\r\n                          <tr class=rowheader>\r\n                            <td align=center>No.</td>\r\n                                <td align=center>".$_SESSION['lang']['karyawanid']."</td>\r\n                            <td align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jennisangsuran']."</td>\r\n                                <td align=center>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>\r\n                                <td align=center>".$_SESSION['lang']['bulanawal']."</td>\r\n                                <td align=center>".$_SESSION['lang']['sampai']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jumlah'].'<br>('.$_SESSION['lang']['bulan'].")</td>\r\n                                <td align=center>".$_SESSION['lang']['potongan'].'/'.$_SESSION['lang']['bulan'].".<br>(Rp.)</td>\t\t\t\t\r\n                                <td align=center>".$_SESSION['lang']['status']."</td>\r\n                          </tr> \r\n                          </thead>\r\n                          <tbody id=tbody>";
$res = mysql_query($str, $conn);
echo mysql_error($conn);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n                            <td class=firsttd>".$no."</td>\r\n                            <td>".$bar->karyawanid."</td>\r\n                                <td>".$bar->namakaryawan."</td>\r\n                                <td>".$arr[$bar->jenis]."</td>\r\n                                <td align=right>".number_format($bar->total, 2, '.', ',')."</td>\r\n                                <td align=center>".$bar->start."</td>\r\n                                <td align=center>".$bar->end."</td>\r\n                                <td align=right>".$bar->jlhbln."</td>\r\n                                <td align=right>".number_format($bar->bulanan, 2, '.', ',')."</td>\t\t\t\t\r\n                                <td align=center>".((1 == $bar->active ? 'Active' : 'Not Active'))."</td>\r\n                          </tr>";
    $ttl += $bar->bulanan;
}
echo "</tbody>\r\n                          <tfoot></tfoot>\r\n                          </table></div>";
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>