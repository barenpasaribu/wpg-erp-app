<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    echo open_body();
    include 'master_mainMenu.php';
    require_once 'lib/zLib.php';
    
    $optOrg = "<option value=''>Pilih data</option>";
    $x = 'select * from '.$dbname.".organisasi where length(kodeorganisasi)=4 and kodeorganisasi like '%M' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    $y = mysql_query($x);
    while ($z = mysql_fetch_assoc($y)) {
        $optOrg .= "<option value='".$z['kodeorganisasi']."'>".$z['namaorganisasi'].'</option>';
    }
    $optProduk = "<option value=''>Pilih data</option>";
    $optProduk .= "<option value='CPO'>CPO</option>";
    $optProduk .= "<option value='KERNEL'>KERNEL</option>";
    $nama = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
    echo "\r\n";
    OPEN_BOX('', 'Kelengkapan Data Loses');
    echo "  <fieldset style='width:500px;'>
                <legend>".$_SESSION['lang']['form']."</legend>
                    <table border=0 cellspacing=1 cellpadding=0>
                        <input id=id disabled type=hidden onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45>
                        <tr>
                            <td>Kodeorg</td>
                            <td>:</td>
                            <td>
                                <input id=kodeorg disabled value='".$_SESSION['empl']['lokasitugas']."' type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45>
                            </td>
                        </tr>
                        <tr>
                            <td>Produk</td>
                            <td>:</td>
                            <td>
                                <select id=produk>".$optProduk."
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Item</td>
                            <td>:</td>
                            <td>
                                <input id=namaitem type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45>
                            </td>
                        <tr>
                            <td>Standard to Sample</td>
                            <td>:</td>
                            <td>
                                <input id=standard value=0 type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" ></td>
                        <tr>
                            <td>Satuan</td>
                            <td>:</td>
                            <td>
                                <input id=satuan value=% type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\">
                            </td>
                        </tr>
                        <tr>
                            <td>Faktor Konversi 1</td>
                            <td>:</td>
                            <td>
                                <input id=faktor_konversi_1 value=0 type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" >
                            </td>
                        </tr>
                        <tr>
                            <td>Faktor Konversi 2</td>
                            <td>:</td>
                            <td>
                                <input id=faktor_konversi_2 value=0 type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" >
                            </td>
                        </tr>
                        <tr>
                            <td>Faktor Konversi 3</td>
                            <td>:</td>
                            <td>
                                <input id=faktor_konversi_3 value=0 type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" >
                            </td>
                        </tr>
                        <tr>
                            <td>Losses to TBS (%)</td>
                            <td>:</td>
                            <td>
                                <input id=losses_to_tbs value=0 type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" >
                            </td>
                        </tr>
                        <tr>
                            <td>Linked to</td>
                            <td>:</td>
                            <td>
                                <select id=linked_to style=\"width:150px;\">
                                    <option value=''>Tidak Ada</option>
                                    <option value='cpo_usb'>CPO - USB</option>
                                    <option value='cpo_empty_bunch'>CPO - Empty Bunch</option>
                                    <option value='cpo_fibre_cyclone'>CPO - Fibre Cyclone</option>
                                    <option value='cpo_nut_from_polishingdrum'>CPO - Nut From Polishingdrum</option>
                                    <option value='cpo_effluent'>CPO - Effluent</option>

                                    <option value='kernel_loses_usb'>PK - USB</option>
                                    <option value='kernel_loses_fibre_cyclone'>PK - Fibre Cyclone</option>
                                    <option value='kernel_loses_ltds_1'>PK - LTDS 1</option>
                                    <option value='kernel_loses_ltds_2'>PK - LTDS 2</option>
                                    <option value='kernel_loses_claybath'>PK - Clybath</option>
                                </select>
                            </td>
                            <td> (Pabrik Produksi)</td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td> </td>
                            <td> <button class=mybutton onclick=simpan()>Simpan</button> <button class=mybutton onclick=muatUlang()>Reload</button></td>
                        </tr>
                    </table>
                </fieldset>
                <input type=hidden id=method value='insert'>";
    ?>
            <fieldset style='width:100%;'>
                <legend>Susunan Data</legend>
                <div id="tableKelengkapanLoses"></div>
            </fieldset>
    <?php
    //onclick=\"edit('".$t['id']."','".$t['kodeorg']."','".$t['produk']."','".$t['namaitem']."','".$t['standard']."','".$t['satuan']."','".$t['faktor_konversi_1']."','".$t['faktor_konversi_2']."','".$t['faktor_konversi_3']."','".$t['losses_to_tbs']."','".$t['linked_to']."');\"
    CLOSE_BOX();

?>
    <script type="text/javascript" src="lib/awan/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="lib/awan/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="lib/awan/sweetalert2/dist/sweetalert2.min.js"></script>
	<!-- <script type="text/javascript" src="js/zTools.js"></script> -->
	<script type="text/javascript" src="js/pabrik_5kelengkapanloses.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/DataTables-1.10.21/css/jquery.dataTables.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/DataTables-1.10.21/css/dataTables.bootstrap4.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/DataTables-1.10.21/css/dataTables.jqueryui.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'lib/awan/sweetalert2/dist/sweetalert2.min.css'));
            loadData();
            // $('#dataKelengkapanLoses').DataTable();
		});
	</script>
	
	</body>
</html>