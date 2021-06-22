<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/sdm_pjdinas.js'></script>\r\n";
OPEN_BOX('', $_SESSION['lang']['perjalanandinas']);
//$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\nwhere tipekaryawan=5 and karyawanid <>".$_SESSION['standard']['userid']." and kodegolongan<'4' order by namakaryawan";
//$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\nwhere karyawanid <>".$_SESSION['standard']['userid']." and karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan' ) order by namakaryawan";
$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
$res = mysql_query($str);
$optKar = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optKar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
}
//$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and kodegolongan in ('1A','1B','1C','1D','1E','1F','1G','1H') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
//$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid <>".$_SESSION['standard']['userid'].' and karyawanid in (select karyawanid from setup_approval where lower(applikasi)=\'atasan dari atasan\' ) order by namakaryawan';
$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='atasan dari atasan') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
$res = mysql_query($str);
$optKar2 = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optKar2 .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
}
//$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and bagian in ('HO_HRGA','RO_HRGA') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
//$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid <>".$_SESSION['standard']['userid'].' and karyawanid in (select karyawanid from setup_approval where lower(applikasi)=\'persetujuan hrd\' ) order by namakaryawan';
$str = 'select namakaryawan,karyawanid,nik from '.$dbname.".datakaryawan\r\n      where karyawanid in (select karyawanid from setup_approval where lower(applikasi)='hrd') and karyawanid <>".$_SESSION['standard']['userid'].' and kodeorganisasi in (select distinct kodeorganisasi from datakaryawan where kodeorganisasi != \'\' and lokasitugas like \''.$_SESSION['empl']['lokasitugas'].'%\' ) order by namakaryawan';
$res = mysql_query($str);
$optKarHrd = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optKarHrd .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
}
$str = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi\r\n      where length(kodeorganisasi)=4 order by namaorganisasi desc";
$res = mysql_query($str);
echo mysql_error($conn);
$optOrg = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optOrg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$lokasitugas = $_SESSION['empl']['lokasitugas'];
$str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where karyawanid=".$_SESSION['standard']['userid']." ";
$str .= "UNION select t2.namakaryawan,t1.karyawanid from setup_pengaturanadmin as t1 left join datakaryawan as t2 on (t1.karyawanid=t2.karyawanid)
where t1.perjalanandinas='1' order by namakaryawan";
$namakaryawan = '';
$karyawanid = '';
$optKarData = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namakaryawan = $bar->namakaryawan;
    $karyawanid = $bar->karyawanid;
	if( $karyawanid == $_SESSION['standard']['userid']){
		$optKarData .= '<option value="'.$karyawanid.'" selected>'.$namakaryawan.'</option>';
	}else{
		$optKarData .= '<option value="'.$karyawanid.'">'.$namakaryawan.'</option>';
	}
}
$frm[0] .= "\r\n     <fieldset>\r\n\t  <legend>".$_SESSION['lang']['form']."</legend>\r\n     <table>\r\n\t <tr>\r\n\t   <input type=hidden value='insert' id=method>\r\n\t   <input type=hidden value='' id=notransaksi>\r\n\t    <td>".$_SESSION['lang']['nama']."</td>\r\n\t\t<td><select id='karyawanid'>".$optKarData."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t    <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td><select id='kodeorg'><option  value='".$lokasitugas."'>".$lokasitugas."</option></select></td>\r\n\t </tr>\t \r\n\t <tr>\r\n\t    <td>".$_SESSION['lang']['tanggaldinas']."</td>\r\n\t\t<td><input type=text id=tanggalperjalanan class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10>\r\n\t\t    ".$_SESSION['lang']['tanggalkembali']." \r\n\t\t\t<input type=text id=tanggalkembali class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10>\r\n\t\t</td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t    <td>".$_SESSION['lang']['transportasi'].'/'.$_SESSION['lang']['akomodasi']."</td>\r\n\t\t<td>\r\n\t\t     <input type=checkbox id=pesawat> ".$_SESSION['lang']['pesawatudara']."\r\n\t\t\t <input type=checkbox id=darat> ".$_SESSION['lang']['transportasidarat']."\r\n\t\t\t <input type=checkbox id=laut> ".$_SESSION['lang']['transportasiair']."\r\n\t\t\t <input type=checkbox id=mess> ".$_SESSION['lang']['mess']."\r\n\t\t\t <input type=checkbox id=hotel> ".$_SESSION['lang']['hotel']."\r\n\t\t\t <input type=checkbox id=mobilsewa>Mobil Sewa\r\n  <input type=checkbox id=mobildinas>Mobil Dinas      </td>\r\n\t </tr>\t\r\n\t \r\n\t <tr>\r\n\t   <td>\r\n\t      ".$_SESSION['lang']['uangmuka']."\r\n\t   </td>\r\n\t   <td>\r\n\t     <input type=text class=myinputtextnumber onblur=change_number(this) id=uangmuka onkeypress=\"return angka_doang(event);\" size=15 maxlength=15>\r\n\t   </td>\r\n\t </tr> \t \r\n\t \r\n\t  <tr>\r\n\t   <td>\r\n\t      ".$_SESSION['lang']['keterangan']."\r\n\t   </td>\r\n\t   <td>\r\n\t     <textarea  id=ket onkeypress=\"return tanpa_kutip(event);\"></textarea>\r\n\t   </td>\r\n\t </tr> \r\n\t \r\n\t \t \r\n\t </table>\r\n\t <table>\r\n\t   <tr>\r\n\t     <td>\r\n\t\t     ".$_SESSION['lang']['tujuan']."1\r\n\t\t </td>\r\n\t     <td>\r\n\t\t   <select id='tujuan1' style='width:150px'>".$optOrg."</select>\r\n\t\t   ".$_SESSION['lang']['tugas']."\r\n\t\t   <input type=text id=tugas1 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>\r\n\t\t </td>\r\n\t\t</tr>\r\n\t\t<tr> \r\n\t     <td>\r\n\t\t    ".$_SESSION['lang']['tujuan']."2\r\n\t\t </td>\r\n\t     <td>\r\n\t\t    <select id='tujuan2' style='width:150px'>".$optOrg."</select>\r\n\t\t\t".$_SESSION['lang']['tugas']."\r\n\t\t\t<input type=text id=tugas2 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>\r\n\t\t </td>\t\t \t\t \t\t \r\n\t   </tr>\r\n\t\t</tr>\r\n\t\t<tr>\t   \r\n\t   <tr>\r\n\t     <td>\r\n\t\t     ".$_SESSION['lang']['tujuan']."3\r\n\t\t </td>\r\n\t     <td>\r\n\t\t   <select id='tujuan3' style='width:150px'>".$optOrg."</select>\r\n\t\t   ".$_SESSION['lang']['tugas']."\r\n\t\t   <input type=text id=tugas3 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>\r\n\t\t </td>\r\n\t\t</tr>\r\n\t\t<tr>\t\t \r\n\t     <td>\r\n\t\t    ".$_SESSION['lang']['tujuan']."4\r\n\t\t </td>\r\n\t     <td>\r\n\t\t    <input type=text style='width:150px' id=tujuanlain class=myinputtext onkeypress=\"return tanpa_kutip(event)\" maxlength=45>\r\n\t\t    ".$_SESSION['lang']['tugas']."\r\n\t\t\t<input type=text id=tugaslain class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>\r\n\t\t </td>\t\t \t\t \t\t \r\n\t   </tr>\r\n\t </table>\r\n\t   <fieldset>\r\n\t      <legend>\r\n\t\t    ".$_SESSION['lang']['approve']."\r\n\t\t  </legend>\r\n\t\t  <table>\r\n\t\t   <tr>\r\n\t\t     <td>".$_SESSION['lang']['atasan']."</td>\r\n\t\t\t <td>\r\n\t\t\t    <select id=persetujuan>".$optKar."</select>\r\n\t\t\t </td>\r\n\t\t   </tr>\r\n\t\t   <tr>\r\n\t\t     <td>".$_SESSION['lang']['atasan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['atasan']."</td>\r\n\t\t\t <td>\r\n\t\t\t    <select id=persetujuan2>".$optKar2."</select>\r\n\t\t\t </td>\r\n\t\t   </tr>\r\n\t\t   <tr>\t \r\n\t\t     <td>".$_SESSION['lang']['hrd']."</td>\r\n\t\t\t <td>\r\n\t\t\t    <select id=hrd>".$optKarHrd."</select>\r\n\t\t\t </td>\t\t\t \r\n\t\t   </tr>\r\n\t\t  </table>\r\n\t   </fieldset>\t \r\n\t <center>\r\n\t   <button class=mybutton onclick=simpanPJD()>".$_SESSION['lang']['save']."</button>\r\n\t   <button class=mybutton onclick=clearForm()>".$_SESSION['lang']['new']."</button>\r\n\t </center>\r\n\t </fieldset>";
$frm[1] = "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t  <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['approval_status']."</td>\r\n\t  <td>".$_SESSION['lang']['approval_status']." 2</td>\r\n\t  <td>".$_SESSION['lang']['hrd']."</td>\r\n\t  <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
$limit = 20;
$page = 0;
if (isset($_POST['tex'])) {
    $notransaksi .= $_POST['tex'];
}

$str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht \r\n        where notransaksi like '%".$notransaksi."%'\r\n\t\tand karyawanid=".$_SESSION['standard']['userid']."\r\n\t\torder by jlhbrs desc";
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
$str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where notransaksi like '%".$notransaksi."%'\r\n        and karyawanid=".$_SESSION['standard']['userid']."\r\n\t\torder by tanggalbuat desc limit ".$offset.',20';
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
    if (0 == $bar->statuspersetujuan && 0 == $bar->statushrd) {
        $add .= "&nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">\r\n\t\t &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">\r\n         ";
    }

    if (2 == $bar->statuspersetujuan) {
        $stpersetujuan = $_SESSION['lang']['ditolak'];
    } else {
        if (1 == $bar->statuspersetujuan) {
            $stpersetujuan = $_SESSION['lang']['disetujui'];
        } else {
            $stpersetujuan = $_SESSION['lang']['wait_approve'];
            $stpersetujuan .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan','".$bar->notransaksi."')>".$optKar.'</select>';
        }
    }

    if (2 == $bar->statuspersetujuan2) {
        $stpersetujuan2 = $_SESSION['lang']['ditolak'];
    } else {
        if (1 == $bar->statuspersetujuan2) {
            $stpersetujuan2 = $_SESSION['lang']['disetujui'];
        } else {
            $stpersetujuan2 = $_SESSION['lang']['wait_approve'];
            $stpersetujuan2 .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan2','".$bar->notransaksi."')>".$optKar2.'</select>';
        }
    }

    if (2 == $bar->statushrd) {
        $sthrd = $_SESSION['lang']['ditolak'];
    } else {
        if (1 == $bar->statushrd) {
            $sthrd = $_SESSION['lang']['disetujui'];
        } else {
            $sthrd = $_SESSION['lang']['wait_approve'];
            $sthrd .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select   style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'hrd','".$bar->notransaksi."')>".$optKarHrd.'</select>';
        }
    }

    if (2 == $bar->statuspersetujuan) {
        $stpersetujuan2 = '';
        $sthrd = '';
    }

    $frm[1] .= "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td>".$stpersetujuan2."</td>\r\n\t  <td>".$sthrd."</td>\t\r\n\t  <td align=center>\r\n\t     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n       ".$add."\r\n\t  </td>\r\n\t  </tr>";
}
$frm[1] .= "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";
$frm[1] .= "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body('');

?>