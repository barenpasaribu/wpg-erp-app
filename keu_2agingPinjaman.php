<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n<script language=javascript>\r\n\tfunction batal()\r\n\t{\r\n\t\tlocation.reload();\t\r\n\t}\r\n</script>\r\n\r\n";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.'.organisasi where length(kodeorganisasi)=4 ORDER BY kodeorganisasi';
} else {
    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
        $sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.'.organisasi where kodeorganisasi in (select kodeunit from '.$dbname.".bgt_regional_assignment\r\n\t\t where regional='".$_SESSION['empl']['regional']."') and tipe!='HOLDING' ";
    } else {
        $sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    }
}

$qry = mysql_query($sql);
while ($data = mysql_fetch_assoc($qry)) {
    $optorg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
}
$sql = 'SELECT karyawanid,namakaryawan FROM '.$dbname.".datakaryawan where bagian in ('HO_FICO','HO_ACTX') and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
$qry = mysql_query($sql);
while ($data = mysql_fetch_assoc($qry)) {
    $optKar .= '<option value='.$data['karyawanid'].'>'.$data['namakaryawan'].'</option>';
}
$a = 'SELECT noakun, namaakun FROM '.$dbname.".keu_5akun WHERE LEFT( noakun, 4 ) IN ('1180','1140','1130') AND detail =1";
$b = mysql_query($a);
while ($c = mysql_fetch_assoc($b)) {
    $optTipe .= '<option value='.$c['noakun'].'>'.$c['namaakun'].'</option>';
}
include 'master_mainMenu.php';
OPEN_BOX();
$arr = '##kdorg##noakun##tgl##dibuat##diperiksa';
echo "<fieldset style='float:left;'><legend><b>Laporan Aging Schedule Pinjaman</b></legend>\r\n<table>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=kdorg style='width:200px;'>".$optorg."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['tipe']." Uang Muka</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=noakun style='width:200px;'>".$optTipe."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>Sampai ".$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >\r\n\t\t</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['dibuat']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=dibuat style='width:200px;'>".$optKar."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['diperiksa']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=diperiksa style='width:200px;'>".$optKar."</select></td>\r\n\t</tr>\t\r\n\t\r\n\t<tr>\r\n\t\t<td colspan=100>&nbsp;</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=100>\r\n\t\t<button onclick=zPreview('keu_slave_2agingPinjaman','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t<button onclick=zExcel(event,'keu_slave_2agingPinjaman.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>\r\n\t\t\r\n\t\t<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>\r\n\t\t</td>\r\n\t</tr>\r\n</table>\r\n</fieldset>";
echo "\r\n<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>\r\n<div id='printContainer'  >\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>