<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>BASIC SALARY SETUP:</b>');
echo '<div id=EList>';
echo OPEN_THEME('Basic Salary Form:');
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
    echo "Warning!!!<br>\r\n\t\t\t\t      <img src=images/onebit_36.png height=50px align=middle>\r\n\t\t\t\t\t  Ada karyawan baru yang belum terdaftar di payroll, lakukan <b>Sinkronisasi</b> data jika perlu.";
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
    echo "<table><thead></thead>\r\n\t\t\t\t\t     <tbody><tr><td>";
    $stra = 'select id,name from '.$dbname.".sdm_ho_component where type='basic'\r\n\t\t\t\t\t       order by name";
    $resa = mysql_query($stra);
    $arrName = [];
    $arrIdx = [];
    while ($bara = mysql_fetch_object($resa)) {
        array_push($arrIdx, $bara->id);
        array_push($arrName, $bara->name);
    }
    $str = 'select karyawanid,name from '.$dbname.".sdm_ho_employee \r\n\t\t\t\t\t      where operator='".$_SESSION['standard']['username']."'\r\n\t\t\t\t\t\t  order by name";
    $res = mysql_query($str, $conn);
    echo "<table class=data celspacong=1 border=0>\r\n\t\t\t\t\t     <thead>\r\n\t\t\t\t\t     <tr class=rowheader align=center>\r\n\t\t\t\t\t\t     <td><b>No.</b></td>\r\n\t\t\t\t\t\t\t <td><b>No.Karyawan</b></td>\r\n\t\t\t\t\t\t     <td><b>Nama.Karyawan</b></td>\r\n\t\t\t\t\t\t\t <td><b>Basic Salary</b></td></tr>\r\n\t\t\t\t\t\t</thead>\r\n\t\t\t\t\t\t<tbody>";
    $n = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$n;
        echo "<tr class=rowcontent>\r\n\t\t\t\t\t\t<td class=firsttd>".$n."</td>\r\n\t\t\t\t\t\t<td>".$bar->karyawanid."</td>\r\n\t\t\t\t\t\t<td>".$bar->name."</td>\r\n\t\t\t\t\t\t<td>";
        echo "<table class=data celspacong=1 border=0>\r\n\t\t\t\t\t\t      <thead>\r\n\t\t\t\t\t\t\t  <tr class=rowheader align=center>\r\n\t\t\t\t\t\t\t  <td>Component</td><td>Value(Rp.)</td>\r\n\t\t\t\t\t\t\t  <td>**</td>\r\n\t\t\t\t\t\t\t  </tr>\r\n\t\t\t\t\t\t\t  </thead>";
        for ($x = 0; $x < count($arrName); ++$x) {
            $strf = 'select `value` from '.$dbname.".sdm_ho_basicsalary \r\n\t\t\t\t\t\t\t      where karyawanid=".$bar->karyawanid."\r\n\t\t\t\t\t\t\t\t  and component=".$arrIdx[$x];
            $val = 0;
            $resf = mysql_query($strf, $conn);
            while ($barf = mysql_fetch_array($resf)) {
                $val = $barf[0];
            }
            echo "<tr class=rowcontent>\r\n\t\t\t\t\t\t\t     <td>".$arrName[$x]."</td>\r\n\t\t\t\t\t\t\t\t <td>\r\n\t\t\t\t\t\t\t\t \t<input type=text class=myinputtextnumber value=".number_format($val, 2, '.', ',').' id=value'.$n.$x." size=13 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);\" maxlength=14>\r\n\t\t\t\t\t\t\t\t </td>\r\n\t\t\t\t\t\t\t\t <td><img src=images/save.png height=13px class=dellicon title='Save' onclick=saveBSalary('".$bar->karyawanid."','".$arrIdx[$x];
            echo "','value".$n.$x."')>\r\n\t\t\t\t\t\t\t\t </td>\r\n\t\t\t\t\t\t\t\t </tr>\r\n\t\t\t\t\t\t\t\t";
        }
        echo '<tfoot></tfoot></table></td></tr>';
    }
    echo "</tbody><tfoot></tfoot>\r\n\t\t\t\t\t</table></td><td valign=top><fieldset style='width:300px'>\r\n \t\t\t\t <legend>\r\n\t\t\t\t <img src=images/info.png align=left height=35px valign=asmiddle>\r\n\t\t\t\t </legend><p>\r\n\t\t\t\t Setiap karyawan harus di set basic salary-nya. Basic salary ini akan otomatis menjadi default\r\n\t\t\t\t pada saat pembuatan rekap gaji bulanan. Untuk menambahkan komponen lain sebagai basic salary, gunakan menu\r\n\t\t\t\t <b>Payroll Component</b>, dan jadikan tipe komponen-nya menjadi <b>Penambah</b>.\r\n\t\t\t\t </p><p>Basic Salary di-Update hanya pada saat adanya perubahan gaji(kenaikan/penurunan).\r\n\t\t\t\t     </p>\r\n\t\t\t\t\t </fieldset></td></tr></tbody><tfoot></tfoot></table>";
}

echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>