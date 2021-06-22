<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='KEBUN'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$intex = ['Eksternal', 2 => 'Internal'];
$optTbs = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optTbsRe = "<option value='3'>".$_SESSION['lang']['all'].'</option>';
foreach ($intex as $dt => $rw) {
    $optTbs .= '<option value='.$dt.'>'.$rw.'</option>';
    $optTbsRe .= '<option value='.$dt.'>'.$rw.'</option>';
}
$arr = '##kdPabrik##tgl_1##tgl_2##tipeIntex##unit##pilTamp';
$arr2 = '##kdPabrik__2##tgl__2##kdUnit__2##kdAfdeling__2';
$arr3 = '##kdPabrik__3##kdUnit__3##periode__3';
$arrRe = '##kdPabrikRe##tglRe';
$optPabrik = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$pt= substr($_SESSION['empl']['lokasitugas'],0,3);
$sOrg2 = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi like '".$pt."%' ";

$qOrg2 = mysql_query($sOrg2);
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optPabrik .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$sOrg = 'select distinct kodeorg from '.$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by kodeorg";
$qOrg = mysql_query($sOrg);
$optUnit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$unitintimbangan = '(';
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optUnit .= '<option value='.$rData['kodeorg'].'>'.$rData['kodeorg'].'</option>';
    $unitintimbangan .= "'".$rData['kodeorg']."',";
}
$unitintimbangan = substr($unitintimbangan, 0, -1);
$unitintimbangan .= ')';
$sOrg = 'select kodeorganisasi from '.$dbname.".organisasi where tipe = 'AFDELING' and induk in ".$unitintimbangan.' order by kodeorganisasi';
$qOrg = mysql_query($sOrg);
$optAfdeling2 = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optAfdeling2 .= '<option value='.$rData['kodeorganisasi'].'>'.$rData['kodeorganisasi'].'</option>';
}
$sOrg = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by periode desc";
$qOrg = mysql_query($sOrg);
$optPeriode = "<option value=''></option>";
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optPeriode .= '<option value='.$rData['periode'].'>'.$rData['periode'].'</option>';
}
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\nfunction getKode()\r\n{\r\n\tkdPabrik=document.getElementById('kdPabrik').options[document.getElementById('kdPabrik').selectedIndex].value;\r\n\ttipeIntex=document.getElementById('tipeIntex').options[document.getElementById('tipeIntex').selectedIndex].value;\r\n\tparam='tipeIntexRe='+tipeIntex+'&kdPabrik='+kdPabrik+'&proses=getKodeorg';\r\n\t//alert(param);\r\n\ttujuan=\"pabrik_slave_2penerimaantbsRe.php\";\r\n\t//alert(param);\t\r\n    \r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n                  \tdocument.getElementById('unit').innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n\r\n}\r\nfunction getKodeRe()\r\n{\r\n\ttipeIntex=document.getElementById('tipeIntexRe').options[document.getElementById('tipeIntexRe').selectedIndex].value;\r\n\tparam='tipeIntexRe='+tipeIntex+'&proses=getKodeorg';\r\n\ttujuan=\"pabrik_slave_2penerimaantbsRe.php\";\r\n\t// alert(param);\t\r\n      post_response_text(tujuan, param, respon);\r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n                  \tdocument.getElementById('unitRe').innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n\r\n\r\n}\r\nfunction getAfd(id)\r\n{\r\n\tkdOrg=document.getElementById('kdOrg_'+id).getAttribute('value');\r\n\ttglAfd=document.getElementById('tanggal_'+id).getAttribute('value');\r\n\tparam=\"kodeOrg=\"+kdOrg+\"&proses=getAfdeling\"+\"&brsKe=\"+id+\"&tglAfd=\"+tglAfd;\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n\t//alert(param);\t\r\n    \r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t//\talert(con.responseText);\r\n                  \tdocument.getElementById('detail_'+id).innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n}\r\nfunction getUnit(n)\r\n{\r\n\tkdPabrik=document.getElementById('kdPabrik__'+n).options[document.getElementById('kdPabrik__'+n).selectedIndex].value;\r\n\tparam=\"kodePabrik=\"+kdPabrik+\"&proses=getUnit\";\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n\t//alert(param);\t\r\n    \r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t//\talert(con.responseText);\r\n                  \tdocument.getElementById('kdUnit__'+n).innerHTML=con.responseText;\r\n                        getAfdeling2();\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n}\r\nfunction getAfdeling2()\r\n{\r\n\tkdPabrik=document.getElementById('kdPabrik__2').options[document.getElementById('kdPabrik__2').selectedIndex].value;\r\n\tkdUnit=document.getElementById('kdUnit__2').options[document.getElementById('kdUnit__2').selectedIndex].value;\r\n\tparam=\"kodePabrik=\"+kdPabrik+\"&kodeUnit=\"+kdUnit+\"&proses=getAfdeling2\";\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n//\talert(param);\t\r\n    \r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t//\talert(con.responseText);\r\n                  \tdocument.getElementById('kdAfdeling__2').innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n}\r\nfunction detailBlok(idAwal,id)\r\n{\r\n\tkdBlok=document.getElementById('kdBlok_'+idAwal+'_'+id).innerHTML;\r\n\tnospb=document.getElementById('nospb_'+idAwal+'_'+id).innerHTML;\r\n\ttgl=document.getElementById('tanggal_'+idAwal).innerHTML;\r\n\t\r\n\tparam='kdBlok='+kdBlok+'&proses=getPrestasi'+'&tgl='+tgl+'&brsKe='+idAwal+'&endKe='+id+'&nospb='+nospb;\r\n\ttujuan=\"kebun_slave_3laporanProduksi.php\";\r\n\r\n\t function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t//\talert(con.responseText);\r\n                  \tdocument.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n  post_response_text(tujuan, param, respon);\r\n}\r\nfunction closeBlok(idAwal,id)\r\n{\r\n\tdocument.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML='';\r\n}\r\nfunction closeAfd(id)\r\n{\r\n\tdocument.getElementById('detail_'+id).innerHTML='';\r\n}\r\n\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n      <div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rePenerimaanTbs'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pabrik'];
echo '</label></td><td><select id="kdPabrikRe" name="kdPabrikRe"  style="width:169px">';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tglRe\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" />\r\n</td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2penerimaantbsRe','";
echo $arrRe;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zPdf('pabrik_slave_2penerimaantbsRe','";
echo $arrRe;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n        <button onclick=\"zExcel(event,'pabrik_slave_2penerimaantbsRe.php','";
echo $arrRe;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n      ";
$arrTampilan = ['Default'];
foreach ($arrTampilan as $lstTampilan => $disTamp) {
    $optTampilan .= "<option value='".$lstTampilan."'>".$disTamp.'</option>';
}
echo "<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rPenerimaanTbs'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pabrik'];
echo '</label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:169px">';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" /> s.d. <input type=\"text\" class=\"myinputtext\" id=\"tgl_2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" />\r\n</td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tbs'];
echo '</label></td><td><select id="tipeIntex" name="tipeIntex" onchange="getKode()" style="width:169px">';
echo $optTbsRe;
echo "</select></td></tr>\r\n<tr><td>";
echo $_SESSION['lang']['unit'].'/'.$_SESSION['lang']['supplier'];
echo '</td><td><select id="unit" style="width:169px"><option value="">';
echo $_SESSION['lang']['all'];
echo "</option></select></td></tr>\r\n<tr><td>";
echo $_SESSION['lang']['tampilkan'];
echo '</td><td><select id="pilTamp" style="width:169px">';
echo $optTampilan;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2penerimaantbs','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('pabrik_slave_2penerimaantbs','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'pabrik_slave_2penerimaantbs.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n      \r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rPenerimaanTbs'].'/'.$_SESSION['lang']['afdeling'].'/'.$_SESSION['lang']['tanggal'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pabrik'];
echo '</label></td><td><select id="kdPabrik__2" name="kdPabrik__2" onchange="getUnit(2)" style="width:169px">';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl__2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdUnit__2" name="kdUnit__2" onchange="getAfdeling2()" style="width:169px">';
echo $optUnit;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['afdeling'];
echo '</label></td><td><select id="kdAfdeling__2" name="kdAfdeling__2" style="width:169px">';
echo $optAfdeling2;
echo "</select></td></tr>\r\n<tr><td></td><td></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2penerimaantbs2','";
echo $arr2;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('pabrik_slave_2penerimaantbs2','";
echo $arr2;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'pabrik_slave_2penerimaantbs2.php','";
echo $arr2;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>      \r\n\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rPenerimaanTbs'].'/'.$_SESSION['lang']['afdeling'].'/'.$_SESSION['lang']['bulan'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pabrik'];
echo '</label></td><td><select id="kdPabrik__3" name="kdPabrik__3" onchange="getUnit(3)" style="width:169px">';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdUnit__3" name="kdUnit__3" style="width:169px">';
echo $optUnit;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode__3" name="periode__3" style="width:169px">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td></td><td></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2penerimaantbs3','";
echo $arr3;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('pabrik_slave_2penerimaantbs3','";
echo $arr3;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'pabrik_slave_2penerimaantbs3.php','";
echo $arr3;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>            \r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>