<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo '<script language=javascript src=js/zMaster.js></script> ' . "\r\n" . '<script language=javascript src=js/zSearch.js></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formTable.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/lbm_main_procurement.js\'></script>' . "\r\n";
$arr = '##periode##judul';
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
echo "\r\n" . '    ' . "\r\n";
$arrTipe = array(1 => 'Capital', 2 => 'Non Capital');
$optTipe = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

foreach ($arrTipe as $lstTipe => $dtTipe) {
	$optTipe .= '<option value=\'' . $lstTipe . '\'>' . $dtTipe . '</option>';
}

$optperiode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select distinct substr(tanggal,1,4) as tahun from  ' . $dbname . '.log_poht  where tanggal!=\'0000-00-00\'  and purchaser!=\'0000000000\'' . "\r\n" . '       order by substr(tanggal,1,4) desc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optperiode .= '<option value=' . $rOrg['tahun'] . '>' . $rOrg['tahun'] . '</option>';
}

OPEN_BOX();
echo '<fieldset style=width:350px;><legend>PP Belum Realisasi</legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '    <tr><td colspan=2>' . $judul . '</td></tr>' . "\r\n" . '    <tr><td><label>' . $_SESSION['lang']['tahun'] . '</label></td><td><select id=\'periode\' style="width:200px;">' . $optperiode . '</select></td></tr></table></filedset>';
echo '<tr><td colspan="2"><input type=hidden id=judul name=judul value=\'' . $judul . '\'></td></tr>' . "\r\n" . '    <tr><td colspan="2"> ' . "\r\n" . '    <button onclick="zPreview(\'lbm_slave_proc_ppblmrealisasi\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">' . $_SESSION['lang']['preview'] . '</button>' . "\r\n" . '    <button onclick="zExcel(event,\'lbm_slave_proc_ppblmrealisasi.php\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>    ' . "\r\n" . '   <!--<button onclick="zPdf(\'lbm_slave_proc_pupuk\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="pdf" id="pdf">' . $_SESSION['lang']['pdf'] . '</button>' . "\r\n" . '    <button onclick="batal()" class="mybutton" name="btnBatal" id="btnBatal">' . $_SESSION['lang']['cancel'] . '</button>--></td></tr>' . "\r\n" . '</table>' . "\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<div id=mainPrint><fieldset><legend><span id=isiJdlBawah>Pending PR</span></legend>' . "\r\n" . '                 <div id=\'reportcontainer\' style=\'width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;\'></div> ' . "\r\n" . '                 </fieldset></div>';
echo '<div id=lyrPertama style=display:none;>' . "\r\n" . '                 <fieldset><legend><span id=isiJdlBawah1></span></legend>' . "\r\n" . '                 <div id=\'reportcontainer1\' style=\'width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;\'></div> ' . "\r\n" . '                 </fieldset>' . "\r\n" . '                 </div>';
echo '<div id=lyrKedua style=display:none;>' . "\r\n" . '                 <fieldset><legend><span id=isiJdlBawah2></span></legend>' . "\r\n" . '                 <div id=\'reportcontainer2\' style=\'width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;\'></div> ' . "\r\n" . '                 </fieldset>' . "\r\n" . '                 </div>';
CLOSE_BOX();
CLOSE_BOX();
close_body();

?>
