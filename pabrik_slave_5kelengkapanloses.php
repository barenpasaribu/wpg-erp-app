<?php
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';

    $id = $_POST['id'];
    $kodeorg = $_POST['kodeorg'];
    $produk = $_POST['produk'];
    $namaitem = $_POST['namaitem'];
    $standard = $_POST['standard'];
    $satuan = $_POST['satuan'];
    $faktor_konversi_1 = $_POST['faktor_konversi_1'];
    $faktor_konversi_2 = $_POST['faktor_konversi_2'];
    $faktor_konversi_3 = $_POST['faktor_konversi_3'];
    $losses_to_tbs = $_POST['losses_to_tbs'];
    $linked_to = $_POST['linked_to'];
    $method = $_POST['method'];
    switch ($method) {
        case 'datajson':
            $query = 'SELECT * FROM pabrik_5kelengkapanloses 
                    WHERE 
                        kodeorg="'.$_SESSION['empl']['lokasitugas'].'" 
                    order by 
                        namaitem ASC';
            $data = fetchData($query);
            if (empty($data[0])) {
                echo null;
            }else{
                echo json_encode($data);
            }
            break;
        case 'loadData':
            $str = 'SELECT * FROM pabrik_5kelengkapanloses 
                    WHERE 
                        kodeorg="'.$_SESSION['empl']['lokasitugas'].'" 
                    order by 
                        produk, namaitem ASC';
            $res = mysql_query($str);
            ?>
            <table id="dataKelengkapanLoses" class="display" style="width:100%">
                <thead>
                    <tr>
                        <td>No</td>
                        <td>Kode ORG</td>
                        <td>Produk</td>
                        <td>Nama Barang</td>
                        <td>Standard to Sample</td>
                        <td>Satuan</td>
                        <td>Faktor Konversi 1</td>
                        <td>Faktor Konversi 2</td>
                        <td>Faktor Konversi 3</td>
                        <td>Losses to TBS (%)</td>
                        <td>Linked to</td>
                        <td>Update By</td>
                        <td><?= $_SESSION['lang']['action'] ?></td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $no = 1;
            while ($bar = mysql_fetch_object($res)) {
            ?>
            
                    <tr class="rowcontent" id="tr_<?= $no ?>">
                        <td><?= $no ?></td>
                        <td><?= $bar->kodeorg ?></td>
                        <td><?= $bar->produk ?></td>
                        <td><?= $bar->namaitem ?></td>
                        <td><?= $bar->standard ?></td>
                        <td><?= $bar->satuan ?></td>
                        <td><?= $bar->faktor_konversi_1 ?></td>
                        <td><?= $bar->faktor_konversi_2 ?></td>
                        <td><?= $bar->faktor_konversi_3 ?></td>
                        <td><?= $bar->losses_to_tbs ?></td>
                        <td><?= $bar->linked_to ?></td>
                        <td><?= getNamaKaryawan($bar->updateby) ?></td>
                        <td>
                            <img src="images/application/application_edit.png" class="resicon" title='Edit' caption='Edit' onclick="edit('<?= $bar->id ?>','<?= $bar->kodeorg ?>','<?= $bar->produk ?>','<?= $bar->namaitem ?>','<?= $bar->standard ?>','<?= $bar->satuan ?>','<?= $bar->faktor_konversi_1 ?>','<?= $bar->faktor_konversi_2 ?>','<?= $bar->faktor_konversi_3 ?>','<?= $bar->losses_to_tbs ?>','<?= $bar->linked_to ?>');">
                            <img src="images/application/application_delete.png" class="resicon" title='Delete' caption='Delete' onclick="del('<?= $bar->id ?>');">
                        </td>
                    </tr>
                
            <?php 
            $no++;
            } 

            ?>
                </tbody>
            </table>
            <?php

            break;
        case 'insert':
            $i = 'insert into '.$dbname.".pabrik_5kelengkapanloses(kodeorg,produk,namaitem,standard,satuan,faktor_konversi_1,faktor_konversi_2,faktor_konversi_3,losses_to_tbs,linked_to,updateby) values ('".$kodeorg."','".$produk."','".$namaitem."','".$standard."','".$satuan."','".$faktor_konversi_1."','".$faktor_konversi_2."','".$faktor_konversi_3."','".$losses_to_tbs."','".$linked_to."','".$_SESSION['standard']['userid']."')";
            if (mysql_query($i)) {
                echo 'berhasil';
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }

            break;
        case 'update':
            $i = '  update '.$dbname.".pabrik_5kelengkapanloses 
                    set 
                    kodeorg='".$kodeorg."',produk='".$produk."',
                    namaitem='".$namaitem."',
                    standard='".$standard."',
                    satuan='".$satuan."',
                    faktor_konversi_1='".$faktor_konversi_1."',
                    faktor_konversi_2='".$faktor_konversi_2."',
                    faktor_konversi_3='".$faktor_konversi_3."',
                    losses_to_tbs='".$losses_to_tbs."',
                    linked_to ='".$linked_to."',
                    updateby='".$_SESSION['standard']['userid']."'
                    where id='".$id."'";
            if (mysql_query($i)) {
                echo 'berhasil';
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }

            break;
        case 'delete':
            $i = 'DELETE FROM '.$dbname.".pabrik_5kelengkapanloses WHERE id='".$id."'";

            $notif = "gagal";
            if (mysql_query($i)) {
                $notif = 'berhasil';
            }
            echo json_encode($notif);
            break;
    }

?>