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
    $isi = $_GET['isi'] ;
    $nodo = $_GET['nodo'] ;
    $list="";
    $dataT = explode(",",$isi);
            foreach($dataT as $T) {
            
              $list .= "'" . $T . "',";
            }
    $kodeorg1=$_SESSION['empl']['lokasitugas'];
    $kodeorg = substr($kodeorg1, 0,3);
    $queryList = "  SELECT 
                        *
                    FROM 
                        pabrik_timbangan 
                    WHERE 
                      
                        notransaksi IN (".$list."'--')
                        and nosipb = '".$nodo."'
                    AND
                    millcode like '".$kodeorg."%'
                        ";
    $result = fetchData($queryList);
    


    $queryCount = "  SELECT 
                        count(*) as total_data,
                        sum(beratmasuk) as beratmasuk,
                        sum(beratkeluar) as beratkeluar,
                        sum(beratbersih) as beratbersih,
                        sum(kgpotsortasi) as kgpotsortasi
                    FROM 
                        pabrik_timbangan 
                    WHERE 
                     notransaksi IN (".$list."'--')
                     and nosipb = '".$nodo."'
                    AND
                    millcode like '".$kodeorg."%'
                        ";
    $resultCount = fetchData($queryCount);  
?>
<h2 align="center">Data Timbangan </h2>
<p>
    Total Data: <?= $resultCount[0]['total_data']; ?> <br> 
    Total Netto: <?= $resultCount[0]['beratbersih']; ?> <br>  
</p>

<h2></h2>
<table border='1'>
    <div id="progress"></div>
    <thead>
        <tr>
            <td>No Transaksi</td>
            <td>Netto</td>
        </tr>
    </thead>
    <tbody>
        <?php             
        $no = 0;
        foreach ($result as $key => $value) {
            ?>
            <tr>
                <td><?= $value['notransaksi']; ?></td>
                <td><?= $value['beratbersih']; ?></td>
                
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