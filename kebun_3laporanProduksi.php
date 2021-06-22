<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

$frm[0] = '';

$frm[1] = '';

$frm[2] = '';

OPEN_BOX();

$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN'";

$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$optper = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sTgl = "select distinct periode from ".$dbname.".setup_periodeakuntansi where kodeorg like '".$_SESSION['empl']['kodeorganisasi']."%'";
$qTgl = mysql_query($sTgl) ;

while ($rTgl = mysql_fetch_assoc($qTgl)) {

    $optper .= "<option value='".$rTgl['periode']."'>".$rTgl['periode']."</option>";

}

$intex = ['External', 2 => 'Internal'];
$optTbs = "<option value=''>".$_SESSION['lang']['all'].'</option>';

foreach ($intex as $dt => $rw) {

    $optTbs .= '<option value='.$dt.'>'.$rw.'</option>';

}

$arr = '##periode##tipeIntex##unit';

echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<script>\r\nfunction getKode()\r\n{\r\n\ttipeIntex=document.getElementById('tipeIntex').options[document.getElementById('tipeIntex').selectedIndex].value;\r\n\tparam='tipeIntex='+tipeIntex+'&proses=getKdorg';\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n\t//alert(param);\t\r\n    \r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n                  \tdocument.getElementById('unit').innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n\r\n}\r\nfunction getAfd(id)\r\n{\r\n\tkdOrg=document.getElementById('kdOrg_'+id).getAttribute('value');\r\n\ttglAfd=document.getElementById('tanggal_'+id).getAttribute('value');\r\n\tparam=\"kodeOrg=\"+kdOrg+\"&proses=getAfdeling\"+\"&brsKe=\"+id+\"&tglAfd=\"+tglAfd;\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n\t//alert(param);\t\r\n    \r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t//\talert(con.responseText);\r\n                  \tdocument.getElementById('detail_'+id).innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n}\r\nfunction detailBlok(idAwal,id)\r\n{\r\n\tkdBlok=document.getElementById('kdBlok_'+idAwal+'_'+id).innerHTML;\r\n\tnospb=document.getElementById('nospb_'+idAwal+'_'+id).innerHTML;\r\n\ttgl=document.getElementById('tanggal_'+idAwal).innerHTML;\r\n\t\r\n\tparam='kdBlok='+kdBlok+'&proses=getPrestasi'+'&tgl='+tgl+'&brsKe='+idAwal+'&endKe='+id+'&nospb='+nospb;\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n\r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t//\talert(con.responseText);\r\n                  \tdocument.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n}\r\nfunction closeBlok(idAwal,id)\r\n{\r\n\tdocument.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML='';\r\n}\r\nfunction closeAfd(id)\r\n{\r\n\tdocument.getElementById('detail_'+id).innerHTML='';\r\n}\r\nfunction batal()\r\n{\r\n\tdocument.getElementById('periode').value='';\r\n\tdocument.getElementById('tipeIntex').value='';\t\r\n\tdocument.getElementById('unit').value='';\r\n\tdocument.getElementById('printContainer').innerHTML='';\r\n\t\r\n}\r\nfunction batal2()\r\n{\r\n\tdocument.getElementById('periodeId').value='';\r\n\tdocument.getElementById('unitId').value='';\t\r\n\t\r\n\tdocument.getElementById('printContainer2').innerHTML='';\r\n\t\r\n}\r\n\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";

$frm[0] .= "<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>".$_SESSION['lang']['rProdKebun']."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>".$_SESSION['lang']['periode'].'</label></td><td><select id="periode" name="periode" style="width:150px">'.$optper."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['tbs'].'</label></td><td><select id="tipeIntex" name="tipeIntex" onchange="getKode()" style="width:150px">'.$optTbs."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['unit'].'/'.$_SESSION['lang']['supplier']."</td><td><select id=\"unit\" style=\"width:150px\"><option value=''>".$_SESSION['lang']['all']."</option></select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3laporanProduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>\r\n<button onclick=\"zPdf('kebun_slave_3laporanProduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['pdf']."</button>\r\n<button onclick=\"zExcel(event,'kebun_slave_3laporanProduksi.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['excel']."</button>\r\n<button onclick=batal() class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['cancel']."</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>";

$optUniDt .= "<option value=''>".$_SESSION['lang']['all'].'</option>';

$sUnit = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by namaorganisasi asc";

$qUnit = mysql_query($sUnit) ;

while ($rUnit = mysql_fetch_assoc($qUnit)) {

    $optUniDt .= "<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi'].'</option>';

}

$arr2 = '##periodeId##unitId';

$frm[0] .= "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";

$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['rProdKebundetail'].'</legend>';

$frm[1] .= '<table cellspacing=1 border=0>';

$frm[1] .= '<tr><td><labe>'.$_SESSION['lang']['periode'].'</label></td><td><select id=periodeId style=width:150px>'.$optper.'</select></td></tr>';

$frm[1] .= '<tr><td><labe>'.$_SESSION['lang']['unit'].'</label></td><td><select id=unitId style=width:150px>'.$optUniDt.'</select></td></tr>';

$frm[1] .= "<tr><td colspan=2>\r\n<button onclick=\"zPreview('kebun_slave_3laporanProduksi2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>\r\n<button onclick=\"zPdf('kebun_slave_3laporanProduksi2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['pdf']."</button>\r\n<button onclick=\"zExcel(event,'kebun_slave_3laporanProduksi2.php','".$arr2."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['excel']."</button>\r\n<button onclick=batal2() class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['cancel'].'</button></td></tr>';

$frm[1] .= '</table></fieldset>';

$frm[1] .= "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer2' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";

$arr3 = '##periodetahun##unittahun';

$frm[2] .= '<fieldset><legend>Production Trend</legend>';

$frm[2] .= '<table cellspacing=1 border=0>';

$frm[2] .= '<tr><td><labe>'.$_SESSION['lang']['periode']."</label></td><td><select id=periodetahun style=width:150px>\r\n                   <option value='".date('Y')."'>".date('Y')."</option>\r\n                   <option value='".(date('Y') - 1)."'>".(date('Y') - 1)."</option>\r\n                   <option value='".(date('Y') - 2)."'>".(date('Y') - 2)."</option>    \r\n                   </select></td></tr>";

$frm[2] .= '<tr><td><labe>'.$_SESSION['lang']['unit'].'</label></td><td><select id=unittahun style=width:150px>'.$optUniDt.'</select></td></tr>';

$frm[2] .= "<tr><td colspan=2>\r\n<button onclick=\"zPreview('kebun_slave_3laporanProduksi3','".$arr3."','printContainer3')\" class=\"mybutton\" name=\"preview2\" id=\"preview2\">".$_SESSION['lang']['preview']."</button>\r\n<button onclick=\"zExcel(event,'kebun_slave_3laporanProduksi3.php','".$arr3."')\" class=\"mybutton\" name=\"excel2\" id=\"excel2\">".$_SESSION['lang']['excel']."</button>\r\n<button onclick=batal2() class=\"mybutton\" name=\"batal2\" id=\"batal2\">".$_SESSION['lang']['cancel'].'</button></td></tr>';

$frm[2] .= '</table></fieldset>';

$frm[2] .= "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer3' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";

$hfrm[0] = $_SESSION['lang']['rProdKebun'];

$hfrm[1] = $_SESSION['lang']['rProdKebundetail'];

$hfrm[2] = 'Production Trend';

drawTab('FRM', $hfrm, $frm, 200, 900);

CLOSE_BOX();

echo close_body();



?>