<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['mutasi']) . '</b>');
OPEN_BOX('', '<b>LAPORAN KELUAR MASUK PERSEDIAAN</b>');
echo '<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
//$str="SELECT 1 as level,d.karyawanid, d.namakaryawan, ".
//	"o.kodeorganisasi,o.namaorganisasi,o.induk ".
//	"FROM datakaryawan d ".
//	"INNER JOIN user u on u.karyawanid=d.karyawanid ".
//	"INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
//	"WHERE u.namauser= '" .$_SESSION['standard']['username'] ."'";
$optPt= makeOption2(getQuery("pt"),
	array( ),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

//$strG="SELECT kodeorganisasi,namaorganisasi FROM organisasi ".
//"WHERE tipe LIKE 'GUDANG%' ".
//"AND LEFT(kodeorganisasi,4) in ".
//"( ".
//"SELECT ".
//"o.kodeorganisasi ".
//"FROM  organisasi o ".
//"WHERE o.kodeorganisasi in ( ".
//"SELECT o.kodeorganisasi ".
//"FROM datakaryawan d ".
//"INNER JOIN user u on u.karyawanid=d.karyawanid ".
//"INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi ".
//"WHERE u.namauser='asep.saefudin' ".
//") ".
//")";
$optGudang= makeOption2(getQuery("gudang"),
	array(),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$str = 'select distinct substr(tanggal,1,7) as tanggal from ' . $dbname . '.log_transaksiht' . "\r\n" . '      order by tanggal desc';
function callbackPeriod($option,$value,$caption){
	$ret = array("newvalue"=>"","newcaption"=>"");
	if($option=='init'){
		$ret["newvalue"]=$value;
		$ret["newcaption"]=$caption;
	}
	if($option=='noninit'){
		$ret["newvalue"]=$value;
		$ret["newcaption"]=substr($value, 0, 7);
	}
	return $ret;
}
$optper= makeOption2($str,
	array(),
	array("valuefield"=>'tanggal',"captionfield"=> 'tanggal' ),
'callbackPeriod'
);
//$res = mysql_query($str);
//
//while ($bar = mysql_fetch_object($res)) {
//	$periode = substr($bar->tanggal, 0, 7);
//	$optper .= '<option value=\'' . $periode . '\'>' . substr($periode, 5, 2) . '-' . substr($periode, 0, 4) . '</option>';
//}

//$optGudang = '';
//$sgdng = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe like \'GUDANG%\'';
//
//#exit(mysql_error());
//($qgdng = mysql_query($sgdng)) || true;
//
//while ($rgdng = mysql_fetch_assoc($qgdng)) {
//	$optGudang .= '<option value=' . $rgdng['kodeorganisasi'] . '>' . $rgdng['namaorganisasi'] . '</option>';
//}

echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['pilihdata'] . '</legend>';
echo $_SESSION['lang']['pt'] . ':<select id=company_id name=company_id>' . $optPt . '</select>&nbsp;';
echo $_SESSION['lang']['pilihgudang'] . ':<select id=gudang_id name=gudang_id>' . $optGudang . '</select>&nbsp;';
echo $_SESSION['lang']['periode'] . ':<select id=period name=period>' . $optper . '</select>';
echo '<button class=mybutton onclick=save_pil()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t\t\t" . '     <button class=mybutton onclick=ganti_pil()>' . $_SESSION['lang']['ganti'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n\t" . ' </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<div id="cari_barang" name="cari_barang">' . "\r\n" . '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['findBrg'];
echo '</legend>' . "\r\n" . '<table cellspacing="1" border="0">' . "\r\n" . '<tr><td>';
echo $_SESSION['lang']['nm_brg'];
echo '</td><td>:</td><td><input type="text" id="nm_goods" name="nm_goods" maxlength="35" onKeyPress="return tanpa_kutip(event)" /> <button class="mybutton" onClick="cari_brng(\'';
echo $_SESSION['lang']['findBrg'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['findnoBrg'];
echo '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>\',\'\',event)">';
echo $_SESSION['lang']['find'];
echo ' </button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '    <div id="hasil_cari" name="hasil_cari" style="display:none;">' . "\r\n" . '    <fieldset>' . "\r\n" . '    <legend>';
echo $_SESSION['lang']['result'];
echo '</legend>' . "\r\n" . '     <img onclick=dataKeExcel(event,\'log_laporanKeluarMasukPerBarang_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=dataKePDF(event) title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\r\n" . '     <table cellspacing="1" border="0" id="table_data_barang">' . "\r\n" . '   <tbody id="isi_conten">' . "\r\n" . '    <tr id="isi_data_barang">' . "\r\n" . '    <td>';
echo $_SESSION['lang']['kodebarang'];
echo '</td>' . "\r\n" . '    <td>:</td>' . "\r\n" . '    <td id="kd_brg"></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr id="isi_data_barang">' . "\r\n" . '    <td>';
echo $_SESSION['lang']['namabarang'];
echo '</td>' . "\r\n" . '    <td>:</td>' . "\r\n" . '    <td id="nm_brg"></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr id="isi_data_barang">' . "\r\n" . '    <td>';
echo $_SESSION['lang']['satuan'];
echo '</td>' . "\r\n" . '    <td>:</td>' . "\r\n" . '    <td id="satuan_brg"></td>' . "\r\n" . '    </tr>' . "\r\n" . '    </tbody>' . "\r\n" . '    </table>' . "\r\n" . '    <table cellspacing="1" border="0">' . "\r\n\t\t" . '<thead>' . "\r\n" . '        ' . "\t" . '<tr class="rowheader">' . "\r\n" . '            <td>No.</td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['notransaksi'];
echo '</td>' . "\r\n" . '           <td align="center">';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['saldoawal'];
echo ' </td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['masuk'];
echo ' </td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['keluar'];
echo ' </td>' . "\r\n" . '            <td align="center">';
echo $_SESSION['lang']['saldo'];
echo ' </td>' . "\r\n" . '            </tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody id="contain">' . "\r\n" . '        </tbody>' . "\r\n" . '    </table>' . "\r\n" . '    </fieldset>' . "\r\n" . '    </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
echo close_body();
?>
