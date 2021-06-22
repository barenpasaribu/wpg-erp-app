<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    
    echo open_body();
    include 'master_mainMenu.php';

    $str = 'select 
                kodeorganisasi 
            from 
                '.$dbname.".organisasi 
            where 
                tipe='PABRIK'
            AND
            kodeorganisasi LIKE '".$_SESSION['empl']['induklokasitugas']."%'
            order by 
                kodeorganisasi";
    $res = mysql_query($str);

    // $optpabrik = '<option value=>Pilih Pabrik</option>';
    $optpabrik = null;
    while ($bar = mysql_fetch_object($res)) {
        $optpabrik .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'</option>';
    }
    $sPeriode = 'select distinct substring(tanggal,1,7) as periode from '.$dbname.'.pabrik_produksi order by tanggal desc ';
    $qPeriode = mysql_query($sPeriode);
    while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
        $optper .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
    }

    OPEN_BOX('', '<b>'.$_SESSION['lang']['rprodksiPabrik'].' </b>');
    echo "  <fieldset>
                ".$_SESSION['lang']['kodeorganisasi'].':
                <select id=kodeorg>'.$optpabrik."</select>
                Dari
                <input autocomplete=off type=text class=myinputtext id=tanggal_awal onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"> 
                Sampai
                <input autocomplete=off type=text class=myinputtext id=tanggal_akhir onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"> 
                <button class=mybutton onclick=getLaporan()>Cari</button>
            ";
    CLOSE_BOX();
    echo "<div id='table_laporan_produksi_harian'></div>";
    // OPEN_BOX('', '');
    // echo "<div id=container style='width:100%;height:500px overflow:scroll'></div>";
    // CLOSE_BOX();
    

?>
    <script type="text/javascript" src="lib/awan/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="lib/awan/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="lib/awan/sweetalert2/dist/sweetalert2.min.js"></script>
    
	<script type="text/javascript" src="js/pabrik_2produksi.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/DataTables-1.10.21/css/jquery.dataTables.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/DataTables-1.10.21/css/dataTables.bootstrap4.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/DataTables-1.10.21/css/dataTables.jqueryui.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/sweetalert2/dist/sweetalert2.min.css'));
		});
	</script>
	
	</body>
</html>