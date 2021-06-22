<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';

if (!empty($_POST) )
{
    $param=$_POST;
} else {
    $param=$_GET;
}

$page=$_GET['page']==''? 1:$_GET['page'];
$limit=$_GET['limit']==''?10:$_GET['limit'];
$proses=$_GET['proses'];
$userlogin = $_SESSION['standard']['userid'];
$sql = "select * from setup_pengaturanadmin where userlogin='$userlogin'";
$jmlRow = getRowCount($sql);
$totpage = ceil($jmlRow / $limit);
$pagecaption=(($page-1) * $limit + 1).' to '.(($page-1) + 1) * $limit.' Of '.$jmlRow;
$prevBtnDisabled=$page==1 && $jmlRow==0;
$nextBtnDisabled=$page==$totpage && $jmlRow==0;
switch ($proses) {
    case "init":
        echo open_body();
        include 'master_mainMenu.php';
        OPEN_BOX('', '<b>Pengaturan Admin</b>');
        echo "<link rel=stylesheet type=text/css href='style/zTable.css'>
        <script language='javascript' src='js/zMaster.js?v=".mt_rand()."'></script>
        <script type='application/javascript' src='js/setup_pengaturanAdmin.js?v=".mt_rand()."'></script>
        <script> jdl_ats_0='";
        echo $_SESSION['lang']['find'];
        echo "';// alert(jdl_ats_0); jdl_ats_1='";
        echo $_SESSION['lang']['findBrg'];
        echo "'; content_0='<fieldset><legend>";
        echo $_SESSION['lang']['findnoBrg'];
        echo "</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';nmSaveHeader='';nmCancelHeader='';nmDetialDone='";
        echo $_SESSION['lang']['done'];
        echo "';nmDetailCancel='";
        echo $_SESSION['lang']['cancel'];
        echo "';</script>
        <input type='hidden' id='proses' name='proses' value='insert'  /><div id='headher'>";

        $str = "
        SELECT * FROM datakaryawan
        WHERE NOT (karyawanid IN
        (SELECT karyawanid FROM user))
        ORDER BY namakaryawan";

        $res = mysql_query($str);
        $optKary = "<option value=''>Pilih Karyawan</option>";
        while ($bar = mysql_fetch_object($res)) {
            $optKary .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->nik.']</option>';
        }

        echo "<fieldset style='float:left;'><legend>";
        echo $_SESSION['lang']['form'];
        echo "</legend><table cellspacing='1' border='0'>";

        echo "<tr><td>";
        echo "Nama karyawan ";
        echo "</td><td>:</td><td><select id='karyawanid' name='karyawanid' style='width:150px'>";
        echo $optKary;
        echo "</select></td></tr>";

        echo "<tr><td>";
        echo "Cuti ";
        echo "</td><td>:</td><td><input type='checkbox' id='cuti'/></td></tr>";

        echo "<tr><td>";
        echo "Perjalanan Dinas ";
        echo "</td><td>:</td><td><input type='checkbox' id='perjalanandinas'/></td></tr>";

        echo "<tr><td colspan='3' id='tmblHeader'>    <button class=mybutton id=dtlForm onclick=saveForm()>";
        echo $_SESSION['lang']['save'];
        echo "</button>    <button class=mybutton id=cancelForm onclick=cancelForm()>";
        echo $_SESSION['lang']['cancel'];
        echo "</button></td></tr></table> </fieldset>";
        CLOSE_BOX();
        echo "</div><div id='list_ganti'>";
        OPEN_BOX();
        echo "    <div id='action_list'></div><fieldset style='float:left;'><legend>";
        echo $_SESSION['lang']['list'];
        echo "</legend><table cellspacing='1' border='0' class='sortable'><thead><tr class='rowheader'><td>No.</td><td>";
        echo "Nama karyawan";
        echo "</td><td>";
        echo "Cuti";
        echo "</td><td>";
        echo "Perjalanan Dinas";
        echo "</td> <td>Action</td></tr></thead><tbody id='contain'>";

        $sql = "SELECT s.*,
        (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.userlogin) AS namauserlogin,
        (SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=s.karyawanid) AS namakaryawan
        FROM setup_pengaturanadmin s where s.userlogin='$userlogin'";
        $res = mysql_query($sql);
        $no=1;
        while ($row = mysql_fetch_assoc($res)) {
            $row_ = str_replace('"','\'',json_encode($row));
            echo "<tr class=\"rowcontent\">\r\n<td>";
            echo $no;
            echo "</td>\r\n<td>";
            echo $row['namakaryawan'];
            echo "</td>\r\n<td>";
            echo   ((int)$row['cuti']==1?"Ya":"Tidak");
            echo "</td>\r\n<td>";
            echo   ((int)$row['perjalanandinas']==1?"Ya":"Tidak");
            echo "</td>";
            echo "<td><img src=images/application/application_edit.png class=resicon  title='Edit' 
            onclick=\"var data = $row_; editData(data); \"    >
            <img src=images/application/application_delete.png class=resicon  title='Delete' 
            onclick=\"delData(".$row['id'].");\" ></td>";
            $no++;
        }

        echo "        <tr class=rowheader>
        <td colspan=5 align=center>        ".($jmlRow==0?'':$pagecaption)."<br />        
        <button id='prevButton' ".($jmlRow==0?"disabled ":$page==1?"disabled ":"")." onclick=\"gotoPage(".($page - 0).",".$totpage.",'prev');\">".$_SESSION['lang']['pref']."</button>        
        <button id='nextButton' ".($jmlRow==0?"disabled ":$page==$totpage?"disabled ":"")."   onclick=\"gotoPage(".($page + 1).",".$totpage.",'next');\">".$_SESSION['lang']['lanjut']."</button>        </td>        </tr>";
        echo "</tbody></table></fieldset>";
        CLOSE_BOX();
        echo "</div>";
        echo close_body();
        break;
    case "transaction": 
        $karyawanid= $param['karyawanid'];
        $cuti= $param['cuti'];
        $perjalanandinas= $param['perjalanandinas'];
        $sql = "select * from setup_pengaturanadmin where userlogin='$userlogin' and karyawanid='$karyawanid'";
        $jml = getRowCount($sql);
        $dml ='';
        if ($jml==0){
            $dml="insert into setup_pengaturanadmin(userlogin,karyawanid,cuti,perjalanandinas) value('$userlogin','$karyawanid',$cuti,$perjalanandinas)";
        } else {
            $dml="update setup_pengaturanadmin set cuti=$cuti,perjalanandinas=$perjalanandinas where userlogin='$userlogin' and karyawanid='$karyawanid' ";
        } 
        executeQuery2($dml);
        break;
    case "delete": 
        $id= $param['id'];
        $dml="delete from setup_pengaturanadmin where id = $id";
        executeQuery2($dml);
        break;
}
?>