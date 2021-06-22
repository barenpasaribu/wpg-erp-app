<?php 

	// date_default_timezone_set("Asia/Bangkok");
	require_once 'config/connection.php';

	$clogin     = $_POST['loginname'];
	$caction    = $_POST['action'];
	$trseq      = intval($_POST['trseq']);
	$kodeorg    = $_POST['kodeorg'];
	$karyawanid = intval($_POST['karyawanid']);
	$kodeasset  = $_POST['kodeasset'];
	$tglterima  = $_POST['tglterima'];
	$tglberakhir= $_POST['tglberakhir'];
	$keterangan = $_POST['keterangan'];

	$cupdate 	= date("Y-m-d H:i:s");
	$result 	= array('pesan' => '' ,
					'flag' => '0' );


	// Create connection
    $conn = new mysqli($dbserver, $uname, $passwd, $dbname, $dbport);

    // Check connection
    if ($conn->connect_error) {
        die("Koneksi Gagal..: " . $conn->connect_error);
    }

	if ($conn) {

		switch ($caction) {
		case 'ADD':

			    $query = "SELECT * FROM sdm_terimaasset WHERE kodeasset='".$kodeasset."' ORDER BY tglberakhir DESC";
				$rs = $conn->query($query);

				$loke = false;
            	if ($rs->num_rows === 0) {
					$loke = true;
				} else {	
	                	while($key = $rs->fetch_assoc()) {
    	                	$tgl = (($key['tglberakhir']==='' || $key['tglberakhir'] === '0000-00-00' ) ? '' : $key['tglberakhir']);
        	            	if ($tgl <> '') {
            	            	if ($key['tglberakhir'] < $tglterima ) {
                        	    	$loke = true;
                            		break;
                       		 	}else {
									$loke = false;
									break;
								}
							}	  
						}
						if (!$loke) {
					    	$result['pesan']="Asset ".$kodeasset." masih digunakan..";
							$result['flag']="1";
						}
                }
				if ($loke) {
					$cquery = "INSERT INTO sdm_terimaasset (kodeorg,tglterima,karyawanid,kodeasset,remark,tglberakhir) VALUES ( ".
							"'".$kodeorg."',".
							"'".$tglterima."',".
							$karyawanid.",".
							"'".$kodeasset."',".
							"'".$keterangan."',".
							"'".$tglberakhir."')";

							if ($conn->query($cquery) === TRUE  ){
						$result['pesan']="Data Berhasil disimpan...";	
						$result['flag']="2";	
					} else {
						$result['pesan']="Data Gagal disimpan...";	
						$result['flag']="1";	
					}
				}	
				// 	$rs->free_result();
				// }
		
				$conn->close();
			break;

		case 'EDIT':

				$cquery = "UPDATE sdm_terimaasset SET ".
						"karyawanid=".$karyawanid.",".
						"remark='".$keterangan."',".
						"tglberakhir='".$tglberakhir."' ".
						"WHERE trseq=".$trseq;

					if ($conn->query($cquery) === TRUE ){
						$result['pesan']="Data Berhasil dirubah...";
						$result['flag']="2";
					} else {
						$result['pesan']="Data Gagal dirubah...";
						$result['flag']="1";
					}
				
				$conn->close();

			break;

		case 'DELETE':

			$cquery = "DELETE FROM sdm_terimaasset ".
					"WHERE trseq = ".$trseq;

					if ( $conn->query($cquery) === TRUE ){
						$result['pesan']="Data Berhasil dihapus...";
						$result['flag']="2";
					} else {
						$result['pesan']="Data Gagal dihapus...";
						$result['flag']="1";
					}
				
				$conn->close();	
			break;

		}

	}
	echo json_encode($result);
	exit();
?>