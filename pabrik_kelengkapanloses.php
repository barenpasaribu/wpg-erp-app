<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    echo open_body();
    include 'master_mainMenu.php';
    include_once 'lib/zLib.php';
    echo "\r\n<script language=javascript1.2 src='js/pabrik_kelengkapanloses.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/iReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n";
    $where = "`tipe`='PABRIK' AND kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
$whereKary = '';
$i = 0;
foreach ($optOrg as $key => $row) {
    if (0 === $i) {
        $whereKary .= "lokasitugas='".$key."'";
    } else {
        $whereKary .= " or lokasitugas='".$key."'";
    }

    ++$i;
}
$lokasitugas = $_SESSION['empl']['lokasitugas'];
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $s = "select a.mandor as karyawanid, b.namakaryawan as nama from ".$dbname.".pabrik_5shift a , datakaryawan b where a.mandor=b.karyawanid and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
    $q = mysql_query($s);
    while ($r = mysql_fetch_assoc($q)) {
        $optKary .= "<option value='".$r['karyawanid']."'>".$r['nama'].'</option>';
    }

    $optProduk = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $i = 'select distinct produk from '.$dbname.'.pabrik_5kelengkapanloses';
    $n = mysql_query($i);
    while ($d = mysql_fetch_assoc($n)) {
        $optProduk .= "<option value='".$d['produk']."'>".$d['produk'].'</option>';
    }

    $optListTanggal = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $queryGetTanggalList = 'select distinct tanggal from '.$dbname.'.pabrik_kelengkapanloses where posting = 0 and kodeorg = "'.$_SESSION['empl']['lokasitugas'].'"';
    $dataTanggalList = mysql_query($queryGetTanggalList);
    while ($d = mysql_fetch_assoc($dataTanggalList)) {
        $optListTanggal .= "<option value='".$d['tanggal']."'>".tanggalnormal($d['tanggal']).'</option>';
    }


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
    $arrLaporan = '##kodeorgLap##tglLap##produkLap';
    echo "\r\n\r\n\r\n\r\n";
    OPEN_BOX();
    $frm[0] .= "<fieldset>";
    $frm[0] .= '<legend><b>'.$_SESSION['lang']['kelengkapanloses'].'</b></legend>';
    $frm[0] .= "<fieldset>";
    $frm[0] .= '<legend>'.$_SESSION['lang']['form'].'</legend>';
    $frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
    $frm[0] .= "<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['kodeorg']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text maxlength=4 disabled value='".$_SESSION['empl']['lokasitugas']."' id=kodeorg onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t</tr> \r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text class=myinputtext  id=tgl autocomplete=off onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t\t\t\t\t\t</tr><tr><td>Shift 1</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=shift1 style=\"width:150px;\">".$optKary."</select></td>\r\n\t\t\t\t\t</tr><tr>\r\n\t\t\t\t\t\t<td>Shift 2</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=shift2  style=\"width:150px;\">".$optKary."</select></td>\r\n\t\t\t\t\t</tr></tr> \r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['produk']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=produk onchange=getForm() style=\"width:150px;\">".$optProduk."</select></td>\r\n\t\t\t\t\t</tr><tr>";
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
    $frm[0] .= "\t<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['kodeorg']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text maxlength=4 disabled value='".$_SESSION['empl']['lokasitugas']."' id=kodeorgEdit onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text class=myinputtext disabled  id=tglEdit onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['produk']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><select id=produkEdit disabled style=\"width:150px;\">".$optProduk."</select></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['namabarang']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=barangEdit disabled maxlength=50 disabled onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['nilai']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=inpEdit onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"></td><td>Nilai 1</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=inpEdit1 onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"></td><td>Nilai 2</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=inpEdit2 onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\t\t\t\t<input type=hidden id=idEdit disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\">\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t\t<button class=mybutton onclick=saveEdit()>Simpan</button>\r\n\t\t\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t</table>";
    $frm[0] .= '</fieldset></div><br>';
    $frm[0] .= "<fieldset>
                    <legend><b>Quick Posting</b></legend>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><select id=tanggal_quick_posting style=\"width:150px;\">".$optListTanggal."</select></td>
                        <td><button onclick=quickPosting() class=mybutton name=btnQuickPosting id=btnQuickPosting>Posting</button></td>
                    </tr>
                    
                </fieldset><br>";
    $frm[0] .= "<fieldset>
                    <legend><b>".$_SESSION['lang']['list']."</b></legend>
                    <tr>
                        <td>".$_SESSION['lang']['tanggal']."</td> 
                        <td>:</td>
                        <td><input type=text class=myinputtext onchange=loadData() id=tglsch onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
                    </tr>
                    <div id=container> 
                        <script>loadData()</script>
                    </div>
                </fieldset>";
    $frm[0] .= '</fieldset>';
    $frm[1] = " <fieldset style='float:left;'>
                    <legend>
                        <b>".$_SESSION['lang']['kelengkapanloses']."</b>
                    </legend>
                    <table>
                        <tr>
                            <td>".$_SESSION['lang']['kodeorg']."</td> 
                            <td>:</td>
                            <td><select id=kodeorgLap style='width:150px;'>".$optOrg."</select></td>
                        </tr>
                        <tr>
                            <td>".$_SESSION['lang']['tanggal']."</td> 
                            <td>:</td>
                            <td>
                                <input type=text class=myinputtext  id=tglLap onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t</tr> \r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['produk']."</td> 
                            <td>:</td>
                            <td><select id=produkLap style=\"width:150px;\">".$optProduk."</select></td>
                        </tr>
                        <tr>
                            <td colspan=100>
                                <button onclick=iPreview('pabrik_slave_kelengkapanloses','".$arrLaporan."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                                <button onclick=iExcel(event,'pabrik_slave_kelengkapanloses.php','".$arrLaporan."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
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