<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/kebun_5mandor.js'></script>\r\n";
include 'master_mainMenu.php';
$optmandor = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
/*
$str = 'select karyawanid, namakaryawan from '.$dbname.".datakaryawan\r\n    where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and alokasi = 0\r\n    order by namakaryawan";
*/
$str = 'select karyawanid, namakaryawan from '.$dbname.".datakaryawan a left join sdm_5jabatan b on a.kodejabatan=b.kodejabatan where alias like '%Mandor%' and lokasitugas like '".$_SESSION['empl']['lokasitugas']."%' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and alokasi = 0 order by namakaryawan";

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optmandor .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->karyawanid.']</option>';
}
$optkaryawan = "<option value=''></option>";
OPEN_BOX();
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['mandor']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['mandor']."</td>\r\n\t   <td>: <select onchange=\"pilihmandor();\" id=mandor style='width:200px'>".$optmandor."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t   <td>\r\n                : <select id=karyawan style='width:200px'>".$optkaryawan."</select>\r\n                ".$_SESSION['lang']['nourut']." \r\n                <input type=text class=myinputtext onkeypress=\"return angka_doang(event);\" id=urut size=3 maxlength=3 class=myinputtextnumber>\r\n                <button class=mybutton onclick=tambahkaryawan()>".$_SESSION['lang']['save']."</button>\r\n           </td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td></td>\r\n\t   <td><div id=anggota></td>\r\n\t </tr>\r\n\t </table>\r\n     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['mandor']."</td>\r\n\t   <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>tampilmandor()</script></tbody>\r\n     <tfoot>\r\n     </tfoot>\r\n     </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>