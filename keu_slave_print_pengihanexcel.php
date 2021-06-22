<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/devLibrary.php';
require_once 'lib/terbilang.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$optnmcust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optnmakun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

$sql = "SELECT p.namacustomer,o.namaorganisasi,a1.namaakun AS akunkredit,a2.namaakun AS akundebet,k.*
from  keu_penagihanht k
LEFT OUTER JOIN organisasi o ON o.kodeorganisasi=k.kodeorg
LEFT OUTER JOIN pmn_4customer p ON p.kodecustomer=k.kodecustomer
LEFT OUTER JOIN keu_5akun a1 ON a1.noakun=k.kredit
LEFT OUTER JOIN keu_5akun a2 ON a2.noakun=k.debet
where noinvoice='".$column."' ";
//$str = 'select * from '.$dbname.'.'.$_GET['table']."  where noinvoice='".$column."'";
$res = mysql_query($sql);
$bar = mysql_fetch_assoc($res);
        
        $test = explode(',', $_GET['column']);
//        list($notransaksi, $kodevhc) = $test;
        $str = 'select * from '.$dbname.'.'.$_GET['table']."  where noinvoice='".$column."'";
        $res = mysql_query($str);
        $bar = mysql_fetch_object($res);
        $posting = $bar->posting;
        $str0 = 'select * from '.$dbname.".organisasi where tipe IN('holding','tipe') and kodeorganisasi='".$bar->kodeorg."'";
        $res0 = mysql_query($str0);
         while ($bar0 = mysql_fetch_object($res0)) {
            $induk = $bar0->induk;
           
        }
      
        $str1 = 'select * from '.$dbname.".organisasi where tipe IN ('holding','pt') and kodeorganisasi='".$induk."'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $namapt = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
            $kodeorganisasi = $bar1->kodeorganisasi;
        }
           $strNpwp = 'select * from '.$dbname.".setup_org_npwp where  kodeorg='".$kodeorganisasi."'";
        $resNpwp = mysql_query($strNpwp);
         while ($barNpwp = mysql_fetch_object($resNpwp)) {
            $npwp = $barNpwp->npwp;
           
        }

        $sql2 = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$bar->updateby."'";
        $query2 = mysql_query($sql2);
        $res2 = mysql_fetch_object($query2);
        $sql5 = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$bar->postingby."'";
        $query5 = mysql_query($sql5);
        $res5 = mysql_fetch_object($query5);
        $sqlJnsVhc = 'select namajenisvhc from '.$dbname.".vhc_5jenisvhc where jenisvhc='".$bar->jenisvhc."'";
        $qJnsVhc = mysql_query($sqlJnsVhc);
        $rJnsVhc = mysql_fetch_assoc($qJnsVhc);
        $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$bar->jenisbbm."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        $strcust = 'select b.*,date(a.tanggal) as tgl from '.$dbname.'.'.$_GET['table']." as a ,  pmn_4customer as b  where a.kodecustomer=b.kodecustomer  and a.noinvoice='".$column."'";
        $rescust = mysql_query($strcust);
        $barcust = mysql_fetch_object($rescust);

$tab = "<table cellpadding=0 cellspacing=0 border=0 class=sortable style='width:100%;'>";
$tab.= "<tbody>
            <tr>
                <td colspan='3' align='left'><h3>".$namapt."</h3></td>
                <td colspan='2' align='right'><h3>INVOICE</h3></td>
            </tr>";
        $PecahStr = explode(",", $alamatpt);
        for ( $i = 0; $i < count( $PecahStr ); $i++ ) {
$tab.= "    <tr>
        <td colspan='5' align='left'>".trim($PecahStr[$i])."</td>
    </tr>";
             }

    $tab.= " <tr> <td colspan='5' align='left'>NPWP : ".$npwp."</td>
    </tr>
    <tr>
        <td colspan='5' align='left'>&nbsp;</td>
    </tr>

    <tr>
        <td colspan='3' align='left'>Kepada : </td>
        <td colspan='2' align='right'>No Invoice : ".$bar->noinvoice."</td>
    </tr>
    <tr>
        <td colspan='3' align='left'>".$barcust->namacustomer."</td>
        <td colspan='2' align='right'>Tanggal : ".$barcust->tgl."</td>
    </tr>";
        $PecahStr = explode(",", $barcust->alamat.','.$barcust->kota);
        for ( $i = 0; $i < count( $PecahStr ); $i++ ) {
    $tab.= "    <tr>
        <td colspan='5' align='left'>".trim($PecahStr[$i])."</td>
    </tr>";
    }
    $tab.= "    <tr>
        <td colspan='5' align='left'>&nbsp;</td>
    </tr>";
