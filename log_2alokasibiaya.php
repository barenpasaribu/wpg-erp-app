<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo "\r\n" . '<script type="text/javascript" src="js/log_2alokasibiaya.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script>' . "\r\n" . '    function zExceldetail(ev,tujuan,passParam)' . "\r\n" . '{' . "\r\n\t" . 'judul=\'Report Excel\';' . "\r\n" . '        var passP = passParam.split(\'##\');' . "\r\n\t\r\n" . '    var param = "proses=exceldetail";' . "\r\n" . '    for(i=0;i<passP.length;i++) {' . "\r\n" . '       // var tmp = document.getElementById(passP[i]);' . "\r\n\t" . '   ' . "\t" . 'a=i;' . "\r\n" . '        param += "&"+passP[a]+"="+passP[i+1];' . "\r\n" . '    }' . "\r\n\t\r\n\t" . 'printFile(param,tujuan,judul,ev)' . "\t\r\n" . '}' . "\r\n" . 'function printFile(param,tujuan,title,ev)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'700\';' . "\r\n" . '   height=\'250\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,ev); ' . "\t\r\n" . '}' . "\r\n" . '</script>' . "\r\n\r\n";
$arr = '##periodeBeli';
$arr2 = '##periode';
$str = 'select distinct periode from ' . $dbname . '.log_5saldobulanan order by periode desc';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optPeriode .= '<option value=\'' . $bar->periode . '\'>' . substr($bar->periode, 5, 2) . '-' . substr($bar->periode, 0, 4) . '</option>';
}

OPEN_BOX('', '<b>LAPORAN ALOKASI BIAYA</b><br>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = $_SESSION['lang']['pembelianBarang'];
$hfrm[1] = $_SESSION['lang']['pemakaianBarang'];
drawTab('FRM', $hfrm, $frm);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
