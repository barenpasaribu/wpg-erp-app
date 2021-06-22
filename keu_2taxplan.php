<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/keu_2taxplan.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper('Tax Planning').'</b>');
$opt_pt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$opt_unit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$s_pt = 'select * from '.$dbname.".organisasi where tipe='PT' order by kodeorganisasi asc";
$q_pt = mysql_query($s_pt);
while ($r_pt = mysql_fetch_assoc($q_pt)) {
    $opt_pt .= "<option value='".$r_pt['kodeorganisasi']."'>".$r_pt['namaorganisasi'].'</option>';
}
echo "<fieldset>\r\n    <legend>Tax Planning</legend>\r\n    ".$_SESSION['lang']['pt']."<select id='pt' style=width:150px; onchange=load_unit()>".$opt_pt."</select>\r\n    <select id='unit' style=width:150px; >".$opt_unit."</select>\r\n    ".$_SESSION['lang']['tgldari']." <input type=\"text\" class=\"myinputtext\" id=\"tanggaldari\" name=\"tanggaldari\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />\r\n    ".$_SESSION['lang']['tglsmp']." <input type=\"text\" class=\"myinputtext\" id=\"tanggalsampai\" name=\"tanggalsampai\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />\r\n    <button class=mybutton onclick=getTax()>".$_SESSION['lang']['proses']."</button>\r\n    </fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n        <img onclick=taxKeExcel(event,'keu_slave_2taxplan.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n        <!--<img onclick=prestasiKePDF(event,'it_slave_2prestasi.php') title='PDF' class=resicon src=images/pdf.jpg>-->\r\n    </span>    \r\n    <div id=container style='width:100%;height:50%;overflow:scroll;'>\r\n    </div>";
CLOSE_BOX();
close_body();

?>