<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
//if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
//    $arr = '##kdOrg##periode##afdId';
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') order by namaorganisasi asc ";
//    $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji order by periode desc';
//    $optOrg = "<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange='getSub()'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//} else {
//    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
//        $arr = '##kdOrg##periode##afdId';
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' or tipe in ('KEBUN','PABRIK','KANWIL') order by kodeorganisasi asc";
//        $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji order by periode desc';
//        $optOrg = "<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange='getSub()'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//    } else {
//        $arr = '##kdOrg##periode';
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
//        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
//        $optOrg = "<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\"><option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//    }
//}

$sPeriode = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
//$qOrg = mysql_query($sOrg);
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}

$optOrg=makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<script>\r\nfunction getSub()\r\n{\r\n    afd=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;\r\n    param='kdOrg='+afd+'&proses=getSubUnit';\r\n    tujuan='sdm_slave_2laporanPremiPerHari.php';\r\n    post_response_text(tujuan, param, respog);\r\n    function respog()\r\n    {\r\n                  if(con.readyState==4)\r\n                  {\r\n                            if (con.status == 200) {\r\n                                            busy_off();\r\n                                            if (!isSaveResponse(con.responseText)) {\r\n                                                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                                            }\r\n                                            else {\r\n                                                    //alert(con.responseText);\r\n                                                    document.getElementById('afdId').innerHTML=con.responseText;\r\n\r\n                                            }\r\n                                    }\r\n                                    else {\r\n                                            busy_off();\r\n                                            error_catch(con.status);\r\n                                    }\r\n                  }\t\r\n     }  \t\r\n}\r\n\r\nfunction showpopup(karyawanid,tanggal,ev)\r\n{\r\n   param='karyawanid='+karyawanid+'&tanggal='+tanggal;\r\n   tujuan='sdm_slave_2laporanPremiPerHari_showpopup.php'+\"?\"+param;  \r\n   width='450';\r\n   height='150';\r\n  \r\n   content=\"<iframe frameborder=0 width=100% height=100% src='\"+tujuan+\"'></iframe>\"\r\n   showDialog1('No Transaksi Premi '+karyawanid+' '+tanggal,content,width,height,ev); \r\n\t\r\n}\r\n</script>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanPremi'].' '.$_SESSION['lang']['harian'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td>\r\n\t<td>";
echo "<select id='kdOrg' name='kdOrg' style='width:150px' onchange='getSub()'>$optOrg</select>";
echo "\t</select></td>\r\n</tr>\r\n";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    echo "<tr>\r\n\t<td><label>";
    echo $_SESSION['lang']['subunit'];
    echo "</label></td>\r\n\t<td><select id=\"afdId\" name=\"afdId\" style=\"width:150px\">";
    echo $optAfd;
    echo "\t</select></td>\r\n</tr>\r\n";
}

echo "<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td>\r\n\t<td><select id=\"periode\" name=\"periode\" style=\"width:150px\">\r\n\t\t<!--<option value=\"\"></option>-->";
echo $optPeriode;
echo "\t</select></td>\r\n</tr>\r\n \r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n\t<button onclick=\"zPreview('sdm_slave_2laporanPremiPerHari','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n\t<!--<button onclick=\"zPdf('sdm_slave_2laporanPremiPerHari','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>-->\r\n\t<button onclick=\"zExcel(event,'sdm_slave_2laporanPremiPerHari.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n\r\n\t<button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:330px;max-width:1100px'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>