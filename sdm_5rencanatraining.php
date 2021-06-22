<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo " \r\n<script language=javascript1.2 src='js/sdm_5rencanatraining.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['rencanatraining'].':</b>');
$str = 'select * from '.$dbname.'.sdm_5jabatan order by kodejabatan';
$res = mysql_query($str);
$optgol = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optgol .= "<option value='".$bar->kodejabatan."'>".$bar->namajabatan.'</option>';
}
$str = 'select * from '.$dbname.".log_5supplier where kodekelompok = 'S001' order by namasupplier";
$res = mysql_query($str);
$opthost = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $host[$bar->supplierid] = $bar->namasupplier;
    $opthost .= "<option value='".$bar->supplierid."'>".$bar->namasupplier.'</option>';
}
$str = 'select * from '.$dbname.'.sdm_5jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jab[$bar->kodejabatan] = $bar->namajabatan;
}
$karyawanid = $_SESSION['standard']['userid'];
$str = 'select * from '.$dbname.".datakaryawan where karyawanid = '".$karyawanid."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $nam[$bar->karyawanid] = $bar->namakaryawan;
}
$str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan=5 and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
$res = mysql_query($str);
$optKar = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optKar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'</option>';
    $nam[$bar->karyawanid] = $bar->namakaryawan;
}
$stat[0] = '';
$stat[1] = $_SESSION['lang']['disetujui'];
$stat[2] = $_SESSION['lang']['ditolak'];
echo "<fieldset style='width:700px;'>\r\n    <legend> ".$_SESSION['lang']['form'].": </legend>\r\n    <table>\r\n    <tr><td valign=top>\r\n    <table>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['karyawan']."</td>\r\n        <td>\r\n            <input type=text class=myinputtext id=namakaryawan value =\"".$nam[$karyawanid]."\" disabled onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\">\r\n            <input type=hidden id=karyawanid name=karyawanid value=".$karyawanid." />\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['budgetyear']."</td>\r\n        <td><input type=text class=myinputtextnumber id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\"></td>\t\r\n    </tr>\r\n    <tr> \r\n        <td>".$_SESSION['lang']['kodetraining']."</td>\r\n        <td><input type=text class=myinputtext id=kodetraining name=kodetraining onkeypress=\"return tanpa_kutip();\" maxlength=30 style=\"width:150px;\" /></td>\t\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['namatraining']."</td>\r\n        <td><input type=text class=myinputtext id=namatraining name=namatraining maxlength=30 style=\"width:150px;\"></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['levelpeserta']."</td>\r\n        <td><select id=levelpeserta name=levelpeserta>".$optgol."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['penyelenggara']."</td>\r\n        <td><select id=penyelenggara name=penyelenggara>".$opthost."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['hargaperpeserta']."</td>\r\n        <td><input type=text class=myinputtextnumber id=hargaperpeserta name=hargaperpeserta onkeypress=\"return angka_doang(event);\" maxlength=12 style=\"width:150px;\"></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggalmulai']."</td>\r\n        <td><input id=\"tanggal1\" name=\"tanggal1\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n        <td><input id=\"tanggal2\" name=\"tanggal2\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['atasan']."</td>\r\n        <td><select id=persetujuan>".$optKar."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['hrd']."</td>\r\n        <td><select id=hrd>".$optKar."</select></td>\t\t\t \r\n    </tr>\r\n    </table>\t  \r\n    </td>\r\n    <td>  \r\n    <table>\r\n    <tr>\r\n        <td><fieldset><legend>".$_SESSION['lang']['deskripsitraining']."</legend>\r\n        <table>\r\n        <tr>\r\n            <td><textarea id='deskripsitraining'></textarea></td>\r\n        </tr>\r\n        </table>\r\n        </fieldset>\r\n        <fieldset><legend>".$_SESSION['lang']['hasildiharapkan']."</legend>\r\n        <table>\r\n        <tr>\r\n            <td><textarea id='hasildiharapkan'></textarea></td>\r\n        </tr>\r\n        </table>\r\n        </fieldset></td>\r\n    </tr>\r\n    </table>\t\r\n    </td>\r\n    </tr>\t  \r\n    </table>\t\r\n    <center><button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=batal()>".$_SESSION['lang']['cancel']."</button></center>\r\n    </fieldset>\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n     <!--".$_SESSION['lang']['budgetyear'].' : <select onchange=displayList() id=listtahun name=listtahun>'.$opttahun."</select>\r\n     <input type=hidden id=pilihantahun name=pilihantahun value='' />-->\r\n    <table class=sortable cellspacing=1 border=0 width=100%>\r\n        <thead>\r\n            <tr class=rowheader>\r\n            <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodetraining']."</td>\r\n            <td align=center>".$_SESSION['lang']['namatraining']."</td>\r\n            <td align=center>".$_SESSION['lang']['levelpeserta']."</td>\r\n            <td align=center>".$_SESSION['lang']['penyelenggara']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargaperpeserta']."</td>\r\n            <td align=center>".$_SESSION['lang']['tanggalmulai']."</td>\r\n            <td align=center>".$_SESSION['lang']['tanggalsampai']."</td>\r\n            <td align=center>".$_SESSION['lang']['atasan']."</td>\r\n            <td align=center>".$_SESSION['lang']['status'].' '.$_SESSION['lang']['atasan']."</td>\r\n            <td align=center>".$_SESSION['lang']['hrd']."</td>\r\n            <td align=center>".$_SESSION['lang']['status'].' '.$_SESSION['lang']['hrd']."</td>\r\n            <td colspan=3 align=center>".$_SESSION['lang']['action']."</td>\r\n            </tr>\r\n        </thead>\r\n\t<tbody id=container>";
$str = 'select * from '.$dbname.".sdm_5training where karyawanid = '".$karyawanid."'\r\n      ";
$res = mysql_query($str);
$no = 1;
while ($bar = mysql_fetch_object($res)) {
    echo "<tr class=rowcontent>\r\n    <td>".$no."</td>\r\n    <td>".$bar->tahunbudget."</td>\r\n    <td>".$bar->kode."</td>\r\n    <td>".$bar->namatraining."</td>\r\n    <td>".$jab[$bar->kodejabatan]."</td>\r\n    <td>".$host[$bar->penyelenggara]."</td>\r\n\r\n    <td align=right>".number_format($bar->hargasatuan, 0, '.', ',')."</td>\r\n    <td align=center>".tanggalnormal($bar->tglmulai)."</td>\r\n    <td align=center>".tanggalnormal($bar->tglselesai)."</td>\r\n    <td>".$nam[$bar->persetujuan1]."</td>\r\n    <td>".$stat[$bar->stpersetujuan1]."</td>\r\n    <td>".$nam[$bar->persetujuanhrd]."</td>\r\n    <td>".$stat[$bar->sthrd]."</td>\r\n    <td>";
    if (0 == $bar->stpersetujuan1 && 0 == $bar->sthrd) {
        echo "<img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edittraining('".$bar->tahunbudget."','".$bar->kode."','".$bar->namatraining."','".$bar->kodejabatan."','".$bar->penyelenggara."','".$bar->hargasatuan."','".tanggalnormal($bar->tglmulai)."','".tanggalnormal($bar->tglselesai)."','".$bar->persetujuan1."','".$bar->persetujuanhrd."','".str_replace("\n", '\\n', $bar->desctraining)."','".str_replace("\n", '\\n', $bar->output)."');\">";
    }

    echo "</td>\r\n    <td>";
    if (0 == $bar->stpersetujuan1 && 0 == $bar->sthrd) {
        echo "<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"deletetraining('".$bar->kode."');\">";
    }

    echo "</td>\r\n    <td>";
    echo "<img class=resicon src=images/pdf.jpg title='PDF' onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."')\">";
    echo "</td>\r\n    </tr>";
    ++$no;
}
echo "\t\r\n        </tbody>\r\n        <tfoot>\r\n        </tfoot>\r\n    </table>\r\n    </fieldset>";
CLOSE_BOX();
close_body();

?>