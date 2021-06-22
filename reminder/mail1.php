<?
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('../config/connection.php');
require_once('../lib/pearUserSMTP.php');
require_once('../lib/nangkoelib.php');

?>
<html>
<head>

<title>Karyawan Kontract Scheduler</title>
</head>
<body>
<?

//mail config
 $host = "116.90.167.32";
 $username = "admin@minanga.co.id";
 $password = "pintubesarutara";
 $mail = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
	 'port' => 25,
     'username' => $username,
     'password' => $password));
	 
//====================== $from = "Sandra Sender <r.ginting@minanga.co.id>";
 $to = "Nangkoel Ginting <r.ginting@minanga.co.id>";
 $subject = "Hi!";
 $body = "Hi,\n\nHow are you?";


$content="Dear All,

Berikut karyawan kontrak yang akan berakhir kontraknya satu bulan dari sekarang:
	";

$content.="

Mohon segera di F/U berkenaan denganakan berakhirnya kontrak dari karyawan-karyawan tersebut.


regards,
Minanga Online System
".date('Ymd H:i:s');   		     	   
	
//============================
	$reply  ='noreply';
	$from   ='Administrator <admin@minanga.co.id>';
	$subject="Employee Contrack Reminder";
	$cc='';
	$bcc='';

    $headers = array(
					"From"=>$from,
					"To"=>$mailto,
					"Reply-To"=>$reply,
					"Cc"=>$cc, 
					"Bcc"=>$bcc,
					"Subject"=>$subject);

 $no=1;
   if($no>0)
   {					
    $res=$mail->send($to, $headers, $body);
	if (PEAR::isError($res)) {
	  echo("<p>" . $res->getMessage() . "</p>");
	 } else {
	 	$stg="update ".$dbname.".user_emplcontract
		      set remindersent=1 where dateend>='".$hariini."' 
              and dateend<='".$tujuh."' and remindersent=0";
		mysql_query($stg);	  
	  echo("<p>Message successfully sent on: ".date('d-m-Y H:i:s')."</p>");
	 }
   }
   else
   {
   	echo "No data.. on: ".date('d-m-Y H:i:s');
   }
?>
</body>
</html>