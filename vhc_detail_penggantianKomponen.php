<?php
// include_once 'lib/nangkoelib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
include_once 'config/connection.php';
$param=$_POST;
if (empty($_POST)) $param=$_GET;
// echoMessage('param ',$param);
// echoMessage('param ',$_GET);
// exit();
if($param['proses']=='createTable') {
    # Get Data

    $query = selectQuery($dbname,'vhc_penggantiandt',"*","`notransaksi`='".$_POST['notransaksi']."'"); 
    $data = fetchData($query);

    # Create Detail Table
    createTabDetail($_POST['notransaksi'],$data);
} else {
    $data = $_POST;
        $tglsdt=tanggalsystem($data['tgl_sdt']);
        $rtglpp=tanggalsystem($data['rtgl_pp']);
    unset($data['proses']);
    switch($_POST['proses']) {
        case 'detail_add' :
                        $lokasi=$_SESSION['empl']['lokasitugas'];
                        $entry_by=$_SESSION['standard']['userid'];
                        #Check Header
                        if($_POST['tgl']=='')
                        {
                                echo "warning: Please complete the form";
                                exit();
                        }
                         if(($data['kd_brg']=='') or ($data['jmlh']=='')) {
                echo "Error :Please Complete The Detail Form";
                exit;
            }
                        $sql="select * from ".$dbname.".vhc_penggantianht where notransaksi='".$_POST['notransaksi']."'";
                        $query=mysql_query($sql) or die(mysql_error());
                        $res=mysql_fetch_row($query);

                        //echo $sql.":warning:".$res;exit();
                        if($res<1)
                        {
                                $sins="insert into ".$dbname.".vhc_penggantianht (kodeorg`,`kodevhc`,`tanggal`,`updateby`,`notransaksi`,`downtime`, `kerusakan`) values 
                                ('".$lokasi."','".$_POST['kd_jenis']."','".tanggalsystem($_POST['tgl'])."','".$entry_by."','".$_POST['notransaksi']."')";
                                if(mysql_query($sins))
                                {
                                        $dins="insert into ".$dbname.".vhc_penggantiandt (`notransaksi`,`kodebarang`,`jumlah`,`satuan`,`keterangan`) 
                                        values ('".$_POST['notransaksi']."','".$_POST['kd_brg']."','".$_POST['jmlh']."','".$_POST['satuan']."',
                                        '".$_POST['ket']."')";
                                        //echo "warning:test".$dins;
                                        if(mysql_query($dins))
                                        {
                                        }
                                        else
                                        {
                                        //echo "warning:masuk";
                                        echo "DB Error : ".mysql_error($conn);
                                        }
                                }
                                else
                                {
                                        echo "DB Error : ".mysql_error($conn);
                                }
                        }
                        else
                        {
                                $dins="insert into ".$dbname.".vhc_penggantiandt (`notransaksi`,`kodebarang`,`jumlah`,`satuan`,`keterangan`) 
                                values ('".$_POST['notransaksi']."','".$_POST['kd_brg']."','".$_POST['jmlh']."','".$_POST['satuan']."',
                                '".$_POST['ket']."')";
                                //echo "warning:test".$dins;
                                if(mysql_query($dins))
                                {
                                        $skdbrg="select kodebarang from ".$dbname.".vhc_penggantiandt order by notransaksi desc";
                                        $qkdbrg=mysql_query($skdbrg) or die(mysql_error());
                                        $rkdbrg=mysql_fetch_assoc($qkdbrg);
                                        echo $rkdbrg['kodebarang'];
                                }
                                else
                                {
                                        echo "warning: masuk";
                                        echo "DB Error : ".mysql_error($conn);
                                }
                        }
            break;
        case 'detail_edit' :
            # Check Valid Data
                        //echo "warning:masuk";
            if(($data['notransaksi']=='') or ($data['kd_brg']=='') or ($data['satuan']=='') or ($data['jmlhDiminta']=='')) {
                echo "Error : All field obligatory";
                exit;
            }

            # Create Condition
            $where = "`notransaksi`='".$data['notransaksi']."'";
            $where .= " and `kodebarang`='".$data['dkd_brg']."'";

            # Make Query

                   $query = "update ".$dbname.".`vhc_penggantiandt` set kodebarang='".$data['kd_brg']."',satuan='".$data['satuan']."',jumlah='".$data['jmlhDiminta']."', keterangan='".$data['ket']."' where ".$where."";

            # Update Data
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
                        //echo "warning:".$query;exit();
            break;

        case 'detail_delete' :
                        //echo "warning:masuk";  
            $data = $_POST;

            # Create Condition
            $where = "`notransaksi`='".$data['notransaksi']."'";
            $where .= " and `kodebarang`='".$data['kd_brg']."'";

            # Create Query
            $query = "delete from `".$dbname."`.`vhc_penggantiandt` where ".$where;
                        //echo "warning:".$query;
            //echo query;
            # Delete
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            break;
        default :
            break;
    }
}

