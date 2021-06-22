<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/sdm_jatahBBM.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['penggantiantransport']);
$optthn = "<option value=''></option>";
for ($x = -1; $x < 10; ++$x) {
    $mk = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optthn .= "<option value='".date('Y-m', $mk)."'>".date('m-Y', $mk).'</option>';
}
$str = 'select a.namakaryawan,a.karyawanid, b.namajabatan from '.$dbname.".datakaryawan a\r\n      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n\t  where a.alokasi=1\r\n\t  and (a.tanggalkeluar is NULL or a.tanggalkeluar>'".date('Y-m-d')."')\r\n\t  order by a.namakaryawan";
$res = mysql_query($str);
$optKaryawan = '';
while ($bar = mysql_fetch_object($res)) {
    $optKaryawan .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' [ '.$bar->namajabatan.' ]</option>';
}
$str = "select kodeorganisasi,namaorganisasi from \r\n      ".$dbname.".organisasi where tipe='pt'";
$res = mysql_query($str);
$optPt = '';
while ($bar = mysql_fetch_object($res)) {
    $optPt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['form']."</legend>\r\n\t <table>\r\n\t   <tr>\r\n\t      <td>".$_SESSION['lang']['periode']."</td>\r\n\t\t  <td><select id=periode onchange=getNotransaksi(this.options[this.selectedIndex].value) style='width:200px;'>".$optthn."</select></td>\r\n\t      <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t  <td><input type=text class=myinputtext id=notransaksi size=15 disabled style='width:200px;'></td>\t\t     \r\n\t   </tr>\r\n\t   <tr>\r\n\t      <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t\t  <td><select id=karyawanid  style='width:200px;'>".$optKaryawan."</select></td>\t\t  \r\n\t      <td>".$_SESSION['lang']['alokasibiaya']."</td>\r\n\t\t  <td><select id=pt  style='width:200px;'>".$optPt."</select></td>\t\t  \r\n\t   </tr>\r\n\t   <tr>\r\n\t      <td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t  <td><input type=text id=keterangan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=45  style='width:200px;'></td>\t\t    \r\n\t   </tr>\r\n\t </table>\r\n     ";
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['biayabiaya']."</legend>\r\n\t <table>\r\n\t   <tr>\r\n\t      <td>".$_SESSION['lang']['transport']."</td>\r\n\t\t  <td><input type=text class=myinputtextnumber id=bytransport size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0></td>\r\n\t      <td>".$_SESSION['lang']['perawatan']."</td>\r\n\t\t  <td><input type=text class=myinputtextnumber id=byperawatan size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0></td>\t\t     \r\n\t   </tr>\r\n\t   <tr>\r\n\t      <td>".$_SESSION['lang']['toll']."</td>\r\n\t\t  <td><input type=text class=myinputtextnumber id=bytoll size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0></td>\t\t  \r\n\t      <td>".$_SESSION['lang']['lain']."</td>\r\n\t\t  <td><input type=text class=myinputtextnumber id=bylain size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0>\r\n\t\t  Total<input type=text id=total disabled value=0 class=myinputtextnumber size=15>\r\n\t\t  </td>\t\t  \r\n\t   </tr>\r\n\t </table>\r\n     </fieldset>\r\n\t <input type=hidden value=insert id=method>\r\n\t <button class=mybutton  id='savebtn' onclick=saveBBM();>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelBBM()>".$_SESSION['lang']['new']."</button>\r\n\t </fieldset>\r\n\t ";
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['detail']."</legend>\r\n\t <table>\r\n\t   <tr>\r\n\t      <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t  <td><input type=text class=myinputtext id=tanggal onmouseover=setCalendar(this) size=12 maxlength=12 onkeypress=\"return false;\"></td>\r\n\t      <td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>\r\n\t\t  <td><input type=text class=myinputtextnumber id=jlhbbm size=12 maxlength=5 onkeypress=\"return angka_doang(event);\" value=0>Ltr.</td>\t\t     \r\n\t      <td>".$_SESSION['lang']['totalharga']."</td>\r\n\t\t  <td><input type=text class=myinputtextnumber id=totalharga size=12 maxlength=8 onkeypress=\"return angka_doang(event);\" value=0>(Rp).</td>\r\n\t      <td><button class=mybutton onclick=saveLitre()>".$_SESSION['lang']['save']."</button></td>\r\n\t   </tr>\r\n\t </table>\r\n     <div style='width:500px;height:150px; overflow:scroll;'>\r\n\t  <table cellspacing=1 border=0 style='width:450px'>\r\n\t  <thead>\r\n\t  <tr class=rowheader>\r\n\t     <td>No</td>\r\n\t\t <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t <td>".$_SESSION['lang']['jumlah']."(Ltr)</td>\r\n\t\t <td>".$_SESSION['lang']['total']."(Rp)</td>\r\n\t\t <td></td>\r\n\t  </thead>\r\n\t  <tbody id=containerSolar>\r\n\t  \r\n\t  </tbody>\r\n\t  <tfoot>\r\n\t  </tfoot>\r\n\t  </table>\r\n\t </div>\t \r\n     </fieldset>";
$frm[1] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['list']."</legend>\r\n\t Periode<select id=periox onchange=getData(this.options[this.selectedIndex].value)>".$optthn."</select>\r\n\t <img src='images/pdf.jpg' class=resicon onclick=previewBBMPeriode(event) title='view'>\r\n\t <div style='width;700px;height:300px;overflow:scroll;'>\r\n\t <table class=sortable cellspacing=1 border=0>\r\n\t <thead>\r\n\t   <tr class=rowheader>\r\n\t     <td>No.</td>\r\n\t\t <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t <td>".$_SESSION['lang']['periode']."</td>\r\n\t\t <td>".$_SESSION['lang']['pt']."</td>\r\n\t\t <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t\t <td>".$_SESSION['lang']['totalbiaya']."</td>\r\n\t\t <td>".$_SESSION['lang']['dibayar']."</td>\r\n\t\t <td>".$_SESSION['lang']['tanggalbayar']."</td>\r\n\t\t <td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>\r\n\t\t <td>".$_SESSION['lang']['keterangan']."</td>\t\r\n\t\t <td></td>\t \r\n\t   </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
$str = 'select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from '.$dbname.".sdm_penggantiantransport a\r\n      left join ".$dbname.".sdm_penggantiantransportdt b \r\n\t  on a.notransaksi=b.notransaksi\r\n\t  left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid\r\n\t   where periode='".date('Y-m')."' and \r\n\t  kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t  group by notransaksi";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $add = '';
    if (0 == $bar->posting) {
        $add .= " <img src='images/close.png' class=resicon onclick=deleteBBM('".$bar->notransaksi."') title='delete'>";
    }

    $add .= " <img src='images/pdf.jpg' class=resicon onclick=previewBBM('".$bar->notransaksi."',event) title='view'>";
    $frm[1] .= "<tr class=rowcontent>\r\n\t     <td>".$no."</td>\r\n\t\t <td>".$bar->notransaksi."</td>\r\n\t\t <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n\t\t <td>".$bar->alokasi."</td>\r\n\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t <td align=right>".number_format($bar->totalklaim, 2, ',', '.')."</td>\r\n\t\t <td align=right>".number_format($bar->dibayar, 2, ',', '.')."</td>\r\n\t\t <td>".tanggalnormal($bar->tanggalbayar)."</td>\r\n\t\t <td align=right>".number_format($bar->bbm, 2, ',', '.')."</td>\r\n\t\t <td>".$bar->keterangan."</td>\t\r\n\t\t <td>".$add."</td>\t \r\n\t   </tr>";
}
$frm[1] .= "</tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>\r\n\t </div>\r\n     </fieldset>";
$frm[2] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['pembayaran']."</legend>\r\n\t <div style='width;700px;height:300px;overflow:scroll;'>\r\n\t <table class=sortable cellspacing=1 border=0>\r\n\t <thead>\r\n\t   <tr class=rowheader>\r\n\t     <td>No.</td>\r\n\t\t <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t <td>".$_SESSION['lang']['periode']."</td>\r\n\t\t <td>".$_SESSION['lang']['pt']."</td>\r\n\t\t <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t\t <td>".$_SESSION['lang']['totalbiaya']."</td>\r\n\t\t <td>".$_SESSION['lang']['dibayar']."</td>\r\n\t\t <td>".$_SESSION['lang']['tanggalbayar']."</td>\r\n\t\t <td></td>\t \r\n\t   </tr>\r\n\t </thead>\r\n\t <tbody id=containerbayar>";
$str2 = 'select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from '.$dbname.".sdm_penggantiantransport a\r\n      left join ".$dbname.".sdm_penggantiantransportdt b \r\n\t  on a.notransaksi=b.notransaksi\r\n\t  left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid\r\n\t   where \r\n\t    a.posting=0 and\r\n\t  a.kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t  group by notransaksi";
$res2 = mysql_query($str2);
$no = 0;
while ($bar = mysql_fetch_object($res2)) {
    ++$no;
    $frm[2] .= "<tr class=rowcontent>\r\n\t     <td>".$no."</td>\r\n\t\t <td>".$bar->notransaksi."</td>\r\n\t\t <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n\t\t <td>".$bar->alokasi."</td>\r\n\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t <td align=right>".number_format($bar->totalklaim, 2, ',', '.')."</td>\r\n\t\t <td align=right><img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value=".$bar->totalklaim."\">\r\n\t\t                  <input type=text id=bayar".$no." class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=12 onblur=change_number(this) size=12></td>\r\n\t\t <td><input type=text id=tglbayar".$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".date('d-m-Y')."'></td>\r\n\t     <td><img src='images/save.png' title='Save' class=resicon onclick=saveBBMClaim('".$no."','".$bar->notransaksi."')></td>\r\n\t   </tr>";
}
$frm[2] .= "</tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>\r\n\t </div>\r\n     </fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
$hfrm[2] = $_SESSION['lang']['pembayaran'];
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body();

?>