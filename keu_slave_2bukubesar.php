<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
$periode1 = $_POST['periode1'];
$revisi = $_POST['revisi'];
if ($periode1 < $periode) {
    $z = $periode;
    $periode = $periode1;
    $periode1 = $z;
}

// $p = explode('-', $periode);
// $p1 = explode('-', $periode1);
// $periode = $p[0].'-'.($p[1] < 9 ? '0'.$p[1] : $p[1]);
// $periode1 = $p1[0].'-'.($p1[1] < 9 ? '0'.$p1[1] : $p1[1]);

$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namagudang = strtoupper($bar->namaorganisasi);
}
$CLM = '';
$str = 'select noakundebet, sampaidebet from '.$dbname.".keu_5parameterjurnal where kodeaplikasi='CLM'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $CLM = $bar->noakundebet;
    $CLM1 = $bar->sampaidebet;
}

$where = "AND noakun!='".$CLM."' AND noakun!='".$CLM1."' ";

$lmperiode = mktime(0, 0, 0, substr($periode, 5, 2) - 1, 4, substr($periode, 0, 4));
$lmperiode = date('Y-m', $lmperiode);
if ('ID' == $_SESSION['language']) {
    $str = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun where 1 ".$where."  order by noakun";
} else {
    $str = 'select distinct noakun,namaakun1 as namaakun from '.$dbname.".keu_5akun where  1 ".$where." order by noakun";
}

$res = mysql_query($str);
$TAB = [];
while ($bar = mysql_fetch_object($res)) {
    $TAB[$bar->noakun]['noakun'] = $bar->noakun;
    $TAB[$bar->noakun]['namaakun'] = $bar->namaakun;
    $TAB[$bar->noakun]['sawal'] = 0;
    $TAB[$bar->noakun]['salak'] = 0;
}


if ('' == $gudang && '' != $pt) {
    $where1 = ' and kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    if ('' != $gudang) {
        $where1 = " and kodeorg ='".$gudang."'";
    } else {
        $where1 = '';
    }
}


$str = 'select sum(awal'.substr(str_replace('-', '', $periode), 4, 2).') as sawal,noakun from '.$dbname.".keu_saldobulanan \r\n      where periode ='".str_replace('-', '', $periode)."'   ".$where." ".$where1.' group by noakun order by noakun';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $TAB[$bar->noakun]['sawal'] = $bar->sawal;
    $TAB[$bar->noakun]['salak'] = $bar->sawal;
}
if ('' == $gudang && '' == $pt) {
    $str = 'select sum(debet) as debet,sum(kredit) as kredit,noakun from '.$dbname.".keu_jurnaldt_vw\r\n    where periode>='".$periode."' and periode<='".$periode1."' \r\n  ".$where." and revisi <= '".$revisi."' group by noakun";
} else {
    if ('' == $gudang && '' != $pt) {
        $str = 'select sum(debet) as debet,sum(kredit) as kredit, noakun from '.$dbname.".keu_jurnaldt_vw\r\n    where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi \r\n    from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)\r\n    ".$where." and revisi <= '".$revisi."' group by noakun";
    } else {
        $str = 'select sum(debet) as debet,sum(kredit) as kredit, noakun from '.$dbname.".keu_jurnaldt_vw\r\n    where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'\r\n    ".$where." and revisi <= '".$revisi."' group by noakun";
    }
}
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $TAB[$bar->noakun]['debet'] = $bar->debet;
    $TAB[$bar->noakun]['kredit'] = $bar->kredit;

    $cekakun=substr($bar->noakun,0,1);
                    
    if($cekakun=='2'){
        $TAB[$bar->noakun]['salak'] = ($TAB[$bar->noakun]['sawal'] + $bar->debet) - $bar->kredit;
    } else {
        $TAB[$bar->noakun]['salak'] = ($TAB[$bar->noakun]['sawal'] + $bar->debet) - $bar->kredit;
    }
}
$no = 0;
$sal_awal = 0;
$sal_debet = 0;
$sal_kredit = 0;
$sal_salak = 0;
foreach ($TAB as $baris => $data) {
    ++$no;
    echo "<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$periode1."','".$lmperiode."','".$pt."','".$gudang."','".$revisi."',event);\">\r\n        <td style='width:50px;'>".$no."</td>\r\n        <td style='width:80px;'>".$data['noakun']."</td>    \r\n        <td style='width:430px;'>".$data['namaakun']."</td>\r\n";    

        if($data['sawal']<0){ 
            echo  "<td align=right style='width:130px;'><strong style=color:red;>0.00</td>";
            echo  "<td align=right style='width:130px;'><strong style=color:red;>".number_format($data['sawal']*-1,2)."</td>";
            $sal_awalkredit = $sal_awalkredit+($data['sawal']*-1);
        }else{
            echo  "<td align=right style='width:130px;'>".number_format($data['sawal'],2)."</td>";
            echo  "<td align=right style='width:130px;'>0.00</td>"; 
            $sal_awaldebit = $sal_awaldebit+$data['sawal'];
        }
     echo   "<td align=right style='width:130px;'>".number_format($data['debet'],2)."</td>
            <td align=right style='width:130px;'>".number_format($data['kredit'],2)."</td>";
    if ($data['salak'] < 0) {
        echo "<td align=right style='width:130px;'><strong style=color:red;></strong>0.00</td>";
        echo "<td align=right style='width:130px;'><strong style=color:red;>".number_format($data['salak']*-1,2)."</strong></td>";
        $sal_salakkredit = $sal_salakkredit+($data['salak']*-1);
    } elseif ($data['salak'] > 0) {
        echo "<td align=right style='width:130px;'>".number_format($data['salak'],2)."</td>";
        echo "<td align=right style='width:130px;'>0.00</td>";
        $sal_salakdebit = $sal_salakdebit+$data['salak'];
    }else{
        echo "<td align=right style='width:130px;'>0.00</td>";
        echo "<td align=right style='width:130px;'>0.00</td>";
    }
    echo "</tr>";
    
    $sal_debet += $data['debet'];
    $sal_kredit += $data['kredit'];
    
}
echo "<tr class=rowcontent>\r\n        <td colspan=3 align=center>TOTAL</td>\r\n 
      <td align=right>".number_format($sal_awaldebit,2)."</td>          
      <td align=right>".number_format($sal_awalkredit,2)."</td>\r\n
      <td align=right>".number_format($sal_debet,2)."</td>\r\n
      <td align=right>".number_format($sal_kredit,2)."</td>   \r\n
      <td align=right>".number_format($sal_salakdebit,2)."</td> \r\n
      <td align=right>".number_format($sal_salakkredit,2)."</td> \r\n    </tr>";

?>