<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>PPh 21 COMPONENT</b>');
echo '<div id=EList>';
echo OPEN_THEME('Component Gaji yang dikenai PPh 21:').'<br>';
$str = 'select id,name,pph21 from '.$dbname.".sdm_ho_component\r\n      where plus=1 order by id";
$res = mysql_query($str, $conn);
$va = "Beri tanda check(V) pada komponen yang kena pajak.\r\n     <table class=sortable cellspacing=1 border=0 width=500px>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t    <td>ID.</td><td align=center>Nama.Komponen</td><td align=center>Yes/No</td>\r\n\t  </tr>\t\r\n\t  </thead>\r\n\t  <tbody>";
while ($bar = mysql_fetch_object($res)) {
    if (1 == $bar->pph21) {
        $ch = 'checked';
        if (1 == $bar->id) {
            $ch .= ' disabled';
        }
    } else {
        $ch = '';
    }

    $va .= "<tr class=rowcontent>\r\n\t        <td class=firsttd align=center>".$bar->id."</td>\r\n\t\t\t<td>".$bar->name."</td>\r\n\t\t\t<td align=center><input type=checkbox id=ch".$bar->id.' '.$ch.' onclick=savePPh21Component(this,this.value) value='.$bar->id."></td>\r\n\t    </tr>";
}
$va .= '</tbody><tfoot></tfoot></table>';
$hfrm[0] = 'Komponen Gaji';
$frm[0] = '<br>'.$va."<br>\r\n\t\t";
drawTab('FRM', $hfrm, $frm, 150, 600);
echo '</div>';
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>