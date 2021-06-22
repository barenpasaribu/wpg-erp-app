<?php 

       require_once 'master_validation.php';

       include 'lib/eagrolib.php';

       //include('lib/nangkoelib.php');

       include('lib/zMysql.php');

       echo open_body();

       include 'master_mainMenu.php';

       

       echo "<script language='javascript' src='js/pmn_kontrakjual.js?v=".mt_rand()."'></script>";

       echo "<script language='javascript' src='js/zMaster.js?v=".mt_rand()."'></script>";

       

       OPEN_BOX('', $_SESSION['lang']['kontrakjual']);

       //$paragraf3=readTextFile_hrd('config/pmn_kontrakjualparagraf.lst');

       $sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where `kelompokbarang`='400'";

       $qBrg=mysql_query($sBrg) or die(mysql_error());

       $optBrg='';

       while($rBrg=mysql_fetch_assoc($qBrg))

       {

              $optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";

       }



       $sTrp="select supplierid,namasupplier from ".$dbname.".log_5supplier where `kodekelompok`='S006'";

       $qTrp=mysql_query($sTrp) or die(mysql_error());

       while($rTrp=mysql_fetch_assoc($qTrp))

       {

              $optTransporter.="<option value=".$rTrp['supplierid'].">".$rTrp['namasupplier']."</option>";

       }



       $sCust="select a.kodecustomer as kodecustomer,a.namacustomer as namacustomer,b.kelompok as kelompok  from ".$dbname.".pmn_4customer a,pmn_4klcustomer b where a.klcustomer=b.kode AND flag_aktif='Y' order by a.namacustomer";

       $qCust=mysql_query($sCust) or die(mysql_error($sCust));

       $optCust='';

       while($rCust=mysql_fetch_assoc($qCust))

       {

              $optCust.="<option value=".$rCust['kodecustomer'].">".$rCust['namacustomer']." (".$rCust['kodecustomer']."-".$rCust['kelompok'].")</option>";

       }

       $sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'"; //echo $sOrg;

       $qOrg=mysql_query($sOrg) or die(mysql_error());

       $optPt='';

       while($rOrg=mysql_fetch_assoc($qOrg))

       {

       if($_SESSION['empl']['kodeorganisasi'] == $rOrg['kodeorganisasi']){

                     $optPt.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";

              }

       }

       $arrKurs=array("IDR","USD");

       $optKurs='';

       foreach($arrKurs as $dt)

       {

              $optKurs.="<option value=".$dt.">".$dt."</option>";

       }



       // FA - 20190105

       $optTipeMuat = getEnum($dbname, 'pmn_kontrakjual', 'tipemuat');

       $tipeMuat = '';

       foreach ($optTipeMuat as $key => $val) {

       $tipeMuat .= "<option value='".$val."'>".ucfirst($val).'</option>';

       }



       //=========================

       $frm=array();

       $frm[0].="

       <div style='height:480px;overflow:scroll;'>

       <input type=hidden id=method name=method value='insert' />     <fieldset>

              <legend>".$_SESSION['lang']['form']."</legend>

              <fieldset>

                     <legend>".$_SESSION['lang']['header']."</legend>

                     <table cellspacing=1 border=0>

                            <tr>

                                   <td>".$_SESSION['lang']['namaorganisasi']."</td>

                                   <td><select id=kdPt name=kdPt onchange=\"getSatuan(0,0,0)\"><option value=''></option>".$optPt."</select></td>			

                                   <td>".$_SESSION['lang']['tglKontrak']."</td>

                                   <td><input type=text id=tlgKntrk size=10 maxlength=10 class=myinputtext onchange=\"getSatuan(0,0,0)\" onkeypress=\"return false;\" onmouseover=setCalendar(this) style=\"width:150px;\" /></td>			

                            </tr>

                            <tr>

                                   <td>".$_SESSION['lang']['NoKontrak']."</td>

                                   <td><input type=text class=myinputtext id=noKtrk name=noKtrk maxlength=100 onkeypress=\"return tanpa_kutip(event)\" style=\"width:150px;\" /></td>

                                   <td>".$_SESSION['lang']['nodo']."</td> 

                                   <td><input type=text id=noDo name=noDo class=myinputtext style=\"width: 170px;\" /></td> 			

                            </tr>

                            <tr>

                                   <td>&nbsp;</td>

                                   <td>&nbsp;</td>

                                   <td>Transporter</td> 

                                   <td><select id=transporter name=transporter ><option value=''></option>".$optTransporter."</select></td> 			

                            </tr>

                     </table>

              </fieldset>

              <br />

              <fieldset>

                     <legend>Informasi Pelanggan</legend>

                     <table>

                            <tr>

                                   <td>Nama Pelanggan</td>

                                   <td>

                                          <select id=custId name=custId style=\"width:150px;\" onchange=\"getDataCust(0)\">

                                          <option value=></option>".$optCust."</select>

                                   </td>

                                   <td>

                                          <span id=nmPerson></span> <span id=fax></span>

                                   </td>

                            </tr>

                     </table>

              </fieldset><br />

              <fieldset>

                            <legend>Informasi Pesanan</legend>



                            <table cellspacing=1 border=0>

                            <thead>

                            <tr>

                            <td colspan=9>".$_SESSION['lang']['goodsDesc']."</td>

                            </tr>

                            <tr class=rowheader>

                                   <td>".$_SESSION['lang']['namabarang']."</td>

                                   <td>".$_SESSION['lang']['satuan']."</td>

                                   <td>".$_SESSION['lang']['hargasatuan']."</td>

                                   <td>".$_SESSION['lang']['matauang']."</td>

                                   <td>PPN</td>

                                   <td>".$_SESSION['lang']['jmlhBrg']."</td>

                                   <td>".$_SESSION['lang']['total']."</td>

                                   <td>".$_SESSION['lang']['terbilang']."</td>

                                   <td>Keterangan</td>

                            </tr>

                            </thead>

                            <tbody>

                                   <td><select id=kdBrg name=kdBrg onchange=\"genNoDO()\" style=\"width:150px;\"><option value=></option>".$optBrg."</select></td>

                                   <td><select id=stn name=stn style=\"width:50px;\"><option value=''></option></select></td>

                                   <td><input type=text class=myinputtextnumber  name=HrgStn id=HrgStn onkeypress=\"return angka_doang(event);\"  onchange=hitungx() style=\"width:100px;\" /></td>

                                   <td><select id=kurs name=kurs style=\"width:50px;\">".$optKurs."</select></td>

                                   <td><input type=text class=myinputtextnumber name=ppn id=ppn style=\"width:100px;\" value=0 onkeypress=\"return angka_doang(event);\" onchange=hitungx() /> %</td>

                                   <td><input type=text class=myinputtextnumber name=jmlh id=jmlh value=0 onkeypress=\"return angka_doang(event);\"  onchange=hitungx() style=\"width:100px;\" /></td>

                                   <td><input type=text disabled=\"disabled\" class=myinputtextnumber name=total id=total onkeypress=\"return angka_doang(event);\"  style=\"width:100px;\" /></td>

                                   <td width:350><span id=tBlg></span> Rupiah</td>

                                   <td><span id=keterangan></span></td>

                            </tbody>

                            </table><br />

                            <table cellspacing=1 border=0>

                            <thead>

                            <tr>

                            <td colspan=2>".$_SESSION['lang']['penyerahan']."</td>

                            </tr>

                            <tr class=rowheader>

                                   <td>".$_SESSION['lang']['tgl_kirim']."</td>

                                   <td>".$_SESSION['lang']['toleransi']."</td>

                            </tr>

                            </thead>

                            <tbody>

                                   <td> <input type=text id=tglKrm size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)> s.d.<input type=text id=tglSd size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)></td>

                                   <td><input type=text class=myinputtextnumber name=tlransi id=tlransi style=\"width:150px;\" onkeypress=\"return angka_doang(event);\" />%</td>



                            </tbody>

                            </table><br />

                            <table cellspacing=1 border=0>

                            <thead>

                            <tr>

                            <td colspan=3 style=\"width:200px;\">".$_SESSION['lang']['timbangan']."</td>

                            </tr></thead>

                            <tbody>

                            <tr>

                            <td>".$_SESSION['lang']['infoTmbngn']."</td><td>:</td><td><textarea style=\"height: 50px; width: 170px;\" name=tmbngn id=tmbngn ></textarea></td></tr>

                                   <tr>

                                          <td>Kwalitet</td>

                                          <td>:</td>

                                          <td>

                                                 <textarea  style=\"height: 50px; width: 170px;\" id=kualitas cols=80 rows=3>FFA : 5,00% MAX, M & I : 0,50% MAX, DOBI : 2,00 MIN</textarea>

                                          </td>

                                   </tr>                                                 

                            </tbody>

                            </table>

                                                 <br/>

                                                 <table cellspacing=1 border=0>

                            <thead>

                            <tr>

                            <td colspan=3 style=\"width:200px;\">".$_SESSION['lang']['syaratPem']."</td>

                            </tr></thead>

                            <tbody>

                                   <tr>

                                          <td>".$_SESSION['lang']['payment']." 1</td><td>:</td><td><textarea style=\"height: 50px; width: 170px;\" name=syrtByr id=syrtByr ></textarea></td></tr>

                                   <tr>

                                          <td>".$_SESSION['lang']['payment']." 2</td>

                                          <td>:</td>

                                          <td>

                                                 <textarea style=\"height: 50px; width: 170px;\" name=syrtByr2 id=syrtByr2 ></textarea>

                                          </td>

                                   </tr>

                                   <tr>

                                          <td>".$_SESSION['lang']['tndaTangan']."</td>

                                          <td>:</td>

                                          <td>

                                                 <input type=text name=tndtng id=tndtng class=myinputtext style=\"width: 170px;\" value=''/>

                                          </td>

                                   </tr>

                                   <tr>

                                          <td>Tanda Tangan Pembeli</td>

                                          <td>:</td>

                                          <td>

                                                 <input type=text name=tanda_tangan_pembeli id=tanda_tangan_pembeli class=myinputtext style=\"width: 170px;\" />

                                          </td>

                                   </tr>

                            </tbody>

                            </table>						

              </fieldset>

              <br />

                                                 <table cellspacing=1 border=0>

                            <thead>

                            <tr>

                            <td colspan=3 style=\"width:200px;\">Lainnya</td>

                            </tr></thead>

                            <tbody>

                                                 </tbody>

                                                 </tr>

                                                 </table>

       

              <table>

                     <tr>

                            <td style='valign:top'>Lama Muat (Hari)</td>

                            <td>

                                   <input type=text id=lamamuat name=lamamuat class=myinputtext onkeypress=\"return tanpa_kutip(event);\" style=\"width:300px;\" />

                            </td>

                     </tr>

                     <tr>

                            <td style='valign:top'>Tipe Muat</td>

                            <td><select id=tipemuat name=tipemuat style=\"width:100px;\">".$tipeMuat."</select></td>

                     

                     </tr>

                     <tr>

                            <td style='valign:top'>Keterangan</td>

                     

                            <td>

                                   <input type=text id=keterangan_muat name=pelabuhan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" style=\"width:300px;\" />

                            </td>

                     </tr>

                     <tr>

                            <td style='valign:top'>Pelabuhan Bongkar</td><td><input type=text id=pelabuhan name=pelabuhan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" style=\"width:300px;\" /></td>

                     </tr>

                     <tr>

                            <td style='valign:top'>Demurage (Per Hari)</td><td><input type=text id=demurage name=demurage class=myinputtext onkeypress=\"return tanpa_kutip(event);\" style=\"width:300px;\" /></td>

                     </tr>

              </table>		  		  

                     <br />

              <fieldset>

                     <table cellspacing=1 border=0>

                            <thead>

                            <tr>

                            <td colspan=3 style=\"width:200px;\">Catatan</td>

                            </tr></thead>

                            <tbody>

                                                 </tbody>

                                                 </tr>

                                                 </table>        

       <table>

              <tr>

                     <td style='valign:top'></td><td>
