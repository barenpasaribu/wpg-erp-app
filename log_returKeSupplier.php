<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_returkesupplier.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['retur'] . '(Supplier):</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';
	echo '</legend>';

	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'GUDANG\',\'GUDANGTEMP\')' . "\r\n" . '       and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\r\n" . '       order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (left(induk,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe in (\'GUDANG\',\'GUDANGTEMP\') order by namaorganisasi desc';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\r\n" . '     <legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\r\n" . '     </legend>' . "\r\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc>' . $optsloc . '</select>' . "\r\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\r\n\t" . '  ' . "\r\n\t" . ' </fieldset>';
	$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
    $frm[0] .= "<table cellspacing=1 border=0>\r\n                <tr>\r\n                <td>".$_SESSION['lang']['momordok']."</td>\r\n                <td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \r\n                <td>".$_SESSION['lang']['tanggalretur']."</td><td>\r\n                <input type=text class=myinputtext id=tanggal size=25 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>\r\n                </td>\r\n\t </tr>\r\n\t </table>\r\n\t <fieldset><legend>".$_SESSION['lang']['dokumenlama']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t <td>".$_SESSION['lang']['nomorlama']."</td><td><input type=text id=nomorlama class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>\r\n\t <td>".$_SESSION['lang']['kodebarang']."</td><td><input type=text id=kodebarang class=myinputtext size=25 maxength=11>\r\n                \r\n\t       <button class=mybutton onclick=Fverify()>".$_SESSION['lang']['cek']."</button>\r\n\t </td>\r\n\t </tr>\r\n\t <tr>\r\n\t <td>".$_SESSION['lang']['namabarang']."</td><td><input type=text id=namabarang class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled></td>\r\n\t <td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jlhlama class=myinputtextnumber size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled>\r\n                \r\n\t \r\n                 <input type=hidden id=supplierid value=''>\r\n\t </td>\r\n\t </tr>\r\n                 <tr>\r\n                 <td>".$_SESSION['lang']['namasupplier']."</td><td><input type=text id=namasupplier class=myinputtext size=35 disabled \">\r\n                  <td>".$_SESSION['lang']['nopo']."</td><td><input type=text id=nopo class=myinputtextnumber size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled>   \r\n\t <input type=text id=satuan size=6 disabled class=myinputtext>\r\n                  </tr>\r\n                  </table>\r\n\t </fieldset>\r\n\t <fieldset><legend>".$_SESSION['lang']['jumlahkembali']."</legend>\r\n\t ".$_SESSION['lang']['jumlahkembali'].": <input type=text id=jlhretur disabled value=0 class=myinputtextnumber size=10 maxlength=6 onkeypress=\"return tanpa_kutip(event);\">\r\n\t <input type=hidden id=hargasatuan value='0'>\r\n\t <input type=hidden id=kodept value=''>\r\n\t <input type=hidden id=untukunit value=''>\r\n\t <input type=hidden id=untukpt value=''>\r\n\t ".$_SESSION['lang']['keterangan']."\r\n\t <input type=text id=keterangan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=25 maxlength=80>\r\n\t <button id=savebutton class=mybutton onclick=simpanRetur() disabled>".$_SESSION['lang']['save']."</button>\r\n\t <button id=savebutton class=mybutton onclick=window.location.reload()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>\r\n\t ";
    foreach ($_SESSION['gudang'] as $key => $val) {
        $frm[0] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\r\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\r\n\t\t";
    }
    $frm[0] .= "</fieldset>\r\n\t ";
    $frm[1] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariBapb()>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['sloc']."</td>\r\n\t  <td>".$_SESSION['lang']['tipe']."</td>\r\n\t  <td>".$_SESSION['lang']['momordok']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t  <td>".$_SESSION['lang']['pt']."</td>\r\n\t  <td>".$_SESSION['lang']['nopo']."</td>\t\r\n\t  <td>".$_SESSION['lang']['dari']."</td> \r\n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n\t  <td>".$_SESSION['lang']['posted']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>\r\n\t   </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\t \r\n\t ";
	$hfrm[0] = $_SESSION['lang']['retur'];
	$hfrm[1] = $_SESSION['lang']['list'];
	drawTab('FRM', $hfrm, $frm, 100, 900);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
