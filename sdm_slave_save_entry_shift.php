<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 

$id		  		= isset($_POST['id']) ? $_POST['id'] : null;
$kode	  		= isset($_POST['kode']) ? $_POST['kode'] : null;
$nama   		= isset($_POST['nama']) ? $_POST['nama'] : null;	
$jam_masuk		= isset($_POST['jam_masuk']) ? $_POST['jam_masuk'] : null;
$jam_keluar		= isset($_POST['jam_keluar']) ? $_POST['jam_keluar'] : null;	
$aktif			= isset($_POST['aktif']) ? $_POST['aktif'] : null;
$kd_organisasi	= isset($_POST['kd_organisasi']) ? $_POST['kd_organisasi'] : null;
$kd_unit		= isset($_POST['kd_unit']) ? $_POST['kd_unit'] : null;
$tgl_start		= isset($_POST['tgl_start']) ? putertanggal($_POST['tgl_start']) : null;
$tgl_end		= isset($_POST['tgl_end']) ? putertanggal($_POST['tgl_end']) : null;
$usrcrt			= isset($_SESSION['standard']['username']) ? $_SESSION['standard']['username'] : null;
$usrdt			= date("Y-m-d H:i:s");
$method			= isset($_POST['method']) ? $_POST['method'] : null;

#pre($tgl_end); exit();
#echo json_decode($_POST['details'][0]['seqno']);
#pre(json_decode($_POST['details']));exit();
switch($method){
	case 'insert':
	#showerror();
	#pre($_POST);exit();

	$QInsert = "INSERT INTO ".$dbname.".sdm_shift 
		(`kode`, `nama`, `jam_masuk`, `jam_keluar`, `aktif`, `kd_organisasi`, `kd_unit`, `tgl_start`, `tgl_end`, `user_create`, `user_date`) 
		VALUES 
		('".$kode."', '".$nama."', '".$jam_masuk."', '".$jam_keluar."', '".$aktif."', '".$kd_organisasi."', '".$kd_unit."', '".$tgl_start."', '".$tgl_end."', '".$usrcrt."', '".$usrdt."')";			  
	if(!mysql_query($QInsert)) {
		echo "<script>alert('Failed');</script>";
		die(mysql_error());
	}
	break;
	case 'update':
	#showerror();
	#pre($_SESSION);exit();

	$QUpdate = "UPDATE ".$dbname.".sdm_shift SET 
		`kode` = '".$kode."',
		`nama` = '".$nama."',
		`jam_masuk` = '".$jam_masuk."',
		`jam_keluar`= '".$jam_keluar."',
		`aktif` = '".$aktif."',
		`kd_organisasi` = '".$kd_organisasi."',
		`kd_unit` = '".$kd_unit."',
		`tgl_start` = '".$tgl_start."',
		`tgl_end` = '".$tgl_end."',
		`user_create` = '".$usrcrt."',
		`user_date` = '".$usrdt."'
		WHERE (`id`='".$id."') LIMIT 1";			  
	if(!mysql_query($QUpdate)) {
		echo "<script>alert('Failed');</script>";
		die(mysql_error());
	}
	break;
	case 'delete':
	#showerror();
	#pre($_SESSION);exit();

	$QDelete = "DELETE FROM ".$dbname.".sdm_shift WHERE (`id`='".$id."');";			  
	if(!mysql_query($QDelete)) {
		echo "<script>alert('Failed');</script>";
		die(mysql_error());
	}
	break;
	default:
	  echo "<script>alert('Case Error');</script>";
	break;	
}
?>
