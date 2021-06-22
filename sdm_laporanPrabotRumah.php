<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/umum_laporanpenghuni.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['penghuni']);
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n      where tipe not in('STENGINE','BLOK','PT','HOLDING','GUDANG','STATION')\r\n\t  order by kodeorganisasi";
$res = mysql_query($str);
$optorg .= '';
while ($bar = mysql_fetch_object($res)) {
    $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
echo '<fieldset>';
echo $_SESSION['lang']['kodeorganisasi'].'<select id=kodeorg>'.$optorg."</select>\r\n    <button class=mybutton onclick=showPrabot()>".$_SESSION['lang']['tampilkan'].'</button>';
echo '</fieldset>';
echo "<fieldset>\r\n      <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <table class=sortable border=0 cellspacing=1>\r\n\t  \t\t<thead>\r\n\t  \t\t <tr class=rowheader>\r\n\t\t\t <td>No</td>\r\n\t\t\t <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t <td>".$_SESSION['lang']['komplek_rmh']."</td>\r\n\t\t\t <td>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t <td>".$_SESSION['lang']['no_rmh']."</td>\r\n\t\t\t <td>".$_SESSION['lang']['tipe']."</td>\r\n\t\t\t <td>".$_SESSION['lang']['jumlahasset']."</td>\r\n\t\t\t <td></td>\r\n\t\t\t </tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody id=container>\r\n\t\t\t</tbody>\r\n\t\t\t<tfoot>\r\n\t\t\t</tfoot>\r\n\t  </table>\r\n\t  ";
echo '</fieldset>';
CLOSE_BOX();
echo close_body();

?>