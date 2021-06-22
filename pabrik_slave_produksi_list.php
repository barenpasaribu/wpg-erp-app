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
    $queryPengolahanPabrik = "  SELECT 
                        *
                    FROM 
                        pabrik_pengolahan 
                    WHERE 
                        tanggal = '".$tanggal."' 
                    AND
                        kodeorg = '".$kodeorg."'
                        ";
    $resultPengolahanPabrik = fetchData($queryPengolahanPabrik);
    
?>
<h2 align="center">Data</h2>
<div id="progress"></div>
<h2>Pengolahan Pabrik</h2>
<table border='1'>
    
    <tbody>
            <tr>
                <td>Tanggal</td>
                <td><?= $resultPengolahanPabrik[0]['tanggal']; ?></td>
            </tr>
            <tr>
                <td>Kode Organisasi</td>
                <td><?= $resultPengolahanPabrik[0]['kodeorg']; ?></td>
            </tr>
            <tr>
                <td>Total Jam Shift</td>
                <td><?= $resultPengolahanPabrik[0]['total_jam_shift']; ?></td>
            </tr>
            <tr>
                <td>Total Jam Operasi</td>
                <td><?= $resultPengolahanPabrik[0]['total_jam_operasi']; ?></td>
            </tr>
            <tr>
                <td>Total Jam Press</td>
                <td><?= $resultPengolahanPabrik[0]['total_jam_press']; ?></td>
            </tr>
            <tr>
                <td>Total Jam Idle</td>
                <td><?= $resultPengolahanPabrik[0]['total_jam_idle']; ?></td>
            </tr>
            <tr>
                <td colspan="2"><b>Lori</b></td>
            </tr>
            <tr>
                <td>Lori Olah Shift 1</td>
                <td><?= $resultPengolahanPabrik[0]['lori_olah_shift_1']; ?></td>
            </tr>
            <tr>
                <td>Lori Olah Shift 2</td>
                <td><?= $resultPengolahanPabrik[0]['lori_olah_shift_2']; ?></td>
            </tr>
            <tr>
                <td>Lori Olah Shift 3</td>
                <td><?= $resultPengolahanPabrik[0]['lori_olah_shift_3']; ?></td>
            </tr>
            <tr>
                <td>Lori Dalam Rebusan</td>
                <td><?= $resultPengolahanPabrik[0]['lori_dalam_rebusan']; ?></td>
            </tr>
            <tr>
                <td>Restan Depan Rebusan</td>
                <td><?= $resultPengolahanPabrik[0]['restan_depan_rebusan']; ?></td>
            </tr>
            <tr>
                <td>Restan Dibelakang Rebusan</td>
                <td><?= $resultPengolahanPabrik[0]['restan_dibelakang_rebusan']; ?></td>
            </tr>
            <tr>
                <td>Estimasi di Peron</td>
                <td><?= $resultPengolahanPabrik[0]['estimasi_di_peron']; ?></td>
            </tr>
            <tr>
                <td>Rata-rata Lori</td>
                <td><?= $resultPengolahanPabrik[0]['rata_rata_lori']; ?></td>
            </tr>
            <tr>
                <td>Total Lori</td>
                <td><?= $resultPengolahanPabrik[0]['total_lori']; ?></td>
            </tr>
            <tr>
                <td colspan="2"><b>TBS</b></td>
            </tr>
            <tr>
                <td>TBS Sisa Kemarin (Awal)</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_sisa_kemarin']; ?></td>
            </tr>
            <tr>
                <td>TBS Masuk (Bruto)</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_masuk_bruto']; ?></td>
            </tr>
            <tr>
                <td>TBS (Potongan)</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_potongan']; ?></td>
            </tr>
            <tr>
                <td>TBS Masuk (Netto)</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_masuk_netto']; ?></td>
            </tr>
            <tr>
                <td>TBS Diolah</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_diolah']; ?></td>
            </tr>
            <tr>
                <td>TBS Diolah (After Grading)</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_diolah_after']; ?></td>
            </tr>
            <tr>
                <td>TBS Sisa (Akhir)</td>
                <td><?= $resultPengolahanPabrik[0]['tbs_sisa']; ?></td>
            </tr>
            <tr>
                <td colspan="2"><b>Pengiriman</b></td>
            </tr>
            <tr>
                <td>Despatch CPO</td>
                <td><?= $resultPengolahanPabrik[0]['despatch_cpo']; ?></td>
            </tr>
            <tr>
                <td>Return CPO</td>
                <td><?= $resultPengolahanPabrik[0]['return_cpo']; ?></td>
            </tr>
            <tr>
                <td>Despatch PK</td>
                <td><?= $resultPengolahanPabrik[0]['despatch_pk']; ?></td>
            </tr>
            <tr>
                <td>Return PK</td>
                <td><?= $resultPengolahanPabrik[0]['return_pk']; ?></td>
            </tr>
            <tr>
                <td>Janjang Kosong</td>
                <td><?= $resultPengolahanPabrik[0]['janjang_kosong']; ?></td>
            </tr>
            <tr>
                <td>Limbah Cair</td>
                <td><?= $resultPengolahanPabrik[0]['limbah_cair']; ?></td>
            </tr>
            <tr>
                <td>Solid Decnter</td>
                <td><?= $resultPengolahanPabrik[0]['solid_decnter']; ?></td>
            </tr>
            <tr>
                <td>Abu Janjang</td>
                <td><?= $resultPengolahanPabrik[0]['abu_janjang']; ?></td>
            </tr>
            <tr>
                <td>Cangkang</td>
                <td><?= $resultPengolahanPabrik[0]['cangkang']; ?></td>
            </tr>
            <tr>
                <td>Fibre</td>
                <td><?= $resultPengolahanPabrik[0]['fibre']; ?></td>
            </tr>
            
    </tbody>
