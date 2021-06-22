<?php

    require_once 'config/connection.php';
    require_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';

    $method = $_POST['method'];

    switch ($method) {
        case 'insert':
            $data = $_POST;
            $data['tanggal'] = substr($data['tanggal'],6,4) . "-" . substr($data['tanggal'],3,2) . "-" . substr($data['tanggal'],0,2);
            unset($data['method']);

            $keyQuery = [];
            $i = 0 ;
            foreach ($data as $key => $value) {
                $keyQuery[$i] = $key;
                $i++;
            }
            $queryCek = "SELECT * FROM pabrik_produksi where kodeorg='".$data['kodeorg']."' and tanggal='".$data['tanggal']."'";
            $resultCek = mysql_query($queryCek);
            $num_rows = mysql_num_rows($resultCek);

            if ($num_rows > 0) {
                $notif = "Gagal, data pada tanggal ".tanggalnormal($data['tanggal'])." sudah ada.";
            } else {
                $query = insertQuery($dbname, 'pabrik_produksi', $data, $keyQuery);

                $notif = "gagal";
                if (mysql_query($query)) {
                    $notif = 'berhasil';
                }
            }
            
            
            echo json_encode($notif);

            break;
        case 'delete':
            $query = '  delete from '.$dbname.".pabrik_produksi 
                        where kodeorg='".$_POST['kodeorg']."' 
                        and tanggal='".$_POST['tanggal']."'";

            $notif = "gagal";
            if (mysql_query($query)) {
                $notif = 'berhasil';
            }
            echo json_encode($notif);
            break;
        case 'loadData':
            $str = 'select a.* from '.$dbname.".pabrik_produksi a
            where kodeorg='".$_SESSION['empl']['lokasitugas']."'
            order by a.tanggal desc limit 1000";
            $res = mysql_query($str);
            ?>
            <table id="data_pabrik_produksi" class="display" style="width:100%">
                <thead>
                    <tr>
                        <td>No</td> 
                        <td>Tanggal</td> 
                        <td>Kode ORG</td> 
                        <td>TBS Sisa Kemarin</td> 
                        <td>TBS Masuk (Bruto)</td> 
                        <td>TBS (Potongan)</td> 
                        <td>TBS Masuk (Netto)</td> 
                        <td>TBS diolah</td> 
                        <td>TBS After Grading</td> 
                        <td>TBS (Sisa)</td> 
                        <td><?= $_SESSION['lang']['action'] ?></td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $no = 1;
            while ($bar = mysql_fetch_object($res)) {
            ?>
            
                    <tr class="rowcontent" id="tr_<?= $no ?>">
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= $no ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= tanggalnormal($bar->tanggal) ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= $bar->kodeorg ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_sisa_kemarin,0,",",".") ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_masuk_bruto,0,",",".") ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_potongan,0,",",".") ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_masuk_netto,0,",",".") ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_diolah,0,",",".") ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_after_grading,0,",",".") ?></td>
                        <td onclick="cetakPDF('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>')"><?= number_format($bar->tbs_sisa,0,",",".") ?></td>
                        <td style="text-align: center;">
                            <img src="images/application/application_delete.png" class="resicon" title='Delete' caption='Delete' onclick="delProduksi('<?= $bar->kodeorg ?>','<?= $bar->tanggal ?>');">
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
    }    

?>