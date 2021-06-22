<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';

$loktugas = $_SESSION['empl']['lokasitugas'];
if (substr($loktugas,3,1)== 'H'){
	$loktugas= (substr($loktugas,0,3));
}
$str = "select a.uname from sdm_ho_payroll_user a inner join (select x.namauser from user x inner join datakaryawan y on x.karyawanid = y.karyawanid where y.lokasitugas like '".$loktugas."%') b on a.uname = b.namauser order by a.uname";
$res = mysql_query($str, $conn);
$opt .= "<option value='".""."'>"."".'</option>';
while ($bar = mysql_fetch_object($res)) {
    $opt .= "<option value='".$bar->uname."'>".$bar->uname.'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['assignpyoperator'].'</b>');
echo '<div id=EList>';
echo OPEN_THEME($_SESSION['lang']['pilihoperator'].':');
$str = "select a.karyawanid,a.name,a.operator from sdm_ho_employee a inner join datakaryawan b on a.karyawanid = b.karyawanid 
	where b.lokasitugas like '".$loktugas."%' order by karyawanid";
$res = mysql_query($str, $conn);
$no = 0;
echo '<table><tr><td>';
echo "<table class=sortable cellspacing=1 width=500px border=0>\r\n\t\t     <thead>\r\n\t\t\t   <tr class=rowheader><td>No.</td><td>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t\t   <td>".$_SESSION['lang']['namakaryawan'].'</td><td>'.$_SESSION['lang']['operator'].'</td><td>'.$_SESSION['lang']['ubahoperator']."</td></tr>\r\n\t\t\t </thead>\r\n\t\t\t <tbody id=tablebody>\r\n\t\t\t ";
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo '<tr class=rowcontent><td class=firsttd>'.$no."</td>\r\n\t\t\t      <td id='user".$no."'>".$bar->karyawanid."</td>\r\n\t\t\t      <td>".$bar->name."</td>\r\n\t\t\t\t  <td>".$bar->operator."</td>\r\n\t\t\t\t  <td><select id=operator".$no." onchange=saveOperator('".$no."')>".$opt."</td></tr>";
}
echo "</tbody>\r\n\t\t     <tfoot>\r\n\t\t\t </tfoot>\r\n\t\t\t </table>";
echo "</td>\r\n\t\t     <td valign=top> \r\n\t\t       <fieldset>\r\n\t\t         <legend>\r\n\t\t\t\t <img src=images/info.png align=left height=35px valign=asmiddle>\r\n\t\t\t\t </legend>\r\n\t\t\t\t ".$_SESSION['lang']['operatorinfo']." Kolom Operator akan muncul setelah form dibuka ulang"."\r\n\t\t      </fieldset>\t\r\n\t\t     </td></tr>\r\n\t\t\t </table>";
echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>