<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';

    echo open_body();
    include 'master_mainMenu.php';

    OPEN_BOX('', '<b>Serah Terima Asset</b>');

    echo "<br>";

    $str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi 
    where induk = '".$_SESSION['empl']['kodeorganisasi']."' ";

    $res=mysql_query($str);
    $optunit="<option value=''>".$_SESSION['lang']['all']."</option>";
    $optunit="<option value=''></option>";

    $aunit = array();
    while($bar=mysql_fetch_object($res)) {
        $optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
        $aunit[] = $bar->kodeorganisasi;
    }

?>

	<script src="js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="datatables/css/jquery.dataTables.min.css">
    <script src="datatables/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" type="text/css" href="datatables/buttons/1.6.1/css/buttons.dataTables.min.css">
    <script src="datatables/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="datatables/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="datatables/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="datatables/buttons/1.6.1/js/buttons.print.min.js"></script>

    <!---  jquery UI -->
    <!-- <link rel="stylesheet" type="text/css" href="jquery-ui/themes/base/jquery-ui.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="jquery-ui/themes/redmond/jquery-ui.css"> -->
    <link rel="stylesheet" type="text/css" href="jquery-ui/themes/start/jquery-ui.css">
    
    <script src="jquery-ui/jquery-ui.min.js"></script>    

    <!-- Select2 -->
    <link rel="stylesheet" type="text/css" href="select2/4.0.13/css/select2.min.css">
    <script src="select2/4.0.13/js/select2.min.js"></script>    

    <!-- bootstrap  -->
    <!-- <link rel="stylesheet" type="text/css" href="bootsrap/css/bootstrap.min.css"> 
    <script src="bootstrap/js/bootstrap.min.js"></script>  -->

    
    <style>
        div.dataTables_wrapper {
            width: 1300px;
            margin: 0 auto;
        }

        /* table.dataTable thead tr {
            background-color: #0099ff;
            color : black;
        } */

        #data-table tbody th,
        #data-table tbody td {
            padding: 1px 1px;
            font: 12px/24px arial, san-serif !important;
        }

        #data-table tbody tr {
            font: 10px/24px arial, san-serif;
            height: 16px !important;
        }

        td.wrapok {
            white-space:normal;
            overflow:hidden;
            width:400px;
        }

        /* thead {
            white-space: nowrap;
        } */

    </style>
    
    <div id="container">
        <!-- modal crud -->
        <div id="modal-input">
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr height="20px">
                    <td><input type="text" id="trseq" name="trseq" size="30" visibility: hidden></td>
                </tr>
                <tr height="20px">
                    <td>Unit&nbsp</td>
                    <td><select id="unit" class="unit" style="width: 100%;" ></select></td>
                    <td><input type="text" id="namaunit" name="namaunit" size="30" visibility: hidden></td>
                </tr>
                <tr height="30px">
                    <td>Tgl.Terima&nbsp</td>
                    <td><input type=text id="tglterima" name="tglterima" onmousemove="setCalendar(this.id)" onkeypress="return false"  maxlength=10 style="width:100px;"></td>
                </tr>
                <tr height="30px">
                    <td>Karyawan&nbsp</td>
                    <td><select id="karyawanid" class="karyawanid" style="width: 100%;" ></select></td>
                    <td><input type="text" id="nik" name="nik" size="40" visibility: hidden></td>
                    <td><input type="text" id="namakaryawan" name="namakaryawan" size="40" visibility: hidden></td>
                </tr>
                <tr height="30px">
                    <td>Asset&nbsp</td>
                    <td><select id="kodeasset" class="kodeasset" style="width: 100%;" ></select></td>
                </tr>
                <tr height="30px">
                    <td>Nama Asset&nbsp</td>
                    <td><input type="text" id="namaasset" name="namaasset" size="50" disabled></td>
                </tr>
                <tr height="30px">
                    <td style="vertical-align:top">Keterangan Asset&nbsp</td>
                    <td><textarea style="resize:none;" id="keteranganasset" name='keteranganasset' rows='2' cols='60' maxlength="100" disabled></textarea><td>
                </tr>
                <tr height="30px">
                    <td style="vertical-align:top">Keterangan&nbsp</td>
                    <td><textarea style="resize:none;" id='keterangan' name='keterangan' rows='3' cols='70' maxlength="100"></textarea><td>
                </tr>
                <tr height="30px">
                    <td>Tgl.Berakhir&nbsp</td>
                    <td><input type=text id="tglberakhir" name="tglberakhir" onmousemove="setCalendar(this.id)" onkeypress="return false"  maxlength=10 style="width:100px;"></td>
                </tr>
            </table>
            <br>
            <div style="text-align: center;">
                <button id="proses_data">Simpan</button> 
                <button id="cancel_data">Batal</button>
            </div>
        </div>

        <div id="content">
            <table id="data-table" class="hover cell-border row-borders nowrap">
                <thead>
                    <tr>
                        <th width="70px">Aksi</th>
                        <th width="50px">Unit</th>
                        <th>Nama Unit</th>
                        <th>Tgl.Terima</th>
                        <th>Karyawan-Id</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Kode Asset</th>
                        <th>Nama Asset</th>
                        <th>Keterangan Asset</th>
                        <th>Keterangan</th>
                        <th>Tgl.Berakhir</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th width="50px">Unit</th>
                        <th>Nama Unit</th>
                        <th>Tgl. Terima</th>
                        <th>Karyawan-Id</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Kode Asset</th>
                        <th>Nama Asset</th>
                        <th>Keterangan Asset</th>
                        <th></th>
                        <th>Tgl.Berakhir</th>
                    </tr>
                </tfoot>
            </table>
        </div> 
    </div>
    <script>
        let cunit = <?php echo "'".$_SESSION['empl']['kodeorganisasi']."'" ?>;
        let imgEdit = <?php echo "'images/".$_SESSION['theme']."/edit.png'" ?>;
        let imgDelete = <?php echo "'images/".$_SESSION['theme']."/delete.png'" ?>;
        let imgPdf = <?php echo "'images/".$_SESSION['theme']."/pdf.jpg'" ?>;
        let clogin = <?php echo "'".$_SESSION['standard']['username']."'" ?>;
        let aOrganisasi = <?php echo json_encode($aunit); ?>;
        
    </script>
<?
    CLOSE_BOX();
    echo "</div>\r\n";
    echo "<script src=\"js/sdm_terimaAsset.js\"></script>";
    echo close_body();
?>