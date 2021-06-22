<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<link rel=stylesheet tyle=text href='style/generic.css'>\n          <script language=javascript src='js/generic.js'></script><script language=\"javascript1.2\">\nfunction viewDetail(kodeorg,tanggal,barang,ev)\n{\n   param='kodeorg='+kodeorg+'&tanggal='+tanggal+'&barang='+barang;\n   tujuan='pabrik_slave_4persediaan_detail.php'+\"?\"+param;  \n   width='600';\n   height='300';\n  \n   content=\"<iframe frameborder=0 width=100% height=100% src='\"+tujuan+\"'></iframe>\";\n   showDialog1('Detail Pengiriman '+kodeorg+' '+barang+' '+tanggal,content,width,height,ev); \n\t\n}\n</script>\n";
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $kodeorg = $param['pks'];
        $tanggal = substr($param['tanggal'], 6, 4).'-'.substr($param['tanggal'], 3, 2).'-'.substr($param['tanggal'], 0, 2);
        echo 'Stok Produksi '.$kodeorg.' Per:'.$tanggal."\n                                         <table class=sortable cellspacing=1 border=0>\n\t\t<thead><tr class=rowheader>\n\t\t<td>".$_SESSION['lang']['produk']."</td>\n\t\t<td>".$_SESSION['lang']['saldoawal']."</td>\n\t\t<td>".$_SESSION['lang']['produksi']."</td>\n\t\t<td>".$_SESSION['lang']['pengiriman']."</td>\n\t\t<td>".$_SESSION['lang']['sisa']."</td>\n\t\t</tr></thead><tbody>";
        $sql = 'select sum(kuantitas) as kuantitas from '.$dbname.".pabrik_masukkeluartangki where kodeorg = '".$kodeorg."' and tanggal = '".$tanggal."'";
        $query = mysql_query($sql) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $z = $res['kuantitas'];
        }
        $sql = 'select oer from '.$dbname.".pabrik_produksi where kodeorg = '".$kodeorg."' and tanggal = '".$tanggal."'";
        $query = mysql_query($sql) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $u = $res['oer'];
        }
        $sql = 'select sum(beratbersih) as beratbersih from '.$dbname.".pabrik_timbangan where millcode = '".$kodeorg."' and tanggal like '".$tanggal."%' and kodebarang = '40000001'";
        $query = mysql_query($sql) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $c = $res['beratbersih'];
        }
        $sql = 'select sum(kernelquantity) as kuantitas from '.$dbname.".pabrik_masukkeluartangki where kodeorg = '".$kodeorg."' and tanggal = '".$tanggal."'";
        $query = mysql_query($sql) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $m = $res['kuantitas'];
        }
        $sql = 'select oerpk from '.$dbname.".pabrik_produksi where kodeorg = '".$kodeorg."' and tanggal = '".$tanggal."'";
        $query = mysql_query($sql) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $n = $res['oerpk'];
        }
        $sql = 'select sum(beratbersih) as beratbersih from '.$dbname.".pabrik_timbangan where millcode = '".$kodeorg."' and tanggal like '".$tanggal."%' and kodebarang = '40000002'";
        $query = mysql_query($sql) || exit(mysql_error($conns));
        while ($res = mysql_fetch_assoc($query)) {
            $g = $res['beratbersih'];
        }
        $a = $z - $u;
        $b = $u;
        $d = $z - $c;
        $e = $m - $n;
        $f = $n;
        $h = $m - $g;
        echo '<tr class=rowcontent><td>CPO (Kg)</td>';
        echo '<td align=right>'.number_format($a, 0).'</td>';
        echo '<td align=right>'.number_format($b, 0).'</td>';
        echo "<td align=right style=cursor:pointer onclick=viewDetail('".$kodeorg."','".$tanggal."','40000001',event) title='Detail Pengiriman'>".number_format($c, 0).'</td>';
        echo '<td align=right>'.number_format($d, 0).'</td>';
        echo '</tr><tr class=rowcontent><td>Kernel (Kg)</td>';
        echo '<td align=right>'.number_format($e, 0).'</td>';
        echo '<td align=right>'.number_format($f, 0).'</td>';
        echo "<td align=right style=cursor:pointer onclick=viewDetail('".$kodeorg."','".$tanggal."','40000002',event) title='Detail Pengiriman'>".number_format($g, 0).'</td>';
        echo '<td align=right>'.number_format($h, 0).'</td>';
        echo '</tr></tbody></table>';

        break;
}

?>