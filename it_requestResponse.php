<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/it_requestResponse.js'></script>\r\n<script>\r\n    tolak=\"";
echo $_SESSION['lang']['ditolak'];
echo "\";\r\n    </script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper('Permintaan Layanan IT:').'</b>');
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optJenis = $optKary;
$sKary = 'select distinct karyawanid,namakaryawan from '.$dbname.".datakaryawan where bagian='HO_ITGS' and alokasi=1 order by namakaryawan asc";
$qKary = mysql_query($sKary) || exit(mysql_error($sKary));
while ($rKary = mysql_fetch_assoc($qKary)) {
    $optKary .= "<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan'].'</option>';
}
echo "\r\n     <!--<img onclick=detailExcel(event,'sdm_slave_laporan_ijin_meninggalkan_kantor.php') src=images/excel.jpg class=resicon title='MS.Excel'>-->\r\n     &nbsp;".$_SESSION['lang']['namakaryawan'].': <select id=karyidCari style=width:150px onchange=loadData()>'.$optKary."</select>&nbsp;\r\n     \r\n         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>\r\n\t <div style='width:100%;height:600px;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center>No.</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['namakegiatan']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n                          <td align=center>".$_SESSION['lang']['status'].' '.$_SESSION['lang']['atasan']."</td>  \r\n                          <td align=center>".$_SESSION['lang']['pelaksana']."</td>\r\n                          <td align=center>".$_SESSION['lang']['saran']."</td>\r\n                          <td align=center>".$_SESSION['lang']['selesai']."</td>\r\n                          <td align=center>".$_SESSION['lang']['view']."</td>  \r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container><script>loadData()</script>\r\n\t\t </tbody>\r\n\t\t \t\t \r\n\t   </table>\r\n     </div>";
CLOSE_BOX();
close_body();

?>