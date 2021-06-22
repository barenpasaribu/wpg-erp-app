<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
echo "<script> warn='";
echo $_SESSION['lang']['transaksi'] . ' ' . $_SESSION['lang']['detail'] . ' ' . $_SESSION['lang']['harus'] . ' ' . $_SESSION['lang']['lihat'] . ' ' . $_SESSION['lang']['per'] . ' ' . $_SESSION['lang']['gudang'];
echo "';</script>";
echo "<script language=javascript1.2 src='js/log_laporan.js'></script>";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['laporanstok']) . '</b>');

$str = 'select distinct periode from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '      order by periode desc';

function callbackPeriod($option,$value,$caption){
	$ret = array("newvalue"=>"","newcaption"=>"");
	if($option=='init'){
		$ret["newvalue"]=$value;
		$ret["newcaption"]=$caption;
	}
	if($option=='noninit'){
		$ret["newvalue"]=$value;
		$ret["newcaption"]=substr($value, 5, 2) . '-' . substr($value, 0, 4);
	}
	return $ret;
}
$optper = makeOption2($str,
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['sekarang'] ),
	array("valuefield"=>'periode',"captionfield"=> 'periode' ),
	'callbackPeriod'
);
$optper1=$optper;

$optpt= makeOption2(getQuery("pt"),
	array(),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$optgudang1= makeOption2(getQuery("gudang"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']." "),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$optgudang= makeOption2(getQuery("gudang"),
	array(),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['persediaanfisikharga'].' '.$_SESSION['lang']['bulanan']."</legend>\r\n\t ".$_SESSION['lang']['pt']."<select id=pt style='width:150px;' onchange=hideById('printPanel')>".$optpt."</select>\r\n\t ".$_SESSION['lang']['sloc']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang1."</select>\r\n\t ".$_SESSION['lang']['periode']."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n\t <button class=mybutton onclick=getLaporanFisikHarga()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>";
$frm[0] .= "<span id=printPanel style='display:none;'>\r\n     <span id=orglegend></span>   \r\n     <img onclick=fisikKeExceltab0(event,'log_laporanPersediaanFisikHarga_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=fisikKePDFHarga(event,'log_laporanPersediaanFisikHarga_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span> \r\n     <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td rowspan=2 align=center>No.</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['saldoawal']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['masuk']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['keluar']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['saldo']."</td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t</tr>   \r\n\t\t </thead>\r\n\t\t <tbody id=container>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
$frm[1] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['persediaanfisikharga'].' '.$_SESSION['language']['setahun']."</legend>\r\n\t ".$_SESSION['lang']['pt']."<select id=pt1 style='width:150px;' onchange=hideById('printPanel1')>".$optpt."</select>\r\n\t ".$_SESSION['lang']['sloc']."<select id=gudang1 style='width:150px;' onchange=hideById('printPanel1')>".$optgudang1."</select>\r\n\t ".$_SESSION['lang']['periode']."<select id=periode1 onchange=hideById('printPanel1')>".$optper1."</select>\r\n\t <button class=mybutton onclick=getLaporanFisikHarga1()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>";
$frm[1] .= "<span id=printPanel1 style='display:none;'>\r\n     <span id=orglegend1></span>   \r\n     <img onclick=fisikKeExcelT1(event,'log_laporanPersediaanFisikHargaTahunan_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=fisikKePDFT1(event,'log_laporanPersediaanFisikHargaTahunan_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span> \r\n     <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td rowspan=2 align=center>No.</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['saldoawal']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['masuk']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['keluar']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['saldo']."</td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t</tr>   \r\n\t\t </thead>\r\n\t\t <tbody id=container1>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
//$optUnit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
//$optGdng = $optUnit;
//$sUnit = 'select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'GUDANG\',\'GUDANGTEMP\') order by namaorganisasi asc';
//
//#exit(mysql_error($conn));
//($qUnit = mysql_query($sUnit)) || true;
//
//while ($rUnit = mysql_fetch_assoc($qUnit)) {
//	$optUnit .= '<option value=\'' . $rUnit['kodeorganisasi'] . '\'>' . $optNmOrg[$rUnit['kodeorganisasi']] . '</option>';
//}

//$str=" SELECT ".
//	"o.kodeorganisasi,o.namaorganisasi,o.induk ".
//	"FROM  organisasi o    ".
//	"WHERE o.induk in ( ".
//	"SELECT o.kodeorganisasi ".
//	"FROM datakaryawan d ".
//	"INNER JOIN user u on u.karyawanid=d.karyawanid ".
//	"INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
//	"WHERE u.namauser='" .$_SESSION['standard']['username'] ."'  ".
//	")";
$optUnit= makeOption2(getQuery("lokasitugas"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$optPeriode = '';
$x = 0;

while ($x < 13) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optPeriode .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	++$x;
}

$frm[2] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['persediaanfisikharga'].' Per '.$_SESSION['lang']['unit']."</legend>\r\n\t ".$_SESSION['lang']['unit']."<select id=unitDt style='width:150px;'>".$optUnit."</select>\r\n\t ".$_SESSION['lang']['periode']."<select id=periode2 onchange=hideById('printPanel2')>".$optPeriode."</select>\r\n\t <button class=mybutton onclick=getLaporanFisikHarga2()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>";
$frm[2] .= "<span id=printPanel2 style='display:none;'>\r\n     <span id=orglegend2></span>   \r\n     <img onclick=fisikKeExcelT2(event,'log_LaporanPersediaanFisikHarga_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=fisikKePDFT2(event,'log_LaporanPersediaanFisikHarga_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span> \r\n     <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td rowspan=2 align=center>No.</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t  <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['saldoawal']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['masuk']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['keluar']."</td>\r\n\t\t\t  <td colspan=3 align=center>".$_SESSION['lang']['saldoakhir']."</td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t   <td align=center>".$_SESSION['lang']['kuantitas']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t   <td align=center>".$_SESSION['lang']['totalharga']."</td>\t   \r\n\t\t\t</tr>   \r\n\t\t </thead>\r\n\t\t <tbody id=container2>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
$hfrm[0] = $_SESSION['lang']['persediaanfisikharga'] . ' ' . $_SESSION['lang']['bulanan'];
$hfrm[1] = $_SESSION['lang']['persediaanfisikharga'] . ' ' . $_SESSION['lang']['setahun'];
$hfrm[2] = $_SESSION['lang']['persediaanfisikharga'] . ' Per ' . $_SESSION['lang']['unit'];
drawTab('FRM', $hfrm, $frm, 200, 900);
CLOSE_BOX();
close_body();

?>
