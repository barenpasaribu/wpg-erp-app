<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=\'JavaScript1.2\' src=\'js/supplier.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<u><b><font face=Verdana size=4 color=#000080>' . $_SESSION['lang']['data'] . ' ' . $_SESSION['lang']['supplier'] . ' </font></b></u>');
echo '<fieldset>' . "\r\n" . '      <legend>Input ' . $_SESSION['lang']['supplier'] . '/' . $_SESSION['lang']['kontraktor'] . '</legend>' . "\r\n\t" . '  <table>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '     <td>' . $_SESSION['lang']['Type'] . '</td><td><select id=tipe onchange="getKelompokSupplier(this.options[this.selectedIndex].value)"><option value=\'\'></option><option value=SUPPLIER>Supplier</option><option value=KONTRAKTOR>Contractor</option></select></td>' . "\r\n\t" . '     <td>' . $_SESSION['lang']['telp'] . '</td><td><input type=text class=myinputtext id=telp onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['kodekelompok'] . '</td><td><select id=kdkelompok onchange="getSupplierNumber(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text);"><option value=\'\'></option></select></td>' . "\r\n" . '          <td>' . $_SESSION['lang']['fax'] . '</td><td><input type=text class=myinputtext id=fax onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\t" . '  ' . "\r\n" . ' ' . "\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>Id.' . $_SESSION['lang']['supplier'] . '/' . $_SESSION['lang']['kontraktor'] . '</td><td><input type=text class=myinputtext disabled id=idsupplier></td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['email'] . '</td><td><input type=text class=myinputtext id=email onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['namasupplier'] . '</td><td><input type=text class=myinputtext id=namasupplier onkeypress=' . "\r" . 'eturn tanpa_kutip(event);" size=20 maxlength=45></td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['npwp'] . '</td><td><input type=text class=myinputtext id=npwp onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['alamat'] . '</td><td><input type=text class=myinputtext id=alamat onkeypress="return tanpa_kutip(event);" size=50 maxlength=100></td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['cperson'] . '</td><td><input type=text class=myinputtext id=cperson onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['kota'] . '</td><td><input type=text class=myinputtext id=kota onkeypress="return tanpa_kutip(event);" size=20 maxlength=30></td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['plafon'] . '</td><td><input type=text  onblur="change_number(this);"class=myinputtextnumber id=plafon onkeypress="return angka_doang(event);" size=15 maxlength=15 value=0></td>' . "\r\n\t" . '  </tr><tr>' . "\r\n\t" . '     <td>PKP</td><td><select id=pkp></option><option value=1>Ya</option><option value=0>Tidak</option></select></td>' . "\r\n\t" . '     <td></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  </table>' . "\r\n\t" . '  <input type=hidden id=method value=insert>' . "\r\n\t" . '<button class=mybutton onclick=saveSupplier()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '<button class=mybutton onclick=cancelSupplier()>' . $_SESSION['lang']['cancel'] . '</button>' . "\t" . '  ' . "\r\n\t" . '  </fieldset>';
CLOSE_BOX();
OPEN_BOX('', $_SESSION['lang']['plafon'] . ': <span id=captiontipe></span> ' . $_SESSION['lang']['namakelompok'] . ':<span id=captionkelompok></span>');
echo '<div style=\'width=100%; height:250px;overflow:scroll\'>' . "\r\n\t" . '     <table class=sortable cellspacing=1 border=0>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=header>' . "\r\n\t" . '     <td>' . $_SESSION['lang']['kodekelompok'] . '</td>' . "\r\n\t\t" . ' <td>Id.' . $_SESSION['lang']['supplier'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['alamat'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['cperson'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['kota'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['telp'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['fax'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['email'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['npwp'] . '</td>'. ' <td>' . 'PKP' . '</td>' . "\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['plafon'] . '</td>' . "\r\n\t\t" . ' </tr>' . "\r\n\t\t" . ' <tbody id=container>' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot></tfoot>' . "\r\n\t\t" . ' </table>' . "\r\n\t\t" . ' </div>' . "\r\n\t\t" . ' ';
CLOSE_BOX();
echo close_body();

?>
