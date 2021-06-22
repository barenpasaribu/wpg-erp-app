<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/bgt_freeQuery.js\'></script>' . "\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', strtoupper($_SESSION['lang']['budget']) . ' FREE QUERY');
$optOrg = '';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '       where length(kodeorganisasi)=4 and tipe=\'KEBUN\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$str = 'select distinct(tahunbudget) as tahunbudget  ' . "\r\n" . '      from ' . $dbname . '.bgt_budget  order by tahunbudget';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optthn .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select kodekegiatan,namakegiatan,kelompok  ' . "\r\n" . '      from ' . $dbname . '.setup_kegiatan where' . "\r\n" . '      kelompok in(\'TB\',\'BBT\',\'TBM\',\'TM\',\'PNN\')' . "\r\n" . '      order by kelompok asc,namakegiatan';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optkeg .= '<option value=\'' . $bar->kodekegiatan . '\'>' . $bar->kelompok . ' - ' . $bar->namakegiatan . '</option>';
}

echo '<fieldset style=\'width:500px;\'><legend>' . $_SESSION['lang']['form'] . '</legend>';
echo '<table>' . "\r\n" . '     <tr>' . "\r\n" . '          <td>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n" . '          <td><select id=\'thnbudget\'>' . $optthn . '</select></td>    ' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '          <td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '          <td><select id=\'kodeorg\'>' . $optOrg . '</select></td>    ' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '          <td>' . $_SESSION['lang']['kegiatan'] . '</td>' . "\r\n" . '          <td><select id=\'kegiatan\'>' . $optkeg . '</select></td>    ' . "\r\n" . '     </tr>     ' . "\r\n" . '</table>' . "\r\n" . '<button class=mybutton onclick=getFreeQuery()>' . $_SESSION['lang']['lihat'] . '</button>';
echo '</fieldset><br>' . "\r\n" . '<fieldset><legend>' . $_SESSION['lang']['list'] . '</legend>' . "\r\n" . '  <div id=container  style=\'width:1000px,overflow:scroll\'></div>  ' . "\r\n" . '</fieldset>    ' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
