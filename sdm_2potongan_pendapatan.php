<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji order by periode desc';
} else {
    $optOrg = '<option value="">'.$_SESSION['lang']['all'].'</option>';
    $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
}

$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
//if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') order by namaorganisasi asc ";
//} else {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
//}
//
//$qOrg = mysql_query($sOrg);
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}
$optOrg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optGaji = "<option value='All'>".$_SESSION['lang']['all'].'</option>';
$arrsgaj = getEnum($dbname, 'datakaryawan', 'sistemgaji');
foreach ($arrsgaj as $kei => $fal) {
    $optGaji .= "<option value='".$kei."'>".$_SESSION['lang'][strtolower($fal)].'</option>';
}
$arr = '##kdOrg##periode##tgl1##tgl2##sistemGaji';
$arrKry = '##kdeOrg##period##idKry##tgl_1##tgl_2';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\nfunction getTgl()\r\n{\r\n\tperiode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;\r\n\tkdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;\r\n\tparam='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdUnit;\r\n\t//alert(param);\r\n\ttujuan='sdm_slave_2potongan_pendapatan';\r\n\tpost_response_text(tujuan+'.php?'+param, param, respon);\r\n\tfunction respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n                        ar=con.responseText.split(\"###\");\r\n                        document.getElementById('tgl1').value=ar[0];\r\n                        document.getElementById('tgl2').value=ar[1];\r\n                        document.getElementById('tgl1').disabled=true;\r\n                        document.getElementById('tgl2').disabled=true;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n//\r\n//  alert(fileTarget+'.php?proses=preview', param, respon);\r\n}\r\n
function getKry(){
    kdeOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
	param='kdeOrg='+kdeOrg;
	tujuan='sdm_slave_2potongan_pendapatan';
post_response_text(tujuan+'.php?proses=getKry', param, respon);
function respon() {
	if (con.readyState == 4) {
	if (con.status == 200) {
		busy_off();
		if (!isSaveResponse(con.responseText)) {
			alert('ERROR TRANSACTION,\\n' + con.responseText);
		} else {
			// Success Response
			dt=con.responseText.split('###');
			document.getElementById('idKry').innerHTML=dt[1];
		}
	} else {
		busy_off();
		error_catch(con.status); 
	}   
}
}
}
function getTgl2()\r\n{\r\n    periode=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;\r\n    kdUnit=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;\r\n    param='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdUnit;\r\n    tujuan='sdm_slave_2potongan_pendapatan';\r\n    post_response_text(tujuan+'.php?'+param, param, respon);\r\n    function respon() {\r\n    if (con.readyState == 4) {\r\n        if (con.status == 200) {\r\n            busy_off();\r\n            if (!isSaveResponse(con.responseText)) {\r\n                alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n            } else {\r\n                // Success Response\r\n                                            ar=con.responseText.split(\"###\");\r\n                                            document.getElementById('tgl_1').value=ar[0];\r\n                                            document.getElementById('tgl_2').value=ar[1];\r\n                                            document.getElementById('tgl_1').disabled=true;\r\n                                            document.getElementById('tgl_2').disabled=true;\r\n            }\r\n        } else {\r\n            busy_off();\r\n            error_catch(con.status);\r\n        }\r\n    }\r\n}\r\n//\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n}\r\n\r\n
function getKry()\r\n{
\r\n\tkdeOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;\r\n\tparam='kdeOrg='+kdeOrg;\r\n\ttujuan='sdm_slave_2rekapabsen';\r\n\tpost_response_text(tujuan+'.php?proses=getKry', param, respon);\r\n\tfunction respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response
    			dt=con.responseText.split('###');
			document.getElementById('idKry').innerHTML=dt[0];\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n}
function Clear1()\r\n{
document.getElementById('tgl1').value='';\r\n\tdocument.getElementById('tgl2').value='';\r\n\tdocument.getElementById('tgl1').disabled=false;\r\n\tdocument.getElementById('tgl2').disabled=false;\r\n\tdocument.getElementById('kdOrg').value='';\r\n\tdocument.getElementById('periode').value='';\r\n\tdocument.getElementById('printContainer').innerHTML='';\r\n}\r\nfunction Clear2()\r\n{\r\n\tdocument.getElementById('tgl_1').value='';\r\n\tdocument.getElementById('tgl_2').value='';\r\n\tdocument.getElementById('tgl_1').disabled=false;\r\n\tdocument.getElementById('tgl_2').disabled=false;\r\n\tdocument.getElementById('kdeOrg').value='';\r\n\tdocument.getElementById('period').value='';\r\n\tdocument.getElementById('idKry').innerHTML=\"<option value''></option>\";\r\n\tdocument.getElementById('printContainer').innerHTML='';\r\n}\r\n</script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['lapPotongan'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px">';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()"><option value=""></option>';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['sistemgaji'];
echo '</label></td><td><select id="sistemGaji" name="sistemGaji" style="width:150px">';
echo $optGaji;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2potongan_pendapatan','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2potongan_pendapatan','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2potongan_pendapatan.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div >\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['lapPotongan'].'/'.$_SESSION['lang']['karyawan'];
echo " </b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdeOrg" name="kdeOrg" style="width:150px" onchange="getKry()">';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['namakaryawan'];
echo "</label></td><td><select id=\"idKry\" name=\"idKry\" style=\"width:150px\"><option value=\"\"></option></select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="period" name="period" style="width:150px" onchange="getTgl2()"><option value=""></option>';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_1\" name=\"tgl_1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_2\" name=\"tgl_2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2potongan_pendapatan_kary','";
echo $arrKry;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2potongan_pendapatan_kary','";
echo $arrKry;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2potongan_pendapatan_kary.php','";
echo $arrKry;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear2()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>