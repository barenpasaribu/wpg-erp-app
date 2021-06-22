<?php

	require_once 'master_validation.php';
	require_once 'config/connection.php';
	include_once 'lib/eagrolib.php';
	include 'lib/zMysql.php';
	include 'lib/zFunction.php';
	include_once 'lib/zLib.php';
	
	$proses = $_POST['proses'];

	switch ($proses) {
        case 'getOptionSupplier':
            $kodekelompok = $_POST['kode_klsupplier'];
            $query = "SELECT * FROM log_5supplier where kodekelompok ='".$kodekelompok."' AND status='1'";
            print_r($query);
            $queryAct = mysql_query($query);
            $optionSupplier = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
            while ($data = mysql_fetch_object($queryAct)){
                $optionSupplier .= '<option value='.$data->supplierid.'>'.$data->supplierid.' - '.$data->namasupplier.'</option>';
            }
            print_r($optionSupplier);
            break;

        case 'deleteSupplierHarga':
            $query = "DELETE FROM log_5supplier_harga
                    WHERE 
                    kode_klsupplier='".$_POST['kode_klsupplier']."' 
                    AND
                    kode_supplier='".$_POST['kode_supplier']."'
                    ";
            mysql_query($query);
            break;
        
        case 'simpanSupplierHarga':

            $tanggal = tanggaldgnbar($_POST['tanggal']);

            // Cek Data di DB
            $queryCek = "SELECT * FROM log_5supplier_harga 
            WHERE 
            kode_klsupplier='".$_POST['kode_klsupplier']."' 
            AND 
            kode_supplier='".$_POST['kode_supplier']."'
            ";
            $queryCekAct = mysql_query($queryCek);
            $hasilCek = mysql_num_rows($queryCekAct);
            if ($hasilCek == 0) {
                $query = "INSERT INTO log_5supplier_harga 
                        (kode_klsupplier, kode_supplier, tanggal, harga) 
                        VALUES 
                        (
                        '".$_POST['kode_klsupplier']."',
                        '".$_POST['kode_supplier']."',
                        '".$tanggal."','".$_POST['harga']."'
                        )";
            }else{
                $query = "UPDATE log_5supplier_harga
                SET 
                tanggal='".$tanggal."', 
                harga='".$_POST['harga']."'
                WHERE 
                kode_klsupplier='".$_POST['kode_klsupplier']."' 
                AND
                kode_supplier='".$_POST['kode_supplier']."'
                ";
            }
            mysql_query($query);
            break;
        case 'clone':
            $tanggal = date("Y-m-d");
            $harga = 1100;

            $getSupplier = "SELECT a.kode, b.supplierid FROM log_5klsupplier a
            JOIN log_5supplier b ON a.kode = b.kodekelompok
            WHERE a.isTBS ='1'
            ";

            $hasilGetSupplier = fetchData($getSupplier);
            foreach ($hasilGetSupplier as $key => $value) {
                print_r($value);
                // Cek Data di DB
                $queryCek = "SELECT * FROM log_5supplier_harga 
                WHERE 
                kode_klsupplier='".$value['kode']."' 
                AND 
                kode_supplier='".$value['supplierid']."'
                ";
                $queryCekAct = mysql_query($queryCek);
                $hasilCek = mysql_num_rows($queryCekAct);
                if ($hasilCek == 0) {
                    $query = "INSERT INTO log_5supplier_harga 
                            (kode_klsupplier, kode_supplier, tanggal, harga) 
                            VALUES 
                            (
                            '".$value['kode']."',
                            '".$value['supplierid']."',
                            '".$tanggal."','".$harga."'
                            )";
                }else{
                    $query = "UPDATE log_5supplier_harga
                    SET 
                    tanggal='".$tanggal."', 
                    harga='".$harga."'
                    WHERE 
                    kode_klsupplier='".$value['kode']."' 
                    AND
                    kode_supplier='".$value['supplierid']."'
                    ";
                }
                mysql_query($query);
            }


            
            
            break;
        case 'loadData':
            ?>
            <thead>
				<tr class="rowheader">
					<td>No</td>
					<td>Tanggal</td>
					<td>Kelompok Supplier</td>
					<td>Supplier</td>
					<td>Harga</td>
					<td>Action</td>
				</tr>
			</thead>
            <tbody id="container">
            <?php
            $limit = 50;
            $page = 0;
    
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                if ($page < 0) {
                    $page = 0;
                }
            }
            $offset = $page * $limit;

            // HARDCODE KARNA STRUKTUR DB NYA BERANTAKAN!
            $lokasitugas = substr($_SESSION['empl']['lokasitugas'],0,3);
            $getKlSupplier = "SELECT * FROM log_5klsupplier WHERE kelompok like '%".$lokasitugas."'";
            $dataKlSupplier = fetchData($getKlSupplier);
            
            $where = null;
            if ($lokasitugas == "SSP" || $lokasitugas == "LSP") {
                foreach ($dataKlSupplier as $key => $value) {
                    if ($where == null) {
                        $where = " WHERE kode_klsupplier ='".$value['kode']."' ";
                    }else{
                        $where .= " OR kode_klsupplier ='".$value['kode']."' ";
                    }
                }
            }
            

            $ql2 = 'SELECT count(*) as jumlahRow FROM ' . $dbname . '.log_5supplier_harga '.$where.' order by tanggal desc';
            ($query2 = mysql_query($ql2)) || true;
    
            while ($jsl = mysql_fetch_object($query2)) {
                $jumlahRow = $jsl->jumlahRow;
            }
    
            $str = 'SELECT * FROM ' . $dbname . '.log_5supplier_harga '.$where.' order by kode_supplier, tanggal desc  limit ' . $offset . ',' . $limit . '';
            $no = 0;
            if ($res = mysql_query($str)) {
                $barisData = mysql_num_rows($res);
                $disabled = '';
                if (0 < $barisData) {
                    while ($bar = mysql_fetch_object($res)) {
                        $no += 1;
                        
                        // mendapatkan nama kelompok supplier
                        $query1 = "SELECT kelompok FROM log_5klsupplier where kode='".$bar->kode_klsupplier."'";
                        $query1Act = mysql_query($query1);
                        $namakelompok = mysql_fetch_object($query1Act);
                        
                        // mendapatkan nama supplier
                        $query2 = "SELECT namasupplier FROM log_5supplier where supplierid='".$bar->kode_supplier."'";
                        $query2Act = mysql_query($query2);
                        $namasupplier = mysql_fetch_object($query2Act);

                        echo '  <tr class=rowcontent id=\'tr_' . $no . '\'>' . '
                                    <td>' . $no . '</td>' . '
                                    <td>' . tanggalnormal($bar->tanggal) . '</td>' . '
                                    <td>' . $bar->kode_klsupplier .' - '.$namakelompok->kelompok. '</td>' . '
                                    <td>' . $bar->kode_supplier .' - '.$namasupplier->namasupplier. ' </td>' . '
                                    <td>' . $bar->harga . '</td>';
                        echo '      <td style="text-align: center;">'.
                            '           <img src=images/application/application_delete.png class=resicon title="Delete" onclick="deleteSupplierHarga(\'' . $bar->kode_klsupplier . '\',\'' . $bar->kode_supplier . '\');">'
                            .'      </td>';
                        echo "  </tr>";
                    }
                }
                else {
                    echo '<tr class=rowcontent>
                            <td colspan=11 style="text-align: center; padding-top: 10px; padding-bottom: 10px;">' . 
                            $_SESSION['lang']['dataempty'] . '
                            </td>
                        </tr>';
                    $disabled = 'disabled';
                }
                if($jumlahRow < $limit ){
                    $disabled = 'disabled';
                }
                echo '</tbody>';
                echo '<tfoot>';
                echo '<tr>
                        <td colspan=11 align=center>' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jumlahRow . '
                        <br/>
                        <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>
                        <button class=mybutton '.$disabled.' onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>
                        </td>
                    </tr>';
                echo '</tfoot>';
            }
            else {
                echo ' Gagal,' . mysql_error($conn);
            }
            break;
	}
?>