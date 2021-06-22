<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';

    $tanggal_awal = substr($_POST['tanggal_awal'],6,4) . "-" . substr($_POST['tanggal_awal'],3,2) . "-" . substr($_POST['tanggal_awal'],0,2);
    $tanggal_akhir = substr($_POST['tanggal_akhir'],6,4) . "-" . substr($_POST['tanggal_akhir'],3,2) . "-" . substr($_POST['tanggal_akhir'],0,2);
    $kodeorg = $_POST['kodeorg'];
    $queryGetPabrikProduksi = "SELECT * FROM pabrik_produksi 
                                WHERE 
                                    tanggal >= '".$tanggal_awal."' 
                                AND
                                    tanggal <= '".$tanggal_akhir."' 
                                AND
                                    kodeorg = '".$kodeorg."' 
                                ORDER BY 
                                    tanggal DESC";
    $hasilGetPabrikProduksi = fetchData($queryGetPabrikProduksi);
    

?>
<style>
    td{
        text-align:center;
    }
</style>
<fieldset>
    <legend><span class="judul">&nbsp;<b>Susunan Data</b></span></legend>
    <table id="data_laporan_pabrik_produksi" class="display" style="width:100%">
        <thead>
            <tr class="rowheader">
                <td>Kode Organisasi</td>
                <td>Tanggal</td>
                <td>TBS Sisa Kemarin</td>
                <td>TBS Masuk (Bruto)</td>
                <td>TBS Potongan</td>
                <td>TBS diolah</td>
                <td>TBS Sisa</td>
                <td>Action</td>
            </tr>  
        </thead>
        <tbody>
            <?php
                if (empty($hasilGetPabrikProduksi)) {
            ?>
            <tr>
                <td colspan="6">
            <?php
                    echo "Tidak ada data.";
                }
            ?>
                </td>
            </tr>
            <?php 
                foreach ($hasilGetPabrikProduksi as $key => $value) {
            
            ?>
            <tr>
                <td><?= $value['kodeorg'] ?></td>
                <td><?= tanggalnormal($value['tanggal']) ?></td>
                <td><?= number_format($value['tbs_sisa_kemarin'],0,",",".") ?></td>
                <td><?= number_format($value['tbs_masuk_bruto'],0,",",".") ?></td>
                <td><?= number_format($value['tbs_potongan'],0,",",".") ?></td>
                <td><?= number_format($value['tbs_diolah'],0,",",".") ?></td>
                <td><?= number_format($value['tbs_sisa'],0,",",".") ?></td>
                <td>
                    <img onclick="cetakExcel('<?= $value['kodeorg'] ?>','<?= $value['tanggal'] ?>')" src="images/excel.jpg" class="resicon" title="PDF"> 
                </td>
            </tr>  
            <?php } ?>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</fieldset>