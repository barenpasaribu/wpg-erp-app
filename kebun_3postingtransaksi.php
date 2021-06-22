<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/zSearch.js></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script>\r\n   \r\n    /* Function zPreview\r\n * Fungsi untuk preview sebuah report\r\n * I : target file, parameter yang akan dilempar, id container\r\n * O : report dalam bentuk HTML\r\n */\r\nfunction zPreview(fileTarget,passParam,idCont) {\r\n    var passP = passParam.split('##');\r\n    var param = \"\";\r\n    for(i=1;i<passP.length;i++) {\r\n        var tmp = document.getElementById(passP[i]);\r\n        if(i==1) {\r\n            param += passP[i]+\"=\"+getValue(passP[i]);\r\n        } else {\r\n            param += \"&\"+passP[i]+\"=\"+getValue(passP[i]);\r\n        }\r\n    }\r\n  // alert(param);\r\n    function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n                    var res = document.getElementById(idCont);\r\n                    res.innerHTML = con.responseText;\r\n                    document.getElementById('detailData').style.display='none';\r\n                    document.getElementById('awal').style.display='block';\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    //\r\n  //  alert(fileTarget+'.php?proses=preview', param, respon);\r\n    post_response_text(fileTarget+'.php?proses=preview', param, respon);\r\n\r\n}\r\nfunction zExcel(ev,tujuan,passParam)\r\n{\r\n\tjudul='Report Excel';\r\n\t//alert(param);\r\n\tvar passP = passParam.split('##');\r\n    var param = \"\";\r\n    for(i=1;i<passP.length;i++) {\r\n        var tmp = document.getElementById(passP[i]);\r\n        if(i==1) {\r\n            param += passP[i]+\"=\"+getValue(passP[i]);\r\n        } else {\r\n            param += \"&\"+passP[i]+\"=\"+getValue(passP[i]);\r\n        }\r\n    }\r\n\tparam+='&proses=excel';\r\n\t//alert(param);\r\n\tprintFile(param,tujuan,judul,ev)\r\n}\r\nfunction printFile(param,tujuan,title,ev)\r\n{\r\n   tujuan=tujuan+\"?\"+param; \r\n   width='700';\r\n   height='250';\r\n   content=\"<iframe frameborder=0 width=100% height=100% src='\"+tujuan+\"'></iframe>\"\r\n   showDialog1(title,content,width,height,ev);\r\n}\r\n\r\n/* Posting Data\r\n */\r\nfunction postingData(numRow) {\r\n//    alert(\"masuk sini\");\r\n//    return;\r\n    var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value');\r\n    var param = \"notransaksi=\"+notrans;\r\n\r\n    function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    //=== Success Response\r\n                   // alert('Posting Berhasil');\r\n                   // javascript:location.reload(true);\r\n                   document.getElementById('rowDt_'+numRow).style.display='none';\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n\r\n    if(confirm('Akan dilakukan posting untuk transaksi '+notrans+\r\n        '\\nData tidak dapat diubah setelah ini. Anda yakin?')) {\r\n        post_response_text('kebun_slave_operasional_posting.php', param, respon);\r\n    }\r\n}\r\nfunction detailData(numRow,ev,tipe)\r\n{\r\n    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');\r\n    param = \"proses=html&tipe=\"+tipe+\"&notransaksi=\"+notransaksi;\r\n        title=\"Data Detail\";\r\n        showDialog1(title,\"<iframe frameborder=0 style='width:795px;height:400px'\"+\r\n        \" src='kebun_slave_operasional_print_detail.php?\"+param+\"'></iframe>\",'800','400',ev);\r\n        var dialog = document.getElementById('dynamic1');\r\n        dialog.style.top = '50px';\r\n        dialog.style.left = '15%';\r\n}\r\nfunction getPeriode()\r\n{\r\n    var kdOrg = document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;\r\n    var param = \"kdOrg=\"+kdOrg+'&proses=getPeriode';\r\n    post_response_text('kebun_slave_3postingtransaksi.php', param, respon);\r\n    function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    //=== Success Response\r\n                   // alert('Posting Berhasil');\r\n                   // javascript:location.reload(true);\r\n                   document.getElementById('thnId').innerHTML=con.responseText;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n\r\n\r\n        \r\n    \r\n}\r\n\r\nfunction postingDat(maxRow)\r\n{\r\n//\r\n\tif(confirm('Anda Yakin Ingin Memposting Data ..?'))\r\n\t{\r\n\t\t   loopClosingFisik(1,maxRow);\r\n\t\t   // lockForm();\r\n\t}\r\n\telse\r\n\t{\r\n\t\tdocument.getElementById('revTmbl').disabled=false;\r\n\t\treturn;\r\n\t}\r\n}\r\n\r\nfunction loopClosingFisik(currRow,maxRow)\r\n{\r\n        notrans = document.getElementById('notransaksi_'+currRow).innerHTML;\r\n        param = \"notransaksi=\"+notrans;\r\n        xtipe=notrans.substr(14,3);\r\n        if(xtipe=='PNN'){\r\n            post_response_text('kebun_slave_panen_posting.php', param, respon);\r\n        }else{\r\n            post_response_text('kebun_slave_operasional_posting.php', param, respon);\r\n        }\t\r\n\tdocument.getElementById('rowDt_'+currRow).style.backgroundColor='orange';\r\n       \r\n\t//lockScreen('wait');\r\n\tfunction respon(){\r\n\t\tif (con.readyState == 4) {\r\n\r\n\t\t\tif (con.status == 200) {\r\n\t\t\t\tbusy_off();\r\n\t\t\t\tif (!isSaveResponse(con.responseText)) {\r\n\t\t\t\t\talert('ERROR TRANSACTION,\\n' + con.responseText);\r\n\t\t\t\t\tdocument.getElementById('rowDt_'+currRow).style.backgroundColor='red';\r\n\t\t\t\t   unlockScreen();\r\n\t\t\t\t}\r\n\t\t\t\telse {\r\n\t\t\t\t\t//alert(con.responseText);\r\n\t\t\t\t\t//return;\r\n                                        document.getElementById('rowDt_'+currRow).style.backgroundColor='green';\r\n                                        currRow+=1;\r\n\t\t\t\t\tif(currRow>maxRow)\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\tdocument.getElementById('revTmbl').disabled=false;\r\n\t\t\t\t\t\ttutupProses('simpan');\r\n\t\t\t\t\t}\r\n\t\t\t\t\telse\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\tparam='';\r\n                                                loopClosingFisik(currRow,maxRow);\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t\telse {\r\n\t\t\t\tbusy_off();\r\n\t\t\t\terror_catch(con.status);\r\n\t\t\t\tunlockScreen();\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\r\n}\r\nfunction tutupProses(x)\r\n{\r\n\tperiod=document.getElementById('revTmbl');\r\n\tif(period.disabled!=true)\r\n\t{\r\n\t\tif (x == 'simpan') {\r\n\t\t\t//unlockScreen();\r\n\t\t\talert(\"Data Telah Terposting\");\r\n\t\t\t//unlockForm();\r\n\t\t\tdocument.getElementById('printContainer').innerHTML='';\r\n\t\t}\r\n\t\telse\r\n\t\t{\r\n\t\t\tunlockScreen();\r\n\t\t}\r\n\t}\r\n}\r\n\r\n</script>\r\n";
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optOrg = $optPeriode;
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $sOrg = 'select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\r\n      tipe='KEBUN'  order by namaorganisasi asc";
} else {
    $sOrg = 'select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\r\n      tipe='KEBUN' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
}

