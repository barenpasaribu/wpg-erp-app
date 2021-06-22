<?php

use \Psr\Http\Message\ServerRequestInterface as Request;

use \Psr\Http\Message\ResponseInterface as Response;



require 'vendor/autoload.php';



$config['displayErrorDetails'] = true;

$config['addContentLengthHeader'] = false;



//$config['db']['host']   = '202.157.177.13';
$config['db']['host']   = '202.157.185.209';

//$config['db']['user']   = 'root';
$config['db']['user']   = 'admin';

//$config['db']['pass']   = 'MOONlight!@#';
$config['db']['pass']   = 'WPG123!@#';


//$config['db']['dbname'] = 'fastenvi_pimdbfr';
$config['db']['dbname'] = 'fastenvi_pimdbfr';




//$app = new \Slim\App;

$app = new \Slim\App(['settings' => $config]);



$container = $app->getContainer();



$container['db'] = function ($c) {

    $db = $c['settings']['db'];

    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],

        $db['user'], $db['pass']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;

};





//get usertpe

$app->get('/usertype', function (Request $request, Response $response, array $args) {

    try{

        

		$sql = "SELECT * FROM usertype ";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"usertype", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"usertype"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});











//get kelbuah

$app->get('/kelbuah', function (Request $request, Response $response, array $args) {

    try{

        

		//$sql = "SELECT * FROM kelbuah where kelbuahtype= 1 and kelbuahcatg= 1 ";
		$sql = "SELECT * FROM kelbuah  ";	

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"kelbuah", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"kelbuah"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});



//get weight bridge

$app->get('/wb', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM jembtimbang";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"wb", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"wb"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});





//get checkout

$app->get('/checkout', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM checkout";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"checkout", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"checkout"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});







//get bak

$app->get('/bak', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM bak";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"bak", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"bak"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});



//get alasbak

$app->get('/alasbak', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM alasbak";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"alasbak", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"alasbak"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});









//get device

$app->get('/device', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM device";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"device", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"device"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});





//get field

$app->get('/field', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM field";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"field", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"field"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});











//get job

$app->get('/job', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM job";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"job", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"job"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});



//get jobmaterial

$app->get('/jobmaterial', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM jobmaterial";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"jobmaterial", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"jobmaterial"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});



//get vehicle

$app->get('/vehicle', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM kendaraan";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"vehicle", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"vehicle"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});





//getUom

$app->get('/uom', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM satuan";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"uom", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"uom"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});





//get employee

$app->get('/employee', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM employee";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"employee", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"employee"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});



//get list of users

$app->get('/users', function (Request $request, Response $response, array $args) {

    try{

		$sql = "SELECT * FROM user";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $response->withJson(["status" => "success","api"=>"users", "data" => $result], 200);



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"users"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});



//get registered device

$app->get('/device/is_registered/{serial_no}', function (Request $request, Response $response, array $args) {

    try{

		$serialNo = $args['serial_no'];

		$sql = "SELECT * FROM device where device_sn='$serialNo'";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		if (count($result) > 0){

			$resultData=array(array("message"=>"Your device already registered","api"=>"is_registered"));

			return $response->withJson(["status" => "success", "data" => $resultData], 200);

		}else{

			$resultData=array(array("message"=>"Your device not registered yet","api"=>"is_registered"));

			return $response->withJson(["status" => "error", "data" => $resultData], 200);	

		}

		



    }catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"is_registered"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

});





$app->post('/device/register', function (Request $request, Response $response, array $args) {

	try{

		$data = $request->getParsedBody();

		$serialNo=$data['data'][0]['serial_no'];

		$deviceId=$data['data'][0]['device_id'];

		$sql="select count(*) total from device where device_sn='$serialNo'";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

        if ( $result[0]['total'] > 0 ){

            return $response->withJson(["status" => "succes","api"=>"register", "message" => 'already register'], 200);

        }else{

            $sql="insert into device(device_id, device_sn,lastupdated,lastuser) values('$deviceId','$serialNo',now(),'admin')";

			$stmt = $this->db->prepare($sql);

		    $stmt->execute();

            return $response->withJson(["status" => "succes","api"=>"register", "message" => 'new register'], 200);

        }

        

	}catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"register"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

	

    //return $response;

});



	





$app->post('/template', function (Request $request, Response $response, array $args) {

	try{

		$data = $request->getParsedBody();

	}catch(Exception $e) {

		

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"login"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

	

    //return $response;

});



//post upload data transation

$app->post('/upload_data_transaction', function (Request $request, Response $response, array $args) {

	try{

		$data = $request->getParsedBody();

		$idsuccess='';



//		$data=$data['data'][34]['TRANSID'];

		//$data=count($data['data']);

		$totaldata=count($data['data']);

		for ($x = 0; $x <= $totaldata; $x++) {

		    $tableName=$data['data'][$x]['table'];

		    if ($tableName=='transpanenheader'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO transpanenheader

(TRANSTYPE, TRANSID,  DEVICE_ID, EMPCODE, EMPCODE_BANTU, FIELD_NO, TASKNO, TOTKELBUAHQTY, SATUANID, TOTKELBUAHQTYCACAT, DEBRIS, REPRINT, LASTUPDATED, LASTUSER, ISDELETED, GPSCOORD, ISSYNC, ISHELPER)

VALUES( '.$tph['TRANSTYPE'].', \''.$tph['TRANSID'].'\', \''.$tph['DEVICE_ID'].'\', \''.$tph['EMPCODE'].'\', \''.$tph['EMPCODE_BANTU'].'\', \''.$tph['FIELD_NO'].'\', \''.$tph['TASKNO'].'\', '.$tph['TOTKELBUAHQTY'].', \''.$tph['SATUANID'].'\', '.$tph['TOTKELBUAHQTYCACAT'].', '.$tph['DEBRIS'].', '.$tph['REPRINT'].', \''.$tph['LASTUPDATED'].'\', \''.$tph['LASTUSER'].'\', '.$tph['ISDELETED'].', \''.$tph['GPSCOORD'].'\', 1, 0) ';



		

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'transpanenheader:'. $tph['TRANSID'] . ';';	

		    }

		    if ($tableName=='transpanendetail'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO transpanendetail

(TRANSID, KELBUAHID, KELBUAHQTY, LASTUPDATED, LASTUSER, ISSYNC)

VALUES(\''.$tph['TRANSID'].'\', '.$tph['KELBUAHID'].', '.$tph['KELBUAHQTY'].', \''.$tph['LASTUPDATED'].'\', \''.$tph['LASTUSER'].'\', 1);			';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'transpanendetail:'. $tph['TRANSID'] . ';';	

		    }

		    if ($tableName=='transpenugasan'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO transpenugasan

(TRANSID, JOBCODE, FIELDNO, DEVICE_ID, EMPCODE, MDCODE, WORKTYPE, ISMORNINGABSENT, PROGRESS, SATUANID, CRCODE, WORKTIME_H, WORKTIME_M, TIMESTAMP1, TIMESTAMP2

, TIMESTAMP3, GPSCOORD1, GPSCOORD2, GPSCOORD3, LASTUSER, ISDELETED, ISSYNC, LASTUPDATED)

VALUES(\''.$tph['TRANSID'].'\', \''.$tph['JOBCODE'].'\', \''.$tph['FIELDNO'].'\', \''.$tph['DEVICE_ID'].'\', \''.$tph['EMPCODE'].'\'

, \''.$tph['MDCODE'].'\', '.$tph['WORKTYPE'].', '.$tph['ISMORNINGABSENT'].', '.$tph['PROGRESS'].', \''.$tph['SATUANID'].'\', \''.$tph['CRCODE'].'\'

, '.$tph['WORKTIME_H'].', '.$tph['WORKTIME_M'].', \''.$tph['TIMESTAMP1'].'\', \''.$tph['TIMESTAMP2'].'\', \''.$tph['TIMESTAMP3'].'\'

, \''.$tph['GPSCOORD1'].'\', \''.$tph['GPSCOORD2'].'\',\''.$tph['GPSCOORD3'].'\',\''.$tph['LASTUSER'].'\', '.$tph['ISDELETED'].', 1

, \''.$tph['LASTUPDATED'].'\');				';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'transpenugasan:'. $tph['TRANSID'] . ';';	

		    }		    

			if ($tableName=='spbheader'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO spbheader

(TRANSID, TRANSTYPE, EMPCODE, KENDNO, BINS, ALASBAK, LASTUPDATED, LASTUSER, ISDELETED, GPSCOORD, REPRINT, RUNNINGNUMBER, TOT_PKT, TOT_CRH, ISSYNC, DEVICE_ID)

VALUES(\''.$tph['TRANSID'].'\', '.$tph['TRANSTYPE'].', \''.$tph['EMPCODE'].'\', \''.$tph['KENDNO'].'\', \''.$tph['BINS'].'\', \''.$tph['ALASBAK'].'\'

, \''.$tph['LASTUPDATED'].'\'

, \''.$tph['LASTUSER'].'\', '.$tph['ISDELETED'].', \''.$tph['GPSCOORD'].'\', '.$tph['REPRINT'].', '.$tph['RUNNINGNUMBER'].', '.$tph['TOT_PKT'].'

, '.$tph['TOT_CRH'].'

, 1, \''.$tph['DEVICE_ID'].'\');				';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'spbheader:'. $tph['TRANSID'] . ';';	



		    }

			if ($tableName=='spbdetailjumlah'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO spbdetailjumlah

(TRANSID_PCK, TRANSID, ISPAKET, FIELDNO_PCK, TOTKELBUAHQTY_PCK, LASTUPDATED, LASTUSER, ISDELETED, ISSYNC)

VALUES(\''.$tph['TRANSID_PCK'].'\', \''.$tph['TRANSID'].'\', '.$tph['ISPAKET'].', \''.$tph['FIELDNO_PCK'].'\', '.$tph['TOTKELBUAHQTY_PCK'].'

, \''.$tph['LASTUPDATED'].'\', \''.$tph['LASTUSER'].'\'

, '.$tph['ISDELETED'].', 1);				';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'spbdetailjumlah:'. $tph['TRANSID'] . ';';	



		    }

			if ($tableName=='spbdetailpemuat'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO spbdetailpemuat

(TRANSID, EMPCODE, LASTUPDATED, LASTUSER, ISDELETED, ISSYNC)

VALUES(\''.$tph['TRANSID'].'\', \''.$tph['EMPCODE'].'\', \''.$tph['LASTUPDATED'].'\', \''.$tph['LASTUSER'].'\', '.$tph['ISDELETED'].', 1);



				';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'spbdetailpemuat:'. $tph['TRANSID'] . ';';	



		    }

			

			if ($tableName=='sortasipabrikheader'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO sortasipabrikheader

(TRANSNO, TRANSNO_OLD, LTSNO, LTSTIME, MILLID, WBTICNO, JEMBTIMBANGCODE, CROPOWNER, UNITID, 

CUSTOUT, DIVCODE, KENDNO, DRIVER_EMP_CODE, DRIVER_NAME, TOTBC, TOTLF, SMPLSIZE, LASTUSER, LASTUPDATED, SCANDEVICE_ID, RUNNING_NUMBER, ISDELETED, ISSYNC)

VALUES(\''.$tph['TRANSNO'].'\', 0, \''.$tph['LTSNO'].'\', \''.$tph['LTSTIME'].'\', \''.$tph['MILLID'].'\', \''.$tph['WBTICNO'].'\', \''.$tph['JEMBTIMBANGCODE'].'\', \''.$tph['CROPOWNER'].'\',

 \''.$tph['UNITID'].'\', \''.$tph['CUSTOUT'].'\', \''.$tph['DIVCODE'].'\', \''.$tph['KENDNO'].'\', \''.$tph['DRIVER_EMP_CODE'].'\', \''.$tph['DRIVER_NAME'].'\',

 \''.$tph['TOTBC'].'\', \''.$tph['TOTLF'].'\', \''.$tph['SMPLSIZE'].'\', \''.$tph['LASTUSER'].'\', \''.$tph['LASTUPDATED'].'\', \''.$tph['SCANDEVICE_ID'].'\', 

 \''.$tph['RUNNING_NUMBER'].'\', \''.$tph['ISDELETED'].'\', \''.$tph['ISSYNC'].'\');

';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'sortasipabrikheader:'. $tph['TRANSNO'] . ';';	



		    }



			if ($tableName=='sortasipabrikdetail'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO sortasipabrikdetail

(TRANSNO, KELBUAHID, AMOUNT, LASTUSER, LASTUPDATED, ISDELETED, ISSYNC)

VALUES(\''.$tph['TRANSNO'].'\', \''.$tph['KELBUAHID'].'\', \''.$tph['AMOUNT'].'\', \''.$tph['LASTUSER'].'\', \''.$tph['LASTUPDATED'].'\', \''.$tph['ISDELETED'].'\', \''.$tph['ISSYNC'].'\');

';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'sortasipabrikdetail:'. $tph['TRANSNO'] . ';';	



		    }



			if ($tableName=='sortasipabrik_keseluruhan'){

				$tph=$data['data'][$x];

				$sql='INSERT INTO sortasipabrik_keseluruhan

(TRANSNO, CF_FRESH, CF_OLD, LF_FRESH, LF_OLD, LF_ROTTEN, CM_LOW, CM_MOD, CM_LS, REMARK, LASTUSER, LASTUPDATED, ISDELETED, ISSYNC)

VALUES(\''.$tph['TRANSNO'].'\', \''.$tph['TRANSNO'].'\', \''.$tph['CF_OLD'].'\', \''.$tph['LF_FRESH'].'\', \''.$tph['LF_OLD'].'\', \''.$tph['LF_ROTTEN'].'\'

, \''.$tph['CM_LOW'].'\', \''.$tph['CM_MOD'].'\', \''.$tph['CM_LS'].'\', \''.$tph['REMARK'].'\', \''.$tph['LASTUSER'].'\'

, \''.$tph['LASTUPDATED'].'\', \''.$tph['ISDELETED'].'\', \''.$tph['ISSYNC'].'\');

';

				$stmt = $this->db->prepare($sql);

				$stmt->execute();

				$idsuccess=$idsuccess .'sortasipabrik_keseluruhan:'. $tph['TRANSNO'] . ';';	



		    }









		}

		

			$resultData=array(array("message"=>"upload success","api"=>"upload_data_transaksi_result","idsuccess"=>$idsuccess));

			return $response->withJson(["status" => "success", "data" => $resultData], 200);

		

		

	}catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"login"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

	

    //return $response;

});

	



//login validation

$app->post('/login', function (Request $request, Response $response, array $args) {

	try{

		$data = $request->getParsedBody();

		$userid=$data['data'][0]['userid'];

		$password=$data['data'][0]['password'];

		

		$sql = "SELECT * FROM user  where userid='$userid' and password=md5('$password')";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		//$response->getBody()->write("Hello ". print_r($result));

		

		

		if (count($result) > 0){

			$resultData=array(array("message"=>"login success","api"=>"login"));

			return $response->withJson(["status" => "success", "data" => $resultData], 200);

			//$response->getBody()->write("login success");

		}else{

			$resultData=array(array("message"=>"invalid username or password","api"=>"login"));

			return $response->withJson(["status" => "error", "data" => $resultData], 200);

		}



	}catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"login"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

	}

	

    //return $response;

});





$app->post('/login_bak', function (Request $request, Response $response, array $args) {

	try{

		$data = $request->getParsedBody();

		$userid=$data['data'][0]['userid'];

		$password=$data['data'][0]['password'];

		

		$sql = "SELECT * FROM user  where empcode='$userid' and password=md5('$password')";

		$stmt = $this->db->prepare($sql);

		$stmt->execute();

		$result = $stmt->fetchAll();

		//$response->getBody()->write("Hello ". print_r($result));

		

		

		if (count($result) > 0){

			$resultData=array(array("message"=>"login success","api"=>"login"));

			return $response->withJson(["status" => "success", "data" => $resultData], 200);

			//$response->getBody()->write("login success");

		}else{

			$resultData=array(array("message"=>"invalid username or password","api"=>"login"));

			return $response->withJson(["status" => "error", "data" => $resultData], 200);

		}



	}catch(Exception $e) {

		$resultData=array(array("message"=>$e->getMessage(),"api"=>"login"));

		return $response->withJson(["status" => "error", "data" => $resultData], 200);

		

	}

	

    //return $response;

});





//sample only

$app->get('/hello2/{name}', function (Request $request, Response $response, array $args) {

    $name = $args['name'];

    $sql = "SELECT * FROM dummy where id=$name";

    $stmt = $this->db->prepare($sql);

    $stmt->execute();

    $result = $stmt->fetchAll();

    return $response->withJson(["status" => "success", "data" => $result], 200);

   

});



//sample only

$app->get('/echo/{name}', function (Request $request, Response $response, array $args) {

    $name = $args['name'];

    $response->getBody()->write("you said : $name");



    return $response;

});



$app->run();



?>