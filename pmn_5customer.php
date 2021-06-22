<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language="javascript" src="js/pmn_5customer.js"></script>' . "\r\n" . '<fieldset>' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['customerlist'];
echo '</b></legend>' . "\r\n" . '<table cellpadding="2" cellspacing="2" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';

echo $_SESSION['lang']['klmpkcust'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="hidden" id="klcustomer_code"  />' . "\r\n" . '                <input type="text" id="nama_group" onchange="getCustomerNumber();" class="myinputtext" disabled="disabled"/> ' . "\r\n" . '                <img src=images/search.png class=dellicon title=';
echo $_SESSION['lang']['find'];
echo ' onclick="searchGruop(\'';
echo $_SESSION['lang']['findgroup'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['findgroup'];
echo '</legend>Find<input type=text class=myinputtext id=group_name><button class=mybutton onclick=findGroup()>Find</button></fieldset><div id=container_cari></div>\',event)";></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['kodecustomer'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="text" class="myinputtext" id="kode_cus" onkeypress="return tanpa_kutip(event);" onclick="getCustomerNumber();" readonly/></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['akun'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="hidden" id="akun_cust"  /><input type="text" id="nama_akun" class="myinputtext" disabled="disabled"/> <img src=images/search.png class=dellicon title=';
echo $_SESSION['lang']['find'];
echo ' onclick="searchAkun(\'';
echo $_SESSION['lang']['findnoakun'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['findnoakun'];
echo '</legend>Find<input type=text class=myinputtext id=no_akun><button class=mybutton onclick=findAkun()>Find</button></fieldset><div id=container_cari_akun></div>\',event)";>' . "\r\n" . '                <!--<input type="text" class="myinputtext" id="no_akun" onkeypress="return tanpa_kutip(event);"  />-->' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['nmcust'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="text" class="myinputtext" id="cust_nm" onkeypress="return tanpa_kutip(event);"  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['alamat'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="almt" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['kota'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="kta" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['telepon'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="tlp_cust" onkeypress="return angka_doang(event);"  />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['kntprson'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="kntk_person" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['plafon'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="plafon_cus" onkeypress="return angka_doang(event);" value="0" />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['nilaihutang'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="n_hutang" onkeypress="return angka_doang(event);"  value="0"/>' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['npwp'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="npwp_no" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['noseripajak'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="seri_no" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '        ' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        ' . "\r\n" . '        ' . "\r\n" . '        <tr>' . "\r\n" . '                <td>Penandatangan Kontrak</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="pk" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '       ' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        ' . "\r\n" . '        <tr>' . "\r\n" . '                <td>Jabatan Penandatangan Kontrak</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <input type="text" class="myinputtext" id="jpk" onkeypress="return tanpa_kutip(event);"  />' . "\r\n" . '        <input type="hidden" value="insert" id="method" />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        ' . "\r\n" . '        ' . "\r\n" . '        ' . "\r\n" . '        <tr>' . "\r\n" . '                <td colspan="3" align="center">' . "\r\n" . '                <button class=mybutton onclick=simpanPlgn()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '         <button class=mybutton onclick=batalPlgn()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '         <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['kodecustomer'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['kntprson'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['nmcust'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['telepon'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['noakun'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['akun'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['plafon'];
echo '</td> ' . "\r\n" . '         <td>';
echo $_SESSION['lang']['nilaihutang'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['klmpkcust'];
echo '</td>' . "\r\n" . '         <td>Penandatangan Kontrak</td>' . "\r\n" . '         <td>Jabatan Penandatangan Kontrak</td>' . "\r\n" . '         <td>Status</td><td colspan="2">Action</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody id="container">' . "\r\n" . '         ';
$srt = 'select * from ' . $dbname . '.pmn_4customer order by kodecustomer desc';

if ($rep = mysql_query($srt)) {
	$no = 0;

	while ($bar = mysql_fetch_object($rep)) {
		$sql = 'select * from ' . $dbname . '.pmn_4klcustomer where `kode`=\'' . $bar->klcustomer . '\'';

		#exit(mysql_error($conn));
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_object($query);
		$spr = 'select * from  ' . $dbname . '.keu_5akun where `noakun`=\'' . $bar->akun . '\'';

		#exit(mysql_error($conn));
		($rej = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rej);
		++$no;
		
		if( $bar->flag_aktif == "Y"){
			$status = "Aktif";
			$link_status = '<img src=images/application/application_delete.png class=resicon  title=\'Set Non-Aktif\' onclick="ubahStatusAktifasi(\'' . $bar->kodecustomer . '\',\'non-aktif\');">';
		}else{
			$status = "Non-Aktif";
			$link_status = '<img src=images/application/application_add.png class=resicon  title=\'Set Aktif\' onclick="ubahStatusAktifasi(\'' . $bar->kodecustomer . '\',\'aktif\');">';
		}
		echo '<tr class=rowcontent>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td>' . $bar->kodecustomer . '</td>' . "\r\n" . '                                  <td>' . $bar->kontakperson . '</td>' . "\r\n" . '                                  <td>' . $bar->namacustomer . '</td>' . "\r\n" . '                                  <td>' . $bar->telepon . '</td>' . "\r\n" . '                                  <td>' . $bar->akun . '</td>' . "\r\n" . '                                  <td>' . $bas->namaakun . '</td>' . "\r\n" . '                                  <td>' . $bar->plafon . '</td>' . "\r\n" . '                                  <td>' . $bar->nilaihutang . '</td>' . "\r\n" . '                                  <td>' . $res->kelompok . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . '  <td>' . $bar->pk . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . '  <td>' . $bar->jpk . '</td>' . "\r\n" . '                                  <td>'.$status.'</td><td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kodecustomer . '\',\'' . $bar->namacustomer . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\',\'' . $bar->telepon . '\',\'' . $bar->kontakperson . '\',\'' . $bar->akun . '\',\'' . $bar->plafon . '\',\'' . $bar->nilaihutang . '\',\'' . $bar->npwp . '\',\'' . $bar->noseri . '\',\'' . $bar->klcustomer . '\',\'' . $bas->namaakun . '\',\'' . $res->kelompok . '\',\'' . $bar->pk . '\',\'' . $bar->jpk . '\');"></td>' .'                                  <td>'.$link_status.'</td>' . "\r\n" . '  <td ><button class=mybutton onclick="trfWB(\'' . $bar->kodecustomer . '\',\'' . $bar->namacustomer .' | '. $res->kelompok . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\');">' . 'Kirim' . '</button></td>' . '                                 </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

echo '          </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\r\n" . '         </table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
