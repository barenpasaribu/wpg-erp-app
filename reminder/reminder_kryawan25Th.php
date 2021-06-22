<?
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('../config/connection.php');
require_once('../lib/pearUserSMTP.php');
require_once('../lib/nangkoelib.php');

?>
<html>
<head>
<script language=javascript1.2 type=text/javascript>
function createXMLHttpRequest() {
   try { return new ActiveXObject("Msxml2.XMLHTTP"); } 
   catch (e) {}
   try { return new ActiveXObject("Microsoft.XMLHTTP"); } 
   catch (e) {}
   try { return new XMLHttpRequest(); } 
   catch(e) {}
   alert("XMLHttpRequest Tidak didukung oleh browser");
   return null;
 }

 var con = createXMLHttpRequest();

function post_response_text(tujuan,param,functiontoexecute)
{
con.open("POST", tujuan, true);
con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
con.setRequestHeader("Content-length", param.length);
con.setRequestHeader("Connection", "close");

con.onreadystatechange = eval(functiontoexecute);
con.send(param);
}
function error_catch(x){
	switch (x) {
		case 203:
			alert('Dibutuhkan Authority');
			break;
		case 400:
			alert('Error Server');
			break;
		case 403:
			alert('Anda dilarang masuk');
			break;
		case 404:
			alert('File tidak ditemukan');
			break;
		case 405:
			alert('Method tidak diijinkan');
			break;
		case 407:
			alert('Proxy Error');
			break;
		case 408:
			alert('Permintaan terlalu lama');
			break;
		case 409:
			alert('Query Conflict');
			break;
		case 414:
			alert('ULI terlalu panjang');
			break;
		case 412:
			alert('Variable terlalu banyak');
			break;
		case 415:
			alert('Unsupported Media Type');
			break;
		case 500:
			alert('Server busy, try submit later');
			break;
		case 502:
			alert('Bad gateway');
			break;
		case 505:
			alert('Browser anda terlalu tua');
			break;
	}
}	
function time_()
{
 setInterval("jam()",500);
}
function jam()
{
 var x=new Date();
 var menit=x.getMinutes();
 var detik=x.getSeconds();
 var jam=x.getHours();
 var tg=x.getDate();

 if (jam == '10' && menit == '12' && detik == '10') {//wil execute on hour 10:11:10 everyday
 	window.location.reload();//reload to send mail 
	}

document.getElementById('waktu').innerHTML=jam+":"+menit+":"+detik;
}

</script>
<title>Karyawan 25 Th Scheduler</title>
</head>
<body onload=time_()>
Karyawan 25Th Sheduler:<br><span id=waktu style='color:#4444FF;font-size:24px;'></span>
<br>Automatic reload on: 10:12:10	
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
//===================================	 
$str="select * from ".$dbname.".remindertarget where UPPER(remindertype)='UMUR'
      and onoff=1";
$res=mysql_query($str);
$mailto='';
$x=0;
while($bar=mysql_fetch_object($res))
{
	$x+=1;
	if($x==1)
	 $mailto="<".$bar->email.">";
	else
	 $mailto.=",<".$bar->email.">";
	 
}


//ambil tanggal mundur 25 tahun dari hr ini
$lah=mktime(0,0,0,date('m'),date('d'),(date('Y')-25));
//dah di respond oleh penerima reminder
$str="select f.*,e.name as karyawan from ".$dbname.".user_family f, ".$dbname.".user_empl e
          where birthdate='".$tglLahir."' and f.userid=e.userid";
$content="Dear All,
Berikut keluarga karyawan yang memasuki umur 25 tahun pada hari ini:
	";
$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    echo $no.". Sdr/i ".$bar->name." Keluarga dari karawan: ".$bar->karyawan."<br>";
 } 

$content.="

Mohon segera di F/U berkenaan dengan akan berakhirnya tanggungan dari keluarga-karyawan tersebut ditas.


regards,
Minanga Online System
".date('Ymd H:i:s');   		     	   
	
//============================
	$reply  ='noreply';
	$from   ='<system@minanga.co.id>';
	$subject="Employee Contrack Reminder";
	$cc='';
	$bcc='<r.ginting@minanga.co.id>';

    $headers = array(
					"From"=>$from,
					"To"=>$mailto,
					"Reply-To"=>$reply,
					"Cc"=>$cc, 
					"Bcc"=>$bcc,
					"Subject"=>$subject);
   if($no>0)
   {					
    $res=$mail->send($mailto, $headers, $content);
	if (PEAR::isError($res)) {
	  echo("<p>" . $res->getMessage() . "</p>");
	 } else {
			echo "reminder sent on ".date('Y-m-d- H:i:s');
		 }
   }
   else
   {
   	echo "No data.. on: ".date('d-m-Y H:i:s');
   }
?>
</body>
</html>