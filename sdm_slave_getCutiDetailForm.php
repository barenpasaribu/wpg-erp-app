<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    require_once 'config/connection.php';
    $periode = $_POST['periode'];
    $karyawanid = $_POST['karyawanid'];
    $kodeorg = $_POST['kodeorg'];
    $namakaryawan = $_POST['namakaryawan'];
    echo '  <fieldset>
                <legend>'.$_SESSION['lang']['form']."</legend>
                <table>
                    <tr>
                        <input type=hidden class=myinputtext id=kodeorgJ  value='".$kodeorg."'>
                        <input type=hidden class=myinputtext id=karyawanidJ value='".$karyawanid."'>
                        <input type=hidden class=myinputtext id=periodeJ value='".$periode."'>
                        <td> ".$_SESSION['lang']['namakaryawan']."</td>
                        <td>
                            <input type=text class=myinputtext id=namakaryawan disabled value='".$namakaryawan."' size=25>
                        </td>
                        <td>".$_SESSION['lang']['tangalcuti']."</td>
                        <td>
                            <input type=text class=myinputtext id=dariJ onmouseover=setCalendar(this) onkeypress=\"return false;\" size=15>
                        </td>
                        <td>".$_SESSION['lang']['tglcutisampai']."</td>
                        <td>
                            <input type=text class=myinputtext id=sampaiJ onmouseover=setCalendar(this) onkeypress=\"return false;\" size=15>
                    </tr>
                    <tr>
                        <td>".$_SESSION['lang']['diambil']."</td>
                        <td>
                            <input type=text class=myinputtextnumber id=diambilJ  size=25 onkeypress=\"return angka_doang(event);\"  size=3 maxlength=2>
                        </td>
                        <td>".$_SESSION['lang']['keterangan']."</td>
                        <td colspan=3>
                            <input type=text class=myinputtext id=keteranganJ onkeypress=\"return tanpa_kutip(event);\" size=35 maxlength=45>
                            <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>".$_SESSION['lang']['cuti'].'->['.$namakaryawan.'] '.$_SESSION['lang']['periode'].':'.$periode."</legend>
                <div style='width:750px;height:220px;overflow:scroll;' id=containerlist3>
                    <table class=sortable cellspacing=1 border=0>
                        <thead>
                            <tr class=rowheader>
                                <td>No</td>
                                <td>".$_SESSION['lang']['tangalcuti']."</td>
                                <td>".$_SESSION['lang']['tglcutisampai']."</td>
                                <td>".$_SESSION['lang']['diambil']."</td><td>".$_SESSION['lang']['keterangan']."</td>
                                <td>Tipe Cuti/Ijin</td>
                                <td>".$_SESSION['lang']['aksi']."</td>
                            </tr>
                        </thead>
                        <tbody>";
    $str = 'select * from '.$dbname.'.sdm_cutidt 
            where karyawanid='.$karyawanid."
            and periodecuti='".$periode."' 
            and kodeorg='".$kodeorg."'";
    $res = mysql_query($str);
    $no = 0;
    $ttl = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $tipeijin = "";
        if (empty($bar->tipeijin)) {
            $tipeijin = "-";
        }else{
            $tipeijin = $bar->tipeijin;
        }
        echo '  <tr class=rowcontent id=barisJ'.$no.">
                    <td>".$no."</td>
                    <td>".tanggalnormal($bar->daritanggal)."</td>
                    <td>".tanggalnormal($bar->sampaitanggal)."</td>
                    <td align=right>".$bar->jumlahcuti."</td>
                    <td>".$bar->keterangan."</td>
                    <td>".$tipeijin."</td>
                    <td>
                        <img src='images/application/application_delete.png'  title='".$_SESSION['lang']['delete']."' class=resicon onclick=\"hapusData('".$periode."','".$karyawanid."','".$kodeorg."','".$bar->daritanggal."','barisJ".$no."',".$bar->jumlahcuti.");\">
                    </td>
                </tr>";
        $ttl += $bar->jumlahcuti;
    }
    echo "  <tr class=rowcontent>
                <td colspan=3><b>TOTAL</b></td>
                <td align=right id=cellttl><b>".$ttl."</b></td>
                <td colspan=3></td>
            </tr>";
    echo "  </tbody>
        <tfoot></tfoot>
    </div>
</fieldset>";

?>