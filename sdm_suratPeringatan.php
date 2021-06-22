<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "<script language=javascript src='js/sdm_sp.js'></script>\r\n";
    echo "<script language=javascript>loadList();</script>\r\n";
    OPEN_BOX('', $_SESSION['lang']['sutarperingatan']);
    $opts = '';
    $str = 'select * from '.$dbname.'.sdm_5jenissp order by kode';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $opts .= "<option value='".$bar->kode."'>".$bar->keterangan.'</option>';
    }
    $str = 'select id,tipe from '.$dbname.'.sdm_5tipekaryawan';
    $grr = mysql_query($str);
    while ($bar = mysql_fetch_object($grr)) {
        $tip[$bar->id] = $bar->tipe;
    }
    if ('HO' == substr($_SESSION['empl']['lokasitugas'], 2, 2)) {
        $str = ' select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from '.$dbname.".datakaryawan\r\n       where tanggalkeluar is NULL and tipekaryawan=5 order by namakaryawan";
    } else {
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
            $str = ' select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from '.$dbname.".datakaryawan\r\n       where left(lokasitugas,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment\r\n       where regional='".$_SESSION['empl']['regional']."') ";
        } else {
            $str = ' select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from '.$dbname.".datakaryawan\r\n       where left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n  order by namakaryawan";
        }
    }


    $optkar = "<option value=''></option>";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optkar .= "<option value='".$bar->karyawanid."'>".$bar->nik.' | '.$bar->namakaryawan.' | '.$tip[$bar->tipekaryawan].' | '.$bar->lokasitugas.' | '.$bar->subbagian.'</option>';
    }
    $paragraf3 = readTextFile('config/sp_paragraf3.lst');
    $paragraf4 = readTextFile('config/sp_paragraf4.lst');
    if ('HO' == substr($_SESSION['empl']['lokasitugas'], 2, 2)) {
        $str = ' select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n         where length(kodeorganisasi)=4";
    } else {
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
            $str = ' select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n         where kodeorganisasi in(select kodeunit from ".$dbname.".bgt_regional_assignment\r\n                                                     where regional='".$_SESSION['empl']['regional']."')";
        } else {
            $str = ' select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n         where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
        }
    }

    $rss = mysql_query($str);
    $optLok = "<option value='%'>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($rss)) {
        $optLok .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $str = ' select id,tipe from '.$dbname.'.sdm_5tipekaryawan';
    $rss = mysql_query($str);
    $optT = "<option value='%'>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($rss)) {
        $optT .= "<option value='".$bar->id."'>".$bar->tipe.'</option>';
    }
    $frm[0] .= "\r\n     <fieldset>\r\n          <legend>".$_SESSION['lang']['form']."</legend>\r\n     <table>\r\n          <tr> \t \r\n                 <td>\r\n                 <input type=hidden value='insert' id=method>\r\n                 <input type=hidden value='' id=nosp>\r\n                 ".$_SESSION['lang']['jenissp'].'</td><td><select id=jenissp>'.$opts."</select></td>\r\n          </tr>\r\n          
                
                <tr>
                    <td>".$_SESSION['lang']['tanggalsurat']."</td>
                    <td>
                        <input type=text id=tanggalsp size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Berlaku</td>
                    <td>
                        <input type=text id=tanggalberlaku size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)>
                    </td>
                </tr>
                <tr> 
                    <td>".$_SESSION['lang']['masaberlaku']."</td>
                    <td>
                        <select id=masaberlaku>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='6'>6</option>
                            <option value='9'>9</option>
                            <option value='12'>12</option>
                        </select>".$_SESSION['lang']['bulan']."
                    </td>
                </tr>
                <tr> 
                    <td>".$_SESSION['lang']['lokasitugas'].'</td>
                    <td>
                        <select id=lokasitugas onchange=filterK()>'.$optLok."</select>
                    </td>
                </tr>
                <tr> 
                    <td>".$_SESSION['lang']['tipekaryawan'].'</td>
                    <td><select id=tipekaryawan onchange=filterK()>'.$optT."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['karyawan'].'</td>
                    <td><select onchange="cekSP()" id=karyawanid>'.$optkar."</select></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style='valign:top'>Paragraf 1</td>
                    <td>
                        <textarea id=paragraf1 onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3></textarea>
                    </td>
                </tr>
                <tr>
                    <td style='valign:top'>".$_SESSION['lang']['pelanggaran']."</td>
                    <td>
                        <textarea id=pelanggaran onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3></textarea>
                    </td>
                </tr>
                <tr>
                    <td style='valign:top'>Paragraf 3</td>
                    <td>
                        <textarea id=paragraf3 onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3>".$paragraf3."</textarea>
                    </td>
                </tr>
                <tr>
                    <td style='valign:top'>Paragraf 4</td>
                    <td>
                        <textarea id=paragraf4 onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3>".$paragraf4."</textarea>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>
                        ".$_SESSION['lang']['disetujui']."\r\n         
                    </td>
                    <td>
                        <input type=text class=myinputtext id=penandatangan size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
                    </td>
                    <td>
                    ".$_SESSION['lang']['verifikasi']."
                    </td>
                    <td>
                        <input type=text class=myinputtext id=verifikasi size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
                    </td>
                    <td>
                        ".$_SESSION['lang']['dibuat']."
                    </td>
                    <td>
                        <input type=text class=myinputtext id=dibuat size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
                    </td>
                </tr>
                <tr>\r\n         <td>\r\n             ".$_SESSION['lang']['functionname']."\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=jabatan size=25 maxlength=50 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td> \r\n         <td>\r\n             ".$_SESSION['lang']['functionname']."\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=jabatan1 size=25 maxlength=50 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td>\r\n          <td>\r\n             ".$_SESSION['lang']['functionname']."\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=jabatan2 size=25 maxlength=50 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td>        \r\n         </tr>\r\n         </table>\r\n         <br>\r\n         <table>\r\n         <tr>\r\n         <td>\r\n            ".$_SESSION['lang']['tembusan']."(i)\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=tembusan1 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td> \r\n         </tr>\r\n         <tr>\r\n         <td>\r\n             ".$_SESSION['lang']['tembusan']."(ii)\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=tembusan2 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td> \r\n         </tr>\r\n         <tr>\r\n         <td>\r\n             ".$_SESSION['lang']['tembusan']."(iii)\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=tembusan3 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td> \r\n         </tr>\r\n         <tr>\r\n         <td>\r\n             ".$_SESSION['lang']['tembusan']."(iiii)\r\n         </td>\r\n         <td>\r\n            <input type=text class=myinputtext id=tembusan4 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">\r\n         </td> \r\n         </tr>\t \t \t \t \r\n         </table>\r\n         <center>\r\n           <button class=mybutton onclick=saveSP()>".$_SESSION['lang']['save']."</button>\r\n           <button class=mybutton onclick=window.location.reload()>".$_SESSION['lang']['new']."</button>\r\n         </center>\r\n         </fieldset>";
    $frm[1] = "<fieldset>\r\n           <legend>".$_SESSION['lang']['list']."</legend>\r\n          <fieldset><legend></legend>\r\n          ".$_SESSION['lang']['caripadanama']."\r\n          <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n          <button class=mybutton onclick=cariSP(0)>".$_SESSION['lang']['find']."</button>\r\n          </fieldset>\r\n          <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n          <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['nomorsk']."</td>\r\n          <td>".$_SESSION['lang']['karyawan']."</td>\r\n          <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n          <td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n          <td>".$_SESSION['lang']['tipetransaksi']."</td>\r\n          <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n          <td></td>\r\n          </tr>\r\n          </head>\r\n           <tbody id=containerlist>\r\n           </tbody>\r\n           <tfoot>\r\n           </tfoot>\r\n           </table>\r\n         </fieldset>";
    $hfrm[0] = $_SESSION['lang']['form'];
    $hfrm[1] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body('');

?>