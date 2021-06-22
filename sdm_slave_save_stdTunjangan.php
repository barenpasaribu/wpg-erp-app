<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodejabatan = $_POST['kodejabatan'];
$lokasi = $_POST['lokasi'];
$tjjabatan = $_POST['tjjabatan'];
$tjkota = $_POST['tjkota'];
$tjtransport = $_POST['tjtransport'];
$tjmakan = $_POST['tjmakan'];
$tjsdaerah = $_POST['tjsdaerah'];
$tjmahal = $_POST['tjmahal'];
$tjpembantu = $_POST['tjpembantu'];
$str = 'delete from '.$dbname.'.sdm_5stdtunjangan where jabatan='.$kodejabatan." and penempatan='".$lokasi."'";
mysql_query($str);
$str = 'insert into '.$dbname.".sdm_5stdtunjangan (\r\n      jabatan, penempatan, tjjabatan, tjkota, \r\n      tjtransport, tjmakan, tjsdaerah, tjmahal, \r\n      tjpembantu)\r\n      values(\r\n      ".$kodejabatan.",\r\n      '".$lokasi."',\r\n      ".$tjjabatan.",\r\n      ".$tjkota.",\r\n      ".$tjtransport.",\r\n      ".$tjmakan.",\r\n      ".$tjsdaerah.",\r\n      ".$tjmahal.",\r\n      ".$tjpembantu.');';
if (mysql_query($str)) {
    $str = 'select a.*,b.namajabatan from '.$dbname.'.sdm_5stdtunjangan a left join '.$dbname.'.sdm_5jabatan b on a.jabatan=b.kodejabatan order by penempatan,jabatan';
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n          <td>".$no."</td>\r\n          <td>".$bar->namajabatan."</td>\r\n          <td>".$bar->penempatan."</td>\r\n          <td>".$bar->tjjabatan."</td>\r\n          <td>".$bar->tjkota."</td>\r\n          <td>".$bar->tjtransport."</td>\r\n          <td>".$bar->tjmakan."</td>\r\n          <td>".$bar->tjsdaerah."</td>\r\n          <td>".$bar->tjmahal."</td>\r\n          <td>".$bar->tjpembantu."</td>\r\n          <td><img class='resicon' onclick=\"fillField('".$bar->jabatan."','".$bar->penempatan."','".$bar->tjjabatan."','".$bar->tjkota."','".$bar->tjtransport."','".$bar->tjmakan."','".$bar->tjsdaerah."','".$bar->tjmahal."','".$bar->tjpembantu."');\" title='Edit' src='images/application/application_edit.png'></td>\r\n          </tr>";
    }
} else {
    echo 'Error '.addslashes(mysql_error($conn));
    exit();
}

?>