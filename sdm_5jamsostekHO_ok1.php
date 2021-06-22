<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    echo open_body();
    echo "<script language=javascript1.2 src='js/sdm_payrollHO.js'></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
    include 'master_mainMenu.php';
    
    $lokasiTugasKaryawan = $_SESSION['empl']['lokasitugas'];
    $statusSelected = '';
    $queryTipePekerjaan = "SELECT tipe from organisasi where kodeorganisasi='" . $lokasiTugasKaryawan."'";
    $queryActTP = mysql_query($queryTipePekerjaan);
    $resultQueryTP = mysql_fetch_array($queryActTP);

    $optlokres = '';
    $str = '';
    if($resultQueryTP['tipe'] == "HOLDING"){
        $statusSelected = 'H';
        $optlokres .= "<option value='H' style='text-transform: uppercase;'> Kantor Pusat (HO) </option>";
        $optlokres .= "<option value='M' style='text-transform: uppercase;'>" . $_SESSION['lang']['pabrik'] . '</option>';
        $optlokres .= "<option value='E' style='text-transform: uppercase;'>" . $_SESSION['lang']['kebun'] . '</option>';
        $str = 'SELECT * FROM ' . $dbname . '.sdm_ho_hr_jms_porsi ORDER BY lokasiresiko, id ASC';
    }
    if($resultQueryTP['tipe'] == "PABRIK"){
        $statusSelected = 'M';
        $optlokres .= "<option value='M' style='text-transform: uppercase;'>" . $_SESSION['lang']['pabrik'] . '</option>';
        $str = 'SELECT * FROM ' . $dbname . '.sdm_ho_hr_jms_porsi where lokasiresiko="M" ORDER BY lokasiresiko, id ASC';
    }
    if($resultQueryTP['tipe'] == "KEBUN"){
        $statusSelected = 'E';
        $optlokres .= "<option value='E' style='text-transform: uppercase;'>" . $_SESSION['lang']['kebun'] . '</option>';
        $str = 'SELECT * FROM ' . $dbname . '.sdm_ho_hr_jms_porsi where lokasiresiko="E" ORDER BY lokasiresiko, id ASC';
    }    
    
    // $optlokres .= "<option value='R' style='text-transform: uppercase;'> Kantor Perwakilan (RO) </option>";
    // $optbeban = "<option value='perusahaan' style='text-transform: uppercase;' selected>" . $_SESSION['lang']['perusahaan'] . '</option>';
    // $optbeban .= "<option value='karyawan' style='text-transform: uppercase;'>" . $_SESSION['lang']['karyawan'] . '</option>';
    
    // 01. Mengambil data dari table sdm_ho_hr_jms_porsi
    
    $res = mysql_query($str);
    $kar = 0;
    $prsh = 0;
    $usiapensiun = 0;
    $jhtkar = 0;
    $jpkar = 0;
    $jhtpt = 0;
    $jppt = 0;
    $jkkptE = 0;
    $jkkptHO = 0;
    $jkkptM = 0;
    $jkkptRO = 0;
    $jkmpt = 0;
    $bpjspt = 0;
    $bpjskar = 0;
    $listtable = "
    <tr>
        <td>No.</td>
        <td>Lokasi Kerja</td>
        <td>ID</td>
        <td>Nilai</td>
        <td>JHT Karyawan</td>
        <td>JP Karyawan</td>
        <td>BPJS Karyawan</td>
        <td>BPJSM Karyawan</td>
        <td>JMP Karyawan</td>
        <td>JHT Perusahaan</td>
        <td>JP Perusahaan</td>
        <td>JKK Perusahaan</td>
        <td>JKM Perusahaan</td>
        <td>BPJS Perusahaan</td>
        <td>BPJSM Perusahaan</td>
        <td>JMP Perusahaan</td>
    </tr>
    ";
    $i = 1;
    while ($bar = mysql_fetch_object($res)) {
        $listtable .= "
            <tr>
                <td>" . $i . "</td>
                <td>" . $bar->lokasiresiko . "</td>
                <td>" . $bar->id . "</td>
                <td>" . $bar->value . "</td>
                <td>" . $bar->jhtkar . "</td>
                <td>" . $bar->jpkar . "</td>
                <td>" . $bar->bpjskar . "</td>
                <td>" . $bar->bpjsmk . "</td>
                <td>" . $bar->jmpk . "</td>
                <td>" . $bar->jhtpt . "</td>
                <td>" . $bar->jppt . "</td>
                <td>" . $bar->jkkpt . "</td>
                <td>" . $bar->jkmpt . "</td>
                <td>" . $bar->bpjspt . "</td>
                <td>" . $bar->bpjsmpt . "</td>
                <td>" . $bar->jmppt . "</td>
            </tr>
        ";
        $i++;

        if ('karyawan' == $bar->id && $bar->lokasiresiko == $statusSelected) {
            $kar = $bar->value;
            $jhtkar = $bar->jhtkar;
            $jpkar = $bar->jpkar;
            $bpjskar = $bar->bpjskar;
            $jmpk = $bar->jmpk;
            $bpjsmk = $bar->bpjsmk;
            $lokres = $bar->lokasiresiko;
        }

        if ('perusahaan' == $bar->id && $bar->lokasiresiko == $statusSelected) {
            $prshE = $bar->value;
            $jhtpt = $bar->jhtpt;
            $jkmpt = $bar->jkmpt;
            $jppt = $bar->jppt;
            $jkkptE = $bar->jkkpt;
            $jmppt = $bar->jmppt;
            $bpjsmpt = $bar->bpjsmpt;
            $bpjspt = $bar->bpjspt;
        }

        if ('usiapensiun' == $bar->id && $bar->lokasiresiko == $statusSelected) {
            $usiapensiun = $bar->value;
        }

       
    }
    
    OPEN_BOX('', '<b>PENGATURAN BPJS TK & BPJS KESEHATAN:</b>');
    echo '<div id=period>';
    $optc = "<option value='" . date('Y-m') . "'>" . date('m-Y') . '</option>';
    for ($v = -2; $v < 3; ++$v) {
        $per = mktime(0, 0, 0, date('m') - $v, 15, date('Y'));
        $optc .= '<option value=' . date('Y-m', $per) . '>' . date('m-Y', $per) . '</option>';
    }
    echo "
            <fieldset style='width:99%;'>\r\n \t\t
                <legend>
                    <b>Porsi BPJS TK Dan BPJS Kesehatan :</b>\r\n\t\t
                </legend>\r\n
                <table border=0 style='width:90%;'>\r\n
                    <tr>\r\n
                        <td align='left'>Lokasi Resiko Kerja</td>\r\n
                        <td>:</td>\r\n
                        <td colspan=15>
                            <select id=lokres disabled onchange=ambilpersen() style='width:100%;text-transform: uppercase;'>" . $optlokres . "</select>
                        </td>\r\n
                    </tr>\r\n
                    <tr>\r\n
                        <td colspan=28></td>\r\n
                    </tr>\r\n
                    <tr>\r\n
                        <td align='left'>Karyawan</td>\r\n
                        <td>:</td>\r\n
                        <td align=right>JHT</td>\r\n
                        <td align=center>:</td>\r\n
                        <td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtKar();\" maxlength=4 id=jhtkar size=2 value=" . $jhtkar . " disabled></td>\r\n
                        <td>%</td>\r\n    
                        <td>&#43;</td>\r\n
                        <td align=right>JP</td>\r\n
                        <td align=center>:</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtKar();\" maxlength=4 id=jpkar size=2 value=" . $jpkar . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n\r\n
                        <td>&#43;</td>\r\n
                        <td align=right>BPJS</td>\r\n
                        <td align=center>:</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtKar();\" maxlength=4 id=bpjskar size=2 value=" . $bpjskar . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n\r\n
                        <td>&#61;</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 id=karyawan size=2 value=" . $kar . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n\r\n\r\n
                    </tr>\r\n
                    <tr>
                        <td align='left'>Perusahaan</td>\r\n
                        <td>:</td>\r\n
                        <td align=right>JHT</td>\r\n
                        <td align=center>:</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtPT();\" maxlength=4 id=jhtpt size=2 value=" . $jhtpt . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n
                        <td>&#43;</td>\r\n
                        <td align=right>JP</td>\r\n
                        <td align=center>:</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtPT();\" maxlength=4 id=jppt size=2 value=" . $jppt . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n\r\n
                        <td>&#43;</td>\r\n
                        <td align=right>JKK</td>\r\n
                        <td align=center>:</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtPT();\" maxlength=4 id=jkkpt size=2 value=" . $jkkptE . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n
                        <td>&#43;</td>\r\n
                        <td align=right>JKM</td>\r\n
                        <td align=center>:</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtPT();\" maxlength=4 id=jkmpt size=2 value=" . $jkmpt . " disabled>
                        </td>\r\n
                        <td>%</td>\r\n
                        <td>&#43;</td>\r\n
                        <td align=right>BPJS</td>\r\n
                        <td align=center>:</td>\r\n
                        <td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=\"hitungjhtPT();\" maxlength=4 id=bpjspt size=2 value=" . $bpjspt . " disabled></td>\r\n
                        <td>%</td>\r\n\r\n\r\n
                        <td>&#61;</td>\r\n
                        <td>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 id=perusahaan size=3 value=" . $prshE . " disabled>%</td>\r\n\r\n\r\n\r\n\r\n
                    </tr>\r\n
                    <tr>
                        <td align='left'>Usia Pensiun (Tahun)</td>\r\n
                        <td>:</td>\r\n
                        <td colspan=6 align=left style='padding-right: 40px;'>
                            <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 id=usiapensiun size=10 value=" . $usiapensiun . " disabled>
                        </td>\r\n
                    </tr>
                    <tr>\r\n
                        <td colspan=28></td>\r\n
                    </tr>\r\n
                </table>\r\n\t\t
                <div style='width: 100%; height:24px; padding-left: 8%;' >
                    
                    <div style='width: 20%;float: left;'>
                        <div style='width: 100%; text-align: left; margin-left: 10px; margin-right: 10px;' >
                            <div style='width: 47%;float: left; padding-top: 5px; text-align: right; padding-right:5px;'>JMPK (Rp)</div>
                            <div style='width: 50%;float: left;'>
                                <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 id=jmpk size=10 value=" . $jmpk . " disabled>
                            </div>
                            <div style='float: none;'></div>
                        </div>
                    </div>
                    
                    
                    <div style='width: 20%;float: left;'>
                        <div style='width: 100%; text-align: left; margin-left: 10px; margin-right: 10px;' >
                            <div style='width: 47%;float: left; padding-top: 5px; text-align: right; padding-right:5px;'>JMPPT (Rp)</div>
                            <div style='width: 50%;float: left;'>
                                <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 id=jmppt size=10 value=" . $jmppt . " disabled>
                            </div>
                            <div style='float: none;'></div>
                        </div>
                    </div>
                    
                    <div style='width: 20%;float: left;'>
                        <div style='width: 100%; text-align: left; margin-left: 10px; margin-right: 10px;' >
                            <div style='width: 47%;float: left; padding-top: 5px; text-align: right; padding-right:5px;'>BPJSMK (Rp)</div>
                            <div style='width: 50%;float: left;'>
                                <input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 id=bpjsmk size=10 value=" . $bpjsmk . " disabled>
                            </div>
                            <div style='float: none;'></div>
                        </div>
                    </div>
                    
                    <div style='width: 20%;float: left;'>
                        <div style='width: 100%; text-align: left; margin-left: 10px; margin-right: 10px;' >
                            <div style='width: 47%;float: left; padding-top: 5px; text-align: right; padding-right:5px;'>BPJSMPT (Rp)</div>
                            <div style='width: 50%;float: left;'>
                                <input type=text class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\" id=bpjsmpt value=" . $bpjsmpt . " disabled>
                            </div>
                            <div style='float: none;'></div>
                        </div>
                    </div>
                    

                    <div style='float: none;'></div>
                </div>
                <div style='float: none;'></div><br>
                <div style='text-align:center; margin-top: 5px; margin-bottom: 10px;'>
                    <button id=saveBPJS class=mybutton onclick=setJmsPorsi() disabled>Save</button>&nbsp;\r\n
                    <button id=updBPJS class=mybutton onclick=updateJmsPorsi()>Update</button>\r\n
                    <button id=batalBPJS class=mybutton onclick=clearJmsPorsi()>Cancel</button>\r\n\t\t
                </div>
            </fieldset>
        </div>";

    echo "
            <fieldset style='width:99%;text-align:center;'>\r\n \t\t
                <legend><b>List Data :</b>\r\n\t\t</legend>\r\n
                <table border=1 style='width:99%' id='listdata'>\r\n
                    " . $listtable . "
                </table>
            </fieldset>
        </div>";
    CLOSE_BOX();
    echo close_body();
?>