<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$tanggalmulai = $_POST['tanggalmulai'];
$tanggalsampai = $_POST['tanggalsampai'];
$noakun = $_POST['noakun'];
$kodeakun = substr($_POST['noakun'],0,3);
$kodeo = $_POST['kodeorg'];
$pt = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
if ('' === $tanggalmulai) {
    echo 'warning: silakan mengisi tanggal';
    exit();
}

if ('' === $tanggalsampai) {
    echo 'warning: silakan mengisi tanggal';
    exit();
}

if ('' === $noakun) {
    echo 'warning: silakan memilih no akun';
    exit();
}

$qwe = explode('-', $tanggalmulai);
$tanggalmulai = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$qwe = explode('-', $tanggalsampai);
$tanggalsampai = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];


$str = " SELECT a.*, sum(awal) as sawal , namaakun, if(namasupplier is null ,namacustomer,namasupplier) as namasupplier from (SELECT if(noakun like '2%', sum(kredit-debet), sum(debet-kredit)) as awal,
noakun, if(kodesupplier='',kodecustomer,kodesupplier) as kodesupplier,kodeorg from keu_jurnaldt_vw x
 where tanggal<'".$tanggalmulai."'  and noakun = '".$noakun."' 
  AND kodeorg like '".$kodeo."%' group by x.kodesupplier, kodecustomer
  union
  SELECT jumlah AS awal, noakun, supplierid AS kodesupplier, kodeorg FROM keu_saldoawalhutang 
WHERE kodeorg LIKE '".$kodeo."%' AND noakun = '".$noakun."') AS a inner join keu_5akun b on a.noakun = b.noakun 
left join log_5supplier c on a.kodesupplier = c.supplierid 
left join pmn_4customer d on a.kodesupplier = d.kodecustomer 
group by a.kodesupplier ";

$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->kodesupplier=='') {
        $sawal['lain'] = $bar->sawal;
        $supplier['lain'] = 'lain';
    } else {
        $sawal[$bar->kodesupplier] = $bar->sawal;
        $supplier[$bar->kodesupplier] = $bar->namasupplier;
    }

    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}

$str = "select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,
        if(a.kodesupplier='',a.kodecustomer,kodesupplier) as kodesupplier,
        if(namasupplier is null ,namacustomer,namasupplier) as namasupplier,a.kodeorg 
        from ".$dbname.".keu_jurnaldt_vw a\r\n      inner join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n     
    left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid\r\n
    left join pmn_4customer d on a.kodecustomer = d.kodecustomer 
    where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."' \r\n   
    and a.noakun = '".$noakun."'\r\n   AND a.kodeorg like '".$kodeo."%' group by a.kodesupplier, a.kodecustomer ";
//saveLog($str);
$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->kodesupplier=='') {
        $debet['lain'] = $bar->debet;
        $kredit['lain'] = $bar->kredit;
        $supplier['lain'] = 'lain';
    } else {
        $debet[$bar->kodesupplier] = $bar->debet;
        $kredit[$bar->kodesupplier] = $bar->kredit;
        $supplier[$bar->kodesupplier] = $bar->namasupplier;
    }

    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}



