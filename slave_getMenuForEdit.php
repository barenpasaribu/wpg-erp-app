<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$id = $_POST['id'];
$str = 'select caption,caption2,caption3, action, class as cl,type from ' . $dbname . '.menu ' . "\r\n\t" . '        where id=' . $id;
$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
	echo ' Gagal, Item menu tsb sudah dihapus';
}
else {
	while ($bar = mysql_fetch_object($res)) {
		$caption = $bar->caption;
		$caption2 = $bar->caption2;
		$caption3 = $bar->caption3;
		$action = $bar->action;
		$class = $bar->cl;
		$type = $bar->type;
	}

	if ($class == 'devider') {
		echo ' Gagal, Devider tidak dapat di ganti/edit';
	}
	else {
		if (($class == 'title') || ($type == 'master')) {
			$disabled = 'disabled';
		}
		else {
			$disabled = '';
		}

		echo '<span style=\'text-align:center;\'>' . "\r\n\t\t" . '  <input type=text value=\'' . $caption . '\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=editcaption' . $id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\r\n" . '                                          <input type=text value=\'' . $caption2 . '\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=editcaption2' . $id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\r\n" . '                                          <input type=text value=\'' . $caption3 . '\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=editcaption3' . $id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>    ' . "\r\n\t" . '                      <input type=text value=\'' . $action . '\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=editaction' . $id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this) ' . $disabled . '>' . "\r\n\t\t" . '  <input type=button class=mybutton value=Save onclick=saveEditedMenu(\'' . $id . '\');>' . "\r\n\t\t" . '  <input type=button class=mybutton value=Close onclick="clearFormEdit(\'edit' . $id . '\');">' . "\r\n\t\t" . '  </span>';
	}
}

?>
