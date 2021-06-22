<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/alokasiIDC.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['input'].' '.$_SESSION['lang']['alokasiidc']);
echo '<fieldset><legend>'.$_SESSION['lang']['form']."</legend>\r\n           <table>\r\n             <tr><td>".$_SESSION['lang']['tanggal']."</td><td><input class='myinputtext' id='tanggal' size='26' onmousemove='setCalendar(this.id)' maxlength='10' onkeypress='return false;' type='text' onblur=ambilBuktiKas(this.value)></td></tr>\r\n             <tr><td>".$_SESSION['lang']['nojurnal']."</td><td><select id=nokas onchange=ambilAlokasi()></select></td></tr>\r\n             <tr><td>".$_SESSION['lang']['alokasibiaya']."</td><td><select id=alokasi onchange=ambilBlok(this.options[this.selectedIndex].value)></select></td></tr>    \r\n           </table>\r\n          </fieldset>";
CLOSE_BOX();
OPEN_BOX('', '');
$str = 'select distinct nojurnal,tanggal,kodeorg from '.$dbname.".keu_jurnaldt_vw where nojurnal like '%/IDC/%' and kodeorg in( select kodeorganisasi from ".$dbname.".organisasi\r\n          where induk='".$_SESSION['empl']['kodeorganisasi']."') order by tanggal desc";
$res = mysql_query($str);
$tab = "<table>\r\n             <thead>\r\n              <tr class=rowheader>\r\n             <td>".$_SESSION['lang']['nomor']."</td>\r\n             <td>".$_SESSION['lang']['nojurnal']."</td>\r\n              <td>".$_SESSION['lang']['tanggal']."</td>\r\n              <td>".$_SESSION['lang']['aksi']."</td>\r\n             </tr>\r\n             </thead>\r\n             <tbody>";
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $tab .= '<tr class=rowcontent><td>'.$no.'</td><td>'.$bar->nojurnal.'</td><td>'.tanggalnormal($bar->tanggal)."</td><td><button onclick=hapusIni('".$bar->nojurnal."','".$bar->tanggal."','".$bar->kodeorg."')>".$_SESSION['lang']['delete'].'</button></tr>';
}
$tab .= '</tbody><tfoot></tfoot></table>';
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n          <div id=space></div>".$tab."\r\n          </fieldset>";
CLOSE_BOX();
echo close_body();

?>