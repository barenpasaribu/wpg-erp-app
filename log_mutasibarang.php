<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_mutasi.js\'></script>' . "\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['mutasi'] . ':</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';
	echo '</legend>';

	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'GUDANG\',\'GUDANGTEMP\') and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\') order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (left(induk,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe in (\'GUDANG\',\'GUDANGTEMP\')order by namaorganisasi desc';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\n" . '     <legend>' . "\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\n" . '     </legend>' . "\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc onchange=getPT(this.options[this.selectedIndex].value)>' . $optsloc . '</select>' . "\n\t" . '   ' . $_SESSION['lang']['ptpemilikbarang'] . '<select id=pemilikbarang style=\'width:200px;\'>' . "\n\t" . '   <option value=\'\'></option>' . "\n\t" . '   </select>' . "\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\n\t" . '  ' . "\n\t" . ' </fieldset>';
	$optlokasitujuan = '<option value=\'\'></option>';

	if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where ' . "\n\t" . '  kodeorganisasi not like \'' . $_SESSION['empl']['lokasitugas'] . '%\' and tipe like \'%GUDANG%\' order by namaorganisasi desc';		
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where ' . "\n\t" . '  kodeorganisasi not like \'' . $_SESSION['empl']['lokasitugas'] . '%\' and tipe like \'%GUDANG%\' and kodeorganisasi like \''.$_SESSION['empl']['kodeorganisasi'].'%\' order by namaorganisasi desc';		
	}
	else {
		//$str = '  select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe like \'GUDANG%\' ' . "\n" . '           and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\n" . '           order by kodeorganisasi    ' . "\n";		
		$str = '  select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe like \'GUDANG%\' ' . "\n" . '      and kodeorganisasi like \''.$_SESSION['empl']['kodeorganisasi'].'%\'      and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\n" . '           order by kodeorganisasi    ' . "\n";		
	}


	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optlokasitujuan .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	$optsubunit = '<option value=\'\'></option>';
	$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
    $frm[0] .= "<table cellspacing=1 border=0>\n     <tr>\n\t\t<td>".$_SESSION['lang']['momordok']."</td>\n\t\t<td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \n\t    <td>".$_SESSION['lang']['tanggal']."</td><td>\n\t\t     <input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>\n\t\t</td>\n\t </tr>\n\t <tr>\n\t <td>".$_SESSION['lang']['tujuan']."</td><td><select id=kegudang style='width:200px;' onchange=cekGudang(this)>".$optlokasitujuan."</select></td>\n \t <td>".$_SESSION['lang']['note']."</td><td><input type=text id=catatan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=40 maxlength=80></td>\n\t </td>\n\t </tr>\n         
	<tr>\n\t <td></td><td><input type=hidden id=konosemen onkeypress='return false' onclick=\"showWindowKonosemen('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['nokonosemen']."',event);\" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=25  maxlength=35 /></td>\n \t <td>&nbsp;</td><td>&nbsp</td>\n\t </td>\n\t </tr>\n\n\t<tr>\n\t <td>No. GRN</td><td><input type=text id=grn onkeypress='return false' onclick=\"showWindowGrn('".$_SESSION['lang']['find'].' '.'GRN'."',event);\" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=25  maxlength=35 /></td>\n \t <td>&nbsp;</td><td>&nbsp</td>\n\t </td>\n\t </tr>\n\n\t </table>";
    foreach ($_SESSION['gudang'] as $key => $val) {
        $frm[0] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\n\t\t";
    }
    $frm[0] .= "</fieldset>\n    <fieldset>\n\t   <legend>".$_SESSION['lang']['detail']."</legend>\n\t   <div id=container>\n\t   <table class=sortable cellspacing=1 border=0>\n\t\t   <thead>\n\t\t   <tr class=rowheader>\n\t\t  <td></td> <td>".$_SESSION['lang']['kodebarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['satuan']."</td>\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\n \t\t   </tr>\n\t\t   </thead>\n                        <tbody>\n                                <tr class=rowcontent id=row_1>\n                                 <td> <input type=checkbox   id=chk1 style='visibility:hidden;'  class=myinputtext checked></td><td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\n                                     <td><input type=text size=55 maxlength=100 id=namabarang class=myinputtext readonly onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\n                                     <td><input type=text size=5 maxlength=5 id=satuan class=myinputtext  onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\n                                     <td><input type=text size=6 maxlength=6 id=qty value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"></td>\n                                </tr>\t\t\t   \n                        </tbody>\n\t\t   <tfoot>\n\t\t  <td style='visibility:hidden;' id=totaldata> 1</td </tfoot>\n\t   </table>\n\t   </div>\n\t   <button onclick=SimpanHeader() class=mybutton>".$_SESSION['lang']['save']."</button>\n\t   <button onclick=nextItem() class=mybutton>".$_SESSION['lang']['cancel']."</button>\t\n\t   <button onclick=bastBaru() class=mybutton>".$_SESSION['lang']['done']."</button>\t \n\t </fieldset>\n\n    <fieldset >\n\t   <legend>".$_SESSION['lang']['datatersimpan']."</legend>\n\t   <table class=sortable cellspacing=1 border=0 width=100%>\n\t\t   <thead>\n\t\t   <tr class=rowheader>\n\t\t   <td>No</td>\n\t\t    <td>".$_SESSION['lang']['kodebarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\n\t\t\t<td>".$_SESSION['lang']['satuan']."</td>\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\n\t\t\t<td></td>\n \t\t   </tr>\n\t\t   </thead>\n\t\t\t   <tbody id=bastcontainer>\t\t\t   \n\t\t\t   </tbody>\n\t\t   <tfoot>\n\t\t   </tfoot>\n\t   </table>\n\t </fieldset>\n\t \t \n\t ";
    $frm[1] .= "<fieldset>\n\t   <legend>".$_SESSION['lang']['list']."</legend>\n\t  <fieldset><legend></legend>\n\t  ".$_SESSION['lang']['cari_transaksi']."\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>\n\t  <button class=mybutton onclick=cariBast()>".$_SESSION['lang']['find']."</button>\n\t  </fieldset>\n\t  <table class=sortable cellspacing=1 border=0>\n      <thead>\n\t  <tr class=rowheader>\n\t  <td>No.</td>\n\t  <td>".$_SESSION['lang']['sloc']."</td>\n\t  <td>".$_SESSION['lang']['tipe']."</td>\n\t  <td>".$_SESSION['lang']['momordok']."</td>\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\n\t  <td>".$_SESSION['lang']['ptpemilikbarang']."</td>\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\t  \t \n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\n\t  <td>".$_SESSION['lang']['posted']."</td>\n\t  <td></td>\n\t  </tr>\n\t  </head>\n\t   <tbody id=containerlist>\n\t   </tbody>\n\t   <tfoot>\n\t   </tfoot>\n\t   </table>\n\t </fieldset>\t \n\t ";
	$hfrm[0] = $_SESSION['lang']['mutasi'];
	$hfrm[1] = $_SESSION['lang']['list'];
	drawTab('FRM', $hfrm, $frm, 100, 900);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