$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= "<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi'].'</option>';
}
$optTrak = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$trk = ['TB', 'BBT', 'TBM', 'TM', 'PNN'];
foreach ($trk as $dtrk => $lstr) {
    $optTrak .= "<option value='".$lstr."'>".$lstr.'</option>';
}
$optTipe = "<option value=''>All</option>";
$optTipe .= "<option value='BBT'>BBT</option>";
$optTipe .= "<option value='TB'>TB</option>";
$optTipe .= "<option value='TBM'>TBM</option>";
$optTipe .= "<option value='TM'>TM</option>";
$optTipe .= "<option value='PNN'>PNN</option>";
$arr = '##kdOrg##thnId##tipe##tanggal1##tanggal2';
echo "\r\n<!--<script language=javascript1.2 src='js/kebun_operasional.js'></script>-->\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>Posting Pekerjaan</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['kodeorg'];
echo '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px" onchange="getPeriode()">';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="thnId" name="thnId" style="width:150px" >';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tipe'];
echo '</label></td><td><select id="tipe" name="tipe" style="width:150px" >';
echo $optTipe;
echo "</select></td></tr>\r\n    <tr>\r\n        <td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1\" name=\"tanggal1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2\" name=\"tanggal2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n    </tr>\r\n\r\n<!--<tr><td><label>";
echo $_SESSION['lang']['tipe'];
echo '</label></td><td><select id="tipeTrk" name="tipeTrk" style="width:150px" >';
echo $optTrak;
echo "</select></td></tr>-->\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3postingtransaksi','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id=awal>\r\n    <div id='printContainer' style='overflow:auto;height:50%;max-width:100%;;'>\r\n\r\n    </div>\r\n</div>\r\n<div id=detailData style=display:none>\r\n<div id=isiData>\r\n</div>\r\n</div>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>