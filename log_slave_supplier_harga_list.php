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
    $queryList = "  SELECT 
                        a.tanggal, a.kode_supplier, a.harga_kenaikan, a.fee, a.fluktuasi, b.namasupplier 
                    FROM 
                        log_supplier_harga_temporary a
                    JOIN 
                        log_5supplier b ON a.kode_supplier = b.supplierid
                    WHERE 
                        temporary_list_id = '".$column."'
                        ";
    $result = fetchData($queryList);  
?>

<table border='1'>
    <div id="progress"></div>
    <thead>
        <tr>
            <td>Tanggal</td>
            <td>Supplier</td>
            <td>Fluktuasi Harga</td>
            <td>Fee</td>
        </tr>
    </thead>
    <tbody>
        <?php 
        $queryTempList = "SELECT * FROM log_supplier_harga_temporary_list where id = '".$column."' ";
        $dataTempList = fetchData($queryTempList);
            if($dataTempList[0]['status1'] == 2 || $dataTempList[0]['status2'] == 2 || $dataTempList[0]['status2'] == 1){
                foreach ($result as $key => $value) {
                    ?>
                    <tr>
                        <td><?= tanggalnormal($value['tanggal']); ?></td>
                        <td><?= $value['kode_supplier']; ?> - <?= $value['namasupplier']; ?></td>
                        <?php if ($value['fluktuasi'] == "naik") { ?>
                        <td style="color:white; background-color: green;"><?= $value['harga_kenaikan']; ?></td>
                        <?php } ?>
                        <?php if ($value['fluktuasi'] == "turun") { ?>
                        <td style="color:white; background-color: red;"><?= $value['harga_kenaikan']; ?></td>
                        <?php } ?>
                        <?php if ($value['fluktuasi'] == "tetap") { ?>
                        <td><?= $value['harga_kenaikan']; ?></td>
                        <?php } ?>
                        <td><?= $value['fee']; ?></td>
                    </tr>
                    <?php
                }
            }else{
                $no = 0;
                foreach ($result as $key => $value) {
                    ?>
                    <tr>
                        <td><?= tanggalnormal($value['tanggal']); ?></td>
                        <td><?= $value['kode_supplier']; ?> - <?= $value['namasupplier']; ?></td>
                        <?php if ($value['fluktuasi'] == "naik") { ?>
                        <td style="color:white; background-color: green;"><input type="text" id="harga_<?= $no ?>" onchange="ubahFluktuasi('<?= $_GET['column']; ?>','<?= $value['tanggal']; ?>','<?= $value['kode_supplier']; ?>',<?= $no; ?>)" value="<?= $value['harga_kenaikan']; ?>"></td>
                        <?php } ?>
                        <?php if ($value['fluktuasi'] == "turun") { ?>
                        <td style="color:white; background-color: red;"><input type="text" id="harga_<?= $no ?>" onchange="ubahFluktuasi('<?= $_GET['column']; ?>','<?= $value['tanggal']; ?>','<?= $value['kode_supplier']; ?>',<?= $no; ?>)" value="<?= $value['harga_kenaikan']; ?>"></td>
                        <?php } ?>
                        <?php if ($value['fluktuasi'] == "tetap") { ?>
                        <td><input type="text" id="harga_<?= $no ?>" onchange="ubahFluktuasi('<?= $_GET['column']; ?>','<?= $value['tanggal']; ?>','<?= $value['kode_supplier']; ?>',<?= $no; ?>)" value="<?= $value['harga_kenaikan']; ?>"></td>
                        <?php } ?>
                        <td><input type="text" id="fee_<?= $no ?>" onchange="ubahFee('<?= $_GET['column']; ?>','<?= $value['tanggal']; ?>','<?= $value['kode_supplier']; ?>',<?= $no; ?>)" value="<?= $value['fee']; ?>"></td>
                    </tr>
                    <?php
                    $no++;
                }
            }
        ?>
    </tbody>
</table>
<script language="javascript" src="js/generic.js"></script>
<script type="text/javascript" src="js/log_supplier_harga_approve.js"></script>
</body>
</html>