$tab.="<tr> <td align='center'  valign='top' colspan=5><table border=1>";   
    $tab.="<tr>
        <td align='center'>No. </td>
        <td align='center'>Keterangan</td>
        <td align='center'>Quantity</td>
        <td align='center'>Harga Satuan</td>
        <td align='center'>Total</td>
    </tr>";
 $no=0;
 $strInv = 'select * from '.$dbname.'.'.$_GET['table']."  where noinvoice='".$column."'";

 $resInv = mysql_query($strInv);
while ($barInv = mysql_fetch_object($resInv)) {
    $no++;
   $nodo =  $barInv->noorder;
   $tipe =  $barInv->tipe;
$tb_timbangan= 'pabrik_timbangan';
$strTonase = 'select sum(b.beratbersih) as netto from '.$dbname.'.'."keu_penagihandt as a,pabrik_timbangan as b  where a.nosipb='".$nodo."' and a.notiket=b.notransaksi AND a.nodo=b.nosipb group by a.nodo";

$resTonase = mysql_query($strTonase);
$barTonase = mysql_fetch_object($resTonase);
$tb_kj= 'pmn_kontrakjual';
$strHarga = 'select * from '.$dbname.'.'.$tb_kj."  where nodo='".$nodo."'";

$resHarga = mysql_query($strHarga);
$barHarga = mysql_fetch_object($resHarga);
$kodebarang = $barHarga->kodebarang;
$nokontrak = $barHarga->nokontrak;
$strProduk = 'select * from '.$dbname.".log_5masterbarang  where kodebarang='".$kodebarang."'";
$resProduk = mysql_query($strProduk);
$barProduk = mysql_fetch_object($resProduk);
  
$tab.="<tr> <td align='center'  valign='top'>".$no."</td>";

    if($tipe == '1'){  
        $tab.="<td align='left'>UANG MUKA PENJUALAN <br> ".$barProduk->namabarang." <br> ".$nokontrak." <br> ".$barInv->keterangan."</td>";
        $tab.="<td align='center'></td>";
        $tab.="<td align='right'></td>";
        $tab.="<td align='right'  valign='top'>Rp. ".number_format($barInv->nilaiinvoice,0,",",".")."</td>";
        $nilai += $barInv->nilaiinvoice ;      
    }else if($tipe == '2'){  
        $tab.="<td align='left'>".$barInv->keterangan."<br> Uang Muka Rp.".number_format($barInv->uangmuka,0,",",".")." <br> ".$nokontrak." ".$barProduk->namabarang."</td>";
        $tab.="<td align='center'  valign='top'>".$barTonase->netto." ".$barProduk->satuan."</td>";
        $tab.="<td align='right'  valign='top'>Rp. ".number_format($barHarga->hargasatuan,0,",",".")."</td>";
        $tab.="<td align='right'  valign='top'>Rp. ".number_format($barHarga->hargasatuan*$barTonase->netto,0,",",".")."</td>";
        $nilai += $barInv->nilaiinvoice+$barInv->uangmuka ;      
    } else {  
        $tab.="<td align='left'> PENJUALAN <br> ".$barProduk->namabarang." <br> ".$nokontrak." <br> ".$barInv->keterangan."</td>";
        $tab.="<td align='center' valign='top'>".$barTonase->netto." ".$barProduk->satuan."</td>";
        $tab.="<td align='right'  valign='top'>Rp. ".number_format($barHarga->hargasatuan,0,",",".")."</td>";
        $tab.="<td align='right'  valign='top'>Rp. ".number_format($barHarga->hargasatuan*$barTonase->netto,0,",",".")."</td>";
        $nilai += $barHarga->hargasatuan*$barTonase->netto ;      
    }
$potsusutjml += $barInv->potongsusutjmlint+$barInv->potongsusutjmlext;
$potmutuint += $barInv->potongmutuint;
$potmutuext += $barInv->potongmutuext;
$nilaippn += ($barInv->nilaippn);
$nilaipph += ($barInv->nilaipph);
$kodekaryawan = $barInv->namattd ;
}
    $tab.="</tr>";
    $potTotal = (($potsusutjml)+($potmutuint)+($potmutuext))+($nilaipph);
        $tab.="<tr><td colspan=3 align='left'>Total</td>";
        $tab.="<td align='right'>Rp.</td>";
        $tab.="<td align='right'>".number_format($nilai,0,",",".")."</td></tr>";

        if($bar->uangmuka>0){
        $tab.="<tr><td colspan=3 align='left'>Uang Muka</td>";
        $tab.="<td align='right'>Rp.</td>";
        $tab.="<td align='right'>".number_format($bar->uangmuka,0,",",".")."</td></tr>";
        }

        if($potsusutjml>0){
            $tab.="<tr><td colspan=3 align='left'>Potongan Kesusutan</td>";
            $tab.="<td align='right'>Rp.</td>";     
            $tab.="<td align='right'>".number_format($potsusutjml,0,",",".")."</td></tr>";
        }

        if(($potmutuint+$potmutuext)>0){
            $tab.="<tr><td colspan=3 align='left'>Potongan Mutu</td>";
            $tab.="<td align='right'>Rp.</td>";
            $tab.="<td align='right'>".number_format($potmutuint+$potmutuext,0,",",".")."</td></tr>";
        }
