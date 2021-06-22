<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['debetkreditnote'].'</b>');
echo "<script language=javascript src='js/zMaster.js'></script> \r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script languange=javascript1.2 src='js/zSearch.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script languange=javascript1.2 src='js/keu_2debitNote.js'></script>\r\n";
$opt_kepada = $opt_unit = $opt_pt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$s_pt = 'select * from '.$dbname.".organisasi where tipe='PT' order by kodeorganisasi asc";
$q_pt = mysql_query($s_pt);
while ($r_pt = mysql_fetch_assoc($q_pt)) {
    $opt_pt .= "<option value='".$r_pt['kodeorganisasi']."'>".$r_pt['namaorganisasi'].'</option>';
}
$array = '##pt##unit##kepada##tanggal##sd##tipe';
echo "<div>\r\n<fieldset style='float:left;'>\r\n<legend>";
echo $_SESSION['lang']['form'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['namapt'];
echo "</td><td>:</td>\r\n        <td colspan=\"4\"><select id='pt' style=\"width:150px;\" onchange=\"load_unit_kpd()\">";
echo $opt_pt;
echo "</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['unitkerja'];
echo "</td><td>:</td>\r\n        <td colspan=\"4\"><select id='unit' style=\"width:150px;\" onchange=\"load_kpd()\">";
echo $opt_unit;
echo "</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['kepada'];
echo "</td><td>:</td>\r\n        <td colspan=\"4\"><select id='kepada' style=\"width:150px;\" >";
echo $opt_kepada;
echo "</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['tanggal'];
echo "</td><td>:</td>\r\n        <td><input type='text' class='myinputtext' id='tanggal' name='tanggal' onmousemove='setCalendar(this.id);' \r\n             onkeypress='return false;'  maxlength=10 style='width:100px;' /></td>\r\n        <td>";
echo $_SESSION['lang']['sd'];
echo "</td><td>:</td>\r\n        <td><input type='text' class='myinputtext' id='sd' name='sd' onmousemove='setCalendar(this.id);' \r\n             onkeypress='return false;'  maxlength=10 style='width:100px;'/></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['tipe'];
echo "</td><td>:</td>\r\n        <td colspan=\"4\">\r\n            <select id='tipe' style=\"width:150px;\">\r\n                <option value=''>";
echo $_SESSION['lang']['pilihdata'];
echo "</option>\r\n                <option value=\"Debet Note\">";
echo $_SESSION['lang']['debet'];
echo " Note</option>\r\n                <option value=\"Kredit Note\">";
echo $_SESSION['lang']['kredit'];
echo " Note</option>\r\n            </select></td>\r\n    </tr>\r\n    <td colspan=\"6\" id=\"tombol\" align=\"center\">\r\n        ";
echo "<button onclick=\"zPreview('keu_slave_2debitNote','".$array."','reportcontainer')\" \r\n         class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>\r\n        <button onclick=\"zPdf('keu_slave_2debitNote','".$array."','reportcontainer')\" class=\"mybutton\" \r\n         name=\"pdf\" id=\"pdf\">".$_SESSION['lang']['pdf']."</button>\r\n        <button onclick=\"zExcel(event,'keu_slave_2debitNote.php','".$array."','reportcontainer')\" \r\n         class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel'].'</button>';
echo "    </td>\r\n    </tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<fieldset><legend>'.$_SESSION['lang']['debetkreditnote']."</legend>\r\n                 <div id='reportcontainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n                 </fieldset>";
CLOSE_BOX();

?>