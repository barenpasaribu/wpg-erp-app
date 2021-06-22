<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
require_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/pabrik_5kelengkapanloses.js'></script>\r\n\r\n";
$optOrg = "<option value=''>Pilih data</option>";
$x = 'select * from '.$dbname.".organisasi where length(kodeorganisasi)=4 and kodeorganisasi like '%M' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$y = mysql_query($x);
while ($z = mysql_fetch_assoc($y)) {
    $optOrg .= "<option value='".$z['kodeorganisasi']."'>".$z['namaorganisasi'].'</option>';
}
$optProduk = "<option value=''>Pilih data</option>";
$optProduk .= "<option value='CPO'>CPO</option>";
$optProduk .= "<option value='KERNEL'>KERNEL</option>";
$nama = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
echo "\r\n";
OPEN_BOX('', 'Kelengkapan Data Loses');
echo "\r\n    <fieldset style='width:500px;'>\r\n        <legend>".$_SESSION['lang']['form']."</legend>\r\n            <table border=0 cellspacing=1 cellpadding=0>\r\n                \r\n               \r\n                  <input id=id disabled type=hidden onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45>\r\n               \r\n                <tr>\r\n                    <td>Kodeorg</td>\r\n                    <td>:</td>\r\n                     <td><input id=kodeorg disabled value='".$_SESSION['empl']['lokasitugas']."' type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45></td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Produk</td>\r\n                    <td>:</td>\r\n                    <td><select id=produk>".$optProduk."</td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Nama Item</td>\r\n                    <td>:</td>\r\n                    <td><input id=namaitem type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45></td>\r\n                <tr>\r\n                    <td>Standard</td>\r\n                    <td>:</td>\r\n                    <td><input id=standard type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=7></td>\r\n                <tr>\r\n                    <td>Satuan</td>\r\n                    <td>:</td>\r\n                    <td><input id=satuan type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=7></td>\r\n                </tr>\r\n                <tr>\r\n                    <td></td>\r\n                    <td></td>\r\n                    <td> <button class=mybutton onclick=simpan()>Simpan</button></td>\r\n                   \r\n                </tr>\r\n            </table>\r\n       </fieldset>\r\n<input type=hidden id=method value='insert'>";
echo "\r\n    <fieldset style='width:500px;'>\r\n        <legend>".$_SESSION['lang']['list']."</legend>\r\n         <table border=0 cellspacing=1 cellpadding=0 class=sortable>\r\n            <thead>\r\n                <tr class=rowheader>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\r\n                    \r\n                    <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['produk']."</td>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n                   \r\n\t\t\t\t   <td>".$_SESSION['lang']['standard']."</td>\r\n\t\t\t\t   <td>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t\t   <td>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t   <td>".$_SESSION['lang']['action']."</td>\r\n                   \r\n                </tr>\r\n            </thead>";
$r = 'select * from '.$dbname.".pabrik_5kelengkapanloses where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$s = mysql_query($r);
while ($t = mysql_fetch_assoc($s)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$no."</td>\r\n                   \r\n                    <td>".$t['kodeorg']."</td>\r\n                    <td>".$t['produk']."</td>\r\n                    <td>".$t['namaitem']."</td>\r\n                    <td>".$t['standard']."</td>\r\n                    <td>".$t['satuan']."</td>\r\n                    <td>".$nama[$t['updateby']]."</td>\r\n                    <td><img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"edit('".$t['id']."','".$t['kodeorg']."','".$t['produk']."','".$t['namaitem']."','".$t['standard']."','".$t['satuan']."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$t['id']."');\"></td>\r\n                    </td>\r\n                </tr>";
}
echo "</table>\r\n    </fieldset>";
CLOSE_BOX();

?>