<style>
[data-tip] {
	position:relative;
}
[data-tip]:before {
	content:'';
	/* hides the tooltip when not hovered */
	display:none;
	content:'';
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #1a1a1a;	
	position:absolute;
	top:30px;
	left:35px;
	z-index:8;
	line-height:0;
	width:0;
	height:0;
}
[data-tip]:after {
	display:none;
	content:attr(data-tip);
	position:absolute;
	top:35px;
	left:0px;
	padding:5px 8px;
	background:#1a1a1a;
	color:#fff;
	z-index:9;
	height:18px;
	line-height:18px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	white-space:nowrap;
	word-wrap:normal;
}
[data-tip]:hover:before,
[data-tip]:hover:after {
	display:block;
}
</style>

<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = 0;
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = $_POST['kodeorg'];
$what = $_POST['what'];
if ('' === $tahunbudget) {
    echo 'WARNING: silakan mengisi tahun budget.';
    exit();
}

if (4 !== strlen($tahunbudget)) {
    echo 'WARNING: silakan mengisi tahun budget dengan benar.';
    exit();
}

if ('' === $kodeorg) {
    echo 'WARNING: silakan mengisi kode organisasi.';
    exit();
}
$query = "SELECT kodeorganisasi, namaorganisasi,induk FROM organisasi WHERE kodeorganisasi='".$kodeorg."'";
//echo $query; ambil nama organisasi dan kode;
$chOrg = mysql_query($query);
while($dtOrg= mysql_fetch_assoc($chOrg)){
	$kodeorganisasi = $dtOrg['kodeorganisasi'];
	$namaorganisasi = $dtOrg['namaorganisasi'];
	$indukorg = $dtOrg['induk'];
}
//periksa data upah di table jida data baru ambil data dari induk org (atau dari tahun sebelumnya)
$query ="SELECT kodegolongan from bgt_upah WHERE kodeorg ='".$kodeorganisasi."' and tahunbudget=".$tahunbudget;
$runChk = mysql_query($query);
$row= mysql_num_rows($runChk);
if($row >= 1){
	$proses='edit';
	$str2 = "SELECT golongan, jumlah FROM ".$dbname.".bgt_upah where tahunbudget='".$tahunbudget."' and kodeorg = '". $kodeorganisasi ."' and closed=0 order by golongan";
	$ket='Nilai yang telah disimpan';
}else{
	$proses='baru';
	$str2 = "SELECT golongan, jumlah FROM ".$dbname.".bgt_upah where tahunbudget='".$tahunbudget."' and kodeorg = '". $indukorg ."' and closed=0 order by golongan";
	$ket='Data Baru, turunan dari '.$indukorg;
}

//echo $str2;
$res2 = mysql_query($str2);
$rowUrg = mysql_num_rows($res2);
if($rowUrg <= 1){
	$tahunsblm = $tahunbudget -1;
	$proses='baru';
	$str2 = "SELECT golongan, jumlah FROM ".$dbname.".bgt_upah where tahunbudget='".$tahunsblm."' and kodeorg = '". $kodeorganisasi ."' and closed=0 order by golongan";
	$ket='Data Baru, turunan dari '.$kodeorganisasi. ' - ' .$tahunsblm;
	$res2 = mysql_query($str2);
}
if($rowUrg <= 1){
	$tahunsblm = $tahunbudget -1;
	$proses='baru';
	$str2 = "SELECT golongan, jumlah FROM ".$dbname.".bgt_upah where tahunbudget='".$tahunsblm."' and kodeorg = '". $indukorg ."' and closed=0 order by golongan";
	$ket='Data Baru, turunan dari '.$indukorg. ' - '.$tahunsblm;
	$res2 = mysql_query($str2);
}
while ($bar2 = mysql_fetch_object($res2)) {
    $isidata[$bar2->golongan][kodegolongan] = $bar2->golongan;
    $isidata[$bar2->golongan][upah] = $bar2->jumlah;
}
if($rowUrg <= 1){
	for($x=1;$x<=4;$x++){
		$isidata[$x][kodegolongan] = '';
		$isidata[$x][upah] = 0;
	}
	$ket='Isian belum pernah ada';
}

//=====================================
if ('closed' === $what || substr($_SESSION['empl']['lokasitugas'], 0, 4) !== $kodeorg) {
} else {
    echo '<button class=mybutton id=simpan onclick=simpanHarga(1)>'.$_SESSION['lang']['save'].'</button>';
}

if($proses='baru'){
	echo "<legend align=right style='font-colors=#800000'>Data Budget Baru Silakan diinput terlebih dahulu</legend>";
}

echo "<table cellspacing=1 border=0 class=sortable style='width:50%;'><thead>
	<tr class=\"rowheader\"><td style='width:5%;'>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>
	<td style='width:35%;'>".$_SESSION['lang']['kodeorg']."</td>
	<td style='width:15%;'>".$_SESSION['lang']['kodegolongan']."</td>
	<td style='width:25%;'>".$_SESSION['lang']['upahkerja']."</td>
	<td style='width:20%;'>".$_SESSION['lang']['action']."</td></tr></thead><tbody>";
foreach ($isidata as $baris) {
    ++$no;
    echo '<tr id=baris_'.$no.' class=rowcontent>';
    echo '<td>'.$no.'</td>';
    echo '<td><label id=kodeorg_'.$no.'>'.$kodeorganisasi .' - '.$namaorganisasi.'</td>';
    echo '<td><input type=text id=kodegolongan_'.$no.' value="'.$baris[kodegolongan].'" class=myinputtext  /></td>';
    //echo '<td>'.$baris[namagolongan].'</td>';	
/*     if ('closed' === $what || substr($_SESSION['empl']['lokasitugas'], 0, 4) !== $kodeorg) {
        echo '<td align=right>'.number_format($baris[upah]).'</td>';
        echo '<td></td>';
    } else {
    }  lepas sortir budget tutup dan periksa akses user  */
    echo '<td align=center><div data-tip="'.$ket.'"><input type=text id=upah_'.$no." value='".$baris[upah]."' class=myinputtext onkeypress=\"return angka_doang(event);\"></div></td>"; 
    echo '<td align=center><button class=mybutton onclick=simpanHargasatusatu('.$no.')>'.$_SESSION['lang']['save'].'</button></td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '<button class=mybutton id=simpan onclick=simpanHarga(1)>'.$_SESSION['lang']['save'].'</button>';
?>