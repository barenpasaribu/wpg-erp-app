<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include 'lib/zMysql.php';
include_once 'lib/zLib.php';
$method = $_POST['method'];
$tanggalkeluar = tanggalsystem($_POST['tanggalkeluar']);
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$karyawanid = $_POST['karyawanid'];
$alasan = $_POST['alasan'];
    $arr[11] = "Angkong";
    $arr[25] = "Pinjaman";
    $arr[52] = "Engrek";
	$arr[]="Lain-lain";
switch ($method) {
    case 'getFormKeluar':
        echo "<div id=laporan style='width:100%; height:340px;overflow:scroll;'><table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>
		<td colspan=3 align=center align=center>".$_SESSION['lang']['alasankeluar']."</td></tr></thead>";
        echo "<tr class=rowcontent><td>".$_SESSION['lang']['alasankeluar']."</td>
		<td>:</td><td><textarea name=textarea id=alasan cols=45 rows=5></textarea></td></tr>";
        echo "<tr class=rowcontent><td colspan=3 align=center><button class=mybutton onclick=saveFormKeluar()>".$_SESSION['lang']['save']."</button></td></tr></table>";
		echo "<table class=sortable width=100% border=0 cellspacing=1><thead><tr class=rowheader>
		<td align=center>No.</td>
		<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
		<td align=center>".$_SESSION['lang']['lokasitugas']."</td>
		<td align=center>".$_SESSION['lang']['jennisangsuran']."</td>
		<td align=center>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>
		<td align=center>".$_SESSION['lang']['bulanawal']."</td>
		<td align=center>".$_SESSION['lang']['sampai']."</td>
		<td align=center>".$_SESSION['lang']['jumlah'].'<br>('.$_SESSION['lang']['bulan'].")</td>
		<td align=center>".$_SESSION['lang']['potongan'].'/'.$_SESSION['lang']['bulan'].".<br>(Rp.)</td>
		<td align=center>".$_SESSION['lang']['status']."</td>
		</tr> </thead>";
		echo "<tbody id=tbody>";

		$sqlAngsuran = "SELECT a.*, b.namakaryawan, b.lokasitugas FROM sdm_angsuran a
		LEFT JOIN datakaryawan b ON a.karyawanid=b.karyawanid WHERE a.karyawanid='".$karyawanid."' AND active=1";
		//echo $sqlAngsuran;
		$resAngsr = mysql_query($sqlAngsuran, $conn);
		$no = 0;
		while ($bar = mysql_fetch_object($resAngsr)) {
			++$no;
			echo "<tr class=rowcontent>
			<td class=firsttd>".$no."</td>
			<td>".$bar->namakaryawan."</td>
			<td>".$bar->lokasitugas.' -- '.$nmOrg[$bar->lokasitugas]." </td>
			<td>".$arr[$bar->jenis]."</td>
			<td align=right>".number_format($bar->total, 2, '.', ',')."</td>
			<td align=center>".$bar->start."</td>
			<td align=center>".$bar->end."</td>
			<td align=right>".$bar->jlhbln."</td>
			<td align=right>".number_format($bar->bulanan, 2, '.', ',')."</td>
			<td align=center>".((1 == $bar->active ? 'Active' : 'Not Active'))."</td>";
			//<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\"><img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\"></td></tr>";
		}
		echo "</tbody></table></div>";
        break;
    case 'saveFormKeluar':
        $i = "delete from sdm_exitinterview where karyawanid='".$karyawanid."'";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal Hapus ALASAN KELUAR yang sudah ada sebelumnya, '.addslashes(mysql_error($conn));
        }

        $i = 'insert into '.$dbname.".sdm_exitinterview (karyawanid,tanggal,alasan,updateby) \r\n\t\t\tvalues ('".$karyawanid."','".$tanggalkeluar."','".$alasan."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal Insert ALASAN KELUAR, '.addslashes(mysql_error($conn));
        }

        break;
}

?>