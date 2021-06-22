<?php
	require_once 'master_validation.php';
	include 'lib/eagrolib.php';
	include 'lib/zLib.php';
	include 'lib/zFunction.php';

	echo open_body();
	echo '	<script language=javascript src=js/zTools.js></script>
			<script language=javascript1.2 src=\'js/log_5supplier_harga.js\'></script>';
	include 'master_mainMenu.php';
	OPEN_BOX();
	$optKlSupplier = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$optSupplier = $optKlSupplier;

	if (substr($_SESSION['empl']['lokasitugas'],0,3) == "SSP" || substr($_SESSION['empl']['lokasitugas'],0,3) == "LSP") {
		$queryKlSupplier = 'SELECT kode, kelompok FROM '.$dbname.'.log_5klsupplier where kelompok like "%'.substr($_SESSION['empl']['lokasitugas'],0,3).'" and isTBS=1';
	}else{
		$queryKlSupplier = 'SELECT kodesubkelompok, subkelompok FROM '.$dbname.'.log_5klsupplier where isTBS=1';
	}
	
	$runQueryKlSupplier = mysql_query($queryKlSupplier);
	while ($resultQueryKlSupplier = mysql_fetch_assoc($runQueryKlSupplier)) {
		$optKlSupplier .= '	<option value=\'' . $resultQueryKlSupplier['kode'] . '\'>
								' . $resultQueryKlSupplier['kode'] . ' - ' . $resultQueryKlSupplier['kelompok'] . '
							</option>';
	}
?>
	<fieldset style=width:350px>
		<legend>Supplier Harga</legend>
		<table>
			<tr>
				<td><?= $_SESSION['lang']['tanggal'] ?></td>
				<td><input type=text class=myinputtext id=tgl1_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
				</td>
			</tr>
			<tr>
				<td>Kelompok Supplier</td>
				<td><select id="kode_klsupplier" onclick="setOptionSupplier()"><?= $optKlSupplier ?></select></td>
			</tr>
			<tr>
				<td>Supplier</td>
				<td>
					<select id="kode_supplier" style="width:100%;"><?= $optSupplier ?></select>
				</td>
			</tr>
			<tr>
				<td>Harga</td>
				<td>
					<input type="text" id="harga" name="harga" maxlength="10" style="width:98%;">
				</td>
			</tr>
		</table>
		<br>
		<button class="mybutton" onclick="simpanSupplierHarga()">
			<?= $_SESSION['lang']['save'] ?>
		</button>
		
		<button class="mybutton" onclick="clone()">
			Clone
		</button>

	</fieldset>
<?php
	CLOSE_BOX();
	OPEN_BOX("SUSUNAN MASTER HARGA HARIAN");
?>
	<fieldset><legend><?= $_SESSION['lang']['list']; ?></legend>
		<script>loadData()</script>
		<table cellpading="1" cellspacing="1" class="sortable" id='loadDataTable'></table>
	</fieldset>
<?php
	CLOSE_BOX();
	echo close_body();
?>