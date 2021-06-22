<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['laporanPabrikTimbangan']).'</b>');
echo "<!--<script type=\"text/javascript\" src=\"js/log_2keluarmasukbrg.js\" /></script>\r\n-->\r\n<script type=\"text/javascript\" src=\"js/pabrik_2timbangan.js\" /></script>\r\n<div id=\"action_list\">\r\n";
$sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where `kelompokbarang` like '400%'";
$qBrg = mysql_query($sBrg);
while ($rBrg = mysql_fetch_assoc($qBrg)) {
    $optBrg .= '<option value='.$rBrg['kodebarang'].'>'.$rBrg['namabarang'].'</option>';
}
$sPbrik = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi like '".$_SESSION['empl']['kodeorganisasi']."%' ";
$qPabrik = mysql_query($sPbrik);
while ($rPabrik = mysql_fetch_assoc($qPabrik)) {
    $optPabrik .= '<option value='.$rPabrik['kodeorganisasi'].' '.(($rPabrik['kodeorganisasi'] === $kdPbrk ? 'selected' : '')).'>'.$rPabrik['namaorganisasi'].'</option>';
}
echo "<table>\r\n     <tr valign=middle>\r\n\t\t <td><fieldset><legend>".$_SESSION['lang']['pilihdata'].'</legend>';
echo $_SESSION['lang']['namabarang'].':<select id=kdBrg name=kdBrg style=width:200px><option value=0>All</option>'.$optBrg.'</select>&nbsp;';
echo $_SESSION['lang']['pabrik'].':<select id=kdPbrk name=kdPbrk style=width:100px>'.$optPabrik.'</select>&nbsp;';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tglTrans name=tglTrans onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=savePil()>'.$_SESSION['lang']['save']."</button>\r\n\t\t\t     <button class=mybutton onclick=gantiPil()>".$_SESSION['lang']['ganti'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "<div id=\"cari_barang\" name=\"cari_barang\">\r\n   <div id=\"hasil_cari\" name=\"hasil_cari\">\r\n    <fieldset>\r\n    <legend>";
echo $_SESSION['lang']['result'];
echo "</legend>\r\n     <img onclick=dataKeExcel(event,'pabrik_slaveLaporanTimbanganExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>\r\n        <div id=\"contain\">\r\n        </div>\r\n    </fieldset>\r\n    </div>\r\n</div>\r\n";
CLOSE_BOX();
echo close_body();

?>