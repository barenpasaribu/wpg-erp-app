<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script languange=javascript1.2 src=\'js/zSearch.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formTable.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formReport.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/zGrid.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/pmn_fakturPajak.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=\'style/zTable.css\'>' . "\r\n";
$optkodept = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$s_kodept = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($q_kodept = mysql_query($s_kodept)) || true;

while ($r_kodept = mysql_fetch_assoc($q_kodept)) {
	$optkodept .= '<option value=\'' . $r_kodept['kodeorganisasi'] . '\'>' . $r_kodept['namaorganisasi'] . '</option>';
}

$optcust = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$s_cust = 'select kodecustomer,namacustomer,kodetimbangan from ' . $dbname . '.pmn_4customer order by namacustomer asc';

#exit(mysql_error($conn));
($q_cust = mysql_query($s_cust)) || true;

while ($r_cust = mysql_fetch_assoc($q_cust)) {
	$optcust .= '<option value=\'' . $r_cust['kodecustomer'] . '\'>' . $r_cust['namacustomer'] . '</option>';
}

$optkomoditi = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$s_komoditi = 'select distinct a.kodebarang as kodebarang,b.namabarang as namabarang from ' . $dbname . '.pmn_kontrakjual a' . "\r\n" . '             left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '             order by b.namabarang asc';

#exit(mysql_error($conn));
($q_komoditi = mysql_query($s_komoditi)) || true;

while ($r_komoditi = mysql_fetch_assoc($q_komoditi)) {
	$optkomoditi .= '<option value=\'' . $r_komoditi['kodebarang'] . '\'>' . $r_komoditi['namabarang'] . '</option>';
}

$optkontrak = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optjenispajak = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$s_jnspajak = 'select * from ' . $dbname . '.pmn_5fakturkode ';

#exit(mysql_error($conn));
($q_jnspajak = mysql_query($s_jnspajak)) || true;

while ($r_jnspajak = mysql_fetch_assoc($q_jnspajak)) {
	$kodepajak = $r_jnspajak['kode'];

	if (strlen($kodepajak) < 3) {
		$jp = '0' . $kodepajak;
	}
	else {
		$jp = $kodepajak;
	}

	$optjenispajak .= '<option value=\'' . $r_jnspajak['kode'] . '\'>' . $jp . ' - ' . $r_jnspajak['nama'] . '</option>';
}

$optbiaya = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$arrbiaya = getEnum($dbname, 'pmn_faktur', 'atasbiaya');

foreach ($arrbiaya as $atasbiaya => $faktur) {
	$optbiaya .= '<option value=\'' . $atasbiaya . '\'>' . $faktur . '</option>';
}

$opt_ttd = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$s_ttd = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where kodejabatan=11 ';

#exit(mysql_error($conn));
($q_ttd = mysql_query($s_ttd)) || true;

while ($r_ttd = mysql_fetch_assoc($q_ttd)) {
	$opt_ttd .= '<option value=\'' . $r_ttd['karyawanid'] . '\'>' . $r_ttd['namakaryawan'] . '</option>';
}

$frm[0] = '';
$frm[1] = '';
$frm .= 0;
$kontrak = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$bulan = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$rekanan = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$s_kontrak = 'select distinct nokontrak from ' . $dbname . '.pmn_faktur order by nokontrak asc';

#exit(mysql_error($conn));
($q_kontrak = mysql_query($s_kontrak)) || true;

while ($r_kontrak = mysql_fetch_assoc($q_kontrak)) {
	$kontrak .= '<option value=' . $r_kontrak['nokontrak'] . '>' . $r_kontrak['nokontrak'] . '</option>';
}

$s_partner = 'select distinct partner from ' . $dbname . '.pmn_faktur order by partner asc';

#exit(mysql_error($conn));
($q_partner = mysql_query($s_partner)) || true;

while ($r_partner = mysql_fetch_assoc($q_partner)) {
	$rekanan .= '<option value=' . $r_partner['partner'] . '>' . $r_partner['partner'] . '</option>';
}

$s_bln = 'select distinct substr(tanggalfaktur,1,7) as bulan from ' . $dbname . '.pmn_faktur order by tanggalfaktur asc';

#exit(mysql_error($conn));
($q_bln = mysql_query($s_bln)) || true;

while ($r_bln = mysql_fetch_assoc($q_bln)) {
	$bulan .= '<option value=' . $r_bln['bulan'] . '>' . $r_bln['bulan'] . '</option>';
}

$frm .= 1;
$frm .= 1;
$frm .= 1;

if ($_SESSION['language'] == 'ID') {
	$hfrm[0] = 'Buat Faktur';
	$hfrm[1] = 'Daftar Faktur';
}
else {
	$hfrm[0] = $_SESSION['lang']['baru'];
	$hfrm[1] = $_SESSION['lang']['list'];
}

drawTab('FRM', $hfrm, $frm, 100, 1000);
echo "\r\n";
CLOSE_BOX();

?>
