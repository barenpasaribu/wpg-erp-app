<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$limit = 0;
if (isset($_GET['limit'])) {
    $limit = $_GET['limit'];
}

OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['sinkronisasidatapy']).'</b>');
echo '<div>';
echo OPEN_THEME($_SESSION['lang']['pilihdata'].':');
echo '<table><tr><td>';
$arrid = [];
$prestr = 'select distinct karyawanid from '.$dbname.'.sdm_ho_employee order by karyawanid';
$preres = mysql_query($prestr, $conn);
while ($prebar = mysql_fetch_object($preres)) {
    array_push($arrid, $prebar->karyawanid);
}
//$str = 'select karyawanid,namakaryawan,statuspajak,tanggalmasuk,tanggalkeluar,npwp from '.$dbname.'.datakaryawan limit '.$limit.',500';
$str = 'select karyawanid,namakaryawan,statuspajak,tanggalmasuk,tanggalkeluar,npwp from '.$dbname.'.datakaryawan where isduplicate=0 and kodeorganisasi like \''.$_SESSION['empl']['kodeorganisasi'].'%\' limit '.$limit.',500';
$res = mysql_query($str, $conn);
echo '<input type=checkbox onclick=checkAll(this,'.mysql_num_rows($res).')>'.$_SESSION['lang']['pilihsemua'];
echo " &nbsp &nbsp &nbsp &nbsp &nbsp \r\n\t\t        <a href='?limit=".((-1 < $_GET['limit'] - 500 ? $_GET['limit'] - 500 : 0))."'>".$_SESSION['lang']['pref']."</a> &nbsp <a href='?limit=".($_GET['limit'] + 500)."'>".$_SESSION['lang']['lanjut'].'</a>';
echo "<table class=sortable cellspacing=1 border=0>\r\n\t\t     <thead>\r\n\t\t\t   <tr class=rowheader>\r\n\t\t\t   <td>".$_SESSION['lang']['pilih']."</td>\r\n\t\t\t    <td>No.</td>\r\n\t\t\t    <td>".$_SESSION['lang']['id']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['statuspajak']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['npwp']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['tanggalmasuk']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['tanggalkeluar']."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t </thead>\r\n\t\t\t <tbody id=tablebody>\r\n\t\t\t ";
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo '<tr class=rowcontent id=row'.$no.'>';
    if (in_array($bar->karyawanid, $arrid, true)) {
        echo "<td><input type=checkbox id='chk".$no."'></td>";
    } else {
        echo "<td style='background-color:orange'><input type=checkbox id='chk".$no."' checked>".$_SESSION['lang']['new'].'</td>';
    }

    echo '<td class=firsttd>'.($no + $limit)."</td>\r\n\t\t\t\t <td id=userid".$no.'>'.$bar->karyawanid."</td>\r\n\t\t\t\t <td id=nama".$no.'>'.$bar->namakaryawan."</td>\r\n\t\t\t\t <td id=mstatus".$no.'>'.$bar->statuspajak."</td>\r\n\t\t\t\t <td id=npwp".$no.'>'.$bar->npwp."</td>\r\n\t\t\t\t <td id=start".$no.'>'.tanggalnormal($bar->tanggalmasuk)."</td>\r\n\t\t\t\t <td id=resign".$no.'>'.tanggalnormal($bar->tanggalkeluar)."</td>\r\n\t\t\t\t </tr>";
}
echo "</tbody>\r\n\t\t     <tfoot>\r\n\t\t\t </tfoot>\r\n\t\t\t </table>";
// echo "</td>\r\n\t\t     <td valign=top>\r\n\t\t\t     <fieldset style='text-align:center'>\r\n\t\t\t\t   <legend id=legend><b>".$_SESSION['lang']['panelsinkronisasi']."</b></legend>\r\n\t\t\t\t   ".$_SESSION['lang']['sinkronisasiinfo']."<br>\r\n\t\t\t\t   <button id=synbutton onclick=sync(".$no.")>Synchronize</button>\r\n\t\t\t\t   <button id=stpbutton onclick=stopSync(".($no + 1).') disabled>'.$_SESSION['lang']['stop']."</button>\r\n\t\t\t\t </fieldset>\r\n\t\t\t </td>\r\n\t\t\t </tr>\r\n\t\t\t </table>\r\n\t\t\t ";
echo "</td>\r\n\t\t     <td valign=top>\r\n\t\t\t     <fieldset style='text-align:center'>\r\n\t\t\t\t   <legend id=legend><b>".$_SESSION['lang']['panelsinkronisasi']."</b></legend>\r\n\t\t\t\t   <b>Proses Sinkronisasi</b> hanya dilakukan <b>1 kali</b> saat terdapat penambahan data karyawan baru. Semua data yang dipilih akan disimpan ke dalam database payroll.<br>\r\n\t\t\t\t   <button id=synbutton onclick=sync(".$no.")>Synchronize</button>\r\n\t\t\t\t   <button id=stpbutton onclick=stopSync(".($no + 1).') disabled>'.$_SESSION['lang']['stop']."</button>\r\n\t\t\t\t </fieldset>\r\n\t\t\t </td>\r\n\t\t\t </tr>\r\n\t\t\t </table>\r\n\t\t\t ";
echo CLOSE_THEME('');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>