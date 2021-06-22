<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$periode = $_POST['periode'];
if ('HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
    $str1 = "select a.*,b.namakaryawan,b.tanggalmasuk\r\n\t       from ".$dbname.".sdm_cutiht a\r\n\t\t   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n\t       where lokasitugas='".$kodeorg."' and alokasi=0\r\n\t\t   and periodecuti='".$periode."'\r\n                   and tanggalkeluar is NULL";
} else {
    $str1 = "select a.*,b.namakaryawan,b.tanggalmasuk\r\n\t       from ".$dbname.".sdm_cutiht a\r\n\t\t   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n\t       where alokasi=1\r\n\t\t   and periodecuti='".$periode."'\r\n                   and tanggalkeluar is NULL";
}

$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td>No</td>\r\n\t\t\t<td>".$_SESSION['lang']['kodeorganisasi']."</td>\t\t \r\n\t\t    <td>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t    <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalmasuk']."</td>\t\t\t\r\n\t\t\t<td>".$_SESSION['lang']['periode']."</td>\t\t\t\r\n\t\t\t<td>".$_SESSION['lang']['dari']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['hakcuti']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['diambil']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['sisa']."</td>\r\n\t\t\t</tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo '<tr class=rowcontent id=baris'.$no.">\r\n\t\t           <td>".$no."</td>\r\n\t\t\t\t   <td id=kodeorg".$no.'>'.substr($bar1->kodeorg, 0, 4)."</td>\r\n\t\t           <td id=karyawanid".$no.'>'.$bar1->karyawanid."</td>\r\n\t\t\t\t   <td class=firsttd id=nama".$no."  title='Click for detail' style='cursor:pointer'  onclick=showByUser('".$bar1->karyawanid."',event)>".$bar1->namakaryawan."</td>\r\n\t\t\t\t   <td>".tanggalnormal($bar1->tanggalmasuk)."</td>\r\n\t\t\t\t   <td id=periode".$no.'>'.$periode."</td>\t\t\t\t   \r\n\t\t\t\t   <td id=dari".$no.'>'.tanggalnormal($bar1->dari)."</td>\r\n\t\t\t\t   <td id=sampai".$no.'>'.tanggalnormal($bar1->sampai)."</td>\r\n\t\t\t\t   <td id=hak".$no.' align=right>'.$bar1->hakcuti."</td>\r\n\t\t\t\t   <td id=diambil".$no.' align=right>'.$bar1->diambil."</td>\r\n\t\t\t\t   <td><input type=text id=sisa".$no." class=myinputtextnumber size=4 conkeypress=\"return angka_doang(event);\" value='".$bar1->sisa."'>\r\n\t\t\t\t   <img src='images/save.png'  title='Save' class=resicon onclick=updateSisa('".$periode."','".$bar1->karyawanid."','".$bar1->kodeorg."','sisa".$no."')>\r\n\t\t\t\t   <img src='images/application/application_edit.png'  title='".$_SESSION['lang']['tambah']."' class=resicon onclick=\"tambahData('".$periode."','".$bar1->karyawanid."','".$bar1->kodeorg."','".$bar1->namakaryawan."');\">\r\n\t\t\t\t   </td>\r\n\t\t\t</tr>\t   \r\n\t\t\t\t   ";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";

?>