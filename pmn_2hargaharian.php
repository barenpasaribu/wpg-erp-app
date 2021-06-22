<?php

	require_once 'master_validation.php';
	include 'lib/eagrolib.php';
	include 'lib/zFunction.php';
	include_once 'lib/zLib.php';
	echo open_body();
	include 'master_mainMenu.php';

	$frm[0] = '';
	$frm[1] = '';
	$frm[2] = '';

	echo '	<script type="text/javascript" src="js/zMaster.js"></script>
			<script language=javascript src=js/zTools.js></script>
			<script language=javascript src=js/zReport.js></script>
			<script language=javascript src=js/chart.js></script>
			<script language=javascript src=js/utils.js></script>
			<script language=javascript src=\'js/pmn_2hargaharian.js\'></script>';
	echo '<style type="text/css">
		canvas{
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>';
	$arr = '##periodePsr##barang';
	$arr2 = '##komodoti##periodePsr2';
	$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$optBrg = $optPeriode;
	$str = 'SELECT distinct substr(tanggal,1,7) as periode, kodeproduk from ' . $dbname . '.pmn_hargapasar order by tanggal desc';
	$res = mysql_query($str);
	$listBarang = '';
	$period = array();

	while ($bar = mysql_fetch_object($res)) {
		if (!empty($listBarang)) {
			$listBarang .= ',';
		}

		$listBarang .= '\'' . $bar->kodeproduk . '\'';
		$period[$bar->periode] = $bar->periode;
	}

	foreach ($period as $p) {
		$optPeriode .= '<option value=\'' . $p . '\'>' . $p . '</option>';
	}

	$sBrng = 'SELECT DISTINCT kodebarang,namabarang FROM ' . $dbname . '.log_5masterbarang where kodebarang in (' . $listBarang . ') order by namabarang asc';

	#exit(mysql_error($conn));
	($qBrng = mysql_query($sBrng)) || true;

	while ($rBarang = mysql_fetch_assoc($qBrng)) {
		$optBrg .= '<option value=\'' . $rBarang['kodebarang'] . '\'>' . $rBarang['namabarang'] . '</option>';
	}

?>
	<div id="" style="width:100%;">
		<fieldset id="">
			<legend>
				<span class="judul">
					<b id="judul">Daily Price</b><br>
				</span>
			</legend>
			<div id="contentBox" style="overflow:auto;">
				<table border="0" cellspacing="0">
					<tbody>
						<tr class="tab">
							<td id="tabFRM0" onclick="pilihTab('daily');" onmouseover="chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');" onmouseout="chgBackgroundImg(this,'./images/tab1.png','#333333');" style="background-image: url(&quot;./images/tab1.png&quot;); color: rgb(51, 51, 51); font-weight: bolder; border-right: 1px solid rgb(222, 222, 222); width: 220px; height: 20px;">Trend Harga Harian</td>
							<td id="tabFRM1" onclick="pilihTab('monthly');" style="border-right: 1px solid rgb(222, 222, 222); height: 20px; width: 220px; background-image: url(&quot;./images/tab1.png&quot;); color: rgb(51, 51, 51);" onmouseover="chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');" onmouseout="chgBackgroundImg(this,'./images/tab1.png','#333333');">Trend Harga Bulanan</td>
							<!-- <td id="tabFRM2" onclick="pilihTab('chart');" style="border-right: 1px solid rgb(222, 222, 222); height: 20px; width: 220px; background-image: url(&quot;./images/tab1.png&quot;); color: rgb(51, 51, 51);" onmouseover="chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');" onmouseout="chgBackgroundImg(this,'./images/tab1.png','#333333');">Chart</td> -->
						</tr>
					</tbody>
				</table>
				<style>
					td, th {
					border: 1px solid #dddddd;
					text-align: left;
					padding: 8px;
					}

					tr:nth-child(even) {
					background-color: #dddddd;
					}
				</style>
				<fieldset style="display:" ';border-color:#2368b0;="" border-style:solid;border-width:2px;="" border-top:#1e5896="" solid="" 8px;="" background-color:#e0ecff;margin-left:0px;width:100%;'="" id="contentFRM0">
					<div id="trendHarga">
						Silahkan pilih Tabs	
					</div>
					<div id="trendHargaChart" style="width:95%; display: block;">
							<!-- <canvas id="canvas"></canvas> -->
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
					</div>
				</fieldset>
			</div>
		</fieldset>
	</div>
<?php 
	echo close_body();
?>