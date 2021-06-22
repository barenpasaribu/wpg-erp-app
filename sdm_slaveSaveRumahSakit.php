<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$name = $_POST['name'];
$add = $_POST['add'];
$city = $_POST['city'];
$phone = $_POST['phone'];
$mail = $_POST['mail'];
$status = $_POST['status'];
$id = $_POST['id'];
if (isset($_POST['name']) && !isset($_POST['del']) && !isset($_POST['update'])) {
    $str = 'insert into '.$dbname.".sdm_5rs(\r\n\t  namars,alamat,telp,kota,email,status)\r\n\t  values(\r\n\t\t'".$name."','".$add."','".$phone."',\r\n\t\t'".$city."','".$mail."',".$status."\r\n\t  )";
} else {
    if (isset($_POST['update'])) {
        $str = 'update '.$dbname.".sdm_5rs\r\n\t      set namars='".$name."',\r\n\t\t  alamat='".$add."',\r\n\t\t  email='".$mail."',\r\n\t\t  telp='".$phone."',\r\n\t\t  kota='".$city."',\r\n\t\t  status=".$status."\r\n\t\t  where id=".$id;
    } else {
        if (isset($_POST['del'])) {
            $str = 'delete from '.$dbname.".sdm_5rs where\r\n\t  id =".$id;
        } else {
            $str = 'select 1=1';
        }
    }
}

if (mysql_query($str)) {
    $std = "select *, case status when 1 then 'Active' when 0 then 'Black List' end as xstatus\r\n\t\t  from ".$dbname.'.sdm_5rs order by namars';
    $res = mysql_query($std);
    $no = 0;
    while ($bad = mysql_fetch_object($res)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n\t\t\t  <td class=firsttd>".$no."</td>\r\n\t\t\t  <td>".$bad->namars."</td>\r\n\t\t\t  <td>".$bad->alamat."</td>\r\n\t\t\t  <td>".$bad->kota."</td>\r\n\t\t\t  <td>".$bad->telp."</td>\r\n\t\t\t  <td>".$bad->email."</td>\r\n\t\t\t  <td>".$bad->xstatus."</td>\r\n\t\t      <td align=center>\r\n\t\t\t     <img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editHospital('".$bad->id."','".$bad->namars."','".$bad->kota."','".$bad->alamat."','".$bad->telp."','".$bad->email."','".$bad->status."')\">\r\n\t\t         <img src=images/close.png class=dellicon title=delete height=11px onclick=\"deleteHospital('".$bad->id."');\">\r\n\t\t\t  </td>\r\n\t\t\t</tr>";
    }
} else {
    echo ' Gagal,'.addslashes(mysql_error($conn));
}

?>