<?php
    require_once 'config/connection.php';
    
    $table = 'v_sdm_terimaasset';
    $primaryKey = 'kodeorg';
    // $aparam = json_decode(stripslashes($_GET['filter']));
    
    $aparam = json_decode($_POST['filter']);

    $columns = array(
        array( 'db' => 'trseq',   'dt' => 0 ),
        array( 'db' => 'kodeorg',  'dt' => 1 ),
        array( 'db' => 'namaorg',  'dt' => 2 ),
        array(
            'db' => 'tglterima',
            'dt' => '3',
            'formatter' => function( $d, $row ) {
                $newDate = ($d==null ? '' : date("d-m-Y", strtotime($d)));  
                return $newDate;
            }
        ),
        array( 'db' => 'karyawanid', 'dt' => 4 ),
        array( 'db' => 'nik', 'dt' => 5 ),
        array( 'db' => 'namakaryawan', 'dt' => 6 ),
        array( 'db' => 'kodeasset','dt' => 7 ),
        array( 'db' => 'namaasset','dt' => 8 ),
        array( 'db' => 'keteranganasset','dt' => 9 ),
        array( 'db' => 'keterangan','dt' => 10 ),
        array(
            'db' => 'tglberakhir',
            'dt' => '11',
            'formatter' => function( $d, $row ) {
                $newDate = ($d==null ? '' : date("d-m-Y", strtotime($d)));  
                return $newDate;
            }
        )
    );

    $sql_details = array(
    	'user' => $uname,
    	'pass' => $passwd,
    	'db'   => $dbname,
        'host' => $dbserver,
        'port' => $dbport
    );

    require('lib/ssp.class.php');

    $where = '';    
    $count = count($aparam);

    for($i = 0; $i < $count; $i++){ 
        $where .= "kodeorg='".$aparam[$i]."'";
        if ($i < $count-1) {
            $where .= " OR ";
        }
    }

    echo json_encode(
        // SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
        SSP::complex ( $_POST, $sql_details, $table, $primaryKey, $columns,null,$where )
    );    

?>