function createTabDetail($id,$data) {
        global $dbname;
        global $conn;
    //echo "<button class=mybutton onclick=addNewRow('detailBody',true)>Add Details</button><br />";

   // $table .= "<table id=\"ppDetailTable\">";
    # Header
    $table .= "<thead class=rowheader>";
    $table .= "<tr>";
    $table .= "<td>".$_SESSION['lang']['kodebarang']."</td>";
        $table .= "<td>".$_SESSION['lang']['namabarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['satuan']."</td>";
    $table .= "<td>".$_SESSION['lang']['jumlah']."</td>";
        $table .= "<td>".$_SESSION['lang']['keterangan']."</td>";
//    $table .= "<td>"."<a href=# onclick=addNewRow(detailBody,true)><img src='images\newfile.png'></a>"."</td>";
    $table .= "<td colspan=3>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";

    # Data
    $table .= "<tbody id='detailBody'>";

    $i=0;

    #======= Display Data =======
    if($data!=array()) {
        foreach($data as $key=>$row) {
        
                        $sbrg="select * from ".$dbname.".log_5masterbarang where kodebarang='".$row['kodebarang']."'";
                        $qbrg=mysql_query($sbrg) or die(mysql_error());
                        $res=mysql_fetch_assoc($qbrg);
                         $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
                         $table .= "<td>".makeElement("kd_brg_".$key."",'txt',$row['kodebarang'],
                array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))."<input type=hidden value='".$row['kodebarang']."' name=skd_brg_".$key." id=skd_brg_".$key." /></td>";
            $table .= "<td>".makeElement("nm_brg_".$key."",'txt',$res['namabarang'],
                array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))."</td>";
                        $table .= "<td>".makeElement("sat_".$key."",'txt',$res['satuan'],
                array('style'=>'width:70px','disabled'=>'disabled','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";></td>";
                                $table .= "<td>".makeElement("jmlh_".$key."",'textnum',$row['jumlah'],
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)'))."</td>";
                        $table .= "<td>".makeElement("ket_".$key."",'text',$row['keterangan'],array('style'=>'width:130px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
            $table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/001_45.png'/>";
            $table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
                        $table .= "</tr>";
            $i = $key;
        }
        $i++;
    }

    #======= New Row ===========
        $table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
        $table .= "<td>".makeElement("kd_brg_".$i."",'txt','',array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))."<input type=hidden id=skd_brg_".$i." name=skd_brg_".$i." /></td>";
        $table .= "<td>".makeElement("nm_brg_".$i."",'txt','',array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))."</td>";
        $table .= "<td>".makeElement("sat_".$i."",'txt','',array('style'=>'width:70px','disabled'=>'disabled','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><input type=hidden id=nomor name=nomor value=".$i."><div id=container></div>',event)\";></td>";
        //$table .= "<td>".makeElement("sat_".$i."",'select',$row['satuan'],array('style'=>'width:70px','class=myinputtext'),$optSatuan)."</td>";
        $table .= "<td>".makeElement("jmlh_".$i."",'textnum','',array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)'))."</td>";
        $table .= "<td>".makeElement("ket_".$i."",'text','',array('style'=>'width:130px','onkeypress'=>'return tanpa_kutip(event)','maxlength'=>'45'))."</td>";

    # Add, Container Delete
    $table .= "<td><img id='detail_add_".$i."' title='Simpan' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/save.png'/>";
    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
    $table .= "</tr>";
    $table .= "</tbody>";
  //  $table .= "</table>";
    echo $table;
}
?>