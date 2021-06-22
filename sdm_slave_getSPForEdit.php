<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$nosp = $_POST['nosp'];
$karyawanid = $_POST['karyawanid'];
$str = 'select * from '.$dbname.".sdm_suratperingatan\r\n      where karyawanid=".$karyawanid." and nomor='".$nosp."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    echo "<?xml version='1.0' ?>\r\n             <sp>\r\n                         <jenissp>".(('' != $bar->jenissp ? $bar->jenissp : '*'))."</jenissp>\r\n                         <karyawanid>".(('' != $bar->karyawanid ? $bar->karyawanid : '*'))."</karyawanid>\r\n                         <tanggal>".(('' != $bar->tanggal ? tanggalnormal($bar->tanggal) : '*'))."</tanggal>\r\n                         <masaberlaku>".(('' != $bar->masaberlaku ? $bar->masaberlaku : '*'))."</masaberlaku>\r\n                         <paragraf1>".(('' != $bar->paragraf1 ? $bar->paragraf1 : '*'))."</paragraf1>\r\n                         <pelanggaran>".(('' != $bar->pelanggaran ? $bar->pelanggaran : '*'))."</pelanggaran>\r\n                     <paragraf3>".(('' != $bar->paragraf3 ? $bar->paragraf3 : '*'))."</paragraf3>\r\n                         <paragraf4>".(('' != $bar->paragraf4 ? $bar->paragraf4 : '*'))."</paragraf4>\r\n                         <penandatangan>".(('' != $bar->penandatangan ? $bar->penandatangan : '*'))."</penandatangan>\r\n                         <jabatan>".(('' != $bar->jabatan ? $bar->jabatan : '*'))."</jabatan>\r\n                         <tembusan1>".(('' != $bar->tembusan1 ? $bar->tembusan1 : '*'))."</tembusan1>\r\n                         <tembusan2>".(('' != $bar->tembusan2 ? $bar->tembusan2 : '*'))."</tembusan2>\r\n                         <tembusan3>".(('' != $bar->tembusan3 ? $bar->tembusan3 : '*'))."</tembusan3>\r\n                         <tembusan4>".(('' != $bar->tembusan4 ? $bar->tembusan4 : '*'))."</tembusan4>\r\n                         <nomor>".(('' != $bar->nomor ? $bar->nomor : '*'))."</nomor>\r\n                         <verifikasi>".(('' != $bar->verifikasi ? $bar->verifikasi : '*'))."</verifikasi>\r\n                         <dibuat>".(('' != $bar->dibuat ? $bar->dibuat : '*'))."</dibuat>\r\n                         <jabatandibuat>".(('' != $bar->jabatandibuat ? $bar->jabatandibuat : '*'))."</jabatandibuat>\r\n                         <jabatanverifikasi>".(('' != $bar->jabatanverifikasi ? $bar->jabatanverifikasi : '*'))."</jabatanverifikasi>    \r\n                 </sp>";
}

?>