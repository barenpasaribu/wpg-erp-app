<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>PAYROLL ENTRY:</b>');
echo '<div id=EList>';
echo OPEN_THEME('Create monthly payroll: PERIOD <font color=red>'.substr($_SESSION['pyperiode'], 5, 2).'/'.substr($_SESSION['pyperiode'], 0, 4)).'</font>';
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
    echo "<font size=4 color=orange><b>Warning!!!</b></font><br>\r\n\t\t\t\t      <img src=images/onebit_36.png height=30px align=middle>\r\n\t\t\t\t\t  Ada karyawan baru yang belum terdaftar di payroll, lakukan <b>Sinkronisasi</b> data terlebih dahulu, jika memang harus.<br>\r\n\t\t\t\t\t  .";
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
        $stra = 'select id,name,type,plus,`lock` from '.$dbname.".sdm_ho_component\r\n\t\t\t\t\t       order by type desc ,name";
        $resa = mysql_query($stra);
        $arrName = [];
        $arrIdx = [];
        $arrType = [];
        $arrPlus = [];
        $arrLock = [];
        while ($bara = mysql_fetch_object($resa)) {
            array_push($arrIdx, $bara->id);
            array_push($arrName, $bara->name);
            array_push($arrType, $bara->type);
            array_push($arrPlus, $bara->plus);
            array_push($arrLock, $bara->lock);
        }
        $strd = 'select value from '.$dbname.".sdm_ho_hr_jms_porsi where id='karyawan'";
        $resd = mysql_query($strd, $conn);
        $jms = 0.02;
        while ($bard = mysql_fetch_array($resd)) {
            $jms = $bard[0] / 100;
        }
        $str = "select karyawanid,name,jmsstart,firstpayment,lastpayment,firstvol,lastvol\r\n\t\t\t\t\t      from ".$dbname.".sdm_ho_employee \r\n\t\t\t\t\t      where operator='".$_SESSION['standard']['username']."'\r\n\t\t\t\t\t\t  and (firstpayment<='".$_SESSION['pyperiode']."' or firstpayment='')\r\n\t\t\t\t\t\t  and (lastpayment>='".$_SESSION['pyperiode']."' or lastpayment='')\r\n\t\t\t\t\t\t  order by name";
        $res = mysql_query($str, $conn);
        echo "<table class=data cellspacing=1 border=0 width=700px>\r\n\t\t\t\t\t     <thead>\r\n\t\t\t\t\t     <tr class=rowheader align=center>\r\n\t\t\t\t\t\t     <td><b>No.</b></td>\r\n\t\t\t\t\t\t\t <td><b>No.Karyawan</b></td>\r\n\t\t\t\t\t\t     <td><b>Nama.Karyawan</b></td>\r\n\t\t\t\t\t\t\t <td width=350px><b>Basic Salary</b></td></tr>\r\n\t\t\t\t\t\t</thead>\r\n\t\t\t\t\t\t<tbody>";
        $n = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$n;
            echo "<tr class=rowcontent id='".$bar->karyawanid."'>\r\n\t\t\t\t\t\t<td class=firsttd>".$n."</td>\r\n\t\t\t\t\t\t<td>".$bar->karyawanid."</td>\r\n\t\t\t\t\t\t<td>".$bar->name."</td>\r\n\t\t\t\t\t\t<td align=center>";
            echo "<table class=data celspacong=1 border=0 width=290px>\r\n\t\t\t\t\t\t      <thead>\r\n\t\t\t\t\t\t\t  <tr class=rowheader align=center>\r\n\t\t\t\t\t\t\t  <td>Component</td><td>Value(Rp.)</td>\r\n\t\t\t\t\t\t\t  </tr>\r\n\t\t\t\t\t\t\t  </thead>";
            $gp = 0;
            $ttl = 0;
            for ($x = 0; $x < count($arrName); ++$x) {
                $strf = 'select `value` from '.$dbname.".sdm_5gajipokok \r\n\t\t\t\t\t\t\t      where karyawanid=".$bar->karyawanid."\r\n\t\t\t\t\t\t\t\t  and idkomponen=".$arrIdx[$x];
                $val = 0;
                $resf = mysql_query($strf, $conn);
                while ($barf = mysql_fetch_array($resf)) {
                    $val = $barf[0];
                    if ($_SESSION['pyperiode'] == $bar->firstpayment) {
                        $val = $bar->firstvol / 100 * $val;
                    }

                    if ($_SESSION['pyperiode'] == $bar->lastpayment) {
                        $val = $bar->lastvol / 100 * $val;
                    }
                }
                if (1 == $arrIdx[$x]) {
                    $gp = $val;
                }

                if (3 == $arrIdx[$x] || -1 < strpos($arrName[$x], 'jamso') || -1 < strpos($arrName[$x], 'jms')) {
                    $strd = 'select sum(value) as ttl from  '.$dbname.".sdm_ho_basicsalary where \r\n\t\t\t\t\t\t\t\t      karyawanid=".$bar->karyawanid."\r\n\t\t\t\t\t\t\t\t\t  and component in(select id from ".$dbname.'.sdm_ho_component where plus=1)';
                    $resd = mysql_query($strd, $conn);
                    $thp = 0;
                    while ($bard = mysql_fetch_object($resd)) {
                        $thp = $bard->ttl;
                    }
                    if ($bar->jmsstart <= $_SESSION['pyperiode'] && '' != $bar->jmsstart) {
                        $val = $thp * $jms;
                    } else {
                        $val = 0;
                    }
                }

                if (-1 < strpos($arrName[$x], 'ngsur')) {
                    $stre = 'select bulanan from '.$dbname.".sdm_angsuran\r\n\t\t\t\t\t\t\t\t       where karyawanid=".$bar->karyawanid.' and jenis='.$arrIdx[$x]."\r\n\t\t\t\t\t\t\t\t\t   and active=1 and `start`<='".$_SESSION['pyperiode']."'\r\n\t\t\t\t\t\t\t\t\t   and `end`>='".$_SESSION['pyperiode']."'";
                    $rese = mysql_query($stre, $conn);
                    $pot = 0;
                    while ($bere = mysql_fetch_array($rese)) {
                        $pot = $bere[0];
                    }
                    $val += $pot;
                }

                echo "<tr class=rowcontent>\r\n\t\t\t\t\t\t\t     <td>".$arrName[$x]."</td>\r\n\t\t\t\t\t\t\t\t <td align=right> ".((1 == $arrPlus[$x] ? '+' : '-'));
                if (1 == $arrLock[$x]) {
                    echo "<input type=text class=myinputtextnumber value='".number_format($val, 2, '.', ',')."' id='value".$bar->karyawanid.$x."' size=13  disabled>";
                } else {
                    echo "<input type=text class=myinputtextnumber value='".number_format($val, 2, '.', ',')."' id='value".$bar->karyawanid.$x."' size=13 onkeypress=\"return angka_doang(event);\" onblur=\"calculatePayroll(this,".(count($arrName) - 1).",'".$bar->karyawanid."');\" maxlength=14>";
                }

                echo "<input type=hidden id='component".$bar->karyawanid.$x."' value='".$arrIdx[$x]."'>";
                echo "<input type=hidden id='plus".$bar->karyawanid.$x."' value='".$arrPlus[$x]."'>";
                echo "</td>\r\n\t\t\t\t\t\t\t\t </tr>\r\n\t\t\t\t\t\t\t\t";
                if (1 == $arrPlus[$x]) {
                    $ttl = $ttl + $val;
                } else {
                    $ttl = $ttl - $val;
                }
            }
            echo " <tr class=rowcontent><td>Total(Rp.)</td><td align=right><input type=text class=myinputtextnumber id='total".$bar->karyawanid."' value='".number_format($ttl, 2, '.', ',')."' size=13  disabled></td></tr>\r\n\t\t\t\t\t\t      </tbody><tfoot></tfoot></table>\r\n\t\t\t\t\t\t\t  <div id='terbilang".$bar->karyawanid."' style='width:300px;background-color:#ffffff;'></div>\r\n\t\t\t\t\t\t     <button class=mybutton id=btn".$bar->karyawanid."  onclick=saveMonthlySalary('".$bar->karyawanid."',".(count($arrName) - 1).")>Save</button>\r\n\t\t\t\t\t\t\t ";
            echo '</td></tr>';
        }
        echo "</tbody><tfoot></tfoot>\r\n\t\t\t\t\t</table>";
    }
}

echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>