<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where  tipe=\'TRAKSI\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThn = 'select distinct  tahunbudget from ' . $dbname . '.bgt_budget  order by tahunbudget desc';

#exit(mysql_error($conn));
($qThn = mysql_query($sThn)) || true;

while ($rThn = mysql_fetch_assoc($qThn)) {
	$optThn .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
}

$arr = '##thnBudget##kdUnit';
echo '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script>' . "\r\n" . 'function summForm()' . "\r\n" . '{' . "\r\n\t" . '//closeDialog();' . "\r\n\t" . 'width=\'350\';' . "\r\n\t" . 'height=\'200\';' . "\r\n\t" . 'content="<div id=container style=\'overflow:auto;width:100%;height:190px;\'></div>";' . "\r\n\t" . 'ev=\'event\';' . "\r\n\t" . 'title="Detail Alokasi";' . "\r\n\t" . 'showDialog1(title,content,width,height,ev);' . "\r\n" . '}' . "\r\n\r\n" . 'function getAlokasi(kdTraksi,kdkend,thnbdget)' . "\r\n" . '{' . "\r\n" . '    summForm();' . "\r\n" . '    kodeTraksi=kdTraksi;' . "\r\n" . '    kdVhc=kdkend;' . "\r\n" . '    thnBudget=thnbdget;' . "\r\n" . '    param=\'kdTraksi=\'+kodeTraksi+\'&kdVhc=\'+kdVhc+\'&thnBudget=\'+thnBudget;' . "\r\n" . '    tujuan=\'bgt_slave_laporan_rp_jam_kendaraan.php\';' . "\r\n" . '    function respog()' . "\r\n" . '    {' . "\r\n" . '          if(con.readyState==4)' . "\r\n" . '          {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                    busy_off();' . "\r\n" . '                    if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                            alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                           // alert(con.responseText);' . "\r\n" . '                            document.getElementById(\'container\').innerHTML=con.responseText;' . "\r\n" . '                            //return con.responseText;' . "\r\n" . '                    }' . "\r\n" . '            }' . "\r\n" . '            else {' . "\r\n" . '                    busy_off();' . "\r\n" . '                    error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '          }' . "\t\r\n" . '     } ' . "\t" . '    ' . "\r\n" . '      post_response_text(tujuan+\'?\'+\'proses=getAlokasi\', param, respog);' . "\r\n" . '    ' . "\r\n" . '}' . "\r\n" . 'function summForm2()' . "\r\n" . '{' . "\r\n\t" . '//closeDialog();' . "\r\n\t" . 'width=\'650\';' . "\r\n\t" . 'height=\'350\';' . "\r\n\t" . 'content="<div id=container2 style=\'overflow:auto;width:100%;height:330px;\'></div>";' . "\r\n\t" . 'ev=\'event\';' . "\r\n\t" . 'title="Detail Alokasi";' . "\r\n\t" . 'showDialog2(title,content,width,height,ev);' . "\r\n" . '}' . "\r\n" . 'function getBiaya(kdTraksi,kdkend,thnbdget)' . "\r\n" . '{' . "\r\n" . '    summForm2();' . "\r\n" . '    kodeTraksi=kdTraksi;' . "\r\n" . '    kdVhc=kdkend;' . "\r\n" . '    thnBudget=thnbdget;' . "\r\n" . '    param=\'kdTraksi=\'+kodeTraksi+\'&kdVhc=\'+kdVhc+\'&thnBudget=\'+thnBudget;' . "\r\n" . '    tujuan=\'bgt_slave_laporan_rp_jam_kendaraan.php\';' . "\r\n" . '    function respog()' . "\r\n" . '    {' . "\r\n" . '          if(con.readyState==4)' . "\r\n" . '          {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                    busy_off();' . "\r\n" . '                    if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                            alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                           // alert(con.responseText);' . "\r\n" . '                            document.getElementById(\'container2\').innerHTML=con.responseText;' . "\r\n" . '                            //return con.responseText;' . "\r\n" . '                    }' . "\r\n" . '            }' . "\r\n" . '            else {' . "\r\n" . '                    busy_off();' . "\r\n" . '                    error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '          }' . "\t\r\n" . '     } ' . "\t" . '    ' . "\r\n" . '      post_response_text(tujuan+\'?\'+\'proses=getBiaya\', param, respog);' . "\r\n" . '    ' . "\r\n" . '}' . "\r\n" . 'function printFile(param,tujuan,title,ev)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'200\';' . "\r\n" . '   height=\'150\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,ev); ' . "\t\r\n" . '}' . "\r\n\r\n" . 'function dataKeExcelAlokasi(ev,kdTraksi,kdkend,thnbdget)' . "\r\n" . '{' . "\r\n" . '        kodeTraksi=kdTraksi;' . "\r\n" . '        kdVhc=kdkend;' . "\r\n" . '        thnBudget=thnbdget;' . "\r\n" . '        param=\'kdTraksi=\'+kodeTraksi+\'&kdVhc=\'+kdVhc+\'&thnBudget=\'+thnBudget+\'&getExcelAlokasi\'+\'&proses=excelAlokasi\';' . "\r\n" . '        tujuan=\'bgt_slave_laporan_rp_jam_kendaraan.php\';' . "\r\n\t" . 'judul=\'Report Ms.Excel\';' . "\t\r\n\t" . 'printFile(param,tujuan,judul,ev)' . "\t\r\n" . '}' . "\r\n" . 'function dataKeExcel(ev,kdTraksi,kdkend,thnbdget)' . "\r\n" . '{' . "\r\n" . '        kodeTraksi=kdTraksi;' . "\r\n" . '        kdVhc=kdkend;' . "\r\n" . '        thnBudget=thnbdget;' . "\r\n" . '        param=\'kdTraksi=\'+kodeTraksi+\'&kdVhc=\'+kdVhc+\'&thnBudget=\'+thnBudget+\'&getExcelAlokasi\'+\'&proses=excelBiaya\';' . "\r\n" . '        tujuan=\'bgt_slave_laporan_rp_jam_kendaraan.php\';' . "\r\n\t" . 'judul=\'Report Ms.Excel\';' . "\t\r\n\t" . 'printFile(param,tujuan,judul,ev)' . "\t\r\n" . '}' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n\r\n\r\n\r\n\r\n" . 'function printFileAlokasiPdf(param,tujuan,title,ev)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'1250\';' . "\r\n" . '   height=\'500\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,ev); ' . "\r\n" . '}' . "\r\n\r\n" . 'function dataKePdfAlokasi(ev,kdTraksi,kdkend,thnbdget)' . "\r\n" . '{' . "\r\n" . '        kodeTraksi=kdTraksi;' . "\r\n" . '        kdVhc=kdkend;' . "\r\n" . '        thnBudget=thnbdget;' . "\r\n" . '        param=\'kdTraksi=\'+kodeTraksi+\'&kdVhc=\'+kdVhc+\'&thnBudget=\'+thnBudget+\'&getExcelAlokasi\'+\'&proses=pdfAlokasi\';' . "\r\n" . '        tujuan=\'bgt_slave_laporan_rp_jam_kendaraan.php\';' . "\r\n\t" . 'judul=\'Report Detail PDF\';' . "\t\r\n\t" . 'printFileAlokasiPdf(param,tujuan,judul,ev)' . "\t\r\n\t" . '//alert (param);' . "\r\n\t" . '//alert (param);' . "\r\n\r\n" . '}' . "\r\n\r\n\r\n\r\n\r\n\r\n" . 'function dataKePdfBiaya(ev,kdTraksi,kdkend,thnbdget)' . "\r\n" . '{' . "\r\n" . '        kodeTraksi=kdTraksi;' . "\r\n" . '        kdVhc=kdkend;' . "\r\n" . '        thnBudget=thnbdget;' . "\r\n" . '        param=\'kdTraksi=\'+kodeTraksi+\'&kdVhc=\'+kdVhc+\'&thnBudget=\'+thnBudget+\'&getExcelAlokasi\'+\'&proses=pdfBiaya\';' . "\r\n" . '        tujuan=\'bgt_slave_laporan_rp_jam_kendaraan.php\';' . "\r\n\t" . 'judul=\'Report Detail PDF\';' . "\t\r\n\t" . 'printFileBiayaPdf(param,tujuan,judul,ev)' . "\t\r\n\t" . '//alert (param);' . "\r\n\t" . '//alert (param);' . "\r\n\r\n" . '}' . "\r\n\r\n" . 'function printFileBiayaPdf(param,tujuan,title,ev)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'1250\';' . "\r\n" . '   height=\'500\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,ev); ' . "\r\n" . '}' . "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n" . '</script>' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['rpperjamKend'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td><select id=\'thnBudget\' style="width:150px;">';
echo $optThn;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kodetraksi'];
echo '</label></td><td><select id=\'kdUnit\'  style="width:150px;">';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'bgt_slave_laporan_rp_jam_kendaraan\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '<button onclick="zPdf(\'bgt_slave_laporan_rp_jam_kendaraan\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button>' . "\r\n" . '        <button onclick="zExcel(event,\'bgt_slave_laporan_rp_jam_kendaraan.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button>' . "\r\n" . '        <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>' . "\r\n" . '        ' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
