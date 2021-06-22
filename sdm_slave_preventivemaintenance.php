<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$id = $_POST['id'];
$jenis = $_POST['jenis'];
$mesin = $_POST['mesin'];
$satuan = $_POST['satuan'];
$atas = ('' == $_POST['atas'] ? 0 : $_POST['atas']);
$peringatan = ('' == $_POST['peringatan'] ? 0 : $_POST['peringatan']);
$tanggal = $_POST['tanggal'];
$tugas = $_POST['tugas'];
$keterangan = $_POST['keterangan'];
$email = $_POST['email'];
$lokasitugas = $_SESSION['empl']['lokasitugas'];
$sekali = $_POST['sekali'];
$kodebarang = $_POST['kodebarang'];
$jumlahbarang = $_POST['jumlahbarang'];
if ('' == $proses) {
    $proses = $_GET['proses'];
}

$tanggalganti = tanggalsystem($_POST['tanggal']);
if ('' == $tanggalganti) {
    $tanggalganti = '0000-00-00';
}

$lokasi = $_SESSION['empl']['lokasitugas'];
$tglGanti = tanggalsystem($_POST['tglGanti']);
$kdJenis = $_POST['kdjenis'];
$usr_id = $_SESSION['standard']['userid'];
$notransaksi = $_POST['notrans'];
$codeOrg = $_POST['codeOrg'];
$descDmg = $_POST['descDmg'];
$dwnTime = $_POST['dwnTime'];
$statInp = $_POST['statInp'];
$optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
switch ($proses) {
    case 'load_mesin':
        if ('' != $id) {
            $svhc = 'select kodemesin from '.$dbname.".schedulerht\r\n                where id = '".$id."'\r\n                ";
            $qvhc = mysql_query($svhc);
            while ($rvhc = mysql_fetch_assoc($qvhc)) {
                $kodemesin = $rvhc['kodemesin'];
            }
        }

        $optmesin = "<option value=''>".$_SESSION['lang']['pilihdata'].' '.$jenis.'</option>';
        if ('STENGINE' == $jenis) {
            $svhc = 'select a.namaorganisasi, a.kodeorganisasi, b.namaorganisasi as namainduk from '.$dbname.".organisasi a\r\n                left join ".$dbname.".organisasi b on a.induk=b.kodeorganisasi\r\n                where a.induk like '".$lokasitugas."%' and length(a.induk)=6 and a.tipe = '".$jenis."'\r\n                order by a.induk, a.kodeorganisasi";
            $qvhc = mysql_query($svhc);
            while ($rvhc = mysql_fetch_assoc($qvhc)) {
                $optmesin .= "<option value='".$rvhc['kodeorganisasi']."' ".(($rvhc['kodeorganisasi'] == $kodemesin ? 'selected' : '')).'>['.$rvhc['namainduk'].'] '.$rvhc['namaorganisasi'].'</option>';
            }
        } else {
            if ('TRAKSI' == $jenis) {
                $svhc = 'select kodevhc, kodeorg from '.$dbname.".vhc_5master\r\n                where kodetraksi like '".$lokasitugas."%'\r\n                order by kodevhc";
                $qvhc = mysql_query($svhc);
                while ($rvhc = mysql_fetch_assoc($qvhc)) {
                    $optmesin .= "<option value='".$rvhc['kodevhc']."' ".(($rvhc['kodevhc'] == $kodemesin ? 'selected' : '')).'>['.$rvhc['kodevhc'].'] '.$rvhc['kodeorg'].'</option>';
                }
            } else {
                if ('UMUM' == $jenis) {
                    $optmesin = "<option value='umum'>UMUM</option>";
                } else {
                    $optmesin = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
                }
            }
        }

        echo $optmesin;

        break;
    case 'simpan_header':
        if ('' == $id) {
            $query = mysql_query('SHOW TABLE STATUS from '.$dbname." WHERE name='schedulerht'");
            if (mysql_num_rows($query)) {
                $result = mysql_fetch_assoc($query);
                echo $result['Auto_increment'];
            }

            $sins = 'insert into '.$dbname.".schedulerht (`jenis`,`kodemesin`,`satuan`,`batasatas`,`batasreminder`,`setiaptanggal`, `namatugas`, `email`, `kodeorg`, `ketrangan`,`sekali`,`resethmkm`) values \r\n            ('".$jenis."','".$mesin."','".$satuan."','".$atas."','".$peringatan."','".$tanggalganti."','".$tugas."','".$email."','".$lokasitugas."','".$keterangan."','".$sekali."','".$_POST['resetHmkm']."')";
            if (mysql_query($sins)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            $sins = 'UPDATE '.$dbname.".schedulerht \r\n                SET jenis = '".$jenis."', kodemesin = '".$mesin."', satuan = '".$satuan."', batasatas = '".$atas."', batasreminder = '".$peringatan."', setiaptanggal = '".$tanggalganti."', \r\n                namatugas = '".$tugas."', email = '".$email."', ketrangan = '".$keterangan."', sekali = '".$sekali."', resethmkm = '".$_POST['resetHmkm']."' \r\n                WHERE `schedulerht`.`id` = '".$id."'";
            if (mysql_query($sins)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        }

        break;
    case 'simpan_detail':
        $sins = 'insert into '.$dbname.".schedulerdt (`id`,`kodebarang`,`jumlah`) values \r\n            ('".$id."','".$kodebarang."','".$jumlahbarang."')";
        if (mysql_query($sins)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'hapus_detail':
        $sins = 'DELETE FROM '.$dbname.".schedulerdt \r\n            WHERE `id` = '".$id."' and `kodebarang` = '".$kodebarang."'";
        if (mysql_query($sins)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'hapus_header':
        $sins = 'DELETE FROM '.$dbname.".schedulerht \r\n            WHERE `id` = '".$id."'";
        if (mysql_query($sins)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'tambahdetail':
        $svhc = 'select a.*, b.namabarang, b.satuan from '.$dbname.".schedulerdt a\r\n            left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\n            where a.id = '".$id."'\r\n            ";
        $qvhc = mysql_query($svhc);
        while ($rvhc = mysql_fetch_assoc($qvhc)) {
            echo '<tr>';
            echo '<td colspan=2 align=center>'.$rvhc['kodebarang'].'</td>';
            echo '<td align=left>'.$rvhc['namabarang'].'</td>';
            echo '<td align=center>'.$rvhc['satuan'].'</td>';
            echo '<td align=right>'.$rvhc['jumlah'].'</td>';
            echo "<td align=center><img src=images/delete1.png class=resicon title='Delete Detail' onclick=\"hapusdetail('".$rvhc['kodebarang']."');\"></td>";
            echo '</tr>';
        }
        echo '<tr>';
        echo "<td><input type=\"text\" class=\"myinputtextnumber\" id=\"kodebarang\" name=\"kodebarang\" onkeypress=\"return angka_doang(event);\" value=\"\" maxlength=\"10\" style=\"width:150px;\" disabled=true/></td>\r\n                  <td><img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value='.$key.">',event)\";></td>";
        echo "<td>\r\n                <input type=\"text\" class=\"myinputtext\" id=\"namabarang\" name=\"namabarang\" onkeypress=\"return tanpa_kutip(event);\" value=\"\" maxlength=\"20\" style=\"width:200px;\" disabled=true/>\r\n                </td><td><input type=\"text\" class=\"myinputtext\" id=\"satuanbarang\" name=\"satuanbarang\" onkeypress=\"return tanpa_kutip(event);\" value=\"\" maxlength=\"10\" style=\"width:150px;\" disabled=true/></td><td><input type=\"text\" class=\"myinputtextnumber\" id=\"jumlahbarang\" name=\"jumlahbarang\" onkeypress=\"return angka_doang(event);\" value=\"\" maxlength=\"10\" style=\"width:150px;\" /></td><td><img src=images/tick_16.png class=resicon title='Save Detail' onclick=\"simpandetail();\"></td></tr>";

        break;
    case 'load_data':
        OPEN_BOX();
        echo "<fieldset>\r\n            <legend>".$_SESSION['lang']['list'].'</legend>';
        echo "<table cellspacing=1 border=0 class=sortable>\r\n        <thead>\r\n            <tr class=rowheader>\r\n                <td>".$_SESSION['lang']['action']."</td>\r\n                <td>".$_SESSION['lang']['nomor']."</td>\r\n                <td>".$_SESSION['lang']['jenis']."</td>\r\n                <td>".$_SESSION['lang']['nmmesin']."</td>\r\n                <td>".$_SESSION['lang']['namatugas']."</td>\r\n                <td>".$_SESSION['lang']['peringatansetiap']."</td>\r\n                <td>".$_SESSION['lang']['batasatas']."</td>\r\n                <td>".$_SESSION['lang']['satuan']."</td>\r\n                <td>".$_SESSION['lang']['resethmkm']."</td>\r\n                <td>".$_SESSION['lang']['setiap'].' '.$_SESSION['lang']['tanggal']."</td>\r\n                <td>".$_SESSION['lang']['keterangan']."</td>\r\n            </tr>\r\n        </thead>\r\n        <tbody>";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".schedulerht where kodeorg = '".$lokasitugas."'";
        $query2 = mysql_query($sql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.".schedulerht where kodeorg = '".$lokasitugas."' limit ".$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc);
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            echo '<tr class=rowcontent>';
            echo "<td>\r\n                    <img src=images/application/application_view_detail.png class=resicon title='Detail' onclick=\"lihatdetail('".$rlvhc['id']."','".$rlvhc['jenis']."','".$rlvhc['kodemesin']."','".$rlvhc['satuan']."','".$rlvhc['batasatas']."','".$rlvhc['batasreminder']."','".tanggalnormal($rlvhc['setiaptanggal'])."','".$rlvhc['namatugas']."','".$rlvhc['ketrangan']."','".$rlvhc['email']."','".$rlvhc['sekali']."','".$rlvhc['resethmkm']."');\">\r\n                    <img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"hapusheader('".$rlvhc['id']."');\" >\t\r\n                    <img src=images/application/application_edit.png class=resicon title='Edit' onclick=\"isiheader('".$rlvhc['id']."','".$rlvhc['jenis']."','".$rlvhc['kodemesin']."','".$rlvhc['satuan']."','".$rlvhc['batasatas']."','".$rlvhc['batasreminder']."','".tanggalnormal($rlvhc['setiaptanggal'])."','".$rlvhc['namatugas']."','".$rlvhc['ketrangan']."','".$rlvhc['email']."','".$rlvhc['sekali']."','".$rlvhc['resethmkm']."');\">\r\n                    <img src=images/pdf.jpg class=resicon title='Print' onclick=\"lihatpdf('".$rlvhc['id']."',event);\">\r\n                </td>\r\n                <td align=right>".$rlvhc['id']."</td>\r\n                <td>".$rlvhc['jenis']."</td>\r\n                <td>".$rlvhc['kodemesin']."</td>\r\n                <td>".$rlvhc['namatugas']."</td>\r\n                <td align=right>".$rlvhc['batasreminder']."</td>\r\n                <td align=right>".$rlvhc['batasatas']."</td>\r\n                <td>".$rlvhc['satuan']."</td>\r\n                <td align=right>".number_format($rlvhc['resethmkm'], 2)."</td>\r\n                <td align=center>".tanggalnormal($rlvhc['setiaptanggal'])."</td>\r\n                <td>".$rlvhc['ketrangan']."</td>\r\n            </tr>";
        }
        echo "<tr><td colspan=10 align=center>\r\n            ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n            <button class=mybutton onclick=browsedata(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n            <button class=mybutton onclick=browsedata(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n        </td>\r\n        </tr>";
        echo '</tbody></table></fieldset>';
        CLOSE_BOX();

        break;
    case 'cari_barang':
        $txtcari = $_POST['txtcari'];
        $str = 'select a.kodebarang,a.namabarang,a.satuan from '.$dbname.".log_5masterbarang a where a.namabarang like '%".$txtcari."%' or a.kodebarang like '%".$txtcari."' and kelompokbarang in (331,332,333,334,335,336,338,341,342,375)";
        $res = mysql_query($str);
        if (mysql_num_rows($res) < 1) {
            echo 'Error: '.$_SESSION['lang']['tidakditemukan'];
        } else {
            echo "<fieldset>\r\n            <legend>".$_SESSION['lang']['result']."</legend>\r\n            <div style=\"width:450px; height:300px; overflow:auto;\">\r\n            <table class=sortable cellspacing=1 border=0>\r\n            <thead>\r\n                <tr class=rowheader>\r\n                    <td>No</td>\r\n                    <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                    <td>".$_SESSION['lang']['namabarang']."</td>\r\n                    <td>".$_SESSION['lang']['satuan']."</td>\r\n                </tr>\r\n            </thead>\r\n            <tbody>";
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                echo "<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"throwThisRow('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."');\">\r\n                    <td>".$no."</td>\r\n                    <td>".$bar->kodebarang."</td>\r\n                    <td>".$bar->namabarang."</td>\r\n                    <td>".$bar->satuan."</td>\r\n                </tr>";
            }
            echo "</tbody>\r\n                <tfoot></tfoot>\r\n                </table></div></fieldset>";
        }

        break;
    case 'getOverDue':
        $tab .= "<fieldset>\r\n            <legend>".$_SESSION['lang']['list'].'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['keterangan'].'</td>';
        $tab .= '<td>PIC</td>';
        $tab .= '<td>'.$_SESSION['lang']['status'].'</td></tr></thead><tbody>';
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $sData = 'select distinct * from '.$dbname.'.scheduler_aksi order by id desc';
        } else {
            $sData = 'select distinct * from '.$dbname.".scheduler_aksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by id desc";
        }

        $arrData = ['Belum Selesai', 'Selesai'];
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$aer;
            $tab .= '<tr class=rowcontent><td>'.$aer.'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
            $tab .= '<td>'.$rData['keterangan'].'</td>';
            $tab .= '<td>'.$rData['pic'].'</td>';
            if ($_SESSION['empl']['lokasitugas'] == $rData['kodeorg']) {
                if (0 == $rData['selesai']) {
                    $tab .= "<td><input type='checkbox' id=statId_".$aer." onclick=upStat('".$rData['id']."','".$rData['tanggal']."') /></td>";
                } else {
                    $tab .= '<td>'.$arrData[$rData['selesai']].'</td>';
                }
            } else {
                $tab .= '<td>'.$arrData[$rData['selesai']].'</td>';
            }

            $tab .= '</tr>';
        }
        $tab .= '</tbody></table></fieldset>';
        OPEN_BOX();
        echo $tab;
        CLOSE_BOX();

        break;
    case 'upDate':
        $sUp = 'update '.$dbname.".scheduler_aksi set selesai=1 \r\n          where id='".$_POST['idStat']."' and tanggal='".$_POST['tgl']."'";
        if (!mysql_query($sUp)) {
            #exit(mysql_error($conn));
        }

        break;
    default:
        break;
}

?>