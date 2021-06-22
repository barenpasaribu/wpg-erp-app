<!DOCTYPE html>
<html>
<head>
<style>
    table {
    border-collapse: collapse;
    width: 100%;
    }

    th, td {
    text-align: left;
    padding: 8px;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}
</style>
</head>
<body>
<?php
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    require_once 'lib/fpdf.php';
    include_once 'lib/zMysql.php';
    include_once 'lib/devLibrary.php';
    $table = $_GET['table'];
    $column = $_GET['column'];
    $where = $_GET['cond'];
    $queryList = "SELECT * FROM log_supplier_harga_history
                    WHERE
                    kode_supplier = '".$_GET['column']."'
                    order by tanggal_akhir desc limit 1000
                    ";
    $result = fetchData($queryList);  

    function rupiah($angka){
	
        $hasil_rupiah = "Rp " . number_format($angka,2,',','.');
        return $hasil_rupiah;
     
    }
?>

<table border='1'>
    <thead>
        <tr>
            <th>Tanggal Awal</th>
            <th style="font-weight:bold">Tanggal Akhir</th>
            <th>Kode Supplier</th>
            <th>Harga Awal</th>
            <th>Fluktuasi</th>
            <th style="font-weight:bold">Harga Akhir</th>
            <th>Fee</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            foreach ($result as $key => $value) {
        ?>
        <tr>
            <td><?= tanggalnormal($value['tanggal_awal']); ?></td>
            <td style="font-weight:bold"><?= tanggalnormal($value['tanggal_akhir']); ?></td>
            <td><?= $value['kode_supplier']; ?></td>
            <td><?= rupiah($value['harga_awal']); ?></td>
            <td><?= $value['harga_kenaikan']; ?> (<?= $value['operator_kenaikan']; ?>)</td>
            <td style="font-weight:bold"><?= rupiah($value['harga_akhir']); ?></td>
            <td><?= $value['fee']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br>
<b><i>*Max 1000 Data</i></b>
</body>
</html>