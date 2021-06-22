<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/sdm_pengobatan.js'></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
OPEN_BOX('', $_SESSION['lang']['adm_peng']);
$optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmRwt = makeOption($dbname, 'sdm_5jenisbiayapengobatan', 'kode,nama');
$optthn = "<option value=''>".$_SESSION['lang']['all'].'</option>';
for ($x = -1; $x < 10; ++$x) {
    if (0 == $x) {
        $qwe = 'selected = "selected"';
    } else {
        $qwe = '';
    }

    $mk = mktime(0, 0, 0, 1, 15, date('Y') - $x);
    $optthn .= "<option value='".date('Y', $mk)."' ".$qwe.'>'.date('Y', $mk).'</option>';
}
////$optkodeorg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
////$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' \r\n    and tipe in ('KEBUN', 'PABRIK', 'KANWIL', 'TRAKSI','HOLDING')  \r\n    order by namaorganisasi asc";
//$sOrg= "SELECT * FROM organisasi WHERE induk = '".$_SESSION['org']['induk']."'";
//$qOrg = mysql_query($sOrg);
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    if (substr($_SESSION['empl']['lokasitugas'], 0, 4) == $rOrg['kodeorganisasi']) {
//        $qwe = 'selected = "selected"';
//    } else {
//        $qwe = '';
//    }
//
//    $optkodeorg .= '<option value='.$rOrg['kodeorganisasi'].' '.$qwe.'>'.$rOrg['namaorganisasi'].'</option>';
//}

$optkodeorg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$str = 'select a.*, b.*from '.$dbname.".sdm_pengobatanht a left join\r\n      ".$dbname.".sdm_5rs b on a.rs=b.id \r\n          order by b.namars";
$res1 = mysql_query($str);
$optrs = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res1)) {
    if ('' == $qwe[$bar->namars]) {
        $optrs .= "<option value='".$bar->namars."'>".$bar->namars.' ['.$bar->kota.']</option>';
    }

    $qwe[$bar->namars] = $bar->namars;
}
$optKaryawan = "<option value=''>Seluruhnya</option>";
$str = "Select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from $dbname.sdm_pengobatanht a ".
       "left join $dbname.datakaryawan b on a.karyawanid=b.karyawanid order by namakaryawan";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optKaryawan .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'['.$bar->lokasitugas.']</option>';
}
if ('HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
    $lokasi = substr($_SESSION['empl']['lokasitugas'], 0, 4);
} else {
    $lokasi = '';
}

