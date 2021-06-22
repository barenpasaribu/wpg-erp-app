<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['verifikasi'] . '</b>');
echo '<script>semua="';
echo $_SESSION['lang']['all'];
echo '";</script>' . "\r\n" . 
'<script language="javascript" src="js/zMaster.js?v='.mt_rand().'"></script>' . "\r\n" . 
'<script type="text/javascript" src="js/log_verivikasi.js?v='.mt_rand().'"></script>' . "\r\n\r\n\r\n" . '<div id="action_list">' . "\r\n";
$optPur = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sPur = "select karyawanid,namakaryawan from $dbname.datakaryawan where bagian in ('HO_PUR','HO_PROC')  and (tanggalkeluar >'" . date('Y-m-d') . "' or tanggalkeluar is NULL or tanggalkeluar= '0000-00-00')  order by namakaryawan asc";
//echoMessage('sql ',$sPur);
$qPur = fetchData($sPur);

foreach ($qPur as $brsKary) {
	$optPur .= '<option value=' . $brsKary['karyawanid'] . '>' . $brsKary['namakaryawan'] . '</option>';
}

$optListUnit .= '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sListUnit = 'select distinct substr(nopp,16,4) as kodeunit from ' . $dbname . '.log_prapoht where close=\'2\'';

#exit(mysql_error($sListUnit));
($qListUnit = mysql_query($sListUnit)) || true;

while ($rListUnit = mysql_fetch_assoc($qListUnit)) {
	$optListUnit .= '<option value=\'' . $rListUnit['kodeunit'] . '\'>' . $rListUnit['kodeunit'] . '</option>';
}

$optKelompokBrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optBrgCari = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sKelompok = 'select distinct kode,kelompok from ' . $dbname . '.log_5klbarang order by kelompok asc';

#exit(mysql_error($sKelompok));
($qKelompok = mysql_query($sKelompok)) || true;

while ($rKelompok = mysql_fetch_assoc($qKelompok)) {
	$optKelompokBrg .= '<option value=\'' . $rKelompok['kode'] . '\'>' . $rKelompok['kelompok'] . '</option>';
}

$optPeriodeCari = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sPeriodeCari = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_prapoht order by substr(tanggal,1,7) desc';

#exit(mysql_error());
($qPeriodeCari = mysql_query($sPeriodeCari)) || true;

while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
	$optPeriodeCari .= '<option value=\'' . $rPeriodeCari['periode'] . '\'>' . $rPeriodeCari['periode'] . '</option>';
}

$optStatusPP = '<option value=\'2\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$stataPP = array($_SESSION['lang']['blmAlokasi'], $_SESSION['lang']['sdhPO']);

foreach ($stataPP as $dataIni => $listNama) {
	$optStatusPP .= '<option value=\'' . $dataIni . '\'>' . $listNama . '</option>';
}

echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n" . '         <td onclick=displaySummary() align=center style=\'width:55px;cursor:pointer;\'><img class=delliconBig src=images/book_icon.gif title=\'Summary\'><br>Summary</td>' . "\r\n" . '         <td onclick=displayTools() align=center style=\'width:55px;cursor:pointer;\'><img class=delliconBig src=images/gear_64.png title=\'Tools\'><br>Tools</td>' . "\r\n" . '         <td align=center style=\'width:55px;cursor:pointer;\' onclick=displayList()>' . "\t" . '   ' . "\r\n" . '           <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '         <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo '<table border=0 cellpadding=1 cellspacing=1><tr><td>';
echo $_SESSION['lang']['carinopp'] . '</td><td><input type=text id=txtsearch size=25 maxlength=30 class=myinputtext></td>';
echo '<td>' . $_SESSION['lang']['periode'] . '</td><td><select id=tgl_cari style=width:100px>' . $optPeriodeCari . '</select></td>';
echo '<td>' . $_SESSION['lang']['purchaser'] . '</td><td><select id=purId name=purId>' . $optPur . '</select></td>';
echo '<td>' . $_SESSION['lang']['unit'] . '</td><td><select id=unitIdCr name=unitIdCr>' . $optListUnit . '</select></td>';
echo '<td>' . $_SESSION['lang']['status'] . '</td><td><select id=\'statPP\' name=\'statPP\'>' . $optStatusPP . '</select></td>';
echo '<td rowspan=2><button class=mybutton onclick=cariNopp()>' . $_SESSION['lang']['find'] . '</button></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['kelompokbarang'] . '</td><td> <select id=klmpkBrg style=width:150px onchange=getBarangCari()>' . $optKelompokBrg . '</select></td><td>' . $_SESSION['lang']['namabarang'] . '</td><td><select id=kdBarangCari style=width:100px>' . $optBrgCari . '</select>&nbsp;<img src="images/search.png" class="resicon" title=\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '\' onclick="searchBrgCari(\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg2()>' . $_SESSION['lang']['find'] . '</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>\',event);"></td><td colspan=3></td></tr></table>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n" . '         </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="list_pp_verication">';
OPEN_BOX();
echo '  <input type=\'hidden\' id=\'method\' name=\'method\' />' . "\r\n\r\n" . '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['list_pp'];
echo '</legend>' . "\r\n" . '  <img onclick=dataKeExcel(event) src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '<div style="overflow:scroll; height:420px;" id=contain>' . "\r\n" . '<script>displayList()</script>' . "\r\n" . '</div>' . "\r\n" . '</fieldset>' . "\r\n";
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
