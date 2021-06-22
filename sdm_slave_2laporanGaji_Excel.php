<?php
require_once('master_validation.php');
require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/eagrolib.php');
require_once 'lib/zLib.php';

$unit=$_GET['unit'];
$subunit=$_GET['subunit'];
$jenis=$_GET['jenis'];
$periode=$_GET['periode'];
$dept=$_GET['dept'];
$gol=$_GET['gol'];
//$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNm = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
if($dept!=''){
  $bagian="AND bagian='".$dept."'";
}

if($subunit!='ALL'){
  $subbagian="AND subbagian='".$subunit."'";
}

if($gol!=''){
  $golongan="AND kodegolongan='".$gol."'";
}

if($jenis!=''){
  $sistemgaji="AND sistemgaji='".$jenis."'";
}

		$stream.="Laporan Gaji : ".$unit." : ".$periode." <br>";
		$stream.="<table border=1>
			<tr>
			 <td align=center>No.</td>
        <td align=center>Periode</td>
        <td align=center>Karyawanid</td>
              <td align=center>NIK</td>  
              <td align=center>Nama Karyawan</td>    
              <td align=center>Nama Jabaatan</td>
              <td align=center>Departemen</td>
              <td align=center>STATUS</td>
              <td align=center>Golongan</td>
              <td align=center  bgcolor='f8ea06'>Gaji Pokok</td>
              <td align=center  bgcolor='f8ea06'>Tunjangan Golongan</td>
              <td align=center  bgcolor='f8ea06'>Tunjangan Jabatan</td>
              <td align=center  bgcolor='f8ea06'>Natura Pekerja</td>
              <td align=center  bgcolor='f8ea06'>Natura Keluarga</td>
              <td align=center  bgcolor='f8ea06'>Tunjangan Prestasi</td>
              <td align=center  bgcolor='f8ea06'>Total Upah</td>

              <td align=center  bgcolor='0099FF'>Lembur</td>
              <td align=center  bgcolor='0099FF'>Premi BKM</td>
              <td align=center  bgcolor='0099FF'>Pendapatan lain</td>
              <td align=center  bgcolor='0099FF'>Tunj. Kehadiran</td>
              <td align=center  bgcolor='0099FF'>Tunj. Harian</td>
              <td align=center  bgcolor='0099FF'>Tunj. Lainnya</td>

              <td align=center  bgcolor='FF99FF'>Potongan HK</td>
              <td align=center  bgcolor='FF99FF'>Denda BKM</td>
              <td align=center  bgcolor='FF99FF'>Potongan Absen</td>
              <td align=center  bgcolor='FF99FF'>Potongan Lainnya</td>

              <td align=center  bgcolor='CCCCCC'>GROSS</td>

              <td align=center  bgcolor='#66FFFF'>JKK</td>
              <td align=center  bgcolor='#66FFFF'>JKM</td>
              <td align=center  bgcolor='#66FFFF'>BPJS Kes.</td>

              <td align=center  bgcolor='#CCCCCC'>TOTAL GAJI (BRUTO)</td>

              <td align=center  bgcolor='#0099CC'>Rapel Kenaikan</td>
              <td align=center  bgcolor='#0099CC'>THR</td>
              <td align=center  bgcolor='#0099CC'>Bonus</td>

              <td align=center  bgcolor='#FF99FF'>Biaya Jabatan</td>
              <td align=center  bgcolor='#FF99FF'>JHT Kar.</td>
              <td align=center  bgcolor='#FF99FF'>JP Kar.</td>

              <td align=center  bgcolor='#CCCCCC'>Gaji Netto sebulan</td>
              <td align=center  bgcolor='#CCCCCC'>Gaji Netto setahun</td>

              <td align=center  bgcolor='#FFFF99'>Pph 21</td>
              <td align=center  bgcolor='#FFFF99'>Pph 21 THR</td>
              <td align=center  bgcolor='#FFFF99'>Pph 21 Bonus</td>

              <td align=center  bgcolor='#CCCCCC'>THP BRUTO</td>
              <td align=center  bgcolor='#FFFF00'>Insentif Pph 21</td>
              <td align=center  bgcolor='#FFFF99'>JP + JHT + BPJS Karyawan </td>
              
              <td align=center  bgcolor='#CCCC00'>Angsuran Pinjaman</td>
              <td align=center  bgcolor='#CCCC00'>Angsuran Egrek</td>
              <td align=center  bgcolor='#CCCC00'>Angsuran Angkong</td>

              <td align=center  bgcolor='#00CC66'>THP NETTO</td>  
		</tr>";