/*

if($kodeakun!='131'){
$str = " SELECT a.*, sum(awal) as sawal , namaakun, namasupplier from (SELECT if(noakun like '2%', sum(kredit-debet), sum(debet-kredit)) as awal,
noakun, kodesupplier,kodeorg from keu_jurnaldt_vw 
 where tanggal<'".$tanggalmulai."'  and noakun = '".$noakun."' 
  AND kodeorg like '".$kodeo."%' group by kodesupplier
  union
  SELECT jumlah AS awal, noakun, supplierid AS kodesupplier, kodeorg FROM keu_saldoawalhutang 
WHERE kodeorg LIKE '".$kodeo."%' AND noakun = '".$noakun."') AS a inner join keu_5akun b on a.noakun = b.noakun 
left join log_5supplier c on a.kodesupplier = c.supplierid group by a.kodesupplier ";

$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->kodesupplier=='') {
        $sawal['lain'] = $bar->sawal;
        $supplier['lain'] = 'lain';
    } else {
        $sawal[$bar->kodesupplier] = $bar->sawal;
        $supplier[$bar->kodesupplier] = $bar->namasupplier;
    }

    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}
}

if($kodeakun=='131'){
$str = " SELECT a.*, sum(awal) as sawal , namaakun, namacustomer from (SELECT if(noakun like '2%', sum(kredit-debet), sum(debet-kredit)) as awal,
noakun, kodecustomer,kodeorg from keu_jurnaldt_vw 
 where tanggal<'".$tanggalmulai."'  and noakun = '".$noakun."' 
  AND kodeorg like '".$kodeo."%' group by kodecustomer
  union
  SELECT jumlah AS awal, noakun, supplierid AS kodecustomer, kodeorg FROM keu_saldoawalhutang 
WHERE kodeorg LIKE '".$kodeo."%' AND noakun = '".$noakun."') AS a inner join keu_5akun b on a.noakun = b.noakun 
left join pmn_4customer c on a.kodecustomer = c.kodecustomer group by a.kodecustomer ";

$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->kodecustomer=='') {
        $sawal['lain'] = $bar->sawal;
        $supplier['lain'] = 'lain';
    } else {
        $sawal[$bar->kodecustomer] = $bar->sawal;
        $supplier[$bar->kodecustomer] = $bar->namacustomer;
    }

    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}

}

$str = 'select if(a.noakun like \'2%\', sum(a.kredit-a.debet), sum(a.debet-a.kredit)) as sawal,a.noakun, b.namaakun,a.nik,c.namakaryawan,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      inner join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n      inner join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     \r\n      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null   AND a.kodeorg like '".$kodeo."%' group by c.nik";
$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $sawal[$bar->nik] = $bar->sawal;
    $supplier[$bar->nik] = $bar->namakaryawan;
    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}



if($kodeakun!='131'){
$str = 'select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.kodesupplier,c.namasupplier,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      inner join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n     left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid\r\n      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."' \r\n   and a.noakun = '".$noakun."'\r\n   AND a.kodeorg like '".$kodeo."%' group by a.kodesupplier";
saveLog($str);
$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->kodesupplier=='') {
        $debet['lain'] = $bar->debet;
        $kredit['lain'] = $bar->kredit;
        $supplier['lain'] = 'lain';
    } else {
        $debet[$bar->kodesupplier] = $bar->debet;
        $kredit[$bar->kodesupplier] = $bar->kredit;
        $supplier[$bar->kodesupplier] = $bar->namasupplier;
    }

    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}
}

if($kodeakun=='131'){
$str = 'select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.kodecustomer,c.namacustomer,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      inner join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n     left join ".$dbname.".pmn_4customer c on a.kodecustomer = c.kodecustomer\r\n      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."' \r\n   and a.noakun = '".$noakun."'\r\n   AND a.kodeorg like '".$kodeo."%' group by a.kodecustomer";
saveLog($str);
$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->kodecustomer=='') {
        $debet['lain'] = $bar->debet;
        $kredit['lain'] = $bar->kredit;
        $supplier['lain'] = 'lain';
    } else {
        $debet[$bar->kodecustomer] = $bar->debet;
        $kredit[$bar->kodecustomer] = $bar->kredit;
        $supplier[$bar->kodecustomer] = $bar->namacustomer;
    }

    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}
}

$str = 'select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.nik,c.namakaryawan,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      inner join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n      inner join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     \r\n      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  \r\n      and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null \r\n   AND a.kodeorg like '".$kodeo."%' group by c.karyawanid ";

$res = mysql_query($str);
if(mysql_num_rows($res)>0){
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $debet[$bar->nik] = $bar->debet;
    $kredit[$bar->nik] = $bar->kredit;
    $supplier[$bar->nik] = $bar->namakaryawan;
    $akun[$bar->noakun] = $bar->namaakun;
    $kodeorg[$no] = $bar->kodeorg;
}
}


*/
$no = 0;
if ($supplier < 1) {
    echo '<tr class=rowcontent><td colspan=9>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    if (!empty($supplier)) {
        foreach ($supplier as $kdsupp => $val) {
            if(substr($noakun,0,1)=='2'){
                $saldoakhr=($sawal[$kdsupp] - $debet[$kdsupp]) + $kredit[$kdsupp];
            }else{
                $saldoakhr=($sawal[$kdsupp] + $debet[$kdsupp]) - $kredit[$kdsupp];
            }

            if ('lain' !== $val) {
                ++$no;
                echo "<tr class=rowcontent onclick=lihatDetailHutang('".$kdsupp."','".$noakun."','".$tanggalmulai."','".$tanggalsampai."','".$pt[$kodeorg[$no]]."',event)>
                        <td align=center width=20>".$no."</td>
                        <td align=center>".$pt[$kodeorg[$no]]."</td>
                        <td>".$noakun."</td>
                        <td>".$akun[$noakun]."</td>
                        <td>".$val."</td>
                        <td align=right width=100>".number_format($sawal[$kdsupp], 2)."</td>
                        <td align=right width=100>".number_format($debet[$kdsupp], 2)."</td>
                        <td align=right width=100>".number_format($kredit[$kdsupp], 2)."</td>
                        <td align=right width=100>".number_format($saldoakhr, 2)."</td>
                    </tr>";
                $tsa += $sawal[$kdsupp];
                $td += $debet[$kdsupp];
                $tk += $kredit[$kdsupp];
                $tak += ($sawal[$kdsupp] + $debet[$kdsupp]) - $kredit[$kdsupp];
            }
     
        }
    }
    if(substr($noakun,0,1)=='2'){
        $saldoakhr=($sawal['lain'] - $debet['lain']) + $kredit['lain'];
    }else{
        $saldoakhr=($sawal['lain'] + $debet['lain']) - $kredit['lain'];
    }
    ++$no;
    echo "<tr class=rowcontent onclick=lihatDetailHutang('','".$noakun."','".$tanggalmulai."','".$tanggalsampai."','".$pt[$kodeorg[$no]]."',event)>\r\n                  <td align=center width=20>".$no."</td>\r\n                  <td align=center>".$pt[$kodeorg[$no]]."</td>\r\n                  <td>".$noakun."</td>\r\n                  <td>".$akun[$noakun]."</td>\r\n                  <td></td>\r\n                   <td align=right width=100>".number_format($sawal['lain'], 2)."</td>   \r\n                  <td align=right width=100>".number_format($debet['lain'], 2)."</td>\r\n                  <td align=right width=100>".number_format($kredit['lain'], 2)."</td>\r\n                  <td align=right width=100>".number_format($saldoakhr, 2)."</td>\r\n                 </tr>";
    $tsa += $sawal['lain'];
    $td += $debet['lain'];
    $tk += $kredit['lain'];
    $tak += ($sawal['lain'] + $debet['lain']) - $kredit['lain'];

}

?>