$frm[0] .= "<fieldset>\r\n    <legend>".$_SESSION['lang']['list']."</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon onchange=loadPengobatanPrint()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim() class=resicon><br>\r\n    ".$_SESSION['lang']['kodeorganisasi'].":\r\n    <select id=optkodeorg onchange=loadPengobatanPrint()>".$optkodeorg."</select>\r\n    ".$_SESSION['lang']['rumahsakit'].":\r\n    <select id=optrs onchange=loadPengobatanPrint()>".$optrs."</select>\r\n    <iframe id=frmku frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td width=50></td>\r\n        <td>No</td>\r\n        <td width=100>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td width=50>".$_SESSION['lang']['periode']."</td>\r\n        <td width=30>".$_SESSION['lang']['tanggal']."</td>\r\n        <td width=200>".$_SESSION['lang']['lokasitugas']."</td>\r\n        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td width=200>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>\r\n        <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td>".$_SESSION['lang']['dibayar']."</td>\r\n        <td width=90>".$_SESSION['lang']['perusahaan']."</td>\r\n        <td width=90>".$_SESSION['lang']['karyawan']."</td>\r\n        <td width=90>Jamsostek</td>      \r\n        <td>".$_SESSION['lang']['diagnosa']."</td>\r\n        <td>".$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    </thead>\r\n    \r\n    <tbody id='container'>";
$frm[0] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset> \t \r\n    ";
$str1 = 'select a.diagnosa, count(*) as kali,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".sdm_5diagnosa d\r\n\t  on a.diagnosa=d.id \r\n          left join ".$dbname.".datakaryawan e\r\n\t  on a.karyawanid=e.karyawanid\r\n\t  where a.periode like '".date('Y')."%'\r\n\t  and e.lokasitugas='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n        group by a.diagnosa order by kali desc\r\n    ";
$res1 = mysql_query($str1);
$frm[1] .= "<fieldset>\r\n    <legend>Ranking ".$_SESSION['lang']['diagnosa']."</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon1 onchange=loadPengobatanPrint1()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim1() class=resicon><br>\r\n    ".$_SESSION['lang']['kodeorganisasi'].":\r\n    <select id=optkodeorg1 onchange=loadPengobatanPrint1()>".$optkodeorg."</select>\r\n    <iframe id=frmku1 frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>Rank</td>\r\n        <td>Diagnose</td>\r\n        <td>Number of visit</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody id='container1'>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    $frm[1] .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar1->ketdiag."</td>\r\n            <td align=right>".$bar1->kali."</td>\r\n        </tr>";
}
$frm[1] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset> \t \r\n    ";
$str2 = "select a.karyawanid, sum(totalklaim) as klaim,d.namakaryawan,d.lokasitugas,d.kodegolongan,\r\n    COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',d.tanggallahir)/365.25,1),0) as umur,kodebiaya\r\n    from ".$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".datakaryawan d\r\n\t  on a.karyawanid=d.karyawanid \r\n          left join ".$dbname.".datakaryawan e\r\n\t  on a.karyawanid=e.karyawanid\r\n\t  where a.periode like '".date('Y')."%'\r\n\t  and e.lokasitugas='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n        group by a.karyawanid,kodebiaya order by klaim desc\r\n    ";
$res2 = mysql_query($str2);
while ($bar2 = mysql_fetch_object($res2)) {
    $kdBiaya[$bar2->kodebiaya] = $bar2->kodebiaya;
    $idKary[$bar2->karyawanid] = $bar2->karyawanid;
    $jmlhRp[$bar2->karyawanid.$bar2->kodebiaya] = $bar2->klaim;
    $umurKary[$bar2->karyawanid] = $bar2->umur;
    $nmKary[$bar2->karyawanid] = $bar2->namakaryawan;
    $kdGol[$bar2->karyawanid] = $bar2->kodegolongan;
    $lksiKary[$bar2->karyawanid] = $bar2->lokasitugas;
}
$frm[2] .= "<fieldset>\r\n    <legend>Ranking  ".$_SESSION['lang']['biaya'].'/'.$_SESSION['lang']['karyawan']."</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon2 onchange=loadPengobatanPrint2()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim2() class=resicon><br>\r\n    ".$_SESSION['lang']['kodeorganisasi'].":\r\n    <select id=optkodeorg2 onchange=loadPengobatanPrint2()>".$optkodeorg."</select>\r\n    <iframe id=frmku2 frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\" id=container2>\r\n    \r\n\r\n   <table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>Rank</td>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n        <td>".$_SESSION['lang']['umur']."</td>\r\n        <td>".$_SESSION['lang']['lokasitugas'].'</td>';
foreach ($kdBiaya as $lsBy) {
    $frm[2] .= '<td>'.$optNmRwt[$lsBy].'</td>';
}
$frm[2] .= '<td>'.$_SESSION['lang']['total']."</td>\r\n        <td>*</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
$no = 0;
foreach ($idKary as $lstKary) {
    ++$no;
    $frm[2] .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$nmKary[$lstKary]."</td>\r\n            <td>".$kdGol[$lstKary]."</td>\r\n            <td>".$umurKary[$lstKary]."(Yrs)</td>\r\n            <td>".$lksiKary[$lstKary].'(Yrs)</td>';
    foreach ($kdBiaya as $lsBy) {
        $frm[2] .= '<td align=right>'.number_format($jmlhRp[$lstKary.$lsBy]).'</td>';
        $total[$lstKary] += $jmlhRp[$lstKary.$lsBy];
        $totPerBy[$lsBy] += $jmlhRp[$lstKary.$lsBy];
    }
    $frm[2] .= '<td align=right>'.number_format($total[$lstKary])."</td>\r\n               <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPerorang('".$lstKary."',event)></td>\r\n            </tr>";
}
$frm[2] .= "<tr class=rowcontent>\r\n              <td></td>\r\n               <td colspan=3 align=right>".$_SESSION['lang']['total'].'</td>';
foreach ($kdBiaya as $lsBy) {
    $frm[2] .= '<td align=right>'.number_format($totPerBy[$lsBy]).'</td>';
    $totBy += $totPerBy[$lsBy];
}
$frm[2] .= '<td>'.number_format($totBy)."</td>\r\n                <td></td></tr>";
$frm[2] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset> \t \r\n    ";
$str3 = 'select a.diagnosa, sum(totalklaim) as klaim,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".sdm_5diagnosa d\r\n\t  on a.diagnosa=d.id \r\n          left join ".$dbname.".datakaryawan e\r\n\t  on a.karyawanid=e.karyawanid\r\n\t  where a.periode like '".date('Y')."%'\r\n\t  and e.lokasitugas='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n        group by a.diagnosa order by klaim desc\r\n    ";
$res3 = mysql_query($str3);
$frm[3] .= "<fieldset>\r\n    <legend>Ranking ".$_SESSION['lang']['biaya'].'/'.$_SESSION['lang']['diagnosa']."</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon3 onchange=loadPengobatanPrint3()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim3() class=resicon><br>\r\n    ".$_SESSION['lang']['kodeorganisasi'].":\r\n    <select id=optkodeorg3 onchange=loadPengobatanPrint3()>".$optkodeorg."</select>\r\n    <iframe id=frmku3 frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>Rank</td>\r\n        <td>Diagnose</td>\r\n        <td>".$_SESSION['lang']['jumlah']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody id='container3'>";
$no = 0;
while ($bar3 = mysql_fetch_object($res3)) {
    ++$no;
    $frm[3] .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar3->ketdiag."</td>\r\n            <td align=right>".number_format($bar3->klaim)."</td>\r\n        </tr>";
}
$frm[3] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset> \t \r\n    ";
$frm[4] .= "<fieldset>\r\n    <legend>Trend ".$_SESSION['lang']['biaya']."</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon4 onchange=loadPengobatanPrint4()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim4() class=resicon><br>\r\n    ".$_SESSION['lang']['kodeorganisasi'].":\r\n    <select id=optkodeorg4 onchange=loadPengobatanPrint4()>".$optkodeorg."</select>\r\n    <iframe id=frmku4 frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>No</td>\r\n        <td>Period</td>\r\n        <td>".$_SESSION['lang']['jumlah']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody id='container4'>";
$frm[4] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset>";
$frm[5] .= "<fieldset>\r\n    <legend>Trend ".$_SESSION['lang']['biaya']."</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon5 onchange=loadPengobatanPrint5()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim5() class=resicon><br>\r\n    ".$_SESSION['lang']['nama'].":\r\n    <select id=karyawanid onchange=loadPengobatanPrint5()>".$optKaryawan."</select>\r\n    <iframe id=frmku5 frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>No</td>\r\n        <td>Period</td>\r\n        <td>".$_SESSION['lang']['biayars']."</td>\r\n        <td>".$_SESSION['lang']['biayadr']."</td>\r\n        <td>".$_SESSION['lang']['biayalab']."</td>\r\n        <td>".$_SESSION['lang']['biayaobat']."</td>\r\n        <td>".$_SESSION['lang']['biayapendaftaran']."</td>\r\n        <td>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td>".$_SESSION['lang']['dibayar']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody id='container5'>";
$frm[5] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset>";
$frm[6] .= "<fieldset>\r\n    <legend>Per Jenis Perawatan</legend>\r\n    ".$_SESSION['lang']['thnplafon'].":\r\n    <select id=optplafon6 onchange=loadPengobatanPrint6()>".$optthn."</select>\r\n    <img src=images/excel.jpg onclick=printKlaim6() class=resicon><br>\r\n    ".$_SESSION['lang']['kodeorganisasi'].":\r\n    <select id=optkodeorg6 onchange=loadPengobatanPrint6()>".$optkodeorg."</select>\r\n    <iframe id=frmku6 frameborder=0 style='width:0px;height:0px;'></iframe>\r\n    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>No</td>\r\n        <td>".$_SESSION['lang']['kodeorg']."</td>\r\n        <td>".$_SESSION['lang']['tahun']."</td>            \r\n        <td>Treatment Type</td>\r\n        <td  align=center>Jan</td>\r\n        <td  align=center>Feb</td>\r\n        <td  align=center>Mar</td>\r\n        <td  align=center>Apr</td>\r\n        <td  align=center>Mei</td>\r\n        <td  align=center>Jun</td>\r\n        <td  align=center>Jul</td>\r\n        <td  align=center>Aug</td>\r\n        <td  align=center>Sep</td>\r\n        <td  align=center>Oct</td>\r\n        <td  align=center>Nov</td>\r\n        <td  align=center>Dec</td>\r\n        <td>".$_SESSION['lang']['total']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody id='container6'>";
$frm[6] .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset>";
$hfrm[0] = $_SESSION['lang']['detail'];
$hfrm[1] = 'Rank '.$_SESSION['lang']['diagnosa'];
$hfrm[2] = 'Rank '.$_SESSION['lang']['biaya'].'/'.$_SESSION['lang']['karyawan'];
$hfrm[3] = 'Rank '.$_SESSION['lang']['biaya'].'/'.$_SESSION['lang']['diagnosa'];
$hfrm[4] = 'Monthly Trend';
$hfrm[5] = 'By cost type';
$hfrm[6] = 'By Treatment type';
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body();

?>