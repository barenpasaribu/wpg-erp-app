<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script   language=javascript1.2 src=\'js/rencanaGis.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'UPLOAD FILE');
echo "\r\n\t\t" . ' <fieldset style=\'width:500px;\'><legend>Upload.file (Max.512Kb)</legend>' . "\r\n\t\t" . ' <iframe frameborder=0 width=500px height=130px name=winForm id=winForm src=rencana_gis_uploadFile.php>' . "\r\n\t\t" . ' </iframe>' . "\r\n\t\t" . ' <button id=btnphoto  class=mybutton onclick=simpanPhoto()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t\t" . ' <button  class=mybutton onclick=cancelPhoto()>' . $_SESSION['lang']['cancel'] . '</button>                  ' . "\r\n\t\t" . ' </fieldset>                ' . "\r\n\t\t" . ' <iframe name=frame id=frame  frameborder=0 width=600px height=50px></iframe><br>';

if ($_SESSION['empl']['bagian'] == 'HRD') {
	$str = 'select * from ' . $dbname . '.rencana_gis_jenis where left(namajenis,3) in (\'HRD\',\'SOP\')   order by namajenis';
}
else {
	$str = 'select * from ' . $dbname . '.rencana_gis_jenis where left(namajenis,3) not in (\'HRD\',\'SOP\') order by namajenis';
}

$res = mysql_query($str);
$optjenis = '<option value=\'\'>All</option>';

while ($bar = mysql_fetch_object($res)) {
	$optjenis .= '<option value=\'' . $bar->kode . '\'>' . $bar->namajenis . '</option>';
}

$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where length(kodeorganisasi)=4' . "\r\n" . '           order by namaorganisasi desc';
$res = mysql_query($str);
$optOrg = '<option value=\'\'>All</option>';

while ($bar = mysql_fetch_object($res)) {
	$optOrg .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select distinct left(tanggal,7) as periode from ' . $dbname . '.rencana_gis_file order by tanggal desc';
$res = mysql_query($str);
$optperiode = '<option value=\'\'>All</option>';

while ($bar = mysql_fetch_object($res)) {
	$optperiode .= '<option value=\'' . $bar->periode . '\'>' . $bar->periode . '</option>';
}

echo 'Unit<select style=\'width:175px;\' name=\'kodeorg1\'  id=\'kodeorg1\'>' . $optOrg . '</select>' . "\r\n" . '          Jenis Data <select name=\'kode1\'  id=\'kode1\'>' . $optjenis . '</select>' . "\r\n" . '          Periode <select name=\'periode\'  id=\'periode\'>' . $optperiode . '</select>' . "\r\n" . '          <button onclick=cariFile() class=mybutton>' . $_SESSION['lang']['find'] . '</button>';
echo '<div style=\'height:350px;width:100%;overflow:scroll;\'>' . "\r\n" . '      <table class=sortable border=0 cellspacing=1>' . "\r\n\t" . '  <thead>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '  <td>No.</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['jenis'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['user'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['updateby'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['filegis'] . '</td>' . "\r\n\t" . '  <td>Size</td>              ' . "\r\n" . '          <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['action'] . '</td>  ' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  </thead>' . "\r\n\t" . '  <tbody id=container>';
$str1 = 'select a.*,b.namakaryawan from ' . $dbname . '.rencana_gis_file a' . "\r\n" . '       left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid' . "\r\n" . '       where a.karyawanid=\'' . $_SESSION['standard']['userid'] . '\'       order by a.lastupdate  desc limit 20';

if ($res1 = mysql_query($str1)) {
	$no = 0;

	while ($bar1 = mysql_fetch_object($res1)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n" . '               <td>' . $no . '</td>' . "\r\n" . '                <td>' . $bar1->unit . '</td>' . "\r\n" . '                    <td>' . $bar1->kode . '</td>' . "\r\n" . '                    <td>' . tanggalnormal($bar1->tanggal) . '</td>' . "\r\n" . '                    <td>' . $bar1->namakaryawan . '</td>' . "\r\n" . '                    <td>' . $bar1->lastupdate . '</td>' . "\r\n" . '                    <td>' . $bar1->keterangan . '</td>' . "\r\n" . '                    <td>' . $bar1->namafile . '</td>' . "\r\n" . '                    <td align=right>' . $bar1->ukuran . '</td>' . "\r\n" . '                    <td>' . $bar1->namakaryawan . '</td>' . "\r\n" . '                    <td>';

		if ($bar1->karyawanid == $_SESSION['standard']['userid']) {
			echo '<img class=\'zImgBtn\' src=\'images/skyblue/delete.png\' title=\'Edit\' onclick="delFile(\'' . $bar1->unit . '\',\'' . $bar1->kode . '\',\'' . $bar1->namafile . '\');"> &nbsp  &nbsp  &nbsp';
		}

		echo '<img class=\'zImgBtn\'  src=\'images/skyblue/save.png\'  title=\'Save\' onclick="download(\'' . $bar1->namafile . '\');"></td></tr>';
	}
}

echo '</tbody>' . "\r\n\t" . '  <tfoot>' . "\r\n\t" . '  </tfoot>' . "\r\n\t" . '  </table>' . "\r\n\t" . '  </div>';
CLOSE_BOX();
echo close_body();

?>
