<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$kodeorg = $_POST['kodeorg'];
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.'.organisasi';
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $namaorg[$rOrg['kodeorganisasi']] = $rOrg['namaorganisasi'];
}
$sOrg = 'select kodejabatan,namajabatan from '.$dbname.'.sdm_5jabatan';
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $kamusjabatan[$rOrg['kodejabatan']] = $rOrg['namajabatan'];
}
$kamusstatus[0] = 'Tidak Aktif';
$kamusstatus[1] = 'Aktif';
$dzArr = [];
$sPrasarana = 'SELECT a.karyawanid, a.status, b.kodejabatan, b.subbagian, b.namakaryawan FROM '.$dbname.".user a\r\n    LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid\r\n    WHERE b.lokasitugas like '".$kodeorg."%'\r\n    ORDER BY b.namakaryawan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $dakar[$res['karyawanid']] = $res['karyawanid'];
    $dzArr[$res['karyawanid']]['id'] = $res['karyawanid'];
    $dzArr[$res['karyawanid']]['nm'] = $res['namakaryawan'];
    $dzArr[$res['karyawanid']]['st'] = $res['status'];
    $dzArr[$res['karyawanid']]['jb'] = $res['kodejabatan'];
    $dzArr[$res['karyawanid']]['sb'] = $res['subbagian'];
}
if (empty($dakar)) {
    echo 'Data Empty.';
    exit();
}

echo "<table width=100% cellspacing='1' border='0' class='sortable'>\r\n<thead class=rowheader> \r\n<tr>\r\n<td align=center>".$_SESSION['lang']['nourut']."</td>\r\n<td align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n<td align=center>".$_SESSION['lang']['jabatan']."</td>\r\n<td align=center>".$_SESSION['lang']['subbagian']."</td>\r\n<td align=center>".$_SESSION['lang']['status']."</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
$dummy = '';
if (!empty($dakar)) {
    foreach ($dakar as $data) {
        ++$nomnom;
        echo '<tr class=rowcontent>';
        echo '<td align=right>'.$nomnom.'</td>';
        echo '<td>'.$dzArr[$data]['nm'].'</td>';
        echo '<td>'.$kamusjabatan[$dzArr[$data]['jb']].'</td>';
        echo '<td>'.$namaorg[$dzArr[$data]['sb']].'</td>';
        echo '<td>'.$kamusstatus[$dzArr[$data]['st']].'</td>';
        echo '</tr>';
    }
}

echo '</tbody></table>';

?>