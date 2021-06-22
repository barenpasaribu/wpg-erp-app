<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script   language=javascript1.2 src='js/jarakblok.js'></script>\r\n\r\n\r\n";
$optOrg = "<option value=''>Pilih data</option>";
$x = 'select * from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN'";
$y = mysql_query($x);
while ($z = mysql_fetch_assoc($y)) {
    $optOrg .= "<option value='".$z['kodeorganisasi']."'>".$z['namaorganisasi'].'</option>';
}
echo "\r\n";
OPEN_BOX();
echo "\r\n    <fieldset style='float:left;'>\r\n        <legend>Jarak Blok ke PKS</legend>\r\n            <table border=0 cellspacing=1 cellpadding=0>\r\n                \r\n               <tr>\r\n                    <td>Regional</td>\r\n                    <td>:</td>\r\n                    <td><input id=regional value=".$_SESSION['empl']['regional']." type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45></td>\r\n                <tr>\r\n                <tr>\r\n                    <td>Kodeorg</td>\r\n                    <td>:</td>\r\n                    <td><select id=kodeorg onchange=getBlok()>".$optOrg."</td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Kode blok</td>\r\n                    <td>:</td>\r\n                    <td><select id=kodeblok>".$optProduk."</td>\r\n                </tr>\r\n               \r\n                    <td>Jarak</td>\r\n                    <td>:</td>\r\n                    <td><input type=text id=jarak onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=4>KM</td>\r\n               \r\n                <tr>\r\n                    <td></td>\r\n                    <td></td>\r\n                    <td><button onclick=simpan() class=mybutton name=saveDt id=saveDt>".$_SESSION['lang']['save']."</button></td>\r\n                </tr>\r\n            </table> <input type=hidden id=method value='insert'>\r\n       </fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo "\r\n    <fieldset style='float:left;'>\r\n        <legend>List Data</legend>\r\n         <table border=0 cellspacing=1 cellpadding=0 class=sortable>\r\n            <thead>\r\n                <tr class=rowheader>\r\n                    <td>No</td>\r\n                    <td>Regional</td>\r\n                    <td>Kodeorg</td>\r\n                    <td>Kode Blok</td>\r\n                    <td>Jarak</td>\r\n                    <td>updateby</td>\r\n                    <td>Aksi</td>\r\n                </tr>\r\n            </thead>";
$a = 'select * from '.$dbname.'.vhc_5jarakblok';
$b = mysql_query($a);
while ($c = mysql_fetch_assoc($b)) {
    ++$no;
    echo "    <tr class=rowcontent>\r\n                   <td>".$no."</td>\r\n                    <td>".$c['regional']."</td>\r\n                    <td>".$c['kodeorg']."</td>      \r\n                    <td>".$c['kodeblok']."</td>\r\n                    <td align=right>".$c['jarak']."</td>\r\n                    <td>".$c['updateby']."</td>    \r\n                    <td>Aksi</td>\r\n                </tr>";
}
echo " </table>\r\n    </fieldset>";
CLOSE_BOX();

?>