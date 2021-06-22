<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$nikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$txtnama = $_POST['txtnama'];
$str1 = 'select induk,kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by kodeorganisasi';
$res1 = mysql_query($str1);
$optorg = '<option value=*></option>';
while ($bar1 = mysql_fetch_object($res1)) {
    $optorg .= "<option value='".$bar1->induk."'>".$bar1->kodeorganisasi.' ['.$bar1->namaorganisasi.']</option>';
}
$str = 'select karyawanid,namakaryawan,lokasitugas from '.$dbname.".datakaryawan where namakaryawan like '%".$txtnama."%' and nik like '%".$_POST['nik']."%' and alokasi=0 and tipekaryawan<>5 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";
$res = mysql_query($str);
echo "<table class=sortable border=0 cellspacing=1>\r\n    <thead>\r\n     <tr class=header>\r\n        <td>".$_SESSION['lang']['nik']."</td>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n            <td>".$_SESSION['lang']['lokasitugas']."</td>\r\n            <td></td>\r\n            <td>".$_SESSION['lang']['rotasike']."</td>\r\n    </tr>\r\n    <thead>\r\n    <tbody>\r\n    ";
while ($bar = mysql_fetch_object($res)) {
    echo "<tr class=rowcontent>\r\n            <td>".$nikKar[$bar->karyawanid]."</td>\r\n                <td>".$bar->namakaryawan."</td>\r\n                <td>".$bar->lokasitugas."</td>\r\n                <td><img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\"></td>\r\n            <td><select id=tujuan".$bar->karyawanid." onchange=setKarTo('".$bar->karyawanid."')>".$optorg."</select> \r\n            </tr>";
}
echo '</tbody></table>';

?>