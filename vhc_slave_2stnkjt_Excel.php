<?php

require_once('master_validation.php');

require_once('config/connection.php');

//require_once('lib/nangkoelib.php');

require_once('lib/eagrolib.php');



//	$pt=$_POST['pt'];

$unit=$_GET['unit'];

//$tgl1=$_GET['tglAwal'];

//$tgl2=$_GET['tglAkhir'];

//$tglAwal=tanggalsystem($tgl1);

//$tglAkhir=tanggalsystem($tgl2);



//		$stream.="Jatuh Tempo STNK  : ".$unit." : ".$periode." (".$tgl1." - ".$tgl2.")<br>";
		$stream.="Jatuh Tempo STNK  : ".$unit." : ";

		$stream.="<table border=1>

			<tr>

			  <td bgcolor=#DEDEDE align=center>No.</td>

			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']." / KEND</td>
			  <td bgcolor=#DEDEDE align=center>Jenis</td>
			  <td bgcolor=#DEDEDE align=center>No.Rangka</td>
	              	  <td bgcolor=#DEDEDE align=center>No.Mesin</td>  
        		  <td bgcolor=#DEDEDE align=center>Tahun Perolehan</td>    
 		          <td bgcolor=#DEDEDE align=center>Tgl.J/T STNK</td>
 		          <td bgcolor=#DEDEDE align=center>Tgl.Akhir STNK</td>
 		          <td bgcolor=#DEDEDE align=center>Tgl.Akhir KIR</td>
 		          <td bgcolor=#DEDEDE align=center>Tgl.Akhir Ijin Bongkar Muat</td>
 		          <td bgcolor=#DEDEDE align=center>Tgl.Akhir Ijin Angkut</td>
    

		</tr>";



// $str="SELECT sum(a.jumlah) as jumlah, a.satuan, b.kodeorg, b.kodevhc, sum(jlhbbm) jumlahbbm FROM vhc_rundt a INNER JOIN vhc_runht b ON a.notransaksi=b.notransaksi WHERE kodeorg='".$unit."' AND tanggal between '".$tglAwal."' and '".$tglAkhir."' group by b.kodevhc ";

$str="SELECT kodevhc, jenisvhc, nomorrangka,nomormesin,tahunperolehan,tgljtstnk, tglakhirstnk,tglakhirkir,tglakhirijinbm,tglakhirijinang ".
"FROM vhc_5master WHERE kodeorg='".$unit."' ";

$qry=mysql_query($str);

$stream.="<br>";

$i=1;

while ($res=mysql_fetch_array($qry)) {

$stream.="<tr class=rowcontent>

    	<td align=center>".$i."</td>

	<td>".$res['kodevhc']."</td>

	<td align='center'>".$res['jenisvhc']."</td>
	<td align='center'>".$res['nomorrangka']."</td>
	<td align='center'>".$res['nomormesin']."</td>
	<td align='center'>".$res['tahunperolehan']."</td>
	<td align='center'>".$res['tgljtstnk']."</td>
	<td align='center'>".$res['tglakhirstnk']."</td>
	<td align='center'>".$res['tglakhirkir']."</td>
	<td align='center'>".$res['tglakhirijinbm']."</td>
	<td align='center'>".$res['tglakhirijinang']."</td>

  </tr>";

$i++;

}



$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];

$nop_ = 'Jatuh Tempo STNK'.date('YmdHis');



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