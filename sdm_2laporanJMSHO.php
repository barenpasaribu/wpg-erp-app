<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$opt3 = '';
for ($z = 0; $z <= 36; ++$z) {
    $da = mktime(0, 0, 0, date('m') - $z, date('d'), date('Y'));
    $opt3 .= "<option value='".date('Y-m', $da)."'>".date('m-Y', $da).'</option>';
}
$str = 'select * from '.$dbname.'.sdm_ho_hr_jms_porsi';
$res = mysql_query($str, $conn);
$karyawan = 3;
$perusahaan = 6.54;
while ($bar = mysql_fetch_object($res)) {
    if ('karyawan' == $bar->id) {
        $karyawan = $bar->value / 100;
        $jhtkar = $bar->jhtkar / 100;
        $jpkar = $bar->jpkar / 100;
    } else {
        $perusahaan = $bar->value / 100;
        $jhtpt = $bar->jhtkar / 100;
        $jppt = $bar->jppt / 100;
        $jkkpt = $bar->jkkpt / 100;
        $jkmpt = $bar->jkmpt / 100;
    }
}
OPEN_BOX('', '<b>BPJS TK REPORT</b>');
echo '<div id=EList>';
echo OPEN_THEME('BPJS TK:');
echo "<br>\r\n\t\t      Periode:<select id=bln onchange=getJmsvalue(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>\r\n\t\t\t ";
$str = "select e.name,e.startdate,e.nojms,d.jumlah,d.karyawanid,d.periodegaji,d.idkomponen,f.name as namakomponen from\r\n\t\t     ".$dbname.'.sdm_ho_employee e, '.$dbname.'.sdm_gaji d, '.$dbname.".sdm_ho_component f\r\n\t\t      where e.karyawanid=d.karyawanid and d.idkomponen=f.id and e.operator='".$_SESSION['standard']['username']."'\r\n\t\t\t  and d.periodegaji='".date('Y-m')."' and d.idkomponen in(5,6,7,9)\r\n\t\t      order by name";
echo '<hr><br>Laporan BPJS TK Bulan:<b><span id=caption>'.date('m-Y')."</span></b>\r\n\t\t\t  <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertJmsExcel()>\r\n\t\t\t  <div style='display:none;'>\r\n\t\t\t  <iframe id=ifrm></iframe>\r\n\t\t\t  </div>\r\n\t\t\t  ";
//echo "<table class=sortable width=100% border=0 cellspacing=1>\r\n\t\t      <thead>\r\n\t\t\t  <tr class=rowheader>\r\n\t\t\t    <td align=center>No.</td>\r\n\t\t\t\t<td align=center>No.Karyawan</td>\r\n\t\t\t    <td align=center>Nama.Karyawan</td>\r\n\t\t\t\t<td align=center>Tipe BPJS TK</td>\r\n\t\t\t\t<td align=center>Tgl.Masuk</td>\r\n\t\t\t\t<td align=center>Periode</td>\r\n\t\t\t\t<td align=center>JHT Kary<br>(Rp.)</td>\r\n                <td align=center>JP Kary<br>(Rp.)</td>\r\n<td align=center>Beban.Karyawan<br>(Rp.)</td>\r\n\t\t\t\t<td align=center>JHT PT<br>(Rp.)</td>\r\n                <td align=center>JP PT<br>(Rp.)</td>\r\n                <td align=center>JKK PT<br>(Rp.)</td>\r\n                <td align=center>JKM PT<br>(Rp.)</td>\r\n<td align=center>Beban.Perusahaan<br>(Rp.)</td>\r\n\t\t\t\t<td align=center>Gaji Bruto</td>\r\n\t\t\t  </tr>\r\n\t\t\t  </thead>\r\n\t\t\t  <tbody id=tbody>";
echo "<table class=sortable width=100% border=0 cellspacing=1>\r\n\t\t      <thead>\r\n\t\t\t  <tr class=rowheader>\r\n\t\t\t    <td align=center>No.</td>\r\n\t\t\t\t<td align=center>No.Karyawan</td>\r\n\t\t\t    <td align=center>Nama.Karyawan</td>
<td align=center>Tgl.Masuk</td>\r\n\t\t\t\t<td align=center>Periode</td>\r\n\t\t\t\t<td align=center>JHT Kary<br>(Rp.)</td>\r\n                <td align=center>JP Kary<br>(Rp.)</td>\r\n<td align=center>Beban.Karyawan<br>(Rp.)</td>\r\n\t\t\t\t<td align=center>JHT PT<br>(Rp.)</td>\r\n                <td align=center>JP PT<br>(Rp.)</td>\r\n                <td align=center>JKK PT<br>(Rp.)</td>\r\n                <td align=center>JKM PT<br>(Rp.)</td>\r\n<td align=center>Beban.Perusahaan<br>(Rp.)</td>\r\n\t\t\t\t<td align=center>Gaji Bruto</td>\r\n\t\t\t  </tr>\r\n\t\t\t  </thead>\r\n\t\t\t  <tbody id=tbody>";
$res = mysql_query($str, $conn);
$no = 0;
$ttl = 0;
$tvp = 0;
$tkar = 0;
$total = 0;
$ttljhtkar = 0;
$ttljpkar = 0;
$ttljhtpt = 0;
$ttljppt = 0;
$ttljkkpt = 0;
$ttljkmpt = 0;
while ($bar = mysql_fetch_object($res)) {
    $valPerusahaan = ($bar->jumlah * -1) / 2 * 100 * $perusahaan;
    $tvp += $valPerusahaan;
    $kar += $bar->jumlah * -1;
    $total = $valPerusahaan + $bar->jumlah * -1;
    $stru = 'select sum(jumlah) as gjk from '.$dbname.".sdm_gaji where idkomponen in(1,2)\r\n                   and periodegaji='".date('Y-m')."' and karyawanid=".$bar->karyawanid;
    $resu = mysql_query($stru, $conn);
    $gjkotor = 0;
    while ($baru = mysql_fetch_object($resu)) {
        $gjkotor = $baru->gjk;
    }
    ++$no;
    echo "<tr class=rowcontent>\r\n\t\t\t    <td class=firsttd>".$no."</td>\r\n\t\t\t    <td>".$bar->karyawanid."</td>\r\n\t\t\t\t<td>".$bar->name."</td>\r\n\t\t\t\t<td>".$bar->namakomponen."</td>\r\n\t\t\t\t<td align=right>".tanggalnormal($bar->startdate)."</td>\r\n\t\t\t\t<td align=center>".$bar->periodegaji."</td>\r\n\t\t\t\t<td align=right>".number_format($ttljhtkar * -1, 2, '.', ',')."</td>\r\n                <td align=right>".number_format($ttljpkar * -1, 2, '.', ',')."</td>\r\n                <td align=right>".number_format($ttlbpjskar * -1, 2, '.', ',')."</td>\r\n                <td align=right>".number_format($ttljhtpt, 2, '.', ',')."</td>\r\n                <td align=right>".number_format($ttljppt, 2, '.', ',')."</td>\r\n                <td align=right>".number_format($ttljkkpt, 2, '.', ',')."</td>\r\n                <td align=right>".number_format($ttljkmpt, 2, '.', ',')."</td>\r\n<td align=right>".number_format($ttlbpjspt, 2, '.', ',')."</td>\r\n\t\t\t\t<td align=right>".number_format($gjkotor, 2, '.', ',')."</td>\r\n\t\t\t  </tr>";
}
echo "</tbody>\r\n\t\t\t  <tfoot></tfoot>\r\n\t\t\t    <tr class=rowcontent>\r\n\t\t      </table></div>";
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>