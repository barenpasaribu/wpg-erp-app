<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';

    $proses = $_POST['proses'];

    switch ($proses) {
        // untuk pengopreasian pabrik
        case 'getCatatan':
            $komoditi = $_POST['komoditi'];
            $isPPn = $_POST['isPPn'];

            $i = '  select *
                    from '.$dbname.".pmn_5catatan 
                    where 
                    komoditi = '".$komoditi."'
                    AND
                    isPPn = '".$isPPn."'
                    ";
            
            $data = fetchData($i);
   
            if (empty($data[0])) {
                echo json_encode("kosong");
            } else {
                echo json_encode($data[0]);
            }
            
            
            break;
        
    }



?>