$str="  SELECT distinct a.karyawanid, nik, namakaryawan, namajabatan, lokasitugas, bagian, tipe, subbagian, kodegolongan, tanggalmasuk FROM sdm_gaji a inner join datakaryawan b on a.karyawanid=b.karyawanid INNER JOIN sdm_5jabatan c ON b.kodejabatan=c.kodejabatan INNER JOIN sdm_5tipekaryawan d ON b.tipekaryawan=d.id where periodegaji='".$periode."' AND kodeorg='".$unit."' ".$sistemgaji." ".$bagian." ".$golongan." ".$subbagian."  ";
//echo $str;
$res = mysql_query($str);
$awaltahun=substr($periode,0,4)."-01-01";
  while ($rows= mysql_fetch_array($res)) {
//echo substr($rows['tanggalmasuk'],5,2)." - ";
if($rows['tanggalmasuk']<$awaltahun){
$setahun=12;
}else{
$setahun=13-substr($rows['tanggalmasuk'],5,2);
}

    $karyawanid=$rows['karyawanid'];    
    $data[$karyawanid]['karyawanid']=$karyawanid;
    $data[$karyawanid]['nik']=$rows['nik'];
    $data[$karyawanid]['namakaryawan']=$rows['namakaryawan'];
    $data[$karyawanid]['bagian']=$rows['bagian'];
    $data[$karyawanid]['lokasitugas']=$rows['lokasitugas'];
    $data[$karyawanid]['namajabatan']=$rows['namajabatan'];
    //$data[$karyawanid]['subbagian']=$optNm[$rows['subbagian']];
    $data[$karyawanid]['subbagian']=$rows['subbagian'];
    $data[$karyawanid]['tipe']=$rows['tipe'];
    $data[$karyawanid]['golongan']=$rows['kodegolongan'];
    $data[$karyawanid]['setahun']=$setahun;

    $str2="SELECT id,NAME, if(jumlah IS NULL ,0,jumlah) AS jumlah FROM sdm_gaji a RIGHT JOIN sdm_ho_component b ON a.idkomponen=b.id AND  a.karyawanid='".$rows['karyawanid']."' AND periodegaji='".$periode."' AND kodeorg='".$unit."' ";
    $res2 = mysql_query($str2);
    while ($rows2= mysql_fetch_array($res2)) {
      $id=$rows2['id'];
      if($id=='1' && $rows['kodegolongan']=='BHL2'){
        $data[$karyawanid][$id]=0;
      }else{
        $data[$karyawanid][$id]=$rows2['jumlah'];
      }
    }

  }
