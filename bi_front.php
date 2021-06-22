<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
OPEN_BODY_BI();
echo " \n<script type=\"text/javascript\" language=\"JavaScript1.2\" src=\"js/biGraph.js\"></script> \n<script type=\"text/javascript\" language=\"JavaScript1.2\" src=\"js/biGraph_map.js\"></script>\n<script type=\"text/javascript\" language=\"JavaScript1.2\" src=\"js/generic.js\"></script> \n<script type=\"text/javascript\" language=\"JavaScript1.2\" src=\"js/zTools.js\"></script> \n";
$optList = "<select id=GRAPHOPTLIST style='width:200px;' onchange=loadGraphForm()>\n                  <option value=''>Pilih Jenis Laporan....</option>\n                  ";
$x = readCountry('config/bi_graph.lst');
foreach ($x as $bar => $val) {
    $optList .= "<option value='".$val[1]."' title='".$val[2]."'>".$val[0].'</option>';
}
$optList .= '</select>';
$optList_map = "<select id=MAPOPTLIST style='width:200px;' onchange=loadGraphForm_map()>\n                  <option value=''>Pilih Jenis Laporan....</option>\n                  ";
$x = readCountry('config/bi_graph_map.lst');
foreach ($x as $bar => $val) {
    $optList_map .= "<option value='".$val[1]."' title='".$val[2]."'>".$val[0].'</option>';
}
$optList_map .= '</select>';
$str = 'SELECT periode FROM '.$dbname.".setup_periodeakuntansi\n        group by periode\n        order by periode desc";
$query = mysql_query($str) || exit(mysql_error($conns));
$optperiode = "<option value='' title=''></option>";
while ($res = mysql_fetch_assoc($query)) {
    $optperiode .= "<option value='".$res['periode']."' title='".$res['periode']."'>".$res['periode'].'</option>';
}
$frm[0] = "\n               <table border=0 cellspacing=2px style='width:100%;height:100%'>\n                <tr>\n                  <td> \n                    <fieldset style='height:40px;width:200px;border:orange solid 1px;'>\n                       <legend>List</legend>\n                     <div  id=GRAPHLIST>".$optList."</div>                          \n                    </fieldset>\n                    <fieldset style='height:110px;width:200px;border:orange solid 1px;'>\n                       <legend>Form</legend>\n                     <div  id=GRAPHFORM>\n                     </div>                          \n                    </fieldset>\n                    <fieldset  style='height:390px;width:200px;border:orange solid 1px;'>\n                     <legend>Info</legend>\n                     <div  id=GRAPHINFO>\n                     </div>                     \n                    </fieldset>\n                  </td>\n                  <td >\n                     <fieldset style='height:570px;width:1000px;border:orange solid 1px;overflow:scroll;background-color:#FFFFFF;'>\n                    <legend>Result</legend>\n                     <div  id=GRAPHRESULT>\n                     </div>\n                    </fieldset>                 \n                  </td>\n                  </tr>\n               </table>\n              ";
$frm[1] = "\n               <table border=0 cellspacing=2px style='width:100%;height:100%'>\n                <tr>\n                  <td>\n                    <fieldset style='height:40px;width:200px;border:orange solid 1px;'>\n                       <legend>List</legend>\n                     <div  id=MAPLIST>".$optList_map."\n                     </div>                          \n                    </fieldset>\n                    <fieldset style='height:70px;width:200px;border:orange solid 1px;'>\n                       <legend>Form</legend>\n                     <div  id=MAPFORM>\n                     </div>                          \n                    </fieldset>\n                    <fieldset style='height:60px;width:200px;border:orange solid 1px;'>\n                        <legend>Control</legend>\n                     <div  id=MAPCONTROL style='text-align:center;font-size:10pt;'>\n                     <table>\n                     <tr>\n                        <td onclick=zoommap(0.6666666666666667) style='background-color:#DEDEDE;cursor:pointer;width:40px' title='Zoom In'>[ + ]</td>\n                        <td style='width:20px'></td>\n                        <td style='background-color:#DEDEDE;width:30px'></td>\n                        <td onclick=movemap('y',-10) style='background-color:#DEDEDE;cursor:pointer;width:30px' title='Move North'>[ ^ ]</td>\n                        <td style='background-color:#DEDEDE;width:30px'></td>\n                     </tr>\n                     <tr>\n                        <td onclick=zoommap(1.5) style='background-color:#DEDEDE;cursor:pointer;width:30px' title='Zoom Out'>[ - ]</td>\n                        <td style='width:30px'></td>\n                        <td onclick=movemap('x',-10) style='background-color:#DEDEDE;cursor:pointer;width:30px' title='Move West'>[ < ]</td>\n                        <td onclick=movemap('y',10) style='background-color:#DEDEDE;cursor:pointer;width:30px' title='Move South'>[ v ]</td>\n                        <td onclick=movemap('x',10) style='background-color:#DEDEDE;cursor:pointer;width:30px' title='Move East'>[ > ]</td>\n                     </tr>\n                     </table>\n                      <!--<a onclick=zoommap(0.6666666666666667) style='background-color:#DEDEDE;cursor:pointer;' title='Zoom In'>[ + ]</a> \n                      <a onclick=zoommap(1.5) style='background-color:#DEDEDE;cursor:pointer;' title='Zoom Out'>[ - ]</a>-->\n                     </div>                           \n                    </fieldset>\n                    <fieldset style='height:350px; width:200px; border:orange solid 1px;'>\n                     <legend>Info</legend>\n                     <input type=hidden id=posx name=posx value=0>\n                     <input type=hidden id=posy name=posy value=0>\n                     <input type=hidden id=posorigx name=posorigx value=0>\n                     <input type=hidden id=posorigy name=posorigy value=0>\n                     <input type=hidden id=drag name=drag value=0>\n                     <input type=hidden id=zoom name=zoom value=1>\n                     <input type=hidden id=origwidth name=origwidth value=0>\n                     <input type=hidden id=origheight name=origheight value=0>\n                     <div  id=MAPHCONTROL style=\"display:none\">\n                        <input onclick=pilihkontrol('divisi') type=radio id=kontroldivisi name=kontrol value=divisi>Divisi<br>\n                        <input onclick=pilihkontrol('tahuntanam') type=radio id=kontroltahuntanam name=kontrol value=tahuntanam>Tahun Tanam<br><hr>\n                        <input onclick=pilihcek('textblock') type=checkbox id=textblock name=textblock value=textblock checked=true>Block Text<br>\n                        <input onclick=pilihcek('textcity') type=checkbox id=textcity name=textcity value=textcity checked=true>City Text<br>\n                        <input onclick=pilihcek('pathroad') type=checkbox id=pathroad name=pathroad value=pathroad checked=true>Road Path<br>\n                        <input onclick=pilihcek('pathriver') type=checkbox id=pathriver name=pathriver value=pathriver checked=true>River Path<br>\n                     <br>    \n                     <div  id=MAPHCONTROLINFO>\n                     </div>\n                     </div>                     \n                    </fieldset>\n                  </td>\n                  <td>\n                     <fieldset style='height:570px;width:800px;border:orange solid 1px;overflow:scroll ;background-color:#FFFFFF;'>\n                    <legend>Result</legend>\n                     <div  id=MAPHRESULT>\n                     </div>\n                    </fieldset>                 \n<!--                     <fieldset style='position: absolute; top:450px; left:250px; border:orange solid 1px; background-color:#FFFFFF;'>\n                    <legend>&nbsp;</legend>\n                     <div  id=MAPHCONTROLINFO style='overflow:auto; height:140px; width:480px;'>\n                     </div>\n                    </fieldset>                 \n-->                  </td>\n                  <td> \n                     <fieldset style='height:400px;width:200px;border:orange solid 1px;'>\n                    <legend>Options</legend>\n                     <div  id=MAPHINFO>\n                        <table>\n                            <tr>\n                                <td>Option</td>\n                                <td>:</td>\n                                <td>\n                                <select style='width:100px;' id=option onchange=cekoption()>\n                                    <option value=''></option>\n                                    <option value='qc'>QC</option>\n                                    <option value='produksi'>Produksi</option>\n                                    <option value='biayapanen'>Biaya Panen vs Budget</option>\n                                    <option value='biayatm'>Biaya TM vs Budget</option>\n                                    <option value='biayatbm'>Biaya TBM vs Budget</option>\n                                    <option value='xblok'>CrossBlock</option>\n                                    <option value='rencanapanen'>Rencana Panen</option>\n                                    <option value='panen'>Panen</option>\n                                    <option value='perawatan'>Perawatan</option>\n                                </select>\n                                </td>\n                            </tr>\n                            <tr>\n                                <td>Sub Option</td>\n                                <td>:</td>\n                                <td>\n                                <select style='width:100px;' id=suboption>\n                                    <option value=''></option>\n                                </select>\n                                </td>\n                            </tr>\n                            <tr>\n                                <td>".$_SESSION['lang']['periode']."</td>\n                                <td>:</td>\n                                <td>\n                                <select style='width:100px;' id=periode onchange=isitanggal()>\n                                    ".$optperiode."\n                                </select>\n                                </td>\n                            </tr>\n                            <tr>\n                                <td>".$_SESSION['lang']['tanggal']."</td>\n                                <td>:</td>  \n                                <td>\n                                    <input type='text' class='myinputtext' id='tanggal0' onmousemove='setCalendar(this.id)' onkeypress='return false;' onmousedown='resetperiode()'\n                                    size='10' maxlength='10' style=\"width:100px;\"/>\n                                </td>\n                            </tr>\n                            <tr>\n                                <td>s/d</td>\n                                <td>:</td>\n                                <td>\n                                    <input type='text' class='myinputtext' id='tanggal1' onmousemove='setCalendar(this.id)' onkeypress='return false;' onmousedown='resetperiode()'  \n                                    size='10' maxlength='10' style=\"width:100px;\"/>\n                                </td>\n                            </tr>\n<tr>\n    <td colspan=3>\n        <button class=mybutton id=preview name=preview onclick=pilihoption()>".$_SESSION['lang']['preview']."</button>\n    </td></tr>                            \n                            </table>\n                     </div>\n                    </fieldset>                 \n                     <fieldset style='height:150px;width:200px;border:orange solid 1px;'>\n                    <legend>Click Info</legend>\n                     <div  id=CLICKINFO>\n                    </fieldset>                 \n                  </td>\n                  </tr>\n               </table>\n              ";
$hfrm[0] = 'GRAPH';
drawTab('FRM', $hfrm, $frm, 200, 1200);
CLOSE_BODY();

?>