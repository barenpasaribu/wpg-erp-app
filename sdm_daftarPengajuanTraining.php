<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/sdm_daftarPengajuanTraining.js'></script>\r\n";
OPEN_BOX('', $_SESSION['lang']['rencanatraining']);
$str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 order by namakaryawan";
$optKar = "<option value=''></option>";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optKar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'</option>';
    $nam[$bar->karyawanid] = $bar->namakaryawan;
}
$frm[0] = "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['find']." : \r\n\t  <select id=pilihkaryawan onchange=loadList()>".$optKar."</select>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t  <td>".$_SESSION['lang']['namatraining']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalmulai']."</td>\r\n\t  <td>".$_SESSION['lang']['hargaperpeserta']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n\t  <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
$limit = 20;
$page = 0;
if (isset($_POST['pilihkaryawan'])) {
    $pilihkaryawan .= $_POST['karyawanid'];
}

$str = 'select count(*) as jlhbrs from '.$dbname.".sdm_5training \r\n        where karyawanid like '%".$pilihkaryawan."%'\r\n\t\torder by jlhbrs desc";
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
$saya = $_SESSION['standard']['userid'];
$str = 'select * from '.$dbname.".sdm_5training \r\n        where karyawanid like '%".$pilihkaryawan."%'\r\n\t\torder by tglmulai desc,tglselesai desc,updatetime desc  limit ".$offset.',20';
$res = mysql_query($str);
$no = $page * $limit;
while ($bar = mysql_fetch_object($res)) {
    if ($bar->persetujuan1 == $saya) {
        $sayaadalah = 'atasan';
    }

    if ($bar->persetujuanhrd == $saya) {
        $sayaadalah = 'hrd';
    }

    ++$no;
    $frm[1] .= "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$nam[$bar->karyawanid]."</td>\r\n\t  <td>".$bar->namatraining."</td>\r\n\t  <td align=center>".tanggalnormal($bar->tglmulai)."</td>\r\n\t  <td align=right>".number_format($bar->hargasatuan)."</td>\r\n\t  <td align=center>".tanggalnormal($bar->tglselesai)."</td>\r\n\t  <td align=center>\r\n             <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."','".$bar->karyawanid."');\">".$_SESSION['lang']['pdf'].'</button>';
    if ($bar->persetujuan1 == $saya && 0 == $bar->stpersetujuan1 || $bar->persetujuanhrd == $saya && 0 == $bar->sthrd) {
        $frm[1] .= "<button class=mybutton onclick=tolak('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['tolak']."</button>\r\n             <button class=mybutton onclick=setuju('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['setuju'].'</button>';
    }

    $frm[1] .= "</td>\r\n\t  </tr>";
}
$frm[1] .= "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";
$frm[1] .= "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
$hfrm[0] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body('');

?>