if( $tipe == '0' || $tipe == '3'){
        $tab.="<tr><td colspan=3 align='left'>Sub Total</td>";
        $tab.="<td align='right'>Rp.</td>";
        $tab.="<td align='right'>".number_format(($nilai)-($potTotal)-$bar->uangmuka,0,",",".")."</td></tr>";
}        
        $tab.="<tr><td colspan=3 align='left'>Nilai PPN</td>";
        $tab.="<td align='right'>Rp.</td>";
        $tab.="<td align='right'>".number_format(($nilaippn),0,",",".")."</td></tr>";

        if($nilaipph>0){
        $tab.="<tr><td colspan=3 align='left'>Nilai Pph</td>";
        $tab.="<td align='right'>Rp.</td>";
        $tab.="<td align='right'>".number_format($nilaipph,0,",",".")."</td></tr>";
        }

        $tab.="<tr><td colspan=3 align='left'>Total Akhir</td>";
        $tab.="<td align='right'>Rp.</td>";
        $tab.="<td align='right'>".number_format((($nilai)-($potTotal))+($nilaippn)-$nilaipph-$bar->uangmuka,0,",",".")."</td></tr>";
    $tab.="</table></td></tr>";

        $tab.="<tr><td colspan=5>Terbilang : ".terbilang((($nilai)-($potTotal))+($nilaippn)-$nilaipph-$bar->uangmuka,0,",",".")." Rupiah</td></tr>";
        $tab.="<tr><td colspan=4>Keterangan</td><td align='center'>Hormat Kami</td></tr>";
        $tab.="<tr><td colspan=4>Pembayaran di transfer ke Rekening</td><td></td></tr>";
        $sakun = "select distinct noakun,namaakun from ".$dbname.".keu_5akun  where noakun=".$bar->bayarke." order by namaakun asc";
$qakun = mysql_query($sakun);
$rakun = mysql_fetch_assoc($qakun);
                $tab.="<tr><td colspan=4>".$rakun['namaakun']."</td><td></td></tr>";
                $tab.="<tr><td colspan=4>A/N ". $namapt."</td><td></td></tr>";
                
                $strKar = 'select namakaryawan from '.$dbname.'.'."datakaryawan where karyawanid='".$kodekaryawan."'";
$resKar = mysql_query($strKar);
$barKar = mysql_fetch_object($resKar);
        $tab.="<tr><td colspan=4></td><td align='center'>".$barKar->namakaryawan."</td></tr>";
//echo $tab;
$wktu = date('Hms');

$nop_ = 'Penagihan'.$wktu.'__'.date('Y');

    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');

    gzwrite($gztralala, $tab);

    gzclose($gztralala);

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
?>