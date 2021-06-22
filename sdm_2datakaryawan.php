<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    include 'lib/zFunction.php';
    require_once 'lib/devLibrary.php';

    echo open_body();
    echo "<script language=javascript1.2 src='js/datakaryawan.js'></script>";


    $optthnmsk = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    $sql = 'SELECT distinct left(tanggalmasuk,4) as thnmsk FROM '.$dbname.'.datakaryawan order by tanggalmasuk desc';
    $qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
    while ($data = mysql_fetch_assoc($qry)) {
        $optthnmsk .= '<option value='.$data['thnmsk'].'>'.$data['thnmsk'].'</option>';
    }
    $optblnmsk = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    $sql = 'SELECT distinct mid(tanggalmasuk,6,2) as blnmsk FROM '.$dbname.'.datakaryawan order by mid(tanggalmasuk,6,2) desc';
    $qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
    while ($data = mysql_fetch_assoc($qry)) {
        $optblnmsk .= '<option value='.$data['blnmsk'].'>'.$data['blnmsk'].'</option>';
    }
    $optthnkel = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    $sql = "SELECT distinct left(tanggalkeluar,4) as thnkel FROM ".$dbname.".datakaryawan where left(tanggalkeluar,4) != '0000' order by tanggalkeluar desc";
    $qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
    while ($data = mysql_fetch_assoc($qry)) {
        $optthnkel .= '<option value='.$data['thnkel'].'>'.$data['thnkel'].'</option>';
    }
    $optblnkel = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    $sql = 'SELECT distinct mid(tanggalkeluar,6,2) as blnkel FROM '.$dbname.'.datakaryawan order by mid(tanggalkeluar,6,2) desc';
    $qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
    while ($data = mysql_fetch_assoc($qry)) {
        $optblnkel .= '<option value='.$data['blnkel'].'>'.$data['blnkel'].'</option>';
    }
    include 'master_mainMenu.php';
    OPEN_BOX('', $_SESSION['lang']['inputdatakaryawan']);
    $optlokasitugas = "<option value=''>".$_SESSION['lang']['all']."</option>";
    $saveable = '';

    $optlokasitugas= makeOption2(getQuery("lokasitugas"),
        array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
        array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
    );

    $opttipekaryawan = '';
    if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas']) || 'KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
        $str = 'select * from '.$dbname.'.sdm_5tipekaryawan order by tipe';
    } else {
        $str = 'select * from '.$dbname.'.sdm_5tipekaryawan where id<>0 order by tipe';
    }

    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $opttipekaryawan .= "<option value='".$bar->id."'>".$bar->tipe.'</option>';
    }
    $opttipekaryawan .= '<option value=100>Kecuali SKU/SKUP</option>';
    $optJK = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    $arrenum = getEnum($dbname, 'datakaryawan', 'jeniskelamin');
    foreach ($arrenum as $key => $val) {
        $optJK .= "<option value='".$key."'>".$val.'</option>';
    }
    echo "  <fieldset>
                <table>
                    <tr valign=middle>
                        <legend>".$_SESSION['lang']['find']."</legend>
                        <td>";
    echo                    $_SESSION['lang']['caripadanama']." : 
                            <input type=text id=txtsearch  style='width:120px' size=25 maxlength=30 class=myinputtext> &nbsp";
    echo                    $_SESSION['lang']['nik']." : 
                            <input type=text id=nik  style='width:75px' size=25 maxlength=30 class=myinputtext> &nbsp";
    echo                    $_SESSION['lang']['lokasitugas']." : 
                            <select id=schorg  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optlokasitugas."</select> &nbsp ";
    echo                    $_SESSION['lang']['tipekaryawan']." : 
                            <select id=schtipe  onchange=changeCaption1(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all'].'</option>'.$opttipekaryawan.'</select> &nbsp ';
    echo                    $_SESSION['lang']['status']." : 
                            <select id=schstatus  style='width:75px' onchange=changeCaption(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option><option value='0000-00-00'>".$_SESSION['lang']['aktif']."</option><option value='*'>".$_SESSION['lang']['tidakaktif'].'</select> &nbsp ';
    echo                    $_SESSION['lang']['jeniskelamin']." : 
                            <select id=schjk  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optJK.'</select> &nbsp ';
    echo "              
                        </td>
                    
                    </tr>
                    <tr>
                        <td>
                            Tipe Duplikat:
                            <select id=tipe_duplikat  style='width:150px'>
                                <option value=''>Seluruhnya</option>
                                <option value='1'>Duplikat</option>
                                <option value='0'>Bukan Duplikat</option>
                            </select> &nbsp
                            Kode Resign:
                            <select id=kode_resign  style='width:150px'>
                                <option value=''>Seluruhnya</option>
                                <option value='PHK'>PHK</option>
                            </select> &nbsp
                            <button class=mybutton onclick=cariKaryawanLaporan(1)>".$_SESSION['lang']['find']."</button>
                        </td>
                    </tr>
                
                </table> 
            </fieldset>
            <table>
                <tr valign=middle>
                    <td>
                        <fieldset>
                            <legend>Find by period</legend>";
    echo ''.$_SESSION['lang']['tahun'].' '.$_SESSION['lang']['masuk']." : <select id='thnmsk' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optthnmsk.' &nbsp  </select>';
    echo ' '.$_SESSION['lang']['bulan'].' '.$_SESSION['lang']['masuk']." : <select id='blnmsk' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optblnmsk.' &nbsp  </select>';
    echo ''.$_SESSION['lang']['tahun'].' '.$_SESSION['lang']['keluar']." : <select id='thnkel' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optthnkel.' &nbsp  </select>';
    echo ' '.$_SESSION['lang']['bulan'].' '.$_SESSION['lang']['keluar']." : <select id='blnkel' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optblnkel.'</select>&nbsp';
    echo '<button class=mybutton onclick=cariKaryawanLaporan(1)>'.$_SESSION['lang']['find'].'</button> ';
    echo "</fieldset></td>\r\n     </tr>\r\n         </table> ";
    echo "<div id='searchplace'>".$_SESSION['lang']['daftarkaryawan'].' '.$_SESSION['empl']['lokasitugas'].":<span id=cap1></span>-<span id=cap2></span>\r\n\r\n         ";
    echo " <img src=images/excel.jpg class=resicon title='Excel' onclick=datakaryawanExcel(event,'','sdm_slave_datakaryawan_Excel.php')>\r\n\r\n\r\n         <table class=sortable border=0 cellspacing=1>\r\n         <thead>\r\n           <tr class=rowheader>\r\n             <td align=center>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>\r\n                 <td align=center>".$_SESSION['lang']['nik']."</td>\r\n                 <td align=center>".$_SESSION['lang']['nama']."</td>\r\n                 <td align=center>".$_SESSION['lang']['functionname']."</td>\r\n                 <td align=center>".$_SESSION['lang']['kodegolongan']."</td>\r\n                 <td align=center>".$_SESSION['lang']['lokasitugas']."</td>\r\n                 <td align=center>".$_SESSION['lang']['pt']."</td>\r\n                 <td align=center>".$_SESSION['lang']['subunit']."</td>\r\n                 <td align=center>".$_SESSION['lang']['pendidikan']."</td>\r\n                 <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['statuspajak'])."</td>\r\n                 <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['statusperkawinan'])."</td>\r\n                 <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['jumlahanak'])."</td>\r\n                 <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>\r\n                 <td align=center>".$_SESSION['lang']['tanggalkeluar']."</td>\r\n                 <td align=center>".str_replace(' ', '<br>', $_SESSION['lang']['tipekaryawan'])."</td>\r\n                 <td align=center>".$_SESSION['lang']['action']."</td>\r\n           </tr>\r\n         </thead>\r\n\r\n         <tbody id=searchplaceresult>\r\n         </tbody>\r\n         <tfoot>\r\n         </tfoot> \r\n                 <tr align=center><td colspan=20 align=center>\r\n         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn>< ".$_SESSION['lang']['pref']." </button> \r\n         &nbsp \r\n         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn> ".$_SESSION['lang']['lanjut']." ></button>\r\n        </td><tr>\r\n         </table>\r\n     </div>";
    CLOSE_BOX();
    close_body('');

?>