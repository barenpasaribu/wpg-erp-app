<?
require_once('master_validation.php');
//include('lib/nangkoelib.php');
include('lib/eagrolib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_grafikturnover.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper('Laporan Turn Over Karyawan').'</b>');
$str1="select distinct left(periode,4) as periode from ".$dbname.".sdm_turnovervw";
//=================ambil unit;  
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING') {

	$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where induk = '".$_SESSION['empl']['kodeorganisasi']."' ";

} else {
	$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."' ";

}

extract($_POST);
extract($_GET);

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
  if($bar->kodeorganisasi==$unit){  
    $selected="selected";
  }else{
    $selected="";
  }

	$optunit.="<option value='".$bar->kodeorganisasi."' ".$selected.">".$bar->namaorganisasi."</option>";
}

$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
  if($bar1->periode==$periode){  
    $selected="selected";
  }else{
    $selected="";
  }
	$optperiode.="<option value='".$bar1->periode."' ".$selected.">".$bar1->periode."</option>";
}

echo"<fieldset>
     <legend>Laporan Turn Over Karyawan</legend>
     <form name='form' id='form'>
	 ".$_SESSION['lang']['unit']."<select id=unit name='unit' style='width:150px;'>".$optunit."</select>
	 	 	 ".$_SESSION['lang']['periode']."<select id=periode name='periode' style='width:170px;'>".$optperiode."</select>
	 <button class=mybutton onclick=getlaporan()>".$_SESSION['lang']['proses']."</button>
	 </form>
   </fieldset>";
CLOSE_BOX();
OPEN_BOX();

if($periode!='' && $unit!=''){
  for($j=1;$j<13;$j++){
    if($j<10){ $j='0'.$j;}
    $periode1=$periode."-".$j;
 
    $sql= mysql_query("SELECT IF(SUM(jumlah) IS NULL, 0, SUM(jumlah)) as total FROM sdm_turnovervw WHERE periode='".$periode1."' AND lokasitugas='".$unit."' ") or die(mysql_error());
    $hasil=mysql_fetch_array($sql);
    $data[$j]['jumlah']=$hasil['total'];
    $data[$j]['periode']=$periode1;

    $data1.=$hasil['total'].",";   
}


?>
<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Highcharts Example</title>

    <style type="text/css">
#kontainer {
  min-width: 310px;
  max-width: 800px;
  height: 400px;
  margin: 0 auto
}
    </style>
  </head>
  <body>
<script src="lib/code/highcharts.js"></script>
<script src="lib/code/modules/series-label.js"></script>
<script src="lib/code/modules/exporting.js"></script>
<script src="lib/code/modules/export-data.js"></script>
<table align="center">
  <tr>
    <td width=800><div id='kontainer'></div></td>
    <td valign="top">
      <table class=sortable cellspacing=1 border=0 width=300>
       <thead>
        <tr class=rowcontent>
          <td>PERIODE</td>
          <td>JUMLAH</td>
        </tr>
        </thead>
    <?php
      foreach ($data as $key) {
        echo "<tr class=rowcontent><td>".$key['periode']."</td><td align='right'>".$key['jumlah']."</td></tr>";
      }
    ?>
    </table>
    </td>
  </tr>
</table>

<script type="text/javascript">
Highcharts.chart('kontainer', {

    title: {
        text: 'GRAFIK TURN OVER KARYAWAN PERIODE <?php echo $periode; ?> '
    },

    subtitle: {
//        text: 'Source: thesolarfoundation.com'
    },

    xAxis: {
                categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                crosshair: true
            },
    yAxis: {
        title: {
            text: 'Number of Employees'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
       //     pointStart: 2010
        }
    },

    series: [{
        name: 'Turn Over',
//        data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
        data: [<?php 
                echo $data1;
           ?>]
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
    </script>
  </body>
</html>
<?php
}
CLOSE_BOX();
close_body();
  //<td align=center>".$_SESSION['lang']['periode']."</td>
?>