<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$jabatan = $_POST['jabatan'];
$lokasitugas = $_POST['lokasitugas'];
$status = 'LOKASI';
$x = substr($lokasitugas, 2, 2);
if ('RO' == $x || 'HO' == $x) {
    $status = 'KOTA';
}

$str = 'SELECT * FROM '.$dbname.'.sdm_5stdtunjangan  where jabatan='.$jabatan." and penempatan='".$status."'";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    while ($bar = mysql_fetch_object($res)) {
        echo "<?xml version='1.0' ?>\r\n\t     <tunjangan>\r\n\t\t\t <tjjabatan>".(('' != $bar->tjjabatan ? $bar->tjjabatan : '*'))."</tjjabatan>\r\n\t\t\t <tjkota>".(('' != $bar->tjkota ? $bar->tjkota : '*'))."</tjkota>\r\n\t\t\t <tjtransport>".(('' != $bar->tjtransport ? $bar->tjtransport : '*'))."</tjtransport>\r\n\t\t\t <tjmakan>".(('' != $bar->tjmakan ? $bar->tjmakan : '*'))."</tjmakan>\r\n\t\t\t <tjsdaerah>".(('' != $bar->tjsdaerah ? $bar->tjsdaerah : '*'))."</tjsdaerah>\r\n\t\t\t <tjmahal>".(('' != $bar->tjmahal ? $bar->tjmahal : '*'))."</tjmahal>\r\n\t\t\t <tjpembantu>".(('' != $bar->tjpembantu ? $bar->tjpembantu : '*'))."</tjpembantu>\r\n\t   </tunjangan>";
    }
} else {
    echo "<?xml version='1.0' ?>\r\n\t     <tunjangan>\r\n\t\t\t <tjjabatan>0</tjjabatan>\r\n\t\t\t <tjkota>0</tjkota>\r\n\t\t\t <tjtransport>0</tjtransport>\r\n\t\t\t <tjmakan>0</tjmakan>\r\n\t\t\t <tjsdaerah>0</tjsdaerah>\r\n\t\t\t <tjmahal>0</tjmahal>\r\n\t\t\t <tjpembantu>0</tjpembantu>\r\n\t   </tunjangan>";
}

?>