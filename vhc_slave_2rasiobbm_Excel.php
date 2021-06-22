<?php
require_once('master_validation.php');
require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/eagrolib.php');

//	$pt=$_POST['pt'];
$unit=$_GET['unit'];
$tgl1=$_GET['tglAwal'];
$tgl2=$_GET['tglAkhir'];
$tglAwal=tanggalsystem($tgl1);
$tglAkhir=tanggalsystem($tgl2);

		$stream.="Rasio BBM : ".$unit." : ".$periode." (".$tgl1." - ".$tgl2.")<br>";
		$stream.="<table border=1>
			<tr>
			  <td bgcolor=#DEDEDE align=center>No.</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']." / MESIN</td>
			  <td bgcolor=#DEDEDE align=center>HM/KM</td>
			  <td bgcolor=#DEDEDE align=center>SATUAN</td>
        <td bgcolor=#DEDEDE align=center>BBM</td>  
        <td bgcolor=#DEDEDE align=center>RASIO</td>                                 
		</tr>";

$str="SELECT sum(a.jumlah) as jumlah, a.satuan, b.kodeorg, b.kodevhc, sum(jlhbbm) jumlahbbm FROM vhc_rundt a INNER JOIN vhc_runht b ON a.notransaksi=b.notransaksi WHERE kodeorg='".$unit."' AND tanggal between '".$tglAwal."' and '".$tglAkhir."' group by b.kodevhc ";
$qry=mysql_query($str);
$stream.="<br>";
$i=1;
while ($res=mysql_fetch_array($qry)) {
$stream.="<tr class=rowcontent>
    <td align=center>".$i."</td>
    <td>".$res['kodevhc']."</td>
    <td align='right'>".number_format($res['jumlah'],2)."</td>
    <td align='center'>".$res['satuan']."</td>
    <td align='right'>".number_format($res['jumlahbbm'],2)."</td>
    <td align='right'>".number_format($res['jumlahbbm']/$res['jumlah'],2)."</td>
  </tr>";
$i++;
}

$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'RasioBBM'.date('YmdHis');

if (0 < strlen($stream)) {
  if ($handle = opendir('tempExcel')) {
    while (false !== $file = readdir($handle)) {
      if (($file != '.') && ($file != '..')) {
        @unlink('tempExcel/' . $file);
      }
    }

    closedir($handle);
  }

  $handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

  if (!fwrite($handle, $stream)) {
    echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
    exit();
  }
  else {
    echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
  }

  closedir($handle);
}
?>