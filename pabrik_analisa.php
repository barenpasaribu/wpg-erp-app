<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    echo open_body();
    include 'master_mainMenu.php';
    include_once 'lib/zLib.php';
    echo "\r\n<script language=javascript1.2 src='js/pabrik_kelengkapanloses.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/iReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n";
    
    $optSubunit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $i = 'select subunit,id from '.$dbname.'.pabrik_subunit_analisa';
    $n = mysql_query($i);
    while ($d = mysql_fetch_assoc($n)) {
        $optSubunit .= "<option value='".$d['id']."'>".$d['subunit'].'</option>';
    }

    $optListTanggal = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $queryGetTanggalList = 'select distinct date(tanggal) as tanggal from '.$dbname.'.pabrik_analisa where posting = 0 and kodeorg = "'.$_SESSION['empl']['lokasitugas'].'"';
    $dataTanggalList = mysql_query($queryGetTanggalList);
    while ($d = mysql_fetch_assoc($dataTanggalList)) {
        $optListTanggal .= "<option value='".$d['tanggal']."'>".tanggalnormal($d['tanggal']).'</option>';
    }

    $tanggal = date('d-m-Y');
    $frm[0] = '';
    $frm[1] = '';
    $optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi where kodeorganisasi like '%M' and length(kodeorganisasi)=4 ORDER BY kodeorganisasi";

    $qry = mysql_query($sql);
    while ($data = mysql_fetch_assoc($qry)) {
        if ($_SESSION['empl']['lokasitugas'] == $data['kodeorganisasi']) {
            $optOrg .= '<option selected value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
        }else {
            $optOrg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
        }
    }
    $optAnalisa = '<option value=0>Pilih Data</option>';
    $optAnalisa .= '<option value=1>Analisa Air</option>';
    $optAnalisa .= '<option value=2>Analisa Limbah</option>';
    $arrLaporan = '##tipeanalisaR##tglLap';
    echo "\r\n\r\n\r\n\r\n";
    OPEN_BOX();
    $frm[0] .= "<fieldset>";
    $frm[0] .= '<legend><b>Analisa Air Dan Limbah</b></legend>';
    $frm[0] .= "<fieldset>";
    $frm[0] .= '<legend>'.$_SESSION['lang']['form'].'</legend>';
    $frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
    $frm[0] .= "<tr>\r\n\t\t\t\t\t\t<td></td> \r\n\t\t\t\t\t\t<td></td>\r\n\t\t\t\t\t\t<td></td>\r\n\t\t\t\t\t</tr> \r\n\t\t\t\t\t
    <tr>\r\n\t\t\t\t\t\t\t\t<td>Tanggal</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=tanggal value='".$tanggal."'  onmousemove= \"setCalendar(this.id);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t
        <tr>\r\n\t\t\t\t\t\t<td>Analisa</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=tipeanalisa  style=\"width:150px;\">".$optAnalisa."</select></td>\r\n\t\t\t\t\t</tr>
        <tr>\r\n\t\t\t\t\t\t<td>Sub Unit</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=subunit onchange=getFormAnalisa() style=\"width:150px;\">".$optSubunit."</select></td>\r\n\t\t\t\t\t</tr>";
    $frm[0] .= '</table></fieldset>';
    $frm[0] .= '<div id=form style=display:none>';
    $frm[0] .= "<fieldset>";
    $frm[0] .= '<legend>'.$_SESSION['lang']['form'].'</legend>';
    $frm[0] .= '<table id=isi border=0 cellpadding=1 cellspacing=1>';
    $frm[0] .= '</table>';
    $frm[0] .= '</fieldset></div>';
    $frm[0] .= '<div id=editForm style=display:none>';
    $frm[0] .= "<fieldset>";
    $frm[0] .= '<legend>'.$_SESSION['lang']['edit'].' '.$_SESSION['lang']['form'].'</legend>';
    $frm[0] .= "\t<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t\t\t\t

    	<tr>\r\n\t\t\t\t\t\t\t\t<td>Parameter</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=parameterEdit disabled maxlength=50 disabled onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t
    	<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['nilai']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=inpEdit onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\t\t\t\t<input type=hidden id=idEdit disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\">\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t\t<button class=mybutton onclick=saveEditAnalisa()>Simpan</button>\r\n\t\t\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t</table>";
    $frm[0] .= '</fieldset></div><br>';
    $frm[0] .= "<fieldset>
                    <legend><b>Quick Posting</b></legend>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><select id=tanggal_quick_posting style=\"width:150px;\">".$optListTanggal."</select></td>
                        <td><button onclick=quickPostingAnalisa() class=mybutton name=btnQuickPosting id=btnQuickPosting>Posting</button></td>
                    </tr>
                    
                    
                </fieldset><br>";
    $frm[0] .= "<fieldset>
                    <legend><b>".$_SESSION['lang']['list']."</b></legend>
                    <tr>
                        <td>".$_SESSION['lang']['tanggal']."</td> 
                        <td>:</td>
                        <td><input type=text class=myinputtext onchange=loadDataAnalisa() id=tglsch onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
                    </tr>
                    <tr>
                        <td>Tipe Analisa</td>
                        <td>:</td>
                        <td><select id=tipeanalisasch onchange=loadDataAnalisa()  style='width:150px;'>".$optAnalisa."</select></td></td>
                       
                    </tr>
                    <div id=container> 
                        <script>loadDataAnalisa()</script>
                    </div>
                </fieldset>";
    $frm[0] .= '</fieldset>';
    $frm[1] = " <fieldset style='float:left;'>
                    <legend>
                        <b>Pabrik Analisa</b>
                    </legend>
                    <table>
                        <tr>
                            <td>Tipe Analisa</td> 
                            <td>:</td>
                            <td><select id=tipeanalisaR  style=\"width:150px;\">".$optAnalisa."</select></td>
                        </tr>
                        <tr>
                            <td>".$_SESSION['lang']['tanggal']."</td> 
                            <td>:</td>
                            <td>
                                <input type=text class=myinputtext  id=tglLap onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t</tr> \r\n\t<tr>\r\n\t\t
                        </tr>
                        <tr>
                            <td colspan=100>
                                <button onclick=iPreview('pabrik_slave_analisa','".$arrLaporan."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                                <button onclick=iExcel(event,'pabrik_slave_analisa.php','".$arrLaporan."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
                                <button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset style='clear:both'>
                    <legend>
                        <b>".$_SESSION['lang']['printArea']."</b>
                    </legend>
                    <div id='printContainer'  ></div>
                </fieldset>";
    $hfrm[0] = $_SESSION['lang']['form'];
    $hfrm[1] = $_SESSION['lang']['printArea'];
    drawTab('FRM', $hfrm, $frm, 250, 800);
    CLOSE_BOX();
    echo close_body();
    echo "\t\t\t\t\r\n";

?>