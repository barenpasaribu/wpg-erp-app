<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script>function submitFile(){\r\n    if(confirm('Are you sure..?')){\r\n    document.getElementById('frm').submit();\r\n    }\r\n}\r\n</script>\r\n";
$arr = '##listTransaksi##pilUn_1##unitId##method';
include 'master_mainMenu.php';
OPEN_BOX();
echo "  <fieldset><legend>Form</legend>\r\n                     <div id=uForm>\r\n                     \t<span id=sample><b>".$_SESSION['lang']['absensi']." Uploader. This form must be preceded by a header on the first line</b> <a href=tool_slave_getExample.php?form=ABSENSI target=frame>Click here for example</a></span><br><br>\r\n                                         (File type support only CSV).\r\n                                        <form id=frm name=frm enctype=multipart/form-data method=post action=tool_slave_uploadData.php target=frame>\t\r\n                                        <input type=hidden name=jenisdata id=jenisdata value='ABSENSI'>\r\n                                        <input type=hidden name=MAX_FILE_SIZE value=1024000>\r\n                                        File:<input name=filex type=file id=filex size=25 class=mybutton>\r\n                                        Field separated by<select name=pemisah>\r\n                                        <option value=','>, (comma)</option>\r\n                                        <option value=';'>; (semicolon)</option>\r\n                                        <option value=':'>: (two dots)</option>\r\n                                        <option value='/'>/ (devider)</option>\r\n                                        </select>\r\n                                        <input type=button class=mybutton  value=".$_SESSION['lang']['save']." title='Submit this File' onclick=submitFile()>\r\n                                    </form>\r\n \r\n                                    <iframe frameborder=0 width=800px height=200px name=frame>\r\n                                    </iframe>\r\n                     </div>\r\n                    </fieldset>";
CLOSE_BOX();
echo close_body();

?>