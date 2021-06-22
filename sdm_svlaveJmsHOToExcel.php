<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$val1 = trim($_GET['periode']);
$val = substr($val1, 3, 4).'-'.substr($val1, 0, 2);
$str = 'select * from '.$dbname.'.sdm_ho_hr_jms_porsi';
$res = mysql_query($str);
$karyawan = 0.02;
$perusahaan = 4.54;
$angka = 1;
    while ($bar = mysql_fetch_object($res)) {
        if ('karyawan' == $bar->id) {
            $karyawan = $bar->value / 100;
            $angka = $bar->value;
            $jhtkar = $bar->jhtkar / 100;
            $jpkar = $bar->jpkar / 100;
        }

        if ('perusahaan' == $bar->id) {
            $perusahaan = $bar->value / 100;
            $jhtpt = $bar->jhtpt / 100;
            $jppt = $bar->jppt / 100;
            $jkkpt = $bar->jkkpt / 100;
            $jkmpt = $bar->jkmpt / 100;
        }
    }
//$str = "select e.name,e.startdate,e.nojms,d.value,d.karyawanid,d.periode from ".$dbname.'.sdm_ho_employee e, '.$dbname.".sdm_ho_detailmonthly where e.karyawanid=d.karyawanid and e.operator='".$_SESSION['standard']['username']."' and d.periode='".$val."' and d.component=3 order by name";
$str = "select e.name,e.startdate,e.nojms,d.jumlah,d.karyawanid,d.periodegaji,d.idkomponen,f.name as namakomponen, d.idkomponen
            from ".$dbname.'.sdm_ho_employee e, '.$dbname.'.sdm_gaji d, '.$dbname.".sdm_ho_component f
            where e.karyawanid=d.karyawanid and d.idkomponen=f.id and e.operator='".$_SESSION['standard']['username']."'
            and 
                d.periodegaji='".$val."' and d.idkomponen in(5,6,7,9,8,57)
            order by karyawanid";
