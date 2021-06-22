<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$periode = $_SESSION['thrperiode'];
$tglthr = $_SESSION['tglthr'];
$tgltmp = tanggalsystem($tglthr);
$ththr = substr($tglthr, 6, 4);
$blthr = substr($tglthr, 3, 2);
$tgthr = substr($tglthr, 0, 2);
OPEN_BOX('', '<b>THR ENTRY:</b>');
echo '<div id=EList>';
echo OPEN_THEME('Create THR: PERIOD <font color=red>'.substr($periode, 5, 2).'/'.substr($periode, 0, 4)).'</font>';
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
    echo "<font size=4 color=orange><b>Warning!!!</b></font><br>\r\n\t\t\t\t      <img src=images/onebit_36.png height=30px align=middle>\r\n\t\t\t\t\t  Ada karyawan baru yang belum terdaftar di payroll.<br>\r\n\t\t\t\t\t  ";
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
        echo "<table class=sortable cellspacing=1 border=0>\r\n\t\t\t\t\t     <thead>\r\n\t\t\t\t\t     <tr class=rowheader align=center>\r\n\t\t\t\t\t\t     <td><b>No.</b></td>\r\n\t\t\t\t\t\t\t <td><b>No.Karyawan</b></td>\r\n\t\t\t\t\t\t     <td><b>Nama.Karyawan</b></td>\r\n\t\t\t\t\t\t\t <td><b>TMK</b></td>\r\n\t\t\t\t\t\t\t <td><b>Tgl.THR</b></td>\r\n\t\t\t\t\t\t\t <td><b>MasaKerja<br>(Bln)</b></td>\r\n\t\t\t\t\t\t\t <td><b>THR</b></td>\r\n\t\t\t\t\t\t\t <td>Terbilang</td>\r\n\t\t\t\t\t\t  </tr>\r\n\t\t\t\t\t\t</thead>\r\n\t\t\t\t\t\t<tbody>";
        $str = "select karyawanid,name,firstpayment,lastpayment,startdate,\r\n\t\t\t\t\t      ROUND(DATEDIFF(".$tgltmp.",startdate)/30,0) as masakerja\r\n\t\t\t\t\t      from ".$dbname.".sdm_ho_employee \r\n\t\t\t\t\t      where operator='".$_SESSION['standard']['username']."'\r\n\t\t\t\t\t\t  and (firstpayment<='".$periode."' or firstpayment='')\r\n\t\t\t\t\t\t  and (lastpayment>='".$periode."' or lastpayment='')\r\n\t\t\t\t\t\t  order by masakerja";
        $res = mysql_query($str);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $tmk = tanggalnormal($bar->startdate);
            if ($bar->masakerja < 3) {
                $porsi = 0;
            } else {
                if ($bar->masakerja < 12) {
                    $porsi = $bar->masakerja / 12;
                } else {
                    $porsi = 1;
                }
            }

            $str1 = 'select sum(value) as gaji from '.$dbname.".sdm_ho_basicsalary\r\n\t\t\t\t\t\t       where karyawanid=".$bar->karyawanid.' and component in(select component from '.$dbname.'.sdm_ho_thr_setup)';
            $gaji = 0;
            $res1 = mysql_query($str1);
            while ($bar1 = mysql_fetch_object($res1)) {
                $gaji = $bar1->gaji;
            }
            $color = 'white';
            $thr = $gaji * $porsi;
            $str2 = 'select value from '.$dbname.".sdm_ho_detailmonthly where periode='".$periode."' and karyawanid=".$bar->karyawanid." and type='thr'";
            $res2 = mysql_query($str2);
            while ($bar2 = mysql_fetch_object($res2)) {
                $thr = $bar2->value;
                $color = 'gray';
            }
            echo "<tr class=rowcontent>\r\n\t\t\t\t\t\t     <td>".$no."</td>\r\n\t\t\t\t\t\t\t <td id=userid".$no.'>'.$bar->karyawanid."</td>\r\n\t\t\t\t\t\t     <td>".$bar->name."</td>\r\n\t\t\t\t\t\t\t <td>".tanggalnormal($bar->startdate)."</td>\r\n\t\t\t\t\t\t\t <td>".$tglthr."</td>\r\n\t\t\t\t\t\t\t <td align=right>".$bar->masakerja."</td>\r\n\t\t\t\t\t\t\t <td><input style='background-color:".$color.";' type=text id=thr".$no.' value='.number_format($thr, 2, '.', ',')." class=myinputtextnumber onkeypress=\"return angka_doang(event);\"  onblur=\"change_number(this);loadTerbilang(this,'".$no."',this.value)\"></td>\r\n\t\t\t\t\t\t     <td id=terbilang".$no."></td>\r\n\t\t\t\t\t\t  </tr>";
        }
        echo "</tbody><tfoot></tfoot>\r\n\t\t\t\t\t</table>\r\n\t\t\t\t\t<center><button onclick=saveTHR('".$no."')>Save</button></center>\r\n\t\t\t\t\t";
    }
}

echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>