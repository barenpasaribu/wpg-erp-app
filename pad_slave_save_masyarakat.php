<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$padid = $_POST['pid'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$desa = $_POST['desa'];
$kecamatan = $_POST['kecamatan'];
$kabupaten = $_POST['kabupaten'];
$ktp = $_POST['ktp'];
$hp = $_POST['hp'];
$method = $_POST['method'];
$unitbawah = $_POST['unitbawah'];

if ($method == '') {
	$method = $_GET['method'];
	$unitbawah = $_GET['unitbawah'];
}

switch ($method) {
case 'excel':
	$str1 = 'select a.*,b.unit from ' . $dbname . '.pad_5masyarakat a' . "\r\n" . '            left join ' . $dbname . '.pad_5desa b on a.desa=b.namadesa where b.unit like \'' . $unitbawah . '%\' order by a.desa,a.nama';

	if ($res1 = mysql_query($str1)) {
		$stream .= '<table class=sortable cellspacing=1 border=1>' . "\r\n" . '     <thead>' . "\r\n" . '             <tr bgcolor=\'#dedede\'>' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['kodeorg'] . '</td>              ' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . '</td>                    ' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['alamat'] . '</td>                        ' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['kecamatan'] . '</td>                  ' . "\r\n" . '                <td>' . $_SESSION['lang']['kabupaten'] . '</td>    ' . "\r\n" . '                <td>' . $_SESSION['lang']['noktp'] . '</td>             ' . "\r\n" . '                <td>' . $_SESSION['lang']['nohp'] . '</td>                       ' . "\r\n" . '      </thead>' . "\r\n" . '      <tbody>';

		while ($bar1 = mysql_fetch_object($res1)) {
			$stream .= '<tr class=rowcontent>' . "\r\n" . '                         <td>' . $bar1->unit . '</td>                    ' . "\r\n" . '                           <td>' . $bar1->nama . '</td>' . "\r\n" . '                           <td>' . $bar1->alamat . '</td>' . "\r\n" . '                           <td>' . $bar1->desa . '</td>                               ' . "\r\n" . '                           <td>' . $bar1->kecamatan . '</td>' . "\r\n" . '                           <td>' . $bar1->kabupaten . '</td>  ' . "\r\n" . '                           <td>' . $bar1->noktp . '</td>  ' . "\r\n" . '                           <td>' . $bar1->hp . '</td>                                 ' . "\r\n" . '                           </tr>';
		}

		$stream .= "\t" . ' ' . "\r\n" . '         </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\r\n" . '         </table><br>';
	}

	$stream .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$qwe = date('YmdHms');
	$nop_ = 'Daftar_Masyarakat_' . $unitbawah . ' ' . $qwe;

	if (0 < strlen($stream)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $stream);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
	}

	exit();
	break;

case 'update':
	$str = 'update ' . $dbname . '.pad_5masyarakat ' . "\r\n" . '           set nama=\'' . $nama . '\',' . "\r\n" . '            alamat=\'' . $alamat . '\',' . "\r\n" . '            desa=\'' . $desa . '\',               ' . "\r\n" . '            kecamatan=\'' . $kecamatan . '\',' . "\r\n" . '            kabupaten=\'' . $kabupaten . '\',' . "\r\n" . '            noktp=\'' . $ktp . '\',' . "\r\n" . '             hp=\'' . $hp . '\'' . "\r\n" . '            where padid=' . $padid;

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'insert':
	$str = 'insert into ' . $dbname . '.pad_5masyarakat (nama,alamat,desa,kecamatan,kabupaten,noktp,hp)' . "\r\n" . '              values(\'' . $nama . '\',\'' . $alamat . '\',\'' . $desa . '\',\'' . $kecamatan . '\',\'' . $kabupaten . '\',\'' . $ktp . '\',\'' . $hp . '\')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.pad_5masyarakat' . "\r\n" . '        where padid=\'' . $padid . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;
}

$str1 = ($padid = $_POST['pid']) . '.pad_5masyarakat a' . "\r\n" . '            left join ' . $dbname . '.pad_5desa b on a.desa=b.namadesa where b.unit like \'' . $unitbawah . '%\' order by a.desa,a.nama';

if ($res1 = mysql_query($str1)) {
	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n" . '             <tr class=rowheader>' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['kodeorg'] . '</td>              ' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . '</td>                    ' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['alamat'] . '</td>                        ' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['kecamatan'] . '</td>                  ' . "\r\n" . '                <td>' . $_SESSION['lang']['kabupaten'] . '</td>    ' . "\r\n" . '                <td>' . $_SESSION['lang']['noktp'] . '</td>             ' . "\r\n" . '                <td>' . $_SESSION['lang']['nohp'] . '</td>                       ' . "\r\n" . '               <td style=\'width:30px;\'>*</td></tr>    ' . "\r\n" . '      </thead>' . "\r\n" . '      <tbody>';

	while ($bar1 = mysql_fetch_object($res1)) {
		echo '<tr class=rowcontent>' . "\r\n" . '                         <td>' . $bar1->unit . '</td>                    ' . "\r\n" . '                           <td>' . $bar1->nama . '</td>' . "\r\n" . '                           <td>' . $bar1->alamat . '</td>' . "\r\n" . '                           <td>' . $bar1->desa . '</td>                               ' . "\r\n" . '                           <td>' . $bar1->kecamatan . '</td>' . "\r\n" . '                           <td>' . $bar1->kabupaten . '</td>  ' . "\r\n" . '                           <td>' . $bar1->noktp . '</td>  ' . "\r\n" . '                           <td>' . $bar1->hp . '</td>                                 ' . "\r\n" . '                           <td><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->padid . '\',\'' . $bar1->nama . '\',\'' . $bar1->alamat . '\',\'' . $bar1->desa . '\',\'' . $bar1->kecamatan . '\',\'' . $bar1->kabupaten . '\',\'' . $bar1->noktp . '\',\'' . $bar1->hp . '\');">' . "\r\n" . '                            </td></tr>';
	}

	echo "\t" . ' ' . "\r\n" . '         </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\r\n" . '         </table>';
}

?>
