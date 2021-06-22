<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/sdm_pertanggungjawabanPJD.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['pertanggungjawabandinas']);
$str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where\r\n        karyawanid=".$_SESSION['standard']['userid']."\r\n\t\tand lunas=0 and statuspertanggungjawaban=0";
$res = mysql_query($str);
$optNo = '';
while ($bar = mysql_fetch_object($res)) {
    $optNo .= "<option value='".$bar->notransaksi."'>".$bar->notransaksi.'</option>';
}
$str = 'select * from '.$dbname.'.sdm_5jenisbiayapjdinas order by keterangan';
$res = mysql_query($str);
$optJns = '';
while ($bar = mysql_fetch_object($res)) {
    $optJns .= "<option value='".$bar->id."'>".$bar->keterangan.'</option>';
}
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['form']."</legend>\r\n\t <table>\r\n\t\t<tr>\r\n\t\t   <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t   <td><select id=notransaksi>".$optNo."</select>\r\n\t\t    <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJDOri(event);\"> \r\n\t\t   </td>\t\t   \r\n\t\t</tr>\t\r\n\t </table>\r\n\t <fieldset>\r\n\t    <legend>".$_SESSION['lang']['detail']."</legend>\r\n\t\t<table>\r\n\t\t <tr>\r\n\t\t    <td>".$_SESSION['lang']['tanggal']."<input type=hidden id=method value=insert></td>\r\n\t\t\t<td><input type=text size=10 id=tanggal class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)></td>\r\n\t\t\t<td>".$_SESSION['lang']['jenisbiaya']."</td>\r\n\t\t    <td><select id=jenisby>".$optJns."</select></td>\r\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t<td><input type=text id=keterangan size=30 maxlength=45 class=myinputtext onkeypress=\"return tanpa_kutip(event);\">\r\n\t\t    </td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t\t<td><input type=text id=jumlah size=12 maxlength=15 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this)>\r\n\t\t\t</td>\r\n\t\t\t<td>\r\n\t\t\t  <button class=mybutton onclick=savePPJD()>".$_SESSION['lang']['save']."</button>\r\n\t\t\t</td>\r\n\t\t</table>\r\n\t </fieldset>\r\n\t <fieldset>\r\n\t    <legend>".$_SESSION['lang']['datatersimpan']."</legend>\r\n\t\t<table class=sortable cellspacing=1>\r\n\t\t<thead>\r\n\t\t <tr>\r\n\t\t    <td>No.</td>\r\n                    <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t    <td>".$_SESSION['lang']['jenisbiaya']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t    <td></td>\r\n\t\t</tr>\t\r\n\t\t </thead>\t\r\n\t\t <tbody id=innercontainer>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t</table>\r\n\t\t<button class=mybutton onclick=selesai()>".$_SESSION['lang']['done']."</button>\r\n\t </fieldset>\r\n\t </fieldset>\r\n\t ";
$frm[1] = "<fieldset>\r\n         <legend>Description of the results of official travel</legend>\r\n\t\t <textarea id=uraian cols=60 rows=15 onkeypress=\"return tanpa_kutip(event);\"></textarea><br>\r\n\t\t <button class=mybutton onclick=simpanUraianPjDinas()>".$_SESSION['lang']['save']."</button>\r\n         </fieldset>\r\n\t\t ";
$frm[2] = "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t  <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['uangmuka']."</td>\r\n\t  <td>".$_SESSION['lang']['digunakan']."</td>\t  \r\n\t  <td>".$_SESSION['lang']['approval_status']."</td>\t  \r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
$limit = 20;
$page = 0;
if (isset($_POST['tex'])) {
    $notransaksi .= " and notransaksi like '%".$_POST['tex']."%' ";
}

$str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht \r\n        where\r\n\t\tkaryawanid=".$_SESSION['standard']['userid']."\r\n\t\t".$notransaksi."\r\n\t\torder by jlhbrs desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jlhbrs = $bar->jlhbrs;
}
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where\r\n        karyawanid=".$_SESSION['standard']['userid']."\r\n\t\t".$notransaksi."\r\n\t\torder by tanggalbuat desc  limit ".$offset.',20';
$res = mysql_query($str);
$no = $page * $limit;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $namakaryawan = '';
    $strx = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->karyawanid;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_object($resx)) {
        $namakaryawan = $barx->namakaryawan;
    }
    $add = '';
    if (0 == $bar->statuspertanggungjawaban) {
        $add .= "&nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPPJD('".$bar->notransaksi."');\">\r\n         ";
    }

    if (2 == $bar->statuspertanggungjawaban) {
        $stpersetujuan = $_SESSION['lang']['ditolak'];
    } else {
        if (1 == $bar->statuspertanggungjawaban) {
            $stpersetujuan = $_SESSION['lang']['disetujui'];
        } else {
            $stpersetujuan = $_SESSION['lang']['wait_approve'];
        }
    }

    $str1 = 'select sum(jumlah) as jumlah from '.$dbname.".sdm_pjdinasdt\r\n         where notransaksi='".$bar->notransaksi."'";
    $res1 = mysql_query($str1);
    $usage = 0;
    while ($bar1 = mysql_fetch_object($res1)) {
        $usage = $bar1->jumlah;
    }
    $frm[2] .= "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1."</td>\r\n\t  <td align=right>".number_format($bar->dibayar, 2, '.', ',')."</td>\r\n\t  <td align=right>".number_format($usage, 2, '.', ',')."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td align=center>\r\n\t     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']." (Cost)' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n\t\t <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']." (Task Result Description)' onclick=\"previewPJDUraian('".$bar->notransaksi."',event);\"> \r\n       ".$add."\r\n\t  </td>\r\n\t  </tr>";
}
$frm[2] .= "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";
$frm[2] .= "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['hasilkerjajumlah'];
$hfrm[2] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body();

?>