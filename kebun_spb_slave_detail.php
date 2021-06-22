<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
$proses = $_POST['proses'];
$kdOrg = $_POST['kdDiv'];
$noSpb = $_POST['noSpb'];
$periode = $_POST['periode'];
$tgl = explode('-', $_POST['tgl']);
list(, $tglBln, $tglThn) = $tgl;
$periodeB = $tglThn.'-'.$tglBln;
switch ($proses) {
    case 'createTable':
        $cekPost = 'select distinct posting from '.$dbname.".kebun_spbht where nospb='".$noSpb."'";
        $qcekPost = mysql_query($cekPost) ;
        $rCek = mysql_fetch_assoc($qcekPost);
        if (0 != $rCek['posting']) {
            exit('Error:Nospb Sudah Posting');
        }

        if ($periode !== $periodeB) {
            echo 'warning:Tanggal dan Periode tidak sama';
            exit();
        }

        if (0 === $_POST['statusCek']) {
            $where = " left(kodeorg,6)='".$kdOrg."' and luasareaproduktif!=0";
        } else {
            $where = "left(kodeorg,4)='".substr($kdOrg, 0, 4)."' and luasareaproduktif!=0";
        }

        $optBlok = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama', $where, '2', true);
        $table .= '<thead>';
        $table .= "<tr class='rowheader'>";
        $table .= '<td>'.$_SESSION['lang']['blok'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['bjr'].'(Kg)'.'</td>';
        $table .= '<td>'.$_SESSION['lang']['janjang'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['brondolan'].'(Kg)'.'</td>';
        $table .= '<td><font size=0.5>Kg WB (Khusus ke Pabrik Luar)</font></td>';
        $table .= '<td>'.$_SESSION['lang']['mentah'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['busuk'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['matang'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['lewatmatang'].'</td>';
        $table .= '<td colspan=3>Action</td>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= "<tbody id='detailBody'>";
        $table .= "<tr id='detail_tr' class='rowcontent'>";
        $table .= '<td>'.makeElement('blok', 'select', '', ['style' => 'width:200px', 'onchange' => 'getBjr()'], $optBlok)."<img src=images/search.png class=dellicon onclick=\"searchBrg('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['blok']."','<fieldset><legend>".$_SESSION['lang']['find'].'</legend>'.$_SESSION['lang']['blok'].'<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>'.$_SESSION['lang']['find'].'</button></fieldset><div id=container></div><input type=hidden id=kdafd value='.$kdOrg." />',event)\"; /><input type=hidden id=oldBlok name=oldBlok value='' /></td>";
        $table .= '<td>'.makeElement('bjr', 'textnum', '0', ['style' => 'width:80px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5', 'disabled' => 'true']).'</td>';
        $table .= '<td>'.makeElement('jjng', 'textnum', '0', ['style' => 'width:80px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5']).'</td>';
        $table .= '<td>'.makeElement('brondln', 'textnum', '0', ['style' => 'width:80px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5']).'</td>';
        $table .= '<td>'.makeElement('kgwb', 'textnum', '0', ['style' => 'width:135px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5']).'</td>';
        $table .= '<td>'.makeElement('mnth', 'textnum', '0', ['style' => 'width:30px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5', 'disabled' => 'true']).'</td>';
        $table .= '<td>'.makeElement('bsk', 'textnum', '0', ['style' => 'width:30px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5', 'disabled' => 'true']).'</td>';
        $table .= '<td>'.makeElement('mtng', 'textnum', '0', ['style' => 'width:30px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5', 'disabled' => 'true']).'</td>';
        $table .= '<td>'.makeElement('lwtmtng', 'textnum', '0', ['style' => 'width:30px', 'onkeypress' => 'return angka_doang(event)', 'maxlength' => '5', 'disabled' => 'true']).'</td>';
        $table .= "<td><img id='detail_add' title=".$_SESSION['lang']['save']." class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/>";
        $table .= "&nbsp;<img id='detail_delete' /></td>";
        $table .= '</tr>';
        $table .= '<tr><td colspan=10><font color=red>KG WB di isi untuk Kebun yang belum memiliki Mill (Pabrik)</font></td></tr>';
        $table .= '</tbody>';
        echo $table;

        break;
    case 'detail_add':
        $lokasi = $_SESSION['empl']['lokasitugas'];
        $lokasi = substr($lokasi, 0, 4);
        $entry_by = $_SESSION['standard']['userid'];
        if ('' === $data['jjng'] || '' === $data['brondolan'] || '' === $data['bjr']) {
            echo 'Error : Tolong lengkap data detail, data tidak boleh kosong';
            exit();
        }

        $sql = 'select nospb from '.$dbname.".kebun_spbht where nospb='".$_POST['noSpb']."'";
        $query = mysql_query($sql) ;
        $res = mysql_fetch_row($query);
        if ($res < 1) {
            $sins = 'insert into '.$dbname.".kebun_spbht (`nospb`, `kodeorg`, `tanggal`,`updateby`) values \r\n\t\t\t\t('".$_POST['noSpb']."','".$_POST['kodeOrg']."','".tanggalsystem($_POST['tgl'])."','".$entry_by."')";
            if (mysql_query($sins)) {
                $kgBjr = (int) ($_POST['jjng']) * (int) ($_POST['bjr']);
                $dins = 'insert into '.$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan,  mentah, busuk, matang, lewatmatang,kgbjr) \r\n\t\t\t\t\tvalues ('".$_POST['noSpb']."','".$_POST['blok']."','".$_POST['jjng']."','".$_POST['bjr']."',\r\n\t\t\t\t\t'".$_POST['brondolan']."','".$_POST['mentah']."','".$_POST['busuk']."','".$_POST['matang']."','".$_POST['lwtmatang']."','".$kgBjr."')";
                if (mysql_query($dins)) {
                    echo '';
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            $kgBjr = (int) ($_POST['jjng']) * (int) ($_POST['bjr']);
            $dins = 'insert into '.$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr) \r\n\t\t\t\tvalues ('".$_POST['noSpb']."','".$_POST['blok']."','".$_POST['jjng']."','".$_POST['bjr']."',\r\n\t\t\t\t\t'".$_POST['brondolan']."','".$_POST['mentah']."','".$_POST['busuk']."','".$_POST['matang']."','".$_POST['lwtmatang']."','".$kgBjr."')";
            if (mysql_query($dins)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        break;
    case 'loadDetail':
        $sDet = 'select * from '.$dbname.".kebun_spbdt where nospb='".$noSpb."' order by blok desc";
        $qDet = mysql_query($sDet) ;
        while ($rDet = mysql_fetch_assoc($qDet)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n\t\t<td>".$no."</td>\r\n\t\t<td>".$rDet['blok']."</td>\r\n\t\t<td>".$rDet['bjr']."</td>\r\n\t\t<td>".$rDet['jjg']."</td>\r\n\t\t<td>".$rDet['brondolan']."</td>\r\n\t\t<td>".$rDet['mentah']."</td>\r\n\t\t<td>".$rDet['busuk']."</td>\r\n\t\t<td>".$rDet['matang']."</td>\r\n\t\t<td>".$rDet['lewatmatang']."</td>\r\n\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDetail('".$rDet['nospb']."','".$rDet['blok']."','".$rDet['jjg']."','".$rDet['bjr']."','".$rDet['brondolan']."','".$rDet['mentah']."','".$rDet['busuk']."','".$rDet['matang']."','".$rDet['lewatmatang']."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['nospb']."','".$rDet['blok']."');\" ></td>\r\n\t\t</tr>\r\n\t\t";
        }

        break;
    case 'getBlokSma':
        $optKdBlok = "<option value=''></option>";
        $sdt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\r\n              induk like '".substr($_POST['kdAfd'], 0, 4)."%'  and tipe='BLOK' order by namaorganisasi asc";
        $qdt = mysql_query($sdt) ;
        while ($rdt = mysql_fetch_assoc($qdt)) {
            $optKdBlok .= "<option value='".$rdt['kodeorganisasi']."'>".$rdt['namaorganisasi'].'</option>';
        }
        echo $optKdBlok;

        break;
    case 'getBlokNor':
        $optKdBlok = "<option value=''></option>";
        $sdt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\r\n              induk='".$_POST['kdAfd']."' and tipe='BLOK' order by namaorganisasi asc";
        $qdt = mysql_query($sdt) ;
        while ($rdt = mysql_fetch_assoc($qdt)) {
            $optKdBlok .= "<option value='".$rdt['kodeorganisasi']."'>".$rdt['namaorganisasi'].'</option>';
        }
        echo $optKdBlok;

        break;
    case 'cariBlok':
        $tab .= "<fieldset>\r\n               <legend>Result</legend>\r\n               <div style=\"overflow:auto; height:300px;\" >\r\n               <table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab .= '<thead><tr><td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['blok'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namaorganisasi'].'</td></tr></thead><tbody>';
        if (1 === $_POST['idCer']) {
            $dhr = " induk like '".substr($_POST['kdAfd'], 0, 4)."%' \r\n                    and kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,4)='".substr($_POST['kdAfd'], 0, 4)."' and luasareaproduktif!=0)";
        } else {
            $dhr = " induk='".$_POST['kdAfd']."' and kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,6)='".$_POST['kdAfd']."' and luasareaproduktif!=0)";
        }

        $sdt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\r\n              ".$dhr." and tipe='BLOK' and namaorganisasi like '%".$_POST['txtfind']."%' order by namaorganisasi asc";
        $qdt = mysql_query($sdt) ;
        while ($rdt = mysql_fetch_assoc($qdt)) {
            ++$ert;
            $tab .= "<tr class=rowcontent onclick=\"setBlok('".$rdt['kodeorganisasi']."')\" style='cursor:pointer;'><td>".$ert.'</td>';
            $tab .= '<td>'.$rdt['kodeorganisasi'].'</td>';
            $tab .= '<td>'.$rdt['namaorganisasi'].'</td></tr>';
        }
        $tab .= '</tbody></table></div></fieldset>';
        echo $tab;

        break;
}

?>