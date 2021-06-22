<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = getQuery("gudang");// 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'GUDANG\'';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$arr = '##kdGudang##periode';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>' . "\r\n" . '<script>' . "\r\n" . 'function getPeriode()' . "\r\n" . '{' . "\r\n\t" . 'kdGudang=document.getElementById(\'kdGudang\').options[document.getElementById(\'kdGudang\').selectedIndex].value;' . "\r\n\t" . 'param=\'kdGudang=\'+kdGudang+\'&proses=getPeriode\';' . "\r\n\t" . 'tujuan="log_slave_2alokasi_pemakaiBrg.php";' . "\r\n\t" . '//alert(param);' . "\t\r\n" . '    ' . "\r\n\t" . ' function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n" . '                  ' . "\t" . 'document.getElementById(\'periode\').innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n" . '    //' . "\r\n" . '  //  alert(fileTarget+\'.php?proses=preview\', param, respon);' . "\r\n" . '  post_response_text(tujuan, param, respon);' . "\r\n\r\n" . '}    ' . "\r\n" . '</script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['lapAlokasiBrg'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['pilihgudang'];
echo '</label></td><td><select id="kdGudang" name="kdGudang" style="width:150px" onchange="getPeriode()">';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px"><option value=\'\'>';
echo $_SESSION['lang']['all'];
echo '</option></select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_2alokasi_pemakaiBrg\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf(\'log_slave_2alokasi_pemakaiBrg\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'log_slave_2alokasi_pemakaiBrg.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:350px;max-width:1220px\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
