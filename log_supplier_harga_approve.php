<?php
	require_once 'master_validation.php';
	include 'lib/eagrolib.php';
	include_once 'lib/zLib.php';
	include_once 'lib/devLibrary.php';
	
	echo open_body();
	include 'master_mainMenu.php';
	OPEN_BOX('', '<b>SUPPLIER TBS APPROVE</b>');
	echo "	<link rel=stylesheet type=text/css href=\"style/zTable.css\">
			<script language=\"javascript\" src=\"js/zMaster.js\"></script>
			<script language=javascript src=js/zTools.js></script>
			<script type=\"text/javascript\" src=\"js/log_supplier_harga_approve.js\" /></script>
		";
	echo "<table>";
	echo "<tr valign=middle>";
	echo "<td align=center style='width:100px;cursor:pointer;' onclick=showForm()>
			<img class=delliconBig src=images/skyblue/addbig.png title='".$_SESSION['lang']['new']."'>
			<br>
			".$_SESSION['lang']['new']."
		</td>";
	echo "<td align=center style='width:100px;cursor:pointer;' onclick=susunanData()>
			<img class=delliconBig src=images/skyblue/list.png title='".$_SESSION['lang']['list']."'>
			<br>
			".$_SESSION['lang']['list']."
		  </td>";
	echo "	</tr>";
	echo "</table>";
	CLOSE_BOX();
?>
	<div id="formInput"></div>
	<br>
<?php 
	OPEN_BOX("", "<b id=judul>SUSUNAN DATA APPROVE</b>");
?>
	<fieldset><legend><?= $_SESSION['lang']['list']; ?></legend>
		<script>loadData()</script>
		<table cellpading="1" cellspacing="1" class="sortable" id='loadDataTable'></table>
	</fieldset>
<?php
	CLOSE_BOX();
	echo close_body();
?>