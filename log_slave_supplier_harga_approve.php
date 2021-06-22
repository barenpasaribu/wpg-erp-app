<?php

	require_once 'master_validation.php';
	require_once 'config/connection.php';
	include_once 'lib/eagrolib.php';
	include 'lib/zMysql.php';
	include 'lib/zFunction.php';
	include_once 'lib/zLib.php';
	
	$proses = $_POST['proses'];

	switch ($proses) {
        case 'showForm':
            echo "<br>";
            OPEN_BOX("", "<b id=judul>FORM INPUT APPROVE</b>");
            $optKlSupplier = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
            $optSupplier = $optKlSupplier;

            if (substr($_SESSION['empl']['lokasitugas'],0,3) == "SSP" || substr($_SESSION['empl']['lokasitugas'],0,3) == "LSP") {
                $queryKlSupplier = 'SELECT kode, kelompok FROM '.$dbname.'.log_5klsupplier where kelompok like "%'.substr($_SESSION['empl']['lokasitugas'],0,3).'" and isTBS=1';
            }else{
                $queryKlSupplier = 'SELECT kode, kelompok FROM '.$dbname.'.log_5klsupplier where isTBS=1';
            }
            $runQueryKlSupplier = mysql_query($queryKlSupplier);
            while ($resultQueryKlSupplier = mysql_fetch_assoc($runQueryKlSupplier)) {
                $optKlSupplier .= '<option value=\'' . $resultQueryKlSupplier['kode'] . '\'>' . $resultQueryKlSupplier['kode'] . ' - ' . $resultQueryKlSupplier['kelompok'] . '</option>';
            }
            ?>
            <fieldset style=width:350px>
            <legend>Supplier</legend>
            <table>
                <tr>
                    <td>Tanggal</td>
                    <td><input type="text" value="<?= date("d-m-Y") ?>" class="myinputtext" id="tanggal" onmousemove="setCalendar(this.id)" onkeypress="return" false;="">
                </tr>
                <tr>
                    <td>Kelompok Supplier</td>
                    <td>
                        <select id="kode_klsupplier" onchange="showCheckbox()">
                            <?= $optKlSupplier ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Kenaikan Harga </td>
                    <td>
                        <input type="number" id="harga" name="harga" maxlength="10" style="width:98%;" required>
                    </td>
                </tr>
                <tr>
                    <td>Operator Kenaikan</td>
                    <td>
                        <select id="operator_kenaikan">
                            <option value="rp">Rp</option>
                            <option value="%">%</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Fluktuasi</td>
                    <td>
                        <select id="fluktuasi">
                            <option value="tetap">Tetap</option>
                            <option value="naik">Naik</option>
                            <option value="turun">Turun</option>
                        </select>
                    </td>
                </tr>
            </table>
            <br>
            <button disabled id="btnSimpan" class="mybutton" onclick="simpanSupplierHarga()">
                Simpan
            </button>
            <button id="btnSimpan" class="mybutton" onclick="batal()">
                Batal
            </button>
            </fieldset>
            <?php
            CLOSE_BOX();
            echo "<div id='menuPilihSupplier' style='display: none;'>";
            OPEN_BOX();
            echo '<fieldset  style=width:800px><legend>List Supplier</legend>';
            echo "<div id='listSupplier'>";
            echo "</div>";
            echo '</fieldset>';
            CLOSE_BOX();
            echo "</div>";
            break;
        case 'simpanSupplierHistory':

            $datasupplier = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
            
            // $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($_POST['tanggal'],6,4) ."-". substr($_POST['tanggal'],3,2) ."-". substr($_POST['tanggal'],0,2);
  
            $arraySupplierStatus = $_POST['arraySupplierStatus'];
/*
            $cek= "SELECT x.tanggal FROM (select tanggal from log_supplier_harga_temporary where kode_klsupplier='".$_POST['kode_klsupplier']."' UNION select tanggal_akhir AS tanggal from log_supplier_harga_history 
                where kode_klsupplier='".$_POST['kode_klsupplier']."') AS x ORDER BY x.tanggal DESC LIMIT 1"; 
            $hasilcek=fetchData($cek);
            
            $date = $hasilcek[0]['tanggal'];
            $date1 = str_replace('-', '/', $date);
            $tomorrow = date('d-m-Y',strtotime($date1 . "+1 days"));
            if($tomorrow!=$_POST['tanggal']){
                echo "silahkan entry tanggal ".$tomorrow;
            }else{
                echo "sukses tanggal ".$tomorrow." !! ";
            }            
*/
            // print_r($_POST);
            // die();
            $queryList="INSERT INTO log_supplier_harga_temporary_list
                            (kode_klsupplier, tanggal, operator_kenaikan, fluktuasi, harga_kenaikan, created_by) 
                            VALUES 
                            (
                            '".$_POST['kode_klsupplier']."',
                            '".$tanggal."',
                            '".$_POST['operator_kenaikan']."',
                            '".$_POST['fluktuasi']."',
                            '".$_POST['harga']."',
                            '".$_SESSION['empl']['karyawanid']."'
                            )";
            mysql_query($queryList);
            $listId = mysql_insert_id();
            $i = 0;
            $dataTertolak = null;                 
            $dataTertolakDuplicate = null;                 

            foreach ($arraySupplierStatus as $key => $value) {
                if($value == 1){
                    // mengecek data supplier yang diupdate sudah data terupdate di log_5supplier_harga
                    $queryCekSupplierTerupdate = "  SELECT 
                                                        * 
                                                    FROM 
                                                        log_5supplier_harga 
                                                    WHERE 
                                                        kode_supplier = '".$_POST['arraySupplier'][$key]."'
                                                ";
                    
                    $dataCekSupplierTerupdate = fetchData($queryCekSupplierTerupdate);

                    $date = $dataCekSupplierTerupdate[0]['tanggal'];
                    $date1 = str_replace('-', '/', $date);
                    $tomorrow = date('d-m-Y',strtotime($date1 . "+1 days"));
                    
                    if ($tomorrow==$_POST['tanggal']) {
                        // cek data di log_supplier_harga_tenporary ada yang duplikat
                        $queryCekSupplierDuplicateDataTemporary = "     SELECT 
                                                                            * 
                                                                        FROM 
                                                                            log_supplier_harga_temporary a
                                                                        JOIN
                                                                            log_supplier_harga_temporary_list b
                                                                        ON
                                                                            a.temporary_list_id = b.id
                                                                        WHERE 
                                                                            a.kode_supplier = '".$_POST['arraySupplier'][$key]."'
                                                                        AND
                                                                            a.tanggal
                                                                        AND
                                                                            (
                                                                                b.status1 = 2
                                                                                OR
                                                                                b.status2 = 2
                                                                            )

                                                                    ";

                        $dataCekSupplierDuplicateDataTemporary = fetchData($queryCekSupplierDuplicateDataTemporary);
                        
                        if (!empty($dataCekSupplierDuplicateDataTemporary[0])) {
                            $dataTertolakDuplicate = $_POST['arraySupplier'][$key];
                        }
                        $query="INSERT INTO log_supplier_harga_temporary
                            (temporary_list_id, kode_klsupplier, kode_supplier, fee, tanggal, harga_kenaikan, fluktuasi, operator_kenaikan) 
                            VALUES 
                            (
                            '".$listId."',
                            '".$_POST['kode_klsupplier']."',
                            '".$_POST['arraySupplier'][$key]."',
                            '".$_POST['arrayFee'][$key]."',
                            '".$tanggal."',
                            '".$_POST['harga']."',
                            '".$_POST['fluktuasi']."',
                            '".$_POST['operator_kenaikan']."'
                            )";
                        
                        mysql_query($query);
                    }else{
                        $dataTertolak[$i] = $_POST['arraySupplier'][$key];
                    }                    
                } 
                $i++;
            }

            // menghapus temporary list jika datanya kosong
            $queryCekDataTemporary = "  SELECT 
                                            * 
                                        FROM 
                                            log_supplier_harga_temporary 
                                        WHERE 
                                            temporary_list_id = '".$listId."'
                                        ";

            $dataCekDataTemporary = fetchData($queryCekDataTemporary);
            if (empty($dataCekDataTemporary[0])) {
                $queryDeleteDataTemporary = "   DELETE 
                                                FROM 
                                                    log_supplier_harga_temporary_list
                                                WHERE 
                                                    id = '".$listId."'";

                mysql_query($queryDeleteDataTemporary);
            }

            if (empty($dataTertolak)) {
                echo "Data berhasil disimpan";
            } else {
                 echo "MAAF UPDATE HARGA TERTOLAK , ";
              //  echo "MAAF UPDATE HARGA TERTOLAK \n supplier dengan id ";
                 echo "GAGAL SIMPAN SUPPLIER \n";
                foreach ($dataTertolak as $dataTertolakK => $dataTertolakV) {
                    echo $dataTertolakV." - ".$datasupplier[$dataTertolakV];
                    echo ", \n";
                }
                echo "\n Silahkan entry tanggal ".$tomorrow;
            }
           
            break;
        case 'showCheckbox':
            $date=substr($_POST['tanggal'],6,4) ."-". substr($_POST['tanggal'],3,2) ."-". substr($_POST['tanggal'],0,2);
            $date1 = str_replace('-', '/', $date);
            $yesterday = date('Y-m-d',strtotime($date1 . "-1 days"));
            
            $kodekelompok = $_POST['kode_klsupplier'];
            $query="SELECT a.kode_klsupplier, a.kode_supplier, a.tanggal, 
                    b.namasupplier AS nama_supplier, a.harga FROM log_5supplier_harga a
                    JOIN 
                    log_5supplier b ON a.kode_supplier = b.supplierid
                    WHERE 
                    kode_klsupplier ='".$kodekelompok."' and b.status='1' ";
            
            $queryAct = mysql_query($query);
            $checkboxSupplier = '';
            ?>
            <table>
                <tr>
                    <td><input checked type='checkbox' id='checkall' name='checkall' onchange='pilihSemua()'/></td>
                    <td><b>Pilih Semua</b></td>
                    <td style="padding-left: 20px;"><b>Harga</b></td>
                    <td style="padding-left: 20px;"><b>Fee</b></td>
                </tr>
                
            
            <?php
            while ($data = mysql_fetch_object($queryAct)){

                echo "<tr>";
                echo "<td>";
                if($data->tanggal==$yesterday){

                echo "      <input checked type='checkbox' id='".$data->kode_supplier."' name='supplier[]' value='".$data->kode_supplier."'>";
                }
                echo "  </td>";
                echo "  <td>";
                echo "      <label for='".$data->kode_supplier."'>[".$data->kode_supplier."] - ".$data->nama_supplier."</label>";
                echo "  </td>";
                echo "  <td style='padding-left: 20px;'>";
                echo "      <b>".$data->harga."</b> ( ".tanggalnormal($data->tanggal)." )";
                echo "  </td>";
                echo "  <td style='padding-left: 20px;'>";
                echo '      <input type="number" name="fee[]"><br>';
                echo "  </td>";
                
                echo "</tr>";
            }
            echo "</table>";
            break;
        case 'loadData':
            ?>
            <thead>
                <tr class='rowheader'>
                    <th>Tanggal</th>
                    <th>Kelompok Supplier</th>
                    <th>Fluktuasi Harga</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="container">
            <?php
            $lokasitugas = substr($_SESSION['empl']['lokasitugas'],0,3);
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
            $ql2 = 'SELECT count(*) as jumlahRow FROM ' . $dbname . '.log_supplier_harga_temporary_list '.$where.'  order by tanggal desc';
            ($query2 = mysql_query($ql2)) || true;
    
            while ($jsl = mysql_fetch_object($query2)) {
                $jumlahRow = $jsl->jumlahRow;
            }
    
            $str = 'SELECT * FROM ' . $dbname . '.log_supplier_harga_temporary_list '.$where.' ORDER BY tanggal DESC  limit ' . $offset . ',' . $limit . '';
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

                        echo '  <tr class=rowcontent id=\'tr_' . $no . '\'>' . '
                                    <td onclick="masterPDF(\'log_supplier_harga_history\','.$bar->id.',\'\',\'log_slave_supplier_harga_list\',\'event\')">' . tanggalnormal($bar->tanggal) . '</td>' . '
                                    <td onclick="masterPDF(\'log_supplier_harga_history\','.$bar->id.',\'\',\'log_slave_supplier_harga_list\',\'event\')">' . $bar->kode_klsupplier .' - '.$namakelompok->kelompok. '</td>';
                        if($bar->fluktuasi == "naik"){
                            echo '<td style="color:white; background-color: green;" onclick="masterPDF(\'log_supplier_harga_history\','.$bar->id.',\'\',\'log_slave_supplier_harga_list\',\'event\')">' . $bar->harga_kenaikan . '</td>';
                        }
                        if($bar->fluktuasi == "turun"){
                            echo '<td style="color:white; background-color: red;" onclick="masterPDF(\'log_supplier_harga_history\','.$bar->id.',\'\',\'log_slave_supplier_harga_list\',\'event\')">' . $bar->harga_kenaikan . '</td>';
                        }
                        if($bar->fluktuasi == "tetap"){
                            echo '<td onclick="masterPDF(\'log_supplier_harga_history\','.$bar->id.',\'\',\'log_slave_supplier_harga_list\',\'event\')">' . $bar->harga_kenaikan . '</td>';
                        }
                        // echo '      <td style="text-align: center;">'.
                        //     '           <img src=images/application/application_delete.png class=resicon title="Delete" onclick="deleteSupplierHarga(\'' . $bar->kode_klsupplier . '\',\'' . $bar->kode_supplier . '\');">'
                        //     .'      </td>';
                        ?>

                        <?php 
                            $isReject = false;
                            $isApprove = false;
                            $status = '';
                            if ($bar->status1 == 2) {
                                $isReject = true;
                                $isApprove = false;
                                $status = '<b style="color: red;">Rejected</b>';
                            }else{
                                if ($bar->status2 == 2) {
                                    $isReject = true;
                                    $isApprove = false;
                                    $status = '<b style="color: red;">Rejected</b>';
                                }else{
                                    if ($bar->status1 == 1 && $bar->status2 == 1) {
                                        $isReject = false;
                                        $isApprove = true;
                                        $status = '<b style="color: green;">Approved</b>';
                                    }else{
                                        $isReject = false;
                                        $isApprove = false;
                                        $status = '<b style="color: black;">Unapproved</b>';
                                    }
                                }
                            }	
                        ?>
                        <td><?= $status; ?></td>
                        <td>
                            <?php 
                                $queryCekApprovel = "SELECT * FROM setup_approval
                                WHERE
                                karyawanid = '".$_SESSION['empl']['karyawanid']."'
                                ";

                                $queryCekApprovelAct = mysql_query($queryCekApprovel);
                                $isApprove1 = false;
                                $isApprove2 = false;
                                while($dataCekApproval = mysql_fetch_object($queryCekApprovelAct)){
                                    if ($dataCekApproval->applikasi == "TBS1") {
                                        $isApprove1 = true;
                                    }
                                    if ($dataCekApproval->applikasi == "TBS2") {
                                        $isApprove2 = true;
                                    }
                                }
                                if (empty($bar->persetujuan1)) {
                                    if ($isApprove1) {
                                        echo "<button class=mybutton onclick=approve(1,".$bar->id.",'".$_SESSION['empl']['karyawanid']."')>Approve 1</button>";	
                                        echo "<button class=mybutton onclick=reject(1,".$bar->id.",'".$_SESSION['empl']['karyawanid']."')>Reject 1</button>";	
                                    }
                                }else{
                                    if ($bar->status1 == 1) {
                                        echo "[Disetujui 1] ";
                                    }
                                    if ($bar->status1 == 2) {
                                        echo '<b style="color: red;">[Ditolak 1]</b>';
                                    }
                                }
                                if ($bar->status1 !=2) {	
                                    if (empty($bar->persetujuan2)) {
                                        if ($isApprove2) {
                                            echo "<button class=mybutton onclick=approve(2,".$bar->id.",'".$_SESSION['empl']['karyawanid']."')>Approve 2</button>";	
                                            echo "<button class=mybutton onclick=reject(2,".$bar->id.",'".$_SESSION['empl']['karyawanid']."')>Reject 2</button>";	
                                        }
                                    }else{
                                        if ($bar->status2 == 1) {
                                            echo " [Disetujui 2]";
                                        }
                                        if ($bar->status2 == 2) {
                                            echo '<b style="color: red;">[Ditolak 2]</b>';
                                        }
                                    }
                                }

                                if ($bar->status1 != 1 || $bar->status2 != 1) {
                                    echo " <button class=mybutton onclick=deleteData(".$bar->id.")>Delete</button>";	
                                }
                                
                            ?>	
                        </td>

                        <?php
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
        case 'listData':
            $query="SELECT * FROM log_supplier_harga_temporary_list
			        ORDER BY id DESC
                    ";
            $queryAct = mysql_query($query);
            ?>
            <table class=sortable cellspacing="1">
                <thead>
                    <tr class='rowheader'>
                        <th>Tanggal</th>
                        <th>Kelompok Supplier</th>
                        <th>Harga Kenaikan</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="container">
                    <?php
                        while ($data = mysql_fetch_object($queryAct)){
                    ?>
                    <tr class=rowcontent id="tr_<?= $no; ?>">
                        <td><?= tanggalnormal($data->tanggal); ?></td>
                        <?php 
                            $queryGetNamaKlSupplier = "SELECT * FROM log_5klsupplier WHERE kode='".$data->kode_klsupplier."'";
                            $queryGetNamaKlSupplierAct = mysql_query($queryGetNamaKlSupplier);
                            $dataNamaKlSupplier = mysql_fetch_object($queryGetNamaKlSupplierAct);
                        ?>
                        <td><?= $data->kode_klsupplier; ?> - <?= $dataNamaKlSupplier->kelompok; ?></td>
                        <?php
                            $kenaikanharga = '';
                            if ($data->operator_kenaikan == 'rp') {
                                $kenaikanharga = "Rp".$data->harga_kenaikan;
                            }
                            if ($data->operator_kenaikan == '%') {
                                $kenaikanharga = $data->harga_kenaikan."%";
                            }
                        ?>
                        <td><?= $kenaikanharga; ?></td>
                        <?php 
                            $isReject = false;
                            $isApprove = false;
                            $status = '';
                            if ($data->status1 == 2) {
                                $isReject = true;
                                $isApprove = false;
                                $status = '<b style="color: red;">Rejected</b>';
                            }else{
                                if ($data->status2 == 2) {
                                    $isReject = true;
                                    $isApprove = false;
                                    $status = '<b style="color: red;">Rejected</b>';
                                }else{
                                    if ($data->status1 == 1 && $data->status2 == 1) {
                                        $isReject = false;
                                        $isApprove = true;
                                        $status = '<b style="color: green;">Approved</b>';
                                    }else{
                                        $isReject = false;
                                        $isApprove = false;
                                        $status = '<b style="color: black;">Unapproved</b>';
                                    }
                                }
                            }	
                        ?>
                        <td><?= $status; ?></td>
                        <td>
                            <?php 
                                $queryCekApprovel = "SELECT * FROM setup_approval
                                WHERE
                                karyawanid = '".$_SESSION['empl']['karyawanid']."'
                                ";

                                $queryCekApprovelAct = mysql_query($queryCekApprovel);
                                $isApprove1 = false;
                                $isApprove2 = false;
                                while($dataCekApproval = mysql_fetch_object($queryCekApprovelAct)){
                                    if ($dataCekApproval->applikasi == "TBS1") {
                                        $isApprove1 = true;
                                    }
                                    if ($dataCekApproval->applikasi == "TBS2") {
                                        $isApprove2 = true;
                                    }
                                }
                                if (empty($data->persetujuan1)) {
                                    if ($isApprove1) {
                                        echo "<button class=mybutton onclick=approve(1,".$data->id.",'".$_SESSION['empl']['karyawanid']."')>Approve 1</button>";	
                                        echo "<button class=mybutton onclick=reject(1,".$data->id.",'".$_SESSION['empl']['karyawanid']."')>Reject 1</button>";	
                                    }
                                }else{
                                    if ($data->status1 == 1) {
                                        echo "[Disetujui 1] ";
                                    }
                                    if ($data->status1 == 2) {
                                        echo '<b style="color: red;">[Ditolak 1]</b>';
                                    }
                                }
                                if ($data->status1 !=2) {	
                                    if (empty($data->persetujuan2)) {
                                        if ($isApprove2) {
                                            echo "<button class=mybutton onclick=approve(2,".$data->id.",'".$_SESSION['empl']['karyawanid']."')>Approve 2</button>";	
                                            echo "<button class=mybutton onclick=reject(2,".$data->id.",'".$_SESSION['empl']['karyawanid']."')>Reject 2</button>";	
                                        }
                                    }else{
                                        if ($data->status2 == 1) {
                                            echo " [Disetujui 2]";
                                        }
                                        if ($data->status2 == 2) {
                                            echo '<b style="color: red;">[Ditolak 2]</b>';
                                        }
                                    }
                                }      
                            ?>	
                        </td>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
            <?php
            break;
        case 'reject':
            if ($_POST['jenisApprove'] == 1) {
                $query="UPDATE log_supplier_harga_temporary_list
                        SET 
                        waktu1=NOW(), 
                        persetujuan1='".$_POST['karyawanid']."', 
                        status1=2
                        WHERE 
                        id='".$_POST['id']."'
                        ";
            }
            
            if ($_POST['jenisApprove'] == 2) {
                $query="UPDATE log_supplier_harga_temporary_list
                        SET 
                        waktu2=NOW(), 
                        persetujuan2='".$_POST['karyawanid']."', 
                        status2=2
                        WHERE 
                        id='".$_POST['id']."'
                        ";
            }
            mysql_query($query);
            break;
        case 'deleteData':
            $listId = $_POST['id'];
            $queryDeleteDataTemporary = "   DELETE 
                                            FROM 
                                                log_supplier_harga_temporary
                                            WHERE 
                                                temporary_list_id = '".$listId."'";
            mysql_query($queryDeleteDataTemporary);
            $queryDeleteDataTemporaryList = "   DELETE 
                                                FROM 
                                                    log_supplier_harga_temporary_list
                                                WHERE 
                                                    id = '".$listId."'";
            mysql_query($queryDeleteDataTemporaryList);
            echo "Delete data berhasil!";
            break;
        case 'approve':

            // approvel 1
            if ($_POST['jenisApprove'] == 1) {
                $query="UPDATE log_supplier_harga_temporary_list
                        SET 
                        waktu1=NOW(), 
                        persetujuan1='".$_POST['karyawanid']."', 
                        status1=1
                        WHERE 
                        id='".$_POST['id']."'
                        ";
                mysql_query($query);
            }            

            // approvel 2
            // cek kalau approve 1 belum approve
            if ($_POST['jenisApprove'] == 2) {
                // tanggal supplier yang diapprove harus lebih besar dari tanggal master 
                $queryCekApproveD ="SELECT * FROM log_supplier_harga_temporary_list WHERE 
                                    id='".$_POST['id']."'
                                    ";
                $resultCek = fetchData($queryCekApproveD);
                if(empty($resultCek[0]['persetujuan1'])){
                    echo "Gagal, Approve 1 belum Approve!";
                }else{
                    // cek approve 2 sama dengan approve 1
                    if($resultCek[0]['persetujuan1'] == $_SESSION['empl']['karyawanid']){
                        echo "Gagal, Approve 1 dan 2 tidak boleh sama!";
                    }else{
                        // update status2 = 1 di log_supplier_harga_temporary_list
                        $query="UPDATE log_supplier_harga_temporary_list
                                SET 
                                waktu2=NOW(), 
                                persetujuan2='".$_POST['karyawanid']."', 
                                status2=1
                                WHERE 
                                id='".$_POST['id']."'
                                ";
                        mysql_query($query);

                        // update data log_5supplier_harga dari log_supplier_harga_temporary
                        $queryGet = "SELECT * FROM log_supplier_harga_temporary where temporary_list_id='".$_POST['id']."'";
                        $queryGetAct = mysql_query($queryGet);
                        while($dataGet = mysql_fetch_object($queryGetAct)){
                            // get master data
                            $getMasterData="SELECT * FROM log_5supplier_harga 
                                            WHERE 
                                            kode_klsupplier='".$dataGet->kode_klsupplier."'
                                            AND
                                            kode_supplier='".$dataGet->kode_supplier."'
                                            ";
                            $getMasterDataAct = mysql_query($getMasterData);
                            $dataMasterData = mysql_fetch_object($getMasterDataAct);
                            $harga_akhir = '';
                            if($dataGet->fluktuasi == 'naik'){
                                if($dataGet->operator_kenaikan == 'rp'){
                                    $harga_akhir = $dataMasterData->harga + $dataGet->harga_kenaikan;
                                }
                                if($dataGet->operator_kenaikan == '%'){
                                    $harga_akhir = $dataMasterData->harga + ($dataMasterData->harga * ($dataGet->harga_kenaikan/100));
                                }
                            }
                            if($dataGet->fluktuasi == 'turun'){
                                if($dataGet->operator_kenaikan == 'rp'){
                                    $harga_akhir = $dataMasterData->harga - $dataGet->harga_kenaikan;
                                }
                                if($dataGet->operator_kenaikan == '%'){
                                    $harga_akhir = $dataMasterData->harga - ($dataMasterData->harga * ($dataGet->harga_kenaikan/100));
                                }
                            }
                            if($dataGet->fluktuasi == 'tetap'){
                                    $harga_akhir = $dataMasterData->harga;
                            }


                            // insert ke history
                            $insertHistory = "INSERT INTO log_supplier_harga_history 
                            (kode_klsupplier, kode_supplier, tanggal_awal, harga_awal, tanggal_akhir, 
                            harga_akhir, operator_kenaikan, fluktuasi, fee,  harga_kenaikan, created_at) 
                            VALUES 
                            (
                            '".$dataGet->kode_klsupplier."',
                            '".$dataGet->kode_supplier."',
                            '".$dataMasterData->tanggal."',
                            '".$dataMasterData->harga."',
                            '".$dataGet->tanggal."',
                            '".$harga_akhir."',
                            '".$dataGet->operator_kenaikan."',
                            '".$dataGet->fluktuasi."',
                            '".$dataGet->fee."',
                            '".$dataGet->harga_kenaikan."',
                            NOW()
                            )";
                            // update ke master data
                            $updateMasterData ="UPDATE log_5supplier_harga
                                                SET 
                                                harga=$harga_akhir, 
                                                tanggal='".$dataGet->tanggal."'
                                                WHERE 
                                                kode_klsupplier='".$dataGet->kode_klsupplier."'
                                                AND
                                                kode_supplier='".$dataGet->kode_supplier."'
                                                ";
                            mysql_query($insertHistory);
                            mysql_query($updateMasterData);
                            // Hapus Truncat Temporary
                        }
                    }
                }
            }
            break;
        case 'updateFee':
            
            $query="UPDATE log_supplier_harga_temporary
                    SET 
                    fee='".$_POST['nilai']."'
                    WHERE 
                    temporary_list_id='".$_POST['id']."'
                    AND
                    tanggal='".$_POST['tanggal']."'
                    AND
                    kode_supplier='".$_POST['kodesupplier']."'
                    ";
            mysql_query($query);
            print_r("Update Fee Berhasil.");
            break;
        case 'updateFluktuasi':
            $query="UPDATE log_supplier_harga_temporary
                    SET 
                    harga_kenaikan='".$_POST['nilai']."'
                    WHERE 
                    temporary_list_id='".$_POST['id']."'
                    AND
                    tanggal='".$_POST['tanggal']."'
                    AND
                    kode_supplier='".$_POST['kodesupplier']."'
                    ";
            mysql_query($query);
            print_r("Update Fluktuasi Berhasil.");
            break;
	}
?>