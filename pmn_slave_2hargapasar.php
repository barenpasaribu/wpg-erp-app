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
			<b><i>*Dibandingkan dengan tanggal hari ini (<?= date("d-m-Y"); ?>)</i></b><br><br>
			<table class=sortable cellspacing="1" style="font-family: arial, sans-serif; border-collapse: collapse;">
				<thead>
					<tr class='rowheader'>
						<th>Tanggal</th>
						<th>Kelompok Supplier</th>
						<th>Kode Supplier</th>
						<th>Nama Supplier</th>
						<th>Fee</th>
						<th>Harga Awal</th>
						<th>Fluktuasi Harga</th>
						<th>Harga Sekarang</th>
					</tr>
				</thead>
					<?php
						// HARDCODE KARNA STRUKTUR DB NYA BERANTAKAN!
						$lokasitugas = substr($_SESSION['empl']['lokasitugas'],0,3);
						$getKlSupplier = "SELECT * FROM log_5klsupplier WHERE kelompok like '%".$lokasitugas."'";
						$dataKlSupplier = fetchData($getKlSupplier);
						
						$where = "";
						
						if ($lokasitugas == "SSP" || $lokasitugas == "LSP") {
							foreach ($dataKlSupplier as $key => $value) {
								if ($where == null) {
									$where = " WHERE kode_klsupplier ='".$value['kode']."' ";
								}else{
									$where .= " OR kode_klsupplier ='".$value['kode']."' ";
								}
							}
						}
						
						date_default_timezone_set("Asia/Jakarta");
						$tanggal = date("Y-m-d");
						// $tanggal = "2020-03-18";
						$getMasterHarga="SELECT 
										a.kode_klsupplier, a.kode_supplier, a.tanggal, 
										a.harga, b.kelompok, c.namasupplier , c.kode_supplier_lapangan
										FROM log_5supplier_harga a
										LEFT JOIN 
										log_5klsupplier b ON a.kode_klsupplier = b.kode
										LEFT JOIN 
										log_5supplier c ON a.kode_supplier = c.supplierid
										".$where."
										ORDER BY 
										tanggal ASC, kode_klsupplier ASC, kode_supplier";
						
										
						$queryActMasterData = mysql_query($getMasterHarga);
						$no = 0;
						while($data = mysql_fetch_object($queryActMasterData)){
						$no++;
					?>
					<tbody id="container">
					<tr onclick="masterPDF('log_supplier_harga_history','<?= $data->kode_supplier; ?>','','log_slave_supplier_harga_detail','event')" class=rowcontent id="tr_<?= $no; ?>">
						<td><?= tanggalnormal($data->tanggal); ?></td>
						<td><?= $data->kelompok; ?></td>
						<td><?= $data->kode_supplier; ?>  (<?= $data->kode_supplier_lapangan; ?>)</td>
						<td><?= $data->namasupplier; ?> </td>
						<?
							$fee = 0;
							$queryGetFee = "SELECT * FROM log_supplier_harga_history WHERE kode_supplier='".$data->kode_supplier."' and tanggal_akhir='".$data->tanggal."'";
							$dataFee = fetchData($queryGetFee);
							if (!empty($dataFee[0])) {
								$fee = $dataFee[0]['fee'];
							}
						?>
						<td><?= $fee; ?> </td>
						
						<?php 
							
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

									if ($dataKenaikan->harga_awal < $dataKenaikan->harga_akhir) {
										echo "<td style='color:white; background-color: green;'>".$kenaikanharga."</td>";
									}
									if ($dataKenaikan->harga_awal > $dataKenaikan->harga_akhir) {
										echo "<td style='color:white; background-color: red;'>".$kenaikanharga."</td>";	
									}
									if ($dataKenaikan->harga_awal == $dataKenaikan->harga_akhir) {
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
			<b><i>*Dibandingkan dengan bulan ini (<?= date("M-Y"); ?>)</i></b><br><br>
			<table class=sortable cellspacing="1" style="font-family: arial, sans-serif; border-collapse: collapse;">
				<thead>
					<tr class='rowheader'>
						<th>Tanggal</th>
						<th>Kelompok Supplier</th>
						<th>Kode Supplier</th>
						<th>Nama Supplier</th>
						<th>Fee</th>
						<th>Harga Awal</th>
						<th>Fluktuasi Harga</th>
						<th>Harga Sekarang</th>
					</tr>
				</thead>
				<?php
					// HARDCODE KARNA STRUKTUR DB NYA BERANTAKAN!
					$lokasitugas = substr($_SESSION['empl']['lokasitugas'],0,3);
					$getKlSupplier = "SELECT * FROM log_5klsupplier WHERE kelompok like '%".$lokasitugas."'";
					$dataKlSupplier = fetchData($getKlSupplier);
					
					$where = null;
					
					if ($lokasitugas == "SSP" || $lokasitugas == "LSP") {
						foreach ($dataKlSupplier as $key => $value) {
							
							if ($where == null) {
								$where = " AND ( kode_klsupplier ='".$value['kode']."' ";
							}else{
								$where .= " OR kode_klsupplier ='".$value['kode']."' ";
							}
						}
						$where .= " ) ";
					}
					date_default_timezone_set("Asia/Jakarta");
					$tanggal = date("Y-m-d");
					// $tanggal = date("2020-03-18");
					$tanggal_awal = date("Y-m-01");
					$tanggal_akhir = date("Y-m-31");
					$getMasterHarga="SELECT 
									a.kode_klsupplier, a.kode_supplier, a.tanggal_akhir AS tanggal, 
									a.harga_akhir AS harga, b.kelompok, c.namasupplier , c.kode_supplier_lapangan, a.fee
									FROM log_supplier_harga_history a
									LEFT JOIN 
									log_5klsupplier b ON a.kode_klsupplier = b.kode
									LEFT JOIN 
									log_5supplier c ON a.kode_supplier = c.supplierid
									WHERE
									tanggal_akhir BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
									".$where."
									ORDER BY 
									tanggal ASC, kode_supplier ASC, tanggal";
									
					$queryActMasterData = mysql_query($getMasterHarga);
					$no = 0;
					while($data = mysql_fetch_object($queryActMasterData)){
					$no++;
				?>
				<tbody id="container">
				<tr class=rowcontent id="tr_<?= $no; ?>">
					<td><?= tanggalnormal($data->tanggal); ?></td>
					<td><?= $data->kelompok; ?></td>
					<td><?= $data->kode_supplier; ?> (<?= $data->kode_supplier_lapangan; ?>)</td>
					<td><?= $data->namasupplier; ?></td>
					<td><?= $data->fee; ?></td>
					
					<?php 
						
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

							if ($dataKenaikan->harga_awal < $dataKenaikan->harga_akhir) {
								echo "<td style='color:white; background-color: green;'>".$kenaikanharga."</td>";
							}
							if ($dataKenaikan->harga_awal > $dataKenaikan->harga_akhir) {
								echo "<td style='color:white; background-color: red;'>".$kenaikanharga."</td>";	
							}
							if ($dataKenaikan->harga_awal == $dataKenaikan->harga_akhir) {
								echo "<td style='color: black;'>Tidak ada kenaikan</td>";	
							}
						}else{
							echo "<td>-</td>";
							echo "<td style='color: black;'>Tidak ada kenaikan</td>";
						}
							
					?>
					<td><?= $data->harga; ?></td>
				</tr>
				</tbody>
				<?php } ?>
			</table>
			<?php
            break;
        case 'chart':
            ?>
			<div style="width:100%;">
							<canvas id="canvas"></canvas>
						</div>
						<script>
							var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
							var config = {
								type: 'line',
								data: {
									labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
									datasets: [{
										label: 'My First dataset',
										backgroundColor: window.chartColors.red,
										borderColor: window.chartColors.red,
										data: [
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor()
										],
										fill: false,
									}, {
										label: 'My Second dataset',
										fill: false,
										backgroundColor: window.chartColors.blue,
										borderColor: window.chartColors.blue,
										data: [
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor(),
											randomScalingFactor()
										],
									}]
								},
								options: {
									responsive: true,
									title: {
										display: true,
										text: 'Chart.js Line Chart'
									},
									tooltips: {
										mode: 'index',
										intersect: false,
									},
									hover: {
										mode: 'nearest',
										intersect: true
									},
									scales: {
										x: {
											display: true,
											scaleLabel: {
												display: true,
												labelString: 'Month'
											}
										},
										y: {
											display: true,
											scaleLabel: {
												display: true,
												labelString: 'Value'
											}
										}
									}
								}
							};

							window.onload = function() {
								var ctx = document.getElementById('canvas').getContext('2d');
								window.myLine = new Chart(ctx, config);
							};

							document.getElementById('randomizeData').addEventListener('click', function() {
								config.data.datasets.forEach(function(dataset) {
									dataset.data = dataset.data.map(function() {
										return randomScalingFactor();
									});

								});

								window.myLine.update();
							});

							var colorNames = Object.keys(window.chartColors);
							document.getElementById('addDataset').addEventListener('click', function() {
								var colorName = colorNames[config.data.datasets.length % colorNames.length];
								var newColor = window.chartColors[colorName];
								var newDataset = {
									label: 'Dataset ' + config.data.datasets.length,
									backgroundColor: newColor,
									borderColor: newColor,
									data: [],
									fill: false
								};

								for (var index = 0; index < config.data.labels.length; ++index) {
									newDataset.data.push(randomScalingFactor());
								}

								config.data.datasets.push(newDataset);
								window.myLine.update();
							});

							document.getElementById('addData').addEventListener('click', function() {
								if (config.data.datasets.length > 0) {
									var month = MONTHS[config.data.labels.length % MONTHS.length];
									config.data.labels.push(month);

									config.data.datasets.forEach(function(dataset) {
										dataset.data.push(randomScalingFactor());
									});

									window.myLine.update();
								}
							});

							document.getElementById('removeDataset').addEventListener('click', function() {
								config.data.datasets.splice(0, 1);
								window.myLine.update();
							});

							document.getElementById('removeData').addEventListener('click', function() {
								config.data.labels.splice(-1, 1); // remove the label first

								config.data.datasets.forEach(function(dataset) {
									dataset.data.pop();
								});

								window.myLine.update();
							});
						</script>
			<?php
            break;
	}
	echo "	<link rel=stylesheet type=text/css href=\"style/zTable.css\">
			<script language=\"javascript\" src=\"js/zMaster.js\"></script>
			<script language=javascript src=js/zTools.js></script>
	";
?>