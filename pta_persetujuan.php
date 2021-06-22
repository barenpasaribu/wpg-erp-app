<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/pta_persetujuan.js\'></script>' . "\r\n" . '<script>' . "\r\n" . '    tolak="';
echo $_SESSION['lang']['ditolak'];
echo '";' . "\r\n" . '    ajukan="';
echo $_SESSION['lang']['diajukan'];
echo '";' . "\r\n" . '    setujuak="';
echo $_SESSION['lang']['setujuakhir'];
echo '";' . "\r\n" . '    </script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['persetujuan'] . ' PTA') . '</b>');
echo '<table cellpadding=1 cellspacing=1 border=0>';
echo '<tr><td>' . $_SESSION['lang']['daftar'] . ' PTA</td></tr>';
echo '<tr><td>' . $_SESSION['lang']['find'] . ' <input type=text class=myinputtext onkeypress=\'return tanpa_kutip(event)\' id=\'txtCari\' />';
echo '&nbsp;<button onclick=\'loadData()\' class=mybutton>' . $_SESSION['lang']['find'] . '</button>';
echo '</td></tr>';
echo '</table>';
echo "\r\n" . '      <div style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n" . '       <table class=sortable cellspacing=1 border=0 width=80%>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . '    <tr>' . "\r\n\t\t\t" . '  <td align=center>No.</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['nopta'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['penjelasan'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['jumlah'] . ' (Rp.)</td>' . "\r\n" . '                         ' . "\r\n" . '                          <td align=center colspan=3>' . $_SESSION['lang']['action'] . '</td>' . "\r\n\t\t\t" . '</tr>  ' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container><script>loadData()</script>' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\t\t" . ' ' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
