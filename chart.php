
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Highcharts Example</title>

		<style type="text/css">

		</style>
	</head>
	<body>
<script src="highchart/code/jquery.min.js"></script>
<script src="highchart/code/highcharts.js"></script>
<script src="highchart/code/modules/exporting.js"></script>
<script src="highchart/code/modules/export-data.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>



		<script type="text/javascript">
		
		
		$(function() {
			url = "chart_json.php";			
			$.post( url, { refData: "" })
			  .done(function( data ) {
				//$('#contentBody').html(data);
				//alert( "Data Loaded: " + data );
				var obj = jQuery.parseJSON( data );
				console.log(obj.qty_po[0]);
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Chart Qty PO & Amount PO'
					},
					subtitle: {
						text: ''
					},
					xAxis: {
						categories: [
							'Jan',
							'Feb',
							'Mar',
							'Apr',
							'May',
							'Jun',
							'Jul',
							'Aug',
							'Sep',
							'Oct',
							'Nov',
							'Dec'
						],
						crosshair: true
					},
					yAxis: {
						min: 0,
						title: {
							text: ''
						}
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Qty PO',
						data: obj.qty_po

					}, {
						name: 'Amount PO',
						data: obj.total_po

					}]
				});
			});
		});
		
		

		</script>
	</body>
</html>
