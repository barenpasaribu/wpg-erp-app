<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payrollHO.css>\r\n";
OPEN_BOX('', '<b>'.$_SESSION['lang']['pengaturanuserpayroll'].'</b>');
echo '<div id=EList>';
echo OPEN_THEME($_SESSION['lang']['daftarpengguna'].':');
$str1 = 'select namauser from '.$dbname.'.user order by namauser';
$res1 = mysql_query($str1, $conn);
$opt = '';
while ($bar1 = mysql_fetch_array($res1)) {
    $opt .= "<option value='".$bar1[0]."'>".$bar1[0].'</option>';
}
echo "<fieldset>\r\n\t\t         <legend>\r\n\t\t\t\t <img src=images/info.png align=left height=35px valign=asmiddle>\r\n\t\t\t\t [INFO]\r\n\t\t\t\t </legend>\r\n\t\t\t\t ".$_SESSION['lang']['assignpyinfo']."\t\r\n\t\t      </fieldset>\t\r\n\t\t\t  <fieldset>\r\n\t\t\t  New User:<select id=user><option></option>".$opt."</select> Type<select id=type><option value='operator'>Operator</option><option value='admin'>Admin</option></select>\r\n\t\t      <button onclick=savePyUser() class=mybutton>".$_SESSION['lang']['save']."</button>\r\n\t\t\t  </fieldset>";
echo "<table class=sortable cellspacing=1 width=500px border=0>\r\n\t\t     <thead>\r\n\t\t\t   <tr class=rowheader><td>No.</td><td>".$_SESSION['lang']['username']."</td>\r\n\t\t\t   <td>".$_SESSION['lang']['tipe']."</td>\r\n\t\t\t   <td>Del</td>\r\n\t\t\t   </tr>\r\n\t\t\t </thead>\r\n\t\t\t <tbody id=tablebody>\r\n\t\t\t ";
$str = 'select * from '.$dbname.'.sdm_ho_payroll_user order by uname';
$res = mysql_query($str, $conn);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo '<tr class=rowcontent><td class=fisttd>'.$no."</td>\r\n\t\t\t      <td id='uname".$no."'>".$bar->uname."</td>\r\n\t\t\t      <td>".$bar->type."</td>\r\n\t\t\t\t  <td align=center><img src=images/close.png  height=11px class=dellicon title=Delete  onclick=\"delPyUser('".$bar->uname."')\"></td>\t  \r\n\t\t\t\t  </tr>";
}
echo "</tbody>\r\n\t\t     <tfoot>\r\n\t\t\t </tfoot>\r\n\t\t\t </table>";
echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>