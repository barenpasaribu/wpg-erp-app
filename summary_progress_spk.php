<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript src=js/zMaster.js></script> ' . "\r\n" . '<script language=javascript src=js/zSearch.js></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formTable.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script>' . "\r\n" . 'function getUnit()' . "\r\n" . '{' . "\r\n" . '    reg=document.getElementById(\'regional\').options[document.getElementById(\'regional\').selectedIndex].value;' . "\r\n" . '    param=\'proses=getUnit\'+\'&regional=\'+reg;' . "\r\n\t" . 'tujuan=\'summary_slave_progress_spk\';' . "\r\n" . '    post_response_text(tujuan+\'.php\', param, respon);' . "\r\n\t" . 'function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n" . '                    //var res = document.getElementById(idCont);' . "\r\n" . '//                    res.innerHTML = con.responseText;' . "\r\n\t\t\t\t\t" . '  document.getElementById(\'unit\').innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n\r\n" . '}' . "\r\n" . 'function getUnit2()' . "\r\n" . '{' . "\r\n" . '    reg=document.getElementById(\'tahun\').options[document.getElementById(\'tahun\').selectedIndex].value;' . "\r\n" . '    param=\'proses=getUnit\'+\'&tahun=\'+reg;' . "\r\n\t" . 'tujuan=\'summary_slave_progress_spk2\';' . "\r\n" . '    post_response_text(tujuan+\'.php\', param, respon);' . "\r\n\t" . 'function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n" . '                    //var res = document.getElementById(idCont);' . "\r\n" . '//                    res.innerHTML = con.responseText;' . "\r\n\t\t\t\t\t" . '  document.getElementById(\'kontaktor\').innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n\r\n" . '}' . "\r\n" . '    </script>' . "\r\n";
include 'master_mainMenu.php';
$arr = '##periode##regional##unit';
OPEN_BOX();
$str = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_spkht' . "\r\n" . '      order by tanggal desc';
$res = mysql_query($str);
$optperiode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optthn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optregional = $optperiode;
//$optUnit = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optUnit= makeOption2(getQuery("lokasitugas"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
while ($bar = mysql_fetch_object($res)) {
	$optperiode .= '<option value=\'' . $bar->periode . '\'>' . $bar->periode . '</option>';
}
$optKontraktor= makeOption2(getQuery("kontraktor"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
	array("valuefield"=>'supplierid',"captionfield"=> 'namasupplier' )
);
$str = 'select distinct substr(tanggal,1,4) as tahun from ' . $dbname . '.log_spkht' . "\r\n" . '      order by tanggal desc';
$res = mysql_query($str);

while ($rdt = mysql_fetch_object($res)) {
	$optthn .= '<option value=\'' . $rdt->tahun . '\'>' . $rdt->tahun . '</option>';
}

$str = 'select distinct * from ' . $dbname . '.bgt_regional' . "\r\n" . '      order by regional desc';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optregional .= '<option value=\'' . $bar->regional . '\'>' . $bar->nama . '</option>';
}

$arr2 = '##tahun##kontaktor';
echo '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['summaryprogress'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td>' . "\r\n" . '    <td><select id=periode style=\'width:200px;\'>';
echo $optperiode;
echo '</select></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr><td><label>';
/*
echo $_SESSION['lang']['regional'];
echo '</label></td>' . "\r\n" . '    <td><select id=regional style=\'width:200px;\' onchange="getUnit()">';
echo $optregional;
echo '</select></td>' . "\r\n" . '</tr>' . "\r\n" . 
*/
echo '<tr><td><label>';
echo $_SESSION['lang']['unit'];
echo '</label></td>' . "\r\n" . '    <td><select id=unit style=\'width:200px;\'>';
echo $optUnit;
echo '</select></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '    <td colspan="3">' . "\r\n" . '      ';
echo ' <button onclick="zPreview(\'summary_slave_progress_spk\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">' . $_SESSION['lang']['preview'] . '</button>' . "\r\n" . '    <button onclick="zExcel(event,\'summary_slave_progress_spk.php\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>    ' . "\r\n" . '    <button onclick="zPdf(\'summary_slave_progress_spk\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="pdf" id="pdf">' . $_SESSION['lang']['pdf'] . '</button>';
echo '</td>' . "\r\n" . '</tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '    ' . "\r\n" . '    <fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['summaryprogress'];
echo ' per ';
echo $_SESSION['lang']['tahun'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['tahun'];
echo '</label></td>' . "\r\n" . '    <td><select id=tahun style=\'width:200px;\' onchange="getUnit2()">';
echo $optthn;
echo '</select></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kontraktor'];
echo '</label></td>' . "\r\n" . '    <td><select id=kontaktor style=\'width:200px;\' >';
echo $optKontraktor;
echo '</select></td>' . "\r\n" . '</tr>' . "\r\n\r\n" . '<tr>' . "\r\n" . '    <td colspan="3">' . "\r\n" . '      ';
echo ' <button onclick="zPreview(\'summary_slave_progress_spk2\',\'' . $arr2 . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">' . $_SESSION['lang']['preview'] . '</button>' . "\r\n" . '    <button onclick="zExcel(event,\'summary_slave_progress_spk2.php\',\'' . $arr2 . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>    ' . "\r\n" . '    <!--<button onclick="zPdf(\'summary_slave_progress_spk2\',\'' . $arr2 . '\',\'reportcontainer\')" class="mybutton" name="pdf" id="pdf">' . $_SESSION['lang']['pdf'] . '</button>-->';
echo '</td>' . "\r\n" . '</tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<fieldset><legend>' . $_SESSION['lang']['list'] . '</legend>' . "\r\n" . '                 <div id=\'reportcontainer\' style=\'width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;\'></div> ' . "\r\n" . '                 </fieldset>';
CLOSE_BOX();
close_body();
exit();

?>