<textarea style=\"height: 600px; width: 500px;\" name=cttn1 id=cttn1 ></textarea>
                     </td>

              </tr>



       </table>

              </fieldset>

              <center>

              <button class=mybutton onclick=saveKP()>".$_SESSION['lang']['save']."</button>

              <button class=mybutton onclick=copyFromLast()>".$_SESSION['lang']['copy']."</button>

              <button class=mybutton onclick=clearFrom()>".$_SESSION['lang']['new']."</button>



              </center>

              </fieldset>";



       $frm[1]="<div style='height:480px;overflow:scroll;'><fieldset>

              <legend>".$_SESSION['lang']['list']."</legend>

              <fieldset><legend></legend>

              ".$_SESSION['lang']['NoKontrak']."

              <input type=text id=txtnokntrk size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" >

              <button class=mybutton onclick=cariNoKntrk()>".$_SESSION['lang']['find']."</button>

              </fieldset>

              <table class=sortable cellspacing=1 border=0>

       <thead>

              <tr class=rowheader>

              <td>No.</td>

              <td>".$_SESSION['lang']['NoKontrak']."</td>

              <td>".$_SESSION['lang']['nm_perusahaan']."</td>

              <td>".$_SESSION['lang']['nmcust']."</td>

              <td>".$_SESSION['lang']['tglKontrak']."</td>

              <td>".$_SESSION['lang']['kodebarang']."</td>

              <td>".$_SESSION['lang']['produk']."</td>

              <td>".$_SESSION['lang']['tgl_kirim']."</td> 

              <td>Action</td>

              </tr>

              </head>

              <tbody id=containerlist>

              <script>

              loadNewData();

              </script>

              </tbody>

              <tfoot>

              </tfoot>

              </table>

              </fieldset>";



       $hfrm[0]=$_SESSION['lang']['form'];

       $hfrm[1]=$_SESSION['lang']['list'];



       drawTab('FRM',$hfrm,$frm,100,900);

       ?>

       <?

       CLOSE_BOX();

       echo close_body();



?>

