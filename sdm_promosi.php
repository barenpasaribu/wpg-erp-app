<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    include 'lib/devLibrary.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "<script language=javascript src='js/sdm_promosi.js?v=".mt_rand()."'></script>";
    OPEN_BOX('', $_SESSION['lang']['promosidemosi']);
    $pos = getEnum($dbname, 'sdm_riwayatjabatan', 'tipesk');
    $opts = "<option value=''></option>";
    foreach ($pos as $key => $val) {
        if ('Promosi' == $val) {
            $caption = $_SESSION['lang']['Promosi'];
        } else {
            if ('Demosi' == $val) {
                $caption = $_SESSION['lang']['Demosi'];
            } else {
                if ('Mutasi' == $val) {
                    $caption = $_SESSION['lang']['Mutasi1'];
                } else {
                    if ('Penyesuaian' == $val) {
                        $caption = $_SESSION['lang']['Penyesuaian'];
                    } else {
                        $caption = 'Rotasi';
                    }
                }
            }
        }

        $opts .= "<option value='".$val."'>".$caption.'</option>';
    }
    $status = getEnum($dbname, 'sdm_riwayatjabatan', 'statussk');
    $optstatus = "<option value=''></option>";
    foreach ($status as $key => $val) {
        $optstatus .= "<option value='".$val."'>".$val.'</option>';
    }
    // if ('HO' == substr($_SESSION['empl']['lokasitugas'], 2, 2)) {
    //     $str = ' select nik,karyawanid,namakaryawan,bagian from '.$dbname.".datakaryawan       where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan=5 order by namakaryawan";
    // } else {
    //     if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    //         $str = ' select nik,karyawanid,namakaryawan,bagian from '.$dbname.".datakaryawan       where left(lokasitugas,4) in (select kodeunit from ".$dbname.".bgt_regional_assignment       where regional='".$_SESSION['empl']['regional']."') and tipekaryawan in(1,2,3,4,5,6) order by namakaryawan";
    //     } else {
    //         $str = 'select nik,karyawanid,namakaryawan,bagian from '.$dbname.".datakaryawan       where left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'       and tipekaryawan in(1,2,3,4,6)       order by namakaryawan";
    //     }
    // }

    $str = '    select 
                nik,karyawanid,namakaryawan,bagian 
                from '.$dbname.".datakaryawan 
                where 
                left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."' 
                order by namakaryawan";
    $optkar = "<option value=''></option>";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optkar .= "<option value='".$bar->karyawanid."'>".$bar->nik.'-'.$bar->namakaryawan.' -['.$bar->bagian.']</option>';
    }
    
    $strAtasan = '  SELECT 
                    a.nik,a.karyawanid,a.namakaryawan,a.bagian 
                    FROM '.$dbname.".datakaryawan a
                    LEFT JOIN setup_approval b
                    ON a.karyawanid = b.karyawanid
                    where 
                    kodeunit = '".$_SESSION['empl']['lokasitugas']."'
                    and
                    applikasi like 'ATASAN%'
                    order by namakaryawan"; 
    $optAtasan = "<option value=''>- Pilih Atasan -</option>";
    $resAtasan = mysql_query($strAtasan);
    while ($bar = mysql_fetch_object($resAtasan)) {
        $optAtasan .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' - '.$bar->nik.' - ['.$bar->bagian.']</option>';
    }


    $optlokasitugas = "<option value=''>- Pilih Lokasi Tugas -</option>";
    $str = 'select 
            namaorganisasi,kodeorganisasi 
            from '.$dbname.".organisasi 
            where tipe not in('BLOK','PT','STENGINE','STATION')
            and length(kodeorganisasi)=4 order by namaorganisasi desc";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optlokasitugas .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $optjabatan = '';
    $str = 'select * from '.$dbname.".sdm_5jabatan order by namajabatan";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optjabatan .= "<option value='".$bar->kodejabatan."'>".$bar->namajabatan.'</option>';
    }
    $str = 'select * from '.$dbname.'.sdm_5tipekaryawan order by tipe';
    $opttipekaryawan = '';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $opttipekaryawan .= "<option value='".$bar->id."'>".$bar->tipe.'</option>';
    }
    $str = 'select * from '.$dbname.'.sdm_5departemen order by nama';
    $optdepartemen = '';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optdepartemen .= "<option value='".$bar->kode."'>".$bar->nama.'</option>';
    }
    $optgolongan = '';
    $str = 'select * from '.$dbname.'.sdm_5golongan order by kodegolongan';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optgolongan .= "<option value='".$bar->kodegolongan."'>".$bar->namagolongan.'</option>';
    }
    $frm[0] .= "    <fieldset>  
                        <legend>".$_SESSION['lang']['form']."</legend>     
                        <table>
                            <tr>   
                                <td>".$_SESSION['lang']['tipetransaksi']."</td>
                                <td><select id=tipetransaksi>".$opts."</select></td>  
                            </tr>
                            <tr>   
                                <td>".$_SESSION['lang']['karyawan']."</td>
                                <td><select id=karyawanid onchange=getKarStat(this.options[this.selectedIndex].value)>".$optkar."</select></td>  
                            </tr>
                            <tr>   
                                <td>".$_SESSION['lang']['tanggalsurat']."</td>
                                <td><input type=text id=tanggalsk size=10 maxlength=10 class=myinputtext onkeypress='return false;' onmouseover='setCalendar(this)'></td>  
                            </tr>
                            <tr>   
                                <td>".$_SESSION['lang']['tanggalberlaku']."</td>
                                <td><input type=text id=tanggalberlaku size=10 maxlength=10 class=myinputtext onkeypress='return false;' onmouseover=setCalendar(this)></td>  
                            </tr>  
                            <tr>   
                                <td>".$_SESSION['lang']['status']." ".$_SESSION['lang']['surat']."</td>
                                <td>
                                    <select id=statustransaksi>".$optstatus."</select>
                                </td>
                            </tr>   
                            <tr>   
                                <td>".$_SESSION['lang']['paragraf1']."<br>(If empty = default)</td>
                                <td><textarea cols=60 rows=2 id=paragraf1></textarea></td>  
                            </tr> 
                        </table> 
                        <table>
                            <tr>
                                <td>
                                    <fieldset>
                                        <legend>".$_SESSION['lang']['lama']."</legend>   
                                        <table>     
                                        <tr>
                                            <td>".$_SESSION['lang']['lokasitugas']."</td>
                                            <td>
                                                <select id=oldokasitugas>".$optlokasitugas."</select>
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td>".$_SESSION['lang']['functionname']."</td>    
                                            <td><select id=oldjabatan>".$optjabatan."</select></td> 
                                        </tr>      
                                        <tr>
                                            <td>".$_SESSION['lang']['tipekaryawan']."</td>    
                                            <td><select id=oldtipekaryawan>".$opttipekaryawan."</select></td> 
                                        </tr>     
                                        <tr>
                                            <td>".$_SESSION['lang']['departemen']."</td>    
                                            <td>
                                                <select id=olddepartemen>".$optdepartemen."</select>
                                            </td> 
                                        </tr>                                      
                                        <tr>
                                            <td>".$_SESSION['lang']['levelname']."</td>    
                                            <td><select id=oldgolongan>".$optgolongan."</select></td> 
                                        </tr>     
                                        <tr>
                                            <td>".$_SESSION['lang']['gajipokok']."</td>    
                                            <td>
                                                <input id=oldgaji type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> </td> 
                                            </tr>     
                                            <tr>
                                                <td>".$_SESSION['lang']['tjjabatan']."</td>    
                                                <td>
                                                    <input id=tjjabatan type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>Tunjangan Prestasi</td>    
                                                <td>
                                                    <input id=tjsdaerah type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> </td> 
                                            </tr>     
                                            <tr>
                                                <td>Tunjangan Kehadiran</td>    
                                                <td>
                                                    <input id=tjmahal type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> </td> 
                                                </tr>       
                                                <tr>
                                                    <td>Tunjangan Lainnya</td>    
                                                    <td><input id=tjpembantu type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> </td> 
                                                </tr>     
                                                <tr style='display: none;'>
                                                    <td>".$_SESSION['lang']['tjkota']."</td>    
                                                    <td>
                                                        <input id=tjkota type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                    </td> 
                                                </tr>     
                                                <tr style='display: none;'>
                                                    <td>".$_SESSION['lang']['tjtransport']."</td>    
                                                    <td>
                                                        <input id=tjtransport type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                    </td> 
                                                </tr>     
                                                <tr style='display: none;'>
                                                    <td>".$_SESSION['lang']['tjmakan']."</td>    
                                                    <td><input id=tjmakan type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> </td> 
                                                </tr>                        
                                            </table>   
                                        </fieldset>
                                    </td>
                                    <td>       
                                        <fieldset>     
                                            <legend>".$_SESSION['lang']['baru']."</legend>   
                                            <table>     
                                            <tr>
                                                <td>".$_SESSION['lang']['lokasitugas']."</td>    
                                                <td>
                                                    <select id=newlokasitugas onchange=getTjBaru()>".$optlokasitugas."</select>
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>".$_SESSION['lang']['functionname']."</td>    
                                                <td>
                                                    <select id=newjabatan onchange=getTjBaru()>".$optjabatan."</select>
                                                </td> 
                                            </tr>      
                                            <tr>
                                                <td>".$_SESSION['lang']['tipekaryawan']."</td>    
                                                <td>
                                                    <select id=newtipekaryawan>".$opttipekaryawan."</select>
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>".$_SESSION['lang']['departemen']."</td>    
                                                <td>
                                                    <select id=newdepartemen>".$optdepartemen."</select>
                                                </td> 
                                            </tr>                             
                                            <tr>
                                                <td>".$_SESSION['lang']['levelname']."</td>    
                                                <td>
                                                    <select id=newgolongan>".$optgolongan."</select>
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>".$_SESSION['lang']['gajipokok']."</td>    
                                                <td>
                                                    <input id=newgaji type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>".$_SESSION['lang']['tjjabatan']."</td>    
                                                <td>
                                                    <input id=ketjjabatan type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>Tunjangan Prestasi</td>    
                                                <td>
                                                    <input id=ketjsdaerah type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>Tunjangan Kehadiran</td>    
                                                <td>
                                                    <input id=ketjmahal type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>       
                                            <tr>
                                                <td>Tunjangan Lainnya</td>    
                                                <td><input id=ketjpembantu type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> </td> 
                                            </tr>     
                                            <tr style='display: none;'>
                                                <td>".$_SESSION['lang']['tjkota']."</td>    
                                                <td>
                                                    <input id=ketjkota type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr style='display: none;'> 
                                                <td>".$_SESSION['lang']['tjtransport']."</td>    
                                                <td>
                                                    <input id=ketjtransport type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr style='display: none;'>
                                                <td>".$_SESSION['lang']['tjmakan']."</td>    
                                                <td>
                                                    <input id=ketjmakan type=text class=myinputtextnumber  value=0 size=15 maxlength=15 onkeypress='return angka_doang(event);' onblur=change_number(this)> 
                                                </td> 
                                            </tr>     
                                            <tr>
                                                <td>".$_SESSION['lang']['atasan']."</td>    
                                                <td>
                                                    <select id=atasanbaru>".$optAtasan."</select>
                                                </td> 
                                            </tr>                                        
                                        </table>   
                                    </fieldset>
                                </td>   
                            </tr>       
                        </table>            
                        <table>       
                            <tr>   
                                <td>
                                    ".$_SESSION['lang']['paragraf2']."
                                    <br>
                                    (If empty = default)
                                </td>
                                <td>
                                    <textarea cols=60 rows=3 id=paragraf2></textarea>
                                </td>  
                            </tr>               
                            <tr> 
                                <td>     ".$_SESSION['lang']['penandatangan']." </td> 
                                <td>    
                                    <input type=text class=myinputtext id=penandatangan size=35 maxlength=35 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr> 
                            <tr> 
                                <td>     ".$_SESSION['lang']['functionname']." </td> 
                                <td>    
                                    <input type=text class=myinputtext id=namajabatan size=35 maxlength=45 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr> 
                            <tr> 
                                <td>    
                                    ".$_SESSION['lang']['tembusan']."(i) 
                                </td> 
                                <td>    
                                    <input type=text class=myinputtext id=tembusan1 size=25 maxlength=35 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr> 
                            <tr> 
                                <td>     ".$_SESSION['lang']['tembusan']."(ii) </td> 
                                <td>    
                                    <input type=text class=myinputtext id=tembusan2 size=25 maxlength=35 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr> 
                            <tr> 
                                <td> ".$_SESSION['lang']['tembusan']."(iii) </td> 
                                <td>    
                                    <input type=text class=myinputtext id=tembusan3 size=25 maxlength=35 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr> 
                            <tr> 
                                <td>     ".$_SESSION['lang']['tembusan']."(iv) </td> 
                                <td>    
                                    <input type=text class=myinputtext id=tembusan4 size=25 maxlength=35 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr> 
                            <tr> 
                                <td>     ".$_SESSION['lang']['tembusan']."(v) </td> 
                                <td>    
                                    <input type=text class=myinputtext id=tembusan5 size=25 maxlength=35 onkeypress='return tanpa_kutip(event);'> 
                                </td>  
                            </tr>      
                        </table> 
                        <center>   
                            <input type=hidden id=method value='insert'>   
                            <input type=hidden id=nosk value=''>   
                            <button class=mybutton onclick=savePromosi()>".$_SESSION['lang']['save']."</button>   
                            <button class=mybutton onclick=clearForm()>".$_SESSION['lang']['new']."</button> 
                        </center> 
                    </fieldset>";
    $frm[1] = "<fieldset>   <legend>".$_SESSION['lang']['list']."</legend>  
                                <fieldset>  ".$_SESSION['lang']['cari_transaksi']."  
                                <input type=text id=txtbabp size=25 class=myinputtext onkeypress='return tanpa_kutip(event);' maxlength=9>  
                                <button class=mybutton onclick=cariSK(0)>".$_SESSION['lang']['find']."</button>  
                                </fieldset>  
                                <table class=sortable cellspacing=1 border=0>      
                                <thead>  <tr class=rowheader>  
                                <td>No.</td>  <td>".$_SESSION['lang']['nomorsk']."</td>  
                                <td>".$_SESSION['lang']['karyawan']."</td>  
                                <td>".$_SESSION['lang']['tanggalsurat']."</td>  
                                <td>".$_SESSION['lang']['tipetransaksi']."</td>
                                <td>".$_SESSION['lang']['dbuat_oleh']."</td>  
                                <td>
                                </td>  
                            </tr>  
                            </head>  
                            <tbody id=containerlist>   </tbody>   
                            <tfoot>   
                            </tfoot>   
                        </table> 
                    </fieldset>";
    $hfrm[0] = $_SESSION['lang']['form'];
    $hfrm[1] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body('');
    echo "  <script>window.onload = function() {
        loadList();
    };</script>";

?>