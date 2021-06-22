<?php

require_once 'master_validation.php';

include 'lib/eagrolib.php';

include 'lib/zLib.php';
include 'lib/devLibrary.php';

echo open_body();

echo '<script language=javascript1.2 src="js/log_transaksi.js"></script>' . "\n";

include 'master_mainMenu.php';

if (isTransactionPeriod()) {

	$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', '', 1);

	OPEN_BOX('', '<b>' . $_SESSION['lang']['penerimaanbarang'] . ':</b>');

	$frm[0] = '';

	$frm[1] = '';

	echo '<div id=\'optSupp\' style=\'display:none\'>';



	foreach ($optSupp as $key => $value) {

		echo '<option value=\'' . $key . '\'>' . $value . '</option>';

	}



	echo '</div>';

	echo '<fieldset><legend>';

	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';

	echo '</legend>';



	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {

		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'GUDANG\'' . "\n" . '       and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\n" . '       order by namaorganisasi desc';

	}

	else {

		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (induk=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe in(\'GUDANGTEMP\',\'GUDANG\') order by namaorganisasi desc';

	}



	$res = mysql_query($str);

	$optsloc = '<option value=\'\'></option>';



	while ($bar = mysql_fetch_object($res)) {

		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . ' ( ' . $bar->kodeorganisasi . ')</option>';

	}



	echo '<fieldset>' . "\n" . '     <legend>' . "\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\n" . '     </legend>' . "\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc>' . $optsloc . '</select>' . "\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\n\t" . '  ' . "\n\t" . ' </fieldset>';

	$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';

    $frm[0] .= "\n     <table cellspacing=1 border=0>\n     <tr>\n\t\t<td>".$_SESSION['lang']['momordok']."</td>\n\t\t<td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \n\t    <td>".$_SESSION['lang']['tanggal']."</td><td>\n\t\t     <input type=text class=myinputtext id=tanggal size=25 onmousemove=setCalendar(this.id) onkeypress=\"return false;\" value='".date('d-m-Y')."'>\n\t\t</td>\n\t </tr>\n\t <tr>\n\t <td>".$_SESSION['lang']['supplier']."</td><td><input type=hidden value='' id=idsupplier><input type=text id=supplier class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled></td>\n\t <td>".$_SESSION['lang']['suratjalan']."</td><td><input type=text id=nosj class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>\n\t </tr>\n\t <tr>\n\t <td>".$_SESSION['lang']['faktur']."</td><td><input type=text id=nofaktur class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>\n\t <td>".$_SESSION['lang']['nopo']."</td><td><input type=text id=nopo class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\">\n\t    <img src=images/zoom.png title='".$_SESSION['lang']['find']."' class=resicon onclick=cariPO('".$_SESSION['lang']['find']."',event)>\n\t    <button class=mybutton onclick=getPOSupplier() id=btnheader>".$_SESSION['lang']['tampilkan']."</button>\n\t </td>\n\t <td></td>\n\t </tr>\n\t </table>";

    foreach ($_SESSION['gudang'] as $key => $val) {

        $frm[0] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\n\t\t";

    }

    $frm[0] .= "</fieldset>\n    <fieldset>\n\t   <legend>".$_SESSION['lang']['detail']."</legend>\n\t   <div id=container>\n\t   </div>\n\t </fieldset>\n\t ";

    $frm[1] .= "<fieldset>\n\t   <legend>".$_SESSION['lang']['list']."</legend>\n\t  

				<fieldset><legend></legend> Cari No. Transaksi / No. PO 

				<input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\">\n\t 

				<button class=mybutton onclick=cariBapb()>"

				.$_SESSION['lang']['find']."</button>\n\t  </fieldset>\n\t 

				<table class=sortable cellspacing=1 border=0>\n      <thead>\n\t  <tr class=rowheader>\n\t  <td>No.</td>\n\t  <td>".$_SESSION['lang']['sloc']."</td>\n\t  <td>".$_SESSION['lang']['tipe']."</td>\n\t  <td>".$_SESSION['lang']['momordok']."</td>\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\n\t  <td>".$_SESSION['lang']['pt']."</td>\n\t  <td>".$_SESSION['lang']['nopo']."</td>\t\n\t  <td>".$_SESSION['lang']['supplier']."</td> \n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\n\t  <td>".$_SESSION['lang']['posted']."</td>\n\t  <td></td>\n\t  </tr>\n\t  </head>\n\t   <tbody id=containerlist>\n\t   </tbody>\n\t   <tfoot>\n\t   </tfoot>\n\t   </table>\n\t </fieldset>\t \n\t ";

	$hfrm[0] = $_SESSION['lang']['penerimaanbarang'];

	$hfrm[1] = $_SESSION['lang']['list'];

	drawTab('FRM', $hfrm, $frm, 200, 900);

}

else {

	echo ' Error: Transaction Period missing';

}



CLOSE_BOX();

close_body();



?>

