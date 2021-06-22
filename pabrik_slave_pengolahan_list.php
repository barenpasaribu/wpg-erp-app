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
    $tanggal = substr($_GET['tanggal'],6,4) . "-" . substr($_GET['tanggal'],3,2) . "-" . substr($_GET['tanggal'],0,2) ;
    $kodeorg = $_GET['kodeorg'];
    $queryList = "  SELECT 
                        *
                    FROM 
                        pabrik_timbangan 
                    WHERE 
                        tanggal LIKE '".$tanggal."%'
                    AND
                        notransaksi LIKE 'M%'
                    AND
                    millcode = '".$kodeorg."'
                        ";
    $result = fetchData($queryList);
    
    $queryList2 = "  SELECT 
                        *
                    FROM 
                        pabrik_timbangan 
                    WHERE 
                        tanggal LIKE '".$tanggal."%'
                    AND
                        notransaksi LIKE 'K%'
                    AND
                    millcode = '".$kodeorg."'
                        ";
    $result2 = fetchData($queryList2);

    $queryCount = "  SELECT 
                        count(*) as total_data,
                        sum(beratmasuk) as beratmasuk,
                        sum(beratkeluar) as beratkeluar,
                        sum(beratbersih) as beratbersih,
                        sum(kgpotsortasi) as kgpotsortasi
                    FROM 
                        pabrik_timbangan 
                    WHERE 
                        tanggal LIKE '".$tanggal."%'
                    AND
                        notransaksi LIKE 'M%'
                    AND
                        millcode = '".$kodeorg."'
                        ";
    $resultCount = fetchData($queryCount);  
?>
<h2 align="center">Timbangan (<?= $_GET['tanggal'] ?>)</h2>
<p>
    Total Data: <?= $resultCount[0]['total_data']; ?> <br> 
    Total Berat Masuk: <?= $resultCount[0]['beratmasuk']; ?> <br> 
    Total Berat Keluar: <?= $resultCount[0]['beratkeluar']; ?> <br> 
    Total Berat Bersih: <?= $resultCount[0]['beratbersih']; ?> <br> 
    Total Sortasi: <?= $resultCount[0]['kgpotsortasi']; ?> <br> 
</p>

<h2>Penerimaan</h2>
<table border='1'>
    <div id="progress"></div>
    <thead>
        <tr>
            <td>No Transaksi</td>
            <td>Plat Nomor</td>
            <td>Kode Customer</td>
            <td>Berat Masuk (Bruto)</td>
            <td>Berat Keluar (Tarra)</td>
            <td>Berat Bersih (Netto)</td>
            <td>Sortasi</td>
        </tr>
    </thead>
    <tbody>
        <?php             
        $no = 0;
        foreach ($result as $key => $value) {
            ?>
            <tr>
                <td><?= $value['notransaksi']; ?></td>
                <td><?= $value['nokendaraan']; ?></td>
                <td><?= $value['kodecustomer']; ?></td>
                <td><?= $value['beratmasuk']; ?></td>
                <td><?= $value['beratkeluar']; ?></td>
                <td><?= $value['beratbersih']; ?></td>
                <td><?= $value['kgpotsortasi']; ?></td>
                
            </tr>
            <?php
            $no++;
        }
        ?>
    </tbody>
</table>
<h2>Pengiriman</h2>
<table border='1'>
    <div id="progress"></div>
    <thead>
        <tr>
            <td>No Transaksi</td>
            <td>Plat Nomor</td>
            <td>Komoditi</td>
            <td>Kode Customer</td>
            <td>Berat Masuk (Bruto)</td>
            <td>Berat Keluar (Tarra)</td>
            <td>Berat Bersih (Netto)</td>
        </tr>
    </thead>
    <tbody>
        <?php             
        $no = 0;
        foreach ($result2 as $key2 => $value2) {
            $queryGetNamaKomodity = "SELECT * FROM log_5masterbarang where kodebarang = '".$value2['kodebarang']."'";
            $hasilNamaKomodity = fetchData($queryGetNamaKomodity);
            ?>
            <tr>
                <td><?= $value2['notransaksi']; ?></td>
                <td><?= $value2['nokendaraan']; ?></td>
                <td><?= $hasilNamaKomodity[0]['namabarang'];  ?></td>
                <td><?= $value2['kodecustomer']; ?></td>
                <td><?= $value2['beratmasuk']; ?></td>
                <td><?= $value2['beratkeluar']; ?></td>
                <td><?= $value2['beratbersih']; ?></td>
                
            </tr>
            <?php
            $no++;
        }
        ?>
    </tbody>
</table>
<script language="javascript" src="js/generic.js"></script>
<script type="text/javascript" src="js/log_supplier_harga_approve.js"></script>
</body>
</html>