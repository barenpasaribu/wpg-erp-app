<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
echo "\t<link rel=stylesheet type=text/css href=style/generic.css>\t\r\n";
$tanggalmulai = $_GET['mulai'];
$tanggalsampai = $_GET['sampai'];
$noakun = $_GET['noakun'];
$kodesupplier = $_GET['kodesupplier'];
$kodeorg = $_GET['kodeorg'];
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

$str = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$kodesupplier."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $supplier = $bar->namakaryawan;
}
$str = 'select namasupplier from '.$dbname.".log_5supplier where supplierid='".$kodesupplier."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $supplier = $bar->namasupplier;
}


if($kodesupplier==''){
   $where="and kodesupplier='".$kodesupplier."' AND nik='".$kodesupplier."' AND kodecustomer='".$kodesupplier."'"; 
} else {
    $where="and (kodesupplier='".$kodesupplier."' OR nik='".$kodesupplier."' OR kodecustomer='".$kodesupplier."')";
}

$kodeorg = substr($_GET['kodeorg'],0,3);

$str = " SELECT a.*, sum(awal) as sawal , namaakun, if(namasupplier is null ,namacustomer,namasupplier) as namasupplier from (SELECT if(noakun like '2%', sum(kredit-debet), sum(debet-kredit)) as awal,
noakun, if(kodesupplier='',kodecustomer,kodesupplier) as kodesupplier,kodeorg from keu_jurnaldt_vw x
 where tanggal<'".$tanggalmulai."'  and noakun = '".$noakun."' 
  AND kodeorg like '".$kodeorg."%'  ".$where." group by x.kodesupplier, kodecustomer
  union
  SELECT jumlah AS awal, noakun, supplierid AS kodesupplier, kodeorg FROM keu_saldoawalhutang 
WHERE kodeorg LIKE '".$kodeorg."%' AND noakun = '".$noakun."' and supplierid='".$kodesupplier."' ) AS a 
inner join keu_5akun b on a.noakun = b.noakun 
left join log_5supplier c on a.kodesupplier = c.supplierid
left join pmn_4customer d on a.kodesupplier = d.kodecustomer 
 group by a.kodesupplier ";

//saveLog($str);
/*
$str = "select (if(a.noakun like \'2%\', sum(a.kredit-a.debet), sum(a.debet-a.kredit))+(select jumlah from keu_saldoawalhutang where supplierid='".$kodesupplier."' AND noakun='".$noakun."')) as sawal,a.noakun from ".$dbname.".keu_jurnaldt_vw a\r\n      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' ".$where." and a.kodeorg like '".$kodeorg."%'";
*/
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $sawal[$kodesupplier] = $bar->sawal;
    $supplier=$bar->namasupplier;
}
/*
$str = 'select a.debet  as debet, a.kredit as kredit,a.nojurnal,a.noreferensi,a.tanggal,a.noakun,a.keterangan, a.kodesupplier from '.$dbname.".keu_jurnaldt_vw a\r\n      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."' \r\n      and a.noakun = '".$noakun."' and kodesupplier='".$kodesupplier."'\r\n   and a.kodeorg like '".$kodeorg."%' order by tanggal";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $dat[$bar->nojurnal] = $bar->tanggal;
    $ket[$bar->nojurnal] = $bar->nojurnal;
    $ref[$bar->nojurnal] = $bar->noreferensi;
    $debet[$bar->nojurnal] = $bar->debet;
    $kredit[$bar->nojurnal] = $bar->kredit;
}
*/
$str = 'select sum(a.debet) as debet,sum(a.kredit) as kredit,a.nojurnal,a.noreferensi,a.tanggal,a.keterangan,a.noakun,a.nik from '.$dbname.".keu_jurnaldt_vw a where a.tanggal between '".$tanggalmulai."' and '".$tanggalsampai."'  and a.noakun = '".$noakun."' ".$where." and a.kodeorg like '".$kodeorg."%' group by a.nojurnal order by a.tanggal";

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $dat[$bar->nojurnal] = $bar->tanggal;
    $ket[$bar->nojurnal] = $bar->nojurnal;
    $ref[$bar->nojurnal] = $bar->noreferensi;
    $debet[$bar->nojurnal] = $bar->debet;
    $kredit[$bar->nojurnal] = $bar->kredit;
    $keterangan[$bar->nojurnal] = $bar->keterangan;
}

echo "<table class=sortable cellspacing=1 border=0 width=100%>\r\n             <thead>\r\n                    <tr>\r\n                          <td align=center width=50>".$_SESSION['lang']['nourut']."</td>\r\n                          <td align=center>".$_SESSION['lang']['organisasi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['tanggal']."</td>    \r\n                          <td align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['noreferensi']."</td>     \r\n                          <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n                          <td align=center>Karyawan/Supplier</td>\r\n   <td>Keterangan</td>                       <td align=center>".$_SESSION['lang']['saldoawal']."</td>                             \r\n                          <td align=center>".$_SESSION['lang']['debet']."</td>\r\n                          <td align=center>".$_SESSION['lang']['kredit']."</td>\r\n                          <td align=center>".$_SESSION['lang']['saldoakhir']."</td>                               \r\n                        </tr>  \r\n                 </thead>\r\n                 <tbody id=container>";
$no = 0;
if (count($dat) < 1) {
    echo '<tr class=rowcontent><td colspan=9>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    $tsa = $sawal[$kodesupplier];
    foreach ($dat as $notran => $val) {
        ++$no;
        if (0 !== $debet[$notran] || 0 !== $kredit[$notran]) {
            if(substr($noakun,0,1)=='2'){
                $saldoakhr=$tsa - $debet[$notran] + $kredit[$notran];
            }else{
                $saldoakhr=$tsa + $debet[$notran] - $kredit[$notran];
            }
            echo "<tr class=rowcontent >\r\n                      <td align=center width=20>".$no."</td>\r\n                      <td align=center>".$kodeorg."</td>   \r\n                      <td align=center>".tanggalnormal($val)."</td>                   \r\n                      <td align=center>".$notran."</td>\r\n                       <td align=center>".$ref[$notran]."</td>     \r\n                      <td>".$noakun."</td>\r\n                      <td>".$supplier."</td>\r\n <td>".$keterangan[$notran]."</td>                      <td align=right width=100>".number_format($tsa, 2)."</td>   \r\n                      <td align=right width=100>".number_format($debet[$notran], 2)."</td>\r\n                      <td align=right width=100>".number_format($kredit[$notran], 2)."</td>\r\n 
                <td align=right width=100>".number_format($saldoakhr, 2)."</td>\r\n                     </tr>";
            $tsa = $saldoakhr;
        }
    }
}

?>