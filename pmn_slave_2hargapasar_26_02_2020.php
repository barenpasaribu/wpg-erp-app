<?php

	require_once 'master_validation.php';
	require_once 'config/connection.php';
	include_once 'lib/eagrolib.php';
	include 'lib/zMysql.php';
	include 'lib/zFunction.php';
	include_once 'lib/zLib.php';
	
	$proses = $_POST['proses'];

	switch ($proses) {
        case 'daily':
            ?>
			<table class=sortable cellspacing="1" style="font-family: arial, sans-serif; border-collapse: collapse;">
				<thead>
					<tr class='rowheader'>
						<th>Tanggal</th>
						<th>Kelompok Supplier</th>
						<th>Supplier</th>
						<th>Harga Awal</th>
						<th>Fluktuasi Harga</th>
						<th>Harga Sekarang</th>
					</tr>
				</thead>
					<?php
						date_default_timezone_set("Asia/Jakarta");
						$tanggal = date("Y-m-d");
						$getMasterHarga="SELECT 
										a.kode_klsupplier, a.kode_supplier, a.tanggal, 
										a.harga, b.kelompok, c.namasupplier 
										FROM log_5supplier_harga a
										LEFT JOIN 
										log_5klsupplier b ON a.kode_klsupplier = b.kode
										LEFT JOIN 
										log_5supplier c ON a.kode_supplier = c.supplierid
										ORDER BY 
										kode_klsupplier ASC, kode_supplier";
						$queryActMasterData = mysql_query($getMasterHarga);
						$no = 0;
						while($data = mysql_fetch_object($queryActMasterData)){
						$no++;
					?>
					<tbody id="container">
					<tr class=rowcontent id="tr_<?= $no; ?>">
						<td><?= tanggalnormal($tanggal); ?></td>
						<td><?= $data->kelompok; ?></td>
						<td><?= $data->namasupplier; ?></td>
						
						<?php 
							
							if ($data->tanggal == $tanggal) {
								$queryCekKenaikan ="SELECT * FROM log_supplier_harga_history
								WHERE 
								kode_klsupplier='".$data->kode_klsupplier."'
								AND
								kode_supplier='".$data->kode_supplier."'
								AND
								tanggal_akhir='".$data->tanggal."'
								";
								$queryCekKenaikanAct = mysql_query($queryCekKenaikan);
								$dataKenaikan = mysql_fetch_object($queryCekKenaikanAct);
								$kenaikanharga = '';
								if(!empty($dataKenaikan)){
									if ($dataKenaikan->operator_kenaikan == 'rp') {
										$kenaikanharga = "Rp".$dataKenaikan->harga_kenaikan;
									}
									if ($dataKenaikan->operator_kenaikan == '%') {
										$kenaikanharga = $dataKenaikan->harga_kenaikan."%";
									}
									echo "<td>".$dataKenaikan->harga_awal."</td>";
									if ($dataKenaikan->fluktuasi == 'naik') {
										echo "<td style='color:white; background-color: green;'>".$kenaikanharga."</td>";
									}
									if ($dataKenaikan->fluktuasi == 'turun') {
										echo "<td style='color:white; background-color: red;'>".$kenaikanharga."</td>";	
									}
								}else{
									echo "<td>-</td>";
									echo "<td style='color: black;'>Tidak ada kenaikan</td>";
								}
								
							}else{
								echo "<td style='color: black;'>Tidak ada kenaikan</td>";
								echo "<td>-</td>";
							}
						?>
						<td><?= $data->harga; ?></td>
					</tr>
					</tbody>
					<?php } ?>
				</table>
			<?php
            break;
        case 'montly':
            ?>
			<table class=sortable cellspacing="1" style="font-family: arial, sans-serif; border-collapse: collapse;">
				<thead>
					<tr class='rowheader'>
						<th>Tanggal</th>
						<th>Kelompok Supplier</th>
						<th>Supplier</th>
						<th>Harga Awal</th>
						<th>Fluktuasi Harga</th>
						<th>Harga Sekarang</th>
					</tr>
				</thead>
				<?php
					date_default_timezone_set("Asia/Jakarta");
					$tanggal = date("Y-m-d");
					$tanggal_awal = date("Y-m-01");
					$tanggal_akhir = date("Y-m-31");
					$getMasterHarga="SELECT 
									a.kode_klsupplier, a.kode_supplier, a.tanggal_akhir AS tanggal, 
									a.harga_akhir AS harga, b.kelompok, c.namasupplier 
									FROM log_supplier_harga_history a
									LEFT JOIN 
									log_5klsupplier b ON a.kode_klsupplier = b.kode
									LEFT JOIN 
									log_5supplier c ON a.kode_supplier = c.supplierid
									WHERE
									tanggal_akhir BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
									ORDER BY 
									kode_supplier ASC, tanggal";
					$queryActMasterData = mysql_query($getMasterHarga);
					$no = 0;
					while($data = mysql_fetch_object($queryActMasterData)){
					$no++;
				?>
				<tbody id="container">
				<tr class=rowcontent id="tr_<?= $no; ?>">
					<td><?= tanggalnormal($data->tanggal); ?></td>
					<td><?= $data->kelompok; ?></td>
					<td><?= $data->namasupplier; ?></td>
					
					<?php 
						
						if ($data->tanggal == $tanggal) {
							$queryCekKenaikan ="SELECT * FROM log_supplier_harga_history
							WHERE 
							kode_klsupplier='".$data->kode_klsupplier."'
							AND
							kode_supplier='".$data->kode_supplier."'
							AND
							tanggal_akhir='".$data->tanggal."'
							";
							$queryCekKenaikanAct = mysql_query($queryCekKenaikan);
							$dataKenaikan = mysql_fetch_object($queryCekKenaikanAct);
							$kenaikanharga = '';
							if(!empty($dataKenaikan)){
								if ($dataKenaikan->operator_kenaikan == 'rp') {
									$kenaikanharga = "Rp".$dataKenaikan->harga_kenaikan;
								}
								if ($dataKenaikan->operator_kenaikan == '%') {
									$kenaikanharga = $dataKenaikan->harga_kenaikan."%";
								}
								echo "<td>".$dataKenaikan->harga_awal."</td>";
								if ($dataKenaikan->fluktuasi == 'naik') {
									echo "<td style='color:white; background-color: green;'>".$kenaikanharga."</td>";
								}
								if ($dataKenaikan->fluktuasi == 'turun') {
									echo "<td style='color:white; background-color: red;'>".$kenaikanharga."</td>";	
								}
							}else{
								echo "<td>-</td>";
								echo "<td style='color: black;'>Tidak ada kenaikan</td>";
							}
							
						}else{
							echo "<td style='color: black;'>Tidak ada kenaikan</td>";
							echo "<td>-</td>";
						}
					?>
					<td><?= $data->harga; ?></td>
				</tr>
				</tbody>
				<?php } ?>
			</table>
			<?php
            break;
	}
?>