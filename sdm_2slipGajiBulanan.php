<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where jenisgaji='B' and sudahproses='0' order by periode desc";
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
}
$sKry = 'select karyawanid,namakaryawan from '.$dbname.".datakaryawan where lokasitugas='".$lksiTugas."' and sistemgaji like '%Bulanan%' order by namakaryawan asc";
$qKry = mysql_query($sKry);
while ($rKry = mysql_fetch_assoc($qKry)) {
    $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['namakaryawan'].'</option>';
}
//if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi \r\n               where tipe in ('PABRIK','KANWIL','KEBUN','TRAKSI', 'HOLDING')  and CHAR_LENGTH(kodeorganisasi)=4 order by namaorganisasi asc";
//} else {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
//}
//
//$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$qOrg = mysql_query($sOrg);
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}

$optOrg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$optDept = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optTipe = $optDept;
$sDept = 'select * from '.$dbname.'.sdm_5departemen order by nama asc';
$qDept = mysql_query($sDept);
while ($rDept = mysql_fetch_assoc($qDept)) {
    $optDept .= '<option value='.$rDept['kode'].'>'.$rDept['nama'].'</option>';
}
$sTipeKary = 'select distinct * from '.$dbname.'.sdm_5tipekaryawan order by tipe asc';
$qTipeKary = mysql_query($sTipeKary);
while ($rTipeKary = mysql_fetch_assoc($qTipeKary)) {
    $optTipe .= "<option value='".$rTipeKary['id']."'>".$rTipeKary['tipe'].'</option>';
}
$arr = '##periode##kdBag##tPkary';
$arrKry = '##period##idKry';
$arrAfd = '##perod##idAfd##kdBag2##tPkary2';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\nfunction  getPeriode()\r\n{\r\n    kdOrg=document.getElementById('idAfd').options[document.getElementById('idAfd').selectedIndex].value;\r\n    tujuan='sdm_slave_2slipGajiBulananAfd';\r\n    param='idAfd='+kdOrg;\r\n    post_response_text(tujuan+'.php?proses=getPeriode', param, respog);\r\n    function respog()\r\n\t{\r\n\t\t      if(con.readyState==4)\r\n\t\t      {\r\n\t\t\t        if (con.status == 200) {\r\n\t\t\t\t\t\tbusy_off();\r\n\t\t\t\t\t\tif (!isSaveResponse(con.responseText)) {\r\n\t\t\t\t\t\t\talert('ERROR TRANSACTION,\\n' + con.responseText);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\telse {\r\n\t\t\t\t\t\t\t//alert(con.responseText);\r\n\t\t\t\t\t\t\tdocument.getElementById('perod').innerHTML=con.responseText;\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}\r\n\t\t\t\t\telse {\r\n\t\t\t\t\t\tbusy_off();\r\n\t\t\t\t\t\terror_catch(con.status);\r\n\t\t\t\t\t}\r\n\t\t      }\t\r\n\t }  \t\r\n}\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n    ";
if ('HOLDING' != $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    echo "<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajibulananper'].'/'.$_SESSION['lang']['periode'];
    echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['bagian'];
    echo '</label></td><td><select id="kdBag" name="kdBag" style="width:150px">';
    echo $optDept;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tPkary" name="tPkary" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2slipGajiBulanan','";
    echo $arr;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiBulanan','";
    echo $arr;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2slipGajiBulanan.php','";
    echo $arr;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div >\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajibulananper'].'/'.$_SESSION['lang']['karyawan'];
    echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="period" name="period" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n\r\n<tr><td><label>";
    echo $_SESSION['lang']['namakaryawan'];
    echo '</label></td><td><select id="idKry" name="idKry" style="width:150px">';
    echo $optKry;
    echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\" align=\"center\"><button onclick=\"zPreview('sdm_slave_2slipGajiBulanan','";
    echo $arrKry;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiBulanan','";
    echo $arrKry;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><!--<button onclick=\"zExcel(event,'sdm_slave_2slipGajiBulanan.php','";
    echo $arrKry;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>--></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajibulananper'].'/';
    echo " Afdeling</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['unit'];
    echo "</label></td><td>\r\n<select id=\"idAfd\" name=\"idAfd\" style=\"width:150px\">";
    echo $optOrg;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="perod" name="perod" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['bagian'];
    echo '</label></td><td><select id="kdBag2" name="kdBag2" style="width:150px">';
    echo $optDept;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tPkary2" name="tPkary2" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr><td colspan=\"2\" align=\"center\"><button onclick=\"zPreview('sdm_slave_2slipGajiBulananAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiBulananAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2slipGajiBulananAfd.php','";
    echo $arrAfd;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
} else {
    echo "<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajibulananper'].'/';
    echo "Afdeling</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['unit'];
    echo "</label></td><td>\r\n<select id=\"idAfd\" name=\"idAfd\" style=\"width:150px\">";
    echo $optOrg;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="perod" name="perod" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['bagian'];
    echo '</label></td><td><select id="kdBag2" name="kdBag2" style="width:150px">';
    echo $optDept;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tPkary2" name="tPkary2" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr><td colspan=\"2\" align=\"center\"><button onclick=\"zPreview('sdm_slave_2slipGajiBulananAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiBulananAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2slipGajiBulananAfd.php','";
    echo $arrAfd;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n    ";
}

echo "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>