$i=1;
  foreach ($data as $id => $value) {
$upah=$value['1']+$value['2']+$value['4']+$value['29']+$value['30']+$value['32']+$value['33']+$value['15'];
$tunjangan=$value['17']+$value['16']+$value['58']+$value['61']+$value['21']+$value['23'];
$potongan=$value['20']+$value['26']+$value['64']+$value['27'];
$bpjs=$value['6']+$value['7']+$value['57'];
$bruto=$upah+$tunjangan-$potongan+$bpjs;
$byjabatan=floor($bruto*0.05);
if($byjabatan>'500000'){
  $byjabatan='500000';
}
$gross=$upah+$tunjangan-$potongan;
$bpjs2=$byjabatan+$value['5']+$value['9'];
$nettosebulan=$bruto+$value['54']+$value['14']+$value['13']-$bpjs2;
$nettosetahun=$nettosebulan*$value['setahun'];
$pph21=$value['24']+0+$value['71']+$value['72'];
$bpjs3=$value['8']+$value['5']+$value['9'];
$thpbruto=$gross-$pph21;
$thpnetto=$thpbruto+$value['70']-$bpjs3-$value['25']-$value['52']-$value['11'];
//<td>".$value['subbagian']."</td>
$stream .= "<tr class=rowcontent>
    <td align=center>".$i."</td>
    <td>".$periode."</td>
    <td>".$value['karyawanid']."</td>
    <td>".$value['nik']."</td>
    <td>".$value['namakaryawan']."</td>
    <td>".$value['namajabatan']."</td>
    <td>".$optNm[$value['bagian']]."</td>
    
    <td>".$value['tipe']."</td>
    <td>".$value['golongan']." </td>

    <td align='right' bgcolor='f8ea06'>".number_format($value['1'])."</td>
    <td align='right' bgcolor='f8ea06'>0,00</td>
    <td align='right' bgcolor='f8ea06'>".number_format($value['2'])."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($value['4'])."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($value['29']+$value['30']+$value['32']+$value['33'])."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($value['15'])."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($upah)."</td>

    <td align='right' bgcolor='#0099FF'>".number_format($value['17'])."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($value['16'])."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($value['58'])."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($value['61'])."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($value['21'])."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($value['23'])."</td>

    <td align='right' bgcolor='#FF99FF'>".number_format($value['20'])."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($value['26'])."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($value['64'])."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($value['27'])."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($upah+$tunjangan-$potongan)."</td>

    <td align='right' bgcolor='#66FFFF'>".number_format($value['6'])."</td>
    <td align='right' bgcolor='#66FFFF'>".number_format($value['7'])."</td>
    <td align='right' bgcolor='#66FFFF'>".number_format($value['57'])."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($bruto)."</td>


    <td align='right' bgcolor='#0099CC'>".number_format($value['54'])."</td>
    <td align='right' bgcolor='#0099CC'>".number_format($value['14'])."</td>
    <td align='right' bgcolor='#0099CC'>".number_format($value['13'])."</td>

    <td align='right' bgcolor='#FF99FF'>".number_format($byjabatan)."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($value['5'])."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($value['9'])."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($nettosebulan)."</td>
    <td align='right' bgcolor='#CCCCCC'>".number_format($nettosetahun)."</td>

    <td align='right' bgcolor='#FFFF99'>".number_format($value['24'])."</td>
    <td align='right' bgcolor='#FFFF99'>".number_format($value['71'])."</td>
    <td align='right' bgcolor='#FFFF99'>".number_format($value['72'])."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($thpbruto)."</td>
    
    <td align='right' bgcolor='#FFFF00'>".number_format($value['70'])."</td>
    
    <td align='right' bgcolor='#FFFF99'>".number_format($bpjs3)."</td>

    <td align='right' bgcolor='#CCCC00'>".number_format($value['25'])."</td>
    <td align='right' bgcolor='#CCCC00'>".number_format($value['52'])."</td>
    <td align='right' bgcolor='#CCCC00'>".number_format($value['11'])."</td>

    <td align='right' bgcolor='#00CC66'>".number_format($thpnetto)."</td>

  </tr>";
  
  $totgapok+=$value['1'];
  $tottunjabatan+=$value['2'];
  $totnatpekerja+=$value['4'];
  $totnatkeluarga+=$value['29']+$value['30']+$value['32']+$value['33'];
  $tottunprestasi+=$value['15'];
  $totupah+=$upah;

  $totlembur+=$value['17'];
  $totpremi+=$value['16'];
  $totpendapatanlain+=$value['58'];
  $tottunjkehadiran+=$value['61'];
  $tottunjharian+=$value['21'];
  $tottunjlainnya+=$value['23'];

  $totpothk+=$value['20'];
  $totdendabkm+=$value['26'];
  $totpotabsen+=$value['64'];
  $totpotlainnya+=$value['27'];

  $totgross+=$upah+$tunjangan-$potongan;

  $totjkk+=$value['6'];
  $totjkm+=$value['7'];
  $totbpjskes+=$value['57'];

  $totbruto+=$bruto;

  $totrapel+=$value['54'];
  $totthr+=$value['14'];
  $totbonus+=$value['13'];
  
  $totbiayajab+=$byjabatan;
  $totjhtkar+=$value['5'];
  $totjpkar+=$value['9'];

  $totnettosebulan+=$nettosebulan;
  $totnettosetahun+=$nettosetahun;

  $totpph21+=$value['24'];
  $totpph21rapel+=0;
  $totpph21thr+=$value['71'];
  $totpph21bonus+=$value['72'];

  $totthpbruto+=$thpbruto;

  $totpph21insentif+=$value['70'];
  $totpotbpjs+=$bpjs3;
  $totangpin+=$value['25'];
  $totangengrek+=$value['52'];
  $totangang+=$value['11'];
  $totthpnetto+=$thpnetto;


$i++;
}

