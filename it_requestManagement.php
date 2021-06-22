<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/it_requestManagement.js'></script>\r\n<script>\r\n    tolak=\"";
echo $_SESSION['lang']['ditolak'];
echo "\";\r\n    </script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper('Request management:').'</b>');
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sKary = 'select distinct karyawanid,namakaryawan from '.$dbname.".datakaryawan where bagian='HO_ITGS' and alokasi=1 order by namakaryawan asc";
$qKary = mysql_query($sKary) || exit(mysql_error($sKary));
while ($rKary = mysql_fetch_assoc($qKary)) {
    $optKary .= "<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan'].'</option>';
}
echo ''.$_SESSION['lang']['namakaryawan'].': <select id=karyidCari style=width:150px onchange=loadData()>'.$optKary."</select>&nbsp;\r\n    <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button><div style='width:1180px;display:fixed;'>\r\n       <table class=sortable cellspacing=1 border=0 width=1160px>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center style='width:40px;'>".$_SESSION['lang']['nomor']."</td>\r\n\t\t\t  <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t  <td align=center style='width:180px;'>".$_SESSION['lang']['namakegiatan']."</td>\r\n\t\t\t  <td align=center style='width:125px;'>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t  <td align=center style='width:125px;'>".$_SESSION['lang']['atasan']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['status'].' '.$_SESSION['lang']['atasan']."</td>\r\n\t\t\t  <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['atasan']."</td>\r\n\t\t\t  <td align=center style='width:200px;'>".$_SESSION['lang']['pelaksana']."</td>\r\n\t\t\t  <td align=center style='width:150px;'>".$_SESSION['lang']['standard'].' '.$_SESSION['lang']['jam']."</td>\r\n\t\t\t  <td align=center style='width:40px;'>".$_SESSION['lang']['view']."</td>\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div><div style='width:1180px;height:420px;overflow:scroll;'>\r\n           <table class=sortable cellspacing=1 border=0 width=1160px>\r\n                 <thead>\r\n                      <tr>\r\n                     </tr>  \r\n                     </thead>\r\n                     <tbody id=container>\r\n                     <script>loadData()</script>\r\n                     </tbody>\r\n                     \t \r\n               </table>\r\n         </div>";
CLOSE_BOX();
close_body();

?>