<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tanggalmulai = $_GET['tanggalmulai'];
$tanggalsampai = $_GET['tanggalsampai'];
$noakun = $_GET['noakun'];
$kodeo = $_GET['kodeorg'];
$stream = "<table border=1>\r\n             <thead>\r\n                    <tr>\r\n                          <td align=center width=50>".$_SESSION['lang']['nourut']."</td>\r\n                          <td align=center>".$_SESSION['lang']['organisasi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n                          <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n                           <td align=center>".$_SESSION['lang']['karyawan'].'/'.$_SESSION['lang']['supplier']."</td>\r\n                          <td align=center>".$_SESSION['lang']['saldoawal']."</td>                             \r\n                          <td align=center>".$_SESSION['lang']['debet']."</td>\r\n                          <td align=center>".$_SESSION['lang']['kredit']."</td>\r\n                          <td align=center>".$_SESSION['lang']['saldoakhir']."</td>                               \r\n                        </tr>  \r\n                 </thead>\r\n                 <tbody id=container>";
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
$str = 'select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.kodesupplier,c.namasupplier,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n      left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid\r\n      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' \r\n      ".$ind." group by a.kodesupplier,a.kodeorg\r\n";

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('' === $bar->kodesupplier) {
        $sawal['lain'] = $bar->sawal;
        $supplier['lain'] = 'lain';
    } else {
        $sawal[$bar->kodesupplier] = $bar->sawal;
        $supplier[$bar->kodesupplier] = $bar->namasupplier;
    }

    $akun[$bar->noakun] = $bar->namaakun;
}
$str = 'select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.nik,c.namakaryawan,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n      left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     \r\n      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null \r\n       ".$ind.' group by c.nik,a.kodeorg';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $sawal[$bar->nik] = $bar->sawal;
    $supplier[$bar->nik] = $bar->namakaryawan;
    $akun[$bar->noakun] = $bar->namaakun;
}
$str = 'select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.kodesupplier,c.namasupplier,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n      left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid\r\n      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."' \r\n      and a.noakun = '".$noakun."'\r\n      ".$ind." group by a.kodesupplier,a.kodeorg\r\n";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('' === $bar->kodesupplier) {
        $debet['lain'] = $bar->debet;
        $kredit['lain'] = $bar->kredit;
        $supplier['lain'] = 'lain';
    } else {
        $debet[$bar->kodesupplier] = $bar->debet;
        $kredit[$bar->kodesupplier] = $bar->kredit;
        $supplier[$bar->kodesupplier] = $bar->namasupplier;
    }

    $akun[$bar->noakun] = $bar->namaakun;
}
$str = 'select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.nik,c.namakaryawan,a.kodeorg from '.$dbname.".keu_jurnaldt_vw a\r\n      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n      left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     \r\n      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  \r\n      and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null \r\n      ".$ind." group by c.karyawanid,a.kodeorg\r\n";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $debet[$bar->nik] = $bar->debet;
    $kredit[$bar->nik] = $bar->kredit;
    $supplier[$bar->nik] = $bar->namakaryawan;
    $akun[$bar->noakun] = $bar->namaakun;
}

*/


$no = 0;

if ($supplier < 1) {
    $stream .= '<tr class=rowcontent><td colspan=9>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    if (!empty($supplier)) {
        foreach ($supplier as $kdsupp => $val) {
            if ('lain' !== $val) {
            if(substr($noakun,0,1)=='2'){
                $saldoakhr=($sawal[$kdsupp] - $debet[$kdsupp]) + $kredit[$kdsupp];
            }else{
                $saldoakhr=($sawal[$kdsupp] + $debet[$kdsupp]) - $kredit[$kdsupp];
            }
                ++$no;
                $stream .= "<tr>\r\n                  <td align=center width=20>".$no."</td>\r\n                  <td align=center>".$kodeo."</td>\r\n                  <td>".$noakun."</td>\r\n                  <td>".$akun[$noakun]."</td>\r\n                  <td>".$val."</td>\r\n                   <td align=right width=100>".number_format($sawal[$kdsupp], 2)."</td>   \r\n                  <td align=right width=100>".number_format($debet[$kdsupp], 2)."</td>\r\n                  <td align=right width=100>".number_format($kredit[$kdsupp], 2)."</td>\r\n                  <td align=right width=100>".number_format($saldoakhr, 2)."</td>\r\n                 </tr>";
                $tsa += $sawal[$kdsupp];
                $td += $debet[$kdsupp];
                $tk += $kredit[$kdsupp];
                $tak += $saldoakhr;
            }
        }
    }
    if(substr($noakun,0,1)=='2'){
        $saldoakhr=($sawal['lain'] - $debet['lain']) + $kredit['lain'];
    }else{
        $saldoakhr=($sawal['lain'] + $debet['lain']) - $kredit['lain'];
    }
    ++$no;
    $stream .= "<tr >\r\n                  <td align=center width=20>".$no."</td>\r\n                  <td align=center>".$kodeo."</td>\r\n                  <td>".$noakun."</td>\r\n                  <td>".$akun[$noakun]."</td>\r\n                  <td></td>\r\n                   <td align=right width=100>".number_format($sawal['lain'], 2)."</td>   \r\n                  <td align=right width=100>".number_format($debet['lain'], 2)."</td>\r\n                  <td align=right width=100>".number_format($kredit['lain'], 2)."</td>\r\n                  <td align=right width=100>".number_format(($saldoakhr, 2)."</td>\r\n                 </tr>";
    $tsa += $sawal['lain'];
    $td += $debet['lain'];
    $tk += $kredit['lain'];
    $tak += $saldoakhr;
}

$stream .= "<tr class=rowcontent>\r\n      <td align=center colspan=5>Total</td>\r\n       <td align=right width=100>".number_format($tsa, 2)."</td>   \r\n      <td align=right width=100>".number_format($td, 2)."</td>\r\n      <td align=right width=100>".number_format($tk, 2)."</td>\r\n      <td align=right width=100>".number_format($tak, 2)."</td>\r\n     </tr>";
$stream .= '</tbody></table>';
$qwe = date('YmdHms');
$nop_ = 'LP_JRNL_Hutang Dan Piutang_'.$noakun.'_'.$qwe;
if (0 < strlen($stream)) {
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>