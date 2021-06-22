<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$strd = 'select uname from '.$dbname.'.sdm_ho_payroll_user';
$optx = "<option value=''>Default</option>";
$red = mysql_query($strd);
while ($badx = mysql_fetch_object($red)) {
    $optx .= "<option value='".$badx->uname."'>".$badx->uname.'</option>';
}
$strd = 'select `type` from '.$dbname.".sdm_ho_payroll_user\r\n       where uname='".$_SESSION['standard']['username']."'";
$status = 'operator';
$red = mysql_query($strd);
while ($badx1 = mysql_fetch_object($red)) {
    $status = $badx1->type;
}
if ('admin' == $status) {
    $add = ' ';
} else {
    $add = " style='display:none;' ";
}

echo "<div class=drag id=pdf style=\" width:750px; display:none;position:absolute;background-image:url('images/title_bg.gif');\">\r\n     </div>";
$opt = '';
for ($x = 0; $x < 24; ++$x) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $opt .= "<option value='".date('Y-m', $dt)."'>".date('m-Y', $dt).'</option>';
}
$opt1 = "<option value='regular'>Regular</option>";
$opt1 .= "<option value='thr'>THR</option>";
$opt1 .= "<option value='jaspro'>Jasa Produksi</option>";
OPEN_BOX('', '<b>PAYROLL PRINT:</b>');
echo '<div id=EList>';
echo OPEN_THEME('Payroll Print Preview: PERIOD <select id=periode>'.$opt.'</option></select> Tipe:<select id=tipe>'.$opt1.'</select><span  '.$add.'> Operator <select id=user>'.$optx."</select></span>\r\n\t\t <button class=mybutton onclick=pyPreview()>Preview</button> &nbsp\r\n\t\t <img src=images/excel.jpg onclick=pyPreviewExcel() style='cursor:pointer;' title='Convert to Excel'> &nbsp \r\n\t\t <img src='images/printer.png' title='Print Slyp/PDF' style='cursor:pointer' onclick=pPDF(event)> \r\n\t\t <!--img src='images/bca.jpg'  onclick=printBank(event,'BCA')  title='Convert to Ms.Excel for BCA Transfer' style='cursor:pointer'-->");
$prestr = 'select distinct karyawanid from '.$dbname.'.sdm_ho_employee order by karyawanid';
$preres = mysql_query($prestr, $conn);
$arrid = '';
while ($prebar = mysql_fetch_object($preres)) {
    if ('' == $arrid) {
        $arrid .= $prebar->karyawanid;
    } else {
        $arrid .= ','.$prebar->karyawanid;
    }
}
if ('' == $arrid) {
    $arrid = "'null'";
}

$str = 'select karyawanid,namakaryawan,statuspajak,tanggalkeluar,npwp from '.$dbname.".datakaryawan\r\n\t\t       where karyawanid not in(".$arrid.') and alokasi=1';
$newempl = mysql_num_rows(mysql_query($str, $conn));
if (0 < $newempl) {
    echo "<font size=4 color=orange><b>Warning!!!</b></font><br>\r\n\t\t\t\t      <img src=images/onebit_36.png height=30px align=middle>\r\n\t\t\t\t\t  Ada karyawan baru yang belum terdaftar d payroll.<br>\r\n\t\t\t\t\t  .";
}

$str = 'select count(*) as d from '.$dbname.".sdm_ho_employee\r\n\t\t\t      where operator is null or operator=''";
$res = mysql_query($str, $conn);
$count = 0;
while ($bar = mysql_fetch_object($res)) {
    $count = $bar->d;
}
if (0 < $count) {
    echo "Forbidden!!!<br>\r\n\t\t\t\t      <img src=images/stop1.png height=100px align=middle>\r\n\t\t\t\t\t  Masih ada karyawan yang belum di <b>set operator</b> payroll-nya.";
} else {
    $str = 'select count(*) as d from '.$dbname.".sdm_ho_employee\r\n\t\t\t\t      where bank is null or bankaccount='' or length(firstpayment)<>7";
    $res = mysql_query($str, $conn);
    $count = 0;
    while ($bar = mysql_fetch_object($res)) {
        $count = $bar->d;
    }
    if (0 < $count) {
        echo "Forbidden!!!<br>\r\n\t\t\t\t\t      <img src=images/stop1.png height=100px align=middle>\r\n\t\t\t\t\t\t  Account bank karyawan atau periode gaji pertama belum si set, lakukan <b>Setup Employee's Payroll Data</b>.";
    } else {
        echo "<div id=output style='height:450px;width:1050px;overflow:scroll;'>\r\n\t\t\t\t\t</div>";
    }
}

echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>