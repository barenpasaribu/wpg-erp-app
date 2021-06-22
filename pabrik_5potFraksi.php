<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2>\r\n    function simpanJabatan()\r\n    {\r\n        kodeOrg=document.getElementById('unit').value;\r\n        kode=document.getElementById('kode').value;\r\n        potongan=document.getElementById('potongan').value;\r\n method=document.getElementById('method').value;\r\n        if(kode=='' || potongan=='')\r\n            alert('Fields are obligatory');\r\n        else\r\n           {\r\n                param='kode='+kode+'&potongan='+potongan+'&kodeorg='+kodeOrg+'&method='+method;\r\n                tujuan = 'pabrik_slave_save_pot_sortasi.php';\r\n\t\tpost_response_text(tujuan, param, respog);               \r\n           } \r\n\t\t\t\r\n\tfunction respog(){\r\n\t\tif (con.readyState == 4) {\r\n\t\t\tif (con.status == 200) {\r\n\t\t\t\tbusy_off();\r\n\t\t\t\tif (!isSaveResponse(con.responseText)) {\r\n\t\t\t\t\talert('ERROR TRANSACTION,\\n' + con.responseText);\r\n\t\t\t\t}\r\n\t\t\t\telse {\r\n\t\t\t\t\tdocument.getElementById('container').innerHTML=con.responseText;\r\n                                        cancelJabatan();\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t\telse {\r\n\t\t\t\tbusy_off();\r\n\t\t\t\terror_catch(con.status);\r\n\t\t\t}\r\n\t\t}\r\n\t}   \r\n    }\r\n\r\nfunction cancelJabatan()\r\n{\r\n         document.getElementById('kode').value='';\r\n         document.getElementById('potongan').value='';\r\n document.getElementById('method').value='insert';\r\n         document.getElementById('kode').disabled=false;\r\n}\r\n\r\nfunction fillField(x,y,z)\r\n{\r\n         document.getElementById('kode').value=x;\r\n         document.getElementById('potongan').value=y;   \r\n document.getElementById('method').value=z;   \r\n         document.getElementById('kode').disabled=true;\r\n}\r\n</script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['potongan'].' '.$_SESSION['lang']['kodefraksi']);
if ($_SESSION['empl']['tipelokasitugas']=='PABRIK') {
    $sFraksi = 'select distinct kode,keterangan,keterangan1 from '.$dbname.'.pabrik_5fraksi order by keterangan asc';
    $qFraksi = mysql_query($sFraksi);
    while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
        if ($_SESSION['language'] == 'EN') {
            $optFraks .= "<option value='".$rFraksi['kode']."'>".$rFraksi['keterangan1'].'</option>';
            $kodeNama[$rFraksi['kode']] = $rFraksi['keterangan1'];
        } else {
            $optFraks .= "<option value='".$rFraksi['kode']."'>".$rFraksi['keterangan'].'</option>';
            $kodeNama[$rFraksi['kode']] = $rFraksi['keterangan'];
        }
    }
    echo "<fieldset style='width:500px;'>
	<legend>".$_SESSION['lang']['form']."</legend>
    <input type=hidden value=insert id=method>
	<table>\r\n<tr><td>".$_SESSION['lang']['unit']."</td><td><input type=text id=unit size=4 value='".$_SESSION['empl']['lokasitugas']."' disabled class=myinputtext></td></tr>     \r\n<tr><td>".$_SESSION['lang']['kodeabs'].'</td><td><select id=kode>'.$optFraks."</select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['potongan']."</td><td><input type=text id=potongan size=4 onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>\r\n \t </table>\r\n\t <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
}


//echo open_theme();
$str1 = 'select * from '.$dbname.".pabrik_5pot_fraksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodefraksi";
$res1 = mysql_query($str1);
echo "<fieldset style='width:500px;'>
<legend>".$_SESSION['lang']['list']."</legend>
<table id='masterTable' class='sortable' cellspacing='1' border='0'>
<thead><tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeabs'].'</td><td>'.$_SESSION['lang']['potongan']."</td><td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td>'.$kodeNama[$bar1->kodefraksi].'</td><td align=right>'.$bar1->potongan."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodefraksi."','".$bar1->potongan."','update');\"></td></tr>";
}
echo " </tbody><tfoot></tfoot></table></fieldset>";
//echo close_theme();
CLOSE_BOX();
echo close_body();

?>