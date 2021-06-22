<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$id = $_POST['id'];
$str = 'delete from '.$dbname.'.sdm_ho_component where id='.$id;
if (mysql_query($str)) {
    $str = 'select * from '.$dbname.'.sdm_ho_component order by id';
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo '<tr class=rowcontent><td class=fisttd>'.$no."</td>\r\n\t\t\t      <td>".$bar->name."</td>\r\n\t\t\t      <td>".((1 == $bar->plus ? $_SESSION['lang']['penambah'] : $_SESSION['lang']['pengurang']))."</td>\r\n\t\t\t\t  <td>".$bar->type."</td>\r\n\t\t\t\t  <td>".((1 == $bar->lock ? $_SESSION['lang']['dikunci'] : $_SESSION['lang']['inputbebas']))."</td>\r\n\t\t\t\t  <td align=center><img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editComp('".$bar->id."','".$bar->name."','".$bar->plus."','".$bar->type."','".$bar->lock."')\"> \r\n\t\t\t\t  <img src=images/close.png  height=11px class=dellicon title=Delete  onclick=\"delComp('".$bar->id."','".$bar->name."')\"></td>\r\n\t\t\t\t  </tr>";
    }
} else {
    echo ' Error: '.addslashes(mysql_error($conn));
}

?>