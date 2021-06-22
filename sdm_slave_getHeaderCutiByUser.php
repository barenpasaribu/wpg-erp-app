<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$karyawanid = $_POST['karyawanid'];
$str1 = "select a.*,b.namakaryawan,b.tanggalmasuk\r\n\t       from ".$dbname.".sdm_cutiht a\r\n\t\t   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n\t       where a.karyawanid=".$karyawanid."\r\n\t\t   order by periodecuti desc";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td>No</td>\t \r\n\t\t    <td>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t    <td>".$_SESSION['lang']['namakaryawan']."</td>\t\t\r\n\t\t\t<td>".$_SESSION['lang']['periode']."</td>\t\t\t\r\n\t\t\t<td>".$_SESSION['lang']['dari']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['hakcuti']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['diambil']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['sisa']."</td>\r\n\t\t\t</tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo '<tr class=rowcontent id=baris'.$no." onlcick=showByUser('".$bar1->karyawanid."',event)>\r\n\t\t           <td>".$no."</td>\r\n\t\t           <td>".$bar1->karyawanid."</td>\r\n\t\t\t\t   <td>".$bar1->namakaryawan."</td>\r\n\t\t\t\t   <td>".$bar1->periodecuti."</td>\t\t\t\t   \r\n\t\t\t\t   <td>".tanggalnormal($bar1->dari)."</td>\r\n\t\t\t\t   <td>".tanggalnormal($bar1->sampai)."</td>\r\n\t\t\t\t   <td align=right>".$bar1->hakcuti."</td>\r\n\t\t\t\t   <td align=right>".$bar1->diambil."</td>\r\n\t\t\t\t   <td>".$bar1->sisa."</td>\r\n\t\t\t</tr>\t   \r\n\t\t\t\t   ";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";

?>