$stream = '';
$stream .= '<b>Laporan Jamsostek Bulan: '.substr($val, 5, 2).'-'.substr($val, 0, 4).'</b>';
$stream .= "<table class=sortable width=100% border=2 cellspacing=1>\r\n\t\t      <thead>\r\n\t\t\t  <tr class=rowheader>\r\n\t\t\t    <td align=center>No.</td>\r\n\t\t\t\t<td align=center>No.Karyawan</td>\r\n\t\t\t    <td align=center>Nama.Karyawan</td>
<td align=center>Tgl.Masuk</td>\r\n\t\t\t\t<td align=center>Periode</td>\r\n\t\t\t\t<td align=center>JHT Kary<br>(Rp.)</td>\r\n                <td align=center>JP Kary<br>(Rp.)</td>\r\n<td align=center>Beban.Karyawan<br>(Rp.)</td>\r\n\t\t\t\t<td align=center>JHT PT<br>(Rp.)</td>\r\n                <td align=center>JP PT<br>(Rp.)</td>\r\n                <td align=center>JKK PT<br>(Rp.)</td>\r\n                <td align=center>JKM PT<br>(Rp.)</td>\r\n<td align=center>Beban.Perusahaan<br>(Rp.)</td>\r\n\t\t\t\t<td align=center>Gaji Bruto</td>\r\n\t\t\t  </tr>\r\n\t\t\t  </thead>\r\n\t\t\t  <tbody id=tbody>";
    $res = mysql_query($str, $conn);
    $no = 0;
    $ttl = 0;
    $tvp = 0;
    $tkar = 0;
    $total = 0;
    $ttljhtkar = 0;
    $ttljpkar = 0;
    $ttljhtpt = 0;
    $ttljppt = 0;
    $ttljkkpt = 0;
    $ttljkmpt = 0;
    $ttlbpjskar = 0;
    $ttlbpjspt = 0;
    while ($bar = mysql_fetch_object($res)) {
        $valPerusahaan = $bar->jumlah;
        $total = $valPerusahaan;
        $stru = '   select sum(jumlah) as gjk, 
                    case when jumlah>7703500 then 7703500 else sum(jumlah) end as gjke 
                    from '.$dbname.".sdm_gaji 
                    where 
                        idkomponen in(1,2,3,4,15,29,30,32,33,35,36,37,38,39,40,41,42,43,44,46,47,48,49,50,51)
                    and 
                        periodegaji='".$val."' and karyawanid=".$bar->karyawanid;
						//$stream .= $stru;
        $resu = mysql_query($stru, $conn);
        $gjkotor = 0;
        $ttljhtkar = 0;
        $ttljpkar = 0;
        $ttljhtpt = 0;
        $ttljppt = 0;
        $ttljkkpt = 0;
        $ttljkmpt = 0;
        $ttlbpjskar = 0;
        $ttlbpjspt = 0;
        while ($baru = mysql_fetch_object($resu)) {
            $gjkotor = $baru->gjk;
            $ttljhtkar = $jhtkar * $baru->gjk;
            $ttljpkar = $jpkar * $baru->gjke;
            $ttlbpjskar = $ttljhtkar + $ttljpkar;
            $ttljhtpt = $jhtpt * $baru->gjk;
            $ttljppt = $jppt * $baru->gjke;
            $ttljkkpt = $jkkpt * $baru->gjk;
            $ttljkmpt = $jkmpt * $baru->gjk;
            $ttlbpjspt = $ttljhtpt + $ttljppt + $ttljkkpt + $ttljkmpt;
        }
		if($bar->idkomponen == 5){
        ++$no;
        $stream .= "<tr class=rowcontent>
		<td class=firsttd>".$no."</td>
		<td>".$bar->karyawanid."</td>
		<td>".$bar->name."</td>";
		
		//echo"<td>".$bar->namakomponen."</td>
		$stream .="<td align=right>".tanggalnormal($bar->startdate)."</td>
		<td align=center>".$bar->periodegaji."</td>
		<td align=right>".number_format($ttljhtkar * -1, 2, '.', ',')."</td>
		<td align=right>".number_format($ttljpkar * -1, 2, '.', ',')."</td>
		<td align=right>".number_format($ttlbpjskar * -1, 2, '.', ',')."</td>
		<td align=right>".number_format($ttljhtpt, 2, '.', ',')."</td>
		<td align=right>".number_format($ttljppt, 2, '.', ',')."</td>
		<td align=right>".number_format($ttljkkpt, 2, '.', ',')."</td>
		<td align=right>".number_format($ttljkmpt, 2, '.', ',')."</td>
		<td align=right>".number_format($ttlbpjspt, 2, '.', ',')."</td>
		<td align=right>".number_format($gjkotor, 2, '.', ',')."</td> </tr>";
		}
    }
$stream .= "</tbody>\r\n\t\t\t  <tfoot></tfoot>\r\n\t\t\t    <tr class=rowcontent>\r\n\t\t      </table></div>";
//$stream .= "</tbody>\r\n\t\t\t  <tfoot></tfoot>\r\n\t\t\t    <tr class=rowcontent>\r\n\t\t\t    <td class=firsttd colspan=6 align=center>TOTAL</td>\r\n\t\t\t\t<td align=right>".number_format($kar, 2, '.', '')."</td>\r\n\t\t\t\t<td align=right>".number_format($tvp, 2, '.', '')."</td>\t\t\t\t\r\n\t\t\t\t<td align=right>".number_format($ttl, 2, '.', '')."</td>\r\n\t\t\t  </tr>\r\n\t\t      </table>";
$nop_ = 'jamsostek'.$val1;
if (0 < strlen($stream)) {
    if ($handle = opendir('tempExcel')) {
        while (false != ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
                @unlink('tempExcel/'.$file);
            }
        }
        closedir($handle);
    }

    $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
    if (!fwrite($handle, $stream)) {
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>