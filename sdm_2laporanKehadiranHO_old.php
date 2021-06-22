<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lokasitugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$arr = '##tanggal1##tanggal2##karyawanid';
$arr1 = '##tahun';
$arr2 = '##tanggal21##tanggal22##karyawanid2';
echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script language=javascript src='js/sdm_2rekapabsenho.js'></script>\r\n\r\n<link rel=stylesheet type='text/css' href='style/zTable.css'>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n\r\n";
$daritahun = 9999;
$sampaitahun = 0;
$optTahun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOpt = 'select darijam, sampaijam from '.$dbname.".sdm_ijin where stpersetujuan1 = '1' and stpersetujuanhrd = '1'";
$qOpt = mysql_query($sOpt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    if ('0000' != substr($rOpt['darijam'], 0, 4) && substr($rOpt['darijam'], 0, 4) < $daritahun) {
        $daritahun = substr($rOpt['darijam'], 0, 4);
    }

    if ('0000' != substr($rOpt['sampaijam'], 0, 4) && $sampaitahun < substr($rOpt['sampaijam'], 0, 4)) {
        $sampaitahun = substr($rOpt['sampaijam'], 0, 4);
    }
}
$sOpt = 'select tanggalperjalanan, tanggalkembali from '.$dbname.".sdm_pjdinasht where statuspersetujuan='1' and statushrd='1'";
$qOpt = mysql_query($sOpt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    if ('0000' != substr($rOpt['tanggalperjalanan'], 0, 4) && substr($rOpt['tanggalperjalanan'], 0, 4) < $daritahun) {
        $daritahun = substr($rOpt['tanggalperjalanan'], 0, 4);
    }

    if ('0000' != substr($rOpt['tanggalkembali'], 0, 4) && $sampaitahun < substr($rOpt['tanggalkembali'], 0, 4)) {
        $sampaitahun = substr($rOpt['tanggalkembali'], 0, 4);
    }
}
$sOpt = 'select tanggal from '.$dbname.".sdm_absensidt where kodeorg like '%HO'";
$qOpt = mysql_query($sOpt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    if ('0000' != substr($rOpt['tanggal'], 0, 4) && substr($rOpt['tanggal'], 0, 4) < $daritahun) {
        $daritahun = substr($rOpt['tanggal'], 0, 4);
    }

    if ('0000' != substr($rOpt['tanggal'], 0, 4) && $sampaitahun < substr($rOpt['tanggal'], 0, 4)) {
        $sampaitahun = substr($rOpt['tanggal'], 0, 4);
    }
}
for ($i = $daritahun; $i <= $sampaitahun; ++$i) {
    $optTahun .= '<option value='.$i.'>'.$i.'</option>';
}
$skaryawan = 'select a.karyawanid, b.namajabatan, a.namakaryawan, c.nama from '.$dbname.".datakaryawan a \r\n    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan \r\n    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode \r\n    where a.lokasitugas like '%HO' \r\n    order by namakaryawan asc";
$rkaryawan = fetchData($skaryawan);
$optkaryawan = "<option value=''>".$_SESSION['lang']['all'].'</option>';
foreach ($rkaryawan as $row => $kar) {
    $optkaryawan .= "<option value='".$kar['karyawanid']."'>".$kar['namakaryawan'].' - '.$kar['namajabatan'].'</option>';
}
$frm[0] .= '<div style=margin-bottom: 30px;>';
$frm[0] .= "<fieldset>\r\n<legend><b>".$_SESSION['lang']['rkpAbsen'].' HO</b></legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['tanggalmulai']."</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1\" name=\"tanggal1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['tanggalsampai']."</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2\" name=\"tanggal2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td><select id=karyawanid name=karyawanid style='width:300px;'>".$optkaryawan."</select></td>\r\n    </tr>\r\n    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n    <tr>\r\n        <td colspan=\"2\">\r\n            <button onclick=\"zPreview('sdm_slave_2rekapabsenho','".$arr."','container')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n            <button onclick=\"zPdf('sdm_slave_2rekapabsenho','".$arr."','container')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">PDF</button>\r\n            <button onclick=\"zExcel(event,'sdm_slave_2rekapabsenho.php','".$arr."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>\r\n            <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>\r\n        </td>\r\n    </tr>\r\n</table>\r\n</fieldset>";
$frm[0] .= "</div>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='container' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$frm[1] .= '<div style=margin-bottom: 30px;>';
$frm[1] .= "<fieldset>\r\n<legend><b>".$_SESSION['lang']['rkpAbsen'].' HO Annually</b></legend>';
$frm[1] .= "<table cellspacing=1 border=0>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['tahun']."</label></td>\r\n        <td><select id=tahun name=tahun style=width:100px>".$optTahun."</select></td>\r\n    </tr>\r\n    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n    <tr>\r\n        <td colspan=\"2\">\r\n            <button onclick=\"zPreview('sdm_slave_2rekapabsenho1','".$arr1."','container1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n            <button onclick=\"zPdf('sdm_slave_2rekapabsenho1','".$arr1."','container1')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">PDF</button>\r\n            <button onclick=\"zExcel(event,'sdm_slave_2rekapabsenho1.php','".$arr1."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>\r\n            <button onclick=\"Clear2()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>\r\n        </td>\r\n    </tr>\r\n</table>\r\n</fieldset>";
$frm[1] .= "</div>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='container1' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$frm[2] .= '<div style=margin-bottom: 30px;>';
$frm[2] .= "<fieldset>\r\n<legend><b>".$_SESSION['lang']['laporanLembur'].' HO</b></legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['tanggalmulai']."</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal21\" name=\"tanggal21\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['tanggalsampai']."</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal22\" name=\"tanggal22\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td><select id=karyawanid2 name=karyawanid2 style='width:300px;'>".$optkaryawan."</select></td>\r\n    </tr>\r\n    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n    <tr>\r\n        <td colspan=\"2\"> \r\n            <button onclick=\"zPreview('sdm_slave_2rekapabsenho2','".$arr2."','container2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n            <!--<button onclick=\"zPdf('sdm_slave_2rekapabsenho2','".$arr2."','container2')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">PDF</button>-->\r\n            <button onclick=\"zExcel(event,'sdm_slave_2rekapabsenho2.php','".$arr2."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>\r\n            <button onclick=\"Clear3()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>\r\n        </td>\r\n    </tr>\r\n</table>\r\n</fieldset>";
$frm[2] .= "</div>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='container2' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$hfrm[0] = $_SESSION['lang']['rkpAbsen'].' HO';
$hfrm[1] = $_SESSION['lang']['rkpAbsen'].' HO Annually';
$hfrm[2] = $_SESSION['lang']['laporanLembur'].' HO';
drawTab('FRM', $hfrm, $frm, 200, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>