</table>
<?php
$querySounding = "  SELECT 
                        *
                    FROM 
                        pabrik_masukkeluartangki 
                    WHERE 
                        tanggal >= '".$tanggal." 00:00:00' - INTERVAL 1 DAY
                    AND
                        tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                    AND
                        kodeorg = '".$kodeorg."'
                        ";
$resultSounding = fetchData($querySounding);
?>
<h2>Sounding Produksi</h2>
<table border='1'>
    <thead>
        <tr>
            <td>Tanggal</td>
            <td>Kode ORG</td>
            <td>Kode Tangki</td>
            <td>Jumlah CPO</td>
            <td>Jumlah Kernel</td>
        </tr>
    </thead>
    <tbody>
        <?php  
        $jumlahCPO = 0;    
        $jumlahPK = 0;    
        foreach ($resultSounding as $key => $value) {
        ?>
            <tr>
                <td><?= $value['tanggal']; ?></td>
                <td><?= $value['kodeorg']; ?></td>
                <td><?= $value['kodetangki']; ?></td>
                <td><?= $value['kuantitas']; ?></td>
                <td><?= $value['kernelquantity']; ?></td>
                
            </tr>
            <?php
            $jumlahCPO += $value['kuantitas'];
            $jumlahPK += $value['kernelquantity'];
        }
        ?>
        <tr>
            <td colspan="3"><b>Jumlah</b></td>
            <td><b><?= $jumlahCPO; ?></b></td>
            <td><b><?= $jumlahPK; ?></b></td>
            
        </tr>
    </tbody>
</table>
<?php
$getTransaksiLoses = "  SELECT a.tanggal, a.kodeorg, b.produk, b.namaitem, a.nilai FROM pabrik_kelengkapanloses a
                        JOIN pabrik_5kelengkapanloses b
                        ON a.id = b.id
                        WHERE 
                        a.kodeorg = '".$kodeorg."'
                        AND
                        a.tanggal = '".$tanggal."' - INTERVAL 1 DAY 
                        ORDER BY b.produk ASC
                        ";
$dataTransaksi = fetchData($getTransaksiLoses);
?>

<h2>Kelengkapan Loses</h2>
<table border='1'>
    <thead>
        <tr>
            <td>Tanggal</td>
            <td>Kode ORG</td>
            <td>Produk</td>
            <td>Nama Item</td>
            <td>Nilai</td>
        </tr>
    </thead>
    <tbody>
        <?php    
        foreach ($dataTransaksi as $key => $value) {
        ?>
            <tr>
                <td><?= $value['tanggal']; ?></td>
                <td><?= $value['kodeorg']; ?></td>
                <td><?= $value['produk']; ?></td>
                <td><?= $value['namaitem']; ?></td>
                <td><?= $value['nilai']; ?></td>
                
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<script language="javascript" src="js/generic.js"></script>
<script type="text/javascript" src="js/log_supplier_harga_approve.js"></script>
</body>
</html>