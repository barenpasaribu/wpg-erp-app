<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_ruangrapat.js'></script>\r\n";
for ($i = 0; $i < 24; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $jm .= '<option value='.$i.'>'.$i.'</option>';
}
for ($i = 0; $i < 60; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $mnt .= '<option value='.$i.'>'.$i.'</option>';
}
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select karyawanid, namakaryawan from '.$dbname.".datakaryawan \r\n       where tipekaryawan='5'  and lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n           and (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\r\n       order by namakaryawan asc";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optKary .= '<option value='.$rOrg['karyawanid'].'>'.$rOrg['namakaryawan'].'</option>';
}
$optPeriodec = '<option value='.date('Y').'>'.date('Y').'</option>';
$optPeriodec .= '<option value='.(date('Y') + 1).'>'.(date('Y') + 1).'</option>';
$frm[0] = '';
$frm[1] = '';
$arr = '##tanggalDt##tglAwal##tglEnd##method##agenda##room##pic##jam1##mnt1##jam2##mnt2';
include 'master_mainMenu.php';
OPEN_BOX();
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['form']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t   <td><input type='text' class='myinputtext' id='tanggalDt' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>Mulai</td>\r\n\t   <td>\r\n           <input type='text' class='myinputtext' id='tglAwal' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /><select id=\"jam1\">".$jm.'</select>:<select id="mnt1">'.$mnt."</select></td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t   <td>Sampai</td>\r\n\t   <td>\r\n           <input type='text' class='myinputtext' id='tglEnd' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /><select id=\"jam2\">".$jm.'</select>:<select id="mnt2">'.$mnt."</select></td>\r\n\t </tr>\t\r\n         <tr>\r\n\t   <td>Agenda</td>\r\n\t   <td><input type=\"text\" class=\"myinputtext\" id=\"agenda\" name=\"agenda\" onkeypress=\"return tanpa_kutip(event);\" maxlength=\"30\" style=\"width:150px;\" /></td>\r\n\t </tr>\r\n         <tr>\r\n\t   <td>Ruangan</td>\r\n\t   <td><select id=room name=room>\r\n                                 <option value='R.Rapat Lt.1 HO'>R.Rapat Lt.1 HO</option>\r\n                                 <option value='R.Rapat Lt.Dasar HO'>R.Rapat Lt.Dasar HO</option>\r\n                                 <option value='R.Rapat Lt.2 HO'>R.Rapat Lt.2 HO</option>\r\n                                 <option value='R.Rapat Besar SSRO'>R.Rapat Besar SSRO</option>\r\n                                 <option value='R.Rapat Direksi SSRO'>R.Rapat Direksi SSRO</option>\r\n                                 <option value='R.Rapat KTRO'>R.Rapat KTRO</option>\r\n                                 </select></td>\r\n\t </tr>\r\n         <tr>\r\n\t   <td>PIC</td>\r\n\t   <td><select id=\"pic\" style=\"width:150px\">".$optKary."</select></td>\r\n\t </tr>\r\n\t </table>\r\n\t <input type=hidden value=insert id=method>\r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_ruangrapat','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type='hidden' id=idData name=idData value='' />";
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n    <table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>".$_SESSION['lang']['tanggal']."</td>\r\n           <td>".$_SESSION['lang']['roomname']."</td>\r\n           <td>".$_SESSION['lang']['mulai']."</td>\r\n           <td>".$_SESSION['lang']['sampai']."</td>\r\n\t   <td>Agenda</td>\r\n           <td>".$_SESSION['lang']['pic']."</td>\r\n           <td>".$_SESSION['lang']['status']."</td>\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container><script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
$frm[1] .= "\r\n<fieldset>\r\nJika ada keperluan yang lebih penting dalam penggunaan ruang rapat, dan ruang rapat sudah dipesan oleh orang lain, \r\nsilahkan negosiasi dengan PIC bersangkutan untuk melakukan 'Cancel'<hr>\r\n<table cellpadding=1 cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td>\r\n    <td align=left><input type='text' class='myinputtext' id='tglCari' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n    </tr></table>\r\n <button class=mybutton onclick=loadData2()>".$_SESSION['lang']['preview']."</button>\r\n</table>\r\n</fieldset>\r\n<fieldset><legend>".$_SESSION['lang']['list']."</legend>\r\n    <table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>".$_SESSION['lang']['tanggal']."</td>\r\n           <td>".$_SESSION['lang']['roomname']."</td>\r\n           <td>".$_SESSION['lang']['mulai']."</td>\r\n           <td>".$_SESSION['lang']['sampai']."</td>\r\n\t   <td>Agenda</td>\r\n           <td>".$_SESSION['lang']['pic']."</td>\r\n           <td>".$_SESSION['lang']['reservedby']."</td>\r\n           <td>".$_SESSION['lang']['status']."</td>\r\n            <td>".$_SESSION['lang']['waktu']."</td>   \r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container2>";
$frm[1] .= "</tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body();

?>