<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
echo "<script language=javascript1.2 src=js/log_penerimaanMutasi.js?v='.date('YmdHis').'></script>";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['terimamutasi'] . ':</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';
	echo '</legend>';

	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'GUDANG\'' . "\r\n" . '       and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\r\n" . '       order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (left(induk,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . "\r\n" . '       or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe in (\'GUDANGTEMP\',\'GUDANG\') order by namaorganisasi desc';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\r\n" . '     <legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\r\n" . '     </legend>' . "\r\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc>' . $optsloc . '</select>' . "\r\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\r\n\t" . '  ' . "\r\n\t" . ' </fieldset>';
	$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
    $frm[1] .= "<table cellspacing=1 border=0>\r\n     <tr>\r\n\t\t<td>".$_SESSION['lang']['momordok']."</td>\r\n\t\t<td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \r\n\t    <td>".$_SESSION['lang']['tanggal']."</td><td>\r\n\t\t     <input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>\r\n\t    </td>\r\n\t </tr>\r\n\r\n\t </table>\r\n    </fieldset>\r\n    <fieldset>\r\n\t   <legend>".$_SESSION['lang']['detail']."</legend>\r\n\t   <div id=containerReceipt>\r\n\r\n\t   </div>\r\n\t </fieldset>\t \t \r\n\t ";
    foreach ($_SESSION['gudang'] as $key => $val) {
        $frm[1] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\r\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\r\n\t\t";
    }
    $frm[0] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>\r\n\t  <button class=mybutton onclick=cariBast()>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['sumber']."</td>\r\n\t  <td>".$_SESSION['lang']['tipe']."</td>\r\n\t  <td>".$_SESSION['lang']['momordok']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t  <td>".$_SESSION['lang']['ptpemilikbarang']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\t  \t \r\n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n\t  <td>".$_SESSION['lang']['rilis']."</td>\r\n\t  <td>".$_SESSION['lang']['status']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>\r\n\t   </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\t \r\n\t ";
    $frm[2] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['sudahditerima']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtrece size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariBapbReceived(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['sloc']."</td>\r\n\t  <td>".$_SESSION['lang']['tipe']."</td>\r\n\t  <td>".$_SESSION['lang']['momordok']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t  <td>".$_SESSION['lang']['pt']."</td>\r\n\t  <td>".$_SESSION['lang']['sumber']."</td>\t\r\n\t  <td>".$_SESSION['lang']['noreferensi']."</td> \r\n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n\t  <td>".$_SESSION['lang']['posted']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlistreceived>\r\n\t   </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\t \r\n\t ";
	$hfrm[1] = $_SESSION['lang']['terimamutasi'];
	$hfrm[2] = $_SESSION['lang']['sudahditerima'];
	$hfrm[0] = $_SESSION['lang']['barangdatang'];
	drawTab('FRM', $hfrm, $frm, 200, 900);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
