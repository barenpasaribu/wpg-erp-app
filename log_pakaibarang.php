<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
echo '<link rel=stylesheet type="text/css" href=\'style/zTable.css\'>' . "\n" . '<script language=javascript1.2 src=\'js/log_transaksi_pengeluaran.js\'></script>' . "\n\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['pengeluaranbarang'] . ':</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';
	echo '</legend>';

	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'GUDANG\',\'GUDANGTEMP\')' . "\n" . '       and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\n" . '       order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (left(induk,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe in (\'GUDANG\',\'GUDANGTEMP\')';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\n" . '     <legend>' . "\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\n" . '     </legend>' . "\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc onchange=getPT(this.options[this.selectedIndex].value)>' . $optsloc . '</select>' . "\n\t" . '   ' . $_SESSION['lang']['ptpemilikbarang'] . '<select id=pemilikbarang style=\'width:200px;\'>' . "\n\t" . '   <option value=\'\'></option>' . "\n\t" . '   </select>' . "\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\t" . '  ' . "\n\t" . ' </fieldset>';

	foreach ($_SESSION['gudang'] as $key => $val) {
		echo '<input type=hidden id=\'' . $key . '_start\' value=\'' . $_SESSION['gudang'][$key]['start'] . '\'>' . "\n\t" . '     <input type=hidden id=\'' . $key . '_end\' value=\'' . $_SESSION['gudang'][$key]['end'] . '\'>' . "\n\t\t";
	}

	$optlokasitujuan = '<option value=\'\'></option>';
	$optlokasitujuan .= ambilUnitPembebananBarang('', $_SESSION['empl']['lokasitugas']);
	$optsubunit = '<option value=\'\'></option>';
	$optKegiatan = '<option value=\'\'></option>';
	$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan order by kelompok,namakegiatan';
	$resf = mysql_query($strf);

	while ($barf = mysql_fetch_object($resf)) {
		$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
	}

	$optionm = '<option value=\'\'></option>';
	$str = 'select * from ' . $dbname . '.vhc_5master ' . "\t" . ' order by kodetraksi,kodevhc';
	$res = mysql_query($str);

	while ($bar1 = mysql_fetch_object($res)) {
		$str = 'select namajenisvhc from ' . $dbname . '.vhc_5jenisvhc where jenisvhc=\'' . $bar1->jenisvhc . '\'';
		$res1 = mysql_query($str);
		$namabarang = '';

		while ($bar = mysql_fetch_object($res1)) {
			$namabarang = $bar->namajenisvhc;
		}

		$optionm .= '<option value=\'' . $bar1->kodevhc . '\'>' . $bar1->kodetraksi . ' : ' . $bar1->kodevhc . ' - ' . $namabarang . '</option>';
	}

	$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
    $frm[0] .= "<table cellspacing=1 border=0>\n     <tr>\n\t\t<td>".$_SESSION['lang']['momordok']."</td>\n\t\t<td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \n\t    <td>".$_SESSION['lang']['tanggal']."</td><td>\n\t\t     <input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>\n\t\t</td>\n\t </tr>\n\t <tr>\n\t <td>".$_SESSION['lang']['untukunit']."</td><td><select id=untukunit onchange=loadSubunit(this.options[this.selectedIndex].value,'','') style='width:200px;'>".$optlokasitujuan."</select></td>\n\t <td>".$_SESSION['lang']['subunit']."</td><td><select id=subunit onchange=loadBlock(this.options[this.selectedIndex].value,'')>".$optsubUnit."</select>\n \t    <input type=hidden value='insert' id=method>\n\t </td>\n\t </tr>                                                                                                                                                                                                 \n\t <tr>\n\t <td>".$_SESSION['lang']['penerima'].'</td><td><select id=penerima style=width:200px>'.$optsubUnit."</select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namakaryawan']."','1',event);\"  /></td>\n\t <td>".$_SESSION['lang']['note']."</td><td><input type=text id=catatan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=40 maslength=80></td>\n\t </tr>\n\n\t </table>\n    </fieldset>\n    <fieldset>\n\t   <legend>".$_SESSION['lang']['detail']."</legend>\n\t   <div id=container>\n\t   <table class=sortable cellspacing=1 border=0>\n\t\t   <thead>\n\t\t   <tr class=rowheader>\n\t\t    <td>Kode.Barang</td>\n\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['satuan']."</td>\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\n\t\t\t<td>".$_SESSION['lang']['blok']."</td>\n\t\t\t<td>".$_SESSION['lang']['mesin']."</td>\n\t\t\t<td>".$_SESSION['lang']['kegiatan']."</td>\n\t\t\t</tr>\n\t\t   </thead>\n\t\t\t   <tbody>\n\t\t\t\t   <tr class=rowcontent>\n\t\t\t\t    <td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\n\t\t\t\t\t<td><input type=text size=45 maxlength=100 id=namabarang class=myinputtext readonly onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\n\t\t\t\t\t<td><input type=text size=5 maxlength=5 id=satuan class=myinputtext  onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\n\t\t\t\t\t<td><input type=text size=8 maxlength=10 id=qty value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"></td>\n\t\t\t\t\t<td><select id=blok style='width:100px;' onchange=getKegiatan(this.options[this.selectedIndex].value,'BLOK')></select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['blok']."','2',event);\"  /></td>\n\t\t\t\t\t<td><select id=mesin style='width:100px;' onchange=getKegiatan(this.options[this.selectedIndex].value,'TRAKSI')>".$optionm."</select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['mesin']."','3',event);\"  /></td>\n\t\t\t\t\t<td><select id=kegiatan style='width:100px;'>".$optKegiatan."</select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['kegiatan']."','4',event);\"  /></td>\n\t\t \t\t   </tr>\t\t\t   \n\t\t\t   </tbody>\n\t\t   <tfoot>\n\t\t   </tfoot>\n\t   </table>\n\t   </div>\n\t   <button onclick=saveItemBast() class=mybutton>".$_SESSION['lang']['save']."</button>\n\t   <button onclick=nextItem() class=mybutton>".$_SESSION['lang']['cancel']."</button>\t\n\t   <button onclick=bastBaru() class=mybutton>".$_SESSION['lang']['done']."</button>\t \n\t </fieldset>\n\n    <fieldset>\n\t   <legend>".$_SESSION['lang']['datatersimpan']."</legend>\n\t   <table class=sortable cellspacing=1 border=0 width=100%>\n\t\t   <thead>\n\t\t   <tr class=rowheader>\n\t\t   <td>No</td>\n\t\t    <td>".$_SESSION['lang']['kodebarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['satuan']."</td>\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\n\t\t\t<td>".$_SESSION['lang']['pt']."</td>\n\t\t\t<td>".$_SESSION['lang']['untukunit']."</td>\n\t\t\t<td>".$_SESSION['lang']['kodeblok']."</td>\n\t\t\t<td>".$_SESSION['lang']['kegiatan']."</td>\n\t\t\t<td>".$_SESSION['lang']['kodenopol']."</td>\n\t\t\t<td></td>\n \t\t   </tr>\n\t\t   </thead>\n\t\t\t   <tbody id=bastcontainer>\t\t\t   \n\t\t\t   </tbody>\n\t\t   <tfoot>\n\t\t   </tfoot>\n\t   </table>\n\t </fieldset>\n\t \t \n\t ";
    $frm[1] .= "<fieldset>\n\t   <legend>".$_SESSION['lang']['list']."</legend>\n\t  <fieldset><legend></legend>\n\t  ".$_SESSION['lang']['cari_transaksi']."\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>\n\t  <button class=mybutton onclick=cariBast()>".$_SESSION['lang']['find']."</button>\n\t  </fieldset>\n\t  <table class=sortable cellspacing=1 border=0>\n      <thead>\n\t  <tr class=rowheader>\n\t  <td>No.</td>\n\t  <td>".$_SESSION['lang']['sloc']."</td>\n\t  <td>".$_SESSION['lang']['tipe']."</td>\n\t  <td>".$_SESSION['lang']['momordok']."</td>\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\n\t  <td>".$_SESSION['lang']['ptpemilikbarang']."</td>\n\t  <td>".$_SESSION['lang']['untukunit']."</td>\t  \t \n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\n\t  <td>".$_SESSION['lang']['posted']."</td>\n\t  <td></td>\n\t  </tr>\n\t  </head>\n\t   <tbody id=containerlist>\n\t   </tbody>\n\t   <tfoot>\n\t   </tfoot>\n\t   </table>\n\t </fieldset>\t \n\t ";
	$hfrm[0] = $_SESSION['lang']['pengeluaranbarang'];
	$hfrm[1] = $_SESSION['lang']['list'];
	drawTab('FRM', $hfrm, $frm, 200, 950);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