$stream .= "<tr class=rowcontent>
    <td align=center colspan=9>T O T A L</td>
    <td align='right' bgcolor='f8ea06'>".number_format($totgapok)."</td>
    <td align='right' bgcolor='f8ea06'>0,00</td>
    <td align='right' bgcolor='f8ea06'>".number_format($tottunjabatan)."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($totnatpekerja)."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($totnatkeluarga)."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($tottunprestasi)."</td>
    <td align='right' bgcolor='f8ea06'>".number_format($totupah)."</td>

    <td align='right' bgcolor='#0099FF'>".number_format($totlembur)."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($totpremi)."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($totpendapatanlain)."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($tottunjkehadiran)."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($tottunjharian)."</td>
    <td align='right' bgcolor='#0099FF'>".number_format($tottunjlainnya)."</td>

    <td align='right' bgcolor='#FF99FF'>".number_format($totpothk)."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($totdendabkm)."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($totpotabsen)."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($totpotlainnya)."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($totgross)."</td>

    <td align='right' bgcolor='#66FFFF'>".number_format($totjkk)."</td>
    <td align='right' bgcolor='#66FFFF'>".number_format($totjkm)."</td>
    <td align='right' bgcolor='#66FFFF'>".number_format($totbpjskes)."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($totbruto)."</td>

    <td align='right' bgcolor='#0099CC'>".number_format($totrapel)."</td>
    <td align='right' bgcolor='#0099CC'>".number_format($totthr)."</td>
    <td align='right' bgcolor='#0099CC'>".number_format($totbonus)."</td>

    <td align='right' bgcolor='#FF99FF'>".number_format($totbiayajab)."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($totjhtkar)."</td>
    <td align='right' bgcolor='#FF99FF'>".number_format($totjpkar)."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($totnettosebulan)."</td>
    <td align='right' bgcolor='#CCCCCC'>".number_format($totnettosetahun)."</td>

    <td align='right' bgcolor='#FFFF99'>".number_format($totpph21)."</td>
    <td align='right' bgcolor='#FFFF99'>".number_format($totpph21thr)."</td>
    <td align='right' bgcolor='#FFFF99'>".number_format($totpph21bonus)."</td>

    <td align='right' bgcolor='#CCCCCC'>".number_format($totthpbruto)."</td>

    <td align='right' bgcolor='#FFFF00'>".number_format($totpph21insentif)."</td>
    
    <td align='right' bgcolor='#FFFF99'>".number_format($totpotbpjs)."</td>

    <td align='right' bgcolor='#CCCC00'>".number_format($totangpin)."</td>
    <td align='right' bgcolor='#CCCC00'>".number_format($totangegrek)."</td>
    <td align='right' bgcolor='#CCCC00'>".number_format($totangang)."</td>

    <td align='right' bgcolor='#00CC66'>".number_format($totthpnetto)."</td>


    "
    ;

$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'LaporanGaji'.date('YmdHis');

if (0 < strlen($stream)) {
  if ($handle = opendir('tempExcel')) {
    while (false !== $file = readdir($handle)) {
      if (($file != '.') && ($file != '..')) {
        @unlink('tempExcel/' . $file);
      }
    }

    closedir($handle);
  }

  $handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

  if (!fwrite($handle, $stream)) {
    echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
    exit();
  }
  else {
    echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
  }

  closedir($handle);
}
?>