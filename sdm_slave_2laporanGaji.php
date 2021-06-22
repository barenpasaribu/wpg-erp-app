<?php
require_once('master_validation.php');
require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/eagrolib.php');

$unit=$_POST['unit'];
$subunit=$_POST['subunit'];
$jenis=$_POST['jenis'];
$periode=$_POST['periode'];
$dept=$_POST['dept'];
$gol=$_POST['gol'];

if($unit==''){
    echo"warning: Working unit required";exit();
}
if($periode==''){
	echo "Warning: periode required"; exit;
}

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

$str="	SELECT distinct a.karyawanid, nik, namakaryawan, namajabatan, tipe, kodegolongan, tanggalmasuk FROM sdm_gaji a inner join datakaryawan b on a.karyawanid=b.karyawanid INNER JOIN sdm_5jabatan c ON b.kodejabatan=c.kodejabatan INNER JOIN sdm_5tipekaryawan d ON b.tipekaryawan=d.id where periodegaji='".$periode."' AND kodeorg='".$unit."' ".$sistemgaji." ".$bagian." ".$golongan." ".$subbagian."  ";
//saveLog($str);
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
		$data[$karyawanid]['namajabatan']=$rows['namajabatan'];
		$data[$karyawanid]['tipe']=$rows['tipe'];
		$data[$karyawanid]['golongan']=$rows['kodegolongan'];
		$data[$karyawanid]['setahun']=$setahun;

		$str2="SELECT id,NAME, if(jumlah IS NULL ,0,jumlah) AS jumlah FROM sdm_gaji a RIGHT JOIN sdm_ho_component b ON a.idkomponen=b.id AND  a.karyawanid='".$rows['karyawanid']."' AND periodegaji='".$periode."' AND kodeorg='".$unit."' ";
		$res2 = mysql_query($str2);
		while ($rows2= mysql_fetch_array($res2)) {
			$id=$rows2['id'];
			if($id=='1' && ( $rows['kodegolongan']=='4' || $rows['kodegolongan']=='8' )){
				$data[$karyawanid][$id]=0;
			}else{
				$data[$karyawanid][$id]=$rows2['jumlah'];
			}
		}

}

$i=1;
$upah=0;
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
$thpbruto=$gross+$value['54']+$value['14']+$value['13']-$pph21;
$thpnetto=$thpbruto+$value['70']-$bpjs3-$value['25']-$value['52']-$value['11'];

echo "<tr class=rowcontent>
		<td align=center>".$i."</td>
		<td>".$periode."</td>
		<td>".$value['karyawanid']."</td>
		<td>".$value['nik']."</td>
		<td>".$value['namakaryawan']."</td>
		<td>".$value['namajabatan']."</td>
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

echo "<tr class=rowcontent>
		<td align=center colspan=8>T O T A L</td>
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
?>